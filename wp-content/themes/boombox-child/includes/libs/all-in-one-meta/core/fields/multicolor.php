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
	require_once 'base-field.php';
}

if ( ! class_exists( 'AIOM_Multicolor_Field' ) ) {

	/**
	 * Class AIOM_Multicolor_Field
     * @since 1.0.0
	 * @version 1.0.0
	 */
	final class AIOM_Multicolor_Field extends AIOM_Base_Field {

		/**
		 * Holds field choices
		 * @var array
         * @since 1.0.0
		 * @version 1.0.0
		 */
		private $choices = array();

		/**
         * Set field choices
		 * @param array $args Field arguments
         * @since 1.0.0
		 * @version 1.0.0
		 */
		private function set_choices( $args ) {
			if( isset( $args[ 'choices' ] ) && is_array( $args[ 'choices' ] ) ) {
				if( is_callable( $args[ 'choices' ] ) ) {
					$this->choices = call_user_func( $args[ 'choices' ] );
				} elseif( is_array( $args[ 'choices' ] ) ) {
					$this->choices = $args[ 'choices' ];
				}
			}
        }

		/**
		 * Get field choices
		 * @return array
         * @since 1.0.0
		 * @version 1.0.0
		 */
		private function get_choices() {
			return $this->choices;
		}

		/**
		 * AIOM_Multicheck_Field constructor.
		 *
		 * @param array            $args
		 * @param bool|null|string $tab_id
		 * @param array            $data
		 * @param array            $structure
		 * @param string           $group
		 *
		 * @see AIOM_Base_Field::__construct()
         * @since 1.0.0
		 * @version 1.0.0
		 */
		public function __construct( array $args, $tab_id, array $data, array $structure, $group ) {
			parent::__construct( $args, $tab_id, $data, $structure, $group );

			/***** Field choices */
			$this->set_choices( $args );
		}

		/**
		 * Parse field arguments
		 * @param array $args Field arguments
		 *
		 * @return array
         * @since 1.0.0
		 * @version 1.0.0
		 */
		public static function parse_field_args( $args ) {
			return wp_parse_args( $args, array(
				'id'                 => '',
				'name'               => '',
				'label'              => '',
				'description'        => '',
				'choices'            => array(),
				'default'            => array(),
				'order'              => 10,
				'sub_order'          => 10,
				'standalone'         => false,
				'class'              => '',
				'attributes'         => array(),
				'wrapper_class'      => '',
				'wrapper_attributes' => '',
				'table_col'          => null,
				'sanitize_callback'  => array( __CLASS__, 'sanitize' ),
				'render_callback'    => null,
				'active_callback'    => null,
			) );
		}
		
		/**
		 * Get field wrapper classes
		 * @return string
         * @since 1.0.0
		 * @version 1.0.0
		 */
		public function get_wrapper_class() {
			$classes = 'aiom-form-row aiom-form-row-multicolor';
			if( $passed_classes = parent::get_wrapper_class() ) {
				$classes .= ' ' . $passed_classes;
			}
			
			return $classes;
		}

		/**
		 * Get form field attributes
         * @param string  $key  The choice key
		 * @return string
         * @since 1.0.0
         * @version 1.0.0
		 */
		protected function get_choice_attribute( $key ) {
			$values = $this->get_value();

			return $this->get_attributes() . ' data-default-color="' . $values[ $key ] . '"';
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
		 * Render field
         * @since 1.0.0
         * @version 1.0.0
		 */
		public function render() {
			$values = $this->get_value();
			$label = $this->get_label(); ?>
			<div class="<?php echo esc_attr( $this->get_wrapper_class() ); ?>" <?php echo $this->get_wrapper_attributes(); ?>>
				<div class="label-col<?php echo $label ? '' : ' label-col-empty'; ?>">
					<label for="<?php echo esc_attr( $this->get_id() ); ?>"><?php echo esc_html( $label ); ?></label>
				</div>
				<div class="control-col">
					<div class="field-list">
						<?php foreach ( $this->get_choices() as $key => $color_label ) { ?>
							<div class="field-list-item">
								<label><?php echo esc_html( $color_label ); ?></label>
								<input type="text"
								       id="<?php echo esc_attr( $this->get_id() ); ?>"
								       name="<?php echo esc_attr($this->get_name( "[{$key}]" ) ); ?>"
								       class="<?php echo esc_attr( $this->get_class() ); ?>"
								       value="<?php echo esc_attr( $values[ $key ] ); ?>"
										<?php echo $this->get_choice_attribute( $key ); ?> />
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

		/**
		 * Sanitize field value
		 * @param array $values Current values
		 *
		 * @return array
         * @since 1.0.0
         * @version 1.0.0
		 */
		public static function sanitize( $values = array() ) {
			return (array)$values;
		}

	}

}