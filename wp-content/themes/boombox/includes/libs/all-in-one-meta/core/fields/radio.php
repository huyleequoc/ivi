<?php
/**
 * Radio field for metaboxes
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

if ( ! class_exists( 'AIOM_Radio_Field' ) && class_exists( 'AIOM_Base_Field' ) ) {

	/**
	 * Class AIOM_Radio_Field
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	final class AIOM_Radio_Field extends AIOM_Base_Field {

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
		 * Holds form field axis
		 * @var string
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		private $axis = 'horizontal';

		/**
         * Set field axis
		 * @param array $args Field arguments
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		private function set_axis( $args ) {
		    if( isset( $args[ 'axis' ] ) && in_array( $args[ 'axis' ], array( 'horizontal', 'vertical' ) ) ) {
			    $this->axis = $args[ 'axis' ];
            }
        }

		/**
		 * Get form field axis
		 * @return string
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		private function get_axis() {
			return $this->axis;
		}

		/**
		 * AIOM_Radio_Field constructor.
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

			/***** Field axis */
			$this->set_axis( $args );
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
				'default'            => '',
				'axis'               => 'horizontal',
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
		 * @return string
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public function get_wrapper_class() {
			$classes = 'aiom-form-row aiom-form-row-radio';
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
			$axis = $this->get_axis();
			$tag = ( 'horizontal' == $axis ) ? 'span' : 'div';
			$label = $this->get_label(); ?>

			<div class="<?php echo esc_attr( $this->get_wrapper_class() ); ?>" <?php echo $this->get_wrapper_attributes(); ?>>
				<div class="label-col<?php echo $label ? '' : ' label-col-empty'; ?>">
					<label for="<?php echo esc_attr( $this->get_id() ); ?>"><?php echo esc_html( $label ); ?></label>
				</div>

				<div class="control-col">
					<div class="field-list field-list-<?php echo $axis; ?>">
						<?php foreach ( $this->get_choices() as $key => $value ) { ?>
						<<?php echo $tag; ?> class="field-list-item">
						<label>
							<input type="radio"
							       id="<?php echo esc_attr( $this->get_id() ); ?>"
							       name="<?php echo esc_attr( $this->get_name() ); ?>"
							       value="<?php echo esc_attr( esc_attr( $key ) ); ?>"
							       class="<?php echo esc_attr( $this->get_class() ); ?>"
									<?php checked( $this->get_value(), esc_html( esc_attr( $key ) ), true ); ?>
									<?php echo $this->get_attributes(); ?> />
							<strong><?php echo $value; ?></strong>
						</label>
                        </<?php echo $tag; ?>>
                        <?php } ?>
                    </div>
                </div>
                <?php if ( $description = $this->get_description() ) { ?>
                    <p class="description"><?php echo $description; ?></p>
                <?php }
                echo $this->get_active_callback(); ?>
			</div>
			<?php
		}

	}

}