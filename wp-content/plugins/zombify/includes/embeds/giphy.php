<?php
/**
 * Zombify Giphy Class
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
    die( 'No direct script access allowed' );
}

if( !class_exists("Zombify_Giphy_Embed") ) {

    class Zombify_Giphy_Embed extends Zombify_Embed {

        public static function createEmbed( $url, $host, $cached_data, $view, $ajax, $post_id )
        {
        	$embed = [];

            $url        	= trim( $url );
            $url        	= rtrim($url, '/');
        	$url_array 		= explode( '/', $url );
        	$video_id 		= $url_array[4];
        	$video_id_arr 	= explode( '-', $video_id );
        	$video_id 		= trim( end( $video_id_arr ) );
        	$video_type 	= 'Giphy';

        	try {

				$gify_url 			= "http://api.giphy.com/v1/gifs/" . $video_id . "?api_key=f065c9a13ba14923867f151607191efe";
				$gify 				= json_decode(file_get_contents($gify_url));
				$img_obj 			= $gify->data->images->fixed_height;
				$embed['html']  	= '<img src="' . $img_obj->url . '" alt="" width="' . $img_obj->width . '" height="' . $img_obj->height . '">';
				$embed['thumbnail'] = $img_obj->url;

			} catch (Exception $e) {

				$embed['html']  	= '<a target="_blank" href="' . $url . '" rel="noopener">' . $url . '</a>';
				$embed['thumbnail'] = '';

			}

        	$embed['type'] 			= $video_type;
        	$embed['variables']     = '';
        	$embed['url']		 	= $url;

        	return $embed;
        }

    }

}