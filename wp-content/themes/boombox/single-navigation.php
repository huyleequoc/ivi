<?php
/**
 * The template for displaying the default navigation
 *
 * @package BoomBox_Theme
 * @since   1.0.0
 * @version 2.0.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
} ?>
<!-- Prev-Next Navigation -->
<div class="row page-nav clearfix m-b-md">
    <?php
    $boombox_prev_post = get_previous_post();
    if ( ! empty( $boombox_prev_post ) ) { ?>
        <div class="col-main page-nav-itm page-nav-prev m-b-md">
            <a href="<?php echo esc_url( get_permalink( $boombox_prev_post->ID ) ); ?>" class="col-inl-blck w-full hvr-opacity" rel="prev">
                <div class="header border-btm"><?php esc_html_e( 'Previous Post', 'boombox' ); ?></div>
                <div class="content border-btm clearfix">
                    <?php
                    if ( boombox_has_post_thumbnail( $boombox_prev_post->ID ) ) {
                        boombox_amp()->render_image( array(
                            'src'    => get_the_post_thumbnail_url( $boombox_prev_post, 'thumbnail' ),
                            'title'  => $boombox_prev_post->post_title,
                            'width'  => 80,
                            'height' => 80,
                            'class'  => 'page-img pull-left'
                        ) );
                    } ?>
                    <div class="page-info">
                        <strong class="title"><?php echo wp_trim_words($boombox_prev_post->post_title, 10, '...'); ?></strong>
                        <p class="author-name"><span class="byline"><?php _e( 'By', 'boombox' ); ?></span> <?php echo boombox_amp()->get_author_name( $boombox_prev_post->post_author ); ?></p>
                    </div>
                </div>
            </a>
        </div>
    <?php }


    $boombox_next_post = get_next_post();
    if ( ! empty( $boombox_next_post ) ) { ?>
        <!-- Next Navigation -->
        <div class="col-main page-nav-itm page-nav-next m-b-md">
            <a href="#" class="col-inl-blck w-full hvr-opacity" rel="next">
                <p class="header border-btm"><?php esc_html_e( 'Next Post', 'boombox' ); ?></p>
                <div class="content border-btm clearfix">
                    <?php
                    if ( boombox_has_post_thumbnail( $boombox_next_post->ID ) ) {
                        boombox_amp()->render_image( array(
                            'src'    => get_the_post_thumbnail_url( $boombox_next_post, 'thumbnail' ),
                            'title'  => $boombox_next_post->post_title,
                            'width'  => 80,
                            'height' => 80,
                            'class'  => 'page-img pull-left'
                        ) );
                    } ?>
                    <div class="page-info">
                        <strong class="title"><?php echo wp_trim_words($boombox_next_post->post_title, 10, '...'); ?></strong>
                        <p class="author-name">
                            <span class="byline"><?php _e( 'By', 'boombox' ); ?></span> <?php echo boombox_amp()->get_author_name( $boombox_next_post->post_author ); ?></p>
                    </div>
                </div>
            </a>
        </div>
    <?php } ?>
</div>