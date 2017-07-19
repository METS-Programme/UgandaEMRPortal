(function ($) {
    'use strict';
    
   $(document).ready(function(){
        var $dates = $('.ptb-submission-date-wrap input');
        var $defaultargs ={
                showOn: 'focus',
                showButtonPanel: true,  
                showHour:1,
                showMinute:1,
                timeOnly:false,
                showTimepicker:false,
                buttonText: false,
                dateFormat: 'yy-mm-dd',
                timeFormat: 'hh:mm tt',
                stepMinute: 5,
                separator: '@',
                minInterval:0,
                beforeShow: function() {
                    $('#ui-datepicker-div').addClass( 'ptb_extra_datepicker' );
                }
        }; 
        $dates.each(function(){
            var $key = $(this).data('id');
            if($key){
                var $endDateTextBox = $('#ptb_submission_'+$key+'_end');
                if($(this).data('time')){
                    $defaultargs.showTimepicker = 1;
                }
                if($endDateTextBox.length==0){
                    $(this).datetimepicker($defaultargs);
                }
                else {
                    $.timepicker.datetimeRange(
                        $(this),
                        $endDateTextBox,
                        $defaultargs
                    );
                } 
            }
            
        });
       
       $('.ptb-submission-date-wrap i').click(function(){
            $(this).prev('input').trigger('focus'); 
       });
       
   });
    
}(jQuery));