<?php
/**
 * Facebook Comments plugin functions
 *
 * @package BoomBox_Theme
 */

if( ! boombox_plugin_management_service()->is_plugin_active( 'facebook-comments-plugin/facebook-comments.php' ) ) {
	return;
}

if ( ! class_exists( 'Boombox_Facebook_Comments' ) ) {

	final class Boombox_Facebook_Comments {

		/**
		 * Holds class single instance
		 * @var null
		 */
		private static $_instance = null;

		/**
		 * Get instance
		 * @return Boombox_Facebook_Comments|null
		 */
		public static function get_instance() {

			if ( null == static::$_instance ) {
				static::$_instance = new self();
			}

			return static::$_instance;

		}

		/**
		 * Boombox_Facebook_Comments constructor.
		 */
		private function __construct() {
			$this->hooks();

			do_action( 'boombox/fb_comments/wakeup', $this );
		}

		/**
		 * A dummy magic method to prevent Boombox_Facebook_Comments from being cloned.
		 *
		 */
		public function __clone() {
			throw new Exception( 'Cloning ' . __CLASS__ . ' is forbidden' );
		}

		/**
		 * Setup Hooks
		 */
		public function hooks() {
			remove_filter( 'the_content', 'fbcommentbox', 100 );
			//Disable the plugin default FBML setup, to avoid the conflict with Zombify FB embed on Post create/update page
			remove_action( 'wp_footer', 'fbmlsetup', 100 );
			//Re-setup FBML
			add_action( 'wp_footer', array( $this, 'fbml_re_setup' ), 100 );
			add_filter( 'boombox/single_post/sortable_section_choices', array( $this, 'add_to_customizer_sortable' ), 10, 1 );
			add_action( 'boombox/single/sortables/fb_comments', array( $this, 'render_section' ) );
		}

		/**
		 * Add facebook comments to sortable section
		 * @param array $choices Current Choices
		 *
		 * @return array
		 */
		public function add_to_customizer_sortable( $choices ) {
			$choices[ 'fb_comments' ] = __( 'Facebook Comments', 'boombox' );

			return $choices;
		}

		/**
		 * Render facebook comments section
		 */
		public function render_section() {
			echo '<div>' . do_shortcode( '[fbcomments]' ) . '</div>';
		}


		/**
		 * Re-setup FBML on pages besides ZF Post create/update page
		 */
		public function fbml_re_setup() {
			$is_zf_backend_page = false;

			if ( boombox_plugin_management_service()->is_plugin_active( 'zombify/zombify.php' ) ) {
				$post_create_page_id = zf_get_option( "zombify_post_create_page" );
				$is_zf_backend_page  = is_page( $post_create_page_id );
			}

			$options = get_option( 'fbcomments' );

			if ( ! $is_zf_backend_page && isset( $options['fbml'] ) && $options['fbml'] == 'on' ) { ?>

				<!-- Facebook Comments Plugin for WordPress: http://peadig.com/wordpress-plugins/facebook-comments/ -->
				<div id="fb-root"></div>
				<script>
					(function( d, s, id ) {
						var js, fjs = d.getElementsByTagName( s )[0];
						if ( d.getElementById( id ) ) return;
						js = d.createElement( s );
						js.id = id;
						js.src = "//connect.facebook.net/<?php echo $options['language']; ?>/sdk.js#xfbml=1&appId=<?php echo $options['appID']; ?>&version=v2.3";
						fjs.parentNode.insertBefore( js, fjs );
					}( document, 'script', 'facebook-jssdk' ));
				</script>

			<?php }
		}

	}

	Boombox_Facebook_Comments::get_instance();

}