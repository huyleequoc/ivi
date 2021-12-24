<?php
/**
 * Custom HTML field for metaboxes
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

if ( ! class_exists( 'AIOM_Custom_Field' ) && class_exists( 'AIOM_Base_Field' ) ) {

	/**
	 * Class AIOM_Custom_Field
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	final class AIOM_Custom_Field extends AIOM_Base_Field {

		/**
		 * Holds field custom HTML
		 * @var string
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		private $html;

		/**
		 * Set field custom HTML
		 * @param array $args Field arguments
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		private function set_html( $args ) {
			if( isset( $args[ 'html' ] ) && $args[ 'html' ] ) {
				$this->html = $args[ 'html' ];
			}
		}

		/**
		 * Get field custom HTML
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		protected function get_html() {
			return $this->html;
		}

		/**
		 * AIOM_Custom_Field constructor.
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

			/***** Field custom HTML */
			$this->set_html( $args );
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
				'html'               => '',
				'order'              => 10,
				'sub_order'          => 10,
				'standalone'         => false,
				'wrapper_class'      => '',
				'wrapper_attributes' => '',
				'sanitize_callback'  => '',
				'active_callback'    => array(),
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
			$classes = 'aiom-form-row aiom-form-row-custom';
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

				<?php if ( $label ) { ?>
				<div class="label-col<?php echo $label ? '' : ' label-col-empty'; ?>">
					<label for="<?php echo esc_attr( $this->get_id() ); ?>"><?php echo esc_html( $label ); ?></label>
				</div>
				<div class="control-col">
					<?php }
					echo $this->get_html();
					if ( $label ) {
					?></div>
			<?php } ?>
				<?php echo $this->get_active_callback(); ?>
			</div>
			<?php
		}

	}

}