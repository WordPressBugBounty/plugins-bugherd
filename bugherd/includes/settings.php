<?php
/**
 * Settings Page.
 *
 * @package BugHerd
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

add_action( 'admin_menu', 'bugherd_register_options_page' );
function bugherd_register_options_page() {
	add_options_page(
		esc_html__( 'BugHerd', 'bugherd' ),
		esc_html__( 'BugHerd', 'bugherd' ),
		'manage_options',
		'bugherd',
		'bugherd_options_page'
	);
}

add_action( 'admin_init', 'bugherd_register_settings' );
function bugherd_register_settings() {
	add_settings_section( 'bugherd_settings', '', '__return_false', 'bugherd_settings' );

	// Project Key
	register_setting( 'bugherd_settings', 'bugherd_project_key', [
		'type' => 'string',
		'description' => esc_html__( 'BugHerd Project Key', 'bugherd' ),
		'sanitize_callback' => 'sanitize_text_field',
		'show_in_rest' => true,
		'default' => '',
	]);

	// Enable BugHerd in WP Admin
	register_setting( 'bugherd_settings', 'bugherd_enable_admin', [
		'type' => 'boolean',
		'description' => esc_html__( 'Enable BugHerd in wp-admin?', 'bugherd' ),
		'sanitize_callback' => 'sanitize_text_field',
		'show_in_rest' => true,
		'default' => false,
	]);

	// Query Params for Disabling BugHerd
	register_setting( 'bugherd_settings', 'bugherd_disable_query_params', [
		'type' => 'string',
		'description' => esc_html__( 'Comma-separated list of query parameters to disable BugHerd.', 'bugherd' ),
		'sanitize_callback' => 'sanitize_text_field',
		'show_in_rest' => true,
		'default' => false,
	]);

	// Add Fields
	add_settings_field( 'bugherd_project_key_field', __( 'Project Key', 'bugherd' ), 'bugherd_project_key_settings_field', 'bugherd_settings', 'bugherd_settings' );

	add_settings_field( 'bugherd_enable_admin_field', __( 'Enable BugHerd in wp-admin?', 'bugherd' ), 'bugherd_enable_admin_settings_field', 'bugherd_settings', 'bugherd_settings' );

	add_settings_field( 'bugherd_disable_query_params_field', __( 'Query Parameters to Disable BugHerd', 'bugherd' ), 'bugherd_disable_query_params_settings_field', 'bugherd_settings', 'bugherd_settings' );
}

function bugherd_project_key_settings_field() {
	printf(
		'<input type="text" id="bugherd-project-key" name="bugherd_project_key" value="%s" class="regular-text ltr" /><p class="description">%s</p>',
		esc_attr( get_option( 'bugherd_project_key' ) ),
		esc_html__( 'Leave blank to disable plugin.', 'bugherd' )
	);
}

function bugherd_enable_admin_settings_field() {
	printf(
		'<input type="checkbox" id="bugherd-enable-admin" name="bugherd_enable_admin" value="1" %s /><label for="bugherd-enable-admin">%s</label>',
		checked( get_option( 'bugherd_enable_admin' ), '1', false ),
		esc_html__( 'Show BugHerd on WP Admin pages?', 'bugherd' )
	);
}

function bugherd_disable_query_params_settings_field() {
	printf(
		'<input type="text" id="bugherd-disable-query-params" name="bugherd_disable_query_params" value="%s" class="regular-text" /> 
		<p class="description">%s</p>',
		esc_attr( get_option( 'bugherd_disable_query_params', 'disable_bugherd, bricks, elementor, no_bugherd' ) ),
		esc_html__( 'Enter query parameters (comma-separated) to disable BugHerd. Example: bricks, elementor' )
	);
}

function bugherd_options_page() {
	echo '<div class="wrap bugherd-settings-container">';
	
	// Logo
	printf(
		'<img src="%s%s" class="bugherd-logo">',
		esc_url( plugin_dir_url( __DIR__ ) ),
		'assets/images/logo-web.png'
	);

	// Tagline
	printf(
		'<h2 class="bugherd-tagline">%s<br>%s</h2>',
		esc_html__( 'The Visual Feedback', 'bugherd' ),
		esc_html__( 'Tool for Websites', 'bugherd' )
	);

	echo '<form method="post" action="options.php" class="card bugherd-container">';

	// Intro
	printf(
		'<p>%s <a href="%s" target="_blank" rel="noopener">%s</a></p>',
		esc_html__( 'To install BugHerd on this site simply add your BugHerd Project Key to the field below. Not sure where to find your Project Key?', 'bugherd' ),
		esc_url( 'https://support.bugherd.com/hc/en-us/articles/360002121575' ),
		esc_html__( 'Check out this help article.', 'bugherd' )
	);

	settings_fields( 'bugherd_settings' );
	do_settings_sections( 'bugherd_settings' );

	// Submit button
	printf(
		'<div class="bugherd-submit">%1$s<p>%2$s <a href="%3$s" target="_blank" rel="noopener">%3$s</a></p></div>',
		get_submit_button(),
		esc_html__( 'Need help? Try', 'bugherd' ),
		esc_url( 'https://support.bugherd.com' )
	);

	echo '</form>';
	echo '</div>';
}
