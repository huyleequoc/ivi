<?php
/**
 * The template part for displaying the site footer featured-strip
 *
 * @package BoomBox_Theme
 * @since   1.0.0
 * @version 2.0.0
 *
 * @var $template_helper Boombox_Featured_Strip_Template_Helper Template helper
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

$template_helper = Boombox_Template::init( 'featured-strip' );
$template_query = $template_helper->get_footer_query();

if ( $template_query != NULL && $template_query->found_posts ) { ?>
	<?php do_action( 'boombox/before_footer_strip' ); ?>
	<div class="bb-featured-strip featured-carousel big-item outside-title mb-md">
		<ul>
			<?php while ( $template_query->have_posts() ): $template_query->the_post();
				$boombox_strip_has_post_thumbnail = boombox_has_post_thumbnail();
				$boombox_strip_item_class = $boombox_strip_has_post_thumbnail ? '' : 'no-thumbnail'; ?>
				<li class="item <?php echo esc_attr( $boombox_strip_item_class ); ?>">
					<figure class="media">
						<a href="<?php echo esc_url( get_permalink() ); ?>">
							<?php
							if ( $boombox_strip_has_post_thumbnail ):
								echo boombox_get_post_thumbnail( null, 'boombox_image200x150' );
							endif; ?>
							<span class="title-inside"><?php echo wp_trim_words( get_the_title(), 8, '...' ); ?></span>
						</a>
					</figure>
					<h3 class="title">
						<a href="<?php echo esc_url( get_permalink() ); ?>">
							<?php echo wp_trim_words( get_the_title(), 8, '...' ); ?>
						</a>
					</h3>
				</li>
			<?php endwhile; ?>
		</ul>
	</div>
	<?php do_action( 'boombox/after_footer_strip' ); ?>
<?php }
wp_reset_postdata(); ?>