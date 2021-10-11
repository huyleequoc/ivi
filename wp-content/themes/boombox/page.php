<?php
/**
 * The template for displaying the page with right sidebar
 *
 * @package BoomBox_Theme
 * @since   1.0.0
 * @version 2.5.0
 * @var $template_helper Boombox_Page_Template_Helper Template helper
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

get_header();

$template_helper = Boombox_Template::init( 'page' );
$template_options = $template_helper->get_options();

if ( $template_options[ 'featured_strip' ] ) {
	boombox_get_template_part( 'template-parts/featured', 'strip' );
}

boombox_the_advertisement( 'boombox-after-header', array(
	'class' => 'container large bb-after-header',
) );

if ( $template_options[ 'title_area' ] ) {
	boombox_get_template_part( 'template-parts/title/layout', Boombox_Template::init( 'title' )->get_layout() );
}

boombox_the_advertisement( 'boombox-page-before-featured-area', array(
	'class'  => 'container large bb-before-f-area'
) );

if ( $template_options[ 'featured_area' ] ) {
	boombox_get_template_part( 'template-parts/featured-area/' . Boombox_Template::init( 'featured-area' )->get_template() );
}

boombox_the_advertisement( 'boombox-page-after-featured-area', array(
	'class'  => 'container large bb-after-f-area'
) ); ?>

<div class="container main-container <?php echo boombox_get_container_classes_by_type( $template_options[ 'listing_type' ] ); ?>">

	<?php if ( ! $template_options[ 'title_area' ] && $template_options[ 'hidden_seo_title' ] ) {
		boombox_render_hidden_seo_title();
	}

	do_action( 'boombox/before_template_content', 'page' ); ?>

	<div class="bb-row">
		<div class="bb-col col-content">
			<div class="bb-row">
				<div class="bb-col col-site-main">
					<div class="site-main" role="main">
						<?php

						boombox_the_advertisement( 'boombox-page-before-content', array( 'class'  => 'large bb-before-cnt-area' ) );

						if ( empty( $template_options[ 'listing_type' ] ) || 'none' == $template_options[ 'listing_type' ] ) {
							if ( have_posts() ) {
								the_post();
								boombox_get_template_part( 'template-parts/content', 'page' );

								// If comments are open or we have at least one comment, load up the comment template.
								if ( comments_open() || get_comments_number() ) {
									comments_template();
								}
							}

						} else if ( NULL != $template_options[ 'query' ] ) {

							if ( have_posts() ) {
								the_post();
								boombox_get_template_part( 'template-parts/content', 'page' );
							}

							global $wp_query;
							$tmp_query = $wp_query;
							$wp_query = $template_options[ 'query' ];

							Boombox_Loop_Helper::set_pagination_type( $template_options[ 'pagination_type' ] );
							if ( Boombox_Loop_Helper::have_posts() ) {
								do_action( 'boombox/loop-start', 'page', array( 'listing_type' => $template_options[ 'listing_type' ] ) ); ?>
								<div class="bb-post-collection <?php echo boombox_get_list_type_classes( $template_options[ 'listing_type' ], array( 'col-2' ) ); ?>">
									<ul id="post-items" class="post-items">
										<?php
										while ( Boombox_Loop_Helper::have_posts() ) {
											$the_post = Boombox_Loop_Helper::the_post();
											if ( $the_post->is_injected && $the_post->is_adv ) {
												$adv_settings = boombox_get_adv_settings( $template_options[ 'listing_type' ] );
												boombox_the_advertisement( $adv_settings[ 'location' ], array(
													'tag'       => 'li',
													'in_the_loop' => true,
													'class'     => array( $adv_settings[ 'class' ], 'post-item' ),
													'tmp_query' => $tmp_query,
													'cur_query' => $wp_query,
												) );
											} else if ( $the_post->is_injected && $the_post->is_newsletter ) {
												echo boombox_get_mailchimp_form_html( array( 'tag' => 'li', 'class' => 'mb-md post-item' ) );
											} else if ( get_the_ID() ) {
												boombox_get_template_part( 'template-parts/listings/content-' . $template_options[ 'listing_type' ], get_post_format() );
											}
										} ?>
									</ul>
								<?php
								if ( 'none' != $template_options[ 'pagination_type' ] ) {
									boombox_get_template_part( 'template-parts/pagination/pagination', $template_options[ 'pagination_type' ] );
								} ?>
								</div>
								<?php
								do_action( 'boombox/loop-end', 'page' );
							}
							wp_reset_query();
						}

						boombox_the_advertisement( 'boombox-page-after-content', array( 'class' => 'large bb-after-cnt-area', 'in_the_loop' => true ) ); ?>
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

	<?php do_action( 'boombox/after_template_content', 'page' ); ?>

</div>

<?php get_footer(); ?>