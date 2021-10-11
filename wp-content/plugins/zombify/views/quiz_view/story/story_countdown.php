<?php
$data  = $story_data;
$index = count( $data["list"] );
?>

<div id="zf-list" class="zf-list zf-numbered">
	<ol class="zf-structure-list">
		<?php
		if ( isset( $data["list"] ) ) {
			foreach ( $data["list"] as $list ) { ?>
				<li class="zf-list_item">
					<div class="zf-list_header">
						<h2 class="zf-list_title">
							<span class="zf-number"><?php echo $index; ?></span>
							<?php echo $list["title"]; ?>
						</h2>
					</div>
					<?php
					if ( ( $list["mediatype"] == 'image' && isset( zf_array_values( $list["image"] )[0]["attachment_id"] ) ) || ( $list["mediatype"] == 'embed' && $list["embed_url"] != '' ) ) {
						switch ( $list["mediatype"] ) {
							case "image":
								$zf_media_html = '';
								if ( isset( zf_array_values( $list["image"] )[0]["attachment_id"] ) ) {
									$zf_media_html = zombify_get_img_tag( zf_array_values( $list["image"] )[0]["attachment_id"], 'full' );
								}
								break;
							case "embed":
								$zf_media_html = sprintf( '<div class="zf-embedded-url">%s</div>', Zombify_BaseQuiz::renderEmbed( $list, true ) );
								break;
							default:
								$zf_media_html = '';
						}
						$zf_media_wrapper_classes = zf_get_media_wrapper_classes( 'zf-list_media zf-media-wrapper zf-' . $list['mediatype'] ); ?>
						<figure class="<?php echo esc_attr( $zf_media_wrapper_classes ); ?>">
							<div class="zf-media">
								<?php
								echo $zf_media_html;

								if ( isset( $list["image_credit"] ) ) { ?>
									<figcaption class="zf-figcaption">
										<cite><?php zf_showCredit( $list["image_credit"], $list["image_credit_text"] ); ?></cite>
										<?php if ( isset( $list["affiliate"] ) && $list["affiliate"] && $list["affiliate_url"] != '' ) { ?>
											<a class="zf-buy-button"
											   href="<?php echo $list["affiliate_url"]; ?>" target="_blank"
											   rel="nofollow noopener"><i class="zf-icon zf-icon-buy_now"></i>
												<span><?php esc_html_e( "Buy Now", "zombify" ); ?></span>
											</a>
										<?php } ?>
									</figcaption>
								<?php } ?>
							</div>
							<hr />
						</figure>
						<?php
					}

					if ( $list["description"] ) { ?>
						<div class="zf-list_description"><?php echo $list["description"]; ?></div>
					<?php } ?>
				</li>
				<?php
				$index --;
			}
		} ?>
	</ol>
</div>


