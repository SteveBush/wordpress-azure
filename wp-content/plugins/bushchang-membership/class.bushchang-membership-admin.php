<?php
/**
 * @package BushChang-Membership
 */

/**
 * class short summary.
 *
 * class description.
 *
 * @version 1.0
 * @author stevebu
 */
class BushChang_Membership_Admin
{
	private static $initiated = false;

	public static function init() {
		if ( ! self::$initiated ) {
			self::init_hooks();
		}
	}

	public static function init_hooks() {
		self::$initiated = true;

		add_action( 'admin_init', array( 'BushChang_Membership_Admin', 'admin_init' ) );
		add_action( 'admin_menu', array( 'BushChang_Membership_Admin', 'admin_menu' ), 5 );
		add_action( 'admin_notices', array( 'BushChang_Membership_Admin', 'display_notice' ) );
	}

	public static function admin_init() {
		load_plugin_textdomain( 'bushchang-membership' );
	}

	public static function admin_menu() {
		self::load_menu();
	}

	public static function admin_head() {
		if ( !current_user_can( 'manage_options' ) )
			return;
	}

	public static function admin_plugin_settings_link( $links ) {
		$settings_link = '<a href="'.esc_url( self::get_page_url() ).'">'.__('Settings', 'bushchang-membership').'</a>';
		array_unshift( $links, $settings_link );
		return $links;
	}

	public static function load_menu() {
		$hook = add_options_page( __('BushChang Membership', 'bushchang-membership'), __('BushChang Membership', 'bushchang-membership'), 'manage_options', 'bushchang-membership-config', array( 'BushChang_Membership_Admin', 'display_page' ) );

		if ( version_compare( $GLOBALS['wp_version'], '3.3', '>=' ) ) {
			add_action( "load-$hook", array( 'BushChang_Membership_Admin', 'admin_help' ) );
		}
	}

    	/**
	 * Add help to the BushChang Membership page
	 *
     * @return false if not the BushChang Membership page
	 */
	public static function admin_help() {

    }

    public static function display_page() {
    ?>
    <h2>BushChang Membership configuration options</h2>
<?php
	}

    public static function display_notice() {


    }


}