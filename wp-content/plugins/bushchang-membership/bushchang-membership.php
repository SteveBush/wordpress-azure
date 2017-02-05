<?php
/**
 * @package BushChang_Membership
 */
/*
Plugin Name: BushChang Membership
Plugin URI: https://wordpress.org/plugins/bushchang-membership
Description: BushChang Membership is a simple plugin for displaying either a social login prompt or membership profile.
Version: 1.0.0
Author: Steve Bush
Author URI: https://profiles.wordpress.org/stevebush
License: GPLv2 or later
Text Domain: bushchang-membership
Domain Path: /languages
*/

/*
Copyright © 2013-2017 Steve Bush

This program is free software; you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation; either version 2 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
this program; if not, write to the Free Software Foundation, Inc., 51 Franklin
Street, Fifth Floor, Boston, MA 02110-1301, USA.
*/

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

define( 'BUSHCHANG_MEMBERSHIP_VERSION', '1.0.0' );
define( 'BUSHCHANG_MEMBERSHIP__MINIMUM_WP_VERSION', '3.5' );
define( 'BUSHCHANG_MEMBERSHIP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'BUSHCHANG_MEMBERSHIP_DEFAULT_SIZE', 0 );


register_activation_hook( __FILE__, array( 'BushChang-Membership', 'plugin_activation' ) );
register_deactivation_hook( __FILE__, array( 'BushChang-Membership', 'plugin_deactivation' ) );

/**
 * Sets up plugin defaults and makes BushChang Membership available for translation.
 *
 * @uses load_theme_textdomain() For translation/localization support.
 * @uses plugin_basename() For retrieving the basename of the plugin.
 *
 * @since BushChang Membership 1.0.0
 */
function bushchang_membership_init() {
	// Makes BushChang Membership available for translation.
	load_plugin_textdomain( 'bushchang-membership', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

add_action( 'init', 'bushchang_membership_init' );

/**
 * Registers sanitization callback and plugin setting fields.
 *
 * @uses register_setting() For registering a setting and its sanitization
 * callback.
 * @uses add_settings_field() For registering a settings field to a settings
 * page and section.
 * @uses __() For retrieving the translated string from the translate().
 *
 * @since BushChang Membership 1.0.0
 */
function bushchang_membership_admin_init() {
	// Registers plugin setting and its sanitization callback.
	register_setting( 'discussion', 'bushchang_membership', 'bushchang_membership_sanitize_options' );

	// Registers Default Size settings field under the Settings Discussion
	// Screen.
	add_settings_field( 'bushchang-membership-default-size', __( 'Default Size', 'bushchang-membership' ), 'bushchang_membership_default_size_settings_field', 'discussion', 'avatars' );
}

add_action( 'admin_init', 'bushchang_membership_admin_init' );

/**
 * Returns plugin default options.
 *
 * @since BushChang Membership 1.0.0
 *
 * @return array Plugin default options.
 */
function bushchang_membership_get_default_options() {
	$options = array(
		'default_size'   => BUSHCHANG_MEMBERSHIP_DEFAULT_SIZE
	);

	return $options;
}

/**
 * Returns plugin options.
 *
 * @uses get_option() For getting values for a named option.
 * @uses bushchang_membership_get_default_options() For retrieving plugin default
 * options.
 *
 * @since BushChang Membership 1.0.0
 *
 * @return array Plugin options.
 */
function bushchang_membership_get_options() {
	return get_option( 'bushchang_membership', bushchang_membership_get_default_options() );
}

/**
 * Sanitizes and validates plugin options.
 *
 * @uses bushchang_membership_get_default_options() For retrieving plugin default
 * options.
 * @uses absint() For converting a value to a non-negative integer.
 *
 * @since BushChang Membership 1.0.0
 *
 * @param array $input An associative array with user input.
 * @return array Sanitized plugin options.
 */
function bushchang_membership_sanitize_options( $input ) {
	$options = bushchang_membership_get_default_options();

	if ( isset( $input['default_size'] ) && is_numeric( trim( $input['default_size'] ) ) ) {
		$options['default_size'] = absint( trim( $input['default_size'] ) );

		if ( $options['default_size'] < 1 )
			$options['default_size'] = 1;
		elseif ( $options['default_size'] > 512 )
			$options['default_size'] = 512;
	}

	return $options;
}

?>
