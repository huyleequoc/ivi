<?php
/**
 * The template part for displaying the site footer bottom section
 *
 * @package BoomBox_Theme
 * @since   1.0.0
 * @version 2.5.0
 *
 * @var $template_helper Boombox_Footer_Template_Helper Template helper
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

$template_helper = Boombox_Template::init( 'footer' );
$template_options = $template_helper->get_options(); ?>

<div class="footer-bottom">
	<?php
	if ( 'bottom' === $template_options[ 'pattern_position' ] ) {
		boombox_get_template_part( 'template-parts/footer/pattern' );
	} ?>

	<div class="container">

		<?php
		// Footer Navigation
		if ( has_nav_menu( 'footer_nav' ) ) { ?>
			<div class="footer-nav">
				<nav>
					<?php
					wp_nav_menu( array(
						'theme_location' => 'footer_nav',
						'menu_class'     => '',
						'depth'          => 1,
						'container'      => false,
					) );
					?>
				</nav>
			</div>
		<?php } ?>

		<?php //Footer Social
		if ( function_exists( 'boombox_get_social_links' ) && $template_options[ 'social_icons' ] ) { ?>
			<div class="social-footer social">
				<?php echo boombox_get_social_links(); ?>
			</div>
		<?php } ?>

		<div class="copy-right">&copy;
			<?php printf( __( '%1$s %2$s', 'boombox' ),
				date( 'Y' ),
				wp_kses_post( $template_options[ 'footer_text' ] )
			); ?>
		</div>

	</div>
</div>