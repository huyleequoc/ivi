<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Hook for view up
 * @since 1.0
 * @version 1.0
 */
if ( ! class_exists( 'GFY_PRS_Hook_View_Up' ) && class_exists( 'myCRED_Hook' ) ) {
	
	class GFY_PRS_Hook_View_Up extends myCRED_Hook {

		const REFERENCE = 'gfy_prs_single_view_up';

		/**
		 * GFY_PRS_Hook_View_Up constructor.
		 *
		 * @param array     $hook_prefs <string, array>
		 * @param string    $type       string
		 */
		function __construct( $hook_prefs, $type = MYCRED_DEFAULT_TYPE_KEY ) {
			
			$defaults = array(
				'disable' => 0,
				'creds'  => 1,
				'log'    => '%plural% for viewing post'
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
			add_action( 'boombox/view_total_updated', array( $this, 'view_total_update' ), 100, 2 );
			add_filter( 'mycred_all_references',  array( $this, 'add_references' ) );
		}

		/**
		 * Register custom references
		 * @param $references   array<string,string>    Registered references
		 *
		 * @return array        array<string,string>
		 */
		public function add_references( $references ) {

			$references[ self::REFERENCE ] = __( 'View up', 'gamify' );

			return $references;

		}

		/**
		 * Render preferences
		 */
		public function preferences() {
			$prefs = $this->prefs; ?>

			<div class="row">

				<?php
				$field_id           = $this->field_id( 'creds' );
				$field_label        = $this->core->plural();
				$field_name         = $this->field_name( 'creds' );
				$field_value        = $this->core->number( $prefs['creds'] );
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
		 * @param $scale    int View scale
		 * @param $post_id  int Post ID
		 */
		public function view_total_update( $scale, $post_id ) {
			
			$article = get_post( (int)$post_id );
			
			$user_id = $article->post_author;
			
			// Check for exclusions
			if ( $this->core->exclude_user( $user_id ) === true ) {
				return;
			}
			
			// Make sure we award points other then zero
			if ( ! isset( $this->prefs['creds'] ) ) {
				return;
			}
			if ( empty( $this->prefs['creds'] ) || $this->prefs['creds'] == 0 ) {
				return;
			}
			
			$entry = $this->prefs['log'];
			$data = array(
				'ip_address' => Boombox_Vote_Restriction::get_ip(),
				'session_id' => Boombox_Vote_Restriction::get_session_id()
			);
			
			// Add Creds
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
		GFY_PRS_Hook_View_Up::REFERENCE,
		__( 'Points for post single view up | Gamify', 'gamify' ),
		__( 'Award %_plural% to authors for viewing their posts.', 'gamify' ),
		array( 'GFY_PRS_Hook_View_Up' )
	);
}