(function ($) {
    'use strict';
    /* Custom Meta Box Gallery*/



    $(document).on('ptb_metabox_create_progress_bar', function (e) {
        var $option = e.container.find('.' + e.id + '_option_wrapper');
        var $optionWrapper = e.container.find('#' + e.id + '_options_wrapper');
        $('#' + e.id + '_' + e.options.orientation).prop('checked', true);
        $option.remove();
        // add options from settings
        $.each(e.options.options, function (index, option) {

            var $newOption = $option.clone();
            $newOption.appendTo($optionWrapper);

            var $options = $newOption.find('input[name^="' + e.id + '_options_"]');
            $options.each(function () {
                var $code = $(this).attr('name').replace(e.id + '_options_', '');
                $code = $code.replace('[]', '');
                var $val = option[$code] ? option[$code] : option.name,
                    $id = $.isNumeric(option.id)?option.id:option.id.replace(e.id+'_','');
                $(this).val($val)
                        .attr('id', e.id + '_' + $id)
                        .data('id', $id);
            });

            $newOption.find('.' + e.id + '_remove').click({option: $newOption}, removeOption);

        });

        $optionWrapper.sortable({
            placeholder: "ui-state-highlight"
        });

        // add new option
        e.container.find('#' + e.id + '_add_new')
                .click(
                        {
                            id: e.id,
                            option: $option,
                            wrapper: $optionWrapper
                        },
                function (e) {
                    e.preventDefault();
                    var $newOption = e.data.option.clone();

                    var nextId = getNextId(e);

                    $newOption.find('input[name^="' + e.data.id + '_options_"]')
                            .val('')
                            .attr('id', e.data.id + '_' + nextId)
                            .data('id', nextId);
                    $newOption.find('.' + e.data.id + '_remove').click({option: $newOption}, removeOption);
                    $newOption.appendTo(e.data.wrapper).hide().show('blind', 500);
                    e.data.wrapper.sortable("refresh");
                });

        // remove option
        function removeOption(e) {
            e.preventDefault();
            e.data.option.hide('blind', 500, function () {
                $(this).remove();
            });
        }

        function getNextId(e) {
            var set = $(e.data.option.selector).find('input[name^="' + e.data.id + '_options_"]').map(function () {
                return $(this).data('id');
            });
            set = set.filter(function (el) {
                return el > 0;
            });
            var maxId =  set.length === 0 ? 0 : Math.max.apply(null, set);

            return ++maxId;
        }
    });

    $(document).on('ptb_add_metabox_progress_bar', function (e) {
        return {
            'orientation': 'horizonal',
            options: [
                {
                    name: "Label 1",
                    id: 1,
                    selected: true
                },
                {
                    name: "Label 2",
                    id: 2,
                    selected: false
                }
            ]
        }
    });

    $(document).on('ptb_metabox_save_progress_bar', function (e) {
        e.options.orientation = $('input[name="' + e.id + '_orientation"]:checked').val();
        e.options.options = {};
        var $li = $('#' + e.id + '_options_wrapper').children('li');
        $li.each(function ($i) {
            var $options = $(this).find('input[name^=' + e.id + '_options_]');
            e.options.options[$i] = {};
            $options.each(function () {
                var $code = $(this).attr('name').replace(e.id + '_options_', '');
                $code = $code.replace('[]', '');
                e.options.options[$i][$code] = $(this).val();
                e.options.options[$i].id = $(this).attr('id');
            });

        });
    });
    $(document).ready(function () {
        $('#ptb_lightbox_container').delegate('div[data-type="progress_bar"] fieldset legend', 'click', function (e) {
            e.preventDefault();
            var $wrapper = $(this).next('.ptb_fields_wrapper');
            if ($wrapper.is(':visible')) {
                $wrapper.slideUp();
            }
            else {
                $wrapper.slideDown();
            }
        });
    });

}(jQuery));
 