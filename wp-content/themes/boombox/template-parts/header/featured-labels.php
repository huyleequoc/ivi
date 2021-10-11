<?php
/**
 * The template part for displaying featured labels navigation
 *
 * @package BoomBox_Theme
 * @since   2.0.0
 * @version 2.0.0
 * @var $template_helper Boombox_Featured_Labels_Template_Helper Template Helper
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( has_nav_menu( 'featured_labels' ) ) {
	$menu_class                              = '';
	$has_transparency                        = 'has-transparency';
	$header_featured_labels_background_color = boombox_get_theme_option( 'header_featured_labels_background_color' );

	if ( '' == $header_featured_labels_background_color ) {
		$menu_class = $has_transparency;
	} elseif ( preg_match( '/(rgba\()(\d+,)(\d+,)(\d+,)(\d*[.]?\d+)/', $header_featured_labels_background_color, $matches ) ) {    //Case when labels "Background Color" have transparency
		//Case when labels "Background Color" transparency value is 0
		if ( 0 == end( $matches ) ) {
			$menu_class = $has_transparency;
		}
	}

	$template_helper  = Boombox_Template::init( 'featured-labels' );
	$template_options = $template_helper->get_options();

	if ( $template_options['is_visible'] ) {
		wp_nav_menu( array(
			'theme_location' => 'featured_labels',
			'menu_class'     => $menu_class,
			'container'      => false,
			'depth'          => 1,
			'items_wrap'     => '<div class="container bb-featured-menu bb-scroll-area bb-stretched-full no-gutters '
			                    . $template_options['class'] . '"><ul id="%1$s" class="%2$s">%3$s</ul></div>',
			'walker'         => new Boombox_Walker_Featured_Labels_Nav_Menu(),
		) );
	}
}