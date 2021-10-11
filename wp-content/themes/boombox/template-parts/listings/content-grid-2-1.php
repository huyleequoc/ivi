<?php
/**
 * The template part for displaying post item for "grid-2-1" listing type
 *
 * @package BoomBox_Theme
 * @since   1.0.0
 * @version 2.5.8.1
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

$listing_type        = 'content-grid-2-1';
do_action( 'boombox/loop-item/before-content', $listing_type );

$featured_image_size = 'boombox_image360x180';
$classes             = 'post bb-post bb-card-item';
$has_post_thumbnail  = boombox_has_post_thumbnail();
$template_options    = Boombox_Template::init( 'collection-item' )->get_options();
$show_media          = apply_filters( 'boombox/loop-item/show-media', ( $template_options['media'] && $has_post_thumbnail ), $template_options['media'], $has_post_thumbnail, $listing_type );

if ( ! $show_media ) {
	$classes .= ' no-thumbnail';
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
$rel       = apply_filters( 'boombox_loop_item_url_rel', '', $permalink, $url ); ?>

	<li class="post-item post-item-grid-2-1">
		<article <?php post_class( $classes ); ?>>

			<div class="post-thumbnail">
				<?php

				if ( apply_filters( 'boombox/loop-item/show-box-index', false ) ) {
					boombox_get_template_part( 'template-parts/numeric', 'badge' );
				}

				if ( apply_filters( 'boombox/loop-item/show-badges', $template_options['badges'] ) ) {
					echo $badges_list['badges'];
				}

				echo boombox_get_post_meta_html( array(
					'views'  => $template_options['views_count'],
					'votes'  => $template_options['votes_count'],
					'shares' => $template_options['share_count'],
					'before' => '<div class="post-meta bb-post-meta post-meta-bg">',
					'after'  => '</div>'
				) );

				if ( $show_media ) { ?>

					<a href="<?php echo $url; ?>"
					   title="<?php echo esc_attr( the_title_attribute( array( 'echo' => false ) ) ); ?>" <?php echo $target; ?> <?php echo $rel; ?>>
						<?php echo boombox_get_post_thumbnail( null, $featured_image_size, array( 'template'     => 'listing',
						                                                                          'listing_type' => $listing_type
						) ); ?>
					</a>

					<?php if ( apply_filters( 'boombox/loop-item/show-post-type-badges', $template_options['post_type_badges'] ) ) {
						echo $badges_list['post_type_badges'];
					}
				} ?>

			</div>

			<div class="content">
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

					if ( $show_title = apply_filters( 'boombox/loop-item/show-title', $template_options['title'] ) ) {
						the_title( sprintf( '<h2 class="entry-title"><a href="%1$s" rel="bookmark" %2$s %3$s>', $url, $target, $rel ), '</a></h2>' );
					}

					if ( apply_filters( 'boombox/loop-item/show-subtitle', ( $template_options[ 'subtitle' ] || $template_options[ 'reading_time' ] ) ) ) {
						echo boombox_get_post_subtitle( array(
							'subtitle'          => $template_options[ 'subtitle' ],
							'reading_time'      => $template_options[ 'reading_time' ],
							'reading_time_size' => 'lg'
						) );
					}

					boombox_get_template_part( 'template-parts/listings/content', 'affiliate' );

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
			</div>

		</article>
	</li>

<?php do_action( 'boombox/loop-item/after-content', $listing_type ); ?>