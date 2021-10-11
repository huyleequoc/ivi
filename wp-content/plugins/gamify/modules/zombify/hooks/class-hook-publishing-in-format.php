<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Hook for publishing posts with "Zombify" plugin
 * @since 1.0
 * @version 1.0
 */
if ( ! class_exists( 'GFY_ZF_Hook_Publishing_In_Format' )
	&& class_exists( 'myCRED_Hook' )
) {

	class GFY_ZF_Hook_Publishing_In_Format extends myCRED_Hook {

		const REFERENCE = 'gfy_zf_publishing_in_format';

		/**
		 * GFY_ZF_Hook_Publishing_In_Format constructor.
		 *
		 * @param array     $hook_prefs <string, array>
		 * @param string    $type       string
		 */
		function __construct( $hook_prefs, $type = MYCRED_DEFAULT_TYPE_KEY ) {

			$defaults = array(
				'row_0' => array(
					'creds'         => 1,
					'zf_data_type'  => '',
					'log'           => '%plural% for publishing %format%'
				)
			);

			if ( isset( $hook_prefs[ self::REFERENCE ] ) ) {
				$defaults = $hook_prefs[ self::REFERENCE ];
			}

			parent::__construct( array(
				'id'       => self::REFERENCE,
				'defaults' => $defaults
			), $hook_prefs, $type );

		}

		/**
		 * Start hook
		 */
		public function run() {
			add_filter( 'mycred_all_references',  array( $this, 'add_references' ), 10, 1 );
			add_action( 'publish_post', array( $this, 'post_published' ), 10, 2 );
		}

		/**
		 * Register custom references
		 * @param $references   array<string,string>    Registered references
		 *
		 * @return array        array<string,string>
		 */
		public function add_references( $references ) {

			$formats = wp_list_pluck( $this->prefs, 'zf_data_type' );

			$post_types = zombify()->get_post_types();

			foreach( $formats as $format_slug ) {
				$reference_id = $this->get_reference_id( $format_slug );

				$format_slug = strtr( $format_slug, array( 'subtype_' => '', 'main' => 'story' ) );
				$reference_label = $post_types[ $format_slug ][ 'name' ];

				$references[ $reference_id ] = $this->get_reference_label( $reference_label );
			}

			return $references;

		}

		/**
		 * Render preferences
		 */
		public function preferences() {
			$prefs = $this->prefs;

			$post_types = zombify()->get_active_post_types();

			$i = 0;
			$unique_id = uniqid(); ?>
			<div class="mycred-repeater" id="<?php echo $unique_id; ?>">
				<?php foreach( $prefs as $key => $pref ) { ?>
					<div class="row hook-instance mycred-repeater-item <?php if( $i == 0 ) { echo 'cloneable'; } ?>" data-index="<?php echo $i; ?>">

						<?php if( $i > 0 ) { ?>
							<a href="#" class="delete-row dashicons dashicons-no"></a>
						<?php } ?>

						<?php
						$field_id           = $this->field_id( array( $key => 'creds' ) );
						$field_label        = $this->core->plural();
						$field_name         = $this->field_name( array( $key => 'creds' ) );
						$field_value        = $this->core->number( $pref['creds'] );
						?>
						<div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
							<div class="form-group">
								<label for="<?php echo $field_id; ?>"><?php echo $field_label; ?></label>
								<input type="number"
                                       min="1"
								       name="<?php echo $field_name; ?>"
								       id="<?php echo $field_id; ?>"
								       value="<?php echo $field_value; ?>"
								       class="form-control" />
							</div>
						</div>

						<?php
						$field_id           = $this->field_id( array( 'down' => 'log' ) );
						$field_label        = __( 'Format', 'gamify' );
						$field_name         = $this->field_name( array( $key => 'zf_data_type' ) );
						?>
						<div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
							<div class="form-group">
								<label for="<?php echo $field_id; ?>"><?php echo $field_label; ?></label>
								<select name="<?php echo $field_name; ?>" id="<?php echo $field_id; ?>">
									<?php foreach( $post_types as $post_type_key => $post_type_data ) {
										$post_type_slug = $post_type_data['post_type_level'] == 1 ? $post_type_data['post_type_slug'] : 'subtype_' . $post_type_data['post_type_slug']; ?>
										<option <?php selected( $post_type_slug, $pref[ 'zf_data_type' ] ); ?>
												value="<?php echo $post_type_slug; ?>"
										><?php echo $post_type_data['name']; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>

						<?php
						$field_id           = $this->field_id( array( $key => 'log' ) );
						$field_label        = __( 'Log template', 'gamify' );
						$field_name         = $this->field_name( array( $key => 'log' ) );
						$field_value        = esc_attr( $pref['log'] );
						$field_description  = $this->available_template_tags( array( 'general', 'post' ) );
						?>
						<div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
							<div class="form-group">
								<label for="<?php echo $field_id; ?>"><?php echo $field_label; ?></label>
								<input type="text"
								       name="<?php echo $field_name; ?>"
								       id="<?php echo $field_id; ?>"
								       value="<?php echo $field_value; ?>"
								       class="form-control" />
								<span class="description"><?php echo $field_description; ?></span>
							</div>
						</div>
					</div>
					<?php
					$i++;
				} ?>
			</div>
			<div class="text-center">
				<a href="#" class="mycred-repeater-add button button-primary button-small" data-target="#<?php echo $unique_id; ?>"><?php _e( 'Add row', 'gamify' ); ?></a>
			</div>
			<?php
		}

		/**
		 * Setup available template tags
		 *
		 * @param $available array[] Available tags
		 * @param string $custom     Custom tags
		 * @return string
		 */
		public function available_template_tags( $available = array(), $custom = '' ) {
			$template_tags = array(
				'%format%' => __( 'selected format', 'gamify' )
			);
			foreach( $template_tags as $tag => $description ) {
				$custom .= sprintf( '%s - %s, ', $tag, $description );
			}

			return parent::available_template_tags( $available, rtrim( $custom, ', ' ) );
		}

		/**
		 * Get dynamic reference ID for specific format
		 * @param string $format_slug Format slug
		 * @return string
		 */
		private function get_reference_id( $format_slug ) {
			return sprintf( '%s_format_%s', self::REFERENCE, $format_slug );
		}

		/**
		 * Get dynamic reference label for specific format
		 * @param string $format_name Format name
		 * @return string
		 */
		private function get_reference_label( $format_name ) {
			return sprintf( __( 'Publish "%s" format', 'gamify' ), $format_name );
		}

		/**
		 * Sanitise preferences
		 *
		 * @param $data array <string,array>  Data to sanitize
		 * @return array Sanitized data
		 */
		public function sanitise_preferences( $data ) {

			$sanitised_data = array();

			$unique_data_types = array_unique( wp_list_pluck( $data, 'zf_data_type' ) );
			$data = array_intersect_key( $data, $unique_data_types );
			$i = 0;
			foreach( $data as $d ) {
				$key = 'row_' . $i;
				$sanitised_data[ $key ] = $d;
				++$i;
			}

			return $sanitised_data;

		}

		/**
		 * Callback logic for point awarding
		 *
		 * @param $post_id  int     Post ID
		 * @param $post     WP_Post Post data
		 */
		public function post_published( $post_id, $post ) {

			$user_id = $post->post_author;

			/**
			 * Check for post type
			 */
			if( 'post' != $post->post_type ) {
				return;
			}

			/**
			 * Check for user exclusions
			 */
			if ( $this->core->exclude_user( $user_id ) === true ) {
				return;
			}

			/**
			 * Check post zf data type to satisfy selected criteria
			 */
			/**
			 * Check post zf data type to satisfy selected criteria
			 */
			$zf_data_type = get_post_meta( $post_id, 'zombify_data_type', true );
			$is_sub_type = false;
			if( $zf_data_type == 'story' ) {
				$zf_data_subtype = get_post_meta( $post_id, 'zombify_data_subtype', true );

				if( 'main' != $zf_data_subtype ) {
					$zf_data_type = $zf_data_subtype;
					$is_sub_type = true;
				}
			}

			if( ! $zf_data_type ) {
				return;
			}

			$prefs = array_values( wp_list_filter( $this->prefs, array( 'zf_data_type' => ( $is_sub_type ? ( 'subtype_' . $zf_data_type ) : $zf_data_type ) ) ) );
			if( empty( $prefs ) ) {
				return;
			}

			/**
			 * Find pref setup
			 */
			$pref = $prefs[0];

			/**
			 * Make sure we award points other then zero
			 */
			if ( ! isset( $pref['creds'] ) || empty( $pref['creds'] ) || ( $pref['creds'] == 0 ) ) {
				return;
			}

			/**
			 * Prepare log data
			 */
			// todo- backend:   we will keep this data for future usage, ie: zf_data_type may become multiple or other
			//                  terms may be included in this criteria
			$data = array( 'formats' => array( ( $is_sub_type ? ( 'subtype_' . $zf_data_type ) : $zf_data_type ) ) );
			$post_types = zombify()->get_post_types();
			$entry = strtr( $pref['log'], array( '%format%' => $post_types[ $is_sub_type && ( $zf_data_type == 'main' ) ? 'story' : $zf_data_type ][ 'name' ] ) );
			$reference = $this->get_reference_id( $is_sub_type ? ( 'subtype_' . $zf_data_type ) : $zf_data_type );

			/**
			 * Make sure this is unique
			 */
			if ( $this->core->has_entry( $reference, $post_id, $user_id, NULL, $this->mycred_type ) ) {
				return;
			}

			/**
			 * Add Creds
			 */
			$this->core->add_creds(
				$reference,
				$user_id,
				$pref['creds'],
				$entry,
				$post_id,
				$data,
				$this->mycred_type
			);

		}

	}

	/**
	 * Register hook
	 */
	GFY_myCRED_Hook_Management_Service::get_instance()->register_hook(
		GFY_ZF_Hook_Publishing_In_Format::REFERENCE,
		__( 'Points for publishing in post formats | Gamify', 'gamify' ),
		__( 'Conditional hook to award %_plural% to authors when they publish posts with "Zombify" plugin in chosen post formats.', 'gamify' ),
		array( 'GFY_ZF_Hook_Publishing_In_Format' )
	);

}