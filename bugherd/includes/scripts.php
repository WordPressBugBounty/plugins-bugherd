<?php
/**
 * Frontend and Admin Scripts.
 *
 * @package BugHerd
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Get the tracking script.
 *
 * @param string $project_key BugHerd project Key.
 * @return string
 */
function bugherd_get_the_script( $project_key ) {
	return sprintf(
		'<script type="text/javascript" src="https://www.bugherd.com/sidebarv2.js?apikey=%s" async="true"></script>',
		esc_html( $project_key )
	);
}

/**
 * Check if BugHerd should be disabled based on query parameters.
 *
 * Users can add `?disable_bugherd` (or any defined query params) to prevent BugHerd from loading.
 *
 * @return bool
 */
function is_bugherd_disabled_by_query() {
	$query_params = get_option( 'bugherd_disable_query_params', 'disable_bugherd, bricks, elementor, no_bugherd' );
	$disabled_params = array_map('trim', explode(',', $query_params)); // Convert to an array

	foreach ( $disabled_params as $param ) {
		if ( isset( $_GET[ $param ] ) ) {
			return true;
		}
	}
	return false;
}

/**
 * Add BugHerd integration code for the frontend.
 */
add_action( 'wp_head', 'bugherd_do_the_frontend_script' );
function bugherd_do_the_frontend_script() {
	$project_key = get_option( 'bugherd_project_key', '' );

	// Prevent BugHerd from loading if any specified query parameter is present
	if ( is_bugherd_disabled_by_query() ) {
		return;
	}

	if ( empty( $project_key ) ) {
		return;
	}

	echo bugherd_get_the_script( $project_key ); 
}

/**
 * Add BugHerd integration code for wp-admin.
 */
add_action( 'admin_head', 'bugherd_do_the_admin_script' );
function bugherd_do_the_admin_script() {
	$enable_admin = filter_var( get_option( 'bugherd_enable_admin', false ), FILTER_VALIDATE_BOOLEAN );

	// If admin mode is disabled in settings, don't load BugHerd
	if ( ! $enable_admin ) {
		return;
	}

	// Prevent BugHerd from loading if any specified query parameter is present
	if ( is_bugherd_disabled_by_query() ) {
		return;
	}

	$project_key = get_option( 'bugherd_project_key', '' );

	if ( empty( $project_key ) ) {
		return;
	}

	echo bugherd_get_the_script( $project_key ); 
}
