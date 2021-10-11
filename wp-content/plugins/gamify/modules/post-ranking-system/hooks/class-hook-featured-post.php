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
if ( ! class_exists( 'GFY_PRS_Hook_Featured_Post' ) && class_exists( 'myCRED_Hook' ) ) {

	class GFY_PRS_Hook_Featured_Post extends myCRED_Hook {

		const REFERENCE = 'gfy_prs_featured_post';

		/**
		 * GFY_PRS_Hook_Featured_Post constructor.
		 *
		 * @param array     $hook_prefs <string, array>
		 * @param string    $type       string
		 */
		function __construct( $hook_prefs, $type = MYCRED_DEFAULT_TYPE_KEY ) {

			$defaults = array(
				'creds'  => 1,
				'log'    => '%plural% for featured post'
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

			$references[ self::REFERENCE ] = __( 'Have featured post', 'gamify' );

			return $references;

		}

		/**
		 * Render preferences
		 */
		public function preferences() {
			$prefs = $this->prefs; ?>

			<div class="row">
				<?php
				$field_id       = $this->field_id( 'creds' );
				$field_label    = $this->core->plural();
				$field_name     = $this->field_name( 'creds' );
				$field_value    = $this->core->number( $prefs['creds'] );
				?>
				<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
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
				$field_id           = $this->field_id( 'log' );
				$field_label        = __( 'Log template', 'gamify' );
				$field_name         = $this->field_name( 'log' );
				$field_value        = esc_attr( $prefs['log'] );
				$field_description  = $this->available_template_tags( array( 'general', 'post' ) );
				?>
				<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
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
			if( 'boombox_is_featured' != $meta_key ) {
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

			/**
			 * Make sure we award points other then zero
			 */
			if ( ! isset( $this->prefs['creds'] ) || empty( $this->prefs['creds'] ) || ( $this->prefs['creds'] == 0 ) ) {
				return;
			}

			$entry = $this->prefs['log'];
			$data = array( $meta_key => $meta_value );

			/**
			 * Make sure this is unique
			 */
			if ( $this->core->has_entry( self::REFERENCE, $article->ID, $user_id, $data, $this->mycred_type ) ) {
				return;
			}

			/**
			 * Award creds
			 */
			$this->core->add_creds(
				self::REFERENCE,
				$user_id,
				$this->prefs['creds'],
				$entry,
				$article->ID,
				$data,
				$this->mycred_type
			);

		}

	}

	/**
	 * Register hook
	 */
	GFY_myCRED_Hook_Management_Service::get_instance()->register_hook(
		GFY_PRS_Hook_Featured_Post::REFERENCE,
		__( 'Points for making featured post | Gamify', 'gamify' ),
		__( 'Award %_plural% to authors when post becomes featured.', 'gamify' ),
		array( 'GFY_PRS_Hook_Featured_Post' )
	);

}