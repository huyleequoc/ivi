<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

$boombox_prev_post = get_previous_post();
$boombox_next_post = get_next_post();

if( $boombox_prev_post || $boombox_next_post ) {
?>

<!-- Prev-Next Navigation -->
<nav class="page-nav clearfix m-b-md">
	<ul class="row page-nav-list">
		<?php if ( $boombox_prev_post ) { ?>
		<!-- Prev Navigation -->
		<li class="col-main page-nav-itm page-nav-prev m-b-md">
			<a href="<?php echo esc_url( amp_get_permalink( $boombox_prev_post->ID ) ); ?>" class="col-inl-blck w-full hvr-opacity" rel="prev">
				<p class="header border-btm"><?php esc_html_e( 'Previous Post', 'boombox' ); ?></p>
				<div class="content border-btm clearfix">
					<?php
						if ( boombox_has_post_thumbnail( $boombox_prev_post->ID ) ) {
							boombox_amp()->render_image( array(
								'src'       => get_the_post_thumbnail_url( $boombox_prev_post->ID, 'thumbnail' ),
								'width'     => 80,
								'height'    => 80,
								'class'     => 'page-img pull-left'
							) );
						}
					?>

					<div class="page-info">
						<strong class="title"><?php echo wp_trim_words( $boombox_prev_post->post_title, 10, '...' ); ?></strong>
						<p class="author-name"><span class="byline m-r-xs"><?php _e( 'by', 'boombox' ); ?></span><?php echo boombox_amp()->get_author_name( $boombox_prev_post->post_author ); ?></p>
					</div>
				</div>
			</a>
		</li>
		<!-- / Prev Navigation -->
		<?php } ?>

		<?php if ( $boombox_next_post ) { ?>
		<!-- Next Navigation -->
		<li class="col-main page-nav-itm page-nav-next m-b-md">
			<a href="<?php echo esc_url( amp_get_permalink( $boombox_next_post->ID ) ); ?>" class="col-inl-blck w-full hvr-opacity" rel="next">
				<p class="header border-btm"><?php esc_html_e( 'Next Post', 'boombox' ); ?></p>
				<div class="content border-btm clearfix">
					<?php
					if ( boombox_has_post_thumbnail( $boombox_next_post->ID ) ) {
						boombox_amp()->render_image( array(
							'src'       => get_the_post_thumbnail_url( $boombox_next_post->ID, 'thumbnail' ),
							'alt'       => $boombox_next_post->post_title,
							'title'     => $boombox_next_post->post_title,
							'width'     => 80,
							'height'    => 80,
							'class'     => 'page-img pull-left'
						) );
					}
					?>
					<div class="page-info">
						<strong class="title"><?php echo wp_trim_words( $boombox_next_post->post_title, 10, '...' ); ?></strong>
						<p class="author-name"><span class="byline m-r-xs"><?php _e( 'by', 'boombox' ); ?></span><?php echo boombox_amp()->get_author_name( $boombox_next_post->post_author ); ?></p>
					</div>
				</div>
			</a>
		</li>
		<!-- / Next Navigation -->
		<?php } ?>

	</ul>
</nav>
<?php } ?>