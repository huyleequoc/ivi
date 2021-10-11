<?php
/**
 * The template part for displaying the site mobile navigation "Brand Bottom" template
 *
 * @package BoomBox_Theme
 * @since   2.0.0
 * @version 2.0.0
 * @var $template_helper Boombox_Header_Template_Helper Header Template Helper
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

$template_helper = Boombox_Template::init( 'header' );
$branding_options = $template_helper->get_mobile_branding_options(); ?>

<div class="branding">
	<p class="site-title">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<?php
			if( ! empty( $branding_options['logo'] ) ) {
				$logo_attrs = array(
					'src="' . esc_url( $branding_options['logo'][ 'src' ] ) . '"',
					'alt="' . esc_attr( $branding_options['site_name'] ) . '"',
				);

				$width = absint( $branding_options['logo'][ 'width' ] );
				if ( $width ) {
					$logo_attrs[] = 'width="' . $width . '"';
				}

				$height = absint( $branding_options['logo'][ 'height' ] );
				if ( $height ) {
					$logo_attrs[] = 'height="' . $height . '"';
				}

				if ( isset( $branding_options['logo'][ 'src_2x' ] ) && $branding_options['logo'][ 'src_2x' ] ) {
					$logo_attrs[] = 'srcset="' . esc_attr( $branding_options['logo'][ 'src_2x' ] ) . '"';
				}
				$logo_attrs = implode( ' ', $logo_attrs ); ?>

				<img <?php echo $logo_attrs; ?> />
			<?php } else {
				echo esc_html( $branding_options['site_name'] );
			} ?>
		</a>
	</p>
</div>