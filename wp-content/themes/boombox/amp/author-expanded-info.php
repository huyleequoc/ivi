<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
    die( 'No direct script access allowed' );
}

$boombox_post_author_id = $this->get( 'post_author' )->ID;
$boombox_post_author_name = $this->get( 'post_author' )->display_name;
$boombox_post_author_url = boombox_amp()->get_author_url( $boombox_post_author_id );
$bio = apply_filters( 'boombox/author_bio', wp_kses_post( get_the_author_meta('description', $boombox_post_author_id) ), $boombox_post_author_id );
?>
<!-- Post Author Card -->
<article class="bb-author-vcard m-b-lg <?php echo $bio ? '' : 'no-author-info'; ?>" itemscope="" itemtype="http://schema.org/Person">
    <div class="author-vcard-inner">
        <header class="author-header clearfix">
            <!-- Post Avatar -->
            <a href="<?php echo $boombox_post_author_url; ?>" class="avatar pull-left-sm border-circle">
                <?php boombox_amp()->render_image( array(
                    'src'   => boombox_amp()->get_avatar_url_from_html( get_avatar( $boombox_post_author_id, 133, '', $boombox_post_author_name, array( 'type' => 'full' ) ) ),
                    'width' => 133,
                    'height' => 133,
                    'alt' => $boombox_post_author_name,
                    'title' => $boombox_post_author_name,
                    'itemprop' => 'image',
                    'class' => 'border-circle'
                )); ?>
            </a>
            <!-- / Post Avatar -->

            <!-- Post Header Info -->
            <div class="header-info hvr-opacity">
                <h3 class="author-name">
                    <span class="byline"><?php echo apply_filters( 'boombox/author/posted-by', esc_html__( 'Posted by', 'boombox' ), 'expanded' ); ?></span>
                    <a href="<?php echo $boombox_post_author_url; ?>" itemprop="url"><span itemprop="name"><?php echo $boombox_post_author_name; ?></span></a>
                </h3>
                <?php echo apply_filters( 'author_extended_data', '', $boombox_post_author_id ); ?>
            </div>
            <!-- / Post Header Info -->
        </header>

        <div class="author-info" itemprop="description">
            <?php echo $bio; ?>
        </div>
    </div>
</article>
<!-- / Post Author Card -->