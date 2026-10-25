<?php
/**
 * Seed the public pages and navigation once.
 *
 * A page that already has content is left alone. Navigation is created only
 * when that slug is absent.
 *
 * @package LiftedPath
 */

/**
 * Create missing pages and menus the first time ACF can store page fields.
 */
function liftedpath_seed_content(): void {
	if ( get_option( 'liftedpath_content_seeded' ) || ! function_exists( 'update_field' ) ) {
		return;
	}

	$images = liftedpath_seed_images();
	$pages  = liftedpath_seed_pages( $images );

	if ( ! isset( $pages['home'] ) ) {
		return;
	}

	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $pages['home'] );
	update_option( 'page_for_posts', 0 );

	liftedpath_seed_navigation( $pages );
	liftedpath_seed_site_identity( $images );

	if ( '' === get_option( 'permalink_structure' ) ) {
		update_option( 'permalink_structure', '/%postname%/' );
		flush_rewrite_rules( false );
	}

	update_option( 'liftedpath_content_seeded', 1, false );
}
add_action( 'init', 'liftedpath_seed_content', 30 );

/**
 * Attachment IDs keyed by the original file slug.
 *
 * @return array<string, int>
 */
function liftedpath_seed_images(): array {
	$slugs = array(
		'classroom',
		'mentorship',
		'orphanage',
		'scholarship',
		'season',
		'skills',
		'students',
		'liftedpath-logo',
	);
	$ids   = array();

	foreach ( $slugs as $slug ) {
		$found = get_posts(
			array(
				'post_type'      => 'attachment',
				'name'           => $slug,
				'posts_per_page' => 1,
				'post_status'    => 'inherit',
			)
		);
		$ids[ $slug ] = $found ? (int) $found[0]->ID : 0;
	}

	return $ids;
}

/**
 * Insert each public page when that path is empty.
 *
 * @param array<string, int> $images Attachment IDs.
 * @return array<string, int>
 */
function liftedpath_seed_pages( array $images ): array {
	$ids   = array();
	$order = array( 'home', 'about', 'what-we-do', 'learning', 'impact', 'contact', 'donate' );

	foreach ( liftedpath_seed_page_definitions( $images ) as $key => $page ) {
		$parent = $page['parent'] ?? '';
		if ( '' !== $parent && ! isset( $ids[ $parent ] ) ) {
			return array();
		}

		$page_id = liftedpath_seed_upsert_page( $page, $ids );
		if ( $page_id <= 0 ) {
			return array();
		}

		$ids[ $key ] = $page_id;
	}

	foreach ( $order as $key ) {
		if ( ! isset( $ids[ $key ] ) ) {
			return array();
		}
	}

	return $ids;
}

/**
 * Create or fill one page. Existing content stays.
 *
 * @param array<string, mixed> $page Page definition.
 * @param array<string, int>   $ids  Pages already created, keyed by slug path.
 */
function liftedpath_seed_upsert_page( array $page, array $ids ): int {
	$parent_key = $page['parent'] ?? '';
	$parent_id  = '' !== $parent_key ? (int) $ids[ $parent_key ] : 0;
	$path       = '' !== $parent_key ? $parent_key . '/' . $page['slug'] : $page['slug'];
	$existing   = get_page_by_path( $path, OBJECT, 'page' );

	if ( $existing instanceof WP_Post && '' !== trim( (string) $existing->post_content ) ) {
		return (int) $existing->ID;
	}

	$post = array(
		'post_type'    => 'page',
		'post_status'  => 'publish',
		'post_title'   => $page['title'],
		'post_name'    => $page['slug'],
		'post_parent'  => $parent_id,
		'post_content' => ' ',
	);

	if ( $existing instanceof WP_Post ) {
		$post['ID'] = (int) $existing->ID;
		$page_id    = (int) wp_update_post( $post, true );
	} else {
		$page_id = (int) wp_insert_post( $post, true );
	}

	if ( $page_id <= 0 ) {
		return 0;
	}

	global $wpdb;
	$wpdb->update(
		$wpdb->posts,
		array( 'post_content' => $page['content'] ),
		array( 'ID' => $page_id ),
		array( '%s' ),
		array( '%d' )
	);
	clean_post_cache( $page_id );

	update_field( 'seo_title', $page['seo_title'], $page_id );
	update_field( 'seo_description', $page['seo_description'], $page_id );
	update_field( 'hero_summary', $page['hero_summary'], $page_id );

	return $page_id;
}

/**
 * Page definitions in parent-before-child order.
 *
 * @param array<string, int> $images Attachment IDs.
 * @return array<string, array<string, mixed>>
 */
function liftedpath_seed_page_definitions( array $images ): array {
	$join = liftedpath_seed_join_band( $images['students'] ?? 0 );

	$pages = array(
		'home'       => liftedpath_seed_home_page( $images ),
		'about'      => liftedpath_seed_about_page( $images, $join ),
		'what-we-do' => liftedpath_seed_work_page( $images ),
		'learning'   => liftedpath_seed_learning_page( $images ),
		'impact'     => liftedpath_seed_impact_page( $images ),
		'contact'    => liftedpath_seed_contact_page( $images ),
		'donate'     => liftedpath_seed_donate_page( $images ),
	);

	foreach ( liftedpath_seed_child_pages( $images ) as $key => $page ) {
		$page['content']      .= $join;
		$pages[ $key ]         = $page;
	}

	return $pages;
}

/**
 * @param array<string, int> $images Attachment IDs.
 * @return array<string, mixed>
 */
function liftedpath_seed_home_page( array $images ): array {
	$intro = 'LiftedPath Empowerment Foundation is a purpose driven non-governmental organization founded on a deep commitment to breaking barriers to education and opportunity for children, teenagers, and young adults.';
	$story = 'What began as a small initiative two years ago has grown into a structured mission to restore hope and create sustainable pathways for individuals who have the passion to succeed but lack the resources to pursue their dreams.';

	$content  = liftedpath_seed_block(
		'hero',
		liftedpath_seed_data(
			array(
				array( 'name' => 'image', 'key' => 'field_liftedpath_hero_image', 'value' => $images['students'] ?? 0 ),
				array(
					'name' => 'portraits',
					'key'  => 'field_liftedpath_hero_portraits',
					'rows' => array(
						array( 'image' => $images['classroom'] ?? 0 ),
						array( 'image' => $images['scholarship'] ?? 0 ),
						array( 'image' => $images['mentorship'] ?? 0 ),
						array( 'image' => $images['orphanage'] ?? 0 ),
					),
					'subs' => array( 'image' => 'field_liftedpath_hero_portrait' ),
				),
				array( 'name' => 'eyebrow', 'key' => 'field_liftedpath_hero_eyebrow', 'value' => 'Lifting Potentials and Inspiring Purpose' ),
				array( 'name' => 'heading', 'key' => 'field_liftedpath_hero_heading', 'value' => 'Breaking Barriers to Education & Opportunity' ),
				array( 'name' => 'intro', 'key' => 'field_liftedpath_hero_intro', 'value' => $intro ),
				array( 'name' => 'button', 'key' => 'field_liftedpath_hero_button', 'value' => liftedpath_seed_link( 'Donate Now', '/donate/' ) ),
				array( 'name' => 'figure', 'key' => 'field_liftedpath_hero_figure', 'value' => '25+' ),
				array( 'name' => 'label', 'key' => 'field_liftedpath_hero_label', 'value' => 'Students supported' ),
				array( 'name' => 'detail', 'key' => 'field_liftedpath_hero_detail', 'value' => 'Students supported with educational assistance and scholarships.' ),
			)
		)
	);
	$content .= liftedpath_seed_block( 'marquee', array() );
	$content .= liftedpath_seed_block(
		'about-tabs',
		liftedpath_seed_data(
			array(
				array( 'name' => 'primary_image', 'key' => 'field_liftedpath_about_primary_image', 'value' => $images['classroom'] ?? 0 ),
				array( 'name' => 'secondary_image', 'key' => 'field_liftedpath_about_secondary_image', 'value' => $images['mentorship'] ?? 0 ),
				array( 'name' => 'figure', 'key' => 'field_liftedpath_about_figure', 'value' => '2+' ),
				array( 'name' => 'figure_label', 'key' => 'field_liftedpath_about_figure_label', 'value' => 'Years of impact' ),
				array( 'name' => 'eyebrow', 'key' => 'field_liftedpath_about_eyebrow', 'value' => 'About our foundation' ),
				array( 'name' => 'heading', 'key' => 'field_liftedpath_about_heading', 'value' => 'A purpose-driven foundation breaking barriers to education' ),
				array( 'name' => 'intro', 'key' => 'field_liftedpath_about_intro', 'value' => $story ),
				array(
					'name' => 'tabs',
					'key'  => 'field_liftedpath_about_tabs',
					'rows' => array(
						array(
							'label' => 'Mission',
							'title' => 'Our Mission',
							'body'  => 'To empower supported individuals by providing access to education, vocational training, and essential support systems, while fostering hope, dignity, and long-term self-reliance through community driven initiatives.',
						),
						array(
							'label' => 'Vision',
							'title' => 'Our Vision',
							'body'  => 'To create a world where every young person, especially those facing financial and social barriers, has access to the education, skills, and opportunities needed to discover their purpose, reach their potential, and contribute meaningfully to society.',
						),
						array(
							'label' => 'Commitment',
							'title' => 'Our Commitment',
							'body'  => 'We manage every contribution with integrity, accountability, and purpose, directing resources toward meaningful opportunities for the communities we serve.',
						),
					),
					'subs' => array(
						'label' => 'field_liftedpath_about_tab_label',
						'title' => 'field_liftedpath_about_tab_title',
						'body'  => 'field_liftedpath_about_tab_body',
					),
				),
				array( 'name' => 'primary_link', 'key' => 'field_liftedpath_about_primary_link', 'value' => liftedpath_seed_link( 'More About Us', '/about/' ) ),
				array( 'name' => 'secondary_link', 'key' => 'field_liftedpath_about_secondary_link', 'value' => liftedpath_seed_link( 'Impact Report', '/impact/' ) ),
			)
		)
	);
	$content .= liftedpath_seed_block(
		'program-cards',
		liftedpath_seed_data(
			array(
				array( 'name' => 'eyebrow', 'key' => 'field_liftedpath_programs_eyebrow', 'value' => 'What We Do' ),
				array( 'name' => 'heading', 'key' => 'field_liftedpath_programs_heading', 'value' => 'Initiatives that empower' ),
				array( 'name' => 'intro', 'key' => 'field_liftedpath_programs_intro', 'value' => 'Five programs, one mission: access to education, skills and the support systems that build long-term self-reliance.' ),
				array(
					'name' => 'cards',
					'key'  => 'field_liftedpath_programs_cards',
					'rows' => array(
						array( 'image' => $images['scholarship'] ?? 0, 'kicker' => 'Stronger People', 'title' => 'Education Sponsorship / Scholarship', 'line' => 'Opportunity Today = Possibility Tomorrow', 'link' => liftedpath_seed_link( 'Read More', '/what-we-do/' ) ),
						array( 'image' => $images['skills'] ?? 0, 'kicker' => 'Stronger People', 'title' => 'Skills Acquisition & Vocational Training', 'line' => 'Learn. Build. Grow.', 'link' => liftedpath_seed_link( 'Read More', '/what-we-do/' ) ),
						array( 'image' => $images['mentorship'] ?? 0, 'kicker' => 'Brighter Communities', 'title' => 'Mentorship & Coaching', 'line' => 'Guidance. Support. Success.', 'link' => liftedpath_seed_link( 'Read More', '/what-we-do/mentorship-coaching/' ) ),
						array( 'image' => $images['orphanage'] ?? 0, 'kicker' => 'Brighter Communities', 'title' => 'Orphanage Visitations', 'line' => 'Love. Care. Hope.', 'link' => liftedpath_seed_link( 'Read More', '/what-we-do/orphanage-outreach/' ) ),
						array( 'image' => $images['season'] ?? 0, 'kicker' => 'Greater Impact', 'title' => 'Share the Joy of the Season', 'line' => 'Food. Gifts. Smiles.', 'link' => liftedpath_seed_link( 'Read More', '/what-we-do/share-the-joy-of-the-season/' ) ),
					),
					'subs' => array(
						'image'  => 'field_liftedpath_programs_card_image',
						'kicker' => 'field_liftedpath_programs_card_kicker',
						'title'  => 'field_liftedpath_programs_card_title',
						'line'   => 'field_liftedpath_programs_card_line',
						'link'   => 'field_liftedpath_programs_card_link',
					),
				),
				array( 'name' => 'callout_heading', 'key' => 'field_liftedpath_programs_callout_heading', 'value' => 'Sponsor a child or a whole program.' ),
				array( 'name' => 'callout_text', 'key' => 'field_liftedpath_programs_callout_text', 'value' => 'Every naira is tracked, receipted and paid directly to institutions where possible.' ),
				array( 'name' => 'callout_button', 'key' => 'field_liftedpath_programs_callout_button', 'value' => liftedpath_seed_link( 'Donate Now', '/donate/' ) ),
			)
		)
	);
	$content .= liftedpath_seed_block(
		'action-grid',
		liftedpath_seed_data(
			array(
				array( 'name' => 'eyebrow', 'key' => 'field_liftedpath_actions_eyebrow', 'value' => 'How we work' ),
				array( 'name' => 'heading', 'key' => 'field_liftedpath_actions_heading', 'value' => 'Our actions for lasting change' ),
				array( 'name' => 'intro', 'key' => 'field_liftedpath_actions_intro', 'value' => 'We give, partner, empower and reach out — turning compassion into sustainable pathways for young people and families facing financial and social barriers' ),
				array(
					'name' => 'items',
					'key'  => 'field_liftedpath_actions_items',
					'rows' => array(
						array( 'title' => 'Give', 'text' => 'Financial and in-kind donations that help fund our programs and initiatives.', 'link' => liftedpath_seed_link( 'Donate', '/donate/' ) ),
						array( 'title' => 'Partner', 'text' => 'We connect with individuals, schools, organizations and communities through our Community Outreach Representatives to identify people who can benefit from support.', 'link' => liftedpath_seed_link( 'Read More', '/what-we-do/' ) ),
						array( 'title' => 'Empower', 'text' => 'Through education, skills development, coaching, mentorship, and practical assistance.', 'link' => liftedpath_seed_link( 'Read More', '/what-we-do/' ) ),
						array( 'title' => 'Reachout', 'text' => 'We reach families and communities through initiatives such as Share the Joy of the Season and our orphanage outreach.', 'link' => liftedpath_seed_link( 'Read More', '/impact/' ) ),
					),
					'subs' => array(
						'title' => 'field_liftedpath_actions_item_title',
						'text'  => 'field_liftedpath_actions_item_text',
						'link'  => 'field_liftedpath_actions_item_link',
					),
				),
			)
		)
	);
	$content .= liftedpath_seed_block(
		'sponsor-callout',
		liftedpath_seed_data(
			array(
				array( 'name' => 'figure', 'key' => 'field_liftedpath_sponsor_figure', 'value' => '₦50,000' ),
				array( 'name' => 'text', 'key' => 'field_liftedpath_sponsor_text', 'value' => 'Keeps one determined student in school for a full term.' ),
				array( 'name' => 'link', 'key' => 'field_liftedpath_sponsor_link', 'value' => liftedpath_seed_link( 'Sponsor A Student', '/donate/' ) ),
			)
		)
	);
	$content .= liftedpath_seed_stewardship(
		$images,
		3,
		'Why choose us',
		'Thoughtful Actions with Measurable Impact',
		'At LiftedPath Empowerment Foundation, we are committed to responsible stewardship of every resource entrusted to us.',
		liftedpath_seed_link( 'Learn More', '/impact/' )
	);
	$content .= liftedpath_seed_block(
		'process',
		liftedpath_seed_data(
			array(
				array( 'name' => 'eyebrow', 'key' => 'field_liftedpath_process_eyebrow', 'value' => 'Our process' ),
				array( 'name' => 'heading', 'key' => 'field_liftedpath_process_heading', 'value' => 'From planning to action, every step counts' ),
				array( 'name' => 'button', 'key' => 'field_liftedpath_process_button', 'value' => liftedpath_seed_link( 'Learn More', '/about/' ) ),
				array(
					'name' => 'steps',
					'key'  => 'field_liftedpath_process_steps',
					'rows' => array(
						array( 'title' => 'Identify real needs', 'text' => 'Through school partnerships and our Community Outreach Representatives.' ),
						array( 'title' => 'Fund & pay directly', 'text' => 'Financial education support paid directly to schools and training providers.' ),
						array( 'title' => 'Mentor & follow up', 'text' => 'Coaching and mentorship keep every beneficiary on track with their goals.' ),
					),
					'subs' => array(
						'title' => 'field_liftedpath_process_step_title',
						'text'  => 'field_liftedpath_process_step_text',
					),
				),
			)
		)
	);
	$content .= liftedpath_seed_stats();

	return liftedpath_seed_page_record(
		'home',
		'Home',
		'',
		$content,
		'Home',
		$intro,
		$intro
	);
}

/**
 * @param array<string, int> $images Attachment IDs.
 */
function liftedpath_seed_about_page( array $images, string $join ): array {
	$mission = 'To empower supported individuals by providing access to education, vocational training, and essential support systems, while fostering hope, dignity, and long-term self-reliance through community driven initiatives.';
	$vision  = 'To create a world where every young person, especially those facing financial and social barriers, has access to the education, skills, and opportunities needed to discover their purpose, reach their potential, and contribute meaningfully to society.';
	$story   = 'What began as a small initiative two years ago has grown into a structured mission to restore hope and create sustainable pathways for individuals who have the passion to succeed but lack the resources to pursue their dreams.';
	$intro   = 'LiftedPath Empowerment Foundation is a purpose driven non-governmental organization founded on a deep commitment to breaking barriers to education and opportunity for children, teenagers, and young adults.';

	$content  = liftedpath_seed_page_hero( $images['classroom'] ?? 0, 'About us', 'Restoring hope, creating sustainable pathways.', 'Lifting Potential, Inspiring Purpose and Creating Futures.', 'Home' );
	$content .= liftedpath_seed_about_story( $images['classroom'] ?? 0, 'Lifting potential, inspiring purpose', 'A purpose-driven foundation breaking barriers to education', $intro . "\n\n" . $story, 'Young people learning in a classroom' );
	$content .= liftedpath_seed_beliefs(
		array(
			array( '01 / Our mission', 'Empower individuals to thrive.', $mission ),
			array( '02 / Our vision', 'Opportunity for every young person.', $vision ),
		)
	);
	$content .= liftedpath_seed_pillars();
	$content .= liftedpath_seed_goals();
	$content .= $join;

	return liftedpath_seed_page_record( 'about', 'About Us', '', $content, 'About Us', 'Restoring hope, creating sustainable pathways.', $intro );
}

/**
 * @param array<string, int> $images Attachment IDs.
 */
function liftedpath_seed_work_page( array $images ): array {
	return liftedpath_seed_work_layout_page( 'what-we-do', $images );
}

/**
 * @param array<string, int> $images Attachment IDs.
 */
function liftedpath_seed_learning_page( array $images ): array {
	return liftedpath_seed_work_layout_page( 'learning', $images );
}

/**
 * @param array<string, int> $images Attachment IDs.
 */
function liftedpath_seed_impact_page( array $images ): array {
	$intro    = 'Every contribution is managed with integrity, accountability, and purpose.';
	$content  = liftedpath_seed_page_hero( $images['mentorship'] ?? 0, 'Impact & transparency', 'Our success so far, and how every naira is accounted for.', $intro, 'Home' );
	$content .= liftedpath_seed_stats();
	$content .= liftedpath_seed_stewardship(
		$images,
		4,
		'Stewardship',
		'Thoughtful Actions with Measurable Impact',
		$intro,
		liftedpath_seed_link( 'Get in touch', '/contact/' )
	);
	$content .= liftedpath_seed_block(
		'faq',
		liftedpath_seed_data(
			array(
				array( 'name' => 'eyebrow', 'key' => 'field_liftedpath_faq_eyebrow', 'value' => 'Questions' ),
				array( 'name' => 'heading', 'key' => 'field_liftedpath_faq_heading', 'value' => 'What donors ask us.' ),
				array(
					'name' => 'items',
					'key'  => 'field_liftedpath_faq_items',
					'rows' => array(
						array( 'question' => 'Where does my donation actually go?', 'answer' => '' ),
						array( 'question' => 'Can I give in kind instead of cash?', 'answer' => '' ),
						array( 'question' => 'Can I sponsor one specific student?', 'answer' => '' ),
						array( 'question' => 'How do I volunteer?', 'answer' => '' ),
					),
					'subs' => array(
						'question' => 'field_liftedpath_faq_item_question',
						'answer'   => 'field_liftedpath_faq_item_answer',
					),
				),
			)
		)
	);

	return liftedpath_seed_page_record( 'impact', 'Impact', '', $content, 'Impact', $intro, $intro );
}

/**
 * @param array<string, int> $images Attachment IDs.
 */
function liftedpath_seed_contact_page( array $images ): array {
	$intro    = 'Volunteer, partner, sponsor a student, or simply ask a question — we would love to hear from you.';
	$content  = liftedpath_seed_page_hero( $images['orphanage'] ?? 0, 'Contact', 'Together, we can inspire purpose and create futures.', $intro, 'Home' );
	$content .= liftedpath_seed_block(
		'contact-form',
		liftedpath_seed_data(
			array(
				array( 'name' => 'eyebrow', 'key' => 'field_liftedpath_contact_eyebrow', 'value' => "Let's talk" ),
				array( 'name' => 'heading', 'key' => 'field_liftedpath_contact_heading', 'value' => 'Every great change begins with a conversation.' ),
				array( 'name' => 'intro', 'key' => 'field_liftedpath_contact_intro', 'value' => $intro ),
				array( 'name' => 'form_eyebrow', 'key' => 'field_liftedpath_contact_form_eyebrow', 'value' => 'Get in touch' ),
				array( 'name' => 'form_heading', 'key' => 'field_liftedpath_contact_form_heading', 'value' => 'Send us a message' ),
				array(
					'name' => 'reasons',
					'key'  => 'field_liftedpath_contact_reasons',
					'rows' => array(
						array( 'reason' => 'volunteer', 'label' => 'Volunteer my time' ),
						array( 'reason' => 'sponsor', 'label' => 'Sponsor a child or program' ),
						array( 'reason' => 'partner', 'label' => 'Partner as a school or organisation' ),
						array( 'reason' => 'in-kind', 'label' => 'Make an in-kind donation' ),
						array( 'reason' => 'other', 'label' => 'Something else' ),
					),
					'subs' => array(
						'reason' => 'field_liftedpath_contact_reason_value',
						'label'  => 'field_liftedpath_contact_reason_label',
					),
				),
				array( 'name' => 'button_label', 'key' => 'field_liftedpath_contact_button_label', 'value' => 'Send message' ),
				array( 'name' => 'note', 'key' => 'field_liftedpath_contact_note', 'value' => '' ),
			)
		)
	);

	return liftedpath_seed_page_record( 'contact', 'Contact Us', '', $content, 'Contact Us', $intro, $intro );
}

/**
 * @param array<string, int> $images Attachment IDs.
 */
function liftedpath_seed_donate_page( array $images ): array {
	$intro    = "Give once or monthly. Where possible we pay schools and training providers directly on a beneficiary's behalf.";
	$content  = liftedpath_seed_page_hero( $images['season'] ?? 0, 'Donate', 'Your gift keeps a determined student in school.', $intro, 'Home' );
	$content .= liftedpath_seed_block(
		'donate-panel',
		liftedpath_seed_data(
			array(
				array( 'name' => 'eyebrow', 'key' => 'field_liftedpath_donate_eyebrow', 'value' => 'Make a difference' ),
				array( 'name' => 'heading', 'key' => 'field_liftedpath_donate_heading', 'value' => 'Choose your impact.' ),
				array( 'name' => 'intro', 'key' => 'field_liftedpath_donate_intro', 'value' => 'Your generosity can keep learning within reach. To arrange your gift, select an amount and contact our team.' ),
				array( 'name' => 'one_off_label', 'key' => 'field_liftedpath_donate_one_off_label', 'value' => 'One-off' ),
				array( 'name' => 'monthly_label', 'key' => 'field_liftedpath_donate_monthly_label', 'value' => 'Monthly' ),
				array(
					'name' => 'amounts',
					'key'  => 'field_liftedpath_donate_amounts',
					'rows' => array(
						array( 'amount' => 15000, 'title' => 'School essentials', 'detail' => 'Can help provide learning materials and school essentials for a pupil.' ),
						array( 'amount' => 50000, 'title' => 'Education support', 'detail' => 'Can contribute toward tuition and related costs for a student.' ),
						array( 'amount' => 120000, 'title' => 'Skills & tools', 'detail' => 'Can help fund practical training and tools for a young person.' ),
						array( 'amount' => 300000, 'title' => 'Family care', 'detail' => 'Can help provide food and essentials to families facing hardship.' ),
					),
					'subs' => array(
						'amount' => 'field_liftedpath_donate_amount_amount',
						'title'  => 'field_liftedpath_donate_amount_title',
						'detail' => 'field_liftedpath_donate_amount_detail',
					),
				),
				array( 'name' => 'usd_rate', 'key' => 'field_liftedpath_donate_usd_rate', 'value' => 1500 ),
				array( 'name' => 'gbp_rate', 'key' => 'field_liftedpath_donate_gbp_rate', 'value' => 2000 ),
				array(
					'name' => 'designations',
					'key'  => 'field_liftedpath_donate_designations',
					'rows' => array(
						array( 'designation' => 'greatest-need', 'label' => 'Wherever the need is greatest' ),
						array( 'designation' => 'education', 'label' => 'Education Sponsorship / Scholarship' ),
						array( 'designation' => 'skills', 'label' => 'Skills Acquisition & Vocational Training' ),
						array( 'designation' => 'mentorship', 'label' => 'Mentorship & Coaching' ),
						array( 'designation' => 'orphanage', 'label' => 'Orphanage Visitations' ),
						array( 'designation' => 'season', 'label' => 'Share the Joy of the Season' ),
					),
					'subs' => array(
						'designation' => 'field_liftedpath_donate_designation_value',
						'label'       => 'field_liftedpath_donate_designation_label',
					),
				),
				array( 'name' => 'button_label', 'key' => 'field_liftedpath_donate_button_label', 'value' => 'Arrange my gift' ),
				array( 'name' => 'note', 'key' => 'field_liftedpath_donate_note', 'value' => 'No payment is collected on this website.' ),
				array( 'name' => 'bank_heading', 'key' => 'field_liftedpath_donate_bank_heading', 'value' => 'Bank transfer' ),
				array( 'name' => 'bank_account_number', 'key' => 'field_liftedpath_donate_bank_account_number', 'value' => '1999590091' ),
				array( 'name' => 'bank_account_name', 'key' => 'field_liftedpath_donate_bank_account_name', 'value' => 'LIFTEDPATH EMPOWERMENT FOUNDATION' ),
				array( 'name' => 'bank_name', 'key' => 'field_liftedpath_donate_bank_name', 'value' => 'ACCESS BANK OF NIGERIA' ),
				array( 'name' => 'bank_currency', 'key' => 'field_liftedpath_donate_bank_currency', 'value' => 'NIGERIAN NAIRA' ),
				array( 'name' => 'bank_mobile_order', 'key' => 'field_liftedpath_donate_bank_mobile_order', 'value' => 'before' ),
				array( 'name' => 'aside_image', 'key' => 'field_liftedpath_donate_aside_image', 'value' => $images['scholarship'] ?? 0 ),
				array( 'name' => 'aside_heading', 'key' => 'field_liftedpath_donate_aside_heading', 'value' => '85% of funds go directly to programs.' ),
				array( 'name' => 'aside_text', 'key' => 'field_liftedpath_donate_aside_text', 'value' => 'Every donation is documented and receipted, and where possible we pay schools and training providers directly.' ),
			)
		)
	);

	return liftedpath_seed_page_record( 'donate', 'Donate', '', $content, 'Donate', $intro, $intro );
}

/**
 * Fill an empty bank-transfer card on existing donate panels once.
 */
function liftedpath_migrate_donate_bank(): void {
	if ( get_option( 'liftedpath_donate_bank_migrated_v1' ) ) {
		return;
	}

	try {
		liftedpath_migrate_donate_bank_run();
	} catch ( Throwable $exception ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
		unset( $exception );
	}
}

/**
 * Write the foundation account onto donate panels that do not have one yet.
 */
function liftedpath_migrate_donate_bank_run(): void {
	global $wpdb;

	$ids = $wpdb->get_col(
		$wpdb->prepare(
			"SELECT ID FROM {$wpdb->posts} WHERE post_content LIKE %s AND post_status != %s",
			'%liftedpath/donate-panel%',
			'auto-draft'
		)
	);

	if ( ! is_array( $ids ) || array() === $ids ) {
		return;
	}

	$defaults = array(
		'bank_heading'        => 'Bank transfer',
		'bank_account_number' => '1999590091',
		'bank_account_name'   => 'LIFTEDPATH EMPOWERMENT FOUNDATION',
		'bank_name'           => 'ACCESS BANK OF NIGERIA',
		'bank_currency'       => 'NIGERIAN NAIRA',
	);
	$keys     = array(
		'bank_heading'        => 'field_liftedpath_donate_bank_heading',
		'bank_account_number' => 'field_liftedpath_donate_bank_account_number',
		'bank_account_name'   => 'field_liftedpath_donate_bank_account_name',
		'bank_name'           => 'field_liftedpath_donate_bank_name',
		'bank_currency'       => 'field_liftedpath_donate_bank_currency',
	);

	foreach ( $ids as $id ) {
		$post = get_post( (int) $id );

		if ( ! $post instanceof WP_Post || ! is_string( $post->post_content ) || ! str_contains( $post->post_content, 'liftedpath/donate-panel' ) ) {
			continue;
		}

		$blocks = parse_blocks( $post->post_content );

		if ( ! liftedpath_migrate_donate_bank_blocks( $blocks, $defaults, $keys ) ) {
			continue;
		}

		$content = serialize_blocks( $blocks );

		if ( ! is_string( $content ) || '' === $content ) {
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

	update_option( 'liftedpath_donate_bank_migrated_v1', 1, false );
}

/**
 * @param array<int, array<string, mixed>> $blocks   Parsed blocks.
 * @param array<string, string>            $defaults Field values.
 * @param array<string, string>            $keys     ACF field keys.
 */
function liftedpath_migrate_donate_bank_blocks( array &$blocks, array $defaults, array $keys ): bool {
	$changed = false;

	foreach ( $blocks as &$block ) {
		if ( isset( $block['innerBlocks'] ) && is_array( $block['innerBlocks'] ) && liftedpath_migrate_donate_bank_blocks( $block['innerBlocks'], $defaults, $keys ) ) {
			$changed = true;
		}

		if ( ( $block['blockName'] ?? '' ) !== 'liftedpath/donate-panel' ) {
			continue;
		}

		if ( ! isset( $block['attrs'] ) || ! is_array( $block['attrs'] ) ) {
			$block['attrs'] = array();
		}

		if ( ! isset( $block['attrs']['data'] ) || ! is_array( $block['attrs']['data'] ) ) {
			$block['attrs']['data'] = array();
		}

		$number = isset( $block['attrs']['data']['bank_account_number'] ) ? trim( (string) $block['attrs']['data']['bank_account_number'] ) : '';

		if ( '' !== $number ) {
			continue;
		}

		foreach ( $defaults as $name => $value ) {
			$block['attrs']['data'][ $name ]        = $value;
			$block['attrs']['data'][ '_' . $name ] = $keys[ $name ];
		}

		$changed = true;
	}
	unset( $block );

	return $changed;
}
add_action( 'init', 'liftedpath_migrate_donate_bank', 32 );

/**
 * Record the phone order on existing donate panels once.
 */
function liftedpath_migrate_donate_bank_mobile_order(): void {
	if ( get_option( 'liftedpath_donate_bank_mobile_order_v1' ) ) {
		return;
	}

	try {
		liftedpath_migrate_donate_bank_mobile_order_run();
	} catch ( Throwable $exception ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
		unset( $exception );
	}
}

/**
 * Write Before the form when a donate panel has no phone order yet.
 */
function liftedpath_migrate_donate_bank_mobile_order_run(): void {
	global $wpdb;

	$ids = $wpdb->get_col(
		$wpdb->prepare(
			"SELECT ID FROM {$wpdb->posts} WHERE post_content LIKE %s AND post_status != %s",
			'%liftedpath/donate-panel%',
			'auto-draft'
		)
	);

	if ( ! is_array( $ids ) || array() === $ids ) {
		return;
	}

	foreach ( $ids as $id ) {
		$post = get_post( (int) $id );

		if ( ! $post instanceof WP_Post || ! is_string( $post->post_content ) || ! str_contains( $post->post_content, 'liftedpath/donate-panel' ) ) {
			continue;
		}

		$blocks = parse_blocks( $post->post_content );

		if ( ! liftedpath_migrate_donate_bank_mobile_order_blocks( $blocks ) ) {
			continue;
		}

		$content = serialize_blocks( $blocks );

		if ( ! is_string( $content ) || '' === $content ) {
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

	update_option( 'liftedpath_donate_bank_mobile_order_v1', 1, false );
}

/**
 * Fill an empty bank_mobile_order on donate panels.
 *
 * @param array<int, array<string, mixed>> $blocks Parsed blocks.
 */
function liftedpath_migrate_donate_bank_mobile_order_blocks( array &$blocks ): bool {
	$changed = false;

	foreach ( $blocks as &$block ) {
		if ( isset( $block['innerBlocks'] ) && is_array( $block['innerBlocks'] ) && liftedpath_migrate_donate_bank_mobile_order_blocks( $block['innerBlocks'] ) ) {
			$changed = true;
		}

		if ( ( $block['blockName'] ?? '' ) !== 'liftedpath/donate-panel' ) {
			continue;
		}

		if ( ! isset( $block['attrs'] ) || ! is_array( $block['attrs'] ) ) {
			$block['attrs'] = array();
		}

		if ( ! isset( $block['attrs']['data'] ) || ! is_array( $block['attrs']['data'] ) ) {
			$block['attrs']['data'] = array();
		}

		$order = isset( $block['attrs']['data']['bank_mobile_order'] ) ? trim( (string) $block['attrs']['data']['bank_mobile_order'] ) : '';

		if ( 'before' === $order || 'after' === $order ) {
			continue;
		}

		$block['attrs']['data']['bank_mobile_order']  = 'before';
		$block['attrs']['data']['_bank_mobile_order'] = 'field_liftedpath_donate_bank_mobile_order';
		$changed = true;
	}
	unset( $block );

	return $changed;
}
add_action( 'init', 'liftedpath_migrate_donate_bank_mobile_order', 33 );

/**
 * Child pages. The description is the sentence already written. No new essay.
 *
 * @param array<string, int> $images Attachment IDs.
 * @return array<string, array<string, mixed>>
 */
function liftedpath_seed_child_pages( array $images ): array {
	$paths = array(
		'learning/lift-root',
		'learning/lift-grow',
		'learning/lift-beyond',
		'learning/lift-equip',
		'what-we-do/path-guide',
		'what-we-do/mentorship-coaching',
		'what-we-do/community-initiatives',
		'what-we-do/lift-beyond-barriers',
		'what-we-do/lift-beyond-borders',
		'what-we-do/orphanage-outreach',
		'what-we-do/share-the-joy-of-the-season',
	);
	$pages = array();

	foreach ( $paths as $path ) {
		$page = liftedpath_seed_work_layout_page( $path, $images );
		if ( array() === $page ) {
			continue;
		}
		$pages[ $path ] = $page;
		$catalog        = liftedpath_work_page_catalog( $images );
		$pages[ $path ]['image']   = (int) ( $catalog[ $path ]['image'] ?? 0 );
		$pages[ $path ]['eyebrow'] = (string) ( $catalog[ $path ]['subtitle'] ?? '' );
	}

	return $pages;
}

/**
 * @param array<string, int> $images Attachment IDs.
 */
function liftedpath_seed_stewardship( array $images, int $count, string $eyebrow, string $heading, string $intro, array $button ): string {
	$points = array(
		array( 'title' => '85% of funds go directly to programs', 'text' => 'We prioritize the direct delivery of our programs, ensuring the majority of funds entrusted to LiftedPath create meaningful opportunities for the communities we serve.' ),
		array( 'title' => 'Direct payment to institutions', 'text' => 'Where possible we make educational payments directly to schools, training providers and other relevant institutions on behalf of beneficiaries.' ),
		array( 'title' => 'Annual audits and reports', 'text' => 'We maintain proper financial oversight through annual financial reviews and reporting, for transparency and accountability.' ),
		array( 'title' => 'Donation tracking and receipts', 'text' => 'Every donation is documented and tracked, with records and receipts maintained as a clear account of funds received and their use.' ),
	);

	return liftedpath_seed_block(
		'stewardship',
		liftedpath_seed_data(
			array(
				array( 'name' => 'eyebrow', 'key' => 'field_liftedpath_stewardship_eyebrow', 'value' => $eyebrow ),
				array( 'name' => 'heading', 'key' => 'field_liftedpath_stewardship_heading', 'value' => $heading ),
				array( 'name' => 'intro', 'key' => 'field_liftedpath_stewardship_intro', 'value' => $intro ),
				array(
					'name' => 'points',
					'key'  => 'field_liftedpath_stewardship_points',
					'rows' => array_slice( $points, 0, $count ),
					'subs' => array(
						'title' => 'field_liftedpath_stewardship_point_title',
						'text'  => 'field_liftedpath_stewardship_point_text',
					),
				),
				array( 'name' => 'button', 'key' => 'field_liftedpath_stewardship_button', 'value' => $button ),
				array( 'name' => 'image', 'key' => 'field_liftedpath_stewardship_image', 'value' => $images['skills'] ?? 0 ),
				array( 'name' => 'inset_image', 'key' => 'field_liftedpath_stewardship_inset_image', 'value' => $images['orphanage'] ?? 0 ),
				array( 'name' => 'note_label', 'key' => 'field_liftedpath_stewardship_note_label', 'value' => 'Call Us' ),
			)
		)
	);
}

/**
 * Four impact figures.
 */
function liftedpath_seed_stats(): string {
	$stats = array(
		array( 'figure' => '25+', 'label' => 'Students supported', 'detail' => 'Students supported with educational assistance and scholarships.' ),
		array( 'figure' => '15', 'label' => 'Families reached', 'detail' => 'Families reached through our Share the Joy of the Season initiative with food items and financial assistance.' ),
		array( 'figure' => '6', 'label' => 'School partnerships', 'detail' => 'Partnerships with schools to identify students with potential who face financial barriers.' ),
		array( 'figure' => '2', 'label' => 'Years of orphanage visits', 'detail' => 'One orphanage home visited consistently for two years with care and essentials.' ),
	);
	$inner = '';

	foreach ( $stats as $stat ) {
		$inner .= liftedpath_seed_block(
			'stat',
			liftedpath_seed_data(
				array(
					array( 'name' => 'figure', 'key' => 'field_liftedpath_stat_figure', 'value' => $stat['figure'] ),
					array( 'name' => 'label', 'key' => 'field_liftedpath_stat_label', 'value' => $stat['label'] ),
					array( 'name' => 'detail', 'key' => 'field_liftedpath_stat_detail', 'value' => $stat['detail'] ),
				)
			),
			false
		);
	}

	$json = wp_json_encode(
		array(
			'name'  => 'liftedpath/stat-group',
			'align' => 'full',
			'mode'  => 'preview',
		)
	);

	return "<!-- wp:liftedpath/stat-group {$json} -->\n{$inner}<!-- /wp:liftedpath/stat-group -->\n\n";
}

/**
 * Numbered pillars.
 */
function liftedpath_seed_pillars(): string {
	return liftedpath_seed_block(
		'pillars',
		liftedpath_seed_data(
			array(
				array( 'name' => 'eyebrow', 'key' => 'field_liftedpath_pillars_eyebrow', 'value' => 'Our pillars' ),
				array( 'name' => 'heading', 'key' => 'field_liftedpath_pillars_heading', 'value' => 'Stronger people. Brighter communities. Greater impact.' ),
				array(
					'name' => 'items',
					'key'  => 'field_liftedpath_pillars_items',
					'rows' => array(
						array( 'title' => 'Give', 'text' => 'Financial and in-kind donations that help fund our programs and initiatives.' ),
						array( 'title' => 'Partner', 'text' => 'We connect with individuals, schools, organizations and communities through our Community Outreach Representatives to identify people who can benefit from support.' ),
						array( 'title' => 'Empower', 'text' => 'Through education, skills development, coaching, mentorship, and practical assistance.' ),
						array( 'title' => 'Reachout', 'text' => 'We reach families and communities through initiatives such as Share the Joy of the Season and our orphanage outreach.' ),
					),
					'subs' => array(
						'title' => 'field_liftedpath_pillars_item_title',
						'text'  => 'field_liftedpath_pillars_item_text',
					),
				),
			)
		)
	);
}

/**
 * Six future goals.
 */
function liftedpath_seed_goals(): string {
	return liftedpath_seed_block(
		'goals',
		liftedpath_seed_data(
			array(
				array( 'name' => 'eyebrow', 'key' => 'field_liftedpath_goals_eyebrow', 'value' => 'Future goals' ),
				array( 'name' => 'heading', 'key' => 'field_liftedpath_goals_heading', 'value' => 'Where we are headed next.' ),
				array( 'name' => 'intro', 'key' => 'field_liftedpath_goals_intro', 'value' => 'We are building lasting pathways for more young people and families.' ),
				array(
					'name' => 'goals',
					'key'  => 'field_liftedpath_goals_goals',
					'rows' => array(
						array( 'title' => 'Reach More Learners', 'text' => 'Extend Educational Support to more students and learners across the communities we serve.' ),
						array( 'title' => 'Grow Skills & Opportunities', 'text' => 'Create more pathways for people to gain practical, vocational, and life skills that can open doors to new possibilities.' ),
						array( 'title' => 'Strengthen Guidance & Mentorship', 'text' => 'Build a network of mentors and coaches who can encourage, guide, and walk alongside people on their journey.' ),
						array( 'title' => 'Build Stronger Partnerships', 'text' => 'Deepen our relationships with schools, communities, organizations, and individuals who share our vision and can help extend our reach.' ),
						array( 'title' => 'Reach More Families & Communities', 'text' => 'Expand initiatives that bring practical support, care, connection, and opportunity to more people beyond the classroom.' ),
						array( 'title' => 'Build for Lasting Impact', 'text' => 'Strengthen our programs, systems, partnerships, and resources so the work we begin today can continue creating possibilities for years to come.' ),
					),
					'subs' => array(
						'title' => 'field_liftedpath_goals_goal_title',
						'text'  => 'field_liftedpath_goals_goal_text',
					),
				),
			)
		)
	);
}

/**
 * Closing photograph band.
 */
function liftedpath_seed_join_band( int $image ): string {
	return liftedpath_seed_block(
		'cta-band',
		liftedpath_seed_data(
			array(
				array( 'name' => 'tone', 'key' => 'field_liftedpath_cta_tone', 'value' => 'photo' ),
				array( 'name' => 'image', 'key' => 'field_liftedpath_cta_image', 'value' => $image ),
				array( 'name' => 'eyebrow', 'key' => 'field_liftedpath_cta_eyebrow', 'value' => 'Join the journey' ),
				array( 'name' => 'heading', 'key' => 'field_liftedpath_cta_heading', 'value' => "Let's lift young people together." ),
				array( 'name' => 'text', 'key' => 'field_liftedpath_cta_text', 'value' => '' ),
				array( 'name' => 'button', 'key' => 'field_liftedpath_cta_button', 'value' => liftedpath_seed_link( 'Get in touch', '/contact/' ) ),
			)
		)
	);
}

/**
 * Inner-page hero.
 */
function liftedpath_seed_page_hero( int $image, string $eyebrow, string $heading, string $intro, string $breadcrumb ): string {
	return liftedpath_seed_block(
		'page-hero',
		liftedpath_seed_data(
			array(
				array( 'name' => 'image', 'key' => 'field_liftedpath_page_hero_image', 'value' => $image ),
				array( 'name' => 'eyebrow', 'key' => 'field_liftedpath_page_hero_eyebrow', 'value' => $eyebrow ),
				array( 'name' => 'breadcrumb_current', 'key' => 'field_liftedpath_page_hero_breadcrumb_current', 'value' => $eyebrow ),
				array( 'name' => 'heading', 'key' => 'field_liftedpath_page_hero_heading', 'value' => $heading ),
				array( 'name' => 'intro', 'key' => 'field_liftedpath_page_hero_intro', 'value' => $intro ),
				array( 'name' => 'breadcrumb_label', 'key' => 'field_liftedpath_page_hero_breadcrumb', 'value' => $breadcrumb ),
			)
		)
	);
}

/**
 * @param array<string, int> $pages Page IDs keyed by path.
 */
function liftedpath_seed_navigation( array $pages ): void {
	if ( ! liftedpath_seed_navigation_exists( 'primary' ) ) {
		$learning = liftedpath_seed_nav_submenu(
			'LiftedPath Learning',
			$pages['learning'],
			home_url( '/learning/' ),
			liftedpath_seed_nav_link( 'LIFT-ROOT', $pages['learning/lift-root'], home_url( '/learning/lift-root/' ), false )
			. liftedpath_seed_nav_link( 'LIFT-GROW', $pages['learning/lift-grow'], home_url( '/learning/lift-grow/' ), false )
			. liftedpath_seed_nav_link( 'LIFT-BEYOND', $pages['learning/lift-beyond'], home_url( '/learning/lift-beyond/' ), false )
			. liftedpath_seed_nav_link( 'LIFT-EQUIP', $pages['learning/lift-equip'], home_url( '/learning/lift-equip/' ), false ),
			false
		);
		$guide = liftedpath_seed_nav_submenu(
			'Path Guide',
			$pages['what-we-do/path-guide'],
			home_url( '/what-we-do/path-guide/' ),
			liftedpath_seed_nav_link( 'Mentorship & Coaching', $pages['what-we-do/mentorship-coaching'], home_url( '/what-we-do/mentorship-coaching/' ), false ),
			false
		);
		$community = liftedpath_seed_nav_submenu(
			'Community Initiatives',
			$pages['what-we-do/community-initiatives'],
			home_url( '/what-we-do/community-initiatives/' ),
			liftedpath_seed_nav_link( 'Lift Beyond Barriers', $pages['what-we-do/lift-beyond-barriers'], home_url( '/what-we-do/lift-beyond-barriers/' ), false )
			. liftedpath_seed_nav_link( 'Lift Beyond Borders', $pages['what-we-do/lift-beyond-borders'], home_url( '/what-we-do/lift-beyond-borders/' ), false )
			. liftedpath_seed_nav_link( 'Orphanage Outreach', $pages['what-we-do/orphanage-outreach'], home_url( '/what-we-do/orphanage-outreach/' ), false )
			. liftedpath_seed_nav_link( 'Share the Joy of the Season', $pages['what-we-do/share-the-joy-of-the-season'], home_url( '/what-we-do/share-the-joy-of-the-season/' ), false ),
			false
		);
		$primary = liftedpath_seed_nav_link( 'Home', $pages['home'], home_url( '/' ) )
			. liftedpath_seed_nav_link( 'About Us', $pages['about'], home_url( '/about/' ) )
			. liftedpath_seed_nav_submenu( 'What We Do', $pages['what-we-do'], home_url( '/what-we-do/' ), $learning . $guide . $community )
			. liftedpath_seed_nav_link( 'Impact', $pages['impact'], home_url( '/impact/' ) )
			. liftedpath_seed_nav_link( 'Contact Us', $pages['contact'], home_url( '/contact/' ) );
		liftedpath_seed_navigation_post( 'primary', 'Primary', $primary );
	}

	if ( ! liftedpath_seed_navigation_exists( 'footer' ) ) {
		$quick = liftedpath_seed_nav_group(
			'Quick Links',
			liftedpath_seed_nav_link( 'Home', $pages['home'], home_url( '/' ), false )
			. liftedpath_seed_nav_link( 'About Us', $pages['about'], home_url( '/about/' ), false )
			. liftedpath_seed_nav_link( 'What We Do', $pages['what-we-do'], home_url( '/what-we-do/' ), false )
			. liftedpath_seed_nav_link( 'Impact', $pages['impact'], home_url( '/impact/' ), false )
			. liftedpath_seed_nav_link( 'Contact Us', $pages['contact'], home_url( '/contact/' ), false )
			. liftedpath_seed_nav_link( 'Donate', $pages['donate'], home_url( '/donate/' ), false )
		);
		$work = liftedpath_seed_nav_group(
			'What We Do',
			liftedpath_seed_nav_link( 'LiftedPath Learning', $pages['learning'], home_url( '/learning/' ), false )
			. liftedpath_seed_nav_link( 'Path Guide', $pages['what-we-do/path-guide'], home_url( '/what-we-do/path-guide/' ), false )
			. liftedpath_seed_nav_link( 'Community Initiatives', $pages['what-we-do/community-initiatives'], home_url( '/what-we-do/community-initiatives/' ), false )
		);
		liftedpath_seed_navigation_post( 'footer', 'Footer', $quick . $work );
	}
}

/**
 * Whether a navigation slug is already published.
 */
function liftedpath_seed_navigation_exists( string $slug ): bool {
	$found = get_posts(
		array(
			'post_type'      => 'wp_navigation',
			'name'           => $slug,
			'post_status'    => array( 'publish', 'draft' ),
			'posts_per_page' => 1,
		)
	);

	return array() !== $found;
}

/**
 * Store one navigation post without encoding ampersands twice.
 */
function liftedpath_seed_navigation_post( string $slug, string $title, string $content ): void {
	$post_id = wp_insert_post(
		array(
			'post_type'    => 'wp_navigation',
			'post_status'  => 'publish',
			'post_title'   => $title,
			'post_name'    => $slug,
			'post_content' => ' ',
		),
		true
	);

	if ( ! is_int( $post_id ) || $post_id <= 0 ) {
		return;
	}

	global $wpdb;
	$wpdb->update(
		$wpdb->posts,
		array( 'post_content' => $content ),
		array( 'ID' => $post_id ),
		array( '%s' ),
		array( '%d' )
	);
	clean_post_cache( $post_id );
}

/**
 * Set the site title, tagline, and logo only when they are still the local default.
 *
 * @param array<string, int> $images Attachment IDs.
 */
function liftedpath_seed_site_identity( array $images ): void {
	if ( in_array( get_option( 'blogname' ), array( 'liftedpath', 'Liftedpath' ), true ) ) {
		update_option( 'blogname', 'LiftedPath' );
	}

	$tagline = function_exists( 'get_field' ) ? get_field( 'tagline', 'option' ) : '';
	if ( is_string( $tagline ) && '' !== $tagline && '' === get_option( 'blogdescription' ) ) {
		update_option( 'blogdescription', $tagline );
	}

	if ( ! get_theme_mod( 'custom_logo' ) && ! empty( $images['liftedpath-logo'] ) ) {
		set_theme_mod( 'custom_logo', $images['liftedpath-logo'] );
	}
}

/**
 * @param array<int, array<string, mixed>> $spec Field list.
 * @return array<string, mixed>
 */
function liftedpath_seed_data( array $spec ): array {
	$data = array();

	foreach ( $spec as $field ) {
		if ( isset( $field['rows'] ) ) {
			$data[ $field['name'] ]       = count( $field['rows'] );
			$data[ '_' . $field['name'] ] = $field['key'];
			foreach ( $field['rows'] as $index => $row ) {
				foreach ( $field['subs'] as $sub => $key ) {
					$data[ $field['name'] . '_' . $index . '_' . $sub ]        = $row[ $sub ];
					$data[ '_' . $field['name'] . '_' . $index . '_' . $sub ] = $key;
				}
			}
			continue;
		}

		$data[ $field['name'] ]       = $field['value'];
		$data[ '_' . $field['name'] ] = $field['key'];
	}

	return $data;
}

/**
 * One self-closing or wrapping LiftedPath block.
 *
 * @param array<string, mixed> $data  ACF block data.
 * @param bool                 $align Include the full alignment.
 */
function liftedpath_seed_block( string $slug, array $data, bool $align = true ): string {
	$payload = array(
		'name' => 'liftedpath/' . $slug,
		'data' => $data,
		'mode' => 'preview',
	);

	if ( $align ) {
		$payload['align'] = 'full';
	}

	$json = wp_json_encode( $payload );

	return "<!-- wp:liftedpath/{$slug} {$json} /-->\n\n";
}

/**
 * @return array{title: string, url: string, target: string}
 */
function liftedpath_seed_link( string $title, string $path ): array {
	return array(
		'title'  => $title,
		'url'    => home_url( $path ),
		'target' => '',
	);
}

/**
 * @return array<string, mixed>
 */
function liftedpath_seed_page_record( string $slug, string $title, string $parent, string $content, string $seo_title, string $seo_description, string $hero_summary ): array {
	return array(
		'slug'            => $slug,
		'title'           => $title,
		'parent'          => $parent,
		'content'         => $content,
		'seo_title'       => $seo_title,
		'seo_description' => $seo_description,
		'hero_summary'    => $hero_summary,
	);
}

/**
 * About story beside one photograph.
 */
function liftedpath_seed_about_story( int $image, string $eyebrow, string $heading, string $body, string $alt ): string {
	return liftedpath_seed_block(
		'about-story',
		liftedpath_seed_data(
			array(
				array( 'name' => 'eyebrow', 'key' => 'field_liftedpath_about_story_eyebrow', 'value' => $eyebrow ),
				array( 'name' => 'heading', 'key' => 'field_liftedpath_about_story_heading', 'value' => $heading ),
				array( 'name' => 'body', 'key' => 'field_liftedpath_about_story_body', 'value' => $body ),
				array( 'name' => 'image', 'key' => 'field_liftedpath_about_story_image', 'value' => $image ),
				array( 'name' => 'image_alt', 'key' => 'field_liftedpath_about_story_image_alt', 'value' => $alt ),
			)
		)
	);
}

/**
 * Mission and vision columns.
 *
 * @param array<int, array{0: string, 1: string, 2: string}> $items Eyebrow, heading, and text.
 */
function liftedpath_seed_beliefs( array $items ): string {
	return liftedpath_seed_block(
		'beliefs',
		liftedpath_seed_data(
			array(
				array(
					'name' => 'items',
					'key'  => 'field_liftedpath_beliefs_items',
					'rows' => array_map(
						static function ( array $item ): array {
							return array(
								'eyebrow' => $item[0],
								'heading' => $item[1],
								'text'    => $item[2],
							);
						},
						$items
					),
					'subs' => array(
						'eyebrow' => 'field_liftedpath_beliefs_item_eyebrow',
						'heading' => 'field_liftedpath_beliefs_item_heading',
						'text'    => 'field_liftedpath_beliefs_item_text',
					),
				),
			)
		)
	);
}

/**
 * Core heading.
 */
function liftedpath_seed_heading( string $text ): string {
	return "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">" . esc_html( $text ) . "</h2>\n<!-- /wp:heading -->\n\n";
}

/**
 * Core paragraph.
 */
function liftedpath_seed_paragraph( string $text ): string {
	return "<!-- wp:paragraph -->\n<p>" . esc_html( $text ) . "</p>\n<!-- /wp:paragraph -->\n\n";
}

/**
 * Core image.
 */
function liftedpath_seed_image( int $id, string $alt ): string {
	if ( $id <= 0 ) {
		return '';
	}

	$src = wp_get_attachment_image_url( $id, 'large' );
	if ( ! is_string( $src ) ) {
		return '';
	}

	$json = wp_json_encode(
		array(
			'id'       => $id,
			'sizeSlug' => 'large',
		)
	);

	return "<!-- wp:image {$json} -->\n<figure class=\"wp-block-image size-large\"><img src=\"" . esc_url( $src ) . '" alt="' . esc_attr( $alt ) . '" class="wp-image-' . $id . "\"/></figure>\n<!-- /wp:image -->\n\n";
}

/**
 * Three text columns.
 *
 * @param array<int, array{0: string, 1: string}> $columns Heading and paragraph pairs.
 */
function liftedpath_seed_columns( array $columns ): string {
	$inner = '';

	foreach ( $columns as $column ) {
		$inner .= "<!-- wp:column -->\n<div class=\"wp-block-column\">\n";
		$inner .= "<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">" . esc_html( $column[0] ) . "</h3>\n<!-- /wp:heading -->\n\n";
		$inner .= liftedpath_seed_paragraph( $column[1] );
		$inner .= "</div>\n<!-- /wp:column -->\n\n";
	}

	return "<!-- wp:columns -->\n<div class=\"wp-block-columns\">\n{$inner}</div>\n<!-- /wp:columns -->\n\n";
}

/**
 * Navigation link.
 */
function liftedpath_seed_nav_link( string $label, int $id, string $url, bool $top = true ): string {
	$json = wp_json_encode(
		array(
			'label'          => $label,
			'type'           => 'page',
			'id'             => $id,
			'url'            => $url,
			'kind'           => 'post-type',
			'isTopLevelLink' => $top,
		)
	);

	return "<!-- wp:navigation-link {$json} /-->\n";
}

/**
 * Navigation submenu whose parent is a page.
 */
function liftedpath_seed_nav_submenu( string $label, int $id, string $url, string $inner, bool $top = true ): string {
	$json = wp_json_encode(
		array(
			'label'          => $label,
			'type'           => 'page',
			'id'             => $id,
			'url'            => $url,
			'kind'           => 'post-type',
			'isTopLevelItem' => $top,
		)
	);

	return "<!-- wp:navigation-submenu {$json} -->\n{$inner}<!-- /wp:navigation-submenu -->\n";
}

/**
 * Footer column. The label is the column heading.
 */
function liftedpath_seed_nav_group( string $label, string $inner ): string {
	$json = wp_json_encode(
		array(
			'label'          => $label,
			'type'           => 'custom',
			'url'            => '#',
			'kind'           => 'custom',
			'isTopLevelItem' => true,
		)
	);

	return "<!-- wp:navigation-submenu {$json} -->\n{$inner}<!-- /wp:navigation-submenu -->\n";
}
