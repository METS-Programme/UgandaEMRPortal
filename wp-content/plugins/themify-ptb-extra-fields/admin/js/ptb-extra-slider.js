(function ($) {
    'use strict';
    /* Custom Meta Box Slider*/


    $(document).on('ptb_add_metabox_slider', function (e) {
        return {};
    });

    function selectImage(attachment, $id, $current) {
        attachment = attachment.toJSON();
        var $new = $('#auto_draft').length > 0,
                $parent = $current ? $current : $('#' + $id + '_options_wrapper li').last(),
                $item = $parent.find('.ptb_post_cmb_image');
        $parent.find('input[name^="' + $id + '[url]"]').val(attachment.url);
        var $title = $parent.find('input[name^="' + $id + '[title]"]');
        if ($new || !$.trim($title.val())) {
            $title.val(attachment.title);
        }
        if (attachment.type == 'image') {
            $item.css('background-image', 'url(' + attachment.url + ')');
        }
        else if (attachment.type == 'video' || attachment.type == 'audio') {
            $item.addClass('ptb_uploaded')
        }
    }
    ;

    $(document).on('ptb_post_cmb_slider_body_handle', function (e) {
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

        $('.ptb_post_cmb_item_slider').delegate('.' + e.id + '_option_wrapper .ptb_post_cmb_image', 'click', function (event) {
            event.preventDefault();
            var $item = $(this);
            var $parent = $item.closest('li');
            // Create the media frame.
            var ptb_cmb_image_file_frame = wp.media.frames.file_frame = wp.media({
                title: $(this).data('uploader_title'),
                button: {
                    text: $(this).data('uploader_button_text')
                },
                multiple: true
            })
                    .on('select', function () {
                        var attachments = ptb_cmb_image_file_frame.state().get('selection');
                        attachments.each(function (attachment, $i) {
                            if ($i == 0) {
                                selectImage(attachment, e.id, $parent);
                            }
                            else if ($i <= attachments.length - 1) {
                                $('#' + e.id + '_add_new').trigger('click');
                                setTimeout(selectImage(attachment, e.id), 600);
                            }
                        });
                    }).open();
        });
        $('.ptb_post_cmb_item_slider').delegate('input[name^="' + e.id + '[url]"][type="text"]', 'paste keyup keypress change', function (event) {
            var $self = $(this);
            var $parent = $self.closest('li');
            setTimeout(function () {
                $parent.find('.ptb_post_cmb_image').css('background-image', 'url(' + $self.val() + ')');
            }, 100);
        });
    });

}(jQuery));
 