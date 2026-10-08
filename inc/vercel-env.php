<?php
/**
 * Vercel variable names on the LiftedPath settings screen.
 *
 * The values are the ones this WordPress site already uses. An administrator
 * copies them into the Vercel project. They are not shown on the public site.
 *
 * @package LiftedPath
 */

/**
 * Add the Vercel tab to the organization field group.
 *
 * @param array<int, array<string, mixed>> $fields Fields already loaded.
 * @param array<string, mixed>             $parent Field group.
 * @return array<int, array<string, mixed>>
 */
function liftedpath_vercel_env_fields( array $fields, array $parent ): array {
	if ( ! isset( $parent['key'] ) || 'group_liftedpath_settings' !== $parent['key'] ) {
		return $fields;
	}

	static $added = false;

	if ( $added ) {
		return $fields;
	}

	foreach ( $fields as $field ) {
		if ( isset( $field['key'] ) && 'field_liftedpath_tab_vercel' === $field['key'] ) {
			return $fields;
		}
	}

	$added = true;

	$fields[] = acf_validate_field(
		array(
		'key'               => 'field_liftedpath_tab_vercel',
		'label'             => __( 'Vercel Environment Variables', 'liftedpath' ),
		'name'              => '',
		'type'              => 'tab',
		'instructions'      => '',
		'required'          => 0,
		'conditional_logic' => false,
		'wrapper'           => array(
			'width' => '',
			'class' => '',
			'id'    => '',
		),
		'placement'         => 'top',
		'endpoint'          => 0,
		'parent'            => $parent['key'],
		)
	);

	$fields[] = acf_validate_field(
		array(
		'key'               => 'field_liftedpath_vercel_env',
		'label'             => '',
		'name'              => '',
		'type'              => 'message',
		'instructions'      => '',
		'required'          => 0,
		'conditional_logic' => false,
		'wrapper'           => array(
			'width' => '',
			'class' => 'liftedpath-vercel-env-field',
			'id'    => '',
		),
		'message'           => '',
		'new_lines'         => '',
		'esc_html'          => 0,
		'parent'            => $parent['key'],
		)
	);

	return $fields;
}
add_filter( 'acf/load_fields', 'liftedpath_vercel_env_fields', 20, 2 );

/**
 * Print the copyable Vercel values inside the message field.
 *
 * @param array<string, mixed> $field Field being rendered.
 */
function liftedpath_vercel_env_render( array $field ): void {
	unset( $field );

	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$rows = liftedpath_vercel_env_rows();

	echo '<div class="liftedpath-vercel-env">';
	echo '<p class="description">';
	echo esc_html__( 'Copy these into the Vercel project for the frontend. They belong to this WordPress site. The public site address stays on the Forms settings screen.', 'liftedpath' );
	echo '</p>';

	if ( function_exists( 'liftedpath_revalidate_status_note' ) ) {
		echo '<p class="description">' . esc_html( liftedpath_revalidate_status_note() ) . '</p>';
	}
	echo '<table class="form-table" role="presentation">';

	foreach ( $rows as $row ) {
		echo '<tr>';
		echo '<th scope="row"><label for="' . esc_attr( $row['id'] ) . '">' . esc_html( $row['name'] ) . '</label></th>';
		echo '<td>';
		echo '<div class="liftedpath-vercel-env__row">';
		echo '<input class="large-text code" id="' . esc_attr( $row['id'] ) . '" type="text" readonly spellcheck="false" autocomplete="off" value="' . esc_attr( $row['value'] ) . '">';
		echo '<button type="button" class="button" data-liftedpath-copy="' . esc_attr( $row['id'] ) . '" data-copied="' . esc_attr__( 'Copied', 'liftedpath' ) . '">' . esc_html__( 'Copy', 'liftedpath' ) . '</button>';
		echo '</div>';
		echo '<p class="description">' . esc_html( $row['note'] ) . '</p>';
		echo '</td>';
		echo '</tr>';
	}

	echo '</table>';
	echo '</div>';
}
add_action( 'acf/render_field/key=field_liftedpath_vercel_env', 'liftedpath_vercel_env_render' );

/**
 * Copy button and row layout for the Vercel tab.
 *
 * @param string $hook_suffix Current admin page.
 */
function liftedpath_vercel_env_assets( string $hook_suffix ): void {
	if ( 'toplevel_page_liftedpath-settings' !== $hook_suffix ) {
		return;
	}

	wp_register_style( 'liftedpath-vercel-env', false, array(), LIFTEDPATH_VERSION );
	wp_enqueue_style( 'liftedpath-vercel-env' );
	wp_add_inline_style(
		'liftedpath-vercel-env',
		'.liftedpath-vercel-env__row{display:flex;gap:8px;align-items:center;max-width:760px}.liftedpath-vercel-env__row input{flex:1;min-width:0}'
	);

	wp_register_script( 'liftedpath-vercel-env', false, array(), LIFTEDPATH_VERSION, true );
	wp_enqueue_script( 'liftedpath-vercel-env' );
	wp_add_inline_script(
		'liftedpath-vercel-env',
		"document.addEventListener('click',function(event){var button=event.target.closest('[data-liftedpath-copy]');if(!button){return;}var input=document.getElementById(button.getAttribute('data-liftedpath-copy'));if(!input){return;}var done=function(){var original=button.textContent;button.textContent=button.getAttribute('data-copied')||'Copied';window.setTimeout(function(){button.textContent=original;},1500);};if(navigator.clipboard&&navigator.clipboard.writeText){navigator.clipboard.writeText(input.value).then(done);return;}input.focus();input.select();document.execCommand('copy');done();});"
	);
}
add_action( 'admin_enqueue_scripts', 'liftedpath_vercel_env_assets' );

/**
 * The six Vercel variables for this WordPress site.
 *
 * @return array<int, array{id: string, name: string, value: string, note: string}>
 */
function liftedpath_vercel_env_rows(): array {
	$user     = liftedpath_vercel_env_preview_user();
	$password = $user instanceof WP_User ? liftedpath_vercel_env_app_password( $user ) : '';
	$login    = $user instanceof WP_User ? $user->user_login : '';

	$password_note = '' === $password
		? __( 'WordPress could not create an application password. In Users → Profile → Application Passwords, create one named Vercel preview and paste that into Vercel. It is not the password you use to log in.', 'liftedpath' )
		: __( 'Application password for the user above. It is not the password you use to log in. The same password is listed on that user’s profile as Vercel preview.', 'liftedpath' );

	return array(
		array(
			'id'    => 'liftedpath-vercel-wordpress-api-url',
			'name'  => 'WORDPRESS_API_URL',
			'value' => untrailingslashit( rest_url() ),
			'note'  => __( 'WordPress REST root for this site.', 'liftedpath' ),
		),
		array(
			'id'    => 'liftedpath-vercel-revalidate-secret',
			'name'  => 'REVALIDATE_SECRET',
			'value' => liftedpath_vercel_env_secret( 'liftedpath_revalidate_secret' ),
			'note'  => __( 'WordPress sends this when a save should refresh the public site.', 'liftedpath' ),
		),
		array(
			'id'    => 'liftedpath-vercel-preview-secret',
			'name'  => 'PREVIEW_SECRET',
			'value' => liftedpath_vercel_env_secret( 'liftedpath_preview_secret' ),
			'note'  => __( 'The public site sends this when it reads a draft.', 'liftedpath' ),
		),
		array(
			'id'    => 'liftedpath-vercel-preview-user',
			'name'  => 'WORDPRESS_PREVIEW_USER',
			'value' => $login,
			'note'  => __( 'Administrator username. This is the user login, not the email address.', 'liftedpath' ),
		),
		array(
			'id'    => 'liftedpath-vercel-preview-app-password',
			'name'  => 'WORDPRESS_PREVIEW_APP_PASSWORD',
			'value' => $password,
			'note'  => $password_note,
		),
		array(
			'id'    => 'liftedpath-vercel-form-secret',
			'name'  => 'FORM_SECRET',
			'value' => liftedpath_vercel_env_secret( 'liftedpath_forms_secret' ),
			'note'  => __( 'Server secret for the contact and donate forms. The same value is on the Forms screen.', 'liftedpath' ),
		),
	);
}

/**
 * A stored secret, created once when the tab is first opened.
 *
 * @param string $option Option name.
 */
function liftedpath_vercel_env_secret( string $option ): string {
	$secret = get_option( $option, '' );

	if ( is_string( $secret ) && '' !== $secret ) {
		return $secret;
	}

	$secret = bin2hex( random_bytes( 32 ) );
	update_option( $option, $secret, false );

	return $secret;
}

/**
 * Administrator account the preview password belongs to.
 */
function liftedpath_vercel_env_preview_user(): ?WP_User {
	$stored_id = (int) get_option( 'liftedpath_preview_user_id', 0 );

	if ( $stored_id > 0 ) {
		$user = get_userdata( $stored_id );

		if ( $user instanceof WP_User && user_can( $user, 'edit_pages' ) ) {
			return $user;
		}
	}

	$users = get_users(
		array(
			'role'    => 'administrator',
			'number'  => 1,
			'orderby' => 'ID',
			'order'   => 'ASC',
		)
	);

	if ( ! isset( $users[0] ) || ! $users[0] instanceof WP_User ) {
		return null;
	}

	update_option( 'liftedpath_preview_user_id', (int) $users[0]->ID, false );

	return $users[0];
}

/**
 * Plain application password for Vercel, created once for the preview user.
 *
 * WordPress only reveals an application password when it is created. The plain
 * value is kept so this screen can show it again. It is not loaded on the public site.
 *
 * @param WP_User $user Preview user.
 */
function liftedpath_vercel_env_app_password( WP_User $user ): string {
	$stored = get_option( 'liftedpath_preview_app_password', '' );
	$uuid   = get_option( 'liftedpath_preview_app_uuid', '' );
	$stored = is_string( $stored ) ? $stored : '';
	$uuid   = is_string( $uuid ) ? $uuid : '';

	if ( '' !== $stored && '' !== $uuid && class_exists( 'WP_Application_Passwords' ) ) {
		$item = WP_Application_Passwords::get_user_application_password( $user->ID, $uuid );

		if ( is_array( $item ) ) {
			return $stored;
		}
	}

	if ( ! class_exists( 'WP_Application_Passwords' ) || ! wp_is_application_passwords_available() || ! wp_is_application_passwords_available_for_user( $user ) ) {
		return '';
	}

	$created = WP_Application_Passwords::create_new_application_password(
		$user->ID,
		array(
			'name' => 'Vercel preview',
		)
	);

	if ( is_wp_error( $created ) || ! isset( $created[0], $created[1]['uuid'] ) || ! is_string( $created[0] ) ) {
		return '';
	}

	$password = preg_replace( '/\s+/', '', $created[0] );
	$password = is_string( $password ) ? $password : '';

	update_option( 'liftedpath_preview_app_password', $password, false );
	update_option( 'liftedpath_preview_app_uuid', (string) $created[1]['uuid'], false );
	update_option( 'liftedpath_preview_user_id', (int) $user->ID, false );

	return $password;
}
