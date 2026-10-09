<?php
/**
 * LiftedPath functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package LiftedPath
 */

if ( ! defined( 'LIFTEDPATH_VERSION' ) ) {
	define( 'LIFTEDPATH_VERSION', '0.19.9' );
}

$liftedpath_boot_log = get_template_directory() . '/boot-error.txt';

register_shutdown_function(
	static function () use ( $liftedpath_boot_log ) {
		$error = error_get_last();
		$fatal = array( E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR );

		if ( is_array( $error ) && in_array( $error['type'] ?? 0, $fatal, true ) ) {
			$line = (string) ( $error['message'] ?? '' ) . ' in ' . (string) ( $error['file'] ?? '' ) . ':' . (string) ( $error['line'] ?? '' );
			file_put_contents( $liftedpath_boot_log, $line );
			return;
		}

		if ( is_file( $liftedpath_boot_log ) ) {
			unlink( $liftedpath_boot_log );
		}
	}
);

/**
 * Sets up theme defaults and registers support for various WordPress features.
 */
function liftedpath_setup() {
	load_theme_textdomain( 'liftedpath', get_template_directory() . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );

	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	add_theme_support( 'align-wide' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/editor.css' );
	add_editor_style( 'assets/blocks.css' );

	add_image_size( 'liftedpath-hero', 1920, 1080, true );
	add_image_size( 'liftedpath-card', 720, 960, true );
	add_image_size( 'liftedpath-portrait', 900, 1200, true );

	add_theme_support(
		'custom-logo',
		array(
			'height'      => 128,
			'width'       => 182,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);
}
add_action( 'after_setup_theme', 'liftedpath_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * @global int $content_width
 */
function liftedpath_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'liftedpath_content_width', 1120 );
}
add_action( 'after_setup_theme', 'liftedpath_content_width', 0 );

/**
 * Enqueue comment reply on singular views that need it.
 *
 * Block themes load style.css on their own. This theme does not ship a
 * classic navigation script.
 */
function liftedpath_scripts() {
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'liftedpath_scripts' );

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';
require get_template_directory() . '/inc/fonts.php';
require get_template_directory() . '/inc/content-types.php';
require get_template_directory() . '/inc/acf.php';
require get_template_directory() . '/inc/rest.php';
require get_template_directory() . '/inc/preview.php';
require get_template_directory() . '/inc/revalidate.php';
require get_template_directory() . '/inc/vercel-env.php';

$liftedpath_site_icon = get_template_directory() . '/inc/site-icon.php';

if ( is_readable( $liftedpath_site_icon ) ) {
	require $liftedpath_site_icon;
}

require get_template_directory() . '/inc/seo.php';
require get_template_directory() . '/inc/blocks.php';
require get_template_directory() . '/inc/block-controls.php';
require get_template_directory() . '/inc/seed.php';
