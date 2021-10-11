<?php
/**
 * The template part for displaying the post navigation
 *
 * @package BoomBox_Theme
 * @since   2.5.0
 * @version 2.5.0
 * @var $helper Boombox_Single_Post_Template_Helper Template Helper
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

$helper = Boombox_Template::init( 'post' );
$nav_posts = $helper->get_post_next_prev_posts( 'navigation' );

$reverse = ( boombox_get_theme_option( 'single_post_general_navigation_direction' ) == 'to-oldest' );
$prev = $reverse ? $nav_posts[ 'next' ] : $nav_posts[ 'prev' ];
$next = $reverse ? $nav_posts[ 'prev' ] : $nav_posts[ 'next' ];

if( ! $prev && ! $next ) {
	return;
} ?>
<nav class="bb-post-nav s-post-small-el bb-mb-el bb-no-play">
	<div class="row">
		<ul>
			<?php if ( ! empty( $prev ) ) { ?>
				<li class="prev-page col-xs-12 col-md-6 col-lg-6">
					<span class="meta-nav"><?php esc_html_e( 'Previous Post', 'boombox' ); ?></span>

					<div class="pg-item bb-card-item">

						<?php if ( boombox_has_post_thumbnail( $prev->ID ) ) { ?>
							<div class="pg-thumb-col pg-col">
								<a class="pg-thumb" href="<?php echo esc_url( get_permalink( $prev->ID ) ); ?>">
									<?php echo boombox_get_post_thumbnail( $prev, 'thumbnail' ); ?>
								</a>
							</div>
						<?php } ?>

						<div class="pg-content-col pg-col">
							<h6 class="post-title">
								<a href="<?php echo esc_url( get_permalink( $prev->ID ) ); ?>"><?php echo wp_trim_words( $prev->post_title, 10, '...' ); ?></a>
							</h6>
							<?php echo boombox_generate_user_mini_card( array(
								'user_id' => $prev->post_author,
								'author'  => true,
								'class'   => 'post-author-vcard'
							) ); ?>
						</div>
					</div>
				</li>
			<?php }

			if ( ! empty( $next ) ) { ?>
				<li class="next-page col-xs-12 col-md-6 col-lg-6">
					<span class="meta-nav"><?php esc_html_e( 'Next Post', 'boombox' ); ?></span>

					<div class="pg-item bb-card-item">

						<?php if ( boombox_has_post_thumbnail( $next->ID ) ) { ?>
							<div class="pg-thumb-col pg-col">
								<a class="pg-thumb" href="<?php echo esc_url( get_permalink( $next->ID ) ); ?>">
									<?php echo boombox_get_post_thumbnail( $next, 'thumbnail' ); ?>
								</a>
							</div>
						<?php } ?>

						<div class="pg-content-col pg-col">
							<h6 class="post-title">
								<a href="<?php echo esc_url( get_permalink( $next->ID ) ); ?>"><?php echo wp_trim_words( $next->post_title, 10, '...' ); ?></a>
							</h6>
							<?php echo boombox_generate_user_mini_card( array(
								'user_id' => $next->post_author,
								'author'  => true,
								'class'   => 'post-author-vcard'
							) ); ?>
						</div>
					</div>
				</li>
			<?php } ?>

		</ul>
	</div>
</nav>