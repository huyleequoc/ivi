<div id="zombify-main-section" class="zombify-main-section zf-image zombify-screen">
    <?php
    $zf_config = zombify()->get_config();
    global $zf_excerpt_characters_limit;

    switch( $action ){
        case "create":
            ?><h2 class="zf-global-title"><?php printf( esc_html__("Create %s", "zombify"), $zf_config['zf_post_types']['image']['name'] ); ?></h2><?php
            break;
        case "update":
            ?><h2 class="zf-global-title"><?php printf( esc_html__("Update %s", "zombify"), $zf_config['zf_post_types']['image']['name'] ); ?></h2><?php
            break;
    }


    ?>
    <div class="zf-container">
        <form id="zombify-form" method="post" action="" enctype="multipart/form-data">

            <?php include zombify()->locate_template('quiz/option-panel.php'); ?>

            <div id="zf-main" class="zf-main">
                <?php if($action == 'create' && !isset($_POST["zombify"]) && !$this->virtual ) {
                    ?>
                    <div class="zf-start zf-open">
                        <div class="zf-uploader">
                            <div class="zf-get-url-popup">
                                <a class="zf-popup-close" href="#"><i class="zf-icon-delete"></i></a>
                                <div class="zf-popup_body">
                                    <div class="zf-form-group">
                                        <label><?php esc_html_e( 'Paste Image URL', 'zombify' ); ?></label>
                                        <div class="zf-form-group-popup">
                                            <input class="zf-start-image_url" name="" type="url">
                                            <button class="zf-start-submit_url zf-button" type="button"><?php esc_html_e( 'Submit', 'zombify' ); ?></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <label class="zf-image-label" for="image_image">
                                <div class="zf-extensions">
                                    <span>png</span>
                                    <span>jpg</span>
                                    <span>gif</span>
                                </div>

                                <div class="zf-label">
                                    <i class="zf-icon zf-icon-image "></i>
                                    <span class="zf-label_text"><?php esc_html_e( 'Browse Image', 'zombify' ); ?></span>
                                    <span class="zf_or "><?php esc_html_e( 'or', 'zombify' ); ?></span>
                                    <a class="zf-get_url js-zf-get_url" href="#"><?php esc_html_e( 'Get by URL', 'zombify' ); ?></a>
                                </div>
                            </label>
                        </div>
                    </div>
                    <?php
                } ?>

                <div class="zf-after-start">
                    <?php echo $this->PostIDHiddenInput() ?>
                    <?php echo $this->QuizTypeHiddenInput() ?>

                    <div class="zf-info-wrapper">
                        <?php
                        echo $this->renderField(['title'], '', 0, $this->data, array(), '', array('showPlaceholder'=>true, 'showLabel'=>false));
                        echo $this->renderField(['description'], '', 0, $this->data, array(), '', array('showPlaceholder'=>true, 'showLabel'=>false)); ?>
                        <div class="zf-preface-excerpt-cont">
                            <?php
                                if( $zf_config['zf_post_types']['image']['excerpt'] === 1 ) {
                                    echo $this->renderField(['use_excerpt'], '', 0, $this->data, array(), '', array('showPlaceholder' => false, 'showLabel' => true));
                                }
                                if( $zf_config['zf_post_types']['image']['preface'] === 1 ) {
                                    echo $this->renderField(['use_preface'], '', 0, $this->data, array(), '', array('showPlaceholder' => false, 'showLabel' => true));
                                }
                            ?>
                        </div>
                        <div class="zf-excerpt <?php if( ( isset( $this->data["use_excerpt"] ) && $this->data["use_excerpt"] ) || $zf_config['zf_post_types']['image']['excerpt'] === 0 ) echo 'zf-open'; ?>">
                            <div class="zf-excerpt_inner">
                                <?php echo $this->renderField(['excerpt_description'], '', 0, $this->data, array('maxlength' => $zf_excerpt_characters_limit), '', array('showPlaceholder' => true, 'showLabel' => false)); ?>
                            </div>
                        </div>
                        <div class="zf-preface <?php if( ( isset( $this->data["use_preface"] ) && $this->data["use_preface"] ) || $zf_config['zf_post_types']['image']['preface'] === 0 ) echo 'zf-open'; ?>">
                            <div class="zf-preface_inner">
                                <?php echo $this->renderField(['preface_description'], '', 0, $this->data, array('class' => 'zf-wysiwyg-advanced'), '', array('showPlaceholder' => true, 'showLabel' => false)); ?>
                            </div>
                        </div>
                    </div>
                    <div id="zf-image_container" class="zf-image_container">
                        <div class="zf-form-group zf-form-group_media">
                            <div class="zf-media-uploader" data-format="image">
                                <div class="">
                                    <div class="zombify_medatype_image">
                                        <?php
                                        echo $this->renderField(['image_image'], '', 0, $this->data,array('id'=>'image_image'));
                                        echo $this->renderField(['original_source'], '', 0, $this->data, array(), '', array('showPlaceholder'=>false, 'showLabel'=>true));
                                        echo $this->renderField(['image_credit'], '', 0, $this->data, array("placeholder"=> esc_attr( __("http://example.com", "zombify")), "rel"=>"nofollow"), '', array('showPlaceholder'=>false, 'showLabel'=>true));
                                        echo $this->renderField(['image_credit_text'], '', 0, $this->data, array(), '', array('showPlaceholder'=>false, 'showLabel'=>true));
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php

                        echo $this->renderField(['image_description'], '', 0, $this->data, array('class' => 'zf-wysiwyg-light'), '', array('showPlaceholder'=>false, 'showLabel'=>true));
                        ?>
                    </div>

                </div>


                <?php include zombify()->locate_template('quiz/save_buttons.php'); ?>

            </div>

        </form>
    </div>
</div>