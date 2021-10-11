<?php
/**
 * Zombify Twitter Class
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
    die( 'No direct script access allowed' );
}

if( !class_exists("Zombify_Twitter_Embed") ) {

    class Zombify_Twitter_Embed extends Zombify_Embed {

        public static function createEmbed( $url, $host, $cached_data, $view, $ajax, $post_id )
        {
        	$embed 		= [];
        	$thumbnail 	= '';

    		$url        = trim( $url );
    		$url        = rtrim($url, '/');
        	$video_type = 'Twitter';

	        if( $ajax ) {

	        	$file_contents 		= file_get_contents("{$url}/photo/1");
	        	preg_match('/property="og:image" content="(.*?)"/', $file_contents, $matches);
	        	$thumbnail 			= ($matches[1]) ? $matches[1] : '';
	        	$thumbnail 			= str_replace( ":large", ":small", $thumbnail );

	        }

      		$embed['html']          = '<div class="zf-embed-cont">' . parent::getWpEmbedCode( $url, $ajax, $post_id, $view ) . '</div>';
        	$embed['thumbnail'] 	= $thumbnail;
        	$embed['type'] 			= $video_type;
        	$embed['variables']     = '';
        	$embed['url']		 	= $url;

        	return $embed;
        }

    }

}