<li class="zf-quiz_question zf-3up">
	<?php include zombify()->locate_template( zombify()->quiz_view_dir( 'personality/header.php' ) ); ?>
	<ol class="zf-structure-list zf-quiz_answer zf-3up clearfix">
		<?php
		if ( isset( $question["answers"] ) ) {
			foreach ( $question["answers"] as $answer ) { ?>
				<li class="zf-answer-item" data-personality_index="<?php echo $answer["answer_result"]; ?>">
					<div class="zf-answer js-zf-answer">
						<div class="zf-checkbox-wrp"></div>
						<?php
						if ( isset( zf_array_values( $answer["image"] )[0]["attachment_id"] ) ) {
							$zf_media_html = zombify_get_img_tag( zf_array_values( $answer["image"] )[0]["attachment_id"], 'zombify_small' ); ?>
							<figure class="zf-answer_media zf-image">
								<div class="zf-checkbox-wrp"></div>
								<?php
								echo $zf_media_html;

								if ( isset( $answer["image_credit"] ) && $answer["image_credit"] ) { ?>
									<figcaption class="zf-figcaption">
										<cite>
											<a href="<?php echo $answer["image_credit"]; ?>" class="zf-answer_credit" target="_blank" rel="nofollow noopener">
												<?php echo ( isset( $answer["image_credit_text"] ) && $answer["image_credit_text"] ) ? $answer["image_credit_text"] : __( 'Credit', 'zombify' ); ?>
											</a>
										</cite>
									</figcaption>
								<?php } ?>
							</figure>
							<?php
						} ?>
						<div class="zf-answer_text"><?php echo $answer["answer_text"]; ?></div>
					</div>
				</li>
				<?php
			}
		} ?>
	</ol>
</li>