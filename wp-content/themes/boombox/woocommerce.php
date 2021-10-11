<?php
/**
 * The template for displaying the page with right sidebar
 *
 * @package BoomBox_Theme
 * @since   1.0.0
 * @version 2.5.0
 *
 * @var $template_helper Boombox_Woocommerce_Template_Helper Template Helper
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

get_header();

$template_helper = Boombox_Template::init( 'woocommerce' );
$template_options = $template_helper->get_options(); ?>

	<div class="container main-container">

		<div class="bb-row">
			<div class="bb-col col-content">
				<div class="bb-row">
					<div class="bb-col col-site-main">
						<div class="site-main" role="main">
							<?php
							boombox_the_advertisement( 'boombox-page-before-content', array( 'class'  => 'large bb-before-cnt-area' ) );

							boombox_get_template_part( 'template-parts/listings/content', 'woocommerce' );

							boombox_the_advertisement( 'boombox-page-after-content', array( 'class' => 'large bb-after-cnt-area' ) ); ?>
						</div>
					</div>

					<?php if ( $template_options[ 'enable_secondary_sidebar' ] ) {
						get_sidebar( 'secondary' );
					} ?>
				</div>

			</div>

			<?php if ( $template_options[ 'enable_primary_sidebar' ] ) {
				get_sidebar();
			} ?>
		</div>

	</div>

<?php get_footer(); ?>