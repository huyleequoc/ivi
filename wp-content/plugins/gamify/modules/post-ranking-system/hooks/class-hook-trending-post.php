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
if ( ! class_exists( 'GFY_PRS_Hook_Trending_Post' ) && class_exists( 'myCRED_Hook' ) ) {

	class GFY_PRS_Hook_Trending_Post extends myCRED_Hook {

		const REFERENCE = 'gfy_prs_trending_post';

		private $trending_disable = true;
		private $hot_disable = true;
		private $popular_disable = true;

		/**
		 * GFY_PRS_Hook_Trending_Post constructor.
		 *
		 * @param array     $hook_prefs <string, array>
		 * @param string    $type       string
		 */
		function __construct( $hook_prefs, $type = MYCRED_DEFAULT_TYPE_KEY ) {

			if( function_exists( 'boombox_get_theme_option' ) ) {
				$this->trending_disable = ! boombox_get_theme_option( 'extras_post_ranking_system_trending_enable' );
				$this->hot_disable = ! boombox_get_theme_option( 'extras_post_ranking_system_hot_enable' );
				$this->popular_disable = ! boombox_get_theme_option( 'extras_post_ranking_system_popular_enable' );
			}

			$defaults = array(
				'trending' => array(
					'creds'  => 1,
					'log'    => '%plural% for trending post'
				),
				'hot' => array(
					'creds'  => 1,
					'log'    => '%plural% for hot post'
				),
				'popular' => array(
					'creds'  => 1,
					'log'    => '%plural% for popular post'
				)
			);

			if ( isset( $hook_prefs[ self::REFERENCE ] ) ) {
				$defaults = $hook_prefs[ self::REFERENCE ];
			}

			parent::__construct( array(
				'id'       => self::REFERENCE,
				'defaults' => $this->check_conditions_activity( $defaults )
			), $hook_prefs, $type );

		}

		/**
		 * Start hook
		 */
		public function run() {
			add_filter( 'boombox_rate_jobs_register', array( $this, 'setup_trending_actions' ), 1, 2 );
			add_filter( 'mycred_all_references',  array( $this, 'add_references' ) );
		}

		/**
		 * Setup actions ( late init )
		 */
		public function setup_trending_actions( $jobs ) {
			foreach( $this->check_conditions_activity( $this->prefs ) as $trending_type => $pref ) {
				if( isset( $jobs[ $trending_type ] ) ) {
					$hash = Boombox_Rate_Cron::get_hash( $jobs[ $trending_type ] );
					add_filter( 'boombox_rated_items_' . $hash, array( $this, 'rated_' . $trending_type . '_posts' ), 10, 1 );
				}
			}
		}

		/**
		 * Register custom references
		 * @param $references   array<string,string>    Registered references
		 *
		 * @return array        array<string,string>
		 */
		public function add_references( $references ) {

			foreach( $this->check_conditions_activity( $this->prefs ) as $trending_type => $pref ) {
				$references[ $this->get_reference_id( $trending_type ) ] = $this->get_reference_label( $trending_type );
			}

			return $references;

		}

		/**
		 * Render preferences
		 */
		public function preferences() {
			$prefs = $this->check_conditions_activity( $this->prefs );

			if( empty( $prefs ) ) {
				printf( '<p>%s</p>', __( 'There are no active trending conditions', 'gamify' ) );
				return;
			}

			foreach( $prefs as $trending_type => $pref ) { ?>

				<div class="hook-instance">
					<h3><?php echo $this->get_trending_label( $trending_type ); ?></h3>
					<div class="row">

						<?php
						$field_id           = $this->field_id( array( $trending_type => 'creds' ) );
						$field_label        = $this->core->plural();
						$field_name         = $this->field_name( array( $trending_type => 'creds' ) );
						$field_value        = $this->core->number( $pref['creds'] );
						$field_description  = $this->get_trending_description( $trending_type );
						?>
						<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
							<div class="form-group">
								<label for="<?php echo $field_id; ?>"><?php echo $field_label; ?></label>
								<input type="number"
								       min="1"
								       name="<?php echo $field_name; ?>"
								       id="<?php echo $field_id; ?>"
								       value="<?php echo $field_value; ?>"
								       class="form-control"/>
								<span class="description"><?php echo $field_description; ?></span>
							</div>
						</div>

						<?php
						$field_id = $this->field_id( array( $trending_type => 'log' ) );
						$field_label = __('Log template', 'gamify');
						$field_name = $this->field_name( array( $trending_type => 'log' ) );
						$field_value = esc_attr( $pref['log'] );
						$field_description = $this->available_template_tags(array('general', 'post'));
						?>
						<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
							<div class="form-group">
								<label for="<?php echo $field_id; ?>"><?php echo $field_label; ?></label>
								<input type="text"
								       name="<?php echo $field_name; ?>"
								       id="<?php echo $field_id; ?>"
								       value="<?php echo $field_value; ?>"
								       class="form-control"/>
								<span class="description"><?php echo $field_description; ?></span>
							</div>
						</div>

					</div>
				</div>
				<?php
			}
		}

		/**
		 * Get dynamic reference ID for specific trending type
		 * @param string $trending_type Trending type
		 * @return string
		 */
		private function get_reference_id( $trending_type ) {
			return sprintf( '%s_%s_post', self::REFERENCE, $trending_type );
		}

		/**
		 * Get dynamic reference label for specific trending type
		 * @param string $trending_type Trending type
		 * @return string
		 */
		private function get_reference_label( $trending_type ) {
			return sprintf( __( 'Have %s post', 'gamify' ), $trending_type );
		}

		/**
		 * Get trending label for specific trending type
		 *
		 * @param string $trending_type Trending type
		 * @return string
		 */
		private function get_trending_label( $trending_type ) {
			switch( $trending_type ) {
				case 'trending':
					$label = __( 'Trending post (top in last 24 hours)', 'gamify' );
					break;
				case 'hot':
					$label = __( 'Hot post (top in last 7 days)', 'gamify' );
					break;
				case 'popular':
					$label = __( 'Popular post (top in last 30 days)', 'gamify' );
					break;
				default:
					$label = '';
			}

			return $label;
		}

		/**
		 * Get trending field description for specific trending type
		 *
		 * @param string $trending_type Trending type
		 * @return string
		 */
		private function get_trending_description( $trending_type ) {
			switch( $trending_type ) {
				case 'trending':
					$description = __( 'Notice: points will be awarded every 24 hours, when new list is generated.', 'gamify' );
					break;
				case 'hot':
					$description = __( 'Notice: points will be awarded every 24 hours, when new list is generated.', 'gamify' );
					break;
				case 'popular':
					$description = __( 'Notice: points will be awarded every 24 hours, when new list is generated.', 'gamify' );
					break;
				default:
					$description = '';
			}

			return $description;
		}

		/**
		 * Check trending conditions activity
		 *
		 * @param array $prefs Preferences configuration
		 * @return array
		 */
		private function check_conditions_activity( $prefs ) {
			if( $this->trending_disable && isset( $prefs[ 'trending' ] ) ) {
				unset( $prefs['trending'] );
			}
			if( $this->hot_disable && isset( $prefs[ 'hot' ] ) ) {
				unset( $prefs['hot'] );
			}
			if( $this->popular_disable && isset( $prefs[ 'popular' ] ) ) {
				unset( $prefs['popular'] );
			}

			return $prefs;
		}

		/**
		 * Callback logic for trending point awarding
		 * @param array $posts Trending posts
		 * @return array
		 */
		public function rated_trending_posts( array $posts ) {
			$this->rated_posts( $posts, 'trending' );

			return $posts;


		}

		/**
		 * Callback logic for hot point awarding
		 * @param array $posts Trending posts
		 * @return array
		 */
		public function rated_hot_posts( array $posts ) {
			$this->rated_posts( $posts, 'hot' );

			return $posts;
		}

		/**
		 * Callback logic for trending point awarding
		 * @param array $posts Trending posts
		 * @return array
		 */
		public function rated_popular_posts( array $posts ) {
			$this->rated_posts( $posts, 'popular' );

			return $posts;
		}

		/**
		 * Callback logic for point awarding
		 * @param array $posts Trending posts
		 * @param string $type Trending type
		 */
		private function rated_posts( array $posts, $type ) {


			$pref = isset( $this->prefs[ $type ] ) ? $this->prefs[ $type ] : false;
			if( ! $pref ) {
				return;
			}

			foreach( $posts as $rated_data ) {

				$article = get_post( $rated_data[ 'post_id' ] );

				if( ! $article ) {
					continue;
				}

				$user_id = $article->post_author;

				// Check for exclusions
				if ( $this->core->exclude_user( $user_id ) === true ) {
					continue;
				}

				// Make sure we award points other then zero
				if ( ! isset( $pref['creds'] ) || empty( $pref['creds'] ) || $pref['creds'] == 0 ) {
					continue;
				}

				$entry = $pref['log'];
				$data = NULL;
				$reference = $this->get_reference_id( $type );

				// Add Creds
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

	}

	/**
	 * Register hook
	 */
	GFY_myCRED_Hook_Management_Service::get_instance()->register_hook(
		GFY_PRS_Hook_Trending_Post::REFERENCE,
		__( 'Points for trending post | Gamify', 'gamify' ),
		__( 'Award %_plural% to authors for trending post (once a day).', 'gamify' ),
		array( 'GFY_PRS_Hook_Trending_Post' )
	);
}