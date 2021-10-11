<?php
/**
 *  The template for displaying author posts
 *
 * @package BoomBox_Theme
 * @since   1.0.0
 * @version 2.5.0
 * @var $template_helper Boombox_Author_Template_Helper Template Helper
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

get_header();

$template_helper = Boombox_Template::init( 'author' );
$template_options = $template_helper->get_options(); ?>

	<div class="container">
		<?php
		if ( have_posts() ) : the_post();
			echo boombox_get_post_author_card();
		endif;
		rewind_posts(); ?>
	</div>

	<div class="container main-container <?php echo boombox_get_container_classes_by_type( $template_options[ 'listing_type' ] ); ?>">

		<?php do_action( 'boombox/before_template_content', 'author' ); ?>
		<div class="bb-row">
			<div class="bb-col col-content">
				<div class="bb-row">
					<div class="bb-col col-site-main">
						<div class="site-main" role="main">
							<?php

							boombox_the_advertisement( 'boombox-archive-before-content', array( 'class' => 'large bb-before-cnt-area' ) );

							Boombox_Loop_Helper::set_pagination_type( $template_options[ 'pagination_type' ] );
							if ( Boombox_Loop_Helper::have_posts() ) {
							do_action( 'boombox/loop-start', 'author', array( 'listing_type' => $template_options[ 'listing_type' ] ) ); ?>
							<div class="bb-post-collection <?php echo boombox_get_list_type_classes( $template_options[ 'listing_type' ], array( 'col-2' ) ); ?>">
								<ul id="post-items" class="post-items">
									<?php
									while ( Boombox_Loop_Helper::have_posts() ) {
										$the_post = Boombox_Loop_Helper::the_post();
										if ( $the_post->is_injected && $the_post->is_adv ) {
											$adv_settings = boombox_get_adv_settings( $template_options[ 'listing_type' ] );
											boombox_the_advertisement( $adv_settings[ 'location' ], array(
												'tag' => 'li',
												'in_the_loop' => true,
												'class' => array( $adv_settings[ 'class' ], 'post-item' )
											) );
										} else if ( $the_post->is_injected && $the_post->is_newsletter ) {
											echo boombox_get_mailchimp_form_html( array( 'tag' => 'li', 'class' => 'mb-md post-item' ) );
										} else {
											boombox_get_template_part( 'template-parts/listings/content-' . $template_options[ 'listing_type' ], get_post_format() );
										}
									} ?>
								</ul>
								<?php
								if ( 'none' != $template_options[ 'pagination_type' ] ) {
									boombox_get_template_part( 'template-parts/pagination/pagination', $template_options[ 'pagination_type' ] );
								}
								do_action( 'boombox/loop-end', 'author' );
								}
								wp_reset_query();
								?>
							</div>
							<?php boombox_the_advertisement( 'boombox-archive-after-content', array( 'class' => 'large bb-after-cnt-area' ) ); ?>
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
		<?php do_action( 'boombox/after_template_content', 'author' ); ?>

	</div>

<?php get_footer(); ?>