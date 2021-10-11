<li class="zf-quiz_question zf-2up zf-poll-item <?php if ( isset( $_COOKIE[ "zf_poll_vote_" . $question["question_id"] ] ) ) {
	echo 'zf-poll-done';
} ?>"
	data-voted_count="<?php echo isset( $zombify_poll_results["groups"][ $question["question_id"] ] ) ? $zombify_poll_results["groups"][ $question["question_id"] ] : 0 ?>">
	<?php include zombify()->locate_template( zombify()->quiz_view_dir( 'poll/header.php' ) ); ?>
	<ol class="zf-structure-list zf-quiz_answer zf-2up zf-clearfix">
		<?php
		if ( isset( $question["answers"] ) ) {
			foreach ( $question["answers"] as $answer ) {
				?>
				<li class="zf-answer-item <?php if ( ! isset( zf_array_values( $answer["image"] )[0]["attachment_id"] ) ) {
					echo 'zf-no-image';
				} ?> <?php if ( isset( $_COOKIE[ "zf_poll_vote_ans_" . $answer["answer_id"] ] ) ) {
					echo 'zf-selected';
				} ?>"
					data-voted="<?php echo isset( $zombify_poll_results["answers"][ $answer["answer_id"] ] ) ? $zombify_poll_results["answers"][ $answer["answer_id"] ] : 0 ?>"
					data-voted-group="<?php echo isset( $zombify_poll_results["groups"][ $question["question_id"] ] ) ? $zombify_poll_results["groups"][ $question["question_id"] ] : 0 ?>"
					data-id="<?php echo $answer["answer_id"]; ?>" data-post-id="<?php the_ID() ?>"
					data-group-id="<?php echo $question["question_id"]; ?>">

					<div class="zf-answer js-zf-answer">
						<?php
						$zf_media_html = '';
						if ( isset( zf_array_values( $answer["image"] )[0]["attachment_id"] ) ) {
							$zf_media_html = zombify_get_img_tag( zf_array_values( $answer["image"] )[0]["attachment_id"], 'zombify_small' );
						} ?>
						<figure class="zf-answer_media zf-image">
							<div class="zf-checkbox-wrp"></div>
							<?php
							echo $zf_media_html;

							if ( isset( $answer["image_credit"] ) && $answer["image_credit"] ) { ?>
								<figcaption class="zf-answer_credit">
									<cite>
										<a href="<?php echo $answer["image_credit"]; ?>"
										   class="zf-media_credit"
										   target="_blank"
										   rel="nofollow noopener"><?php echo ( isset( $answer["image_credit_text"] ) && $answer["image_credit_text"] ) ? $answer["image_credit_text"] : __( 'Credit', 'zombify' ); ?></a></cite>
								</figcaption>
							<?php } elseif ( ! empty( $answer["image_credit_text"] ) ) { ?>
								<figcaption class="zf-answer_credit">
									<cite><?php echo $answer["image_credit_text"]; ?></cite>
								</figcaption>
							<?php } ?>
							<div class="zf-poll-stat"></div>
							<div class="zf-poll-stat_count"></div>
						</figure>

						<div class="zf-answer_text"><?php echo $answer["answer_text"]; ?></div>
					</div>
				</li>
				<?php
			}
		} ?>
	</ol>
	<div class="zf-poll_total">
		<span class="voted-count"><?php echo isset( $zombify_poll_results["groups"][ $question["question_id"] ] ) ? $zombify_poll_results["groups"][ $question["question_id"] ] : 0 ?></span> <?php esc_html_e( "votes", "zombify" ); ?>
	</div>
	<?php include zombify()->locate_template( zombify()->quiz_view_dir( 'poll/share.php' ) ); ?>
</li>