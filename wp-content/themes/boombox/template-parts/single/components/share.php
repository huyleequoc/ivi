<?php
/**
 * The template part for displaying the share buttons section
 *
 * @package BoomBox_Theme
 * @since   1.0.0
 * @version 2.5.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * @var $wp_the_query WP_Query Global template query
 */
global $wp_the_query;
$elements = array();
if( $wp_the_query->is_home() ) {
	$elements = boombox_get_theme_option( 'home_main_posts_share_bar_elements' );
} elseif( $wp_the_query->is_page() ) {
	$elements = boombox_get_post_meta( $wp_the_query->get_queried_object_id(), 'boombox_listing_share_bar_elements' );
} elseif( $wp_the_query->is_archive() ) {
	$elements = boombox_get_theme_option( 'archive_main_posts_share_bar_elements' );
} elseif( $wp_the_query->is_single() ) {
	$elements = boombox_get_theme_option( 'single_post_general_share_box_elements' );
}
$elements = apply_filters( 'boombox_posts_share_bar_elements', $elements );

$has_share_buttons = ( boombox_plugin_management_service()->is_plugin_active( 'mashsharer/mashshare.php' ) || function_exists( 'essb_core' ) );

if ( empty( $elements ) && !$has_share_buttons ) {
	return;
}

echo Boombox_Template::get_clean( 'before' );

if( Boombox_Template::get_clean( 'heading' ) ) {
	echo boombox_get_share_box_heading();
} ?>
<div class="content <?php echo $has_share_buttons ? 'has-share-buttons' : ''; ?>">
	<?php
	$show_share = $show_points = $show_comments = false;
	
	if ( ! empty( $elements ) ) {
		
		$show_share = in_array( 'share_count', $elements );
		if ( $show_share && ( boombox_plugin_management_service()->is_plugin_active( 'mashsharer/mashshare.php' ) || boombox_plugin_management_service()->is_plugin_active( 'easy-social-share-buttons3/easy-social-share-buttons3.php' ) ) ) {
			echo boombox_get_post_share_count( array(
				'html' => true,
				'location' => 'share-box',
				'before' => '<div class="post-share-count bb-post-meta size-lg"><span class="post-meta-item">',
				'after'  => '</span></div>'
			) );
		}
		
		$show_points = in_array( 'points', $elements );
		if ( $show_points ) {
			echo boombox_get_post_points_html();
		}
		
		$show_comments = ( comments_open() && in_array( 'comments', $elements ) );
		if ( $show_comments ) {
			echo boombox_get_post_comments_count_html( array(
				'before' => '<div class="post-meta bb-post-meta size-lg">',
				'after'  => '</div>'
			) );
		}
		
	}
	
	if ( $has_share_buttons ) {
		echo boombox_get_post_share_buttons_html();
		echo boombox_get_post_share_mobile_buttons_html( array(
			'comments' => $show_comments,
			'shares'   => $show_share,
			'points'   => $show_points,
		) );
	} ?>
</div>
<?php echo Boombox_Template::get_clean( 'after' );