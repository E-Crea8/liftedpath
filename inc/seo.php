<?php
/**
 * Search fields, local identity, verification codes, and redirects.
 *
 * Yoast SEO is the per-page editor. This file reads those fields, keeps the
 * page SEO fields as the fallback, and publishes the Search & verification
 * screen under the LiftedPath menu.
 *
 * @package LiftedPath
 */

/**
 * Add Search & verification under the LiftedPath menu.
 */
function liftedpath_seo_menu(): void {
	add_submenu_page(
		'liftedpath-settings',
		__( 'LiftedPath settings', 'liftedpath' ),
		__( 'Settings', 'liftedpath' ),
		'manage_options',
		'liftedpath-settings'
	);
	add_submenu_page(
		'liftedpath-settings',
		__( 'Search and verification', 'liftedpath' ),
		__( 'Search & verification', 'liftedpath' ),
		'manage_options',
		'liftedpath-discovery',
		'liftedpath_seo_render_screen'
	);
}
add_action( 'admin_menu', 'liftedpath_seo_menu', 99 );

/**
 * Register the discovery option.
 */
function liftedpath_seo_register_setting(): void {
	register_setting(
		'liftedpath_discovery',
		'liftedpath_discovery',
		array(
			'type'              => 'array',
			'sanitize_callback' => 'liftedpath_discovery_sanitize',
			'default'           => array(),
			'show_in_rest'      => false,
		)
	);
}
add_action( 'admin_init', 'liftedpath_seo_register_setting' );

/**
 * Only an administrator can save the screen.
 */
function liftedpath_seo_option_capability(): string {
	return 'manage_options';
}
add_filter( 'option_page_capability_liftedpath_discovery', 'liftedpath_seo_option_capability' );

/**
 * Render the Search & verification screen.
 */
function liftedpath_seo_render_screen(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$discovery = liftedpath_discovery_get();
	$redirects = $discovery['redirects'];

	while ( count( $redirects ) < 8 ) {
		$redirects[] = array(
			'from'   => '',
			'to'     => '',
			'status' => 301,
		);
	}

	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Search and verification', 'liftedpath' ); ?></h1>
		<p><?php esc_html_e( 'These values are published on the public site. After you choose Save Changes, the next visit uses them.', 'liftedpath' ); ?></p>
		<p><?php esc_html_e( 'Edit a page’s search title, description, and social image in the Yoast SEO box on that page. When a Yoast title or description is empty, the SEO title and SEO description on the page are used. The default share image is Share image on LiftedPath settings.', 'liftedpath' ); ?></p>
		<?php settings_errors( 'liftedpath_discovery' ); ?>
		<form method="post" action="options.php">
			<?php settings_fields( 'liftedpath_discovery' ); ?>

			<h2><?php esc_html_e( 'Organization', 'liftedpath' ); ?></h2>
			<p><?php esc_html_e( 'The legal name, address, phone, and email come from LiftedPath settings. This summary is used in structured data and on the public /llms.txt page.', 'liftedpath' ); ?></p>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="liftedpath-area-served"><?php esc_html_e( 'Area served', 'liftedpath' ); ?></label></th>
					<td><input name="liftedpath_discovery[area_served]" id="liftedpath-area-served" type="text" class="regular-text" value="<?php echo esc_attr( $discovery['area_served'] ); ?>"></td>
				</tr>
				<tr>
					<th scope="row"><label for="liftedpath-geo-summary"><?php esc_html_e( 'Organization summary', 'liftedpath' ); ?></label></th>
					<td><textarea name="liftedpath_discovery[geo_summary]" id="liftedpath-geo-summary" class="large-text" rows="5"><?php echo esc_textarea( $discovery['geo_summary'] ); ?></textarea></td>
				</tr>
				<tr>
					<th scope="row"><label for="liftedpath-same-as"><?php esc_html_e( 'Profile links', 'liftedpath' ); ?></label></th>
					<td>
						<textarea name="liftedpath_discovery[same_as_text]" id="liftedpath-same-as" class="large-text" rows="4"><?php echo esc_textarea( implode( "\n", $discovery['same_as'] ) ); ?></textarea>
						<p class="description"><?php esc_html_e( 'One web address per line. Add the Google Business Profile, and other public profiles that should be connected with the foundation.', 'liftedpath' ); ?></p>
					</td>
				</tr>
			</table>

			<h2><?php esc_html_e( 'Verification codes', 'liftedpath' ); ?></h2>
			<p><?php esc_html_e( 'Paste the code the platform shows you. A whole meta tag is accepted, and the code is taken from it. Leave a box empty until you have a code. An empty box adds nothing to the public site.', 'liftedpath' ); ?></p>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="liftedpath-gsc"><?php esc_html_e( 'Google Search Console', 'liftedpath' ); ?></label></th>
					<td>
						<input name="liftedpath_discovery[google_site_verification]" id="liftedpath-gsc" type="text" class="regular-text" value="<?php echo esc_attr( $discovery['google_site_verification'] ); ?>">
						<p class="description"><?php esc_html_e( 'The content value from the HTML tag method.', 'liftedpath' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="liftedpath-ga"><?php esc_html_e( 'Google Analytics', 'liftedpath' ); ?></label></th>
					<td>
						<input name="liftedpath_discovery[google_analytics]" id="liftedpath-ga" type="text" class="regular-text" value="<?php echo esc_attr( $discovery['google_analytics'] ); ?>" placeholder="G-XXXXXXXX">
						<p class="description"><?php esc_html_e( 'Measurement ID. When Tag Manager is set, Analytics is kept here and is not printed, so you can add it inside Tag Manager without counting visitors twice.', 'liftedpath' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="liftedpath-gtm"><?php esc_html_e( 'Google Tag Manager', 'liftedpath' ); ?></label></th>
					<td>
						<input name="liftedpath_discovery[google_tag_manager]" id="liftedpath-gtm" type="text" class="regular-text" value="<?php echo esc_attr( $discovery['google_tag_manager'] ); ?>" placeholder="GTM-XXXX">
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="liftedpath-gbp"><?php esc_html_e( 'Google Business Profile', 'liftedpath' ); ?></label></th>
					<td>
						<input name="liftedpath_discovery[google_business_verification]" id="liftedpath-gbp" type="text" class="regular-text" value="<?php echo esc_attr( $discovery['google_business_verification'] ); ?>">
						<p class="description"><?php esc_html_e( 'Google usually verifies a Business Profile by phone, email, postcard, or Search Console. Paste a code here only when Google asks for a website meta tag. Put the public profile link in Profile links.', 'liftedpath' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="liftedpath-bing"><?php esc_html_e( 'Bing Webmaster Tools', 'liftedpath' ); ?></label></th>
					<td>
						<input name="liftedpath_discovery[bing_verification]" id="liftedpath-bing" type="text" class="regular-text" value="<?php echo esc_attr( $discovery['bing_verification'] ); ?>">
						<p class="description"><?php esc_html_e( 'The content value from the msvalidate.01 meta tag.', 'liftedpath' ); ?></p>
					</td>
				</tr>
			</table>

			<h2><?php esc_html_e( 'Redirects', 'liftedpath' ); ?></h2>
			<p><?php esc_html_e( 'A public path such as /old-name sends visitors to another public path, or to an https address. The change is active on the next visit.', 'liftedpath' ); ?></p>
			<table class="widefat striped">
				<thead>
					<tr>
						<th><?php esc_html_e( 'From', 'liftedpath' ); ?></th>
						<th><?php esc_html_e( 'To', 'liftedpath' ); ?></th>
						<th><?php esc_html_e( 'Type', 'liftedpath' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $redirects as $index => $redirect ) : ?>
						<tr>
							<td><input name="liftedpath_discovery[redirects][<?php echo esc_attr( (string) $index ); ?>][from]" type="text" class="regular-text" value="<?php echo esc_attr( $redirect['from'] ); ?>" placeholder="/old-name"></td>
							<td><input name="liftedpath_discovery[redirects][<?php echo esc_attr( (string) $index ); ?>][to]" type="text" class="regular-text" value="<?php echo esc_attr( $redirect['to'] ); ?>" placeholder="/about"></td>
							<td>
								<select name="liftedpath_discovery[redirects][<?php echo esc_attr( (string) $index ); ?>][status]">
									<option value="301" <?php selected( 301, (int) $redirect['status'] ); ?>><?php esc_html_e( '301 Permanent', 'liftedpath' ); ?></option>
									<option value="302" <?php selected( 302, (int) $redirect['status'] ); ?>><?php esc_html_e( '302 Temporary', 'liftedpath' ); ?></option>
								</select>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>

			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}

/**
 * Explain the page SEO fields when Yoast is the editor.
 *
 * @param array<string, mixed> $field ACF field.
 * @return array<string, mixed>
 */
function liftedpath_seo_title_instructions( array $field ): array {
	if ( defined( 'WPSEO_VERSION' ) ) {
		$field['instructions'] = __( 'Used when the Yoast SEO title on this page is empty.', 'liftedpath' );
	}

	return $field;
}
add_filter( 'acf/prepare_field/name=seo_title', 'liftedpath_seo_title_instructions' );

/**
 * Explain the page SEO description when Yoast is the editor.
 *
 * @param array<string, mixed> $field ACF field.
 * @return array<string, mixed>
 */
function liftedpath_seo_description_instructions( array $field ): array {
	if ( defined( 'WPSEO_VERSION' ) ) {
		$field['instructions'] = __( 'Used when the Yoast meta description on this page is empty.', 'liftedpath' );
	}

	return $field;
}
add_filter( 'acf/prepare_field/name=seo_description', 'liftedpath_seo_description_instructions' );

/**
 * Default discovery values. Verification codes stay empty.
 *
 * @return array<string, mixed>
 */
function liftedpath_discovery_defaults(): array {
	return array(
		'area_served'                   => 'Lagos, Nigeria',
		'geo_summary'                   => 'LiftedPath Empowerment Foundation is a non-governmental organization in Aboru, Iyana Ipaja, Lagos, Nigeria. It helps children, teenagers, and young adults break barriers to education and opportunity through school support, vocational training, mentorship, orphanage outreach, and seasonal help for families.',
		'same_as'                       => array(),
		'google_site_verification'      => '',
		'google_analytics'              => '',
		'google_tag_manager'            => '',
		'google_business_verification'  => '',
		'bing_verification'             => '',
		'redirects'                     => array(),
	);
}

/**
 * Stored discovery values, with missing keys filled from the defaults.
 *
 * @return array<string, mixed>
 */
function liftedpath_discovery_get(): array {
	$stored = get_option( 'liftedpath_discovery', null );

	if ( ! is_array( $stored ) ) {
		return liftedpath_discovery_defaults();
	}

	$defaults = liftedpath_discovery_defaults();
	$merged   = $defaults;

	foreach ( $defaults as $key => $default ) {
		if ( array_key_exists( $key, $stored ) ) {
			$merged[ $key ] = $stored[ $key ];
		}
	}

	$merged['same_as']   = is_array( $merged['same_as'] ) ? array_values( $merged['same_as'] ) : array();
	$merged['redirects'] = is_array( $merged['redirects'] ) ? array_values( $merged['redirects'] ) : array();

	return $merged;
}

/**
 * Sanitize the Search & verification form.
 *
 * @param mixed $input Raw option value.
 * @return array<string, mixed>
 */
function liftedpath_discovery_sanitize( mixed $input ): array {
	$previous = liftedpath_discovery_get();

	if ( ! is_array( $input ) ) {
		return $previous;
	}

	$area = isset( $input['area_served'] ) && is_string( $input['area_served'] ) ? sanitize_text_field( $input['area_served'] ) : '';
	$area = function_exists( 'mb_substr' ) ? mb_substr( $area, 0, 160, 'UTF-8' ) : substr( $area, 0, 160 );

	$summary = isset( $input['geo_summary'] ) && is_string( $input['geo_summary'] ) ? sanitize_textarea_field( $input['geo_summary'] ) : '';
	$summary = function_exists( 'mb_substr' ) ? mb_substr( $summary, 0, 2000, 'UTF-8' ) : substr( $summary, 0, 2000 );

	$links = '';

	if ( isset( $input['same_as_text'] ) && is_string( $input['same_as_text'] ) ) {
		$links = $input['same_as_text'];
	}

	return array(
		'area_served'                  => $area,
		'geo_summary'                  => $summary,
		'same_as'                      => liftedpath_seo_profile_links( $links ),
		'google_site_verification'     => liftedpath_seo_keep_code( $input['google_site_verification'] ?? '', (string) $previous['google_site_verification'], '/^[A-Za-z0-9_-]{8,200}$/', '', 'google_site_verification', __( 'The Search Console code was not recognized, so the previous value was kept.', 'liftedpath' ) ),
		'google_analytics'             => liftedpath_seo_keep_code( $input['google_analytics'] ?? '', (string) $previous['google_analytics'], '/^G-[A-Z0-9]{4,32}$/', '/G-[A-Z0-9]{4,32}/', 'google_analytics', __( 'The Analytics measurement ID was not recognized, so the previous value was kept.', 'liftedpath' ) ),
		'google_tag_manager'           => liftedpath_seo_keep_code( $input['google_tag_manager'] ?? '', (string) $previous['google_tag_manager'], '/^GTM-[A-Z0-9]{4,32}$/', '/GTM-[A-Z0-9]{4,32}/', 'google_tag_manager', __( 'The Tag Manager container ID was not recognized, so the previous value was kept.', 'liftedpath' ) ),
		'google_business_verification' => liftedpath_seo_keep_code( $input['google_business_verification'] ?? '', (string) $previous['google_business_verification'], '/^[A-Za-z0-9_-]{8,200}$/', '', 'google_business_verification', __( 'The Business Profile code was not recognized, so the previous value was kept.', 'liftedpath' ) ),
		'bing_verification'            => liftedpath_seo_keep_code( $input['bing_verification'] ?? '', (string) $previous['bing_verification'], '/^[A-Za-z0-9_-]{8,200}$/', '', 'bing_verification', __( 'The Bing verification code was not recognized, so the previous value was kept.', 'liftedpath' ) ),
		'redirects'                    => liftedpath_seo_redirects( $input['redirects'] ?? array() ),
	);
}

/**
 * Keep a verification code, or the previous one when the new value is not recognized.
 *
 * @param mixed  $raw      Submitted value.
 * @param string $previous Stored value.
 * @param string $anchored Full-string pattern.
 * @param string $search   Substring pattern. Empty skips the search.
 * @param string $code     Settings error code.
 * @param string $message  Editor-facing message.
 */
function liftedpath_seo_keep_code( mixed $raw, string $previous, string $anchored, string $search, string $code, string $message ): string {
	if ( ! is_string( $raw ) || '' === trim( $raw ) ) {
		return '';
	}

	$found = liftedpath_seo_code( $raw, $anchored, $search );

	if ( '' === $found ) {
		liftedpath_seo_settings_error( $code, $message );
		return $previous;
	}

	return $found;
}

/**
 * Record a settings error when the screen is saving.
 *
 * @param string $code    Error code.
 * @param string $message Editor-facing message.
 */
function liftedpath_seo_settings_error( string $code, string $message ): void {
	if ( function_exists( 'add_settings_error' ) ) {
		add_settings_error( 'liftedpath_discovery', $code, $message );
	}
}

/**
 * Read a verification token from a bare code or a pasted meta tag.
 *
 * @param string $raw      Submitted text.
 * @param string $anchored Full-string pattern.
 * @param string $search   Substring pattern.
 */
function liftedpath_seo_code( string $raw, string $anchored, string $search ): string {
	$raw = trim( $raw );

	if ( 1 === preg_match( '/content\s*=\s*["\']([^"\']+)["\']/', $raw, $matches ) ) {
		$raw = trim( $matches[1] );
	} elseif ( '' !== $search && 1 === preg_match( $search, $raw, $matches ) ) {
		return $matches[0];
	} else {
		$raw = trim( wp_strip_all_tags( $raw ) );
	}

	if ( 1 === preg_match( $anchored, $raw ) ) {
		return $raw;
	}

	if ( '' !== $search && 1 === preg_match( $search, $raw, $matches ) ) {
		return $matches[0];
	}

	return '';
}

/**
 * Profile links, one address per line.
 *
 * @param string $raw Textarea contents.
 * @return array<int, string>
 */
function liftedpath_seo_profile_links( string $raw ): array {
	$links = array();
	$lines = preg_split( "/\r\n|\n|\r/", $raw );

	if ( ! is_array( $lines ) ) {
		return array();
	}

	foreach ( $lines as $line ) {
		if ( count( $links ) >= 12 ) {
			break;
		}

		$url = esc_url_raw( trim( $line ) );

		if ( liftedpath_seo_is_profile_url( $url ) ) {
			$links[] = $url;
		} elseif ( '' !== trim( $line ) ) {
			liftedpath_seo_settings_error(
				'profile_link',
				__( 'A profile link was not a web address and was left out.', 'liftedpath' )
			);
		}
	}

	return $links;
}

/**
 * Redirect rows from the settings form.
 *
 * @param mixed $rows Submitted rows.
 * @return array<int, array{from: string, to: string, status: int}>
 */
function liftedpath_seo_redirects( mixed $rows ): array {
	if ( ! is_array( $rows ) ) {
		return array();
	}

	$redirects = array();
	$seen      = array();

	foreach ( $rows as $row ) {
		if ( count( $redirects ) >= 20 || ! is_array( $row ) ) {
			continue;
		}

		$from = isset( $row['from'] ) && is_string( $row['from'] ) ? trim( $row['from'] ) : '';
		$to   = isset( $row['to'] ) && is_string( $row['to'] ) ? trim( $row['to'] ) : '';

		if ( '' === $from && '' === $to ) {
			continue;
		}

		$status = ( isset( $row['status'] ) && '302' === (string) $row['status'] ) ? 302 : 301;
		$to     = liftedpath_seo_is_path( $to ) ? $to : esc_url_raw( $to );

		if ( ! liftedpath_seo_is_path( $from ) || ( ! liftedpath_seo_is_path( $to ) && ! liftedpath_seo_is_https_url( $to ) ) || $from === $to || isset( $seen[ $from ] ) ) {
			liftedpath_seo_settings_error(
				'redirect',
				__( 'A redirect row was incomplete or repeated, so it was left out.', 'liftedpath' )
			);
			continue;
		}

		$seen[ $from ] = true;
		$redirects[]   = array(
			'from'   => $from,
			'to'     => $to,
			'status' => $status,
		);
	}

	return $redirects;
}

/**
 * A public page path.
 *
 * @param string $path Path.
 */
function liftedpath_seo_is_path( string $path ): bool {
	return 1 === preg_match( '#^/$|^/[a-z0-9]+(?:-[a-z0-9]+)*(?:/[a-z0-9]+(?:-[a-z0-9]+)*)*$#', $path );
}

/**
 * A public profile address. A host needs a dot, so a sentence is not saved as a link.
 *
 * @param string $url Address.
 */
function liftedpath_seo_is_profile_url( string $url ): bool {
	if ( ! liftedpath_seo_is_http_url( $url ) ) {
		return false;
	}

	$host = wp_parse_url( $url, PHP_URL_HOST );

	return is_string( $host ) && str_contains( $host, '.' ) && ! str_contains( $host, '%' ) && ! str_contains( $host, ' ' );
}

/**
 * An http or https address with a host.
 *
 * @param string $url Address.
 */
function liftedpath_seo_is_http_url( string $url ): bool {
	$parts = wp_parse_url( $url );

	if ( ! is_array( $parts ) ) {
		return false;
	}

	$scheme = isset( $parts['scheme'] ) ? $parts['scheme'] : '';
	$host   = isset( $parts['host'] ) ? $parts['host'] : '';

	return in_array( $scheme, array( 'http', 'https' ), true ) && '' !== $host && empty( $parts['user'] );
}

/**
 * An https address with a host.
 *
 * @param string $url Address.
 */
function liftedpath_seo_is_https_url( string $url ): bool {
	$parts = wp_parse_url( $url );

	if ( ! is_array( $parts ) ) {
		return false;
	}

	$scheme = isset( $parts['scheme'] ) ? $parts['scheme'] : '';
	$host   = isset( $parts['host'] ) ? $parts['host'] : '';

	return 'https' === $scheme && '' !== $host && empty( $parts['user'] );
}

/**
 * Public search payload.
 */
function liftedpath_seo_rest( WP_REST_Request $request ): WP_REST_Response {
	$discovery = liftedpath_discovery_get();

	if ( 'redirects' === $request->get_param( 'part' ) ) {
		$response = rest_ensure_response(
			array(
				'redirects' => $discovery['redirects'],
			)
		);
		$response->header( 'Cache-Control', 'no-store' );

		return $response;
	}

	$response = rest_ensure_response(
		array(
			'site_url'                     => function_exists( 'liftedpath_preview_public_base' ) ? liftedpath_preview_public_base() : 'http://localhost:3000',
			'area_served'                  => (string) $discovery['area_served'],
			'geo_summary'                  => (string) $discovery['geo_summary'],
			'same_as'                      => $discovery['same_as'],
			'share_image'                  => liftedpath_seo_share_image(),
			'google_site_verification'     => (string) $discovery['google_site_verification'],
			'google_analytics'             => (string) $discovery['google_analytics'],
			'google_tag_manager'           => (string) $discovery['google_tag_manager'],
			'google_business_verification' => (string) $discovery['google_business_verification'],
			'bing_verification'            => (string) $discovery['bing_verification'],
			'redirects'                    => $discovery['redirects'],
			'pages'                        => liftedpath_seo_pages(),
		)
	);
	$response->header( 'Cache-Control', 'no-store' );

	return $response;
}

/**
 * Register the public SEO route.
 */
function liftedpath_seo_register_route(): void {
	register_rest_route(
		'liftedpath/v1',
		'/seo',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'liftedpath_seo_rest',
			'permission_callback' => '__return_true',
			'args'                => array(
				'part' => array(
					'required' => false,
					'type'     => 'string',
				),
			),
		)
	);
}
add_action( 'rest_api_init', 'liftedpath_seo_register_route' );

/**
 * Resolved search fields for one page.
 *
 * @param WP_Post $page Page.
 * @return array{title: string, description: string, image: array<string, mixed>|null, noindex: bool}
 */
function liftedpath_seo_for_page( WP_Post $page ): array {
	$title = liftedpath_seo_public_text(
		(string) get_post_meta( $page->ID, '_yoast_wpseo_title', true ),
		liftedpath_rest_text_field( 'seo_title', (int) $page->ID ),
		$page
	);
	$description = liftedpath_seo_public_text(
		(string) get_post_meta( $page->ID, '_yoast_wpseo_metadesc', true ),
		liftedpath_rest_text_field( 'seo_description', (int) $page->ID ),
		$page
	);

	if ( '' === $title ) {
		$title = html_entity_decode( $page->post_title, ENT_QUOTES, 'UTF-8' );
	}

	return array(
		'title'       => $title,
		'description' => $description,
		'image'       => liftedpath_seo_image( $page ),
		'noindex'     => '1' === (string) get_post_meta( $page->ID, '_yoast_wpseo_meta-robots-noindex', true ),
	);
}

/**
 * Yoast text when it is set, otherwise the page SEO field.
 *
 * @param string  $yoast Yoast value.
 * @param string  $acf   Page field value.
 * @param WP_Post $page  Page.
 */
function liftedpath_seo_public_text( string $yoast, string $acf, WP_Post $page ): string {
	$yoast = liftedpath_seo_expand( $yoast, $page );

	if ( '' !== $yoast ) {
		return $yoast;
	}

	return trim( wp_strip_all_tags( $acf ) );
}

/**
 * Replace Yoast template variables and strip tags.
 *
 * @param string  $text Source text.
 * @param WP_Post $page Page.
 */
function liftedpath_seo_expand( string $text, WP_Post $page ): string {
	$text = trim( $text );

	if ( '' === $text ) {
		return '';
	}

	if ( str_contains( $text, '%%' ) && function_exists( 'wpseo_replace_vars' ) ) {
		$replaced = wpseo_replace_vars( $text, $page );
		$text     = is_string( $replaced ) ? trim( $replaced ) : $text;
	}

	return trim( wp_strip_all_tags( $text ) );
}

/**
 * Social image for a page: Yoast, featured image, then the settings share image.
 *
 * @param WP_Post $page Page.
 * @return array<string, mixed>|null
 */
function liftedpath_seo_image( WP_Post $page ): ?array {
	$image = liftedpath_rest_image( (int) get_post_meta( $page->ID, '_yoast_wpseo_opengraph-image-id', true ) );

	if ( null !== $image ) {
		return $image;
	}

	$url = get_post_meta( $page->ID, '_yoast_wpseo_opengraph-image', true );

	if ( is_string( $url ) && '' !== $url ) {
		$image = liftedpath_rest_image( (int) attachment_url_to_postid( $url ) );

		if ( null !== $image ) {
			return $image;
		}
	}

	$featured = liftedpath_rest_featured_image( (int) $page->ID );

	if ( null !== $featured ) {
		return $featured;
	}

	return liftedpath_seo_share_image();
}

/**
 * Share image from LiftedPath settings.
 *
 * @return array<string, mixed>|null
 */
function liftedpath_seo_share_image(): ?array {
	if ( ! function_exists( 'get_field' ) ) {
		return null;
	}

	$value = get_field( 'share_image', 'option' );

	return liftedpath_rest_image( is_numeric( $value ) ? (int) $value : 0 );
}

/**
 * Published pages for the sitemap and /llms.txt.
 *
 * @return array<int, array<string, mixed>>
 */
function liftedpath_seo_pages(): array {
	$posts = get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'posts_per_page' => 100,
			'orderby'        => 'title',
			'order'          => 'ASC',
		)
	);
	$rows  = array();

	foreach ( $posts as $page ) {
		if ( ! $page instanceof WP_Post || '' !== $page->post_password ) {
			continue;
		}

		$seo    = liftedpath_seo_for_page( $page );
		$rows[] = array(
			'path'        => liftedpath_rest_public_path( $page ),
			'modified'    => liftedpath_rest_modified( $page ),
			'title'       => $seo['title'],
			'description' => $seo['description'],
			'indexable'   => ! $seo['noindex'],
		);
	}

	usort(
		$rows,
		static function ( array $left, array $right ): int {
			if ( '/' === $left['path'] ) {
				return -1;
			}

			if ( '/' === $right['path'] ) {
				return 1;
			}

			return strcmp( (string) $left['path'], (string) $right['path'] );
		}
	);

	return $rows;
}

/**
 * Write the generated search text once, without replacing a later edit.
 */
function liftedpath_seo_seed(): void {
	if ( '2' === (string) get_option( 'liftedpath_seo_copy_version', '' ) ) {
		return;
	}

	$GLOBALS['liftedpath_seo_seeding'] = true;
	$paths                             = array();

	foreach ( liftedpath_seo_copy() as $path => $copy ) {
		$page = liftedpath_seo_page_by_path( $path );

		if ( ! $page instanceof WP_Post ) {
			continue;
		}

		if ( liftedpath_seo_fill_page( $page, $copy ) ) {
			$paths[] = $path;
		}
	}

	if ( false === get_option( 'liftedpath_discovery', false ) ) {
		add_option( 'liftedpath_discovery', liftedpath_discovery_defaults(), '', false );
	}

	$paths = array_merge( $paths, liftedpath_seo_hide_editor_previews() );

	unset( $GLOBALS['liftedpath_seo_seeding'] );
	update_option( 'liftedpath_seo_copy_version', '2', false );
	liftedpath_seo_refresh_public( $paths );
}
add_action( 'init', 'liftedpath_seo_seed', 30 );

/**
 * Fill empty Yoast fields, and replace page SEO fields that are still the original seed.
 *
 * @param WP_Post              $page Page.
 * @param array<string, mixed> $copy Generated copy.
 */
function liftedpath_seo_fill_page( WP_Post $page, array $copy ): bool {
	$changed      = false;
	$acf_title    = liftedpath_rest_text_field( 'seo_title', (int) $page->ID );
	$acf_desc     = liftedpath_rest_text_field( 'seo_description', (int) $page->ID );
	$yoast_title  = (string) get_post_meta( $page->ID, '_yoast_wpseo_title', true );
	$yoast_desc   = (string) get_post_meta( $page->ID, '_yoast_wpseo_metadesc', true );
	$replace_title = isset( $copy['replace_titles'] ) && is_array( $copy['replace_titles'] ) ? $copy['replace_titles'] : array();
	$replace_desc  = isset( $copy['replace_descriptions'] ) && is_array( $copy['replace_descriptions'] ) ? $copy['replace_descriptions'] : array();
	$new_title     = ( '' === $acf_title || in_array( $acf_title, $replace_title, true ) ) ? (string) $copy['title'] : $acf_title;
	$new_desc      = ( '' === $acf_desc || in_array( $acf_desc, $replace_desc, true ) ) ? (string) $copy['description'] : $acf_desc;

	if ( $acf_title !== $new_title ) {
		liftedpath_seo_write_field( 'seo_title', $new_title, (int) $page->ID );
		$changed = true;
	}

	if ( $acf_desc !== $new_desc ) {
		liftedpath_seo_write_field( 'seo_description', $new_desc, (int) $page->ID );
		$changed = true;
	}

	if ( '' === trim( $yoast_title ) || in_array( $yoast_title, $replace_title, true ) ) {
		if ( $yoast_title !== $new_title ) {
			update_post_meta( $page->ID, '_yoast_wpseo_title', $new_title );
			$changed = true;
		}
	}

	if ( '' === trim( $yoast_desc ) || in_array( $yoast_desc, $replace_desc, true ) ) {
		if ( $yoast_desc !== $new_desc ) {
			update_post_meta( $page->ID, '_yoast_wpseo_metadesc', $new_desc );
			$changed = true;
		}
	}

	$focus = (string) get_post_meta( $page->ID, '_yoast_wpseo_focuskw', true );

	if ( '' === trim( $focus ) && '' !== (string) $copy['focus'] ) {
		update_post_meta( $page->ID, '_yoast_wpseo_focuskw', (string) $copy['focus'] );
		$changed = true;
	}

	if ( $changed ) {
		clean_post_cache( $page->ID );
	}

	return $changed;
}

/**
 * Write a page SEO field through ACF when it is available.
 *
 * @param string $name    Field name.
 * @param string $value   Field value.
 * @param int    $post_id Page ID.
 */
function liftedpath_seo_write_field( string $name, string $value, int $post_id ): void {
	if ( function_exists( 'update_field' ) ) {
		update_field( $name, $value, $post_id );
		return;
	}

	update_post_meta( $post_id, $name, $value );
}

/**
 * Keep the block-preview pages and the sample page out of the sitemap.
 *
 * An empty Yoast index setting is changed to noindex. A saved choice stays.
 *
 * @return array<int, string>
 */
function liftedpath_seo_hide_editor_previews(): array {
	$paths = array();

	foreach ( array( '/block-preview', '/home-sections', '/inner-sections', '/sample-page' ) as $path ) {
		$page = liftedpath_seo_page_by_path( $path );

		if ( ! $page instanceof WP_Post ) {
			continue;
		}

		$current = (string) get_post_meta( $page->ID, '_yoast_wpseo_meta-robots-noindex', true );

		if ( '' !== $current ) {
			continue;
		}

		update_post_meta( $page->ID, '_yoast_wpseo_meta-robots-noindex', '1' );
		clean_post_cache( $page->ID );
		$paths[] = $path;
	}

	return $paths;
}

/**
 * Find a published page by its public path.
 *
 * @param string $path Public path.
 */
function liftedpath_seo_page_by_path( string $path ): ?WP_Post {
	if ( '/' === $path ) {
		$front = (int) get_option( 'page_on_front' );
		$page  = $front ? get_post( $front ) : null;

		return ( $page instanceof WP_Post && 'page' === $page->post_type && 'publish' === $page->post_status ) ? $page : null;
	}

	$page = get_page_by_path( trim( $path, '/' ) );

	return ( $page instanceof WP_Post && 'publish' === $page->post_status ) ? $page : null;
}

/**
 * Ask Next.js to refresh the pages whose search text changed.
 *
 * One failure stops the rest, so a down site does not wait on every page.
 *
 * @param array<int, string> $paths Public paths.
 */
function liftedpath_seo_refresh_public( array $paths ): void {
	if ( ! function_exists( 'liftedpath_revalidate_send' ) ) {
		return;
	}

	foreach ( $paths as $path ) {
		$code = liftedpath_revalidate_send(
			array(
				'action' => 'update',
				'path'   => $path,
			)
		);

		if ( 200 !== $code ) {
			return;
		}
	}

	if ( array() === $paths ) {
		liftedpath_revalidate_send(
			array(
				'action' => 'update',
				'tag'    => 'seo',
			)
		);
	}
}

/**
 * Refresh a published page when its Yoast fields change.
 *
 * @param int    $meta_id  Meta row ID.
 * @param int    $post_id  Page ID.
 * @param string $meta_key Meta key.
 */
function liftedpath_seo_revalidate_meta( int $meta_id, int $post_id, string $meta_key ): void {
	unset( $meta_id );

	if ( ! empty( $GLOBALS['liftedpath_seo_seeding'] ) || ! str_starts_with( $meta_key, '_yoast_wpseo_' ) ) {
		return;
	}

	$page = get_post( $post_id );

	if ( ! $page instanceof WP_Post || 'page' !== $page->post_type || 'publish' !== $page->post_status ) {
		return;
	}

	if ( ! function_exists( 'liftedpath_revalidate_queue' ) || ! function_exists( 'liftedpath_rest_public_path' ) ) {
		return;
	}

	liftedpath_revalidate_queue(
		array(
			'action' => 'update',
			'path'   => liftedpath_rest_public_path( $page ),
		)
	);
}
add_action( 'added_post_meta', 'liftedpath_seo_revalidate_meta', 20, 3 );
add_action( 'updated_post_meta', 'liftedpath_seo_revalidate_meta', 20, 3 );

/**
 * Refresh discovery output when the verification screen is saved.
 *
 * @param mixed $old Previous value.
 * @param mixed $new Saved value.
 */
function liftedpath_seo_revalidate_discovery( mixed $old, mixed $new ): void {
	unset( $old, $new );

	if ( ! empty( $GLOBALS['liftedpath_seo_seeding'] ) || ! function_exists( 'liftedpath_revalidate_queue' ) ) {
		return;
	}

	liftedpath_revalidate_queue(
		array(
			'action' => 'update',
			'tag'    => 'seo',
		)
	);
}
add_action( 'update_option_liftedpath_discovery', 'liftedpath_seo_revalidate_discovery', 10, 2 );
add_action( 'add_option_liftedpath_discovery', 'liftedpath_seo_revalidate_discovery', 10, 2 );

/**
 * Generated search text for the public pages.
 *
 * replace_titles and replace_descriptions are the original seed values.
 * A field that no longer matches those values is an editor change and stays.
 *
 * @return array<string, array<string, mixed>>
 */
function liftedpath_seo_copy(): array {
	$home = 'LiftedPath Empowerment Foundation is a purpose driven non-governmental organization founded on a deep commitment to breaking barriers to education and opportunity for children, teenagers, and young adults.';

	return array(
		'/'                                        => liftedpath_seo_entry(
			'Breaking barriers to education and opportunity',
			'LiftedPath Empowerment Foundation helps children, teenagers, and young adults in Lagos reach education, skills, and lasting opportunity.',
			'education and opportunity',
			array( 'Home' ),
			array( $home )
		),
		'/about'                                   => liftedpath_seo_entry(
			'About the foundation',
			'LiftedPath is a Lagos foundation restoring hope through education, vocational training, and community support for young people.',
			'about LiftedPath',
			array( 'About Us' ),
			array( 'Restoring hope, creating sustainable pathways.' )
		),
		'/what-we-do'                              => liftedpath_seo_entry(
			'Education, guidance, and community support',
			'Explore LiftedPath Learning, Path Guide, and community initiatives — pathways to education, guidance, and lasting support in Lagos.',
			'what we do',
			array( 'What We Do' ),
			array( 'Explore LiftedPath Learning, Path Guide and Community Initiatives — pathways to education, guidance and lasting support.' )
		),
		'/learning'                                => liftedpath_seo_entry(
			'Learning pathways for every stage',
			'From first steps to new skills, LiftedPath helps young people in Lagos continue learning through four pathways and create their own futures.',
			'learning pathways',
			array( 'LiftedPath Learning' ),
			array( 'From first steps to new skills, we help young people continue learning and create their own futures.' )
		),
		'/impact'                                  => liftedpath_seo_entry(
			'Impact and how gifts are used',
			'See how LiftedPath accounts for every contribution, pays schools directly where possible, and directs gifts toward real opportunities.',
			'impact and accountability',
			array( 'Impact' ),
			array( 'Every contribution is managed with integrity, accountability, and purpose.' )
		),
		'/contact'                                 => liftedpath_seo_entry(
			'Contact the foundation',
			'Volunteer, partner, sponsor a student, or ask a question. LiftedPath Empowerment Foundation is in Aboru, Iyana Ipaja, Lagos.',
			'contact LiftedPath',
			array( 'Contact Us' ),
			array( 'Volunteer, partner, sponsor a student, or simply ask a question — we would love to hear from you.' )
		),
		'/donate'                                  => liftedpath_seo_entry(
			'Give to keep a student in school',
			'Give once or monthly to LiftedPath. Where possible the foundation pays schools and training providers directly for a beneficiary.',
			'donate to LiftedPath',
			array( 'Donate' ),
			array( "Give once or monthly. Where possible we pay schools and training providers directly on a beneficiary's behalf." )
		),
		'/learning/lift-root'                      => liftedpath_seo_entry(
			'LIFT-ROOT early education',
			'A strong beginning opens a lifetime of possibility. LiftedPath helps young learners in Lagos access the essentials for their first steps in education.',
			'early education',
			array( 'LIFT-ROOT' ),
			array( 'A strong beginning opens a lifetime of possibility. We help young learners access the essentials that support their first steps in education.' )
		),
		'/learning/lift-grow'                      => liftedpath_seo_entry(
			'LIFT-GROW secondary education',
			'LiftedPath supports determined students through their secondary years in Lagos, helping them stay in school and keep moving toward their goals.',
			'secondary education',
			array( 'LIFT-GROW' ),
			array( 'We support determined students through their secondary years, helping them stay in school and keep moving toward their goals.' )
		),
		'/learning/lift-beyond'                    => liftedpath_seo_entry(
			'LIFT-BEYOND higher education',
			'LiftedPath helps young adults in Lagos overcome financial barriers to further education and pursue the futures they imagine for themselves.',
			'higher education',
			array( 'LIFT-BEYOND' ),
			array( 'We help young adults overcome financial barriers to further education and pursue the futures they imagine for themselves.' )
		),
		'/learning/lift-equip'                     => liftedpath_seo_entry(
			'LIFT-EQUIP skills and vocational training',
			'Practical training and tools from LiftedPath build confidence, independence, and pathways into meaningful work for young people in Lagos.',
			'vocational training',
			array( 'LIFT-EQUIP' ),
			array( 'Practical training and tools build confidence, independence, and pathways into meaningful work.' )
		),
		'/what-we-do/path-guide'                   => liftedpath_seo_entry(
			'Path Guide',
			'Guidance and encouragement from LiftedPath help young people in Lagos navigate their next steps with confidence.',
			'path guide',
			array( 'Path Guide' ),
			array( 'Guidance and encouragement help young people navigate their next steps with confidence.' )
		),
		'/what-we-do/mentorship-coaching'          => liftedpath_seo_entry(
			'Mentorship and coaching',
			'LiftedPath mentors and coaches walk with students and trainees through study habits, career choices, and confidence.',
			'mentorship and coaching',
			array( 'Mentorship & Coaching' ),
			array( 'Our mentors and coaches walk with students and trainees through study habits, career choices and confidence, helping them stay focused on their goals.' )
		),
		'/what-we-do/community-initiatives'        => liftedpath_seo_entry(
			'Community initiatives',
			'Community-led support from LiftedPath creates opportunities for young people and families in Lagos who are facing barriers.',
			'community initiatives',
			array( 'Community Initiatives' ),
			array( 'Community-led support creates opportunities for young people and families facing barriers.' )
		),
		'/what-we-do/lift-beyond-barriers'         => liftedpath_seo_entry(
			'Lift Beyond Barriers',
			'LiftedPath believes access to education should include young people with special educational needs, and works to open that path.',
			'special educational needs',
			array( 'Lift Beyond Barriers' ),
			array( 'We believe access to education should include young people with special educational needs.' )
		),
		'/what-we-do/lift-beyond-borders'          => liftedpath_seo_entry(
			'Lift Beyond Borders',
			'Education and guidance from LiftedPath can open new pathways for young people in juvenile and correctional settings.',
			'correctional education outreach',
			array( 'Lift Beyond Borders' ),
			array( 'Education and guidance can open new pathways for young people in juvenile and correctional settings.' )
		),
		'/what-we-do/orphanage-outreach'           => liftedpath_seo_entry(
			'Orphanage outreach',
			'LiftedPath visits orphanage homes to share care, encouragement, and essentials, reminding children they are seen, valued, and remembered.',
			'orphanage outreach',
			array( 'Orphanage Outreach' ),
			array( 'We visit orphanage homes to share care, encouragement and essentials, reminding children they are seen, valued and remembered.' )
		),
		'/what-we-do/share-the-joy-of-the-season'  => liftedpath_seo_entry(
			'Share the Joy of the Season',
			'Each festive season LiftedPath reaches families facing financial hardship with food and assistance, sharing the joy of the season with dignity.',
			'festive season support',
			array( 'Share the Joy of the Season' ),
			array( 'Each festive season we reach families facing financial hardship with food items and financial assistance, sharing the joy of the season with dignity.' )
		),
	);
}

/**
 * One page of generated search text.
 *
 * @param string        $title        Search title.
 * @param string        $description  Meta description.
 * @param string        $focus        Focus keyphrase.
 * @param array<int, string> $titles  Original seed titles that may be replaced.
 * @param array<int, string> $descriptions Original seed descriptions that may be replaced.
 * @return array<string, mixed>
 */
function liftedpath_seo_entry( string $title, string $description, string $focus, array $titles, array $descriptions ): array {
	return array(
		'title'                 => $title,
		'description'           => $description,
		'focus'                 => $focus,
		'replace_titles'        => $titles,
		'replace_descriptions'  => $descriptions,
	);
}
