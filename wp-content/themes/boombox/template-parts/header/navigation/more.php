<?php
/**
 * The template part for displaying the site more navigation
 *
 * @package BoomBox_Theme
 * @since   2.0.0
 * @version 2.0.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

?>
<div id="more-menu" class="more-menu bb-header-dropdown toggle-content">
	<div class="more-menu-header">
		<?php
		boombox_get_template_part( 'template-parts/header/navigation/burger', 'badges' );
		boombox_get_template_part( 'template-parts/header/navigation/burger', 'top' ); ?>
	</div>
	<?php boombox_get_template_part( 'template-parts/header/navigation/burger', 'bottom' ); ?>
	<div class="more-menu-footer">
		<?php
		boombox_get_template_part( 'template-parts/header/community' );
			$show_socials = (
				function_exists( 'boombox_get_social_links' )
				&& ! apply_filters( 'boombox/header/navigation_more/disable_socials', false )
			);

			if ( $show_socials ) { ?>
		<div class="social circle"><?php echo boombox_get_social_links(); ?></div>
		<?php } ?>
	</div>
</div>