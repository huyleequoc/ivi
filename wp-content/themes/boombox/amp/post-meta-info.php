<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
    die( 'No direct script access allowed' );
}

$boombox_post_author_id = $this->get( 'post_author' )->ID;
$boombox_post_author_name = $this->get( 'post_author' )->display_name;
$boombox_post_author_url = boombox_amp()->get_author_url( $boombox_post_author_id );

if( $this->get( 'boombox_template_options' )->author || $this->get( 'boombox_template_options' )->date || $this->get( 'boombox_template_options' )->views ) {
?>
<!-- Post Meta -->
<div class="post-meta col-sec-wrapper-sm text-center m-b-sm">
	<?php if( $this->get( 'boombox_template_options' )->author || $this->get( 'boombox_template_options' )->date ) { ?>
    <!-- Post Author Mini Info -->
    <div class="col-sec col-sec-1 vmiddle m-b-sm">
        <span class="bb-author-vcard-mini vcard-lg">
            <?php if( $this->get( 'boombox_template_options' )->author ) { ?>
            <a href="<?php echo $boombox_post_author_url; ?>" class="avatar border-circle m-r-xs">
                <?php boombox_amp()->render_image( array(
                    'src'       => boombox_amp()->get_avatar_url_from_html( get_avatar( $boombox_post_author_id, 42 ) ),
                    'width'     => 42,
                    'height'    => 42,
                    'alt'       => $boombox_post_author_name,
                    'title'     => $boombox_post_author_name,
                    'class'     => 'avatar photo border-circle btm-shadow'
                )); ?>
            </a>
            <span class="byline m-r-xs"><?php _e( 'by', 'boombox' ); ?></span>
            <address class="author-name"><a class="m-r-xs hvr-opacity" href="<?php echo $boombox_post_author_url; ?>"><?php echo $boombox_post_author_name; ?></a></address>
            <?php }
            if( $this->get( 'boombox_template_options' )->date ) {
                boombox_amp()->render_post_time_tag( $this->get( 'post_id' ) );
            } ?>
        </span>
    </div>
    <!-- / Post Author Mini Info -->
	<?php } ?>

    <?php if ( $this->get( 'boombox_template_options' )->views || $this->get( 'boombox_template_options' )->comments_count ) {
	    $views_count = boombox_get_views_count( $this->get( 'post_id' ) );
	    $comments_count = (int) get_comments_number( $this->get( 'post_id' ) );
	    
	    if ( ! boombox_is_view_count_tresholded( $views_count ) || $comments_count ) { ?>
            <!-- Post View Count -->
            <div class="col-sec col-sec-2 vmiddle m-b-sm">
                <?php if( ! boombox_is_view_count_tresholded( $views_count ) ) {
	                $view_count_style = boombox_get_theme_option( 'extras_post_ranking_system_views_count_style' ); ?>
                <span class="bb-meta-itm itm-lg">
                    <i class="icon icon-eye m-r-sm"></i><span class="count"><?php echo ( $view_count_style == 'rounded' ) ? boombox_numerical_word( $views_count ) : $views_count; ?></span>
                </span>
                <?php } ?>
                
                <?php if( $comments_count ) { ?>
                <span class="bb-meta-itm itm-lg m-b-sm m-l-md">
                    <i class="icon icon-comment-o m-r-sm"></i><span class="count"><?php echo boombox_numerical_word( $comments_count ); ?></span>
                </span>
                <?php } ?>
            </div>
		    <?php
	    }
    }?>
</div>
<!-- / Post Meta -->
<?php } ?>