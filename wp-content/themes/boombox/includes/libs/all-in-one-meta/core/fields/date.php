<?php
/**
 * Datepicker field for metaboxes
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

if ( ! class_exists( 'AIOM_Date_Field' ) && class_exists( 'AIOM_Base_Field' ) ) {

	/**
	 * Class AIOM_Date_Field
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	final class AIOM_Date_Field extends AIOM_Base_Field {

		/**
		 * Holds js options
		 * @var array
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		private $js_options = array();

		/**
		 * Set field js options
		 * @param array $args Field arguments
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		private function set_js_options( $args ) {
			if( isset( $args[ 'js_options' ] ) && is_array( $args[ 'js_options' ] ) && ! empty( $args[ 'js_options' ] ) ) {
				$this->js_options = $args[ 'js_options' ];
			}
		}

		/**
		 * Get js options
		 * @return array
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		private function get_js_options() {
			return $this->js_options;
		}

		/**
		 * AIOM_Date_Field constructor.
		 *
		 * @param array            $args
		 * @param bool|null|string $tab_id
		 * @param array            $data
		 * @param array            $structure
		 * @param                  $group
		 *
		 * @see AIOM_Base_Field::__construct()
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public function __construct( array $args, $tab_id, array $data, array $structure, $group ) {
			parent::__construct( $args, $tab_id, $data, $structure, $group );

			/** Set field js options */
			$this->set_js_options( $args );
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
				'js'                 => array(),
				'order'              => 10,
				'sub_order'          => 10,
				'standalone'         => false,
				'class'              => '',
				'attributes'         => '',
				'wrapper_class'      => '',
				'wrapper_attributes' => '',
				'table_col'          => null,
				'sanitize_callback'  => array( __CLASS__, 'sanitize' ),
				'render_callback'    => array( __CLASS__, 'filter_value' ),
				'active_callback'    => null,
			) );

			return $args;
		}

		/**
		 * Get form field attributes
		 * @return string
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public function get_attributes() {
			return 'js-config="' . esc_attr( json_encode( $this->get_js_options() ) ) . '" ' . parent::get_attributes();
		}

		/**
		 * Get field HTML classes
		 * @return string
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public function get_class() {
			$classes = 'regular-text';
			if ( $passed_classes = parent::get_class() ) {
				$classes .= ' ' . $passed_classes;
			}

			return $classes;
		}

		/**
		 * Enqueue color picker assets
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public static function enqueue() {
			wp_enqueue_style( 'aiom-admin-jquery-ui', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css' );
			wp_enqueue_script( 'jquery-ui-datepicker' );
			$scripts = wp_scripts();
			$deps = $scripts->registered[ 'aiom-script' ]->deps;
			$deps[] = 'jquery-ui-datepicker';
			$scripts->registered[ 'aiom-script' ]->deps = array_unique( $deps );
		}

		/**
		 * Get field wrapper classes
		 * @return string
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public function get_wrapper_class() {
			$classes = 'aiom-form-row aiom-form-row-date';
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

		/**
		 * Sanitize value
		 *
		 * @param string $value Current value
		 *
		 * @return false|string
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public static function sanitize( $value ) {
			if ( $value ) {
				$value = date( 'Y-m-d H:i:s', strtotime( $value ) );
			}

			return $value;
		}

		/**
		 * Filter value
		 *
		 * @param string $value Current value
		 *
		 * @return false|string
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public static function filter_value( $value ) {
			if ( $value ) {
				$value = date( get_option( 'date_format' ), strtotime( $value ) );
			}

			return $value;
		}

	}

}