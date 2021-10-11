<?php if ( ! empty( $question["question"] ) || count( $data["questions"] ) > 1 ) { ?>
	<div class="zf-quiz_header">
		<h2 class="zf-quiz_title">
			<?php
			if ( count( $data["questions"] ) > 1 ) { ?>
				<span class="zf-number"><?php echo $order_index; ?></span>
			<?php }

			if ( $question["question"] ) {
				echo $question["question"];
			} ?>
		</h2>
	</div>
<?php }

if ( ( $question["mediatype"] == 'image' && isset( zf_array_values( $question["image"] )[0]["attachment_id"] ) ) || ( $question["mediatype"] == 'embed' && $question["embed_url"] != '' ) ) {
	switch ( $question["mediatype"] ) {
		case "image":
			$zf_media_html = '';
			if ( isset( zf_array_values( $question["image"] )[0]["attachment_id"] ) ) {
				$zf_media_html = zombify_get_img_tag( zf_array_values( $question["image"] )[0]["attachment_id"], 'full' );
			}
			break;
		case "embed":
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

			if ( isset( $question["image_credit"] ) ) { ?>
				<figcaption class="zf-figcaption">
					<cite><?php zf_showCredit( $question["image_credit"], $question["image_credit_text"] ); ?></cite>
				</figcaption>
			<?php } ?>
		</div>
	</figure>
<?php }

if ( $question["description"] ) { ?>
	<div class="zf-quiz_description"><?php echo $question["description"]; ?></div>
<?php } ?>