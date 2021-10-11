<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Hook for voting on posts
 * @since 1.0
 * @version 1.0
 */
if ( ! class_exists( 'GFY_PRS_Hook_Point_Up_Down' ) && class_exists( 'myCRED_Hook' ) ) {
	
	class GFY_PRS_Hook_Point_Up_Down extends myCRED_Hook {

		const ID      = 'pfy_prs_point_up_down';
		const REFERENCE_UP   = 'pfy_prs_point_up_down';
		const REFERENCE_DOWN = 'pfy_prs_point_down';

		/**
		 * GFY_PRS_Hook_View_Up constructor.
		 *
		 * @param array     $hook_prefs <string, array>
		 * @param string    $type       string
		 */
		function __construct( $hook_prefs, $type = MYCRED_DEFAULT_TYPE_KEY ) {
			
			$defaults = array(
				'disable' => 0,
				'up'    => array(
					'creds'  => 1,
					'log'    => '%plural% for upvote',
				),
				'down'    => array(
					'creds'  => '-1',
					'log'    => '%plural% for downvote',
				)
			);
			
			if ( isset( $hook_prefs[ self::ID ] ) ) {
				$defaults = $hook_prefs[ self::ID ];
			}
			
			parent::__construct( array(
				'id'       => self::ID,
				'defaults' => $defaults
			), $hook_prefs, $type );
			
		}

		/**
		 * Start hook
		 */
		public function run() {
			add_action( 'boombox/point_total_updated', array( $this, 'point_total_update' ), 100, 3 );
			add_filter( 'mycred_all_references',  array( $this, 'add_references' ) );
		}

		/**
		 * Register custom references
		 * @param $references   array<string,string>    Registered references
		 *
		 * @return array        array<string,string>
		 */
		public function add_references( $references ) {

			$references[ self::REFERENCE_UP ] = __( 'Points for upvote', 'gamify' );
			$references[ self::REFERENCE_DOWN ] = __( 'Points for downvote', 'gamify' );

			return $references;

		}

		/**
		 * Render preferences
		 */
		public function preferences() {
			$prefs = $this->prefs; ?>

			<div class="hook-instance">
				<h3><?php _e( 'Upvote', 'gamify' ); ?></h3>
				<div class="row">

					<?php
					$field_id           = $this->field_id( array( 'up' => 'creds' ) );
					$field_label        = $this->core->plural();
					$field_name         = $this->field_name( array( 'up' => 'creds' ) );
					$field_value        = $this->core->number( $prefs['up']['creds'] );
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
					$field_id           = $this->field_id( array( 'up' => 'log' ) );
					$field_label        = __( 'Log template', 'gamify' );
					$field_name         = $this->field_name( array( 'up' => 'log' ) );
					$field_value        = esc_attr( $prefs['up']['log'] );
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
			</div>

			<div class="hook-instance">
				<h3><?php _e( 'Downvote', 'gamify' ); ?></h3>
				<div class="row">

					<?php
					$field_id           = $this->field_id( array( 'down' => 'creds' ) );
					$field_label        = $this->core->plural();
					$field_name         = $this->field_name( array( 'down' => 'creds' ) );
					$field_value        = $this->core->number( $prefs['down']['creds'] );
					?>
					<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
						<div class="form-group">
							<label for="<?php echo $field_id; ?>"><?php echo $field_label; ?></label>
							<input type="number"
							       name="<?php echo $field_name; ?>"
							       id="<?php echo $field_id; ?>"
							       value="<?php echo $field_value; ?>"
							       class="form-control" />
						</div>
					</div>

					<?php
					$field_id           = $this->field_id( array( 'down' => 'log' ) );
					$field_label        = __( 'Log template', 'gamify' );
					$field_name         = $this->field_name( array( 'down' => 'log' ) );
					$field_value        = esc_attr( $prefs['down']['log'] );
					$field_description  = $this->available_template_tags( array( 'general', 'post' ) );
					?>
					<div
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
			</div>
			<?php
		}
		
		/**
		 * Callback logic for point awarding
		 *
         * @param $post_id  int Post ID
		 * @param $scale    int Vote scale
		 */
		public function point_total_update( $post_id, $scale, $is_discard ) {
		    
		    if( $scale == 0 ) {
		        return;
            }

			if( $is_discard ) {
		        $multiplier = '-1';
		        $action = ( $scale < 0 ) ? 'up' : 'down';
			} else {
				$multiplier = 1;
				$action = ( $scale < 0 ) ? 'down' : 'up';
            }


			$article = get_post( (int)$post_id );

			$user_id = $article->post_author;

			// Check for exclusions
			if ( $this->core->exclude_user( $user_id ) === true ) {
				return;
			}

			// Make sure we award points other then zero
			if ( ! isset( $this->prefs[ $action ]['creds'] ) ) {
				return;
			}
			if ( empty( $this->prefs[ $action ]['creds'] ) || $this->prefs[ $action ]['creds'] == 0 ) {
				return;
			}

			$entry = $this->prefs[ $action ]['log'];
			$data = array(
                'action' => $action,
                'ip_address' => Boombox_Vote_Restriction::get_ip(),
                'session_id' => Boombox_Vote_Restriction::get_session_id()
            );

			// Add Creds
			$this->core->add_creds(
				( ( $action == 'up' ) ? self::REFERENCE_UP : self::REFERENCE_DOWN ),
				$user_id,
				$multiplier * $this->prefs[ $action ]['creds'],
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
		GFY_PRS_Hook_Point_Up_Down::ID,
			__( 'Points for upvote / downvote | Gamify', 'gamify' ),
		__( 'Award %_plural% to authors for upvote / downvote on their posts.', 'gamify' ),
		array( 'GFY_PRS_Hook_Point_Up_Down' )
	);

}