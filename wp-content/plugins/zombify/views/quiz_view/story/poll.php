<div class="zf-poll">

    <ol class="zf-structure-list">
        <?php
        $data = $story_data;

        if( isset( $data["questions"] ) ) {

	        foreach( $data["questions"] as $question_index => $question ){

	            include zombify()->locate_template( zombify()->quiz_view_dir('poll/question_'.$question["answers_format"].'.php'));

	        }

	    } ?>
    </ol>
</div>