(function ($) {
    'use strict';
    $(document).ready(function(){
        $(document).on('ptb_submission_validate_file',function(e,$this,filesList){
            var $audio = $($this).closest('.ptb_module').find('input[type="file"]');
            var $error = true;
            $audio.each(function(){
                if(filesList[$(this).attr('id')]){
                   $error = false;
                   return true; 
                }
            });
            if($error){
                $audio = $($this).closest('.ptb_module').find('.ptb-submission-priview input');
                if($audio.length>0){
                    $error = false;
                }
            }
            return $error;
        });
        var $up = $('.ptb_extra_submission_files .ptb-submission-priview input');
        $up.each(function(){
            var $label = $(this).closest('li').find('label.ptb-submission-upload-btn');
            var $v = $label.text();
            if($v.length>12){
                $v = $v.substr(0,12)+'...';
            }
             $label.html('<span>'+$v+'</span>');
        });
    });
    
}(jQuery));