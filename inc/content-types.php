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
				'name'               => __( 'Submissions', 'liftedpath' ),
				'singular_name'      => __( 'Submission', 'liftedpath' ),
				'add_new'            => __( 'Add New', 'liftedpath' ),
				'add_new_item'       => __( 'Add New Submission', 'liftedpath' ),
				'edit_item'          => __( 'Edit Submission', 'liftedpath' ),
				'new_item'           => __( 'New Submission', 'liftedpath' ),
				'view_item'          => __( 'View Submission', 'liftedpath' ),
				'search_items'       => __( 'Search Submissions', 'liftedpath' ),
				'not_found'          => __( 'No submissions found.', 'liftedpath' ),
				'not_found_in_trash' => __( 'No submissions found in Trash.', 'liftedpath' ),
				'all_items'          => __( 'Submissions', 'liftedpath' ),
				'menu_name'          => __( 'Submissions', 'liftedpath' ),
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
		$next[ $key ] = $label;

		if ( 'title' === $key ) {
			$next['submission_type']  = __( 'Type', 'liftedpath' );
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
