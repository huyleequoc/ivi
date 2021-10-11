<?php
/**
 * Zombify Vimeo Class
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
    die( 'No direct script access allowed' );
}

if( !class_exists("Zombify_Vimeo_Embed") ) {

    class Zombify_Vimeo_Embed extends Zombify_Embed {

        public static function createEmbed( $url, $host, $cached_data, $view, $ajax, $post_id )
        {
        	$embed 		= [];
        	$thumbnail 	= '';

            $url        = trim( $url );
            $url        = rtrim($url, '/');
        	$url_array 	= explode( '/', $url );
        	$video_id 	= end( $url_array );
        	$video_type = 'Vimeo';

            if( $ajax ) {

    	    	$file_contents 	= file_get_contents("http://vimeo.com/api/v2/video/{$video_id}.json");
    	    	$file_contents 	= json_decode($file_contents);
    		    $thumbnail 		= ( isset( $file_contents[0] ) && isset( $file_contents[0]->thumbnail_medium ) ) ? $file_contents[0]->thumbnail_medium : '';

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