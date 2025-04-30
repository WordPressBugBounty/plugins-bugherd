<?php
/**
 * Plugin Name:     BugHerd
 * Plugin URI:      https://bugherd.com
 * Description:     BugHerd is the visual feedback tool for websites. For help, go to <a href="http://support.bugherd.com">support.bugherd.com</a>
 * Author:          BugHerd
 * Author URI:      https://bugherd.com
 * Text Domain:     bugherd
 * Version:         1.0.14
 * License:         GPLv3 or later
 *
 * @package         BugHerd
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Bootstrap
 *
 * @since 1.0.0
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/settings.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/scripts.php';

/**
 * Add a "Settings" link in the Plugins section.
 */
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'bugherd_add_settings_link' );
function bugherd_add_settings_link( $links ) {
	$settings_link = '<a href="' . esc_url(admin_url('options-general.php?page=bugherd')) . '">' . esc_html__('Settings', 'bugherd') . '</a>';
	array_unshift( $links, $settings_link ); // Adds it at the beginning of the links
	return $links;
}
