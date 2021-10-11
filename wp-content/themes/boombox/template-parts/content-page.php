<?php
/**
 * The template part for displaying page content
 *
 * @package BoomBox_Theme
 * @since   1.0.0
 * @version 2.0.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}
do_action( 'boombox/before_page_content' );

global $post;
$featured_image_size = 'boombox_image768';
$show_page_content   = apply_filters( 'boombox/render_page_content', (bool) trim( $post->post_content ) );

//Case when WPBakery frontend editor is running.
if ( boombox_plugin_management_service()->is_plugin_active( 'js_composer/js_composer.php' ) && vc_is_page_editable() ) {
	$show_page_content = true;
}

// temporary solution
if ( $show_page_content ) { ?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'bb-card-item' ); ?>>
		<div class="entry-content">
			<div class="section-box">
				<?php
				$post_thumbnail_html = boombox_get_post_thumbnail( null, $featured_image_size );
				if ( $post_thumbnail_html ) { ?>
					<div class="post-thumbnail">
						<?php echo $post_thumbnail_html;
						echo boombox_get_post_thumbnail_caption(); ?>
					</div>
					<?php
				}

				the_content();
				wp_link_pages( array( 'layout' => 'page_xy', 'class' => 'pg-lg' ) ); ?>
			</div>
		</div>
	</article>
<?php }

do_action( 'boombox/after_page_content' );