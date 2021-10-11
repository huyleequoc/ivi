/* global zf_back */

var zombify_unsaved_form = false;
var zombify_virtual_unsaved_form = false;
var zombify_virtual_save_interval_default = 5000;
var zombify_virtual_save_interval_max = 60000 * 20;
var zombify_virtual_save_interval = zombify_virtual_save_interval_default;
var zombify_video_upload = new Array();
var zombify_virtual_addit_data = {};

var zf_wysiwyg_config_advanced = {
    language:                   zf_back.zf_froala_lang,
    heightMin:                  zf_back.zf_editor_settings.zf_editor_adv_height,
    charCounterMax:             zf_back.zf_editor_settings.zf_editor_adv_char_counter_max,
    paragraphFormat:            zf_back.zf_editor_settings.zf_editor_paragraphs,
    placeholderText:            zf_back.zf_editor_settings.zf_editor_adv_placeholder,
    imageDefaultWidth:          zf_back.zf_editor_settings.zf_editor_adv_image_default_width,
    toolbarButtons:             zf_back.zf_editor_settings.zf_editor_adv_toolbar,
    toolbarButtonsMD:           zf_back.zf_editor_settings.zf_editor_adv_toolbar,
    toolbarButtonsSM:           zf_back.zf_editor_settings.zf_editor_adv_toolbar,
    toolbarButtonsXS:           zf_back.zf_editor_settings.zf_editor_adv_mobile_toolbar,
    imageManagerPreloader:      zf_back.zf_editor_settings.zf_editor_loader,
    imageUploadParams:          zf_back.zf_editor_image_upload_params,
    imageManagerLoadParams:     zf_back.zf_editor_image_manager_load_params,
    imageUploadURL:             zf_back.zf_editor_image_upload_action,
    imageManagerLoadURL:        zf_back.ajaxurl,
    imageUploadMethod:          'POST',
    imageManagerLoadMethod:     'POST',
    pastePlain:                 zf_back.zf_editor_settings.zf_editor_paste_plain,
    imageMaxSize:               zf_back.zf_max_upload_size,
    spellcheck:                 zf_back.zf_editor_settings.zf_spellcheck,
    rtgOptions:                 zf_back.zf_editor_settings.zf_rtg_options,
};
var zf_wysiwyg_config_light = {
    language:                   zf_back.zf_froala_lang,
    heightMin:                  zf_back.zf_editor_settings.zf_editor_lt_height,
    charCounterMax:             zf_back.zf_editor_settings.zf_editor_lt_char_counter_max,
    paragraphFormat:            zf_back.zf_editor_settings.zf_editor_paragraphs,
    placeholderText:            zf_back.zf_editor_settings.zf_editor_lt_placeholder,
    imageDefaultWidth:          zf_back.zf_editor_settings.zf_editor_lt_image_default_width,
    toolbarButtons:             zf_back.zf_editor_settings.zf_editor_lt_toolbar,
    toolbarButtonsMD:           zf_back.zf_editor_settings.zf_editor_lt_toolbar,
    toolbarButtonsSM:           zf_back.zf_editor_settings.zf_editor_lt_toolbar,
    toolbarButtonsXS:           zf_back.zf_editor_settings.zf_editor_lt_mobile_toolbar,
    imageManagerPreloader:      zf_back.zf_editor_settings.zf_editor_loader,
    imageUploadParams:          zf_back.zf_editor_image_upload_params,
    imageManagerLoadParams:     zf_back.zf_editor_image_manager_load_params,
    imageUploadURL:             zf_back.zf_editor_image_upload_action,
    imageManagerLoadURL:        zf_back.ajaxurl,
    imageUploadMethod:          'POST',
    imageManagerLoadMethod:     'POST',
    pastePlain:                 zf_back.zf_editor_settings.zf_editor_paste_plain,
    imageMaxSize:               zf_back.zf_max_upload_size,
    spellcheck:                 zf_back.zf_editor_settings.zf_spellcheck,
    rtgOptions:                 zf_back.zf_editor_settings.zf_rtg_options,
};

if( typeof zf_back.zf_editor_settings.quick_insert_buttons !== 'undefined' ) {
    zf_wysiwyg_config_advanced.quickInsertButtons = zf_back.zf_editor_settings.quick_insert_buttons;
    zf_wysiwyg_config_light.quickInsertButtons = zf_back.zf_editor_settings.quick_insert_buttons;
}
var zf_stamp;

/**
 * Global to hold selected answer format for `quiz` post type
 *
 * @type {string}
 */
var zf_selected_answer_format;

jQuery(document).ready(function ($) {

    window.onbeforeunload = function (e) {

        if (zombify_unsaved_form) {

            e = e || window.event;

            // For IE and Firefox prior to version 4
            if (e) {
                e.returnValue = 'Are you sure you want to exit without saving?';
            }

            // For Safari
            return 'Are you sure you want to exit without saving?';

        }
    };


    // Focus main Title field after page ready
    $('#zombify-main-section input[data-zombify-field-path="title"]:first').focus();

    $(document).on("change", "#zombify-form :input", function () {

        zombify_unsaved_form = true;

    });

    // Correct builder
    ZombifyBuilder.correctBuilder();

    // Zombify Post actions  Save/Preview/Publish
    if ($('#zf-fixed-bottom-pane').length) {

        if ($(window).width() > 700) {

            $('#zf-fixed-bottom-pane').appendTo('body');
        }

        // Event for previewing the Zombify post
        $(document).on("click", ".zombify_preview", function (e) {

            url_post_id = parseInt($('#zombify-form').find(".zombify_post_id").val());

            if (!url_post_id) {
                alert( zf_back.translatable["preview_alert"] );
                e.preventDefault();
                return false;
            }

            if (!$(this).attr("href")) {
                e.preventDefault();
                return false;
            }

        });

        if( $( '.zf-post-schedule').length ) {
            zf_stamp = $('#timestamp').html();
        }

        // Event for saving the Zombify post
        $(document).on("click", ".zombify_save", function (e) {

            e.preventDefault();

            if( $( '.zf-post-schedule').length ) {
            /* One extra validation step before saving */
                var isDateValid = handleSchedulingDate();
                if( ! isDateValid ) return false;
            }

            //Preview
            ZombifyBuilder.save($(this));

        });

        // Event for saving and publishing the Zombify post
        $(document).on("click", ".zombify_publish", function (e) {

            e.preventDefault();

            if( $( '.zf-post-schedule').length ) {
                /* One extra validation step before saving */
                var isDateValid = handleSchedulingDate();
                if( ! isDateValid ) return false;
            }

            //Preview
            ZombifyBuilder.save($(this), false, true);

        });
    }

    // Event for adding Zombify group
    $(document).on("click", ".zombify_add_group", function (e) {

        e.preventDefault();

        // Add new group
        ZombifyBuilder.addGroup($(this));

        /*
         * Special Case for Trivia Quiz, Input type
         * todo maybe rewrite in favor of changing `ZombifyBuilder.addGroup` method
         */
        /* Taking use of the global variables `quiz_type`, `group_name` */
        if( typeof quiz_type !== 'undefined' && typeof group_name !== 'undefined' ) {
            if ('trivia' === quiz_type && 'questions' === group_name) {
                var $newAnswerBox = $('.zf-answers-box').last();
                if ('input' === $newAnswerBox.data('format')) {
                    var $firstAnswer = $newAnswerBox.find('input[type="radio"][name*="correct"]').first();
                    /* Check the first [primary] answer as 'correct' in order to work with the existing logic */
                    $firstAnswer.attr('checked', true);
                    /* We don't need it checked as 'correct' for the other answer formats */
                    $firstAnswer.attr('default-correct', 'false');
                }
            }
        }

        zf_video_upload_func();

    });

    // Event for adding Zombify group
    $(document).on("click", ".zf-components .zombify_add_group", function (e) {

        $(this).closest('.zf-components').removeClass('zf-open');

    });

    // Event for removing Zombify group
    $(document).on("click", ".zombify_delete_group", function (e) {

        e.preventDefault();

        var $this = $( this );
        var $currentAnswerBox   = $this.parents( '.zf-answers-box' );
        var initialAnswerFormat = $currentAnswerBox.data( 'format' );

        var $previousCorrectIndex = $currentAnswerBox.parent().find( 'input[type="hidden"][data-previous-correct-pointer="true"]' );
        //console.log();

        // Delete group
        ZombifyBuilder.deleteGroup( $this );

        /*
         * Checking if removed answer in the Trivia Quiz, Input type,
         * and if so, add extra logic to take first answer as `correct`
         */
        /* Taking use of the global variable `group_name` */
        if(
            (
                'input' === initialAnswerFormat
                && typeof zf_selected_answer_format === 'undefined'
            )
        ||
            (
                typeof group_name !== 'undefined'
                && 'answers' === group_name
                && 'input' === zf_selected_answer_format
            )
        ) {
            var $firstAnswer = $currentAnswerBox.find( 'input[type="radio"][name*="correct"]' ).first();
            if ( ! $firstAnswer.is( ':checked' ) ) {
                /* Check the first [primary] answer as 'correct' in order to work with the existing logic */
                $firstAnswer.attr( 'checked', true );
                /* We don't need it checked as 'correct' for the other answer formats */
                $firstAnswer.attr( 'default-correct', 'false' );
            }
        }

        /* Taking care of the deletion of previously saved correct answer, clean up the value of separate input field also */
        if( typeof group_name !== 'undefined' && 'answers' === group_name ) {
            $previousCorrectIndex.val( '' );
        }
    });

    // Event for removing Media
    $(document).on("click", ".zf-remove-media", function (e) {
        e.preventDefault();

        var attach_id = $(this).closest(".zf-uploader").find("input[data-zf-media-id]").attr("data-zf-media-id");

        if( attach_id !== '' ) {

            zombify_virtual_addit_data.attach_id = attach_id;
            zombify_virtual_addit_data.addit_action = "zf_delete_media";

        }

        $(this).closest(".zf-form-group").find(".zombify_uploaded_image_item").remove();

        if( $(this).parents('.zf-uploader').find('.zf_media_player').length > 0 ) {

            $(this).parents('.zf-uploader').find('.zf_media_player').each(function(){
                var media = $(this);
                media[0].pause();
            });
        }

        // if( window.ZFMediaElementPlayer !== undefined ) {
        //
        //     window.ZFMediaElementPlayer.pause();
        //
        // } else {
        //     if( $(this).parents('.zf-uploader').find('div.zf-video-player').length > 0 ) {
        //
        //         var file = $(this).parents('.zf-uploader').find('div.zf-video-player');
        //
        //         new MediaElementPlayer(file[0], {success: function(media) {
        //                 media.pause();
        //             }
        //         });
        //
        //     }
        // }

        // Delete Media
        ZombifyBuilder.deleteMedia($(this));

        ZombifyBuilder.updateShowDependency();

        ZombifyBuilder.validateFields();

        zombify_virtual_unsaved_form = true;

        $(this).parent().find('input[type="file"]').attr("data-zf-file-browsed", 1);

        ZombifyBuilder.virtualSave();

    });

    $(document).on('change', '.zf-media-uploader .zf-checkbox-format input', function () {
        if (this.checked) {
            var format = $(this).data('format');
            $(this).parent().parent().find('input[type="radio"]').removeAttr('checked');
            $(this).attr('checked', 'checked');
            $(this).parent().parent().parent().attr('data-format', format);
        }
    });

    // Sorting the zombofy group Down
    $(document).on('click', '.js-zf-down', function (e) {
        e.preventDefault();

        ZombifyBuilder.sort($(this).closest(".zombify_group"), 'down');
    });

    // Sorting the zombify group Up
    $(document).on('click', '.js-zf-up', function (e) {
        e.preventDefault();

        ZombifyBuilder.sort($(this).closest(".zombify_group"), 'up');
    });


    // Sorting Quiz Answers
    // $('.zf-answers_container').sortable({});

    $(document).on("change", ".zombify_medatype_radio", function (e) {

        ZombifyBuilder.changeMediaType($(this));

    });

    $(document).on("change", ".zombify_quiz input", function() {

        ZombifyBuilder.updateDependencies(false);

        if( $(this).hasClass("zf-image_url") ){

            $(this).parent().find(".zf-submit_url").parents('.zf-uploader').find('.zf-preview-gif').hide();
            zf_get_video_by_url( this );

            $(this).attr("data-zf-url-changed", "1");

            zombify_virtual_unsaved_form = true;

            ZombifyBuilder.virtualSave();

        }

    });

    $(document).on("input", ".zombify_quiz :input[data-embed-url='1']", function () {

        $(this).closest(".zf-embed").find(".zf-embed-video").html("");

        parseEmbedURL($(this), $(this).val(), '100%', 500, $(this).attr("data-embed-sources"), 1);

    });

    $(document).on("input", ".zombify_embed_url_textarea", function () {

        $(this).closest(".zf-embed").find(".zf-embed-video").html("");

        parseEmbedURL($(this), $(this).val(), '100%', 500, $(this).attr("data-embed-sources"), 1);

    });

    $(document).on("change", ".zombify_quiz .zf-uploader :input[type='file']", function () {

        var container = $(this).closest("label");

        if (this.files && this.files[0]) {

            if ($(this).prop("multiple") == false) {

                $(this).closest(".zf-form-group").find(".zombify_uploaded_image_item").remove();

            }

            if( ZombifyBuilder.validateField( $(this) ) ) {

                var reader = new FileReader();
                var extension = this.files[0].name.split('.').pop().toLowerCase();

                reader.onload = function (e) {

                    if( extension === 'mp4' ) {
                        $(container).find(".zf-preview-gif-mp4").find('source').attr('src', e.target.result);
                        $(container).find(".zf-preview-gif-mp4").show();

                        $(container).find(".gif-video-wrapper:not(.zf-preview-gif-mp4)").each(function () {
                            $(this).hide()
                        });

                        $(container).find(".zf-preview-gif-mp4").find('video')[0].load();
                        $(container).find(".zf-preview-gif-mp4").find('video')[0].play();
                    } else {
                        $(container).find(".gif-video-wrapper:not(.zf-preview-img)").each(function () {
                            $(this).hide()
                        });
                        $(container).find(".zf-preview-img").attr('src', e.target.result).show();
                    }
                    $(container).closest(".zf-uploader").addClass("zf-uploader-uploaded");

                    ZombifyBuilder.updateShowDependency();

                }

                reader.readAsDataURL(this.files[0]);

            }

        }

    });


    // Image Start
    $(document).on('click', ".zf-uploader .zf-start-submit_url", function (e) {
        var url = $(this).parent().find('.zf-start-image_url').val();
        if(url) {
            $('.zf-after-start .zf-image_url').val(url);
            $('.zf-after-start .zf-image_url').attr("data-zf-url-changed", "1");
            $('.zf-after-start .zf-submit_url').trigger('click');
            $(this).parents('.zf-start').removeClass("zf-open");
            $('.zf-after-start input[data-zombify-field-path="title"]:first').focus();

            zombify_virtual_unsaved_form = true;

            ZombifyBuilder.virtualSave();

        } else {
            $(this).parents('.zf-uploader').find('.zf-get-url-popup').removeClass('zf-open');
        }

    });

    $(document).on("change", ".zf-after-start .zf-uploader :input[type='file']", function () {
        $('.zf-start.zf-open').removeClass('zf-open');
    });

    /*
     * Handle the change of quiz answer formats
     */
    $( document ).on( 'change', '.js-zf-answer-format input', function () {
        var $this = $( this );
        if ( this.checked ) {
            var format = $this.data( 'format' );
            var $answerBox = $this.parent().parent().siblings( '.zf-answers-box' )
            $answerBox.attr( 'data-format', format ); 

            /*
             * Extra logic for `input` answer format, since, in this case
             * the `correct answer` functionality can not be the same
             * */
            var radioList = $answerBox.find( 'input[type="radio"][name*="correct"]' );

            if( 'input' === format ) {
                zf_selected_answer_format = 'input';
                /* Check the first input's `correct` as the primary answer */
                radioList.each( function( index ) {
                    var $radio = $( this );
                    if( $radio.is( ':checked' ) ) {
                        $radio.attr( 'default-correct', 'true' );
                        $radio.attr( 'checked', false );
                    }
                } );
                var $firstRadio = radioList.first();
                $firstRadio.attr( 'checked', true );
                /* If it weren't checked before changing to `input` answer format, un-check it */
                if( ! $firstRadio.attr( 'default-correct' ) ) {
                    $firstRadio.attr( 'default-correct', 'false' )
                }
            } else {
                zf_selected_answer_format = 'text';
                /* Restore the default checked `correct` answer */
                radioList.each( function( index ) {
                    var $radio = $( this );
                    var defaultCorrect = $radio.attr( 'default-correct' );
                    if( 'true' === defaultCorrect ) {
                        $radio.attr( 'checked', true );
                    } else if( 'false' === defaultCorrect ) {
                        $radio.attr( 'checked', false );
                    }
                    $radio.removeAttr( 'default-correct' );
                } );
            }
        }
    } );

    $('.js-zf-answer-format input:checked').each(function () {

        var format = $(this).data('format');
        $(this).parent().parent().siblings('.zf-answers-box').attr('data-format', format);

    });

    $(document).on('change', '.zombify_quiz input', function() {

        ZombifyBuilder.updateShowDependency();

    });

    // Story add Component toggle
    $(document).on('click', '.zf-js-components_toggle', function (e) {
        e.preventDefault();
        if ($(this).parent().hasClass('zf-open')) {
            $(this).parent().removeClass('zf-open')
        } else {
            $(this).parent().addClass('zf-open')
        }
    });

    // add tags plugin (option panel)
    if ($('#tag-editor').length) {
        $('#tag-editor').tagEditor({
            placeholder: $('#tag-editor').attr("placeholder"),
            maxTags: $('#tag-editor').attr("data-count-limit"),
            autocomplete: {
                delay: 0,
                position: {collision: 'flip'}, // automatic menu position up/down
                source: zf_back.ajaxurl + '?action=zombify_get_tags'
            },
            forceLowercase: false
        });

    }

    // Decrease editor height on mobile
    if( $('.zombify-screen').hasClass('zf-screen-xs') ) {
        zf_wysiwyg_config_advanced.heightMin = 200;
        zf_wysiwyg_config_light.heightMin    = 200;
    }

    // wysiwyg plugin
    $('.zf-wysiwyg-advanced').each(function(){
        if( $(this).parents('.zf-erase-before-save').length === 0 ) {
            callFroalaEditor($(this), zf_wysiwyg_config_advanced);
        }
    });

    $('.zf-wysiwyg-light').each(function(){
        if( $(this).parents('.zf-erase-before-save').length === 0 ) {
            callFroalaEditor($(this), zf_wysiwyg_config_light);
        }
    });

    $('.zf-result-wysiwyg-light.active').each(function(){
        if( $(this).parents('.zf-erase-before-save').length === 0 ) {
            callFroalaEditor($(this), zf_wysiwyg_config_light);
        }
    });

    // add editor on result description field after click
    $(document).on('click', '.zf-result-wysiwyg-light', function (e) {
        e.preventDefault();
        e.stopPropagation();

        var _that = $(this);
        _that.addClass('active');

        _that.animate(
            { height: parseInt(zf_back.zf_editor_settings.zf_editor_lt_height) + 45 },
            500,
            function() {
                setTimeout(function(){ _that.froalaEditor(zf_wysiwyg_config_light); }, 500);
            }
        );
    });


    $('.zf-multiple-select').zombifyMultiSelect();

    ZombifyBuilder.updateShowDependency();
    ZombifyBuilder.updateDependencies(true);

    // Validating inputs

    //ZombifyBuilder.validateFields();


    /** URL field validation must contain http:// */
    $(document).on("change", "input[zf-validation-url]", function(){
       var url = $(this).val();
       if (!/^https?:\/\//i.test(url) && url !='') {
           url = 'http://' + url;
       }
       $(this).val(url);
    });

    $(document).on("change",".zf-preface-excerpt-cont input[name='zombify[use_preface]']", function () {

        if ($(this).prop("checked") == true) {

            $(this).parents('.zf-preface-excerpt-cont').siblings('.zf-preface').addClass('zf-open');
        } else {
            $(this).parents('.zf-preface-excerpt-cont').siblings('.zf-preface').removeClass('zf-open');
        }

    });

    $(document).on("change",".zf-preface-excerpt-cont input[name='zombify[use_excerpt]']", function () {

        if ($(this).prop("checked") == true) {

            $(this).parents('.zf-preface-excerpt-cont').siblings('.zf-excerpt').addClass('zf-open');
        } else {
            $(this).parents('.zf-preface-excerpt-cont').siblings('.zf-excerpt').removeClass('zf-open');
        }

    });


    zf_video_upload_func();

    $(document).on("click", ".zf_discard_virtual", function(){

        if( window.confirm(zf_back.translatable["confirm_discard_virtual"]) ){

            var quiz_type = $(".zombify_quiz_type").val();
            var quiz_subtype = $(".zombify_quiz_subtype").val();

            toggleDisabledButton('add');

            jQuery.ajax({
                url: zf_back.ajaxurl,
                type: 'GET',
                data: {action: 'zombify_discard_virtual', type: quiz_type, subtype: quiz_subtype},
                dataType: 'json',
                success: function (data) {

                    toggleDisabledButton('remove');

                    if( typeof data.result != 'undefined' && data.result == 1 ){

                        window.location.reload();

                    }

                }
            });

        }

    });

    zf_autosave_virtual();

    // $(".zf_media_player").each(function(){
    //
    //     $(this).mediaelementplayer({
    //         alwaysShowControls: true
    //     });
    //
    // });

    $(document).on('click', '.zf-preview-video-block', function(e) {
        e.preventDefault();
    });

    $('.zf-preview-video-block').each(function(){
        var _this = $(this);

        _this.hover(function() {
            _this.parents('.zf-uploader').addClass('zf-uploader-on-progress');
        }, function() {
            _this.parents('.zf-uploader').removeClass('zf-uploader-on-progress');
        });
    });

    if( typeof story_first_group != 'undefined' && story_first_group != '' ){

        $(".zombify_add_group[data-include-group='"+story_first_group+"']").attr("data-zf-without-focus", "1");

        $(".zombify_add_group[data-include-group='"+story_first_group+"']").trigger("click");

    }

    $(document).on('keyup keypress', '#zombify-form', function(e) {

        var keyCode = e.keyCode || e.which;

        if (keyCode === 13) {

            e.preventDefault();
            return false;

        }

    });

    $( document ).on( 'click', '#zf-edit-timestamp', function( e ) {
        e.preventDefault();
        $(this).hide();
        $('#zf-cancel-timestamp').show();
        $('#zf-timestampdiv').show();
        $( '#display_date_fields' ).val( 'block' );
    } );

    /* Save scheduled date */
    $( document ).on( 'click', '#zf-save-timestamp', function( e ) {
        e.preventDefault();
        handleSchedulingDate( 'save' );
        $( '#display_date_fields' ).val( 'none' );
    } );

    /* Reset scheduled date */
    $( document ).on( 'click', '#zf-cancel-timestamp', function( e ) {
        e.preventDefault();
        $(this).hide();
        $('#zf-edit-timestamp').show();
        $('#zf-timestampdiv').hide();

        handleSchedulingDate( 'cancel' );
        $( '#display_date_fields' ).val( 'none' );
    } );

    jQuery('body').on( 'zfAjaxContentLoaded', function(e, newContent) {
        callMediaElementJs(newContent.output);
    });

});

function froalaImageErrorCallback(e, editor, error, response) {
    toggleDisabledButton('remove');

    if (error.code == 5) {
        var $popup = editor.popups.get('image.insert');
        var $layer = $popup.find('.fr-image-progress-bar-layer');

        $layer.find('h3').html(zf_back.zf_max_upload_message);
    }
}

function froalaImageInsertedCallback(e, editor, $img, response) {
    if( undefined !== $img.data('zf_media_id') ) {
        $img.addClass('wp-image-'+$img.data('zf_media_id'));
    } else if( undefined !== response && undefined !== JSON.parse(response).id ) {
        $img.addClass('wp-image-'+JSON.parse(response).id);
    }
}

function zombify_makeid() {
    var text = "";
    var possible = "abcdefghijklmnopqrstuvwxyz0123456789";

    for( var i=0; i < 10; i++ )
        text += possible.charAt(Math.floor(Math.random() * possible.length));

    return text;
}

function zf_video_upload_func() {

    jQuery(".zf-video-upload").each(function(){

        if( jQuery(this).attr("data-zf-resumable") != 1 && jQuery(this).parents('.zf-erase-before-save').length == 0 ) {

            jQuery(this).attr("data-zf-resumable", "1");

            var unid = zombify_makeid();
            jQuery(this).attr("data-zombify-field-unique", unid);
            jQuery(this).closest(".zf-uploader").find("input[data-zombify-field-path='" + jQuery(this).attr("data-zombify-field-path") + "']").attr("data-zombify-field-unique", unid);

            var n = zombify_video_upload.length;
            var field_path = jQuery(this).attr("data-zombify-field-path");
            var quiz_type = jQuery(".zombify_quiz_type").val();
            var object_index = unid;
            var progressBar = new ProgressBar(jQuery(this).closest('.zf-uploader').find('.zf-progressbar'));
            var that = jQuery(this);

            zombify_video_upload[object_index] = new Resumable({
                target: zf_back.ajaxurl,
                query: {field_path: field_path, unid: unid, action: 'zombify_video_upload'},
                uploadMethod: 'POST',
                quiz_type: quiz_type,
                maxFiles: 1,
                chunkSize: zf_back.chunkSize
            });

            zombify_video_upload[object_index].assignBrowse(jQuery(this));

            zombify_video_upload[object_index].on('fileSuccess', function (file, data) {

                toggleDisabledButton('remove');

                data = JSON.parse(data);

                if (typeof data.result != 'undefined' && data.result == 1) {
                    jQuery("input[data-zombify-field-path='" + data.field_path + "'][data-zombify-field-unique='" + data.unid + "']").val(data.attachment_id);
                    jQuery("input[data-zombify-field-path='" + data.field_path + "'][data-zombify-field-unique='" + data.unid + "']").attr("data-zf-media-id", data.attachment_id);

                    zombify_virtual_unsaved_form = true;

                    progressBar.finish();

                    setTimeout(function () {

                        that.parents('.zf-uploader').find('.zf-preview-video-block').hide();
                        that.parents('.zf-uploader').addClass('zf-uploader-uploaded');
                        that.parents('.zf-uploader').next('.zf-file-info').hide();
                        that.parents('.zf-uploader').find('.zf-video-player-preview').prop('src', data.file_url);

                        var video = that.parents('.zf-uploader').find('.zf-video-player-preview');
                        video.css('display','block');

                        // if (that.parents('.zf-uploader').find('div.zf-video-player-preview').length === 0) {
                        //     window.ZFMediaElementPlayer = new MediaElementPlayer(video[0], {
                        //          stretching: 'auto',
                        //         success: function (mediaElement, originalNode, instance) {
                        //             that.parents('.zf-uploader').find('.zf-video-player-preview').show();
                        //         }
                        //     });
                        // } else {
                        //     that.parents('.zf-uploader').find('.zf-video-player-preview').show();
                        // }

                    }, 1000);

                    ZombifyBuilder.virtualSave();

                } else {

                    progressBar.finish();

                    jQuery('.zf-preview-video-block').hide();

                    if (typeof data.errorMessage != 'undefined' && data.errorMessage != '') {

                        alert(data.errorMessage);

                    } else
                        alert(zf_back.translatable["incorrect_file_upload"]);

                }

                ZombifyBuilder.updateShowDependency();

            });

            zombify_video_upload[object_index].on('fileAdded', function (file, event) {

                var validFile = 1;

                toggleDisabledButton('add');

                var exts = jQuery("input[data-zombify-field-path='" + this.getOpt("query").field_path + "'][data-zombify-field-unique='" + this.getOpt("query").unid + "']").attr("zf-validation-extensions");
                var valid_extensions = exts.split(",");

                for (j = 0; j < valid_extensions.length; j++) {
                    valid_extensions[j] = valid_extensions[j].trim();
                }

                var nm = file.fileName;

                var file_ext = nm.substr(nm.lastIndexOf('.') + 1);
                file_ext = file_ext.toLowerCase();

                if (!ZombifyBuilder.inArray(file_ext, valid_extensions)) {

                    validFile = 2;

                }

                var maxsize = jQuery("input[data-zombify-field-path='" + this.getOpt("query").field_path + "'][data-zombify-field-unique='" + this.getOpt("query").unid + "']").attr("zf-validation-maxsize");

                if( maxsize < file.size ){

                    validFile = 3;

                }

                var parentDiv = jQuery("input[data-zombify-field-path='" + this.getOpt("query").field_path + "'][data-zombify-field-unique='" + this.getOpt("query").unid + "']").closest(".zf-uploader");

                switch( validFile ){

                    case 1:

                        parentDiv.find('.zf-preview-video-block').show();

                        progressBar.fileAdded();

                        zombify_video_upload[object_index].upload();

                        break;

                    case 2:

                        var errMsg = zf_back.translatable['invalid_file_extension'] + ' ' + jQuery("input[data-zombify-field-path='" + this.getOpt("query").field_path + "'][data-zombify-field-unique='" + this.getOpt("query").unid + "']").attr("zf-validation-extensions");

                        if (parentDiv.find(".zf-help").length > 0) {
                            parentDiv.find(".zf-help").html(errMsg);
                        } else {
                            parentDiv.append('<span class="zf-help">' + errMsg + '</span>');
                        }

                        break;

                    case 3:

                        var errMsg = zf_back.translatable['invalid_file_size'] + ' ' + Math.round( parseInt(jQuery("input[data-zombify-field-path='" + this.getOpt("query").field_path + "'][data-zombify-field-unique='" + this.getOpt("query").unid + "']").attr("zf-validation-maxsize"))/1024/1024 )+zf_back.translatable['mb'];

                        if (parentDiv.find(".zf-help").length > 0) {
                            parentDiv.find(".zf-help").html(errMsg);
                        } else {
                            parentDiv.append('<span class="zf-help">' + errMsg + '</span>');
                        }

                        break;

                }

            });


            zombify_video_upload[object_index].on('progress', function () {

                progressBar.uploading(zombify_video_upload[object_index].progress() * 100);

            });

            zombify_video_upload[object_index].on('fileError', function (file, message) {
                toggleDisabledButton('remove');
                console.debug('fileError', file, message);
                alert(zf_back.translatable["incorrect_file_upload"]);
            });

            zombify_video_upload[object_index].on('error', function (message, file) {
                toggleDisabledButton('remove');
                console.debug('error', message, file);
                alert(zf_back.translatable["incorrect_file_upload"]);
            });

            jQuery('.zf-progress-cancel-btn').on('click', function () {

                toggleDisabledButton('add');

                n = zombify_video_upload[object_index].files.length;

                for (var i = 0; i < n; i++) {

                    var unid = zombify_video_upload[object_index].files[i].uniqueIdentifier;

                    if (unid == '') continue;

                    jQuery.ajax({
                        url: zf_back.ajaxurl,
                        type: 'GET',
                        data: {cancel: 1, action: 'zombify_video_upload', uniqueIdentifier: unid},
                        dataType: 'json',
                        success: function (data) {
                            toggleDisabledButton('remove');
                        }
                    });

                }
                zombify_video_upload[object_index].cancel();


                jQuery(this).parents('.zf-preview-video-block').hide();
            });

        }

    });

}

function ProgressBar(element) {
    this.fileAdded = function() {
        jQuery(element).find('.zf-progressbar-active').css('width','0%');
    },

    this.uploading = function(progress) {
        jQuery(element).find('.zf-progressbar-active').css('width', progress + '%');
    },

    this.finish = function() {
    }
}

function zombify_submit_process( perc ){

    jQuery(".zf-image-preview-block").each(function(){

        if( jQuery(this).closest(".zf-uploader").find("input[data-zombify-field-type='file']").attr("data-zf-file-browsed") == 1 || jQuery(this).closest(".zf-uploader").find("input.zf-image_url").attr("data-zf-url-changed") == 1 ){

            jQuery(this).show();

            var progressBar = new ProgressBar(jQuery(this).find(".zf-progressbar"));
            progressBar.fileAdded();
            progressBar.uploading(perc > 99 ? 99 : perc);

            if( perc >= 100 ){

                jQuery(this).addClass("zf_remove_progress");

                progressBar.finish();

            }
        }


    });

}

function zf_autosave_virtual(){


    ZombifyBuilder.virtualSave();

    setTimeout("zf_autosave_virtual();", zombify_virtual_save_interval);

}

jQuery.fn.zombifyMultiSelect = function () {

    return this.each(function () {

        var $this = jQuery(this),
            selectedLabel = '',
            selected = [],
            limit = zf_back.zf_category_select_limit;

        $this.find('.zf-select_header').on('click',function(e){
            $this.toggleClass('zf-active');
        });
        $this.on('click',function(e){
            e.stopPropagation();
        });
        jQuery('body').on('click',function(e){
            $this.removeClass('zf-active');
        });

        var init = function(){
            selected = [];
            selectedLabel = '';
            if($this.find('input[type="checkbox"]:checked').length) {
                $this.find('input[type="checkbox"]:checked').each(function(){
                    var label = jQuery(this).data('label');
                    selected.push(label);
                });
                for(var i = 0; i < selected.length; i++) {
                    if( i + 1 ==  selected.length ) {
                        selectedLabel += selected[i];
                    } else {
                        selectedLabel += selected[i]+ ', ';
                    }
                    $this.find('.zf-selected').html(selectedLabel)
                }
            } else {
                $this.find('.zf-selected').html($this.find('.zf-select_header').data('label'))
            }
        };

        init();

        $this.find('input[type="checkbox"]').on('change',function(e){

            if( limit === '1' ) {

                if( jQuery(this).prop("checked") === false ) {
                    $this.find('input[type="checkbox"]').prop('checked', false);
                } else {
                    $this.find('input[type="checkbox"]').prop('checked', false);
                    jQuery(this).prop("checked", true)
                }

                jQuery(this).parents('.zf-multiple-select').removeClass('zf-active');

            } else {

                if (jQuery(this).prop("checked") == true && $this.find('input[type="checkbox"]:checked').length >= limit) {
                    $this.find('input[type="checkbox"]').attr('disabled', true);
                    $this.find('input[type="checkbox"]:checked').attr('disabled', false);
                } else {
                    $this.find('input[type="checkbox"]').attr('disabled', false);
                }

            }

            init();

        });

    });
};

function toggleDisabledButton( $toggle ) {
    if( $toggle === 'add' ) {
        jQuery('.zf-fixed-bottom-pane').find('.zombify_save').addClass('zf-disabled');
        jQuery('.zf-fixed-bottom-pane').find('.zf_discard_virtual').addClass('zf-disabled');
        jQuery('.zf-fixed-bottom-pane').find('.zombify_preview').addClass('zf-disabled');
        jQuery('.zf-fixed-bottom-pane').find('.zombify_publish').addClass('zf-disabled');
    } else if( $toggle === 'remove' ) {
        jQuery('.zf-fixed-bottom-pane').find('.zombify_save').removeClass('zf-disabled');
        jQuery('.zf-fixed-bottom-pane').find('.zf_discard_virtual').removeClass('zf-disabled');
        jQuery('.zf-fixed-bottom-pane').find('.zombify_preview').removeClass('zf-disabled');
        jQuery('.zf-fixed-bottom-pane').find('.zombify_publish').removeClass('zf-disabled');
    }
}

function IsJsonString(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}

/**
 * Handle / validate inserted date fields' values for post scheduling
 *
 * @param {string} action Can be 'save' or 'cancel'
 *
 * @returns {boolean}
 */
function handleSchedulingDate( action ) {
    var attemptedDate,
        currentDate,
        aa              = jQuery( '#aa' ).val(),
        mm              = jQuery( '#mm' ).val(),
        jj              = jQuery( '#jj' ).val(),
        hh              = jQuery( '#hh' ).val(),
        mn              = jQuery( '#mn' ).val(),
        $timeStampDiv   = jQuery( '.zf-timestamp-wrap'),

    attemptedDate   = new Date( aa, mm - 1, jj, hh, mn );
    currentDate     = new Date(
        jQuery( '#cur_aa' ).val(),
        jQuery( '#cur_mm' ).val() -1,
        jQuery( '#cur_jj' ).val(),
        jQuery( '#cur_hh' ).val(),
        jQuery( '#cur_mn' ).val()
    );    

    // Catch unexpected date problems.
    if ( attemptedDate.getFullYear() != aa
        || ( 1 + attemptedDate.getMonth() ) != mm
        || attemptedDate.getDate() != jj
        || attemptedDate.getMinutes() != mn
    ) {
        $timeStampDiv.find( 'label' ).each( function(){
            jQuery( this ).addClass( 'zf-error' );
        } );
        return false;
    } else {
        $timeStampDiv.find( 'label' ).each( function(){
            jQuery( this ).removeClass( 'zf-error' );
        } );
    }


    if( action == 'save' ) {

        jQuery('#zf-edit-timestamp').show();
        jQuery('#zf-cancel-timestamp').hide();
        jQuery('#zf-timestampdiv').hide();

        var text = '<i>'
                + zf_back.translatable.schedule_stamp.date_format
                .replace( '%1$s', jQuery( 'option[value="' + mm + '"]', '#mm' ).attr( 'data-text' ) )
                .replace( '%2$s', parseInt( jj, 10 ) )
                .replace( '%3$s', aa )
                .replace( '%4$s', ( '00' + hh ).slice( -2 ) )
                .replace( '%5$s', ( '00' + mn ).slice( -2 ) )
                + '</i>';

        var timestampText = '', button_text = '';
		// Determine what the publish should be depending on the date and post status.
			if ( attemptedDate > currentDate && jQuery('#zombify_original_post_status').val() != 'future' ) {
				// Schedule for
				timestampText = zf_back.translatable.schedule_stamp.schedule_for;
				button_text = zf_back.translatable.publish_button.schedule;
			} else if ( attemptedDate <= currentDate && jQuery('#zombify_original_post_status').val() != 'publish' ) {
				// Publish on
				timestampText = zf_back.translatable.schedule_stamp.publish_on;
				button_text = zf_back.translatable.publish_button.publish;
			} else {
				// Published on
				timestampText = zf_back.translatable.schedule_stamp.published_on;
				button_text = zf_back.translatable.publish_button.publish;
			}
		
		timestampText = timestampText + ' ' + text;
		
        var $timestamp  = jQuery( '#timestamp' );
        var $stampText  = jQuery( '#stamp_text');
		var $zombify_publish = jQuery( '.zombify_publish' ).find( '.zf-text' );

        $timestamp.html( timestampText );
        $stampText.val( timestampText );
        $zombify_publish.text( button_text );

        if ( currentDate.toUTCString() == attemptedDate.toUTCString() ) {
            // Re-set to the current value.
            $timestamp.html( zf_stamp );
            $stampText.val( zf_stamp );
        }
    }

    if( action == 'cancel' ) {
        // Re-set to the current value.
        jQuery( '#timestamp' ).html( zf_stamp );
        jQuery( '#stamp_text').val( zf_stamp );
        jQuery( '#aa' ).val( document.getElementById( 'aa' ).defaultValue );
        jQuery( '#mm' ).val( jQuery( '#hidden_mm' ).val() );
        jQuery( '#jj' ).val( document.getElementById( 'jj' ).defaultValue );
        jQuery( '#hh' ).val( document.getElementById( 'hh' ).defaultValue );
        jQuery( '#mn' ).val( document.getElementById( 'mn' ).defaultValue );
        jQuery( '.zombify_publish' ).find( '.zf-text' ).text( zf_back.translatable.publish_button.publish );
    }

    return true;
}

function callFroalaEditor( el, settings ) {

    el.froalaEditor(settings)
    .on('froalaEditor.image.beforeUpload', function (e, editor, images) {
        toggleDisabledButton('add');
    })
    .on('froalaEditor.image.uploaded', function (e, editor, response) {
        toggleDisabledButton('remove');
    })
    .on('froalaEditor.image.inserted', function (e, editor, $img, response) {
        froalaImageInsertedCallback (e, editor, $img, response);
    })
    .on('froalaEditor.image.error', function (e, editor, error, response) {
        froalaImageErrorCallback (e, editor, error, response);
    });

}

function callMediaElementJs(content) {

    // Are there media players in the incoming set of posts?
    if ( ! content || -1 === content.indexOf( 'wp-audio-shortcode' ) && -1 === content.indexOf( 'wp-video-shortcode' ) ) {
        return;
    }

    // Don't bother if mejs isn't loaded for some reason
    if ( 'undefined' === typeof mejs ) {
        return;
    }

    // Modified to not initialize already-initialized players, as Mejs doesn't handle that well
    jQuery(function () {
        var settings = {};

        if ( typeof _wpmejsSettings !== 'undefined' ) {
            settings.pluginPath = _wpmejsSettings.pluginPath;
        }

        settings.success = function (mejs) {
            var autoplay = mejs.attributes.autoplay && 'false' !== mejs.attributes.autoplay;
            if ( 'flash' === mejs.pluginType && autoplay ) {
                mejs.addEventListener( 'canplay', function () {
                    mejs.play();
                }, false );
            }
        };
        jQuery('.wp-audio-shortcode, .wp-video-shortcode').not( '.mejs-container' ).mediaelementplayer( settings );
    });
}
