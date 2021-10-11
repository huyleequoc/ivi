<?php
/**
 * The template part for displaying the "Burger Icon" menu component
 *
 * @package BoomBox_Theme
 * @since   2.0.0
 * @version 2.0.4
 * @var $template_helper Boombox_Header_Template_Helper Header Template Helper
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}
$template_helper = Boombox_Template::init( 'header' ); ?>

<a id="menu-button"
   class="header-item bb-header-icon menu-button pos-<?php echo $template_helper->get_component_location(); ?>"
   role="button"
   href="#">
	<i class="bb-ui-icon-burger-menu"></i>
</a>