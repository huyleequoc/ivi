<?php
/**
 * Zombify TikTok Class
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! class_exists( "Zombify_TikTok_Embed" ) ) {

	class Zombify_TikTok_Embed extends Zombify_Embed {

		public static function createEmbed( $url, $host, $cached_data, $view, $ajax, $post_id ) {
			$embed = [];

			$url           = trim( $url );
			$url           = rtrim( $url, '/' );
			$video_type    = 'TikTok';
			$html          = '';
			$thumbnail_url = '';

			if ( $ajax ) {
				$response_data = wp_remote_get( 'https://www.tiktok.com/oembed?url=' . $url, array( 'timeout' => 15 ) );

				if ( isset( $response_data['response']['code'] ) && 200 === $response_data['response']['code'] ) {
					$response_body = json_decode( $response_data['body'] );
					$html          = $response_body->html;
					$thumbnail_url = $response_body->thumbnail_url;
				}

			} else {
				$html = parent::getWpEmbedCode( $url, $ajax, $post_id, $view );
			}

			$embed['html']      = '<div class="zf-embed-cont">' . $html . '</div>';
			$embed['thumbnail'] = $thumbnail_url;
			$embed['type']      = $video_type;
			$embed['variables'] = '';
			$embed['url']       = $url;

			return $embed;
		}

	}

}