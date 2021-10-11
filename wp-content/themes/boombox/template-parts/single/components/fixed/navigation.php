<?php
/**
 * The template part for displaying the post fixed navigation
 *
 * @package BoomBox_Theme
 * @since   2.5.0
 * @version 2.5.6
 * @var $helper Boombox_Single_Post_Template_Helper Template helper
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

$helper = Boombox_Template::init( 'post' );
$nav_posts = $helper->get_post_next_prev_posts( 'fixed-navigation' );

$reverse = ( boombox_get_theme_option( 'single_post_general_navigation_direction' ) == 'to-oldest' );
$prev = $reverse ? $nav_posts[ 'next' ] : $nav_posts[ 'prev' ];
$next = $reverse ? $nav_posts[ 'prev' ] : $nav_posts[ 'next' ];
if( ! $prev && ! $next ) {
	return;
}

$elements = apply_filters( 'boombox/single_components/fixed/navigation/elements', array(
	'thumbnail' => true,
	'title'     => true,
	'author'    => true
) ); ?>

<nav class="bb-fixed-pagination bb-el-before-main-cnt hide">
	<ul>
		
		<?php if( $prev ) { ?>
		<li class="page prev-page">
			
			<?php $url = get_permalink( $prev->ID ); ?>
			<a class="pg-arrow" href="<?php echo esc_url( $url ); ?>">
				<span class="pg-arrow-icon"><i class="bb-icon bb-ui-icon-chevron-left"></i></span>
			</a>
			<a class="pg-link" href="<?php echo esc_url( $url ); ?>"></a>
			<div class="pg-content">
				
				<?php if ( $elements[ 'thumbnail' ] && boombox_has_post_thumbnail( $prev->ID ) ) { ?>
				<div class="pg-col pg-thumb-col">
					<div class="pg-thumb thumb-circle"><?php echo boombox_get_post_thumbnail( $prev, 'thumbnail' ); ?></div>
				</div>
				<?php } ?>
				
				<div class="pg-col pg-info-col">
					<?php if( $elements[ 'title' ] ) { ?>
					<h4 class="pg-title"><?php echo wp_trim_words( $prev->post_title, 10, '...' ); ?></h4>
					<?php }

					if( $elements[ 'author' ] ) {
						echo boombox_generate_user_mini_card( array(
							'user_id' => $prev->post_author,
							'author'  => true,
							'class'   => 'pg-author-vcard'
						) );
					} ?>
					
				</div>
			</div>
		</li>
		<?php } ?>
		
		<?php if( $next ) { ?>
		<li class="page next-page">
			
			<?php $url = get_permalink( $next->ID ); ?>
			<a class="pg-arrow" href="<?php echo esc_url( $url ); ?>">
				<span class="pg-arrow-icon"><i class="bb-icon bb-ui-icon-chevron-right"></i></span>
			</a>
			<a class="pg-link" href="<?php echo esc_url( $url ); ?>"></a>
			<div class="pg-content">
				
				<?php if ( $elements[ 'thumbnail' ] && boombox_has_post_thumbnail( $next->ID ) ) { ?>
				<div class="pg-col pg-thumb-col">
					<div class="pg-thumb thumb-circle"><?php echo boombox_get_post_thumbnail( $next, 'thumbnail' ); ?></div>
				</div>
				<?php } ?>
				
				<div class="pg-col pg-info-col">
					<?php if( $elements[ 'title' ] ) { ?>
					<h4 class="pg-title"><?php echo wp_trim_words( $next->post_title, 10, '...' ); ?></h4>
					<?php }

					if( $elements[ 'author' ] ) {
						echo boombox_generate_user_mini_card( array(
							'user_id' => $next->post_author,
							'author'  => true,
							'class'   => 'pg-author-vcard'
						) );
					} ?>
					
				</div>
				
			</div>
		</li>
		<?php } ?>
	
	</ul>
</nav>