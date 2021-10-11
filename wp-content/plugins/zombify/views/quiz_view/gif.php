<div id="zombify-main-section-front" class="<?php echo zombify_get_front_main_section_classes( 'zombify-main-section-front zombify-screen' ); ?>">
	<div class="zf-container">
		<div id="zf-image" class="zf-image">
			<?php
			$gif_data = zf_array_values( $data["gif"] )[0];

			if ( isset( zf_array_values( $gif_data["image_image"] )[0]["attachment_id"] ) && zf_array_values( $gif_data["image_image"] )[0]["attachment_id"] ) {

				$file_mime_type = get_post_mime_type( zf_array_values( $gif_data["image_image"] )[0]["attachment_id"] );
				if ( $file_mime_type == 'video/mp4' ) {
					$zf_media_html = zombify_get_video_tag( zf_array_values( $gif_data["image_image"] )[0]["attachment_id"], 'full' );
				} else {
					$zf_media_html = zombify_get_img_tag( zf_array_values( $gif_data["image_image"] )[0]["attachment_id"], 'full' );
				}
				$zf_media_wrapper_classes = zf_get_media_wrapper_classes( 'zf-image_media zf-media-wrapper zf-image' ); ?>
				<figure class="<?php echo esc_attr( $zf_media_wrapper_classes ); ?>">
					<div class="zf-media">
						<?php
						echo $zf_media_html;

						if ( isset( $gif_data['image_credit'] ) ) { ?>
							<figcaption class="zf-figcaption">
								<cite><?php zf_showCredit( $gif_data['image_credit'], $gif_data['image_credit_text'] ); ?></cite>
							</figcaption>
						<?php } ?>
					</div>
				</figure>
			<?php } ?>

			<div class="zf-image_description"><?php echo $gif_data["image_description"]; ?></div>
		</div>
	</div>
	<?php do_action( 'zombify_after_post_layout' ); ?>
</div>