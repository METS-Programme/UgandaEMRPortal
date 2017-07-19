(function ($) {
    'use strict';
    /* Custom Meta Box Gallery*/


    $(document).on('ptb_add_metabox_gallery', function (e) {
        return {};
    });
   
    function selectImage(attachment,$id,$current){
         var $parent=$current?$current:$('#'+$id+'_options_wrapper li').last();
         $parent.find('input[name^="' + $id + '[url]"]').val(attachment.url);
         var $new = $('#auto_draft').length > 0,
             $item = $parent.find('.ptb_post_cmb_image');
         var $title = $parent.find('input[name^="' + $id + '[title]"]');
         if ($new || !$.trim($title.val())) {
             $title.val(attachment.title);
         }
         var $desc = $parent.find('textarea');
         if ($new || $.trim($desc.val())) {
             $desc.val(attachment.caption);
         }
        $item.css('background-image', 'url(' + attachment.url + ')');
     };
     
    $(document).on('ptb_post_cmb_gallery_body_handle', function (e) {
        var $optionsWrapper = e.cmbItemBody.find('.ptb_cmb_options_wrapper');

        if ($optionsWrapper.length == 0) {
            return false;
        }

        var $option = $optionsWrapper.children().first().clone();

        $optionsWrapper.sortable({
            placeholder: "ui-state-highlight"
        });

        e.cmbItemBody.find('.ptb_cmb_option_add')
                .click(
                        {
                            wrapper: $optionsWrapper
                        },
                function (event) {
                    var $newOption = $option.clone();
                    $newOption.appendTo($optionsWrapper).hide().show('blind', 500);
                    $newOption.find('[name^="' + e.id + '"]').val('');
                    $newOption.find('.ptb_post_cmb_image').removeAttr('style', '');
                    $newOption.find('.' + e.id + '_remove').click({item: $newOption}, removeOption);

                    event.data.wrapper.sortable("refresh");
                });

        $optionsWrapper.children().each(function () {
            var $self = $(this);
            $self.find('.' + e.id + '_remove').click({item: $self}, removeOption);
        });

        // remove option
        function removeOption(e) {
            e.preventDefault();
            e.data.item.hide('blind', 500, function () {
                $(this).remove();
            })
        }
        
     
        
        $('.ptb_post_cmb_item_gallery').delegate('.' + e.id + '_option_wrapper .ptb_post_cmb_image', 'click', function (event) {
            event.preventDefault();
            var $item = $(this);
            var $parent = $item.closest('li');
            // Create the media frame.
            wp.media.frames.file_frame = wp.media({
                title: wp.media.view.l10n.editGalleryTitle,
                button: {
                    text: $(this).data('uploader_button_text')
                },
                frame:      'post',
                state:      'gallery-edit',
                library: {type: 'image'},
                editing: true,
                multiple: true,
                selection: false

            }).on('update', function (selection) {
                for(var $i in selection.models){
                    var attachment = selection.models[$i].attributes;
                    if($i==0){
                         selectImage(attachment,e.id,$parent);
                    }
                    else if($i<=selection.models.length-1){
                         $('#'+e.id +'_add_new').trigger('click'); 
                         setTimeout(selectImage(attachment,e.id),600);
                     }
                }
            }).open();;
            
        });
        $('.ptb_post_cmb_item_gallery').delegate('input[name^="' + e.id + '[url]"][type="text"]', 'paste keyup keypress change', function (event) {
            var $self = $(this);
            var $parent = $self.closest('li');
            setTimeout(function () {
                $parent.find('.ptb_post_cmb_image').css('background-image', 'url(' + $self.val() + ')');
            }, 100);
        });
    });
    $(document).ready(function () {
        $('body').delegate('.ptb_extra_gallery_mode input[type="radio"]', 'change', function () {
            var $column = $(this).closest('.ptb_back_active_module_content').find('.ptb_extra_gallery_columns');
            if ($(this).val() == 'showcase') {
                $column.slideUp();
            }
            else {
                $column.slideDown();
            }
        });
        $(document).on('PTB.template_load', function () {
            $('.ptb_extra_gallery_mode input[type="radio"]:checked').trigger('change');
        });
    });

}(jQuery));
 