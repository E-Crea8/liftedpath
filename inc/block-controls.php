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
 * Sample content so the inserter can draw the block.
 *
 * @return array<string, mixed>
 */
function liftedpath_block_example( string $name ): array {
	$samples = array(
		'liftedpath/hero'          => liftedpath_example_merge(
			liftedpath_example_pair( 'eyebrow', 'field_liftedpath_hero_eyebrow', 'Lifting potential' ),
			liftedpath_example_pair( 'heading', 'field_liftedpath_hero_heading', 'Breaking Barriers to Education & Opportunity' ),
			liftedpath_example_pair( 'intro', 'field_liftedpath_hero_intro', 'A pathway for young people to learn, build, and grow.' ),
			liftedpath_example_pair( 'figure', 'field_liftedpath_hero_figure', '128' ),
			liftedpath_example_pair( 'label', 'field_liftedpath_hero_label', 'Scholars' ),
			liftedpath_example_pair( 'detail', 'field_liftedpath_hero_detail', 'In school this year' )
		),
		'liftedpath/page-hero'     => liftedpath_example_merge(
			liftedpath_example_pair( 'eyebrow', 'field_liftedpath_page_hero_eyebrow', 'About us' ),
			liftedpath_example_pair( 'breadcrumb_current', 'field_liftedpath_page_hero_breadcrumb_current', 'About us' ),
			liftedpath_example_pair( 'heading', 'field_liftedpath_page_hero_heading', 'Restoring hope, creating sustainable pathways.' ),
			liftedpath_example_pair( 'intro', 'field_liftedpath_page_hero_intro', 'Lifting Potential, Inspiring Purpose and Creating Futures.' ),
			liftedpath_example_pair( 'breadcrumb_label', 'field_liftedpath_page_hero_breadcrumb', 'Home' )
		),
		'liftedpath/stat'          => liftedpath_example_merge(
			liftedpath_example_pair( 'figure', 'field_liftedpath_stat_figure', '128' ),
			liftedpath_example_pair( 'label', 'field_liftedpath_stat_label', 'Scholars' ),
			liftedpath_example_pair( 'detail', 'field_liftedpath_stat_detail', 'In school this year' )
		),
		'liftedpath/marquee'       => liftedpath_example_repeater(
			'phrases',
			'field_liftedpath_marquee_block_phrases',
			array( 'phrase' => 'field_liftedpath_marquee_block_phrase' ),
			array(
				array( 'phrase' => 'Lifting potential' ),
				array( 'phrase' => 'Inspiring purpose' ),
				array( 'phrase' => 'Creating futures' ),
			)
		),
		'liftedpath/cta-band'      => liftedpath_example_merge(
			liftedpath_example_pair( 'tone', 'field_liftedpath_cta_tone', 'teal' ),
			liftedpath_example_pair( 'eyebrow', 'field_liftedpath_cta_eyebrow', 'Join the journey' ),
			liftedpath_example_pair( 'heading', 'field_liftedpath_cta_heading', 'Help a young person take the next step.' ),
			liftedpath_example_pair( 'text', 'field_liftedpath_cta_text', 'Give, partner, or share a skill.' )
		),
		'liftedpath/about-tabs'    => liftedpath_example_merge(
			liftedpath_example_pair( 'eyebrow', 'field_liftedpath_about_eyebrow', 'About our foundation' ),
			liftedpath_example_pair( 'heading', 'field_liftedpath_about_heading', 'A pathway that stays open.' ),
			liftedpath_example_pair( 'intro', 'field_liftedpath_about_intro', 'Mission, vision, and commitment.' ),
			liftedpath_example_repeater(
				'tabs',
				'field_liftedpath_about_tabs',
				array(
					'label' => 'field_liftedpath_about_tab_label',
					'title' => 'field_liftedpath_about_tab_title',
					'body'  => 'field_liftedpath_about_tab_body',
				),
				array(
					array(
						'label' => 'Mission',
						'title' => 'Keep learning within reach',
						'body'  => 'We walk with young people from the first classroom to a livelihood.',
					),
				)
			)
		),
		'liftedpath/about-story'   => liftedpath_example_merge(
			liftedpath_example_pair( 'eyebrow', 'field_liftedpath_about_story_eyebrow', 'Lifting potential, inspiring purpose' ),
			liftedpath_example_pair( 'heading', 'field_liftedpath_about_story_heading', 'Education is the start of a lasting pathway.' ),
			liftedpath_example_pair( 'body', 'field_liftedpath_about_story_body', "Young people need more than a single gift.\nThey need a path that continues." )
		),
		'liftedpath/beliefs'       => liftedpath_example_repeater(
			'items',
			'field_liftedpath_beliefs_items',
			array(
				'eyebrow' => 'field_liftedpath_beliefs_item_eyebrow',
				'heading' => 'field_liftedpath_beliefs_item_heading',
				'text'    => 'field_liftedpath_beliefs_item_text',
			),
			array(
				array(
					'eyebrow' => '01 / Our mission',
					'heading' => 'Mission',
					'text'    => 'Create pathways that last.',
				),
				array(
					'eyebrow' => '02 / Our vision',
					'heading' => 'Vision',
					'text'    => 'A community where potential is not wasted.',
				),
			)
		),
		'liftedpath/program-cards' => liftedpath_example_merge(
			liftedpath_example_pair( 'eyebrow', 'field_liftedpath_programs_eyebrow', 'What we do' ),
			liftedpath_example_pair( 'heading', 'field_liftedpath_programs_heading', 'Programs that open the next door.' ),
			liftedpath_example_pair( 'intro', 'field_liftedpath_programs_intro', 'Learning, skills, and support.' ),
			liftedpath_example_repeater(
				'cards',
				'field_liftedpath_programs_cards',
				array(
					'kicker' => 'field_liftedpath_programs_card_kicker',
					'title'  => 'field_liftedpath_programs_card_title',
					'line'   => 'field_liftedpath_programs_card_line',
				),
				array(
					array(
						'kicker' => 'Learning',
						'title'  => 'LIFT-GROW',
						'line'   => 'Secondary education',
					),
					array(
						'kicker' => 'Skills',
						'title'  => 'LIFT-EQUIP',
						'line'   => 'Vocational training',
					),
				)
			)
		),
		'liftedpath/action-grid'   => liftedpath_example_merge(
			liftedpath_example_pair( 'eyebrow', 'field_liftedpath_actions_eyebrow', 'How we work' ),
			liftedpath_example_pair( 'heading', 'field_liftedpath_actions_heading', 'Four ways to take part.' ),
			liftedpath_example_repeater(
				'items',
				'field_liftedpath_actions_items',
				array(
					'title' => 'field_liftedpath_actions_item_title',
					'text'  => 'field_liftedpath_actions_item_text',
				),
				array(
					array(
						'title' => 'Give',
						'text'  => 'Fund a scholarship or a skill.',
					),
					array(
						'title' => 'Partner',
						'text'  => 'Bring a program to your community.',
					),
				)
			)
		),
		'liftedpath/sponsor-callout' => liftedpath_example_merge(
			liftedpath_example_pair( 'figure', 'field_liftedpath_sponsor_figure', '₦50,000' ),
			liftedpath_example_pair( 'text', 'field_liftedpath_sponsor_text', 'can keep a student in school for a term.' )
		),
		'liftedpath/stewardship'   => liftedpath_example_merge(
			liftedpath_example_pair( 'eyebrow', 'field_liftedpath_stewardship_eyebrow', 'Stewardship' ),
			liftedpath_example_pair( 'heading', 'field_liftedpath_stewardship_heading', 'Every gift stays accountable.' ),
			liftedpath_example_pair( 'intro', 'field_liftedpath_stewardship_intro', 'We report where support goes.' ),
			liftedpath_example_repeater(
				'points',
				'field_liftedpath_stewardship_points',
				array(
					'title' => 'field_liftedpath_stewardship_point_title',
					'text'  => 'field_liftedpath_stewardship_point_text',
				),
				array(
					array(
						'title' => 'Tracked',
						'text'  => 'Each program has a record.',
					),
					array(
						'title' => 'Reported',
						'text'  => 'Partners can see the result.',
					),
				)
			)
		),
		'liftedpath/process'       => liftedpath_example_merge(
			liftedpath_example_pair( 'eyebrow', 'field_liftedpath_process_eyebrow', 'Our process' ),
			liftedpath_example_pair( 'heading', 'field_liftedpath_process_heading', 'From a first meeting to a lasting skill.' ),
			liftedpath_example_repeater(
				'steps',
				'field_liftedpath_process_steps',
				array(
					'title' => 'field_liftedpath_process_step_title',
					'text'  => 'field_liftedpath_process_step_text',
				),
				array(
					array(
						'title' => 'Listen',
						'text'  => 'Start with the young person.',
					),
					array(
						'title' => 'Support',
						'text'  => 'Stay through the next step.',
					),
				)
			)
		),
		'liftedpath/pillars'       => liftedpath_example_merge(
			liftedpath_example_pair( 'eyebrow', 'field_liftedpath_pillars_eyebrow', 'Our pillars' ),
			liftedpath_example_pair( 'heading', 'field_liftedpath_pillars_heading', 'Give, partner, empower, and reach out.' ),
			liftedpath_example_repeater(
				'items',
				'field_liftedpath_pillars_items',
				array(
					'title' => 'field_liftedpath_pillars_item_title',
					'text'  => 'field_liftedpath_pillars_item_text',
				),
				array(
					array(
						'title' => 'Give',
						'text'  => 'Resources that reach a student.',
					),
					array(
						'title' => 'Partner',
						'text'  => 'Work that is shared.',
					),
				)
			)
		),
		'liftedpath/goals'         => liftedpath_example_merge(
			liftedpath_example_pair( 'eyebrow', 'field_liftedpath_goals_eyebrow', 'Future goals' ),
			liftedpath_example_pair( 'heading', 'field_liftedpath_goals_heading', 'Grow skills and opportunities.' ),
			liftedpath_example_pair( 'intro', 'field_liftedpath_goals_intro', 'The next years of the pathway.' ),
			liftedpath_example_repeater(
				'goals',
				'field_liftedpath_goals_goals',
				array(
					'title' => 'field_liftedpath_goals_goal_title',
					'text'  => 'field_liftedpath_goals_goal_text',
				),
				array(
					array(
						'title' => 'Scholarships',
						'text'  => 'Keep more students in class.',
					),
					array(
						'title' => 'Skills',
						'text'  => 'Open a route to work.',
					),
				)
			)
		),
		'liftedpath/faq'           => liftedpath_example_merge(
			liftedpath_example_pair( 'eyebrow', 'field_liftedpath_faq_eyebrow', 'Questions' ),
			liftedpath_example_pair( 'heading', 'field_liftedpath_faq_heading', 'What partners ask first.' ),
			liftedpath_example_repeater(
				'items',
				'field_liftedpath_faq_items',
				array(
					'question' => 'field_liftedpath_faq_item_question',
					'answer'   => 'field_liftedpath_faq_item_answer',
				),
				array(
					array(
						'question' => 'Where does a gift go?',
						'answer'   => 'Into the program you choose, with a record of the result.',
					),
				)
			)
		),
		'liftedpath/contact-form'  => liftedpath_example_merge(
			liftedpath_example_pair( 'eyebrow', 'field_liftedpath_contact_eyebrow', "Let's talk" ),
			liftedpath_example_pair( 'heading', 'field_liftedpath_contact_heading', 'Tell us how you want to help.' ),
			liftedpath_example_pair( 'intro', 'field_liftedpath_contact_intro', 'A note is enough to begin.' ),
			liftedpath_example_pair( 'form_eyebrow', 'field_liftedpath_contact_form_eyebrow', 'Get in touch' ),
			liftedpath_example_pair( 'form_heading', 'field_liftedpath_contact_form_heading', 'Send a message' )
		),
		'liftedpath/donate-panel'  => liftedpath_example_merge(
			liftedpath_example_pair( 'eyebrow', 'field_liftedpath_donate_eyebrow', 'Make a difference' ),
			liftedpath_example_pair( 'heading', 'field_liftedpath_donate_heading', 'Choose a gift.' ),
			liftedpath_example_pair( 'intro', 'field_liftedpath_donate_intro', 'One gift can keep a pathway open.' ),
			liftedpath_example_repeater(
				'amounts',
				'field_liftedpath_donate_amounts',
				array(
					'amount' => 'field_liftedpath_donate_amount_amount',
					'title'  => 'field_liftedpath_donate_amount_title',
					'detail' => 'field_liftedpath_donate_amount_detail',
				),
				array(
					array(
						'amount' => '50000',
						'title'  => 'A term',
						'detail' => 'School costs for one student.',
					),
				)
			)
		),
		'liftedpath/team'          => liftedpath_example_merge(
			liftedpath_example_pair( 'eyebrow', 'field_liftedpath_team_eyebrow', 'Our team' ),
			liftedpath_example_pair( 'heading', 'field_liftedpath_team_heading', 'People carrying the pathway.' ),
			liftedpath_example_repeater(
				'members',
				'field_liftedpath_team_members',
				array(
					'name'     => 'field_liftedpath_team_member_name',
					'position' => 'field_liftedpath_team_member_position',
				),
				array(
					array(
						'name'     => 'Amaka Adeyemi',
						'position' => 'Executive Director',
					),
					array(
						'name'     => 'Chinedu Okonkwo',
						'position' => 'Programs Lead',
					),
					array(
						'name'     => 'Fatima Bello',
						'position' => 'Learning Coordinator',
					),
					array(
						'name'     => 'Tunde Lawal',
						'position' => 'Partnerships',
					),
				)
			)
		),
		'liftedpath/gallery'       => liftedpath_example_merge(
			liftedpath_example_pair( 'eyebrow', 'field_liftedpath_gallery_eyebrow', 'The work' ),
			liftedpath_example_pair( 'heading', 'field_liftedpath_gallery_heading', 'Scenes from the pathway.' ),
			liftedpath_example_pair( 'layout', 'field_liftedpath_gallery_layout', 'mosaic' ),
			liftedpath_example_pair( 'columns', 'field_liftedpath_gallery_columns', '3' ),
			liftedpath_example_pair( 'show_captions', 'field_liftedpath_gallery_captions', 1 ),
			liftedpath_example_pair( 'enlarge', 'field_liftedpath_gallery_enlarge', 1 ),
			liftedpath_example_repeater(
				'items',
				'field_liftedpath_gallery_items',
				array(
					'media_kind' => 'field_liftedpath_gallery_item_kind',
					'caption'    => 'field_liftedpath_gallery_item_caption',
					'tile'       => 'field_liftedpath_gallery_item_tile',
				),
				array(
					array(
						'media_kind' => 'image',
						'caption'    => 'A classroom visit',
						'tile'       => 'wide',
					),
					array(
						'media_kind' => 'video',
						'caption'    => 'A short film',
						'tile'       => 'standard',
					),
					array(
						'media_kind' => 'image',
						'caption'    => 'A season together',
						'tile'       => 'tall',
					),
				)
			)
		),
	);

	$data = $samples[ $name ] ?? liftedpath_example_pair( 'heading', '', 'Section heading' );

	$example = array(
		'viewportWidth' => 1100,
		'attributes'    => array(
			'mode' => 'preview',
			'data' => $data,
		),
	);

	if ( 'liftedpath/stat-group' === $name ) {
		$example['innerBlocks'] = array(
			array(
				'name'       => 'liftedpath/stat',
				'attributes' => array(
					'mode' => 'preview',
					'data' => $samples['liftedpath/stat'],
				),
			),
		);
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
