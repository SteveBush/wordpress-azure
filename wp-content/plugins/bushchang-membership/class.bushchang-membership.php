<?php

/**
 * class short summary.
 *
 * class description.
 *
 * @version 1.0
 * @author stevebu
 */
class BushChang_Membership
{
	private static $initiated = false;

	public static function init() {
		if ( ! self::$initiated ) {
			self::init_hooks();
		}
	}

	/**
	 * Initializes WordPress hooks
	 */
	private static function init_hooks() {
		self::$initiated = true;

	}

	/**
	 * Attached to activate_{ plugin_basename( __FILES__ ) } by register_activation_hook()
	 * @static
	 */
	public static function plugin_activation() {

	}

	/**
	 * Removes plugin
	 * @static
	 */
	public static function plugin_deactivation( ) {
		return 'deactivated';
	}

}