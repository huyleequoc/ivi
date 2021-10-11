<?php
/**
 * The template for displaying the header
 *
 * Displays all of the head element and everything up until the "main" div.
 *
 * @package BoomBox_Theme
 * @since 1.0.0
 * @version 2.5.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

?>
<!DOCTYPE HTML>
<html <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
		<?php if ( is_singular() && pings_open( get_queried_object() ) ) : ?>
			<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
		<?php endif; ?>
		<?php wp_head(); ?>
	</head>

	<body <?php body_class(); ?>>

		<?php
			do_action( 'boombox/body_start' );

			boombox_get_template_part( 'template-parts/header/navigation/mobile' );
			boombox_get_template_part( 'template-parts/header/background', 'image' ); ?>

		<div id="page-wrapper" class="page-wrapper">

			<?php
				boombox_get_template_part( 'template-parts/header/ad');
				boombox_get_template_part('template-parts/header/bootstrap' );
				boombox_get_template_part('template-parts/single/components/fixed/bootstrap' ); ?>

			<main id="main" role="main">

				<?php
					boombox_get_template_part( 'template-parts/header/featured', 'labels' );

					if( ( is_home() || is_front_page() )
						&& ( 'outside' == boombox_get_theme_option( 'header_layout_badges_position' ) )
					) { ?>

					<div class="container bb-top-badge-list bb-scroll-area bb-stretched-full no-gutters">
						<?php boombox_get_template_part( 'template-parts/header/navigation/badges' ); ?>
					</div>

				<?php } ?>