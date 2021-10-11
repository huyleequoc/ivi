<?php
/**
 * Register an attachment meta box using a class.
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
    die( 'No direct script access allowed' );
}


if ( ! class_exists( 'Boombox_Custom_Attachment_Meta_Box' ) ) {

	class Boombox_Custom_Attachment_Meta_Box {

		private $post = null;
		private $postmeta = array();

		/**
		 * Constructor.
		 */
		public function __construct() {
			add_action( 'load-post.php', array( $this, 'init_metabox' ) );
		}

		/**
		 * Singleton.
		 */
		static function get_instance() {
			static $Inst = null;
			if ( $Inst == null ) {
				$Inst = new self();
			}

			return $Inst;
		}

		public function init_metabox() {

			add_action( 'add_meta_boxes', array( $this, 'add_metabox' ), 1, 2 );
			add_action( 'admin_print_styles-post.php', array( $this, 'post_attachment_admin_enqueue_scripts' ) );

		}

		/**
		 * Enqueue Scripts and Styles
		 */
		public function post_attachment_admin_enqueue_scripts() {
			global $current_screen;
			if ( isset( $current_screen ) && 'attachment' === $current_screen->id ) {
				$min = boombox_get_minified_asset_suffix();
				wp_enqueue_style(
					'boombox-admin-meta-style',
					BOOMBOX_ADMIN_URL . 'metaboxes/assets/css/boombox-metabox-style' . $min . '.css',
					array(),
					boombox_get_assets_version()
				);
				wp_enqueue_script(
					'boombox-admin-meta-script',
					BOOMBOX_ADMIN_URL . 'metaboxes/assets/js/boombox-metabox-script' . $min . '.js',
					array( 'jquery' ),
					boombox_get_assets_version(),
					true
				);
			}
		}

		/**
		 * Add the meta box.
		 */
		public function add_metabox( $post_type, $post ) {

			if( "attachment" == $post_type && "image/gif" == $post->post_mime_type ) {

				$this->post = $post;
				$this->postmeta = get_post_meta($post->ID);
				/**
				 * Add Advanced Fields to Page screen
				 */
				add_meta_box(
					'boombox-attachment-metabox',
					__('Boombox Attachment Advanced Fields', 'boombox'),
					array($this, 'render_metabox'),
					'attachment',
					'normal',
					'high'
				);
			}
		}

		/**
		 * Render the advances fields meta box.
		 *
		 * @param $post
		 */
		public function render_metabox( $post ) {

			$mp4_url = isset( $this->postmeta['mp4_url'][0] ) ? $this->postmeta['mp4_url'][0] : '';
			$jpg_url = isset( $this->postmeta['jpg_url'][0] ) ? $this->postmeta['jpg_url'][0] : '';

			?>
			<div class="boombox-advanced-fields">

				<?php // Video URL ( Format: mp4 ) ?>
				<div class="boombox-form-row">
					<label for="boombox_video_url"><?php esc_html_e( 'Video URL ( mp4 )', 'boombox' ); ?></label>
					<input type="text" id="boombox_video_url" readonly value="<?php echo esc_html( $mp4_url ); ?>"/>
				</div>

				<?php // Image URL ( Format: jpg ) ?>
				<div class="boombox-form-row">
					<label for="boombox_video_url"><?php esc_html_e( 'Image URL ( jpg )', 'boombox' ); ?></label>
					<input type="text" id="boombox_video_url" readonly value="<?php echo esc_html( $jpg_url ); ?>"/>
				</div>

			</div>
			<?php
		}

	}
}
//Boombox_Custom_Attachment_Meta_Box::get_instance();

/* For adding custom field to gallery popup */
function boombox_attachment_add_custom_fields($form_fields, $post) {
	// $form_fields is a an array of fields to include in the attachment form
	// $post is nothing but attachment record in the database
	//     $post->post_type == 'attachment'
	// attachments are considered as posts in WordPress. So value of post_type in wp_posts table will be attachment
	// now add our custom field to the $form_fields array
	// input type="text" name/id="attachments[$attachment->ID][custom1]"
	$form_fields[ 'boombox_post_regular_price' ] = array(
		'label' => __( 'Regular Price', 'boombox' ),
		'input' => 'text',
		'value' => boombox_get_post_meta( $post->ID, 'boombox_post_regular_price' )
	);
	$form_fields[ 'boombox_post_discount_price' ] = array(
		'label' => __( 'Discount Price', 'boombox' ),
		'input' => 'text',
		'value' => boombox_get_post_meta( $post->ID, 'boombox_post_discount_price' )
	);
	$form_fields[ 'boombox_post_affiliate_link' ] = array(
		'label' => __( 'Affiliate Link', 'boombox' ),
		'input' => 'text',
		'value' => boombox_get_post_meta( $post->ID, 'boombox_post_affiliate_link' )
	);

	return $form_fields;
}
add_filter( 'attachment_fields_to_edit', 'boombox_attachment_add_custom_fields', null, 2);

function boombox_attachment_save_custom_fields($post, $attachment) {
	// $attachment part of the form $_POST ($_POST[attachments][postID])
	// $post['post_type'] == 'attachment'
	if( isset( $attachment[ 'boombox_post_regular_price' ] ) ) {
		update_post_meta( $post['ID'], 'boombox_post_regular_price', $attachment['boombox_post_regular_price'] );
	}

	if( isset( $attachment[ 'boombox_post_discount_price' ] ) ) {
		update_post_meta( $post['ID'], 'boombox_post_discount_price', $attachment['boombox_post_discount_price'] );
	}

	if( isset( $attachment[ 'boombox_post_affiliate_link' ] ) ) {
		update_post_meta( $post['ID'], 'boombox_post_affiliate_link', $attachment['boombox_post_affiliate_link'] );
	}

	return $post;
}
add_filter( 'attachment_fields_to_save', 'boombox_attachment_save_custom_fields', null , 2);