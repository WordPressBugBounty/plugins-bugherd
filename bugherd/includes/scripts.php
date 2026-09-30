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
 * @param string $script_url  Script URL without query string.
 * @return string
 */
function bugherd_get_the_script( $project_key, $script_url = 'https://www.bugherd.com/sidebarv2.js' ) {
	return sprintf(
		'<script type="text/javascript" src="%s?utm_source=wordpress&apikey=%s" async="true"></script>',
		esc_url( $script_url ),
		esc_html( $project_key )
	);
}

/**
 * Whether the admin sidebar should be printed on this request.
 *
 * @return bool
 */
function bugherd_admin_script_is_enabled() {
	$enable_admin = filter_var( get_option( 'bugherd_enable_admin', false ), FILTER_VALIDATE_BOOLEAN );

	if ( ! $enable_admin || is_bugherd_disabled_by_query() ) {
		return false;
	}

	$project_key = get_option( 'bugherd_project_key', '' );

	return ! empty( $project_key );
}

/**
 * Keep the admin sidebar on the normal script load.
 *
 * WordPress 7.1 isolates block-editor screens and adds crossorigin="anonymous"
 * to external scripts. That asks sidebarv2.js for CORS headers. Other BugHerd
 * installs never send that request. Skipping client-side media processing on
 * these screens leaves the script load unchanged. Image uploads still run on
 * the server.
 *
 * @param bool $enabled Whether WordPress should isolate the editor document.
 * @return bool
 */
function bugherd_keep_sidebar_on_standard_script_load( $enabled ) {
	if ( ! is_admin() || ! bugherd_admin_script_is_enabled() ) {
		return $enabled;
	}

	if ( ! bugherd_is_document_isolated_block_editor_screen() ) {
		return $enabled;
	}

	return false;
}
add_filter( 'wp_client_side_media_processing_enabled', 'bugherd_keep_sidebar_on_standard_script_load' );

/**
 * Whether the current admin screen loads scripts under WP 7.1+ Document-Isolation-Policy (CORS).
 *
 * @return bool
 */
function bugherd_is_document_isolated_block_editor_screen() {
	if ( ! function_exists( 'get_current_screen' ) ) {
		return false;
	}

	$screen = get_current_screen();
	if ( ! $screen ) {
		return false;
	}

	if ( $screen->is_block_editor() ) {
		return true;
	}

	if ( 'site-editor' === $screen->id ) {
		return true;
	}

	if ( 'widgets' === $screen->id && function_exists( 'wp_use_widgets_block_editor' ) && wp_use_widgets_block_editor() ) {
		return true;
	}

	return false;
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
	if ( ! bugherd_admin_script_is_enabled() ) {
		return;
	}

	$project_key = get_option( 'bugherd_project_key', '' );

	echo bugherd_get_the_script( $project_key );
}
