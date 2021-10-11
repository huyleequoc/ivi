<?php
/**
 * Zombify Pinterest Class
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! class_exists( "Zombify_Pinterest_Embed" ) ) {

	class Zombify_Pinterest_Embed extends Zombify_Embed {

		public static $unique_html = false;

		public static function createEmbed( $url, $host, $cached_data, $view, $ajax, $post_id ) {

			if ( ! parent::createEmbed( $url, $host, $cached_data, $view, $ajax, $post_id ) ) {
				return false;
			}

			$embed     = [];
			$thumbnail = '';

			if ( ! $cached_data ) {

				$url = trim( $url );
				$url = rtrim( $url, '/' );
				preg_match( '/\?id=(\d+)/', $url, $video_id_info );
				$video_id   = $video_id_info[1];
				$video_type = 'Pinterest';

				$file_contents = file_get_contents( "https://www.pinterest.com/pin/{$video_id}" );
				preg_match( '/property="og:image" name="og:image" content="(.*?)"/', $file_contents, $matches );
				$thumbnail = ( $matches[1] ) ? $matches[1] : '';

			} else {

				$embed_variables = zf_decode_data( $cached_data['embed_variables'] );
				$video_id        = $embed_variables['video_id'];
				$video_type      = $cached_data['embed_type'];

			}

			if ( ! self::$unique_html ) {

				$embed['unique_html'] = '<script async defer src="//assets.pinterest.com/js/pinit_main.js"></script>';
				self::$unique_html    = true;

			} else {

				$embed['unique_html'] = '';

			}

			if ( zombify()->amp()->is_amp_endpoint() ) {

				function zf_amp_pinterest( $data ) {
					$scripts = array(
						'amp-pinterest' => 'https://cdn.ampproject.org/v0/amp-pinterest-0.1.js',
					);

					$data["amp_component_scripts"] = array_merge( $data["amp_component_scripts"], $scripts );

					return $data;
				}

				add_filter( 'amp_post_template_data', 'zf_amp_pinterest', 10, 2 );

				$embed['html'] = '<div class="zf-embed-cont">
                                            <amp-pinterest width="236"
                                                height="326"
                                                data-do="embedPin"
                                                data-url="https://www.pinterest.com/pin/' . $video_id . '/">
                                            </amp-pinterest>
                                        </div>';

			} else {

				$embed['html'] = '<div class="zf-embed-cont"><a data-pin-do="embedPin" data-pin-width="large" href="https://www.pinterest.com/pin/' . $video_id . '/"></a></div>' . $embed['unique_html'];

			}

			$embed['thumbnail'] = $thumbnail;
			$embed['type']      = $video_type;
			$embed['variables'] = zf_encode_data( [ 'video_id' => $video_id ] );
			$embed['url']       = $url;

			if ( ! $cached_data && $view ) {
				parent::saveEmbedCode( $embed );
			}

			return $embed;
		}

	}

}