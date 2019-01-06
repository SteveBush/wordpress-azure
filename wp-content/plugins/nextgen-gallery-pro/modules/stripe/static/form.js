jQuery(function($) {
    $('#tr_ecommerce_stripe_enable').addClass('ngg_payment_gateway_enable_row');

    $('input[name="ecommerce[stripe_enable]"]')
        .nextgen_radio_toggle_tr('1', $('#tr_ecommerce_stripe_sandbox'))
        .nextgen_radio_toggle_tr('1', $('#tr_ecommerce_stripe_currencies_supported'))
        .nextgen_radio_toggle_tr('1', $('#tr_ecommerce_stripe_key_public'))
        .nextgen_radio_toggle_tr('1', $('#tr_ecommerce_stripe_key_private'));

    $('#tr_ecommerce_stripe_key_private input').attr('type', 'password');
});