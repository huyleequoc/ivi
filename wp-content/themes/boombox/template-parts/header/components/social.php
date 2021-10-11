<?php
/**
 * The template part for displaying the site header share
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

if ( function_exists( 'boombox_get_social_links' ) ) {
	$header_helper = Boombox_Template::init( 'header' ); ?>
	<div class="bb-header-share header-item bb-toggle pos-<?php echo $header_helper->get_component_location(); ?>">
		<a class="share-toggle element-toggle bb-header-icon" role="button" data-toggle=".social-dropdown">
			<i class="bb-icon bb-ui-icon-share-alt"></i>
		</a>
		<div class="social-dropdown bb-header-dropdown toggle-content social circle">
			<?php echo boombox_get_social_links( array( 'empty' => '' ) ); ?>
		</div>
	</div>
<?php } ?>