<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

use CloudConvert\Api;
use GuzzleHttp\Client as Client;

if ( ! class_exists( 'ZF_Converter_Cloudconvert' ) ) {

	class ZF_Converter_Cloudconvert extends ZF_Converter_Base {

		protected $_api;

		public function process( $attachment, $target_format, $create_attachment = false, $attachment_mime_type = 'image/jpeg', $source_format = null ) {

			if ( $this->create_client() ) {

				$storage = zf_get_option( "zombify_media_storage", "local" );

				$convert_function = "convert_" . strtolower( $storage );

				if ( method_exists( $this, $convert_function ) ) {

					if ( is_int( $attachment ) ) {
						$attachment = get_post( $attachment );
					}

					$result_arr = $this->$convert_function( $attachment, $target_format, $source_format );

					if ( isset( $result_arr["success"] ) && $result_arr["success"] === true && $create_attachment ) {

						$converted_attachment_args = array(
							'guid'           => $result_arr['result']["url"],
							'post_mime_type' => $attachment_mime_type,
							'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $result_arr['result']["url"] ) ),
							'post_content'   => '',
							'post_status'    => 'inherit'
						);


						$converted_attach_id = wp_insert_attachment( $converted_attachment_args, ( isset( $result_arr["result"]["path"] ) ? $result_arr["result"]["path"] : false ), $attachment->ID );

						if ( isset( $result_arr["result"]["path"] ) ) {
							require_once( ABSPATH . 'wp-admin/includes/image.php' );

							$converted_attach_data = wp_generate_attachment_metadata( $converted_attach_id, $result_arr["result"]["path"] );
							wp_update_attachment_metadata( $converted_attach_id, $converted_attach_data );
						}

						$result_arr["attachment_id"] = $converted_attach_id;

					}

					return $result_arr;

				} else {

					$message = sprintf( esc_html__( 'Invalid storage: %s', 'zombify' ), $storage );

					$this->log( $message );

					return array( 'success' => false, 'message' => $message, 'result' => '' );

				}

			}

		}

		/**
		 * Create Client
		 *
		 * @return bool
		 */
		protected function create_client() {

			if ( $this->_api ) {
				return true;
			}

			if ( $api_key = zf_get_option( "zombify_cloudconvert_api_key" ) ) {

				$client = new Client( array( 'defaults' => array( 'verify' => false ) ) );

				$this->_api = new Api( $api_key, $client );

				return true;
			}

			return false;
		}

		protected function convert_local( $attachment, $target_format, $source_format = null ) {

			$success = false;
			$message = '';
			$result  = '';

			$source_path = get_attached_file( $attachment->ID );

			if ( ! $source_format ) {
				$source_format = strtolower( pathinfo( $source_path, PATHINFO_EXTENSION ) );
			}

			$file_name = uniqid() . '_' . trim( basename( $source_path, $source_format ), '.' );

			$wp_upload_dir   = wp_upload_dir();
			$output_location = sprintf( '%1$s/%2$s.%3$s', $wp_upload_dir['path'], $file_name, $target_format );

			if ( in_array( $source_format, array( 'mp4', 'webm' ) ) && in_array( $target_format, array(
					'png',
					'jpg'
				) ) ) {

				$convert_args = array(
					"mode"             => "info",
					"input"            => "upload",
					"file"             => fopen( $source_path, 'r' ),
					'filename'         => $file_name . '.' . $source_format,
					"converteroptions" => [
						"thumbnail_format" => $target_format,
						"thumbnail_size"   => "1200x",
					],
					'wait'             => true,
				);

			} else {

				$convert_args = array(
					'inputformat'  => $source_format,
					'outputformat' => $target_format,
					'input'        => 'upload',
					'file'         => fopen( $source_path, 'r' ),
					'filename'     => $file_name . '.' . $source_format,
					'wait'         => true
				);

			}

			try {

				$this->_api->convert( $convert_args )->download( $output_location );

				$success = true;
				$result  = array(
					'path' => $output_location,
					'url'  => $wp_upload_dir['url'] . '/' . $file_name . '.' . $target_format
				);

			} catch ( Exception $e ) {
				$message = $e->getMessage();
				$this->log( $message );
			}

			return array( 'success' => $success, 'message' => $message, 'result' => $result );

		}

		protected function convert_aws_s3( $attachment, $target_format, $source_format = null ) {

			$success = false;
			$message = '';
			$result  = '';

			$aws_accesskeyid     = zf_get_option( 'zombify_aws_s3_access_key_id' );
			$aws_secretaccesskey = zf_get_option( 'zombify_aws_s3_secret_access_key' );
			$aws_bucket          = zf_get_option( 'zombify_aws_s3_bucket_name' );

			if ( ! $aws_accesskeyid || ! $aws_secretaccesskey ) {
				$message = 'Amazon S3:' . esc_html__( 'Invalid Configuration', 'zombify' );
				$this->log( $message );
			} else {
				$source_path = get_attached_file( $attachment->ID );

				if ( ! $source_format ) {
					$source_format = strtolower( pathinfo( $source_path, PATHINFO_EXTENSION ) );
				}

				$file_name       = uniqid() . '_' . trim( basename( $source_path, $source_format ), '.' );
				$wp_upload_dir   = wp_upload_dir();
				$output_location = sprintf( '%1$s/%2$s.%3$s', $wp_upload_dir['path'], $file_name, $target_format );

				if ( in_array( $source_format, array( 'mp4', 'webm' ) ) && in_array( $target_format, array('png',	'jpg') ) ) {

					$convert_args = array(
						"mode"             => "info",
						'input'            => 'upload',
						'file'             => fopen( $source_path, 'r' ),
						'filename'         => $file_name . '.' . $source_format,
						'wait'             => true,
						"converteroptions" => [
							"thumbnail_format" => $target_format,
							"thumbnail_size"   => "1200x",
						],
						'output'           => array(
							"s3" => array(
								'acl'             => 'public-read',
								'accesskeyid'     => $aws_accesskeyid,
								'secretaccesskey' => $aws_secretaccesskey,
								'bucket'          => $aws_bucket
							),
						)
					);

				} else {

					$convert_args = array(
						'inputformat'  => $source_format,
						'outputformat' => $target_format,
						'input'        => 'upload',
						'file'         => fopen( $source_path, 'r' ),
						'filename'     => $file_name . '.' . $source_format,
						'wait'         => true,
						'output'       => array(
							"s3" => array(
								'acl'             => 'public-read',
								'accesskeyid'     => $aws_accesskeyid,
								'secretaccesskey' => $aws_secretaccesskey,
								'bucket'          => $aws_bucket
							),
						)
					);

				}

				try {

					$this->_api->convert( $convert_args );
					$success = true;

					//Get uploaded image from amazon bucket
					$aws_object_url = sprintf( 'https://%1$s.s3.us-east-2.amazonaws.com/%2$s.%3$s', $aws_bucket, $file_name, $target_format );
					$response       = wp_remote_get( $aws_object_url );

					if ( is_array( $response ) && ! is_wp_error( $response ) ) {
						//Save image to wp-content/uploads
						wp_upload_bits( $file_name . '.' . $target_format, '', wp_remote_retrieve_body( $response ) );
					}

					$result = array(
						'path' => $output_location,
						'url'  => $wp_upload_dir['url'] . '/' . $file_name . '.' . $target_format
					);

				} catch ( Exception $e ) {
					$message = $e->getMessage();
					$this->log( $message );
				}

			}

			return array( 'success' => $success, 'message' => $message, 'result' => $result );

		}

	}

}