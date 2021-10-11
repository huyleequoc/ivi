<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Hook for recurrent post votes
 * @since 1.0
 * @version 1.0
 */
if ( ! class_exists( 'GFY_PRS_Hook_Recurrent_Post_Votes' ) && class_exists( 'myCRED_Hook' ) ) {

	class GFY_PRS_Hook_Recurrent_Post_Votes extends myCRED_Hook {

		const REFERENCE = 'gfy_prs_recurrent_post_votes';

		/**
		 * GFY_PRS_Hook_Recurrent_Post_Votes constructor.
		 *
		 * @param array     $hook_prefs <string, array>
		 * @param string    $type       string
		 */
		function __construct( $hook_prefs, $type = MYCRED_DEFAULT_TYPE_KEY ) {

			$defaults = array(
				'votes_amount'  => 10,
				'creds'         => 1,
				'log'           => '%plural% for %count% votes'
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
			add_action( 'boombox/point_total_updated', array( $this, 'point_total_updated' ), 10, 3 );
			//add_filter( 'mycred_all_references',  array( $this, 'add_references' ) );
		}

		/**
		 * Register custom references
		 * @param $references   array<string,string>    Registered references
		 *
		 * @return array        array<string,string>
		 */
		public function add_references( $references ) {

			$references[ self::REFERENCE ] = __( 'Count X votes', 'gamify' );

			return $references;

		}

		/**
		 * Render preferences
		 */
		public function preferences() {
			$prefs = $this->prefs; ?>

			<div class="row">

				<?php
				$field_id       = $this->field_id( 'votes_amount' );
				$field_label    = __( 'Votes amount', 'gamify' );
				$field_name     = $this->field_name( 'votes_amount' );
				$field_value    = $this->core->number( $prefs['votes_amount'] );
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

            $data['votes_amount'] = absint( $data['votes_amount'] );
			$data['votes_amount'] = $data['votes_amount'] > 0 ? $data['votes_amount'] : 1;

			$data['creds'] = absint( $data['creds'] );
			$data['creds'] = $data['creds'] > 0 ? $data['creds'] : 1;

		    return $data;
		}

		/**
		 * Callback logic for point awarding
		 * @param int  $post_id     The post ID
		 * @param int  $scale       The point scale
		 * @param bool $is_discard  Whether current pointing is a discard action
		 */
		public function point_total_updated( $post_id, $scale, $is_discard ) {

			/***** Prevent processing on point discard */
			if( $is_discard ) {
				return;
			}

			/***** Prevent processing on invalid scale value */
			$scale = absint( $scale );
			if( ! $scale ) {
				return;
			}

			/***** Prevent processing for guest user */
			$user_id = Boombox_Vote_Restriction::get_user_id();
			if( ! $user_id ) {
				return;
			}

			/***** Prevent processing on invalid posts */
			if( ! $post_id ) {
				return;
			}

			/***** Check for exclusions */
			if ( true === $this->core->exclude_user( $user_id ) ) {
				return;
			}

			/***** Make sure we have votes amount other then zero */
			if ( ! isset( $this->prefs[ 'votes_amount' ] ) ) {
				return;
			}
			if ( empty( $this->prefs[ 'votes_amount' ] ) || $this->prefs[ 'votes_amount' ] == 0 ) {
				return;
			}

			/***** Make sure we award points other then zero */
			if ( ! isset( $this->prefs[ 'creds' ] ) ) {
				return;
			}
			if ( empty( $this->prefs[ 'creds' ] ) || $this->prefs[ 'creds' ] == 0 ) {
				return;
			}

			$user_meta_key = 'gfy_recurrent_post_points';
			
			/***** Get user recurrent post votes count */
			$user_meta_data = wp_parse_args( get_user_meta( $user_id, $user_meta_key, true ), array(
				'recurrent_points' => 0,
				'pointed_on'       => array(),
			) );

			$user_meta_data[ 'pointed_on' ] = (array)$user_meta_data[ 'pointed_on' ];
			$user_meta_data[ 'recurrent_points' ] = absint( $user_meta_data[ 'recurrent_points' ] );

			/***** Prevent processing on already voted posts */
			if( in_array( $post_id, $user_meta_data[ 'pointed_on' ] ) ) {
				return;
			}

			$user_meta_data[ 'recurrent_points' ] += $scale;

			/***** Process awarding */
			if( $user_meta_data[ 'recurrent_points' ] >= $this->prefs[ 'votes_amount' ] ) {

				/***** Prepare log entry */
				$entry = strtr( $this->prefs[ 'log' ], array( '%count%' => $this->prefs[ 'votes_amount' ] ) );

				/***** Award cred */
				$awarded = $this->core->add_creds( self::REFERENCE, $user_id, $this->prefs[ 'creds' ], $entry, 0, null, $this->mycred_type );

				/***** Reset user meta data on successfully award */
				if( $awarded ) {
					$user_meta_data[ 'recurrent_points' ] = 0;
					$user_meta_data[ 'pointed_on' ][] = $post_id;
				}

            } else {
				$user_meta_data[ 'pointed_on' ][] = $post_id;
			}

			/***** Update user meta data */
			update_user_meta( $user_id, $user_meta_key, $user_meta_data );

		}

	}

	/**
	 * Register hook
	 */
	GFY_myCRED_Hook_Management_Service::get_instance()->register_hook(
		GFY_PRS_Hook_Recurrent_Post_Votes::REFERENCE,
		__( 'Points for every X votes | Gamify', 'gamify' ),
		__( 'Award %_plural% to authenticated user for every X votes through other users posts.', 'gamify' ),
		array( 'GFY_PRS_Hook_Recurrent_Post_Votes' )
	);

}