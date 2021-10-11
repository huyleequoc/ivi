<?php
/**
 * The template for displaying the attachment
 *
 * @package BoomBox_Theme
 * @since   1.0.0
 * @version 2.6.9
 * @var $template_helper Boombox_Single_Post_Template_Helper Template Helper
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

get_header();

$image_size = 'boombox_image768';
$helper     = Boombox_Template::init( 'post' );
$options    = $helper->get_options( $image_size );

$bb_have_posts = have_posts();
if ( $bb_have_posts ) {
	the_post();
}

$media_el_style = '';
if ( $options['featured_image_src'] ) {
	$media_el_style = ' style="background-image: url(' . $options['featured_image_src'] . ');"';
}

?>

	<div class="single-container s-mt-sm">

		<?php do_action( 'boombox/before_template_content', 'attachment' ); ?>

		<article id="post-<?php the_ID(); ?>" <?php post_class( $options['classes'] ); ?> <?php boombox_single_article_structured_data(); ?>>

			<?php if ( $bb_have_posts ) { ?>

				<div class="single-top-container s-mb-md">

					<?php
					// Post Breadcrumb
					if ( $options['elements']['breadcrumb'] ) {
						boombox_get_template_part( 'template-parts/breadcrumb', '', array(
							'before' => '<nav class="s-post-breadcrumb container bb-breadcrumb mb-xs bb-mb-el clr-style1">',
							'after'  => '</nav>'
						) );
					}

					// Post Featured Media
					if ( ! empty( $options['featured_image'] ) ) { ?>

						<div class="s-post-featured-media container boxed">
							<div class="featured-media-el"<?php echo $media_el_style; ?>>
								<?php
								// Post Image
								boombox_get_template_part( 'template-parts/single/components/media/thumbnail' );
								?>
							</div>
						</div>

					<?php } ?>

				</div>

			<?php } ?>

			<div class="single-main-container container">
				<div class="bb-row">
					<div class="bb-col col-content">
						<div class="bb-row">
							<div class="bb-col col-site-main">
								<div class="site-main" role="main">

									<?php
									if ( $bb_have_posts ) {
										// Post Main Content for Card View ?>
										<div class="s-post-main mb-md bb-mb-el bb-card-item">

											<?php // Post Header ?>
											<header class="s-post-header entry-header bb-mb-el">

												<?php
												// Post title
												boombox_get_template_part( 'template-parts/single/components/title' );
												?>

											</header>

											<?php // Post Meta ?>
											<div class="s-post-meta-block bb-mb-el">
												<div class="post-meta-content row">
													<div class="d-table-center-sm">

														<?php
														// Post author mini card
														boombox_get_template_part( 'template-parts/single/components/mini-card', '', array(
															'author' => $options['elements']['author'],
															'avatar' => $options['elements']['author'],
															'date'   => $options['elements']['date'],
															'before' => '<div class="col-l d-table-cell col-md-6 col-sm-6 text-left-sm">',
															'after'  => '</div>',
														) );
														?>

													</div>
												</div>
											</div>

											<?php
											//Attached file
											boombox_get_template_part( 'template-parts/single/components/media/attachment', '', array(
												'image_size' => $image_size
											) );

											// Post Main Content
											boombox_get_template_part( 'template-parts/single/components/content', '', array(
												'protect_content'       => $options['protect_content'],
												'pagination_layout'     => $options['pagination_layout'],
												'has_secondary_sidebar' => $options['enable_secondary_sidebar']
											) );
											?>

										</div>

										<?php // -/end Post Main Content for Card View ?>

									<?php } ?>

								</div>
							</div>

							<?php
							if ( $options['enable_secondary_sidebar'] ) {
								get_sidebar( 'secondary' );
							}
							?>

						</div>
					</div>

					<?php
					if ( $options['enable_primary_sidebar'] ) {
						get_sidebar();
					}
					?>

				</div>
			</div>
		</article>

		<?php do_action( 'boombox/after_template_content', 'attachment' ); ?>

	</div>

<?php get_footer();