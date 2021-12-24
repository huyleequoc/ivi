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

if ( ! class_exists( 'AIOM_Image_Field' ) && class_exists( 'AIOM_Base_Field' ) ) {

	/**
	 * Class AIOM_Image_Field
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	final class AIOM_Image_Field extends AIOM_Base_Field {

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
		 * Enqueue media assets
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public static function enqueue() {
			wp_enqueue_media();
		}
		
		/**
		 * Get field wrapper classes
		 * @return string
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public function get_wrapper_class() {
			$classes = 'aiom-form-row aiom-form-row-image';
			$passed_classes = parent::get_wrapper_class();
			if( $passed_classes ) {
				$classes .= ' ' . $passed_classes;
			}
			
			return $classes;
		}
		
		/**
		 * Get field HTML classes
		 * @return string
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public function get_class() {
			$classes = 'image_id';
			
			$passed_classes = parent::get_class();
			if( $passed_classes ) {
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
			$value = $this->get_value();
			$label = $this->get_label(); ?>
			<div class="<?php echo esc_attr( $this->get_wrapper_class() ); ?>" <?php echo $this->get_wrapper_attributes(); ?>>
				<div class="label-col<?php echo $label ? '' : ' label-col-empty'; ?>">
					<label for="<?php echo esc_attr( $this->get_id() ); ?>"><?php echo esc_html( $label ); ?></label>
				</div>
				<div class="control-col">
					<div class="upload-wrapper <?php echo $value ? 'has-image' : ''; ?>">
						<div class="image-wrapper">
							<div class="placeholder"><?php esc_html_e( 'No file selected', 'aiom' ); ?></div>
							<div class="image-holder">
								<?php if ( $value ) {
									echo wp_get_attachment_image( $value, 'medium' );
								} ?>
							</div>
						</div>
						<div class="buttons-wrapper">
							<button type="button" class="button button-upload"><?php esc_html_e( 'Select File', 'aiom' ); ?></button>
							<button type="button" class="button button-remove"><?php esc_html_e( 'Remove', 'aiom' ); ?></button>
						</div>
						<input type="hidden"
						       class="<?php echo esc_attr( $this->get_class() ); ?>"
						       name="<?php echo esc_attr( $this->get_name() ); ?>"
						       value="<?php echo esc_attr( $this->get_value() ); ?>" />
					</div>
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