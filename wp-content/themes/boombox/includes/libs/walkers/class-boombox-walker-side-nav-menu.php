<?php
/**
 * Custom Walker For Side Navigation
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! class_exists( 'Boombox_Walker_Side_Nav_Menu' ) ) {

	class Boombox_Walker_Side_Nav_Menu extends Walker_Nav_Menu {

		function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {

			$child_arrow = '';
			if( ! in_array( $args->theme_location, array( 'badges_nav', 'burger_badges_nav' ) ) ) {

				$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

				$classes = empty( $item->classes ) ? array() : (array)$item->classes;
				$classes[] = 'menu-item-' . $item->ID;

				// Icon
				$icon = '';
				$icon_name = boombox_get_post_meta( $item->ID, '_menu_item_icon' );
				if ( $icon_name ) {
					$icon = '<i class="bb-icon bb-icon-' . esc_attr( $icon_name ) . '"></i>';
					$classes[] = 'menu-item-icon';
				}

				// Arrow icon for sub-menu
				if ( in_array( 'menu-item-has-children', $classes ) ) {
					$child_arrow = '<span class="dropdown-toggle icon-chevron-down"></span>';
				}

				$args = apply_filters( 'nav_menu_item_args', $args, $item, $depth );

				$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
				$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

				$id = apply_filters( 'nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args, $depth );
				$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

				$output .= $indent . '<li' . $id . $class_names . '>';

				$atts = array();
				$atts[ 'title' ] = ! empty( $item->attr_title ) ? $item->attr_title : '';
				$atts[ 'target' ] = ! empty( $item->target ) ? $item->target : '';
				$atts[ 'rel' ] = ! empty( $item->xfn ) ? $item->xfn : '';
				$atts[ 'href' ] = ! empty( $item->url ) ? $item->url : '';

				$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

				$attributes = '';
				foreach ( $atts as $attr => $value ) {
					if ( ! empty( $value ) ) {
						$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
						$attributes .= ' ' . $attr . '="' . $value . '"';
					}
				}

				$title = apply_filters( 'the_title', $item->title, $item->ID );
				$title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );

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

				$item_output = $args_before;
				$item_output .= '<a' . $attributes . '>';
				$item_output .= $icon;
				$item_output .= $args_link_before . $title . $args_link_after;
				$item_output .= '</a>';
				$item_output .= $child_arrow;
				$item_output .= $args_after;

				$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
			}

		}

	}

}