<?php

/**
 * Template Name: Blog Mosaic
 **/

/* Add custom body class */
add_filter( 'body_class', 'imagely_mosaic_body_class' );
function imagely_mosaic_body_class( $classes ) {
	$classes[] = 'imagely-mosaic';
	return $classes;
}

/* Enqueue mosaic scripts */
add_action( 'wp_enqueue_scripts', 'imagely_mosaic_template_scripts' );
function imagely_mosaic_template_scripts() {
	// wp_enqueue_script( 'jmosaic', get_stylesheet_directory_uri() . '/js/jquery.jMosaic.js', array( 'jquery' ), '', true );
	// wp_enqueue_script( 'imagely-mosaic', get_stylesheet_directory_uri() . '/js/imagely-blog-mosaic.js', array( 'jquery', 'jmosaic' ), '', true );
	// wp_enqueue_style( 'imagely-mosaic-css', get_stylesheet_directory_uri() . '/js/jquery.jMosaic.css' , array(), PARENT_THEME_VERSION );
	wp_enqueue_script( 'isotope', get_stylesheet_directory_uri() . '/js/isotope.pkgd.js', array( 'jquery' ), '', true );
	wp_enqueue_script( 'imagely-mosaic', get_stylesheet_directory_uri() . '/js/imagely-mosaic.js', array( 'jquery', 'isotope' ), '', true );
	// wp_enqueue_script( 'freewall', get_stylesheet_directory_uri() . '/js/freewall.js', array( 'jquery' ), '', true );
	// wp_enqueue_script( 'imagely-mosaic-freewall', get_stylesheet_directory_uri() . '/js/imagely-mosaic-freewall.js', array( 'jquery', 'freewall' ), '', true );
	wp_enqueue_script( 'justified-js', get_stylesheet_directory_uri() . '/js/jquery.justified.js', array( 'jquery' ), '', true );
	wp_enqueue_script( 'imagely-justified', get_stylesheet_directory_uri() . '/js/imagely-justified.js', array( 'jquery', 'justified-js' ), '', true );
	wp_enqueue_style( 'justified-css', get_stylesheet_directory_uri() . '/js/jquery.justified.css' , array(), PARENT_THEME_VERSION );
}


/* Removes the sidebar by forcing full width layout */
add_filter( 'genesis_site_layout', '__genesis_return_full_width_content' );

/* Remove sidebar/content layout */
genesis_unregister_layout( 'sidebar-content' );

/* Reposition the entry meta in the entry header */
remove_action( 'genesis_entry_header', 'genesis_do_post_title' );
// add_action( 'genesis_entry_header', 'genesis_do_post_title', 13 );

/* Customize the entry meta in the entry header */
remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );
// add_action( 'genesis_entry_header', 'imagely_post_info', 12 );
function imagely_post_info() {
	echo '<p class="entry-meta">' . do_shortcode( '[post_date]' ) . '</p>';
}

/* Remove entry content */
remove_action( 'genesis_entry_content', 'genesis_do_post_content' );

/* Remove entry footer content */
remove_action( 'genesis_entry_footer', 'genesis_entry_footer_markup_open', 5 );
remove_action( 'genesis_entry_footer', 'genesis_entry_footer_markup_close', 15 );
remove_action( 'genesis_entry_footer', 'genesis_post_meta' );

/* Remove page navigation */
remove_action( 'genesis_entry_content', 'genesis_do_post_content_nav', 12 );

/* Display featured image */
remove_action( 'genesis_entry_content', 'genesis_do_post_image', 8 );
add_action( 'genesis_entry_header', 'imagely_mosaic_image', 3 );
function imagely_mosaic_image() {
	$image_args = array(
		'size' => 'large'
	);
	$image = genesis_get_image( $image_args );
	echo '<a rel="bookmark" href="'. get_permalink() .'">'. $image .'</a>';
}

/* Set up custom loop */
remove_action( 'genesis_loop', 'genesis_do_loop' );
add_action( 'genesis_loop', 'imagely_mosaic_loop' );
function imagely_mosaic_loop() {
	$include = genesis_get_option( 'blog_cat' );
	$exclude = genesis_get_option( 'blog_cat_exclude' ) ? explode( ',', str_replace( ' ', '', genesis_get_option( 'blog_cat_exclude' ) ) ) : '';
	$paged   = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
	$query_args = wp_parse_args(
		genesis_get_custom_field( 'query_args' ),
		array(
			'cat'              => $include,
			'category__not_in' => $exclude,
			'showposts'        => genesis_get_option( 'blog_cat_num' ),
			'paged'            => $paged,
		)
	);
	genesis_custom_loop( $query_args );
}








/* Remove breadcrumbs */
// remove_action( 'genesis_before_loop', 'genesis_do_breadcrumbs' );



/* Force content limit regardless of Content Archive theme settings */
// add_filter( 'genesis_pre_get_option_content_archive', 'imagely_show_full_content' );
// add_filter( 'genesis_pre_get_option_content_archive_limit', 'imagely_content_limit' );
// function imagely_show_full_content() {
// 	return 'full';
// }
// function imagely_content_limit() {
// 	return '100';
// }

/* Remove author and comment link */
// add_filter( 'genesis_post_info', 'imagely_post_info_filter' );
// function imagely_post_info_filter($post_info) {
// 	$post_info = '[post_date] [post_edit]';
// 	return $post_info;
// }

/* Display featured image */
// remove_action( 'genesis_entry_content', 'genesis_do_post_image', 8 );
// add_action( 'genesis_entry_header', 'imagely_mosaic_image', 9 );
// function imagely_mosaic_image() {
// 	$image_args = array(
// 		'size' => 'large'
// 	);
// 	$image = genesis_get_image( $image_args );
// 	echo '<a rel="bookmark" href="'. get_permalink() .'">'. $image .'</a>';
// }

/* Edit the read more link text */
// add_filter( 'excerpt_more' , 'imagely_read_more_link' );
// add_filter( 'get_the_content_more_link' , 'imagely_read_more_link' );
// add_filter( 'the_content_more_link' , 'imagely_read_more_link' );
// function imagely_read_more_link() {
// 	return '<a class="more-link" href="' . get_permalink() . '">' . __( 'Read More' , 'text-domain' ) .'</a>';
// }

/* Remove the entry footer content */
// remove_action( 'genesis_entry_footer', 'genesis_entry_footer_markup_open', 5 );
// remove_action( 'genesis_entry_footer', 'genesis_post_meta' );
// remove_action( 'genesis_entry_footer', 'genesis_entry_footer_markup_close', 15 );

/* Set up custom loop */
// remove_action( 'genesis_loop', 'genesis_do_loop' );
// add_action( 'genesis_loop', 'imagely_mosaic_loop' );
// function imagely_mosaic_loop() {
// 	$include = genesis_get_option( 'blog_cat' );
// 	$exclude = genesis_get_option( 'blog_cat_exclude' ) ? explode( ',', str_replace( ' ', '', genesis_get_option( 'blog_cat_exclude' ) ) ) : '';
// 	$paged   = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
// 	$query_args = wp_parse_args(
// 		genesis_get_custom_field( 'query_args' ),
// 		array(
// 			'cat'              => $include,
// 			'category__not_in' => $exclude,
// 			'showposts'        => genesis_get_option( 'blog_cat_num' ),
// 			'paged'            => $paged,
// 		)
// 	);
// 	genesis_custom_loop( $query_args );
// }

/* Run it all */
genesis();