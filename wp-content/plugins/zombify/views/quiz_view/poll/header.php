<div class="zf-quiz_header"><h2 class="zf-quiz_title"><?php echo $question["question"]; ?></h2></div>
<?php if ( ( $question["mediatype"] == 'image' && isset( zf_array_values( $question["image"] )[0]["attachment_id"] ) ) || ( $question["mediatype"] == 'embed' && $question["embed_url"] != '' ) ) {
	switch ( $question["mediatype"] ) {
		case 'image':
			$zf_media_html = '';
			if ( isset( zf_array_values( $question["image"] )[0]["attachment_id"] ) ) {
				$zf_media_html = zombify_get_img_tag( zf_array_values( $question["image"] )[0]["attachment_id"], 'full' );
			}
			break;
		case 'embed':
			$zf_media_html = sprintf( '<div class="zf-embedded-url">%s</div>', Zombify_BaseQuiz::renderEmbed( $question, true ) );
			break;
		default:
			$zf_media_html = '';
	}
	$zf_media_wrapper_classes = zf_get_media_wrapper_classes( 'zf-quiz_media zf-media-wrapper zf-' . $question['mediatype'] ); ?>
	<figure class="<?php echo esc_attr( $zf_media_wrapper_classes ); ?>">
		<div class="zf-media">
			<?php
			echo $zf_media_html;

			if ( isset( $question["image_credit"] ) && $question["image_credit"] != '' ) : ?>
				<figcaption class="zf-figcaption">
					<cite>
						<a href="<?php echo $question["image_credit"]; ?>"
						   class="zf-media_credit"
						   target="_blank"
						   rel="nofollow noopener"><?php echo ( isset( $question["image_credit_text"] ) && $question["image_credit_text"] ) ? $question["image_credit_text"] : __( 'Credit', 'zombify' ); ?></a>
					</cite>
				</figcaption>
			<?php elseif ( ! empty( $question["image_credit_text"] ) ): ?>
				<figcaption class="zf-figcaption">
					<cite><?php echo $question["image_credit_text"]; ?></cite>
				</figcaption>
			<?php endif; ?>
		</div>
	</figure>
<?php }

if ( $question["description"] ) { ?>
	<div class="zf-quiz_description"><?php echo $question["description"]; ?></div>
<?php } ?>