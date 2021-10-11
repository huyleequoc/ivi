<?php
/**
 * The template part for displaying the "Mode Switcher"
 *
 * @package BoomBox_Theme
 * @since   2.6.0
 * @version 2.6.0
 * @var $template_helper Boombox_Header_Template_Helper Header Template Helper
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}
$template_helper = Boombox_Template::init( 'header' ); ?>

<div class="bb-mode-switcher header-item">
	<a href="#" class="bb-mode-toggle bb-header-icon" role="button" rel="nofollow">
		<i class="bb-placeholder bb-icon bb-ui-icon-sun"></i>
		<i class="bb-day-mode-icon bb-icon bb-ui-icon-sun"></i>
		<i class="bb-night-mode-icon bb-icon bb-ui-icon-moon"></i>
	</a>
</div>