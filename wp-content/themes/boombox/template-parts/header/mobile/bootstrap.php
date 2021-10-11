<?php
/**
 * The template part for displaying the site mobile navigation
 *
 * @package BoomBox_Theme
 * @since   2.0.0
 * @version 2.0.0
 *
 * @var $template_helper Boombox_Header_Template_Helper Header Template Helper
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

$template_helper = Boombox_Template::init( 'header' );
boombox_get_template_part( 'template-parts/header/mobile/template', $template_helper->get_mobile_layout() ); ?>
