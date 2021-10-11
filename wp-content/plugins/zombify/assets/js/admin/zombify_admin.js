jQuery(document).ready(function ($) {

    $('.zf-color-field').wpColorPicker();

    $('.zombify_logo_upload').click(function(e) {
        e.preventDefault();

        var custom_uploader = wp.media({
            title: 'Custom Image',
            button: {
                text: 'Upload Image'
            },
            multiple: false  // Set this to true to allow multiple files to be selected
        })
            .on('select', function() {
                var attachment = custom_uploader.state().get('selection').first().toJSON();
                $('.zombify_logo').attr('src', attachment.url);
                $('.zombify_logo_url').val(attachment.url);
                $('.zombify_logo').show();

            })
            .open();
    });

    $('.zombify_logo_url').on("change", function(){

        if( $(this).val() != '' ) {

            $('.zombify_logo').attr('src', $(this).val());
            $('.zombify_logo').show();

        } else {

            $('.zombify_logo').attr('src', $(this).val());
            $('.zombify_logo').hide();

        }

    });

    $(".zf_categories_dropdown").find("option").on("click", function(){

        var dropdownobj = $(this).parent();


        if( $(this).prop("selected") == true ){


                $(this).parent().find("option").each(function(){

                    if( $(this).prop("selected") == true )
                        zf_select_cat_dropdown_parents( dropdownobj, $(this).attr("data-parent-id") );

                });


        } else {

            zf_unselect_cat_dropdown_parents( dropdownobj, $(this).val() );

        }

    });

    $("#sortable").sortable({
        'update': function() {
            var types = {};
            var subtypes = {};
            var story_format = {};

            for( var i = 1; i <= $(this).find('.type_checkbox').length; i++ ) {

                var post_type_name = $($(this).find('.type_checkbox')[i-1]).val();

                types[post_type_name] = i;

            }

            for( var i = 1; i <= $(this).find('.subtype_checkbox').length; i++ ) {

                var post_type_name = $($(this).find('.subtype_checkbox')[i-1]).val();

                subtypes[post_type_name] = i;

            }

            for( var i = 1; i <= $(this).find('.story_format_checkbox').length; i++ ) {

                var post_type_name = $($(this).find('.story_format_checkbox')[i-1]).val();

                story_format[post_type_name] = i;

            }

            $('.zombify_types_order').val(JSON.stringify(types));
            $('.zombify_subtypes_order').val(JSON.stringify(subtypes));
            $('.zombify_story_format_order').val(JSON.stringify(story_format));
        }
    });

    $("#sortable").disableSelection();

});

function zf_select_cat_dropdown_parents( dropdownobj, parent_id ){

    if( parent_id == 0 ) return false;

    jQuery(dropdownobj).find("option[data-val='"+parent_id+"']").prop("selected", true);

    zf_select_cat_dropdown_parents( dropdownobj, jQuery(dropdownobj).find("option[data-val='"+parent_id+"']").attr("data-parent-id") )

}

function zf_unselect_cat_dropdown_parents(dropdownobj, val){

    jQuery(dropdownobj).find("option[data-parent-id='"+val+"']").each(function(){

        jQuery(this).prop("selected", false);

        zf_unselect_cat_dropdown_parents(dropdownobj, jQuery(this).val());

    });

}

