<?php
/**
 * Boombox customizer "bb-composition-sortable-master" field type
 *
 * @package BoomBox_Theme
 * @since   2.0.0
 * @version 2.0.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! class_exists( 'Boombox_Controls_Composition_Sortable_Master_Control' ) ) {

	/**
	 * The "bb-composition-sortable-master" class
	 */
	class Boombox_Controls_Composition_Sortable_Master_Control extends WP_Customize_Control {

		/**
		 * @var string Control's Type.
		 */
		public $type = 'bb-composition-sortable-master';


		public $components_dependencies = array();

		/**
		 * Boombox_Controls_Composition_Sortable_Master_Control constructor.
		 *
		 * @param WP_Customize_Manager $manager
		 * @param string               $id
		 * @param array                $args
		 * @see WP_Customize_Control
		 */
		public function __construct( WP_Customize_Manager $manager, $id, array $args = array() ) {
			parent::__construct( $manager, $id, $args );

			if( isset( $args['components_dependencies'] ) && is_array( $args['components_dependencies'] ) ) {
				$this->components_dependencies = $args['components_dependencies'];
			}
		}

		/**
		 * Enqueue control related scripts/styles.
		 */
		public function enqueue() {
			$min = boombox_get_minified_asset_suffix();

			wp_enqueue_script( 'jquery-ui-sortable' );
			wp_enqueue_script(
				'boombox-customize-control-header-composition',
				BOOMBOX_INCLUDES_URL . 'customizer/assets/js/header-composition-sortable' . $min . '.js',
				array( 'jquery', 'jquery-ui-sortable' ),
				boombox_get_assets_version(),
				true
			);
			wp_localize_script( 'boombox-customize-control-header-composition', 'bb', array(
				'option' => Boombox_Customizer::OPTION_NAME,
				'components_dependencies' => $this->components_dependencies
			) );
		}

		private function is_component_disabled_by_dependency( $component_id ) {
			$is_disabled = false;
			if( ! empty( $this->components_dependencies ) ) {
				$dependencies = wp_list_filter( $this->components_dependencies, array( 'component' => $component_id ) );
				if( ! empty( $dependencies ) ) {
					foreach( $dependencies as $dependency ) {
						$value = boombox_get_theme_option( $dependency['setting'] );
						switch( $dependency['operator'] ) {
							case '!=':
							case '<>':
								$compare = $value != $dependency['value'];
								break;
							case '==':
							case '=':
							default:
								$compare = $value != $dependency['value'];
								break;
						}

						if( $compare ) {
							$is_disabled = true;
							break;
						}
					}
				}
			}

			return $is_disabled;
		}

		/**
		 * Render the control's content.
		 */
		public function render_content() {
			if ( ! empty( $this->label ) ) { ?>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php }

			if ( ! empty( $this->description ) ) { ?>
				<span class="description customize-control-description"><?php echo wp_kses_post( $this->description ); ?></span>
			<?php } ?>

			<div class="bb-control bb-control-composition-sortable-master">
				<ul class="bb-composition-sortable bb-composition-sortable-master">
					<?php foreach( $this->choices as $component_id => $label ) {
						$is_disabled = $this->is_component_disabled_by_dependency( $component_id ); ?>
					<li id="bb-composition-item-<?php echo $component_id; ?>"
						class="bb-composition-sortable-item <?php echo $is_disabled ? 'bb-disabled' : ''; ?>"
					    component-id="<?php echo $component_id; ?>">
						<?php echo $label; ?>
					</li>
					<?php } ?>
				</ul>
			</div>
			<?php
		}

	}

	/**
	 * Register "bb-composition-sortable-master"
	 */
	add_filter( 'kirki/control_types', function ( $controls ) {
		$controls[ 'bb-composition-sortable-master' ] = 'Boombox_Controls_Composition_Sortable_Master_Control';

		return $controls;
	} );

}