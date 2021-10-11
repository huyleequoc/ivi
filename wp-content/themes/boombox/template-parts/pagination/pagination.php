<?php
/**
 * The template part for displaying "Prev/Next" pagination.
 *
 * @package BoomBox_Theme
 * @since   1.0.0
 * @version 2.0.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

global $wp_query;
$boombox_posts_per_page = absint( $wp_query->get( 'posts_per_page' ) );
if( ! $boombox_posts_per_page ) {
	return;
}

$paged = max( ( absint( $wp_query->get( 'paged' ) ) ? absint( $wp_query->get( 'paged' ) ) : 1 ), 1 );
$total = absint( $wp_query->max_num_pages );

$page_links = paginate_links( array(
	'base'      => str_replace( PHP_INT_MAX, '%#%', esc_url( get_pagenum_link( PHP_INT_MAX ) ) ),
	'format'    => '?paged=%#%',
	'prev_text' => esc_html__( 'Previous', 'boombox' ),
	'next_text' => esc_html__( 'Next', 'boombox' ),
	'total'     => $total,
	'current'   => $paged,
	'type'      => 'array',
	'end_size'  => 1,
	'mid_size'  => 1,
) );

if ( ! empty( $page_links ) ) { ?>
	<nav class="bb-wp-pagination" role="navigation">
		<ul class="pg-list">
			<li class="pg-item"><?php echo join( "</li>\n\t<li class=\"pg-item\">", $page_links ); ?></li>
		</ul>
	</nav>
<?php
}