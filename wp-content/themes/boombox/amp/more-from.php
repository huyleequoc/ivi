<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

global $post;
$set = boombox_get_theme_options_set( array(
	'single_post_related_posts_more_entries_per_page',
	'single_post_related_posts_more_entries_heading',
) );

$boombox_loop_item_layout = apply_filters( 'boombox/amp/loop-item-layout/more-from', 'list' );
$boombox_post_first_category = boombox_get_post_first_category( $post );

if ( $boombox_post_first_category ) {
	$boombox_more_from_posts = boombox_get_more_from_posts_items( 'more_from', $boombox_post_first_category, $set[ 'single_post_related_posts_more_entries_per_page' ] );
	if ( NULL != $boombox_more_from_posts && $boombox_more_from_posts->have_posts() ) {
		$classes = 'container post-list one-col-layout m-b-md';
		$style = '';
		if( $this->get( 'boombox_settings' )->is_customizer_preview ) {
			$classes .= ' bb-customizer-toggle bb-customizer-toggle-more_from_posts';
			if( ! $this->get( 'boombox_template_options' )->more_from_posts ) {
				$style .= 'display:none;';
			}
		}
		if(
			$this->get( 'boombox_settings' )->is_customizer_preview
			|| $this->get( 'boombox_template_options' )->more_from_posts
		) { ?>
		<div class="<?php echo $classes; ?>"<?php if( $style ) { echo ' style="' . $style . '"'; } ?>>
			<?php if ( $set[ 'single_post_related_posts_more_entries_heading' ] ) { ?>
				<header>
					<h2 class="bb-entry-header border-btm"><?php esc_html_e( $set[ 'single_post_related_posts_more_entries_heading' ], 'boombox' ); ?> <a href="#"><?php echo $boombox_post_first_category->name; ?></a></h2>
				</header>
			<?php } ?>
			<div class="row clearfix">
				<?php
				while ( $boombox_more_from_posts->have_posts() ) {
					$boombox_more_from_posts->the_post();
					$this->load_parts( array( sprintf( 'loop-item-%s', $boombox_loop_item_layout ) ) );
				}
				wp_reset_postdata();
				?>
			</div>

		</div>
	<?php }
	}
}