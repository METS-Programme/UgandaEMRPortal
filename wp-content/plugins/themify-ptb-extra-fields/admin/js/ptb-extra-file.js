(function ($) {
    'use strict';
    /* Custom Meta Box Gallery*/


    $(document).on('ptb_add_metabox_file', function (e) {
        return {};
    });



    $(document).on('ptb_post_cmb_file_body_handle', function (e) {
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
                    $newOption.find('.ptb_post_cmb_image').removeAttr('class').addClass('ptb_post_cmb_image');
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



        $('.ptb_post_cmb_item_file').delegate('.' + e.id + '_option_wrapper .ptb_post_cmb_image', 'click', function (event) {
            event.preventDefault();
            var $item = $(this);
            var $parent = $item.closest('li');
            // Create the media frame.
            var ptb_cmb_image_file_frame = wp.media.frames.file_frame = wp.media({
                title: wp.media.view.l10n.editGalleryTitle,
                button: {
                    text: $(this).data('uploader_button_text')
                },
                library: {type: 'application'},
                multiple: false

            }).on('select', function (selection) {
                // We set multiple to false so only get one image from the uploader
                var attachment = ptb_cmb_image_file_frame.state().get('selection').first().toJSON();
                var $new = $('#auto_draft').length > 0;
                $parent.find('input[name^="' + e.id + '[url]"]').val(attachment.url);
                var $title = $parent.find('input[name^="' + e.id + '[title]"]');
                if ($new || !$.trim($title.val())) {
                    $title.val(attachment.title);
                }
                $item.addClass('ptb_uploaded ptb_extra_' + attachment.filename.split('.').pop());
            }).open();
            ;

        });
        $('.ptb_post_cmb_item_file').delegate('input[name^="' + e.id + '[url]"][type="text"]', 'paste keyup keypress change', function (event) {
            var $self = $(this);
            var $parent = $self.closest('li');
            setTimeout(function () {
                $parent.find('.ptb_post_cmb_image').css('background-image', 'url(' + $self.val() + ')');
            }, 100);
        });
    });

}(jQuery));
 