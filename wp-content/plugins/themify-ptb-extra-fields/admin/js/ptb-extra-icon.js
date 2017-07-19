(function ($) {
    'use strict';

    /* Custom Meta Box Icon*/

    $(document).on('ptb_metabox_create_icon', function (e) {

    });

    $(document).on('ptb_add_metabox_icon', function (e) {
        return {};
    });

    $(document).on('ptb_metabox_save_icon', function (e) {

    });
    $(document).on('ptb_post_cmb_icon_body_handle', function (e) {
        var $optionsWrapper = e.cmbItemBody.find('.ptb_cmb_options_wrapper');

        if ($optionsWrapper.length == 0) {
            return false;
        }

        var $option = $optionsWrapper.children().first().clone();

        $optionsWrapper.sortable({
            placeholder: "ui-state-highlight"
        });
        InitColor(e.cmbItemBody.find('.ptb_color_picker'), $optionsWrapper);
        e.cmbItemBody.find('.ptb_cmb_option_add')
                .click(
                        {
                            wrapper: $optionsWrapper
                        },
                function (event) {
                    var $newOption = $option.clone();
                    $newOption.appendTo($optionsWrapper).hide().show('blind', 500);

                    var $cl = $newOption.find('.ptb_extra_input_icon').val();
                    $newOption.find('[name^="' + e.id + '"]').val('');
                    $newOption.find('.ptb_post_cmb_image').removeClass('fa fa-' + $cl);
                    $newOption.find('.' + e.id + '_remove').click({item: $newOption}, removeOption);
                    InitColor(e.cmbItemBody.find('.ptb_color_picker'), $optionsWrapper);
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
    });

    $(document).delegate('.ptb-icons-list a', 'click', function (e) {
        e.preventDefault();
        var $cl = $.trim($(this).find('i').attr('class').replace('fa', ''));
        var $current = $('.ptb_post_cmb_item_icon .ptb_current_ajax');
        var $icon = $current.closest('li').find('.ptb_extra_input_icon');
        if ($icon.val()) {
            $current.removeClass('fa-' + $icon.val());
        }
        $icon.val($cl.replace('fa-', ''));
        $current.addClass('fa ' + $cl);
        $('.ptb_close_lightbox').trigger('click');
    });
    var InitColor = function (el, $optionsWrapper) {
        el.minicolors({
            opacity: false,
            show: function () {
                $optionsWrapper.sortable('disable');
            },
            hide: function () {
                $optionsWrapper.sortable('enable');
            },
            change: function (hex, opacity) {
                $(this).closest('.ptb_cmb_option').find('.ptb_custom_lightbox').css('color', hex);
            }
        });
    };
}(jQuery));

