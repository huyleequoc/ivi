<?php
/**
 * Template to show single post taxonomy section
 * @since   2.5.0
 * @version 2.5.0
 * @var $helper Boombox_Single_Post_Template_Helper Template helper
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

$categories = Boombox_Template::get_clean( 'categories' );
$badges = Boombox_Template::get_clean( 'badges' );
if( ! $categories && $badges ) {
	return;
} ?>

<div class="s-post-header-top d-table-center-sm bb-mb-el">
	
	<?php
	// Categories
	if ( $categories ) {
		boombox_get_template_part( 'template-parts/single/components/categories', '', array(
			'before' => '<div class="d-table-cell text-left-sm">',
			'after'  => '</div>'
		) );
	}
	
	// Badges
	if ( $badges ) {
		boombox_get_template_part( 'template-parts/single/components/badges', '', array(
			'before' => '<div class="d-table-cell text-right-sm">',
			'after'  => '</div>'
		) );
	} ?>

</div>