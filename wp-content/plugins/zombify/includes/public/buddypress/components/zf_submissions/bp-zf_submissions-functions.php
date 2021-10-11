<?php
/**
 * BuddyPress Zombify Submissions Component functions.
 *
 *
 * @package Zombify
 * @subpackage Buddypress Submissions
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Get component page slug
 *
 * @return mixed|void
 */
function zf_submissions_get_slug() {
    return apply_filters( 'zombify_bp_zumbissions_page_slug', 'submissions' );
}

/**
 * Get component sub-page slug
 * @param $subpage
 * @return mixed|void
 */
function zf_submissions_get_subpage_slug( $subpage ) {
    switch( $subpage ) {
        case 'all':
            $slug = 'all';
            break;
        case 'published':
            $slug = 'publish';
            break;
        case 'pending':
            $slug = 'pending';
            break;
        case 'draft':
            $slug = 'drafts';
            break;
        default:
            $slug = '';
    }

    return apply_filters( 'zf_bp_subpage_slug', $slug, $subpage );
}

/**
 * Get component page/sub-page URL
 *
 * @param string $sub_page
 * @return string
 */
function zf_submissions_get_page_url( $sub_page = '' ) {
    $sub_page = $sub_page ? $sub_page : '';
    $url = trailingslashit( bp_displayed_user_domain() . zf_submissions_get_slug() ) . $sub_page;

    return trailingslashit( $url );
}

/**
 * Get pagination query param
 * @return mixed|void
 */
function zf_submissions_get_pagination_query_param() {
    return apply_filters( 'zombify_bp_pagination_query_param', 'zp' );
}

/**
 * Get posts per page
 * @return mixed|void
 */
function zf_submissions_get_posts_per_page() {
    $per_page = absint( apply_filters( 'zombify_bp_posts_per_page', 10 ) );
    return $per_page ? $per_page : 1;
}

/**
 * Get paged param from query
 *
 * @return mixed
 */
function zf_submissions_get_paged() {
    return get_query_var( zf_submissions_get_pagination_query_param(), 1 );
}

/**
 * Get pagination for query
 *
 */
function zf_submissions_pagination() {
	
	global $wp_query;
	
	$html = apply_filters( 'zombify_bp_pagination_html', '<nav class="navigation pagination" role="navigation"><div class="nav-links">%s</div></nav>' );
	
	$pagination_query_args = array(
		'base' =>  @add_query_arg( zf_submissions_get_pagination_query_param(), '%#%' ),
		'format' => '',
		'current' => max( 1, $wp_query->get( 'paged' ) ),
		'total' => $wp_query->max_num_pages
	);
	$pagination_flexable_args = apply_filters( 'zombify_bp_pagination_args', array() );
	
	$pagination = sprintf( $html, paginate_links( array_merge( $pagination_query_args, $pagination_flexable_args ) ) );
	
	echo $pagination;
}

/**
 * Render a specific template
 *
 * @param $template
 * @param array $data
 * @return string
 */
function zf_submissions_render_view( $template, $data = array() ) {

    $path = apply_filters('zombify_bp_template_path', ( 'public/buddypress/' . $template . '.php' ) );
    $path = zombify()->locate_template( $path );

    if ( file_exists($path) ) {
        ob_start();

        foreach ((array)$data as $var => $value) {
            ${$var} = $value;
        }

        include $path;
        echo ob_get_clean();

    }

}