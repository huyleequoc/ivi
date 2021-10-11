<?php
/**
 * Zombify Imgur Class
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
    die( 'No direct script access allowed' );
}

if( !class_exists("Zombify_Imgur_Embed") ) {

    class Zombify_Imgur_Embed extends Zombify_Embed {

        public static function createEmbed( $url, $host, $cached_data, $view, $ajax, $post_id )
        {
        	$embed 			= [];

            $url            = trim( $url );
            $url            = rtrim($url, '/');
        	$url_array 	    = explode( '/', $url );
        	$video_id 	    = end( $url_array );
            $video_id       = explode( '.', $video_id )[0];
        	$video_type     = 'Imgur';

            if( zombify()->amp()->is_amp_endpoint() ) {

                function zf_amp_imgur( $data ){
                    $scripts = array(
                        'amp-imgur' => 'https://cdn.ampproject.org/v0/amp-imgur-0.1.js',
                    );

                    $data["amp_component_scripts"] = array_merge($data["amp_component_scripts"], $scripts );
                    return $data;
                }

                add_filter('amp_post_template_data', 'zf_amp_imgur', 10, 2);


                if( strlen( $video_id ) === 5 )
                    $video_id = 'a/' . $video_id;

                $embed['html']      = '<div class="zf-embed-cont">
                                            <amp-imgur data-imgur-id="' . $video_id . '" layout="responsive" width="1" height="1"></amp-imgur>
                                        </div>';

            } else {

                $embed['html']      = '<div class="zf-embed-cont">' . parent::getWpEmbedCode( $url, $ajax, $post_id, $view ) . '</div>';

            }

        	$embed['thumbnail'] 	= 'http://i.imgur.com/' . $video_id . 'm.jpg';
        	$embed['type'] 			= $video_type;
        	$embed['variables']     = '';
        	$embed['url']		 	= $url;

        	return $embed;
        }

    }

}