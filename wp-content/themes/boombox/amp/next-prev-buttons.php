<?php
/**
 * The template part for displaying the Next/Prev buttons
 *
 * @package BoomBox_Theme
 * @since 1.0
 * @version 2.0.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

global $page, $numpages;
$set = boombox_get_theme_options_set( array(
	'single_post_general_navigation_direction',
	'single_post_general_next_prev_buttons'
) );
boombox_single_post_link_pages( array(
	'before'                => '<nav class="next-prev-pagination clearfix text-center m-b-md"><ul>',
	'after'                 => '</ul></nav>',
	'link_class'            => 'nav-link bb-btn btn-primary text-center hvr-btm-shadow',
	'prev_class'            => 'page-nav prev-page pull-left text-left',
	'next_class'            => 'page-nav next-page pull-right text-right',
	'reverse'               => ( $set['single_post_general_navigation_direction'] == 'to-oldest' ),
	'link_wrap_before'      => '<li class="%s">',
	'link_wrap_after'       => '</li>',
	'go_to_prev_next'       => $set['single_post_general_next_prev_buttons'],
	'paging'                => sprintf( '<li class="pages col-inl-blck"><span class="text cur-page vmiddle">%1$d</span><span class="text all-pages vmiddle"> / %2$d</span></li>', $page, $numpages ),
	'previous_page_link'    => sprintf( '<i class="icon icon-chevron-left icn-left"></i><span class="text vmiddle">%1$s</span>', esc_html__( 'Previous Page', 'boombox' ) ),
	'next_page_link'        => sprintf( '<i class="icon icon-chevron-right icn-right"></i><span class="text vmiddle">%1$s</span>', esc_html__( 'Next Page', 'boombox' ) ),
	'previous_post_link'    => sprintf( '<i class="icon icon-chevron-left icn-left"></i><span class="text vmiddle">%1$s</span>', esc_html__( 'Previous Post', 'boombox' ) ),
	'next_post_link'        => sprintf( '<i class="icon icon-chevron-right icn-right"></i><span class="text vmiddle">%1$s</span>', esc_html__( 'Next Post', 'boombox' ) )
) );