<?php
/**
 * ACF integration.
 *
 * Field groups and the options page live in acf-json/. This file warns when
 * ACF PRO is missing and seeds the organization facts once.
 *
 * @package LiftedPath
 */

/**
 * Warn administrators when the content fields cannot load.
 */
function liftedpath_acf_missing_notice() {
	if ( function_exists( 'acf_add_options_page' ) || ! current_user_can( 'activate_plugins' ) ) {
		return;
	}

	echo '<div class="notice notice-warning"><p>';
	echo esc_html__( 'LiftedPath settings, page SEO fields, and submission fields need Advanced Custom Fields PRO. The theme stays active without it.', 'liftedpath' );
	echo '</p></div>';
}
add_action( 'admin_notices', 'liftedpath_acf_missing_notice' );

/**
 * Organization facts used until an administrator edits them.
 *
 * @return array<string, mixed>
 */
function liftedpath_settings_seed() {
	return array(
		'legal_name'           => 'LiftedPath Empowerment Foundation',
		'short_name'           => 'LiftedPath',
		'tagline'              => 'Lifting Potential, Inspiring Purpose and Creating Futures.',
		'registration_number'  => 'RC 9872033',
		'phone'                => '+234 806 812 8785',
		'email'                => 'liftedpathfoundation@gmail.com',
		'postal_address'       => '19, George Tienne Crescent, Hilltop Estate, Aboru, Iyana Ipaja, Lagos, Nigeria.',
		'copyright_text'       => 'LiftedPath Empowerment Foundation. All Rights Reserved.',
		'form_recipient_email' => 'liftedpathfoundation@gmail.com',
		'marquee_phrases'      => array(
			array( 'phrase' => 'Brighter communities' ),
			array( 'phrase' => 'Greater impact' ),
			array( 'phrase' => 'Opportunity today, possibility tomorrow' ),
			array( 'phrase' => 'Learn. Build. Grow.' ),
			array( 'phrase' => 'Lifting potential' ),
			array( 'phrase' => 'Inspiring purpose' ),
			array( 'phrase' => 'Creating futures' ),
		),
	);
}

/**
 * Store the seed the first time ACF can write options.
 */
function liftedpath_seed_settings() {
	if ( ! function_exists( 'update_field' ) || get_option( 'liftedpath_settings_seeded' ) ) {
		return;
	}

	foreach ( liftedpath_settings_seed() as $name => $value ) {
		$current = get_field( $name, 'option' );

		if ( null === $current || false === $current || '' === $current ) {
			update_field( $name, $value, 'option' );
		}
	}

	update_option( 'liftedpath_settings_seeded', 1, false );
}
add_action( 'acf/init', 'liftedpath_seed_settings', 20 );

/**
 * Starting menu subtitles. An editor can change or clear each one on the page.
 *
 * @return array<string, string>
 */
function liftedpath_menu_subtitle_seed(): array {
	return array(
		'learning/lift-root'                 => 'Early Education',
		'learning/lift-grow'                 => 'Secondary Education',
		'learning/lift-beyond'               => 'Higher Education',
		'learning/lift-equip'                => 'Skills & Vocational Development',
		'what-we-do/lift-beyond-barriers'    => 'Special Needs Education',
		'what-we-do/lift-beyond-borders'     => 'Juvenile & Correctional Center Education Outreach',
	);
}

/**
 * Store empty menu subtitles once the page field exists.
 */
function liftedpath_seed_menu_subtitles(): void {
	if ( ! function_exists( 'update_field' ) || ! function_exists( 'acf_get_field' ) || get_option( 'liftedpath_menu_subtitles_seeded' ) ) {
		return;
	}

	if ( ! acf_get_field( 'field_liftedpath_menu_subtitle' ) ) {
		return;
	}

	foreach ( liftedpath_menu_subtitle_seed() as $path => $detail ) {
		$page = get_page_by_path( $path );

		if ( ! $page instanceof WP_Post ) {
			return;
		}

		$current = get_field( 'menu_subtitle', $page->ID );

		if ( is_string( $current ) && '' !== trim( $current ) ) {
			continue;
		}

		update_field( 'menu_subtitle', $detail, $page->ID );
	}

	update_option( 'liftedpath_menu_subtitles_seeded', 1, false );
}
add_action( 'acf/init', 'liftedpath_seed_menu_subtitles', 30 );

/**
 * Load the settings field group when a new Footer field is not in the database yet.
 */
function liftedpath_import_settings_fields(): void {
	if ( ! function_exists( 'acf_import_field_group' ) || ! function_exists( 'acf_get_field' ) ) {
		return;
	}

	if ( acf_get_field( 'field_liftedpath_facebook_show' ) ) {
		return;
	}

	$path = get_template_directory() . '/acf-json/group_liftedpath_settings.json';
	$raw  = is_readable( $path ) ? file_get_contents( $path ) : false;
	$json = is_string( $raw ) ? json_decode( $raw, true ) : null;

	if ( ! is_array( $json ) || ! isset( $json['key'] ) ) {
		return;
	}

	acf_import_field_group( $json );
}

/**
 * Point the settings logo at the current custom logo until an editor chooses one.
 */
function liftedpath_seed_logo(): void {
	if ( ! function_exists( 'update_field' ) || ! function_exists( 'acf_get_field' ) || ! acf_get_field( 'field_liftedpath_logo' ) ) {
		return;
	}

	if ( liftedpath_attachment_id( get_field( 'logo', 'option' ) ) <= 0 ) {
		$attachment_id = (int) get_theme_mod( 'custom_logo' );

		if ( $attachment_id > 0 ) {
			update_field( 'logo', $attachment_id, 'option' );
		}
	}

	$height = get_field( 'logo_height', 'option' );

	if ( ! is_numeric( $height ) || (int) $height <= 0 ) {
		update_field( 'logo_height', 104, 'option' );
	}

	liftedpath_import_settings_fields();

	if ( ! acf_get_field( 'field_liftedpath_footer_wordmark' ) ) {
		return;
	}

	$footer_height = get_field( 'footer_logo_height', 'option' );

	if ( ! is_numeric( $footer_height ) || (int) $footer_height <= 0 ) {
		update_field( 'footer_logo_height', 95, 'option' );
	}

	$wordmark = get_field( 'footer_wordmark', 'option' );

	if ( ! is_string( $wordmark ) ) {
		$short_name = get_field( 'short_name', 'option' );
		update_field( 'footer_wordmark', is_string( $short_name ) && '' !== trim( $short_name ) ? $short_name : 'LiftedPath', 'option' );
	}

	$location = get_field( 'location_heading', 'option' );

	if ( ! is_string( $location ) ) {
		update_field( 'location_heading', 'Our Location', 'option' );
	}
}
add_action( 'acf/init', 'liftedpath_seed_logo', 30 );
