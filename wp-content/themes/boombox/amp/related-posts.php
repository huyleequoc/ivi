<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

$set = boombox_get_theme_options_set( array(
	'single_post_related_posts_related_entries_per_page',
	'single_post_related_posts_related_entries_heading',
) );
$boombox_loop_item_layout = apply_filters( 'boombox/amp/loop-item-layout/related', 'grid' );
$boombox_related_posts = boombox_get_related_posts_items( 'related', $set[ 'single_post_related_posts_related_entries_per_page' ] );

if ( NULL != $boombox_related_posts && $boombox_related_posts->have_posts() ) {
	$classes = 'container post-list two-col-layout m-b-md';
	$style = '';
	if( $this->get( 'boombox_settings' )->is_customizer_preview ) {
		$classes .= ' bb-customizer-toggle bb-customizer-toggle-related_posts';
		if( ! $this->get( 'boombox_template_options' )->related_posts ) {
			$style .= 'display:none;';
		}
	}
	if(
		$this->get( 'boombox_settings' )->is_customizer_preview
		|| $this->get( 'boombox_template_options' )->related_posts
	) { ?>
		<div class="<?php echo $classes; ?>"<?php if( $style ) { echo ' style="' . $style . '"'; } ?>>
			<?php if ( $set[ 'single_post_related_posts_related_entries_heading' ] ) { ?>
				<header>
					<h2 class="bb-entry-header border-btm"><?php esc_html_e( $set[ 'single_post_related_posts_related_entries_heading' ], 'boombox' ); ?></h2>
				</header>
			<?php } ?>

			<div class="row clearfix">
				<?php
				while ( $boombox_related_posts->have_posts() ) {
					$boombox_related_posts->the_post();
					$this->load_parts( array( sprintf( 'loop-item-%s', $boombox_loop_item_layout ) ) );
				}
				wp_reset_postdata();
				?>
			</div>

		</div>
		<?php
	}
}