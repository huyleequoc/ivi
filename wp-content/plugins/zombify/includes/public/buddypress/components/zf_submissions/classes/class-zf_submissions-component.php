<?php
/**
 * BuddyPress Zombify Submissions Loader.
 *
 *
 * @package Zombify
 * @subpackage Buddypress Submissions
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if( ! class_exists( 'BP_Zombify_Submissions_Component' ) ) {

    class BP_Zombify_Submissions_Component extends BP_Component {

        /**
         * Start the members component creation process
         */
        function __construct() {

            parent::start(
                static::get_id(),
                __( 'Zombify Submissions', 'zombify' ),
                ZF_BP_COMPOMENTS_PATH . '/zf_submissions'
            );

        }
	
	    /**
	     * Get component ID
	     * @return string
	     */
        public static function get_id() {
        	return 'zf_submissions';
        }

	    /**
	     * Check if displayed user should have enabled current component
	     * @return bool
	     */
        private function check_displayed_user_role_access() {
			$user_id = bp_displayed_user_id();
			$has_access = true;
			if( $user_id && ! user_can( $user_id, 'edit_posts' ) ) {
				$has_access = false;
			}

			return $has_access;
        }
	
	    /**
	     * Get information about the component
	     * @return array
	     */
	    public static function get_component_info() {
		    return array(
			    'title'       => __( 'Zombify Submissions', 'zombify' ),
			    'description' => __( 'Lists posts created by users', 'zombify' )
		    );
	    }

        /**
         * Include files
         *
         * @param array $includes
         */
        function includes( $includes = array() ) {

            $includes = array(
                'functions',
                'template-functions',
                'actions',
                'filters'
            );
	        if( bp_is_active( 'notifications' ) ) {
		        $includes[] = 'notifications';
	        }

	        if( bp_is_active( 'activity' ) ) {
		        $includes[] = 'activity';
	        }
            parent::includes( $includes );
        }

        /**
         * Setup globals
         * @param array $args
         */
        function setup_globals( $args = array() ) {

	        if( ! $this->check_displayed_user_role_access() ) {
	        	return;
	        }

            // Define a slug, if necessary
            if ( !defined( 'BP_ZF_SUBMISSIONS_SLUG' ) ) {
                define( 'BP_ZF_SUBMISSIONS_SLUG', $this->id );
            }

            $globals = array(
                'slug' => BP_ZF_SUBMISSIONS_SLUG,
                'root_slug' => BP_ZF_SUBMISSIONS_SLUG,
                'has_directory' => false,
                'notification_callback' => 'bp_zf_submissions_format_notifications',
                'search_string' => __( 'Search Submissions...', 'zombify' ),
            );

            parent::setup_globals( $globals );

        }

        /**
         * Setup BuddyBar navigation
         * @param array $main_nav
         * @param array $sub_nav
         */
        function setup_nav( $main_nav = array(), $sub_nav = array() ) {

	        if( ! $this->check_displayed_user_role_access() ) {
		        return;
	        }

            $parent_slug = zf_submissions_get_slug();
            $parent_url = zf_submissions_get_page_url();

            $main_nav = array(
                'name'              => __( 'Submissions', 'zombify' ),
                'slug'              => $parent_slug,
                'position'          => 21
            );

            $sub_nav = array(
                array(
                    'name'              => __( 'Published', 'zombify' ),
                    'slug'              => zf_submissions_get_subpage_slug( 'published' ),
                    'parent_slug'       => $parent_slug,
                    'parent_url'        => $parent_url,
                    'position'          => 20,
                    'screen_function'   => 'zf_submissions_render_published_submissions',
                )
            );

            if( bp_displayed_user_id() == get_current_user_id() ) {
                $sub_nav = array_merge( $sub_nav, array(
                    array(
                        'name'              => __( 'All', 'zombify' ),
                        'slug'              => zf_submissions_get_subpage_slug( 'all' ),
                        'parent_slug'       => $parent_slug,
                        'parent_url'        => $parent_url,
                        'position'          => 10,
                        'screen_function'   => 'zf_submissions_render_all_submissions',
                    ),
                    array(
                        'name'              => __( 'Pending', 'zombify' ),
                        'slug'              => zf_submissions_get_subpage_slug( 'pending' ),
                        'parent_slug'       => $parent_slug,
                        'parent_url'        => $parent_url,
                        'position'          => 30,
                        'screen_function'   => 'zf_submissions_render_pending_submissions',
                    ),
                    array(
                        'name'              => __( 'Drafts', 'zombify' ),
                        'slug'              => zf_submissions_get_subpage_slug( 'draft' ),
                        'parent_slug'       => $parent_slug,
                        'parent_url'        => $parent_url,
                        'position'          => 40,
                        'screen_function'   => 'zf_submissions_render_draft_submissions',
                    ),
                    array(
                        'name'              => __( 'Add new post', 'zombify' ),
                        'slug'              => 'add-new-post',
                        'parent_slug'       => $parent_slug,
                        'parent_url'        => 'add-new-post',
                        'link'              => get_permalink( get_option( 'zombify_frontend_page' ) ),
                        'position'          => 50,
                        'screen_function'   => 'zf_submissions_render_add_new_post',
                    ),
                ) );
            }

            usort( $sub_nav, function ( $a, $b ) {
                return $a['position'] - $b['position'];
            });

            $zombify_nav = array(
                'main_nav'  => $main_nav,
                'sub_nav'   => $sub_nav
            );

            $zombify_nav = apply_filters('zombify_bp_navigation', $zombify_nav);

            if ( ! empty ( $zombify_nav['sub_nav'] ) ) {
                $zombify_nav['main_nav']['default_subnav_slug'] = $zombify_nav['sub_nav'][0]['slug'];
                $zombify_nav['main_nav']['screen_function'] = $zombify_nav['sub_nav'][0]['screen_function'];
            }

            parent::setup_nav( $zombify_nav['main_nav'], $zombify_nav['sub_nav'] );
        }

    }

}