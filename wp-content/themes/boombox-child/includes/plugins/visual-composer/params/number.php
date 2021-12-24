<?php

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

final class Boombox_VC_Number_Field {

	public static function render( $settings, $value ) {

		$min = '';
		if( isset( $settings['min'] ) ) {
			$min = 'min="' . $settings['min'] . '"';
		}

		$max = '';
		if( isset( $settings['max'] ) ) {
			if( isset( $settings['min'] ) ) {
				if( $settings['min'] < $settings['max'] ) {
					$max = 'max="' . $settings['max'] . '"';
				}
			} else {
				$max = 'max="' . $max . '"';
			}
		}

		ob_start();
		?>
		<div class="boombox_number">
			<input
				name="<?php echo esc_attr( $settings['param_name'] ); ?>"
				class="wpb_vc_param_value wpb-number <?php echo esc_attr( $settings['param_name'] ); ?> <?php echo esc_attr( $settings['type'] ); ?>_field"
				type="number" value="<?php echo esc_attr( $value ); ?>"
				<?php echo $min; ?>
				<?php echo $max; ?>
			/>
		</div>
		<?php

		return ob_get_clean();
	}

}
vc_add_shortcode_param( 'boombox_number', array( 'Boombox_VC_Number_Field', 'render' ) );