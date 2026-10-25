<?php
/**
 * Send public front-end visits on the live CMS host to the public site.
 *
 * wp-login.php, wp-admin, the REST API, cron, and uploaded media never reach
 * this hook, or they are skipped. The public site reads
 * https://cms.liftedpathfoundation.org/wp-json and the media library from
 * this host. Local WordPress (liftedpath.local) keeps the theme fallback.
 *
 * @package LiftedPath
 */

/**
 * Landing page on the public site.
 */
function liftedpath_public_landing_url(): string {
	return 'https://www.liftedpathfoundation.org/';
}

/**
 * Host stored as this install's home URL.
 */
function liftedpath_configured_home_host(): string {
	return strtolower( (string) wp_parse_url( home_url(), PHP_URL_HOST ) );
}

/**
 * Request path without a query string. The root stays `/`.
 */
function liftedpath_request_path(): string {
	$uri  = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( (string) $_SERVER['REQUEST_URI'] ) : '/';
	$path = wp_parse_url( $uri, PHP_URL_PATH );

	if ( ! is_string( $path ) || '' === $path ) {
		return '/';
	}

	$trimmed = untrailingslashit( $path );

	return '' === $trimmed ? '/' : $trimmed;
}

/**
 * Paths that must stay on the CMS host.
 *
 * `/login`, `/admin`, and `/dashboard` are WordPress shortcuts. Core sends
 * them to wp-login.php and wp-admin from `wp_redirect_admin_locations`.
 */
function liftedpath_cms_path_stays( string $path ): bool {
	$path = strtolower( $path );

	$exact = array(
		'/wp-login.php',
		'/wp-cron.php',
		'/login',
		'/login.php',
		'/admin',
		'/dashboard',
	);

	if ( in_array( $path, $exact, true ) ) {
		return true;
	}

	foreach ( array( '/wp-admin', '/wp-json' ) as $prefix ) {
		if ( $path === $prefix || str_starts_with( $path, $prefix . '/' ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Whether this front-end request should leave the CMS host.
 */
function liftedpath_should_redirect_cms_frontend(): bool {
	if ( is_admin() || wp_doing_ajax() || wp_doing_cron() || is_customize_preview() ) {
		return false;
	}

	if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
		return false;
	}

	if ( function_exists( 'wp_is_json_request' ) && wp_is_json_request() ) {
		return false;
	}

	if ( defined( 'WP_CLI' ) && WP_CLI ) {
		return false;
	}

	// The editor uses this pair while saving a theme or plugin file.
	if ( isset( $_REQUEST['wp_scrape_key'], $_REQUEST['wp_scrape_nonce'] ) ) {
		return false;
	}

	if ( 'cms.liftedpathfoundation.org' !== liftedpath_configured_home_host() ) {
		return false;
	}

	return ! liftedpath_cms_path_stays( liftedpath_request_path() );
}

/**
 * Redirect a public CMS page to the public landing page.
 *
 * Priority 1001 is after the editor preview redirect (10) and after
 * WordPress sends `/login` and `/admin` to the real screens (1000).
 */
function liftedpath_redirect_cms_frontend(): void {
	if ( ! liftedpath_should_redirect_cms_frontend() ) {
		return;
	}

	nocache_headers();
	wp_safe_redirect( liftedpath_public_landing_url(), 302 );
	exit;
}
add_action( 'template_redirect', 'liftedpath_redirect_cms_frontend', 1001 );

/**
 * The public site is a different host from the CMS.
 *
 * @param string[] $hosts Allowed redirect hosts.
 * @return string[]
 */
function liftedpath_allow_public_landing_host( array $hosts ): array {
	$hosts[] = 'www.liftedpathfoundation.org';

	return $hosts;
}
add_filter( 'allowed_redirect_hosts', 'liftedpath_allow_public_landing_host' );
