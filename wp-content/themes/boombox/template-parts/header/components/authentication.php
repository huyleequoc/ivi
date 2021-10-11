<?php
/**
 * The template part for displaying the site header authentication box
 *
 * @package BoomBox_Theme
 * @since   2.0.0
 * @version 2.0.0
 *
 * @var $template_helper Boombox_Authentication_Template_Helper Authentication Template Helper
 * @var $header_template_helper Boombox_Header_Template_Helper Header Template Helper
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if( ! boombox_is_auth_allowed() ) {
	return;
}

$header_template_helper = Boombox_Template::init( 'header' );
$template_helper = Boombox_Template::init( 'authentication' );
$template_options = $template_helper->get_options();

do_action( 'boombox/before_authentication', $template_options, array(
	'auth' => $template_helper,
	'header' => $header_template_helper
) ); ?>
<div class="header-item bb-header-user-box bb-toggle pos-<?php echo $header_template_helper->get_component_location(); ?>">
	<a class="bb-header-icon <?php echo $template_options['class']; ?>" role="button" data-toggle=".bb-header-user-box .menu" href="<?php echo $template_options['url']; ?>">
		<?php echo $template_options['image']; ?>
	</a>
	<?php echo $template_options['navigation']; ?>
</div>
<?php do_action( 'boombox/after_authentication', $template_options, array(
	'auth' => $template_helper,
	'header' => $header_template_helper
) ); ?>