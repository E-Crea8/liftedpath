<?php
/**
 * Font delivery.
 *
 * Design 01 faces ship in the theme. Administrators can install more families
 * from the Site Editor Google Fonts collection. WordPress copies those files
 * onto this site. Public pages never request Google.
 *
 * @package LiftedPath
 */

/**
 * Whether a font source is a Google host.
 *
 * @param string $src Font file or stylesheet URL.
 */
function liftedpath_is_google_font_url( $src ) {
	return is_string( $src ) && ( str_contains( $src, 'fonts.googleapis.com' ) || str_contains( $src, 'fonts.gstatic.com' ) );
}

/**
 * Drop Google-hosted sources from a font-face list.
 *
 * A face that only pointed at Google is omitted. The family stack then uses
 * the next available face, which for Design 01 is the local file.
 *
 * @param array $fonts Font-face groups from WP_Font_Face_Resolver.
 * @return array
 */
function liftedpath_keep_local_font_faces( $fonts ) {
	$kept_groups = array();

	foreach ( $fonts as $faces ) {
		$kept = array();

		foreach ( $faces as $face ) {
			$sources = isset( $face['src'] ) ? (array) $face['src'] : array();
			$local   = array();

			foreach ( $sources as $src ) {
				if ( ! liftedpath_is_google_font_url( $src ) ) {
					$local[] = $src;
				}
			}

			if ( $local ) {
				$face['src'] = $local;
				$kept[]      = $face;
			}
		}

		if ( $kept ) {
			$kept_groups[] = $kept;
		}
	}

	return $kept_groups;
}

/**
 * Print theme font faces on the public front, without Google hosts.
 */
function liftedpath_print_front_font_faces() {
	if ( is_admin() || ! class_exists( 'WP_Font_Face_Resolver' ) ) {
		return;
	}

	$fonts = WP_Font_Face_Resolver::get_fonts_from_theme_json();
	wp_print_font_faces( liftedpath_keep_local_font_faces( $fonts ) );
}

/**
 * Remove the core front-end print so this theme can filter the sources first.
 */
function liftedpath_replace_front_font_print() {
	remove_action( 'wp_head', 'wp_print_font_faces', 50 );
}
add_action( 'after_setup_theme', 'liftedpath_replace_front_font_print' );
add_action( 'wp_head', 'liftedpath_print_front_font_faces', 50 );

/**
 * Do not print a Google Fonts stylesheet on the public front.
 */
function liftedpath_dequeue_google_fonts() {
	if ( is_admin() ) {
		return;
	}

	global $wp_styles;

	if ( ! ( $wp_styles instanceof WP_Styles ) ) {
		return;
	}

	foreach ( $wp_styles->registered as $handle => $style ) {
		if ( liftedpath_is_google_font_url( $style->src ) ) {
			wp_dequeue_style( $handle );
			wp_deregister_style( $handle );
		}
	}
}
add_action( 'wp_enqueue_scripts', 'liftedpath_dequeue_google_fonts', 100 );
add_action( 'wp_print_styles', 'liftedpath_dequeue_google_fonts', 100 );

/**
 * Do not preconnect to Google Fonts on the public front.
 *
 * @param array  $urls     Resource hint URLs.
 * @param string $relation Hint relation.
 * @return array
 */
function liftedpath_strip_google_font_hints( $urls, $relation ) {
	if ( is_admin() || ( 'preconnect' !== $relation && 'dns-prefetch' !== $relation ) ) {
		return $urls;
	}

	$kept = array();

	foreach ( $urls as $url ) {
		$href = is_array( $url ) ? ( $url['href'] ?? '' ) : $url;

		if ( ! liftedpath_is_google_font_url( $href ) ) {
			$kept[] = $url;
		}
	}

	return $kept;
}
add_filter( 'wp_resource_hints', 'liftedpath_strip_google_font_hints', 10, 2 );
