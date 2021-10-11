<?php
/**
 * Plugin for converting GIF images to Video
 *
 * @package BoomBox_Theme_Extensions
 *
 */

use CloudConvert\Api;
use GuzzleHttp\Client as Client;

class Boombox_Gif_To_Video {

	private $_api;

	/**
	 * Init
	 */
	private function init() {
		add_action( 'admin_notices', array( $this, 'show_notices' ) );

		add_action( 'add_attachment', array( $this, 'add_attachment' ) );
		add_action( 'edit_attachment', array( $this, 'edit_attachment' ) );

		add_filter( 'post_thumbnail_html', array( $this, 'filter_gif_thumbnail_html' ), 100, 5 );
		add_filter( 'image_send_to_editor', array( $this, 'send_shortcode_to_editor' ), 10, 8 );

		add_shortcode( 'boombox_gif_video', array( $this, 'boombox_gif_video_shortcode' ) );
	}

	/**
	 * Create Client
	 * @return bool
	 */
	private function create_client() {

		if ( $this->_api ) {
			return true;
		}

		if ( $api_key = boombox_get_theme_option( 'extras_gif_control_cloudconvert_app_key' ) ) {

			$client = new Client( array( 'defaults' => array( 'verify' => false ) ) );

			$this->_api = new Api( $api_key, $client );

			return true;
		}

		return false;
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

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Callback to handle attachment adding
	 *
	 * @param $postID
	 */
	public function add_attachment( $postID ) {
		$this->process( $postID, 'new' );
	}

	/**
	 * Callback to handle attachment editing
	 *
	 * @param $postID
	 */
	public function edit_attachment( $postID ) {
		$this->process( $postID, 'edit' );
	}

	/**
	 * Process attachment converting
	 *
	 * @param $postID
	 * @param $action
	 */
	private function process( $postID, $action ) {

		$attachment = get_post( $postID );

		/**
		 * Converting options
		 */
		switch ( $attachment->post_mime_type ) {
			case 'image/gif':
				$input_format = 'gif';
				$output_format = 'mp4';

				break;
			default:
				$input_format = false;
				$output_format = false;
		}

		if ( ! $input_format || ! $output_format ) {
			return;
		}

		$unique_id = uniqid();

		// hook to provide ability to skip converting
		$allow_converting = apply_filters( 'bbte/cloudconvert_allow_processing', true, $attachment, $action, $input_format, $output_format, $unique_id );
		if ( ! $allow_converting ) {
			return;
		}

		/**
		 * Converting from gif
		 */
		if ( $attachment->post_mime_type == 'image/gif' ) {

			// in any case we create a jpg to gif
			$this->create_image( $attachment, $input_format, $postID, $action, $unique_id );

			if ( $this->create_client() ) {

				$response = $this->convert( $attachment, $postID, $action, $input_format, $output_format, $unique_id );

				if ( $response[ 'success' ] ) {
					if ( isset( $response[ 'result' ][ 'id' ] ) ) {
						update_post_meta( $postID, sprintf( '%s_id', $output_format ), $response[ 'result' ][ 'id' ] );
					}

					update_post_meta( $postID, sprintf( '%s_url', $output_format ), $response[ 'result' ][ 'url' ] );
				} else {
					$this->add_notice( 'error', $response[ 'message' ] );
				}
			}
		}
	}

	/**
	 * Add admin notice
	 *
	 * @param $type
	 * @param $message
	 */
	private function add_notice( $type, $message ) {
	    $notices = $this->get_notices();
        $notices[] = array(
			'type'    => $type,
			'message' => $message,
		);
        update_option( 'bbte_gtv_notices', $notices );
	}

	/**
	 * Get existing notices
	 *
	 * @return array
	 */
	private function get_notices() {
	    return (array) get_option( 'bbte_gtv_notices', array() );
	}

	/**
	 * Clear admin notices
	 */
	private function clear_notices() {
        update_option( 'bbte_gtv_notices', array() );
	}

	/**
	 * Show admin notices
	 */
	public function show_notices() {
		if ( get_current_screen()->parent_base == 'upload' ) {
			foreach ( $this->get_notices() as $notice ) {
				echo sprintf( '<div class="notice notice-%1$s is-dismissible"><p>%2$s</p></div>', $notice[ 'type' ], $notice[ 'message' ] );
			}
		}
		$this->clear_notices();
	}

	/**
	 * Create a JPG for current format
	 *
	 * @param $input_format
	 * @param $post_id
	 * @param $action
	 */
	private function create_image( $attachment, $input_format, $post_id, $action, $unique_id ) {

		$output_format = 'jpg';
		$output_mime = 'image/jpeg';

		$input_location = get_attached_file( $attachment->ID );
		$file_name = $unique_id . '_' . trim( basename( $input_location, $input_format ), '.' );

		$wp_upload_dir = wp_upload_dir();
		$output_location = sprintf( '%1$s/%2$s.%3$s', $wp_upload_dir[ 'path' ], $file_name, $output_format );

		$image = wp_get_image_editor( $input_location );
		$result = $image->save( $output_location, $output_mime );

		if ( is_wp_error( $result ) ) {
			$this->add_notice( 'error', sprintf( esc_html__( 'Error converting to %s', 'boombox-theme-extensions' ), " {$input_format}" ) );
		} else {
			$attachment_id = $this->insert_attachment( $result[ 'path' ] );

			// Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
			require_once( ABSPATH . 'wp-admin/includes/image.php' );

			// Generate the metadata for the attachment, and update the database record.
			$attach_data = wp_generate_attachment_metadata( $attachment_id, $result[ 'path' ] );
			wp_update_attachment_metadata( $attachment_id, $attach_data );

			if ( $attachment_id ) {

				// remove old attachment from media library
				if ( "edit" == $action ) {
					$this->remove_old( $post_id, $output_format );
				}

				update_post_meta( $post_id, sprintf( '%s_id', $output_format ), $attachment_id );
				update_post_meta( $post_id, sprintf( '%s_url', $output_format ), wp_get_attachment_url( $attachment_id ) );

			}
		}

	}

	/**
	 * Convert a single attachment from one format to another
	 *
	 * @param $post_id
	 * @param $action
	 * @param $input_format
	 * @param $output_format
	 *
	 * @return array
	 */
	private function convert( $attachment, $post_id, $action, $input_format, $output_format, $unique_id ) {

		$success = false;
		$message = '';
		$result = array();

		$input_location = get_attached_file( $attachment->ID );
		$file_name = $unique_id . '_' . trim( basename( $input_location, $input_format ), '.' );

		$wp_upload_dir = wp_upload_dir();
		$output_location = sprintf( '%1$s/%2$s.%3$s', $wp_upload_dir[ 'path' ], $file_name, $output_format );

		$convert_args = array(
			'inputformat'  => $input_format,
			'outputformat' => $output_format,
			'input'        => 'upload',
			'file'         => fopen( $input_location, 'r' ),
			'filename'     => $file_name . '.' . $input_format,
			'wait'         => true,
		);

		$storage = boombox_get_theme_option( 'extras_gif_control_storage' );
		if ( $storage === 'local' ) {
			try {

				$process = $this->_api->convert( $convert_args )->download( $output_location );

				$success = true;
			} catch ( Exception $e ) {
				$message = 'CloudConvert: ' . $e->getMessage();
			}

			if ( $success ) {
				$attachment_id = $this->insert_attachment( $output_location );
				if ( $attachment_id ) {

					// remove old attachment from media library
					if ( "edit" == $action ) {
						$this->remove_old( $post_id, $output_format );
					}

					$result = array(
						'id'  => $attachment_id,
						'url' => wp_get_attachment_url( $attachment_id ),
					);
				} else {
					$message = esc_html__( 'Error inserting attachment', 'boombox-theme-extensions' );
				}
			}

		} else if ( $storage == 'aws_s3' ) {   // Amazon S3

			$aws_accesskeyid = boombox_get_theme_option( 'extras_gif_control_aws_s3_access_key_id' );
			$aws_secretaccesskey = boombox_get_theme_option( 'extras_gif_control_aws_s3_secret_access_key' );
			$aws_bucket = boombox_get_theme_option( 'extras_gif_control_aws_s3_bucket_name' );

			$is_aws_valid = true;
			if ( ! ( $aws_accesskeyid && $aws_secretaccesskey && $is_aws_valid ) ) {
				$message = 'Amazon S3:' . esc_html__( 'Invalid Configuration', 'boombox-theme-extensions' );
			} else {

				$convert_args = array_merge(
					$convert_args,
					array(
						'output' => array(
							"s3" => array(
								'acl'             => 'public-read',
								'accesskeyid'     => $aws_accesskeyid,
								'secretaccesskey' => $aws_secretaccesskey,
								'bucket'          => $aws_bucket,
							),
						),
					)
				);

				try {
					$process = $this->_api->convert( $convert_args );

					$success = true;
				} catch ( Exception $e ) {
					$message = 'CloudConvert: ' . $e->getMessage();
				}

				if ( $success ) {
					$result = array(
						'url' => sprintf( 'https://s3.amazonaws.com/%1$s/%2$s.%3$s', $aws_bucket, $file_name, $output_format ),
					);
				}

			}
		} else {
			$message = esc_html__( 'Invalid storage', 'boombox-theme-extensions' );
		}

		return array( 'success' => $success, 'message' => $message, 'result' => $result );

	}

	/**
	 * Insert converted files as wp attachment
	 *
	 * @param $path
	 *
	 * @return int
	 */
	private function insert_attachment( $path ) {

		$filetype = wp_check_filetype( basename( $path ), null );
		$wp_upload_dir = wp_upload_dir();

		$attachment = array(
			'guid'           => $wp_upload_dir[ 'url' ] . '/' . basename( $path ),
			'post_mime_type' => $filetype[ 'type' ],
			'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $path ) ),
			'post_content'   => '',
			'post_status'    => 'inherit',
		);

		return wp_insert_attachment( $attachment, $path );

	}

	/**
	 * Delete old attachment
	 *
	 * @param $post_id
	 * @param $output_format
	 */
	private function remove_old( $post_id, $output_format ) {
		$attachment_id = boombox_get_post_meta( $post_id, sprintf( '%s_id', $output_format ) );
		if ( $attachment_id ) {
			wp_delete_attachment( $attachment_id, true );
		}
	}

	/**
	 * Callback to modify post thumbnail HTML
	 *
	 * @param $html
	 * @param $post_id
	 * @param $post_thumbnail_id
	 * @param $size
	 * @param $attr
	 *
	 * @return string
	 */
	public function filter_gif_thumbnail_html( $html, $post_id, $post_thumbnail_id, $size, $attr ) {

		if ( 'image/gif' == get_post_mime_type( $post_thumbnail_id ) ) {

			$default_atts = array(
				'play' => false,
			);

			$attr = wp_parse_args( $attr, $default_atts );
			if ( $attr[ 'play' ] ) {
				$attr[ 'alt' ] = boombox_get_post_meta( $post_thumbnail_id, '_wp_attachment_image_alt' );
				$media_data = $this->get_media_data( $post_thumbnail_id );

				if ( $size ) {
					$get_full = ( $size == 'full' );
					if ( isset( $media_data[ 'attachment_metadata' ][ 'sizes' ][ $size ] ) ) {
						if ( isset( $media_data[ 'attachment_metadata' ][ 'sizes' ][ $size ][ 'width' ] ) && $media_data[ 'attachment_metadata' ][ 'sizes' ][ $size ][ 'width' ] ) {
							$attr[ 'width' ] = $media_data[ 'attachment_metadata' ][ 'sizes' ][ $size ][ 'width' ];
						}
						if ( isset( $media_data[ 'attachment_metadata' ][ 'sizes' ][ $size ][ 'height' ] ) && $media_data[ 'attachment_metadata' ][ 'sizes' ][ $size ][ 'height' ] ) {
							$attr[ 'height' ] = $media_data[ 'attachment_metadata' ][ 'sizes' ][ $size ][ 'height' ];
						}
					} else {
						$get_full = true;
					}

					if ( $get_full ) {
						if ( isset( $media_data[ 'attachment_metadata' ][ 'width' ] ) && $media_data[ 'attachment_metadata' ][ 'width' ] ) {
							$attr[ 'width' ] = $media_data[ 'attachment_metadata' ][ 'width' ];
						}
						if ( isset( $media_data[ 'attachment_metadata' ][ 'height' ] ) && $media_data[ 'attachment_metadata' ][ 'height' ] ) {
							$attr[ 'height' ] = $media_data[ 'attachment_metadata' ][ 'height' ];
						}
					}
				}

				$converted_html = $this->get_gif_video_html( $post_id, $media_data[ 'gif' ], $media_data[ 'mp4' ], $media_data[ 'jpg' ], $attr );

				$html = $converted_html ? $converted_html : $html;
			}
		}

		return $html;
	}

	/**
	 * Callback to modify media HTML inserting into editor
	 *
	 * @param $html
	 * @param $id
	 * @param $caption
	 * @param $title
	 * @param $align
	 * @param $url
	 * @param $size
	 * @param $alt
	 *
	 * @return string
	 */
	public function send_shortcode_to_editor( $html, $id, $caption, $title, $align, $url, $size, $alt ) {

		if ( 'image/gif' == get_post_mime_type( $id ) ) {
			$shortcode_html = $this->get_shortcode( $id );
			if ( $shortcode_html ) {
				$html = $shortcode_html;
			}
		}

		return $html;
	}

	/**
	 * Get shortcode for provided gif image
	 *
	 * @param $media_id
	 *
	 * @return string
	 */
	private function get_shortcode( $media_id ) {

		$html = '';

		$media_data = $this->get_media_data( $media_id );

		if ( $media_data[ 'mp4' ] ) {
			$shortcode_atts = array(
				sprintf( 'mp4="%s"', $media_data[ 'mp4' ] ),
			);

			if ( $media_data[ 'gif' ] ) {
				$shortcode_atts[] = sprintf( 'gif="%s"', $media_data[ 'gif' ] );
			}

			if ( $media_data[ 'jpg' ] ) {
				$shortcode_atts[] = sprintf( 'jpg="%s"', $media_data[ 'jpg' ] );
			}

			if ( (bool)$shortcode_atts ) {
				$html = sprintf( '[boombox_gif_video %1$s]', implode( ' ', $shortcode_atts ) );
			}

		}

		return $html;

	}

	/**
	 * Create HTML to replace gif image HTML
	 *
	 * @param string $gif
	 * @param string $mp4
	 * @param string $jpg
	 *
	 * @return string
	 */
	private function get_gif_video_html( $post_id = 0, $gif = '', $mp4 = '', $jpg = '', $atts = array() ) {
		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}
		$is_nsfw = boombox_is_nsfw_post( $post_id );

		return wp_is_mobile() ? $this->mobile_html( $is_nsfw, $gif, $mp4, $jpg, $atts ) : $this->desktop_html( $is_nsfw, $gif, $mp4, $jpg, $atts );

	}

	/**
	 * Generate HTML for desktop devices
	 *
	 * @param string $gif
	 * @param string $mp4
	 * @param string $jpg
	 *
	 * @return string
	 */
	private function desktop_html( $is_nsfw, $gif = '', $mp4 = '', $jpg = '', $atts = array() ) {
		$html = '';

		$sources = array();
		if ( $mp4 ) {
			$sources[] = sprintf( '<source src="%s" type="video/mp4">', $mp4 );
		}

		if ( (bool)$sources ) {
			$sources[] = esc_html__( 'Your browser does not support the video tag.', 'boombox-theme-extensions' );

			$poster = $jpg ? sprintf( 'poster="%s"', $jpg ) : '';

			Boombox_Template::set( 'bb_post_media_type', 'video' );
			if ( isset( $atts[ 'width' ] ) && isset( $atts[ 'height' ] ) ) {
				Boombox_Template::set( 'bb_post_media_w', absint( $atts[ 'width' ] ) );
				Boombox_Template::set( 'bb_post_media_h', absint( $atts[ 'height' ] ) );
			}

			$inner_html = '';
			if ( ! $is_nsfw ) {
				$inner_html = '<div class="gif-video-wrapper"><video class="gif-video" loop muted ' . $poster . '>' . implode( '', $sources ) . '</video></div>';
			}

			$html .= $inner_html;
		} else if ( $gif ) {

			$img_atts = array(
				'src'   => $gif,
				'class' => 'boombox-gif',
				'alt'   => ( isset( $atts[ 'alt' ] ) && $atts[ 'alt' ] ) ? $atts[ 'alt' ] : basename( $gif ),
			);

			if ( isset( $atts[ 'width' ] ) && $atts[ 'width' ] ) {
				$img_atts[ 'width' ] = $atts[ 'width' ];
			}

			if ( isset( $atts[ 'height' ] ) && $atts[ 'height' ] ) {
				$img_atts[ 'height' ] = $atts[ 'height' ];
			}

			if ( isset( $img_atts[ 'width' ] ) && isset( $img_atts[ 'height' ] ) ) {
				Boombox_Template::set( 'bb_post_media_w', absint( $img_atts[ 'width' ] ) );
				Boombox_Template::set( 'bb_post_media_h', absint( $img_atts[ 'height' ] ) );
			}

			foreach ( $img_atts as $a => $v ) {
				$img_atts[ $a ] = $a . '="' . $v . '"';
			}

			$inner_html = '';
			if ( ! $is_nsfw ) {
				$inner_html = '<img ' . implode( ' ', $img_atts ) . '/>';
			}

			$html .= $inner_html;
		}

		return $html;
	}

	/**
	 * Generate HTML for mobile devices
	 *
	 * @param string $gif
	 * @param string $mp4
	 * @param string $jpg
	 *
	 * @return string
	 */
	private function mobile_html( $is_nsfw, $gif = '', $mp4 = '', $jpg = '', $atts = array() ) {

		$before = '';
		$after = '';
		$html = '';
		$attributes = array(
			'src'   => false,
			'class' => 'boombox-gif',
		);

		if ( $mp4 ) {

			$attributes[ 'class' ] .= ' gif-image';
			$attributes[ 'data-video' ] = $mp4;
			$attributes[ 'src' ] = $jpg;

			if ( isset( $atts[ 'width' ] ) && isset( $atts[ 'height' ] ) ) {
				Boombox_Template::set( 'bb_post_media_w', absint( $atts[ 'width' ] ) );
				Boombox_Template::set( 'bb_post_media_h', absint( $atts[ 'height' ] ) );
			}

			$before .= '<div class="gif-video-wrapper">';
			$after .= '</div>';

		} else if ( $gif ) {
			$attributes[ 'src' ] = $gif;

			if ( isset( $atts[ 'width' ] ) && $atts[ 'width' ] ) {
				$attributes[ 'width' ] = $atts[ 'width' ];
			}
			if ( isset( $atts[ 'height' ] ) && $atts[ 'height' ] ) {
				$attributes[ 'height' ] = $atts[ 'height' ];
			}

			if ( isset( $attributes[ 'width' ] ) && isset( $attributes[ 'height' ] ) ) {
				Boombox_Template::set( 'bb_post_media_w', absint( $attributes[ 'width' ] ) );
				Boombox_Template::set( 'bb_post_media_h', absint( $attributes[ 'height' ] ) );
			}
		}

		if ( $attributes[ 'src' ] ) {
			$attributes[ 'alt' ] = ( isset( $atts[ 'alt' ] ) && $atts[ 'alt' ] ) ? $atts[ 'alt' ] : basename( $attributes[ 'src' ] );

			foreach ( $attributes as $a => $v ) {
				$attributes[ $a ] = $a . '="' . $v . '"';
			}

			$inner_html = '';
			if ( ! $is_nsfw ) {
				$inner_html = '<img ' . implode( ' ', $attributes ) . '/>';
			}

			$html = $before . $inner_html . $after;

		}

		return $html;

	}

	/**
	 * Render Shortcode
	 *
	 * @param null   $atts
	 * @param null   $content
	 * @param string $tag
	 *
	 * @return string
	 */
	public function boombox_gif_video_shortcode( $atts = null, $content = null, $tag = '' ) {

		$atts = array_change_key_case( (array)$atts, CASE_LOWER );

		$a = shortcode_atts( array(
			'post_id' => get_the_ID(),
			'mp4' => false, // mp4 URL
			'gif' => false, // gif URL
			'jpg' => false  // jpg URL
		), $atts, $tag );

		return $this->get_gif_video_html( $a[ 'post_id' ], $a[ 'gif' ], $a[ 'mp4' ], $a[ 'jpg' ] );

	}

	/**
	 * Get Media data
	 *
	 * @param $media_id
	 *
	 * @return array
	 */
	private function get_media_data( $media_id ) {
		list( $gif_url, $gif_width, $gif_height, $gif_is_intermediate ) = wp_get_attachment_image_src( $media_id, 'full' );
		$post_metas = boombox_get_post_meta( $media_id );

		$attachment_metadata = array();
		if ( isset( $post_metas[ '_wp_attachment_metadata' ] ) ) {
			$attachment_metadata = maybe_unserialize( $post_metas[ '_wp_attachment_metadata' ] );
		}

		$mp4_url = isset( $post_metas[ 'mp4_url' ] ) ? $post_metas[ 'mp4_url' ] : false;
		$jpg_url = isset( $post_metas[ 'jpg_url' ] ) ? $post_metas[ 'jpg_url' ] : false;

		return array(
			'media_id'            => $media_id,
			'gif'                 => $gif_url,
			'mp4'                 => $mp4_url,
			'jpg'                 => $jpg_url,
			'attachment_metadata' => $attachment_metadata,
		);
	}

}

Boombox_Gif_To_Video::get_instance();