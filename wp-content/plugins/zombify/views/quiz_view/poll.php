<?php
$zombify_poll_results = get_post_meta( get_the_ID(), 'zombify_poll_results', true );
?>
<div id="zombify-main-section-front" class="<?php echo zombify_get_front_main_section_classes( 'zombify-main-section-front zombify-screen' ); ?>">

    <div class="zf-container">

		<div  class="zf-poll">
            <ol class="zf-structure-list">
                <?php
                if( isset( $data["questions"] ) ) {
                    foreach( $data["questions"] as $question_index => $question ){

                        include zombify()->locate_template( zombify()->quiz_view_dir('poll/question_'.$question["answers_format"].'.php'));

                    }
                }
                ?>
            </ol>
        </div>
    </div>

    <?php do_action( 'zombify_after_post_layout' ); ?>
</div>

