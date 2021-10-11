<?php
/**
 * Zombify Mixcloud Class
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
    die( 'No direct script access allowed' );
}

if( !class_exists("Zombify_Mixcloud_Embed") ) {

    class Zombify_Mixcloud_Embed extends Zombify_Embed {

        public static function createEmbed( $url, $host, $cached_data, $view, $ajax, $post_id )
        {
        	$embed          = [];
            $thumbnail      = '';

            $url            = trim( $url );
            $url            = rtrim($url, '/');
            $video_type     = 'Mixcloud';
            $iframe_height  = '120px';

            if( $ajax ) {

                $file_contents      = file_get_contents("{$url}");
                preg_match('/property="og:image" content="(.*?)"/', $file_contents, $matches);
                $thumbnail          = ($matches[1]) ? $matches[1] : '';

	        }

            if( zombify()->amp()->is_amp_endpoint() ) {

                $iframe_height = '110px';

            }

            $embed['html']          = '<div class="zf-embed-cont"><iframe width="600px" height="' . $iframe_height . '" src="https://www.mixcloud.com/widget/iframe/?feed=' . urlencode( $url ) . '%2F&hide_cover=1&light=1" frameborder="0"></iframe></div>';
        	$embed['thumbnail'] 	= $thumbnail;
        	$embed['type'] 			= $video_type;
        	$embed['variables']     = '';
        	$embed['url']		 	= $url;

        	return $embed;
        }

    }

}