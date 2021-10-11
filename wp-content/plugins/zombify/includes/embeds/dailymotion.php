<?php
/**
 * Zombify Dailymotion Class
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
    die( 'No direct script access allowed' );
}

if( !class_exists("Zombify_Dailymotion_Embed") ) {

    class Zombify_Dailymotion_Embed extends Zombify_Embed {

        public static function createEmbed( $url, $host, $cached_data, $view, $ajax, $post_id )
        {
        	$embed     = [];
            $thumbnail = '';

            $url                = trim( $url );
            $url                = rtrim($url, '/');
        	$url_array 	        = explode( '/', $url );
        	$video_id 	        = end( $url_array );
        	$video_id 	        = explode( '_', $video_id )[0];
        	$video_type         = 'Dailymotion';

            if( $ajax ) {

                $thumbnail      = 'http://www.dailymotion.com/thumbnail/video/' . $video_id;
                $response       = wp_remote_head($thumbnail);
                $thumbnail      = wp_remote_retrieve_header( $response, 'location' );

	        }

        	$embed['html'] 			= '<div class="zf-embed-cont">' . parent::getWpEmbedCode( $url, $ajax, $post_id, $view ) . '</div>';
        	$embed['thumbnail'] 	= $thumbnail;
        	$embed['type'] 			= $video_type;
        	$embed['variables']     = '';
        	$embed['url']		 	= $url;

        	return $embed;
        }

    }

}