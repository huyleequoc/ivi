<?php
/**
 * Zombify Soundcloud Class
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! class_exists( "Zombify_Soundcloud_Embed" ) ) {

	class Zombify_Soundcloud_Embed extends Zombify_Embed {

		public static function createEmbed( $url, $host, $cached_data, $view, $ajax, $post_id ) {
			$embed      = [];
			$thumbnail  = '';
			$url        = trim( $url );
			$url        = rtrim( $url, '/' );
			$video_type = 'Soundcloud';

			if ( $ajax ) {
				$result = wp_remote_get( 'https://soundcloud.com/oembed?format=json&iframe=true&auto_play=false&show_comments=false&url=' . $url, array( 'timeout' => 15 ) );
				$body   = json_decode( $result['body'] );

				if ( ! is_null( $body ) ) {
					$thumbnail = $body->thumbnail_url;
				}
			}

			$embed['html']      = '<div class="zf-embed-cont">' . parent::getWpEmbedCode( $url, $ajax, $post_id, $view ) . '</div>';
			$embed['thumbnail'] = $thumbnail;
			$embed['type']      = $video_type;
			$embed['variables'] = '';
			$embed['url']       = $url;

			return $embed;
		}

	}

}