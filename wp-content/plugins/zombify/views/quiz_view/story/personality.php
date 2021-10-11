<?php $data = $story_data; ?>
<div class="zf-quiz zf-personality_quiz zf-numbered" data-quiz_type="personality" data-question_count="<?php echo count( $data["questions"] ); ?>" data-result_count="<?php echo count( $data["results"] ); ?>">
	<ol class="zf-structure-list">
		<?php
		$order_index = 0;
		if ( isset( $data["questions"] ) ) {
			foreach ( $data["questions"] as $question_index => $question ) {
				$order_index ++;
				include zombify()->locate_template( zombify()->quiz_view_dir( 'personality/question_' . $question["answers_format"] . '.php' ) );
			}
		} ?>
	</ol>
	<div class="zf-quiz_results">
		<div class="zf-result_header">
			<h3 class="zf-result_title"><?php /* echo $data["title"]; */ ?></h3>
			<span class="zf-result_date"><?php esc_html_e( "Created on", "zombify" ); ?>
				<time datetime="<?php echo get_the_date( "Y-m-d" ); ?>"><?php echo get_the_date( "d M Y" ); ?></time></span>
		</div>
		<ol class="zf-structure-list zf-remove-list-style">
			<?php
			if ( isset( $data["results"] ) ) {
				foreach ( $data["results"] as $result ) { ?>
					<li class="zf-result">
						<div class="zf-result_text"><?php esc_html_e( "Quiz result", "zombify" ); ?></div>
						<h2 class="zf-result_title"><?php echo $result["result"] ?></h2>
						<?php if ( isset( zf_array_values( $result["image"] )[0]["attachment_id"] ) ) { ?>
							<div class="zf-result_media">
								<?php
								echo zombify_get_img_tag( zf_array_values( $result["image"] )[0]["attachment_id"], 'full', array( 'allow_modification' => false ) );

								if ( isset( $result["image_credit"] ) ) { ?>
									<figcaption class="zf-figcaption">
										<cite><?php zf_showCredit( $result["image_credit"], $result["image_credit_text"] ); ?></cite>
									</figcaption>
								<?php } ?>
							</div>
						<?php }

						if ( $result["description"] ) { ?>
							<div class="zf-result_description"><?php echo $result["description"] ?></div>
						<?php }

						include zombify()->locate_template( zombify()->quiz_view_dir( 'personality/share.php' ) ); ?>
					</li>
					<?php
				}
			} ?>
		</ol>
	</div>
</div>