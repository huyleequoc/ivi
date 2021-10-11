<?php
/**
 * The template part for displaying the site burger bottom navigation
 *
 * @package BoomBox_Theme
 * @since   2.0.0
 * @version 2.0.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

$template_data = Boombox_Template::get_clean( 'burger_bottom', array() );
$template_data = wp_parse_args( $template_data, array(
	'menu_location' => 'burger_bottom_nav'
) );

if ( $template_data[ 'menu_location' ] && has_nav_menu( $template_data[ 'menu_location' ] ) ) { ?>
	<div class="more-menu-body">
		<span class="sections-header"><?php esc_html_e( 'sections', 'boombox' ) ?></span>
		<nav class="section-navigation">
			<?php
			wp_nav_menu( array(
				'theme_location' => $template_data[ 'menu_location' ],
				'menu_class'     => '',
				'depth'          => 1,
				'container'      => false,
			) ); ?>
		</nav>
	</div>
<?php }