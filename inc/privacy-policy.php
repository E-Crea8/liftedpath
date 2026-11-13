<?php
/**
 * One-time Privacy Policy page + footer link.
 *
 * Content comes from the foundation PDF (local file only). The structured
 * copy lives in privacy-policy-content.json so the public page can be seeded
 * without committing the PDF.
 *
 * @package LiftedPath
 */

declare(strict_types=1);

/**
 * Create /privacy-policy and add it under Footer → Quick Links once.
 */
function liftedpath_privacy_policy_seed(): void {
	if ( get_option( 'liftedpath_privacy_policy_seeded_v1' ) ) {
		return;
	}

	$page_id = liftedpath_privacy_policy_upsert_page();
	if ( $page_id <= 0 ) {
		return;
	}

	liftedpath_privacy_policy_append_footer_link( $page_id );
	update_option( 'liftedpath_privacy_policy_seeded_v1', 1, false );
}
add_action( 'init', 'liftedpath_privacy_policy_seed', 35 );

/**
 * Publish the Privacy Policy page with hero + core copy blocks.
 */
function liftedpath_privacy_policy_upsert_page(): int {
	$existing = get_posts(
		array(
			'name'             => 'privacy-policy',
			'post_type'        => 'page',
			'post_status'      => array( 'publish', 'draft', 'private' ),
			'posts_per_page'   => 1,
			'suppress_filters' => true,
		)
	);

	if ( array() !== $existing && $existing[0] instanceof WP_Post ) {
		$page_id = (int) $existing[0]->ID;
	} else {
		$page_id = (int) wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_title'   => 'Privacy Policy',
				'post_name'    => 'privacy-policy',
				'post_content' => ' ',
			),
			true
		);
	}

	if ( $page_id <= 0 ) {
		return 0;
	}

	$image   = liftedpath_privacy_policy_hero_image();
	$content = liftedpath_seed_page_hero(
		$image,
		'Privacy Policy',
		'How LiftedPath collects, uses, and protects personal information.',
		'Please read this Privacy Policy carefully. It explains what we collect when you use our website, how we use it, and the choices available to you.',
		'Home'
	);
	$content .= liftedpath_privacy_policy_core_blocks();

	global $wpdb;
	$wpdb->update(
		$wpdb->posts,
		array(
			'post_content' => $content,
			'post_status'  => 'publish',
			'post_title'   => 'Privacy Policy',
			'post_name'    => 'privacy-policy',
		),
		array( 'ID' => $page_id )
	);
	clean_post_cache( $page_id );

	if ( function_exists( 'update_field' ) ) {
		update_field( 'field_liftedpath_page_seo_title', 'Privacy Policy', $page_id );
		update_field(
			'field_liftedpath_page_seo_description',
			'How LiftedPath Empowerment Foundation collects, uses, and protects personal information on liftedpathfoundation.org.',
			$page_id
		);
	}

	return $page_id;
}

/**
 * Prefer an existing classroom/students photograph for the page hero.
 */
function liftedpath_privacy_policy_hero_image(): int {
	if ( function_exists( 'liftedpath_seed_images' ) ) {
		$images = liftedpath_seed_images();
		foreach ( array( 'classroom', 'students', 'mentorship' ) as $key ) {
			if ( ! empty( $images[ $key ] ) ) {
				return (int) $images[ $key ];
			}
		}
	}

	$attachments = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_mime_type' => 'image',
			'posts_per_page' => 1,
			'fields'         => 'ids',
		)
	);

	return array() !== $attachments ? (int) $attachments[0] : 0;
}

/**
 * Build core heading, paragraph, and list blocks from the JSON copy.
 */
function liftedpath_privacy_policy_core_blocks(): string {
	$path = get_template_directory() . '/inc/privacy-policy-content.json';
	if ( ! is_readable( $path ) ) {
		return '';
	}

	$decoded = json_decode( (string) file_get_contents( $path ), true );
	if ( ! is_array( $decoded ) ) {
		return '';
	}

	$out = '';
	foreach ( $decoded as $block ) {
		if ( ! is_array( $block ) || empty( $block['type'] ) ) {
			continue;
		}

		$type = (string) $block['type'];
		if ( 'h2' === $type || 'h3' === $type ) {
			$text  = isset( $block['text'] ) ? (string) $block['text'] : '';
			$level = 'h2' === $type ? 2 : 3;
			$tag   = 'h' . $level;
			$safe  = esc_html( wp_strip_all_tags( $text ) );
			$out  .= "<!-- wp:heading {\"level\":{$level}} -->\n<{$tag} class=\"wp-block-heading\">{$safe}</{$tag}>\n<!-- /wp:heading -->\n\n";
			continue;
		}

		if ( 'p' === $type ) {
			$text = isset( $block['text'] ) ? (string) $block['text'] : '';
			$html = wp_kses(
				$text,
				array(
					'strong' => array(),
					'em'     => array(),
					'a'      => array(
						'href'   => true,
						'title'  => true,
						'rel'    => true,
						'target' => true,
					),
				)
			);
			if ( '' === trim( wp_strip_all_tags( $html ) ) ) {
				continue;
			}
			$out .= "<!-- wp:paragraph -->\n<p>{$html}</p>\n<!-- /wp:paragraph -->\n\n";
			continue;
		}

		if ( 'ul' === $type && ! empty( $block['items'] ) && is_array( $block['items'] ) ) {
			$items = '';
			foreach ( $block['items'] as $item ) {
				$safe   = esc_html( wp_strip_all_tags( (string) $item ) );
				$items .= "<!-- wp:list-item -->\n<li>{$safe}</li>\n<!-- /wp:list-item -->\n";
			}
			$out .= "<!-- wp:list -->\n<ul class=\"wp-block-list\">{$items}</ul>\n<!-- /wp:list -->\n\n";
		}
	}

	return $out;
}

/**
 * Legacy helper kept for the first seed; Privacy Policy now sits beside RC in the Next.js footer.
 *
 * @param int $page_id Unused. Kept for call-site compatibility.
 */
function liftedpath_privacy_policy_append_footer_link( int $page_id ): void {
	unset( $page_id );
	liftedpath_privacy_policy_remove_footer_nav_link();
}

/**
 * Remove Privacy Policy from the footer Quick Links nav (moved to RC meta row).
 */
function liftedpath_privacy_policy_remove_footer_nav_link(): void {
	if ( get_option( 'liftedpath_privacy_footer_meta_v1' ) ) {
		return;
	}

	$posts = get_posts(
		array(
			'post_type'      => 'wp_navigation',
			'name'           => 'footer',
			'post_status'    => array( 'publish', 'draft' ),
			'posts_per_page' => 1,
		)
	);

	if ( array() === $posts || ! $posts[0] instanceof WP_Post ) {
		update_option( 'liftedpath_privacy_footer_meta_v1', 1, false );
		return;
	}

	$nav     = $posts[0];
	$content = (string) $nav->post_content;
	$updated = preg_replace(
		'/\s*<!-- wp:navigation-link \{[^}]*"(?:label":"Privacy Policy"|url":"[^"]*privacy-policy[^"]*")[^}]*\} \/-->\s*/',
		"\n",
		$content
	);

	if ( is_string( $updated ) && $updated !== $content ) {
		global $wpdb;
		$wpdb->update(
			$wpdb->posts,
			array( 'post_content' => trim( $updated ) . "\n" ),
			array( 'ID' => (int) $nav->ID )
		);
		clean_post_cache( (int) $nav->ID );
	}

	update_option( 'liftedpath_privacy_footer_meta_v1', 1, false );
}
add_action( 'init', 'liftedpath_privacy_policy_remove_footer_nav_link', 36 );
