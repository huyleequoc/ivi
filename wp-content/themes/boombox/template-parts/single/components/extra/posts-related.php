<?php
/**
 * The template part for displaying single post related entries.
 *
 * @package BoomBox_Theme
 * @since   1.0.0
 * @version 2.5.8.1
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}
$boombox_related_entries_per_page = boombox_get_theme_option( 'single_post_related_posts_related_entries_per_page' );
$boombox_related_entries_heading  = boombox_get_theme_option( 'single_post_related_posts_related_entries_heading' );
$boombox_related_listing_type     = apply_filters( 'boombox_related_listing_type', 'grid' );

$boombox_posts = boombox_get_related_posts_items( 'related', $boombox_related_entries_per_page );
if ( NULL != $boombox_posts && count( $boombox_posts->posts ) > 0 ) { ?>
	<aside class="bb-other-posts bb-posts-related bb-post-collection hfeed mb-xs bb-mb-el <?php echo boombox_get_list_type_classes( $boombox_related_listing_type, array( 'col-3' ) ); ?>">

		<?php if ( $boombox_related_entries_heading ) { ?>
			<h3 class="title"><?php esc_html_e( $boombox_related_entries_heading, 'boombox' ); ?></h3>
		<?php } ?>

		<?php do_action( 'boombox/loop-start', 'posts-related', array( 'listing_type' => $boombox_related_listing_type ) ); ?>
		<ul class="post-items">
			<?php while ( $boombox_posts->have_posts() ) {
				$boombox_posts->the_post();
				boombox_get_template_part( 'template-parts/listings/content-' . $boombox_related_listing_type, get_post_format() );
			}
			wp_reset_postdata(); ?>
		</ul>
		<?php do_action( 'boombox/loop-end', 'posts-related' ); ?>

	</aside>
	<?php boombox_the_advertisement( 'boombox-single-after-also-like-section', array( 'class' => 'large bb-after-also-like-sec', 'in_the_loop' => true ) );
}
wp_reset_query();