<?php
/**
 * Boombox customizer "bb-composition-sortable-slave" field type
 *
 * @package BoomBox_Theme
 * @since   2.0.0
 * @version 2.0.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! class_exists( 'Boombox_Controls_Composition_Sortable_Slave_Control' ) ) {

	/**
	 * The "bb-composition-sortable-slave" class
	 */
	class Boombox_Controls_Composition_Sortable_Slave_Control extends WP_Customize_Control {

		/**
		 * @var string Control's Type.
		 */
		public $type = 'bb-composition-sortable-slave';

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

		/**
		 * Render the control's content.
		 */
		public function render_content() {
			$choices = Boombox_Choices_Helper::get_instance()->get_header_composition_component_choices();
			$values = $this->value();

			if ( ! empty( $this->label ) ) { ?>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php }

			if ( ! empty( $this->description ) ) { ?>
				<span class="description customize-control-description"><?php echo wp_kses_post( $this->description ); ?></span>
			<?php } ?>

			<div class="customize-control-content">
				<div class="bb-control bb-control-composition-sortable-slave">
					<div class="clearfix">
						<div class="bb-composition-sortable-slave-section bb-composition-sortable-slave-left">
							<ul class="bb-composition-sortable bb-composition-slave"
							    data-control="<?php echo $this->id; ?>" data-location="left">
								<?php
								$l_values = array();
								if ( isset( $values[ 'left' ] ) && ! empty( $values[ 'left' ] ) ) {
									$l_values = (array)$values[ 'left' ];
								}
								foreach ( $l_values as $component_id ) {
									if( array_key_exists( $component_id, $choices ) ) { ?>
									<li id="bb-composition-item-<?php echo $component_id; ?>"
										class="bb-composition-sortable-item" component-id="<?php echo $component_id; ?>">
										<?php echo $choices[ $component_id ]; ?>
									</li>
									<?php }
								} ?>
							</ul>
						</div>
						<div class="bb-composition-sortable-slave-section bb-composition-sortable-slave-right">
							<ul class="bb-composition-sortable bb-composition-slave"
							    data-control="<?php echo $this->id; ?>" data-location="right">
								<?php
								$r_values = array();
								if ( isset( $values[ 'right' ] ) && ! empty( $values[ 'right' ] ) ) {
									$r_values = (array)$values[ 'right' ];
								}
								foreach ( $r_values as $component_id ) {
									if( array_key_exists( $component_id, $choices ) ) { ?>
									<li class="bb-composition-sortable-item" component-id="<?php echo $component_id; ?>">
										<?php echo $choices[ $component_id ]; ?>
									</li>
									<?php }
								} ?>
							</ul>
						</div>
					</div>
				</div>
			</div>
			<?php
		}

	}

	/**
	 * Register "bb-composition-sortable-slave"
	 */
	add_filter( 'kirki/control_types', function ( $controls ) {
		$controls[ 'bb-composition-sortable-slave' ] = 'Boombox_Controls_Composition_Sortable_Slave_Control';

		return $controls;
	} );

}