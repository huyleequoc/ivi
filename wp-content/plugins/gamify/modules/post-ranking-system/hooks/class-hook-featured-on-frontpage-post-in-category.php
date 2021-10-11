<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Hook for featured posts
 * @since 1.0
 * @version 1.0
 */
if ( ! class_exists( 'GFY_PRS_Hook_Featured_Front_Page_Post_In_Category' ) && class_exists( 'myCRED_Hook' ) ) {

	class GFY_PRS_Hook_Featured_Front_Page_Post_In_Category extends myCRED_Hook {

	    const REFERENCE = 'gfy_prs_featured_frontpage_post';

		/**
		 * GFY_PRS_Hook_Featured_Front_Page_Post_In_Category constructor.
		 *
		 * @param array     $hook_prefs <string, array>
		 * @param string    $type       string
		 */
		function __construct( $hook_prefs, $type = MYCRED_DEFAULT_TYPE_KEY ) {

			$defaults = array(
				'row_0' => array(
					'creds'  => 1,
					'log'    => '%plural% for featured post on front page'
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
			add_action( 'added_post_meta', array( $this, 'post_meta_updated' ), 100, 4 );
			add_action( 'updated_post_meta', array( $this, 'post_meta_updated' ), 100, 4 );
			add_filter( 'mycred_all_references',  array( $this, 'add_references' ) );
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
			return sprintf( __( 'Have featured post on front page in "%s" category', 'gamify' ), $cat_name );
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
		 * @param $meta_id      int     Post meta ID
		 * @param $post_id    int     Post ID
		 * @param $meta_key     string  Meta key
		 * @param $meta_value   mixed   Meta value
		 */
		public function post_meta_updated( $meta_id, $post_id, $meta_key, $meta_value ) {

			/**
			 * Do nothing if meta tag is not related to current hook
			 */
			if( 'boombox_is_featured_frontpage' != $meta_key ) {
				return;
			}

			/**
			 * Make sure that we are working with appropriate checkbox checked state
			 */
			if( ! $meta_value ) {
				return;
			}

			$article = get_post( (int)$post_id );
			$user_id = $article->post_author;

			/**
			 * Check for exclusions
			 */
			if ( $this->core->exclude_user( $user_id ) === true ) {
				return;
			}

			$post_categories = wp_list_pluck( wp_get_post_categories( $post_id, array( 'fields' => 'all' ) ), 'name', 'term_id' );
			foreach( $this->prefs as $pref_setup ) {

				$term_id = $pref_setup['category'];

				if( ! array_key_exists( $term_id, $post_categories ) ) {
					continue;
				}

				/**
				 * Make sure we award points other then zero
				 */
				if ( ! isset( $pref_setup['creds'] ) || empty( $pref_setup['creds'] ) || ( $pref_setup['creds'] == 0 ) ) {
					continue;
				}

				$reference = $this->get_reference_id( $term_id );
				$entry = strtr( $pref_setup['log'], array( '%category%' => $post_categories[ $term_id ] ) );
				$data = array( $meta_key => $meta_value );

				/**
				 * Make sure this is unique
				 */
				if ( $this->core->has_entry( $reference, $article->ID, $user_id, $data, $this->mycred_type ) ) {
					return;
				}

				/**
				 * Award creds
				 */
				$this->core->add_creds(
					$reference,
					$user_id,
					$pref_setup['creds'],
					$entry,
					$article->ID,
					$data,
					$this->mycred_type
				);

			}

		}

	}

	/**
	 * Register hook
	 */
	GFY_myCRED_Hook_Management_Service::get_instance()->register_hook(
		GFY_PRS_Hook_Featured_Front_Page_Post_In_Category::REFERENCE,
		__( 'Points for featured post on front page in category | Gamify', 'gamify' ),
		__( 'Award %_plural% to authors when post from chosen categories becomes featured on front page .', 'gamify' ),
		array( 'GFY_PRS_Hook_Featured_Front_Page_Post_In_Category' )
	);

}