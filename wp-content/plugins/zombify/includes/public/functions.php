<?php
$zombify_zfps = '';

function zf_showCredit( $url, $text ) {

	if ( $url != '' ) {

		if ( substr( $url, 0, 7 ) != 'http://' && substr( $url, 0, 8 ) != 'https://' ) {
			$url = 'http://' . $url;
		}

		?>
		<a class="zf-media_credit" target="_blank" href="<?php echo $url; ?>" rel="nofollow noopener"><?php echo $text ? $text : __( 'Credit', 'zombify' ); ?></a>
		<?php
	} elseif ( $text ) {
		echo $text;
	}

}

function zombify_frontend_save_post( &$zf_openlist_errors ) {

	global $zombify_zfps;

	$current_post_id = get_the_ID();

	if ( zombify_check_form_token() && isset( $_POST["zombify_frontend_save"] ) && is_user_logged_in() && get_post_meta( $current_post_id, "openlist_close_submission", true ) != 1 ) {

		if ( $post_type = get_post_meta( $current_post_id, 'zombify_data_type', true ) ) {

			if ( in_array( $post_type, array( "openlist", "rankedlist" ) ) ) {

				if ( ! isset( $_POST["zombify_openlist"]["mediatype"] ) || ! in_array( $_POST["zombify_openlist"]["mediatype"], array(
						"image",
						"embed"
					) ) ) {

					if ( ! isset( $zf_openlist_errors["mediatype"] ) ) {
						$zf_openlist_errors["mediatype"] = array();
					}

					$zf_openlist_errors["mediatype"][] = __( "Incorrect media type.", "zombify" );

				} else {

					if ( $_POST["zombify_openlist"]["mediatype"] == 'embed' ) {

						if ( ! isset( $_POST["zombify_openlist"]["embed_url"] ) || empty( $_POST["zombify_openlist"]["embed_url"] ) ) {

							if ( ! isset( $zf_openlist_errors["embed"] ) ) {
								$zf_openlist_errors["embed"] = array();
							}

							$zf_openlist_errors["embed"][] = __( "Please, fill the Embed / URL.", "zombify" );

						}

					}

					if ( $_POST["zombify_openlist"]["mediatype"] == 'image' ) {

						if ( ( ! isset( $_FILES['zombify_openlist']['tmp_name']['image'] ) || $_FILES['zombify_openlist']['tmp_name']['image'] == '' ) && ( ! isset( $_POST['zombify_openlist']['image_url'] ) || $_POST['zombify_openlist']['image_url'] == '' ) ) {

							if ( ! isset( $zf_openlist_errors["image_url"] ) ) {
								$zf_openlist_errors["image_url"] = array();
							}

							$zf_openlist_errors["image_url"][] = __( "Please, attach an image or fill the image URL field.", "zombify" );

						}

					}

				}

				if ( ! isset( $_POST["zombify_openlist"]["title"] ) || empty( $_POST["zombify_openlist"]["title"] ) ) {

					if ( ! isset( $zf_openlist_errors["title"] ) ) {
						$zf_openlist_errors["title"] = array();
					}

					$zf_openlist_errors["title"][] = __( "Please, fill the title field.", "zombify" );

				}

				if ( count( $zf_openlist_errors ) == 0 ) {

					$current_user_id = get_current_user_id();

					$current_post = get_post( $current_post_id );

					$data = zf_decode_data( get_post_meta( $current_post_id, 'zombify_data', true ) );

					if ( zf_user_can_publish( $current_post_id ) ) {

						$post_zf_status = 'publish';

					} else {

						$post_zf_status = 'pending';

					}

					$post_args = array(
						'post_author'           => $current_user_id,
						'post_content'          => htmlspecialchars( $_POST['zombify_openlist']['description'] ),
						'post_content_filtered' => '',
						'post_title'            => htmlspecialchars( $_POST['zombify_openlist']['title'] ),
						'post_excerpt'          => '',
						'post_status'           => $post_zf_status,
						'post_type'             => 'list_item',
						'post_parent'           => $current_post_id,
					);
					$post_id   = wp_insert_post( $post_args );


					$f = array();

					if ( $_POST["zombify_openlist"]["mediatype"] == 'image' ) {

						if ( isset( $_FILES['zombify_openlist']['tmp_name']['image'] ) && $_FILES['zombify_openlist']['tmp_name']['image'] != '' ) {

							$f = array(
								"name"     => $_FILES['zombify_openlist']['name']['image'],
								"type"     => $_FILES['zombify_openlist']['type']['image'],
								"size"     => $_FILES['zombify_openlist']['size']['image'],
								"tmp_name" => $_FILES['zombify_openlist']['tmp_name']['image'],
							);

							if ( in_array( $_FILES['zombify_openlist']['type']['image'], array(
								"image/jpeg",
								"image/gif",
								"image/png"
							) ) ) {

								$f = zf_get_file_upload( $f );

								if ( isset( $f["uploaded"]["error"] ) && $f["uploaded"]["error"] != '' ) {
									$zf_openlist_errors["image_url"][] = __( "Incorrect image uploaded.", "zombify" );
								}

							} else {

								if ( ! isset( $zf_openlist_errors["image_url"] ) ) {
									$zf_openlist_errors["image_url"] = array();
								}

								$zf_openlist_errors["image_url"][] = __( "Incorrect image uploaded.", "zombify" );

							}

							if ( $_FILES['zombify_openlist']['size']['image'] > zf_get_option( "zombify_max_upload_size" ) ) {

								$zf_openlist_errors["image_url"][] = __( "File is too large.", "zombify" );

							}


						} else {

							if ( isset( $_POST['zombify_openlist']['image_url'] ) && $_POST['zombify_openlist']['image_url'] != '' ) {

								try {

									$url_file_data = zf_get_file_by_url( $_POST['zombify_openlist']['image_url'], false );

									$f["name"]     = $url_file_data["name"];
									$f["size"]     = $url_file_data["size"];
									$f["type"]     = $url_file_data["type"];
									$f["uploaded"] = $url_file_data["uploaded"];

								} catch ( Exception $e ) {

									if ( ! isset( $zf_openlist_errors["image_url"] ) ) {
										$zf_openlist_errors["image_url"] = array();
									}

									$zf_openlist_errors["image_url"][] = $e->getMessage();

								}

							}

						}

						if ( isset( $zf_openlist_errors["image_url"] ) && count( $zf_openlist_errors["image_url"] ) > 0 ) {
							unset( $f );
						} else {

							$f["uploaded"]["file"] = $f["uploaded"]["file"];

							$attach_data        = zf_insert_attachment( $post_id, $f["uploaded"]["file"], $f['type'], true );
							$attach_id          = $attach_data["id"];
							$f["attachment_id"] = $attach_id;
							$f["size_urls"]     = $attach_data["size_urls"];

						}

					} else {

						if ( $_POST["zombify_openlist"]["embed_thumb"] != '' ) {

							$image_url_hash = md5( $_POST["zombify_openlist"]["embed_thumb"] );

							$downloaded_images = zf_get_downloaded_attachment( $post_id );

							if ( isset( $downloaded_images[ $image_url_hash ] ) ) {

								zf_set_post_thumbnail( $post_id, $downloaded_images[ $image_url_hash ] );

							} else {

								$file_data_url = $_POST["zombify_openlist"]["embed_thumb"];

								try {

									$url_file_data = zf_get_file_by_url( $file_data_url, false );

									$file_data_up_path = $url_file_data["uploaded"]["file"];
									$mime_type         = $url_file_data["type"];

									$attach_data = zf_insert_attachment( $post_id, $file_data_up_path, $mime_type, true );
									$attach_id   = $attach_data["id"];

									zf_save_downloaded_attachment( $post_id, $attach_id, $file_data_url );

								} catch ( Exception $e ) {

								}


							}

						}

					}

					if ( count( $zf_openlist_errors ) == 0 ) {

						$listitem = array();

						$listitem["post_id"]           = $post_id;
						$listitem["title"]             = htmlspecialchars( $_POST['zombify_openlist']['title'] );
						$listitem["mediatype"]         = htmlspecialchars( $_POST['zombify_openlist']['mediatype'] );
						$listitem["embed_url"]         = isset( $_POST['zombify_openlist']['embed_url'] ) ? htmlspecialchars( $_POST['zombify_openlist']['embed_url'] ) : '';
						$listitem["original_source"]   = isset( $_POST['zombify_openlist']['original_source'] ) ? 1 : 0;
						$listitem["image_credit"]      = isset( $_POST['zombify_openlist']['original_source'] ) ? htmlspecialchars( $_POST['zombify_openlist']['image_credit'] ) : '';
						$listitem["image_credit_text"] = isset( $_POST['zombify_openlist']['original_source'] ) ? htmlspecialchars( $_POST['zombify_openlist']['image_credit_text'] ) : '';
						$listitem["shop_url"]          = '';
						$listitem["shop_button_text"]  = isset( $_POST['zombify_openlist']['original_source'] ) ? htmlspecialchars( $_POST['zombify_openlist']['shop_button_text'] ) : '';
						$listitem["description"]       = htmlspecialchars( $_POST['zombify_openlist']['description'] );
						$listitem["image"]             = array();
						if ( count( $f ) > 0 ) {
							$listitem["image"][0] = [ "attachment_id" => $f["attachment_id"] ];
						}

						$data["list"][ count( $data["list"] ) ] = $listitem;

						$post_data_type = $post_type;

						if ( in_array( $post_data_type, array( "openlist", "rankedlist" ) ) ) {

							$post_data = $data;

							foreach ( $post_data['list'] as $list_index => $list_item ) {

								$post_data['list'][ $list_index ]["temp_item_rateing"] = (int) get_post_meta( $list_item['post_id'], "zombify_post_rateing", true );

							}

							usort( $post_data['list'], function ( $a, $b ) {
								return $b['temp_item_rateing'] != $a['temp_item_rateing'] ? $b['temp_item_rateing'] - $a['temp_item_rateing'] : $a['post_id'] - $b['post_id'];
							} );

							foreach ( $post_data['list'] as $list_index => $list_item ) {

								unset( $post_data['list'][ $list_index ]["temp_item_rateing"] );

							}

							$data = $post_data;

						}

						update_post_meta( $current_post_id, 'zombify_data', zf_encode_data( $data ) );

						do_action( 'zf_save_open_list_item', $post_id, $current_post_id );

						$zombify_zfps = $post_zf_status;


					}


				}

			}

		}

	}

}

function zf_showFormErrors( $errors ) {

	$output = '';

	foreach ( $errors as $error ) {
		$output .= '<div class="zf-help">' . $error . '</div>';
	}

	return $output;

}

function zf_branding_color_css() {

	$color = zf_get_option( 'zombify_branding_color', zombify()->options_defaults["zombify_branding_color"] );

	$css = '/* Primary Color Scheme */

/* background color */
#zombify-main-section.zf-story .zf-start .zf-add-component i,#zombify-main-section .zf-uploader .zf-label .zf-label_text,#zombify-main-section.zf-story .zf-components .zf-components_plus,#zombify-main-section .zf-checkbox-currect input:checked+.zf-toggle .zf-icon,#zombify-main-section-front .zf-list .zf-next-prev-pagination .zf-nav,
#zf-fixed-bottom-pane .zf-button, .zf-fixed-bottom-pane .zf-button,.zf-create-box .zf-item:hover .zf-wrapper,#zombify-main-section-front .zf-poll .zf-quiz_answer .zf-poll-stat,#zombify-main-section .zf-button,#zombify-main-section .zf-upload-content .zf-uploader .zf-label .zf-icon,.zombify-submit-popup .zf-content .zf-btn-group .zf-btn.zf-create, #zombify-main-section .zf-progressbar .zf-progressbar-active,#zombify-main-section-front .zf-quiz .zf-quiz_answer.zf-input .zf-quiz-guess-btn {
  background-color: ' . $color . ';
}

/* text color */
#zombify-main-section .zf-item-wrapper .zf-body.zf-numeric .zf-index,#zombify-main-section.zf-meme .zf-options .zf-options_toggle,#zombify-main-section-front .zf-comments .bypostauthor > .comment-body .vcard .fn,
#zombify-main-section #zf-options-section .zf-head .zf-icon,.zf-create-box .zf-item .zf-icon, #zombify-main-section .zf-item-wrapper .zf-type-wrapper,#zombify-main-section-front .zf-quiz .zf-quiz_question .zf-quiz_header .zf-number,.zombify-create-popup .zf-popup_close:hover i,.zombify-submit-popup .zf-popup_close:hover i,
.zf-desktop #zombify-main-section.zf-story .zf-components .zf-add-component:hover,#zombify-main-section.zombify-personality-quiz .zf-item-wrapper .zf-type-wrapper, #zombify-main-section.zf-story .zf-item-wrapper .zf-type-wrapper,#zombify-main-section-front .zf-create-page .zf-title,#zombify-main-section-front a,#zombify-main-section-front .zf-list .zf-list_item .zf-list_header .zf-number,.zf-desktop #zombify-main-section-front .zf-list .zf-list_item .zf-list_header .zf-list_title:hover a,#zombify-main-section .fr-toolbar .fr-command.fr-btn.fr-active, .fr-popup .fr-command.fr-btn.fr-active,
#zombify-main-section h1, #zombify-main-section h2, #zombify-main-section h3, #zombify-main-section h4, #zombify-main-section h5, #zombify-main-section h6,#zombify-main-section h1, #zombify-main-section h2, #zombify-main-section h3, #zombify-main-section h4, #zombify-main-section h5, #zombify-main-section h6 {
  color: ' . $color . ';
}

/* border color */
.zf-fixed-bottom-pane,#zombify-main-section .zf-button,#zombify-main-section .zf-checkbox-currect input:checked+.zf-toggle .zf-icon,#zombify-main-section .fr-toolbar,
#zf-fixed-bottom-pane .zf-button, .zf-fixed-bottom-pane .zf-button,#zombify-main-section-front .zombify-comments .zf-tabs-menu li.zf-active,
#zf-fixed-bottom-pane {
  border-color: ' . $color . ';
}';

	return $css;

}

/**
 * Wrapper method to render plugin images
 *
 * @param int          $post_id Attachment ID
 * @param string|array $size    Preferred size
 * @param array        $args    Additional arguments
 */
function zombify_img_tag( $post_id, $size, $args = array() ) {
	echo zombify_get_img_tag( $post_id, $size, $args );
}

/**
 * Wrapper method to get plugin images
 *
 * @param int          $post_id Attachment ID
 * @param string|array $size    Preferred size
 * @param array        $args    Additional arguments
 *
 * @return string
 */
function zombify_get_img_tag( $post_id, $size, $args = array() ) {
	$r = wp_parse_args( $args, array(
		'class'              => '',
		'style'              => '',
		'allow_modification' => true
	) );

	$html = wp_get_attachment_image( $post_id, $size, false, array(
		'class' => $r['class'],
		'style' => $r['style']
	) );

	if ( $r['allow_modification'] ) {
		$html = apply_filters( 'zombify_img_tag', $html, $post_id, $size );
	}

	return $html;
}

/**
 * Wrapper method to render plugin videos
 *
 * @param int          $post_id Attachment ID
 * @param string|array $size    Preferred size
 * @param array        $args    Additional arguments
 */
function zombify_video_tag( $post_id, $size, $args = array() ) {
	echo zombify_get_video_tag( $post_id, $size, $args );
}

/**
 * Wrapper method to get plugin videos
 *
 * @param int          $post_id Attachment ID
 * @param string|array $size    Preferred size
 * @param array        $args    Additional arguments
 *
 * @return string
 */
function zombify_get_video_tag( $post_id, $size, $args = array() ) {

	$r   = wp_parse_args( $args, array(
		'class'              => '',
		'allow_modification' => true
	) );
	$url = wp_get_attachment_url( $post_id );

	$type = 'video/mp4';
	$html = sprintf(
		'<video autoplay loop muted="" class="%s"><source src="%s" type="%s">%s</video>',
		$r['class'],
		$url,
		$type,
		__( 'Your browser does not support the video tag.', 'zombify' )
	);

	if ( $r['allow_modification'] ) {
		$html = apply_filters( 'zombify_video_tag', $html, $url, $type, $post_id, $size );
	}

	return $html;
}

/**
 * Get front main section classes
 *
 * @param string $classes Additional classes
 *
 * @return string
 * @since   1.4.3
 * @varsion 1.4.3
 */
function zombify_get_front_main_section_classes( $classes = '' ) {
	$media_width_eq_post_width = zf_get_option( 'zombify_media_width_equal_post_width', 'all' );
	switch ( $media_width_eq_post_width ) {
		case 'all':
			$classes .= ' zf-media-full';
			break;

		case 'images':
			$classes .= '  zf-images-full';
			break;

		case 'gifs':
			$classes .= ' zf-gif-full';
			break;

		case 'none':
			$classes .= ' zf-nomedia-full';
			break;

		default:
			$classes .= '';
	}

	return trim( $classes );
}

/**
 * Generate HTML audio for mejs
 *
 * @param string $src   Audio source
 * @param string $class Html classes
 *
 * @return string
 */
function zombify_mejs_audio( $src, $class = '' ) {

	if ( zombify()->amp()->is_amp_endpoint() ) {

		$url_array = explode( '.', $src );
		$ext       = end( $url_array );

		$html = '<audio controls><source src="' . $src . '" type="audio/' . $ext . '" /></audio>';

	} else {
		$html = '<audio width="100%" src="' . $src . '" class="' . $class . '"></audio>';
	}

	return $html;
}

/**
 * Generate HTML video for mejs
 *
 * @param string $src   Video source
 * @param string $class Html classes
 * @param array  $args  Additional arguments
 *
 * @return string
 */
function zombify_mejs_video( $src, $class = '', $args = array() ) {

	$args = wp_parse_args( $args, array(
		'poster'   => false,
		'video_id' => 0
	) );

	if ( zombify()->amp()->is_amp_endpoint() ) {
		$poster = $args['poster'] ? ( ' poster="' . $args['poster'] . '"' ) : '';

		$url_array = explode( '.', $src );
		$ext       = end( $url_array );

		$html = '<video controls ' . $poster . '><source type="video/' . $ext . '" src="' . $src . '" /></video>';

	} else {
		$shortcode_atts = '';

		$metadata = wp_get_attachment_metadata( $args['video_id'] );
		if ( $metadata ) {

			if ( isset( $metadata['fileformat'] ) ) {
				$shortcode_atts .= ' ' . $metadata['fileformat'] . '="' . $src . '"';
			}

			if ( isset( $metadata['width'] ) ) {
				$shortcode_atts .= ' width="' . $metadata['width'] . '"';
			}

			if ( isset( $metadata['height'] ) ) {
				$shortcode_atts .= ' height="' . $metadata['height'] . '"';
			}
		}

		if ( $args['poster'] ) {
			$shortcode_atts .= ' poster="' . $args['poster'] . '"';
		}

		$class = 'wp-video-shortcode ' . $class;
		if ( $class ) {
			$shortcode_atts .= ' class="' . $class . '"';
		}

		$html = do_shortcode( '[video' . $shortcode_atts . '][/video]' );

	}

	return $html;
}


function zombify_get_form_token() {

	if ( ! isset( $_SESSION["zombify_form_tokens"] ) ) {
		$_SESSION["zombify_form_tokens"] = array();
	}

	$token = md5( rand( 0, 10000000 ) . time() );

	$_SESSION["zombify_form_tokens"][] = $token;

	return $token;

}

function zombify_check_form_token( $token = '' ) {

	if ( $token == '' && isset( $_POST["zombify_form_token"] ) ) {
		$token = $_POST["zombify_form_token"];
	}

	if ( isset( $_SESSION["zombify_form_tokens"] ) && in_array( $token, $_SESSION["zombify_form_tokens"] ) ) {

		unset( $_SESSION["zombify_form_tokens"][ array_search( $token, $_SESSION["zombify_form_tokens"] ) ] );

		return true;

	} else {
		return false;
	}

}

function zombify_get_form_token_tag() {

	echo '<input type="hidden" name="zombify_form_token" value="' . zombify_get_form_token() . '">';

}

function zf_remove_first_comment( &$comments ) {

	if ( ! isset( $comments[0] ) ) {
		return true;
	}

	$first_comment = $comments[0];

	unset( $comments[0] );

	zf_remove_comment_childs( $comments, $first_comment->comment_ID );

}

function zf_remove_comment_childs( &$comments, $parent_id ) {

	foreach ( $comments as $index => $comment ) {

		if ( $comment->comment_parent == $parent_id ) {

			zf_remove_comment_childs( $comments, $comment->comment_ID );

			unset( $comments[ $index ] );

		}

	}

}

function zf_purify( $html ) {

	return zombify()->zf_purifier->purify( $html );

}

/* For sanitizing HTML with the Wordpress functionality */
function zf_purify_kses( $html ) {
	global $allowedposttags;

	$tags                   = $allowedposttags;
	$tags['img']['data-id'] = array();

	/*
	 * Allow <iframe> HTML tag
	 */
	$tags['iframe'] = array(
		'src'             => array(),
		'height'          => array(),
		'width'           => array(),
		'frameborder'     => array(),
		'allowfullscreen' => array(),
	);

	return wp_kses( stripslashes( $html ), $tags );
}

function zf_validate_option( $object, $option ) {

	if ( $object->data_options !== false ) {

		$req_options = zombify()->getRequiredOptions( $object->slug );

		if ( count( $req_options ) > 0 ) {

			if ( in_array( $option, $req_options ) ) {

				if ( ! isset( $object->data_options[ $option ] ) || empty( $object->data_options[ $option ] ) ) {

					return false;

				} else {
					return true;
				}

			} else {
				return true;
			}

		} else {
			return true;
		}

	} else {
		return true;
	}
}

/**
 * Wrapper method to get array values
 *
 * @param array $arr
 *
 * @return array
 */
function zf_array_values( &$arr ) {

	if ( is_array( $arr ) && count( $arr ) > 0 ) {
		return array_values( $arr );
	} else {
		return array();
	}

}

function zf_compare_attachments( $new_data, $old_data ) {

	//we need to process featured images separately later
	if ( ! empty( $old_data["image"][0]["attachment_id"] ) ) {
		$old_featured_image = $old_data["image"][0]["attachment_id"];
		unset( $old_data["image"][0]["attachment_id"] );
	}
	if ( ! empty( $new_data["image"][1000]["attachment_id"] ) ) {
		$new_featured_image = $new_data["image"][1000]["attachment_id"];
		unset( $new_data["image"][1000]["attachment_id"] );
	}
	$old_data_content_attachments = array();
	$new_data_content_attachments = array();

	array_walk_recursive( $old_data, function ( $item, $key ) use ( &$old_data_content_attachments ) {
		if ( $key == 'attachment_id' || $key == 'videofile' ) {
			$old_data_content_attachments[] = $item;
		}
	} );
	array_walk_recursive( $new_data, function ( $item, $key ) use ( &$new_data_content_attachments ) {
		if ( $key == 'attachment_id' || $key == 'videofile' ) {
			$new_data_content_attachments[] = $item;
		}
	} );

	$new_data_attachments = $new_data_content_attachments;
	$old_data_attachments = $old_data_content_attachments;

	if ( ! empty( $new_featured_image ) ) {

		//if featured image was taked from content, and not it's removed in new content, we need to remove it.
		if ( in_array( $new_featured_image, $old_data_content_attachments ) && ! in_array( $new_featured_image, $new_data_content_attachments ) ) {
			$old_data_attachments[] = $old_featured_image;
		} else {
			$old_data_attachments[] = $old_featured_image;
			$new_data_attachments[] = $new_featured_image;
		}

	}

	$added_attachments   = array_diff( $new_data_attachments, $old_data_attachments );
	$removed_attachments = array_diff( $old_data_attachments, $new_data_attachments );


	return array( 'added' => $added_attachments, 'removed' => $removed_attachments );

}

function zf_set_attachments_parent_id( $data, $post_id, $old_data = array() ) {
	$added_attachments = zf_compare_attachments( $data, $old_data );
	$added_attachments = $added_attachments['added'];

	if ( ! empty( $added_attachments ) ) {
		foreach ( $added_attachments as $item ) {
			wp_update_post(
				array(
					'ID'          => $item,
					'post_parent' => $post_id
				)
			);
		}
	}
}

/**
 *
 * Delete a directory RECURSIVELY
 *
 * @param string $dir - directory path
 *
 * @link http://php.net/manual/en/function.rmdir.php
 */
function zf_rrmdir( $dir ) {
	if ( is_dir( $dir ) ) {
		$objects = scandir( $dir );
		foreach ( $objects as $object ) {
			if ( $object != "." && $object != ".." ) {
				if ( filetype( $dir . "/" . $object ) == "dir" ) {
					zf_rrmdir( $dir . "/" . $object );
				} else {
					unlink( $dir . "/" . $object );
				}
			}
		}
		reset( $objects );
		rmdir( $dir );
	}
}

/**
 *
 * Check if all the parts exist, and
 * gather all the parts of the file together
 *
 * @param string $temp_dir  - the temporary directory holding all the parts of the file
 * @param string $fileName  - the original file name
 * @param string $chunkSize - each chunk size (in bytes)
 * @param string $totalSize - original file size (in bytes)
 * @param        $total_files
 *
 * @return bool|string
 */

function zf_createFileFromChunks( $temp_dir, $fileName, $chunkSize, $totalSize, $total_files ) {

	// count all the parts of this file
	$total_files_on_server_size = 0;
	$temp_total                 = 0;
	foreach ( scandir( $temp_dir ) as $file ) {
		$temp_total                 = $total_files_on_server_size;
		$tempfilesize               = filesize( $temp_dir . '/' . $file );
		$total_files_on_server_size = $temp_total + $tempfilesize;
	}
	// check that all the parts are present
	// If the Size of all the chunks on the server is equal to the size of the file uploaded.
	if ( $total_files_on_server_size >= $totalSize ) {

		$up_dir = wp_upload_dir();

		$attachment_path = $up_dir["path"] . '/' . $fileName;

		//case when the same file already exists
		if ( file_exists( $attachment_path ) ) {
			//generate a new file name to avoid overwriting the file
			$fileNewName     = pathinfo( $attachment_path, PATHINFO_FILENAME ) . '-' . uniqid() . '.' . pathinfo( $attachment_path, PATHINFO_EXTENSION );
			$attachment_path = $up_dir["path"] . '/' . $fileNewName;
		}

		$result = $attachment_path;

		// create the final destination file
		if ( ( $fp = fopen( $attachment_path, 'w' ) ) !== false ) {
			for ( $i = 1; $i <= $total_files; $i ++ ) {
				fwrite( $fp, file_get_contents( $temp_dir . '/' . $fileName . '.part' . $i ) );
			}
			fclose( $fp );
		} else {
			error_log( 'cannot create the destination file: ' . $attachment_path );
			$result = false;
		}

		// rename the temporary directory (to avoid access from other
		// concurrent chunks uploads) and than delete it
		zf_remove_temp( $temp_dir );

		return $result;
	} else {
		return false;
	}

}

function zf_remove_temp( $temp_dir ) {

	if ( rename( $temp_dir, $temp_dir . '_UNUSED' ) ) {
		zf_rrmdir( $temp_dir . '_UNUSED' );
	} else {
		zf_rrmdir( $temp_dir );
	}

}

function zf_get_downloaded_attachment( $post_id ) {

	$downloaded_images = get_post_meta( $post_id, 'zombify_downloaded_images', true );

	if ( ! $downloaded_images ) {
		$downloaded_images = array();
	}

	return $downloaded_images;

}

function zf_save_downloaded_attachment( $post_id, $attach_id, $url ) {

	$url_hash = md5( $url );

	$downloaded_images = get_post_meta( $post_id, 'zombify_downloaded_images', true );

	if ( ! $downloaded_images ) {
		$downloaded_images = array();
	}

	$downloaded_images[ $url_hash ] = $attach_id;

	update_post_meta( $post_id, "zombify_downloaded_images", $downloaded_images );

}

//@todo: we can use media_sideload_image instead of all this
function zf_get_file_by_url( $file_data_url, $decode = true, $local_file = false ) {

	if ( $decode ) {
		$file_data_url = htmlspecialchars_decode( $file_data_url );
	}

	$file_data = false;

	$up_dir = wp_upload_dir();

	$file_data_url_ext = pathinfo( $file_data_url, PATHINFO_EXTENSION );

	if ( ! in_array( strtolower( $file_data_url_ext ), array( "jpg", "jpeg", "gif", "png", "mp4" ) ) ) {
		$file_data_url_ext = 'jpg';
	}

	$filerandname = pathinfo( $file_data_url, PATHINFO_FILENAME );

	// This will get first 200 characters if file name is too big
	$filerandname = substr( $filerandname, 0, 200 );

	$filerandname = sanitize_file_name( $filerandname );

	$file_data_up_path = $up_dir["path"] . "/" . $filerandname . '.' . $file_data_url_ext;
	$file_data_up_url  = $up_dir["url"] . "/" . $filerandname . '.' . $file_data_url_ext;

	$i = 0;

	while ( file_exists( $file_data_up_path ) ) {

		$i ++;
		$file_data_up_path = $up_dir["path"] . '/' . $filerandname . '_' . $i . '.' . $file_data_url_ext;
		$file_data_up_url  = $up_dir["url"] . '/' . $filerandname . '_' . $i . '.' . $file_data_url_ext;

	}

	if ( $local_file ) {

		copy( $file_data_url, $file_data_up_path );

	} elseif ( function_exists( 'curl_version' ) ) {

		$ch = curl_init( $file_data_url );
		$fp = fopen( $file_data_up_path, 'wb' );
		curl_setopt( $ch, CURLOPT_FILE, $fp );
		curl_setopt( $ch, CURLOPT_HEADER, 0 );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, false );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
		curl_exec( $ch );
		curl_close( $ch );
		fclose( $fp );

	} else {

		file_put_contents( $file_data_up_path, file_get_contents( $file_data_url ) );

	}

	$max_file_size = zf_get_option( "zombify_max_upload_size" );
	$file_size     = filesize( $file_data_up_path );

	if ( $max_file_size >= $file_size ) {

		$mime_type = wp_check_filetype( $file_data_url )["type"];

		if ( ! $mime_type ) {
			$mime_type = 'image/jpg';
		}

		if ( ( $file_data_url_ext != 'mp4' && @is_array( getimagesize( $file_data_up_path ) ) ) || $file_data_url_ext == 'mp4' ) {

			// crop black borders on youtube videos
			if ( in_array( $file_data_url_ext, array( "jpg", "jpeg", "gif", "png" ) ) ) {
				zf_youtube_crop( $file_data_up_path, $file_data_url );
			}

			$file_data["name"] = pathinfo( $file_data_up_path, PATHINFO_BASENAME );
			$file_data["size"] = $file_size;
			$file_data["type"] = $mime_type;

			$file_data["uploaded"] = array(
				"file"     => $file_data_up_path,
				"url"      => $file_data_up_url,
				"type"     => $mime_type,
				"original" => $file_data_url
			);


		} else {

			@unlink( $file_data_up_path );

			throw new Exception( __( "Incorrect file type", "zombify" ) );

		}

	} else {

		@unlink( $file_data_up_path );

		throw new Exception( __( "File is too large. Maximum size is ", "zombify" ) . round( ( $max_file_size > 0 ? $max_file_size / 1024 / 1024 : 0 ), 2 ) . __( "MB", "zombify" ) );

	}

	if ( ! $file_data ) {
		throw new Exception( __( "There was a problem while getting the file by url", "zombify" ) );
	}

	return $file_data;

}

function zf_get_file_upload( $file ) {

	if ( $file["size"] > zf_get_option( "zombify_max_upload_size" ) ) {

		throw new Exception( __( 'File is too large.', 'zombify' ) );

		return false;
	}

	$file["name"] = $file["name"];

	if ( ! function_exists( 'wp_handle_upload' ) ) {
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
	}

	$upload_overrides = array( 'test_form' => false );
	$file["uploaded"] = wp_handle_upload( $file, $upload_overrides );

	if ( isset( $file["uploaded"]["original"] ) ) {
		zf_youtube_crop( $file["uploaded"]["file"], $file["uploaded"]["original"] );
	}

	return $file;

}

function zf_youtube_crop( $file_data_up_path, $file_data_url ) {

	if ( preg_match( '/img.youtube/', $file_data_url ) ) {
		$t_width      = 480;
		$t_height     = 360;
		$border_width = 45;

		$start_x = 0;
		$start_y = $border_width;
		$crop_w  = $t_width;
		$crop_h  = $t_height - ( $border_width * 2 );

		$image_editor = wp_get_image_editor( $file_data_up_path );
		$image_editor->crop( $start_x, $start_y, $crop_w, $crop_h );
		$image_editor->save( $file_data_up_path );
	}

}

function zf_insert_attachment( $post_id, $file_data_up_path, $mime_type, $as_featured = false ) {

	$original_file_data_up_path = $file_data_up_path;

	//$file_data_up_path = zf_create_image_resize_temp( $original_file_data_up_path );

	// Prepare an array of post data for the attachment.
	$attachment = array(
		'guid'           => $original_file_data_up_path,
		'post_mime_type' => $mime_type,
		'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $original_file_data_up_path ) ),
		'post_content'   => '',
		'post_status'    => 'inherit',
		'post_parent'    => $post_id,
		'meta_input'     => array(
			'zf_attachment' => 1
		)
	);

	// Insert the attachment.
	$attach_id = wp_insert_attachment( $attachment, $original_file_data_up_path );

	// Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
	if ( ! function_exists( "wp_generate_attachment_metadata" ) ) {
		require_once( ABSPATH . 'wp-admin/includes/image.php' );
	}

	// Generate the metadata for the attachment, and update the database record.
	$attach_data = wp_generate_attachment_metadata( $attach_id, $file_data_up_path );
	wp_update_attachment_metadata( $attach_id, $attach_data );

	do_action( "zombify_insert_attachment", $attach_id, $post_id, false );

	$size_urls = array();

	$zombify_small_image        = wp_get_attachment_image_src( $attach_id, 'zombify_small' );
	$size_urls["zombify_small"] = isset( $zombify_small_image[0] ) ? $zombify_small_image[0] : '';

	if ( $as_featured ) {

		zf_set_post_thumbnail( $post_id, $attach_id );

	}

	if ( $file_data_up_path != $original_file_data_up_path ) {

		@unlink( $file_data_up_path );

	}


	return array(
		"id"        => $attach_id,
		"size_urls" => $size_urls,
	);

}

function zf_create_image_resize_temp( $file ) {

	global $_wp_additional_image_sizes;

	$image_data = getimagesize( $file );

	if ( ! is_array( $image_data ) || ! isset( $image_data[0] ) || ! isset( $image_data[1] ) || $image_data[0] <= 0 || $image_data[1] <= 0 ) {
		return $file;
	}

	$width  = $image_data[0];
	$height = $image_data[1];

	$max_width  = 0;
	$max_height = 0;

	foreach ( $_wp_additional_image_sizes as $size ) {

		if ( $size["width"] > $max_width ) {
			$max_width = $size["width"];
		}
		if ( $size["height"] > $max_height ) {
			$max_height = $size["height"];
		}

	}

	$max_width  += 100;
	$max_height += 100;

	if ( $max_width && $max_height && $width > $max_width && $height > $max_height ) {

		$n_width  = 0;
		$n_height = 0;

		if ( round( ( $max_width * $height ) / $width ) >= $max_height ) {

			$n_width  = $max_width;
			$n_height = round( ( $max_width * $height ) / $width );

		} else {

			if ( round( ( $max_height * $width ) / $height ) >= $max_width ) {

				$n_width  = round( ( $max_height * $width ) / $height );
				$n_height = $max_height;

			}

		}

		if ( $n_width && $n_height ) {

			$path_parts = pathinfo( $file );

			$editor = wp_get_image_editor( $file );

			$editor->resize( $n_width, $n_height );

			$n_file = $path_parts['dirname'] . '/zombify_' . $path_parts['filename'] . '.' . $path_parts['extension'];

			$editor->save( $n_file );

			return $n_file;

		}

	}

	return $file;

}

/**
 * Check if current user has post create access
 *
 * @param int $user_id The user ID to check the permission
 *
 * @return bool
 */
function zf_user_can_create( $user_id = 0 ) {

	$can_create = false;
	if ( is_user_logged_in() ) {

		if ( ! $user_id ) {
			$user_id = get_current_user_id();
		}
		$user = get_user_by( 'ID', $user_id );

		while ( true ) {

			/***** Super admin can create anytime */
			if ( is_super_admin() ) {
				$can_create = true;
				break;
			}

			$user_role_permissions = zombify()->user_roles_permissions();

			/***** Check by current user role */
			if ( (bool) array_intersect( $user->roles, $user_role_permissions['create'] ) ) {
				$can_create = true;
				break;
			}

			break;
		}

	}

	return $can_create;

}

/**
 * Check if current user has post edit access
 *
 * @param int|string $post_id The post ID
 * @param int        $user_id The user ID to check the permission
 *
 * @return bool
 */
function zf_user_can_edit( $post_id, $user_id = 0 ) {

	$can_edit = false;
	if ( is_user_logged_in() ) {

		if ( ! $user_id ) {
			$user_id = get_current_user_id();
		}
		$user = get_user_by( 'ID', $user_id );

		while ( true ) {

			/***** Super admin can edit anytime */
			if ( is_super_admin() ) {
				$can_edit = true;
				break;
			}

			$user_role_permissions = zombify()->user_roles_permissions();

			/***** Check by current user role */
			if ( (bool) array_intersect( $user->roles, $user_role_permissions['edit'] ) ) {
				$can_edit = true;
				break;
			}

			$post = get_post( $post_id );
			if ( $post->post_author == $user_id ) {

				/***** Post author can edit while post is in draft */
				if ( in_array( $post->post_status, array( 'pending', 'draft' ) ) ) {
					$can_edit = true;
					break;
				}

				/***** Post author can edit his own post anytime */
				if ( (bool) array_intersect( $user->roles, $user_role_permissions['edit_own'] ) ) {
					$can_edit = true;
					break;
				}
			}

			break;
		}

	}

	return apply_filters( 'zf_user_can_edit_post', $can_edit, $post_id );

}

/**
 * Check if current user has post publish access
 *
 * @param int|string $post_id $post_id The post ID
 * @param int        $user_id The user ID to check the permission
 *
 * @return bool
 */
function zf_user_can_publish( $post_id = 0, $user_id = 0 ) {

	$can_publish = false;

	if ( is_user_logged_in() ) {

		if ( ! $user_id ) {
			$user_id = get_current_user_id();
		}
		$user = get_user_by( 'ID', $user_id );

		while ( true ) {

			/***** Super admin can publish anytime */
			if ( is_super_admin() ) {
				$can_publish = true;
				break;
			}

			$user_role_permissions = zombify()->user_roles_permissions();

			/***** Check by current user role */
			if ( (bool) array_intersect( $user->roles, $user_role_permissions['edit'] ) ) {
				$can_publish = true;
				break;
			}

			/***** Post author can edit his own post anytime */
			$is_author = $post_id ? ( get_post_field( 'post_author', $post_id ) == get_current_user_id() ) : false;
			if ( ( ! $post_id || $is_author ) && (bool) array_intersect( $user->roles, $user_role_permissions['edit_own'] ) ) {
				$can_publish = true;
				break;
			}

			break;

		}

	}

	return $can_publish;

}

function zf_save_shutdown() {

	$error = error_get_last();

	if ( $error != null && ! headers_sent() && isset( $error["type"] ) && $error["type"] == 1 ) {
		echo json_encode( $error );
	}

}

//check and delete attachment if it's orphan 
function zf_delete_media() {

	if ( isset( $_GET["attach_id"] ) && (int) $_GET["attach_id"] > 0 ) {

		if ( $post = get_post( (int) $_GET["attach_id"] ) ) {

			$zf_attachment = get_post_meta( $post->ID, 'zf_attachment', true );

			if ( $zf_attachment && $post->post_type == 'attachment' && $post->post_parent == 0 && zf_user_can_edit( $post->ID ) ) {
				//todo: we may delete featured image, which can be required in zombify
				wp_delete_attachment( $post->ID, true );

				return true;

			}

		}

	}

	return false;

}

function zf_max_upload_size() {

	$seled_size = zf_get_option( "zombify_max_upload_size" );
	$max_size   = zombify()->get_file_upload_max_size();

	return $max_size >= $seled_size ? $seled_size : $max_size;

}

function zf_get_categories_dropdown( $attributes = array(), $selected = array(), $predef_options = array() ) {

	$cats = get_categories( array(
		"hide_empty" => 0
	) );

	$html = '<select ' . join( ' ', array_map( function ( $key ) use ( $attributes ) {
			if ( is_bool( $attributes[ $key ] ) ) {
				return $attributes[ $key ] ? $key : '';
			}

			return $key . '="' . $attributes[ $key ] . '"';
		}, array_keys( $attributes ) ) ) . '>';

	foreach ( $predef_options as $index => $value ) {

		$html .= '<option value="' . $index . '" ' . ( in_array( $index, $selected ) ? 'selected' : '' ) . ' data-parent-id="0">' . $value . '</option>';

	}

	$html .= zf_get_categories_dropdown_options( $cats, 0, $selected );

	$html .= '</select>';

	return $html;

}

function zf_get_categories_dropdown_options( $cats, $parent_id, $selected = array(), $level = 0 ) {

	$html = '';

	foreach ( $cats as $cat ) {

		if ( $cat->parent != $parent_id ) {
			continue;
		}

		$padding = '';
		for ( $l = 0; $l < $level; $l ++ ) {
			$padding .= '&nbsp;&nbsp;&nbsp;';
		}

		$html .= '<option value="' . $cat->term_id . '" data-val="' . $cat->term_id . '" ' . ( in_array( $cat->term_id, $selected ) ? 'selected' : '' ) . ' data-parent-id="' . $cat->parent . '">' . $padding . $cat->name . '</option>';

		$html .= zf_get_categories_dropdown_options( $cats, $cat->term_id, $selected, $level + 1 );

	}

	return $html;

}

function zf_get_option( $option_name, $default = false ) {

	$default_options = zombify()->get_default_options();

	$db_value = get_option( $option_name, $default );

	if ( isset( $default_options[ $option_name ] ) ) {

		if ( is_array( $default_options[ $option_name ] ) ) {

			if ( $db_value !== false ) {

				$value = $default_options[ $option_name ];

				$value = array_merge( $value, $db_value );

				return $value;

			} else {

				return $default_options[ $option_name ];

			}

		} else {

			return $db_value !== false ? $db_value : $default_options[ $option_name ];

		}

	} else {

		return $db_value;

	}

}

function zf_set_post_thumbnail( $post_id, $thumbnail_id ) {

	$thumbnail_id = apply_filters( "zombify_set_featured_image", $thumbnail_id, $post_id, true );

	set_post_thumbnail( $post_id, $thumbnail_id );

}

function zf_generateRandomString( $length = 8 ) {
	$characters       = '0123456789abcdefghijklmnopqrstuvwxyz';
	$charactersLength = strlen( $characters );
	$randomString     = '';
	for ( $i = 0; $i < $length; $i ++ ) {
		$randomString .= $characters[ rand( 0, $charactersLength - 1 ) ];
	}

	return $randomString;
}

function zombify_get_virtual_post_id( $quiz_type, $quiz_sub_type = false, $create_if_not_exist = true ) {

	$meta_query = array(
		array(
			'key'     => 'zombify_virtual_post',
			'value'   => '1',
			'compare' => '=',
		),
		array(
			'key'     => 'zombify_data_type',
			'value'   => addslashes( $quiz_type ),
			'compare' => '=',
		)
	);

	if ( $quiz_sub_type !== false ) {
		$meta_query[] = array(
			'key'     => 'zombify_data_subtype',
			'value'   => addslashes( $quiz_sub_type ),
			'compare' => '=',
		);
	}

	$vargs  = array(
		'author'      => get_current_user_id(),
		'post_type'   => 'zf_vpost',
		'post_status' => 'any',
		'meta_query'  => $meta_query
	);
	$vposts = get_posts( $vargs );

	if ( count( $vposts ) > 0 ) {

		$vpost_id = $vposts[0]->ID;

	} else {

		if ( $create_if_not_exist ) {

			$vpost_args = array(
				'post_author'           => get_current_user_id(),
				'post_content'          => '',
				'post_content_filtered' => '',
				'post_title'            => 'Zombify Virtual Post',
				'post_excerpt'          => '',
				'post_status'           => 'draft',
				'post_type'             => 'zf_vpost',
				'post_parent'           => 0,
				'comment_status'        => zf_get_option( "default_comment_status" ),
			);
			$vpost_id   = wp_insert_post( $vpost_args );

			update_post_meta( $vpost_id, "zombify_virtual_post", "1" );
			update_post_meta( $vpost_id, "zombify_data_type", addslashes( $quiz_type ) );
			update_post_meta( $vpost_id, "zombify_data_subtype", addslashes( $quiz_sub_type ) );

		} else {
			return false;
		}

	}

	return $vpost_id;

}

function zf_share_html( $tw_url = false, $fb_url = false ) {
	$title = get_the_title();

	if ( ! $tw_url ) {
		$tw_url = 'https://twitter.com/intent/tweet?text=' . zf_get_post_title_for_share( $title ) . '+' . urlencode( get_permalink() );
	}

	if ( ! $fb_url ) {
		$fb_url = 'http://www.facebook.com/share.php?u=' . urlencode( get_permalink() ) . '&title=' . zf_get_post_title_for_share( $title );
	}

	?>
	<div class="zf-result_share">
		<div class="zf-share_text"><?php esc_html_e( "Share Your Result", "zombify" ); ?></div>
		<div class="zf-share_box">
			<a class="zf-share zf_twitter" target='_blank'
			   href="<?php echo $tw_url; ?>">
				<i class="zf-icon zf-icon-twitter"></i><?php esc_html_e( "Twitter", "zombify" ); ?></a>
			<a class="zf-share  zf_facebook" target='_blank'
			   href="<?php echo $fb_url; ?>">
				<i class="zf-icon zf-icon-facebook"></i><?php esc_html_e( "Facebook", "zombify" ); ?>
			</a>
		</div>
	</div>
	<?php

}

/**
 * Flush post cache by ID
 *
 * @param int|string $post_id The post ID
 */
function zf_flush_post_cache( $post_id ) {
	do_action( 'zf_flush_post_by_id', (int) $post_id );
}

/**
 * Flush post by URL
 *
 * @param string $url The post URL
 */
function zf_flush_post_cache_by_url( $url ) {
	do_action( 'zf_flush_post_by_url', $url );
}

/**
 * Get plugin shortcode
 *
 * @param array $args
 * @param bool  $after
 * @param bool  $before
 *
 * @return string
 */
function zf_get_shortcode( $args = array(), $after = false, $before = false ) {

	$parsed_args = '';
	foreach ( $args as $attr => $value ) {
		$parsed_args .= sprintf( ' %1$s="%2$s"', $attr, $value );
	}

	return sprintf( '%1$s[%2$s%3$s]%4$s', $before, zf_get_shortcode_name(), $parsed_args, $after );
}

/**
 * Get plugin shortcode name
 *
 * @return string
 */
function zf_get_shortcode_name() {
	return 'zombify_post';
}

/**
 * Remove shortcode different variations from provided text
 *
 * @param $text
 *
 * @return mixed
 */
function zf_remove_shortcode( $text ) {

	$shortcode = zf_get_shortcode();
	$search    = array(
		sprintf( '<p class="zf-hide-shortcode">%s</p>', $shortcode ),
		sprintf( '<p>%s</p>', $shortcode ),
		$shortcode
	);
	$replace   = "";

	return str_replace( $search, $replace, $text );

}

/**
 * Append shortcode to provided text
 *
 * @param $text
 *
 * @return string
 */
function zf_append_shortcode( $text ) {
	return $text . zf_get_shortcode();
}

/**
 * Check for user daily limit
 *
 * @return bool
 */
function zf_user_exceeded_daily_limit() {

	while ( true ) {
		/***** check for user for authentication */
		if ( ! is_user_logged_in() ) {
			$has_exceeded = true;
			break;
		}

		/***** check check by logged in user role */
		if ( ! in_array( 'contributor', wp_get_current_user()->roles ) ) {
			$has_exceeded = false;
			break;
		}

		/***** check for max count to be a positive number greater than zero number */
		$max_posts_count = absint( zf_get_option( 'zombify_contributor_can_submit', 0 ) );
		if ( ! $max_posts_count ) {
			$has_exceeded = false;
			break;
		}

		/***** Finaly check by current user posts submitted today */
		$query = new WP_Query( array(
			'author'         => get_current_user_id(),
			'post_type'      => 'post',
			'post_status'    => 'any',
			'posts_per_page' => - 1,
			'meta_query'     => array(
				array(
					'key'     => 'zombify_data_type',
					'value'   => '',
					'compare' => '!=',
				)
			),
			'date_query'     => array(
				array(
					'after'     => current_time( "Y-m-d 00:00:00" ),
					'inclusive' => true,
				),
			),
		) );

		$has_exceeded = ( $query->found_posts >= $max_posts_count );
		break;
	}

	return $has_exceeded;

}

/**
 * Check date validity
 *
 * @param $date
 * @param $format
 *
 * @return bool
 */
function zf_validateDate( $date, $format ) {
	$d = DateTime::createFromFormat( $format, $date );

	return ( $d && $d->format( $format ) === $date );
}

/**
 * Get items per page
 *
 * @param array $zf_shortcode_args
 *
 * @return int
 */
function zf_get_items_per_page( $zf_shortcode_args = array() ) {
	// why 957426? just in case, so, don't use this number to compare something
	$items_per_page = 957426;
	if ( isset( $zf_shortcode_args['zf_items_per_page'] ) && $zf_shortcode_args['zf_items_per_page'] > 0 ) {
		$items_per_page = (int) $zf_shortcode_args['zf_items_per_page'];
	}

	return $items_per_page;
}

/**
 * Get current page number
 *
 * @param $zf_shortcode_args
 *
 * @return int
 */
function zf_get_current_page( $zf_shortcode_args ) {
	$current_page = 1;
	if ( isset( $zf_shortcode_args['zf_page'] ) && ctype_digit( $zf_shortcode_args['zf_page'] ) ) {
		$current_page = (int) $zf_shortcode_args['zf_page'];
	}

	return $current_page;
}

/**
 * Get image url generated for video
 *
 * @param int $video_file_id
 *
 * @return mixed
 */
function zf_get_video_image_url( $video_file_id ) {
	return get_post_meta( $video_file_id, 'zombify_jpeg_url', true );
}

/**
 * Get quiz class name
 *
 * @param $quiz_type
 *
 * @return string
 */
function zf_get_quiz_class_name( $quiz_type ) {
	return sprintf( 'Zombify_%sQuiz', ucfirst( strtolower( $quiz_type ) ) );
}

/**
 * Get category walker
 *
 * @return ZF_Category_Walker
 */
function zf_get_category_walker() {
	if ( ! class_exists( 'ZF_Category_Walker' ) ) {
		require_once( zombify()->includes_dir . 'classes/class-zf-category-walker.php' );
	}

	return new ZF_Category_Walker();
}

/**
 * Encode data to plugin specific format
 *
 * @param $data
 *
 * @return string
 */
function zf_encode_data( $data ) {
	return base64_encode( json_encode( $data ) );
}

/**
 * Decode data from plugin specific format
 *
 * @param $data
 *
 * @return array|mixed|null|object
 */
function zf_decode_data( $data ) {
	return json_decode( base64_decode( $data ), true );
}

function zf_get_meme_templates_folder() {

	if ( is_dir( zombify()->theme_dir . '/zombify/assets/images/meme-templates' ) ) {
		$zombify_meme_templates_dir = zombify()->theme_dir . '/zombify/assets/images/meme-templates/';
		$zombify_meme_templates_url = zombify()->theme_url . '/zombify/assets/images/meme-templates/';

	} else {
		$zombify_meme_templates_dir = zombify()->plugin_dir . 'assets/images/meme-templates/';
		$zombify_meme_templates_url = zombify()->assets_url . 'images/meme-templates/';
	}

	return [
		'zombify_meme_templates_dir' => $zombify_meme_templates_dir,
		'zombify_meme_templates_url' => $zombify_meme_templates_url
	];

}

function zf_modify_meme_template_url( $url ) {

	$path_array                        = explode( '/', $url );
	$image_name                        = end( $path_array );
	$zombify_meme_templates_folder_arr = zf_get_meme_templates_folder();

	return $zombify_meme_templates_folder_arr['zombify_meme_templates_dir'] . $image_name;
}

/**
 * Returns 'html_entity_decoded' title for facebook/twitter share
 *
 * @param string $title
 *
 * @return string
 */
function zf_get_post_title_for_share( $title ) {
	return urlencode( html_entity_decode( $title, ENT_COMPAT, 'UTF-8' ) );
}

/**
 * Check whether html contains an element with specific extension within src attribute
 *
 * @param string       $html        HTML to check
 * @param string|array $extenstions Pipe ( "|" ) separated list of extensions or array of extensions
 *
 * @return bool
 */
function zf_html_contains_src_extension( $html, $extenstions ) {
	if ( is_array( $extenstions ) ) {
		$extenstions = implode( '|', $extenstions );
	}
	$pattern = '/src="(.*\.(' . $extenstions . '))"/i';
	preg_match( $pattern, $html, $has_extenstion );

	return (bool) $has_extenstion;
}

/**
 * Setup image data from image HTML
 *
 * @param string $html Current HTML
 *
 * @return string
 */
function zf_setup_data_for_image_tag( $html = '' ) {
	if ( $html ) {
		if ( zf_html_contains_src_extension( $html, 'gif' ) ) {
			ZF_Data_Provider::set( 'current_media_type', 'image/gif' );
		} elseif ( zf_html_contains_src_extension( $html, 'mp4' ) ) {
			ZF_Data_Provider::set( 'current_media_type', 'video/mp4' );
		} else {
			ZF_Data_Provider::set( 'current_media_type', 'image' );
		}
	}

	return $html;
}

add_filter( 'zombify_img_tag', 'zf_setup_data_for_image_tag', 999, 1 );

/**
 * Get media wrapper classes
 *
 * @param string $classes Current classes
 *
 * @return string
 */
function zf_get_media_wrapper_classes( $classes = '' ) {
	$media_type = ZF_Data_Provider::get_clean( 'current_media_type', '' );
	if ( $media_type ) {
		switch ( $media_type ) {
			case 'image/gif':
				$classes .= ' zf-type-gif';
				break;
			case 'video/mp4':
				$classes .= ' zf-type-video-gif';
				break;
			default:
				$classes .= ' zf-type-image';
		}
	}

	return $classes;
}

/**
 * Determines whether the current page relate to an received page.
 *
 * @param $page_id
 *
 * @return bool
 *
 */
function zf_same_page_or_translate( $page_id ) {

	if ( is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) ) {
		global $sitepress;

		//If active language is not default language.
		if ( $sitepress->get_default_language() != ICL_LANGUAGE_CODE ) {
			$default_lang                      = $sitepress->get_default_language();
			$translated_id_of_the_current_page = apply_filters( 'wpml_object_id', get_the_ID(), 'page', true, $default_lang );

			//Case when original page of current translation and $page_id are same pages.
			if ( $page_id == $translated_id_of_the_current_page ) {
				return true;
			}
		}

	}

	return is_page( $page_id );
}