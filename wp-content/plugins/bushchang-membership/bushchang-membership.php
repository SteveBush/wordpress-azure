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
define( 'BUSHCHANG_MEMBERSHIP__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

// Add Default values


// Register activation and deactivation hooks for plugin
register_activation_hook( __FILE__, array( 'BushChang-Membership', 'plugin_activation' ) );
register_deactivation_hook( __FILE__, array( 'BushChang-Membership', 'plugin_deactivation' ) );

require_once( BUSHCHANG_MEMBERSHIP__PLUGIN_DIR . 'class.bushchang-membership.php' );
require_once( BUSHCHANG_MEMBERSHIP__PLUGIN_DIR . 'class.bushchang-membership-widget.php' );

// Register action for initializing our plug-in
add_action( 'init', array( 'BushChang_Membership', 'init' ) );


if ( is_admin() ) {
	require_once( BUSHCHANG_MEMBERSHIP__PLUGIN_DIR . 'class.bushchang-membership-admin.php' );
	add_action( 'init', array( 'BushChang_Membership_Admin', 'init' ) );
}


?>
