<?php
/**
 * Text field for metaboxes
 *
 * @package "All In One Meta" library
 * @since   1.0.0
 * @version 1.0.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! class_exists( 'AIOM_Base_Field' ) ) {
	require_once( 'base-field.php' );
}

if ( ! class_exists( 'AIOM_URL_Field' ) && class_exists( 'AIOM_Base_Field' ) ) {

	/**
	 * Class AIOM_URL_Field
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	final class AIOM_URL_Field extends AIOM_Base_Field {

		/**
		 * Parse field arguments
		 *
		 * @param array $args Field arguments
		 *
		 * @return array
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public static function parse_field_args( $args ) {
			$args = wp_parse_args( $args, array(
				'id'                 => '',
				'name'               => '',
				'default'            => '',
				'label'              => '',
				'description'        => '',
				'order'              => 10,
				'sub_order'          => 10,
				'standalone'         => false,
				'attributes'         => '',
				'class'              => '',
				'wrapper_class'      => '',
				'wrapper_attributes' => '',
				'table_col'          => null,
				'sanitize_callback'  => 'sanitize_text_field',
				'render_callback'    => array( __CLASS__, 'filter_value' ),
				'active_callback'    => array(),
			) );

			return $args;
		}
		
		/**
		 * Get field HTML classes
		 * @return string
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public function get_class() {
			$classes = 'regular-text';
			if( $passed_classes = parent::get_class() ) {
				$classes .= ' ' . $passed_classes;
			}
			
			return $classes;
		}
		
		/**
		 * Get field wrapper classes
		 * @return string
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public function get_wrapper_class() {
			$classes = 'aiom-form-row aiom-form-row-text';
			if( $passed_classes = parent::get_wrapper_class() ) {
				$classes .= ' ' . $passed_classes;
			}
			
			return $classes;
		}

		/**
		 * Render field
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public function render() {
			$label = $this->get_label(); ?>
			<div class="<?php echo esc_attr( $this->get_wrapper_class() ); ?>" <?php echo $this->get_wrapper_attributes(); ?>>
				<div class="label-col<?php echo $label ? '' : ' label-col-empty'; ?>">
					<label for="<?php echo esc_attr( $this->get_id() ); ?>"><?php echo esc_html( $label ); ?></label>
				</div>
				<div class="control-col">
					<input
							type="text" id="<?php echo esc_attr( $this->get_id() ); ?>"
							name="<?php echo esc_attr( $this->get_name() ); ?>"
							class="<?php echo esc_attr( $this->get_class() ); ?>"
							value="<?php echo esc_attr( $this->get_value() ); ?>"
							<?php echo $this->get_attributes(); ?> />
					
					<?php if ( $description = $this->get_description() ) { ?>
						<p class="description"><?php echo $description; ?></p>
					<?php } ?>
				</div>
				<?php echo $this->get_active_callback(); ?>
			</div>
			<?php
		}
		
		/**
		 * Sanitize URL
		 * @param array $value Value to sanitize
		 *
		 * @return string
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public static function filter_value( $value ) {
			$value = filter_var( filter_var( $value, FILTER_SANITIZE_URL ), FILTER_VALIDATE_URL );

			return $value ? $value : '';
		}

	}

}