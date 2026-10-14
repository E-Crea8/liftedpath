<?php
/**
 * Shared block controls: visibility, layout, inserter previews, and the
 * page-hero breadcrumb that is separate from the eyebrow.
 *
 * @package LiftedPath
 */

/**
 * Classes for the public layout choices and the editor's hidden state.
 */
function liftedpath_block_state_classes(): string {
	if ( ! function_exists( 'get_field' ) ) {
		return '';
	}

	$classes = array();
	$show    = get_field( 'show' );

	if ( null !== $show && '' !== $show && ! liftedpath_block_flag( $show, true ) ) {
		$classes[] = 'liftedpath-is-hidden';
	}

	$align = get_field( 'heading_align' );

	if ( is_string( $align ) && in_array( $align, array( 'left', 'center', 'right' ), true ) ) {
		$classes[] = 'is-heading-' . $align;
	}

	if ( 'below' === get_field( 'heading_place' ) ) {
		$classes[] = 'is-heading-below';
	}

	if ( 'reverse' === get_field( 'column_order' ) ) {
		$classes[] = 'is-reversed';
	}

	$weight = get_field( 'heading_weight' );
	$weight = is_scalar( $weight ) ? (string) $weight : '';

	if ( ! in_array( $weight, array( '500', '600', '700', '800' ), true ) ) {
		$weight = '700';
	}

	$classes[] = 'is-weight-' . $weight;

	return implode( ' ', $classes );
}

/**
 * The editor canvas and the inserter still render a hidden block.
 */
function liftedpath_block_editor_request(): bool {
	if ( is_admin() ) {
		return true;
	}

	if ( ! defined( 'REST_REQUEST' ) || ! REST_REQUEST ) {
		return false;
	}

	$route = '';

	if ( isset( $GLOBALS['wp'] ) && $GLOBALS['wp'] instanceof WP && isset( $GLOBALS['wp']->query_vars['rest_route'] ) ) {
		$route = (string) $GLOBALS['wp']->query_vars['rest_route'];
	}

	return str_contains( $route, '/block-renderer/' );
}

/**
 * Drop a hidden block from public HTML. A missing switch stays visible.
 *
 * @param string               $content Rendered block.
 * @param array<string, mixed> $block   Parsed block.
 */
function liftedpath_hide_block_output( string $content, array $block ): string {
	if ( liftedpath_block_editor_request() ) {
		return $content;
	}

	$attrs = ( isset( $block['attrs'] ) && is_array( $block['attrs'] ) ) ? $block['attrs'] : array();

	if ( array_key_exists( 'liftedpathShow', $attrs ) && ! liftedpath_block_flag( $attrs['liftedpathShow'], true ) ) {
		return '';
	}

	$data = ( isset( $attrs['data'] ) && is_array( $attrs['data'] ) ) ? $attrs['data'] : array();

	if ( array_key_exists( 'show', $data ) && ! liftedpath_block_flag( $data['show'], true ) ) {
		return '';
	}

	return $content;
}
add_filter( 'render_block', 'liftedpath_hide_block_output', 8, 2 );

/**
 * One field location group for every LiftedPath block, including blocks added later.
 *
 * @return array<int, array<int, array<string, string>>>
 */
function liftedpath_block_field_locations(): array {
	if ( ! function_exists( 'liftedpath_block_slugs' ) ) {
		return array();
	}

	$locations = array();

	foreach ( liftedpath_block_slugs() as $slug ) {
		$locations[] = array(
			array(
				'param'    => 'block',
				'operator' => '==',
				'value'    => 'liftedpath/' . $slug,
			),
		);
	}

	return $locations;
}

/**
 * Show or hide, on every LiftedPath block.
 */
function liftedpath_register_block_visibility_fields(): void {
	if ( ! function_exists( 'acf_add_local_field_group' ) || ! function_exists( 'liftedpath_block_slugs' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'                   => 'group_liftedpath_block_visibility',
			'title'                 => 'Visibility',
			'fields'                => array(
				array(
					'key'           => 'field_liftedpath_block_show',
					'label'         => 'Show on the site',
					'name'          => 'show',
					'type'          => 'true_false',
					'instructions'  => 'Hide removes this block from the public site. It stays in the editor so it can be shown again.',
					'default_value' => 1,
					'ui'            => 1,
					'ui_on_text'    => 'Show',
					'ui_off_text'   => 'Hide',
				),
			),
			'location'              => liftedpath_block_field_locations(),
			'menu_order'            => -1,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'active'                => true,
			'show_in_rest'          => 1,
		)
	);
}
add_action( 'acf/init', 'liftedpath_register_block_visibility_fields', 20 );

/**
 * Heading position and column direction. An empty or Design value keeps the current layout.
 */
function liftedpath_register_block_layout_fields(): void {
	if ( ! function_exists( 'acf_add_local_field_group' ) || ! function_exists( 'liftedpath_block_slugs' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'                   => 'group_liftedpath_block_layout',
			'title'                 => 'Layout',
			'fields'                => array(
				array(
					'key'           => 'field_liftedpath_block_heading_align',
					'label'         => 'Heading position',
					'name'          => 'heading_align',
					'type'          => 'button_group',
					'instructions'  => 'Move the heading to the left, center, or right. Design keeps the alignment this block already uses.',
					'choices'       => array(
						'design' => 'Design',
						'left'   => 'Left',
						'center' => 'Center',
						'right'  => 'Right',
					),
					'default_value' => 'design',
					'layout'        => 'horizontal',
					'return_format' => 'value',
				),
				array(
					'key'           => 'field_liftedpath_block_heading_place',
					'label'         => 'Heading placement',
					'name'          => 'heading_place',
					'type'          => 'button_group',
					'instructions'  => 'Below moves the heading under the cards on pillars, team, programs, process, actions, and questions. Two-column sections use Column order instead. Drag a repeater row to reorder people, cards, or steps.',
					'choices'       => array(
						'design' => 'Design',
						'above'  => 'Above',
						'below'  => 'Below',
					),
					'default_value' => 'design',
					'layout'        => 'horizontal',
					'return_format' => 'value',
				),
				array(
					'key'           => 'field_liftedpath_block_column_order',
					'label'         => 'Column order',
					'name'          => 'column_order',
					'type'          => 'button_group',
					'instructions'  => 'Right to left swaps the two columns on story, beliefs, about, stewardship, contact, donate, and goals, and reverses the team grid. Drag a repeater row to move one item.',
					'choices'       => array(
						'design'  => 'Design',
						'normal'  => 'Left to right',
						'reverse' => 'Right to left',
					),
					'default_value' => 'design',
					'layout'        => 'horizontal',
					'return_format' => 'value',
				),
				array(
					'key'           => 'field_liftedpath_block_heading_weight',
					'label'         => 'Heading weight',
					'name'          => 'heading_weight',
					'type'          => 'button_group',
					'instructions'  => 'Sets how strong this block’s headings are. Strong is the default. It applies to the heading and to titles inside the block.',
					'choices'       => array(
						'500' => 'Regular',
						'600' => 'Medium',
						'700' => 'Strong',
						'800' => 'Extra strong',
					),
					'default_value' => '700',
					'allow_null'    => 0,
					'layout'        => 'horizontal',
					'return_format' => 'value',
				),
			),
			'location'              => liftedpath_block_field_locations(),
			'menu_order'            => 40,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'active'                => true,
			'show_in_rest'          => 1,
		)
	);
}
add_action( 'acf/init', 'liftedpath_register_block_layout_fields', 20 );

/**
 * Color, photograph, or video behind every LiftedPath block.
 */
function liftedpath_register_block_background_fields(): void {
	if ( ! function_exists( 'acf_add_local_field_group' ) || ! function_exists( 'liftedpath_block_slugs' ) ) {
		return;
	}

	$kind = 'field_liftedpath_block_background_kind';

	acf_add_local_field_group(
		array(
			'key'                   => 'group_liftedpath_block_background',
			'title'                 => 'Background',
			'fields'                => array(
				array(
					'key'           => $kind,
					'label'         => 'Background',
					'name'          => 'background_kind',
					'type'          => 'button_group',
					'instructions'  => 'Design keeps the background this block already uses. Color, Image, and Video replace it.',
					'choices'       => array(
						'design' => 'Design',
						'color'  => 'Color',
						'image'  => 'Image',
						'video'  => 'Video',
					),
					'default_value' => 'design',
					'layout'        => 'horizontal',
					'return_format' => 'value',
				),
				array(
					'key'               => 'field_liftedpath_block_background_variant',
					'label'             => 'Color variant',
					'name'              => 'background_variant',
					'type'              => 'select',
					'instructions'      => 'A design color, or Custom for a color of your own. On a photograph or video this color tints the media.',
					'choices'           => array(
						'paper'   => 'Paper',
						'surface' => 'Surface',
						'teal'    => 'Teal',
						'deep'    => 'Deep',
						'gold'    => 'Gold',
						'bronze'  => 'Bronze',
						'sand'    => 'Sand',
						'custom'  => 'Custom',
					),
					'default_value'     => 'paper',
					'return_format'     => 'value',
					'conditional_logic' => array(
						array(
							array(
								'field'    => $kind,
								'operator' => '!=',
								'value'    => 'design',
							),
						),
					),
				),
				array(
					'key'               => 'field_liftedpath_block_background_color',
					'label'             => 'Custom color',
					'name'              => 'background_color',
					'type'              => 'color_picker',
					'default_value'     => '#043C42',
					'conditional_logic' => array(
						array(
							array(
								'field'    => $kind,
								'operator' => '!=',
								'value'    => 'design',
							),
							array(
								'field'    => 'field_liftedpath_block_background_variant',
								'operator' => '==',
								'value'    => 'custom',
							),
						),
					),
				),
				array(
					'key'               => 'field_liftedpath_block_background_opacity',
					'label'             => 'Opacity',
					'name'              => 'background_opacity',
					'type'              => 'range',
					'instructions'      => '100 is solid. On a photograph or video, lower this so the media stays visible.',
					'default_value'     => 100,
					'min'               => 0,
					'max'               => 100,
					'step'              => 1,
					'append'            => '%',
					'conditional_logic' => array(
						array(
							array(
								'field'    => $kind,
								'operator' => '!=',
								'value'    => 'design',
							),
						),
					),
				),
				array(
					'key'               => 'field_liftedpath_block_background_image',
					'label'             => 'Background image',
					'name'              => 'background_image',
					'type'              => 'image',
					'return_format'     => 'id',
					'preview_size'      => 'medium',
					'library'           => 'all',
					'conditional_logic' => array(
						array(
							array(
								'field'    => $kind,
								'operator' => '==',
								'value'    => 'image',
							),
						),
					),
				),
				array(
					'key'               => 'field_liftedpath_block_background_video_url',
					'label'             => 'Video address',
					'name'              => 'background_video_url',
					'type'              => 'text',
					'instructions'      => 'An https address of an MP4 or WebM. This is used before an uploaded file.',
					'conditional_logic' => array(
						array(
							array(
								'field'    => $kind,
								'operator' => '==',
								'value'    => 'video',
							),
						),
					),
				),
				array(
					'key'               => 'field_liftedpath_block_background_video',
					'label'             => 'Video file',
					'name'              => 'background_video',
					'type'              => 'file',
					'return_format'     => 'id',
					'library'           => 'all',
					'mime_types'        => 'mp4,webm',
					'conditional_logic' => array(
						array(
							array(
								'field'    => $kind,
								'operator' => '==',
								'value'    => 'video',
							),
						),
					),
				),
			),
			'location'              => liftedpath_block_field_locations(),
			'menu_order'            => 30,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'active'                => true,
			'show_in_rest'          => 1,
		)
	);
}
add_action( 'acf/init', 'liftedpath_register_block_background_fields', 20 );

/**
 * Keep the breadcrumb field on the page hero even before a field-group sync.
 *
 * @param array<int, array<string, mixed>> $fields Fields already loaded.
 * @param array<string, mixed>             $parent Field group.
 * @return array<int, array<string, mixed>>
 */
function liftedpath_page_hero_fields( $fields, $parent ) {
	static $adding = false;

	if ( $adding || ! is_array( $fields ) || ! is_array( $parent ) || ( $parent['key'] ?? '' ) !== 'group_liftedpath_page_hero' ) {
		return is_array( $fields ) ? $fields : array();
	}

	$updated = array();
	$present = false;

	foreach ( $fields as $field ) {
		if ( is_array( $field ) && ( $field['key'] ?? '' ) === 'field_liftedpath_page_hero_breadcrumb_current' ) {
			$present = true;
		}
	}

	$adding = true;

	foreach ( $fields as $field ) {
		if ( ! is_array( $field ) ) {
			$updated[] = $field;
			continue;
		}

		if ( ( $field['key'] ?? '' ) === 'field_liftedpath_page_hero_eyebrow' ) {
			$field['instructions'] = __( 'The small line above the heading.', 'liftedpath' );
		}

		if ( ! $present && ( $field['key'] ?? '' ) === 'field_liftedpath_page_hero_heading' ) {
			$crumb           = liftedpath_page_hero_breadcrumb_field();
			$crumb['parent'] = $parent['key'];
			$updated[]       = function_exists( 'acf_validate_field' ) ? acf_validate_field( $crumb ) : $crumb;
			$present         = true;
		}

		$updated[] = $field;
	}

	$adding = false;

	return $updated;
}
add_filter( 'acf/load_fields', 'liftedpath_page_hero_fields', 15, 2 );

/**
 * Current-page breadcrumb. It does not follow the eyebrow.
 *
 * @return array<string, mixed>
 */
function liftedpath_page_hero_breadcrumb_field(): array {
	return array(
		'key'          => 'field_liftedpath_page_hero_breadcrumb_current',
		'label'        => 'Breadcrumb',
		'name'         => 'breadcrumb_current',
		'type'         => 'text',
		'instructions' => 'The current page in the trail. Clearing the eyebrow leaves this in place.',
	);
}

/**
 * Copy an existing eyebrow into the breadcrumb once, then leave the two fields independent.
 *
 * @param array<int, array<string, mixed>> $blocks Parsed blocks.
 */
function liftedpath_migrate_breadcrumb_blocks( array &$blocks ): bool {
	$changed = false;

	foreach ( $blocks as &$block ) {
		if ( ! empty( $block['innerBlocks'] ) && is_array( $block['innerBlocks'] ) ) {
			if ( liftedpath_migrate_breadcrumb_blocks( $block['innerBlocks'] ) ) {
				$changed = true;
			}
		}

		if ( ( $block['blockName'] ?? '' ) !== 'liftedpath/page-hero' ) {
			continue;
		}

		if ( ! isset( $block['attrs'] ) || ! is_array( $block['attrs'] ) ) {
			$block['attrs'] = array();
		}

		if ( ! isset( $block['attrs']['data'] ) || ! is_array( $block['attrs']['data'] ) ) {
			continue;
		}

		$data = $block['attrs']['data'];

		if ( array_key_exists( 'breadcrumb_current', $data ) ) {
			continue;
		}

		$eyebrow                      = ( isset( $data['eyebrow'] ) && is_string( $data['eyebrow'] ) ) ? $data['eyebrow'] : '';
		$data['breadcrumb_current']   = $eyebrow;
		$data['_breadcrumb_current']  = 'field_liftedpath_page_hero_breadcrumb_current';
		$block['attrs']['data']       = $data;
		$changed                      = true;
	}

	return $changed;
}

/**
 * Run the breadcrumb split once for pages that already use the eyebrow as the crumb.
 */
function liftedpath_migrate_breadcrumb_current(): void {
	if ( get_option( 'liftedpath_breadcrumb_current_migrated' ) ) {
		return;
	}

	try {
		liftedpath_migrate_breadcrumb_current_run();
	} catch ( Throwable $exception ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
		unset( $exception );
	}
}

/**
 * Rewrite stored page heroes once. Failures stay off the public request.
 */
function liftedpath_migrate_breadcrumb_current_run(): void {

	global $wpdb;

	$ids = $wpdb->get_col(
		$wpdb->prepare(
			"SELECT ID FROM {$wpdb->posts} WHERE post_content LIKE %s AND post_status != %s",
			'%liftedpath/page-hero%',
			'auto-draft'
		)
	);

	if ( ! is_array( $ids ) ) {
		return;
	}

	foreach ( $ids as $id ) {
		$post = get_post( (int) $id );

		if ( ! $post instanceof WP_Post || ! is_string( $post->post_content ) || ! str_contains( $post->post_content, 'liftedpath/page-hero' ) ) {
			continue;
		}

		$blocks = parse_blocks( $post->post_content );

		if ( ! liftedpath_migrate_breadcrumb_blocks( $blocks ) ) {
			continue;
		}

		$content = serialize_blocks( $blocks );

		if ( ! is_string( $content ) || '' === $content || ! str_contains( $content, 'breadcrumb_current' ) ) {
			continue;
		}

		$wpdb->update(
			$wpdb->posts,
			array( 'post_content' => $content ),
			array( 'ID' => (int) $id ),
			array( '%s' ),
			array( '%d' )
		);
		clean_post_cache( (int) $id );
	}

	update_option( 'liftedpath_breadcrumb_current_migrated', 1, false );
}
add_action( 'init', 'liftedpath_migrate_breadcrumb_current', 30 );

/**
 * @param array<string, mixed> ...$parts Field pairs.
 * @return array<string, mixed>
 */
function liftedpath_example_merge( array ...$parts ): array {
	$data = array();

	foreach ( $parts as $part ) {
		foreach ( $part as $key => $value ) {
			$data[ $key ] = $value;
		}
	}

	return $data;
}

/**
 * @param mixed $value Field value.
 * @return array<string, mixed>
 */
function liftedpath_example_pair( string $name, string $key, $value ): array {
	return array(
		$name          => $value,
		'_' . $name    => $key,
	);
}

/**
 * @param array<string, string>        $subkeys Subfield keys keyed by name.
 * @param array<int, array<string, mixed>> $rows Rows.
 * @return array<string, mixed>
 */
function liftedpath_example_repeater( string $name, string $key, array $subkeys, array $rows ): array {
	$data           = array(
		$name       => count( $rows ),
		'_' . $name => $key,
	);

	foreach ( $rows as $index => $row ) {
		foreach ( $row as $sub => $value ) {
			if ( ! isset( $subkeys[ $sub ] ) ) {
				continue;
			}

			$data[ "{$name}_{$index}_{$sub}" ]   = $value;
			$data[ "_{$name}_{$index}_{$sub}" ] = $subkeys[ $sub ];
		}
	}

	return $data;
}

/**
 * Attachment ID for sample content, by media file basename without extension.
 */
function liftedpath_example_media_id( string $slug ): int {
	static $cache = array();

	if ( isset( $cache[ $slug ] ) ) {
		return $cache[ $slug ];
	}

	$posts = get_posts(
		array(
			'post_type'              => 'attachment',
			'post_status'            => 'inherit',
			'posts_per_page'         => 1,
			'fields'                 => 'ids',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'meta_query'             => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- editor samples only.
				array(
					'key'     => '_wp_attached_file',
					'value'   => $slug . '.',
					'compare' => 'LIKE',
				),
			),
		)
	);

	if ( array() === $posts ) {
		$by_title = get_page_by_title( $slug, OBJECT, 'attachment' );
		$posts    = $by_title instanceof WP_Post ? array( (int) $by_title->ID ) : array();
	}

	$cache[ $slug ] = ( isset( $posts[0] ) ) ? (int) $posts[0] : 0;

	return $cache[ $slug ];
}

/**
 * Published IA pages used as the source of insert samples, in preference order.
 *
 * @return array<int, WP_Post>
 */
function liftedpath_example_source_pages(): array {
	$pages = array();
	$front = (int) get_option( 'page_on_front' );

	if ( $front > 0 ) {
		$home = get_post( $front );
		if ( $home instanceof WP_Post ) {
			$pages[] = $home;
		}
	}

	foreach ( array( 'about', 'what-we-do', 'learning', 'impact', 'contact', 'donate' ) as $slug ) {
		$page = get_page_by_path( $slug );
		if ( $page instanceof WP_Post ) {
			$pages[] = $page;
		}
	}

	return $pages;
}

/**
 * First published occurrence of each LiftedPath block on the public IA pages.
 *
 * @return array<string, array{data: array<string, mixed>, inner: array<int, array<string, mixed>>}>
 */
function liftedpath_example_site_catalog(): array {
	static $catalog = null;

	if ( is_array( $catalog ) ) {
		return $catalog;
	}

	$catalog = array();
	$needed  = array();

	foreach ( liftedpath_block_slugs() as $slug ) {
		$needed[ 'liftedpath/' . $slug ] = true;
	}

	$walk = static function ( array $blocks ) use ( &$walk, &$catalog, &$needed ): void {
		foreach ( $blocks as $block ) {
			if ( ! is_array( $block ) ) {
				continue;
			}

			$name = isset( $block['blockName'] ) ? (string) $block['blockName'] : '';

			if ( isset( $needed[ $name ] ) && ! isset( $catalog[ $name ] ) ) {
				$data = ( isset( $block['attrs']['data'] ) && is_array( $block['attrs']['data'] ) ) ? $block['attrs']['data'] : array();
				$inner = array();

				if ( ! empty( $block['innerBlocks'] ) && is_array( $block['innerBlocks'] ) ) {
					foreach ( $block['innerBlocks'] as $child ) {
						if ( ! is_array( $child ) || empty( $child['blockName'] ) ) {
							continue;
						}

						$child_data = ( isset( $child['attrs']['data'] ) && is_array( $child['attrs']['data'] ) ) ? $child['attrs']['data'] : array();
						$inner[]    = array(
							'name' => (string) $child['blockName'],
							'data' => $child_data,
						);
					}
				}

				if ( array() !== $data || array() !== $inner ) {
					$catalog[ $name ] = array(
						'data'  => $data,
						'inner' => $inner,
					);
					unset( $needed[ $name ] );
				}
			}

			if ( array() === $needed ) {
				return;
			}

			if ( ! empty( $block['innerBlocks'] ) && is_array( $block['innerBlocks'] ) ) {
				$walk( $block['innerBlocks'] );
			}
		}
	};

	foreach ( liftedpath_example_source_pages() as $page ) {
		$walk( parse_blocks( (string) $page->post_content ) );

		if ( array() === $needed ) {
			break;
		}
	}

	return $catalog;
}

/**
 * Ensure Strong heading weight is present on sample data.
 *
 * @param array<string, mixed> $data ACF block data.
 * @return array<string, mixed>
 */
function liftedpath_example_with_weight( array $data ): array {
	if ( ! isset( $data['heading_weight'] ) || '' === $data['heading_weight'] || null === $data['heading_weight'] ) {
		$data['heading_weight']  = '700';
		$data['_heading_weight'] = 'field_liftedpath_block_heading_weight';
	}

	return $data;
}

/**
 * Hand-written samples only for blocks that are not yet on a published IA page.
 *
 * @return array<string, mixed>
 */
function liftedpath_example_fallback_data( string $name ): array {
	$classroom = liftedpath_example_media_id( 'classroom' );
	$students  = liftedpath_example_media_id( 'students' );
	$skills    = liftedpath_example_media_id( 'skills' );
	$season    = liftedpath_example_media_id( 'season' );
	$orphanage = liftedpath_example_media_id( 'orphanage' );

	$fallbacks = array(
		'liftedpath/team' => liftedpath_example_merge(
			liftedpath_example_pair( 'eyebrow', 'field_liftedpath_team_eyebrow', 'Our team' ),
			liftedpath_example_pair( 'heading', 'field_liftedpath_team_heading', 'People carrying the pathway.' ),
			liftedpath_example_pair( 'heading_weight', 'field_liftedpath_block_heading_weight', '700' ),
			liftedpath_example_repeater(
				'members',
				'field_liftedpath_team_members',
				array(
					'image'    => 'field_liftedpath_team_member_image',
					'name'     => 'field_liftedpath_team_member_name',
					'position' => 'field_liftedpath_team_member_position',
				),
				array(
					array(
						'image'    => $classroom > 0 ? $classroom : '',
						'name'     => 'Name',
						'position' => 'Executive Director',
					),
					array(
						'image'    => $students > 0 ? $students : '',
						'name'     => 'Name',
						'position' => 'Programs Lead',
					),
					array(
						'image'    => $skills > 0 ? $skills : '',
						'name'     => 'Name',
						'position' => 'Learning Coordinator',
					),
					array(
						'image'    => $season > 0 ? $season : ( $orphanage > 0 ? $orphanage : '' ),
						'name'     => 'Name',
						'position' => 'Partnerships',
					),
				)
			)
		),
		'liftedpath/stat' => liftedpath_example_merge(
			liftedpath_example_pair( 'figure', 'field_liftedpath_stat_figure', '128' ),
			liftedpath_example_pair( 'label', 'field_liftedpath_stat_label', 'Scholars' ),
			liftedpath_example_pair( 'detail', 'field_liftedpath_stat_detail', 'In school this year' )
		),
	);

	return $fallbacks[ $name ] ?? liftedpath_example_pair( 'heading', '', 'Section heading' );
}

/**
 * When the marquee block leaves phrases empty, the public site uses Settings phrases.
 *
 * @param array<string, mixed> $data Block data.
 * @return array<string, mixed>
 */
function liftedpath_example_fill_marquee( array $data ): array {
	$has_phrase = false;

	foreach ( $data as $key => $value ) {
		if ( is_string( $key ) && preg_match( '/^phrases_\d+_phrase$/', $key ) && is_string( $value ) && '' !== trim( $value ) ) {
			$has_phrase = true;
			break;
		}
	}

	if ( $has_phrase ) {
		return $data;
	}

	$rows = function_exists( 'get_field' ) ? get_field( 'marquee_phrases', 'option' ) : null;

	if ( ! is_array( $rows ) || array() === $rows ) {
		$rows = array(
			array( 'phrase' => 'Lifting potential' ),
			array( 'phrase' => 'Inspiring purpose' ),
			array( 'phrase' => 'Creating futures' ),
		);
	}

	return array_merge(
		$data,
		liftedpath_example_repeater(
			'phrases',
			'field_liftedpath_marquee_block_phrases',
			array( 'phrase' => 'field_liftedpath_marquee_block_phrase' ),
			array_values(
				array_filter(
					array_map(
						static function ( $row ) {
							if ( ! is_array( $row ) || empty( $row['phrase'] ) || ! is_string( $row['phrase'] ) ) {
								return null;
							}

							return array( 'phrase' => $row['phrase'] );
						},
						$rows
					)
				)
			)
		)
	);
}

/**
 * Sample content so the inserter and a new insert draw the block as it appears on the site.
 *
 * @return array<string, mixed>
 */
function liftedpath_block_example( string $name ): array {
	$catalog = liftedpath_example_site_catalog();
	$entry   = $catalog[ $name ] ?? null;
	$data    = is_array( $entry ) ? liftedpath_example_with_weight( $entry['data'] ) : liftedpath_example_with_weight( liftedpath_example_fallback_data( $name ) );

	if ( 'liftedpath/marquee' === $name ) {
		$data = liftedpath_example_fill_marquee( $data );
	}

	$example = array(
		'viewportWidth' => 1100,
		'attributes'    => array(
			'mode' => 'preview',
			'data' => $data,
		),
	);

	if ( 'liftedpath/stat-group' === $name ) {
		$inner = ( is_array( $entry ) && ! empty( $entry['inner'] ) ) ? $entry['inner'] : array();

		if ( array() === $inner ) {
			$inner = array(
				array(
					'name' => 'liftedpath/stat',
					'data' => liftedpath_example_fallback_data( 'liftedpath/stat' ),
				),
			);
		}

		$example['innerBlocks'] = array();

		foreach ( $inner as $child ) {
			$child_name = isset( $child['name'] ) ? (string) $child['name'] : 'liftedpath/stat';
			$child_data = isset( $child['data'] ) && is_array( $child['data'] ) ? $child['data'] : liftedpath_example_fallback_data( 'liftedpath/stat' );

			$example['innerBlocks'][] = array(
				'name'       => $child_name,
				'attributes' => array(
					'mode' => 'preview',
					'data' => $child_data,
				),
			);
		}

		$example['attributes']['data'] = array();
	}

	return $example;
}

/**
 * Inserter previews for LiftedPath blocks, and a visibility attribute for every other block.
 *
 * @param array<string, mixed> $args Block type arguments.
 * @return array<string, mixed>
 */
function liftedpath_register_block_type_args( array $args, string $name ): array {
	if ( str_starts_with( $name, 'liftedpath/' ) ) {
		$args['example'] = liftedpath_block_example( $name );
		return $args;
	}

	if ( ! isset( $args['attributes'] ) || ! is_array( $args['attributes'] ) ) {
		$args['attributes'] = array();
	}

	$args['attributes']['liftedpathShow'] = array(
		'type'    => 'boolean',
		'default' => true,
	);

	return $args;
}
add_filter( 'register_block_type_args', 'liftedpath_register_block_type_args', 10, 2 );

/**
 * Team card colors, even before a field-group sync. Link fields stay out of the editor.
 *
 * @param array<int, array<string, mixed>> $fields Fields already loaded.
 * @param array<string, mixed>             $parent Field group or repeater.
 * @return array<int, array<string, mixed>>
 */
function liftedpath_team_fields( $fields, $parent ) {
	static $adding = false;

	if ( $adding || ! is_array( $fields ) || ! is_array( $parent ) ) {
		return is_array( $fields ) ? $fields : array();
	}

	$parent_key = $parent['key'] ?? '';

	if ( 'group_liftedpath_team' !== $parent_key && 'field_liftedpath_team_members' !== $parent_key ) {
		return $fields;
	}

	$drop = array(
		'field_liftedpath_team_link_color'   => true,
		'field_liftedpath_team_link_label'   => true,
		'field_liftedpath_team_member_link'  => true,
	);

	$adding  = true;
	$updated = array();
	$seen    = array();

	foreach ( $fields as $field ) {
		if ( ! is_array( $field ) ) {
			$updated[] = $field;
			continue;
		}

		$key = $field['key'] ?? '';

		if ( isset( $drop[ $key ] ) || in_array( $field['name'] ?? '', array( 'link_color', 'link_label', 'link' ), true ) ) {
			continue;
		}

		$seen[ $key ] = true;
		$updated[]    = $field;
	}

	if ( 'field_liftedpath_team_members' === $parent_key ) {
		$adding = false;
		return $updated;
	}

	$extras = array(
		array(
			'key'            => 'field_liftedpath_team_overlay',
			'label'          => 'Photo overlay',
			'name'           => 'overlay_color',
			'type'           => 'color_picker',
			'instructions'   => 'The color that fades up over the photograph. Leave blank for the deep teal.',
			'wrapper'        => array( 'width' => '33' ),
			'enable_opacity' => 0,
			'return_format'  => 'string',
		),
		array(
			'key'            => 'field_liftedpath_team_name_color',
			'label'          => 'Name color',
			'name'           => 'name_color',
			'type'           => 'color_picker',
			'instructions'   => 'Leave blank for white.',
			'wrapper'        => array( 'width' => '33' ),
			'enable_opacity' => 0,
			'return_format'  => 'string',
		),
		array(
			'key'            => 'field_liftedpath_team_position_color',
			'label'          => 'Position color',
			'name'           => 'position_color',
			'type'           => 'color_picker',
			'instructions'   => 'Leave blank for soft white.',
			'wrapper'        => array( 'width' => '33' ),
			'enable_opacity' => 0,
			'return_format'  => 'string',
		),
	);

	$with_colors = array();

	foreach ( $updated as $field ) {
		if ( is_array( $field ) && ( $field['key'] ?? '' ) === 'field_liftedpath_team_members' ) {
			foreach ( $extras as $extra ) {
				if ( isset( $seen[ $extra['key'] ] ) ) {
					continue;
				}

				$extra['parent'] = $parent_key;
				$with_colors[]   = function_exists( 'acf_validate_field' ) ? acf_validate_field( $extra ) : $extra;
			}
		}

		$with_colors[] = $field;
	}

	$adding = false;

	return $with_colors;
}
add_filter( 'acf/load_fields', 'liftedpath_team_fields', 15, 2 );

/**
 * Visibility control for core blocks. LiftedPath blocks use the field in the sidebar.
 */
function liftedpath_block_editor_assets(): void {
	wp_enqueue_script(
		'liftedpath-block-controls',
		get_template_directory_uri() . '/assets/block-controls.js',
		array( 'wp-blocks', 'wp-element', 'wp-hooks', 'wp-compose', 'wp-block-editor', 'wp-components', 'wp-i18n' ),
		LIFTEDPATH_VERSION,
		true
	);
}
add_action( 'enqueue_block_editor_assets', 'liftedpath_block_editor_assets' );
