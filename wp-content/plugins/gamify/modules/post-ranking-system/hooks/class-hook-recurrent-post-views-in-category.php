<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Hook for recurrent post views
 * @since 1.0
 * @version 1.0
 */
if ( ! class_exists( 'GFY_PRS_Hook_Recurrent_Post_Views_In_Category' ) && class_exists( 'myCRED_Hook' ) ) {

	class GFY_PRS_Hook_Recurrent_Post_Views_In_Category extends myCRED_Hook {

		const REFERENCE = 'gfy_prs_recurrent_post_views_in_category';

		/**
		 * GFY_PRS_Hook_Recurrent_Post_Views_In_Category constructor.
		 *
		 * @param array     $hook_prefs <string, array>
		 * @param string    $type       string
		 */
		function __construct( $hook_prefs, $type = MYCRED_DEFAULT_TYPE_KEY ) {

			$defaults = array(
				'row_0' => array(
					'views_amount'  => 10,
					'creds'         => 1,
					'category'      => 0,
					'log'           => '%plural% for %count% views in %category%'
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
			add_filter( 'mycred_all_references',  array( $this, 'add_references' ) );
			add_action( 'boombox/view_total_updated', array( $this, 'view_total_updated' ), 120, 2 );
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
						$field_id       = $this->field_id( array( $key => 'views_amount' ) );
						$field_label    = __( 'Views amount', 'gamify' );
						$field_name     = $this->field_name( array( $key => 'views_amount' ) );
						$field_value    = $this->core->number( $pref['views_amount'] );
						?>
						<div class="col-lg-3 col-md-12 col-sm-12 col-xs-12">
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
						$field_id       = $this->field_id( array( $key => 'creds' ) );
						$field_label    = $this->core->plural();
						$field_name     = $this->field_name( array( $key => 'creds' ) );
						$field_value    = $this->core->number( $pref['creds'] );
						?>
						<div class="col-lg-3 col-md-12 col-sm-12 col-xs-12">
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
						<div class="col-lg-3 col-md-12 col-sm-12 col-xs-12">
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
						<div class="col-lg-3 col-md-12 col-sm-12 col-xs-12">
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
				'%count%'       => __( 'selected count', 'gamify' ),
				'%category%'    => __( 'selected category', 'gamify' )
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
			return sprintf( __( 'Count X views in "%s" category', 'gamify' ), $cat_name );
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

				$d['views_amount'] = absint( $d['views_amount'] );
				$d['views_amount'] = $d['views_amount'] > 0 ? $d['views_amount'] : 1;

				$d['creds'] = absint( $d['creds'] );
				$d['creds'] = $d['creds'] > 0 ? $d['creds'] : 1;

				$sanitised_data[ $key ] = $d;
				++$i;
			}

			return $sanitised_data;
		}

		/**
		 * Callback logic for point awarding
		 *
		 * @param $scale    int View scale
		 * @param $post_id  int Post ID
		 */
		public function view_total_updated( $scale, $post_id ) {

			$article = get_post( (int)$post_id );

			$user_id = $article->post_author;

			/***** Check for exclusions */
			if ( $this->core->exclude_user( $user_id ) === true ) {
				return;
			}

			/***** Get user recurrent post in categories views count */
			$user_recurrent_post_views_in_cats = get_user_meta( $user_id, 'gfy_recurrent_post_views_in_category', true );
			if( ! $user_recurrent_post_views_in_cats ) {
				$user_recurrent_post_views_in_cats = array();
			}
			$post_categories = wp_list_pluck( wp_get_post_categories( $post_id, array( 'fields' => 'all' ) ), 'name', 'term_id' );
			foreach( $this->prefs as $pref_setup ) {

				$term_id = $pref_setup[ 'category' ];

				if( ! array_key_exists( $term_id, $post_categories ) ) {
					continue;
				}

				/***** Make sure we have views amount other then zero */
				if ( ! isset( $pref_setup['views_amount'] ) ) {
					continue;
				}
				if ( empty( $pref_setup['views_amount'] ) || $pref_setup['views_amount'] == 0 ) {
					continue;
				}

				if( ! isset( $user_recurrent_post_views_in_cats[ $term_id ] ) ) {
					$user_recurrent_post_views_in_cats[ $term_id ] = 0;
				}
				$user_recurrent_post_views_in_cats[ $term_id ] += $scale;

				/******* setup user meta data if we user did not reach configured view count */
				if( $user_recurrent_post_views_in_cats[ $term_id ] < $pref_setup['views_amount'] ) {
					continue;
				} else {
					$user_recurrent_post_views_in_cats[ $term_id ] = 0;
				}

				/**
				 * Prepare log entry
				 */
				$entry = strtr( $pref_setup['log'], array(
					'%count%'       => $pref_setup['views_amount'],
					'%category%'    => $post_categories[ $term_id ]
				) );

				$reference = $this->get_reference_id( $term_id );

				/***** Award cred */
				$added = $this->core->add_creds(
					$reference,
					$user_id,
					$pref_setup['creds'],
					$entry,
					0,
					null,
					$this->mycred_type
				);
			}
			update_user_meta( $user_id, 'gfy_recurrent_post_views_in_category', $user_recurrent_post_views_in_cats );

		}

	}

	/**
	 * Register hook
	 */
	GFY_myCRED_Hook_Management_Service::get_instance()->register_hook(
		GFY_PRS_Hook_Recurrent_Post_Views_In_Category::REFERENCE,
		__( 'Points for every X views in categories | Gamify', 'gamify' ),
		__( 'Award %_plural% to author for every X views through all their posts in chosen categories.', 'gamify' ),
		array( 'GFY_PRS_Hook_Recurrent_Post_Views_In_Category' )
	);

}