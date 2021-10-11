<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * @var $title           string                 Widget title
 * @var $tooltip_content string                 Widget tooltip content
 * @var $module          myCRED_Badge_Module    Badges module instance
 * @version 1.0
 */
?>

<!--
	1. data-badge-count: count of badges after which more badge is shown
	2. data-execute: can have two values: mobile and all
	                 if mobile - will execute more functionality only in mobile
	                 if all    - will execute more functionality both in mobile and desktop
	3. In order to turn off badge more functionality, please remove gfy-badge-more-func class and data-badge-count, data-execute attributes.
-->
<div class="gfy-bp-component gfy-user-achievements gfy-badge-more-func" data-badge-count="5" data-execute="mobile">
	<?php if( $title || $tooltip_content ) { ?>

	<div class="header-block">

		<?php if( $title ) { ?>
		<h3 class="gfy-title title-sm"><?php echo $title; ?></h3>
		<?php }

		if( $tooltip_content ) {
			gfy_get_template_part( 'core/templates/helper.php', array(
				'tooltip_content' => $tooltip_content
			) );
		} ?>
	</div>
	<?php }
	    $module->insert_into_buddypress();
	?>
</div>
