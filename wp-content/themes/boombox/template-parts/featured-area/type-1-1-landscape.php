<?php
/**
 * The template part for displaying featured area "1-1-landscape" template
 *
 * @package BoomBox_Theme
 * @since   2.0.0
 * @version 2.5.0
 *
 * @var $template_helper Boombox_Featured_Area_Template_Helper Template Helper
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

$template_helper = Boombox_Template::init( 'featured-area' );
$template_query = $template_helper->get_query();
$template_options = $template_helper->get_options();
if ( $template_query && $template_query->have_posts() ) {
	$boombox_area_elements = $template_helper->fill_template_absentee_items( $template_query->posts, 'type-1-1-landscape' ); ?>
	<div class="container bb-featured-area boxed item-1-1-landscape <?php echo $template_options['class']; ?>">
		<div class="featured-area-wrapper">
			<div class="f-col col1">
				<?php
				$name = $template_helper->get_item_template_part_name( $boombox_area_elements[ 0 ] );
				boombox_get_template_part( 'template-parts/featured-area/loop', $name ); ?>
			</div>
			<div class="f-col col2">
				<?php
				$name = $template_helper->get_item_template_part_name( $boombox_area_elements[ 1 ] );
				boombox_get_template_part( 'template-parts/featured-area/loop', $name ); ?>
			</div>
		</div>
	</div>
<?php }
wp_reset_postdata(); ?>