<?php
/**
 * Zombify Twitch Class
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! class_exists( "Zombify_Twitch_Embed" ) ) {

	class Zombify_Twitch_Embed extends Zombify_Embed {

		public static function createEmbed( $url, $host, $cached_data, $view, $ajax, $post_id ) {
			$embed     = [];
			$thumbnail = '';

			if ( ! $cached_data ) {
				$url        = trim( $url );
				$url        = rtrim( $url, '/' );
				$url_array  = explode( '/', $url );
				$video_id   = end( $url_array );
				$video_type = 'Twitch';
			} else {
				$embed_variables = zf_decode_data( $cached_data['embed_variables'] );
				$video_id        = $embed_variables['video_id'];
				$video_type      = $cached_data['embed_type'];
			}

			if ( strpos( $url, '/videos/' ) !== false || strpos( $url, '/clip/' ) !== false ) {
				$helix_type = strpos( $url, '/videos/' ) !== false ? 'videos' : 'clips';

				if ( $helix_type == 'videos' ) {
					$iframe_src = 'https://player.twitch.tv/?video=v' . $video_id;
				} else {
					$iframe_src = 'https://clips.twitch.tv/embed?clip=' . $video_id;
				}

				if ( ! $cached_data ) {
					//Start to get thumbnail via API
					$client_id     = zf_get_option( 'zombify_twitch_app_id' );
					$client_secret = zf_get_option( 'zombify_twitch_app_secret' );

					if ( false !== $client_id && false !== $client_secret ) {
						$wp_http_curl = new WP_Http_Curl();
						$access_token = self::get_access_token( $wp_http_curl, $client_id, $client_secret );

						if ( $access_token ) {
							$api_response_body = self::get_api_response_body( $wp_http_curl, $access_token, $client_id, $video_id, $helix_type );
							$thumbnail         = self::get_thumbnail( $api_response_body, $helix_type );
						}
					}
				}
			} else {
				//Case for Live Video
				$iframe_src = 'https://player.twitch.tv/?channel=' . $video_id;
			}

			//Requirements: Domains that use Twitch embeds must use SSL certificates.
			$domain = basename( get_site_url() );

			/**
			 * @attr stinrg autoplay e.g 'true' || 'false'. The exception is mobile devices, on which video cannot
			 * be played without user interaction. Default: true.
			 * @attr string|array parent e.g 'boombox.px-lab.com' || ['boombox.px-lab.com', 'zombify.px-lab.com']
			 */
			$embed_atts = array( 'autoplay' => 'false', 'parent' => $domain );
			$embed_atts = apply_filters( 'boombox/embed/twitch/single_post/src_atts', $embed_atts, $video_id );
			$iframe_src = $iframe_src . '&' . build_query( $embed_atts );

			$embed['html']      = '<div class="zf-embed-cont"><iframe width="600px" height="400px" src="' . $iframe_src . '" frameborder="0" allowfullscreen></iframe></div>';
			$embed['thumbnail'] = $thumbnail;
			$embed['type']      = $video_type;
			$embed['variables'] = zf_encode_data( [ 'video_id' => $video_id ] );
			$embed['url']       = $url;

			if ( ! $cached_data && $view ) {
				parent::saveEmbedCode( $embed );
			}

			return $embed;
		}

		/**
		 * @param $wp_http_curl
		 * @param $client_id
		 * @param $client_secret
		 *
		 * @return string
		 */
		private static function get_access_token( $wp_http_curl, $client_id, $client_secret ) {
			$access_token   = '';
			$access_request = array(
				'method' => 'POST',
				'body'   => array(
					'client_id'     => $client_id,
					'client_secret' => $client_secret,
					'grant_type'    => 'client_credentials'
				),
			);

			$access_response = $wp_http_curl->request( 'https://id.twitch.tv/oauth2/token', $access_request );

			if ( isset( $access_response['response']['code'] ) && $access_response['response']['code'] == 200 ) {
				$access_body  = json_decode( $access_response['body'], true );
				$access_token = $access_body['access_token'];
			}

			return $access_token;
		}

		/**
		 * Documentation links
		 * https://dev.twitch.tv/docs/api/reference#get-videos
		 * https://dev.twitch.tv/docs/api/reference#get-clips
		 *
		 * @param $wp_http_curl
		 * @param $access_token
		 * @param $client_id
		 * @param $video_id
		 * @param $helix_type
		 *
		 * @return array|mixed|null
		 */
		private static function get_api_response_body( $wp_http_curl, $access_token, $client_id, $video_id, $helix_type ) {
			$response_body = [];

			$request_args = array(
				'method'  => 'GET',
				'headers' => array(
					'Authorization' => 'Bearer ' . $access_token,
					'Client-Id'     => $client_id
				),
			);

			$response = $wp_http_curl->request( 'https://api.twitch.tv/helix/' . $helix_type . '/?id=' . $video_id, $request_args );

			if ( isset( $response['response']['code'] ) && $response['response']['code'] == 200 ) {
				$response_body = json_decode( $response['body'], true );
			}

			return $response_body;
		}

		/**
		 * Twitch new API returns a thumbnail for "Videos" and "Clips"
		 *
		 * @param $api_response_body
		 * @param $helix_type
		 *
		 * @return mixed|string
		 */
		private static function get_thumbnail( $api_response_body, $helix_type ) {
			$thumbnail = '';
			if ( isset( $api_response_body['data'][0]['thumbnail_url'] ) ) {
				$thumbnail = $api_response_body['data'][0]['thumbnail_url'];

				//Set thumbnail width and height for "Videos"
				if ( $helix_type == 'videos' ) {
					$thumbnail = str_replace(
						array( '%{width}', '%{height}' ),
						array( 480, 272 ),
						$thumbnail
					);
				}
			}

			return $thumbnail;
		}

	}

}