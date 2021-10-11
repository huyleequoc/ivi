<?php

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Wrapper method for _doing_it_wrong() core method
 *
 * @param string $function The function that was called.
 * @param string $message  A message explaining what has been done incorrectly.
 * @param string $version  The version of WordPress where the message was added.
 *
 * @see _doing_it_wrong() for additional details
 */
function gfy_doing_it_wrong( $function, $message, $version ) {
	_doing_it_wrong( $function, $message, $version );
}

/**
 * Include template file
 * @param $template_name        string  Template file relative path to plugin directory:
 *                                      relative path will also be used for overwrite purposes from theme
 * @param array $template_data  array   Data to pass to included template
 */
function gfy_get_template_part( $template_name, $template_data = array() ) {

	/***** add .php suffix to template name if it's missed */
	$extension = pathinfo( $template_name, PATHINFO_EXTENSION );
	if( ! $extension ) {
		$template_name .= '.php';
	}

	/***** Populate possible template names */
	$template_names = array(
		'gamify/' . $template_name,
		'gamify/' . str_replace( '/templates', '', $template_name ),
		'gamify/' . str_replace( basename( $template_name ), 'templates/' . basename( $template_name ), $template_name )
	);

	/***** locate template */
	$located = locate_template( $template_names );

	/***** Use default template if template file is not overwritten */
	$template_path = $located ? $located : ( GFY_DIR . $template_name );

	/***** Finally, include template */
	if( is_file( $template_path ) ) {

		/***** All other to add custom data to template */
		$template_data = wp_parse_args(
			apply_filters( 'gfy/template_data', $template_data, $template_name ),
			$template_data
		);

		extract( $template_data );

		include $template_path;
	}
}

/**
 * Wrapper for shortcode parsing
 *
 * @param $shortcode            string  Shortcode name
 * @param array $attributes     array   Shortcode attributes
 * @return string
 * @throws Exception
 */
function gfy_do_shortcode( $shortcode, $attributes = array() ) {
	if( ! is_array( $attributes ) ) {
		throw new Exception( 'Variable $attributes should be an array' );
	}

	$attributes = apply_filters( 'gfy/shortcode/' . $shortcode . '/attributes', $attributes );

	return GFY_Shortcode_Management_Service::get_instance()->do_shortcode( $shortcode, $attributes );
}

/**
 * Get Facebook application ID
 * @return string|int
 * @since 1.1.3
 * @version 1.1.3
 */
function gfy_get_fb_app_id() {
	return apply_filters( 'gfy/fb_app_id', '' );
}