<?php
/**
 * This file loads the content partially.
 * @version 1.4.4
 */

// Fetch plugin settings.
$remove_comments = get_option('auto_load_next_post_remove_comments');

// Load content before the loop.
do_action( 'alnp_load_before_loop' );

// Check that there are more posts to load.
if ( have_posts() ) {

	the_post();
	
	$post_format = get_post_format();
	if( false === $post_format ) {
		$post_format = 'standard';
	}

	// Load content before the post content.
	do_action( 'balnp_load_before_content' );

	// Load content before the post content for a specific post format.
	do_action( 'balnp_load_before_content_type_' . $post_format );

	$helper = Boombox_Template::init( 'post' );
	$options = $helper->get_options();

	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( $options[ 'classes' ] ); ?> <?php boombox_single_article_structured_data(); ?>>

		<?php
		// Post Breadcrumb
		boombox_get_template_part( 'template-parts/breadcrumb', '', array(
			'before' => '<nav class="s-post-breadcrumb bb-breadcrumb mb-xs bb-mb-el clr-style1">',
			'after'  => '</nav>'
		) ); ?>

		<?php // Post Main Content for Card View ?>
		<div class="s-post-main mb-md bb-mb-el bb-card-item">

			<?php
			// Post Vendor Block
			boombox_get_template_part( 'template-parts/single/components/brand', '', array(
				'before' => '<aside class="bb-brand-block mb-sm bb-mb-el">',
				'after'  => '</aside>'
			) ); ?>

			<?php // Post Header ?>
			<header class="entry-header s-post-header bb-mb-el">

				<?php
				// Post Microdata
				boombox_get_template_part( 'template-parts/single/components/microdata', '', array(
					'microdata' => $options[ 'microdata' ]
				) );

				// Post taxonomies
				if( $options[ 'elements' ][ 'categories' ] || $options[ 'elements' ][ 'badges' ] ) {
					boombox_get_template_part( 'template-parts/single/components/taxonomy', '', array(
						'categories' => $options[ 'elements' ][ 'categories' ],
						'badges'     => $options[ 'elements' ][ 'badges' ]
					) );
				}

				// Post title
				boombox_get_template_part( 'template-parts/single/components/title' );

				// Post sub title
				if ( $options[ 'elements' ][ 'subtitle' ] ) {
					boombox_get_template_part( 'template-parts/single/components/sub-title' );
				}

				// Affiliate content
				boombox_get_template_part( 'template-parts/listings/content', 'affiliate', array(
					'class' => 's-post-affiliate bb-mb-el'
				) );

				// Separator
				boombox_get_template_part( 'template-parts/single/components/separator', '', array(
					'class' => 's-post-header-sep bb-mb-el'
				) ); ?>

			</header>

			<?php // Post Meta ?>
			<div class="s-post-meta-block bb-mb-el">
				<div class="post-meta-content row">
					<div class="d-table-center-sm">

						<?php
						// Post author mini card
						boombox_get_template_part( 'template-parts/single/components/mini-card', '', array(
							'author' => $options[ 'elements' ][ 'author' ],
							'avatar' => $options[ 'elements' ][ 'author' ],
							'date'   => $options[ 'elements' ][ 'date' ],
							'before' => '<div class="col-l d-table-cell col-md-6 col-sm-6 text-left-sm">',
							'after'  => '</div>',
						) );

						// Post metadata
						boombox_get_template_part( 'template-parts/single/components/metadata', '', array(
							'comments' => $options[ 'elements' ][ 'comments_count' ],
							'views'    => $options[ 'elements' ][ 'views' ],
							'before'   => '<div class="col-r d-table-cell col-md-6 col-sm-6 text-right-sm">',
							'after'    => '</div>',
						) ); ?>

					</div>
				</div>
			</div>

			<?php
			// Post Top Share Box
			boombox_get_template_part( 'template-parts/single/components/share', '', array(
				'heading' => false,
				'before'  => '<div class="bb-post-share-box s-post-share-box top bb-mb-el">',
				'after'   => '</div>'
			) );

			// Post Media
			boombox_get_template_part( 'template-parts/single/components/media/mix', '', array(
				'protect_content' => $options['protect_content'],
				'media'           => $options['featured_media'],
				'caption'         => $options['featured_caption'],
				'image_size'      => $options['image_size'],
			) );

			// Post Main Content
			boombox_get_template_part( 'template-parts/single/components/content', '', array(
				'protect_content'       => $options['protect_content'],
				'pagination_layout'     => $options['pagination_layout'],
				'has_secondary_sidebar' => $options['enable_secondary_sidebar']
			) );

			// Post Source
			boombox_get_template_part( 'template-parts/single/components/source', '', array(
				'protect_content'       => $options['protect_content'],
			) );

			// Separator
			boombox_get_template_part( 'template-parts/single/components/separator', '', array(
				'class' => 'bb-mb-el'
			) );

			// Post Tags
			boombox_get_template_part( 'template-parts/single/components/post-tags' );

			// Post Bottom Share Box
			boombox_get_template_part( 'template-parts/single/components/share', '', array(
				'heading' => true,
				'before'  => '<div class="bb-post-share-box s-post-share-box bottom mb-md bb-mb-el">',
				'after'   => '</div>'
			) ); ?>

		</div>
		<?php // -/end Post Main Content for Card View ?>

		<?php
		// Sortable sections
		boombox_get_template_part( 'template-parts/single/components/sortable' ); ?>

	</article>
	<?php

	// Load content after the post content for a specific post format.
	do_action( 'balnp_load_after_content_type_' . $post_format );

	// Load content after the post content.
	do_action( 'balnp_load_after_content' );

}

// Load content after the loop.
do_action( 'alnp_load_after_loop' );
