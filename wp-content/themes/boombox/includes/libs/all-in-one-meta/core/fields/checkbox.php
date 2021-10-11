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

if ( ! class_exists( 'AIOM_Checkbox_Field' ) && class_exists( 'AIOM_Base_Field' ) ) {

	/**
	 * Class AIOM_Checkbox_Field
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	final class AIOM_Checkbox_Field extends AIOM_Base_Field {
		
		/**
		 * Holds checkbox value
		 * @var int
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		private $val = 1;

		/**
		 * Set field value
		 * @param array $args Field arguments
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		private function set_val( $args ) {
			if( isset( $args[ 'val' ] ) ) {
				$this->val = max( absint( $args[ 'val' ] ), 1 );
			}
		}

		/**
		 * Get checkbox value
		 * @return int
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		private function get_val() {
			return $this->val;
		}

		/**
		 * Holds field text
		 * @var string
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		private $text = '';

		/**
		 * Set field text
		 * @param array $args Field arguments
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		private function set_text( $args ) {
			if( isset( $args[ 'text' ] ) && $args[ 'text' ] ) {
				$this->text = $args[ 'text' ];
			}
		}

		/**
		 * Get field text
		 * @return bool|string
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		protected function get_text() {
			return $this->text;
		}
		
		/**
		 * AIOM_Checkbox_Field constructor.
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

			/***** Field text */
			$this->set_text( $args );

			/***** Set checkbox value */
            $this->set_val( $args );
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
				'text'               => '',
				'val'                => 1,
				'default'            => '',
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
					<input type="hidden" name="<?php echo esc_attr( $this->get_name() ); ?>" value="0"/>
					<input type="checkbox"
					       id="<?php echo esc_attr( $this->get_id() ); ?>"
					       name="<?php echo esc_attr( $this->get_name() ); ?>"
					       value="<?php echo esc_attr( $this->get_val() ); ?>"
					       class="<?php echo esc_attr( $this->get_class() ) ?>"
							<?php checked( !!$this->get_value(), !!$this->get_val() ); ?>
							<?php echo $this->get_attributes(); ?> />
					<?php if ( $text = $this->get_text() ) { ?>
						<label for="<?php echo esc_attr( $this->get_id() ); ?>"><strong><?php echo $text; ?></strong></label>
					<?php } ?>
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
		 * @param int $value Current value
		 *
		 * @return int
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public static function sanitize( $value = 0 ) {
			return absint( $value );
		}

	}

}