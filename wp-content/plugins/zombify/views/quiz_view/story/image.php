<div class="zf-story_item">
	<h2 class="zf_title"><?php echo $story_data["image_title"]; ?></h2>
	<?php
	if ( isset( zf_array_values( $story_data["image_image"] )[0]["attachment_id"] ) ) {
		$zf_media_html = zombify_get_img_tag( zf_array_values( $story_data["image_image"] )[0]["attachment_id"], 'full' );
		$zf_media_wrapper_classes = zf_get_media_wrapper_classes( 'zf-story_media zf-media-wrapper zf-image' ); ?>
		<figure class="<?php echo esc_attr( $zf_media_wrapper_classes ); ?>">
			<div class="zf-media">
				<?php
				echo $zf_media_html;

				if ( isset( $story_data["image_image_credit"] ) && $story_data["image_image_credit"] != '' ) { ?>
					<figcaption class="zf-figcaption">
						<cite>
							<a href="<?php echo $story_data["image_image_credit"]; ?>"
							   class="zf-media_credit"
							   target="_blank"
							   rel="nofollow noopener"><?php echo $story_data["image_image_credit_text"] ? $story_data["image_image_credit_text"] : __( 'Credit', 'zombify' ); ?></a>
						</cite>
					</figcaption>
				<?php } elseif ( ! empty( $story_data["image_image_credit_text"] ) ) { ?>
					<figcaption class="zf-figcaption">
						<cite><?php echo $story_data["image_image_credit_text"]; ?></cite>
					</figcaption>
				<?php } ?>
			</div>
		</figure>
		<?php
	} ?>
	<?php if ( $story_data["image_caption"] ) { ?>
		<div class="zf_description"><?php echo $story_data["image_caption"]; ?></div><?php } ?>
</div>