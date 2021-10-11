<div id="zombify-main-section-front" class="<?php echo zombify_get_front_main_section_classes( 'zombify-main-section-front zombify-screen' ); ?>">
	<div class="zf-container">
		<div class="zf-quiz zf-trivia_quiz zf-show-answer zf-numbered" data-quiz_type="trivia" data-question_count="<?php echo count( $data["questions"] ); ?>">
			<ol class="zf-structure-list">
				<?php
				$order_index = 0;
				if ( isset( $data["questions"] ) ) {
					foreach ( $data["questions"] as $question_index => $question ) {
						$order_index ++;
						include zombify()->locate_template( zombify()->quiz_view_dir( 'trivia/question_' . $question["answers_format"] . '.php' ) );
					}
				} ?>
			</ol>
			<div id="zf-quiz_results" class="zf-quiz_results zf-trivia-quiz_results">
				<div class="zf-result_header">
					<h3 class="zf-result_title"><?php echo $data["title"]; ?></h3>
					<span class="zf-result_date"><?php esc_html_e( "Created on", "zombify" ); ?>
						<time datetime="<?php echo get_the_date( "Y-m-d" ); ?>"><?php echo get_the_date( "d M Y" ); ?></time>
					</span>
				</div>
				<ol class="zf-structure-list">
					<li class="zf-result zf-default-result">
						<div class="zf-result_text"><?php esc_html_e( "Quiz result", "zombify" ); ?></div>
						<div class="zf-result_score"><?php esc_html_e( "You scored", "zombify" ); ?>
							<span class="zf-score_count"></span></div>
						<div class="zf-result_media">
							<div class="zf-no-image-result">
								<div class="zf-score_count_correct"></div>
								<div class="zf-correct"><?php esc_html_e( "Correct!", "zombify" ); ?></div>
							</div>
						</div>
						<?php include zombify()->locate_template( zombify()->quiz_view_dir( 'share.php' ) ); ?>
					</li>

					<?php
					if ( isset( $data["results"] ) ) {
						foreach ( $data["results"] as $result ) { ?>
							<li class="zf-result" data-range_start="<?php echo $result["range_from"]; ?>" data-range_end="<?php echo $result["range_to"]; ?>">
								<div class="zf-result_text"><?php esc_html_e( "Quiz result", "zombify" ); ?></div>
								<h2 class="zf-result_title"><?php echo $result["result"] ?></h2>
								<div class="zf-result_score"><?php esc_html_e( "You scored", "zombify" ); ?>
									<span class="zf-score_count"></span></div>
								<div class="zf-result_media">
									<?php
									if ( isset( zf_array_values( $result["image"] )[0]["attachment_id"] ) ) {
										echo zombify_get_img_tag( zf_array_values( $result["image"] )[0]["attachment_id"], 'full', array( 'allow_modification' => false ) );
									} else { ?>
										<div class="zf-no-image-result">
											<div class="zf-score_count_correct"></div>
											<div class="zf-correct"><?php esc_html_e( "Correct!", "zombify" ); ?></div>
										</div>
									<?php } ?>
								</div>
								<?php if ( $result["description"] ) { ?>
									<div class="zf-result_description"><?php echo $result["description"]; ?></div>
								<?php }

								include zombify()->locate_template( zombify()->quiz_view_dir( 'share.php' ) ); ?>
							</li>
							<?php
						}
					} ?>
				</ol>
			</div>
		</div>
	</div>

	<?php do_action( 'zombify_after_post_layout' ); ?>
</div>

