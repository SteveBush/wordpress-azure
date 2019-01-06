jQuery(function($) {
    $('#npl_content').on('npl_ready', function(e, data) {

        var methods = data.methods;
        var self    = data.galleria_theme;

        log = function(message, table, level) {
            jQuery.nplModal('log', message, table, level)
        };

        log("ecommerce initializing");

        methods.sidebars.cart = {
            init: function() {
            },

            render: function(id) {
                log("ecommerce sidebars.cart.render()", {
                    id: id
                });

                // impose overlay
                $('#npl_wrapper').addClass('npl-sidebar-overlay-open');
                var container = $('#npl_sidebar');
                container.empty();
                var app = new Ngg_Pro_Cart.Views.Add_To_Cart({
                    image_id: id,
                    container: container
                });

                app.on('rendered', this.show_licensing_terms);
                app.on('ready', function() {
                    log("ecommerce sidebars.cart.render() ready");
                    app.render();
                    self.trigger('npl_sidebar_rendered');
                });
            },

            show_licensing_terms: function() {
                log("ecommerce sidebars.cart.show_licensing_terms() beginning");

                var request = {
                    action: 'get_digital_download_settings',
                    image_id: this.image_id
                };

                var header = this.$el.find('#ngg_digital_downloads_header');

                $.post(parent.photocrati_ajax.url, request, function(response) {
                    if (typeof(response) !== 'object') {
                        response = JSON.parse(response);
                    }

                    log("ecommerce sidebars.cart.show_licensing_terms() response", {
                        response: response
                    });

                    header.html(response.header);
                    $('#npl_wrapper').removeClass('npl-sidebar-overlay-open');
                });
            },

            get_type: function() {
                return 'cart';
            },

            events: {

                bind: function() {
                    log("ecommerce sidebars.cart.events.bind()");
                    self.bind('npl_init', this.npl_init);
                    self.bind('npl_init_keys', this.npl_init_keys);
                    self.bind('image', this.image);
                    self.bind('loadstart', this.loadstart);
                },

                loadstart: function() {
                    log("ecommerce sidebars.cart.events.loadstart()");
                    if ($.nplModal('get_state').sidebar && $.nplModal('get_state').sidebar == methods.sidebars.cart.get_type()) {
                        $('#npl_wrapper').addClass('npl-sidebar-overlay-open');
                    }
                },

                _image_ran_once: false,
                image: function() {
                    log("ecommerce sidebars.cart.events.image()");
                    if (methods.sidebars.cart.events.is_ecommerce_enabled()) {
                        if (!methods.sidebars.cart._image_ran_once) {
                            // possibly display the cart sidebar at startup
                            // display_comments may attempt to load at the same time--skip if it is on
                            if (($.nplModal('get_state').sidebar && $.nplModal('get_state').sidebar == methods.sidebars.cart.get_type())
                            ||  ($.nplModal('get_setting', 'display_cart', false) && !$.nplModal('get_setting', 'display_comments', false))) {
                                methods.sidebar.open(methods.sidebars.cart.get_type());
                            }
                        } else if ($.nplModal('get_state').sidebar && $.nplModal('get_state').sidebar == methods.sidebars.cart.get_type()) {
                            // updates the sidebar
                            methods.sidebar.render(methods.sidebars.cart.get_type());
                        }
                        methods.sidebars.cart._image_ran_once = true;
                    }
                },

                is_ecommerce_enabled: function() {
                    var retval = false;
                    var gallery = $.nplModal('get_gallery_from_id', $.nplModal('get_state').gallery_id);
                    if (gallery && typeof(gallery.display_settings['is_ecommerce_enabled']) != 'undefined') {
                        retval = gallery.display_settings['is_ecommerce_enabled'];
                    }
                    if (gallery && typeof(gallery.display_settings['original_settings']) != 'undefined') {
                        if (typeof(gallery.display_settings['original_settings']['is_ecommerce_enabled']) != 'undefined') {
                            retval = gallery.display_settings['original_settings']['is_ecommerce_enabled'];
                        }
                    }

                    return parseInt(retval);
                },

                npl_init: function() {
                    log("ecommerce sidebars.cart.events.npl_init()");
                    var is_ecommerce_enabled = methods.sidebars.cart.events.is_ecommerce_enabled;
                    if (is_ecommerce_enabled()) {
                        // Add cart toolbar button
                        var cart_button = $('<i/>')
                            .addClass('nggpl-toolbar-button-cart fa fa-shopping-cart')
                            .attr({'title': ngg_cart_i18n.nggpl_toggle_sidebar});
                        methods.thumbnails.register_button(
                            cart_button,
                            function(event) {
                                methods.sidebar.toggle(methods.sidebars.cart.get_type());
                                event.preventDefault();
                            }
                        );
                    }
                },

                npl_init_keys: function(event) {
                    log("ecommerce sidebars.cart.events.npl_init_keys");
                    var input_types = methods.galleria.get_keybinding_exclude_list();
                    self.attachKeyboard({
                        // 'e' for shopping cart
                        69: function() {
                            var is_ecommerce_enabled = methods.sidebars.cart.events.is_ecommerce_enabled;
                            if (!$(document.activeElement).is(input_types) && is_ecommerce_enabled()) {
                                methods.sidebar.toggle(methods.sidebars.cart.get_type());
                            }
                        }
                    });
                }
            }
        };

        methods.sidebars.cart.events.bind();
    });

    $(document).on('ngg-caption-add-icons', function(event, obj) {

        if (!$.nplModal('get_displayed_gallery_setting', obj.gallery_id, 'is_ecommerce_enabled', false)) {
            return;
        }

        var cart_icon = $('<i/>', {
            'class': 'fa fa-shopping-cart nextgen_pro_lightbox ngg-caption-icon',
            'data-nplmodal-gallery-id': obj.gallery_id,
            'data-nplmodal-image-id': obj.image_id,
            'data-image-id': obj.image_id,
            'data-nplmodal-show-cart': '1'
        }).on('click', function(event) {
            event.preventDefault();
            $.nplModal('open', $(this));
            return false;
        });

        obj.el.append(cart_icon);
    });
});
