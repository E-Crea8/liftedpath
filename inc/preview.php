<?php
/**
 * Draft preview for the Next.js site.
 *
 * The editor link carries a one-time token. Next.js exchanges that token,
 * then reads the draft with an application password. The public page route
 * stays published-only.
 *
 * @package LiftedPath
 */

/**
 * Allow application passwords on the local HTTP hosts WordPress would otherwise reject.
 *
 * @param bool $available Whether WordPress already allows application passwords.
 */
function liftedpath_preview_allow_local_application_passwords( bool $available ): bool {
	if ( $available ) {
		return true;
	}

	$host = wp_parse_url( home_url(), PHP_URL_HOST );

	if ( ! is_string( $host ) ) {
		return false;
	}

	return in_array( strtolower( $host ), array( 'liftedpath.local', 'localhost', '127.0.0.1' ), true );
}
add_filter( 'wp_is_application_passwords_available', 'liftedpath_preview_allow_local_application_passwords' );

/**
 * Register the authenticated preview routes.
 */
function liftedpath_preview_register_routes(): void {
	register_rest_route(
		'liftedpath/v1',
		'/preview/exchange',
		array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => 'liftedpath_preview_exchange',
			'permission_callback' => 'liftedpath_preview_permission',
			'args'                => array(
				'token' => array(
					'required' => true,
					'type'     => 'string',
				),
			),
		)
	);

	register_rest_route(
		'liftedpath/v1',
		'/preview',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'liftedpath_preview_page',
			'permission_callback' => 'liftedpath_preview_permission',
			'args'                => array(
				'path' => array(
					'required'          => true,
					'type'              => 'string',
					'sanitize_callback' => 'liftedpath_rest_sanitize_path',
				),
			),
		)
	);
}
add_action( 'rest_api_init', 'liftedpath_preview_register_routes' );

/**
 * Point the editor preview link at the Next.js draft route.
 *
 * @param string  $preview_link WordPress preview URL.
 * @param WP_Post $post         Page being previewed.
 */
function liftedpath_preview_post_link( string $preview_link, WP_Post $post ): string {
	$url = liftedpath_preview_url_for_post( $post );

	return '' === $url ? $preview_link : $url;
}
add_filter( 'preview_post_link', 'liftedpath_preview_post_link', 10, 2 );

/**
 * Send the block editor's draft preview (the WordPress preview URL) to Next.js.
 *
 * Published pages already open the filtered preview link. A draft's editor
 * button opens this WordPress URL instead.
 */
function liftedpath_preview_redirect_wordpress_preview(): void {
	if ( ! is_preview() ) {
		return;
	}

	$post = get_queried_object();

	if ( ! $post instanceof WP_Post || 'page' !== $post->post_type ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post->ID ) ) {
		return;
	}

	$url = liftedpath_preview_url_for_post( $post );

	if ( '' === $url ) {
		return;
	}

	wp_redirect( $url );
	exit;
}
add_action( 'template_redirect', 'liftedpath_preview_redirect_wordpress_preview' );

/**
 * Exchange a one-time token for the page path.
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function liftedpath_preview_exchange( WP_REST_Request $request ): WP_REST_Response|WP_Error {
	$token = $request->get_param( 'token' );

	if ( ! is_string( $token ) || ! preg_match( '/^[a-f0-9]{64}$/', $token ) ) {
		return liftedpath_preview_invalid();
	}

	$key     = 'lp_prev_' . hash( 'sha256', $token );
	$post_id = (int) get_transient( $key );
	delete_transient( $key );

	if ( $post_id <= 0 ) {
		return liftedpath_preview_invalid();
	}

	$post = get_post( $post_id );

	if ( ! $post instanceof WP_Post || ! liftedpath_preview_page_is_readable( $post ) ) {
		return liftedpath_preview_invalid();
	}

	$response = rest_ensure_response(
		array(
			'path' => liftedpath_rest_public_path( $post ),
		)
	);
	$response->header( 'Cache-Control', 'no-store' );

	return $response;
}

/**
 * Return one page, including an autosave when the editor has one.
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function liftedpath_preview_page( WP_REST_Request $request ): WP_REST_Response|WP_Error {
	$path = $request->get_param( 'path' );

	if ( ! is_string( $path ) ) {
		return new WP_Error(
			'invalid_path',
			__( 'The path is not a page path.', 'liftedpath' ),
			array( 'status' => 400 )
		);
	}

	$page = liftedpath_preview_find_page( $path );

	if ( is_wp_error( $page ) ) {
		return $page;
	}

	$response = rest_ensure_response( liftedpath_rest_page_payload( liftedpath_preview_with_autosave( $page ) ) );
	$response->header( 'Cache-Control', 'no-store' );

	return $response;
}

/**
 * Application password plus the preview secret.
 *
 * @param WP_REST_Request $request Request.
 * @return true|WP_Error
 */
function liftedpath_preview_permission( WP_REST_Request $request ): bool|WP_Error {
	if ( ! current_user_can( 'edit_pages' ) ) {
		return liftedpath_preview_forbidden();
	}

	$given  = (string) $request->get_header( 'x-liftedpath-preview-secret' );
	$secret = liftedpath_preview_secret();

	if ( '' === $given || '' === $secret || ! hash_equals( $secret, $given ) ) {
		return liftedpath_preview_forbidden();
	}

	return true;
}

/**
 * Shared secret checked by the preview routes. Empty until it is set locally.
 */
function liftedpath_preview_secret(): string {
	$secret = get_option( 'liftedpath_preview_secret', '' );

	return is_string( $secret ) ? $secret : '';
}

/**
 * Build a one-time Next.js preview URL, or an empty string when this post cannot be previewed.
 *
 * @param WP_Post $post Page.
 */
function liftedpath_preview_url_for_post( WP_Post $post ): string {
	if ( ! liftedpath_preview_page_is_readable( $post ) || ! is_user_logged_in() || ! current_user_can( 'edit_post', $post->ID ) ) {
		return '';
	}

	$token = bin2hex( random_bytes( 32 ) );
	set_transient( 'lp_prev_' . hash( 'sha256', $token ), (int) $post->ID, 10 * MINUTE_IN_SECONDS );

	return liftedpath_preview_public_base() . '/api/preview?token=' . $token;
}

/**
 * Public Next.js origin used in the editor preview link.
 */
function liftedpath_preview_public_base(): string {
	$candidate = 'http://localhost:3000';

	if ( function_exists( 'liftedpath_forms_get' ) ) {
		$settings = liftedpath_forms_get();

		if ( isset( $settings['public_site_url'] ) && is_string( $settings['public_site_url'] ) && '' !== $settings['public_site_url'] ) {
			$candidate = $settings['public_site_url'];
		}
	}

	$parts  = wp_parse_url( $candidate );
	$scheme = ( is_array( $parts ) && isset( $parts['scheme'] ) ) ? $parts['scheme'] : '';
	$host   = ( is_array( $parts ) && isset( $parts['host'] ) ) ? strtolower( (string) $parts['host'] ) : '';

	if ( ! in_array( $scheme, array( 'http', 'https' ), true ) || '' === $host ) {
		return liftedpath_preview_production_base( 'http://localhost:3000' );
	}

	$base = $scheme . '://' . $host;

	if ( ! empty( $parts['port'] ) ) {
		$base .= ':' . (int) $parts['port'];
	}

	return liftedpath_preview_production_base( $base );
}

/**
 * Point a local or apex address at the public site when this WordPress is the live CMS.
 *
 * The forms setting was still http://localhost:3000, so saves never reached Vercel.
 */
function liftedpath_preview_production_base( string $base ): string {
	$host = strtolower( (string) wp_parse_url( $base, PHP_URL_HOST ) );
	$home = strtolower( (string) wp_parse_url( home_url(), PHP_URL_HOST ) );
	$live = str_contains( $home, 'liftedpathfoundation.org' );
	$local_host = in_array( $host, array( 'localhost', '127.0.0.1' ), true ) || str_ends_with( $host, '.local' );

	if ( $live && $local_host ) {
		return 'https://www.liftedpathfoundation.org';
	}

	if ( 'liftedpathfoundation.org' === $host ) {
		return 'https://www.liftedpathfoundation.org';
	}

	return $base;
}

/**
 * Store the corrected public site URL so the forms screen matches the webhook.
 */
function liftedpath_correct_public_site_url(): void {
	if ( ! function_exists( 'liftedpath_forms_get' ) ) {
		return;
	}

	$settings  = liftedpath_forms_get();
	$current   = isset( $settings['public_site_url'] ) && is_string( $settings['public_site_url'] ) ? $settings['public_site_url'] : '';
	$corrected = liftedpath_preview_public_base();
	$host      = strtolower( (string) wp_parse_url( $current, PHP_URL_HOST ) );
	$wrong     = in_array( $host, array( 'localhost', '127.0.0.1', 'liftedpathfoundation.org' ), true ) || str_ends_with( $host, '.local' );

	if ( ! $wrong || 'https://www.liftedpathfoundation.org' !== $corrected || untrailingslashit( $current ) === $corrected ) {
		return;
	}

	$saved = get_option( 'liftedpath_forms_settings', array() );
	if ( ! is_array( $saved ) ) {
		$saved = array();
	}

	$saved['public_site_url'] = $corrected;
	update_option( 'liftedpath_forms_settings', $saved, false );
}
add_action( 'init', 'liftedpath_correct_public_site_url', 20 );

/**
 * Page statuses an editor may preview.
 *
 * @param string $status Post status.
 */
function liftedpath_preview_status_allowed( string $status ): bool {
	return in_array( $status, array( 'publish', 'draft', 'pending', 'future' ), true );
}

/**
 * Whether this post can be shown on the preview route.
 *
 * @param WP_Post $post Post.
 */
function liftedpath_preview_page_is_readable( WP_Post $post ): bool {
	return 'page' === $post->post_type
		&& liftedpath_preview_status_allowed( $post->post_status )
		&& '' === $post->post_password;
}

/**
 * Find a previewable page by its public path.
 *
 * @param string $path Public path.
 * @return WP_Post|WP_Error
 */
function liftedpath_preview_find_page( string $path ): WP_Post|WP_Error {
	$post = null;

	if ( '/' === $path ) {
		if ( 'page' === get_option( 'show_on_front' ) ) {
			$front = get_post( (int) get_option( 'page_on_front' ) );
			$post  = $front instanceof WP_Post ? $front : null;
		}
	} else {
		$post = liftedpath_preview_page_by_path( ltrim( $path, '/' ) );
	}

	if ( ! $post instanceof WP_Post || ! liftedpath_preview_page_is_readable( $post ) ) {
		return new WP_Error(
			'page_not_found',
			__( 'No published page exists at that path.', 'liftedpath' ),
			array( 'status' => 404 )
		);
	}

	return $post;
}

/**
 * Walk a public path, including unpublished pages.
 *
 * @param string $path Path without the leading slash.
 */
function liftedpath_preview_page_by_path( string $path ): ?WP_Post {
	$parent = 0;
	$found  = null;

	foreach ( explode( '/', $path ) as $segment ) {
		$slug = sanitize_title( $segment );

		if ( '' === $slug ) {
			return null;
		}

		$posts = get_posts(
			array(
				'name'             => $slug,
				'post_type'        => 'page',
				'post_status'      => array( 'publish', 'draft', 'pending', 'future' ),
				'post_parent'      => $parent,
				'posts_per_page'   => 1,
				'orderby'          => 'ID',
				'order'            => 'ASC',
				'no_found_rows'    => true,
				'suppress_filters' => true,
			)
		);

		if ( array() === $posts || ! $posts[0] instanceof WP_Post ) {
			return null;
		}

		$found  = $posts[0];
		$parent = (int) $found->ID;
	}

	return $found;
}

/**
 * Overlay the latest autosave onto a copy of the saved page.
 *
 * The copy keeps the saved page ID, so the public path does not become the autosave slug.
 *
 * @param WP_Post $page Saved page.
 */
function liftedpath_preview_with_autosave( WP_Post $page ): WP_Post {
	if ( ! function_exists( 'wp_get_post_autosave' ) ) {
		return $page;
	}

	$autosave = wp_get_post_autosave( $page->ID );

	if ( ! $autosave instanceof WP_Post ) {
		return $page;
	}

	$display                     = clone $page;
	$display->post_title         = $autosave->post_title;
	$display->post_content       = $autosave->post_content;
	$display->post_excerpt       = $autosave->post_excerpt;
	$display->post_modified      = $autosave->post_modified;
	$display->post_modified_gmt  = $autosave->post_modified_gmt;

	return $display;
}

/**
 * Token was missing, expired, or already used.
 */
function liftedpath_preview_invalid(): WP_Error {
	return new WP_Error(
		'preview_not_found',
		__( 'This preview link is not valid.', 'liftedpath' ),
		array( 'status' => 404 )
	);
}

/**
 * Preview request was not authenticated.
 */
function liftedpath_preview_forbidden(): WP_Error {
	return new WP_Error(
		'preview_forbidden',
		__( 'This preview link is not valid.', 'liftedpath' ),
		array( 'status' => 401 )
	);
}
