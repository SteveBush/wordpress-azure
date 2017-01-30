<?php

/* Start the engine */
include_once( get_template_directory() . '/lib/init.php' );

/* Include code to add post templates */
include_once( get_stylesheet_directory() . '/lib/customize.php' );

/* Include code to add post templates */
include_once( get_stylesheet_directory() . '/lib/post_templates.php' );

/* Set Localization */
load_child_theme_textdomain( 'imagely-sidewinder', apply_filters( 'child_theme_textdomain', get_stylesheet_directory() . '/languages', 'imagely-sidewinder' ) );

/* Child theme */
define( 'CHILD_THEME_NAME', 'Imagely Sidewinder' );
define( 'CHILD_THEME_URL', 'www.imagely.com/wordpress-photography-themes/sidewinder' );
define( 'CHILD_THEME_VERSION', '1.0.7' );
define( 'IMAGELY_FRONT_SLIDESHOW', false );
define( 'IMAGELY_BG_IMAGE', false );

/* Add HTML5 markup structure */
add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );

/* Add viewport meta tag for mobile browsers */
add_theme_support( 'genesis-responsive-viewport' );

/* Enqueue general scripts and fonts */
add_action( 'wp_enqueue_scripts', 'imagely_enqueue_scripts' );
function imagely_enqueue_scripts() {

	wp_enqueue_style( 'google-font', '//fonts.googleapis.com/css?family=Oswald:300,400,700|Droid+Serif:400,700|Open+Sans:400,300,600', array(), PARENT_THEME_VERSION );
	wp_enqueue_script( 'imagely-responsive-menu', get_stylesheet_directory_uri() . '/js/imagely-responsive-menu.js', array( 'jquery' ), '1.0.0' );
	wp_enqueue_style( 'font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css' );

}

if ( IMAGELY_BG_IMAGE ) {

	/* Add support for custom background image */
	$bg_defaults = array(
		'default-color'          => '#f7f7f7',
		'default-image'          => sprintf( '%s/images/background.jpg', get_stylesheet_directory_uri() ),
		'default-repeat'         => 'no-repeat',
		'default-position-x'     => 'left',
		'default-attachment'     => 'fixed',
	);
	add_theme_support( 'custom-background', $bg_defaults );

} 

/* Add support for custom header */
add_theme_support( 'custom-header', array(
	'width'           => 800,
	'height'          => 300,
	'header-selector' => '.site-title a',
	'header-text'     => false,
	'flex-height'     => true,
) );

/* Unregister layout settings */
genesis_unregister_layout( 'sidebar-content' );
genesis_unregister_layout( 'content-sidebar-sidebar' );
genesis_unregister_layout( 'sidebar-sidebar-content' );
genesis_unregister_layout( 'sidebar-content-sidebar' );

/* Rename Header Right Sidebar to Side Header */
unregister_sidebar( 'header-right' );
genesis_register_sidebar( array( 'id' => 'header-right', 'name' => 'Side Header' ) );

/* Unregister primary/secondary navigation menus */
remove_theme_support( 'genesis-menus' );

/* Add Imagely featured image sizes */
add_image_size( 'imagely-featured', 1280, 640, TRUE );
add_image_size( 'imagely-square', 640, 640, TRUE );

/* Unregister secondary sidebar */
unregister_sidebar( 'sidebar-alt' );

/* Modify the size of the Gravatar in the author box */
add_filter( 'genesis_author_box_gravatar_size', 'imagely_author_box_gravatar' );
function imagely_author_box_gravatar( $size ) {

	return 140;

}

/* Modify the size of the Gravatar in the entry comments */
add_filter( 'genesis_comment_list_args', 'imagely_comments_gravatar' );
function imagely_comments_gravatar( $args ) {

	$args['avatar_size'] = 96;

	return $args;

}

/* Remove comment form allowed tags */
add_filter( 'comment_form_defaults', 'imagely_remove_comment_form_allowed_tags' );
function imagely_remove_comment_form_allowed_tags( $defaults ) {
	
	$defaults['comment_notes_after'] = '';
	return $defaults;

}

/* Relocate footer widgets to be below content sidebar wrap */
remove_action( 'genesis_before_footer', 'genesis_footer_widget_areas' );
add_action( 'genesis_after_content_sidebar_wrap', 'genesis_footer_widget_areas' );

/* Add support for 3-column footer widgets */
add_theme_support( 'genesis-footer-widgets', 3 );

/* Relocate footer info div to be below content sidebar wrap */
remove_action( 'genesis_footer', 'genesis_footer_markup_open', 5 );
remove_action( 'genesis_footer', 'genesis_do_footer' );
remove_action( 'genesis_footer', 'genesis_footer_markup_close', 15 );
add_action( 'genesis_after_content_sidebar_wrap', 'genesis_footer_markup_open', 11 );
add_action( 'genesis_after_content_sidebar_wrap', 'genesis_do_footer', 12 );
add_action( 'genesis_after_content_sidebar_wrap', 'genesis_footer_markup_close', 13 );

/* Add markup for after post widget after the entry content */
add_action( 'genesis_after_entry', 'imagely_after_post', 5 );
function imagely_after_post() {

	if ( is_singular( 'post' ) )
		genesis_widget_area( 'after-post', array(
			'before' => '<div class="after-post" class="widget-area">',
			'after'  => '</div>',
		) );

}

/* Register widget areas */
genesis_register_sidebar( array(
	'id'          => 'after-post',
	'name'        => __( 'After Post', 'imagely-sidewinder' ),
	'description' => __( 'This widget area appears after the content and before the footer on all blog posts. It is often used to highlight important products or brand messages to readers of your blog posts.', 'imagely-sidewinder' ),
) );

/* Set defaults for Genesis themes settings */
add_filter( 'genesis_theme_settings_defaults', 'imagely_theme_defaults' );
function imagely_theme_defaults( $defaults ) {
	$defaults['blog_cat_num']              	= 10;
	$defaults['content_archive']           	= 'full';
	$defaults['content_archive_limit']     	= 150;
	$defaults['content_archive_thumbnail'] 	= 1;
	$defaults['image_size']					= 'imagely-featured';
	$defaults['image_alignment']			= '';
	$defaults['posts_nav']                 	= 'numeric';
	$defaults['site_layout']               	= 'full-width-content';
	$defaults['comments_pages']				= 1;
	return $defaults;
}

/* Customize the footer copyright text */
add_action( 'genesis_footer_output', 'imagely_custom_footer', 9 );
function imagely_custom_footer( $output ) {

	$powered_by_imagely = get_theme_mod( 'powered_by_imagely', true );
	
	$output = '<p>&copy; ' . date('Y') . ' &middot; <a href="' . esc_url( home_url( '/' )) . '" rel="home">' . get_bloginfo( 'name' ) . '</a>';

	if ( $powered_by_imagely ) {
		$output .= ' &middot; ' . __( 'Powered by', 'imagely-sidewinder') . ' <a href="http://www.imagely.com/" rel="nofollow">Imagely</a></p>';
	} else {
		$output .= '</p>';
	}

	return $output;
}
 

add_filter( 'genesis_theme_settings_defaults', 'sidewinder_theme_defaults', 15);
function sidewinder_theme_defaults( $defaults ) {
	$defaults['blog_cat_num']              	= 9;
	$defaults['site_layout']               	= 'content-sidebar';
	return $defaults;
}