<?php
/**
 *  The template for displaying archive pages
 *
 * If you'd like to further customize these archive views, you may create a
 * new template file for each one. For example, tag.php (Tag archives),
 * category.php (Category archives), etc.
 *
 * @package BoomBox_Theme
 * @since   1.0.0
 * @version 2.6.7
 *
 * @var $template_helper Boombox_Archive_Template_Helper Template helper
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

get_header();

$template_helper  = Boombox_Template::init( 'archive' );
$template_options = $template_helper->get_options();

if ( $template_options['featured_strip'] ) {
	boombox_get_template_part( 'template-parts/featured', 'strip' );
}

boombox_the_advertisement( 'boombox-after-header', array(
	'class' => 'container large bb-after-header',
) );


if ( $template_options['title_area'] ) {
	boombox_get_template_part( 'template-parts/title/layout', Boombox_Template::init( 'title' )->get_layout() );
}

boombox_the_advertisement( 'boombox-archive-before-featured-area', array(
	'class' => 'container large bb-before-f-area'
) );

if ( $template_options['featured_area'] ) {
	boombox_get_template_part( 'template-parts/featured-area/' . Boombox_Template::init( 'featured-area' )->get_template() );
}

boombox_the_advertisement( 'boombox-archive-after-featured-area', array(
	'class' => 'container large bb-after-f-area'
) ); ?>

	<div class="container main-container <?php echo boombox_get_container_classes_by_type( $template_options['listing_type'] ); ?>">
		<?php
		if ( ! $template_options['title_area'] ) {
			the_archive_title( '<h1 class="bb-page-title mf-hide">', '</h1>' );
		}
		do_action( 'boombox/before_template_content', 'archive' );
		?>
		<div class="bb-row">
			<div class="bb-col col-content">
				<div class="bb-row">
					<div class="bb-col col-site-main">
						<div class="site-main" role="main">
							<?php
							boombox_the_advertisement( 'boombox-archive-before-content', array( 'class' => 'large bb-before-cnt-area' ) );
							Boombox_Loop_Helper::set_pagination_type( $template_options['pagination_type'] );

							if ( Boombox_Loop_Helper::have_posts() ) {
								do_action( 'boombox/loop-start', 'archive', array( 'listing_type' => $template_options['listing_type'] ) ); ?>
								<div class="bb-post-collection <?php echo boombox_get_list_type_classes( $template_options['listing_type'], array( 'col-2' ) ); ?>">
									<ul id="post-items" class="post-items">
										<?php
										while ( Boombox_Loop_Helper::have_posts() ) {
											$the_post = Boombox_Loop_Helper::the_post();
											if ( $the_post->is_injected && $the_post->is_adv ) {
												$adv_settings = boombox_get_adv_settings( $template_options['listing_type'] );
												boombox_the_advertisement( $adv_settings['location'], array(
													'tag'         => 'li',
													'in_the_loop' => true,
													'class'       => array( $adv_settings['class'], 'post-item' ),
												) );
											} elseif ( $the_post->is_injected && $the_post->is_newsletter ) {
												echo boombox_get_mailchimp_form_html( array(
													'tag'   => 'li',
													'class' => 'mb-md post-item'
												) );
											} elseif ( get_the_ID() ) {
												boombox_get_template_part( 'template-parts/listings/content-' . $template_options['listing_type'], get_post_format() );
											}
										} ?>
									</ul>
									<?php
									if ( 'none' != $template_options['pagination_type'] ) {
										boombox_get_template_part( 'template-parts/pagination/pagination', $template_options['pagination_type'] );
									} ?>
								</div>
								<?php
								do_action( 'boombox/loop-end', 'archive' );
							}
							wp_reset_query();

							boombox_the_advertisement( 'boombox-archive-after-content', array( 'class' => 'large bb-after-cnt-area' ) ); ?>
						</div>
					</div>

					<?php if ( $template_options['enable_secondary_sidebar'] ) {
						get_sidebar( 'secondary' );
					} ?>
				</div>
			</div>

			<?php if ( $template_options['enable_primary_sidebar'] ) {
				get_sidebar();
			} ?>
		</div>
		<?php do_action( 'boombox/after_template_content', 'archive' ); ?>

	</div>

<?php get_footer(); ?>