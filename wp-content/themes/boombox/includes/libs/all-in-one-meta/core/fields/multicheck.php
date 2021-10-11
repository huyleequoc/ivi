<?php
/**
 * Checkbox field for metaboxes
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

if ( ! class_exists( 'AIOM_Multicheck_Field' ) && class_exists( 'AIOM_Base_Field' ) ) {

	/**
	 * Class AIOM_Multicheck_Field
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	final class AIOM_Multicheck_Field extends AIOM_Base_Field {

		/**
		 * Holds field choices
		 * @var array
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		private $choices;

		/**
		 * Set field choices
		 * @param array $args Field arguments
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		private function set_choices( $args ) {
			if( isset( $args[ 'choices' ] ) ) {
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
				'choices'            => array(),
				'default'            => array(),
				'order'              => 10,
				'sub_order'          => 10,
				'standalone'         => false,
				'class'              => '',
				'attributes'         => '',
				'wrapper_class'      => '',
				'wrapper_attributes' => '',
				'table_col'          => null,
				'sanitize_callback'  => array( __CLASS__, 'sanitize' ),
				'render_callback'    => null,
				'active_callback'    => null,
			) );

			return $args;
		}
		
		/**
		 * Get field wrapper classes
		 * @return string
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public function get_wrapper_class() {
			$classes = 'aiom-form-row aiom-form-row-checkbox';
			$passed_classes = parent::get_wrapper_class();
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
			$label = $this->get_label(); ?>
			<div class="<?php echo esc_attr( $this->get_wrapper_class() ); ?>" <?php echo $this->get_wrapper_attributes(); ?>>
				<div class="label-col<?php echo $label ? '' : ' label-col-empty'; ?>">
					<label for="<?php echo esc_attr( $this->get_id() ); ?>"><?php echo esc_html( $label ); ?></label>
				</div>
				<div class="control-col">
					<?php
					foreach ( $this->get_choices() as $key => $value ) {
						$checked = checked( in_array( esc_html( esc_attr( $key ) ), (array)$this->get_value() ), true, false ); ?>
						<div class="field-list-item <?php echo $checked ? 'selected' : ''; ?>">
							<label>
								<input type="checkbox"
								       id="<?php echo esc_attr( $this->get_id() ); ?>"
								       name="<?php echo esc_attr( $this->get_name( '[]' ) ); ?>"
								       value="<?php echo esc_attr( $key ); ?>"
								       class="<?php echo esc_attr( $this->get_class() ); ?>"
										<?php echo $checked; ?>
										<?php echo $this->get_attributes(); ?> />
								<?php echo $value; ?>
							</label>
						</div>
					<?php } ?>
				</div>
				<?php echo $this->get_active_callback(); ?>
			</div>
			<?php
		}

		/**
		 * Sanitize field value
		 *
		 * @param array $values Current values
		 *
		 * @return array
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public static function sanitize( $values = array() ) {
			if ( ! is_array( $values ) ) {
				$values = (array)$values;
			}

			return $values;
		}

	}

}