(function ($) {
    'use strict';
    var options = {
         type:'ajax',
         slideshow:false,
         closeOnOverlayClick:false,
         showSequenceInfo:false,
         useKeys:false,
         maxWidth:880
    }
    $('.ptb-submission-form').delegate('a.ptb_extra_submission_icon','click',function(e){
       e.preventDefault(); 
       if(e.isTrigger){
           return false;
       }
       $('.ptb-submission-form a.ptb_extra_submission_icon_c').removeClass('ptb_extra_submission_icon_c');
       $(this).addClass('ptb_extra_submission_icon_c');
       $(this).unbind('click.lightcase').bind('click.lightcase', function (event) {
                event.preventDefault();
                $(this).lightcase('start', options);
        }).trigger('click');
    });
     
    $(document).delegate('.ptb-icons-list a','click',function(e){
          e.preventDefault(); 
          var $cl = $(this).find('i').attr('class');
          var $current = $('.ptb-submission-form a.ptb_extra_submission_icon_c i');
          $current.prop('class',$cl).next('input').val($.trim($cl.replace('fa','').replace('fa-','')));
          $('.lightcase-icon-close').trigger('click');
    });
    $(document).on('ptb_submission_before_option_add',function(e,$option){
        $option = $($option)
        $option.find('a.ptb_extra_submission_icon i').prop('class','fa fa-plus-circle');
        var  $input_color = $option.find('.ptb_extra_color_picker');
        $input_color.prop('class','ptb_extra_color_picker');
        $option.find('div.minicolors').replaceWith($input_color);
        $option.find('.fa').removeAttr('style');
        PTB_Extra_Color($input_color);
    });
    var PTB_Extra_Color= function($color){
       $color.minicolors({
                opacity:false,
                position: 'top right',
                theme: 'default',
                show:function(){
                   $('.ptb-submission-multi-text>ul').sortable('disable');  
                },
                hide:function(){
                   $('.ptb-submission-multi-text>ul').sortable('enable');
                },
                change:function(hex){
                    $(this).closest('.ptb_icon_wrap').find('i.fa').css('color',hex);
                }
            });
    };
    PTB_Extra_Color( $('.ptb_extra_color_picker'));
    
}(jQuery));