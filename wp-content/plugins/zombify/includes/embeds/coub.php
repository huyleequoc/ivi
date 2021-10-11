<?php
/**
 * Zombify Coub Class
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
    die( 'No direct script access allowed' );
}

if( !class_exists("Zombify_Coub_Embed") ) {

    class Zombify_Coub_Embed extends Zombify_Embed {

        public static function createEmbed( $url, $host, $cached_data, $view, $ajax, $post_id )
        {
        	$embed 		= [];
        	$html 		= '';
        	$thumbnail 	= '';

        	if( !$cached_data ) {

                $url        = trim( $url );
                $url        = rtrim($url, '/');
	        	$video_type = 'Coub';

	        } else {

	        	$video_type = $cached_data['embed_type'];

	        }

	        $result = wp_remote_get( 'https://coub.com/api/oembed.json?url=' . $url . '&autoplay=true&maxwidth=500&maxheight=500', array( 'timeout' => 15 ) );
	        $body 	= json_decode( $result['body'] );

	        if( !is_null($body) ) {

	        	$html 		= $body->html;
	        	$thumbnail 	= $body->thumbnail_url;

	        }

        	$embed['html'] 			= '<div class="zf-embed-cont">' . $html . '</div>';
        	$embed['thumbnail'] 	= $thumbnail;
        	$embed['type'] 			= $video_type;
        	$embed['variables']     = '';
        	$embed['url']		 	= $url;

        	if( !$cached_data && $view ) parent::saveEmbedCode( $embed );

        	return $embed;
        }

    }

}