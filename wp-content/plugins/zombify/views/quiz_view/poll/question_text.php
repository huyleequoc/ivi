<li class="zf-quiz_question zf-text zf-poll-item <?php if (isset($_COOKIE["zf_poll_vote_" . $question["question_id"]])) echo 'zf-poll-done'; ?>"
    data-voted_count="<?php echo isset($zombify_poll_results["groups"][$question["question_id"]]) ? $zombify_poll_results["groups"][$question["question_id"]] : 0 ?>">
    <?php include zombify()->locate_template( zombify()->quiz_view_dir('poll/header.php'));?>
    <ol class="zf-structure-list zf-quiz_answer zf-text zf-clearfix">
        <?php
        if( isset( $question["answers"] ) ) {
            foreach ($question["answers"] as $answer) {
                ?>
                <li class="zf-answer-item <?php if (isset($_COOKIE["zf_poll_vote_ans_" . $answer["answer_id"]])) echo 'zf-selected'; ?>"
                    data-voted="<?php echo isset($zombify_poll_results["answers"][$answer["answer_id"]]) ? $zombify_poll_results["answers"][$answer["answer_id"]] : 0 ?>"
                    data-id="<?php echo $answer["answer_id"]; ?>" data-post-id="<?php the_ID() ?>"
                    data-group-id="<?php echo $question["question_id"]; ?>"
                    data-voted-group="<?php echo isset($zombify_poll_results["groups"][$question["question_id"]]) ? $zombify_poll_results["groups"][$question["question_id"]] : 0 ?>">
                   <div class="zf-answer js-zf-answer">
                       <div class="zf-checkbox-wrp"></div>
                       <div class="zf-poll-stat"></div>
                       <div class="zf-poll-stat_count"></div>
                       <div class="zf-answer_text">
                           <?php echo $answer["answer_text"]; ?>
                       </div>
                   </div>
                </li>
            <?php
            }
        } ?>
    </ol>
    <div class="zf-poll_total"><span
            class="voted-count"><?php echo isset($zombify_poll_results["groups"][$question["question_id"]]) ? $zombify_poll_results["groups"][$question["question_id"]] : 0 ?></span> <?php esc_html_e("votes", "zombify"); ?>
    </div>
    <?php include zombify()->locate_template(zombify()->quiz_view_dir('poll/share.php')); ?>
</li>