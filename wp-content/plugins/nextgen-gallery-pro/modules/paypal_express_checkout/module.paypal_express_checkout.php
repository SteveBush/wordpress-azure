<?php
/**
{
	Module: photocrati-paypal_express_checkout
}
**/
class M_PayPal_Express_Checkout extends C_Base_Module
{
	function define()
	{
		parent::define(
			'photocrati-paypal_express_checkout',
			'PayPal Express Checkout',
			'Provides integration with PayPal Express Checkout',
			'0.11',
			'http://www.nextgen-gallery.com',
			'Photocrati Media',
			'http://www.photocrati.com'
		);

        C_Photocrati_Installer::add_handler($this->module_id, 'C_Paypal_Express_Checkout_Installer');
	}

	function _register_adapters()
	{
        if (!is_admin())
        {
            $this->get_registry()->add_adapter('I_NextGen_Pro_Checkout', 'A_PayPal_Express_Checkout_Button');
            $this->get_registry()->add_adapter('I_Ajax_Controller',      'A_PayPal_Express_Checkout_Ajax');
        }

	}

	function _register_hooks()
	{
		add_action('init', array(&$this, 'process_paypal_responses'));
	}

	function process_paypal_responses()
	{
        // Process return from PayPal
		if (isset($_REQUEST['ngg_ppxc_rtn']))
        {
            $checkout = C_NextGen_Pro_Checkout::get_instance();
			try {
				$order = $checkout->finish_paypal_express_order();
				if ($order->status == 'fraud') {
					wp_die(__('This order has been marked as fraud and has been reported.', 'nextgen-gallery-pro'));
				}
				if ($order->status != 'verified') {
					wp_die(__("We're sorry, but something went wrong processing your order. Please try again.", 'nextgen-gallery-pro'));
				}
				else {
					$checkout->redirect_to_thank_you_page($order);
					throw new E_Clean_Exit();
				}

			}
			catch (E_NggProPaymentExpressError $ex) {
				wp_die($ex->getMessage());
			}
		}
        // Process cancelled PayPal order
		elseif (isset($_REQUEST['ngg_ppxc_ccl'])) {
            $checkout = C_NextGen_Pro_Checkout::get_instance();
            $checkout->redirect_to_cancel_page();
		}
	}

	function get_type_list()
	{
        return array(
            'A_PayPal_Express_Checkout_Button' => 'adapter.paypal_express_checkout_button.php',
            'A_PayPal_Express_Checkout_Ajax'   => 'adapter.paypal_express_checkout_ajax.php'
        );
	}
}

class C_Paypal_Express_Checkout_Installer
{
    function install()
    {
        $settings = C_NextGen_Settings::get_instance();
        $settings->set_default_value('ecommerce_paypal_enable', '0');
        $settings->set_default_value('ecommerce_paypal_sandbox', 1);
        $settings->set_default_value('ecommerce_paypal_email', '');
        $settings->set_default_value('ecommerce_paypal_username', '');
        $settings->set_default_value('ecommerce_paypal_password', '');
        $settings->set_default_value('ecommerce_paypal_signature', '');
    }
}

new M_PayPal_Express_Checkout;
