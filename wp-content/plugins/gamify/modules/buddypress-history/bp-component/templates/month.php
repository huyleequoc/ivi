<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * @var $component_classes      string              Component HTML classes
 * @var $component_id           string              Component unique id
 * @var $shortcode_config       array<string,mixed> Shortcode attributes
 * @version 1.0
 */
$local_config = array();
$shortcode_config = array_merge( $shortcode_config, $local_config ); ?>
<div class="<?php echo $component_classes; ?>">
	<?php echo gfy_do_shortcode( 'mycred_history', $shortcode_config ); ?>
</div>
