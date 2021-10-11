<?php
/**
 * Number field for metaboxes
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

if ( ! class_exists( 'AIOM_Number_Field' ) && class_exists( 'AIOM_Base_Field' ) ) {

	/**
	 * Class AIOM_Number_Field
	 */
	final class AIOM_Number_Field extends AIOM_Base_Field {

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
				'label'              => '',
				'description'        => '',
				'default'            => '',
				'order'              => 10,
				'sub_order'          => 10,
				'standalone'         => false,
				'class'              => '',
				'attributes'         => '',
				'wrapper_class'      => '',
				'wrapper_attributes' => '',
				'table_col'          => null,
				'sanitize_callback'  => 'sanitize_text_field',
				'render_callback'    => null,
				'active_callback'    => null,
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
			$classes = 'aiom-form-row aiom-form-row-number';
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
			<div class="<?php echo esc_attr($this->get_wrapper_class() ); ?>" <?php echo $this->get_wrapper_attributes(); ?>>
				<div class="label-col<?php echo $label ? '' : ' label-col-empty'; ?>">
					<label for="<?php echo esc_attr( $this->get_id() ); ?>"><?php echo esc_html( $label ); ?></label>
				</div>
				<div class="control-col">
					<input type="number"
					       id="<?php echo esc_attr( $this->get_id() ); ?>"
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

	}

}