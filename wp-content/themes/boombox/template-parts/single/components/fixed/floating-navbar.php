<?php
/**
 * Template part to render single post floating navigation bar section
 * @since 2.5.0
 * @version 2.5.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

$type = boombox_get_theme_option( 'single_post_general_floating_navbar' );
if ( ! in_array( $type, array( 'post_title', 'share_bar' ) ) ) {
	return;
} ?>
<div class="bb-sticky bb-floating-navbar bb-el-before-main-cnt">
	<div class="bb-sticky-el floating-navbar-inner">
		<div class="container">
			<div class="navbar-content">
				<div class="navbar-col navbar-col1">
					<?php if( 'post_title' == $type ) { ?>
					<div class="f-n-post-title-block">
						<h2 class="f-n-post-title"><?php echo wp_trim_words( get_the_title(), 10, '...' ) ?></h2>
					</div>
					<?php
					} elseif( 'share_bar' == $type ) {
						boombox_get_template_part( 'template-parts/single/components/share', '', array(
							'heading' => false,
							'before'  => '<div class="f-n-post-share-box bb-post-share-box">',
							'after'   => '</div>'
						) );
					} ?>
				</div>

				<?php
				if( boombox_get_theme_option( 'single_post_general_floating_navbar_navigation' ) ) {
					$nav_html = wp_link_pages( array(
						'layout'                          => 'page_xy',
						'class'                           => 'f-n-next-prev-pg pg-xs',
						'prev'                            => false,
						'next_prev_posts'                 => true,
						'hide_disable_inactive_next_prev' => 'hide',
						'echo'                            => 0
					) );
					if ( $nav_html ) { ?>
						<div class="navbar-col navbar-col2"><?php echo $nav_html; ?></div>
					<?php }
				} ?>
			</div>
		</div>
	</div>
</div>