<?php
/**
 * The template part for displaying the site header bottom navigation
 *
 * @package BoomBox_Theme
 * @since   2.0.0
 * @version 2.0.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

$template_data = Boombox_Template::get_clean( 'header_bottom', array() );
$template_data = wp_parse_args( $template_data, array(
	'menu_location' => 'bottom_header_nav',
	'include_more_menu' => false
) );

if ( $template_data[ 'menu_location' ] && has_nav_menu( $template_data[ 'menu_location' ] ) ) { ?>
	<nav class="main-navigation">
		<?php
		wp_nav_menu( array(
			'theme_location' => $template_data[ 'menu_location' ],
			'menu_class'     => '',
			'container'      => false,
			'walker'         => new Boombox_Walker_Nav_Menu_Custom_Fields(),
		) );
		?>
	</nav>
	<?php
	if ( $template_data[ 'include_more_menu' ] && ( boombox_get_theme_option( 'header_layout_more_menu_position' ) == 'bottom' ) ) {
		boombox_get_template_part( 'template-parts/header/components/more-menu-icons' );
	}
}