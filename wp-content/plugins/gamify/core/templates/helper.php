<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * @var string $tooltip_content     Tooltip content
 * @var string $classes             Tooltip classes
 * @version 1.0
 */
$classes = isset( $classes ) ? $classes : 'tooltip-general';
$tooltip_content = isset( $tooltip_content ) ? $tooltip_content : '';
?>
<div class="gfy-toggle-tooltip tooltip-icon-style <?php echo $classes; ?>">
	<span class="gfy-icon-btn icon-xs gfy-icon-info"></span>
	<?php gfy_get_template_part( 'core/templates/tooltip.php', array( 'content' => $tooltip_content ) ); ?>
</div>