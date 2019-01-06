<?php
/**
{
	Module: photocrati-stripe
}
**/
class M_Photocrati_Stripe extends C_Base_Module
{
    function define($id = 'pope-module',
                    $name = 'Pope Module',
                    $description = '',
                    $version = '',
                    $uri = '',
                    $author = '',
                    $author_uri = '',
                    $context = FALSE)
	{
		parent::define(
			'photocrati-stripe',
			'Stripe',
			'Provides integration with Stripe payment gateway',
			'2.6.8',
            'https://www.imagely.com/wordpress-gallery-plugin/nextgen-pro/',
            'Imagely',
            'https://www.imagely.com'
		);

        C_Photocrati_Installer::add_handler($this->module_id, 'C_Stripe_Installer');
	}

	function _register_adapters()
	{
        if (!is_admin())
        {
            $this->get_registry()->add_adapter('I_NextGen_Pro_Checkout', 'A_Stripe_Checkout_Button');
            $this->get_registry()->add_adapter('I_Ajax_Controller',      'A_Stripe_Checkout_Ajax');
        }
        else {
            $this->get_registry()->add_adapter('I_Form', 'A_Stripe_Checkout_Form', NGG_PRO_PAYMENT_PAYMENT_FORM);
        }
	}

    function _register_hooks()
    {
        add_action('init', array(&$this, 'route'));
        add_filter('ngg_pro_settings_reset_installers', array($this, 'return_own_installer'));

        // Possibly warn users that TLS 1.2 is necessary for Stripe API calls
        $notices = C_Admin_Notification_Manager::get_instance();
        $notices->add(
            'stripe_tls12_check',
            new C_Stripe_TLS12_Check_Notification()
        );
    }

    function initialize()
    {
        parent::initialize();

        if (class_exists('C_Admin_Requirements_Manager'))
        {
            C_Admin_Requirements_Manager::get_instance()->add(
                'stripe_curl_requirement',
                'phpext',
                array($this, 'check_curl_requirement'),
                array('message' => __('cURL is required for Stripe support to function', 'nggallery'))
            );
            C_Admin_Requirements_Manager::get_instance()->add(
                'stripe_json_requirement',
                'phpext',
                array($this, 'check_json_requirement'),
                array('message' => __('JSON is required for Stripe support to function', 'nggallery'))
            );
            C_Admin_Requirements_Manager::get_instance()->add(
                'stripe_multibyte_requirement',
                'phpext',
                array($this, 'check_multibyte_requirement'),
                array('message' => __('Multibyte is required for Stripe support to function', 'nggallery'))
            );
        }
    }

    public function check_curl_requirement()
    {
        return function_exists('curl_init');
    }

    public function check_json_requirement()
    {
        return function_exists('json_decode');
    }

    public function check_multibyte_requirement()
    {
        return function_exists('mb_detect_encoding');
    }

    function route()
    {
        if (isset($_REQUEST['ngg_stripe_rtn']) && isset($_REQUEST['order']))
        {
            $checkout = C_NextGen_Pro_Checkout::get_instance();
            $checkout->redirect_to_thank_you_page($_REQUEST['order']);
        }
    }

    public function return_own_installer($installers)
    {
        $installers[] = 'C_Stripe_Installer';
        return $installers;
    }

	function get_type_list()
	{
        return array(
            'A_Stripe_Checkout_Ajax'			=> 'adapter.stripe_checkout_ajax.php',
            'A_Stripe_Checkout_Button'			=> 'adapter.stripe_checkout_button.php',
            'A_Stripe_Checkout_Form'   => 'adapter.stripe_checkout_form.php',
            'C_Stripe_TLS12_Check_Notification' => 'class.stripe_tls12_check_notification.php'
        );
	}
}

class C_Stripe_Installer extends AC_NextGen_Pro_Settings_Installer
{
    function __construct()
    {
        $this->set_defaults(array(
            'ecommerce_stripe_enable'      => '0',
            'ecommerce_stripe_key_public'  => '',
            'ecommerce_stripe_key_private' => '',
            'ecommerce_stripe_tls12_checked' => FALSE,
            'ecommerce_stripe_tls12_missing' => FALSE
        ));

        $this->set_groups(array('ecommerce'));
    }
}

new M_Photocrati_Stripe;