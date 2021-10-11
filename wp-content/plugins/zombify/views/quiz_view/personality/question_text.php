<li class="zf-quiz_question zf-text">
	<?php include zombify()->locate_template( zombify()->quiz_view_dir('personality/header.php'));?>
   
    <ol class="zf-structure-list zf-quiz_answer zf-text clearfix">
        <?php
        if( isset( $question["answers"] ) ) {
            foreach( $question["answers"] as $answer ) {
                ?>
                <li class="zf-answer-item" data-personality_index="<?php echo $answer["answer_result"]; ?>">
                   <div class="zf-answer js-zf-answer">
                       <div class="zf-checkbox-wrp"></div>
                       <div class="zf-answer_text">
                           <?php echo $answer["answer_text"]; ?>
                       </div>
                   </div>
                </li>
            <?php
            }
        } ?>
    </ol>
</li>