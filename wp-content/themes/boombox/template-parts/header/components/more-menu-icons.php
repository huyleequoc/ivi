<?php
/**
 * The template part for displaying the site header more box
 *
 * @package BoomBox_Theme
 * @since   2.0.0
 * @version 2.0.0
 *
 * @var $header_helper Boombox_Header_Template_Helper Header Template Helper
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}
$header_helper = Boombox_Template::init( 'header' ); ?>

<nav class="more-navigation header-item bb-toggle pos-<?php echo $header_helper->get_component_location(); ?>">
	<a class="more-menu-toggle element-toggle bb-header-icon" role="button" href="#" data-toggle=".more-menu">
		<i class="bb-icon bb-ui-icon-dots"></i>
	</a>
	<?php boombox_get_template_part( 'template-parts/header/navigation/more' ); ?>
</nav>