<?php
/**
 * The template part for displaying single post "Don't Miss" section.
 *
 * @package BoomBox_Theme
 * @since   1.0.0
 * @version 2.5.8.1
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
    die( 'No direct script access allowed' );
}

$boombox_entries_per_page = boombox_get_theme_option( 'single_post_related_posts_dont_miss_entries_per_page' );
$boombox_entries_heading  = boombox_get_theme_option( 'single_post_related_posts_dont_miss_entries_heading' );
$boombox_listing_type     = apply_filters( 'boombox_dont_miss_listing_type', 'grid' );

$boombox_posts = boombox_get_dont_miss_posts_items( 'dont_miss', $boombox_entries_per_page );
if ( null != $boombox_posts && count( $boombox_posts->posts ) > 0 ) { ?>
    <aside class="bb-other-posts bb-posts-dont-miss bb-post-collection hfeed mb-xs bb-mb-el <?php echo boombox_get_list_type_classes( $boombox_listing_type, array( 'col-3' ) ); ?>">

        <?php if ( $boombox_entries_heading ) { ?>
            <h2 class="title"><?php esc_html_e( $boombox_entries_heading, 'boombox' ); ?></h2>
        <?php } ?>

        <?php do_action( 'boombox/loop-start', 'dont-miss', array( 'listing_type' => $boombox_listing_type ) ); ?>
        <ul class="post-items">
            <?php while ( $boombox_posts->have_posts() ) {
                $boombox_posts->the_post();
                boombox_get_template_part( 'template-parts/listings/content-' . $boombox_listing_type, get_post_format() );
            }
            wp_reset_postdata(); ?>
        </ul>
        <?php do_action( 'boombox/loop-end', 'dont-miss' ); ?>

    </aside>
    <?php boombox_the_advertisement( 'boombox-single-after-dont-miss-section', array( 'class' => 'large bb-after-dont-miss-sec' ) );
}
wp_reset_query();