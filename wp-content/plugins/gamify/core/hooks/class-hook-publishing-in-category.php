<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Hook for publishing posts in preconfigured categories
 */
if ( ! class_exists( 'GFY_Core_Hook_Publishing_In_Category' )
	&& class_exists( 'myCRED_Hook' )
) {

	class GFY_Core_Hook_Publishing_In_Category extends myCRED_Hook {

		const REFERENCE = 'gfy_hook_publishing_in_category';

		/**
		 * GFY_Core_Hook_Publishing_In_Category constructor.
		 *
		 * @param array     $hook_prefs <string, array>
		 * @param string    $type       string
		 */
		function __construct( $hook_prefs, $type = MYCRED_DEFAULT_TYPE_KEY ) {

			$defaults = array(
				'row_0' => array(
					'creds'     => 1,
					'category'  => 0,
					'log'       => '%plural% for publishing in %category%'
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

			$categories = get_terms( array(
				'taxonomy' => 'category',
				'fields'   => 'id=>name',
				'include'  => wp_list_pluck( $this->prefs, 'category' )
			) );

			foreach( $categories as $cat_id => $cat_name ) {
				$references[ $this->get_reference_id( $cat_id ) ] = $this->get_reference_label( $cat_name );
			}

			return $references;

		}

		/**
		 * Render preferences
		 */
		public function preferences() {

			$prefs = $this->prefs;

			$categories = get_terms( 'category', array(
				'hide_empty' => false,
			) );

			$i = 0;
			$unique_id = uniqid(); ?>
			<div class="mycred-repeater" id="<?php echo $unique_id; ?>">
				<?php foreach( $prefs as $key => $pref ) { ?>
					<div class="row hook-instance mycred-repeater-item <?php if( $i == 0 ) { echo 'cloneable'; } ?>" data-index="<?php echo $i; ?>">

						<?php if( $i > 0 ) { ?>
							<a href="#" class="delete-row dashicons dashicons-no"></a>
						<?php } ?>

						<?php
						$field_id       = $this->field_id( array( $key => 'creds' ) );
						$field_label    = $this->core->plural();
						$field_name     = $this->field_name( array( $key => 'creds' ) );
						$field_value    = $this->core->number( $pref['creds'] );
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
						$field_id       = $this->field_id( array( $key => 'category' ) );
						$field_label    = __( 'Category', 'gamify' );
						$field_name     = $this->field_name( array( $key => 'category' ) );
						?>
						<div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
							<div class="form-group">
								<label for="<?php echo $field_id; ?>"><?php echo $field_label; ?></label>
								<select name="<?php echo $field_name; ?>"
								        id="<?php echo $field_id; ?>"
								        class="form-control">
									<?php foreach( $categories as $category ) { ?>
										<option <?php selected( $pref['category'], $category->term_id ) ?>
												value="<?php echo $category->term_id; ?>"
										><?php echo $category->name; ?></option>
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
				'%category%'       => __( 'selected category', 'gamify' )
			);
			foreach( $template_tags as $tag => $description ) {
				$custom .= sprintf( '%s - %s, ', $tag, $description );
			}

			return parent::available_template_tags( $available, rtrim( $custom, ', ' ) );
		}

		/**
		 * Get dynamic reference ID for specific category
		 * @param int $cat_id Category term ID
		 * @return string
		 */
		private function get_reference_id( $cat_id ) {
			return sprintf( '%s_category_%d', self::REFERENCE, $cat_id );
		}

		/**
		 * Get dynamic reference label for specific category
		 * @param string $cat_name Category name
		 * @return string
		 */
		private function get_reference_label( $cat_name ) {
			return sprintf( __( 'Publish in "%s" category', 'gamify' ), $cat_name );
		}

		/**
		 * Sanitise preferences
		 *
		 * @param $data array <string,array>  Data to sanitize
		 * @return array Sanitized data
		 */
		public function sanitise_preferences( $data ) {

			$sanitised_data = array();

			$unique_data_types = array_unique( wp_list_pluck( $data, 'category' ) );
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
			 * Check post categories to satisfy selected criteria
			 */
			$post_categories = wp_list_pluck( wp_get_post_categories( $post_id, array( 'fields' => 'all' ) ), 'name', 'term_id' );
			$pref = false;
			foreach( $this->prefs as $pref_setup ) {
				if( array_key_exists( $pref_setup['category'], $post_categories ) ) {
					$pref = $pref_setup;
					break;
				}
			}

			if( ! $pref ) {
				return;
			}

			/**
			 * Make sure we award points other then zero
			 */
			if ( ! isset( $pref['creds'] ) || empty( $pref['creds'] ) || ( $pref['creds'] == 0 ) ) {
				return;
			}

			// todo- backend:   we will keep this data for future usage, ie: categories may become multiple or other
			//                  terms may be included in this criteria
			$data = array( 'categories' => array( $pref['category'] ) );
			$entry = strtr( $pref['log'], array( '%category%' => $post_categories[ $pref['category'] ] ) );
			$reference = $this->get_reference_id( $pref['category'] );

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
		GFY_Core_Hook_Publishing_In_Category::REFERENCE,
		__( 'Points for publishing in categories | Gamify', 'gamify' ),
		__( 'Conditional hook to award %_plural% to authors when they publish posts in chosen categories.', 'gamify' ),
		array( 'GFY_Core_Hook_Publishing_In_Category' )
	);

}