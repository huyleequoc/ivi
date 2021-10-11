<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Hook for publishing posts in categories
 */
if ( ! class_exists( 'GFY_Core_Hook_Publishing_Firstly_In_Category' )
	&& class_exists( 'myCRED_Hook' )
) {

	class GFY_Core_Hook_Publishing_Firstly_In_Category extends myCRED_Hook {

		const REFERENCE = 'gfy_hook_publishing_firstly_in_category';

		/**
		 * GFY_Core_Hook_Publishing_Firstly_In_Category constructor.
		 *
		 * @param array     $hook_prefs <string, array>
		 * @param string    $type       string
		 */
		function __construct( $hook_prefs, $type = MYCRED_DEFAULT_TYPE_KEY ) {

			$defaults = array(
				'creds'      => 1,
				'categories' => array(),
				'log'        => '%plural% for publishing first time in %category%'
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

			$references[ self::REFERENCE ] = __( 'Publish first time in category', 'gamify' );

			return $references;

		}

		/**
		 * Render preferences
		 */
		public function preferences() {

			$prefs = $this->prefs;

			$categories = get_terms( 'category', array(
				'hide_empty' => false,
			) ); ?>

			<div class="row">

				<?php
				$field_id       = $this->field_id( 'creds' );
				$field_label    = $this->core->plural();
				$field_name     = $this->field_name( 'creds' );
				$field_value    = $this->core->number( $prefs['creds'] );
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
				$field_id       = $this->field_id( 'categories' );
				$field_label    = __( 'Categories', 'gamify' );
				$field_name     = $this->field_name( 'categories' );
				?>
				<div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
					<div class="form-group">
						<label for="<?php echo $field_id; ?>"><?php echo $field_label; ?></label>
						<select name="<?php echo $field_name; ?>[]"
						        id="<?php echo $field_id; ?>"
						        class="form-control"
						        multiple="multiple"
						        size="10">
							<?php foreach( $categories as $category ) {
								$selected = in_array( $category->term_id, $prefs['categories'] ) ? ' selected="selected" ' : ''; ?>
								<option <?php echo $selected; ?> value="<?php echo $category->term_id; ?>">
									<?php echo $category->name; ?>
								</option>
							<?php } ?>
						</select>
					</div>
				</div>

				<?php
				$field_id           = $this->field_id( 'log' );
				$field_label        = __( 'Log template', 'gamify' );
				$field_name         = $this->field_name( 'log' );
				$field_value        = esc_attr( $prefs['log'] );
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
		 * Sanitise preferences
		 *
		 * @param $data array <string,array>  Data to sanitize
		 * @return array Sanitized data
		 */
		public function sanitise_preferences( $data ) {

			$data['creds'] = absint( $data['creds'] );
			$data['creds'] = $data['creds'] > 0 ? $data['creds'] : 1;

			return $data;

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

			$user_published_categories = get_user_meta( $user_id, 'gfy_user_published_categories', true );
			$user_published_categories = (bool)$user_published_categories ? $user_published_categories : array();

			/**
			 * Check post categories to satisfy selected criteria
			 */
			$post_categories = wp_list_pluck(
				wp_get_post_categories( $post_id, array( 'fields' => 'all' ) ),
				'name',
				'term_id'
			);
			$cat_id = false;
			foreach( (array)$this->prefs['categories'] as $configured_term_id ) {
				if( array_key_exists( $configured_term_id, $post_categories ) && ! in_array( $configured_term_id, $user_published_categories ) ) {
					$cat_id = $configured_term_id;
					break;
				}
			}

			if( ! $cat_id ) {
				return;
			}

			/**
			 * Make sure we award points other then zero
			 */
			if ( ! isset( $this->prefs['creds'] ) || empty( $this->prefs['creds'] ) || ( $this->prefs['creds'] == 0 ) ) {
				return;
			}

			// todo- backend:   we will keep this data for future usage, ie: categories may become multiple or other
			//                  terms may be included in this criteria
			$data = array( 'categories' => array( $cat_id ) );
			$entry = strtr( $this->prefs['log'], array( '%category%' => $post_categories[ $cat_id ] ) );

			/**
			 * Add Creds
			 */
			$added = $this->core->add_creds(
				self::REFERENCE,
				$user_id,
				$this->prefs['creds'],
				$entry,
				$post_id,
				$data,
				$this->mycred_type
			);

			if( $added ) {
				$user_published_categories[] = $cat_id;
				update_user_meta( $user_id, 'gfy_user_published_categories', $user_published_categories );
			}

		}

	}

	/**
	 * Register hook
	 */
	GFY_myCRED_Hook_Management_Service::get_instance()->register_hook(
		GFY_Core_Hook_Publishing_Firstly_In_Category::REFERENCE,
		__( 'Points for publishing 1st post in categories | Gamify', 'gamify' ),
		__( 'Award %_plural% to authors for publishing their 1st post in one of chosen categories.', 'gamify' ),
		array( 'GFY_Core_Hook_Publishing_Firstly_In_Category' )
	);

}