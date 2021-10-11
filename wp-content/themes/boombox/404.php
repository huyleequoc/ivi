<?php
/**
 * The template for displaying 404 page
 *
 * @package BoomBox_Theme
 * @since   1.0.0
 * @version 2.0.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

get_header();

$boombox_404_image = boombox_get_theme_option( 'branding_404_image' ); ?>

<div class="site-main" role="main">
	<section class="error-404 not-found">
		<div class="row">
			<div class="col-lg-5 col-md-5">
				<!-- page-header -->
				<header class="bb-page-header">
					<h1 class="page-title"><?php esc_html_e( 'Oops', 'boombox' ); ?></h1>
				</header>

				<!-- page-content -->
				<div class="page-content">
					<p class="text"><?php esc_html_e( 'We couldn\'t find the page you are looking for.', 'boombox' ); ?></p>
					<p class="text"><?php esc_html_e( 'It may have expired, or there could be a typo. Maybe you can find what you need from our homepage.', 'boombox' ); ?></p>
					<a class="btn-back bb-btn bb-btn-icon btn-sm icon-left" href="<?php echo esc_url( home_url( '/' ) ); ?>">
						<span class="bb-icon bb-ui-icon-long-arrow-left "></span><?php esc_html_e( 'Return To Home', 'boombox' ); ?>
					</a>
				</div>
			</div>
			<div class="col-lg-7 col-md-7 img-col">
				<?php if ( $boombox_404_image ): ?>
					<img src="<?php echo esc_url( $boombox_404_image ); ?>" alt="Page not found">
				<?php endif; ?>
			</div>
		</div>
	</section>
</div>

<?php get_footer(); ?>

<div class="head-bg">
	<div class="head-image-left"></div>
	<div class="headimage-right"></div>
</div>