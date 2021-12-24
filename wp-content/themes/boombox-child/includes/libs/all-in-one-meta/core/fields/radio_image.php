<?php
/**
 * Image Radio HTML field for metaboxes
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

if ( ! class_exists( 'AIOM_Radio_Image_Field' ) && class_exists( 'AIOM_Base_Field' ) ) {

	/**
	 * Class AIOM_Radio_Image_Field
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	final class AIOM_Radio_Image_Field extends AIOM_Base_Field {

		/**
		 * Holds field choices
		 *
		 * @var array
		 * @since   1.0.0
		 * @version 1.0.0
		 */
		private $choices;

		/**
		 * Set field choices
		 *
		 * @param array $args Field arguments
		 *
		 * @since   1.0.0
		 * @version 1.0.0
		 */
		private function set_choices( $args ) {
			if ( isset( $args['choices'] ) ) {
				if ( is_callable( $args['choices'] ) ) {
					$this->choices = call_user_func( $args['choices'] );
				} elseif ( is_array( $args['choices'] ) ) {
					$this->choices = $args['choices'];
				}
			}
		}

		/**
		 * Get field choices
		 *
		 * @return array
		 * @since   1.0.0
		 * @version 1.0.0
		 */
		private function get_choices() {
			return $this->choices;
		}

		/**
		 * AIOM_Radio_Image_Field constructor.
		 *
		 * @param array            $args
		 * @param bool|null|string $tab_id
		 * @param array            $data
		 * @param array            $structure
		 * @param string           $group
		 *
		 * @see     AIOM_Base_Field::__construct()
		 * @since   1.0.0
		 * @version 1.0.0
		 */
		public function __construct( array $args, $tab_id, array $data, array $structure, $group ) {
			parent::__construct( $args, $tab_id, $data, $structure, $group );

			/***** Field choices */
			$this->set_choices( $args );
		}

		/**
		 * Parse field arguments
		 *
		 * @param array $args Field arguments
		 *
		 * @return array
		 * @since   1.0.0
		 * @version 1.0.0
		 */
		public static function parse_field_args( $args ) {
			$args = wp_parse_args( $args, array(
				'id'                 => '',
				'name'               => '',
				'label'              => '',
				'description'        => '',
				'choices'            => array(),
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
		 * Get field wrapper classes
		 *
		 * @return string
		 * @since   1.0.0
		 * @version 1.0.0
		 */
		public function get_wrapper_class() {
			$classes = 'aiom-form-row aiom-form-row-radio-image aiom-field-list';
			if ( $passed_classes = parent::get_wrapper_class() ) {
				$classes .= ' ' . $passed_classes;
			}

			return $classes;
		}

		/**
		 * Render field
		 *
		 * @since   1.0.0
		 * @version 1.0.0
		 */
		public function render() {
			$label = $this->get_label(); ?>
			<div class="<?php echo esc_attr( $this->get_wrapper_class() ); ?>" <?php echo $this->get_wrapper_attributes(); ?>>
				<div class="label-col<?php echo $label ? '' : ' label-col-empty'; ?>">
					<label for="<?php echo esc_attr( $this->get_id() ); ?>"><?php echo esc_html( $label ); ?></label>
				</div>

				<div class="control-col">
					<div class="field-list">
						<?php

						foreach ( $this->get_choices() as $key => $value ) {
							$checked = checked( $this->get_value(), esc_html( esc_attr( $key ) ), false );

							//Posts Listing => Listing Type
							if ( is_array( $value ) ) {
								$list_item_title_attr = ! empty( $value['label'] ) ? 'title="' . $value['label'] . '"' : '';
								$img_src         = ! empty( $value['src'] ) ? $value['src'] : '';
							} else {
								$img_src = $value;
							}
							?>

							<div class="field-list-item <?php echo $checked ? 'selected' : ''; ?>" <?php echo isset( $list_item_title_attr ) ? $list_item_title_attr : '' ?>>
								<input type="radio"
									   id="<?php echo esc_attr( $this->get_id() ); ?>"
									   name="<?php echo esc_attr( $this->get_name() ); ?>"
									   value="<?php echo esc_attr( esc_attr( $key ) ); ?>"
									   class="aiom-hidden <?php echo esc_attr( $this->get_class() ); ?>"
									<?php echo $checked; ?>
									<?php echo $this->get_attributes(); ?> />
								<?php if ( $img_src ) { ?>
									<img src="<?php echo $img_src; ?>" />
								<?php } ?>
							</div>
						<?php } ?>
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