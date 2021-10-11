<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Hook for publishing posts in preconfigured format
 */
if ( ! class_exists( 'GFY_ZF_Hook_Publishing_Firstly_In_Format' )
	&& class_exists( 'myCRED_Hook' )
) {

	class GFY_ZF_Hook_Publishing_Firstly_In_Format extends myCRED_Hook {

		const REFERENCE = 'gfy_zf_hook_publishing_firstly_in_format';

		/**
		 * GFY_ZF_Hook_Publishing_Firstly_In_Format constructor.
		 *
		 * @param array     $hook_prefs <string, array>
		 * @param string    $type       string
		 */
		function __construct( $hook_prefs, $type = MYCRED_DEFAULT_TYPE_KEY ) {

			$defaults = array(
				'creds'   => 1,
				'formats' => array(),
				'log'     => '%plural% for publishing first %format%'
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

			$references[ self::REFERENCE ] = __( 'Publish first time in formats', 'gamify' );

			return $references;

		}

		/**
		 * Render preferences
		 */
		public function preferences() {

			$prefs = $this->prefs;

			$post_types = zombify()->get_active_post_types(); ?>

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
				$field_id       = $this->field_id( 'formats' );
				$field_label    = __( 'Post Formats', 'gamify' );
				$field_name     = $this->field_name( 'formats' );
				?>
				<div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
					<div class="form-group">
						<label for="<?php echo $field_id; ?>"><?php echo $field_label; ?></label>
						<select name="<?php echo $field_name; ?>[]"
						        id="<?php echo $field_id; ?>"
						        class="form-control"
						        multiple="multiple"
						        size="10">

							<?php foreach( $post_types as $post_type_key => $post_type_data ) {
								$post_type_slug = $post_type_data['post_type_level'] == 1 ? $post_type_data['post_type_slug'] : 'subtype_' . $post_type_data['post_type_slug'];
								$selected = in_array( $post_type_slug, (array)$this->prefs['formats'] ) ? ' selected="selected" ' : ''; ?>
								<option <?php echo $selected; ?> value="<?php echo $post_type_slug; ?>">
									<?php echo $post_type_data['name']; ?>
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
				'%format%'       => __( 'selected format', 'gamify' )
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

			/**
			 * Make sure we award points other then zero
			 */
			if ( ! isset( $this->prefs['creds'] ) || empty( $this->prefs['creds'] ) || ( $this->prefs['creds'] == 0 ) ) {
				return;
			}

			/**
			 * Get formats where user previously published if any
			 */
			$user_published_formats = get_user_meta( $user_id, 'gfy_user_published_formats', true );
			$user_published_formats = (bool)$user_published_formats ? $user_published_formats : array();

			/**
			 * Check post zf data type to satisfy selected criteria
			 */
			$zf_data_type = get_post_meta( $post_id, 'zombify_data_type', true );
			$is_sub_type = false;
			if( $zf_data_type == 'story' ) {
				$zf_data_subtype = get_post_meta( $post_id, 'zombify_data_subtype', true );
				if( $zf_data_subtype != 'main' ) {
					$zf_data_type = $zf_data_subtype;
					$is_sub_type = true;
				}
			}
			if( ! $zf_data_type ) {
				return;
			}

			/**
			 * Make sure that post format is in configured formats
			 */
			if(
				! in_array( ( $is_sub_type ? ( 'subtype_' . $zf_data_type ) : $zf_data_type ), (array)$this->prefs['formats'] )
				|| in_array( ( $is_sub_type ? ( 'subtype_' . $zf_data_type ) : $zf_data_type ), $user_published_formats )
			) {
				return;
			}

			$data = array( 'formats' => array( ( $is_sub_type ? ( 'subtype_' . $zf_data_type ) : $zf_data_type ) ) );
			$post_types = zombify()->get_active_post_types();
			$entry = strtr( $this->prefs['log'], array( '%format%' => $post_types[ $zf_data_type ][ 'name' ] ) );

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

			/**
			 * Update user metadata for future usage
			 */
			if( $added ) {
				$user_published_formats[] = ( $is_sub_type ? ( 'subtype_' . $zf_data_type ) : $zf_data_type );
				update_user_meta( $user_id, 'gfy_user_published_formats', $user_published_formats );
			}

		}

	}

	/**
	 * Register hook
	 */
	GFY_myCRED_Hook_Management_Service::get_instance()->register_hook(
		GFY_ZF_Hook_Publishing_Firstly_In_Format::REFERENCE,
		__( 'Points for publishing 1st post in formats | Gamify', 'gamify' ),
		__( 'Award %_plural% to authors for publishing their 1st post with "Zombify" plugin in one of chosen formats.', 'gamify' ),
		array( 'GFY_ZF_Hook_Publishing_Firstly_In_Format' )
	);

}