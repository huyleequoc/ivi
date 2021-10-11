<?php
/**
 * The template part for displaying the footer.
 *
 * @package BoomBox_Theme
 * @since   1.0.0
 * @version 2.5.7
 *
 * @var $template_helper Boombox_Footer_Template_Helper Template Helper
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

$template_helper = Boombox_Template::init( 'footer' );
$template_options = $template_helper->get_options(); ?>
			<!--Div for sticky elements -->
			<div id="sticky-border"></div>
			</main>

			<?php

			boombox_get_template_part( 'template-parts/footer/ad' );

			/* Footer */
			if( $template_options[ 'footer_top' ] || $template_options[ 'footer_bottom' ] ) { ?>
				<footer id="footer" class="footer <?php echo esc_attr( $template_options['classes'] ); ?>">
					<?php
					if( $template_options[ 'footer_top' ] ) {
						boombox_get_template_part( 'template-parts/footer/footer', 'top' );
					}
					if( $template_options[ 'footer_bottom' ] ) {
						boombox_get_template_part( 'template-parts/footer/footer', 'bottom' );
					} ?>
				</footer>
			<?php }

			$device = wp_is_mobile() ? 'mobile' : 'desktop';
			boombox_the_advertisement( 'boombox-sticky-bottom-' . $device . '-area', array(
				'class' => 'large bb-sticky-bottom-area bb-sticky-btm',
			) ); ?>

        <span id="go-top" class="go-top"><i class="bb-icon bb-ui-icon-arrow-up"></i></span>
		</div>

		<?php

		do_action( 'boombox/after-footer' );

		wp_footer(); ?>
	</body>
</html>