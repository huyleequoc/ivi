<?php
/**
 * Zombify Embed Abstract Class
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
    die( 'No direct script access allowed' );
}

if( !class_exists("Zombify_Embed") ){

    abstract class Zombify_Embed {

        public static $enabled = true;

        /**
         * The object instance
         *
         * @var Zombify_Embed
         */
        protected static $instance;


        /**
         * Return the only existing instance of object
         *
         * @return Zombify_Embed
         */
        public static function get_instance()
        {
            if (!isset(self::$instance)) {

                $className = get_called_class();
                self::$instance = new $className();

            }

            return self::$instance;
        }


        /**
         * Prevent object cloning
         */
        protected function __clone() {}


        /**
         * Zombify_Embed constructor.
         */
        protected function __construct() {}


        /**
         * Array of all embed types with their domains
         */
        protected static function embedTypes()
        {
            return [
                'Youtube'       => [ 'youtube.com', 'youtu.be' ],
                'Facebook'      => [ 'facebook.com', 'fb.watch' ],
                'Twitter'       => [ 'twitter.com' ],
                'Vine'          => [ 'vine.co' ],
                'Vimeo'         => [ 'vimeo.com' ],
                'Dailymotion'   => [ 'dailymotion.com' ],
                'Instagram'     => [ 'instagram.com' ],
                'Pinterest'     => [ 'pinterest.com' ],
                'Soundcloud'    => [ 'soundcloud.com' ],
                'Mixcloud'      => [ 'mixcloud.com' ],
                'Reddit'        => [ 'reddit.com' ],
                'Coub'          => [ 'coub.com' ],
                'Imgur'         => [ 'imgur.com' ],
                'Vidme'         => [ 'vid.me' ],
                'Twitch'        => [ 'twitch.tv' ],
                'VK'            => [ 'vk.com' ],
                'Odnoklassniki' => [ 'ok.ru' ],
                'GooglePlus'    => [ 'plus.google' ],
                'GoogleMaps'    => [ 'maps.google' ],
                'Giphy'         => [ 'giphy.com' ],
                'TikTok'         => [ 'tiktok.com' ],
            ];
        }


        /**
         * Array of allowed image extensions
         */
        protected static function imageExtensions()
        {
            return [ 'gif', 'jpg', 'jpeg', 'png' ];
        }


        /**
         * Array of allowed video extensions
         */
        protected static function videoExtensions()
        {
            return [ 'mp4' ];
        }


        /**
         * Return different response for and without ajax
         */
        public static function returnResponse( $embed, $ajax )
        {
            if( $ajax )
                return json_encode( $embed );

            return $embed['html'];

        }


        /**
         * Parse url to get domain
         */
        public static function parseUrl( $url )
        {
            $host           = parse_url( $url )['host'];
            $host           = str_replace( "www.", "", $host );
            $host_array     = explode( '.', $host );

            if( count( $host_array ) === 3 ) {

                if( strpos($url, 'google.') !== false ) {

                    $host = $host_array[0] . '.' . $host_array[1];

                } else {

                    $host = $host_array[1] . '.' . $host_array[2];

                }

            } else if( $host_array[0] === 'google' && strpos($url, '/maps/') !== false ) {

                $host = 'maps.google';

            }

            return $host;
        }


        /**
         * Return image ot video type html
         */
        public static function imageVideoType( $url )
        {
            $embed              = [];
            $imageExtensions    = self::imageExtensions();
            $videoExtensions    = self::videoExtensions();
            $url_array          = explode( '.', $url );
            $ext                = trim( end( $url_array ) );

            if( in_array($ext, $imageExtensions) ) {

                if( zombify()->amp()->is_amp_endpoint() ) {

                    $embed['html']  = '<a target="_blank" href="' . $url . '" rel="noopener">' . $url . '</a>';

                } else {

                    $embed['html']  = '<img src="' . $url . '" alt="" width="100%" height="auto">';

                }

                $embed['type']      = 'image';
                $embed['thumbnail'] = $url;
                $embed['variables'] = '';

                return $embed;

            } else if( in_array($ext, $videoExtensions) ) {

            	global $wp_embed;

                $embed['html']      = do_shortcode( $wp_embed->run_shortcode( '[embed]' . $url . '[/embed]' ) );
                $embed['type']      = 'video';
                $embed['thumbnail'] = '';
                $embed['variables'] = '';

                return $embed;

            }

            return false;
        }


        /**
         * Return final embed code
         */
        public static function getEmbedCode( $url = '', $host = '', $ajax = false, $cached_data = false, $view = false, $post_id = null )
        {
            $url        = trim( $url );
            $check_type = true;

            if( $url === '' )
                $url = $cached_data['embed_url'];

            if( $host === '' )
                $host = self::parseUrl( $url );

            if( !isset( $cached_data['embed_type'] ) || !isset( $cached_data['embed_variables'] ) || $cached_data['embed_type'] === '' || $cached_data['embed_variables'] === '' )
                $cached_data = false;

            $return = '';

            if( $check_type ) {

                $typesArray = self::embedTypes();

                foreach ($typesArray as $key => $type) {

                    if( in_array( $host, $type ) ) {

                        require( plugin_dir_path(__FILE__) . strtolower( $key ) . '.php' );

                        $classname  = 'Zombify_' . $key . '_Embed';
                        $embed      = $classname::createEmbed( $url, $host, $cached_data, $view, $ajax, $post_id );

                        $return .= self::returnResponse( $embed, $ajax );

                        $check_type = false;

                        break;
                    }

                }

            }

            if( $check_type && $embed = self::imageVideoType( $url ) ) {

                $return .= self::returnResponse( $embed, $ajax );

            }

            if( $ajax ) {
                echo $return;
                exit;
            }

            return $return;

        }

        /**
         * Return embed code from wp core
         */
        public static function getWpEmbedCode( $url, $ajax, $post_id, $view )
        {
            global $wp_embed;
            global $post;

            $old_post = '';
            $embed    = $url;

            if( !$view ) {

                $old_post = $post;

                if( isset($_GET) && isset($_GET['post_id']) && $_GET['post_id'] !== '' ) {

                    $post_id = $_GET['post_id'];

                } else {

                    $type    = isset($_GET["type"]) ? $_GET["type"] : 'story';
                    $subtype = isset($_GET["subtype"]) ? $_GET["subtype"] : 'main';
                    $post_id = zombify_get_virtual_post_id( $type, $subtype, true );

                }

                $post = get_post($post_id);

            }

            do_action( 'zf_before_embed_grab', $wp_embed, $url );
	        try {
		        $embed = $wp_embed->shortcode( array(), $url );
	        } catch (Exception $e) {}
	        do_action( 'zf_after_embed_grab', $wp_embed, $url );

            if( !$view )
                $post = $old_post;

            wp_reset_query();

            return do_shortcode( $embed );

        }


        /**
         * Zombify_Embed get embed code from API.
         */
        public static function createEmbed( $url, $host, $cached_data, $view, $ajax, $post_id ) {

            return self::$enabled;

        }


        /**
         * Zombify_View save embed variables.
         */
        public static function saveEmbedCode( $embed )
        {

            $post_id = get_the_ID();

            if( $data = get_post_meta($post_id, 'zombify_data', true) ){

                $post_data = zf_decode_data($data);
                $post_type = get_post_meta($post_id, 'zombify_data_type', true);

                if( isset( $post_data[$post_type] ) ) {

                    foreach ( $post_data[$post_type] as $key => $each_embed ) {

                        if( isset( $each_embed['embedd'] ) ) {

                            if( $embed['url'] === $each_embed['embedd'][0]['embed_url'] ) {

                                $post_data[$post_type][$key]['embedd'][0]['embed_type']         = $embed['type'];
                                $post_data[$post_type][$key]['embedd'][0]['embed_variables']    = $embed['variables'];

                            }

                        } else if( isset( $each_embed['embed_url'] ) ) {

                            if( $embed['url'] === $each_embed['embed_url'] ) {

                                $post_data[$post_type][$key]['embed_type']         = $embed['type'];
                                $post_data[$post_type][$key]['embed_variables']    = $embed['variables'];

                            }

                        }

                    }

                    update_post_meta($post_id, "zombify_data", zf_encode_data( $post_data ) );

                }

            }
        }

    }

}