<?php
/**
 * Site icon for wp-admin. Block themes do not show Appearance → Customize.
 *
 * @package LiftedPath
 */

/**
 * Marker stored once the LiftedPath mark is the site icon.
 */
function liftedpath_site_icon_marker(): string {
	return 'liftedpath-mark-v1';
}

/**
 * Install the logo mark as the WordPress site icon.
 */
function liftedpath_install_site_icon(): void {
	if ( wp_installing() ) {
		return;
	}

	$marker  = liftedpath_site_icon_marker();
	$current = (int) get_option( 'site_icon' );

	if ( (string) get_option( 'liftedpath_site_icon_marker', '' ) === $marker && $current > 0 && 'attachment' === get_post_type( $current ) ) {
		return;
	}

	$file = get_template_directory() . '/assets/site-icon.png';

	if ( ! is_readable( $file ) ) {
		return;
	}

	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$contents = file_get_contents( $file );

	if ( ! is_string( $contents ) || '' === $contents ) {
		return;
	}

	$upload = wp_upload_bits( 'liftedpath-site-icon.png', null, $contents );

	if ( ! empty( $upload['error'] ) || empty( $upload['file'] ) ) {
		return;
	}

	$attachment_id = wp_insert_attachment(
		array(
			'post_mime_type' => 'image/png',
			'post_title'     => 'LiftedPath site icon',
			'post_content'   => '',
			'post_status'    => 'inherit',
		),
		$upload['file']
	);

	if ( is_wp_error( $attachment_id ) || ! $attachment_id ) {
		return;
	}

	$metadata = wp_generate_attachment_metadata( $attachment_id, $upload['file'] );

	if ( is_array( $metadata ) ) {
		wp_update_attachment_metadata( $attachment_id, $metadata );
	}

	update_option( 'site_icon', (int) $attachment_id );
	update_option( 'liftedpath_site_icon_marker', $marker, false );
}
add_action( 'init', 'liftedpath_install_site_icon', 20 );

/**
 * Show the mark in wp-admin before the site icon attachment exists.
 */
function liftedpath_admin_site_icon_link(): void {
	if ( has_site_icon() ) {
		return;
	}

	printf(
		'<link rel="icon" href="%s" sizes="512x512" />' . "\n",
		esc_url( get_theme_file_uri( 'assets/site-icon.png' ) )
	);
}
add_action( 'admin_head', 'liftedpath_admin_site_icon_link', 5 );
add_action( 'login_head', 'liftedpath_admin_site_icon_link', 5 );
