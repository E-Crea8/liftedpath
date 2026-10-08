<?php
/**
 * Ask Next.js to refresh published content after an editor save.
 *
 * The webhook runs after the save is stored. A failure is logged and does
 * not stop the editor.
 *
 * @package LiftedPath
 */

/**
 * Shared secret for the revalidate webhook. Empty until it is set locally.
 */
function liftedpath_revalidate_secret(): string {
	$secret = get_option( 'liftedpath_revalidate_secret', '' );

	return is_string( $secret ) ? $secret : '';
}

/**
 * Remember a webhook until the request finishes.
 *
 * @param array<string, string> $payload Path or tag, plus the action. The secret is added at send time.
 */
function liftedpath_revalidate_queue( array $payload ): void {
	if ( ! isset( $GLOBALS['liftedpath_revalidate_queue'] ) || ! is_array( $GLOBALS['liftedpath_revalidate_queue'] ) ) {
		$GLOBALS['liftedpath_revalidate_queue'] = array();
	}

	$GLOBALS['liftedpath_revalidate_queue'][] = $payload;
}

/**
 * Send the queued webhooks once.
 */
function liftedpath_revalidate_flush(): void {
	if ( empty( $GLOBALS['liftedpath_revalidate_queue'] ) || ! is_array( $GLOBALS['liftedpath_revalidate_queue'] ) ) {
		return;
	}

	$queued                                = $GLOBALS['liftedpath_revalidate_queue'];
	$GLOBALS['liftedpath_revalidate_queue'] = array();
	$seen                                  = array();

	foreach ( $queued as $payload ) {
		if ( ! is_array( $payload ) ) {
			continue;
		}

		$key = wp_json_encode( $payload );

		if ( ! is_string( $key ) || isset( $seen[ $key ] ) ) {
			continue;
		}

		$seen[ $key ] = true;
		liftedpath_revalidate_send( $payload );
	}
}
add_action( 'shutdown', 'liftedpath_revalidate_flush' );

/**
 * POST one revalidate request. Returns the HTTP status, or 0 when it was not sent.
 *
 * @param array<string, string> $payload Path or tag, plus the action.
 */
function liftedpath_revalidate_send( array $payload ): int {
	$secret = liftedpath_revalidate_secret();

	if ( '' === $secret ) {
		error_log( 'LiftedPath revalidate skipped: secret is not set.' );
		return 0;
	}

	if ( ! function_exists( 'liftedpath_preview_public_base' ) ) {
		error_log( 'LiftedPath revalidate skipped: public site URL is not available.' );
		return 0;
	}

	$payload['secret'] = $secret;
	$body              = wp_json_encode( $payload );

	if ( ! is_string( $body ) ) {
		error_log( 'LiftedPath revalidate skipped: payload could not be encoded.' );
		return 0;
	}

	$response = wp_remote_post(
		liftedpath_preview_public_base() . '/api/revalidate',
		array(
			'timeout' => 8,
			'headers' => array(
				'Content-Type' => 'application/json',
				'Accept'       => 'application/json',
			),
			'body'    => $body,
		)
	);

	if ( is_wp_error( $response ) ) {
		error_log( 'LiftedPath revalidate failed: ' . $response->get_error_code() );
		return 0;
	}

	$code = (int) wp_remote_retrieve_response_code( $response );

	if ( 200 !== $code ) {
		error_log( 'LiftedPath revalidate failed: HTTP ' . $code );
	}

	return $code;
}

/**
 * Decide whether a page status change should refresh or remove the public page.
 *
 * @param string  $new_status Status after the save.
 * @param string  $old_status Status before the save.
 * @param WP_Post $post       Page.
 */
function liftedpath_revalidate_page_action( string $new_status, string $old_status, WP_Post $post ): string {
	if ( 'page' !== $post->post_type || wp_is_post_revision( $post->ID ) || wp_is_post_autosave( $post->ID ) ) {
		return '';
	}

	if ( 'publish' === $new_status ) {
		return 'update';
	}

	if ( 'publish' === $old_status ) {
		return 'delete';
	}

	return '';
}

/**
 * Queue a page refresh when a page is published or unpublished.
 *
 * @param string  $new_status Status after the save.
 * @param string  $old_status Status before the save.
 * @param WP_Post $post       Post that changed.
 */
function liftedpath_revalidate_on_page_status( string $new_status, string $old_status, WP_Post $post ): void {
	if ( defined( 'WP_IMPORTING' ) && WP_IMPORTING ) {
		return;
	}

	$action = liftedpath_revalidate_page_action( $new_status, $old_status, $post );

	if ( '' === $action ) {
		return;
	}

	liftedpath_revalidate_queue(
		array(
			'action' => $action,
			'path'   => liftedpath_rest_public_path( $post ),
		)
	);
}
add_action( 'transition_post_status', 'liftedpath_revalidate_on_page_status', 20, 3 );

/**
 * Queue a navigation refresh.
 *
 * @param int     $post_id Navigation post ID.
 * @param WP_Post $post    Navigation post.
 */
function liftedpath_revalidate_on_navigation( int $post_id, WP_Post $post ): void {
	if ( defined( 'WP_IMPORTING' ) && WP_IMPORTING ) {
		return;
	}

	if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) || 'publish' !== $post->post_status ) {
		return;
	}

	liftedpath_revalidate_queue(
		array(
			'action' => 'update',
			'tag'    => 'nav',
		)
	);
}
add_action( 'save_post_wp_navigation', 'liftedpath_revalidate_on_navigation', 20, 2 );

/**
 * Queue a design-token refresh.
 *
 * @param int     $post_id Global styles post ID.
 * @param WP_Post $post    Global styles post.
 */
function liftedpath_revalidate_on_global_styles( int $post_id, WP_Post $post ): void {
	if ( defined( 'WP_IMPORTING' ) && WP_IMPORTING ) {
		return;
	}

	if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
		return;
	}

	liftedpath_revalidate_queue(
		array(
			'action' => 'update',
			'tag'    => 'design',
		)
	);
}
add_action( 'save_post_wp_global_styles', 'liftedpath_revalidate_on_global_styles', 20, 2 );

/**
 * Queue a settings refresh when the LiftedPath options page is saved.
 *
 * @param int|string $post_id ACF post ID. Options use `options`.
 */
function liftedpath_revalidate_on_settings( int|string $post_id ): void {
	if ( 'options' !== $post_id && 'option' !== $post_id ) {
		return;
	}

	liftedpath_revalidate_queue(
		array(
			'action' => 'update',
			'tag'    => 'settings',
		)
	);
}
add_action( 'acf/save_post', 'liftedpath_revalidate_on_settings', 20 );

/**
 * Queue a settings refresh when the Site Editor logo changes.
 *
 * @param mixed $old   Previous theme mods.
 * @param mixed $value New theme mods.
 */
function liftedpath_revalidate_on_theme_mods( $old, $value ): void {
	$old_logo = is_array( $old ) ? (int) ( $old['custom_logo'] ?? 0 ) : 0;
	$new_logo = is_array( $value ) ? (int) ( $value['custom_logo'] ?? 0 ) : 0;

	if ( $old_logo === $new_logo ) {
		return;
	}

	liftedpath_revalidate_queue(
		array(
			'action' => 'update',
			'tag'    => 'settings',
		)
	);
}
add_action( 'update_option_theme_mods_' . get_stylesheet(), 'liftedpath_revalidate_on_theme_mods', 20, 2 );
