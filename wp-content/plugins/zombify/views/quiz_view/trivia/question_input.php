<li class="zf-quiz_question zf-input">
	<?php include zombify()->locate_template( zombify()->quiz_view_dir('trivia/header.php'));?>
    

    <?php
    /* Gather up answers `primary` and `others` */
    $answersChain = '';
    if( isset( $question["answers"] ) ) {
        foreach( $question["answers"] as $answer ) {
            $answersChain .= '"' . $answer['answer_text'] . '",';
        }
    }

    if( $answersChain ) { ?>
        <ol class="zf-structure-list zf-quiz_answer zf-input zf-clearfix">
            <li class="zf-answer-item">
                <input type="text" class="js-zf-quiz-input" data-answers='[<?php echo rtrim( $answersChain, ',' ); ?>]' data-limit="999" data-try="0" placeholder="<?php esc_attr_e( 'Your Answer', 'zombify' ); ?>" autocomplete="off">
                <a href="#" class="zf-quiz-guess-btn js-zf-quiz-guess-btn"><?php esc_html_e("Guess", "zombify"); ?></a>
                <a href="#" class="zf-quiz-giveup-btn zf-float-right js-zf-quiz-giveup-btn"><?php esc_html_e("I give up", "zombify"); ?></a>
            </li>
        </ol>
    <?php } ?>

    <div class="zf-quiz_reveal zf-reveal_with_media">
        <div class="zf-reveal_header">
            <div class="zf-answer_response zf-correct">
                <i class="zf-icon-check"></i>
                <?php esc_html_e("Correct!", "zombify"); ?>
            </div>
            <div class="zf-answer_response zf-wrong">
                <i class="zf-icon-close"></i>
                <?php esc_html_e("Wrong!", "zombify"); ?>
            </div>
        </div>
        <div class="zf-reveal_body" <?php if( $question["after_answer_title"]=='' && $question["after_answer_description"]=='' && ( !isset(zf_array_values($question["after_answer_image"])[0]["attachment_id"]) || zf_array_values($question["after_answer_image"])[0]["attachment_id"] == '' ) ) echo 'style="display:none"'; ?>>
            <?php
            if( isset(zf_array_values($question["after_answer_image"])[0]["attachment_id"]) ){
                ?>
                <div class="zf-reveal_media">
                    <?php
                    echo zombify_get_img_tag( zf_array_values($question["after_answer_image"])[0]["attachment_id"], 'full' );

                    if( isset($question["after_answer_image_credit"]) && $question["after_answer_image_credit"]!='' ) { ?>
                        <a href="<?php echo $question["after_answer_image_credit"]; ?>" class="zf-reveal_credit" target="_blank" rel="noopener"><?php echo $question["after_answer_image_credit_text"] ? $question["after_answer_image_credit_text"] : __('Credit', 'zombify'); ?></a>
					<?php } ?>
                </div>
                <?php
            }
            ?>
            <div class="zf-reveal_content">
                <h3><?php echo $question["after_answer_title"]; ?></h3>
                <div class="zf-reveal_text"><?php echo $question["after_answer_description"]; ?></div>
            </div>
        </div>
    </div>
</li>

