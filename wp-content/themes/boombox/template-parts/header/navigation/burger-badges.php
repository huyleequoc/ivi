<?php
/**
 * The template part for displaying the site burger badges navigation
 *
 * @package BoomBox_Theme
 * @since   2.0.0
 * @version 2.0.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( has_nav_menu( 'burger_badges_nav' ) ) {
	wp_nav_menu( array(
		'theme_location' => 'burger_badges_nav',
		'menu_class'     => '',
		'container'      => false,
		'depth'          => 1,
		'items_wrap'     => '<div class="bb-badge-list"><ul>%3$s</ul></div>',
		'walker'         => new Boombox_Walker_Badges_Nav_Menu(),
	) );
}