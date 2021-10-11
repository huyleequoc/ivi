<?php
/**
 * The template part for displaying the site featured strip
 *
 * @package BoomBox_Theme
 * @since   1.0.0
 * @version 2.5.0
 *
 * @var $template_helper Boombox_Featured_Strip_Template_Helper Template helper
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

$template_helper = Boombox_Template::init( 'featured-strip' );
$template_options = $template_helper->get_options();
$template_query = $template_helper->get_query();
$featured_image_size = 'boombox_image200x150';

if ( $template_query != NULL && $template_query->found_posts ) { ?>
	<div class="<?php echo $template_options[ 'classes' ]; ?>">
		<ul>
			<?php while ( $template_query->have_posts() ) {
				$template_query->the_post();

				$boombox_strip_has_post_thumbnail = boombox_has_post_thumbnail();
				$boombox_strip_item_class = $boombox_strip_has_post_thumbnail ? '' : 'no-thumbnail';
				$boombox_strip_item_title = wp_trim_words( get_the_title(), 8, '...' ); ?>
				<li class="item <?php echo esc_attr( $boombox_strip_item_class ); ?>">
					<figure class="media">
						<a href="<?php echo esc_url( get_permalink() ); ?>">
							<?php
							/***** Thumbnail */
							if ( $boombox_strip_has_post_thumbnail ) {
								echo boombox_get_post_thumbnail( null, $featured_image_size );
							}

							/***** Post Format Badges */
							if( $template_options[ 'post_format_badges' ] ) {
								$badges = boombox_get_post_badge_list( array(
                                    'badges' => false,
                                    'post_type_badges_before' => '<div class="bb-post-format xs">',
                                    'post_type_badges_after'  => '</div>'
                                ) );
								echo $badges['post_type_badges'];
							}

							/***** Post title ( inside ) */
							if ( 'inside' == $template_options[ 'title_position' ] ) { ?>
								<span class="title-inside"><?php echo $boombox_strip_item_title; ?></span>
							<?php } ?>
						</a>
					</figure>
					<?php
					/***** Post title ( outside ) */
					if ( 'outside' == $template_options[ 'title_position' ] ) { ?>
						<h3 class="title">
							<a href="<?php echo esc_url( get_permalink() ); ?>"><?php echo $boombox_strip_item_title; ?></a>
						</h3>
					<?php } ?>
				</li>
			<?php } ?>
		</ul>
	</div>
<?php }
wp_reset_query(); ?>