(function ($) {
    'use strict';
    /* Custom Meta Box Date*/


    $(document).on('ptb_add_metabox_event_date', function (e) {
        return {
            'showrange': true,
        };
    });
    $(document).on('ptb_metabox_create_event_date', function (e) {
        if (e.options.showrange) {
            e.container.find('input[name="' + e.id + '_showrange"]').prop('checked', true);
        }
    });
    $(document).on('ptb_metabox_save_event_date', function (e) {
        e.options.showrange = $('input[name="' + e.id + '_showrange"]:checked').val();
    });

    $(document).on('ptb_post_cmb_event_date_body_handle', function (e) {

        var $self = $('#' + e.id + '_start').length > 0 ? $('#' + e.id + '_start') : $('#ptb_extra_' + e.id);
        var $defaultargs = {
            showOn: 'both',
            showButtonPanel: true,
            showHour: 1,
            showMinute: 1,
            timeOnly: false,
            showTimepicker: 1,
            buttonText: false,
            dateFormat: 'yy-mm-dd',
            timeFormat: 'hh:mm tt',
            stepMinute: 5,
            separator: '@',
            minInterval: 0,
            beforeShow: function () {
                $('#ui-datepicker-div').addClass('ptb_extra_datepicker');
            }
        };
        if ($('#' + e.id + '_start').length == 0) {
            $self.datetimepicker($defaultargs);
        }
        else {

            var startDateTextBox = $('#' + e.id + '_start'),
                    endDateTextBox = $('#' + e.id + '_end');
            $.timepicker.datetimeRange(
                    startDateTextBox,
                    endDateTextBox,
                    $defaultargs
                    );
        }

    });

}(jQuery));
 