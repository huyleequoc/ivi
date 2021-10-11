<?php
$user_comment_must_login = (int) zf_get_option( "comment_registration" );
$items_per_page          = zf_get_items_per_page( $zf_shortcode_args );
$current_page            = zf_get_current_page( $zf_shortcode_args );

?>
<div id="zombify-main-section-front" class="<?php echo zombify_get_front_main_section_classes( 'zombify-main-section-front zombify-screen' ); ?>">

	<div class="zf-container">


		<div id="zf-open_list" class="zf-open_list zf-list  zf-numbered">
			<ol class="zf-structure-list">
				<?php
				$index    = 1;
				$datalist = array();
				if ( isset( $data["list"] ) ) {
					foreach ( $data["list"] as $dl ) {
						$postObj = get_post( $dl["post_id"] );
						if ( ! zf_user_can_edit( $dl["post_id"] ) ) {
							$postObj = get_post( $dl["post_id"] );
							if ( $postObj->post_status != 'publish' ) {
								continue;
							}
						}
						$dl["wp_post_object"] = $postObj;
						$datalist[]           = $dl;
					}

					$index        += ( $current_page - 1 ) * $items_per_page;
					$data["list"] = array_slice( $datalist, ( $current_page - 1 ) * $items_per_page, $items_per_page );
					foreach ( $data["list"] as $list ) {
						$postObj = $list["wp_post_object"]; ?>
						<li class="zf-list_item">
							<div class="zf-list_header">
								<h2 class="zf-list_title">
									<span class="zf-number"><?php echo $index; ?></span>
									<a href="<?php echo get_permalink( $list["post_id"] ); ?>"><?php echo $list["title"]; ?></a>
								</h2>
							</div>
							<?php
							if ( ( $list["mediatype"] == 'image' && isset( zf_array_values( $list["image"] )[0]["attachment_id"] ) ) || ( $list["mediatype"] == 'embed' && $list["embed_url"] != '' ) ) {
								switch ( $list["mediatype"] ) {
									case 'image':
										$zf_media_html = '';
										if ( isset( zf_array_values( $list["image"] )[0]["attachment_id"] ) ) {
											$zf_media_html = zombify_get_img_tag( zf_array_values( $list["image"] )[0]["attachment_id"], 'full' );
										}
										break;
									case 'embed':
										$zf_media_html = sprintf( '<div class="zf-embedded-url">%s</div>', Zombify_BaseQuiz::renderEmbed( $list, true ) );
										break;
									default:
										$zf_media_html = '';
								}
								$zf_media_wrapper_classes = zf_get_media_wrapper_classes( 'zf-list_media zf-media-wrapper zf-' . $list["mediatype"] ); ?>
								<figure class="<?php echo esc_attr( $zf_media_wrapper_classes ); ?>">
									<div class="zf-media">
										<?php
										echo $zf_media_html;

										if ( isset( $list["image_credit"] ) ) { ?>

											<figcaption class="zf-figcaption">
												<cite><?php zf_showCredit( $list["image_credit"], ( isset( $list["image_credit_text"] ) ? $list["image_credit_text"] : '' ) ); ?></cite>
												<?php if ( isset( $list["affiliate"] ) && $list["affiliate"] && $list["shop_url"] != '' ) {
													$shop_button_text = ! empty( $list['shop_button_text'] ) ? $list['shop_button_text'] : __( "Buy Now", "zombify" );
													?>

													<a class="zf-buy-button" href="<?php echo esc_url( $list["shop_url"] ); ?>"
													   target="_blank" rel="nofollow noopener">
														<i class="zf-icon zf-icon-buy_now"></i>
														<span><?php esc_html_e( $shop_button_text, "zombify" ); ?></span>
													</a>

												<?php } ?>
											</figcaption>

										<?php } ?>
									</div>
									<hr />
								</figure>
								<?php
							}
							?>
							<div class="zf-item_meta">
                              <span class="zf-author">
								  <span class="zf-author_avatar">
									  <a href="<?php echo get_author_posts_url( $postObj->post_author ); ?>"><?php echo get_avatar( $postObj->post_author ); ?></a>
								  </span>
								  <span class="zf-author_info"><span class="zf-author_name">
										  <a href="<?php echo get_author_posts_url( $postObj->post_author ); ?>">
											  <?php echo get_the_author_meta( 'display_name', $postObj->post_author ); ?>
										  </a></span>
									  <span class="zf-posted-on">
										  <time>
											  <?php
											  if ( function_exists( 'booombox_get_single_post_date' ) ) {
												  echo booombox_get_single_post_date( 'published', $postObj->ID );
											  }
											  ?>
										  </time>
									  </span>
								  </span>
							  </span>
								<?php
								if ( get_post_meta( get_the_ID(), "openlist_close_voting", true ) != 1 ) { ?>
									<div class="zf-item-vote-box">
										<div class="zf-item-vote" data-zf-post-id="<?php echo $list["post_id"]; ?>"
											 data-zf-post-parent-id="<?php echo get_the_ID(); ?>">
											<button class="zf-vote_btn zf-vote_up"><i
														class="zf-icon zf-icon-vote_up"></i>
											</button>
											<span class="zf-vote_count"
												  data-zf-post-id="<?php echo $list["post_id"]; ?>">
                                          <i class="zf-icon zf-spinner-pulse"></i>
                                          <span class="zf-vote_number">
                                              <?php echo (int) get_post_meta( $list["post_id"], "zombify_post_rateing", true ); ?>
                                          </span>
                                      </span>
											<button class="zf-vote_btn zf-vote_down"><i
														class="zf-icon zf-icon-vote_down"></i></button>
										</div>
									</div>
									<?php
								}
								?>
							</div>
							<?php if ( $list["description"] ) { ?>
								<div class="zf-list_description"><?php echo html_entity_decode( $list["description"] ); ?></div><?php } ?>

							<?php if ( get_post_meta( get_the_ID(), "openlist_hide_comments", true ) != 1 ) { ?>
								<div id="zf-comments-<?php echo $list["post_id"]; ?>"
									 data-post-id="<?php echo $list["post_id"]; ?>" class="zf-comments">
									<?php
									$commenter = wp_get_current_commenter();
									$req       = zf_get_option( 'require_name_email' );
									$aria_req  = ( $req ? " aria-required='true'" : '' );

									if ( ! $user_comment_must_login || is_user_logged_in() ) {

										$comment_args = array(
											"fields"               => array(
												'author' =>
													'<div class="zf-comment-form-author">' .
													'<input id="zf-author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) .
													'" size="30"' . $aria_req . ' placeholder="' . __( 'Name *', 'zombify' ) . '"/></div>',

												'email' =>
													'<div class="zf-comment-form-email">' .
													'<input id="zf-email" name="email" type="text" value="' . esc_attr( $commenter['comment_author_email'] ) .
													'" size="30"' . $aria_req . '  placeholder="' . __( 'Email *', 'zombify' ) . '"/></div>',
											),
											"comment_notes_before" => '<div class="zf-comment-notes">' . __( 'Your email address will not be published.' ) . ( $req ? 'Required fields are marked *' : '' ) . '</div>',
											"comment_field"        => '<input type="hidden" name="redirectback" value="1"/><div class="zf-comment-form-comment"><textarea id="zf-comment" name="comment" cols="45" rows="8" aria-required="true"  placeholder="' . __( 'Write a comment', 'zombify' ) . '"></textarea></div>',
											"id_form"              => "zf-commentform",
											"class_form"           => "zf-comment-form",
											"id_submit"            => "zf-submit",
											"class_submit"         => "zf-submit",
											"title_reply_before"   => '',
											"title_reply_after"    => '',
											"title_reply"          => '',
											"title_reply_to"       => '',
											"label_submit"         => __( "Post", "zombify" ),
											"cancel_reply_before"  => '<small class="zf-cancel-reply">'
										);

										$comment_args = apply_filters( "zf_comment_args", $comment_args );

										comment_form( $comment_args, $list["post_id"] );

									} else {
										?>
										<div id="zf-commentform" class="zf-comment-form"><a
													class="zf-login-popup js-authentication" href="#sign-in"></a><input
													type="hidden" name="redirectback" value="1" />
											<div class="zf-comment-form-comment"><textarea id="zf-comment"
																						   name="comment" cols="45"
																						   rows="8" aria-required="true"
																						   placeholder="<?php esc_attr_e( "Write a comment", "zombify" ); ?>"></textarea>
											</div>
											<p class="form-submit"><input name="submit" type="submit" id="zf-submit"
																		  class="zf-submit"
																		  value="<?php esc_attr_e( "Post", "zombify" ); ?>" />
											</p></div>
										<?php
									}
									?>
									<div class="zf-comments-box">
										<?php

										$curr_user = wp_get_current_user();

										$comments = get_comments(
											array(
												"post_id"            => $list["post_id"],
												"status"             => "approve",
												"include_unapproved" => array( $curr_user->user_email )

											)
										);

										$comments_count = get_comments(
											array(
												"post_id"            => $list["post_id"],
												"status"             => "approve",
												"parent"             => 0,
												"include_unapproved" => array( $curr_user->user_email )

											)
										);

										$per_page      = 5;
										$on_first_page = 1;

										$pages_count = ceil( ( count( $comments_count ) - $on_first_page ) / $per_page ) + 1;

										wp_list_comments(
											array(
												'walker'            => null,
												'max_depth'         => '',
												'style'             => 'div',
												'callback'          => null,
												'end-callback'      => null,
												'type'              => 'all',
												'reply_text'        => __( 'Reply', 'zombify' ),
												'page'              => '',
												'per_page'          => $on_first_page,
												'avatar_size'       => 32,
												'reverse_top_level' => null,
												'reverse_children'  => '',
												'format'            => 'html5',
												// or 'xhtml' if no 'HTML5' theme support
												'short_ping'        => false,
												// @since 3.6
												'echo'              => true
												// boolean, default is true
											),
											$comments
										);
										?>
									</div>
									<?php
									if ( $pages_count > 1 ) {
										?>
										<div class="zf-comments_load_more">
											<a href="#" data-post-id="<?php echo $list["post_id"]; ?>"
											   data-pages-count="<?php echo $pages_count; ?>"
											   data-page="2"><?php _e( "view more comments", "zombify" ) ?></a><span
													class="zf-spinner-pulse"></span>
										</div>
										<?php
									}

									?>
								</div>
							<?php } // Close comments condition
							?>
						</li>
						<?php
						$index ++;
					}
				} ?>
			</ol>
		</div>
	</div>

</div>

<div id="zombify-main-section" class="zombify-main-section zf-front-submission zombify-screen">
	<?php
	if ( get_post_meta( get_the_ID(), "openlist_close_submission", true ) != 1 ) {
		?>
		<div class="zf-upload-content">
			<div class="zf-head">
				<i class="zf-icon zf-icon-add"></i> <?php esc_html_e( "Add Your Content", "zombify" ); ?>
			</div>
			<div class="zf-body">
				<?php if ( is_user_logged_in() ){ ?>
				<form class="zombify_quiz" action="<?php echo get_permalink( get_the_ID() ); ?>#new-item" method="post"
					  enctype="multipart/form-data"><?php } else {
						?>
						<a class="zf-login-popup js-authentication" href="#sign-in"></a>
						<?php
					} ?>
					<div class="zf-form-group zf-form-group_media zf-dependency-show">
						<div class="zf-media-uploader zf-openlist"
							 data-format="<?php echo isset( $_POST["zombify_openlist"]["mediatype"] ) && count( $zf_openlist_errors ) ? $_POST["zombify_openlist"]["mediatype"] : 'image' ?>">
							<div class="zf-media-type">
								<label class="zf-checkbox-format">
									<input type="radio" name="zombify_openlist[mediatype]" value="image"
										   data-format="image" checked class="zombify_medatype_radio">
									<span class="zf-toggle">
                                    <span class="zf-icon zf-icon-image"></span>
                                    <span class="zf-text"><?php esc_html_e( "Upload Image", "zombify" ); ?></span>
                                </span>
								</label>
								<span class="_or"><?php esc_html_e( "Or", "zombify" ); ?></span>
								<label class="zf-checkbox-format">
									<input type="radio" name="zombify_openlist[mediatype]" value="embed"
										   data-zombify-name-index="0" data-format="embed"
										   class="zombify_medatype_radio">
									<span class="zf-toggle">
                                    <span class="zf-icon zf-icon-embed"></span>
                                    <span class="zf-text"><?php esc_html_e( "Embed Content", "zombify" ); ?></span>
                                </span>
								</label>
							</div>
							<div
									class="zf-form-group <?php echo count( $zf_openlist_errors ) ? '' : 'zf-hide'; ?> <?php echo isset( $zf_openlist_errors["title"] ) ? 'zf-error' : '' ?>">
								<input type="text" name="zombify_openlist[title]"
									   placeholder="<?php esc_attr_e( "Add Title", "zombify" ); ?>"
									   value="<?php echo isset( $_POST["zombify_openlist"]["title"] ) && count( $zf_openlist_errors ) ? htmlspecialchars( $_POST["zombify_openlist"]["title"] ) : ''; ?>">
								<?php echo isset( $zf_openlist_errors["title"] ) ? zf_showFormErrors( $zf_openlist_errors["title"] ) : '' ?>
							</div>
							<div class="zf-form-group">
								<div class="zombify_medatype_image">
									<div class="zf-form-group  " data-zombify-fieldgroup-path="list/image">
										<div class="zf-uploader ">
											<button class="zf-remove-media"><i class="zf-icon-delete"></i></button>
											<div class="zf-get-url-popup">
												<a class="zf-popup-close" href="#"><i class="zf-icon-delete"></i></a>

												<div class="zf-popup_body">
													<div
															class="zf-form-group <?php echo isset( $zf_openlist_errors["image_url"] ) ? 'zf-error' : ''; ?>">
														<label><?php esc_html_e( "Paste Image URL", "zombify" ); ?></label>

														<div class="zf-form-group-popup">
															<input class="zf-image_url"
																   name="zombify_openlist[image_url]" type="url"
																   value="<?php echo isset( $_POST["zombify_openlist"]["image_url"] ) && count( $zf_openlist_errors ) ? htmlspecialchars( $_POST["zombify_openlist"]["image_url"] ) : ''; ?>">
															<button class="zf-submit_url zf-button"
																	type="button"><?php esc_html_e( "Submit", "zombify" ); ?></button>
														</div>
													</div>
												</div>
											</div>
											<label class="zf-image-label">
												<div class="zf-label">
													<i class="zf-icon zf-icon-add "></i>
													<span
															class="zf-label_text"><?php esc_html_e( "Browse Image", "zombify" ); ?></span>
													<span class="zf_or "><?php esc_html_e( "or", "zombify" ); ?></span>
													<a class="zf-get_url js-zf-get_url"
													   href="#"><?php esc_html_e( "Get by URL", "zombify" ); ?></a>
												</div>
												<input type="file" name="zombify_openlist[image]" value=""
													   zf-validation-maxsize="<?php echo zf_get_option( "zombify_max_upload_size" ) / 1024; ?>"
													   zf-validation-extensions="png, jpg, gif, jpeg">
												<img src="" class="zf-preview-img" style="display: none">
											</label>
											<?php echo isset( $zf_openlist_errors["image_url"] ) ? zf_showFormErrors( $zf_openlist_errors["image_url"] ) : '' ?>
										</div>
									</div>

								</div>
								<div class="zombify_medatype_embed">
									<div class="zf-form-group">

										<div class="zf-embed">
											<div
													class="zf-form-group <?php echo isset( $zf_openlist_errors["embed"] ) ? 'zf-error' : ''; ?>">
                                                <textarea name="zombify_openlist[embed_url]"
														  class="zombify_embed_url_textarea"
														  placeholder="<?php esc_attr_e( "Embed / URL", "zombify" ); ?>"><?php echo isset( $_POST["zombify_openlist"]["embed"] ) && count( $zf_openlist_errors ) ? htmlspecialchars( $_POST["zombify_openlist"]["embed"] ) : ''; ?></textarea>
												<?php echo isset( $zf_openlist_errors["embed"] ) ? zf_showFormErrors( $zf_openlist_errors["embed"] ) : '' ?>
												<input type="hidden" name="zombify_openlist[embed_thumb]"
													   value="" data-zombify-name-index="0"
													   data-zombify-field-path="list/embed_thumb"
													   data-zombify-field-name="embed_thumb">

											</div>
											<div
													class="zf-note"><?php esc_html_e( "Paste a YouTube, Instagram or SoundCloud link or embed code.", "zombify" ); ?></div>
											<div class="zf-embed-formats">
												<i class="zf-icon zf-icon-facebook"></i>
												<i class="zf-icon zf-icon-youtube"></i>
												<i class="zf-icon zf-icon-vine"></i>
												<i class="zf-icon zf-icon-vimeo"></i>
												<i class="zf-icon zf-icon-dailymotion"></i>
												<i class="zf-icon zf-icon-instagram"></i>
												<i class="zf-icon zf-icon-twitter"></i>
												<i class="zf-icon zf-icon-pinterest-p"></i>
												<i class="zf-icon zf-icon-map-marker"></i>
												<i class="zf-icon zf-icon-type-gif"></i>
												<i class="zf-icon zf-icon-image"></i>
												<i class="zf-icon zf-icon-soundcloud"></i>
												<i class="zf-icon zf-icon-mixcloud"></i>
												<i class="zf-icon zf-icon-reddit"></i>
												<i class="zf-icon zf-icon-coubcom"></i>
												<i class="zf-icon zf-icon-imgur"></i>
												<i class="zf-icon zf-icon-vidme"></i>
												<i class="zf-icon zf-icon-twitch"></i>
												<i class="zf-icon zf-icon-odnoklassniki"></i>
												<i class="zf-icon zf-icon-google-plus"></i>
												<i class="zf-icon zf-icon-giphy"></i>
											</div>
											<div class="zf-embed-video"></div>
										</div>
									</div>

								</div>
							</div>
							<div class="zf-form-group <?php echo count( $zf_openlist_errors ) ? '' : 'zf-hide'; ?>"
								 data-zombify-fieldgroup-path="list/original_source">
								<div class="zf-checkbox-inline">
									<label>
										<input type="checkbox" name="zombify_openlist[original_source]" value="1"
											   data-zombify-name-index="0"
											   data-zombify-field-path="list/original_source" <?php echo isset( $_POST["zombify_openlist"]["original_source"] ) && count( $zf_openlist_errors ) ? 'checked' : ''; ?>>
										<?php esc_html_e( "Via", "zombify" ); ?>
									</label>
								</div>
							</div>
							<div class="zf-form-group <?php echo count( $zf_openlist_errors ) ? '' : 'zf-hide'; ?>"
								 data-zombify-fieldgroup-path="list/image_credit">
								<label><?php esc_html_e( "Source URL", "zombify" ); ?></label>
								<input type="url" name="zombify_openlist[image_credit]"
									   value="<?php echo isset( $_POST["zombify_openlist"]["image_credit"] ) && count( $zf_openlist_errors ) ? htmlspecialchars( $_POST["zombify_openlist"]["image_credit"] ) : ''; ?>"
									   data-zombify-name-index="0" data-zombify-field-path="list/image_credit"
									   data-zombify-show-dependency="list/original_source"
									   placeholder="<?php esc_attr_e( "http://example.com", "zombify" ); ?>"
									   rel="nofollow">
							</div>
							<div class="zf-form-group <?php echo count( $zf_openlist_errors ) ? '' : 'zf-hide'; ?>"
								 data-zombify-fieldgroup-path="list/image_credit_text">
								<label><?php esc_html_e( "Credit", "zombify" ); ?></label>
								<input type="text" name="zombify_openlist[image_credit_text]"
									   value="<?php echo isset( $_POST["zombify_openlist"]["image_credit_text"] ) && count( $zf_openlist_errors ) ? htmlspecialchars( $_POST["zombify_openlist"]["image_credit_text"] ) : ''; ?>"
									   data-zombify-name-index="0" data-zombify-field-path="list/image_credit_text"
									   data-zombify-show-dependency="list/original_source">
							</div>

							<div
									class="zf-form-group <?php echo count( $zf_openlist_errors ) ? '' : 'zf-hide'; ?> <?php echo isset( $zf_openlist_errors["description"] ) ? 'zf-error' : ''; ?>">
                                <textarea class="zf-wysiwyg-light" name="zombify_openlist[description]"
										  placeholder="<?php esc_attr_e( "Description", "zombify" ); ?>"><?php echo isset( $_POST["zombify_openlist"]["description"] ) && count( $zf_openlist_errors ) ? htmlspecialchars( $_POST["zombify_openlist"]["description"] ) : ''; ?></textarea>
								<?php echo isset( $zf_openlist_errors["description"] ) ? zf_showFormErrors( $zf_openlist_errors["description"] ) : '' ?>
							</div>
							<div class="zf-row <?php echo count( $zf_openlist_errors ) ? '' : 'zf-hide'; ?>">
								<div class="zf-col-md-4">
									<div class="zf-form-group">
										<button class="zf-button"
												type="submit"><?php esc_html_e( "Submit", "zombify" ); ?></button>
									</div>
								</div>
							</div>
						</div>
					</div>
					<input type="hidden" name="zombify_frontend_save" value="1">
					<?php zombify_get_form_token_tag(); ?>
					<?php if ( is_user_logged_in() ){ ?></form><?php } ?>
			</div>
		</div>
		<?php
	}
	?>

	<?php do_action( 'zombify_after_post_layout' ); ?>
</div>

