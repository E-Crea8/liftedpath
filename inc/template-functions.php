<?php
/**
 * Functions which enhance the theme by hooking into WordPress.
 *
 * @package LiftedPath
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param string[] $classes Classes for the body element.
 * @return string[]
 */
function liftedpath_body_classes( $classes ) {
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	$classes[] = 'liftedpath';

	return $classes;
}
add_filter( 'body_class', 'liftedpath_body_classes' );

/**
 * Add a pingback url auto-discovery header for singular content.
 */
function liftedpath_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', 'liftedpath_pingback_header' );
