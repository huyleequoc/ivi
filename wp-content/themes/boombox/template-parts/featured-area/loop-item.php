<?php
/**
 * The template part for displaying featured area item
 *
 * @package BoomBox_Theme
 * @since   2.0.0
 * @version 2.5.0
 *
 * @var $template_helper Boombox_Featured_Area_Template_Helper  Template Helper
 */
$template_helper = Boombox_Template::init( 'featured-area' );
$template_options = $template_helper->get_options();

$boombox_title = get_the_title();
$boombox_thumb_style = '';
$boombox_thumbnail_url = boombox_get_post_dafault_thumbnail_url( $template_options[ 'image_size' ] );

if ( boombox_has_post_thumbnail() ) {
	$thumbnail_url = wp_get_attachment_image_src( get_post_thumbnail_id(), $template_options[ 'image_size' ] );
	if( ! empty( $thumbnail_url ) ) {
		$thumbnail_url = $thumbnail_url[ 0 ];
		$thumbnail_extension = pathinfo( $thumbnail_url, PATHINFO_EXTENSION );
		if( $thumbnail_extension && ( false !== strpos( $thumbnail_extension, 'gif' ) ) ) {
			$thumbnail_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
			if( ! empty( $thumbnail_url ) ) {
				$thumbnail_url = $thumbnail_url[0];
			}
		}
		$boombox_thumbnail_url = $thumbnail_url;
	}

	$boombox_thumb_style = $boombox_thumbnail_url ? sprintf( 'style="background-image:url(\'%s\')"', esc_url( $boombox_thumbnail_url ) ) : '';
} ?>

<article class="featured-item" <?php echo $boombox_thumb_style; ?>>
	<?php if ( $boombox_thumbnail_url ) { ?>
		<figure class="featured-media">
			<a href="<?php echo esc_url( get_permalink() ); ?>" class="featured-link">
				<img src="<?php echo $boombox_thumbnail_url; ?>" alt="<?php echo $boombox_title; ?>" title="<?php echo $boombox_title; ?>"/>
			</a>
		</figure>
	<?php } ?>

	<header class="featured-header">
		<div class="featured-caption">
			<?php

			echo boombox_get_post_meta_html( array(
				'views'  => $template_options[ 'views_count' ],
				'votes'  => $template_options[ 'votes_count' ],
				'shares' => $template_options[ 'share_count' ],
				'order'  => array( 'shares', 'views', 'votes' ),
				'before' => '<div class="post-meta bb-post-meta">',
				'after'  => '</div>'
			) );

			if ( $template_options[ 'post_title' ] ) { ?>
				<h2 class="entry-title">
					<a href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark"><?php the_title(); ?></a>
				</h2>
			<?php }

				echo boombox_generate_user_mini_card( array(
					'author'    => $template_options[ 'author' ],
					'avatar'    => false,
					'date'      => $template_options[ 'date' ],
					'class'     => '',
				) );
			?>
		</div>
	</header>

	<?php if ( $template_options[ 'badges' ] ) {
		$badges_list = boombox_get_post_badge_list( array(
			'post_id'          => get_the_ID(),
			'badges_count'     => $template_options[ 'badges_count' ],
            'post_type_badges' => false
		) );
		if ( $badges_list[ 'badges' ] ) { ?>
			<div class="featured-badge-list">
				<?php echo $badges_list[ 'badges' ]; ?>
			</div>
		<?php }
	} ?>
</article>