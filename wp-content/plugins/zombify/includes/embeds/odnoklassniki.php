<?php
/**
 * Zombify Odnoklassniki Class
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
    die( 'No direct script access allowed' );
}

if( !class_exists("Zombify_Odnoklassniki_Embed") ) {

    class Zombify_Odnoklassniki_Embed extends Zombify_Embed {

        public static function createEmbed( $url, $host, $cached_data, $view, $ajax, $post_id )
        {
        	$embed     = [];
            $thumbnail = 'http://st.mycdn.me/res/i/ok_logo.png';

        	if( !$cached_data ) {

                $url        = trim( $url );
                $url        = rtrim($url, '/');
	        	$url_array 	= explode( '/', $url );
	        	$video_id 	= end( $url_array );
	        	$video_type = 'Odnoklassniki';

	        } else {

	        	$embed_variables    = zf_decode_data($cached_data['embed_variables']);
	        	$video_id          	= $embed_variables['video_id'];
	        	$video_type 		= $cached_data['embed_type'];

	        }

        	$embed['html'] 			= '<div class="zf-embed-cont"><iframe width="600px" height="400px" src="https://ok.ru/videoembed/' . $video_id . '" frameborder="0" allowfullscreen></iframe></div>';
        	$embed['thumbnail'] 	= $thumbnail;
        	$embed['type'] 			= $video_type;
        	$embed['variables']     = zf_encode_data( ['video_id' => $video_id] );
        	$embed['url']		 	= $url;

        	if( !$cached_data && $view ) parent::saveEmbedCode( $embed );

        	return $embed;
        }

    }

}