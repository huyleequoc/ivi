<?php
/**
 * The template part for displaying the site burger top navigation
 *
 * @package BoomBox_Theme
 * @since   2.0.0
 * @version 2.0.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( has_nav_menu( 'burger_top_nav' ) ) { ?>
	<nav class="bb-trending-navigation">
		<?php
		wp_nav_menu( array(
			'theme_location' => 'burger_top_nav',
			'menu_class'     => '',
			'depth'          => 1,
			'container'      => false,
			'walker'         => new Boombox_Walker_Nav_Menu_Custom_Fields(),
		) ); ?>
	</nav>
<?php } ?>