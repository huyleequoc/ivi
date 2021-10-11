<?php
/**
 * The template part for displaying woocommerce product item
 *
 * @package BoomBox_Theme
 * @since   1.0.0
 * @version 2.0.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}
add_filter( 'post_class', 'boombox_remove_editor_article_classes', 10, 3 );
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="entry-content">
		<div class="section-box">
			<?php woocommerce_content(); ?>

			<?php wp_link_pages(); ?>
		</div>
	</div>
</article>