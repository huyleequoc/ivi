<?php
/**
 * The template part for displaying the site header search box
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

$header_helper = Boombox_Template::init( 'header' );
$search_placeholder = esc_html__( 'Search and hit enter', 'boombox' );
$search_query = get_search_query(); ?>
<div class="bb-header-search header-item bb-toggle bb-focus pos-<?php echo $header_helper->get_component_location(); ?>">
	<a class="form-toggle element-toggle element-focus bb-header-icon" href="#" role="button" data-toggle=".search-dropdown" data-focus=".search-form input">
		<i class="bb-icon bb-ui-icon-search"></i>
	</a>
	<div class="search-dropdown bb-header-dropdown toggle-content">
		<form role="search" method="get" class="search-form form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<input type="search" class="pull-left" name="s" value="<?php echo $search_query; ?>">
			<button class="search-submit pull-right"
			        type="submit"><?php esc_html_e( 'Search', 'boombox' ); ?></button>
		</form>
	</div>
</div>
