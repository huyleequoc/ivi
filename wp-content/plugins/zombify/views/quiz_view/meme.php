<div id="zombify-main-section-front" class="<?php echo zombify_get_front_main_section_classes( 'zombify-main-section-front zombify-screen' ); ?>">

    <div class="zf-container">


        <div id="zf-image" class="zf-image">
            <?php
            if( $data["readyimage"] ) {
                ?>
                <figure class="zf-image_media zf-image zf-type-image">
                    <img src="<?php echo $data["readyimage"]; ?>" alt="">
                    <?php
                    if (isset($data["image_credit"])) { ?>
                        <figcaption class="zf-figcaption">
                            <cite><?php zf_showCredit($data["image_credit"], $data["image_credit_text"]); ?></cite>
                        </figcaption>
                    <?php } ?>
                </figure>
            <?php
            }
            ?>
            <div class="zf-image_description"><?php echo $data["image_description"]; ?></div>
        </div>
    </div>

    <?php do_action( 'zombify_after_post_layout' ); ?>
</div>