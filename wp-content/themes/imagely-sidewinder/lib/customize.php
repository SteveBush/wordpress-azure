<?php

/************************************************ 
 * 
 * WP CUSTOMIZER: Register settings and controls 
 * 
 ************************************************/

add_action( 'customize_register', 'imagely_customizer_register', 16 );
function imagely_customizer_register() {

	global $wp_customize;

	/* Inlcude the Alpha Color Picker control file 
	 * Source: https://github.com/BraadMartin/components/tree/master/customizer/alpha-color-picker */
    require_once( dirname( __FILE__ ) . '/alpha-color-picker/alpha-color-picker.php' );

	/* Adjust the title and description of the default WordPress "Header Image" section */
	$wp_customize->get_section( 'header_image' )->title = __( 'Logo', 'imagely-sidewinder' );
	$wp_customize->get_section( 'header_image' )->description = __( 'LOGO SIZE: Although we give *maximum* dimensions below, there is a lot of flexibility. Experiment by uploading and cropping your logo at different sizes, aspect ratios, and with more/less space around the edges until it looks the way you want.', 'imagely-sidewinder' );
	
	/* Remove layout section from Theme Customizer */
	$wp_customize->remove_section( 'genesis_layout' );

	if ( IMAGELY_FRONT_SLIDESHOW == true ) {
	
		/* Front Featured Slideshow */
		$wp_customize->add_section( 'imagely-image', array(
			'title'          => __( 'Front Page Slideshow', 'imagely-sidewinder' ),
			'description'    => __( '<p>Upload image(s) for front page background slideshow. To change the background image for other pages on the site, please click to any other page on the site on the right to see the normal background image controls.</p><p>We recommend horizontal or landscape images that are at least <strong>1920 pixels wide</strong>.</p>', 'imagely-sidewinder' ),
			'priority'       => 75,
		) );

		$wp_customize->add_setting( 'imagely-front-image-1', array(
			'default'  => sprintf( '%s/images/background.jpg', get_stylesheet_directory_uri() ),
			'type'     => 'option',
			'sanitize_callback' => 'esc_url_raw',
		) );
		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'front-featured-image-1',
				array(
					'label'       => __( 'First Image Upload', 'imagely-sidewinder' ),
					'section'     => 'imagely-image',
					'settings'    => 'imagely-front-image-1',
				)
			)
		);

		$wp_customize->add_setting( 'imagely-front-image-2', array(
			'type'     => 'option',
			'sanitize_callback' => 'esc_url_raw',
		) );
		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'front-featured-image-2',
				array(
					'label'       => __( 'Second Image Upload', 'imagely-sidewinder' ),
					'section'     => 'imagely-image',
					'settings'    => 'imagely-front-image-2',
				)
			)
		);

		$wp_customize->add_setting( 'imagely-front-image-3', array(
			'type'     => 'option',
			'sanitize_callback' => 'esc_url_raw',
		) );
		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'front-featured-image-3',
				array(
					'label'       => __( 'Third Image Upload', 'imagely-sidewinder' ),
					'section'     => 'imagely-image',
					'settings'    => 'imagely-front-image-3',
				)
			)
		);

	}

	/****************************************************************
	 *
	 * COLORS CUSTOMIZATIONS. We provide extensive color controls via
	 * the customizer. Below we create a new panel, Custom Colors, to 
	 * hold all color controls. Within that panel, we create new 
	 * sections for header, content, footer, and other. And then 
	 * within each section, we provide specific controls for each
	 * relevant element.
	 *  
	 ****************************************************************/

	if ( IMAGELY_BG_IMAGE ) {
		/* Move default background color control to background image section */
		$wp_customize->get_control( 'background_color'  )->section   = 'background_image';
		$wp_customize->get_control( 'background_color'  )->description   = 'The background color will show only if there is no background image.';
	}	

	/* Add Customize Colors panel to contain all other colors */
	$wp_customize->add_panel( 'custom-colors', array(
        'priority' => 99,
        'capability' => 'edit_theme_options',
        'theme_supports' => '',
        'title' => __( 'Customize Colors', 'imagely-sidewinder' ),
        'description' => __( 'Customize your website colors.', 'imagely-sidewinder' ),
    ) );

    /* Add Header section to Custom Color Panel */
	$wp_customize->add_section( 'header-colors', array(
        'priority' => 10,
        'capability' => 'edit_theme_options',
        'theme_supports' => '',
        'title' => __( 'Header Colors', 'imagely-sidewinder' ),
        'description' => 'Note: Custom header colors may not apply on your home page if your site has a transparent header area. To see changes, click a different page on the right first.',
        'panel' => 'custom-colors',
    ) );

		/* Header: Background color (except for Summerly theme) 
		 * Uses Alpha Color Picker for rgba color selection */
		if (CHILD_THEME_NAME != "Imagely Summerly") {

		    $wp_customize->add_setting(
		        'imagely_header_background',
		        array(
		            'default'     		=> '#fff',
		            'type'        		=> 'theme_mod',
		            'capability'  		=> 'edit_theme_options',
		            'transport'   		=> 'refresh',
					'sanitize_callback' => 'imagely_sanitize_color'
		        )
		    );

		    $wp_customize->add_control(
		        new Customize_Alpha_Color_Control(
		            $wp_customize,
		            'imagely_header_background_control',
		            array(
		            	'description' 	=> __( 'Change the header background color. If your theme has a header background image, this color may not be visible.', 'imagely-sidewinder' ),
					    'label'       	=> __( 'Header Background Color', 'imagely-sidewinder' ),
					    'section'     	=> 'header-colors',
					    'settings'    	=> 'imagely_header_background',
		                'show_opacity'  => true,
		                'palette'   	=> true
		                )
		        )
		    );

		}

		/* Header: Border color (only for Summerly theme) */
		if (CHILD_THEME_NAME == "Imagely Summerly") {

			$wp_customize->add_setting(
				'imagely_header_border',
				array(
					'default'           => '#eee',
					'sanitize_callback' => 'sanitize_hex_color'
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'imagely_header_border_control',
					array(
						'description' => __( 'NOTE: This will change the top/bottom header border color, but also the content top/bottom borders to keep colors consistent.', 'imagely-sidewinder' ),
					    'label'       => __( 'Border Color (See Note)', 'imagely-sidewinder' ),
					    'section'     => 'header-colors',
					    'settings'    => 'imagely_header_border'
					)
				)
			);
			
		}

		/* Header: Title color */
		$wp_customize->add_setting(
			'imagely_title_color',
			array(
				'default'           => '#222',
				'sanitize_callback' => 'sanitize_hex_color'
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'imagely_title_color_control',
				array(
					'description' => __( 'Change the title color.', 'imagely-sidewinder' ),
				    'label'       => __( 'Title Color', 'imagely-sidewinder' ),
				    'section'     => 'header-colors',
				    'settings'    => 'imagely_title_color'
				)
			)
		);

		/* Header: Description color */
		$wp_customize->add_setting(
			'imagely_description_color',
			array(
				'default'           => '#aaa',
				'sanitize_callback' => 'sanitize_hex_color'
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'imagely_description_color_control',
				array(
					'description' => __( 'Change the description color (if visible).', 'imagely-sidewinder' ),
				    'label'       => __( 'Site Description Color (if Visible)', 'imagely-sidewinder' ),
				    'section'     => 'header-colors',
				    'settings'    => 'imagely_description_color'
				)
			)
		);
		
		/* Header: Menu link color */
		$wp_customize->add_setting(
			'imagely_menu_link',
			array(
				'default'           => '#000',
				'sanitize_callback' => 'sanitize_hex_color'
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'imagely_menu_link_control',
				array(
					'description' => __( 'Change the menu link color.', 'imagely-sidewinder' ),
				    'label'       => __( 'Menu Link Color', 'imagely-sidewinder' ),
				    'section'     => 'header-colors',
				    'settings'    => 'imagely_menu_link'
				)
			)
		);

		/* Header: Menu link hover color */
		$wp_customize->add_setting(
			'imagely_menu_hover',
			array(
				'default'           => '#000',
				'sanitize_callback' => 'sanitize_hex_color'
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'imagely_menu_hover_control',
				array(
					'description' => __( 'Change the menu link hover color.', 'imagely-sidewinder' ),
				    'label'       => __( 'Menu Link Hover Color', 'imagely-sidewinder' ),
				    'section'     => 'header-colors',
				    'settings'    => 'imagely_menu_hover'
				)
			)
		);

		/* Header: Submenu background color (except Summerly theme) 
		 * Uses Alpha Color Picker for rgba color selection */
		
		if (CHILD_THEME_NAME != "Imagely Summerly") {

			$wp_customize->add_setting(
				'imagely_submenu_background',
				array(
					'default'     		=> '#fcfcfc',
					'type'        		=> 'theme_mod',
		            'capability'  		=> 'edit_theme_options',
		            'transport'   		=> 'refresh',
		            'sanitize_callback' => 'imagely_sanitize_color'
				)
			);

			$wp_customize->add_control(
				new Customize_Alpha_Color_Control(
					$wp_customize,
					'imagely_submenu_background_control',
					array(
						'description' 	=> __( 'Change the submenu background color.', 'imagely-sidewinder' ),
					    'label'       	=> __( 'Submenu Background Color', 'imagely-sidewinder' ),
					    'section'     	=> 'header-colors',
					    'settings'    	=> 'imagely_submenu_background',
					    'show_opacity'  => true,
		                'palette'   	=> true
					)
				)
			);

		}

		/* Header: Submenu link color */
		$wp_customize->add_setting(
			'imagely_submenu_link',
			array(
				'default'           => '#666',
				'sanitize_callback' => 'sanitize_hex_color'
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'imagely_submenu_link_control',
				array(
					'description' => __( 'Change the submenu link color.', 'imagely-sidewinder' ),
				    'label'       => __( 'Submenu Link Color', 'imagely-sidewinder' ),
				    'section'     => 'header-colors',
				    'settings'    => 'imagely_submenu_link'
				)
			)
		);

		/* Header: Submenu link hover color */
		$wp_customize->add_setting(
			'imagely_submenu_hover',
			array(
				'default'           => '#000',
				'sanitize_callback' => 'sanitize_hex_color'
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'imagely_submenu_hover_control',
				array(
					'description' => __( 'Change the submenu link hover color.', 'imagely-sidewinder' ),
				    'label'       => __( 'Submenu Link Hover Color', 'imagely-sidewinder' ),
				    'section'     => 'header-colors',
				    'settings'    => 'imagely_submenu_hover'
				)
			)
		);


	/* Add Mobile Header section to Custom Color Panel */
	$wp_customize->add_section( 'mobile-header-colors', array(
        'priority' 			=> 10,
        'capability' 		=> 'edit_theme_options',
        'theme_supports' 	=> '',
        'title' 			=> __( 'Mobile Header Colors', 'imagely-sidewinder' ),
        'description' 		=> 'Customize the header colors on smaller mobile devices like phones. To preview the appearance, grab the lower right corner of this browser window and reduce the window width until the responsive header appears.',
        'panel' 			=> 'custom-colors'
    ) );

    	/* Mobile Header: Background color 
    	 * Uses Alpha Color Picker for rgba color selection */
		$wp_customize->add_setting(
			'imagely_mobile_header_background',
			array(
				'default'           => '#fff',
				'type'        		=> 'theme_mod',
	            'capability'  		=> 'edit_theme_options',
	            'transport'   		=> 'refresh',
				'sanitize_callback' => 'imagely_sanitize_color'
			)
		);

		$wp_customize->add_control(
			new Customize_Alpha_Color_Control(
				$wp_customize,
				'imagely_mobile_header_background_control',
				array(
					'description' 	=> __( 'Change the header background color on mobile devices.', 'imagely-sidewinder' ),
				    'label'       	=> __( 'Mobile Header Background', 'imagely-sidewinder' ),
				    'section'     	=> 'mobile-header-colors',
				    'settings'    	=> 'imagely_mobile_header_background',
				    'show_opacity'  => true,
	                'palette'   	=> true
				)
			)
		);

		/* Mobile Header: Title color */
		$wp_customize->add_setting(
			'imagely_mobile_title_color',
			array(
				'default'           => '#222',
				'sanitize_callback' => 'sanitize_hex_color'
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'imagely_mobile_title_color_control',
				array(
					'description' => __( 'Change the mobile title color.', 'imagely-sidewinder' ),
				    'label'       => __( 'Mobile Title Color', 'imagely-sidewinder' ),
				    'section'     => 'mobile-header-colors',
				    'settings'    => 'imagely_mobile_title_color'
				)
			)
		);

		/* Mobile Header: Description color */
		$wp_customize->add_setting(
			'imagely_mobile_description_color',
			array(
				'default'           => '#aaa',
				'sanitize_callback' => 'sanitize_hex_color'
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'imagely_mobile_description_color_control',
				array(
					'description' => __( 'Change the site description color on mobile devices (if visible).', 'imagely-sidewinder' ),
				    'label'       => __( 'Site Description Color (if visible)', 'imagely-sidewinder' ),
				    'section'     => 'mobile-header-colors',
				    'settings'    => 'imagely_mobile_description_color'
				)
			)
		);
		
		/* Mobile Header: Menu link color */
		$wp_customize->add_setting(
			'imagely_mobile_menu_link',
			array(
				'default'           => '#222',
				'sanitize_callback' => 'sanitize_hex_color'
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'imagely_mobile_menu_link_control',
				array(
					'description' => __( 'Change the menu/submenu link color.', 'imagely-sidewinder' ),
				    'label'       => __( 'Menu/Submenu Link Color', 'imagely-sidewinder' ),
				    'section'     => 'mobile-header-colors',
				    'settings'    => 'imagely_mobile_menu_link'
				)
			)
		);

		/* Mobile Header: Menu hover color */
		$wp_customize->add_setting(
			'imagely_mobile_menu_hover',
			array(
				'default'           => '#999',
				'sanitize_callback' => 'sanitize_hex_color'
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'imagely_mobile_menu_hover_control',
				array(
					'description' => __( 'Change the menu/submenu hover color.', 'imagely-sidewinder' ),
				    'label'       => __( 'Menu/Submenu Hover Color', 'imagely-sidewinder' ),
				    'section'     => 'mobile-header-colors',
				    'settings'    => 'imagely_mobile_menu_hover'
				)
			)
		);


	/* Add Content section to Custom Color Panel */
	$wp_customize->add_section( 'content-colors', array(
        'priority' 			=> 10,
        'capability' 		=> 'edit_theme_options',
        'theme_supports' 	=> '',
        'title' 			=> __( 'Content Area Colors', 'imagely-sidewinder' ),
        'description' 		=> '',
        'panel' 			=> 'custom-colors'
    ) );

		/* Content: Background color 
		 * Uses Alpha Color Picker for rgba color selection */
		$wp_customize->add_setting(
			'imagely_content_background',
			array(
				'default'           => '#fff',
				'type'        		=> 'theme_mod',
	            'capability'  		=> 'edit_theme_options',
	            'transport'   		=> 'refresh',
				'sanitize_callback' => 'imagely_sanitize_color'
			)
		);

		$wp_customize->add_control(
			new Customize_Alpha_Color_Control(
				$wp_customize,
				'imagely_content_background_control',
				array(
					'description' 	=> __( 'Change the color of the content background.', 'imagely-sidewinder' ),
				    'label'       	=> __( 'Content Background Color', 'imagely-sidewinder' ),
				    'section'     	=> 'content-colors',
				    'settings'    	=> 'imagely_content_background',
				    'show_opacity'  => true,
	                'palette'   	=> true
				)
			)
		);

		/* Content: Heading color */
		$wp_customize->add_setting(
			'imagely_heading_color',
			array(
				'default'           => '#222',
				'sanitize_callback' => 'sanitize_hex_color'
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'imagely_heading_color_control',
				array(
					'description' => __( 'Change the color of page and post headings (H1-H6). This will not affect headings on top of images, which will generally stay white for contrast.', 'imagely-sidewinder' ),
				    'label'       => __( 'Heading Color', 'imagely-sidewinder' ),
				    'section'     => 'content-colors',
				    'settings'    => 'imagely_heading_color'
				)
			)
		);

		/* Content: Font color */
		$wp_customize->add_setting(
			'imagely_font_color',
			array(
				'default'           => '#666',
				'sanitize_callback' => 'sanitize_hex_color'
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'imagely_font_color_control',
				array(
					'description' => __( 'Change the main website font color.', 'imagely-sidewinder' ),
				    'label'       => __( 'Font Color', 'imagely-sidewinder' ),
				    'section'     => 'content-colors',
				    'settings'    => 'imagely_font_color'
				)
			)
		);

		/* Content: Link color */
		$wp_customize->add_setting(
			'imagely_link_color',
			array(
				'default'           => '#aaa',
				'sanitize_callback' => 'sanitize_hex_color'
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'imagely_link_color_control',
				array(
					'description' => __( 'Change the main link color.', 'imagely-sidewinder' ),
				    'label'       => __( 'Link Color', 'imagely-sidewinder' ),
				    'section'     => 'content-colors',
				    'settings'    => 'imagely_link_color'
				)
			)
		);

		/* Content: Link hover color */
		$wp_customize->add_setting(
			'imagely_link_hover',
			array(
				'default'           => '#222',
				'sanitize_callback' => 'sanitize_hex_color'
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'imagely_link_hover_control',
				array(
					'description' => __( 'Change the main link hover color.', 'imagely-sidewinder' ),
				    'label'       => __( 'Link Hover Color', 'imagely-sidewinder' ),
				    'section'     => 'content-colors',
				    'settings'    => 'imagely_link_hover'
				)
			)
		);

		/* Other: Button color */
		$wp_customize->add_setting(
			'imagely_button_color',
			array(
				'default'           => '#222',
				'sanitize_callback' => 'sanitize_hex_color'
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'imagely_button_color_control',
				array(
					'description' => __( 'Change the button color.', 'imagely-sidewinder' ),
				    'label'       => __( 'Button Color', 'imagely-sidewinder' ),
				    'section'     => 'content-colors',
				    'settings'    => 'imagely_button_color'
				)
			)
		);

    	/* Other: Button hover color */
		$wp_customize->add_setting(
			'imagely_button_hover',
			array(
				'default'           => '#555',
				'sanitize_callback' => 'sanitize_hex_color'
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'imagely_button_hover_control',
				array(
					'description' => __( 'Change the button hover color.', 'imagely-sidewinder' ),
				    'label'       => __( 'Button Hover Color', 'imagely-sidewinder' ),
				    'section'     => 'content-colors',
				    'settings'    => 'imagely_button_hover'
				)
			)
		);
	
	/* Add Footer section to Custom Color Panel */
	$wp_customize->add_section( 'footer-colors', array(
        'priority' 			=> 10,
        'capability' 		=> 'edit_theme_options',
        'theme_supports' 	=> '',
        'title' 			=> __( 'Footer Colors', 'imagely-sidewinder' ),
        'description' 		=> '',
        'panel' 			=> 'custom-colors'
    ) );


		/* Footer: Display Imagely in Footer */
		$wp_customize->add_setting( 
			'powered_by_imagely', 
			array(
		    	'default' => 1,
		    	'sanitize_callback' => 'imagely_sanitize_checkbox'
		) );
		 
		$wp_customize->add_control( 
			'powered_by_imagely_control', 
			array(
				'description' => __( 'We always appreciate when users leave this on, but it is your site :).', 'imagely-sidewinder' ),
			    'label' => __( 'Display "Powered by Imagely" in footer?', 'imagely-sidewinder'),
			    'type' => 'checkbox',
			    'section' => 'footer-colors',
			    'settings' => 'powered_by_imagely'
		) );

		/* Footer: Background color */
		$wp_customize->add_setting(
			'imagely_footer_background',
			array(
				'default'           => '#fff',
				'type'        		=> 'theme_mod',
	            'capability'  		=> 'edit_theme_options',
	            'transport'   		=> 'refresh',
				'sanitize_callback' => 'imagely_sanitize_color'
			)
		);

		$wp_customize->add_control(
			new Customize_Alpha_Color_Control(
				$wp_customize,
				'imagely_footer_background_control',
				array(
					'description' => __( 'Change the footer background color.', 'imagely-sidewinder' ),
				    'label'       => __( 'Footer Background Color', 'imagely-sidewinder' ),
				    'section'     => 'footer-colors',
				    'settings'    => 'imagely_footer_background',
				    'show_opacity'  => true,
	                'palette'   	=> true
				)
			)
		);

		/* Footer: Widget title color */
		$wp_customize->add_setting(
			'imagely_footer_title',
			array(
				'default'           => '#222',
				'sanitize_callback' => 'sanitize_hex_color'
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'imagely_footer_title_control',
				array(
					'description' => __( 'Change the footer title color.', 'imagely-sidewinder' ),
				    'label'       => __( 'Footer Title Color', 'imagely-sidewinder' ),
				    'section'     => 'footer-colors',
				    'settings'    => 'imagely_footer_title'
				)
			)
		);

		/* Footer: Text color */
		$wp_customize->add_setting(
			'imagely_footer_text',
			array(
				'default'           => '#666',
				'sanitize_callback' => 'sanitize_hex_color'
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'imagely_footer_text_control',
				array(
					'description' => __( 'Change the footer text color.', 'imagely-sidewinder' ),
				    'label'       => __( 'Footer Text Color', 'imagely-sidewinder' ),
				    'section'     => 'footer-colors',
				    'settings'    => 'imagely_footer_text'
				)
			)
		);

		/* Footer: Link color */
		$wp_customize->add_setting(
			'imagely_footer_link',
			array(
				'default'           => '#aaa',
				'sanitize_callback' => 'sanitize_hex_color'
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'imagely_footer_link_control',
				array(
					'description' => __( 'Change the footer link color.', 'imagely-sidewinder' ),
				    'label'       => __( 'Footer Link Color', 'imagely-sidewinder' ),
				    'section'     => 'footer-colors',
				    'settings'    => 'imagely_footer_link'
				)
			)
		);

		/* Footer: Link hover color */
		$wp_customize->add_setting(
			'imagely_footer_hover',
			array(
				'default'           => '#222',
				'sanitize_callback' => 'sanitize_hex_color'
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'imagely_footer_hover_control',
				array(
					'description' => __( 'Change the footer link hover color.', 'imagely-sidewinder' ),
				    'label'       => __( 'Footer Link Hover Color', 'imagely-sidewinder' ),
				    'section'     => 'footer-colors',
				    'settings'    => 'imagely_footer_hover'
				)
			)
		);	

}

/************************************************ 
 * 
 * WP CUSTOMIZER: Build and Output the CSS
 * 
 ************************************************/

add_action( 'wp_enqueue_scripts', 'imagely_css' );
function imagely_css() {

	$handle  = defined( 'CHILD_THEME_NAME' ) && CHILD_THEME_NAME ? sanitize_title_with_dashes( CHILD_THEME_NAME ) : 'child-theme';

	/* Retrieve the colors from customizer */
	$header_background = get_theme_mod( 'imagely_header_background', '#fff' );
	$header_border = get_theme_mod( 'imagely_header_border', '#eee' );
	$title_color = get_theme_mod( 'imagely_title_color', '#222' );
	$description_color = get_theme_mod( 'imagely_description_color', '#aaa' );
	$menu_background = get_theme_mod( 'imagely_menu_background', ',' );
	$menu_link = get_theme_mod( 'imagely_menu_link', '#000' );
	$menu_hover = get_theme_mod( 'imagely_menu_hover', '#000' );
	$submenu_background = get_theme_mod( 'imagely_submenu_background', '#fcfcfc' );
	$submenu_link = get_theme_mod( 'imagely_submenu_link', '#666' );
	$submenu_hover = get_theme_mod( 'imagely_submenu_hover', '#000' );
	$mobile_header_background = get_theme_mod( 'imagely_mobile_header_background', '#fff' );
	$mobile_title_color = get_theme_mod( 'imagely_mobile_title_color', '#222' );
	$mobile_description_color = get_theme_mod( 'imagely_mobile_description_color', '#aaa' );
	$mobile_menu_link = get_theme_mod( 'imagely_mobile_menu_link', '#222' );
	$mobile_menu_hover = get_theme_mod( 'imagely_mobile_menu_hover', '#999' );
	$content_background = get_theme_mod( 'imagely_content_background', '#fff' );
	$heading_color = get_theme_mod( 'imagely_heading_color', '#222' );
	$font_color = get_theme_mod( 'imagely_font_color', '#666' );
	$link_color = get_theme_mod( 'imagely_link_color', '#aaa' );
	$link_hover = get_theme_mod( 'imagely_link_hover', '#222' );
	$button_color = get_theme_mod( 'imagely_button_color', '#222' );
	$button_hover = get_theme_mod( 'imagely_button_hover', '#555' );
	$footer_background = get_theme_mod( 'imagely_footer_background', '#fff' );
	$footer_title = get_theme_mod( 'imagely_footer_title', '#222' );
	$footer_text = get_theme_mod( 'imagely_footer_text', '#666' );
	$footer_link = get_theme_mod( 'imagely_footer_link', '#aaa' );
	$footer_hover = get_theme_mod( 'imagely_footer_hover', '#222' );

	/* Build the CSS */
	$css = '';
	
	$css .= ( '#fff' !== $header_background ) ? sprintf( '
		.site-header {
			background-color: %1$s; 
			border: none;
		}
		.header-widget-area {
			border-color: rgba(150,150,150,.2);
		}
		.genesis-nav-menu a:hover {
			background-color: transparent;
		}' , $header_background ) : '';

	$css .= ( '#eee' !== $header_border ) ? sprintf( '
		.site-header,
		.site-inner,
		.footer-widgets {
			border-color: %1$s;
		}' , $header_border ) : '';
	
	$css .= ( '#222' !== $title_color ) ? sprintf( '
		.site-title a, 
		.site-title a:hover {
			color: %1$s;
		}' , $title_color ) : '';
	
	$css .= ( '#aaa' !== $description_color ) ? sprintf( '
		.site-description {
			color: %1$s;
		}' , $description_color ) : '';
	
	$css .= ( '#000' !== $menu_link ) ? sprintf( '
		.genesis-nav-menu a,
		.responsive-menu-icon::before {
			color: %1$s;
		}' , $menu_link ) : '';
	
	$css .= ( '#000' !== $menu_hover ) ? sprintf( '
		.genesis-nav-menu a:hover, 
		.genesis-nav-menu a:active {
			color: %1$s;
		}' , $menu_hover ) : '';
	
	$css .= ( '#fcfcfc' !== $submenu_background ) ? sprintf( '
		.genesis-nav-menu .sub-menu {
			background-color: %1$s; 
			border-color: %1$s;
		}' , $submenu_background ) : '';

	$css .= ( '#666' !== $submenu_link ) ? sprintf( '
		.genesis-nav-menu .sub-menu a {
			color: %1$s;
		}' , $submenu_link ) : '';
	
	$css .= ( '#000' !== $submenu_hover ) ? sprintf( '
		.genesis-nav-menu .sub-menu a:hover, 
		.genesis-nav-menu .sub-menu a:active,
		.genesis-nav-menu .current-menu-item > a {
			color: %1$s;
		}' , $submenu_hover ) : '';

	$css .= ( '#fff' !== $mobile_header_background ) ? sprintf( '
		@media only screen and (max-width: 1120px) {
			.site-header {
				background-color: %1$s;
			}
			.title-area,
			.genesis-nav-menu,
			.genesis-nav-menu .sub-menu,
			.gensis-nave-menu.responsive-menu, 
			.gensis-nave-menu.responsive-menu .sub-menu {
				background-color: transparent;
			}
		}' , $mobile_header_background ) : '';

	$css .= ( '#222' !== $mobile_title_color ) ? sprintf( '
		@media only screen and (max-width: 1120px) {
			.site-title a, 
			.site-title a:hover {
				color: %1$s;
			}
		}' , $mobile_title_color ) : '';
	
	$css .= ( '#aaa' !== $mobile_description_color ) ? sprintf( '
		@media only screen and (max-width: 1120px) {
			.site-description {
				color: %1$s;
			}
		}' , $mobile_description_color ) : '';

	$css .= ( '#222' !== $mobile_menu_link ) ? sprintf( '
		@media only screen and (max-width: 1120px) {
			.responsive-menu-icon::before,
			.genesis-nav-menu a,
			.genesis-nav-menu .sub-menu a,
			.genesis-nav-menu.responsive-menu a,
			.genesis-nav-menu.responsive-menu .sub-menu li a,
			.genesis-nav-menu.responsive-menu .menu-item-has-children::before {
				color:  %1$s;
			}
		}' , $mobile_menu_link ) : '';
	
	$css .= ( '#999' !== $mobile_menu_hover ) ? sprintf( '
		@media only screen and (max-width: 1120px) {
			.genesis-nav-menu a:hover, 
			.genesis-nav-menu a:active,
			.genesis-nav-menu .sub-menu a:hover, 
			.genesis-nav-menu .sub-menu a:active,
			.genesis-nav-menu.responsive-menu a:hover,
			.genesis-nav-menu.responsive-menu a:active,
			.genesis-nav-menu.responsive-menu .current-menu-item > a,
			.genesis-nav-menu.responsive-menu .sub-menu li a:hover,
			.genesis-nav-menu.responsive-menu .sub-menu li a:focus {
				color: %1$s;
			}
		}' , $mobile_menu_hover ) : '';

	$css .= ( '#fff' !== $content_background ) ? sprintf( '
		body,
		.site-inner,
		.imagely-masonry .entry {
			background-color: %1$s;
			border: none;
		}

		.imagely-masonry .entry {
			border-color: rgba(150,150,150,.2);
		}

		.imagely-featured-image .content > .entry .entry-header,
		.imagely-featured-image .entry-header {
			border-color: %1$s;
		}
		
		.after-post {
			background-color: transparent;
			border-color: rgba(120,120,120,.2);
		}

		.sidebar,
		.entry {
			border-color: rgba(120,120,120,.2);
		}

		.imagely-masonry .content .entry {
		    background-color: rgba(120,120,120,.2);
		    border-color: rgba(120,120,120,.15);
		}

		.content select,
		.content textarea {
			background-color: #eee;
			border-color: rgba(150,150,150,.2);
		}' , $content_background ) : '';

	$css .= ( '#222' !== $heading_color ) ? sprintf( '
		h1,
		h2,
		h3,
		h4,
		h5,
		h6,
		.entry-title,
		.entry-title a,
		.entry-title a:hover,
		.widgettitle {
			color: %1$s;
		}' , $heading_color ) : '';

	$css .= ( '#666' !== $font_color ) ? sprintf( '
		body,
		.imagely-featured-image .content > .entry .entry-footer .entry-meta {
			color: %1$s;
		}' , $font_color ) : '';

	$css .= ( '#aaa' !== $link_color ) ? sprintf( '
		a,
		.author-box a,
		.archive-description a,
		.entry-meta a,
		.sidebar a,
		.imagely-featured-image .content > .entry .entry-footer .entry-meta a {
			color: %1$s;
		}' , $link_color ) : '';
	
	$css .= ( '#222' !== $link_hover ) ? sprintf( '
		a:hover, 
		a:focus,
		.author-box a:hover,
		.author-box a:focus,
		.archive-description a:hover,
		.archive-description a:focus,
		.entry-meta a:hover,
		.sidebar a:hover,
		.sidebar a:focus,
		.sidebar a:active,
		.imagely-featured-image .content > .entry .entry-meta a:hover,
		.imagely-featured-image .content > .entry .entry-meta a:focus,
		.imagely-featured-image .content > .entry .entry-meta a:active {
			color: %1$s;
		}' , $link_hover ) : '';
	
	$css .= ( '#fff' !== $footer_background ) ? sprintf( '
		.footer-widgets, 
		.site-footer {
			background-color: %1$s;
		}

		.footer-widgets input,
		.footer-widgets select,
		.footer-widgets textarea {
			background-color: #eee;
			border-color: rgba(150,150,150,.2);
		}' , $footer_background ) : '';
	
	$css .= ( '#222' !== $footer_title ) ? sprintf( '
		.footer-widgets .widget-title {
			color:  %1$s;
		}' , $footer_title ) : '';
	
	$css .= ( '#666' !== $footer_text ) ? sprintf( '
		.footer-widgets, 
		.site-footer {
			color:  %1$s;
		}' , $footer_text ) : '';
	
	$css .= ( '#aaa' !== $footer_link) ? sprintf( '
		.footer-widgets a, 
		.footer-widgets .genesis-nav-menu a,
		.footer-widgets .genesis-nav-menu .sub-menu a,
		.footer-widgets .entry-title a,
		.site-footer a,
		.site-footer .genesis-nav-menu a {
			color:  %1$s;
		}' , $footer_link ) : '';
		
	$css .= ( '#222' !== $footer_hover ) ? sprintf( '
		.footer-widgets a:hover, 
		.footer-widgets a:active, 
		.footer-widgets .genesis-nav-menu a:active,
		.footer-widgets .genesis-nav-menu a:hover,
		.footer-widgets .genesis-nav-menu .sub-menu a:active,
		.footer-widgets .genesis-nav-menu .sub-menu a:hover,
		.footer-widgets .entry-title a:hover,
  		.footer-widgets .entry-title a:focus,
		.site-footer a:hover, 
		.site-footer a:hover,
		.site-footer .genesis-nav-menu a:active,
		.site-footer .genesis-nav-menu a:hover {
			color:  %1$s;
		}' , $footer_hover ) : '';
	
	$css .= ( '#222' !== $button_color ) ? sprintf( '
		button,
		input[type="button"],
		input[type="reset"],
		input[type="submit"],
		.button,
		.content .widget .textwidget a.button,
		.entry-content a.button,
		.entry-content a.more-link,
		.footer-widgets button,
		.footer-widgets input[type="button"],
		.footer-widgets input[type="reset"],
		.footer-widgets input[type="submit"],
		.footer-widgets .button,
		.content .front-page-1 .widget a.button,
		.content .front-page-1 .widget .textwidget a.button,
		.front-page-1 button,
		.front-page-1 input[type="button"],
		.front-page-1 input[type="reset"],
		.front-page-1 input[type="submit"],
		.front-page-1 .entry-content a.button,
		.nav-primary li.highlight > a,
		.archive-pagination li a:hover,
		.archive-pagination li a:active,
		.archive-pagination li.active a,
		.front-page .content .fa {
			background-color:  %1$s;
		}' , $button_color ) : '';
	
	$css .= ( '#555' !== $button_hover ) ? sprintf( '
		button:hover,
		button:focus,
		input:hover[type="button"],
		input:focus[type="button"],
		input:hover[type="reset"],
		input:focus[type="reset"],
		input:hover[type="submit"],
		input:focus[type="submit"],
		.button:hover,
		.button:focus,
		.content .widget .textwidget a.button:hover,
		.content .widget .textwidget a.button:focus,
		.entry-content a.button:hover,
		.entry-content a.button:focus,
		.entry-content a.more-link:hover,
		.entry-content a.more-link:focus,
		.footer-widgets button:hover,
		.footer-widgets button:focus,
		.footer-widgets input:hover[type="button"],
		.footer-widgets input:focus[type="button"],
		.footer-widgets input:hover[type="reset"],
		.footer-widgets input:focus[type="reset"],
		.footer-widgets input:hover[type="submit"],
		.footer-widgets input:focus[type="submit"],
		.footer-widgets .button:hover,
		.footer-widgets .button:focus,
		.content .front-page-1 .widget a.button:hover,
		.content .front-page-1 .widget a.button:focus,
		.content .front-page-1 .widget .textwidget a.button:hover,
		.content .front-page-1 .widget .textwidget a.button:focus,
		.front-page-1 button:hover,
		.front-page-1 button:focus,
		.front-page-1 input:hover[type="button"],
		.front-page-1 input:focus[type="button"],
		.front-page-1 input:hover[type="reset"],
		.front-page-1 input:focus[type="reset"],
		.front-page-1 input:hover[type="submit"],
		.front-page-1 input:focus[type="submit"],
		.front-page-1 .entry-content a.button:hover,
		.front-page-1 .entry-content a.button:focus,
		.genesis-nav-menu li.highlight > a:hover,
		.genesis-nav-menu li.highlight > a:focus,
		.archive-pagination li a {
			background-color:  %1$s;
		}' , $button_hover ) : '';


	/* Output the CSS */
	if( $css ){
		wp_add_inline_style( $handle, $css );
	}

}

/* Callback to sanitize checkbox setting */
function imagely_sanitize_checkbox( $checked ) {
	return ( ( isset( $checked ) && true == $checked ) ? true : false );
}

function imagely_sanitize_color( $color ) {

	trim( $color );

	if ( preg_match( '|^#([A-Fa-f0-9]{3}){1,2}$|' , $color ) ) {
        return $color; 
	} elseif ( preg_match( '/^rgba\((\d+),\s*(\d+),\s*(\d+)(?:,\s*(\d+(?:\.\d+)?))?\)$/' , $color ) ) {
        return $color;
    } else {
    	return '';
    }
   
}

 