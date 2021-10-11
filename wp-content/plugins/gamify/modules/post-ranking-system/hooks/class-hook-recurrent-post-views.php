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
if ( ! class_exists( 'GFY_PRS_Hook_Recurrent_Post_Views' ) && class_exists( 'myCRED_Hook' ) ) {

	class GFY_PRS_Hook_Recurrent_Post_Views extends myCRED_Hook {

		const REFERENCE = 'gfy_prs_recurrent_post_views';

		/**
		 * GFY_PRS_Hook_Recurrent_Post_Views constructor.
		 *
		 * @param array     $hook_prefs <string, array>
		 * @param string    $type       string
		 */
		function __construct( $hook_prefs, $type = MYCRED_DEFAULT_TYPE_KEY ) {

			$defaults = array(
				'views_amount'  => 10,
				'creds'         => 1,
				'log'           => '%plural% for %count% views'
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
			add_action( 'boombox/view_total_updated', array( $this, 'view_total_updated' ), 120, 2 );
			add_filter( 'mycred_all_references',  array( $this, 'add_references' ) );
		}

		/**
		 * Register custom references
		 * @param $references   array<string,string>    Registered references
		 *
		 * @return array        array<string,string>
		 */
		public function add_references( $references ) {

			$references[ self::REFERENCE ] = __( 'Count X views', 'gamify' );

			return $references;

		}

		/**
		 * Render preferences
		 */
		public function preferences() {
			$prefs = $this->prefs; ?>

			<div class="row">

				<?php
				$field_id       = $this->field_id( 'views_amount' );
				$field_label    = __( 'Views amount', 'gamify' );
				$field_name     = $this->field_name( 'views_amount' );
				$field_value    = $this->core->number( $prefs['views_amount'] );
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
				'%count%'       => __( 'selected count', 'gamify' )
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

            $data['views_amount'] = absint( $data['views_amount'] );
			$data['views_amount'] = $data['views_amount'] > 0 ? $data['views_amount'] : 1;

			$data['creds'] = absint( $data['creds'] );
			$data['creds'] = $data['creds'] > 0 ? $data['creds'] : 1;

		    return $data;
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

			/***** Make sure we have views amount other then zero */
			if ( ! isset( $this->prefs['views_amount'] ) ) {
				return;
			}
			if ( empty( $this->prefs['views_amount'] ) || $this->prefs['views_amount'] == 0 ) {
				return;
			}

			/***** Make sure we award points other then zero */
			if ( ! isset( $this->prefs['creds'] ) ) {
				return;
			}
			if ( empty( $this->prefs['creds'] ) || $this->prefs['creds'] == 0 ) {
				return;
			}

			/***** Get user recurrent post views count */
			$user_recurrent_post_views = absint( get_user_meta( $user_id, 'gfy_recurrent_post_views', true ) );
			$user_recurrent_post_views += $scale;

			/******* Update user meta data if we user did not reach configured view count */
			if( $user_recurrent_post_views < $this->prefs['views_amount'] ) {
                update_user_meta( $user_id, 'gfy_recurrent_post_views', $user_recurrent_post_views );
                return;
            }

			/**
			 * Prepare log entry
			 */
			$entry = strtr( $this->prefs['log'], array(
				'%count%' => $this->prefs['views_amount']
			) );

			/***** Award cred */
			$added = $this->core->add_creds(
				self::REFERENCE,
				$user_id,
				$this->prefs['creds'],
				$entry,
				0,
				null,
				$this->mycred_type
			);

			/***** Reset user meta data on successfully cred award */
			if( $added ) {
				update_user_meta( $user_id, 'gfy_recurrent_post_views', 0 );
            }

		}

	}

	/**
	 * Register hook
	 */
	GFY_myCRED_Hook_Management_Service::get_instance()->register_hook(
		GFY_PRS_Hook_Recurrent_Post_Views::REFERENCE,
		__( 'Points for every X views | Gamify', 'gamify' ),
		__( 'Award %_plural% to author for every X views through all their posts.', 'gamify' ),
		array( 'GFY_PRS_Hook_Recurrent_Post_Views' )
	);

}