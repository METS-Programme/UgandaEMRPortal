(function ($) {
    'use strict';
    
    $('.ptb-submission-form .ptb_extra_rating span').on('click',function(e){
        e.preventDefault();
        var $self = $(this).closest('.ptb_extra_rating');
        var $spans =  $self.children('span').get().reverse();
        var $value = $spans.length-$(this).index();
        $($spans).each(function($i){
            if($value>$i){
                $(this).addClass('ptb_extra_voted');
            }
        });
        $self.addClass('ptb_extra_readonly_rating');
        var $module = $self.closest('.ptb_rating');
        $module.find('.ptb-submission-rate-cancel').show();
        $module.find('input[type="hidden"]').val($value);
    });
    
    $('.ptb-submission-form .ptb-submission-rate-cancel').on('click',function(e){
        e.preventDefault();
        var $module = $(this).closest('.ptb_rating');
        $module.find('.ptb_extra_rating').removeClass('ptb_extra_readonly_rating');
        $module.find('input[type="hidden"]').val('');
        $module.find('.ptb_extra_voted').removeClass('ptb_extra_voted');
        $(this).hide();
    });
    
}(jQuery));