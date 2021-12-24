<?php

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}


if (  ! class_exists( 'Boombox_Walker_Featured_Labels_Nav_Menu' ) ) {
	/**
	* Custom walker class.
	 */
	class Boombox_Walker_Featured_Labels_Nav_Menu extends Walker_Nav_Menu {
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

			$args_before = '';
			$args_after = '';
			$args_link_before = '';
			$args_link_after = '';
			if( is_object( $args ) ){
				$args_before = $args->before;
				$args_after = $args->after;
				$args_link_before = $args->link_before;
				$args_link_after = $args->link_after;
			}elseif( is_array( $args ) ){
				$args_before = $args['before'];
				$args_after = $args['after'];
				$args_link_before = $args['link_before'];
				$args_link_after = $args['link_after'];
			}

			$classes = empty( $item->classes ) ? array() : (array)$item->classes;
			$classes[] = 'menu-item-' . $item->ID;

			// Icon
			$icon = '';
			if ( $icon_name = boombox_get_post_meta( $item->ID, '_menu_item_icon' ) ) {
				$icon = '<i class="bb-icon bb-icon-' . $icon_name . '"></i>';
				$classes[] = 'menu-item-icon';
			}

			$args = apply_filters( 'nav_menu_item_args', $args, $item, $depth );

			$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
			$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

			$id = apply_filters( 'nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args, $depth );
			$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

			$args_before = '<li' . $id . $class_names . '>' . $args_before;
			$args_after .= '</li>';

			// Build HTML output and pass through the proper filter.
			$item_output = sprintf( '%1$s<a%2$s>%3$s%4$s%5$s%6$s</a>%7$s',
				/* -1- */
				$args_before,
				/* -2- */
				$attributes,
				/* -3- */
				$icon,
				/* -4- */
				$args_link_before,
				/* -5- */
				apply_filters( 'the_title', $item->title, $item->ID ),
				/* -6- */
				$args_link_after,
				/* -7- */
				$args_after
			);

			$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
		}

		/**
		 * End the element output.
		 *
		 * @param string $output
		 * @param object $item
		 * @param int $depth
		 * @param array $args
		 */
		function end_el(&$output, $item, $depth = 0, $args = array())
		{
			$output .= "";
		}
	}
}
