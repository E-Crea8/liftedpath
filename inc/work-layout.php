<?php
/**
 * Work layout seed data and local page migration (Unit 27).
 *
 * @package LiftedPath
 */

/**
 * Design 01 work page definitions keyed by path.
 *
 * @param array<string, int> $images Attachment IDs.
 * @return array<string, array<string, mixed>>
 */
function liftedpath_work_page_catalog( array $images ): array {
	$catalog = array();

	$catalog[ 'what-we-do' ] = array(
		'slug'            => 'what-we-do',
		'title'           => 'What We Do',
		'parent'          => '',
		'seo_title'       => 'What We Do',
		'seo_description' => 'We help children, teenagers and young adults overcome financial and social barriers through education, practical skills, guidance and community support.',
		'hero_summary'    => 'Education, guidance and community support — connected by one purpose.',
		'image'           => $images[ 'scholarship' ] ?? 0,
		'subtitle'        => 'Education. Guidance. Community.',
		'hero_intro'      => 'Education, guidance and community support — connected by one purpose.',
				'end_eyebrow'      => 'Be part of the journey',
		'end_heading'      => 'Small acts. Meaningful change.',
		'end_text'         => 'Support this work with your time, resources or a partnership.',
		'end_button_title' => 'Let’s connect',
'layout'          => array(
			array( 'name' => 'values', 'key' => 'field_liftedpath_work_values', 'rows' => array(
				array( 'phrase' => 'Learning with purpose' ),
				array( 'phrase' => 'Guidance with care' ),
				array( 'phrase' => 'Opportunity with dignity' ),
			), 'subs' => array( 'phrase' => 'field_liftedpath_work_value_phrase' ) ),
			array( 'name' => 'sidebar_heading', 'key' => 'field_liftedpath_work_sidebar_heading', 'value' => 'Explore our work' ),
			array(
				'name' => 'nav_links',
				'key'  => 'field_liftedpath_work_nav',
				'rows' => array(
					array( 'title' => 'LiftedPath Learning', 'subtitle' => 'A pathway for every stage.', 'link' => liftedpath_seed_link( 'LiftedPath Learning', '/learning/' ) ),
						array( 'title' => 'Path Guide', 'subtitle' => 'Direction for the journey ahead.', 'link' => liftedpath_seed_link( 'Path Guide', '/what-we-do/path-guide/' ) ),
						array( 'title' => 'Community Initiatives', 'subtitle' => 'Care that reaches beyond the classroom.', 'link' => liftedpath_seed_link( 'Community Initiatives', '/what-we-do/community-initiatives/' ) ),
				),
				'subs' => array(
					'title'    => 'field_liftedpath_work_nav_title',
					'subtitle' => 'field_liftedpath_work_nav_subtitle',
					'link'     => 'field_liftedpath_work_nav_link',
				),
			),
				array( 'name' => 'show_back', 'key' => 'field_liftedpath_work_show_back', 'value' => 0 ),
				array( 'name' => 'back_label', 'key' => 'field_liftedpath_work_back_label', 'value' => '' ),
				array( 'name' => 'back_link', 'key' => 'field_liftedpath_work_back_link', 'value' => '' ),
			array( 'name' => 'contact_image', 'key' => 'field_liftedpath_work_contact_image', 'value' => $images['scholarship'] ?? ( $images[ 'scholarship' ] ?? 0 ) ),
			array( 'name' => 'contact_heading', 'key' => 'field_liftedpath_work_contact_heading', 'value' => 'A conversation can open a new path.' ),
			array( 'name' => 'contact_text', 'key' => 'field_liftedpath_work_contact_text', 'value' => 'Ask about support, share your skills or explore a partnership.' ),
			array( 'name' => 'contact_button', 'key' => 'field_liftedpath_work_contact_button', 'value' => liftedpath_seed_link( 'Get in touch', '/contact/' ) ),
			array( 'name' => 'contact_phone', 'key' => 'field_liftedpath_work_contact_phone', 'value' => '' ),
			array( 'name' => 'lead_image', 'key' => 'field_liftedpath_work_lead_image', 'value' => $images[ 'scholarship' ] ?? 0 ),
			array( 'name' => 'lead_caption', 'key' => 'field_liftedpath_work_lead_caption', 'value' => 'Education. Guidance. Community.' ),
			array( 'name' => 'lead_eyebrow', 'key' => 'field_liftedpath_work_lead_eyebrow', 'value' => 'Our purpose in action' ),
			array( 'name' => 'lead_heading', 'key' => 'field_liftedpath_work_lead_heading', 'value' => 'Connected pathways. Lasting possibility.' ),
			array( 'name' => 'lead_intro', 'key' => 'field_liftedpath_work_lead_intro', 'value' => 'We help children, teenagers and young adults overcome financial and social barriers through education, practical skills, guidance and community support.' ),
			array( 'name' => 'audience_kicker', 'key' => 'field_liftedpath_work_audience_kicker', 'value' => '01 / People at the heart' ),
			array( 'name' => 'audience_heading', 'key' => 'field_liftedpath_work_audience_heading', 'value' => 'Who this work helps' ),
			array( 'name' => 'audience_text', 'key' => 'field_liftedpath_work_audience_text', 'value' => 'Our work supports learners with the determination to move forward, and families who need practical support along the way.' ),
			array(
				'name' => 'support',
				'key'  => 'field_liftedpath_work_support',
				'rows' => array(
					array( 'title' => 'Access to learning', 'text' => 'Education support and practical training that help learners take their next step.' ),
						array( 'title' => 'Support beyond the classroom', 'text' => 'Mentorship, care and community connections that help people keep moving forward.' ),
				),
				'subs' => array(
					'title' => 'field_liftedpath_work_support_title',
					'text'  => 'field_liftedpath_work_support_text',
				),
			),
				array( 'name' => 'areas_kicker', 'key' => 'field_liftedpath_work_areas_kicker', 'value' => 'Three areas. One purpose.' ),
				array( 'name' => 'areas_heading', 'key' => 'field_liftedpath_work_areas_heading', 'value' => 'Our work, connected.' ),
				array(
					'name' => 'areas',
					'key'  => 'field_liftedpath_work_areas',
					'rows' => array(
						array( 'title' => 'LiftedPath Learning', 'subtitle' => 'A pathway for every stage.', 'text' => 'From early education to higher education and vocational development, our four pathways help young people continue learning and build a more independent future.', 'link' => liftedpath_seed_link( 'LiftedPath Learning', '/learning/' ) ),
						array( 'title' => 'Path Guide', 'subtitle' => 'Direction for the journey ahead.', 'text' => 'Progress takes more than financial support. Path Guide brings encouragement, mentorship and coaching to young people as they make decisions about learning, work and their future.', 'link' => liftedpath_seed_link( 'Path Guide', '/what-we-do/path-guide/' ) ),
						array( 'title' => 'Community Initiatives', 'subtitle' => 'Care that reaches beyond the classroom.', 'text' => 'Our community initiatives bring practical support, care and connection to children, young people and families facing barriers to opportunity.', 'link' => liftedpath_seed_link( 'Community Initiatives', '/what-we-do/community-initiatives/' ) ),
					),
					'subs' => array(
						'title'    => 'field_liftedpath_work_area_title',
						'subtitle' => 'field_liftedpath_work_area_subtitle',
						'link'     => 'field_liftedpath_work_area_link',
						'text'     => 'field_liftedpath_work_area_text',
					),
				),
			array( 'name' => 'why_kicker', 'key' => 'field_liftedpath_work_why_kicker', 'value' => '02 / Looking ahead' ),
			array( 'name' => 'why_heading', 'key' => 'field_liftedpath_work_why_heading', 'value' => 'Why it matters' ),
			array( 'name' => 'why_text', 'key' => 'field_liftedpath_work_why_text', 'value' => 'Opportunity grows when learning, encouragement and everyday needs are addressed together. Our three areas of work connect those pieces.' ),
			array(
				'name' => 'principles',
				'key'  => 'field_liftedpath_work_principles',
				'rows' => array(
					array( 'text' => 'Dignity and individual needs come first.' ),
					array( 'text' => 'Support is shaped by real needs and available resources.' ),
				),
				'subs' => array( 'text' => 'field_liftedpath_work_principle_text' ),
			),
			array( 'name' => 'faq_kicker', 'key' => 'field_liftedpath_work_faq_kicker', 'value' => 'A little more clarity' ),
			array( 'name' => 'faq_heading', 'key' => 'field_liftedpath_work_faq_heading', 'value' => 'Questions about this work' ),
			array(
				'name' => 'faq_items',
				'key'  => 'field_liftedpath_work_faq_items',
				'rows' => array(
					array( 'question' => 'Who is What We Do for?', 'answer' => 'Our work supports learners with the determination to move forward, and families who need practical support along the way.' ),
					array( 'question' => 'How can I discuss support?', 'answer' => 'Contact our team with the learner’s or community’s needs. We can discuss the relevant pathway and what support may be possible; contacting us does not guarantee funding or a place.' ),
					array( 'question' => 'How can I get involved?', 'answer' => 'You can donate, offer relevant skills or discuss a partnership to support What We Do. Get in touch so we can coordinate help around actual needs.' ),
				),
				'subs' => array(
					'question' => 'field_liftedpath_work_faq_question',
					'answer'   => 'field_liftedpath_work_faq_answer',
				),
			),
		),
	);

	$catalog[ 'learning' ] = array(
		'slug'            => 'learning',
		'title'           => 'LiftedPath Learning',
		'parent'          => '',
		'seo_title'       => 'LiftedPath Learning',
		'seo_description' => 'From early education to higher education and vocational development, our four pathways help young people continue learning and build a more independent future.',
		'hero_summary'    => 'From early education to higher education and vocational development — a pathway for every stage.',
		'image'           => $images[ 'classroom' ] ?? 0,
		'subtitle'        => 'A pathway for every stage.',
		'hero_intro'      => 'From early education to higher education and vocational development — a pathway for every stage.',
				'end_eyebrow'      => 'Be part of the journey',
		'end_heading'      => 'Keep possibility within reach.',
		'end_text'         => 'Support this work with your time, resources or a partnership.',
		'end_button_title' => 'Let’s connect',
'layout'          => array(
			array( 'name' => 'values', 'key' => 'field_liftedpath_work_values', 'rows' => array(
				array( 'phrase' => 'Learning with purpose' ),
				array( 'phrase' => 'Guidance with care' ),
				array( 'phrase' => 'Opportunity with dignity' ),
			), 'subs' => array( 'phrase' => 'field_liftedpath_work_value_phrase' ) ),
			array( 'name' => 'sidebar_heading', 'key' => 'field_liftedpath_work_sidebar_heading', 'value' => 'LiftedPath Learning' ),
			array(
				'name' => 'nav_links',
				'key'  => 'field_liftedpath_work_nav',
				'rows' => array(
					array( 'title' => 'LiftedPath Learning', 'subtitle' => 'A pathway for every stage.', 'link' => liftedpath_seed_link( 'LiftedPath Learning', '/learning/' ) ),
						array( 'title' => 'LIFT-ROOT', 'subtitle' => 'Early Education', 'link' => liftedpath_seed_link( 'LIFT-ROOT', '/learning/lift-root/' ) ),
						array( 'title' => 'LIFT-GROW', 'subtitle' => 'Secondary Education', 'link' => liftedpath_seed_link( 'LIFT-GROW', '/learning/lift-grow/' ) ),
						array( 'title' => 'LIFT-BEYOND', 'subtitle' => 'Higher Education', 'link' => liftedpath_seed_link( 'LIFT-BEYOND', '/learning/lift-beyond/' ) ),
						array( 'title' => 'LIFT-EQUIP', 'subtitle' => 'Skills & Vocational Development', 'link' => liftedpath_seed_link( 'LIFT-EQUIP', '/learning/lift-equip/' ) ),
				),
				'subs' => array(
					'title'    => 'field_liftedpath_work_nav_title',
					'subtitle' => 'field_liftedpath_work_nav_subtitle',
					'link'     => 'field_liftedpath_work_nav_link',
				),
			),
				array( 'name' => 'show_back', 'key' => 'field_liftedpath_work_show_back', 'value' => 1 ),
				array( 'name' => 'back_label', 'key' => 'field_liftedpath_work_back_label', 'value' => 'All our work' ),
				array( 'name' => 'back_link', 'key' => 'field_liftedpath_work_back_link', 'value' => liftedpath_seed_link( 'All our work', '/what-we-do/' ) ),
			array( 'name' => 'contact_image', 'key' => 'field_liftedpath_work_contact_image', 'value' => $images['scholarship'] ?? ( $images[ 'classroom' ] ?? 0 ) ),
			array( 'name' => 'contact_heading', 'key' => 'field_liftedpath_work_contact_heading', 'value' => 'A conversation can open a new path.' ),
			array( 'name' => 'contact_text', 'key' => 'field_liftedpath_work_contact_text', 'value' => 'Ask about support, share your skills or explore a partnership.' ),
			array( 'name' => 'contact_button', 'key' => 'field_liftedpath_work_contact_button', 'value' => liftedpath_seed_link( 'Get in touch', '/contact/' ) ),
			array( 'name' => 'contact_phone', 'key' => 'field_liftedpath_work_contact_phone', 'value' => '' ),
			array( 'name' => 'lead_image', 'key' => 'field_liftedpath_work_lead_image', 'value' => $images[ 'classroom' ] ?? 0 ),
			array( 'name' => 'lead_caption', 'key' => 'field_liftedpath_work_lead_caption', 'value' => 'A pathway for every stage.' ),
			array( 'name' => 'lead_eyebrow', 'key' => 'field_liftedpath_work_lead_eyebrow', 'value' => 'A pathway for every stage.' ),
			array( 'name' => 'lead_heading', 'key' => 'field_liftedpath_work_lead_heading', 'value' => 'About LiftedPath Learning' ),
			array( 'name' => 'lead_intro', 'key' => 'field_liftedpath_work_lead_intro', 'value' => 'From early education to higher education and vocational development, our four pathways help young people continue learning and build a more independent future.' ),
			array( 'name' => 'audience_kicker', 'key' => 'field_liftedpath_work_audience_kicker', 'value' => '01 / People at the heart' ),
			array( 'name' => 'audience_heading', 'key' => 'field_liftedpath_work_audience_heading', 'value' => 'Who this pathway helps' ),
			array( 'name' => 'audience_text', 'key' => 'field_liftedpath_work_audience_text', 'value' => 'Children, secondary school students, young adults and aspiring trainees whose education or training is limited by financial barriers.' ),
			array(
				'name' => 'support',
				'key'  => 'field_liftedpath_work_support',
				'rows' => array(
					array( 'title' => 'Educational assistance', 'text' => 'Where possible, financial education support is paid directly to schools and training providers.' ),
						array( 'title' => 'Encouragement to continue', 'text' => 'Guidance helps supported learners stay focused on their education and personal goals.' ),
				),
				'subs' => array(
					'title' => 'field_liftedpath_work_support_title',
					'text'  => 'field_liftedpath_work_support_text',
				),
			),
				array( 'name' => 'areas_kicker', 'key' => 'field_liftedpath_work_areas_kicker', 'value' => 'Explore this pathway' ),
				array( 'name' => 'areas_heading', 'key' => 'field_liftedpath_work_areas_heading', 'value' => 'Four stages of possibility.' ),
				array(
					'name' => 'areas',
					'key'  => 'field_liftedpath_work_areas',
					'rows' => array(
						array( 'title' => 'LIFT-ROOT', 'subtitle' => 'Early Education', 'text' => 'A strong beginning opens a lifetime of possibility. LIFT-ROOT helps young learners access the essentials that support their first steps in education.', 'link' => liftedpath_seed_link( 'LIFT-ROOT', '/learning/lift-root/' ) ),
						array( 'title' => 'LIFT-GROW', 'subtitle' => 'Secondary Education', 'text' => 'LIFT-GROW supports determined students through their secondary years, helping them stay in school and keep moving toward their goals.', 'link' => liftedpath_seed_link( 'LIFT-GROW', '/learning/lift-grow/' ) ),
						array( 'title' => 'LIFT-BEYOND', 'subtitle' => 'Higher Education', 'text' => 'LIFT-BEYOND helps young adults overcome financial barriers to further education and pursue the futures they imagine for themselves.', 'link' => liftedpath_seed_link( 'LIFT-BEYOND', '/learning/lift-beyond/' ) ),
						array( 'title' => 'LIFT-EQUIP', 'subtitle' => 'Skills & Vocational Development', 'text' => 'Practical training and tools build confidence, independence and pathways into meaningful work. LIFT-EQUIP supports young people in developing skills they can put to use.', 'link' => liftedpath_seed_link( 'LIFT-EQUIP', '/learning/lift-equip/' ) ),
					),
					'subs' => array(
						'title'    => 'field_liftedpath_work_area_title',
						'subtitle' => 'field_liftedpath_work_area_subtitle',
						'link'     => 'field_liftedpath_work_area_link',
						'text'     => 'field_liftedpath_work_area_text',
					),
				),
			array( 'name' => 'why_kicker', 'key' => 'field_liftedpath_work_why_kicker', 'value' => '02 / Looking ahead' ),
			array( 'name' => 'why_heading', 'key' => 'field_liftedpath_work_why_heading', 'value' => 'Why it matters' ),
			array( 'name' => 'why_text', 'key' => 'field_liftedpath_work_why_text', 'value' => 'A learner’s needs change over time. Support that meets each stage can help turn today’s opportunity into tomorrow’s possibilities.' ),
			array(
				'name' => 'principles',
				'key'  => 'field_liftedpath_work_principles',
				'rows' => array(
					array( 'text' => 'Dignity and individual needs come first.' ),
					array( 'text' => 'Support is shaped by real needs and available resources.' ),
				),
				'subs' => array( 'text' => 'field_liftedpath_work_principle_text' ),
			),
			array( 'name' => 'faq_kicker', 'key' => 'field_liftedpath_work_faq_kicker', 'value' => 'A little more clarity' ),
			array( 'name' => 'faq_heading', 'key' => 'field_liftedpath_work_faq_heading', 'value' => 'Questions about this work' ),
			array(
				'name' => 'faq_items',
				'key'  => 'field_liftedpath_work_faq_items',
				'rows' => array(
					array( 'question' => 'Who is LiftedPath Learning for?', 'answer' => 'Children, secondary school students, young adults and aspiring trainees whose education or training is limited by financial barriers.' ),
					array( 'question' => 'How can I discuss support?', 'answer' => 'Contact our team with the learner’s or community’s needs. We can discuss the relevant pathway and what support may be possible; contacting us does not guarantee funding or a place.' ),
					array( 'question' => 'How can I get involved?', 'answer' => 'You can donate, offer relevant skills or discuss a partnership to support LiftedPath Learning. Get in touch so we can coordinate help around actual needs.' ),
				),
				'subs' => array(
					'question' => 'field_liftedpath_work_faq_question',
					'answer'   => 'field_liftedpath_work_faq_answer',
				),
			),
		),
	);

	$catalog[ 'what-we-do/path-guide' ] = array(
		'slug'            => 'path-guide',
		'title'           => 'Path Guide',
		'parent'          => 'what-we-do',
		'seo_title'       => 'Path Guide',
		'seo_description' => 'Progress takes more than financial support. Path Guide brings encouragement, mentorship and coaching to young people as they make decisions about learning, work and their future.',
		'hero_summary'    => 'Progress takes more than financial support — guidance for the journey ahead.',
		'image'           => $images[ 'mentorship' ] ?? 0,
		'subtitle'        => 'Direction for the journey ahead.',
		'hero_intro'      => 'Progress takes more than financial support — guidance for the journey ahead.',
				'end_eyebrow'      => 'Be part of the journey',
		'end_heading'      => 'Help someone find their next step.',
		'end_text'         => 'Share your experience, become a mentor or explore a partnership.',
		'end_button_title' => 'Let’s connect',
'layout'          => array(
			array( 'name' => 'values', 'key' => 'field_liftedpath_work_values', 'rows' => array(
				array( 'phrase' => 'Learning with purpose' ),
				array( 'phrase' => 'Guidance with care' ),
				array( 'phrase' => 'Opportunity with dignity' ),
			), 'subs' => array( 'phrase' => 'field_liftedpath_work_value_phrase' ) ),
			array( 'name' => 'sidebar_heading', 'key' => 'field_liftedpath_work_sidebar_heading', 'value' => 'Path Guide' ),
			array(
				'name' => 'nav_links',
				'key'  => 'field_liftedpath_work_nav',
				'rows' => array(
					array( 'title' => 'Path Guide', 'subtitle' => 'Direction for the journey ahead.', 'link' => liftedpath_seed_link( 'Path Guide', '/what-we-do/path-guide/' ) ),
						array( 'title' => 'Mentorship & Coaching', 'subtitle' => 'Someone to walk alongside you.', 'link' => liftedpath_seed_link( 'Mentorship & Coaching', '/what-we-do/mentorship-coaching/' ) ),
				),
				'subs' => array(
					'title'    => 'field_liftedpath_work_nav_title',
					'subtitle' => 'field_liftedpath_work_nav_subtitle',
					'link'     => 'field_liftedpath_work_nav_link',
				),
			),
				array( 'name' => 'show_back', 'key' => 'field_liftedpath_work_show_back', 'value' => 1 ),
				array( 'name' => 'back_label', 'key' => 'field_liftedpath_work_back_label', 'value' => 'All our work' ),
				array( 'name' => 'back_link', 'key' => 'field_liftedpath_work_back_link', 'value' => liftedpath_seed_link( 'All our work', '/what-we-do/' ) ),
			array( 'name' => 'contact_image', 'key' => 'field_liftedpath_work_contact_image', 'value' => $images['scholarship'] ?? ( $images[ 'mentorship' ] ?? 0 ) ),
			array( 'name' => 'contact_heading', 'key' => 'field_liftedpath_work_contact_heading', 'value' => 'A conversation can open a new path.' ),
			array( 'name' => 'contact_text', 'key' => 'field_liftedpath_work_contact_text', 'value' => 'Ask about support, share your skills or explore a partnership.' ),
			array( 'name' => 'contact_button', 'key' => 'field_liftedpath_work_contact_button', 'value' => liftedpath_seed_link( 'Get in touch', '/contact/' ) ),
			array( 'name' => 'contact_phone', 'key' => 'field_liftedpath_work_contact_phone', 'value' => '' ),
			array( 'name' => 'lead_image', 'key' => 'field_liftedpath_work_lead_image', 'value' => $images[ 'mentorship' ] ?? 0 ),
			array( 'name' => 'lead_caption', 'key' => 'field_liftedpath_work_lead_caption', 'value' => 'Direction for the journey ahead.' ),
			array( 'name' => 'lead_eyebrow', 'key' => 'field_liftedpath_work_lead_eyebrow', 'value' => 'Direction for the journey ahead.' ),
			array( 'name' => 'lead_heading', 'key' => 'field_liftedpath_work_lead_heading', 'value' => 'About Path Guide' ),
			array( 'name' => 'lead_intro', 'key' => 'field_liftedpath_work_lead_intro', 'value' => 'Progress takes more than financial support. Path Guide brings encouragement, mentorship and coaching to young people as they make decisions about learning, work and their future.' ),
			array( 'name' => 'audience_kicker', 'key' => 'field_liftedpath_work_audience_kicker', 'value' => '01 / People at the heart' ),
			array( 'name' => 'audience_heading', 'key' => 'field_liftedpath_work_audience_heading', 'value' => 'Who this pathway helps' ),
			array( 'name' => 'audience_text', 'key' => 'field_liftedpath_work_audience_text', 'value' => 'Students and vocational trainees who need someone to listen, help them set goals and encourage them through challenges.' ),
			array(
				'name' => 'support',
				'key'  => 'field_liftedpath_work_support',
				'rows' => array(
					array( 'title' => 'Mentorship', 'text' => 'Encouragement with study habits, personal confidence and decisions about the next step.' ),
						array( 'title' => 'Coaching', 'text' => 'Practical conversations about goals, progress and the challenges that arise along the way.' ),
				),
				'subs' => array(
					'title' => 'field_liftedpath_work_support_title',
					'text'  => 'field_liftedpath_work_support_text',
				),
			),
				array( 'name' => 'areas_kicker', 'key' => 'field_liftedpath_work_areas_kicker', 'value' => 'Explore this pathway' ),
				array( 'name' => 'areas_heading', 'key' => 'field_liftedpath_work_areas_heading', 'value' => 'Mentorship that moves with you.' ),
				array(
					'name' => 'areas',
					'key'  => 'field_liftedpath_work_areas',
					'rows' => array(
						array( 'title' => 'Mentorship & Coaching', 'subtitle' => 'Someone to walk alongside you.', 'text' => 'Our mentors and coaches walk with students and trainees through study habits, career choices and confidence, helping them stay focused on their goals.', 'link' => liftedpath_seed_link( 'Mentorship & Coaching', '/what-we-do/mentorship-coaching/' ) ),
					),
					'subs' => array(
						'title'    => 'field_liftedpath_work_area_title',
						'subtitle' => 'field_liftedpath_work_area_subtitle',
						'link'     => 'field_liftedpath_work_area_link',
						'text'     => 'field_liftedpath_work_area_text',
					),
				),
			array( 'name' => 'why_kicker', 'key' => 'field_liftedpath_work_why_kicker', 'value' => '02 / Looking ahead' ),
			array( 'name' => 'why_heading', 'key' => 'field_liftedpath_work_why_heading', 'value' => 'Why it matters' ),
			array( 'name' => 'why_text', 'key' => 'field_liftedpath_work_why_text', 'value' => 'Consistent guidance can help young people recognise their strengths, make thoughtful choices and stay connected to the future they want to build.' ),
			array(
				'name' => 'principles',
				'key'  => 'field_liftedpath_work_principles',
				'rows' => array(
					array( 'text' => 'Dignity and individual needs come first.' ),
					array( 'text' => 'Support is shaped by real needs and available resources.' ),
				),
				'subs' => array( 'text' => 'field_liftedpath_work_principle_text' ),
			),
			array( 'name' => 'faq_kicker', 'key' => 'field_liftedpath_work_faq_kicker', 'value' => 'A little more clarity' ),
			array( 'name' => 'faq_heading', 'key' => 'field_liftedpath_work_faq_heading', 'value' => 'Questions about this work' ),
			array(
				'name' => 'faq_items',
				'key'  => 'field_liftedpath_work_faq_items',
				'rows' => array(
					array( 'question' => 'Who is Path Guide for?', 'answer' => 'Students and vocational trainees who need someone to listen, help them set goals and encourage them through challenges.' ),
					array( 'question' => 'How can I discuss support?', 'answer' => 'Contact our team with the learner’s or community’s needs. We can discuss the relevant pathway and what support may be possible; contacting us does not guarantee funding or a place.' ),
					array( 'question' => 'How can I get involved?', 'answer' => 'You can donate, offer relevant skills or discuss a partnership to support Path Guide. Get in touch so we can coordinate help around actual needs.' ),
				),
				'subs' => array(
					'question' => 'field_liftedpath_work_faq_question',
					'answer'   => 'field_liftedpath_work_faq_answer',
				),
			),
		),
	);

	$catalog[ 'what-we-do/mentorship-coaching' ] = array(
		'slug'            => 'mentorship-coaching',
		'title'           => 'Mentorship & Coaching',
		'parent'          => 'what-we-do',
		'seo_title'       => 'Mentorship & Coaching',
		'seo_description' => 'Our mentors and coaches walk with students and trainees through study habits, career choices and confidence, helping them stay focused on their goals.',
		'hero_summary'    => 'Mentors and coaches who walk with students and trainees toward their goals.',
		'image'           => $images[ 'mentorship' ] ?? 0,
		'subtitle'        => 'Someone to walk alongside you.',
		'hero_intro'      => 'Mentors and coaches who walk with students and trainees toward their goals.',
				'end_eyebrow'      => 'Be part of the journey',
		'end_heading'      => 'Help someone find their next step.',
		'end_text'         => 'Share your experience, become a mentor or explore a partnership.',
		'end_button_title' => 'Let’s connect',
'layout'          => array(
			array( 'name' => 'values', 'key' => 'field_liftedpath_work_values', 'rows' => array(
				array( 'phrase' => 'Learning with purpose' ),
				array( 'phrase' => 'Guidance with care' ),
				array( 'phrase' => 'Opportunity with dignity' ),
			), 'subs' => array( 'phrase' => 'field_liftedpath_work_value_phrase' ) ),
			array( 'name' => 'sidebar_heading', 'key' => 'field_liftedpath_work_sidebar_heading', 'value' => 'Path Guide' ),
			array(
				'name' => 'nav_links',
				'key'  => 'field_liftedpath_work_nav',
				'rows' => array(
					array( 'title' => 'Path Guide', 'subtitle' => 'Direction for the journey ahead.', 'link' => liftedpath_seed_link( 'Path Guide', '/what-we-do/path-guide/' ) ),
						array( 'title' => 'Mentorship & Coaching', 'subtitle' => 'Someone to walk alongside you.', 'link' => liftedpath_seed_link( 'Mentorship & Coaching', '/what-we-do/mentorship-coaching/' ) ),
				),
				'subs' => array(
					'title'    => 'field_liftedpath_work_nav_title',
					'subtitle' => 'field_liftedpath_work_nav_subtitle',
					'link'     => 'field_liftedpath_work_nav_link',
				),
			),
				array( 'name' => 'show_back', 'key' => 'field_liftedpath_work_show_back', 'value' => 1 ),
				array( 'name' => 'back_label', 'key' => 'field_liftedpath_work_back_label', 'value' => 'All our work' ),
				array( 'name' => 'back_link', 'key' => 'field_liftedpath_work_back_link', 'value' => liftedpath_seed_link( 'All our work', '/what-we-do/' ) ),
			array( 'name' => 'contact_image', 'key' => 'field_liftedpath_work_contact_image', 'value' => $images['scholarship'] ?? ( $images[ 'mentorship' ] ?? 0 ) ),
			array( 'name' => 'contact_heading', 'key' => 'field_liftedpath_work_contact_heading', 'value' => 'A conversation can open a new path.' ),
			array( 'name' => 'contact_text', 'key' => 'field_liftedpath_work_contact_text', 'value' => 'Ask about support, share your skills or explore a partnership.' ),
			array( 'name' => 'contact_button', 'key' => 'field_liftedpath_work_contact_button', 'value' => liftedpath_seed_link( 'Get in touch', '/contact/' ) ),
			array( 'name' => 'contact_phone', 'key' => 'field_liftedpath_work_contact_phone', 'value' => '' ),
			array( 'name' => 'lead_image', 'key' => 'field_liftedpath_work_lead_image', 'value' => $images[ 'mentorship' ] ?? 0 ),
			array( 'name' => 'lead_caption', 'key' => 'field_liftedpath_work_lead_caption', 'value' => 'Someone to walk alongside you.' ),
			array( 'name' => 'lead_eyebrow', 'key' => 'field_liftedpath_work_lead_eyebrow', 'value' => 'Someone to walk alongside you.' ),
			array( 'name' => 'lead_heading', 'key' => 'field_liftedpath_work_lead_heading', 'value' => 'About Mentorship & Coaching' ),
			array( 'name' => 'lead_intro', 'key' => 'field_liftedpath_work_lead_intro', 'value' => 'Our mentors and coaches walk with students and trainees through study habits, career choices and confidence, helping them stay focused on their goals.' ),
			array( 'name' => 'audience_kicker', 'key' => 'field_liftedpath_work_audience_kicker', 'value' => '01 / People at the heart' ),
			array( 'name' => 'audience_heading', 'key' => 'field_liftedpath_work_audience_heading', 'value' => 'Who this pathway helps' ),
			array( 'name' => 'audience_text', 'key' => 'field_liftedpath_work_audience_text', 'value' => 'Supported students and trainees navigating education, training and the transition toward greater independence.' ),
			array(
				'name' => 'support',
				'key'  => 'field_liftedpath_work_support',
				'rows' => array(
					array( 'title' => 'Set a clear direction', 'text' => 'Explore strengths, interests and realistic next steps in learning or work.' ),
						array( 'title' => 'Keep moving forward', 'text' => 'Reflect on progress, build positive habits and work through setbacks with encouragement.' ),
				),
				'subs' => array(
					'title' => 'field_liftedpath_work_support_title',
					'text'  => 'field_liftedpath_work_support_text',
				),
			),
				array( 'name' => 'areas_kicker', 'key' => 'field_liftedpath_work_areas_kicker', 'value' => '' ),
				array( 'name' => 'areas_heading', 'key' => 'field_liftedpath_work_areas_heading', 'value' => '' ),
				array(
					'name' => 'areas',
					'key'  => 'field_liftedpath_work_areas',
					'rows' => array(),
					'subs' => array(
						'title'    => 'field_liftedpath_work_area_title',
						'subtitle' => 'field_liftedpath_work_area_subtitle',
						'link'     => 'field_liftedpath_work_area_link',
						'text'     => 'field_liftedpath_work_area_text',
					),
				),
			array( 'name' => 'why_kicker', 'key' => 'field_liftedpath_work_why_kicker', 'value' => '02 / Looking ahead' ),
			array( 'name' => 'why_heading', 'key' => 'field_liftedpath_work_why_heading', 'value' => 'Why it matters' ),
			array( 'name' => 'why_text', 'key' => 'field_liftedpath_work_why_text', 'value' => 'Being heard and encouraged matters. Thoughtful guidance helps people see their options and take manageable steps toward their goals.' ),
			array(
				'name' => 'principles',
				'key'  => 'field_liftedpath_work_principles',
				'rows' => array(
					array( 'text' => 'Dignity and individual needs come first.' ),
					array( 'text' => 'Support is shaped by real needs and available resources.' ),
				),
				'subs' => array( 'text' => 'field_liftedpath_work_principle_text' ),
			),
			array( 'name' => 'faq_kicker', 'key' => 'field_liftedpath_work_faq_kicker', 'value' => 'A little more clarity' ),
			array( 'name' => 'faq_heading', 'key' => 'field_liftedpath_work_faq_heading', 'value' => 'Questions about this work' ),
			array(
				'name' => 'faq_items',
				'key'  => 'field_liftedpath_work_faq_items',
				'rows' => array(
					array( 'question' => 'Who is Mentorship & Coaching for?', 'answer' => 'Supported students and trainees navigating education, training and the transition toward greater independence.' ),
					array( 'question' => 'How can I discuss support?', 'answer' => 'Contact our team with the learner’s or community’s needs. We can discuss the relevant pathway and what support may be possible; contacting us does not guarantee funding or a place.' ),
					array( 'question' => 'How can I get involved?', 'answer' => 'You can donate, offer relevant skills or discuss a partnership to support Mentorship & Coaching. Get in touch so we can coordinate help around actual needs.' ),
				),
				'subs' => array(
					'question' => 'field_liftedpath_work_faq_question',
					'answer'   => 'field_liftedpath_work_faq_answer',
				),
			),
		),
	);

	$catalog[ 'what-we-do/community-initiatives' ] = array(
		'slug'            => 'community-initiatives',
		'title'           => 'Community Initiatives',
		'parent'          => 'what-we-do',
		'seo_title'       => 'Community Initiatives',
		'seo_description' => 'Our community initiatives bring practical support, care and connection to children, young people and families facing barriers to opportunity.',
		'hero_summary'    => 'Practical support, care and connection beyond the classroom.',
		'image'           => $images[ 'orphanage' ] ?? 0,
		'subtitle'        => 'Care that reaches beyond the classroom.',
		'hero_intro'      => 'Practical support, care and connection beyond the classroom.',
				'end_eyebrow'      => 'Be part of the journey',
		'end_heading'      => 'Small acts. Meaningful change.',
		'end_text'         => 'Support this work with your time, resources or a partnership.',
		'end_button_title' => 'Let’s connect',
'layout'          => array(
			array( 'name' => 'values', 'key' => 'field_liftedpath_work_values', 'rows' => array(
				array( 'phrase' => 'Learning with purpose' ),
				array( 'phrase' => 'Guidance with care' ),
				array( 'phrase' => 'Opportunity with dignity' ),
			), 'subs' => array( 'phrase' => 'field_liftedpath_work_value_phrase' ) ),
			array( 'name' => 'sidebar_heading', 'key' => 'field_liftedpath_work_sidebar_heading', 'value' => 'Community Initiatives' ),
			array(
				'name' => 'nav_links',
				'key'  => 'field_liftedpath_work_nav',
				'rows' => array(
					array( 'title' => 'Community Initiatives', 'subtitle' => 'Care that reaches beyond the classroom.', 'link' => liftedpath_seed_link( 'Community Initiatives', '/what-we-do/community-initiatives/' ) ),
						array( 'title' => 'Lift Beyond Barriers', 'subtitle' => 'Special Needs Education', 'link' => liftedpath_seed_link( 'Lift Beyond Barriers', '/what-we-do/lift-beyond-barriers/' ) ),
						array( 'title' => 'Lift Beyond Borders', 'subtitle' => 'Juvenile & Correctional Center Education Outreach', 'link' => liftedpath_seed_link( 'Lift Beyond Borders', '/what-we-do/lift-beyond-borders/' ) ),
						array( 'title' => 'Orphanage Outreach', 'subtitle' => 'Care. Connection. Belonging.', 'link' => liftedpath_seed_link( 'Orphanage Outreach', '/what-we-do/orphanage-outreach/' ) ),
						array( 'title' => 'Share the Joy of the Season', 'subtitle' => 'Food. Gifts. Smiles.', 'link' => liftedpath_seed_link( 'Share the Joy of the Season', '/what-we-do/share-the-joy-of-the-season/' ) ),
				),
				'subs' => array(
					'title'    => 'field_liftedpath_work_nav_title',
					'subtitle' => 'field_liftedpath_work_nav_subtitle',
					'link'     => 'field_liftedpath_work_nav_link',
				),
			),
				array( 'name' => 'show_back', 'key' => 'field_liftedpath_work_show_back', 'value' => 1 ),
				array( 'name' => 'back_label', 'key' => 'field_liftedpath_work_back_label', 'value' => 'All our work' ),
				array( 'name' => 'back_link', 'key' => 'field_liftedpath_work_back_link', 'value' => liftedpath_seed_link( 'All our work', '/what-we-do/' ) ),
			array( 'name' => 'contact_image', 'key' => 'field_liftedpath_work_contact_image', 'value' => $images['scholarship'] ?? ( $images[ 'orphanage' ] ?? 0 ) ),
			array( 'name' => 'contact_heading', 'key' => 'field_liftedpath_work_contact_heading', 'value' => 'A conversation can open a new path.' ),
			array( 'name' => 'contact_text', 'key' => 'field_liftedpath_work_contact_text', 'value' => 'Ask about support, share your skills or explore a partnership.' ),
			array( 'name' => 'contact_button', 'key' => 'field_liftedpath_work_contact_button', 'value' => liftedpath_seed_link( 'Get in touch', '/contact/' ) ),
			array( 'name' => 'contact_phone', 'key' => 'field_liftedpath_work_contact_phone', 'value' => '' ),
			array( 'name' => 'lead_image', 'key' => 'field_liftedpath_work_lead_image', 'value' => $images[ 'orphanage' ] ?? 0 ),
			array( 'name' => 'lead_caption', 'key' => 'field_liftedpath_work_lead_caption', 'value' => 'Care that reaches beyond the classroom.' ),
			array( 'name' => 'lead_eyebrow', 'key' => 'field_liftedpath_work_lead_eyebrow', 'value' => 'Care that reaches beyond the classroom.' ),
			array( 'name' => 'lead_heading', 'key' => 'field_liftedpath_work_lead_heading', 'value' => 'About Community Initiatives' ),
			array( 'name' => 'lead_intro', 'key' => 'field_liftedpath_work_lead_intro', 'value' => 'Our community initiatives bring practical support, care and connection to children, young people and families facing barriers to opportunity.' ),
			array( 'name' => 'audience_kicker', 'key' => 'field_liftedpath_work_audience_kicker', 'value' => '01 / People at the heart' ),
			array( 'name' => 'audience_heading', 'key' => 'field_liftedpath_work_audience_heading', 'value' => 'Who this pathway helps' ),
			array( 'name' => 'audience_text', 'key' => 'field_liftedpath_work_audience_text', 'value' => 'Families facing financial hardship, children in orphanage homes and young people whose circumstances can make education harder to access.' ),
			array(
				'name' => 'support',
				'key'  => 'field_liftedpath_work_support',
				'rows' => array(
					array( 'title' => 'Practical care', 'text' => 'Food, essentials and encouragement through orphanage visits and seasonal outreach.' ),
						array( 'title' => 'Inclusive opportunity', 'text' => 'A focus on education for young people with special needs and those in juvenile or correctional settings.' ),
				),
				'subs' => array(
					'title' => 'field_liftedpath_work_support_title',
					'text'  => 'field_liftedpath_work_support_text',
				),
			),
				array( 'name' => 'areas_kicker', 'key' => 'field_liftedpath_work_areas_kicker', 'value' => 'Explore this pathway' ),
				array( 'name' => 'areas_heading', 'key' => 'field_liftedpath_work_areas_heading', 'value' => 'Care takes many forms.' ),
				array(
					'name' => 'areas',
					'key'  => 'field_liftedpath_work_areas',
					'rows' => array(
						array( 'title' => 'Lift Beyond Barriers', 'subtitle' => 'Special Needs Education', 'text' => 'We believe young people with special educational needs should have access to learning that respects their dignity, strengths and individual needs.', 'link' => liftedpath_seed_link( 'Lift Beyond Barriers', '/what-we-do/lift-beyond-barriers/' ) ),
						array( 'title' => 'Lift Beyond Borders', 'subtitle' => 'Juvenile & Correctional Center Education Outreach', 'text' => 'Lift Beyond Borders focuses on education and encouragement for young people in juvenile and correctional settings, recognising that a difficult chapter should not define an entire future.', 'link' => liftedpath_seed_link( 'Lift Beyond Borders', '/what-we-do/lift-beyond-borders/' ) ),
						array( 'title' => 'Orphanage Outreach', 'subtitle' => 'Care. Connection. Belonging.', 'text' => 'We visit orphanage homes to share care, encouragement and essentials, creating moments of connection that remind children they are seen, valued and remembered.', 'link' => liftedpath_seed_link( 'Orphanage Outreach', '/what-we-do/orphanage-outreach/' ) ),
						array( 'title' => 'Share the Joy of the Season', 'subtitle' => 'Food. Gifts. Smiles.', 'text' => 'Each festive season we reach families facing financial hardship with essential food items and financial assistance, helping them experience the season with dignity.', 'link' => liftedpath_seed_link( 'Share the Joy of the Season', '/what-we-do/share-the-joy-of-the-season/' ) ),
					),
					'subs' => array(
						'title'    => 'field_liftedpath_work_area_title',
						'subtitle' => 'field_liftedpath_work_area_subtitle',
						'link'     => 'field_liftedpath_work_area_link',
						'text'     => 'field_liftedpath_work_area_text',
					),
				),
			array( 'name' => 'why_kicker', 'key' => 'field_liftedpath_work_why_kicker', 'value' => '02 / Looking ahead' ),
			array( 'name' => 'why_heading', 'key' => 'field_liftedpath_work_why_heading', 'value' => 'Why it matters' ),
			array( 'name' => 'why_text', 'key' => 'field_liftedpath_work_why_text', 'value' => 'Education does not happen in isolation. Care, inclusion and practical support help create the conditions in which people can move forward with dignity.' ),
			array(
				'name' => 'principles',
				'key'  => 'field_liftedpath_work_principles',
				'rows' => array(
					array( 'text' => 'Dignity and individual needs come first.' ),
					array( 'text' => 'Support is shaped by real needs and available resources.' ),
				),
				'subs' => array( 'text' => 'field_liftedpath_work_principle_text' ),
			),
			array( 'name' => 'faq_kicker', 'key' => 'field_liftedpath_work_faq_kicker', 'value' => 'A little more clarity' ),
			array( 'name' => 'faq_heading', 'key' => 'field_liftedpath_work_faq_heading', 'value' => 'Questions about this work' ),
			array(
				'name' => 'faq_items',
				'key'  => 'field_liftedpath_work_faq_items',
				'rows' => array(
					array( 'question' => 'Who is Community Initiatives for?', 'answer' => 'Families facing financial hardship, children in orphanage homes and young people whose circumstances can make education harder to access.' ),
					array( 'question' => 'How can I discuss support?', 'answer' => 'Contact our team with the learner’s or community’s needs. We can discuss the relevant pathway and what support may be possible; contacting us does not guarantee funding or a place.' ),
					array( 'question' => 'How can I get involved?', 'answer' => 'You can donate, offer relevant skills or discuss a partnership to support Community Initiatives. Get in touch so we can coordinate help around actual needs.' ),
				),
				'subs' => array(
					'question' => 'field_liftedpath_work_faq_question',
					'answer'   => 'field_liftedpath_work_faq_answer',
				),
			),
		),
	);

	$catalog[ 'what-we-do/lift-beyond-barriers' ] = array(
		'slug'            => 'lift-beyond-barriers',
		'title'           => 'Lift Beyond Barriers',
		'parent'          => 'what-we-do',
		'seo_title'       => 'Lift Beyond Barriers',
		'seo_description' => 'We believe young people with special educational needs should have access to learning that respects their dignity, strengths and individual needs.',
		'hero_summary'    => 'Inclusive learning that respects dignity, strengths and individual needs.',
		'image'           => $images[ 'classroom' ] ?? 0,
		'subtitle'        => 'Special Needs Education',
		'hero_intro'      => 'Inclusive learning that respects dignity, strengths and individual needs.',
				'end_eyebrow'      => 'Be part of the journey',
		'end_heading'      => 'Small acts. Meaningful change.',
		'end_text'         => 'Support this work with your time, resources or a partnership.',
		'end_button_title' => 'Let’s connect',
'layout'          => array(
			array( 'name' => 'values', 'key' => 'field_liftedpath_work_values', 'rows' => array(
				array( 'phrase' => 'Learning with purpose' ),
				array( 'phrase' => 'Guidance with care' ),
				array( 'phrase' => 'Opportunity with dignity' ),
			), 'subs' => array( 'phrase' => 'field_liftedpath_work_value_phrase' ) ),
			array( 'name' => 'sidebar_heading', 'key' => 'field_liftedpath_work_sidebar_heading', 'value' => 'Community Initiatives' ),
			array(
				'name' => 'nav_links',
				'key'  => 'field_liftedpath_work_nav',
				'rows' => array(
					array( 'title' => 'Community Initiatives', 'subtitle' => 'Care that reaches beyond the classroom.', 'link' => liftedpath_seed_link( 'Community Initiatives', '/what-we-do/community-initiatives/' ) ),
						array( 'title' => 'Lift Beyond Barriers', 'subtitle' => 'Special Needs Education', 'link' => liftedpath_seed_link( 'Lift Beyond Barriers', '/what-we-do/lift-beyond-barriers/' ) ),
						array( 'title' => 'Lift Beyond Borders', 'subtitle' => 'Juvenile & Correctional Center Education Outreach', 'link' => liftedpath_seed_link( 'Lift Beyond Borders', '/what-we-do/lift-beyond-borders/' ) ),
						array( 'title' => 'Orphanage Outreach', 'subtitle' => 'Care. Connection. Belonging.', 'link' => liftedpath_seed_link( 'Orphanage Outreach', '/what-we-do/orphanage-outreach/' ) ),
						array( 'title' => 'Share the Joy of the Season', 'subtitle' => 'Food. Gifts. Smiles.', 'link' => liftedpath_seed_link( 'Share the Joy of the Season', '/what-we-do/share-the-joy-of-the-season/' ) ),
				),
				'subs' => array(
					'title'    => 'field_liftedpath_work_nav_title',
					'subtitle' => 'field_liftedpath_work_nav_subtitle',
					'link'     => 'field_liftedpath_work_nav_link',
				),
			),
				array( 'name' => 'show_back', 'key' => 'field_liftedpath_work_show_back', 'value' => 1 ),
				array( 'name' => 'back_label', 'key' => 'field_liftedpath_work_back_label', 'value' => 'All our work' ),
				array( 'name' => 'back_link', 'key' => 'field_liftedpath_work_back_link', 'value' => liftedpath_seed_link( 'All our work', '/what-we-do/' ) ),
			array( 'name' => 'contact_image', 'key' => 'field_liftedpath_work_contact_image', 'value' => $images['scholarship'] ?? ( $images[ 'classroom' ] ?? 0 ) ),
			array( 'name' => 'contact_heading', 'key' => 'field_liftedpath_work_contact_heading', 'value' => 'A conversation can open a new path.' ),
			array( 'name' => 'contact_text', 'key' => 'field_liftedpath_work_contact_text', 'value' => 'Ask about support, share your skills or explore a partnership.' ),
			array( 'name' => 'contact_button', 'key' => 'field_liftedpath_work_contact_button', 'value' => liftedpath_seed_link( 'Get in touch', '/contact/' ) ),
			array( 'name' => 'contact_phone', 'key' => 'field_liftedpath_work_contact_phone', 'value' => '' ),
			array( 'name' => 'lead_image', 'key' => 'field_liftedpath_work_lead_image', 'value' => $images[ 'classroom' ] ?? 0 ),
			array( 'name' => 'lead_caption', 'key' => 'field_liftedpath_work_lead_caption', 'value' => 'Special Needs Education' ),
			array( 'name' => 'lead_eyebrow', 'key' => 'field_liftedpath_work_lead_eyebrow', 'value' => 'Special Needs Education' ),
			array( 'name' => 'lead_heading', 'key' => 'field_liftedpath_work_lead_heading', 'value' => 'About Lift Beyond Barriers' ),
			array( 'name' => 'lead_intro', 'key' => 'field_liftedpath_work_lead_intro', 'value' => 'We believe young people with special educational needs should have access to learning that respects their dignity, strengths and individual needs.' ),
			array( 'name' => 'audience_kicker', 'key' => 'field_liftedpath_work_audience_kicker', 'value' => '01 / People at the heart' ),
			array( 'name' => 'audience_heading', 'key' => 'field_liftedpath_work_audience_heading', 'value' => 'Who this pathway helps' ),
			array( 'name' => 'audience_text', 'key' => 'field_liftedpath_work_audience_text', 'value' => 'Young people with special educational needs and families seeking more inclusive opportunities for learning.' ),
			array(
				'name' => 'support',
				'key'  => 'field_liftedpath_work_support',
				'rows' => array(
					array( 'title' => 'Listen to individual needs', 'text' => 'Work with families and educators to understand the barriers a learner faces.' ),
						array( 'title' => 'Build inclusive pathways', 'text' => 'Explore appropriate educational support with schools and community partners.' ),
				),
				'subs' => array(
					'title' => 'field_liftedpath_work_support_title',
					'text'  => 'field_liftedpath_work_support_text',
				),
			),
				array( 'name' => 'areas_kicker', 'key' => 'field_liftedpath_work_areas_kicker', 'value' => '' ),
				array( 'name' => 'areas_heading', 'key' => 'field_liftedpath_work_areas_heading', 'value' => '' ),
				array(
					'name' => 'areas',
					'key'  => 'field_liftedpath_work_areas',
					'rows' => array(),
					'subs' => array(
						'title'    => 'field_liftedpath_work_area_title',
						'subtitle' => 'field_liftedpath_work_area_subtitle',
						'link'     => 'field_liftedpath_work_area_link',
						'text'     => 'field_liftedpath_work_area_text',
					),
				),
			array( 'name' => 'why_kicker', 'key' => 'field_liftedpath_work_why_kicker', 'value' => '02 / Looking ahead' ),
			array( 'name' => 'why_heading', 'key' => 'field_liftedpath_work_why_heading', 'value' => 'Why it matters' ),
			array( 'name' => 'why_text', 'key' => 'field_liftedpath_work_why_text', 'value' => 'Different learning needs should not mean fewer possibilities. Inclusive education helps young people participate, develop confidence and explore their potential.' ),
			array(
				'name' => 'principles',
				'key'  => 'field_liftedpath_work_principles',
				'rows' => array(
					array( 'text' => 'Dignity and individual needs come first.' ),
					array( 'text' => 'Support is shaped by real needs and available resources.' ),
				),
				'subs' => array( 'text' => 'field_liftedpath_work_principle_text' ),
			),
			array( 'name' => 'faq_kicker', 'key' => 'field_liftedpath_work_faq_kicker', 'value' => 'A little more clarity' ),
			array( 'name' => 'faq_heading', 'key' => 'field_liftedpath_work_faq_heading', 'value' => 'Questions about this work' ),
			array(
				'name' => 'faq_items',
				'key'  => 'field_liftedpath_work_faq_items',
				'rows' => array(
					array( 'question' => 'Who is Lift Beyond Barriers for?', 'answer' => 'Young people with special educational needs and families seeking more inclusive opportunities for learning.' ),
					array( 'question' => 'How can I discuss support?', 'answer' => 'Contact our team with the learner’s or community’s needs. We can discuss the relevant pathway and what support may be possible; contacting us does not guarantee funding or a place.' ),
					array( 'question' => 'How can I get involved?', 'answer' => 'You can donate, offer relevant skills or discuss a partnership to support Lift Beyond Barriers. Get in touch so we can coordinate help around actual needs.' ),
				),
				'subs' => array(
					'question' => 'field_liftedpath_work_faq_question',
					'answer'   => 'field_liftedpath_work_faq_answer',
				),
			),
		),
	);

	$catalog[ 'what-we-do/lift-beyond-borders' ] = array(
		'slug'            => 'lift-beyond-borders',
		'title'           => 'Lift Beyond Borders',
		'parent'          => 'what-we-do',
		'seo_title'       => 'Lift Beyond Borders',
		'seo_description' => 'Lift Beyond Borders focuses on education and encouragement for young people in juvenile and correctional settings, recognising that a difficult chapter should not define an entire future.',
		'hero_summary'    => 'Education and encouragement when a difficult chapter should not define a future.',
		'image'           => $images[ 'students' ] ?? 0,
		'subtitle'        => 'Juvenile & Correctional Center Education Outreach',
		'hero_intro'      => 'Education and encouragement when a difficult chapter should not define a future.',
				'end_eyebrow'      => 'Be part of the journey',
		'end_heading'      => 'Small acts. Meaningful change.',
		'end_text'         => 'Support this work with your time, resources or a partnership.',
		'end_button_title' => 'Let’s connect',
'layout'          => array(
			array( 'name' => 'values', 'key' => 'field_liftedpath_work_values', 'rows' => array(
				array( 'phrase' => 'Learning with purpose' ),
				array( 'phrase' => 'Guidance with care' ),
				array( 'phrase' => 'Opportunity with dignity' ),
			), 'subs' => array( 'phrase' => 'field_liftedpath_work_value_phrase' ) ),
			array( 'name' => 'sidebar_heading', 'key' => 'field_liftedpath_work_sidebar_heading', 'value' => 'Community Initiatives' ),
			array(
				'name' => 'nav_links',
				'key'  => 'field_liftedpath_work_nav',
				'rows' => array(
					array( 'title' => 'Community Initiatives', 'subtitle' => 'Care that reaches beyond the classroom.', 'link' => liftedpath_seed_link( 'Community Initiatives', '/what-we-do/community-initiatives/' ) ),
						array( 'title' => 'Lift Beyond Barriers', 'subtitle' => 'Special Needs Education', 'link' => liftedpath_seed_link( 'Lift Beyond Barriers', '/what-we-do/lift-beyond-barriers/' ) ),
						array( 'title' => 'Lift Beyond Borders', 'subtitle' => 'Juvenile & Correctional Center Education Outreach', 'link' => liftedpath_seed_link( 'Lift Beyond Borders', '/what-we-do/lift-beyond-borders/' ) ),
						array( 'title' => 'Orphanage Outreach', 'subtitle' => 'Care. Connection. Belonging.', 'link' => liftedpath_seed_link( 'Orphanage Outreach', '/what-we-do/orphanage-outreach/' ) ),
						array( 'title' => 'Share the Joy of the Season', 'subtitle' => 'Food. Gifts. Smiles.', 'link' => liftedpath_seed_link( 'Share the Joy of the Season', '/what-we-do/share-the-joy-of-the-season/' ) ),
				),
				'subs' => array(
					'title'    => 'field_liftedpath_work_nav_title',
					'subtitle' => 'field_liftedpath_work_nav_subtitle',
					'link'     => 'field_liftedpath_work_nav_link',
				),
			),
				array( 'name' => 'show_back', 'key' => 'field_liftedpath_work_show_back', 'value' => 1 ),
				array( 'name' => 'back_label', 'key' => 'field_liftedpath_work_back_label', 'value' => 'All our work' ),
				array( 'name' => 'back_link', 'key' => 'field_liftedpath_work_back_link', 'value' => liftedpath_seed_link( 'All our work', '/what-we-do/' ) ),
			array( 'name' => 'contact_image', 'key' => 'field_liftedpath_work_contact_image', 'value' => $images['scholarship'] ?? ( $images[ 'students' ] ?? 0 ) ),
			array( 'name' => 'contact_heading', 'key' => 'field_liftedpath_work_contact_heading', 'value' => 'A conversation can open a new path.' ),
			array( 'name' => 'contact_text', 'key' => 'field_liftedpath_work_contact_text', 'value' => 'Ask about support, share your skills or explore a partnership.' ),
			array( 'name' => 'contact_button', 'key' => 'field_liftedpath_work_contact_button', 'value' => liftedpath_seed_link( 'Get in touch', '/contact/' ) ),
			array( 'name' => 'contact_phone', 'key' => 'field_liftedpath_work_contact_phone', 'value' => '' ),
			array( 'name' => 'lead_image', 'key' => 'field_liftedpath_work_lead_image', 'value' => $images[ 'students' ] ?? 0 ),
			array( 'name' => 'lead_caption', 'key' => 'field_liftedpath_work_lead_caption', 'value' => 'Juvenile & Correctional Center Education Outreach' ),
			array( 'name' => 'lead_eyebrow', 'key' => 'field_liftedpath_work_lead_eyebrow', 'value' => 'Juvenile & Correctional Center Education Outreach' ),
			array( 'name' => 'lead_heading', 'key' => 'field_liftedpath_work_lead_heading', 'value' => 'About Lift Beyond Borders' ),
			array( 'name' => 'lead_intro', 'key' => 'field_liftedpath_work_lead_intro', 'value' => 'Lift Beyond Borders focuses on education and encouragement for young people in juvenile and correctional settings, recognising that a difficult chapter should not define an entire future.' ),
			array( 'name' => 'audience_kicker', 'key' => 'field_liftedpath_work_audience_kicker', 'value' => '01 / People at the heart' ),
			array( 'name' => 'audience_heading', 'key' => 'field_liftedpath_work_audience_heading', 'value' => 'Who this pathway helps' ),
			array( 'name' => 'audience_text', 'key' => 'field_liftedpath_work_audience_text', 'value' => 'Young people in juvenile and correctional settings who face barriers to continued learning and personal development.' ),
			array(
				'name' => 'support',
				'key'  => 'field_liftedpath_work_support',
				'rows' => array(
					array( 'title' => 'Learning opportunities', 'text' => 'Explore educational outreach in collaboration with relevant institutions.' ),
						array( 'title' => 'Guidance and encouragement', 'text' => 'Support conversations about personal growth, goals and future possibilities.' ),
				),
				'subs' => array(
					'title' => 'field_liftedpath_work_support_title',
					'text'  => 'field_liftedpath_work_support_text',
				),
			),
				array( 'name' => 'areas_kicker', 'key' => 'field_liftedpath_work_areas_kicker', 'value' => '' ),
				array( 'name' => 'areas_heading', 'key' => 'field_liftedpath_work_areas_heading', 'value' => '' ),
				array(
					'name' => 'areas',
					'key'  => 'field_liftedpath_work_areas',
					'rows' => array(),
					'subs' => array(
						'title'    => 'field_liftedpath_work_area_title',
						'subtitle' => 'field_liftedpath_work_area_subtitle',
						'link'     => 'field_liftedpath_work_area_link',
						'text'     => 'field_liftedpath_work_area_text',
					),
				),
			array( 'name' => 'why_kicker', 'key' => 'field_liftedpath_work_why_kicker', 'value' => '02 / Looking ahead' ),
			array( 'name' => 'why_heading', 'key' => 'field_liftedpath_work_why_heading', 'value' => 'Why it matters' ),
			array( 'name' => 'why_text', 'key' => 'field_liftedpath_work_why_text', 'value' => 'Learning can restore a sense of direction. Education and guidance can help young people imagine constructive next steps beyond their present circumstances.' ),
			array(
				'name' => 'principles',
				'key'  => 'field_liftedpath_work_principles',
				'rows' => array(
					array( 'text' => 'Dignity and individual needs come first.' ),
					array( 'text' => 'Support is shaped by real needs and available resources.' ),
				),
				'subs' => array( 'text' => 'field_liftedpath_work_principle_text' ),
			),
			array( 'name' => 'faq_kicker', 'key' => 'field_liftedpath_work_faq_kicker', 'value' => 'A little more clarity' ),
			array( 'name' => 'faq_heading', 'key' => 'field_liftedpath_work_faq_heading', 'value' => 'Questions about this work' ),
			array(
				'name' => 'faq_items',
				'key'  => 'field_liftedpath_work_faq_items',
				'rows' => array(
					array( 'question' => 'Who is Lift Beyond Borders for?', 'answer' => 'Young people in juvenile and correctional settings who face barriers to continued learning and personal development.' ),
					array( 'question' => 'How can I discuss support?', 'answer' => 'Contact our team with the learner’s or community’s needs. We can discuss the relevant pathway and what support may be possible; contacting us does not guarantee funding or a place.' ),
					array( 'question' => 'How can I get involved?', 'answer' => 'You can donate, offer relevant skills or discuss a partnership to support Lift Beyond Borders. Get in touch so we can coordinate help around actual needs.' ),
				),
				'subs' => array(
					'question' => 'field_liftedpath_work_faq_question',
					'answer'   => 'field_liftedpath_work_faq_answer',
				),
			),
		),
	);

	$catalog[ 'what-we-do/orphanage-outreach' ] = array(
		'slug'            => 'orphanage-outreach',
		'title'           => 'Orphanage Outreach',
		'parent'          => 'what-we-do',
		'seo_title'       => 'Orphanage Outreach',
		'seo_description' => 'We visit orphanage homes to share care, encouragement and essentials, creating moments of connection that remind children they are seen, valued and remembered.',
		'hero_summary'    => 'Care, encouragement and essentials that remind children they are seen and valued.',
		'image'           => $images[ 'orphanage' ] ?? 0,
		'subtitle'        => 'Care. Connection. Belonging.',
		'hero_intro'      => 'Care, encouragement and essentials that remind children they are seen and valued.',
				'end_eyebrow'      => 'Be part of the journey',
		'end_heading'      => 'Small acts. Meaningful change.',
		'end_text'         => 'Support this work with your time, resources or a partnership.',
		'end_button_title' => 'Let’s connect',
'layout'          => array(
			array( 'name' => 'values', 'key' => 'field_liftedpath_work_values', 'rows' => array(
				array( 'phrase' => 'Learning with purpose' ),
				array( 'phrase' => 'Guidance with care' ),
				array( 'phrase' => 'Opportunity with dignity' ),
			), 'subs' => array( 'phrase' => 'field_liftedpath_work_value_phrase' ) ),
			array( 'name' => 'sidebar_heading', 'key' => 'field_liftedpath_work_sidebar_heading', 'value' => 'Community Initiatives' ),
			array(
				'name' => 'nav_links',
				'key'  => 'field_liftedpath_work_nav',
				'rows' => array(
					array( 'title' => 'Community Initiatives', 'subtitle' => 'Care that reaches beyond the classroom.', 'link' => liftedpath_seed_link( 'Community Initiatives', '/what-we-do/community-initiatives/' ) ),
						array( 'title' => 'Lift Beyond Barriers', 'subtitle' => 'Special Needs Education', 'link' => liftedpath_seed_link( 'Lift Beyond Barriers', '/what-we-do/lift-beyond-barriers/' ) ),
						array( 'title' => 'Lift Beyond Borders', 'subtitle' => 'Juvenile & Correctional Center Education Outreach', 'link' => liftedpath_seed_link( 'Lift Beyond Borders', '/what-we-do/lift-beyond-borders/' ) ),
						array( 'title' => 'Orphanage Outreach', 'subtitle' => 'Care. Connection. Belonging.', 'link' => liftedpath_seed_link( 'Orphanage Outreach', '/what-we-do/orphanage-outreach/' ) ),
						array( 'title' => 'Share the Joy of the Season', 'subtitle' => 'Food. Gifts. Smiles.', 'link' => liftedpath_seed_link( 'Share the Joy of the Season', '/what-we-do/share-the-joy-of-the-season/' ) ),
				),
				'subs' => array(
					'title'    => 'field_liftedpath_work_nav_title',
					'subtitle' => 'field_liftedpath_work_nav_subtitle',
					'link'     => 'field_liftedpath_work_nav_link',
				),
			),
				array( 'name' => 'show_back', 'key' => 'field_liftedpath_work_show_back', 'value' => 1 ),
				array( 'name' => 'back_label', 'key' => 'field_liftedpath_work_back_label', 'value' => 'All our work' ),
				array( 'name' => 'back_link', 'key' => 'field_liftedpath_work_back_link', 'value' => liftedpath_seed_link( 'All our work', '/what-we-do/' ) ),
			array( 'name' => 'contact_image', 'key' => 'field_liftedpath_work_contact_image', 'value' => $images['scholarship'] ?? ( $images[ 'orphanage' ] ?? 0 ) ),
			array( 'name' => 'contact_heading', 'key' => 'field_liftedpath_work_contact_heading', 'value' => 'A conversation can open a new path.' ),
			array( 'name' => 'contact_text', 'key' => 'field_liftedpath_work_contact_text', 'value' => 'Ask about support, share your skills or explore a partnership.' ),
			array( 'name' => 'contact_button', 'key' => 'field_liftedpath_work_contact_button', 'value' => liftedpath_seed_link( 'Get in touch', '/contact/' ) ),
			array( 'name' => 'contact_phone', 'key' => 'field_liftedpath_work_contact_phone', 'value' => '' ),
			array( 'name' => 'lead_image', 'key' => 'field_liftedpath_work_lead_image', 'value' => $images[ 'orphanage' ] ?? 0 ),
			array( 'name' => 'lead_caption', 'key' => 'field_liftedpath_work_lead_caption', 'value' => 'Care. Connection. Belonging.' ),
			array( 'name' => 'lead_eyebrow', 'key' => 'field_liftedpath_work_lead_eyebrow', 'value' => 'Care. Connection. Belonging.' ),
			array( 'name' => 'lead_heading', 'key' => 'field_liftedpath_work_lead_heading', 'value' => 'About Orphanage Outreach' ),
			array( 'name' => 'lead_intro', 'key' => 'field_liftedpath_work_lead_intro', 'value' => 'We visit orphanage homes to share care, encouragement and essentials, creating moments of connection that remind children they are seen, valued and remembered.' ),
			array( 'name' => 'audience_kicker', 'key' => 'field_liftedpath_work_audience_kicker', 'value' => '01 / People at the heart' ),
			array( 'name' => 'audience_heading', 'key' => 'field_liftedpath_work_audience_heading', 'value' => 'Who this pathway helps' ),
			array( 'name' => 'audience_text', 'key' => 'field_liftedpath_work_audience_text', 'value' => 'Children in orphanage homes and the caregivers supporting their everyday needs.' ),
			array(
				'name' => 'support',
				'key'  => 'field_liftedpath_work_support',
				'rows' => array(
					array( 'title' => 'Everyday essentials', 'text' => 'Food items and essential supplies coordinated around the needs of the home.' ),
						array( 'title' => 'Meaningful connection', 'text' => 'Visits that bring encouragement, warmth and shared moments with children and caregivers.' ),
				),
				'subs' => array(
					'title' => 'field_liftedpath_work_support_title',
					'text'  => 'field_liftedpath_work_support_text',
				),
			),
				array( 'name' => 'areas_kicker', 'key' => 'field_liftedpath_work_areas_kicker', 'value' => '' ),
				array( 'name' => 'areas_heading', 'key' => 'field_liftedpath_work_areas_heading', 'value' => '' ),
				array(
					'name' => 'areas',
					'key'  => 'field_liftedpath_work_areas',
					'rows' => array(),
					'subs' => array(
						'title'    => 'field_liftedpath_work_area_title',
						'subtitle' => 'field_liftedpath_work_area_subtitle',
						'link'     => 'field_liftedpath_work_area_link',
						'text'     => 'field_liftedpath_work_area_text',
					),
				),
			array( 'name' => 'why_kicker', 'key' => 'field_liftedpath_work_why_kicker', 'value' => '02 / Looking ahead' ),
			array( 'name' => 'why_heading', 'key' => 'field_liftedpath_work_why_heading', 'value' => 'Why it matters' ),
			array( 'name' => 'why_text', 'key' => 'field_liftedpath_work_why_text', 'value' => 'Practical help meets immediate needs, while consistent care and connection help children feel a sense of belonging.' ),
			array(
				'name' => 'principles',
				'key'  => 'field_liftedpath_work_principles',
				'rows' => array(
					array( 'text' => 'Dignity and individual needs come first.' ),
					array( 'text' => 'Support is shaped by real needs and available resources.' ),
				),
				'subs' => array( 'text' => 'field_liftedpath_work_principle_text' ),
			),
			array( 'name' => 'faq_kicker', 'key' => 'field_liftedpath_work_faq_kicker', 'value' => 'A little more clarity' ),
			array( 'name' => 'faq_heading', 'key' => 'field_liftedpath_work_faq_heading', 'value' => 'Questions about this work' ),
			array(
				'name' => 'faq_items',
				'key'  => 'field_liftedpath_work_faq_items',
				'rows' => array(
					array( 'question' => 'Who is Orphanage Outreach for?', 'answer' => 'Children in orphanage homes and the caregivers supporting their everyday needs.' ),
					array( 'question' => 'How can I discuss support?', 'answer' => 'Contact our team with the learner’s or community’s needs. We can discuss the relevant pathway and what support may be possible; contacting us does not guarantee funding or a place.' ),
					array( 'question' => 'How can I get involved?', 'answer' => 'You can donate, offer relevant skills or discuss a partnership to support Orphanage Outreach. Get in touch so we can coordinate help around actual needs.' ),
				),
				'subs' => array(
					'question' => 'field_liftedpath_work_faq_question',
					'answer'   => 'field_liftedpath_work_faq_answer',
				),
			),
		),
	);

	$catalog[ 'what-we-do/share-the-joy-of-the-season' ] = array(
		'slug'            => 'share-the-joy-of-the-season',
		'title'           => 'Share the Joy of the Season',
		'parent'          => 'what-we-do',
		'seo_title'       => 'Share the Joy of the Season',
		'seo_description' => 'Each festive season we reach families facing financial hardship with essential food items and financial assistance, helping them experience the season with dignity.',
		'hero_summary'    => 'Food items and financial assistance so families can experience the season with dignity.',
		'image'           => $images[ 'season' ] ?? 0,
		'subtitle'        => 'Food. Gifts. Smiles.',
		'hero_intro'      => 'Food items and financial assistance so families can experience the season with dignity.',
				'end_eyebrow'      => 'Be part of the journey',
		'end_heading'      => 'Small acts. Meaningful change.',
		'end_text'         => 'Support this work with your time, resources or a partnership.',
		'end_button_title' => 'Let’s connect',
'layout'          => array(
			array( 'name' => 'values', 'key' => 'field_liftedpath_work_values', 'rows' => array(
				array( 'phrase' => 'Learning with purpose' ),
				array( 'phrase' => 'Guidance with care' ),
				array( 'phrase' => 'Opportunity with dignity' ),
			), 'subs' => array( 'phrase' => 'field_liftedpath_work_value_phrase' ) ),
			array( 'name' => 'sidebar_heading', 'key' => 'field_liftedpath_work_sidebar_heading', 'value' => 'Community Initiatives' ),
			array(
				'name' => 'nav_links',
				'key'  => 'field_liftedpath_work_nav',
				'rows' => array(
					array( 'title' => 'Community Initiatives', 'subtitle' => 'Care that reaches beyond the classroom.', 'link' => liftedpath_seed_link( 'Community Initiatives', '/what-we-do/community-initiatives/' ) ),
						array( 'title' => 'Lift Beyond Barriers', 'subtitle' => 'Special Needs Education', 'link' => liftedpath_seed_link( 'Lift Beyond Barriers', '/what-we-do/lift-beyond-barriers/' ) ),
						array( 'title' => 'Lift Beyond Borders', 'subtitle' => 'Juvenile & Correctional Center Education Outreach', 'link' => liftedpath_seed_link( 'Lift Beyond Borders', '/what-we-do/lift-beyond-borders/' ) ),
						array( 'title' => 'Orphanage Outreach', 'subtitle' => 'Care. Connection. Belonging.', 'link' => liftedpath_seed_link( 'Orphanage Outreach', '/what-we-do/orphanage-outreach/' ) ),
						array( 'title' => 'Share the Joy of the Season', 'subtitle' => 'Food. Gifts. Smiles.', 'link' => liftedpath_seed_link( 'Share the Joy of the Season', '/what-we-do/share-the-joy-of-the-season/' ) ),
				),
				'subs' => array(
					'title'    => 'field_liftedpath_work_nav_title',
					'subtitle' => 'field_liftedpath_work_nav_subtitle',
					'link'     => 'field_liftedpath_work_nav_link',
				),
			),
				array( 'name' => 'show_back', 'key' => 'field_liftedpath_work_show_back', 'value' => 1 ),
				array( 'name' => 'back_label', 'key' => 'field_liftedpath_work_back_label', 'value' => 'All our work' ),
				array( 'name' => 'back_link', 'key' => 'field_liftedpath_work_back_link', 'value' => liftedpath_seed_link( 'All our work', '/what-we-do/' ) ),
			array( 'name' => 'contact_image', 'key' => 'field_liftedpath_work_contact_image', 'value' => $images['scholarship'] ?? ( $images[ 'season' ] ?? 0 ) ),
			array( 'name' => 'contact_heading', 'key' => 'field_liftedpath_work_contact_heading', 'value' => 'A conversation can open a new path.' ),
			array( 'name' => 'contact_text', 'key' => 'field_liftedpath_work_contact_text', 'value' => 'Ask about support, share your skills or explore a partnership.' ),
			array( 'name' => 'contact_button', 'key' => 'field_liftedpath_work_contact_button', 'value' => liftedpath_seed_link( 'Get in touch', '/contact/' ) ),
			array( 'name' => 'contact_phone', 'key' => 'field_liftedpath_work_contact_phone', 'value' => '' ),
			array( 'name' => 'lead_image', 'key' => 'field_liftedpath_work_lead_image', 'value' => $images[ 'season' ] ?? 0 ),
			array( 'name' => 'lead_caption', 'key' => 'field_liftedpath_work_lead_caption', 'value' => 'Food. Gifts. Smiles.' ),
			array( 'name' => 'lead_eyebrow', 'key' => 'field_liftedpath_work_lead_eyebrow', 'value' => 'Food. Gifts. Smiles.' ),
			array( 'name' => 'lead_heading', 'key' => 'field_liftedpath_work_lead_heading', 'value' => 'About Share the Joy of the Season' ),
			array( 'name' => 'lead_intro', 'key' => 'field_liftedpath_work_lead_intro', 'value' => 'Each festive season we reach families facing financial hardship with essential food items and financial assistance, helping them experience the season with dignity.' ),
			array( 'name' => 'audience_kicker', 'key' => 'field_liftedpath_work_audience_kicker', 'value' => '01 / People at the heart' ),
			array( 'name' => 'audience_heading', 'key' => 'field_liftedpath_work_audience_heading', 'value' => 'Who this pathway helps' ),
			array( 'name' => 'audience_text', 'key' => 'field_liftedpath_work_audience_text', 'value' => 'Families facing financial hardship who need help meeting immediate household needs during the festive season.' ),
			array(
				'name' => 'support',
				'key'  => 'field_liftedpath_work_support',
				'rows' => array(
					array( 'title' => 'Food and essentials', 'text' => 'Help with everyday necessities that ease pressure on a household.' ),
						array( 'title' => 'Support with dignity', 'text' => 'Community-led outreach that respects families and brings people together.' ),
				),
				'subs' => array(
					'title' => 'field_liftedpath_work_support_title',
					'text'  => 'field_liftedpath_work_support_text',
				),
			),
				array( 'name' => 'areas_kicker', 'key' => 'field_liftedpath_work_areas_kicker', 'value' => '' ),
				array( 'name' => 'areas_heading', 'key' => 'field_liftedpath_work_areas_heading', 'value' => '' ),
				array(
					'name' => 'areas',
					'key'  => 'field_liftedpath_work_areas',
					'rows' => array(),
					'subs' => array(
						'title'    => 'field_liftedpath_work_area_title',
						'subtitle' => 'field_liftedpath_work_area_subtitle',
						'link'     => 'field_liftedpath_work_area_link',
						'text'     => 'field_liftedpath_work_area_text',
					),
				),
			array( 'name' => 'why_kicker', 'key' => 'field_liftedpath_work_why_kicker', 'value' => '02 / Looking ahead' ),
			array( 'name' => 'why_heading', 'key' => 'field_liftedpath_work_why_heading', 'value' => 'Why it matters' ),
			array( 'name' => 'why_text', 'key' => 'field_liftedpath_work_why_text', 'value' => 'The festive season can place extra pressure on struggling households. Practical support makes space for relief, connection and shared joy.' ),
			array(
				'name' => 'principles',
				'key'  => 'field_liftedpath_work_principles',
				'rows' => array(
					array( 'text' => 'Dignity and individual needs come first.' ),
					array( 'text' => 'Support is shaped by real needs and available resources.' ),
				),
				'subs' => array( 'text' => 'field_liftedpath_work_principle_text' ),
			),
			array( 'name' => 'faq_kicker', 'key' => 'field_liftedpath_work_faq_kicker', 'value' => 'A little more clarity' ),
			array( 'name' => 'faq_heading', 'key' => 'field_liftedpath_work_faq_heading', 'value' => 'Questions about this work' ),
			array(
				'name' => 'faq_items',
				'key'  => 'field_liftedpath_work_faq_items',
				'rows' => array(
					array( 'question' => 'Who is Share the Joy of the Season for?', 'answer' => 'Families facing financial hardship who need help meeting immediate household needs during the festive season.' ),
					array( 'question' => 'How can I discuss support?', 'answer' => 'Contact our team with the learner’s or community’s needs. We can discuss the relevant pathway and what support may be possible; contacting us does not guarantee funding or a place.' ),
					array( 'question' => 'How can I get involved?', 'answer' => 'You can donate, offer relevant skills or discuss a partnership to support Share the Joy of the Season. Get in touch so we can coordinate help around actual needs.' ),
				),
				'subs' => array(
					'question' => 'field_liftedpath_work_faq_question',
					'answer'   => 'field_liftedpath_work_faq_answer',
				),
			),
		),
	);

	$catalog[ 'learning/lift-root' ] = array(
		'slug'            => 'lift-root',
		'title'           => 'LIFT-ROOT',
		'parent'          => 'learning',
		'seo_title'       => 'LIFT-ROOT',
		'seo_description' => 'A strong beginning opens a lifetime of possibility. LIFT-ROOT helps young learners access the essentials that support their first steps in education.',
		'hero_summary'    => 'A strong beginning opens a lifetime of possibility.',
		'image'           => $images[ 'scholarship' ] ?? 0,
		'subtitle'        => 'Early Education',
		'hero_intro'      => 'A strong beginning opens a lifetime of possibility.',
				'end_eyebrow'      => 'Be part of the journey',
		'end_heading'      => 'Keep possibility within reach.',
		'end_text'         => 'Support this work with your time, resources or a partnership.',
		'end_button_title' => 'Let’s connect',
'layout'          => array(
			array( 'name' => 'values', 'key' => 'field_liftedpath_work_values', 'rows' => array(
				array( 'phrase' => 'Learning with purpose' ),
				array( 'phrase' => 'Guidance with care' ),
				array( 'phrase' => 'Opportunity with dignity' ),
			), 'subs' => array( 'phrase' => 'field_liftedpath_work_value_phrase' ) ),
			array( 'name' => 'sidebar_heading', 'key' => 'field_liftedpath_work_sidebar_heading', 'value' => 'LiftedPath Learning' ),
			array(
				'name' => 'nav_links',
				'key'  => 'field_liftedpath_work_nav',
				'rows' => array(
					array( 'title' => 'LiftedPath Learning', 'subtitle' => 'A pathway for every stage.', 'link' => liftedpath_seed_link( 'LiftedPath Learning', '/learning/' ) ),
						array( 'title' => 'LIFT-ROOT', 'subtitle' => 'Early Education', 'link' => liftedpath_seed_link( 'LIFT-ROOT', '/learning/lift-root/' ) ),
						array( 'title' => 'LIFT-GROW', 'subtitle' => 'Secondary Education', 'link' => liftedpath_seed_link( 'LIFT-GROW', '/learning/lift-grow/' ) ),
						array( 'title' => 'LIFT-BEYOND', 'subtitle' => 'Higher Education', 'link' => liftedpath_seed_link( 'LIFT-BEYOND', '/learning/lift-beyond/' ) ),
						array( 'title' => 'LIFT-EQUIP', 'subtitle' => 'Skills & Vocational Development', 'link' => liftedpath_seed_link( 'LIFT-EQUIP', '/learning/lift-equip/' ) ),
				),
				'subs' => array(
					'title'    => 'field_liftedpath_work_nav_title',
					'subtitle' => 'field_liftedpath_work_nav_subtitle',
					'link'     => 'field_liftedpath_work_nav_link',
				),
			),
				array( 'name' => 'show_back', 'key' => 'field_liftedpath_work_show_back', 'value' => 1 ),
				array( 'name' => 'back_label', 'key' => 'field_liftedpath_work_back_label', 'value' => 'All our work' ),
				array( 'name' => 'back_link', 'key' => 'field_liftedpath_work_back_link', 'value' => liftedpath_seed_link( 'All our work', '/what-we-do/' ) ),
			array( 'name' => 'contact_image', 'key' => 'field_liftedpath_work_contact_image', 'value' => $images['scholarship'] ?? ( $images[ 'scholarship' ] ?? 0 ) ),
			array( 'name' => 'contact_heading', 'key' => 'field_liftedpath_work_contact_heading', 'value' => 'A conversation can open a new path.' ),
			array( 'name' => 'contact_text', 'key' => 'field_liftedpath_work_contact_text', 'value' => 'Ask about support, share your skills or explore a partnership.' ),
			array( 'name' => 'contact_button', 'key' => 'field_liftedpath_work_contact_button', 'value' => liftedpath_seed_link( 'Get in touch', '/contact/' ) ),
			array( 'name' => 'contact_phone', 'key' => 'field_liftedpath_work_contact_phone', 'value' => '' ),
			array( 'name' => 'lead_image', 'key' => 'field_liftedpath_work_lead_image', 'value' => $images[ 'scholarship' ] ?? 0 ),
			array( 'name' => 'lead_caption', 'key' => 'field_liftedpath_work_lead_caption', 'value' => 'Early Education' ),
			array( 'name' => 'lead_eyebrow', 'key' => 'field_liftedpath_work_lead_eyebrow', 'value' => 'Early Education' ),
			array( 'name' => 'lead_heading', 'key' => 'field_liftedpath_work_lead_heading', 'value' => 'About LIFT-ROOT' ),
			array( 'name' => 'lead_intro', 'key' => 'field_liftedpath_work_lead_intro', 'value' => 'A strong beginning opens a lifetime of possibility. LIFT-ROOT helps young learners access the essentials that support their first steps in education.' ),
			array( 'name' => 'audience_kicker', 'key' => 'field_liftedpath_work_audience_kicker', 'value' => '01 / People at the heart' ),
			array( 'name' => 'audience_heading', 'key' => 'field_liftedpath_work_audience_heading', 'value' => 'Who this pathway helps' ),
			array( 'name' => 'audience_text', 'key' => 'field_liftedpath_work_audience_text', 'value' => 'Young children whose access to early education is affected by financial barriers.' ),
			array(
				'name' => 'support',
				'key'  => 'field_liftedpath_work_support',
				'rows' => array(
					array( 'title' => 'A supported start', 'text' => 'Educational assistance and school essentials that help children begin learning.' ),
						array( 'title' => 'School connections', 'text' => 'Work with families and schools to understand what a young learner needs.' ),
				),
				'subs' => array(
					'title' => 'field_liftedpath_work_support_title',
					'text'  => 'field_liftedpath_work_support_text',
				),
			),
				array( 'name' => 'areas_kicker', 'key' => 'field_liftedpath_work_areas_kicker', 'value' => '' ),
				array( 'name' => 'areas_heading', 'key' => 'field_liftedpath_work_areas_heading', 'value' => '' ),
				array(
					'name' => 'areas',
					'key'  => 'field_liftedpath_work_areas',
					'rows' => array(),
					'subs' => array(
						'title'    => 'field_liftedpath_work_area_title',
						'subtitle' => 'field_liftedpath_work_area_subtitle',
						'link'     => 'field_liftedpath_work_area_link',
						'text'     => 'field_liftedpath_work_area_text',
					),
				),
			array( 'name' => 'why_kicker', 'key' => 'field_liftedpath_work_why_kicker', 'value' => '02 / Looking ahead' ),
			array( 'name' => 'why_heading', 'key' => 'field_liftedpath_work_why_heading', 'value' => 'Why it matters' ),
			array( 'name' => 'why_text', 'key' => 'field_liftedpath_work_why_text', 'value' => 'Early learning builds foundations for confidence, curiosity and future education. Timely support helps keep those first steps within reach.' ),
			array(
				'name' => 'principles',
				'key'  => 'field_liftedpath_work_principles',
				'rows' => array(
					array( 'text' => 'Dignity and individual needs come first.' ),
					array( 'text' => 'Support is shaped by real needs and available resources.' ),
				),
				'subs' => array( 'text' => 'field_liftedpath_work_principle_text' ),
			),
			array( 'name' => 'faq_kicker', 'key' => 'field_liftedpath_work_faq_kicker', 'value' => 'A little more clarity' ),
			array( 'name' => 'faq_heading', 'key' => 'field_liftedpath_work_faq_heading', 'value' => 'Questions about this work' ),
			array(
				'name' => 'faq_items',
				'key'  => 'field_liftedpath_work_faq_items',
				'rows' => array(
					array( 'question' => 'Who is LIFT-ROOT for?', 'answer' => 'Young children whose access to early education is affected by financial barriers.' ),
					array( 'question' => 'How can I discuss support?', 'answer' => 'Contact our team with the learner’s or community’s needs. We can discuss the relevant pathway and what support may be possible; contacting us does not guarantee funding or a place.' ),
					array( 'question' => 'How can I get involved?', 'answer' => 'You can donate, offer relevant skills or discuss a partnership to support LIFT-ROOT. Get in touch so we can coordinate help around actual needs.' ),
				),
				'subs' => array(
					'question' => 'field_liftedpath_work_faq_question',
					'answer'   => 'field_liftedpath_work_faq_answer',
				),
			),
		),
	);

	$catalog[ 'learning/lift-grow' ] = array(
		'slug'            => 'lift-grow',
		'title'           => 'LIFT-GROW',
		'parent'          => 'learning',
		'seo_title'       => 'LIFT-GROW',
		'seo_description' => 'LIFT-GROW supports determined students through their secondary years, helping them stay in school and keep moving toward their goals.',
		'hero_summary'    => 'Support through the secondary years so students stay in school and keep moving.',
		'image'           => $images[ 'students' ] ?? 0,
		'subtitle'        => 'Secondary Education',
		'hero_intro'      => 'Support through the secondary years so students stay in school and keep moving.',
				'end_eyebrow'      => 'Be part of the journey',
		'end_heading'      => 'Keep possibility within reach.',
		'end_text'         => 'Support this work with your time, resources or a partnership.',
		'end_button_title' => 'Let’s connect',
'layout'          => array(
			array( 'name' => 'values', 'key' => 'field_liftedpath_work_values', 'rows' => array(
				array( 'phrase' => 'Learning with purpose' ),
				array( 'phrase' => 'Guidance with care' ),
				array( 'phrase' => 'Opportunity with dignity' ),
			), 'subs' => array( 'phrase' => 'field_liftedpath_work_value_phrase' ) ),
			array( 'name' => 'sidebar_heading', 'key' => 'field_liftedpath_work_sidebar_heading', 'value' => 'LiftedPath Learning' ),
			array(
				'name' => 'nav_links',
				'key'  => 'field_liftedpath_work_nav',
				'rows' => array(
					array( 'title' => 'LiftedPath Learning', 'subtitle' => 'A pathway for every stage.', 'link' => liftedpath_seed_link( 'LiftedPath Learning', '/learning/' ) ),
						array( 'title' => 'LIFT-ROOT', 'subtitle' => 'Early Education', 'link' => liftedpath_seed_link( 'LIFT-ROOT', '/learning/lift-root/' ) ),
						array( 'title' => 'LIFT-GROW', 'subtitle' => 'Secondary Education', 'link' => liftedpath_seed_link( 'LIFT-GROW', '/learning/lift-grow/' ) ),
						array( 'title' => 'LIFT-BEYOND', 'subtitle' => 'Higher Education', 'link' => liftedpath_seed_link( 'LIFT-BEYOND', '/learning/lift-beyond/' ) ),
						array( 'title' => 'LIFT-EQUIP', 'subtitle' => 'Skills & Vocational Development', 'link' => liftedpath_seed_link( 'LIFT-EQUIP', '/learning/lift-equip/' ) ),
				),
				'subs' => array(
					'title'    => 'field_liftedpath_work_nav_title',
					'subtitle' => 'field_liftedpath_work_nav_subtitle',
					'link'     => 'field_liftedpath_work_nav_link',
				),
			),
				array( 'name' => 'show_back', 'key' => 'field_liftedpath_work_show_back', 'value' => 1 ),
				array( 'name' => 'back_label', 'key' => 'field_liftedpath_work_back_label', 'value' => 'All our work' ),
				array( 'name' => 'back_link', 'key' => 'field_liftedpath_work_back_link', 'value' => liftedpath_seed_link( 'All our work', '/what-we-do/' ) ),
			array( 'name' => 'contact_image', 'key' => 'field_liftedpath_work_contact_image', 'value' => $images['scholarship'] ?? ( $images[ 'students' ] ?? 0 ) ),
			array( 'name' => 'contact_heading', 'key' => 'field_liftedpath_work_contact_heading', 'value' => 'A conversation can open a new path.' ),
			array( 'name' => 'contact_text', 'key' => 'field_liftedpath_work_contact_text', 'value' => 'Ask about support, share your skills or explore a partnership.' ),
			array( 'name' => 'contact_button', 'key' => 'field_liftedpath_work_contact_button', 'value' => liftedpath_seed_link( 'Get in touch', '/contact/' ) ),
			array( 'name' => 'contact_phone', 'key' => 'field_liftedpath_work_contact_phone', 'value' => '' ),
			array( 'name' => 'lead_image', 'key' => 'field_liftedpath_work_lead_image', 'value' => $images[ 'students' ] ?? 0 ),
			array( 'name' => 'lead_caption', 'key' => 'field_liftedpath_work_lead_caption', 'value' => 'Secondary Education' ),
			array( 'name' => 'lead_eyebrow', 'key' => 'field_liftedpath_work_lead_eyebrow', 'value' => 'Secondary Education' ),
			array( 'name' => 'lead_heading', 'key' => 'field_liftedpath_work_lead_heading', 'value' => 'About LIFT-GROW' ),
			array( 'name' => 'lead_intro', 'key' => 'field_liftedpath_work_lead_intro', 'value' => 'LIFT-GROW supports determined students through their secondary years, helping them stay in school and keep moving toward their goals.' ),
			array( 'name' => 'audience_kicker', 'key' => 'field_liftedpath_work_audience_kicker', 'value' => '01 / People at the heart' ),
			array( 'name' => 'audience_heading', 'key' => 'field_liftedpath_work_audience_heading', 'value' => 'Who this pathway helps' ),
			array( 'name' => 'audience_text', 'key' => 'field_liftedpath_work_audience_text', 'value' => 'Secondary school students whose continued education is at risk because of financial hardship.' ),
			array(
				'name' => 'support',
				'key'  => 'field_liftedpath_work_support',
				'rows' => array(
					array( 'title' => 'Stay in school', 'text' => 'Educational assistance that helps reduce financial barriers to continued study.' ),
						array( 'title' => 'Stay on track', 'text' => 'Encouragement with study habits, confidence and future goals.' ),
				),
				'subs' => array(
					'title' => 'field_liftedpath_work_support_title',
					'text'  => 'field_liftedpath_work_support_text',
				),
			),
				array( 'name' => 'areas_kicker', 'key' => 'field_liftedpath_work_areas_kicker', 'value' => '' ),
				array( 'name' => 'areas_heading', 'key' => 'field_liftedpath_work_areas_heading', 'value' => '' ),
				array(
					'name' => 'areas',
					'key'  => 'field_liftedpath_work_areas',
					'rows' => array(),
					'subs' => array(
						'title'    => 'field_liftedpath_work_area_title',
						'subtitle' => 'field_liftedpath_work_area_subtitle',
						'link'     => 'field_liftedpath_work_area_link',
						'text'     => 'field_liftedpath_work_area_text',
					),
				),
			array( 'name' => 'why_kicker', 'key' => 'field_liftedpath_work_why_kicker', 'value' => '02 / Looking ahead' ),
			array( 'name' => 'why_heading', 'key' => 'field_liftedpath_work_why_heading', 'value' => 'Why it matters' ),
			array( 'name' => 'why_text', 'key' => 'field_liftedpath_work_why_text', 'value' => 'The secondary years shape future choices. Keeping education within reach helps students develop the knowledge and confidence for what comes next.' ),
			array(
				'name' => 'principles',
				'key'  => 'field_liftedpath_work_principles',
				'rows' => array(
					array( 'text' => 'Dignity and individual needs come first.' ),
					array( 'text' => 'Support is shaped by real needs and available resources.' ),
				),
				'subs' => array( 'text' => 'field_liftedpath_work_principle_text' ),
			),
			array( 'name' => 'faq_kicker', 'key' => 'field_liftedpath_work_faq_kicker', 'value' => 'A little more clarity' ),
			array( 'name' => 'faq_heading', 'key' => 'field_liftedpath_work_faq_heading', 'value' => 'Questions about this work' ),
			array(
				'name' => 'faq_items',
				'key'  => 'field_liftedpath_work_faq_items',
				'rows' => array(
					array( 'question' => 'Who is LIFT-GROW for?', 'answer' => 'Secondary school students whose continued education is at risk because of financial hardship.' ),
					array( 'question' => 'How can I discuss support?', 'answer' => 'Contact our team with the learner’s or community’s needs. We can discuss the relevant pathway and what support may be possible; contacting us does not guarantee funding or a place.' ),
					array( 'question' => 'How can I get involved?', 'answer' => 'You can donate, offer relevant skills or discuss a partnership to support LIFT-GROW. Get in touch so we can coordinate help around actual needs.' ),
				),
				'subs' => array(
					'question' => 'field_liftedpath_work_faq_question',
					'answer'   => 'field_liftedpath_work_faq_answer',
				),
			),
		),
	);

	$catalog[ 'learning/lift-beyond' ] = array(
		'slug'            => 'lift-beyond',
		'title'           => 'LIFT-BEYOND',
		'parent'          => 'learning',
		'seo_title'       => 'LIFT-BEYOND',
		'seo_description' => 'LIFT-BEYOND helps young adults overcome financial barriers to further education and pursue the futures they imagine for themselves.',
		'hero_summary'    => 'Help young adults overcome financial barriers to further education.',
		'image'           => $images[ 'classroom' ] ?? 0,
		'subtitle'        => 'Higher Education',
		'hero_intro'      => 'Help young adults overcome financial barriers to further education.',
				'end_eyebrow'      => 'Be part of the journey',
		'end_heading'      => 'Keep possibility within reach.',
		'end_text'         => 'Support this work with your time, resources or a partnership.',
		'end_button_title' => 'Let’s connect',
'layout'          => array(
			array( 'name' => 'values', 'key' => 'field_liftedpath_work_values', 'rows' => array(
				array( 'phrase' => 'Learning with purpose' ),
				array( 'phrase' => 'Guidance with care' ),
				array( 'phrase' => 'Opportunity with dignity' ),
			), 'subs' => array( 'phrase' => 'field_liftedpath_work_value_phrase' ) ),
			array( 'name' => 'sidebar_heading', 'key' => 'field_liftedpath_work_sidebar_heading', 'value' => 'LiftedPath Learning' ),
			array(
				'name' => 'nav_links',
				'key'  => 'field_liftedpath_work_nav',
				'rows' => array(
					array( 'title' => 'LiftedPath Learning', 'subtitle' => 'A pathway for every stage.', 'link' => liftedpath_seed_link( 'LiftedPath Learning', '/learning/' ) ),
						array( 'title' => 'LIFT-ROOT', 'subtitle' => 'Early Education', 'link' => liftedpath_seed_link( 'LIFT-ROOT', '/learning/lift-root/' ) ),
						array( 'title' => 'LIFT-GROW', 'subtitle' => 'Secondary Education', 'link' => liftedpath_seed_link( 'LIFT-GROW', '/learning/lift-grow/' ) ),
						array( 'title' => 'LIFT-BEYOND', 'subtitle' => 'Higher Education', 'link' => liftedpath_seed_link( 'LIFT-BEYOND', '/learning/lift-beyond/' ) ),
						array( 'title' => 'LIFT-EQUIP', 'subtitle' => 'Skills & Vocational Development', 'link' => liftedpath_seed_link( 'LIFT-EQUIP', '/learning/lift-equip/' ) ),
				),
				'subs' => array(
					'title'    => 'field_liftedpath_work_nav_title',
					'subtitle' => 'field_liftedpath_work_nav_subtitle',
					'link'     => 'field_liftedpath_work_nav_link',
				),
			),
				array( 'name' => 'show_back', 'key' => 'field_liftedpath_work_show_back', 'value' => 1 ),
				array( 'name' => 'back_label', 'key' => 'field_liftedpath_work_back_label', 'value' => 'All our work' ),
				array( 'name' => 'back_link', 'key' => 'field_liftedpath_work_back_link', 'value' => liftedpath_seed_link( 'All our work', '/what-we-do/' ) ),
			array( 'name' => 'contact_image', 'key' => 'field_liftedpath_work_contact_image', 'value' => $images['scholarship'] ?? ( $images[ 'classroom' ] ?? 0 ) ),
			array( 'name' => 'contact_heading', 'key' => 'field_liftedpath_work_contact_heading', 'value' => 'A conversation can open a new path.' ),
			array( 'name' => 'contact_text', 'key' => 'field_liftedpath_work_contact_text', 'value' => 'Ask about support, share your skills or explore a partnership.' ),
			array( 'name' => 'contact_button', 'key' => 'field_liftedpath_work_contact_button', 'value' => liftedpath_seed_link( 'Get in touch', '/contact/' ) ),
			array( 'name' => 'contact_phone', 'key' => 'field_liftedpath_work_contact_phone', 'value' => '' ),
			array( 'name' => 'lead_image', 'key' => 'field_liftedpath_work_lead_image', 'value' => $images[ 'classroom' ] ?? 0 ),
			array( 'name' => 'lead_caption', 'key' => 'field_liftedpath_work_lead_caption', 'value' => 'Higher Education' ),
			array( 'name' => 'lead_eyebrow', 'key' => 'field_liftedpath_work_lead_eyebrow', 'value' => 'Higher Education' ),
			array( 'name' => 'lead_heading', 'key' => 'field_liftedpath_work_lead_heading', 'value' => 'About LIFT-BEYOND' ),
			array( 'name' => 'lead_intro', 'key' => 'field_liftedpath_work_lead_intro', 'value' => 'LIFT-BEYOND helps young adults overcome financial barriers to further education and pursue the futures they imagine for themselves.' ),
			array( 'name' => 'audience_kicker', 'key' => 'field_liftedpath_work_audience_kicker', 'value' => '01 / People at the heart' ),
			array( 'name' => 'audience_heading', 'key' => 'field_liftedpath_work_audience_heading', 'value' => 'Who this pathway helps' ),
			array( 'name' => 'audience_text', 'key' => 'field_liftedpath_work_audience_text', 'value' => 'Young adults seeking to continue into higher education but facing limited financial resources.' ),
			array(
				'name' => 'support',
				'key'  => 'field_liftedpath_work_support',
				'rows' => array(
					array( 'title' => 'Continue learning', 'text' => 'Educational assistance toward further study, based on identified needs and available support.' ),
						array( 'title' => 'Prepare for the future', 'text' => 'Guidance as young adults consider their education and career choices.' ),
				),
				'subs' => array(
					'title' => 'field_liftedpath_work_support_title',
					'text'  => 'field_liftedpath_work_support_text',
				),
			),
				array( 'name' => 'areas_kicker', 'key' => 'field_liftedpath_work_areas_kicker', 'value' => '' ),
				array( 'name' => 'areas_heading', 'key' => 'field_liftedpath_work_areas_heading', 'value' => '' ),
				array(
					'name' => 'areas',
					'key'  => 'field_liftedpath_work_areas',
					'rows' => array(),
					'subs' => array(
						'title'    => 'field_liftedpath_work_area_title',
						'subtitle' => 'field_liftedpath_work_area_subtitle',
						'link'     => 'field_liftedpath_work_area_link',
						'text'     => 'field_liftedpath_work_area_text',
					),
				),
			array( 'name' => 'why_kicker', 'key' => 'field_liftedpath_work_why_kicker', 'value' => '02 / Looking ahead' ),
			array( 'name' => 'why_heading', 'key' => 'field_liftedpath_work_why_heading', 'value' => 'Why it matters' ),
			array( 'name' => 'why_text', 'key' => 'field_liftedpath_work_why_text', 'value' => 'Further education can open doors to knowledge, work and greater independence. Financial hardship should not be the end of a determined learner’s journey.' ),
			array(
				'name' => 'principles',
				'key'  => 'field_liftedpath_work_principles',
				'rows' => array(
					array( 'text' => 'Dignity and individual needs come first.' ),
					array( 'text' => 'Support is shaped by real needs and available resources.' ),
				),
				'subs' => array( 'text' => 'field_liftedpath_work_principle_text' ),
			),
			array( 'name' => 'faq_kicker', 'key' => 'field_liftedpath_work_faq_kicker', 'value' => 'A little more clarity' ),
			array( 'name' => 'faq_heading', 'key' => 'field_liftedpath_work_faq_heading', 'value' => 'Questions about this work' ),
			array(
				'name' => 'faq_items',
				'key'  => 'field_liftedpath_work_faq_items',
				'rows' => array(
					array( 'question' => 'Who is LIFT-BEYOND for?', 'answer' => 'Young adults seeking to continue into higher education but facing limited financial resources.' ),
					array( 'question' => 'How can I discuss support?', 'answer' => 'Contact our team with the learner’s or community’s needs. We can discuss the relevant pathway and what support may be possible; contacting us does not guarantee funding or a place.' ),
					array( 'question' => 'How can I get involved?', 'answer' => 'You can donate, offer relevant skills or discuss a partnership to support LIFT-BEYOND. Get in touch so we can coordinate help around actual needs.' ),
				),
				'subs' => array(
					'question' => 'field_liftedpath_work_faq_question',
					'answer'   => 'field_liftedpath_work_faq_answer',
				),
			),
		),
	);

	$catalog[ 'learning/lift-equip' ] = array(
		'slug'            => 'lift-equip',
		'title'           => 'LIFT-EQUIP',
		'parent'          => 'learning',
		'seo_title'       => 'LIFT-EQUIP',
		'seo_description' => 'Practical training and tools build confidence, independence and pathways into meaningful work. LIFT-EQUIP supports young people in developing skills they can put to use.',
		'hero_summary'    => 'Practical training and tools that build confidence and pathways into work.',
		'image'           => $images[ 'skills' ] ?? 0,
		'subtitle'        => 'Skills & Vocational Development',
		'hero_intro'      => 'Practical training and tools that build confidence and pathways into work.',
				'end_eyebrow'      => 'Be part of the journey',
		'end_heading'      => 'Keep possibility within reach.',
		'end_text'         => 'Support this work with your time, resources or a partnership.',
		'end_button_title' => 'Let’s connect',
'layout'          => array(
			array( 'name' => 'values', 'key' => 'field_liftedpath_work_values', 'rows' => array(
				array( 'phrase' => 'Learning with purpose' ),
				array( 'phrase' => 'Guidance with care' ),
				array( 'phrase' => 'Opportunity with dignity' ),
			), 'subs' => array( 'phrase' => 'field_liftedpath_work_value_phrase' ) ),
			array( 'name' => 'sidebar_heading', 'key' => 'field_liftedpath_work_sidebar_heading', 'value' => 'LiftedPath Learning' ),
			array(
				'name' => 'nav_links',
				'key'  => 'field_liftedpath_work_nav',
				'rows' => array(
					array( 'title' => 'LiftedPath Learning', 'subtitle' => 'A pathway for every stage.', 'link' => liftedpath_seed_link( 'LiftedPath Learning', '/learning/' ) ),
						array( 'title' => 'LIFT-ROOT', 'subtitle' => 'Early Education', 'link' => liftedpath_seed_link( 'LIFT-ROOT', '/learning/lift-root/' ) ),
						array( 'title' => 'LIFT-GROW', 'subtitle' => 'Secondary Education', 'link' => liftedpath_seed_link( 'LIFT-GROW', '/learning/lift-grow/' ) ),
						array( 'title' => 'LIFT-BEYOND', 'subtitle' => 'Higher Education', 'link' => liftedpath_seed_link( 'LIFT-BEYOND', '/learning/lift-beyond/' ) ),
						array( 'title' => 'LIFT-EQUIP', 'subtitle' => 'Skills & Vocational Development', 'link' => liftedpath_seed_link( 'LIFT-EQUIP', '/learning/lift-equip/' ) ),
				),
				'subs' => array(
					'title'    => 'field_liftedpath_work_nav_title',
					'subtitle' => 'field_liftedpath_work_nav_subtitle',
					'link'     => 'field_liftedpath_work_nav_link',
				),
			),
				array( 'name' => 'show_back', 'key' => 'field_liftedpath_work_show_back', 'value' => 1 ),
				array( 'name' => 'back_label', 'key' => 'field_liftedpath_work_back_label', 'value' => 'All our work' ),
				array( 'name' => 'back_link', 'key' => 'field_liftedpath_work_back_link', 'value' => liftedpath_seed_link( 'All our work', '/what-we-do/' ) ),
			array( 'name' => 'contact_image', 'key' => 'field_liftedpath_work_contact_image', 'value' => $images['scholarship'] ?? ( $images[ 'skills' ] ?? 0 ) ),
			array( 'name' => 'contact_heading', 'key' => 'field_liftedpath_work_contact_heading', 'value' => 'A conversation can open a new path.' ),
			array( 'name' => 'contact_text', 'key' => 'field_liftedpath_work_contact_text', 'value' => 'Ask about support, share your skills or explore a partnership.' ),
			array( 'name' => 'contact_button', 'key' => 'field_liftedpath_work_contact_button', 'value' => liftedpath_seed_link( 'Get in touch', '/contact/' ) ),
			array( 'name' => 'contact_phone', 'key' => 'field_liftedpath_work_contact_phone', 'value' => '' ),
			array( 'name' => 'lead_image', 'key' => 'field_liftedpath_work_lead_image', 'value' => $images[ 'skills' ] ?? 0 ),
			array( 'name' => 'lead_caption', 'key' => 'field_liftedpath_work_lead_caption', 'value' => 'Skills & Vocational Development' ),
			array( 'name' => 'lead_eyebrow', 'key' => 'field_liftedpath_work_lead_eyebrow', 'value' => 'Skills & Vocational Development' ),
			array( 'name' => 'lead_heading', 'key' => 'field_liftedpath_work_lead_heading', 'value' => 'About LIFT-EQUIP' ),
			array( 'name' => 'lead_intro', 'key' => 'field_liftedpath_work_lead_intro', 'value' => 'Practical training and tools build confidence, independence and pathways into meaningful work. LIFT-EQUIP supports young people in developing skills they can put to use.' ),
			array( 'name' => 'audience_kicker', 'key' => 'field_liftedpath_work_audience_kicker', 'value' => '01 / People at the heart' ),
			array( 'name' => 'audience_heading', 'key' => 'field_liftedpath_work_audience_heading', 'value' => 'Who this pathway helps' ),
			array( 'name' => 'audience_text', 'key' => 'field_liftedpath_work_audience_text', 'value' => 'Young people who want practical or vocational training but lack the resources to pursue it.' ),
			array(
				'name' => 'support',
				'key'  => 'field_liftedpath_work_support',
				'rows' => array(
					array( 'title' => 'Practical training', 'text' => 'Support for vocational placements and learning with training providers.' ),
						array( 'title' => 'Tools and follow-through', 'text' => 'Tools, materials and coaching that help turn training into practical next steps.' ),
				),
				'subs' => array(
					'title' => 'field_liftedpath_work_support_title',
					'text'  => 'field_liftedpath_work_support_text',
				),
			),
				array( 'name' => 'areas_kicker', 'key' => 'field_liftedpath_work_areas_kicker', 'value' => '' ),
				array( 'name' => 'areas_heading', 'key' => 'field_liftedpath_work_areas_heading', 'value' => '' ),
				array(
					'name' => 'areas',
					'key'  => 'field_liftedpath_work_areas',
					'rows' => array(),
					'subs' => array(
						'title'    => 'field_liftedpath_work_area_title',
						'subtitle' => 'field_liftedpath_work_area_subtitle',
						'link'     => 'field_liftedpath_work_area_link',
						'text'     => 'field_liftedpath_work_area_text',
					),
				),
			array( 'name' => 'why_kicker', 'key' => 'field_liftedpath_work_why_kicker', 'value' => '02 / Looking ahead' ),
			array( 'name' => 'why_heading', 'key' => 'field_liftedpath_work_why_heading', 'value' => 'Why it matters' ),
			array( 'name' => 'why_text', 'key' => 'field_liftedpath_work_why_text', 'value' => 'Useful skills can create possibilities for earning and self-reliance. Training is strongest when it is paired with tools and ongoing encouragement.' ),
			array(
				'name' => 'principles',
				'key'  => 'field_liftedpath_work_principles',
				'rows' => array(
					array( 'text' => 'Dignity and individual needs come first.' ),
					array( 'text' => 'Support is shaped by real needs and available resources.' ),
				),
				'subs' => array( 'text' => 'field_liftedpath_work_principle_text' ),
			),
			array( 'name' => 'faq_kicker', 'key' => 'field_liftedpath_work_faq_kicker', 'value' => 'A little more clarity' ),
			array( 'name' => 'faq_heading', 'key' => 'field_liftedpath_work_faq_heading', 'value' => 'Questions about this work' ),
			array(
				'name' => 'faq_items',
				'key'  => 'field_liftedpath_work_faq_items',
				'rows' => array(
					array( 'question' => 'Who is LIFT-EQUIP for?', 'answer' => 'Young people who want practical or vocational training but lack the resources to pursue it.' ),
					array( 'question' => 'How can I discuss support?', 'answer' => 'Contact our team with the learner’s or community’s needs. We can discuss the relevant pathway and what support may be possible; contacting us does not guarantee funding or a place.' ),
					array( 'question' => 'How can I get involved?', 'answer' => 'You can donate, offer relevant skills or discuss a partnership to support LIFT-EQUIP. Get in touch so we can coordinate help around actual needs.' ),
				),
				'subs' => array(
					'question' => 'field_liftedpath_work_faq_question',
					'answer'   => 'field_liftedpath_work_faq_answer',
				),
			),
		),
	);

	return $catalog;
}

/**
 * Page content: work hero + work-layout block.
 *
 * @param array<string, mixed> $page   Catalog entry.
 * @param array<string, int>   $images Attachment IDs.
 */
function liftedpath_work_page_content( array $page, array $images ): string {
	$content  = liftedpath_seed_block(
		'page-hero',
		liftedpath_seed_data(
			array(
				array( 'name' => 'variant', 'key' => 'field_liftedpath_page_hero_variant', 'value' => 'work' ),
				array( 'name' => 'image', 'key' => 'field_liftedpath_page_hero_image', 'value' => (int) ( $page['image'] ?? 0 ) ),
				array( 'name' => 'eyebrow', 'key' => 'field_liftedpath_page_hero_eyebrow', 'value' => (string) ( $page['subtitle'] ?? '' ) ),
				array( 'name' => 'breadcrumb_current', 'key' => 'field_liftedpath_page_hero_breadcrumb_current', 'value' => (string) ( $page['title'] ?? '' ) ),
				array( 'name' => 'heading', 'key' => 'field_liftedpath_page_hero_heading', 'value' => (string) ( $page['title'] ?? '' ) ),
				array( 'name' => 'intro', 'key' => 'field_liftedpath_page_hero_intro', 'value' => (string) ( $page['hero_intro'] ?? '' ) ),
				array( 'name' => 'breadcrumb_label', 'key' => 'field_liftedpath_page_hero_breadcrumb', 'value' => 'Home' ),
			)
		)
	);
	$content .= liftedpath_seed_block(
		'work-layout',
		liftedpath_seed_data( is_array( $page['layout'] ?? null ) ? $page['layout'] : array() )
	);
	$content .= liftedpath_seed_footer_cta_2(
		(int) ( $images['students'] ?? ( $page['image'] ?? 0 ) ),
		(string) ( $page['end_eyebrow'] ?? 'Be part of the journey' ),
		(string) ( $page['end_heading'] ?? 'Small acts. Meaningful change.' ),
		(string) ( $page['end_text'] ?? 'Support this work with your time, resources or a partnership.' ),
		(string) ( $page['end_button_title'] ?? 'Let’s connect' )
	);

	return $content;
}

/**
 * Seed / migrate definition for one work path.
 *
 * @param array<string, int> $images Attachment IDs.
 */
function liftedpath_seed_work_layout_page( string $path, array $images ): array {
	$catalog = liftedpath_work_page_catalog( $images );
	$page    = $catalog[ $path ] ?? null;

	if ( ! is_array( $page ) ) {
		return array();
	}

	$content = liftedpath_work_page_content( $page, $images );

	return liftedpath_seed_page_record(
		(string) $page['slug'],
		(string) $page['title'],
		(string) $page['parent'],
		$content,
		(string) $page['seo_title'],
		(string) $page['seo_description'],
		(string) $page['hero_summary']
	);
}

/**
 * Rewrite What We Do / Learning trees to the work layout once.
 */
function liftedpath_migrate_work_layout(): void {
	if ( get_option( 'liftedpath_work_layout_migrated_v1' ) || ! function_exists( 'update_field' ) ) {
		return;
	}

	if ( ! did_action( 'acf/init' ) && ! function_exists( 'acf_get_store' ) ) {
		return;
	}

	liftedpath_migrate_work_layout_run();
}
add_action( 'init', 'liftedpath_migrate_work_layout', 35 );

/**
 * Force-update published work pages with Design 01 content.
 */
function liftedpath_migrate_work_layout_run(): void {
	$images  = function_exists( 'liftedpath_seed_images' ) ? liftedpath_seed_images() : array();
	$catalog = liftedpath_work_page_catalog( $images );

	foreach ( $catalog as $path => $page ) {
		$existing = get_page_by_path( $path, OBJECT, 'page' );
		if ( ! $existing instanceof WP_Post ) {
			continue;
		}

		$content = liftedpath_work_page_content( $page, $images );
		global $wpdb;
		$wpdb->update(
			$wpdb->posts,
			array( 'post_content' => $content ),
			array( 'ID' => (int) $existing->ID ),
			array( '%s' ),
			array( '%d' )
		);
		clean_post_cache( (int) $existing->ID );
		update_field( 'seo_title', (string) $page['seo_title'], (int) $existing->ID );
		update_field( 'seo_description', (string) $page['seo_description'], (int) $existing->ID );
		update_field( 'hero_summary', (string) $page['hero_summary'], (int) $existing->ID );
	}

	update_option( 'liftedpath_work_layout_migrated_v1', 1, false );
}

/**
 * Footer CTA 2 seed block for work pages.
 */
function liftedpath_seed_footer_cta_2( int $image, string $eyebrow, string $heading, string $text, string $button_title = 'Let’s connect' ): string {
	return liftedpath_seed_block(
		'footer-cta-2',
		liftedpath_seed_data(
			array(
				array( 'name' => 'image', 'key' => 'field_liftedpath_footer_cta_2_image', 'value' => $image ),
				array( 'name' => 'overlay_opacity', 'key' => 'field_liftedpath_footer_cta_2_opacity', 'value' => 72 ),
				array( 'name' => 'eyebrow', 'key' => 'field_liftedpath_footer_cta_2_eyebrow', 'value' => $eyebrow ),
				array( 'name' => 'heading', 'key' => 'field_liftedpath_footer_cta_2_heading', 'value' => $heading ),
				array( 'name' => 'text', 'key' => 'field_liftedpath_footer_cta_2_text', 'value' => $text ),
				array( 'name' => 'button', 'key' => 'field_liftedpath_footer_cta_2_button', 'value' => liftedpath_seed_link( $button_title, '/contact/' ) ),
			)
		)
	);
}

/**
 * Move work-layout end band into Footer CTA 2 on published pages (once).
 */
function liftedpath_migrate_footer_cta_2(): void {
	if ( get_option( 'liftedpath_footer_cta_2_migrated_v1' ) || ! function_exists( 'update_field' ) ) {
		return;
	}

	liftedpath_migrate_footer_cta_2_run();
}
add_action( 'init', 'liftedpath_migrate_footer_cta_2', 36 );

/**
 * Split end-band fields from work-layout into footer-cta-2; refresh work pages from catalog.
 */
function liftedpath_migrate_footer_cta_2_run(): void {
	$images  = function_exists( 'liftedpath_seed_images' ) ? liftedpath_seed_images() : array();
	$catalog = liftedpath_work_page_catalog( $images );

	foreach ( $catalog as $path => $page ) {
		$existing = get_page_by_path( $path, OBJECT, 'page' );
		if ( ! $existing instanceof WP_Post ) {
			continue;
		}

		$content = liftedpath_work_page_content( $page, $images );
		global $wpdb;
		$wpdb->update(
			$wpdb->posts,
			array( 'post_content' => $content ),
			array( 'ID' => (int) $existing->ID ),
			array( '%s' ),
			array( '%d' )
		);
		clean_post_cache( (int) $existing->ID );
	}

	update_option( 'liftedpath_footer_cta_2_migrated_v1', 1, false );
}

/**
 * Whether a page's work-layout block is missing its ACF field payload.
 */
function liftedpath_work_layout_needs_repair( string $content ): bool {
	$found = false;

	foreach ( parse_blocks( $content ) as $block ) {
		if ( ( $block['blockName'] ?? '' ) !== 'liftedpath/work-layout' ) {
			continue;
		}

		$found = true;
		$data  = ( isset( $block['attrs']['data'] ) && is_array( $block['attrs']['data'] ) ) ? $block['attrs']['data'] : array();
		$sidebar = isset( $data['sidebar_heading'] ) ? trim( (string) $data['sidebar_heading'] ) : '';
		$lead    = isset( $data['lead_heading'] ) ? trim( (string) $data['lead_heading'] ) : '';
		$values  = isset( $data['values'] ) ? (int) $data['values'] : 0;

		return ( '' === $sidebar && '' === $lead && $values <= 0 );
	}

	return ! $found;
}

/**
 * Rewrite catalog pages whose work-layout block has empty ACF data (once).
 */
function liftedpath_repair_empty_work_layouts(): void {
	if ( get_option( 'liftedpath_work_layout_repaired_v1' ) || ! function_exists( 'update_field' ) ) {
		return;
	}

	liftedpath_repair_empty_work_layouts_run();
}
add_action( 'init', 'liftedpath_repair_empty_work_layouts', 37 );

/**
 * Restore Design 01 work-layout (+ Footer CTA 2) only on broken catalog pages.
 */
function liftedpath_repair_empty_work_layouts_run(): void {
	$images  = function_exists( 'liftedpath_seed_images' ) ? liftedpath_seed_images() : array();
	$catalog = liftedpath_work_page_catalog( $images );

	foreach ( $catalog as $path => $page ) {
		$existing = get_page_by_path( $path, OBJECT, 'page' );
		if ( ! $existing instanceof WP_Post ) {
			continue;
		}

		if ( ! liftedpath_work_layout_needs_repair( (string) $existing->post_content ) ) {
			continue;
		}

		$content = liftedpath_work_page_content( $page, $images );
		global $wpdb;
		$wpdb->update(
			$wpdb->posts,
			array( 'post_content' => $content ),
			array( 'ID' => (int) $existing->ID ),
			array( '%s' ),
			array( '%d' )
		);
		clean_post_cache( (int) $existing->ID );
		update_field( 'seo_title', (string) $page['seo_title'], (int) $existing->ID );
		update_field( 'seo_description', (string) $page['seo_description'], (int) $existing->ID );
		update_field( 'hero_summary', (string) $page['hero_summary'], (int) $existing->ID );
	}

	update_option( 'liftedpath_work_layout_repaired_v1', 1, false );
}

