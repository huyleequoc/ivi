<?php
/**
 * Register a Boombox_Post_Reactions_Metabox using a class.
 *
 * @package Boombox_Theme_Extensions
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}


if ( ! class_exists( 'Boombox_Post_Reactions_Metabox' ) ) {

	class Boombox_Post_Reactions_Metabox {
		
		/**
		 * Holds single instance
		 * @var Boombox_Post_Reactions_Metabox
		 */
		private static $_instance;
		
		/**
		 * Constructor.
		 */
		public function __construct() {
			add_action( 'load-post.php', array( $this, 'init_metabox' ) );
			add_action( 'load-post-new.php', array( $this, 'init_metabox' ) );
		}

		/**
		 * Singleton.
		 */
		static function get_instance() {
			if ( null === static::$_instance ) {
				static::$_instance = new static();
			}
			return static::$_instance;
		}

		/**
		 * Meta box initialization.
		 */
		public function init_metabox() {
			add_action( 'add_meta_boxes', array( $this, 'add_metabox' ), 1 );
			add_action( 'admin_print_styles-post.php', array( $this, 'post_page_admin_enqueue_scripts' ) );
			add_action( 'admin_print_styles-post-new.php', array( $this, 'post_page_admin_enqueue_scripts' ) );
		}

		/**
		 * Adds the meta box.
		 */
		public function add_metabox() {
			/**
			 * Add Reaction Metabox to Post Screen
			 */
			add_meta_box(
				'boombox-reactions-metabox',
				__( 'Reactions', 'boombox-theme-extensions' ),
				array( $this, 'render_reaction_metabox' ),
				'post',
				'normal',
				'default'
			);
		}

		/**
		 * Enqueue Scripts and Styles
		 */
		public function post_page_admin_enqueue_scripts() {
			global $current_screen;
			if ( isset( $current_screen ) && 'post' === $current_screen->id ) {
				wp_enqueue_style( 'boombox-reaction-meta-styles', BBTE_REACTIONS_URL . 'css/boombox-reaction-meta-styles.css' );
			}
		}

		/**
		 * Render Reaction metabox
		 *
		 * @param $post
		 */
		public function render_reaction_metabox( $post ){
			if( function_exists( 'boombox_get_reaction_taxonomy_name' ) ){
				$reactions = array();
				$reactions_ids = Boombox_Reaction_Helper::get_post_reactions( $post->ID );
				if( !empty( $reactions_ids ) ){
					$taxonomy = boombox_get_reaction_taxonomy_name();
					foreach( $reactions_ids as $reaction_id ){
						$reaction = get_term_by( 'term_id', $reaction_id, $taxonomy );
						if( $reaction ){
							$reactions[] = $reaction;
						}
					}
				}

				if( !empty($reactions) && is_array( $reactions ) ) {
					?>
					<div class="badge-list">
						<?php foreach ( $reactions as $reaction ){
							$reaction_icon_url = boombox_get_reaction_icon_url( $reaction->term_id );
							$image             = !empty( $reaction_icon_url ) ? ' <img src="' . esc_url( $reaction_icon_url ) . '" alt="' . $reaction->name . '">' : ''; ?>
							<a class="badge <?php echo $reaction->taxonomy; ?>" href="<?php echo esc_url( get_term_link( $reaction->term_id ) ); ?>" title="<?php echo $reaction->name; ?>">
								<span class="circle"><?php echo $image; ?></span>
								<span class="text"><?php echo $reaction->name; ?></span>
							</a>
						<?php } ?>
					</div>
				<?php
				}else{
					esc_html_e( 'This post has no reaction.' , 'boombox-theme-extensions' );
				}
			}
		}
	}
}

Boombox_Post_Reactions_Metabox::get_instance();