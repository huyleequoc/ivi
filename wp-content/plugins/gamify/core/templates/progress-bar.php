<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * @var int     $progress           Current progress
 * @var string  $tooltip_content    Tooltip content
 * @var string  $classes            Tooltip classes
 * @version 1.0
 */
$progress           = isset( $progress ) ? absint( $progress ) : 0;
$tooltip_content    = isset( $tooltip_content ) && $tooltip_content ? $tooltip_content : '';
if( $tooltip_content ) {
	$classes = isset( $classes ) && $classes ? $classes : ''; ?>
	<div class="gfy-toggle-tooltip tooltip-minimal tooltip-progress-bar <?php echo $classes; ?>">
<?php } ?>

	<div class="gfy-progress-bar progress-bar">
		<div class="progress-bar-bg"></div>
		<div class="progress" style="width:<?php echo min( $progress, 100 ); ?>%"></div>
	</div>

<?php if( $tooltip_content ) {
	gfy_get_template_part( 'core/templates/tooltip.php', array(
		'content' => $tooltip_content
	) ); ?>
	</div>
<?php } ?>