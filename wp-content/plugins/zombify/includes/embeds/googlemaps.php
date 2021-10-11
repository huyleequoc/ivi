<?php
/**
 * Zombify GoogleMaps Class
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
    die( 'No direct script access allowed' );
}

if( !class_exists("Zombify_GoogleMaps_Embed") ) {

    class Zombify_GoogleMaps_Embed extends Zombify_Embed {

        public static function createEmbed( $url, $host, $cached_data, $view, $ajax, $post_id )
        {
        	$embed     = [];
            $thumbnail = 'https://www.seeklogo.net/wp-content/uploads/2015/09/new-google-maps-logo-vector-download.jpg';

        	if( !$cached_data ) {

                $url                = trim( $url );
                $url                = rtrim($url, '/');
	        	$url_array 	        = explode( '/', $url );
	        	$video_id 	        = $url_array[5];
	        	$video_type         = 'GoogleMaps';

	        } else {

	        	$embed_variables    = zf_decode_data($cached_data['embed_variables']);
	        	$video_id          	= $embed_variables['video_id'];
	        	$video_type 		= $cached_data['embed_type'];

	        }

            $embed['html'] 			= '<div class="zf-embed-cont"><iframe width="600px" height="400" frameborder="0" style="border:0" src="https://www.google.com/maps/embed/v1/search?key=AIzaSyAEQaPbp2TE1pEoYbkAE8le5IscUDXtg8Y&q=' . $video_id . '" allowfullscreen></iframe></div>';
        	$embed['thumbnail'] 	= $thumbnail;
        	$embed['type'] 			= $video_type;
        	$embed['variables']     = zf_encode_data( ['video_id' => $video_id] );
        	$embed['url']		 	= $url;

        	if( !$cached_data && $view ) parent::saveEmbedCode( $embed );

        	return $embed;
        }

    }

}