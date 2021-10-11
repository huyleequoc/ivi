<?php
/**
 * Zombify Facebook Class
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! class_exists( "Zombify_Facebook_Embed" ) ) {

	class Zombify_Facebook_Embed extends Zombify_Embed {

		public static $unique_html = false;

		public static function createEmbed( $url, $host, $cached_data, $view, $ajax, $post_id ) {
			$embed        = [];
			$parse_script = '';
			$url          = trim( $url );
			$url          = rtrim( $url, '/' );
			$type         = 'Facebook';
			$app_id       = zf_get_option( 'zombify_facebook_app_id' );
			$app_secret   = zf_get_option( 'zombify_facebook_app_secret' );

			if ( false === $app_id || false === $app_secret ) {
				return $embed;
			}

			if ( strpos( $url, 'fb.watch' ) !== false ) {
				//Oembed Video
				$post_type   = 'video';
				$oembed_type = 'oembed_video';
			} else {
				//Oembed Post
				$post_type   = 'post';
				$oembed_type = 'oembed_post';
			}

			//documentation link is https://developers.facebook.com/docs/plugins/oembed
			//access_token={app-id}|{app-secret}
			$access_token = $app_id . '|' . $app_secret;
			$response = wp_remote_get( 'https://graph.facebook.com/v8.0/' . $oembed_type . '/?url=' . $url . '&access_token=' . $access_token );

			if ( 200 != $response['response']['code'] ) {
				return $embed;
			}

			$response_body = json_decode( $response['body'], true );

			if ( ! self::$unique_html ) {

				preg_match( '/<script.*\/script>/', $response_body['html'], $matches );

				if ( ! empty( $matches ) ) {
					$parse_script = $matches[0];
				}

				self::$unique_html = true;
			}


			//todo tmp comment, in future need to check wp core embed for FB, if will be ok than can be uncomment
//			if ( ! $view ) {
//
//				$embed['html'] = $parse_script . "<div class='fb-" . $post_type . "' data-href='" . $url . "' data-show-text='false'></div>";
//
//			} else {
//
//				$embed['html'] = $parse_script . '<div class="zf-embed-cont">' . parent::getWpEmbedCode( $url, $ajax, $post_id, $view ) . '</div>';
//
//			}

			$embed['html']      = $parse_script . "<div class='fb-" . $post_type . "' data-href='" . $url . "' data-show-text='false'></div>";
			$embed['type']      = $type;
			$embed['variables'] = '';
			$embed['url']       = $url;

			return $embed;
		}

	}

}