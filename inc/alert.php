<?php
/**
 * Email the WordPress administration address when the public site has to keep serving.
 *
 * Next.js calls this after a failed read. The public page stays up either way.
 *
 * @package LiftedPath
 */

/**
 * Register the server-to-server alert route.
 */
function liftedpath_alert_register_route(): void {
	register_rest_route(
		'liftedpath/v1',
		'/alert',
		array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => 'liftedpath_alert_rest',
			'permission_callback' => '__return_true',
		)
	);
}
add_action( 'rest_api_init', 'liftedpath_alert_register_route' );

/**
 * Send one administration email for a public-site failure.
 *
 * @param WP_REST_Request $request JSON body from the Next.js server.
 */
function liftedpath_alert_rest( WP_REST_Request $request ): WP_REST_Response {
	$params = $request->get_json_params();

	if ( ! is_array( $params ) ) {
		return liftedpath_alert_response( 400, 'invalid_body', 'The alert could not be read.' );
	}

	$allowed = array( 'secret', 'code', 'path', 'message', 'kept' );

	foreach ( array_keys( $params ) as $key ) {
		if ( ! in_array( $key, $allowed, true ) ) {
			return liftedpath_alert_response( 400, 'invalid_body', 'The alert could not be read.' );
		}
	}

	$secret = isset( $params['secret'] ) && is_string( $params['secret'] ) ? trim( $params['secret'] ) : '';

	if ( ! liftedpath_alert_secret_matches( $secret ) ) {
		return liftedpath_alert_response( 401, 'forbidden', 'The alert was not accepted.' );
	}

	$code    = liftedpath_alert_code( $params['code'] ?? '' );
	$path    = liftedpath_alert_text( $params['path'] ?? '', 180 );
	$message = liftedpath_alert_text( $params['message'] ?? '', 280 );
	$kept    = isset( $params['kept'] ) && true === $params['kept'];

	if ( '' === $path ) {
		$path = '/';
	}

	if ( '' === $message ) {
		$message = __( 'WordPress could not be read.', 'liftedpath' );
	}

	$issue = $code . '|' . $path;

	if ( ! liftedpath_alert_allow( $issue ) ) {
		return new WP_REST_Response(
			array(
				'sent' => false,
				'held' => true,
			),
			200
		);
	}

	$sent = liftedpath_alert_mail( $path, $message, $kept );

	return new WP_REST_Response(
		array(
			'sent' => $sent,
			'held' => false,
		),
		$sent ? 200 : 502
	);
}

/**
 * The revalidate secret is the shared server credential. Both sides are hashed so the compare stays fixed length.
 *
 * @param string $given Secret from the request.
 */
function liftedpath_alert_secret_matches( string $given ): bool {
	$expected = function_exists( 'liftedpath_revalidate_secret' ) ? liftedpath_revalidate_secret() : '';

	if ( '' === $expected || '' === $given ) {
		return false;
	}

	return hash_equals( hash( 'sha256', $expected ), hash( 'sha256', $given ) );
}

/**
 * One note per issue per hour, and at most twelve notes an hour.
 *
 * @param string $issue Code and path.
 */
function liftedpath_alert_allow( string $issue ): bool {
	$issue_key = 'lp_alert_' . md5( $issue );
	$hour_key  = 'lp_alert_hour';

	if ( get_transient( $issue_key ) ) {
		return false;
	}

	$count = (int) get_transient( $hour_key );

	if ( $count >= 12 ) {
		return false;
	}

	set_transient( $issue_key, 1, HOUR_IN_SECONDS );
	set_transient( $hour_key, $count + 1, HOUR_IN_SECONDS );

	return true;
}

/**
 * Mail the Settings → General administration address.
 *
 * @param string $path    Page or read that failed.
 * @param string $message Short description. Secrets are not included.
 * @param bool   $kept    Whether visitors are still seeing the last saved copy.
 */
function liftedpath_alert_mail( string $path, string $message, bool $kept ): bool {
	$admin = get_option( 'admin_email' );
	$admin = is_string( $admin ) ? sanitize_email( $admin ) : '';

	if ( ! is_email( $admin ) ) {
		error_log( 'LiftedPath alert skipped: the administration email is empty.' );
		return false;
	}

	$standby = $kept
		? __( 'The public site is still showing the last saved copy of this content. Please resolve this so new edits reach the site.', 'liftedpath' )
		: __( 'The public site stayed online. This part is using its built-in standby until WordPress responds. Please resolve this.', 'liftedpath' );

	$body = sprintf(
		"%s\n\n%s\n%s\n\n%s\n\n%s",
		__( 'A public-site read failed. The site stayed online.', 'liftedpath' ),
		sprintf( /* translators: %s: page path or read name. */ __( 'Where: %s', 'liftedpath' ), $path ),
		sprintf( /* translators: %s: short failure description. */ __( 'What happened: %s', 'liftedpath' ), $message ),
		$standby,
		__( 'This message was sent to the WordPress administration email (Settings → General). The same issue will not send another message for an hour.', 'liftedpath' )
	);

	$sent = wp_mail(
		$admin,
		__( 'LiftedPath public site: action needed', 'liftedpath' ),
		$body,
		array( 'Content-Type: text/plain; charset=UTF-8' )
	);

	if ( ! $sent ) {
		error_log( 'LiftedPath alert failed: wp_mail returned false.' );
	}

	return (bool) $sent;
}

/**
 * @param mixed $value Raw code.
 */
function liftedpath_alert_code( $value ): string {
	$code = is_string( $value ) ? strtolower( $value ) : '';
	$code = preg_replace( '/[^a-z0-9_]/', '', $code );
	$code = is_string( $code ) ? substr( $code, 0, 40 ) : '';

	return '' === $code ? 'unavailable' : $code;
}

/**
 * @param mixed $value Raw text.
 * @param int   $max   Maximum length.
 */
function liftedpath_alert_text( $value, int $max ): string {
	if ( ! is_string( $value ) ) {
		return '';
	}

	$text = sanitize_text_field( $value );

	if ( strlen( $text ) > $max ) {
		$text = substr( $text, 0, $max );
	}

	return $text;
}

/**
 * @param int    $status  HTTP status.
 * @param string $code    Machine code.
 * @param string $message Public message. No secret.
 */
function liftedpath_alert_response( int $status, string $code, string $message ): WP_REST_Response {
	return new WP_REST_Response(
		array(
			'code'    => $code,
			'message' => $message,
		),
		$status
	);
}
