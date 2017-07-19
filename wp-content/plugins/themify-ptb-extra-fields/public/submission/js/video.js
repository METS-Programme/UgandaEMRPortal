(function ($) {
    'use strict';
    
    function parseVideoURL(url) {
        var matches;
        var $res = {};
        if ( url.match('http(s)?://(www.)?youtube|youtu\.be') ) {
            var $id = url.match('embed')?url.split(/embed\//)[1].split('"')[0]:url.split(/v\/|v=|youtu\.be\//)[1].split(/[?&]/)[0]; 
            $res.host = 'youtube';
            $res.url = '//img.youtube.com/vi/'+$id+'/default.jpg';
        } 
        else if (matches = url.match(/vimeo.com\/.*?\/?(\d+)/i)) {
            $res.host = 'vimeo';
            $res.url = 'http://www.vimeo.com/api/v2/video/' + matches[1];
         
        }
        return $res.host?$res:false;
    }
    $(document).ready(function(){
        
        $('.ptb-submission-form').delegate('.ptb_extra_video_url','focusout',function(){
            var $val = $.trim($(this).val());
            var $preiview = $(this).closest('li').find('.ptb-submission-priview');
            $preiview.next('span').remove();
            $(this).removeClass('ptb-submission-required-error');
            if($val){
                if($preiview.find('img').data('url')!=$val){
                    var $data = parseVideoURL($val);;
                    if($data){
                        if($data.host=='vimeo'){
                            var $this = $(this);
                            $.getJSON($data.url + '.json?callback=?', {format: "json"}, function(resp) {
                                if(resp){
                                    $preiview.hide('blind',300,function(){
                                         $(this).html('<img data-url="'+$val+'" width="100" height="75" src="'+resp[0].thumbnail_small+'" />');
                                         $(this).show('blind',300);
                                    });
                                }
                                else{
                                    $preiview.html('').hide().after('<span class="ptb-submission-file-danger ptb-submission-error-text">'+ptb_submission_video.video_error+'</span>');
                                    $this.addClass('ptb-submission-required-error');
                                }
                            });
                        }
                        else{
                           $preiview.hide('blind',300,function(){
                              $(this).html('<img data-url="'+$val+'" width="120" height="90" src="'+$data.url+'" />');
                              $(this).show('blind',300);
                           });
                        }
                    }
                    else{
                        $preiview.html('').hide().after('<span class="ptb-submission-file-danger ptb-submission-error-text">'+ptb_submission_video.video_error+'</span>');
                        $(this).addClass('ptb-submission-required-error');
                    }
                }
            }
            else{
                $preiview.hide('blind',300,function(){
                    $(this).html('').hide();
                });
            }
        });
        var $prev = $('.ptb-submission-form .ptb_extra_video_url');
        $prev.each(function(){
           if($.trim($(this).val())){
               $(this).trigger('focusout');
           } 
        });
    });
    
}(jQuery));