<?php
/**
 * Template part to organize single post
 * @since 2.5.0
 * @version 2.5.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if( is_single() && have_posts() ) {
	the_post();

	$helper = Boombox_Template::init( 'post' );
	$options = $helper->get_options();

	if( $options[ 'side_navigation' ] ) {
	    get_template_part( 'template-parts/single/components/fixed/navigation' );
	}

    get_template_part( 'template-parts/single/components/fixed/floating-navbar' );
}
rewind_posts();
wp_reset_query();