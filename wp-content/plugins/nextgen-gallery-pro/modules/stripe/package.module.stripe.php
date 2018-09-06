<?php
class A_Stripe_Checkout_Ajax extends Mixin
{
    function stripe_checkout_action()
    {
        $checkout = C_NextGen_Pro_Checkout::get_instance();
        return $checkout->create_stripe_charge();
    }
}
class A_Stripe_Checkout_Button extends Mixin
{
    function get_checkout_buttons()
    {
        $buttons = parent::call_parent('get_checkout_buttons');
        if ($this->is_stripe_enabled()) {
            $buttons[] = 'stripe_checkout';
        }
        return $buttons;
    }
    function is_stripe_enabled()
    {
        return C_NextGen_Settings::get_instance()->ecommerce_stripe_enable;
    }
    function get_i18n_strings()
    {
        $i18n = new stdClass();
        $i18n->pay_with_card = __('Pay with Card', 'nextgen-gallery-pro');
        return $i18n;
    }
    function get_stripe_vars($include_private_key = FALSE)
    {
        $settings = C_NextGen_Settings::get_instance();
        $retval = array('site_name' => get_bloginfo('name'), 'key' => $settings->ecommerce_stripe_key_public, 'currency' => C_NextGen_Pro_Currencies::$currencies[$settings->ecommerce_currency]['code'], 'shippingAddress' => TRUE);
        if ($include_private_key) {
            $retval['private_key'] = $settings->ecommerce_stripe_key_private;
        }
        return $retval;
    }
    function enqueue_stripe_checkout_resources()
    {
        wp_enqueue_script('stripe-checkout', 'https://checkout.stripe.com/checkout.js');
        wp_enqueue_style('stripe-checkout', 'https://checkout.stripe.com/v3/checkout/button.css');
    }
    function _render_stripe_checkout_button()
    {
        return $this->render_partial('photocrati-stripe#button', array('i18n' => $this->get_i18n_strings(), 'stripe_vars' => json_encode($this->object->get_stripe_vars())), TRUE);
    }
    function create_stripe_charge()
    {
        $retval = array();
        $total = 0.0;
        // Include the SDK if another plugin hasn't already done so
        if (!class_exists('Stripe')) {
            include_once 'stripe-sdk/lib/Stripe.php';
        }
        // Get Stripe input params
        if (($stripe = $this->param('stripe')) && isset($stripe['token']) && $this->param('items')) {
            $stripe = array_merge($stripe, $this->get_stripe_vars(TRUE));
            // Set Stripe API key
            Stripe::setApiKey($stripe['private_key']);
            // Ensure we have sufficient data returned from Stripe Checkout
            $req_fields = array('customer_name', 'email', 'shipping_street_address', 'shipping_city', 'shipping_state', 'shipping_zip', 'shipping_country');
            $missing_fields = array();
            foreach ($req_fields as $field) {
                if (!isset($stripe[$field])) {
                    $missing_fields[] = $field;
                }
            }
            if ($missing_fields) {
                $retval['error'] = __("Invalid request", 'nextgen-gallery-pro');
            } else {
                $cart = new C_NextGen_Pro_Cart();
                $use_home_country = $this->object->param('ship_to');
                $cart->add_items($this->param('items'));
                $cart->apply_coupon($this->param('coupon'));
                $subtotal = $cart->get_subtotal();
                $shipping = $cart->get_shipping($use_home_country);
                $total = $cart->get_total($use_home_country);
                if ($total <= 0) {
                    return array('error' => __('Invalid request', 'nextgen-gallery-pro'));
                }
                // Create order
                $order = $this->create_order($cart->to_array($use_home_country), $stripe['customer_name'], $stripe['email'], $total, 'stripe_checkout', $stripe['shipping_street_address'], $stripe['shipping_city'], $stripe['shipping_state'], $stripe['shipping_zip'], $stripe['shipping_country'], $use_home_country);
                $order->gateway_admin_note = __('Payment was successfully made via Stripe, with no further payment action required.', 'nextgen-gallery-pro');
                $order->save();
                try {
                    $charge_params = array('amount' => round($total, 2) * 100, 'currency' => $stripe['currency'], 'card' => $stripe['token'], 'metadata' => array('order_id' => $order->ID(), 'description' => sprintf(__('Order from %s for %s (%s)', 'nextgen-gallery-pro'), $stripe['site_name'], $stripe['customer_name'], $stripe['email'])));
                    $charge = Stripe_Charge::create($charge_params);
                    $order->stripe_data = get_object_vars($charge);
                    if ($order->save()) {
                        $retval['redirect'] = site_url('/?ngg_stripe_rtn=1&order=' . $order->hash);
                    }
                } catch (Stripe_Error $ex) {
                    $retval['request'] = $charge_params;
                    $retval['error'] = $ex->getMessage();
                    $order->destroy();
                }
            }
        } else {
            $retval['error'] = __('Invalid request', 'nextgen-gallery-pro');
        }
        return $retval;
    }
}
class C_Stripe_TLS12_Check_Notification
{
    function is_renderable()
    {
        $settings = C_NextGen_Settings::get_instance();
        // Only check if Stripe payment gateway is enabled
        if (!$settings->ecommerce_stripe_enable) {
            return FALSE;
        }
        // If we've already checked and are missing TLS 1.2 then just show the notification
        if ($settings->ecommerce_stripe_tls12_missing) {
            return TRUE;
        }
        // We've checked before and it wasn't missing then - don't show this notification and don't check again
        if ($settings->ecommerce_stripe_tls12_checked) {
            return FALSE;
        }
        // Load Stripe and run our test
        if (!class_exists('Stripe')) {
            include_once 'stripe-sdk/lib/Stripe.php';
        }
        Stripe::setApiKey("sk_test_BQokikJOvBiI2HlWgH4olfQ2");
        Stripe::$apiBase = "https://api-tls12.stripe.com";
        try {
            $settings->ecommerce_stripe_tls12_checked = TRUE;
            $settings->save();
            Stripe_Charge::all();
        } catch (Stripe_ApiConnectionError $e) {
            $settings->ecommerce_stripe_tls12_missing = TRUE;
            $settings->save();
            return TRUE;
        }
        return FALSE;
    }
    function render()
    {
        return __('Stripe no longer supports API requests made with TLS 1.0. Please contact your systems administrator to enable TLS 1.2 support on your host.', 'nggallery');
    }
    function get_css_class()
    {
        return 'error';
    }
    function is_dismissable()
    {
        return TRUE;
    }
    function dismiss($code)
    {
        return array('handled' => TRUE);
    }
}