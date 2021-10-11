<?php
/**
 * The template part for displaying the site footer top section
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

<div class="footer-top">
	<?php
	// Pattern
	if ( 'top' === $template_options[ 'pattern_position' ] ) {
		boombox_get_template_part( 'template-parts/footer/pattern' );
	} ?>

	<div class="container">

		<?php
		// Featured Strip
		if ( $template_options[ 'featured_strip' ] ) {
			boombox_get_template_part( 'template-parts/footer/featured', 'strip' );
		} ?>

		<?php
		// Widgets
		if ( is_active_sidebar( 'footer-left-widgets' ) || is_active_sidebar( 'footer-middle-widgets' ) || is_active_sidebar( 'footer-right-widgets' ) ) : ?>
			<div class="row">
				<div class="col-md-4 mb-md">
					<?php
					if ( is_active_sidebar( 'footer-left-widgets' ) ) :
						dynamic_sidebar( 'footer-left-widgets' );
					endif;
					?>
				</div>
				<div class="col-md-4 mb-md">
					<?php
					if ( is_active_sidebar( 'footer-middle-widgets' ) ) :
						dynamic_sidebar( 'footer-middle-widgets' );
					endif;
					?>
				</div>
				<div class="col-md-4 mb-md">
					<?php
					if ( is_active_sidebar( 'footer-right-widgets' ) ) :
						dynamic_sidebar( 'footer-right-widgets' );
					endif;
					?>
				</div>
			</div>
		<?php endif; ?>
	</div>
</div>