<?php
/**
 * The template part for displaying single post sortable sections
 *
 * @package BoomBox_Theme
 * @since   2.0.0
 * @version 2.5.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

$sections = boombox_get_single_sortable_sections();

do_action( 'boombox/single/before_sortables', $sections );
foreach ( $sections as $section ) {
	switch ( $section ) {
		case 'reactions':
			if ( 'post' == get_post_type() ) {
				do_action( 'boombox/single/sortables/before_reactions' );
				boombox_get_template_part( 'template-parts/single/components/reactions' );
				do_action( 'boombox/single/sortables/after_reactions' );
			}

			break;
		case 'author_info':
			if ( 'post' == get_post_type() ) {
				do_action( 'boombox/single/sortables/before_author_info' );
				boombox_get_template_part( 'template-parts/single/components/author', 'extended', array(
					'class' => 's-post-small-el mb-md bb-mb-el'
				) );
				do_action( 'boombox/single/sortables/after_author_info' );
			}

			break;
		case 'comments':
			if ( comments_open() || get_comments_number() ) {
				do_action( 'boombox/single/sortables/before_comments' );
				boombox_get_template_part( 'template-parts/single/components/comments', '', array(
					'class' => 's-post-small-el mb-md bb-mb-el'
				) );
				do_action( 'boombox/single/sortables/after_comments' );
			}

			break;
		case 'navigation':
			do_action( 'boombox/single/sortables/before_navigation' );
			boombox_get_template_part( 'template-parts/single/components/navigation' );
			do_action( 'boombox/single/sortables/after_navigation' );

			break;
		case 'related_posts':
			if ( 'post' == get_post_type() ) {
				do_action( 'boombox/single/sortables/before_related_posts' );
				boombox_get_template_part( 'template-parts/single/components/extra/posts', 'related' );
				do_action( 'boombox/single/sortables/after_related_posts' );
			}
			break;
		case 'more_from_posts':
			if ( 'post' == get_post_type() ) {
				do_action( 'boombox/single/sortables/before_more_from_posts' );
				boombox_get_template_part( 'template-parts/single/components/extra/posts', 'more-from' );
				do_action( 'boombox/single/sortables/after_more_from_posts' );
			}

			break;
		case 'dont_miss_posts':
			if ( 'post' == get_post_type() ) {
				do_action( 'boombox/single/sortables/before_dont_miss_posts' );
				boombox_get_template_part( 'template-parts/single/components/extra/posts', 'dont-miss' );
				do_action( 'boombox/single/sortables/after_dont_miss_posts' );
			}
			break;
		case 'subscribe_form':
			do_action( 'boombox/single/sortables/before_subscribe_form' );
			echo boombox_get_mailchimp_form_html( array( 'tag' => 'aside', 'class' => 's-post-small-el mb-lg bb-mb-el' ) );
			do_action( 'boombox/single/sortables/before_subscribe_form' );
			break;
		default:
			if( $section ){
				do_action( 'boombox/single/sortables/' . $section );
			}
	}
}
do_action( 'boombox/single/after_sortables', $sections );