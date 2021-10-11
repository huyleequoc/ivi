<?php
/**
 * The template part for displaying the site badges navigation
 *
 * @package BoomBox_Theme
 * @since   2.0.0
 * @version 2.0.0
 *
 * @var $header_helper Boombox_Header_Template_Helper Header Template Helper
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( has_nav_menu( 'badges_nav' ) ) {

	$header_helper = Boombox_Template::init( 'header' );

	wp_nav_menu( array(
		'theme_location' => 'badges_nav',
		'menu_class'     => '',
		'container'      => false,
		'depth'          => 1,
		'items_wrap'     => '<div class="header-item bb-badge-list pos-' . $header_helper->get_component_location() . '"><ul>%3$s</ul></div>',
		'walker'         => new Boombox_Walker_Badges_Nav_Menu(),
	) );
}