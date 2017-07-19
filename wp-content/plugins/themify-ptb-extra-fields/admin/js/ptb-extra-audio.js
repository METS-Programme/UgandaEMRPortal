(function ($) {
    'use strict';
    /* Custom Meta Box Slider*/


    $(document).on('ptb_add_metabox_audio', function (e) {
        return {};
    });


    $(document).on('ptb_post_cmb_audio_body_handle', function (e) {
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
                    $newOption.find('.ptb_post_cmb_image').removeClass('ptb_uploaded');
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

        $('.ptb_post_cmb_item_audio').delegate('.' + e.id + '_option_wrapper .ptb_post_cmb_image', 'click', function (event) {
            event.preventDefault();
            var $item = $(this);
            var $parent = $item.closest('li');
            // Create the media frame.
            var ptb_cmb_image_file_frame = wp.media.frames.file_frame = wp.media({
                title: $(this).data('uploader_title'),
                button: {
                    text: $(this).data('uploader_button_text')
                },
                library: {type: 'audio'},
                multiple: false  // Set to true to allow multiple files to be selected
            }).on('select', function () {
                // We set multiple to false so only get one image from the uploader
                var attachment = ptb_cmb_image_file_frame.state().get('selection').first().toJSON();
                var $new = $('#auto_draft').length > 0;
                $parent.find('input[name^="' + e.id + '[url]"]').val(attachment.url);
                var $title = $parent.find('input[name^="' + e.id + '[title]"]');
                if ($new || !$.trim($title.val())) {
                    $title.val(attachment.title);
                }
                var $desc = $parent.find('textarea');
                if ($new || $.trim($desc.val())) {
                    $desc.val(attachment.caption);
                }
                $item.addClass('ptb_uploaded');
            }).open();
        });
    });

}(jQuery));
 