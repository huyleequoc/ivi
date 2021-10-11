<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
    die( 'No direct script access allowed' );
}

$author_id = boombox_amp()->get_current_post_author_id();
$author_name = boombox_amp()->get_author_name( $author_id );
$author_url = boombox_amp()->get_author_url( $author_id );

$boombox_has_post_thumbnail  = boombox_has_post_thumbnail();
$boombox_show_media          = apply_filters( 'boombox/loop-item/show-media', ( $this->get( 'boombox_template_grid_elements_options' )->media && $boombox_has_post_thumbnail ), $this->get( 'boombox_template_grid_elements_options' )->media, $boombox_has_post_thumbnail, 'content-grid-amp' );
$boombox_post_title          = get_the_title();
?>
<!-- Post Item -->
<article id="<?php esc_attr_e( 'post-' . get_the_ID() ) ?>" class="post-itm col-main m-b-md">

    <?php if( $boombox_show_media ) { ?>
    <!-- Post Thumbnail -->
    <div class="post-thumbnail">
        <?php
            $view_count = false;
            $share_count = false;

            if( apply_filters( 'boombox/loop-item/show-post-vote-count', true ) ) {
                if ($this->get('boombox_template_grid_elements_options')->views_count) {
                    $view_count = boombox_get_views_count( get_the_ID() );
                }
                if ($this->get('boombox_template_grid_elements_options')->share_count) {
                    $share_count = boombox_get_post_share_count( array( 'html' => false ) );
                }
            }


            if( ! boombox_is_view_count_tresholded( $view_count ) || $share_count ) {
        ?>
        <span class="post-meta">
            <?php if( $view_count && ! boombox_is_view_count_tresholded( $view_count ) ) {
	            $view_count_style = boombox_get_theme_option( 'extras_post_ranking_system_views_count_style' ); ?>
            <span class="bb-meta-itm itm-sm post-view-count">
                <i class="icon icon-eye m-r-sm vmiddle"></i><span class="vmiddle"><?php echo ( $view_count_style == 'rounded' ) ? boombox_numerical_word( $view_count ) : $view_count; ?></span>
            </span>
            <?php } ?>

            <?php if( $share_count ) { ?>
            <span class="bb-meta-itm itm-sm post-share-count">
                <i class="icon icon-share m-r-sm vmiddle"></i><span class="vmiddle"><?php echo $share_count; ?></span>
            </span>
            <?php } ?>
        </span>
        <?php } ?>

        <?php if( $boombox_show_media ) { ?>
        <a href="<?php echo amp_get_permalink( get_the_ID() ); ?>" title="<?php echo $boombox_post_title; ?>" target="_self">
            <?php boombox_amp()->render_image(array(
                'src'    => get_the_post_thumbnail_url( get_the_ID(), 'boombox_image360x270' ),
                'width'  => 360,
                'height' => 250,
                'alt'    => $boombox_post_title,
                'title'  => $boombox_post_title,
                'layout' => 'responsive'
            )); ?>
        </a>
        <?php } ?>
    </div>
    <!-- / Post Thumbnail -->
    <?php } ?>

    <!-- Post Content -->
    <div class="post-content">
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

        <?php if ( apply_filters( 'boombox/loop-item/show-subtitle', $this->get( 'boombox_template_grid_elements_options' )->subtitle ) ) { ?>
        <div class="post-summary"><p><?php echo $this->get( 'post' )->post_excerpt; ?></p></div>
        <?php } ?>

        <?php if( $this->get( 'boombox_template_grid_elements_options' )->author || $this->get( 'boombox_template_grid_elements_options' )->date ) { ?>
        <!-- Post Divider -->
        <hr class="divide-h" />

        <!-- Post Footer -->
        <footer class="post-footer">
            <span class="bb-author-vcard-mini vcard-sm">
                <?php if( $this->get( 'boombox_template_grid_elements_options' )->author ) { ?>
                <a href="<?php echo $author_url; ?>" class="avatar border-circle m-r-xs">
                    <?php boombox_amp()->render_image(array(
                        'src'    => boombox_amp()->get_avatar_url_from_html( get_avatar( boombox_amp()->get_current_post_author_id(), 24 ) ),
                        'class'  => 'avatar photo border-circle btm-shadow',
                        'width'  => 24,
                        'height' => 24,
                        'alt'    => $author_name
                    )); ?>
                </a>
                <span class="byline m-r-xs"><?php _e( 'by', 'boombox' ); ?></span>
                <address class="author-name"><a class="m-r-xs hvr-opacity" href="<?php echo $author_url; ?>"><?php echo $author_name; ?></a></address>
                <?php }
                if( $this->get( 'boombox_template_grid_elements_options' )->date ) {
                    boombox_amp()->render_post_time_tag($this->get('post')->ID);
                } ?>
            </span>
        </footer>
        <?php } ?>
    </div>
    <!-- / Post Content -->
</article>
<!-- / Post Item -->