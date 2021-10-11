<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
    die( 'No direct script access allowed' );
}

$this->load_parts( array( 'header' ) ); ?>

<!-- Main Content -->
<main>

    <article class="container post m-b-lg">
        <!-- Post Header -->
        <header class="post-header clearfix m-b-md">
            <?php if ( $this->get( 'boombox_template_options' )->categories || $this->get( 'boombox_template_options' )->badges ) { ?>
            <div class="col-sec-wrapper-sm m-b-sm">
                <?php if ( $this->get( 'boombox_template_options' )->categories ) { ?>
                <div class="col-sec col-sec-1 vmiddle m-b-sm">
                    <?php
                        // Category Links
                        boombox_amp()->post_categories_list(array(
                            'before' => '<p class="bb-cat-links links-lg pull-left">',
                            'after' => '</p>'
                        ));
                    ?>
                </div>
                <?php } ?>

                <?php if ( $this->get( 'boombox_template_options' )->badges ) { ?>
                <div class="col-sec col-sec-2 vmiddle  m-b-sm-1">
                    <?php
                        // Badge List
                        $badges_list = boombox_get_post_badge_list( array(
                            'post_id'          => $this->get( 'post_id' ),
                            'badges_count'     => 4,
                            'post_type_badges' => false
                        ) );
                        if( $badges_list['badges'] ) { ?>
                            <p class="bb-badge-list clearfix">
                                <?php foreach( $badges_list['badges'] as $badge_key => $badge ) {
	                                $link_classes = '';
	                                $link_content = '';
	                                if ( $badge['amp']['icon_type'] == 'image' ) {
		                                $link_classes = 'badge pull-left pull-right-sm reaction border-circle btm-shadow ' . sprintf( '%1$s-%2$d', $badge['taxonomy'], $badge['term_id'] );
		                                $link_content = boombox_amp()->render_image( array(
			                                'src'    => wp_kses_post( $badge['amp']['icon'] ),
			                                'width'  => 40,
			                                'height' => 40,
			                                'alt'    => esc_html( $badge['name'] ),
			                                'title'  => esc_html( $badge['name'] )
		                                ), false );
	                                } elseif( $badge['amp']['icon_type'] == 'icon' ) {
		                                $link_classes = 'badge pull-left pull-right-sm trending border-circle btm-shadow';
		                                $link_content = sprintf( '<i class="icon icon-%s"></i>', $badge['amp']['icon'] );
	                                }
	                                if( $link_content ) {
                                        printf( '<a class="%1$s" href="%2$s" title="%3$s">%4$s</a>', $link_classes, esc_url( $badge['link'] ), esc_html( $badge['name'] ), $link_content );
                                    }
                                } ?>
                            </p>
                        <?php }
                    ?>
                </div>
                <?php } ?>
            </div>
            <?php } ?>


            <h1 class="post-title m-b-sm-1 text-left clearfix"><?php echo wp_kses_data( $this->get( 'post_title' ) ); ?></h1>

            <?php if( $this->get( 'boombox_template_options' )->subtitle ) { ?>
                <h2 class="post-summary m-b-sm-1 text-left"><?php echo $this->get( 'post' )->post_excerpt; ?></h2>
            <?php } ?>

            <?php $this->load_parts( array( 'post-affiliate-content' ) ); ?>

            <hr />

            <?php $this->load_parts( array( 'post-meta-info' ) ); ?>

        </header>

        <!-- Post Featured Image -->
        <?php
        if (apply_filters('boombox/single/show_media', ($this->get( 'boombox_template_options' )->media && boombox_show_multipage_thumbnail() && (boombox_has_post_thumbnail())))) {
            $this->load_parts( array( 'featured-image' ) );
        }
        ?>

        <!-- Post Content -->
        <div class="post-content">
            <?php
                echo $this->get( 'post_amp_content' );
                do_action( 'boombox/amp/after_post_content', $this );
            ?>
        </div>

        <!-- Post Footer -->
        <footer class="post-footer">
            <?php $this->load_parts( array('next-prev-buttons') ); ?>

            <!-- Post Divider -->
            <hr class="m-b-md" />

            <!-- Post Tags -->
            <?php if ( $this->get( 'boombox_template_options' )->tags ) {
                boombox_amp()->post_tags_list(array(
                    'before' => '<p class="bb-tags clearfix m-b-md-2 hvr-opacity">',
                    'after' => '</p>'
                ));
            } ?>
            <!-- / Post Tags -->

            <?php
            if ( $this->get( 'boombox_template_options' )->author_info && ( get_post_type() != 'attachment' )) {
                $this->load_parts( array( 'author-expanded-info' ) );
            }

            if ( $this->get( 'boombox_template_options' )->navigation ) {
                $this->load_parts( array( 'navigation' ) );
            }
            ?>

            <!-- Navigation Button -->
            <?php $this->load_parts( array('comments') ); ?>

        </footer>

    </article>

</main>

<!-- Aside Content -->
<aside>

    <?php
    $this->load_parts( array( 'related-posts' ) );
    $this->load_parts( array( 'more-from' ) );
    $this->load_parts( array( 'dont-miss' ) ); ?>

    <!-- Navigation Button -->
    <div class="container text-center m-b-lg">
        <a href="<?php echo $this->get( 'canonical_url' ); ?>" class="bb-btn btn-default hvr-btm-shadow">
            <?php echo boombox_amp()->get_full_version_text(); ?>
        </a>
    </div>
</aside>


<?php $this->load_parts( array( 'footer' ) ); ?>
