<?php
/**
 * Private submission records.
 *
 * Contact and donate intents are stored here in a later unit. This unit
 * registers the type and keeps it off the public site.
 *
 * @package LiftedPath
 */

/**
 * Register the submission post type.
 */
function liftedpath_register_submission_type() {
	register_post_type(
		'submission',
		array(
			'labels'              => array(
				'name'               => __( 'LP Contact Form', 'liftedpath' ),
				'singular_name'      => __( 'Contact message', 'liftedpath' ),
				'add_new'            => __( 'Add New', 'liftedpath' ),
				'add_new_item'       => __( 'Contact Message', 'liftedpath' ),
				'edit_item'          => __( 'View Contact Message', 'liftedpath' ),
				'new_item'           => __( 'Contact Message', 'liftedpath' ),
				'view_item'          => __( 'View Contact Message', 'liftedpath' ),
				'search_items'       => __( 'Search Contact Messages', 'liftedpath' ),
				'not_found'          => __( 'No contact messages found.', 'liftedpath' ),
				'not_found_in_trash' => __( 'No contact messages found in Trash.', 'liftedpath' ),
				'all_items'          => __( 'Messages', 'liftedpath' ),
				'menu_name'          => __( 'LP Contact Form', 'liftedpath' ),
			),
			'public'              => false,
			'publicly_queryable'  => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => false,
			'show_in_admin_bar'   => false,
			'show_in_rest'        => true,
			'rest_base'           => 'submissions',
			'menu_icon'           => 'dashicons-email-alt',
			'capability_type'     => 'post',
			'capabilities'        => array(
				'create_posts' => 'do_not_allow',
			),
			'map_meta_cap'        => true,
			'hierarchical'        => false,
			'supports'            => array( 'title' ),
			'has_archive'         => false,
			'rewrite'             => false,
			'query_var'           => false,
			'exclude_from_search' => true,
			'delete_with_user'    => false,
		)
	);
}
add_action( 'init', 'liftedpath_register_submission_type' );

/**
 * LP Contact Form list shows contact messages only. Donations live under LP Donations.
 *
 * @param WP_Query $query Admin query.
 */
function liftedpath_contact_list_only( $query ) {
	if ( ! is_admin() || ! $query->is_main_query() ) {
		return;
	}

	$post_type = $query->get( 'post_type' );
	if ( 'submission' !== $post_type ) {
		return;
	}

	// Only the CPT list table, not arbitrary admin queries.
	$pagenow = $GLOBALS['pagenow'] ?? '';
	if ( 'edit.php' !== $pagenow ) {
		return;
	}

	if ( isset( $_GET['lp_show_all'] ) && '1' === $_GET['lp_show_all'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return;
	}

	$meta_query   = (array) $query->get( 'meta_query' );
	$meta_query[] = array(
		'key'   => 'submission_type',
		'value' => 'contact',
	);
	$query->set( 'meta_query', $meta_query );
}
add_action( 'pre_get_posts', 'liftedpath_contact_list_only' );

/**
 * Rename the ACF options top-level menu to LP Config and use a settings icon.
 * Remove the unused “Add New” contact submenu (records arrive from the public form only).
 */
function liftedpath_rename_config_menu_label() {
	global $menu, $submenu;

	if ( is_array( $menu ) ) {
		foreach ( $menu as $index => $item ) {
			if ( isset( $item[2] ) && 'liftedpath-settings' === $item[2] ) {
				$menu[ $index ][0] = 'LP Config';
				$menu[ $index ][6] = 'dashicons-admin-generic';
			}
		}
	}

	if ( isset( $submenu['liftedpath-settings'] ) && is_array( $submenu['liftedpath-settings'] ) ) {
		foreach ( $submenu['liftedpath-settings'] as $index => $item ) {
			if ( isset( $item[2] ) && 'liftedpath-settings' === $item[2] ) {
				$submenu['liftedpath-settings'][ $index ][0] = 'LP Config';
			}
		}
	}

	if ( isset( $submenu['edit.php?post_type=submission'] ) && is_array( $submenu['edit.php?post_type=submission'] ) ) {
		foreach ( $submenu['edit.php?post_type=submission'] as $index => $item ) {
			if ( isset( $item[2] ) && 'post-new.php?post_type=submission' === $item[2] ) {
				unset( $submenu['edit.php?post_type=submission'][ $index ] );
			}
		}
	}
}
add_action( 'admin_menu', 'liftedpath_rename_config_menu_label', 1000 );

/**
 * Block the classic Add New screen for contact records.
 */
function liftedpath_block_manual_contact_create() {
	global $pagenow;
	if ( 'post-new.php' !== $pagenow ) {
		return;
	}
	$type = isset( $_GET['post_type'] ) ? sanitize_key( wp_unslash( $_GET['post_type'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( 'submission' !== $type ) {
		return;
	}
	wp_safe_redirect( admin_url( 'edit.php?post_type=submission' ) );
	exit;
}
add_action( 'admin_init', 'liftedpath_block_manual_contact_create' );

/**
 * Keep ACF options page titles on LP Config after JSON sync.
 *
 * @param array<string, mixed> $page Options page.
 * @return array<string, mixed>
 */
function liftedpath_rename_config_options_page( $page ) {
	if ( ! is_array( $page ) ) {
		return $page;
	}

	$slug = (string) ( $page['menu_slug'] ?? '' );
	if ( 'liftedpath-settings' !== $slug ) {
		return $page;
	}

	$page['menu_title']      = 'LP Config';
	$page['page_title']      = 'LP Config';
	$page['update_button']   = 'Save LP Config';
	$page['updated_message'] = 'LP Config saved successfully.';

	return $page;
}
add_filter( 'acf/validate_options_page', 'liftedpath_rename_config_options_page' );

/**
 * Keep submissions private once they leave auto-draft.
 *
 * @param array $data    Post data being inserted.
 * @param array $postarr Raw post array.
 * @return array
 */
function liftedpath_force_submission_private( $data, $postarr ) {
	if ( 'submission' !== $data['post_type'] ) {
		return $data;
	}

	if ( in_array( $data['post_status'], array( 'auto-draft', 'trash' ), true ) ) {
		return $data;
	}

	$data['post_status'] = 'private';

	return $data;
}
add_filter( 'wp_insert_post_data', 'liftedpath_force_submission_private', 10, 2 );

/**
 * The submissions REST route is for logged-in editors only.
 *
 * @param mixed           $result  Response to short-circuit.
 * @param WP_REST_Server  $server  Server instance.
 * @param WP_REST_Request $request Request used to generate the response.
 * @return mixed
 */
function liftedpath_restrict_submission_rest( $result, $server, $request ) {
	unset( $server );

	$route = $request->get_route();

	if ( 0 !== strpos( $route, '/wp/v2/submissions' ) ) {
		return $result;
	}

	if ( is_user_logged_in() ) {
		return $result;
	}

	return new WP_Error(
		'rest_forbidden',
		__( 'Submissions are private.', 'liftedpath' ),
		array( 'status' => 401 )
	);
}
add_filter( 'rest_pre_dispatch', 'liftedpath_restrict_submission_rest', 10, 3 );

/**
 * Add the type and email to the submissions list.
 *
 * @param string[] $columns List columns.
 * @return string[]
 */
function liftedpath_submission_columns( $columns ) {
	$next = array();

	foreach ( $columns as $key => $label ) {
		// Contact inbox is contact-only — drop Type. Payment is removed by the forms plugin.
		if ( 'submission_type' === $key ) {
			continue;
		}

		$next[ $key ] = $label;

		if ( 'title' === $key ) {
			$next['submission_email'] = __( 'Email', 'liftedpath' );
		}
	}

	return $next;
}
add_filter( 'manage_submission_posts_columns', 'liftedpath_submission_columns' );

/**
 * Print a submission list column.
 *
 * @param string $column  Column key.
 * @param int    $post_id Post ID.
 */
function liftedpath_submission_column( $column, $post_id ) {
	if ( ! function_exists( 'get_field' ) ) {
		return;
	}

	if ( 'submission_type' === $column ) {
		echo esc_html( (string) get_field( 'submission_type', $post_id ) );
	}

	if ( 'submission_email' === $column ) {
		echo esc_html( (string) get_field( 'email', $post_id ) );
	}
}
add_action( 'manage_submission_posts_custom_column', 'liftedpath_submission_column', 10, 2 );
