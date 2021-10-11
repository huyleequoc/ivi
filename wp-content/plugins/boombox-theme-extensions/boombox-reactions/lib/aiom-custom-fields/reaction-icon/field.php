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

if ( ! class_exists( 'Boombox_AIOM_Reaction_Icon_Field' ) && class_exists( 'AIOM_Base_Field' ) ) {
	
	/**
	 * Class Boombox_AIOM_Reaction_Icon_Field
	 */
	final class Boombox_AIOM_Reaction_Icon_Field extends AIOM_Base_Field {
		
		/**
		 * Holds field choices
		 * @var array
		 */
		private $_choices;
		
		/**
		 * Get field choices
		 * @return array
		 */
		private function get_choices() {
			return $this->_choices;
		}
		
		/**
		 * Holds placeholder image URL
		 * @var
		 */
		private $_placeholder_image_url;
		
		/**
		 * Get placeholder image URL
		 * @return mixed
		 */
		private function get_placeholder_image_url() {
			return $this->_placeholder_image_url;
		}
		
		/**
		 * Holds thumbnail warpper attributes
		 * @var array
		 */
		private $_thumbnail_wrapper_attributes = array();
		
		/**
		 * Get thumbnail warpper attributes
		 * @return array
		 */
		public function get_thumbnail_wrapper_attributes() {
			return $this->_thumbnail_wrapper_attributes;
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
			$this->_placeholder_image_url = isset( $args[ 'placeholder' ] ) && $args[ 'placeholder' ] ? $args[ 'placeholder' ] : '';
			$this->_choices = isset( $args[ 'choices' ] ) && is_array( $args[ 'choices' ] ) ? $args[ 'choices' ] : array();
			
			$thumbnail_wrapper_attributes = isset( $args[ 'thumbnail_wrapper_attributes' ] ) && is_array( $args[ 'thumbnail_wrapper_attributes' ] ) ? $args[ 'thumbnail_wrapper_attributes' ] : array();
			foreach( $thumbnail_wrapper_attributes as $attribute => $value ) {
				$thumbnail_wrapper_attributes[ $attribute ] = sprintf( '%s="%s"', $attribute, $value );
			}
			$this->_thumbnail_wrapper_attributes = join( ' ', $thumbnail_wrapper_attributes );
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
				'placeholder'        => '',
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
			wp_enqueue_style( 'boombox-aiom-reaction-icon-styles', BBTE_REACTIONS_URL . 'lib/aiom-custom-fields/reaction-icon/style.css' );
			wp_enqueue_script( 'boombox-aiom-reaction-icon-scripts', BBTE_REACTIONS_URL . 'lib/aiom-custom-fields/reaction-icon/scripts.js', array( 'jquery' ), null, true );
		}
		
		/**
		 * Get field wrapper classes
		 * @return string
		 */
		public function get_wrapper_class() {
			$classes = 'aiom-form-row aiom-form-row-text bbte-custom-reaction-icon';
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
				<div class="label-col<?php echo $label ? '' : ' empty-label'; ?>">
					<label for="<?php echo $this->get_id(); ?>"><?php echo esc_html( $label ); ?></label>
				</div>
				<div class="control-col">
					
					<select id="<?php echo $this->get_id(); ?>" class="<?php echo $this->get_class(); ?>" name="<?php echo $this->get_name(); ?><?php echo $this->has_attribute( 'multiple' ) ? '[]' : ''; ?>" <?php echo $this->get_attributes(); ?>>
						<?php foreach ( $this->get_choices() as $value ) { ?>
							<option value="<?php echo esc_attr( $value['basename'] ); ?>" data-url="<?php echo esc_attr( $value[ 'filepath' ] ); ?>" <?php selected( $this->get_value(), $value['basename'] ); ?>><?php echo esc_html( $value[ 'filename' ] ); ?></option>
						<?php } ?>
					</select>
					<?php
					$term_id = isset( $_REQUEST[ 'tag_ID' ] ) ? absint( $_REQUEST[ 'tag_ID' ] ) : 0;
					$image_url = $this->get_placeholder_image_url();
					if( $term_id && $this->get_value() ) {
						$image_url = boombox_get_reaction_icon_url( $term_id, $this->get_value() );
					} ?>
					<span class="reaction-thumb" <?php echo $this->get_thumbnail_wrapper_attributes(); ?>>
						<img src="<?php echo esc_url( $image_url ); ?>" width="50px" height="50px" alt="reaction" />
					</span>
					
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