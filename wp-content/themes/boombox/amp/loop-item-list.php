<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
    die( 'No direct script access allowed' );
}

$boombox_post_title = get_the_title();
$boombox_has_post_thumbnail  = boombox_has_post_thumbnail();
$author_id = boombox_amp()->get_current_post_author_id();
$boombox_show_media = apply_filters( 'boombox/loop-item/show-media', ( $this->get( 'boombox_template_grid_elements_options' )->media && $boombox_has_post_thumbnail ), $this->get( 'boombox_template_grid_elements_options' )->media, $boombox_has_post_thumbnail, 'content-list-amp' );
?>
<!-- Post Item -->
<article id="<?php esc_attr_e( 'post-' . get_the_ID() ) ?>" class="post-itm pull-left m-b-md-1">
    <?php if( $boombox_show_media ) { ?>
    <!-- Post Thumbnail -->
    <div class="post-thumbnail col-main m-b-md-1">
        <a href="<?php echo amp_get_permalink( get_the_ID() ); ?>" title="<?php echo $boombox_post_title; ?>" target="_self">
            <?php boombox_amp()->render_image(array(
                'src' => get_the_post_thumbnail_url( get_the_ID(), 'boombox_image360x270' ),
                'width' => 360,
                'height' => 250,
                'alt'    => $boombox_post_title,
                'title'  => $boombox_post_title,
                'layout' => 'responsive'
            )); ?>
        </a>
    </div>
    <!-- / Post Thumbnail -->
    <?php } ?>

    <!-- Post Content -->
    <div class="post-content col-main m-b-md-1">
        <!-- Post Header -->
        <header class="post-header">
            <?php
            if( apply_filters( 'boombox/loop-item/show-categories', $this->get( 'boombox_template_grid_elements_options' )->categories ) ) {
                boombox_amp()->post_categories_list(array(
                    'before' => '<p class="bb-cat-links links-sm col-inl-blck">',
                    'after' => '</p>'
                ));
            } ?>
            <h3 class="post-title hvr-opacity"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php echo $boombox_post_title; ?></a></h3>
        </header>
    </div>
    <!-- / Post Content -->
</article>
<!-- / Post Item -->