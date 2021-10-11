<?php
/**
 * Zombify plugin functions
 *
 * @package BoomBox_Theme
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
    die( 'No direct script access allowed' );
}

if( ! boombox_plugin_management_service()->is_plugin_active( 'zombify/zombify.php' ) ) {
    return;
}

if ( ! class_exists( 'Boombox_Zombify' ) ) {

    final class Boombox_Zombify {

	    /**
	     * Holds class single instance
	     * @var null
	     */
	    private static $_instance = null;

	    /**
	     * Get instance
	     * @return Boombox_Zombify|null
	     */
	    public static function get_instance() {

		    if (null == static::$_instance) {
			    static::$_instance = new self();
		    }

		    return static::$_instance;

	    }

	    /**
	     * Boombox_Zombify constructor.
	     */
	    private function __construct() {
            $this->hooks();

		    do_action( 'boombox/zombify/wakeup', $this );
        }

	    /**
	     * A dummy magic method to prevent Boombox_Zombify from being cloned.
	     *
	     */
	    public function __clone() {
		    throw new Exception('Cloning ' . __CLASS__ . ' is forbidden');
	    }

        /**
         * Setup Hooks
         */
        private function hooks() {
	        add_filter( 'boombox/single/sortable_sections', array( $this, 'edit_single_sortable_sections' ), 10, 1 );
            add_filter( 'boombox/single/link-pages-args', array( $this, 'edit_single_link_pages_args' ), 10, 1 );
            add_filter( 'boombox_single_post_share_box_elements', array( $this, 'zombify_single_post_share_box_elements' ), 10, 1 );
            add_filter( 'boombox_fixed_navigation_post', array( $this, 'zombify_fixed_navigation_post' ), 10, 2 );

            add_filter( 'zombify_bp_pagination_args', array( $this, 'zombify_bp_pagination_args' ), 10, 1);
            add_filter( 'zombify_bp_posts_per_page', array( $this, 'zombify_bp_posts_per_page' ), 10, 1);
            add_filter( 'zombify_video_tag', array( $this, 'zombify_video_layout' ), 10, 5 );
            add_filter( 'boombox/loop-item/show-media', array( $this, 'force_loop_item_show_thumbnail' ), 10, 4 );
            add_filter( 'post_thumbnail_html', array( $this, 'post_thumbnail_fallback' ), 200, 5 );
            add_filter( 'boombox/amp/template_head_scripts', array( $this, 'amp_template_scripts' ), 100, 1 );
            add_filter( 'bbte/cloudconvert_allow_processing', array( $this, 'cloudconvert_allow_processing' ), 10, 6 );
            add_filter( 'boombox/create-post-button-args', array( $this, 'edit_post_create_button_args' ), 10, 1 );
            add_filter( 'boombox/post/has-thumbnail', array( $this, 'may_be_has_thumbnail_for_formats' ), 10, 2 );
	        add_filter( 'boombox/render_page_content', array( $this, 'render_page_content' ), 10, 1 );
	        add_filter( 'boombox/single/component_content/media_mix', array( $this, 'edit_single_media_mix_content' ), 10, 1 );

            if( has_action( 'bp_notification_settings', 'zf_submissions_notification_settings' ) ) {
                remove_action( 'bp_notification_settings', 'zf_submissions_notification_settings', 10 );
                add_action( 'bp_notification_settings', array( $this, 'zf_submissions_component_notification_settings' ), 10 );
            }

            if( boombox_plugin_management_service()->is_plugin_active('boombox-theme-extensions/boombox-theme-extensions.php') ) {
                add_filter( 'zombify_img_tag', array($this, 'zombify_img_tag'), 10, 3 );
            }
        }

        /**
         * Prevent scripts doubling
         *
         * @param array $scripts Current strips
         * @return array
         */
        public function amp_template_scripts( $scripts ) {
            unset( $scripts['custom-element']['amp_form'] );
            return $scripts;
        }

        /**
         * Setup pagination properties
         *
         * @param array $args Current args
         * @return array
         */
        public function zombify_bp_pagination_args( $args ) {
            return array_merge( $args, array(
                'end_size' => 1,
                'mid_size' => 1,
                'prev_text' => _x( 'Previous', 'previous set of posts', 'zombify' ),
                'next_text'  => _x( 'Next', 'next set of posts', 'zombify' )
            ) );
        }

        /**
         * Setup posts per page for bp component posts
         *
         * @param int $per_page Current per page
         * @return int
         */
        public function zombify_bp_posts_per_page( $per_page ) {
	        $per_page = 10;
            return $per_page;
        }

        /**
         * Render gif images as video if possible
         *
         * @param string $html Current HTML
         * @param int $post_thumbnail_id Post thumbnail ID
         * @param string $size Thumbnail size
         * @return string
         */
        public function zombify_img_tag( $html, $post_thumbnail_id, $size ) {

            $mime_type = get_post_mime_type( $post_thumbnail_id );
            if( $mime_type == 'image/gif' ) {
                $mp4_url = boombox_get_post_meta( $post_thumbnail_id, 'zombify_mp4_url' );
                $mp4_id = boombox_get_post_meta( $post_thumbnail_id, 'zombify_mp4_id' );
                $jpeg_url = boombox_get_post_meta( $post_thumbnail_id, 'zombify_jpeg_url' );

                if( $mp4_url && $mp4_id && $jpeg_url ) {
                	if( wp_is_mobile() ) {
		                $html = do_shortcode(
		                		sprintf( '[boombox_gif_video mp4="%s" gif="%s" jpg="%s"]',
									$mp4_url,
					                wp_get_attachment_image_url( $post_thumbnail_id, 'full' ),
					                $jpeg_url
								)
						);
	                } else {
		                $html = $this->zombify_video_layout( $html, $mp4_url, 'video/mp4', $mp4_id, $size, $jpeg_url );
					}
                } else {
                    $html = Boombox_Gif_To_Video::get_instance()->filter_gif_thumbnail_html( $html, get_the_ID(), $post_thumbnail_id, $size, array( 'play' => true ) );
                }
            } elseif( ! boombox_is_amp() ) {
	             list( $before, $after ) = array_values( boombox_get_media_placeholder_atts( $post_thumbnail_id, $size ) );
	             $html = $before . $html . $after;
            }

            return $html;

        }

	    /**
         * Remove reactions section from sortable sections for single post
	     * @param array $sections Current sections
	     * @return array
	     */
	    public function edit_single_sortable_sections ( $sections ) {
		    if ( ( 'list_item' == get_post_type() ) ) {
			    $index = array_search( 'reactions', $sections );
			    if ( $index !== false ) {
				    unset( $sections[ $index ] );
				    $sections = array_values( $sections );
			    }
		    }

		    return $sections;

	    }

        /**
         * Edit link pages options
         *
         * @param array $options Current options
         * @return array
         */
	    public function edit_single_link_pages_args ( $options ) {

		    if ( ( 'list_item' == get_post_type() ) ) {
			    $options[ 'go_to_prev_next' ] = false;
		    }

		    return $options;

	    }

        /**
         * Hide single template points for "list_item" post type
         *
         * @param array $elements Current elements setup
         * @return array
         */
        public function zombify_single_post_share_box_elements( $elements ) {

            if( ( 'list_item' == get_post_type() ) ) {
                $points_key = array_search( 'points', $elements );
                if( false !== $points_key ) {
                    unset( $elements[ $points_key ] );
                }
            }

            return $elements;

        }

        /**
         * Modify single template fixed pagination for "list_item" post type
         *
         * @param null|string|WP_Post Post object
         * @param string $nav Current nav direction ( prev | next )
         * @return null|string|WP_Post
         */
        public function zombify_fixed_navigation_post( $boombox_post, $nav ) {

            if( 'list_item' == get_post_type() ) {

                static $fixed_navigation_data;

                if( ! $fixed_navigation_data ) {

                    global $post;

                    $parent_post_data = zf_decode_data( get_post_meta( $post->post_parent, 'zombify_data', true));

                    $prev_data = '';
                    $next_data = '';
                    $prev_data_temp = '';
                    $first_data = '';
                    $last_data = '';

                    $i = 0;
                    foreach ($parent_post_data["list"] as $pdata) {
                        $i++;
                        if ($pdata["post_id"] == $post->ID) {
                            $prev_data = $prev_data_temp;
                        }

                        if ($prev_data_temp != '' && $prev_data_temp["post_id"] == $post->ID) {
                            $next_data = $pdata;
                        }

                        $prev_data_temp = $pdata;

                        if ($first_data == '') {
                            $first_data = $pdata;
                        }

                        $last_data = $pdata;

                    }

                    if (zombify()->sub_posts_loop) {

                        if ($next_data == '') $next_data = $first_data;
                        if ($prev_data == '') $prev_data = $last_data;

                    }

                    $fixed_navigation_data[ 'prev' ] = $prev_data ? get_post( $prev_data[ 'post_id' ] ) : null;
                    $fixed_navigation_data[ 'next' ] = $next_data ? get_post( $next_data[ 'post_id' ] ) : null;

                }

                $boombox_post = isset( $fixed_navigation_data[ $nav ] ) ? $fixed_navigation_data[ $nav ] : null;

            }

            return $boombox_post;

        }

        /**
         * Handle video layout to match theme styles
         *
         * @param string $html Current HTML
         * @param string $url Video URL
         * @param int $video_post_id Video post ID
         * @param string $size Size
         * @return string
         */
        public function zombify_video_layout( $html, $url, $type, $video_post_id, $size, $poster_url = false ) {
            $attributes = ' loop muted';

            $poster_url = $poster_url ? $poster_url : boombox_get_post_meta( $video_post_id, 'zombify_jpeg_url' );
            if( $poster_url ) {
                $attributes .= sprintf( ' poster="%s"', esc_attr( $poster_url ) );
            }
            if( boombox_is_amp() ) {
                $attributes .= ' controls';
            }
            $html = sprintf('
                <div class="gif-video-wrapper">
                    <video class="gif-video"%1$s>
                        <source src="%2$s" type="%3$s">
                    </video>
                </div>',
                $attributes,
                esc_url( $url ),
                $type
            );

            return $html;
        }

        /**
         * Force theme to have a placeholder for post thumbnails in case for gif story type
         *
         * @param bool $show_media Current state
         * @param bool $enabled_by_template If's enabled via template
         * @param bool $has_thumbnail_or_video Has it a thumbnail or video
         * @param string $layout Current rendering layout
         * @return bool
         */
        public function force_loop_item_show_thumbnail( $show_media, $enabled_by_template, $has_thumbnail_or_video, $layout ) {

            if( $enabled_by_template && in_array( $layout, array( 'content-classic', 'content-stream' ) ) && ! $has_thumbnail_or_video ) {
                $zombify_data_type = boombox_get_post_meta( get_the_ID(), 'zombify_data_type' );
                if( 'gif' == $zombify_data_type ) {
                    $show_media = true;
                }
            }

            return $show_media;
        }

        /**
         * Replace post thumbnail HTML for "Gif" format
         *
         * @param $html                 string          Current HTML
         * @param $post_id              int             Post ID
         * @param $post_thumbnail_id    int             Post thumbnail ID
         * @param $size                 string          Post thumbnail size
         * @param $attr                 string|array    Optional. Attributes for the image markup
         */
        private function post_thumbnail_fallback_for_gif( &$html, $post_id, $post_thumbnail_id, $size, $attr ) {

            if ( $zombify_featured_media = boombox_get_post_meta( $post_id, 'zombify_featured_media' ) ) {

                switch ( $zombify_featured_media['media_mime_type'] ) {
                    /***** media_type = "mp4" */
                    case 'video/mp4':
                        $html = $this->zombify_video_layout(
                            $html,
                            $zombify_featured_media['media_url'],
                            'video/mp4',
                            $zombify_featured_media['media_id'],
                            $size,
                            ( isset( $zombify_featured_media['zombify_jpeg_url'] ) ? $zombify_featured_media['zombify_jpeg_url'] : false )
                        );

	                    /***** Let template to know about the thumbnail content type */
                        Boombox_Template::set( 'bb_post_media_type', 'video' );

                        break;

                    /***** media_type = "gif" */
                    case 'image/gif':
                        if ( isset( $zombify_featured_media['zombify_mp4_url'] ) ) {
                            $html = $this->zombify_video_layout(
                                $html,
                                $zombify_featured_media['zombify_mp4_url'],
                                'video/mp4',
                                $zombify_featured_media['zombify_mp4_id'],
                                $size,
                                ( isset( $zombify_featured_media['zombify_jpeg_url'] ) ? $zombify_featured_media['zombify_jpeg_url'] : false )
                            );

	                        /***** Let template to know about the thumbnail content type */
	                        Boombox_Template::set( 'bb_post_media_type', 'video' );
                        } else {
	                        list( $media_url, $media_width, $media_height ) = wp_get_attachment_image_src( $zombify_featured_media['media_id'], $size, false );
	                        Boombox_Template::set( 'bb_post_media_w', $media_width );
	                        Boombox_Template::set( 'bb_post_media_h', $media_height );

	                        $html = wp_get_attachment_image( $zombify_featured_media['media_id'], $size, false, $attr );
                        }
                        break;
                }

            } else {

                // keep compatibility for posts created with older versions of plugin
                $zombify_data = zf_decode_data( boombox_get_post_meta( $post_id, 'zombify_data' ) );

                zf_array_values( zf_array_values( $zombify_data['gif'] )[0]['image_image'] );

                $video_url = zf_array_values( zf_array_values( $zombify_data['gif'] )[0]['image_image'] )[0]['uploaded']['url'];
                if ( 'mp4' == pathinfo( $video_url, PATHINFO_EXTENSION ) ) {
                    $html = $this->zombify_video_layout( $html, $video_url, 'video/mp4', 0, $size );
                }

            }

        }

        /**
         * Replace post thumbnail HTML for "Video" format
         *
         * @param $html                 string          Current HTML
         * @param $post_id              int             Post ID
         * @param $post_thumbnail_id    int             Post thumbnail ID
         * @param $size                 string          Post thumbnail size
         * @param $attr                 string|array    Optional. Attributes for the image markup
         */
        private function post_thumbnail_fallback_for_video( &$html, $post_id, $post_thumbnail_id, $size, $attr ) {

            if ( $zombify_featured_media = boombox_get_post_meta( $post_id, 'zombify_featured_media' ) ) {

                $template = ( isset( $attr['template'] ) && $attr['template'] ) ? $attr['template'] : '';

                switch ( $zombify_featured_media['media_mime_type'] ) {

                    /***** media_type == "mp4" || media_type == "webm" ****/
                    case 'video/mp4':
                    case 'video/webm':
                        if( isset( $zombify_featured_media['media_url'] ) && $zombify_featured_media['media_url'] && boombox_is_video_mp4_allowed( $template ) ) {

                            $featured_image_style = '';
                            $featured_image_class = 'no-thumbnail';
                            $featured_image_src   = ( isset( $zombify_featured_media['zombify_jpeg_url'] ) && $zombify_featured_media['zombify_jpeg_url'] ) ? esc_url( $zombify_featured_media['zombify_jpeg_url'] ) : get_the_post_thumbnail_url( $post_id, 'full' );
                            if ( $featured_image_src ) {
                                $featured_image_style = sprintf( 'style="background-image:url(%s)"', $featured_image_src );
                                $featured_image_class = '';
                            }

                            $html = sprintf( '<div class="video-wrapper boombox-featured-video %s" %s>%s</div>',
                                $featured_image_class,
                                $featured_image_style,
                                boombox_get_html_video( $zombify_featured_media['media_url'], $featured_image_src )
                            );

	                        /***** Let template to know about the thumbnail content type */
                            Boombox_Template::set( 'bb_post_media_type', 'video' );
                        }
                        break;

                    /***** media_type == "embed" *****/
                    default:
                        if( boombox_is_video_embed_allowed( $template ) ) {

                            $embed_data = boombox_get_embed_video_data_from_url( htmlspecialchars_decode( $zombify_featured_media['media_url'] ) );

                            if ( ! empty( $embed_data ) ) {

                                switch( $embed_data['type'] ) {
                                    case 'facebook':
                                    case 'twitter':
                                        $params = array( 'video_url' => $embed_data['video_url'] );
                                        break;
                                    case 'vk':
                                        $params = array( 'id' => $embed_data['id'], 'oid' => $embed_data['oid'], 'hash' => $embed_data['hash'] );
                                        break;
                                    case 'twitch':
                                        $params = array( 'video_id' => $embed_data['video_id'], 'stream_type' => $embed_data['stream_type'] );
                                        break;
                                    default:
                                        $params = array( 'video_id' => $embed_data['video_id'] );
                                }

                                $classes = 'video-wrapper boombox-featured-embed bb-embed-' . $embed_data['type'];
                                if( ! has_post_thumbnail( $post_id ) ) {
	                                $classes .= ' no-thumbnail';
                                }
                                $html = sprintf( '<div class="%s">%s</div>',
	                                $classes,
                                    boombox_get_embed_html( $embed_data['type'], $params )
                                );

                                /***** Let template to know about the thumbnail content type */
	                            Boombox_Template::set( 'bb_post_media_type', 'video' );

                            }

                        }
                }
            }

        }

        /**
         * Replace post thumbnail HTML
         *
         * @param $html                 string          Current HTML
         * @param $post_id              int             Post ID
         * @param $post_thumbnail_id    int             Post thumbnail ID
         * @param $size                 string          Post thumbnail size
         * @param $attr                 string|array    Optional. Attributes for the image markup
         * @return mixed                string          Modified HTMl
         */
        public function post_thumbnail_fallback( $html, $post_id, $post_thumbnail_id, $size, $attr ) {
        	
            if( ! is_admin() && $post_thumbnail_id && ! boombox_is_nsfw_post( $post_id ) && ( $zombify_data_type = boombox_get_post_meta( $post_id, 'zombify_data_type' ) ) ) {

                if ( isset( $attr['play'] ) && $attr['play'] ) {

                    /***** zf_data_type = "gif" */
                    if ( 'gif' == $zombify_data_type ) {

                        $this->post_thumbnail_fallback_for_gif( $html, $post_id, $post_thumbnail_id, $size, $attr );

                        /***** zf_data_type = "video" */
                    } elseif ( 'video' == $zombify_data_type ) {

                       $this->post_thumbnail_fallback_for_video( $html, $post_id, $post_thumbnail_id, $size, $attr );

                    }

                }

            }

            return $html;

        }

	    /**
         * Prevent cloudconvert processing
         *
	     * @param bool $allow Current state
	     * @param WP_Post $attachment Attachment post object
	     * @param string $action Current action ( new | edit )
	     * @param string $input_format Input format
	     * @param string $output_format Output format
	     * @param string $unique_id Unique identifier
	     * @return bool
	     */
        public function cloudconvert_allow_processing( $allow, $attachment, $action, $input_format, $output_format, $unique_id ) {

            // do not process BBTE conveting if attachment will be handled via zombify
            if( boombox_get_post_meta( $attachment->ID, 'zf_attachment' ) ) {
                $allow = false;
            }
            return $allow;
        }

        /**
         * Edit post create button options
         *
         * @param array $args Current configuration
         * @return array
         */
        public function edit_post_create_button_args( $args ) {

            $args['url'] = '#';

            if( ! is_user_logged_in() && ( $key = array_search( 'js-authentication', $args['classes'] ) ) !== false ) {
                unset( $args['classes'][ $key ] );
            }

            $args['classes'][] = 'zf-create-popup';

            if( function_exists('prepare_popups_for_rendering') ){
                prepare_popups_for_rendering();
            }

            return $args;
        }

        /**
         * Force show thumbnail for "Gif" & "Video" data types, to make video layout work on listing
         *
         * @param bool $has_thumbnail Current state
         * @param null|int|WP_Post $post Post
         * @return bool
         */
        public function may_be_has_thumbnail_for_formats( $has_thumbnail, $post ) {

            if( ! $has_thumbnail && ! boombox_is_amp() ) {

                if( ! $post ) {
                    $post = get_post();
                } elseif( ! is_a( $post , 'WP_Post' ) ) {
                    $post = get_post( $post );
                }

                $zombify_data_type = boombox_get_post_meta( $post->ID, 'zombify_data_type' );
                if( in_array( $zombify_data_type, array( 'video', 'gif' ) ) ) {
                    $has_thumbnail = true;
                }
            }


            return $has_thumbnail;
        }

        /**
         * Notification settings for 'sf-submissions' screen
         */
        public function zf_submissions_component_notification_settings() {
            if ( ! $post_published = bp_get_user_meta( bp_displayed_user_id(), 'notification_zf_submission_post_published', true ) ) {
                $post_published = 'yes';
            }

            ?>
            <table class="notification-settings" id="zf-sumbissions-notification-settings">
                <thead>
                <tr>
                    <th class="title"><?php _e( 'Submissions', 'zombify' ) ?></th>
                    <th class="yes"><?php _e( 'Yes', 'buddypress' ) ?></th>
                    <th class="no"><?php _e( 'No', 'buddypress' )?></th>
                </tr>
                </thead>

                <tbody>

                <tr id="zf-sumbissions-notification-settings-post-published">
					<td><?php _e( 'Administrator publish your post', 'zombify' ); ?></td>
                    <td class="yes">
                        <label for="notification-zf-submission-new-post-published-yes" class="bbp-radio">
                            <input type="radio" name="notifications[notification_zf_submission_post_published]" id="notification-zf-submission-new-post-published-yes" value="yes" <?php checked( $post_published, 'yes', true ) ?>/>
                            <span class="bbp-radio-check"></span>
                            <span class="bp-screen-reader-text">
                            <?php
                            /* translators: accessibility text */
                            _e( 'Yes, send email', 'buddypress' );
                            ?></span>
                        </label>
                    </td>
                    <td class="no">
                        <label for="notification-zf-submission-new-post-published-no" class="bbp-radio">
                            <input type="radio" name="notifications[notification_zf_submission_post_published]" id="notification-zf-submission-new-post-published-no" value="no" <?php checked( $post_published, 'no', true ) ?>/>
                            <span class="bbp-radio-check"></span>
                            <span class="bp-screen-reader-text">
                            <?php
                            /* translators: accessibility text */
                            _e( 'No, do not send email', 'buddypress' );
                            ?></span>
                        </label>
                    </td>
                </tr>

                <?php

                /**
                 * Fires inside the closing </tbody> tag for zf-submissions screen notification settings.
                 *
                 * @since 1.2.0
                 */
                do_action( 'bp_zf_submission_screen_notification_settings' ) ?>
                </tbody>
            </table>
            <?php
        }

	    /**
	     * Render page content for zf pages
	     *
	     * @param bool $render Current status
	     *
	     * @return bool
	     */
	    public function render_page_content( $render ) {
		    if ( ! $render ) {
			    // Getting Zombify frontend page ID
			    $frontend_page_id = zf_get_option( "zombify_frontend_page" );
			    // Getting Zombify post create page ID
			    $post_create_page_id = zf_get_option( "zombify_post_create_page" );

			    if ( zf_same_page_or_translate( $frontend_page_id ) || zf_same_page_or_translate( $post_create_page_id ) ) {
				    $render = true;
			    }
		    }

		    return $render;
	    }

	    /**
		 * Prevent double featured media for single post
	     * @param string $media Current media
	     *
	     * @return string
	     */
	    public function edit_single_media_mix_content( $media ) {

		    $type = boombox_get_post_meta( get_the_ID(), 'zombify_data_type' );
		    while( true ) {
		    	if( in_array( $type, array( 'video', 'gif' ) ) ) {
				    $media = '';
				    break;
				}

				if( 'story' == $type ) {
		    		$sub_type = boombox_get_post_meta( get_the_ID(), 'zombify_data_subtype' );
		    		if( in_array( $sub_type, array( 'video', 'gif' ) ) ) {
					    $media = '';
					    break;
					}
				}

				break;
			}

	    	return $media;
		}

    }

	Boombox_Zombify::get_instance();
}