<div id="zombify-main-section-front" class="<?php echo zombify_get_front_main_section_classes( 'zombify-main-section-front zombify-screen' ); ?>">
	<div class="zf-container">
		<div class="zf-open_list zf-list">
			<div class="zf-list_item zf-single">
				<?php
				if ( ( $data["mediatype"] == 'image' && isset( zf_array_values( $data["image"] )[0]["attachment_id"] ) ) || ( $data["mediatype"] == 'embed' && $data["embed_url"] != '' ) ) {
					switch ( $data["mediatype"] ) {
						case "image":
							$zf_media_html = '';
							if ( isset( zf_array_values( $data["image"] )[0]["attachment_id"] ) ) {
								$zf_media_html = zombify_get_img_tag( zf_array_values( $data["image"] )[0]["attachment_id"], 'full' );
							}
							break;
						case "embed":
							$zf_media_html = sprintf( '<div class="zf-embedded-url">%s</div>', Zombify_BaseQuiz::renderEmbed( $data, true ) );
							break;
						default:
							$zf_media_html = '';
					}
					$zf_media_wrapper_classes = zf_get_media_wrapper_classes( 'zf-list_media zf-media-wrapper zf-' . $data[ 'mediatype' ] ); ?>
					<figure class="<?php echo esc_attr( $zf_media_wrapper_classes ); ?>">
						<div class="zf-media">
							<?php
							echo $zf_media_html;

							if ( isset( $data["image_credit"] ) ) { ?>
								<figcaption class="zf-figcaption">
									<cite><?php zf_showCredit( $data["image_credit"], ( isset( $data["image_credit_text"] ) ? $data["image_credit_text"] : '' ) ); ?></cite>
									<?php if ( isset( $data["affiliate"] ) && $data["affiliate"] && $data["shop_url"] != '' ) {
										$shop_button_text = ! empty( $data['shop_button_text'] ) ? $data['shop_button_text'] : __( "Buy Now", "zombify" );
										?>
										<a class="zf-buy-button" href="<?php echo esc_url( $data["shop_url"] ); ?>" target="_blank" rel="nofollow noopener">
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
							<a href="<?php echo get_author_posts_url( $post->post_author ); ?>"><?php echo get_avatar( $post->post_author ); ?></a>
						</span>
						<span class="zf-author_info">
							<span class="zf-author_name">
								<a href="<?php echo get_author_posts_url( $post->post_author ); ?>"><?php echo get_the_author_meta( 'display_name', $post->post_author ); ?></a>
							</span>
							<span class="zf-posted-on">
								<time>
									<?php
									if ( function_exists( 'booombox_get_single_post_date' ) ) {
										echo booombox_get_single_post_date( 'published' );
									}
									?>
								</time>
							</span>
						</span>
					</span>

					<?php
					if ( get_post_meta( get_the_ID(), "openlist_close_voting", true ) != 1 ) { ?>
						<div class="zf-item-vote-box">
							<div class="zf-item-vote"
								 data-zf-post-id="<?php echo $post->ID; ?>"
								 data-zf-post-parent-id="<?php echo get_the_ID(); ?>">
								<button class="zf-vote_btn zf-vote_up"><i class="zf-icon zf-icon-vote_up"></i></button>

								<span class="zf-vote_count" data-zf-post-id="<?php echo $post->ID; ?>">
									<i class="zf-icon zf-spinner-pulse"></i>
									<span class="zf-vote_number"><?php echo (int) get_post_meta( $post->ID, "zombify_post_rateing", true ); ?></span>
								</span>

								<button class="zf-vote_btn zf-vote_down"><i class="zf-icon zf-icon-vote_down"></i></button>
							</div>
						</div>
						<?php
					}
					?>
				</div>
				<?php if ( $data["description"] ) { ?>
					<div class="zf-list_description"><?php echo $data["description"]; ?></div>
				<?php }

				if ( $post->post_status == 'publish' ) { ?>
					<div class="zf-next-prev-pagination">
						<?php if ( $prev_data ) { ?>
							<a class="zf-nav zf-prev" href="<?php echo get_permalink( $prev_data["post_id"] ); ?>">
								<span class="zf-icon zf-icon-angle-double-left"></span><?php esc_html_e( "Previous", "zombify" ); ?>
							</a>
							<?php
						} ?>
						<span class="zf-pages"><?php echo $data_num . '/' . $total_sub_count; ?></span>

						<?php if ( $next_data ) { ?>
							<a class="zf-nav zf-next" href="<?php echo get_permalink( $next_data["post_id"] ); ?>">
								<span class="zf-icon zf-icon-angle-double-right"></span><?php esc_html_e( "Next", "zombify" ); ?>
							</a>
							<?php
						} ?>
					</div>
					<?php
				}
				?>
				<div class="zf-view-full-list">
					<a href="<?php echo get_permalink( $post->post_parent ); ?>"><?php esc_html_e( "View full list", "zombify" ); ?></a>
				</div>
			</div>
		</div>
	</div>
</div>
