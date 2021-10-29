<?php
/**
 * Zombify Public Hooks
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

function zombify_start_session() {
	if ( session_status() == PHP_SESSION_NONE ) {
		session_start();
	}
}
add_action( 'wp', 'zombify_start_session', 1 );

$zombify_zfps = '';

/**
 * Init function for zombify
 */
function zombify_init() {
	global $zombify_zfps;
	global $zf_tags_limit;
	global $zf_excerpt_characters_limit;

	if ( isset( $_COOKIE["zombify_zfps"] ) ) {
		$zombify_zfps = $_COOKIE["zombify_zfps"];
		setcookie( "zombify_zfps", "", - 1, "/" );
	}

	$zf_config = zombify()->get_config();

	$zf_tags_limit               = apply_filters( 'zombify_tags_limit', zf_get_option( "zf_tags_limit", 3 ) );
	$zf_excerpt_characters_limit = apply_filters( 'zombify_excerpt_characters_limit', $zf_config['zf_excerpt_characters_limit'] );
}
add_action( 'init', 'zombify_init', 9 );

/**
 * Load CSS.
 */
function zombify_enqueue_styles() {

	$post_create_page_id = zf_get_option( "zombify_post_create_page" );
	$is_zf_backend_page  = is_page( $post_create_page_id );

	if ( $is_zf_backend_page ) {

		$enable_google_fonts = apply_filters( 'zombify_enable_google_fonts', true );

		if ( $enable_google_fonts ) {
			wp_enqueue_style( 'zombify-font-cabin', 'https://fonts.googleapis.com/css?family=Cabin|Open+Sans', array(), zombify()->get_plugin_data()->version );
		}

	}

	wp_enqueue_style( 'zombify-iconfonts', zombify()->assets_url . 'fonts/icon-fonts/icomoon/style.min.css', array(), zombify()->get_plugin_data()->version );
	wp_enqueue_style( 'zombify-style', zombify()->assets_url . 'css/zombify.min.css', array(), zombify()->get_plugin_data()->version );


	// Editor Styles
	wp_enqueue_style( 'zombify-froala-pkgd-css', zombify()->assets_url . 'js/plugins/froala-editor/css/froala_editor.pkgd.min.css', array(), zombify()->get_plugin_data()->version );
	wp_enqueue_style( 'zombify-froala-css', zombify()->assets_url . 'js/plugins/froala-editor/css/froala_style.min.css', array( 'zombify-froala-pkgd-css' ), zombify()->get_plugin_data()->version );
	wp_enqueue_style( 'zombify-froala-font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css', array( 'zombify-froala-css' ), zombify()->get_plugin_data()->version );
	// /END Editor Styles

	wp_enqueue_style( 'wp-mediaelement' );
	wp_enqueue_style( 'zombify-plugins-css', zombify()->assets_url . 'js/plugins/zombify-plugins.min.css', array(), zombify()->get_plugin_data()->version );
	wp_add_inline_style( 'zombify-plugins-css', zf_branding_color_css() );

	if ( is_rtl() ) {
		wp_enqueue_style( 'zombify-rtl', zombify()->assets_url . 'css/zombify-rtl.min.css', array( 'zombify-style' ), zombify()->get_plugin_data()->version );
	}

}
add_action( 'wp_enqueue_scripts', 'zombify_enqueue_styles', 9 );

/**
 * Load javascripts.
 */
function zombify_enqueue_scripts() {
	$zf_config = zombify()->get_config();

	$post                   = get_post();
	$locale                 = get_locale();
	$zf_froala_lang         = '';
	$post_create_page_id    = zf_get_option( "zombify_post_create_page" );
	$is_zf_backend_page     = is_page( $post_create_page_id );
	$zombify_post_meta      = '';
	$zombify_post_type_meta = '';
	$post_id                = null;

	if ( $is_zf_backend_page && isset( $_GET ) && isset( $_GET['post_id'] ) ) {
		$post_id = $_GET['post_id'];
	}

	if ( $post ) {
		$zombify_post_meta      = get_post_meta( $post->ID, 'zombify_data', true );
		$zombify_post_type_meta = get_post_meta( $post->ID, 'zombify_data_type', true );
	}

	$is_zombify_post = $post && $zombify_post_meta !== '';
	$is_openlist     = $is_zombify_post && $zombify_post_type_meta === 'openlist';
	$is_rankedlist   = $is_zombify_post && $zombify_post_type_meta === 'rankedlist';
	$is_list_item    = $post && $post->post_type == 'list_item';
	$text_on_meme    = esc_html__( "Thêm nội dung", 'zombify' );

	$translatable = array(
		"invalid_file_extension"  => esc_html__( "Sai định dạnh file. Định dạng chuẩn :", "zombify" ),
		"invalid_file_size"       => esc_html__( "Dung lượng file quá lớn. Dung lượng tối đa:", "zombify" ),
		"mb"                      => esc_html__( "MB", "zombify" ),
		"error_saving_post"       => esc_html__( "Có lỗi trong quá trình lưu. Thử lại.", "zombify" ),
		"processing_files"        => esc_html__( "Đang xử lý ...", "zombify" ),
		"uploading_files"         => esc_html__( "Đang tải lên ...", "zombify" ),
		"preview_alert"           => esc_html__( "Lưu bài!", "zombify" ),
		"meme"                    => array(
			"top_text"    => $text_on_meme,
			"bottom_text" => $text_on_meme
		),
		"incorrect_file_upload"   => esc_html__( "Tải tệp lên thất bại.", "zombify" ),
		"confirm_discard_virtual" => esc_html__( "Bạn chắc chắn thoát? Dữ liệu sẽ bị mất.", "zombify" ),
		"unknown_error"           => esc_html__( "Lỗi không xác định", "zombify" ),
		"publish_button"          => array(
			"publish"  => esc_html__( "Tải lên", "zombify" ),
			"schedule" => esc_html__( "Lên lịch", "zombify" )
		),
		"schedule_immediately"    => esc_html__( "immediately", "zombify" ),
		"schedule_stamp"          => array(
			"schedule_for" => esc_html__( "Lên lịch cho:", "zombify" ),
			"published_on" => esc_html__( "Đã tải lên lúc:", "zombify" ),
			"publish_on"   => esc_html__( "Tải lên lúc:", "zombify" ),
			"date_format"  => __( '%1$s %2$s, %3$s @ %4$s:%5$s' )
		),
        "saved" => esc_html__( "Đã lưu", "zombify" )
	);

	$zf_editor_settings       = apply_filters( 'zf-editor-settings', $zf_config['zf_editor'] );
	$zf_category_select_limit = apply_filters( 'zf-category-select-limit', zf_get_option( "zf_categories_limit", 3 ) );
	$wp_nonce                 = wp_create_nonce( 'media-form' );

	$zf_editor_image_upload_action = 'zf_froala_image';
	$zf_editor_image_upload_params = array(
		'action'   => $zf_editor_image_upload_action,
		'security' => $wp_nonce,
	);

	$zf_image_manager_load_params = array(
		'action'   => 'zf_load_user_uploaded_images',
		'security' => $wp_nonce,
	);

	$zf_max_upload_size    = zf_max_upload_size();
	$zf_max_upload_message = esc_html__( "FDung lượng file quá lớn. Dung lượng tối đa: ", "zombify" ) . round( ( $zf_max_upload_size > 0 ? $zf_max_upload_size / 1024 / 1024 : 0 ), 2 ) . esc_html__( "MB", "zombify" );

	wp_enqueue_script( 'zombify-main-js', zombify()->assets_url . 'js/minify/zombify-main-scripts.min.js', array( 'jquery' ), zombify()->get_plugin_data()->version );

	wp_localize_script( 'zombify-main-js', 'zf_main', array(
		'ajaxurl' => admin_url( 'admin-ajax.php' )
	) );

	if ( is_user_logged_in() ) {

		// Backend or "Open List" or List item
		if ( $is_zf_backend_page || $is_openlist || $is_list_item ) {

			wp_enqueue_script( 'zombify-resumable-js', zombify()->assets_url . 'js/plugins/resumable.min.js', array( 'jquery' ), zombify()->get_plugin_data()->version );

			if ( file_exists( zombify()->assets_dir . sprintf( 'js/plugins/froala-editor/js/languages/%s.js', strtolower( $locale ) ) ) ) {
				$zf_froala_lang = strtolower( $locale );
			} elseif ( file_exists( zombify()->assets_dir . sprintf( 'js/plugins/froala-editor/js/languages/%s.js', substr( strtolower( $locale ), 0, 2 ) ) ) ) {
				$zf_froala_lang = substr( strtolower( $locale ), 0, 2 );
			} else {
				$zf_froala_lang = 'en_gb';
			}

			wp_enqueue_script( 'jquery-ui-core' );
			wp_enqueue_script( 'jquery-ui-draggable' );
			wp_enqueue_script( 'jquery-ui-resizable' );
			wp_enqueue_script( 'jquery-ui-sortable' );
			wp_enqueue_script( 'jquery-ui-autocomplete' );

			wp_enqueue_script( 'zombify-editor-js', zombify()->assets_url . 'js/plugins/froala-editor/js/froala_editor.pkgd.min.js', array( 'jquery' ), zombify()->get_plugin_data()->version );
			wp_add_inline_script( 'zombify-editor-js', 'try{(function (k){localStorage.FEK=k;t=document.getElementById("fr-fek");t.parentNode.removeChild(t);})("GIBEVFBOHF1c1UNYVM==")}catch(e){}', 'before' );
			wp_enqueue_script( 'zombify-editor-lang', zombify()->assets_url . 'js/plugins/froala-editor/js/languages/' . $zf_froala_lang . '.js', array( 'jquery' ), zombify()->get_plugin_data()->version );
			wp_enqueue_script( 'zombify-back-js', zombify()->assets_url . 'js/minify/zombify-back-scripts.min.js', array( 'jquery' ), zombify()->get_plugin_data()->version, true );

			wp_localize_script( 'zombify-back-js', 'zf_back', array(
				'translatable'                        => $translatable,
				'locale'                              => $locale,
				'ajaxurl'                             => admin_url( 'admin-ajax.php' ),
				'zf_editor_settings'                  => $zf_editor_settings,
				'zf_editor_image_upload_params'       => $zf_editor_image_upload_params,
				'zf_editor_image_manager_load_params' => $zf_image_manager_load_params,
				'zf_editor_image_upload_action'       => admin_url( 'admin-ajax.php' ) . '?action=' . $zf_editor_image_upload_action,
				'zf_category_select_limit'            => $zf_category_select_limit,
				'upload_url'                          => admin_url( 'async-upload.php' ),
				'chunkSize'                           => zombify()->get_chunk_file_size(),
				'currect_user_id'                     => get_current_user_id(),
				'zf_froala_lang'                      => $zf_froala_lang,
				'zf_max_upload_size'                  => $zf_max_upload_size,
				'zf_max_upload_message'               => $zf_max_upload_message,
			) );

		}

	}

	// Zombify all pages
	if ( $is_zf_backend_page || $is_zombify_post ) {

		wp_enqueue_script( 'wp-mediaelement' );
		wp_enqueue_script( 'zombify-common-js', zombify()->assets_url . 'js/minify/zombify-common-scripts.min.js', array(
			'jquery',
			'wp-mediaelement'
		), zombify()->get_plugin_data()->version );

		wp_localize_script( 'zombify-common-js', 'zf', array(
			'translatable'  => $translatable,
			'ajaxurl'       => admin_url( 'admin-ajax.php' ),
			'post_id'       => $post_id,
			'fetching_text' => esc_html__( "Đang tải bản xem trước", "zombify" )
		) );

	}

	// Open list or Ranked list or List item
	if ( $is_openlist || $is_rankedlist || $is_list_item ) {
		wp_enqueue_script( 'zombify-comments-js', zombify()->assets_url . 'js/minify/zombify-comments.min.js', array( 'comment-reply' ), zombify()->get_plugin_data()->version, true );
	}

}
add_action( 'wp_enqueue_scripts', 'zombify_enqueue_scripts', 9 );

add_filter( 'wp_ajax_zf_froala_image', 'zf_froala_image_uploaded_response' );
function zf_froala_image_uploaded_response( $id ) {
	if ( isset( $_FILES ) && isset( $_FILES['file'] ) ) {

		$f = array(
			"name"     => $_FILES['file']['name'],
			"type"     => $_FILES['file']['type'],
			"size"     => $_FILES['file']['size'],
			"tmp_name" => $_FILES['file']['tmp_name'],
		);

		if ( $_FILES['file']['tmp_name'] != '' ) {

			$f = zf_get_file_upload( $f );

			if ( isset( $f["uploaded"]["error"] ) && $f["uploaded"]["error"] != '' ) {
				unset( $f );
			}

			unset( $f["tmp_name"] );

			$attach_data = zf_insert_attachment( 0, $f["uploaded"]["file"], $f['type'] );
			$attach_id   = $attach_data["id"];
			$res         = false;
			$url         = wp_get_attachment_url( $attach_id );

			add_post_meta( $attach_id, "zf_attachment_froala", "1", true );

			if ( $url ) {
				$res = wp_json_encode( array(
					'link' => $url,
					'id'   => $attach_id
				) );
			}

			echo $res;
			die();

		}
	}
}

function zf_load_user_uploaded_images_callback() {
	$user_id = get_current_user_id();
	$images  = array();

	$query_args = array(
		'author'         => $user_id,
		'post_type'      => 'attachment',
		'post_status'    => 'inherit',
		'post_parent'    => 0,
		'posts_per_page' => 100,
		'meta_query'     => array(
			array(
				'key'     => 'zf_attachment_froala',
				'value'   => '1',
				'compare' => '=',
			)
		)
	);

	$media = get_posts( $query_args );

	if ( count( $media ) > 0 ) {
		foreach ( $media as $image ) {
			if( $image_data = wp_get_attachment_image_src( $image->ID, 'full' ) ) {
				$images[] = array(
					'url'         => $image_data[0],
					'zf_media_id' => $image->ID,
				);
			}
		}
		echo wp_json_encode( $images );
	}
	exit;
}
add_action( 'wp_ajax_zf_load_user_uploaded_images', 'zf_load_user_uploaded_images_callback' );

if ( ! function_exists( "zombify_public_frontend_page_func" ) ) {
	// Global variable for zombify frontend content
	global $zombify_frontend_content;

	/**
	 * Zombify public page controller
	 */
	function zombify_public_frontend_page_func() {
		global $zombify_frontend_content, $post;
		$content = '';
		//Getting Zombify frontend page ID
		$frontend_page_id = zf_get_option( "zombify_frontend_page" );
		//Getting Zombify post create page ID
		$post_create_page_id = zf_get_option( "zombify_post_create_page" );

		if ( $frontend_page_id && zf_same_page_or_translate( $frontend_page_id ) ) {
			$content = zombify_public_frontend_controller( "index" );
		}

		if ( $post_create_page_id && zf_same_page_or_translate( $post_create_page_id ) ) {
			if ( zf_user_can_create() ) {
				$content = zombify_public_frontend_controller( "", "create" );
			} else {
				$content = zombify_public_frontend_controller( "", "permissiondenied" );
			}
		}

		if ( $post && $post->post_type == 'list_item' ) {
			$content = zombify_public_frontend_controller( "", "subpost" );
		}

		$zombify_frontend_content .= $content;
	}

}
add_filter( 'wp', 'zombify_public_frontend_page_func' );

function zombify_title_filter( $title, $id = null ) {

	$post = get_post();

	if ( $post && $post->ID == $id && $post->post_type == 'list_item' && $post->post_status == 'publish' ) {
		$parent_post_data = zf_decode_data( get_post_meta( $post->post_parent, 'zombify_data', true ) );

		$num = 0;
		$post_ids = array();
		if ( isset( $parent_post_data["list"] ) && is_array( $parent_post_data["list"] ) ) {
			foreach ( $parent_post_data["list"] as $pdata ) {
				$post_ids[] = $pdata["post_id"];
			}
		}

		$args = array(
			'post__in'       => $post_ids,
			'post_type'      => 'list_item',
			'post_status'    => 'publish',
			"posts_per_page" => - 1
		);

		$posts = get_posts( $args );

		if ( isset( $parent_post_data["list"] ) && is_array( $parent_post_data["list"] ) ) {
			foreach ( $parent_post_data["list"] as $pdata ) {

				$st = '';
				foreach ( $posts as $p ) {
					if ( $p->ID == $pdata["post_id"] ) {
						$st = $p->post_status;
					}
				}

				if ( $st != 'publish' ) {
					continue;
				}

				$num ++;
				if ( $pdata["post_id"] == $post->ID ) {
					break;
				}
			}
		}

		$title .= ' (' . $num . '/' . count( $posts ) . ')';
	}

	return $title;
}
add_filter( 'the_title', 'zombify_title_filter', 10, 2 );

if ( ! function_exists( "zombify_public_frontend_page_content" ) ) {

	/**
	 * Zombify public page content
	 */
	function zombify_public_frontend_page_content( $content ) {
		global $zombify_frontend_content;

		return $content . $zombify_frontend_content;
	}


}
/*
increase zombify_public_frontend_page_content priority to disable of rendering of shortcodes in zombify content
by default, WordPress executes do_shortcode with priority 11
some plug-ins are running do_shorcode manually with higher priority.
*/
add_filter( 'the_content', 'zombify_public_frontend_page_content', 99, 1 );

if ( ! function_exists( "zombify_save_ajax" ) ) {

	/**
	 * Zombify save ajax function
	 */
	function zombify_save_ajax() {
		echo zombify_public_frontend_controller(); exit;
	}

}
add_action( 'wp_ajax_zombify_save', 'zombify_save_ajax' );

if ( ! function_exists( "zombify_video_upload" ) ) {

	/**
	 * Zombify save ajax function
	 */
	function zombify_video_upload() {
		echo zombify_public_frontend_controller(); exit;
	}

}
add_action( 'wp_ajax_zombify_video_upload', 'zombify_video_upload' );

if ( ! function_exists( "zombify_virtual_save_ajax" ) ) {

	/**
	 * Zombify save ajax function
	 */
	function zombify_virtual_save_ajax() {

		if ( isset( $_GET["addit_action"] ) ) {

			switch ( $_GET["addit_action"] ) {
				case 'zf_delete_media':
					zf_delete_media();
					break;
			}

		}

		echo zombify_public_frontend_controller(); exit;
	}

}

add_action( 'wp_ajax_zombify_virtual_save', 'zombify_virtual_save_ajax' );

if ( ! function_exists( "zombify_poll_vote_ajax" ) ) {

	/**
	 * Zombify save ajax function
	 */
	function zombify_poll_vote_ajax() {
		echo zombify_public_frontend_controller(); exit;
	}

}
add_action( 'wp_ajax_zombify_poll_vote', 'zombify_poll_vote_ajax' );
add_action( 'wp_ajax_nopriv_zombify_poll_vote', 'zombify_poll_vote_ajax' );

if ( ! function_exists( "zombify_post_shortcode" ) ) {

	function zombify_post_shortcode( $atts ) {

		$current_post_id = get_the_ID();

		$postsavetype = get_post_meta( $current_post_id, 'zombify_postsave_type', true );
		if ( ! $postsavetype ) {
			$postsavetype = 'shortcode';
		}

		$html = '';
		if ( ( $postsavetype == 'shortcode' ) && apply_filters( 'zombify_allow_shortcode_process', true ) ) {

			if ( $post_type = get_post_meta( $current_post_id, 'zombify_data_type', true ) ) {

				$zf_shortcode_args = shortcode_atts( array(
					'zf_page'           => '1',
					'zf_items_per_page' => '0'
				), $atts, 'zombify_post' );

				$zf_openlist_errors = array();

				zombify_frontend_save_post( $zf_openlist_errors );
				$data = zf_decode_data( get_post_meta( $current_post_id, 'zombify_data', true ) );
				$template_file = zombify()->locate_template( zombify()->quiz_view_dir( strtolower( $post_type ) . '.php' ) );

				ob_start();
				include $template_file;
				$output = ob_get_clean();
				$html   = do_shortcode( $output );
			}
		}

		return $html;

	}

}
add_shortcode( 'zombify_post', 'zombify_post_shortcode' );

if ( ! function_exists( "zombify_post_view" ) ) {

	function zombify_post_view( $content ) {
		$current_post_id = get_the_ID();
		$post_type       = get_post_meta( $current_post_id, 'zombify_data_type', true );

		if ( is_single() && $post_type ) {
			if ( ! has_shortcode( $content, 'zombify_post' ) ) {
				$postsavetype = get_post_meta( $current_post_id, 'zombify_postsave_type', true );
				if ( ! empty( $postsavetype ) && $postsavetype == 'meta' ) {
					$zf_openlist_errors = array();
					zombify_frontend_save_post( $zf_openlist_errors );
					$data          = zf_decode_data( get_post_meta( $current_post_id, 'zombify_data', true ) );
					$template_file = zombify()->locate_template( zombify()->quiz_view_dir( strtolower( $post_type ) . '.php' ) );
					ob_start();
					include $template_file;
					$output = ob_get_contents();
					ob_end_clean();
					$content .= $output;
				}
			}
			//if it's zobmfiy post single - add wrapper for Froala editor
			$content = "<div class=\"fr-view\">" . $content . "</div>";
		}

		return $content;
	}

}
add_filter( 'the_content', 'zombify_post_view' );

if ( ! function_exists( "zombify_custom_post_types" ) ) {

	function zombify_custom_post_types() {
		register_post_type( 'list_item',
			array(
				'labels'      => array(
					'name'          => esc_html__( 'Danh sách', 'zombify' ),
					'singular_name' => esc_html__( 'Danh sách', 'zombify' ),
				),
				'public'      => true,
				'has_archive' => true,
				'supports'    => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' ),
				'exclude_from_search' => true
			)
		);

		register_post_type( 'zf_vpost',
			array(
				'public'      => false,
				'has_archive' => true,
				'supports'    => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' )
			)
		);
	}

}
add_action( 'init', 'zombify_custom_post_types', 10 );

if ( ! function_exists( "zombify_add_new_image_sizes" ) ) {

	function zombify_add_new_image_sizes() {
		add_image_size( 'zombify_small', 350, 350, true );
	}

}
add_action( 'init', 'zombify_add_new_image_sizes', 10 );


if ( ! function_exists( "zombify_toolbar_link" ) ) {

	function zombify_toolbar_link( $wp_admin_bar ) {
		global $pagenow, $post;

		if ( is_user_logged_in() ) {

			$args = array(
				'id' => 'zombify-frontend-page',
				'title' => esc_html__( 'Zombify', 'zombify' ),
				'href' => get_permalink( zf_get_option( "zombify_frontend_page" ) ),
				'meta' => array(
					'title' => esc_html__( 'Zombify', 'zombify' ),
				)
			);
			$wp_admin_bar->add_node( $args );

		}

		if ( ( ( ! is_admin() && is_single() ) || ( is_admin() && $pagenow == 'post.php' ) ) && is_user_logged_in() && ( get_post_meta( get_the_ID(), 'zombify_data_type', true ) || $post->post_type == 'list_item' ) != '' && zf_user_can_edit( get_the_ID() ) ) {

			$args = array(
				'id' => 'zombify-edit-post',
				'title' => esc_html__( 'Sửa với Zombify', 'zombify' ),
				'href' => add_query_arg( array(
					'action'  => 'update',
					'post_id' => $post->post_type == 'list_item' ? $post->post_parent : get_the_ID()
				), get_permalink( zf_get_option( "zombify_post_create_page" ) ) ),
				'meta' => array(
					'title' => esc_html__( 'Sửa với Zombify', 'zombify' ),
				)
			);

			$wp_admin_bar->add_node( $args );
		}
	}

}
add_action( 'admin_bar_menu', 'zombify_toolbar_link', 999 );

if ( ! function_exists( "zombify_post_update" ) ) {

	function zombify_post_update( $post_id ) {

		if ( $data = get_post_meta( $post_id, 'zombify_data', true ) ) {

			$post              = get_post( $post_id );
			$post_thumbnail_id = get_post_thumbnail_id( $post_id );

			$post_data = zf_decode_data( $data );

			$post_data["title"] = $post->post_title;
			// $post_data["description"] = $post->post_excerpt;
			if ( $post_thumbnail_id ) {

				$zombify_small_image = wp_get_attachment_image_src( $post_thumbnail_id, 'zombify_small' );
				$attachment_post     = get_post( $post_thumbnail_id );

				$file = get_attached_file( $post_thumbnail_id );

				$image    = array();
				$image[0] = array(
					"name"          => pathinfo( $file, PATHINFO_BASENAME ),
					"type"          => $attachment_post->post_mime_type,
					"size"          => "",
					"attachment_id" => $post_thumbnail_id,
					"uploaded"      => array(
						"file" => $file,
						"url"  => get_the_post_thumbnail_url( $post_id ),
						"type" => $attachment_post->post_mime_type,
					),
					"size_urls"     => array(
						"zombify_small" => isset( $zombify_small_image[0] ) ? $zombify_small_image[0] : ''
					)
				);

			} else {
				$image = array();
			}

			$post_data["image"] = $image;

			update_post_meta( $post_id, "zombify_data", zf_encode_data( $post_data ) );
		}
	}

}
add_action( 'save_post', 'zombify_post_update' );

if ( ! function_exists( "zombify_get_tags_ajax" ) ) {

	/**
	 * Zombify get all tags ajax function
	 */
	function zombify_get_tags_ajax() {

		$tags = get_tags( array(
			'name__like' => ( isset( $_GET["term"] ) ? $_GET["term"] : '' )
		) );

		$json = array();
		foreach ( $tags as $tag ) {
			$json[] = $tag->name;
		}

		echo json_encode( $json ); exit;
	}

}
add_action( 'wp_ajax_zombify_get_tags', 'zombify_get_tags_ajax' );


if ( ! function_exists( 'zombify_render_flash_popup' ) ) {

	/**
	 * Render flash popup to notify about submission progress
	 * @param int $post_id The post ID
	 * @param string $status The post submission status
	 */
	function zombify_render_flash_popup( $post_id, $status ) {

		if ( zf_get_option( 'zf_disable_popup_publish', 0 ) == 1 ) {
			return;
		}

		if( ! in_array( $status, array( 'pending', 'publish', 'future' ) ) ) {
			return;
		}

		$post = get_post( $post_id );
		$popup_class = 'zombify-submit-popup zf-open';
		if( 'pending' == $status ) {
			$title = __( 'Cảm ơn đã đóng góp!', 'zombify' );
			$content = __( "Sản phẩm của bạn đang chờ kiểm duyệt. Bạn sẽ được thông báo ngay sau khi nội dung gửi của bạn được chấp thuận.", 'zombify' );
			$buttons = array( 'share' => false, 'create' => true );
		} elseif( 'publish' == $status ) {
			$popup_class .= ' zombify-congrads-popup';
			$title = esc_html__( 'Chúc mừng!', 'zombify' );
			$content = sprintf( '%1$s<br>%2$s', esc_html__( 'Mục của bạn hiện đã được xuất bản.', 'zombify' ), esc_html__( "Đừng quên chia sẻ nó với bạn bè của bạn.", 'zombify' ) );
			$buttons = array( 'share' => true, 'create' => false );
		} elseif( 'future' == $status ) {
			$popup_class .= ' zombify-congrads-popup';
			$title = esc_html__( 'Chúc mừng!', 'zombify' );
			$content = apply_filters( 'zf-publish-popup-content', sprintf( esc_html__( 'Mục của bạn đã được lên lịch thành công cho: %s.', 'zombify' ), '<br/><strong>' . date_i18n( __( 'M j, Y @ H:i' ), strtotime( $post->post_date ) ) . '</strong>' ) );
			$buttons = array( 'share' => true, 'create' => false );
		}

		$title = apply_filters( 'zf-publish-popup-title', $title, $post, $status );
		$content = apply_filters( 'zf-publish-popup-content', $content, $post, $status );
		$buttons[ 'share' ] = apply_filters( 'zf-publish-popup-render-share-buttons', $buttons[ 'share' ], $post, $status );
		$buttons[ 'create' ] = apply_filters( 'zf-publish-popup-render-create-buttons', $buttons[ 'create' ], $post, $status ); ?>

		<div class="<?php echo esc_attr( $popup_class ); ?>">
			<div class="zombify-popup_body">
				<a class="zf-popup_close" href="#"><i class="zf-icon zf-icon-delete"></i></a>
				<div class="zf-content">
					<div class="zf-head"><i class="zf-icon zf-icon-check"></i></div>
					<div class="zf-inner">

						<div class="zf-inner_text">
							<?php if ( $title ) { ?>
								<div class="h4"><?php echo esc_html( $title ); ?></div>
							<?php }
							if ( $content ) { ?>
								<div class="zf-text"><?php echo $content; ?></div>
							<?php } ?>
						</div>

						<?php if( $buttons[ 'share' ] || $buttons[ 'create' ] ) { ?>
							<div class="zf-btn-group">
								<?php if ( $buttons['share'] && defined( 'BOOMBOX_THEME_PATH' ) ) { ?>
									<div class="zf-share_text"><?php esc_html_e( "Chia sẻ", "zombify" ); ?></div>
									<div class="zf-share_box">

										<?php
										$has_share_buttons = ( boombox_plugin_management_service()->is_plugin_active( 'mashsharer/mashshare.php' ) || function_exists( 'essb_core' ) );

										//Dynamic buttons
										if ( $has_share_buttons ) {
											$post_share_buttons_html_full = boombox_get_post_share_buttons_html();

											//Find and remove the <li> tag which contains the Total shares count info
											$pattern                          = '%<li.*class=\".*essb_totalcount_item.*?\<\/li\>%m';
											$post_share_buttons_html_replaced = preg_replace( $pattern, '', $post_share_buttons_html_full );

											echo $post_share_buttons_html_replaced;
										} else { ?>

											<a class="zf-share zf_twitter" target='_blank'
											   href="https://twitter.com/intent/tweet?text=<?php print( urlencode( get_the_title( $post ) ) ); ?>+<?php print( urlencode( get_permalink( $post ) ) ); ?>">
												<i class="zf-icon zf-icon-twitter"></i><?php esc_html_e( 'Twitter', 'zombify' ); ?>
											</a>
											<a class="zf-share zf_facebook" target='_blank'
											   href="http://www.facebook.com/share.php?u=<?php print( urlencode( get_permalink( $post ) ) ); ?>&title=<?php print( urlencode( get_the_title( $post ) ) ); ?>">
												<i class="zf-icon zf-icon-facebook"></i><?php esc_html_e( 'Facebook', 'zombify' ); ?>
											</a>

										<?php } ?>

									</div>

								<?php }
								if ( $buttons['create'] ) { ?>
									<a class="zf-btn zf-create"
									   href="<?php echo esc_url( get_permalink( zf_get_option( 'zombify_frontend_page' ) ) ); ?>"><i
												class="zf-icon zf-icon-add"></i><?php esc_html_e( 'Tạo thêm', 'zombify' ); ?>
									</a>
								<?php } ?>

							</div>

						<?php }

						$zombify_logo = zf_get_option( 'zombify_logo', zombify()->options_defaults[ 'zombify_logo' ] );
						if ( $zombify_logo ) { ?>
							<div class="zf-footer">
								<span class="zombify-logo"><img src="<?php echo esc_url( $zombify_logo ); ?>" alt="zombify-logo"></span>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

}

if ( ! function_exists( 'zombify_configure_flash_popups' ) ) {

	/**
	 * Configure flash popups
	 */
	function zombify_configure_flash_popups() {
		global $zombify_zfps;
		$post_id = get_the_ID();

		if ( isset( $zombify_zfps ) && $zombify_zfps != '' && is_single() && get_post_meta( $post_id, 'zombify_data_type', true ) ) {

			$zfps = $zombify_zfps;
			$zombify_zfps = '';

			zombify_render_flash_popup( $post_id, $zfps );
		}
	}

}
add_action( 'wp_footer', 'zombify_configure_flash_popups' );

function zombify_render_meme_popup() {
	$post_create_page_id    = zf_get_option( "zombify_post_create_page" );
	$is_zf_backend_page     = is_page( $post_create_page_id );
	$disable_meme_templates = zf_get_option( 'zombify_disable_meme_templates', 0 );

	if ( $is_zf_backend_page && isset( $_GET ) && isset( $_GET['type'] ) && $_GET['type'] === 'meme' && ! $disable_meme_templates ) { ?>

		<div class="zombify-submit-popup zombify-meme-popup">
			<div class="zombify-popup_body">
				<a class="zf-popup_close" href="#"><i class="zf-icon zf-icon-delete"></i></a>
				<div class="zf-content">
					<div class="zf-inner">
						<div class="images-cont">
							<?php
							$zombify_meme_templates_folder_arr = zf_get_meme_templates_folder();
							$zombify_meme_templates_dir        = $zombify_meme_templates_folder_arr['zombify_meme_templates_dir'];
							$zombify_meme_templates_url        = $zombify_meme_templates_folder_arr['zombify_meme_templates_url'];

							$directory = $zombify_meme_templates_dir . '*';
							$images    = glob( $directory );

							foreach ( $images as $image ) {
								$extension = pathinfo( $image, PATHINFO_EXTENSION );

								if ( in_array( strtolower( $extension ), array( "jpg", "jpeg", "gif", "png" ) ) ) {

									$path_array         = explode( '/', $image );
									$image_name         = end( $path_array );
									$name_array         = explode( '.', $image_name );
									$image_display_name = $name_array[0];
									$image_display_name = str_replace( '_', ' ', $image_display_name ); ?>

									<div class="single-image" data-url="<?php echo $zombify_meme_templates_url . $image_name; ?>" style="background-image: url('<?php echo $zombify_meme_templates_url . $image_name; ?>');">
										<div class="meme-title"><?php echo $image_display_name; ?></div>
									</div>

								<?php } ?>
							<?php } ?>
						</div>
						<?php
						$zombify_logo = zf_get_option( 'zombify_logo', zombify()->options_defaults["zombify_logo"] );
						if ( $zombify_logo ):
							?>
							<div class="zf-footer">
								<span class="zombify-logo"><img src="<?php echo $zombify_logo; ?>" alt="zombify-logo"></span>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	<?php } ?>
<?php }
add_action( 'wp_footer', 'zombify_render_meme_popup', 11 );
/**
 * End of Zombify flush popups
 */


if ( ! function_exists( 'zombify_render_popups' ) ) {

	function zombify_render_popups() {

		$zombify_active_formats = zombify()->get_active_formats();
		$zombify_active_formats = array_filter( $zombify_active_formats );

		$post_types = zombify()->get_active_post_types();

		$popup = '<div class="zombify-create-popup">
            <div class="zombify-popup_body">
                <a class="zf-popup_close" href="#"><i class="zf-icon zf-icon-delete"></i></a>
                <div class="zf-content">
                    <div class="zf-head">' . esc_html__( 'Chọn một định dạng', 'zombify' ) . '</div>
                    <div class="zf-create-box" data-count="' . count( $zombify_active_formats ) . '">';

		foreach ( $post_types as $post_type_data ) {

			$post_type_slug = $post_type_data['post_type_slug'];

			if ( $post_type_data['post_type_level'] == 1 ) {

				$popup .= '<div class="zf-item">
                                            <div class="zf-wrapper">
                                                <a class="zf-link ' . ( ! is_user_logged_in() ? 'js-authentication' : '' ) . '"
                                                   href="' . ( is_user_logged_in() ? add_query_arg( 'type', $post_type_slug, get_permalink( zf_get_option( "zombify_post_create_page" ) ) ) : '#sign-in' ) . '"></a>
                                                <i class="zf-icon zf-icon-type-' . $post_type_slug . '"></i>

                                                <div class="zf-item_title">' . $post_type_data['name'] . '</div>
                                                <div class="zf-item_description">' . $post_type_data['description'] . '</div>
                                            </div>
                                        </div>';

			} else {

				$popup .= '<div class="zf-item">
                                            <div class="zf-wrapper">
                                                <a class="zf-link ' . ( ! is_user_logged_in() ? 'js-authentication' : '' ) . '"
                                                   href="' . ( is_user_logged_in() ? add_query_arg( 'subtype', $post_type_slug, get_permalink( zf_get_option( "zombify_post_create_page" ) ) ) : '#sign-in' ) . '"></a>
                                                <i class="zf-icon zf-icon-type-' . $post_type_data['icon'] . '"></i>

                                                <div class="zf-item_title">' . $post_type_data['name'] . '</div>
                                                <div class="zf-item_description">' . $post_type_data['description'] . '</div>
                                            </div>
                                        </div>';

			}

		}
		$zombify_logo = zf_get_option( 'zombify_logo', zombify()->options_defaults["zombify_logo"] );
		if ( $zombify_logo ) {
			$popup .= '            </div>
                            <div class="zf-footer">
                                <span class="zombify-logo"><img src="' . $zombify_logo . '" alt="zombify-logo"> </span>
                            </div>
                        </div>
                    </div>
                </div>';
		} else {
			$popup .= '</div>
                        </div>
                    </div>
                </div>';
		}


		echo apply_filters( 'zombify_create_popup', $popup );
	}

}

/**
 * Prepare "create post" popup for rendering
 * TODO: - we need to replace this funcion
 */
function prepare_popups_for_rendering() {
	add_action( 'wp_footer', 'zombify_render_popups' );
}


/**
 * Add edit button if user can edit
 */
if ( ! function_exists( 'zombify_edit_post_button' ) ) {

	function zombify_edit_post_button() {
		//in case if it's AMP, don't render edit buttons to eliminate duplicate buttons problem
		if ( zf_user_can_edit( get_the_ID() ) && ! zombify()->amp()->is_amp_endpoint() ) {
			?>
			<a class="zf-edit-button"
			   href="<?php echo add_query_arg( array(
				   'action'  => 'update',
				   'post_id' => get_the_ID()
			   ), get_permalink( zf_get_option( "zombify_post_create_page" ) ) ); ?>">
				<i class="zf-icon zf-icon-edit"></i>
				<?php esc_html_e( 'Edit the post', 'zombify' ); ?>
			</a>
			<?php
		}
	}

}
add_action( 'zombify_after_post_layout', 'zombify_edit_post_button' );

/**
 * Deny user role access to admin panel
 */
function zombify_deny_admin_access() {
	if ( is_super_admin() ) {
		return;
	}

	if ( is_admin() && ( current_user_can( 'contributor' ) || current_user_can( 'subscriber' ) ) && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) && is_plugin_active( 'buddypress/bp-loader.php' ) ) {
		wp_redirect( esc_url( home_url( '/' ) ) );
		exit;
	}
}
add_action( 'init', 'zombify_deny_admin_access', 10 );

//paginate posts after they've been fetched and internally processed, do it before most other plugins
function zombify_post_pagination( $pages, $post ) {

	// check that post content was not filtered
	if ( ! empty( $post ) && empty( $post->zf_paginated ) ) {
		//convert pagination attributes to integer, beucaes we will use it as shortcode attribute
		$items_per_page = (int) get_post_meta( $post->ID, 'zombify_items_per_page', true );

		//check if we need to paginate this post manually
		if ( ! empty( $items_per_page ) && $items_per_page > 0 && $data = get_post_meta( $post->ID, 'zombify_data', true ) ) {

			$screen        = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
			$is_admin_edit = ( $screen && 'edit' == $screen->parent_base );
			if ( ! $is_admin_edit ) {

				$post_type = get_post_meta( $post->ID, 'zombify_data_type', true );
				$data      = zf_decode_data( $data );
				$QuizClass = 'Zombify_' . ucfirst( strtolower( $post_type ) ) . 'Quiz';
				if ( class_exists( $QuizClass ) ) {
					$quiz            = new $QuizClass();
					$pagination_path = $quiz->pagination_path;
					$temp_data       = $data;
					foreach ( $pagination_path as $pag_path ) {
						$temp_data = $temp_data[ $pag_path ];
					}
					$items_count = count( $temp_data );
				} else {
					$items_count = 1;
				}
				$pages_count = ceil( $items_count / $items_per_page );

				if ( $pages_count > 1 ) {
					//if we have at least one page, we need page brake right away
					$post->post_content .= '<!--nextpage-->';
					for ( $i = 2; $i < $pages_count; $i ++ ) {
						$post->post_content .= zf_get_shortcode( array( 'zf_page' => $i ), '<!--nextpage-->' );
					}
					$post->post_content .= zf_get_shortcode( array( 'zf_page' => $i ) );

					// mark all zombify shortcodes as paginated
					$shortcode_name     = zf_get_shortcode_name();
					$post->post_content = str_replace(
						sprintf( '[%s', $shortcode_name ),
						sprintf( '[%s zf_items_per_page=%d ', $shortcode_name, $items_per_page ),
						$post->post_content
					);
				}
				/*TODO:-normal we can do this better by looking up and replacing zombify shorcode in every page*/
				$pages = explode( '<!--nextpage-->', $post->post_content );

			}
		}
	}
	$post->zf_paginated = true;

	return $pages;
}
add_filter( 'content_pagination', 'zombify_post_pagination', 1, 2 );

/**
 * Filters to short-circuit default header status handling
 *
 * Disable handling on zf pages(e.g "Open List", "Ranked List") that have items_per_page
 *
 * @param $preempt
 * @param $wp_query
 *
 * @return bool
 */
function zombify_handle_404( $preempt, $wp_query ) {
	if ( ! empty( $wp_query->post->ID ) ) {
		$post_id        = $wp_query->post->ID;
		$items_per_page = get_post_meta( $post_id, 'zombify_items_per_page', true );

		if ( $items_per_page ) {
			return true;
		}
	}

	return false;
}

add_filter( 'pre_handle_404', 'zombify_handle_404', 10, 2 );

function zombify_redirect_after_comment( $location, $comment ) {
	if ( isset( $_POST['redirectback'] ) && isset( $_SERVER["HTTP_REFERER"] ) ) {
		$location = $_SERVER["HTTP_REFERER"] . '#comment-' . $comment->comment_ID;
	}
	return $location;
}
add_filter( 'comment_post_redirect', 'zombify_redirect_after_comment', 10, 2 );

if ( ! function_exists( "zombify_post_vote_ajax" ) ) {

	/**
	 * Zombify save ajax function
	 */
	function zombify_post_vote_ajax() {

		if ( isset( $_POST["post_id"] ) && isset( $_POST["post_parent_id"] ) && isset( $_POST["vote_type"] ) && get_post_meta( (int) $_POST["post_id"], "openlist_close_voting", true ) != 1 ) {

			$votes_count = (int) get_post_meta( (int) $_POST["post_id"], "zombify_post_rateing", true );

			$voted      = 0;
			$voted_type = '';

			if ( isset( $_COOKIE[ "zombify_post_vote_" . (int) $_POST["post_id"] ] ) ) {
				$voted      = 1;
				$voted_type = $_COOKIE[ "zombify_post_vote_" . (int) $_POST["post_id"] ];
			}

			if ( $voted == 1 ) {
				if ( $_POST["vote_type"] == 'up' ) {
					if ( $voted_type == 'up' ) {
						$votes_count --;
						setcookie( "zombify_post_vote_" . (int) $_POST["post_id"], "up", time() - 60 * 60 * 24, '/' );
					} else {
						$votes_count += 2;
						setcookie( "zombify_post_vote_" . (int) $_POST["post_id"], "up", time() + 60 * 60 * 24, '/' );
					}
				} else {
					if ( $voted_type == 'down' ) {
						$votes_count ++;
						setcookie( "zombify_post_vote_" . (int) $_POST["post_id"], "down", time() - 60 * 60 * 24, '/' );
					} else {
						$votes_count -= 2;
						setcookie( "zombify_post_vote_" . (int) $_POST["post_id"], "down", time() + 60 * 60 * 24, '/' );
					}
				}
			} else {
				if ( $_POST["vote_type"] == 'up' ) {
					$votes_count ++;
					setcookie( "zombify_post_vote_" . (int) $_POST["post_id"], "up", time() + 60 * 60 * 24, '/' );
				} else {
					$votes_count --;
					setcookie( "zombify_post_vote_" . (int) $_POST["post_id"], "down", time() + 60 * 60 * 24, '/' );
				}
			}

			update_post_meta( (int) $_POST["post_id"], "zombify_post_rateing", $votes_count );

			$post_parent_id = (int) $_POST["post_parent_id"];
			$post_data_type = get_post_meta( $post_parent_id, "zombify_data_type", true );

			if ( $post_data_type == "openlist" || $post_data_type == "rankedlist" ) {
				$post_data = zf_decode_data( get_post_meta( $post_parent_id, "zombify_data", true ) );
				foreach ( $post_data['list'] as $list_index => $list_item ) {
					$post_data['list'][ $list_index ]["temp_item_rateing"] = (int) get_post_meta( $list_item['post_id'], "zombify_post_rateing", true );
				}

				usort( $post_data['list'], function ( $a, $b ) {
					return $b['temp_item_rateing'] != $a['temp_item_rateing'] ? $b['temp_item_rateing'] - $a['temp_item_rateing'] : $a['post_id'] - $b['post_id'];
				} );

				foreach ( $post_data['list'] as $list_index => $list_item ) {
					unset( $post_data['list'][ $list_index ]["temp_item_rateing"] );
				}

				update_post_meta( $post_parent_id, "zombify_data", zf_encode_data( $post_data ) );
			}

			if ( isset( $_POST["amp"] ) ) {

				header( "Content-type: application/json" );
				header( "Access-Control-Allow-Credentials: true" );
				header( "Access-Control-Allow-Origin: *.ampproject.org" );
				header( "AMP-Access-Control-Allow-Source-Origin: " . ( isset( $_SERVER['HTTPS'] ) ? "https" : "http" ) . "://" . $_SERVER["HTTP_HOST"] );
				header( "Access-Control-Expose-Headers: AMP-Access-Control-Allow-Source-Origin" );

				echo json_encode( array(
					"votes" . (int) $_POST["post_id"] => $votes_count,
					"post_id"                         => (int) $_POST["post_id"]
				) );

			} else {
				echo json_encode( array( "votes" => $votes_count, "post_id" => (int) $_POST["post_id"] ) );
			}

			zf_flush_post_cache( $_POST["post_id"] );
			zf_flush_post_cache( $post_parent_id );

			exit;
		}
	}

}

add_action( 'wp_ajax_zombify_post_vote', 'zombify_post_vote_ajax' );
add_action( 'wp_ajax_nopriv_zombify_post_vote', 'zombify_post_vote_ajax' );

function zombify_color_mode_class( $classes ) {
	$classes[] = 'zombify-' . zf_get_option( 'zombify_color_mode', 'light' );
	return $classes;
}
add_filter( 'body_class', 'zombify_color_mode_class' );


if ( ! function_exists( "zombify_get_post_comments" ) ) {

	/**
	 * Zombify save ajax function
	 */
	function zombify_get_post_comments() {

		$json = array();
		if ( isset( $_POST["post_id"] ) && isset( $_POST["page"] ) ) {

			global $post;

			$post = get_post( (int) $_POST["post_id"] );
			$curr_user = wp_get_current_user();

			$comments = get_comments(
				array(
					"post_id"            => (int) $_POST["post_id"],
					"status"             => "approve",
					"include_unapproved" => array( $curr_user->user_email ),
					"orderby"            => array( "comment_parent" => "ASC", "comment_ID" => "DESC" ),
				)
			);

			zf_remove_first_comment( $comments );

			$comments_html = wp_list_comments(
				array(
					'walker'            => null,
					'max_depth'         => '',
					'style'             => 'div',
					'callback'          => null,
					'end-callback'      => null,
					'type'              => 'all',
					'reply_text'        => esc_html__( 'Trả lời', 'zombify' ),
					'page'              => (int) $_POST["page"] - 1,
					'per_page'          => 5,
					'avatar_size'       => 32,
					'reverse_top_level' => null,
					'reverse_children'  => '',
					'format'            => 'html5', // or 'xhtml' if no 'HTML5' theme support
					'short_ping'        => false,   // @since 3.6
					'echo'              => false     // boolean, default is true
				),
				$comments
			);
			$json['comments'] = $comments_html;
		}
		echo json_encode( $json ); exit;
	}

}
add_action( 'wp_ajax_zombify_get_post_comments', 'zombify_get_post_comments' );
add_action( 'wp_ajax_nopriv_zombify_get_post_comments', 'zombify_get_post_comments' );


if ( ! function_exists( "zombify_discard_virtual" ) ) {

	/**
	 * Zombify discard virtual post
	 */
	function zombify_discard_virtual() {

		$result = 1;

		if ( isset( $_GET["type"] ) ) {

			$vargs  = array(
				'author'      => get_current_user_id(),
				'post_type'   => 'zf_vpost',
				'post_status' => 'any',
				'meta_query'  => array(
					array(
						'key'     => 'zombify_virtual_post',
						'value'   => '1',
						'compare' => '=',
					),
					array(
						'key'     => 'zombify_data_type',
						'value'   => addslashes( $_GET["type"] ),
						'compare' => '=',
					),
					array(
						'key'     => 'zombify_data_subtype',
						'value'   => addslashes( $_GET["subtype"] ),
						'compare' => '=',
					)
				)
			);
			$vposts = get_posts( $vargs );

			if ( count( $vposts ) > 0 ) {
				wp_delete_post( $vposts[0]->ID, true );
			}
		}

		echo json_encode( array( "result" => $result ) ); exit;
	}

}
add_action( 'wp_ajax_zombify_discard_virtual', 'zombify_discard_virtual' );
add_action( 'wp_ajax_nopriv_zombify_discard_virtual', 'zombify_discard_virtual' );

if ( ! function_exists( "zf_post_save_filter" ) ) {

	function zf_post_save_filter( $post_ID, $post_after, $post_before ) {

		$save_data = false;
		if ( $data = get_post_meta( $post_ID, 'zombify_data', true ) ) {
			$data = zf_decode_data( $data );
			if ( $post_before->post_excerpt != $post_after->post_excerpt ) {
				if ( $data["excerpt_description"] != $post_after->post_excerpt ) {
					$data["excerpt_description"] = $post_after->post_excerpt;
					$save_data = true;
				}
			}

			if ( $post_before->post_content != $post_after->post_content ) {
				$post_cont = zf_remove_shortcode( wpautop( $post_after->post_content ) );
				if ( $data["preface_description"] != $post_cont ) {
					$data["preface_description"] = $post_cont;
					$save_data = true;
				}
			}
		}

		if ( $save_data ) {
			$data = zf_encode_data( $data );
			update_post_meta( $post_ID, 'zombify_data', $data );
		}
	}

}
add_action( 'post_updated', 'zf_post_save_filter', 10, 3 );

function zf_sanitize_file_name( $filename ) {
	return remove_accents( $filename );
}
add_filter( 'sanitize_file_name', 'zf_sanitize_file_name', 10 );

function zf_mask_empty( $value ) {
	if ( empty( $value ) ) {
		return ' ';
	}

	return $value;
}
add_filter( 'pre_post_title', 'zf_mask_empty' );
add_filter( 'pre_post_content', 'zf_mask_empty' );

function zf_unmask_empty( $data ) {
	if ( ' ' == $data['post_title'] ) {
		$data['post_title'] = '';
	}
	if ( ' ' == $data['post_content'] ) {
		$data['post_content'] = '';
	}

	return $data;
}
add_filter( 'wp_insert_post_data', 'zf_unmask_empty' );

if ( ! function_exists( "zombify_embed_from_url" ) ) {
	/**
	 * Zombify call embed class for current url
	 */
	function zombify_embed_from_url() {
		if ( isset( $_GET ) && isset( $_GET["url"] ) && isset( $_GET["post_id"] ) ) {
			$url     = $_GET["url"];
			$post_id = $_GET["post_id"];
			$host    = Zombify_Embed::parseUrl( $url );
			$embed   = Zombify_Embed::getEmbedCode( $url, $host, true, false, false, $post_id );

			return $embed;
		}
	}
}
add_action( 'wp_ajax_zombify_embed_from_url', 'zombify_embed_from_url' );

function zf_amp_styles( $amp_template ) {
	$amp_styles_file = zombify()->assets_dir . "amp/styles.php";
	if ( is_file( $amp_styles_file ) ) {
		include $amp_styles_file;
	}
}
add_action( 'amp_post_template_css', 'zf_amp_styles' );

function zf_convert_attachment( $thumbnail_id, $post_id, $featured = false ) {

	include_once( zombify()->includes_dir . 'lib/vendor/autoload.php' );

	require_once( zombify()->includes_dir . 'classes/ZF_Converter_Base.php' );
	require_once( zombify()->includes_dir . 'classes/ZF_Converter_Cloudconvert.php' );
	require_once( zombify()->includes_dir . 'classes/ZF_Converter.php' );

	if ( $attachment = get_post( $thumbnail_id ) ) {

		switch ( $attachment->post_mime_type ) {
			case 'image/gif':
				if ( ! get_post_meta( $thumbnail_id, 'zombify_mp4_url', true ) ) {
					$result = ZF_Converter::Instance()->process( $attachment, 'mp4', true, 'video/mp4' );
					if ( isset( $result["success"] ) && $result["success"] === true && isset( $result["attachment_id"] ) && $result["attachment_id"] ) {
						update_post_meta( $thumbnail_id, 'zombify_mp4_id', $result["attachment_id"] );
						update_post_meta( $thumbnail_id, 'zombify_mp4_url', $result["result"]["url"] );
					}
				}

				if ( ! get_post_meta( $thumbnail_id, 'zombify_jpeg_id', true ) ) {
					$gif_file = get_attached_file( $thumbnail_id );
					$gif_file_filename = pathinfo( $gif_file, PATHINFO_FILENAME );

					$wp_upload_dir = wp_upload_dir();
					$filepath = $wp_upload_dir["path"] . '/' . $gif_file_filename . '.jpg';

					$i = 0;
					while ( file_exists( $filepath ) ) {
						$i ++;
						$filepath = $wp_upload_dir["path"] . '/' . $gif_file_filename . '_' . $i . '.jpg';
					}

					$image = wp_get_image_editor( $gif_file );
					if ( ! is_wp_error( $image ) ) {
						$image->save( $filepath );
					}

					$converted_attachment_args = array(
						'guid'           => $filepath,
						'post_mime_type' => 'image/jpeg',
						'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filepath ) ),
						'post_content'   => '',
						'post_status'    => 'inherit'
					);

					$converted_attach_id = wp_insert_attachment( $converted_attachment_args, $filepath, $thumbnail_id );

					// Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
					if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) {
						require_once( ABSPATH . 'wp-admin/includes/image.php' );
					}

					// Generate the metadata for the attachment, and update the database record.
					$attach_data = wp_generate_attachment_metadata( $converted_attach_id, $filepath );
					wp_update_attachment_metadata( $converted_attach_id, $attach_data );

					update_post_meta( $thumbnail_id, 'zombify_jpeg_id', $converted_attach_id );
					update_post_meta( $thumbnail_id, 'zombify_jpeg_url', wp_get_attachment_url( $converted_attach_id ) );
				}
				break;
			case 'video/mp4':
			case 'video/webm':

				if ( ! $jpeg_id = get_post_meta( $thumbnail_id, 'zombify_jpeg_id', true ) ) {
					$result = ZF_Converter::Instance()->process( $attachment, 'jpg', true, 'image/jpeg' );
					if ( isset( $result["success"] ) && $result["success"] === true && isset( $result["attachment_id"] ) && $result["attachment_id"] ) {

						update_post_meta( $thumbnail_id, 'zombify_jpeg_id', $result["attachment_id"] );
						update_post_meta( $thumbnail_id, 'zombify_jpeg_url', wp_get_attachment_url( $result["attachment_id"] ) );

						$thumbnail_id = $result["attachment_id"];
					}
				} else {
					$thumbnail_id = $jpeg_id;
				}
				break;
			case 'image/jpeg':
			case 'image/png':

				if ( $featured ) {
					if ( ! get_post_meta( $thumbnail_id, 'zombify_mp4_url', true ) ) {
						if ( $zombify_data_type = get_post_meta( $post_id, 'zombify_data_type', true ) ) {
							if ( $zombify_data_type == 'gif' ) {
								if ( $zombify_data = get_post_meta( $post_id, 'zombify_data', true ) ) {
									$zombify_data = zf_decode_data( $zombify_data );

									if ( isset( zf_array_values( $zombify_data["gif"][0]["image_image"] )[0]["type"] ) && isset( zf_array_values( $zombify_data["gif"][0]["image_image"] )[0]["attachment_id"] ) && zf_array_values( $zombify_data["gif"][0]["image_image"] )[0]["attachment_id"] ) {
										if ( zf_array_values( $zombify_data["gif"][0]["image_image"] )[0]["type"] == 'video/mp4' ) {
											update_post_meta( $thumbnail_id, 'zombify_jpeg_id', addslashes( zf_array_values( $zombify_data["gif"][0]["image_image"] )[0]["uploaded"]["url"] ) );
										}

										if ( zf_array_values( $zombify_data["gif"][0]["image_image"] )[0]["type"] == 'image/gif' ) {
											$attach = get_post( zf_array_values( $zombify_data["gif"][0]["image_image"] )[0]["attachment_id"] );
											$result = ZF_Converter::Instance()->process( $attach, 'mp4' );

											if ( isset( $result["success"] ) && $result["success"] === true ) {
												update_post_meta( $thumbnail_id, 'zombify_mp4_url', $result["result"]["url"] );
												if ( isset( $result["result"]["path"] ) ) {
													update_post_meta( $thumbnail_id, 'zombify_mp4_path', addslashes( $result["result"]["path"] ) );
												}
											}
										}
									}
								}
							}
						}
					}
				}

				break;
		}

	}

	return $thumbnail_id;

}
//todo: seems like we don't need this hook anymore.
add_filter( 'zombify_set_featured_image', 'zf_convert_attachment', 10, 3 );
add_action( "zombify_insert_attachment", "zf_convert_attachment", 10, 3 );

function zf_delete_attachment( $post_id ) {
	if ( $zombify_mp4_path = get_post_meta( $post_id, "zombify_mp4_path", true ) ) {
		@unlink( $zombify_mp4_path );
	}

	if ( $zombify_jpeg_id = get_post_meta( $post_id, "zombify_jpeg_id", true ) ) {
		wp_delete_attachment( $zombify_jpeg_id, true );
	}
}
add_action( 'delete_attachment', 'zf_delete_attachment' );

/**
 * Save additional post data after post saveing
 */
function zf_post_saved_function( $post_id, $post_action, $data ) {
	Zombify_MemeQuiz::saveMemeTemplateImage( $data, $post_id );
}
add_action( 'zf_post_saved', 'zf_post_saved_function', 10, 3 );