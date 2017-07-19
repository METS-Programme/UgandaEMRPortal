function PTBSearchMap(){
    var $ = jQuery,
        geocoder;
    if(!geocoder){
        geocoder = new google.maps.Geocoder();
    }
    function geocodePosition(pos,$option,$this,$i,$length,$wrap) {
        var $args = pos.place?{placeId:pos.place}:{latLng:{lat:pos.lat,lng:pos.lng}};
        geocoder.geocode(
            $args, 
            function(responses) {
                if (responses && responses.length > 0) {
                   $option.text(responses[0].formatted_address);
                }
                else{
                    $option.remove();
                }
                if($i===$length){
                    $this.trigger("chosen:updated");
                    $wrap.removeClass('ptb_search_map_disable');
                }
            }
        );
    }
    
    var $selects = $('.ptb_search_map select');
    $selects.each(function($i){
        var $interupt = $i * 2,
            $this = $(this),
            $wrap = $this.closest('.ptb_search_map');
            $wrap.addClass('ptb_search_map_disable');
        setTimeout(function () {
           var  $options = $this.find('option[value!=""]'),
                $length  = $options.length-1;
            $options.each(function($j){
                var $delay = $j * 10,
                    $val = $(this).val(),
                    $opt = $(this);
                setTimeout(function () {
                        if($val){
                            $val = window.atob($val);
                            geocodePosition($.parseJSON($val),$opt,$this,$j,$length,$wrap);
                        }
                },$delay);
           });
            $this.trigger("chosen:updated");
       },$interupt);
    });
}
(function ($) {
        'use strict';
    
        if (typeof google !== 'object' || typeof google.maps !== 'object') {
            PTB.LoadAsync('//maps.googleapis.com/maps/api/js?v=3.exp&callback=PTBSearchMap', false, true, true);
        }
        else {
           PTBSearchMap();
        }
        
}(jQuery));