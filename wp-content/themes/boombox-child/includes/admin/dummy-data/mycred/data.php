<?php
/**
 *  Provides ability to import MyCRED dummy data
 */

// Prevent direct script access.
if ( !defined('ABSPATH') ) {
    die('No direct script access allowed');
}

if ( !boombox_plugin_management_service()->is_plugin_active('mycred/mycred.php') ) {
    return;
}

class Boombox_Dummy_Data_MyCRED
{

    /**
     * Holds import action name
     * @var string
     * @since 2.5.0
     * @version 2.5.0
     */
    public static $action_import = 'import_mycred_data';

    /**
     * Get hooks dummy data
     * @return array
     * @since 2.5.0
     * @version 2.5.0
     */
    private static function _get_hooks_data()
    {
        return array(
            // Points for every X views | Gamify
            'gfy_prs_recurrent_post_views' => array(
                'views_amount' => 1,
                'creds' => 1,
                'log' => '%plural% for %count% views'
            ),

            // Points for trending post | Gamify
            'gfy_prs_trending_post' => array(
                'trending' => array(
                    'creds' => 100,
                    'log' => '%plural% for trending post'
                ),
                'hot' => array(
                    'creds' => 1000,
                    'log' => '%plural% for hot post'
                ),
                'popular' => array(
                    'creds' => 5000,
                    'log' => '%plural% for popular post'
                ),
            )
        );
    }

    /**
     * Get badges dummy data
     * @return array
     * @since 2.5.0
     * @version 2.5.0
     */
    private static function _get_badges_data()
    {
        return array(
            // Total Reads
            array(
                'post' => array(
                    'post_title' => 'Total Reads',
                    'menu_order' => 10
                ),
                'post_meta' => array(
                    // holds temp data and should not be imported.
                    '_tmp_data' => array(
                        'attachment_location' => 'badges/total-reads.svg',
                    ),

                    // main data
                    'main_image' => '',
                    'manual_badge' => 0,
                    'badge_prefs' => array(
                        array(
                            'attachment_id' => 0,
                            'image_url' => '',
                            'label' => 'Reached 5000 Reads in Total',
                            'compare' => 'AND',
                            'requires' => array(
                                array(
                                    'type' => 'mycred_default',
                                    'reference' => 'view_content',
                                    'amount' => 5000,
                                    'by' => 'sum'
                                )
                            ),
                            'reward' => array(
                                'type' => 'mycred_default',
                                'log' => '',
                                'amount' => 0
                            )
                        ),
                        array(
                            'attachment_id' => 0,
                            'image_url' => '',
                            'label' => 'Reached 15k Reads in Total',
                            'compare' => 'AND',
                            'requires' => array(
                                array(
                                    'type' => 'mycred_default',
                                    'reference' => 'view_content',
                                    'amount' => 15000,
                                    'by' => 'sum'
                                )
                            ),
                            'reward' => array(
                                'type' => 'mycred_default',
                                'log' => '',
                                'amount' => 0
                            )
                        ),
                        array(
                            'attachment_id' => 0,
                            'image_url' => '',
                            'label' => 'Reached 25k Reads in Total',
                            'compare' => 'AND',
                            'requires' => array(
                                array(
                                    'type' => 'mycred_default',
                                    'reference' => 'view_content',
                                    'amount' => 25000,
                                    'by' => 'sum'
                                )
                            ),
                            'reward' => array(
                                'type' => 'mycred_default',
                                'log' => '',
                                'amount' => 0
                            )
                        ),
                    )
                )
            ),
            // Top Author
            array(
                'post' => array(
                    'post_title' => 'Top Author',
                    'menu_order' => 10
                ),
                'post_meta' => array(
                    // holds temp data and should not be imported.
                    '_tmp_data' => array(
                        'attachment_location' => 'badges/top-author.svg',
                    ),

                    // main data
                    'main_image' => '',
                    'manual_badge' => 0,
                    'badge_prefs' => array(
                        array(
                            'attachment_id' => 0,
                            'image_url' => '',
                            'label' => 'MVP: Became a Top Author of the Month',
                            'compare' => 'AND',
                            'requires' => array(
                                array(
                                    'type' => 'mycred_default',
                                    'reference' => 'view_content_author',
                                    'amount' => '',
                                    'by' => 'count'
                                )
                            ),
                            'reward' => array(
                                'type' => 'mycred_default',
                                'log' => '',
                                'amount' => 0
                            )
                        ),
                    )
                )
            ),
            // Trending Posts
            array(
                'post' => array(
                    'post_title' => 'Trending Posts',
                    'menu_order' => 10
                ),
                'post_meta' => array(
                    // holds temp data and should not be imported.
                    '_tmp_data' => array(
                        'attachment_location' => 'badges/trending-posts.svg',
                    ),

                    // main data
                    'main_image' => '',
                    'manual_badge' => 0,
                    'badge_prefs' => array(
                        array(
                            'attachment_id' => 0,
                            'image_url' => '',
                            'label' => 'Trendsetter: Had a post that was trending',
                            'compare' => 'AND',
                            'requires' => array(
                                array(
                                    'type' => 'mycred_default',
                                    'reference' => 'gfy_prs_trending_post_trending_post',
                                    'amount' => 1,
                                    'by' => 'sum'
                                )
                            ),
                            'reward' => array(
                                'type' => 'mycred_default',
                                'log' => '',
                                'amount' => 0
                            )
                        ),
                    )
                )
            ),
            // Creating Memes
            array(
                'post' => array(
                    'post_title' => 'Creating Memes',
                    'menu_order' => 10
                ),
                'post_meta' => array(
                    // holds temp data and should not be imported.
                    '_tmp_data' => array(
                        'attachment_location' => 'badges/creating-memes.svg',
                    ),

                    // main data
                    'main_image' => '',
                    'manual_badge' => 0,
                    'badge_prefs' => array(
                        array(
                            'attachment_id' => 0,
                            'image_url' => '',
                            'label' => 'Meme Ninja: Created 13 Memes that were published',
                            'compare' => 'AND',
                            'requires' => array(
                                array(
                                    'type' => 'mycred_default',
                                    'reference' => 'publishing_content',
                                    'amount' => 5,
                                    'by' => 'count'
                                )
                            ),
                            'reward' => array(
                                'type' => 'mycred_default',
                                'log' => '',
                                'amount' => 0
                            )
                        ),
                    )
                )
            ),
            // Creating Polls
            array(
                'post' => array(
                    'post_title' => 'Creating Polls',
                    'menu_order' => 10
                ),
                'post_meta' => array(
                    // holds temp data and should not be imported.
                    '_tmp_data' => array(
                        'attachment_location' => 'badges/creating-polls.svg',
                    ),

                    // main data
                    'main_image' => '',
                    'manual_badge' => 0,
                    'badge_prefs' => array(
                        array(
                            'attachment_id' => 0,
                            'image_url' => '',
                            'label' => 'Poll Guru: Created 20 Polls that were published',
                            'compare' => 'AND',
                            'requires' => array(
                                array(
                                    'type' => 'mycred_default',
                                    'reference' => 'publishing_content',
                                    'amount' => 10,
                                    'by' => 'count'
                                )
                            ),
                            'reward' => array(
                                'type' => 'mycred_default',
                                'log' => '',
                                'amount' => 0
                            )
                        ),
                    )
                )
            ),
            // Creating Quizzes
            array(
                'post' => array(
                    'post_title' => 'Creating Quizzes',
                    'menu_order' => 10
                ),
                'post_meta' => array(
                    // holds temp data and should not be imported.
                    '_tmp_data' => array(
                        'attachment_location' => 'badges/creating-quizzes.svg',
                    ),

                    // main data
                    'main_image' => '',
                    'manual_badge' => 0,
                    'badge_prefs' => array(
                        array(
                            'attachment_id' => 0,
                            'image_url' => '',
                            'label' => 'Quiz Maker: Created 5 Quizzes that were published',
                            'compare' => 'AND',
                            'requires' => array(
                                array(
                                    'type' => 'mycred_default',
                                    'reference' => 'publishing_content',
                                    'amount' => 10,
                                    'by' => 'count'
                                )
                            ),
                            'reward' => array(
                                'type' => 'mycred_default',
                                'log' => '',
                                'amount' => 0
                            )
                        ),
                    )
                )
            ),
            // Creating Gifs
            array(
                'post' => array(
                    'post_title' => 'Creating Gifs',
                    'menu_order' => 10
                ),
                'post_meta' => array(
                    // holds temp data and should not be imported.
                    '_tmp_data' => array(
                        'attachment_location' => 'badges/creating-gifs.svg',
                    ),

                    // main data
                    'main_image' => '',
                    'manual_badge' => 0,
                    'badge_prefs' => array(
                        array(
                            'attachment_id' => 0,
                            'image_url' => '',
                            'label' => 'Gif King: Created 10 Gifs that were published',
                            'compare' => 'AND',
                            'requires' => array(
                                array(
                                    'type' => 'mycred_default',
                                    'reference' => 'publishing_content',
                                    'amount' => 10,
                                    'by' => 'count'
                                )
                            ),
                            'reward' => array(
                                'type' => 'mycred_default',
                                'log' => '',
                                'amount' => 0
                            )
                        ),
                        array(
                            'attachment_id' => 0,
                            'image_url' => '',
                            'label' => 'Gif King: Created 50 Gifs that were published',
                            'compare' => 'AND',
                            'requires' => array(
                                array(
                                    'type' => 'mycred_default',
                                    'reference' => 'publishing_content',
                                    'amount' => 50,
                                    'by' => 'count'
                                )
                            ),
                            'reward' => array(
                                'type' => 'mycred_default',
                                'log' => '',
                                'amount' => 0
                            )
                        ),
                    )
                )
            ),
            // Post Views
            array(
                'post' => array(
                    'post_title' => 'Post Views',
                    'menu_order' => 10
                ),
                'post_meta' => array(
                    // holds temp data and should not be imported.
                    '_tmp_data' => array(
                        'attachment_location' => 'badges/post-views.svg',
                    ),

                    // main data
                    'main_image' => '',
                    'manual_badge' => 0,
                    'badge_prefs' => array(
                        // level 1
                        array(
                            'attachment_id' => 0,
                            'image_url' => '',
                            'label' => 'Best-Seller: Had a post that reached 100 views',
                            'compare' => 'AND',
                            'requires' => array(
                                array(
                                    'type' => 'mycred_default',
                                    'reference' => 'gfy_prs_recurrent_post_views',
                                    'amount' => 100,
                                    'by' => 'count'
                                )
                            ),
                            'reward' => array(
                                'type' => 'mycred_default',
                                'log' => '',
                                'amount' => 0
                            )
                        ),
                        array(
                            'attachment_id' => 0,
                            'image_url' => '',
                            'label' => 'Best-Seller: Had a post that reached 1k views',
                            'compare' => 'AND',
                            'requires' => array(
                                array(
                                    'type' => 'mycred_default',
                                    'reference' => 'gfy_prs_recurrent_post_views',
                                    'amount' => 1000,
                                    'by' => 'count'
                                )
                            ),
                            'reward' => array(
                                'type' => 'mycred_default',
                                'log' => '',
                                'amount' => 0
                            )
                        ),
                        array(
                            'attachment_id' => 0,
                            'image_url' => '',
                            'label' => 'Best-Seller: Had a post that reached 5k views',
                            'compare' => 'AND',
                            'requires' => array(
                                array(
                                    'type' => 'mycred_default',
                                    'reference' => 'gfy_prs_recurrent_post_views',
                                    'amount' => 5000,
                                    'by' => 'count'
                                )
                            ),
                            'reward' => array(
                                'type' => 'mycred_default',
                                'log' => '',
                                'amount' => 0
                            )
                        ),
                    )
                )
            ),
            // Popular Posts
            array(
                'post' => array(
                    'post_title' => 'Popular Posts',
                    'menu_order' => 10
                ),
                'post_meta' => array(
                    // holds temp data and should not be imported.
                    '_tmp_data' => array(
                        'attachment_location' => 'badges/popular-posts.svg',
                    ),

                    // main data
                    'main_image' => '',
                    'manual_badge' => 0,
                    'badge_prefs' => array(
                        array(
                            'attachment_id' => 0,
                            'image_url' => '',
                            'label' => 'Headliner: Had a post that was popular',
                            'compare' => 'AND',
                            'requires' => array(
                                array(
                                    'type' => 'mycred_default',
                                    'reference' => 'gfy_prs_trending_post_popular_post',
                                    'amount' => 1,
                                    'by' => 'count'
                                )
                            ),
                            'reward' => array(
                                'type' => 'mycred_default',
                                'log' => '',
                                'amount' => 0
                            )
                        ),
                    )
                )
            ),
            // Featured Posts
            array(
                'post' => array(
                    'post_title' => 'Featured Posts',
                    'menu_order' => 10
                ),
                'post_meta' => array(
                    // holds temp data and should not be imported.
                    '_tmp_data' => array(
                        'attachment_location' => 'badges/featured-posts.svg',
                    ),

                    // main data
                    'main_image' => '',
                    'manual_badge' => 0,
                    'badge_prefs' => array(
                        array(
                            'attachment_id' => 0,
                            'image_url' => '',
                            'label' => 'Editor\'s Choice: Had a post that was featured on home page',
                            'compare' => 'AND',
                            'requires' => array(
                                array(
                                    'type' => 'mycred_default',
                                    'reference' => 'gfy_prs_featured_frontpage_post',
                                    'amount' => 1,
                                    'by' => 'count'
                                )
                            ),
                            'reward' => array(
                                'type' => 'mycred_default',
                                'log' => '',
                                'amount' => 0
                            )
                        ),
                        array(
                            'attachment_id' => 0,
                            'image_url' => '',
                            'label' => 'Editor\'s Choice: Had 5 posts that were featured on home page',
                            'compare' => 'AND',
                            'requires' => array(
                                array(
                                    'type' => 'mycred_default',
                                    'reference' => 'gfy_prs_featured_frontpage_post',
                                    'amount' => 5,
                                    'by' => 'count'
                                )
                            ),
                            'reward' => array(
                                'type' => 'mycred_default',
                                'log' => '',
                                'amount' => 0
                            )
                        )
                    )
                )
            ),
        );
    }

    /**
     * Get ranks dummy data
     * @return array
     * @since 2.5.0
     * @version 2.5.0
     */
    private static function _get_ranks_data()
    {
        return array(
            // Novice
            array(
                'post' => array(
                    'post_title' => 'Novice',
                    'menu_order' => 10
                ),
                'post_meta' => array(
                    // holds temp data and should not be imported.
                    '_tmp_data' => array(
                        'attachment_location' => 'ranks/novice.svg',
                    ),

                    '_thumbnail_id' => '',
                    'ctype' => 'mycred_default',
                    'gfy_rank_description' => 'You get points every time you submit a post, leave a comment, or interact with the site in other ways. When you get enough points, you\'ll hit the next level!',
                    'mycred_rank_min' => 0,
                    'mycred_rank_max' => 10000
                )
            ),
            // Explorer
            array(
                'post' => array(
                    'post_title' => 'Explorer',
                    'menu_order' => 10
                ),
                'post_meta' => array(
                    // holds temp data and should not be imported.
                    '_tmp_data' => array(
                        'attachment_location' => 'ranks/explorer.svg',
                    ),

                    '_thumbnail_id' => '',
                    'ctype' => 'mycred_default',
                    'gfy_rank_description' => 'You get points every time you submit a post, leave a comment, or interact with the site in other ways. When you get enough points, you\'ll hit the next level!',
                    'mycred_rank_min' => 10000,
                    'mycred_rank_max' => 20000,
                )
            ),
            // Senior
            array(
                'post' => array(
                    'post_title' => 'Senior',
                    'menu_order' => 10
                ),
                'post_meta' => array(
                    // holds temp data and should not be imported.
                    '_tmp_data' => array(
                        'attachment_location' => 'ranks/senior.svg',
                    ),

                    '_thumbnail_id' => '',
                    'ctype' => 'mycred_default',
                    'gfy_rank_description' => 'You get points every time you submit a post, leave a comment, or interact with the site in other ways. When you get enough points, you\'ll hit the next level!',
                    'mycred_rank_min' => 20000,
                    'mycred_rank_max' => 30000,
                )
            ),
            // Hero
            array(
                'post' => array(
                    'post_title' => 'Hero'
                ),
                'post_meta' => array(
                    // holds temp data and should not be imported.
                    '_tmp_data' => array(
                        'attachment_location' => 'ranks/hero.svg',
                    ),

                    '_thumbnail_id' => '',
                    'ctype' => 'mycred_default',
                    'gfy_rank_description' => 'You get points every time you submit a post, leave a comment, or interact with the site in other ways. When you get enough points, you\'ll hit the next rank!',
                    'mycred_rank_min' => 30000,
                    'mycred_rank_max' => 40000,
                )
            ),
            // Emperor
            array(
                'post' => array(
                    'post_title' => 'Emperor'
                ),
                'post_meta' => array(
                    // holds temp data and should not be imported.
                    '_tmp_data' => array(
                        'attachment_location' => 'ranks/emperor.svg',
                    ),

                    '_thumbnail_id' => '',
                    'ctype' => 'mycred_default',
                    'gfy_rank_description' => 'You get points every time you submit a post, leave a comment, or interact with the site in other ways. When you get enough points, you\'ll hit the next rank!',
                    'mycred_rank_min' => 40000,
                    'mycred_rank_max' => 70000,
                )
            ),
            // Legend
            array(
                'post' => array(
                    'post_title' => 'Legend'
                ),
                'post_meta' => array(
                    // holds temp data and should not be imported.
                    '_tmp_data' => array(
                        'attachment_location' => 'ranks/legend.svg',
                    ),

                    '_thumbnail_id' => '',
                    'ctype' => 'mycred_default',
                    'gfy_rank_description' => 'You get points every time you submit a post, leave a comment, or interact with the site in other ways. When you get enough points, you\'ll hit the next rank!',
                    'mycred_rank_min' => 70000,
                    'mycred_rank_max' => 100000,
                )
            ),
        );
    }

    /**
     * Import image
     * @param string $url Relative URL to image file
     * @param int $post_id Post ID to update image as attachment to
     * @return null|int Attachment ID
     * @since 2.5.0
     * @version 2.5.0
     */
    private static function _import_image( $url, $post_id )
    {
        require_once( ABSPATH . 'wp-admin/includes/media.php' );
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
        require_once( ABSPATH . 'wp-admin/includes/image.php' );

        $url = BOOMBOX_ADMIN_URL . 'dummy-data/mycred/images/' . $url;

        $attachment_id = null;
        $filename = download_url($url);
        if ( !is_wp_error($filename) ) {
            $attachment_id = media_handle_sideload(array(
                'name' => basename($url),
                'type' => 'image/jpg',
                'tmp_name' => $filename,
                'error' => 0,
                'size' => filesize($filename),
            ), $post_id);
        }

        return $attachment_id;
    }

    /**
     * Import hooks
     * @param bool $clear_existing Delete existing hooks
     * @since 2.5.0
     * @version 2.5.0
     */
    private static function _import_hooks( $clear_existing = false )
    {
        $option = get_option('mycred_pref_hooks');

        if ( $clear_existing ) {
            $option[ 'active' ] = array();
            $option[ 'hook_prefs' ] = array();
        }

        $hooks = static::_get_hooks_data();
        foreach ( $hooks as $id => $prefs ) {
            $option[ 'active' ][] = $id;
            $option[ 'hook_prefs' ][ $id ] = $prefs;
        }

        update_option('mycred_pref_hooks', $option);
    }

    /**
     * Import badges
     * @param bool $clear_existing Delete existing badges
     * @since 2.5.0
     * @version 2.5.0
     */
    private static function _import_bagdes( $clear_existing = false )
    {
        $post_type = 'mycred_badge';

        if ( $clear_existing ) {
            $args = array(
                'post_type' => $post_type,
                'posts_per_page' => -1,
            );
            $posts = get_posts($args);
            foreach ( $posts as $post ) {
                wp_delete_post($post->ID, true);
            }
        }

        $badges = static::_get_badges_data();
        foreach ( $badges as $badge_setup ) {

            $postarr = array_merge($badge_setup[ 'post' ], array( 'post_type' => 'mycred_badge', 'post_status' => 'publish' ));
            $post_id = wp_insert_post($postarr, true);
            if ( is_wp_error($post_id) ) {
                continue;
            }

            // try to manually upload attachment
            $attachment_id = static::_import_image($badge_setup[ 'post_meta' ][ '_tmp_data' ][ 'attachment_location' ], $post_id);
            unset($badge_setup[ 'post_meta' ][ '_tmp_data' ]);
            if ( $attachment_id ) {
                foreach ( $badge_setup[ 'post_meta' ][ 'badge_prefs' ] as $index => $pref ) {
                    $badge_setup[ 'post_meta' ][ 'badge_prefs' ][ $index ][ 'attachment_id' ] = $attachment_id;
                }
            }

            foreach ( $badge_setup[ 'post_meta' ] as $meta_key => $meta_value ) {
                update_post_meta($post_id, $meta_key, $meta_value);
            }
        }
    }

    /**
     * Import ranks
     * @param bool $clear_existing Delete existing ranks
     * @since 2.5.0
     * @version 2.5.0
     */
    private static function _import_ranks( $clear_existing = false )
    {
        $post_type = 'mycred_rank';

        if ( $clear_existing ) {
            $args = array(
                'post_type' => $post_type,
                'posts_per_page' => -1,
            );
            $posts = get_posts($args);
            foreach ( $posts as $post ) {
                wp_delete_post($post->ID, true);
            }
        }

        $ranks = static::_get_ranks_data();
        foreach ( $ranks as $rank_setup ) {
            $postarr = array_merge($rank_setup[ 'post' ], array( 'post_type' => 'mycred_rank', 'post_status' => 'publish' ));
            $post_id = wp_insert_post($postarr, true);
            if ( is_wp_error($post_id) ) {
                continue;
            }

            // try to manually upload attachment
            $attachment_id = static::_import_image($rank_setup[ 'post_meta' ][ '_tmp_data' ][ 'attachment_location' ], $post_id);
            unset($rank_setup[ 'post_meta' ][ '_tmp_data' ]);
            if ( $attachment_id ) {
                $rank_setup[ 'post_meta' ][ '_thumbnail_id' ] = $attachment_id;
            }

            foreach ( $rank_setup[ 'post_meta' ] as $meta_key => $meta_value ) {
                update_post_meta($post_id, $meta_key, $meta_value);
            }

        }
    }

    /**
     * Render section
     * @since 2.5.0
     * @version 2.5.0
     */
    public static function render_section()
    { ?>
        <table class="bb_status_table widefat" cellspacing="0" id="mycred-demo-import" style="table-layout: fixed">
            <thead>
            <tr>
                <th colspan="3"><h2><?php _e('MyCRED Data Import', 'boombox'); ?></h2></th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td colspan="2">
                    <p><strong><?php esc_html_e('Import MyCRED plugin dummy data', 'boombox'); ?></strong></p>
                </td>
                <td>
                    <form action="" method="post">
                        <input type="hidden" name="bb_action" value="<?php echo esc_attr(static::$action_import); ?>"/>
                        <?php wp_nonce_field('bb-import-mycred-data'); ?>

                        <p><strong><?php esc_html_e('Select content', 'boombox'); ?></strong></p>
                        <div>
                            <label for="bb-mycred-dummy-hooks">
                                <input type="hidden" name="bb[mycred][hooks]" value="0"/>
                                <input id="bb-mycred-dummy-hooks" type="checkbox" name="bb[mycred][hooks]" value="1"
                                       checked/>
                                <strong><?php esc_html_e('Hooks', 'boombox'); ?></strong>
                            </label>&nbsp;
                            <label for="bb-mycred-dummy-badges">
                                <input type="hidden" name="bb[mycred][badges]" value="0"/>
                                <input id="bb-mycred-dummy-badges" type="checkbox" name="bb[mycred][badges]" value="1"
                                       checked/>
                                <strong><?php esc_html_e('Badges', 'boombox'); ?></strong>
                            </label>&nbsp;
                            <label for="bb-mycred-dummy-ranks">
                                <input type="hidden" name="bb[mycred][ranks]" value="0"/>
                                <input id="bb-mycred-dummy-ranks" type="checkbox" name="bb[mycred][ranks]" value="1"
                                       checked/>
                                <strong><?php esc_html_e('Ranks', 'boombox'); ?></strong>
                            </label>
                        </div>
                        <hr/>
                        <div>
                            <label for="bb-mycred-dummy-cleanup">
                                <input type="hidden" name="bb[mycred][cleanup]" value="0"/>
                                <input id="bb-mycred-dummy-cleanup" type="checkbox" name="bb[mycred][cleanup]"
                                       value="1"/>
                                <strong><?php esc_html_e('Remove existing', 'boombox'); ?></strong>
                            </label>
                        </div>
                        <br/>
                        <button class="button-primary"><?php esc_html_e('Import Now', 'boombox'); ?></button>
                    </form>
                </td>
            </tr>
            </tbody>
        </table>
        <?php
    }

    /**
     * Handle import request
     * @since 2.5.0
     * @version 2.5.0
     */
    public static function handle_request()
    {
        check_admin_referer('bb-import-mycred-data');

        if ( !isset($_POST[ 'bb' ][ 'mycred' ]) ) {
            wp_nonce_ays('bb-invalid-request');
            die();
        }

        $post_data = $_POST[ 'bb' ][ 'mycred' ];
        if ( empty($post_data) ) {
            wp_nonce_ays('bb-invalid-request');
            die();
        }

        $importing = false;
        $cleanup = isset($post_data[ 'cleanup' ]) ? filter_var($post_data[ 'cleanup' ], FILTER_VALIDATE_BOOLEAN) : false;
        if ( isset($post_data[ 'hooks' ]) && filter_var($post_data[ 'hooks' ], FILTER_VALIDATE_BOOLEAN) ) {
            $importing = true;
            static::_import_hooks($cleanup);
        }

        if ( isset($post_data[ 'badges' ]) && filter_var($post_data[ 'badges' ], FILTER_VALIDATE_BOOLEAN) ) {
            $importing = true;
            static::_import_bagdes($cleanup);
        }

        if ( isset($post_data[ 'ranks' ]) && filter_var($post_data[ 'ranks' ], FILTER_VALIDATE_BOOLEAN) ) {
            $importing = true;
            static::_import_ranks($cleanup);
        }

        if ( $importing ) {

            add_action('admin_notices', function () {
                $class = 'notice notice-success is-dismissible';

                printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html__('Succesfully Imported', 'boombox'));
            });
        }
    }

}

add_action('boombox/theme_status_sections', array( 'Boombox_Dummy_Data_MyCRED', 'render_section' ));
add_action('boombox/theme_status_handle_' . Boombox_Dummy_Data_MyCRED::$action_import . '_request', array( 'Boombox_Dummy_Data_MyCRED', 'handle_request' ));