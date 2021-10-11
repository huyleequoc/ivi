<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * @var string $content Tooltip content
 * @version 1.0
 */

if( isset( $content ) && $content ) {
?>
	<div class="gfy-tooltip">
		<div class="tooltip-inner">
			<span class="tooltip-arrow"></span>
			<?php echo $content; ?>
		</div>
	</div>
<?php } ?>