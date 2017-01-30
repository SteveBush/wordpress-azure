<?php

/**
 * Template Name: No Sidebar
 **/

/* Removes the sidebar by forcing full width layout */
add_filter( 'genesis_pre_get_option_site_layout', '__genesis_return_full_width_content' );

/* Run it all */
genesis();