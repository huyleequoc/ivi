<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Hook for view milestones
 * @since 1.0
 * @version 1.0
 */
if ( ! class_exists( 'GFY_PRS_Hook_Single_Post_View' ) && class_exists( 'myCRED_Hook' ) ) {
	
	class GFY_PRS_Hook_Single_Post_View extends myCRED_Hook {

		const REFERENCE = 'gfy_prs_single_post_views';

		/**
		 * GFY_PRS_Hook_Single_Post_View constructor.
		 *
		 * @param array     $hook_prefs <string, array>
		 * @param string    $type       string
		 */
		function __construct( $hook_prefs, $type = MYCRED_DEFAULT_TYPE_KEY ) {
		    
			$defaults = array(
                'row_0' => array(
                    'views'  => 10,
                    'creds'  => 1,
                    'log'    => '%plural% for %milestone% views on single post'
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
			add_action( 'boombox/view_total_updated', array( $this, 'view_total_updated' ), 110, 2 );
			add_filter( 'mycred_all_references',  array( $this, 'add_references' ) );
		}

		/**
		 * Register custom references
		 * @param $references   array<string,string>    Registered references
		 *
		 * @return array        array<string,string>
		 */
		public function add_references( $references ) {

			foreach( $this->prefs as $pref ) {
				$references[ $this->get_reference_id( $pref['views'] ) ] = $this->get_reference_label( $pref['views'] );
			}

			return $references;

		}

		/**
		 * Render preferences
		 */
		public function preferences() {

			$prefs = $this->prefs;
			$i = 0;
			$unique_id = uniqid(); ?>
			<div class="mycred-repeater" id="<?php echo $unique_id; ?>">
				<?php foreach( $prefs as $key => $pref ) { ?>
					<div class="row hook-instance mycred-repeater-item <?php if( $i == 0 ) { echo 'cloneable'; } ?>" data-index="<?php echo $i; ?>">

						<?php if( $i > 0 ) { ?>
							<a href="#" class="delete-row dashicons dashicons-no"></a>
						<?php } ?>

						<?php
						$field_id               = $this->field_id( array( $key => 'views' ) );
						$field_dynamic_label    = __( 'Milestone #{N}', 'gamify' );
						$field_label    = strtr( $field_dynamic_label, array( '{N}' => ( preg_replace("/[^0-9,.]/", "", $key ) + 1 ) ) );
						$field_name     = $this->field_name( array( $key => 'views' ) );
						$field_value    = $this->core->number( $pref['views'] );
						?>
						<div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
							<div class="form-group">
								<label for="<?php echo $field_id; ?>" data-dynamic="<?php echo $field_dynamic_label; ?>"><?php echo $field_label; ?></label>
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
				'%milestone%'       => __( 'selected milestone', 'gamify' )
			);
			foreach( $template_tags as $tag => $description ) {
				$custom .= sprintf( '%s - %s, ', $tag, $description );
			}

			return parent::available_template_tags( $available, rtrim( $custom, ', ' ) );
		}

		/**
		 * Get dynamic reference ID for specific milestone
		 * @param int $milestone Milestone
		 * @return string
		 */
		private function get_reference_id( $milestone ) {
			return sprintf( '%s_milestone_%d', self::REFERENCE, $milestone );
		}

		/**
		 * Get dynamic reference label for specific milestone
		 * @param int $milestone Milestone
		 * @return string
		 */
		private function get_reference_label( $milestone ) {
			return sprintf( __( 'Reaching single post milestone: %d views', 'gamify' ), $milestone );
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
			
			// Check for exclusions
			if ( $this->core->exclude_user( $user_id ) === true ) {
				return;
			}
			
			/***** Get current total view cound related to mycred integration */
			$gfy_prs_total_views = (int)get_post_meta( $post_id, 'gfy_prs_total_views', true );
			
			/******* Let's check now for single post views milestone */
			$prefs = array_filter( $this->prefs, function( $pref ) use ( $gfy_prs_total_views ){
				return $pref['views'] >= $gfy_prs_total_views;
			} );
			
			/******* Sort preferences to get nearby value as the first element in array */
			usort( $prefs, function ( $a, $b ) { return $a['views'] - $b['views']; } );
			
			/******* Update post total views meta key related to mycred integration */
			$gfy_prs_total_views += $scale;
			update_post_meta( $post_id, 'gfy_prs_total_views', $gfy_prs_total_views );
			
			/******* Make sure that we have it */
			if( empty( $prefs ) ) {
				return;
			}
			
			$pref = $prefs[0];
			
			// Make sure we award points other then zero
			if ( ! isset( $pref['creds'] ) ) {
				return;
			}
			if ( empty( $pref['creds'] ) || $pref['creds'] == 0 ) {
				return;
			}
			
			/******* Log in case if single post views reached nearby milestone */
			if( $pref['views'] > $gfy_prs_total_views ) {
                return;
            }

			$entry = strtr( $pref['log'], array( '%milestone%'   => $pref['views'] ) );
			$data = null;
			$reference = $this->get_reference_id( $pref['views'] );

			/**
			 * Make sure this is unique
			 */
			if ( $this->core->has_entry( $reference, $article->ID, $user_id, $data, $this->mycred_type ) ) {
				return;
			}

			/******* Award creds */
			$this->core->add_creds(
				$reference,
				$user_id,
				$pref['creds'],
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
		GFY_PRS_Hook_Single_Post_View::REFERENCE,
		__( 'Points for reaching views milestones on single post | Gamify', 'gamify' ),
		__( 'Conditional hook to award %_plural% to authors when their post views reach chosen counts.', 'gamify' ),
		array( 'GFY_PRS_Hook_Single_Post_View' )
	);


}