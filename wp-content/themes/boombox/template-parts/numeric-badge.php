<?php
/**
 * The template part for displaying the site featured strip
 *
 * @package BoomBox_Theme
 * @since   2.0.0
 * @version 2.0.0
 *
 */

global $wp_query;
$posts_per_page = $wp_query->query_vars[ 'posts_per_page' ];
$paged = isset( $wp_query->query[ 'paged' ] ) ? absint( $wp_query->query[ 'paged' ] ) : 1;

$item_index = (int)$wp_query->current_post + 1;
if ( $paged > 1 ) {
	$item_index += $posts_per_page * ( $paged - 1 );
} ?>
<div class="post-number"><?php echo esc_html( $item_index ); ?></div>