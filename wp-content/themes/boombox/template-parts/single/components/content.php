<?php
/**
 * Template part to render single post content body
 * @since 2.5.0
 * @version 2.5.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if( Boombox_Template::get_clean( 'protect_content' ) ) { ?>
	<figure class="s-post-thumbnail post-thumbnail bb-mb-el">
		<a href="<?php echo esc_url( '#sign-in' ); ?>" class="entry-nsfw js-authentication"><?php echo boombox_get_nsfw_message(); ?></a>
	</figure>
	<?php
	return;
} ?>

<div itemprop="articleBody" class="s-post-content s-post-small-el bb-mb-el"><?php the_content(); ?></div>

<?php
// Pagination
boombox_get_template_part( 'template-parts/single/components/pagination', '', array(
	'layout' 			=> Boombox_Template::get_clean( 'pagination_layout' ),
	'next_prev_posts'	=> boombox_get_theme_option( 'single_post_general_next_prev_buttons' )
) );

do_action( 'boombox/single/after_main_content' );