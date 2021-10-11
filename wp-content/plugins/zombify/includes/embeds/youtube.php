<?php
/**
 * Zombify Youtube Class
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
    die( 'No direct script access allowed' );
}

if( !class_exists("Zombify_Youtube_Embed") ) {

    class Zombify_Youtube_Embed extends Zombify_Embed {

        public static function createEmbed( $url, $host, $cached_data, $view, $ajax, $post_id )
        {
        	$embed = [];

            $url        = trim( $url );
            $url        = rtrim($url, '/');
        	$url_array 	= explode( '/', $url );
        	$video_id 	= end( $url_array );
        	$video_type = 'Youtube';

	        if( in_array( $host, array( 'youtube.com', 'youtu.be' ) ) ) {

		        $regex = "/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/";
		        preg_match( $regex, $url, $youtube_matches );

		        $video_id = end( $youtube_matches );

        	}

        	$embed['html'] 			= '<div class="zf-embed-cont">' . parent::getWpEmbedCode( $url, $ajax, $post_id, $view ) . '</div>';
        	$embed['thumbnail'] 	= 'https://img.youtube.com/vi/' . $video_id . '/0.jpg';
        	$embed['type'] 			= $video_type;
        	$embed['variables']     = '';
        	$embed['url']		 	= $url;

        	return $embed;
        }

    }

}