<?php
/**
 * The template part for displaying post item for "stream" listing type
 *
 * @package BoomBox_Theme
 * @since   1.0.0
 * @version 2.5.8.1
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

$listing_type                       = 'content-stream';
do_action( 'boombox/loop-item/before-content', $listing_type );

$featured_image_size                = 'boombox_image545';
$classes                            = 'post bb-post post-stream bb-card-item';
$has_post_thumbnail                 = boombox_has_post_thumbnail();
$template_options                   = Boombox_Template::init( 'collection-item' )->get_options();
$featured_video                     = boombox_get_post_featured_video( get_the_ID(), $featured_image_size, array(
	'template'     => 'listing',
	'listing_type' => $listing_type
) );
$enable_full_post_button_conditions = boombox_get_theme_option( 'single_post_general_enable_full_post_button_conditions' );
$full_post_button_label             = boombox_get_theme_option( 'single_post_general_post_button_label' );
$is_nsfw_post                       = boombox_is_nsfw_post();
$show_media                         = apply_filters( 'boombox/loop-item/show-media', ( $template_options['media'] && ( $has_post_thumbnail || $featured_video ) ), $template_options['media'], ( $has_post_thumbnail || $featured_video ), $listing_type );

if ( ! $show_media ) {
	$classes .= ' no-thumbnail';
}
if ( false !== array_search( 'post_content', $enable_full_post_button_conditions ) && get_the_content() ) {
	$classes .= ' full-post-show';
}

if ( $template_options['badges'] || $template_options['post_type_badges'] ) {
	$badges_list = boombox_get_post_badge_list( array(
		'post_type_badges_before' => '<div class="bb-post-format lg">',
		'post_type_badges_after'  => '</div>'
	) );
}

$permalink = get_permalink();
$url       = apply_filters( 'boombox_loop_item_url', $permalink, get_the_ID() );
$target    = apply_filters( 'boombox_loop_item_url_target', '', $permalink, $url );
$rel       = apply_filters( 'boombox_loop_item_url_rel', '', $permalink, $url );

$media_html        = '';
$is_media_playable = false;
if ( $show_media ) {
	if ( $featured_video ) {
		$is_media_playable = true;
		$media_html        = $featured_video;
	} else {
		$post_thumbnail_html    = boombox_get_post_thumbnail( null, $featured_image_size, array(
			'play'         => true,
			'template'     => 'listing',
			'listing_type' => $listing_type
		) );
		$boombox_post_thumbnail = boombox_do_post_thumbnail_wrap( $post_thumbnail_html, $url, $target, $rel );
		$media_html             = $boombox_post_thumbnail['before'] . $post_thumbnail_html . $boombox_post_thumbnail['after'];
		$is_media_playable      = $boombox_post_thumbnail['is_playable'];
	}
} ?>

	<li class="post-item post-item-stream">
		<article <?php post_class( $classes ); ?>>
			<?php

			if ( apply_filters( 'boombox/loop-item/show-title', $template_options['title'] ) ) {
				the_title( sprintf( '<h2 class="entry-title"><a href="%1$s" rel="bookmark" %2$s %3$s>', $url, $target, $rel ), '</a></h2>' );
			}

			if ( apply_filters( 'boombox/loop-item/show-subtitle', ( $template_options['subtitle'] || $template_options['reading_time'] ) ) ) {
				echo boombox_get_post_subtitle( array(
					'subtitle'          => $template_options['subtitle'],
					'reading_time'      => $template_options['reading_time'],
					'reading_time_size' => 'lg'
				) );
			} ?>

			<!-- thumbnail -->
			<div class="post-thumbnail">
				<?php
				if ( apply_filters( 'boombox/loop-item/show-box-index', false ) ) {
					boombox_get_template_part( 'template-parts/numeric', 'badge' );
				}

				if ( apply_filters( 'boombox/loop-item/show-badges', $template_options['badges'] ) ) {
					echo $badges_list['badges'];
				}

				if ( $show_media ) {
					echo $media_html;

					if ( ! $is_nsfw_post && $full_post_button_label ) { ?>
						<a class="view-full-post" href="<?php echo $url; ?>" <?php echo $target; ?> <?php echo $rel; ?>>
							<span class="bb-btn bb-btn-primary"><?php esc_html_e( $full_post_button_label, 'boombox' ); ?></span>
						</a>
					<?php }
				}

				if ( apply_filters( 'boombox/loop-item/show-post-type-badges', $template_options['post_type_badges'] ) && ! $is_media_playable ) {
					echo $badges_list['post_type_badges'];
				} ?>
			</div>
			<!-- thumbnail -->

			<div class="content">
				<!-- entry-header -->
				<header class="entry-header">
					<?php
					do_action( 'boombox/loop-item/content-start' );

					$terms_html = boombox_terms_list_html( array(
						'category' => apply_filters( 'boombox/loop-item/show-categories', $template_options['categories'] ),
						'post_tag' => apply_filters( 'boombox/loop-item/show-tags', $template_options['tags'] )
					) );

					if ( apply_filters( 'boombox/loop-item/show-comments-count', ( comments_open() && $template_options['comments_count'] ) ) ) {
						$terms_html .= boombox_get_post_comments_count_html( array(
							'before' => '<div class="post-meta bb-post-meta">',
							'after'  => '</div>'
						) );
					}

					if ( $terms_html ) {
						printf( '<div class="bb-post-terms">%s</div>', $terms_html );
					}

					if ( apply_filters( 'boombox/loop-item/show-post-author-meta', true ) ) {
						echo boombox_generate_user_mini_card( array(
							'author' => $template_options['author'],
							'avatar' => $template_options['author'],
							'date'   => $template_options['date'],
							'class'  => 'post-author-meta'
						) );
					}

					do_action( 'boombox/loop-item/content-end' ); ?>
				</header>
				<!-- entry-header -->

				<?php if ( apply_filters( 'boombox/loop-item/show-post-excerpt', $template_options['excerpt'] ) ) { ?>
					<div class="entry-content"><?php echo wp_trim_excerpt(); ?></div>
				<?php } ?>
			</div>

			<!-- entry-footer -->
			<footer class="entry-footer">
				<?php if ( $template_options['share_bar'] ) { ?>
					<div class="bb-post-share-box">
						<?php boombox_get_template_part( 'template-parts/single/components/share' ); ?>
					</div>
				<?php } ?>
			</footer>
			<!-- entry-footer -->

			<?php boombox_get_template_part( 'template-parts/listings/content', 'affiliate' ); ?>

		</article>
	</li>

<?php do_action( 'boombox/loop-item/after-content', $listing_type ); ?>