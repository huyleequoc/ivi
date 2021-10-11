<?php
/**
 * The template part for displaying single post "More From" section.
 *
 * @package BoomBox_Theme
 * @since   1.0.0
 * @version 2.5.8.1
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

global $post;
$boombox_entries_per_page = boombox_get_theme_option( 'single_post_related_posts_more_entries_per_page' );
$boombox_entries_heading  = boombox_get_theme_option( 'single_post_related_posts_more_entries_heading' );
$boombox_listing_type     = apply_filters( 'boombox_more_from_listing_type', 'list' );

$boombox_post_first_category = boombox_get_post_first_category( $post );
if ( $boombox_post_first_category ) {
	$boombox_posts = boombox_get_more_from_posts_items( 'more_from', $boombox_post_first_category, $boombox_entries_per_page );
	if ( null != $boombox_posts && count( $boombox_posts->posts ) > 0 ) { ?>
		<aside class="bb-other-posts bb-posts-more-from bb-post-collection hfeed mb-lg bb-mb-el <?php echo boombox_get_list_type_classes( $boombox_listing_type, array( 'col-3' ) ); ?>">

			<?php if ( $boombox_entries_heading ) {
				$category_link = get_category_link( $boombox_post_first_category->term_id );
				$category_link = wp_kses_post( sprintf( '<a href="%1$s">%2$s</a>', esc_url( $category_link ), esc_html( $boombox_post_first_category->name ) ) ); ?>
				<h3 class="title"><?php echo esc_html__( $boombox_entries_heading, 'boombox' ) . ' ' . $category_link; ?></h3>
			<?php } ?>

			<?php do_action( 'boombox/loop-start', 'more-from', array( 'listing_type' => $boombox_listing_type ) ); ?>
			<ul class="post-items">
				<?php while ( $boombox_posts->have_posts() ) {
					$boombox_posts->the_post();
					boombox_get_template_part( 'template-parts/listings/content-' . $boombox_listing_type, get_post_format() );
				}
				wp_reset_postdata(); ?>
			</ul>
			<?php do_action( 'boombox/loop-end', 'more-from' ); ?>
		</aside>

		<?php boombox_the_advertisement( 'boombox-single-after-more-from-section', array( 'class' => 'large bb-after-more-from-sec' ) );
	}
}
wp_reset_query();