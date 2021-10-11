<?php

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}


if ( ! class_exists( 'Boombox_Walker_Badges_Nav_Menu' ) ) {
	/**
	 * Custom walker class.
	 */
	class Boombox_Walker_Badges_Nav_Menu extends Walker_Nav_Menu {
		/**
		 * Start the element output.
		 *
		 * Adds main/sub-classes to the list items and links.
		 *
		 * @param string $output Passed by reference. Used to append additional content.
		 * @param object $item   Menu item data object.
		 * @param int    $depth  Depth of menu item. Used for padding.
		 * @param array  $args   An array of arguments. @see wp_nav_menu()
		 * @param int    $id     Current item ID.
		 */
		function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
			// Link attributes.
			$attributes = ! empty( $item->title ) ? ' title="' . esc_attr( $item->title ) . '"' : '';
			$attributes .= ! empty( $item->target ) ? ' target="' . esc_attr( $item->target ) . '"' : '';
			$attributes .= ! empty( $item->xfn ) ? ' rel="' . esc_attr( $item->xfn ) . '"' : '';
			$attributes .= ! empty( $item->url ) ? ' href="' . esc_attr( $item->url ) . '"' : '';

			$classes = 'bb-badge badge';
			$classes .= 'reaction' == $item->object ? apply_filters( 'boombox_badge_wrapper_advanced_classes', ' reaction', 'reaction', $item->object_id ) : '';
			$classes .= 'category' == $item->object ? apply_filters( 'boombox_badge_wrapper_advanced_classes', ' category', 'category', $item->object_id ) : '';
			$classes .= 'post_tag' == $item->object ? apply_filters( 'boombox_badge_wrapper_advanced_classes', ' post_tag', 'post_tag', $item->object_id ) : '';

			$classes .= ( 'page' == $item->object && boombox_is_trending( $item->object_id ) ) ? apply_filters( 'boombox_badge_wrapper_advanced_classes', ' trending', 'trending', '' ) : '';
			$attributes .= ! empty( $classes ) ? ' class="' . esc_attr( $classes ) . '"' : '';

			$icon = '';
			if ( 'reaction' == $item->object ) {
				$image_url = boombox_get_reaction_icon_url( $item->object_id );
				$icon = ! empty( $image_url ) ? ' <img src="' . esc_url( $image_url ) . '" alt="' . $item->title . '">' : '';
			} else if ( in_array( $item->object, array( 'category', 'post_tag' ) ) ) {
				$term_icon = boombox_get_term_icon_html( $item->object_id, '', $item->object );
				$icon = $term_icon ? $term_icon : '';
			} else if ( boombox_is_trending( $item->object_id ) ) {
				$trending_icon_name = boombox_get_trending_icon_name( 'post_id', $item->object_id );
				$icon = ! empty( $trending_icon_name ) ? '<i class="bb-icon bb-ui-icon-' . $trending_icon_name . '"></i>' : '';
			}

			$args_before = '';
			$args_after = '';
			$args_link_before = '';
			$args_link_after = '';
			if ( is_object( $args ) ) {
				$args_before = $args->before;
				$args_after = $args->after;
				$args_link_before = $args->link_before;
				$args_link_after = $args->link_after;
			} else if ( is_array( $args ) ) {
				$args_before = $args[ 'before' ];
				$args_after = $args[ 'after' ];
				$args_link_before = $args[ 'link_before' ];
				$args_link_after = $args[ 'link_after' ];
			}

			// Build HTML output and pass through the proper filter.
			$item_output = sprintf( '<li>%1$s<a%2$s><span class="circle">%3$s</span><span class="text">%4$s%5$s%6$s</span></a>%7$s</li>',
				$args_before,
				$attributes,
				$icon,
				$args_link_before,
				apply_filters( 'the_title', $item->title, $item->ID ),
				$args_link_after,
				$args_after
			);

			$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
		}

		/**
		 * End the element output.
		 *
		 * @param string $output
		 * @param object $item
		 * @param int    $depth
		 * @param array  $args
		 */
		function end_el( &$output, $item, $depth = 0, $args = array() ) {
			$output .= "";
		}
	}
}
