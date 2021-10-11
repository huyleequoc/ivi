<?php
/**
 * Color Picker field for metaboxes
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

if ( ! class_exists( 'AIOM_Color_Field' ) && class_exists( 'AIOM_Base_Field' ) ) {

	/**
	 * Class AIOM_Color_Field
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	final class AIOM_Color_Field extends AIOM_Base_Field {

		/**
		 * Get form field attributes
		 * @return string
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		protected function get_attributes() {
			$attributes = parent::get_attributes();
			if ( $this->get_default() ) {
				$attributes .= ' data-default-color="' . $this->get_default() . '"';
			}

			return $attributes;
		}

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
		 * Enqueue color picker assets
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public static function enqueue() {
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker' );

			$scripts = wp_scripts();
			$deps = $scripts->registered[ 'aiom-script' ]->deps;
			$deps[] = 'wp-color-picker';
			$scripts->registered[ 'aiom-script' ]->deps = array_unique( $deps );
		}

		/**
		 * Get field wrapper classes
		 * @return string
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public function get_wrapper_class() {
			$classes        = 'aiom-form-row aiom-form-row-color';
			if ( $passed_classes = parent::get_wrapper_class() ) {
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
					<input type="text"
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