<?php
/**
 * Public read API for the Next.js site.
 *
 * Published pages, the path index, the resolved design tokens, navigation,
 * and the public organization facts. Draft reads live in inc/preview.php.
 * Form writes live in the LiftedPath Forms plugin.
 *
 * @package LiftedPath
 */

/**
 * Register the public read routes.
 */
function liftedpath_register_rest_routes(): void {
	register_rest_route(
		'liftedpath/v1',
		'/page',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'liftedpath_rest_page',
			'permission_callback' => '__return_true',
			'args'                => array(
				'path' => array(
					'required'          => true,
					'type'              => 'string',
					'sanitize_callback' => 'liftedpath_rest_sanitize_path',
				),
			),
		)
	);

	register_rest_route(
		'liftedpath/v1',
		'/paths',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'liftedpath_rest_paths',
			'permission_callback' => '__return_true',
		)
	);

	register_rest_route(
		'liftedpath/v1',
		'/design',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'liftedpath_rest_design',
			'permission_callback' => '__return_true',
		)
	);

	register_rest_route(
		'liftedpath/v1',
		'/navigation',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'liftedpath_rest_navigation',
			'permission_callback' => '__return_true',
		)
	);

	register_rest_route(
		'liftedpath/v1',
		'/settings',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'liftedpath_rest_settings',
			'permission_callback' => '__return_true',
		)
	);
}
add_action( 'rest_api_init', 'liftedpath_register_rest_routes' );

/**
 * Reduce a request path to a public page path.
 *
 * @param mixed $value Raw query value.
 * @return string|WP_Error
 */
function liftedpath_rest_sanitize_path( $value ): string|WP_Error {
	if ( ! is_string( $value ) ) {
		return new WP_Error(
			'invalid_path',
			__( 'The path must be a string.', 'liftedpath' ),
			array( 'status' => 400 )
		);
	}

	$value = trim( rawurldecode( wp_unslash( $value ) ) );

	if ( '' === $value || '/' === $value ) {
		return '/';
	}

	if ( strlen( $value ) > 200 || preg_match( '/\.\.|[?#\\\\]|:\/\//', $value ) ) {
		return new WP_Error(
			'invalid_path',
			__( 'The path is not a page path.', 'liftedpath' ),
			array( 'status' => 400 )
		);
	}

	$segments = array();

	foreach ( explode( '/', trim( $value, '/' ) ) as $segment ) {
		if ( '' === $segment ) {
			continue;
		}

		$slug = sanitize_title( $segment );

		if ( '' === $slug ) {
			return new WP_Error(
				'invalid_path',
				__( 'The path is not a page path.', 'liftedpath' ),
				array( 'status' => 400 )
			);
		}

		$segments[] = $slug;
	}

	if ( array() === $segments ) {
		return '/';
	}

	return '/' . implode( '/', $segments );
}

/**
 * Return one published page.
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function liftedpath_rest_page( WP_REST_Request $request ): WP_REST_Response|WP_Error {
	$path = $request->get_param( 'path' );

	if ( ! is_string( $path ) ) {
		return new WP_Error(
			'invalid_path',
			__( 'The path is not a page path.', 'liftedpath' ),
			array( 'status' => 400 )
		);
	}

	$page = liftedpath_rest_find_page( $path );

	if ( is_wp_error( $page ) ) {
		return $page;
	}

	return rest_ensure_response( liftedpath_rest_page_payload( $page ) );
}

/**
 * Return the published page index.
 *
 * @return WP_REST_Response
 */
function liftedpath_rest_paths(): WP_REST_Response {
	$query = new WP_Query(
		array(
			'post_type'              => 'page',
			'post_status'            => 'publish',
			'posts_per_page'         => -1,
			'orderby'                => array(
				'menu_order' => 'ASC',
				'title'      => 'ASC',
			),
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
		)
	);

	$paths = array();

	foreach ( $query->posts as $post ) {
		if ( ! $post instanceof WP_Post || '' !== $post->post_password ) {
			continue;
		}

		$paths[] = array(
			'id'       => (int) $post->ID,
			'path'     => liftedpath_rest_public_path( $post ),
			'modified' => liftedpath_rest_modified( $post ),
		);
	}

	return rest_ensure_response( array( 'paths' => $paths ) );
}

/**
 * Return the resolved design tokens.
 *
 * @return WP_REST_Response|WP_Error
 */
function liftedpath_rest_design(): WP_REST_Response|WP_Error {
	if ( ! class_exists( 'WP_Theme_JSON_Resolver' ) ) {
		return new WP_Error(
			'design_unavailable',
			__( 'Design tokens are unavailable.', 'liftedpath' ),
			array( 'status' => 500 )
		);
	}

	return rest_ensure_response( liftedpath_rest_design_payload() );
}

/**
 * Find a published page from its public path.
 *
 * @param string $path Sanitized path.
 * @return WP_Post|WP_Error
 */
function liftedpath_rest_find_page( string $path ): WP_Post|WP_Error {
	$post = null;

	if ( '/' === $path ) {
		if ( 'page' === get_option( 'show_on_front' ) ) {
			$post = get_post( (int) get_option( 'page_on_front' ) );
		}
	} else {
		$post = get_page_by_path( ltrim( $path, '/' ), OBJECT, 'page' );
	}

	$is_public = $post instanceof WP_Post
		&& 'page' === $post->post_type
		&& 'publish' === $post->post_status
		&& '' === $post->post_password;

	if ( ! $is_public ) {
		return new WP_Error(
			'page_not_found',
			__( 'No published page exists at that path.', 'liftedpath' ),
			array( 'status' => 404 )
		);
	}

	return $post;
}

/**
 * Shape one page for the frontend.
 *
 * @param WP_Post $page Published page.
 * @return array<string, mixed>
 */
function liftedpath_rest_page_payload( WP_Post $page ): array {
	$restore         = isset( $GLOBALS['post'] ) ? $GLOBALS['post'] : null;
	$GLOBALS['post'] = $page;
	setup_postdata( $page );

	$blocks = liftedpath_rest_blocks( parse_blocks( $page->post_content ) );

	wp_reset_postdata();

	if ( $restore instanceof WP_Post ) {
		$GLOBALS['post'] = $restore;
		setup_postdata( $restore );
	}

	$parent_path = null;

	if ( $page->post_parent ) {
		$parent = get_post( (int) $page->post_parent );

		if ( $parent instanceof WP_Post && 'page' === $parent->post_type ) {
			$parent_path = liftedpath_rest_public_path( $parent );
		}
	}

	$seo = liftedpath_seo_for_page( $page );

	return array(
		'id'              => (int) $page->ID,
		'slug'            => $page->post_name,
		'path'            => liftedpath_rest_public_path( $page ),
		'title'           => html_entity_decode( $page->post_title, ENT_QUOTES, 'UTF-8' ),
		'status'          => $page->post_status,
		'modified'        => liftedpath_rest_modified( $page ),
		'parent_path'     => $parent_path,
		'featured_image'  => liftedpath_rest_featured_image( (int) $page->ID ),
		'seo_title'       => $seo['title'],
		'seo_description' => $seo['description'],
		'seo_image'       => $seo['image'],
		'seo_noindex'     => $seo['noindex'],
		'hero_summary'    => liftedpath_rest_text_field( 'hero_summary', (int) $page->ID ),
		'blocks'          => $blocks,
	);
}

/**
 * Public path for a page. The static front page is `/`.
 *
 * @param WP_Post $page Page.
 */
function liftedpath_rest_public_path( WP_Post $page ): string {
	$front_id = ( 'page' === get_option( 'show_on_front' ) ) ? (int) get_option( 'page_on_front' ) : 0;

	if ( $front_id && $front_id === (int) $page->ID ) {
		return '/';
	}

	$uri = trim( (string) get_page_uri( $page ), '/' );

	return '' === $uri ? '/' : '/' . $uri;
}

/**
 * RFC 3339 modified time in GMT.
 *
 * @param WP_Post $page Page.
 */
function liftedpath_rest_modified( WP_Post $page ): string {
	$gmt = $page->post_modified_gmt;

	if ( ! is_string( $gmt ) || '0000-00-00 00:00:00' === $gmt ) {
		$gmt = $page->post_date_gmt;
	}

	$formatted = mysql_to_rfc3339( $gmt );

	if ( ! is_string( $formatted ) || '' === $formatted ) {
		return '';
	}

	return $formatted . '+00:00';
}

/**
 * Read a page text field from ACF, then from post meta.
 *
 * @param string $name    Field name.
 * @param int    $post_id Page ID.
 */
function liftedpath_rest_text_field( string $name, int $post_id ): string {
	$value = function_exists( 'get_field' ) ? get_field( $name, $post_id ) : get_post_meta( $post_id, $name, true );

	return is_string( $value ) ? $value : '';
}

/**
 * Featured image for a page.
 *
 * @param int $post_id Page ID.
 * @return array<string, mixed>|null
 */
function liftedpath_rest_featured_image( int $post_id ): ?array {
	return liftedpath_rest_image( (int) get_post_thumbnail_id( $post_id ) );
}

/**
 * Public image fields for an attachment.
 *
 * @param int $attachment_id Attachment ID.
 * @return array<string, mixed>|null
 */
function liftedpath_rest_image( int $attachment_id ): ?array {
	if ( $attachment_id <= 0 ) {
		return null;
	}

	$attachment = get_post( $attachment_id );

	if ( ! $attachment instanceof WP_Post || 'attachment' !== $attachment->post_type ) {
		return null;
	}

	$url = wp_get_attachment_image_url( $attachment_id, 'full' );

	if ( ! is_string( $url ) || '' === $url ) {
		return null;
	}

	$meta = wp_get_attachment_metadata( $attachment_id );

	return array(
		'id'     => $attachment_id,
		'url'    => $url,
		'alt'    => (string) get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ),
		'width'  => ( is_array( $meta ) && isset( $meta['width'] ) ) ? (int) $meta['width'] : 0,
		'height' => ( is_array( $meta ) && isset( $meta['height'] ) ) ? (int) $meta['height'] : 0,
	);
}

/**
 * Prepare parsed blocks, dropping the empty spacers WordPress inserts.
 *
 * @param array<int, array<string, mixed>> $parsed Parsed blocks.
 * @return array<int, array<string, mixed>>
 */
function liftedpath_rest_blocks( array $parsed ): array {
	$blocks = array();

	foreach ( $parsed as $block ) {
		if ( ! is_array( $block ) ) {
			continue;
		}

		$prepared = liftedpath_rest_block( $block );

		if ( null !== $prepared ) {
			$blocks[] = $prepared;
		}
	}

	return $blocks;
}

/**
 * Shape one block.
 *
 * Nested blocks stay under attributes.innerBlocks so the item keeps
 * the three keys name, attributes, and html.
 *
 * @param array<string, mixed> $block Parsed block.
 * @return array<string, mixed>|null
 */
function liftedpath_rest_block( array $block ): ?array {
	$name      = isset( $block['blockName'] ) ? $block['blockName'] : null;
	$inner     = isset( $block['innerHTML'] ) ? trim( (string) $block['innerHTML'] ) : '';
	$children  = array();

	if ( ! empty( $block['innerBlocks'] ) && is_array( $block['innerBlocks'] ) ) {
		$children = liftedpath_rest_blocks( $block['innerBlocks'] );
	}

	if ( ( null === $name || '' === $name ) && '' === $inner && array() === $children ) {
		return null;
	}

	if ( null === $name || '' === $name ) {
		$name = 'core/freeform';
	}

	$attributes = liftedpath_rest_block_attributes( $block );

	if ( array() !== $children ) {
		$attributes['innerBlocks'] = $children;
	}

	return array(
		'name'       => (string) $name,
		'attributes' => array() === $attributes ? new stdClass() : $attributes,
		'html'       => wp_kses_post( (string) render_block( $block ) ),
	);
}

/**
 * Attributes for one block.
 *
 * LiftedPath blocks expose ACF field names. Core blocks expose parsed attributes.
 *
 * @param array<string, mixed> $block Parsed block.
 * @return array<string, mixed>
 */
function liftedpath_rest_block_attributes( array $block ): array {
	$attributes = ( isset( $block['attrs'] ) && is_array( $block['attrs'] ) ) ? $block['attrs'] : array();
	$name       = isset( $block['blockName'] ) ? (string) $block['blockName'] : '';

	if ( str_starts_with( $name, 'liftedpath/' ) && isset( $attributes['data'] ) && is_array( $attributes['data'] ) ) {
		$fields = array();

		foreach ( $attributes['data'] as $key => $value ) {
			if ( ! is_string( $key ) || str_starts_with( $key, '_' ) ) {
				continue;
			}

			$fields[ $key ] = $value;
		}

		return $fields;
	}

	return $attributes;
}

/**
 * Palette, type sizes, spacing, and layout after user global styles merge.
 *
 * @return array<string, mixed>
 */
function liftedpath_rest_design_payload(): array {
	$merged   = WP_Theme_JSON_Resolver::get_merged_data();
	$data     = $merged->get_data();
	$settings = ( isset( $data['settings'] ) && is_array( $data['settings'] ) ) ? $data['settings'] : array();

	$palette = liftedpath_rest_preset_map(
		$settings['color']['palette'] ?? null,
		'color'
	);
	$font_sizes = liftedpath_rest_preset_map(
		$settings['typography']['fontSizes'] ?? null,
		'size'
	);
	$spacing_sizes = liftedpath_rest_preset_map(
		$settings['spacing']['spacingSizes'] ?? null,
		'size'
	);

	$layout = array();

	if ( isset( $settings['layout']['contentSize'] ) && is_string( $settings['layout']['contentSize'] ) ) {
		$layout['contentSize'] = $settings['layout']['contentSize'];
	}

	if ( isset( $settings['layout']['wideSize'] ) && is_string( $settings['layout']['wideSize'] ) ) {
		$layout['wideSize'] = $settings['layout']['wideSize'];
	}

	ksort( $layout );

	$maps = array(
		'palette'      => $palette,
		'fontSizes'    => $font_sizes,
		'spacingSizes' => $spacing_sizes,
		'layout'       => $layout,
	);

	$maps['version'] = liftedpath_rest_version_hash( $maps );

	return $maps;
}

/**
 * Map theme and custom presets to slug => value. Default presets are skipped.
 *
 * @param mixed  $node      Preset node from merged theme JSON.
 * @param string $value_key `color` or `size`.
 * @return array<string, string>
 */
function liftedpath_rest_preset_map( $node, string $value_key ): array {
	if ( ! is_array( $node ) ) {
		return array();
	}

	$groups = array();

	if ( array_is_list( $node ) ) {
		$groups[] = $node;
	} else {
		foreach ( array( 'theme', 'custom' ) as $origin ) {
			if ( isset( $node[ $origin ] ) && is_array( $node[ $origin ] ) ) {
				$groups[] = $node[ $origin ];
			}
		}
	}

	$map = array();

	foreach ( $groups as $group ) {
		foreach ( $group as $preset ) {
			if ( ! is_array( $preset ) || ! isset( $preset['slug'], $preset[ $value_key ] ) ) {
				continue;
			}

			$slug  = (string) $preset['slug'];
			$value = $preset[ $value_key ];

			if ( '' === $slug || ! is_string( $value ) || '' === $value ) {
				continue;
			}

			$map[ $slug ] = $value;
		}
	}

	ksort( $map );

	return $map;
}

/**
 * Hash the design maps. The hash is not part of the input.
 *
 * @param array<string, mixed> $maps Palette, font sizes, spacing, and layout.
 */
function liftedpath_rest_version_hash( array $maps ): string {
	$encoded = wp_json_encode( liftedpath_rest_sort_keys( $maps ) );

	return hash( 'sha256', is_string( $encoded ) ? $encoded : '' );
}

/**
 * Sort associative keys so the version hash is stable.
 *
 * @param mixed $value Any JSON-like value.
 * @return mixed
 */
function liftedpath_rest_sort_keys( $value ) {
	if ( ! is_array( $value ) ) {
		return $value;
	}

	if ( array_is_list( $value ) ) {
		return array_map( 'liftedpath_rest_sort_keys', $value );
	}

	ksort( $value );

	foreach ( $value as $key => $item ) {
		$value[ $key ] = liftedpath_rest_sort_keys( $item );
	}

	return $value;
}

/**
 * Published primary and footer navigation trees.
 */
function liftedpath_rest_navigation(): WP_REST_Response {
	return rest_ensure_response(
		array(
			'primary' => liftedpath_rest_navigation_items( 'primary' ),
			'footer'  => liftedpath_rest_navigation_items( 'footer' ),
		)
	);
}

/**
 * Footer social icons that are switched on and have an https address.
 *
 * @return array<int, array{network: string, label: string, url: string}>
 */
function liftedpath_rest_social_links(): array {
	$networks = array(
		'facebook'  => 'Facebook',
		'instagram' => 'Instagram',
		'linkedin'  => 'LinkedIn',
		'youtube'   => 'YouTube',
		'tiktok'    => 'TikTok',
	);
	$links    = array();

	foreach ( $networks as $network => $label ) {
		if ( ! liftedpath_rest_setting_enabled( $network . '_show' ) ) {
			continue;
		}

		$url = liftedpath_rest_https_url( $network . '_url' );

		if ( '' === $url ) {
			continue;
		}

		$links[] = array(
			'network' => $network,
			'label'   => $label,
			'url'     => $url,
		);
	}

	return $links;
}

/**
 * A Footer show/hide switch. Missing and off both stay hidden.
 */
function liftedpath_rest_setting_enabled( string $name ): bool {
	if ( ! function_exists( 'get_field' ) ) {
		return false;
	}

	$value = get_field( $name, 'option' );

	return true === $value || 1 === $value || '1' === $value;
}

/**
 * A settings address that is https and has a real host.
 */
function liftedpath_rest_https_url( string $name ): string {
	$url = esc_url_raw( liftedpath_rest_setting_text( $name ) );

	if ( '' === $url ) {
		return '';
	}

	$parts = wp_parse_url( $url );

	if ( ! is_array( $parts ) ) {
		return '';
	}

	$scheme = isset( $parts['scheme'] ) ? $parts['scheme'] : '';
	$host   = isset( $parts['host'] ) ? $parts['host'] : '';

	if ( 'https' !== $scheme || ! is_string( $host ) || ! str_contains( $host, '.' ) ) {
		return '';
	}

	if ( str_contains( $host, '%' ) || str_contains( $host, ' ' ) || ! empty( $parts['user'] ) ) {
		return '';
	}

	return $url;
}

/**
 * Public organization facts and the marquee phrases. The form recipient stays off this route.
 */
function liftedpath_rest_settings(): WP_REST_Response {
	$legal_name = liftedpath_rest_setting_text( 'legal_name' );

	return rest_ensure_response(
		array(
			'legal_name'          => $legal_name,
			'short_name'          => liftedpath_rest_setting_text( 'short_name' ),
			'tagline'             => liftedpath_rest_setting_text( 'tagline' ),
			'registration_number' => liftedpath_rest_setting_text( 'registration_number' ),
			'phone'               => liftedpath_rest_setting_text( 'phone' ),
			'email'               => liftedpath_rest_setting_email( 'email' ),
			'postal_address'      => liftedpath_rest_setting_text( 'postal_address' ),
			'copyright_text'      => liftedpath_rest_copyright(),
			'logo'                => liftedpath_rest_logo( $legal_name ),
			'logo_height'         => liftedpath_rest_logo_height(),
			'submenu_arrows'      => liftedpath_rest_setting_enabled( 'submenu_arrows' ),
			'footer_logo'         => liftedpath_rest_footer_logo( $legal_name ),
			'footer_logo_height'  => liftedpath_rest_bounded_number( 'footer_logo_height', 95, 48, 200 ),
			'footer_wordmark'     => liftedpath_rest_footer_wordmark(),
			'location_heading'    => liftedpath_rest_location_heading(),
			'marquee_phrases'     => liftedpath_rest_marquee_phrases(),
			'social'              => liftedpath_rest_social_links(),
		)
	);
}

/**
 * Items from one published navigation post.
 *
 * @return array<int, array<string, mixed>>
 */
function liftedpath_rest_navigation_items( string $slug ): array {
	$posts = get_posts(
		array(
			'post_type'              => 'wp_navigation',
			'name'                   => $slug,
			'post_status'            => 'publish',
			'posts_per_page'         => 1,
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
		)
	);

	if ( array() === $posts || ! $posts[0] instanceof WP_Post ) {
		return array();
	}

	return liftedpath_rest_navigation_tree( parse_blocks( $posts[0]->post_content ), 0 );
}

/**
 * Navigation links and submenus, dropping empty blocks.
 *
 * @param array<int, array<string, mixed>> $blocks Parsed blocks.
 * @return array<int, array<string, mixed>>
 */
function liftedpath_rest_navigation_tree( array $blocks, int $depth ): array {
	if ( $depth > 6 ) {
		return array();
	}

	$items = array();

	foreach ( $blocks as $block ) {
		$name = isset( $block['blockName'] ) ? (string) $block['blockName'] : '';

		if ( 'core/navigation-link' !== $name && 'core/navigation-submenu' !== $name ) {
			continue;
		}

		$attributes = ( isset( $block['attrs'] ) && is_array( $block['attrs'] ) ) ? $block['attrs'] : array();
		$label      = isset( $attributes['label'] ) ? trim( wp_strip_all_tags( (string) $attributes['label'] ) ) : '';

		if ( '' === $label ) {
			continue;
		}

		$url      = isset( $attributes['url'] ) ? (string) $attributes['url'] : '';
		$children = isset( $block['innerBlocks'] ) && is_array( $block['innerBlocks'] ) ? $block['innerBlocks'] : array();

		$path = liftedpath_rest_navigation_path( $url );

		$items[] = array(
			'label'    => $label,
			'path'     => $path,
			'detail'   => liftedpath_rest_menu_detail( $path ),
			'children' => liftedpath_rest_navigation_tree( $children, $depth + 1 ),
		);
	}

	return $items;
}

/**
 * Menu subtitle from the linked page. Editors set it on the page.
 */
function liftedpath_rest_menu_detail( ?string $path ): string {
	if ( ! is_string( $path ) || '' === $path || str_starts_with( $path, 'http://' ) || str_starts_with( $path, 'https://' ) ) {
		return '';
	}

	$slug = trim( $path, '/' );

	if ( '' === $slug || ! function_exists( 'get_field' ) ) {
		return '';
	}

	$page = get_page_by_path( $slug );

	if ( ! $page instanceof WP_Post ) {
		return '';
	}

	$value = get_field( 'menu_subtitle', $page->ID );

	return is_string( $value ) ? trim( $value ) : '';
}

/**
 * A menu URL as a public path, an external URL, or null for a group heading.
 */
function liftedpath_rest_navigation_path( string $url ): ?string {
	$url = trim( $url );

	if ( '' === $url || '#' === $url ) {
		return null;
	}

	$parsed = wp_parse_url( $url );
	$home   = wp_parse_url( home_url( '/' ) );

	if ( ! is_array( $parsed ) || ! is_array( $home ) ) {
		return null;
	}

	$host      = isset( $parsed['host'] ) ? strtolower( (string) $parsed['host'] ) : '';
	$home_host = isset( $home['host'] ) ? strtolower( (string) $home['host'] ) : '';

	if ( '' !== $host && $host !== $home_host ) {
		$scheme = isset( $parsed['scheme'] ) ? strtolower( (string) $parsed['scheme'] ) : '';

		if ( 'http' !== $scheme && 'https' !== $scheme ) {
			return null;
		}

		$external = esc_url_raw( $url );

		return is_string( $external ) && '' !== $external ? $external : null;
	}

	$path = isset( $parsed['path'] ) ? (string) $parsed['path'] : '/';
	$path = '/' . trim( $path, '/' );

	if ( '/' === $path ) {
		return '/';
	}

	if ( ! preg_match( '#^/[a-z0-9]+(?:-[a-z0-9]+)*(?:/[a-z0-9]+(?:-[a-z0-9]+)*)*$#', $path ) ) {
		return null;
	}

	return $path;
}

/**
 * One plain settings value.
 */
function liftedpath_rest_setting_text( string $name ): string {
	if ( ! function_exists( 'get_field' ) ) {
		return '';
	}

	$value = get_field( $name, 'option' );

	return is_string( $value ) ? $value : '';
}

/**
 * Marquee phrases from LiftedPath settings.
 *
 * @return array<int, string>
 */
function liftedpath_rest_marquee_phrases(): array {
	if ( ! function_exists( 'get_field' ) ) {
		return array();
	}

	$rows = get_field( 'marquee_phrases', 'option' );

	if ( ! is_array( $rows ) ) {
		return array();
	}

	$phrases = array();

	foreach ( $rows as $row ) {
		if ( ! is_array( $row ) || ! isset( $row['phrase'] ) || ! is_string( $row['phrase'] ) ) {
			continue;
		}

		$phrase = trim( $row['phrase'] );

		if ( '' !== $phrase ) {
			$phrases[] = $phrase;
		}
	}

	return array_values( $phrases );
}

/**
 * Footer copyright line. The current year is always written in front of the saved text.
 */
function liftedpath_rest_copyright(): string {
	$text = liftedpath_rest_copyright_body( liftedpath_rest_setting_text( 'copyright_text' ) );

	if ( '' === $text ) {
		return '';
	}

	return 'Copyright © ' . wp_date( 'Y' ) . ' ' . $text;
}

/**
 * Copyright wording without a typed year, so the displayed year can move forward.
 */
function liftedpath_rest_copyright_body( string $text ): string {
	$text = trim( $text );
	$text = preg_replace( '/^copyright\s*(?:©|&copy;|&#169;)?\s*\d{4}\s*/i', '', $text );
	$text = preg_replace( '/^copyright\s*(?:©|&copy;|&#169;)?\s*/i', '', (string) $text );

	return trim( (string) $text );
}

/**
 * A settings email, or an empty string when it is not an email.
 */
function liftedpath_rest_setting_email( string $name ): string {
	$email = liftedpath_rest_setting_text( $name );

	return is_email( $email ) ? $email : '';
}

/**
 * A settings number kept inside a range.
 */
function liftedpath_rest_bounded_number( string $name, int $default, int $min, int $max ): int {
	if ( ! function_exists( 'get_field' ) ) {
		return $default;
	}

	$value  = get_field( $name, 'option' );
	$number = is_numeric( $value ) ? (int) $value : $default;

	return max( $min, min( $max, $number ) );
}

/**
 * Header logo height from LiftedPath settings, in pixels.
 */
function liftedpath_rest_logo_height(): int {
	return liftedpath_rest_bounded_number( 'logo_height', 104, 64, 160 );
}

/**
 * Large footer word. Falls back to the short name.
 */
function liftedpath_rest_footer_wordmark(): string {
	$wordmark = liftedpath_rest_setting_text( 'footer_wordmark' );

	if ( '' !== $wordmark ) {
		return $wordmark;
	}

	$short_name = liftedpath_rest_setting_text( 'short_name' );

	return '' !== $short_name ? $short_name : __( 'LiftedPath', 'liftedpath' );
}

/**
 * Heading above the footer address. Empty when an editor clears it.
 */
function liftedpath_rest_location_heading(): string {
	if ( ! function_exists( 'get_field' ) ) {
		return __( 'Our Location', 'liftedpath' );
	}

	$value = get_field( 'location_heading', 'option' );

	if ( is_string( $value ) ) {
		return trim( $value );
	}

	return __( 'Our Location', 'liftedpath' );
}

/**
 * Footer logo, or the header logo when the footer field is empty.
 *
 * @return array<string, int|string>|null
 */
function liftedpath_rest_footer_logo( string $fallback_alt ): ?array {
	$attachment_id = 0;

	if ( function_exists( 'get_field' ) ) {
		$attachment_id = liftedpath_attachment_id( get_field( 'footer_logo', 'option' ) );
	}

	if ( $attachment_id > 0 ) {
		return liftedpath_rest_logo_attachment( $attachment_id, $fallback_alt );
	}

	return liftedpath_rest_logo( $fallback_alt );
}

/**
 * Logo from LiftedPath settings, then the Site Editor custom logo.
 *
 * @return array<string, int|string>|null
 */
function liftedpath_rest_logo( string $fallback_alt ): ?array {
	$attachment_id = 0;

	if ( function_exists( 'get_field' ) ) {
		$attachment_id = liftedpath_attachment_id( get_field( 'logo', 'option' ) );
	}

	if ( $attachment_id <= 0 ) {
		$attachment_id = (int) get_theme_mod( 'custom_logo' );
	}

	if ( $attachment_id <= 0 ) {
		return null;
	}

	return liftedpath_rest_logo_attachment( $attachment_id, $fallback_alt );
}

/**
 * One media-library logo.
 *
 * @return array<string, int|string>|null
 */
function liftedpath_rest_logo_attachment( int $attachment_id, string $fallback_alt ): ?array {
	$image = wp_get_attachment_image_src( $attachment_id, 'full' );

	if ( ! is_array( $image ) || ! is_string( $image[0] ) || (int) $image[1] <= 0 || (int) $image[2] <= 0 ) {
		return null;
	}

	$alt = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );

	if ( ! is_string( $alt ) || '' === $alt ) {
		$alt = '' !== $fallback_alt ? $fallback_alt : __( 'LiftedPath', 'liftedpath' );
	}

	return array(
		'id'     => $attachment_id,
		'url'    => $image[0],
		'alt'    => $alt,
		'width'  => (int) $image[1],
		'height' => (int) $image[2],
	);
}
