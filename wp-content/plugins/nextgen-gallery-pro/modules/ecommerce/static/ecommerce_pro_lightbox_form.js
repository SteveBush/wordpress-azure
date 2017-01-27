(function($) {
    // this moves the optionto under "Display comments" rather than the very bottom of the form
    var $header_row = $('#tr_photocrati-nextgen_pro_lightbox_ecommerce');
    var $option_row = $('#tr_photocrati-nextgen_pro_lightbox_display_cart');

    $header_row.detach();
    $option_row.detach();

    $header_row.insertAfter('#tr_photocrati-nextgen_pro_lightbox_display_captions');
    $option_row.insertAfter($header_row);

    var $context_tbody = $('select#lightbox_global').parents('tbody');
    $context_tbody.detach();

    var $effect_tbody = $('select#lightbox_library').parents('tbody');
    $context_tbody.insertAfter($effect_tbody);
})(jQuery);