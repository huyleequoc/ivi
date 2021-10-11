<?php
/**
 * Zombify Instagram Class
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! class_exists( "Zombify_Instagram_Embed" ) ) {

	class Zombify_Instagram_Embed extends Zombify_Embed {

		public static function createEmbed( $url, $host, $cached_data, $view, $ajax, $post_id ) {
			$embed        = [];
			$html         = '';
			$thumbnail    = '';

			$url        = trim( $url );
			$url        = strtok( $url, '?' );
			$url        = rtrim( $url, '/' );
			$url_array  = explode( '/', $url );
			$video_id   = end( $url_array );
			$video_type = 'Instagram';
			$app_id     = zf_get_option( 'zombify_facebook_app_id' );
			$app_secret = zf_get_option( 'zombify_facebook_app_secret' );

			if ( false === $app_id || false === $app_secret ) {
				return $embed;
			}


			//Documentation https://developers.facebook.com/docs/plugins/oembed
			//access_token={app-id}|{app-secret}
			$access_token = $app_id . '|' . $app_secret;

			//Documentation https://developers.facebook.com/docs/instagram/oembed
			$args     = array(
				'url'          => $url,
				'access_token' => $access_token,
			);
			$response = wp_remote_get( 'https://graph.facebook.com/v10.0/instagram_oembed?' . http_build_query( $args ), array( 'timeout' => 15 ) );
			if ( wp_remote_retrieve_response_code( $response ) === 200 ) {
				$body = json_decode( wp_remote_retrieve_body( $response ), true );
			}

			$parse_script = '<script>try { window.instgrm.Embeds.process() } catch (e) {}</script>';

			if ( ! empty( $body ) ) {
				$html      = $body['html'];
				$thumbnail = $body['thumbnail_url'];
			}

			if ( zombify()->amp()->is_amp_endpoint() ) {

				if ( ! function_exists( 'zf_amp_instagram' ) ) {
					function zf_amp_instagram( $data ) {
						$scripts = array(
							'amp-instagram' => 'https://cdn.ampproject.org/v0/amp-instagram-0.1.js',
						);

						$data["amp_component_scripts"] = array_merge( $data["amp_component_scripts"], $scripts );

						return $data;
					}
				}

				add_filter( 'amp_post_template_data', 'zf_amp_instagram', 10, 2 );

				$embed['html'] = '<amp-instagram
                                    data-shortcode="' . $video_id . '"
                                    data-captioned
                                    width="400"
                                    height="400"
                                    layout="responsive">
                                </amp-instagram>';

			} else {
				$embed['html'] = '<div class="zf-embed-cont">' . $html . '</div>' . $parse_script;
			}

			$embed['thumbnail'] = $thumbnail;
			$embed['type']      = $video_type;
			$embed['variables'] = '';
			$embed['url']       = $url;

			return $embed;
		}

	}

}