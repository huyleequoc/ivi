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
	require_once( AIOM_PATH . 'core/fields/base-field.php' );
}

if ( ! class_exists( 'Boombox_AIOM_Icons_Dropdown_Field' ) && class_exists( 'AIOM_Base_Field' ) ) {
	
	/**
	 * Class Boombox_AIOM_Icons_Dropdown_Field
	 */
	final class Boombox_AIOM_Icons_Dropdown_Field extends AIOM_Base_Field {
		
		/**
		 * Holds field choices
		 * @var array
		 */
		private $choices;
		
		/**
		 * Get field choices
		 * @return array
		 */
		private function get_choices() {
			return $this->choices;
		}
		
		/**
		 * AIOM_Select_Field constructor.
		 *
		 * @param array            $args
		 * @param bool|null|string $tab_id
		 * @param array            $data
		 * @param array            $structure
		 * @param string           $group
		 *
		 * @see AIOM_Base_Field::__construct()
		 */
		public function __construct( array $args, $tab_id, array $data, array $structure, $group ) {
			parent::__construct( $args, $tab_id, $data, $structure, $group );
			
			/***** Field choices */
			$icons = boombox_get_icomoon_icons_array();
			array_unshift( $icons, array(
				'icon'    => '',
				'name'    => __( 'Select', 'boombox' ),
				'prefix'  => '',
				'postfix' => ''
			) );
            $this->choices = $icons;
		}
		
		/**
		 * Parse field arguments
		 *
		 * @param array $args Field arguments
		 *
		 * @return array
		 */
		public static function parse_field_args( $args ) {
			$args = wp_parse_args( $args, array(
				'id'                 => '',
				'name'               => '',
				'default'            => '',
				'label'              => '',
				'description'        => '',
				'order'              => 0,
				'sub_order'          => 0,
				'standalone'         => false,
				'attributes'         => '',
				'class'              => '',
				'wrapper_class'      => '',
				'wrapper_attributes' => '',
				'sanitize_callback'  => 'sanitize_text_field',
				'render_callback'    => '',
				'active_callback'    => array(),
			) );
			
			return $args;
		}
		
		/**
		 * Enqueue color picker assets
		 */
		public static function enqueue() {

			wp_enqueue_style( 'boombox-icomoon-style', BOOMBOX_THEME_URL . 'fonts/icon-fonts/icomoon/icons.min.css', array(), boombox_get_assets_version() );
			wp_enqueue_style( 'bb-select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css', array(), boombox_get_assets_version() );
			wp_enqueue_script( 'bb-select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js', array( 'jquery' ), boombox_get_assets_version(), true );
			wp_enqueue_script( 'boombox-aiom-icons-dropdown-scripts', BOOMBOX_ADMIN_URL . 'metaboxes/aiom-custom-fields/icons-dropdown/scripts.js', array( 'bb-select2' ), boombox_get_assets_version(), true );
			
		}
		
		/**
		 * Get field wrapper classes
		 * @return string
		 */
		public function get_wrapper_class() {
			$classes = 'aiom-form-row aiom-form-row-text bb-custom-icons-dropdown';
			$passed_classes = parent::get_wrapper_class();
			if( $passed_classes ) {
				$classes .= ' ' . $passed_classes;
			}
			
			return $classes;
		}
		
		/**
		 * Render field
		 */
		public function render() {
			$label = $this->get_label(); ?>
			<div class="<?php echo $this->get_wrapper_class(); ?>" <?php echo $this->get_wrapper_attributes(); ?>>
				<div class="label-col<?php echo $label ? '' : ' label-col-empty'; ?>">
					<label for="<?php echo $this->get_id(); ?>"><?php echo esc_html( $label ); ?></label>
				</div>
				<div class="control-col">
					<select
						id="<?php echo $this->get_id(); ?>"
						class="<?php echo $this->get_class(); ?>"
						name="<?php echo $this->get_name(); ?><?php echo $this->has_attribute( 'multiple' ) ? '[]' : ''; ?>"
						<?php echo $this->get_attributes(); ?>>
						
						<?php foreach ( $this->get_choices() as $value ) {
							if ( is_array( $this->get_value() ) ) {
								$selected = selected( in_array( $value[ 'icon' ], $this->get_value(), true ), true, false );
							} else {
								$selected = selected( $value[ 'icon' ], $this->get_value(), false );
							} ?>
							<option value="<?php echo esc_html( esc_attr( $value[ 'icon' ] . $value[ 'postfix' ] ) ); ?>" data-class="<?php echo esc_attr( $value['prefix'] . $value[ 'icon' ] . $value[ 'postfix' ] ); ?>" <?php echo $selected; ?>><?php echo esc_html( $value[ 'name' ] ); ?></option>
						<?php } ?>
					</select>
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