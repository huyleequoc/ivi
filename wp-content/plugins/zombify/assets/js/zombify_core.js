Array.prototype.contains = function(obj) {
    var i = this.length;
    while (i--) {
        if (this[i] === obj) {
            return true;
        }
    }
    return false;
};

var isSafari11 = false;
navigator.browserSpecs = (function(){
    var ua = navigator.userAgent, tem,
        M = ua.match(/(opera|chrome|safari|firefox|msie|trident(?=\/))\/?\s*(\d+)/i) || [];
    if(/trident/i.test(M[1])){
        tem = /\brv[ :]+(\d+)/g.exec(ua) || [];
        return {name:'IE',version:(tem[1] || '')};
    }
    if(M[1]=== 'Chrome'){
        tem = ua.match(/\b(OPR|Edge)\/(\d+)/);
        if(tem != null) return {name:tem[1].replace('OPR', 'Opera'),version:tem[2]};
    }
    M = M[2]? [M[1], M[2]]: [navigator.appName, navigator.appVersion, '-?'];
    if((tem = ua.match(/version\/(\d+)/i))!= null)
        M.splice(1, 1, tem[1]);
    return {name:M[0], version:M[1]};
})();

if (navigator.browserSpecs.name.toLowerCase() == 'safari') {
    if (navigator.browserSpecs.version >= 11 && navigator.browserSpecs.version < 11.5) {
        isSafari11 = true;
    }
}
var ZombifyBuilder = new function() {

    this.saving_post = false;
    this.saving_virtual_post = false;
    this.trigger_post_save = false;
    this.input_focus = false;
    this.trigger_post_save_data = {};

    // Add new group
    this.addGroup = function( eButton ){

        // Get quiz type
        quiz_type = eButton.closest("form").find(".zombify_quiz_type").val();

        // Get group name
        group_name = eButton.data("zombify-group");

        // Find the parent container
        if( eButton.attr("data-zombify-inside") == 1 ){

            var parentGroupContainer = eButton.closest(".zombify_group").parent().closest(".zombify_group");

        } else {

            var parentGroupContainer = eButton.closest(".zombify_group");

        }

        if( parentGroupContainer.length > 0 ){

            var parentGroup = parentGroupContainer;

        } else {

            var parentGroup = eButton.closest(".zombify_quiz");

        }

        var  reverse =  parentGroup.find(".zf-"+group_name+"-container").data("reverse");

        // Destroy wysiwygs
        parentGroup.find(".zombify_group[data-zombify-group-name='"+group_name+"']").eq(0).find('.zf-wysiwyg-advanced').each(function(){
            if( jQuery(this).parents('.zf-erase-before-save').length === 0 ) {
                jQuery(this).froalaEditor('destroy');
            }
        });
        parentGroup.find(".zombify_group[data-zombify-group-name='"+group_name+"']").eq(0).find('.zf-wysiwyg-light').each(function(){
            if( jQuery(this).parents('.zf-erase-before-save').length === 0 ) {
                jQuery(this).froalaEditor('destroy');
            }
        });
        parentGroup.find(".zombify_group[data-zombify-group-name='"+group_name+"']").eq(0).find('.zf-result-wysiwyg-light.active').each(function(){
            if( jQuery(this).parents('.zf-erase-before-save').length === 0 ) {
                jQuery(this).froalaEditor('destroy');
            }
        });

        // Get group clone
        var cloneGroup = parentGroup.find(".zombify_group[data-zombify-group-name='"+group_name+"']").eq(0).clone();

        // Create wysiwygs
        parentGroup.find(".zombify_group[data-zombify-group-name='"+group_name+"']").eq(0).find('.zf-wysiwyg-advanced').each(function(){
            if( jQuery(this).parents('.zf-erase-before-save').length === 0 ) {
                jQuery(this).froalaEditor(zf_wysiwyg_config_advanced);
            }
        });
        parentGroup.find(".zombify_group[data-zombify-group-name='"+group_name+"']").eq(0).find('.zf-wysiwyg-light').each(function(){
            if( jQuery(this).parents('.zf-erase-before-save').length === 0 ) {
                jQuery(this).froalaEditor(zf_wysiwyg_config_light);
            }
        });
        parentGroup.find(".zombify_group[data-zombify-group-name='"+group_name+"']").eq(0).find('.zf-result-wysiwyg-light.active').each(function(){
            if( jQuery(this).parents('.zf-erase-before-save').length === 0 ) {
                jQuery(this).froalaEditor(zf_wysiwyg_config_light);
            }
        });

        if( eButton.attr("data-include-group") != '' ){

            cloneGroup.find(".zf-included-group").each(function(){

                if( !jQuery(this).hasClass("zf-"+eButton.attr("data-include-group")+"_container") )
                    jQuery(this).remove();

            });

        }

        //Remove progress bar
        cloneGroup.find('input[type="file"]').attr("data-zf-file-browsed", 0);
        cloneGroup.find(".zf-image-preview-block").hide();
        cloneGroup.find(".zf-image-preview-block").removeClass("zf_remove_progress");

        // Remove cloned elements
        cloneGroup.find(".zombify_clone").remove();

        // Add zombify_clone class to clonned group
        cloneGroup.addClass("zombify_clone");

        cloneGroup.find('.zf-hidden').removeClass('zf-hidden');

        // Erase filled data from inputs
        cloneGroup.find(":input[type!='checkbox'][type!='radio']").val('');
        cloneGroup.find(":input[type='radio'][data-zombify-erase-on-clone]").prop('checked', false);

        // Remove existing uploaded files
        cloneGroup.find(".zombify_uploaded_image_item").remove();

        // Delete embedded video content
        cloneGroup.find(".zf-embed-video").html("");

        // Remove buttons open class
        cloneGroup.find(".zf-components").removeClass("zf-open");

        // Add group type
        cloneGroup.find(".zombify_group_format").attr('data-group',eButton.data('type'));
        cloneGroup.find("input[data-zombify-field-path='story/group_format']").val(eButton.data('type'));

        // Delete image previews
        cloneGroup.find(".zf-uploader").removeClass("zf-uploader-uploaded");
        cloneGroup.find(".zf-uploader").find(".zf-preview-img").attr("src", "").hide();

        // Unique ID fields new values
        cloneGroup.find("[data-unique-id]").each(function(){

            randStr = ZombifyBuilder.randomString(32);

            jQuery(this).val( randStr );
        });

        // Create regular expression
        var regExp = new RegExp( "\\["+group_name+"\\]\\[\\d+\\]" );

        // Set temporary indexes
        cloneGroup.find(":input").each(function() {
            this.name = this.name.replace(regExp, "["+group_name+"][10000]");
            jQuery(this).attr("data-zombify-name-index", "10000");
        });


        // Append new group

        if(eButton.attr("data-zombify-inside") == 1) {

            cloneGroup.insertAfter(eButton.closest(".zombify_group"));

        } else if (eButton.attr("data-zombify-position") == 'first') {

            cloneGroup.prependTo( parentGroup.find(".zf-"+group_name+"_container"));

        } else {

            cloneGroup.appendTo( parentGroup.find(".zf-"+group_name+"_container"));
        }

        // Implement wysiwyg
        cloneGroup.find('.zf-wysiwyg-advanced').froalaEditor(zf_wysiwyg_config_advanced);
        cloneGroup.find('.zf-wysiwyg-light').froalaEditor(zf_wysiwyg_config_light);

        // Destroy wysiwygs for results
        cloneGroup.find('.zf-result-wysiwyg-light.active').each(function(){
            jQuery(this).removeClass('active');
            jQuery(this).froalaEditor('destroy');
            jQuery(this).css({'display': 'block', 'height': '80px'});
            jQuery(this).siblings('.fr-box.fr-basic').remove();
        });

        cloneGroup.find(".zf-video-upload").each(function(){

            jQuery(this).attr("data-zf-resumable", "");

        });

        // Remove image validations
        cloneGroup.find('.zf-help').remove();

        // Show delete icon
        if( cloneGroup.parents('.zf-answers_container').find('.zombify_group').length > 1 ) {

            cloneGroup.parents('.zf-answers_container').find('.zombify_group').each(function() {

                jQuery(this).find('.zombify_delete_group').removeClass('zf-hide-delete-icon');

            });

        } else if( cloneGroup.parents('.zf-results_container').find('.zombify_group').length > 1 ) {

            cloneGroup.parents('.zf-results_container').find('.zombify_group').each(function() {

                jQuery(this).find('.zombify_delete_group').removeClass('zf-hide-delete-icon');

            });

        } else if( cloneGroup.parents('.zf-list_container').find('.zombify_group').length > 1 ) {

            cloneGroup.parents('.zf-list_container').find('.zombify_group').each(function() {

                jQuery(this).find('.zombify_delete_group').removeClass('zf-hide-delete-icon');

            });

        } else {

            cloneGroup.find('.zf-answers_container').find('.zombify_group').find('.zombify_delete_group').addClass('zf-hide-delete-icon');

            cloneGroup.parents('.zf-questions_container').find('.zombify_delete_group').each(function() {

                if( jQuery(this).parents('.zf-answers_container').length == 0 ) {

                    jQuery(this).removeClass('zf-hide-delete-icon');

                }

            });

        }

        // Rearrange input names indexes
        var elements = parentGroup.find(".zf-"+group_name+"_container").find(".zombify_group[data-zombify-group-name='"+group_name+"']");

        elements.each(function(index) {

            var prefix = "["+group_name+"][" + index + "]";

            if( eButton.attr("data-zf-not-arrange-group") != 1 ) { jQuery(this).find(".zf-index").html( reverse ? elements.length - index  : index + 1 ); }

            jQuery(this).find(":input").each(function() {

                this.name = this.name.replace(regExp, prefix);

                var group_index = jQuery(this).closest(".zombify_group").index();

                jQuery(this).attr("data-zombify-name-index", group_index);

                if( jQuery(this).attr("data-zf-use-index-as-value") == 1 ){
                    jQuery(this).val( group_index );
                }
            });
            jQuery(this).find('.zombify_medatype_radio[checked]').prop( "checked", true );
        });

        this.updateDependencies(false);

        if( eButton.data("zf-without-focus") == "1" ){

            if( this.input_focus ) {

                cloneGroup.find('.zf-inner-wrapper input[type=text]:first').focus();

            } else {

                jQuery("input[data-zombify-field-path='title']").focus();

            }

            eButton.data("zf-without-focus", "0");

            this.input_focus = true;

        } else {

            if (eButton.data('type')) {

                cloneGroup.find('.zf-inner-wrapper[data-type=' + eButton.data('type') + '] input:first').focus();

            } else {

                cloneGroup.find('.zf-inner-wrapper input[type=text]:first').focus();

            }

        }

    };

    this.randomString = function(length) {
        var text = "";
        var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

        for( var i=0; i < length; i++ )
            text += possible.charAt(Math.floor(Math.random() * possible.length));

        return text;
    };

    // Delete group
    this.deleteGroup = function( eButton ){

        zombify_group = eButton.closest(".zombify_group");

        group_name = zombify_group.attr("data-zombify-group-name");


        if( zombify_group.parent().find(".zombify_group[data-zombify-group-name='"+group_name+"']").length > 1 || eButton.closest(".zf-"+group_name+"_container").hasClass("zf-must-delete") ){

            // Remove zombify_group class
            zombify_group.removeClass("zombify_group");

            // Create regular expression
            var regExp = new RegExp( "\\["+group_name+"\\]\\[\\d+\\]" );

            // Rearrange input names indexes
            eButton.closest(".zf-"+group_name+"_container").find(".zombify_group[data-zombify-group-name='"+group_name+"']").each(function(index) {

                var prefix = "["+group_name+"][" + index + "]";

                jQuery(this).find(":input").each(function() {
                    this.name = this.name.replace(regExp, prefix);
                });

            });

            var  reverse =  zombify_group.parents(".zf-"+group_name+"-container").data("reverse");

            // Rearrange input names indexes
         var elements = zombify_group.parent().find(".zombify_group[data-zombify-group-name='"+group_name+"']");

            elements.each(function(index) {

                var prefix = "["+group_name+"][" + index + "]";

                if( eButton.attr("data-zf-not-arrange-group") != 1 ) { jQuery(this).find(".zf-index").html( reverse ? elements.length - index : index + 1 ); }

                jQuery(this).find(":input").each(function() {
                    this.name = this.name.replace(regExp, prefix);
                    jQuery(this).attr("data-zombify-name-index", index);
                });

            });

            // Hide delete button if answer is single
            if( zombify_group.parent().find(".zombify_group[data-zombify-group-name='"+group_name+"']").length == 1 ) {

                if( group_name === 'answers' ||  group_name === 'results' || group_name === 'list') {

                    zombify_group.parent().find(".zombify_group[data-zombify-group-name='"+group_name+"']").find('.zombify_delete_group').addClass('zf-hide-delete-icon');

                } else if ( group_name === 'questions' ) {

                    zombify_group.parent().find(".zombify_group[data-zombify-group-name='"+group_name+"']").find('.zombify_delete_group').each(function() {

                        if( jQuery(this).parents('.zf-answers_container').length == 0 ) {

                            jQuery(this).addClass('zf-hide-delete-icon');

                        }

                    });

                }
            }

            // Delete group
            zombify_group.remove();

        } else {

            // Destroy wysiwygs
            zombify_group.find('.zf-wysiwyg-advanced').each(function(){
               jQuery(this).froalaEditor('destroy');
            });
            zombify_group.find('.zf-wysiwyg-light').each(function(){
               jQuery(this).froalaEditor('destroy');
            });

            // Get group clone
            var cloneGroup = zombify_group.clone();

            // Remove cloned elements
            cloneGroup.find(".zombify_clone").remove();

            // Remove cloned class
            cloneGroup.removeClass("zombify_clone");

            // Erase filled data from inputs
            cloneGroup.find(":input").val('');

            // Append new group
            cloneGroup.appendTo( zombify_group.parent() );

            // Delete group
            zombify_group.remove();

            // Create regular expression
            var regExp = new RegExp( "\\["+group_name+"\\]\\[\\d+\\]" );

            var prefix = "["+group_name+"][0]";

            cloneGroup.find(":input").each(function() {
                this.name = this.name.replace(regExp, prefix);
            });

            // Implement wysiwyg
            cloneGroup.find('.zf-wysiwyg-advanced').froalaEditor(zf_wysiwyg_config_advanced);
            cloneGroup.find('.zf-wysiwyg-light').froalaEditor(zf_wysiwyg_config_light);
        }

        this.updateDependencies(false);
    };

    // Delete Media
    this.deleteMedia =function (eButton) {
        eButton.parent().find('img').each(function () {
            jQuery(this).attr("src", "").hide();
        });
        eButton.parent().find('.zf-preview-gif-mp4').hide();
        eButton.parent().find('.zf-preview-gif-mp4').find('source').attr("src", "");
        eButton.parent().find('input[type="file"]').val('');
        eButton.parent().removeClass('zf-uploader-uploaded');
        eButton.parent().find('.zf-video-player').hide();
        eButton.parent().find('.zf-image_url').val("")
        eButton.parent().find('input[data-zf-media-id]').val("");
        eButton.parent().find('input[data-zf-media-id]').attr("data-zf-media-id","");
    };

    this.virtualSave = function(){

        if( this.saving_post || zombify_virtual_unsaved_form == false || ( jQuery(".zombify_quiz_type").length == 0 || jQuery(".zombify_quiz_type").val() == 'meme' ) ) return false;

        this.saving_post = true;
        this.saving_virtual_post = true;

        if( jQuery("#meme_settings").length > 0 ){

            if( !createImageDataPressed() ) {

                this.saving_post = false;

                if( jQuery('.zf-meme').find('.zf-start').find('.zf-help').length !== 0 ) {

                    jQuery('.zf-meme').find('.zf-start').find('.zf-help').remove();

                }

                jQuery('.zf-meme').find('.zf-start').append('<span class="zf-help">The field is required</span>');

                return false;

            }

            createImageDataPressed();

            jQuery("#meme_settings").val( encodeObj(zombify_settings) );


        }

        var erbsaveclone = jQuery(".zf-erase-before-save").html();
        jQuery(".zf-erase-before-save").html('');

       if(isSafari11){
           var $form = jQuery('#zombify-form');
           var $inputs = jQuery('input[type="file"]:not([disabled])', $form);
           $inputs.each(function(_, input) {
               if (input.files.length > 0) return;
               jQuery(input).prop('disabled', true);
           });
       }

        var formData = new FormData(jQuery('#zombify-form')[0]);

        if(isSafari11){
            $inputs.prop('disabled', false);
        }


        jQuery(".zf-erase-before-save").html(erbsaveclone);

        formData.append("action", "zombify_virtual_save");

        url_post_id = parseInt( jQuery(".zombify_post_id").val() );

        zombify_virtual_unsaved_form = false;

        var zf_ajax_call_url = zf.ajaxurl+'?action=virtual_save'+( url_post_id ? '&post_id='+url_post_id : '' );

        if( typeof zombify_virtual_addit_data != 'undefined' ){

            var addit_params = jQuery.param(zombify_virtual_addit_data);

            if( addit_params != '' ){

                zf_ajax_call_url += '&'+addit_params;

            }

            zombify_virtual_addit_data = {};

        }

        var browsedFileInputs = jQuery("input[data-zf-file-browsed='1']");
        var changedUrlInputs = jQuery("input[data-zf-url-changed='1']");
        jQuery.ajax({
            xhr: function() {
                var xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener("progress", function(evt) {
                    if (evt.lengthComputable) {
                        var percentComplete = evt.loaded / evt.total;

                        percentComplete = parseInt(percentComplete * 100);

                        zombify_submit_process(percentComplete);
                    }
                }, false);
                
                return xhr;
            },
            url: zf_ajax_call_url,
            type: 'POST',
            data: formData,
            async: true,
            dataType: 'json',
            success: function (data) {
                zombify_virtual_save_interval = zombify_virtual_save_interval_default;

                if( typeof data.result != 'undefined' ) {

                    if (data.result == 1) {

                        var appendAttachedFiles = function( data, parentClass, attributeName ){

                            return function(index, element){

                                var field_path = jQuery(element).attr("name");

                                if( !field_path ) return false;

                                field_path = field_path.replace("file_url][", "");

                                field_path = field_path.replace("[]", "");

                                field_path = field_path.substr(8, field_path.length-9);

                                var field_path_arr = field_path.split("][");

                                var fielddata = data.data;

                                var dataexist = true;

                                field_path_arr = Object.keys(field_path_arr).map(function(key) {
                                    return field_path_arr[key];
                                });



                                for( var findex=0; findex<field_path_arr.length; findex++ ){

                                    if( typeof fielddata[ field_path_arr[findex] ] == 'undefined' ){

                                        dataexist = false;

                                        break;

                                    }

                                    fielddata = fielddata[ field_path_arr[findex] ];

                                }



                                if( dataexist ){

                                    var field_name = jQuery(element).attr("name");
                                    field_name = field_name.replace("[]", "");
                                    field_name = field_name.substr(7);
                                    field_name = field_name.replace("[file_url]", "");

                                    filedatahtml = '';

                                    fielddata = Object.keys(fielddata).map(function(key) {
                                        return fielddata[key];
                                    });

                                    for( var i = 0; i<fielddata.length; i++ ){

                                        filedatahtml += '<div class="zombify_uploaded_image_item">';
                                        filedatahtml += '<input type="hidden" name="zombify_existing_data'+field_name+'['+i+'][attachment_id]" data-zf-media-id="'+fielddata[i]["attachment_id"]+'" value="'+fielddata[i]["attachment_id"]+'">';
                                        filedatahtml += '<input type="hidden" name="zombify_existing_data'+field_name+'['+i+'][existingfile]" value="1">';
                                        filedatahtml += '</div>';

                                    }

                                    if( jQuery(element).closest(parentClass).find(".zombify_uploaded_image_data").attr("zf-changed-uploaded-items") == 1 ){

                                        jQuery(element).closest(parentClass).find(".zombify_uploaded_image_data").append( filedatahtml );

                                    } else {

                                        jQuery(element).closest(parentClass).find(".zombify_uploaded_image_data").html( filedatahtml );

                                    }

                                    jQuery(element).closest(parentClass).find(".zombify_uploaded_image_data").attr("zf-changed-uploaded-items", "1");

                                }

                                jQuery(element).val("");
                                jQuery(element).attr(attributeName, "0");
                            };

                        };

                        browsedFileInputs.each( appendAttachedFiles(data, ".zf-form-group", "data-zf-file-browsed") );
                        changedUrlInputs.each( appendAttachedFiles(data, ".zf-uploader", "data-zf-url-changed") );

                        jQuery(".zombify_uploaded_image_data").attr("zf-changed-uploaded-items", "0");

                    } else {

                        for( var i = 0; i < data.errors.length; i++ ){

                            if( typeof data.errors[i].uploaded != 'undefined' ) {
                                if (typeof data.errors[i].uploaded.original != 'undefined') {

                                    jQuery(".zf-image_url").each(function () {

                                        if (jQuery(this).val() == data.errors[i].uploaded.original) {

                                            jQuery(this).val("");

                                            if (jQuery(this).closest(".zf-uploader").find(".zf-remove-media").closest(".zf-form-group").find(".zf-help").length > 0) {

                                                jQuery(this).closest(".zf-uploader").find(".zf-remove-media").closest(".zf-form-group").find(".zf-help").html(data.errors[i].error.errorMessage);

                                            } else {

                                                jQuery(this).closest(".zf-uploader").find(".zf-remove-media").closest(".zf-form-group").append('<div class="zf-help">' + data.errors[i].error.errorMessage + '</div>');

                                            }

                                            ZombifyBuilder.deleteMedia(jQuery(this).closest(".zf-uploader").find(".zf-remove-media"));

                                            ZombifyBuilder.updateShowDependency();

                                            jQuery(this).closest(".zf-uploader").find(".zf-remove-media").closest(".zf-form-group").find(".zombify_uploaded_image_item").remove();

                                        }

                                    });

                                }
                            }

                        }

                    }

                }

                ZombifyBuilder.saving_post = false;
                ZombifyBuilder.saving_virtual_post = false;

                if( ZombifyBuilder.trigger_post_save ){

                    ZombifyBuilder.trigger_post_save = false;

                    ZombifyBuilder.save( ZombifyBuilder.trigger_post_save_data.eButton, ZombifyBuilder.trigger_post_save_data.preview, ZombifyBuilder.trigger_post_save_data.publish );

                }

                browsedFileInputs.each(function(){
                    jQuery(this).closest(".zf-uploader").find(".zf_remove_progress").hide();
                    jQuery(this).closest(".zf-uploader").find(".zf_remove_progress").removeClass("zf_remove_progress");
                });

                changedUrlInputs.each(function(){
                    jQuery(this).closest(".zf-uploader").find(".zf_remove_progress").hide();
                    jQuery(this).closest(".zf-uploader").find(".zf_remove_progress").removeClass("zf_remove_progress");
                });

            },
            error: function(data){

                toggleDisabledButton('remove');
                //alert(zf.translatable['error_saving_post'] + ': ' + data.status+' '+data.statusText+', '+data.responseText);
                ZombifyBuilder.saving_post = false;
                //zombify_virtual_unsaved_form = true;

                ZombifyBuilder.saving_virtual_post = false;

                //zombify_virtual_save_interval = ( zombify_virtual_save_interval*4 <= zombify_virtual_save_interval_max ) ? zombify_virtual_save_interval*4 : zombify_virtual_save_interval;

                if( ZombifyBuilder.trigger_post_save ){

                    ZombifyBuilder.trigger_post_save = false;

                    ZombifyBuilder.save( ZombifyBuilder.trigger_post_save_data.eButton, ZombifyBuilder.trigger_post_save_data.preview, ZombifyBuilder.trigger_post_save_data.publish );

                }

                browsedFileInputs.each(function(){
                    jQuery(this).closest(".zf-uploader").find(".zf_remove_progress").hide();
                    jQuery(this).closest(".zf-uploader").find(".zf_remove_progress").removeClass("zf_remove_progress");
                });
            },
            cache: false,
            contentType: false,
            processData: false
        });

    }

    // Save post
    this.save = function( eButton, preview, publish ){

        if( this.saving_post ){

            if( this.saving_virtual_post ){

                eButton.addClass('zf-loading');

                this.trigger_post_save = true;
                this.trigger_post_save_data = {
                    eButton: eButton,
                    preview: preview,
                    publish: publish
                };

            }

            return false;

        }

        this.saving_post = true;

        if( typeof preview == 'undefined' ) preview = false;
        if( typeof publish == 'undefined' ) publish = false;


        jQuery(".zombify_save").parent().find(".zf-button-cont.zf-saved").remove();

        eButton.addClass('zf-loading');

        toggleDisabledButton('add');

        if( jQuery("#meme_settings").length > 0 ){

            if( !createImageDataPressed() ) {

                eButton.removeClass('zf-loading');

                toggleDisabledButton('remove');

                this.saving_post = false;

                if( jQuery('.zf-meme').find('.zf-start').find('.zf-help').length !== 0 ) {

                    jQuery('.zf-meme').find('.zf-start').find('.zf-help').remove();

                }

                jQuery('.zf-meme').find('.zf-start').append('<span class="zf-help">The field is required</span>');

                return false;

            }

            createImageDataPressed();

            jQuery("#meme_settings").val( encodeObj(zombify_settings) );


        }

        jQuery(".zf-erase-before-save").remove();

        if(isSafari11){
            var $form = jQuery('#zombify-form');
            var $inputs = jQuery('input[type="file"]:not([disabled])', $form);
            $inputs.each(function(_, input) {
                if (input.files.length > 0) return;
                jQuery(input).prop('disabled', true);
            });
        }

        var formData = new FormData(jQuery('#zombify-form')[0]);

        if(isSafari11){
            $inputs.prop('disabled', false);
        }

        formData.append("action", "zombify_save");

        if( publish )
            formData.append("zombify_publish_post", "1");

        url_post_id = parseInt( eButton.closest("form").find(".zombify_post_id").val() );

        jQuery.ajax({
            xhr: function() {
                var xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener("progress", function(evt) {
                    if (evt.lengthComputable) {
                        var percentComplete = evt.loaded / evt.total;
                        percentComplete = parseInt(percentComplete * 100);

                         zombify_submit_process(percentComplete == 100 ? zf.translatable["processing_files"] : zf.translatable["uploading_files"]+": "+percentComplete+"%");
                    }
                }, false);

                return xhr;
            },
            url: zf.ajaxurl+'?action=save'+( url_post_id ? '&post_id='+url_post_id : '' ),
            type: 'POST',
            data: formData,
            async: true,
            dataType: 'json',
            success: function (data) {

                eButton.removeClass('zf-loading');

                toggleDisabledButton('remove');

                if( typeof data.result != 'undefined' ) {

                    if (data.result == 0 || ( data.result == 1 && !publish )) {

                        jQuery(".zombify_quiz").replaceWith(data.output);

                    }

                    jQuery('.zf-result-wysiwyg-light.active').froalaEditor(zf_wysiwyg_config_light);

                    zf_video_upload_func();

                    zfContainerWidth();

                    if(jQuery('#tag-editor').length){

                        jQuery('#tag-editor').tagEditor('destroy');

                        jQuery('#tag-editor').tagEditor({
                            placeholder: jQuery('#tag-editor').attr("placeholder"),
                            maxTags:jQuery('#tag-editor').attr("data-count-limit"),
                            autocomplete: {
                                delay: 0,
                                position: {collision: 'flip'}, // automatic menu position up/down
                                source: zf.ajaxurl + '?action=zombify_get_tags'
                            },
                            forceLowercase: false
                        });

                    }

                    jQuery('.zf-wysiwyg-advanced').each(function(){
                        if( jQuery(this).parents('.zf-erase-before-save').length === 0 ) {
                            jQuery(this).froalaEditor(zf_wysiwyg_config_advanced);
                        }
                    });

                    jQuery('.zf-wysiwyg-light').each(function(){
                        if( jQuery(this).parents('.zf-erase-before-save').length === 0 ) {
                            jQuery(this).froalaEditor(zf_wysiwyg_config_light);
                        }
                    });

                    if (data.result == 1) {

                        jQuery(".zf-button-cont.zf-draft").hide();

                        zombify_unsaved_form = false;

                        jQuery('.zf-errors-btn').hide();

                        if( publish ){

                           window.location = data.post_url;

                        } else {

                            jQuery(".zombify_preview").attr("href", data.post_url).removeClass('zf-disabled');

                            ZombifyBuilder.saving_post = false;

                            if( jQuery(".zombify_save").parent().find(".zf-button-cont.zf-saved").length == 0 )
                                jQuery(".zombify_save").after('<div class="zf-float-left zf-button-cont zf-saved"><i class="zf-icon zf-icon-check"></i>'+zf.translatable['saved']+'</div>');

                        }

                        if( jQuery('.meme-template').val() !== '' ) {

                            jQuery("#zf-meme-img").attr('src', jQuery('.meme-template').val());

                        }

                        initMeme(jQuery);

                        eButton.closest("form").find(".zombify_post_id").val( data.post_id );

                    } else {

                        if( jQuery('.zf-error').length > 0 ) {

                            jQuery('.zf-error').find(".zf-uploader-uploaded").removeClass('zf-uploader-uploaded');
                            jQuery('.zf-errors-btn').find(".zf-custom-error").remove();
                            jQuery('.zf-errors-btn').find(".zf-errors-count").show();
                            jQuery('.zf-errors-btn').show();

                            jQuery('.zf-errors-btn').find('.zf-errors-count').html( jQuery('.zf-error').length );

                            if( jQuery('.zf-error').length === 1 ) {

                                jQuery('.zf-errors-btn').find('.zf-single-error-case').show();

                                jQuery('.zf-errors-btn').find('.zf-many-errors-case').hide();

                            } else {

                                jQuery('.zf-errors-btn').find('.zf-single-error-case').hide();

                                jQuery('.zf-errors-btn').find('.zf-many-errors-case').show();

                            }

                        }

                        if( jQuery('.meme-template').val() !== '' ) {

                            jQuery("#zf-meme-img").attr('src', jQuery('.meme-template').val());

                        }

                        initMeme(jQuery);

                        jQuery("#zombify-main-section .zf-error:first")[0].scrollIntoView(true);

                        ZombifyBuilder.saving_post = false;
                    }

                    ZombifyBuilder.correctBuilder();
                    ZombifyBuilder.updateShowDependency();
                    ZombifyBuilder.updateDependencies(true);
                    jQuery('.zf-multiple-select').zombifyMultiSelect();

                } else {

                    ZombifyBuilder.saving_post = false;

                }

                // Trigger 'zfAjaxContentLoaded' if ajax content loaded
                //jQuery('body').trigger( 'zfAjaxContentLoaded', [ data ] );



                // if( jQuery('.zf-video-player-preview').length > 0 ) {
                //
                //     var video = jQuery('.zf-video-player-show-on-draft');
                //
                //
                //     if( jQuery('div.zf-video-player-show-on-draft').length === 0 && video.prop('src') !== undefined ) {
                //
                //         window.ZFMediaElementPlayer = new MediaElementPlayer( video[0], {
                //             //stretching: 'auto'
                //         } );
                //
                //     }
                //
                // }

                jQuery(".zf_remove_progress").hide();
                jQuery(".zf_remove_progress").removeClass("zf_remove_progress");


            },
            error: function(data){
console.log('error');
                if( IsJsonString( data.responseText ) ) {

                    var errorObj = jQuery.parseJSON(data.responseText);

                    if (errorObj && typeof errorObj.message != 'undefined' && errorObj.message != '') {
                        errorMessage = errorObj.message;
                    } else {
                        errorMessage = zf.translatable['unknown_error'];
                    }

                } else {

                    errorMessage = zf.translatable['unknown_error'];

                }

                if( jQuery(".zf-errors-btn").find(".zf-custom-error").length == 0 ){
                    jQuery(".zf-errors-btn").append('<span class="zf-custom-error">'+errorMessage+'</span>')
                } else {
                    jQuery(".zf-errors-btn").find(".zf-custom-error").html( errorMessage );
                }

                jQuery(".zf-errors-btn").find(".zf-custom-error").show();
                jQuery(".zf-errors-btn").find(".zf-single-error-case").hide();
                jQuery(".zf-errors-btn").find(".zf-many-errors-case").hide();
                jQuery(".zf-errors-btn").find(".zf-errors-count").hide();
                jQuery(".zf-errors-btn").show();

                //alert(zf.translatable['error_saving_post'] + ': ' + data.status+' '+data.statusText+', '+data.responseText);
                ZombifyBuilder.saving_post = false;
                eButton.removeClass('zf-loading');

                toggleDisabledButton('remove');

                jQuery(".zf-image-preview-block").hide();

            },
            cache: false,
            contentType: false,
            processData: false
        });

    };

    // Sorting
    this.sort = function( group, direction ){

        switch( direction ){
            case "down":
                var replace_group = group.next();

                group.insertAfter(replace_group);
                break;

            case "up":
                var replace_group = group.prev();
                group.insertBefore(replace_group);
                break;

            default:
                console.log("Incorrect direction");
                return false;
                break;
        }
        group[0].scrollIntoView(true);

        // Find the parent container
        if( group.parent().closest(".zombify_group").length > 0 ){

            var parentGroup = group.parent().closest(".zombify_group");

        } else {

            var parentGroup = group.parent().closest(".zombify_quiz");

        }

        var  reverse =  parentGroup.find(".zf-"+group.attr("data-zombify-group-name")+"-container").data("reverse");

        // Create regular expression
        var regExp = new RegExp( "\\["+group.attr("data-zombify-group-name")+"\\]\\[\\d+\\]" );

        var elements = parentGroup.find(".zf-"+group.attr("data-zombify-group-name")+"_container").find(".zombify_group[data-zombify-group-name='"+group.attr("data-zombify-group-name")+"']");

        elements.each(function(index) {

            jQuery(this).find(".zf-index").html( reverse ? elements.length - index  : index + 1 );

        });

        this.updateDependencies(false);

    };

    // Change media type radio button
    this.changeMediaType = function( eButton ){

	    jQuery( eButton ).prop( "checked", true );
	    var selected_media_type = jQuery( eButton ).val();

        switch( selected_media_type ){

            case "embed":

                eButton.closest(".zf-form-group").find(".zombify_medatype_image").find(":input").prop("disabled", true);
                eButton.closest(".zf-form-group").find(".zombify_medatype_embed").find(":input").prop("disabled", false);

                break;

            case "image":

                eButton.closest(".zf-form-group").find(".zombify_medatype_image").find(":input").prop("disabled", false);
                eButton.closest(".zf-form-group").find(".zombify_medatype_embed").find(":input").prop("disabled", true);

                break;

            default:
                console.log("Unknown media type");
                break;

        }

    };

    this.correctBuilder = function(){

        if( jQuery('textarea[name="zombify[preface_description]"]').length > 0 ) {

            var preface_description = jQuery('textarea[name="zombify[preface_description]"]').val();

            jQuery('textarea[name="zombify[preface_description]"]').val( preface_description );

        }

        jQuery(".zf-form-group_media").each(function(){

            var selected_media_type = jQuery(this).find(".zombify_medatype_radio:checked").val();

            jQuery(this).find(".zf-media-uploader").attr("data-format", selected_media_type);

        });

        jQuery('.js-zf-answer-format input:checked').each(function(){

            var format = jQuery(this).data('format');
            jQuery(this).parent().parent().siblings('.zf-answers-box').attr('data-format', format);

        });

    };

    this.updateDependencies = function(firstTime){

        jQuery("select[data-zombify-dependency]").each(function(){

            var dropdown = jQuery(this);
            var dropdown_selected_value = firstTime ? dropdown.attr("data-zf-seled-val") : dropdown.val();
            var dropdown_dependency = dropdown.attr("data-zombify-dependency");

            dropdown.find("option[value!='']").remove();

            jQuery(":input[data-zombify-field-path='"+dropdown_dependency+"']").each(function(){

                if( jQuery(this).val() )
                dropdown.append('<option value="'+jQuery(this).attr("data-zombify-name-index")+'" '+( jQuery(this).attr("data-zombify-name-index") == dropdown_selected_value ? 'selected' : '' )+'>'+jQuery(this).val()+'</option>');

            });

        });

        this.updateShowDependency();

    };

    this.updateShowDependency = function(){

        jQuery("input[data-zombify-show-dependency]").each(function(){

            var mainCont = jQuery(this).closest(".zombify_group").length > 0 ? jQuery(this).closest(".zombify_group") : ( jQuery(this).closest(".zf-main").length > 0 ? jQuery(this).closest(".zf-main") : jQuery(this).closest(".zombify-main-section") );

            var show_dependency = jQuery(this).attr("data-zombify-show-dependency");
            var show_dependency_arr = show_dependency.split(",");

            var show = 0;

            for( ii=0; ii<show_dependency_arr.length; ii++ ){

                if(
                    (
                    mainCont.find("input[data-zombify-field-path='"+show_dependency_arr[ii]+"']").attr("type") == 'file' &&
                    mainCont.find("input[data-zombify-field-path='"+show_dependency_arr[ii]+"']").val() == '' &&
                    mainCont.find("input[data-zombify-field-path='"+show_dependency_arr[ii]+"']").closest(".zf-uploader").find(".zombify_uploaded_image_data").find(".zombify_uploaded_image_item").length == 0
                    )
                    ||
                    (
                    mainCont.find("input[data-zombify-field-path='"+show_dependency_arr[ii]+"']").attr("type") == 'checkbox' &&
                    mainCont.find("input[data-zombify-field-path='"+show_dependency_arr[ii]+"']").prop("checked") == false
                    )
                    ||
                    (
                    mainCont.find("input[data-zombify-field-path='"+show_dependency_arr[ii]+"']").length > 0 &&
                    mainCont.find("input[data-zombify-field-path='"+show_dependency_arr[ii]+"']").attr("type") == 'url' &&
                    mainCont.find("input[data-zombify-field-path='"+show_dependency_arr[ii]+"']").val() == ''
                    )
                    ||
                    (
                    mainCont.find("input[data-zombify-field-path='"+show_dependency_arr[ii]+"']").length > 0 &&
                    mainCont.find("input[data-zombify-field-path='"+show_dependency_arr[ii]+"']").attr("type") == 'text' &&
                    mainCont.find("input[data-zombify-field-path='"+show_dependency_arr[ii]+"']").val() == ''
                    )
                    ||
                    (
                    mainCont.find("input[data-zombify-field-path='"+show_dependency_arr[ii]+"']").length > 0 &&
                    mainCont.find("input[data-zombify-field-path='"+show_dependency_arr[ii]+"']").attr("type") == 'hidden' &&
                    mainCont.find("input[data-zombify-field-path='"+show_dependency_arr[ii]+"']").val() == ''
                    )
                    ||
                    (
                    mainCont.find("textarea[data-zombify-field-path='"+show_dependency_arr[ii]+"']").length > 0 &&
                    mainCont.find("textarea[data-zombify-field-path='"+show_dependency_arr[ii]+"']").val() == ''
                    )
                ){

                } else {

                    show = 1;

                }


            }

            if(
                show == 0
            ){

                jQuery(this).css("visibility", "hidden");
                if( jQuery(this).attr("type") == 'text' ) jQuery(this).val("");
                if( jQuery(this).attr("type") == 'checkbox' ) jQuery(this).prop("checked", false);
                if( jQuery(this).parent().hasClass("zf-form-group") ) jQuery(this).parent().hide();
                if( jQuery(this).parent().parent().hasClass("zf-form-group") ) jQuery(this).parent().parent().hide();
                if( jQuery(this).parent().parent().parent().hasClass("zf-form-group") && !jQuery(this).parent().parent().parent().hasClass("zf-dependency-show") ) jQuery(this).parent().parent().parent().hide();

            } else {

                jQuery(this).css("visibility", "visible");
                if( jQuery(this).parent().hasClass("zf-form-group") ) jQuery(this).parent().show();
                if( jQuery(this).parent().parent().hasClass("zf-form-group") ) jQuery(this).parent().parent().show();
                if( jQuery(this).parent().parent().parent().hasClass("zf-form-group") && !jQuery(this).parent().parent().parent().hasClass("zf-dependency-show") ) jQuery(this).parent().parent().parent().show();

            }

        });

    };

    this.validateFields = function(){

        jQuery(".zombify_quiz input[type='file']").each(function(){

            ZombifyBuilder.validateField( jQuery(this) );

        });

    };

    this.validateField = function(fieldObj){

        var fieldErrors = new Array();

        if( jQuery(fieldObj).attr("zf-validation-extensions") ) {

            errMsg = zf.translatable['invalid_file_extension']+' ' + jQuery(fieldObj).attr("zf-validation-extensions");

            var exts = jQuery(fieldObj).attr("zf-validation-extensions");
            var valid_extensions = exts.split(",");

            for (j = 0; j < valid_extensions.length; j++) {
                valid_extensions[j] = valid_extensions[j].trim();
            }

            filesList = jQuery(fieldObj)[0].files;

            for (i = 0; i < filesList.length; i++) {

                var file_ext = filesList[i]["name"].substr(filesList[i]["name"].lastIndexOf('.') + 1);
                file_ext = file_ext.toLowerCase();

                if (!ZombifyBuilder.inArray(file_ext, valid_extensions)) {

                    fieldErrors[ fieldErrors.length ] = errMsg;

                }

            }

        }

        if( jQuery(fieldObj).attr("zf-validation-maxSize") ) {

            errMsg = zf.translatable['invalid_file_size']+' '+Math.round( parseInt( jQuery(fieldObj).attr("zf-validation-maxSize") ) / 1024 ) + 'MB';

            var maxsize = parseInt( jQuery(fieldObj).attr("zf-validation-maxSize") ) * 1024;

            filesList = jQuery(fieldObj)[0].files;

            for( i = 0; i<filesList.length; i++ ){

                if( filesList[i]["size"] > maxsize ){

                    fieldErrors[ fieldErrors.length ] = errMsg;

                }

            }

        }

        if( fieldErrors.length > 0 ){

            if (jQuery(fieldObj).closest(".zf-form-group").find(".zf-help").length > 0) {

                jQuery(fieldObj).closest(".zf-form-group").find(".zf-help").html(fieldErrors.join('<br>'));

            } else {

                jQuery(fieldObj).closest(".zf-form-group").append('<div class="zf-help">' + fieldErrors.join('<br>') + '</div>');

            }

            return false;

        } else {

            jQuery(fieldObj).closest(".zf-form-group").find(".zf-help").remove();

            return true;

        }

    };

    this.deleteMediaAttachment = function(attach_id){
        toggleDisabledButton('add');

        jQuery.ajax({
            url: zf.ajaxurl + '?action=zombify_delete_media',
            type: 'POST',
            data: {attach_id: attach_id, action: 'zombify_delete_media'},
            dataType: 'json',
            success: function (data) {
                toggleDisabledButton('remove');
            }
        });

    };

    this.inArray = function(needle, haystack) {
        var length = haystack.length;
        for(var i = 0; i < length; i++) {
            if(haystack[i] == needle) return true;
        }
        return false;
    }

};