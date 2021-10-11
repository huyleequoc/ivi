<?php
/**
 * Zombify Vine Class
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
    die( 'No direct script access allowed' );
}

if( !class_exists("Zombify_Vine_Embed") ) {

    class Zombify_Vine_Embed extends Zombify_Embed {

        public static function createEmbed( $url, $host, $cached_data, $view, $ajax, $post_id )
        {
        	$embed 		= [];
        	$thumbnail 	= '';

        	if( !$cached_data ) {

        		$url 			= trim( $url );
        		$url 			= rtrim($url, '/');
	        	$url_array 		= explode( '/', $url );
	        	$video_id 		= end( $url_array );
	        	$video_type 	= 'Vine';

    	    	$file_contents 	= file_get_contents("http://vine.co/v/{$video_id}");
    		    preg_match('/property="og:image" content="(.*?)"/', $file_contents, $matches);
    		    $thumbnail 		= ($matches[1]) ? $matches[1] : '';

	        } else {

	        	$embed_variables    = zf_decode_data($cached_data['embed_variables']);
	        	$video_id          	= $embed_variables['video_id'];
	        	$video_type 		= $cached_data['embed_type'];

	        }

        	$embed['html'] 			= '<div class="zf-embed-cont"><iframe width="600px" height="400px" src="https://vine.co/v/' . $video_id . '/embed/simple" frameborder="0" allowfullscreen></iframe></div>';
        	$embed['thumbnail'] 	= $thumbnail;
        	$embed['type'] 			= $video_type;
        	$embed['variables']     = zf_encode_data( ['video_id' => $video_id] );
        	$embed['url']		 	= $url;

        	if( !$cached_data && $view ) parent::saveEmbedCode( $embed );

        	return $embed;
        }

    }

}