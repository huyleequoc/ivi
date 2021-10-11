<?php
/**
 * Shortcodes for content components
 *
 * @package BoomBox_Theme_Extensions
 */

// Prevent direct script access
if ( ! defined( 'ABSPATH' ) ) {
	die ( 'No direct script access allowed' );
}

global $boombox_components;
$boombox_components['tabs_on_page'] = 0;

/**
 * Tabs
 */
add_shortcode( 'boombox_tabs', 'boombox_tabs_group' );
add_shortcode( 'boombox_tab', 'boombox_tab' );

/**
 * Button
 */
add_shortcode( 'boombox_button', 'boombox_button' );

/**
 * Dropcap
 */
add_shortcode( 'boombox_dropcap', 'boombox_dropcap' );

/**
 * Tooltip
 */
add_shortcode( 'boombox_tooltip', 'boombox_tooltip' );

/**
 * Highlight
 */
add_shortcode( 'boombox_highlight', 'boombox_highlight' );

/**
 * Columns
 */
add_shortcode( 'boombox_row', 'boombox_row' );
add_shortcode( 'boombox_one_half', 'boombox_one_half' );
add_shortcode( 'boombox_one_third', 'boombox_one_third' );
add_shortcode( 'boombox_one_fourth', 'boombox_one_fourth' );

/**
 * Thumbnail
 */
add_shortcode( 'boombox_thumbnail', 'boombox_thumbnail' );


/**
 * Tabs container
 *
 * @param null $atts
 * @param null $content
 *
 * @return string
 */
function boombox_tabs_group( $atts = null, $content = null ) {
	global $boombox_components;
	$a = shortcode_atts( array(
		'layout' => 'horizontal', //  horizontal|vertical
		'active'     => 1,
		'classes'    => ''
	), $atts, 'boombox_tabs_group' );

	$boombox_components['tabs_titles'] = array();
	$boombox_components['tabs_on_page'] ++;
	$boombox_components['tabs_active'] = absint( $a['active'] );

	$layout      = esc_html( $a['layout'] );
	$classes     = esc_html( $a['classes'] );
	$tab_type    = esc_html( $a['layout'] );

	$tab_content = do_shortcode( $content );

	$output = '<div class="bb-wpc-tabs mb-md bb-tabs tabs-' . $tab_type . ' ' . $classes . '">';
	$output .= '<ul class="tabs-menu">';
	foreach ( $boombox_components['tabs_titles'] as $key => $title ) {
		$id       = $key + 1;
		$tab_slug = sanitize_title( $title );
		$output .= sprintf( '<li %1$s><a href="#%2$s">%3$s</a></li>',
			$id == $boombox_components['tabs_active'] ? ' class="active"' : '',
			'tab' . $boombox_components['tabs_on_page'] . '-' . $tab_slug,
			$title
		);
	}
	$output .= '</ul>';

	$output .= '<div class="tabs-content">';
	$output .= $tab_content;
	$output .= '</div>';
	$output .= '</div>';

	// reset tabs
	$boombox_components['tabs_titles'] = array();
	$boombox_components['tabs_active'] = 1;

	return $output;
}

/**
 * Tab Item
 *
 * @param null $atts
 * @param null $content
 *
 * @return string
 */
function boombox_tab( $atts = null, $content = null ) {
	global $boombox_components;
	$a = shortcode_atts( array(
		'title' => esc_html__( 'Title', 'boombox-theme-extensions' )
	), $atts, 'boombox_tab' );

	$title = esc_html( $a['title'] );

	array_push( $boombox_components['tabs_titles'], $title );

	$id       = count( $boombox_components['tabs_titles'] );
	$tab_slug = sanitize_title( $title );
	$output   = sprintf( '<div id="%1$s" class="tab-content %2$s">%3$s</div>',
		'tab' . $boombox_components['tabs_on_page'] . '-' . $tab_slug,
		$id == $boombox_components['tabs_active'] ? 'active' : '',
		do_shortcode( $content )
	);

	return $output;
}

/**
 * Button
 *
 * @param null $atts
 * @param null $content
 *
 * @return string
 */
function boombox_button( $atts = null, $content = null ) {
	$a = shortcode_atts( array(
		'tag_type'         => 'a',       // a|button
		'type'             => '',        // primary|secondary|success|info|warning|danger
		'size'             => '',        // large|small
		'url'              => get_option( 'home' ),
		'target'           => 'self',    // blank|self
		'background_color' => '',
		'text_color'       => '',
		'class'            => ''

	), $atts, 'boombox_button' );

	$classes    = array();
	$attributes = array();
	$styles     = array();

	$tag_type         = esc_html( $a['tag_type'] );
	$type             = esc_html( $a['type'] );
	$size             = esc_html( $a['size'] );
	$url              = esc_url( $a['url'] );
	$target           = esc_attr( $a['target'] );
	$background_color = esc_html( $a['background_color'] );
	$text_color       = esc_html( $a['text_color'] );
	$class            = esc_attr( $a['class'] );

	$classes[] = 'bb-btn';

	if( empty( $tag_type ) ){
		$tag_type = 'a';
	}

	if( !empty( $type ) && in_array( $type, array( 'primary', 'secondary', 'success', 'info', 'warning', 'danger') ) ){
		$classes[] = 'bb-btn-' . $type;
	}

	if( !empty( $size ) && in_array( $size, array( 'large', 'small' ) ) ){
		$classes[] = 'large' == $size ? 'bb-btn-lg' : 'bb-btn-sm';
	}

	if( !empty( $class ) ){
		if( is_string( $class ) ){
			$class = explode( ' ', $class );
		}
		if( is_array( $class ) ){
			$classes = array_merge( $classes, $class);
		}
	}

	if( !empty( $background_color ) ){
		$styles[] = 'background-color: ' . $background_color;
		$styles[] = 'border-color: ' . $background_color;
	}

	if( !empty( $text_color ) ){
		$styles[] = 'color: ' . $text_color;
	}

	if( 'a' == $tag_type ) {
		if( !empty( $url ) ){
			$attributes[] = 'href="' . $url . '"';
		}
		if( !empty( $target ) && 'blank' == $target ){
			$attributes[] = 'target="_' . $target . '" rel="noopener"';
		}
	}

	$classes    = implode( ' ', $classes );
	$attributes = implode( '', $attributes );
	$styles     = 'style="' . implode( '; ', $styles ) . '"';

	if( 'button' == $tag_type ) {
		return sprintf( '<button type="button" class="%1$s" %2$s>%3$s</button>',
			$classes,
			$styles,
			do_shortcode( stripcslashes( $content ) )
		);
	}else{
		return sprintf( '<a class="%1$s" %2$s %3$s>%4$s</a>',
			esc_attr( $classes ),
			$attributes,
			$styles,
			do_shortcode( stripcslashes( $content ) )
		);
	}
}

/**
 * Dropcap
 *
 * @param null $atts
 * @param null $content
 *
 * @return string
 */
function boombox_dropcap( $atts = null, $content = null ) {
	$a = shortcode_atts( array(
		'style'     => '', // primary
		'font_size' => '',
		'color'     => '',
		'class'     => ''
	), $atts, 'boombox_dropcap' );

	$classes    = array();
	$styles      = array();

	$classes[]  = 'bb-text-dropcap';

	$style     = esc_attr( $a['style'] );
	$font_size = absint( $a['font_size'] );
	$color     = esc_attr( $a['color'] );
	$class     = esc_attr( $a['class'] );

	if( 'primary' == $style ){
		$classes[] = 'primary-color';
	}

	if( !empty( $font_size ) ){
		$font_size = intval( $font_size ) . 'px';
		$styles[] = 'font-size:' . $font_size . '';
	}

	if( !empty( $color ) ){
		$styles[] = 'color:' . $color . '';
	}

	if( !empty( $class ) ){
		if( is_string( $class ) ){
			$class = explode( ' ', $class );
		}
		if( is_array( $class ) ){
			$classes = array_merge( $classes, $class);
		}
	}

	$classes    = implode( ' ', $classes );
	$styles     = 'style="' . implode( '; ', $styles ) . '"';

	return sprintf( '<span class="%1$s" %2$s>%3$s</span>',
		esc_attr( $classes ),
		$styles,
		do_shortcode( $content )
	);

}

/**
 * Tooltips
 *
 * @param null $atts
 * @param null $content
 *
 * @return string
 */
function boombox_tooltip( $atts = null, $content = null ) {
	$a = shortcode_atts( array(
		'title'            => __( 'Tooltip text', 'boombox-theme-extensions' ),
		'class'            => ''
	), $atts, 'boombox_tooltip' );

	$classes    = array();
	$attributes = array();

	$title            = esc_html( $a['title'] );
	$class            = esc_attr( $a['class'] );

	$classes[]  = 'bb-tooltip';

	if( !empty( $title ) ){
		$attributes[] = 'title="' . $title . '"';
	}

	if( !empty( $class ) ){
		if( is_string( $class ) ){
			$class = explode( ' ', $class );
		}
		if( is_array( $class ) ){
			$classes = array_merge( $classes, $class);
		}
	}

	$classes    = implode( ' ', $classes );
	$attributes = implode( '', $attributes );

	return sprintf( '<span class="%1$s" %2$s>%3$s</span>',
		esc_attr( $classes ),
		$attributes,
		do_shortcode( $content )
	);
}

/**
 * Highlight
 *
 * @param null $atts
 * @param null $content
 *
 * @return string
 */
function boombox_highlight( $atts = null, $content = null ) {
	$a = shortcode_atts( array(
		'style'            => '', // primary
		'background_color' => '',
		'text_color'       => '',
		'class'            => ''
	), $atts, 'boombox_highlight' );

	$classes = array();
	$styles  = array();

	$style            = esc_html( $a['style'] );
	$background_color = esc_html( $a['background_color'] );
	$text_color       = esc_html( $a['text_color'] );
	$class            = esc_attr( $a['class'] );

	$classes[] = 'bb-text-highlight';

	if( 'primary' == $style ){
		$classes[] = 'primary-color';
	}

	if( !empty( $background_color ) ){
		$styles[] = 'background-color: ' . $background_color;
	}

	if( !empty( $text_color ) ){
		$styles[] = 'color: ' . $text_color;
	}

	if( !empty( $class ) ){
		if( is_string( $class ) ){
			$class = explode( ' ', $class );
		}
		if( is_array( $class ) ){
			$classes = array_merge( $classes, $class);
		}
	}

	$classes    = implode( ' ', $classes );
	$styles     = 'style="' . implode( '; ', $styles ) . '"';

	return sprintf( '<span class="%1$s" %2$s>%3$s</span>',
		esc_attr( $classes ),
		$styles,
		do_shortcode( $content )
	);
}

/**
 * Grid Row
 *
 * @param null $atts
 * @param null $content
 *
 * @return string
 */
function boombox_row( $atts = null, $content = null ){
	$a = shortcode_atts( array(
		'class'            => ''
	), $atts, 'boombox_row' );

	$class  = esc_attr( $a['class'] );

	return sprintf( '<div class="bb-wpc-row row mb-md %1$s">%2$s</div>',
		$class,
		do_shortcode( $content )
	);
}

/**
 * One Half Column
 *
 * @param null $atts
 * @param null $content
 *
 * @return string
 */
function boombox_one_half( $atts = null, $content = null ){
	$a = shortcode_atts( array(
		'class'            => ''
	), $atts, 'boombox_one_half' );

	$class  = esc_attr( $a['class'] );

	return sprintf( '<div class="col-sm-6 %1$s">%2$s</div>',
		$class,
		do_shortcode( $content )
	);
}

/**
 * One Third Column
 *
 * @param null $atts
 * @param null $content
 *
 * @return string
 */
function boombox_one_third( $atts = null, $content = null ){
	$a = shortcode_atts( array(
		'class'            => ''
	), $atts, 'boombox_one_third' );

	$class  = esc_attr( $a['class'] );

	return sprintf( '<div class="col-sm-4 %1$s">%2$s</div>',
		$class,
		do_shortcode( $content )
	);
}

/**
 * One Fourth Column
 *
 * @param null $atts
 * @param null $content
 *
 * @return string
 */
function boombox_one_fourth( $atts = null, $content = null ){
	$a = shortcode_atts( array(
		'class'            => ''
	), $atts, 'boombox_one_fourth' );

	$class  = esc_attr( $a['class'] );

	return sprintf( '<div class="col-sm-6 col-md-3 %1$s">%2$s</div>',
		$class,
		do_shortcode( $content )
	);
}

/**
 * Thumbnail
 *
 * @param null $atts
 * @param null $content
 *
 * @return string
 */
function boombox_thumbnail( $atts = null, $content = null ){
	$a = shortcode_atts( array(
		'style' => '' // circle||border
	), $atts, 'boombox_thumbnail' );

	$style = esc_attr( $a['style'] );

	if( 'circle' == $style ){
		$class = 'bb-circle-thumb';
	}else{
		$class = 'bb-border-thumb';
	}

	return sprintf( '<div class="%1$s">%2$s</div>',
		esc_attr( $class ),
		do_shortcode( $content )
	);
}