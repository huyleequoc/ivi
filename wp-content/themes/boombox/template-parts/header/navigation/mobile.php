<?php
/**
 * The template part for displaying the site mobile navigation
 *
 * @package BoomBox_Theme
 * @since   2.0.0
 * @version 2.1.2
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

$template_helper = Boombox_Template::init( 'header' );
if ( $template_helper->has_component( 'burger-icon' ) ) {
	$badges_position = boombox_get_theme_option( 'header_layout_badges_position' ); ?>

	<div id="mobile-nav-bg"></div>
	<div id="mobile-navigation" class="bb-mobile-navigation">
		<button id="menu-close" class="close">
			<i class="bb-icon bb-ui-icon-close"></i>
		</button>
		<div class="holder">
			<div class="more-menu">
				<div class="more-menu-header">
					<?php boombox_get_template_part( 'template-parts/header/search', 'box' ); ?>
				</div>
				<?php
				Boombox_Template::set( 'header_top', array(
					'menu_location' => 'burger_mobile_menu_1'
				) );
				boombox_get_template_part( 'template-parts/header/navigation/header', 'top' );


				Boombox_Template::set( 'header_bottom', array(
					'menu_location' => 'burger_mobile_menu_2'
				) );
				boombox_get_template_part( 'template-parts/header/navigation/header', 'bottom' );


				Boombox_Template::set( 'burger_bottom', array(
					'menu_location' => 'burger_mobile_menu_3'
				) );
				boombox_get_template_part( 'template-parts/header/navigation/burger', 'bottom' );

				if ( ( $badges_position == 'inside' ) && $template_helper->has_component( 'badges' ) ) {
					boombox_get_template_part( 'template-parts/header/navigation/badges' );
				} ?>

				<div class="more-menu-footer">

					<?php
					boombox_get_template_part( 'template-parts/header/community' );

					if ( function_exists( 'boombox_get_social_links' ) && ! apply_filters( 'boombox/header/navigation_mobile/disable_socials', false ) ) { ?>

						<div class="social circle"><?php echo boombox_get_social_links(); ?></div>

					<?php } ?>

				</div>
			</div>

		</div>
	</div>
	<?php
} ?>