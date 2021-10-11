<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Hook for publishing posts with preconfigured criteria
 * @since 1.0
 * @version 1.0
 */
if ( ! class_exists( 'GFY_Core_Hook_Publishing_Bonuses' )
	&& class_exists( 'myCRED_Hook' )
) {

	class GFY_Core_Hook_Publishing_Bonuses extends myCRED_Hook {

		const REFERENCE = 'gfy_hook_publishing_bonuses';

		/**
		 * GFY_Core_Hook_Publishing_Bonuses constructor.
		 *
		 * @param array     $hook_prefs <string, array>
		 * @param string    $type       string
		 */
		function __construct( $hook_prefs, $type = MYCRED_DEFAULT_TYPE_KEY ) {

			$defaults = array(
				'row_0' => array(
					'interval'  => 'day',
					'creds'     => 1,
					'count'     => 1,
					'log'       => '%plural% for publishing %count% posts during the %interval%'
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
			add_action( 'publish_post', array( $this, 'post_published' ), 9999, 2 );
		}

		/**
		 * Register custom references
		 * @param $references   array<string,string>    Registered references
		 *
		 * @return array        array<string,string>
		 */
		public function add_references( $references ) {
			$references[ self::REFERENCE ] = __( 'Publish in time intervals', 'gamify' );

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
							$field_id       = $this->field_id( array( $key => 'interval' ) );
							$field_label    = __( 'Interval', 'gamify' );
							$field_name     = $this->field_name( array( $key => 'interval' ) );
							$field_choices  = $this->get_interval_choices();
						?>
						<div class="col-lg-3 col-md-12 col-sm-12 col-xs-12">
							<div class="form-group">
								<label for="<?php echo $field_id; ?>"><?php echo $field_label; ?></label>
								<select name="<?php echo $field_name; ?>" id="<?php echo $field_id; ?>" class="form-control">
									<?php foreach( $field_choices as $interval => $label ) { ?>
										<option value="<?php echo $interval; ?>" <?php selected( $pref['interval'], $interval ) ?>><?php echo $label; ?></option>
									<?php } ?>
								</select>
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
							$field_id       = $this->field_id( array( $key => 'count' ) );
							$field_label    = __( 'Published posts count', 'gamify' );
							$field_name     = $this->field_name( array( $key => 'count' ) );
							$field_value    = $this->core->number( $pref['count'] );
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
				'%interval%'    => __( 'selected interval', 'gamify' )
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

			$sanitised_data = array();

			$unique_data_types = array_unique( wp_list_pluck( $data, 'interval' ) );
			$data = array_intersect_key( $data, $unique_data_types );

			$i = 0;
			foreach( $data as $d ) {
				$key = 'row_' . $i;
				$d['creds'] = max( absint( $d['creds'] ), 1 );
				$d['count'] = max( absint( $d['count'] ), 1 );

				$sanitised_data[ $key ] = $d;
				++$i;
			}

			return $sanitised_data;

		}

		/**
		 * Get interval choices
		 * @return array<string,string> Intervals configuration
		 */
		private function get_interval_choices() {
			return array(
				'day'   => __( 'Day', 'gamify' ),
				'week'  => __( 'Week', 'gamify' ),
				'month' => __( 'Month', 'gamify' ),
				'year'  => __( 'Year', 'gamify' )
			);
		}

		/**
		 * Callback logic for point awarding
		 *
		 * @param $post_id  int     Post ID
		 * @param $post     WP_Post Post data
		 */
		public function post_published( $post_id, $post ) {

			global $wpdb;

			$user_id = $post->post_author;

			/**
			 * Check for user exclusions
			 */
			if ( $this->core->exclude_user( $user_id ) === true ) {
				return;
			}

			/**
			 * Check for post type to satisfy configuration
			 */
			if( 'post' != $post->post_type ) {
				return;
			}

			foreach( $this->prefs as $row => $pref ) {

				/**
				 * Make sure we award points other then zero
				 */
				if ( ! isset( $pref['creds'] ) || empty( $pref['creds'] ) || ( $pref['creds'] == 0 ) ) {
					continue;
				}

				/**
				 * Make sure configured count is other then zero
				 */
				if ( ! isset( $pref['count'] ) || empty( $pref['count'] ) || ( $pref['count'] == 0 ) ) {
					continue;
				}

				switch ( $pref['interval'] ) {
					case 'day':
						$timestamp = strtotime( 'today midnight' );
						break;
					case 'week':
						$timestamp = strtotime( 'this week midnight' );
						break;
					case 'month':
						$timestamp = strtotime( 'first day of this month midnight' );
						break;
					case 'year':
						$timestamp = strtotime( 'first day of January' );
						break;
					default:
						$timestamp = false;
				}

				/**
				 * Make sure timestamp configuration is valid
				 */
				if( ! $timestamp ) {
					continue;
				}
				$timestamp += get_option( 'gmt_offset' ) * HOUR_IN_SECONDS;

				$data = array( 'interval' => $pref['interval'], 'timestamp' => $timestamp );

				/**
				 * Make sure this is unique
				 */
				if( $this->core->has_entry( self::REFERENCE, NULL, $user_id, $data, $this->mycred_type ) ) {
					continue;
				}

				/**
				 * Let's get possible published posts count for this user in configured interval
				 */
				$query = $wpdb->prepare(
					"SELECT COUNT(`ID`) 
							FROM `{$wpdb->posts}` 
							WHERE 
								`post_status` = 'publish' 
								AND `post_type` = 'post'
								AND `post_author` = %d
								AND `post_date` > %s",
					$user_id,
					date( 'Y-m-d H:i:s', $timestamp )
				);
				$total_posts_in_interval = $wpdb->get_var( $query );

				/**
				 * Make sure user reached configured count
				 */
				if( $total_posts_in_interval < $pref['count'] ) {
					continue;
				}

				$intervals = $this->get_interval_choices();
				$interval = $intervals[ $pref['interval'] ];

				/**
				 * Prepare log entry
				 */
				$entry = strtr( $pref['log'], array(
					'%count%'       => $pref['count'],
					'%interval%'    => mb_convert_case( $interval, MB_CASE_LOWER )
				) );

				/**
				 * Safe to award creds
				 */
				$this->core->add_creds(
					self::REFERENCE,
					$user_id,
					$pref['creds'],
					$entry,
					NULL,
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
		GFY_Core_Hook_Publishing_Bonuses::REFERENCE,
		__( 'Points for publishing in time intervals | Gamify', 'gamify' ),
		__( 'Conditional hook to award %_plural% for publishing posts in specific time intervals.', 'gamify' ),
		array( 'GFY_Core_Hook_Publishing_Bonuses' )
	);

}