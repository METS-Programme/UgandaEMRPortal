var PTB_Submission_Map = function(){
        var $ = jQuery;
        var geocoder;
        function geocodePosition(pos,infowindow,map,marker) {

            geocoder.geocode({
                latLng: {lat:typeof pos.lat=='function'?pos.lat():pos.lat,lng:typeof pos.lng=='function'?pos.lng():pos.lng}}, 
                function(responses) {
                    if (responses && responses.length > 0) {
                      infowindow.setContent(responses[0].formatted_address);
                      infowindow.open(map,marker);
                    } else {
                       infowindow.close();
                      infowindow.setContent('');
                    }
            });
        };
        
        function setvalue(place,place_id,$pos){
             place.val(JSON.stringify({
                        'place':place_id,
                        'location':[$pos.lat(),$pos.lng()]
                        }));
        };
        
        function initialize(id) {
            if(!geocoder){
                geocoder = new google.maps.Geocoder();
            }
            var mapOptions = {
               center: new google.maps.LatLng(43.67023, -79.38676),
               zoom: 13,
               mapTypeControl: true,
                mapTypeControlOptions: {
                  style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
                }
            };
            var map = new google.maps.Map(document.getElementById('ptb_extra_'+id+'_canvas'),mapOptions);
            var input = (document.getElementById('ptb_extra_'+id+'_location'));
            var autocomplete = new google.maps.places.Autocomplete(input);
            autocomplete.bindTo('bounds', map);
            var infowindow = new google.maps.InfoWindow({
                maxWidth: 300
            });
            var marker = new google.maps.Marker({
              map: map,
              draggable: true,
              position: mapOptions.center,
              animation: google.maps.Animation.DROP,
              anchorPoint: new google.maps.Point(0, -29)
            });
            marker.setVisible(true);
            var $setplace = false;
            var $place = $('#ptb_extra_'+id+'_place');
            var $v = $place.val();
            if($v){
                $v = $.parseJSON($v);
                if($v){
                    if($v.place){
                        var service = new google.maps.places.PlacesService(map);
                        service.getDetails({placeId: $v.place}, function(place, status) {
                            if (status == google.maps.places.PlacesServiceStatus.OK) {
                              $setplace = place;
                              google.maps.event.trigger(autocomplete, "place_changed", id); 
                              if(place.formatted_address){
                                  $(input).val(place.formatted_address);
                              }
                              else{
                                  $(input).val(place.name);
                              }
                            }
                      });
                    }
                    else if($v.location){
                        var $latlng = {'lat':$v.location[0],'lng':$v.location[1]};
                        marker.setPosition($latlng);
                        map.setCenter($latlng);
                        geocodePosition($latlng,infowindow,map,marker);
                        delete $latlng;
                    }
                }
            
            }
            
            google.maps.event.addListener(map, 'click', function(event) {
              marker.setPosition(event.latLng);
              geocodePosition(event.latLng,infowindow,map,marker);
              setvalue($place,'',event.latLng);
            });
            google.maps.event.addListener(marker, 'dragstart', function() {
                infowindow.close();
            });
            google.maps.event.addListener(marker, 'dragend', function() {
                var $pos = marker.getPosition();
                geocodePosition($pos,infowindow,map,marker);
                setvalue($place,'',$pos);
            });
            google.maps.event.addListener(marker, 'click', function() {
              infowindow.open(map,marker);
            });
            google.maps.event.addListener(autocomplete, 'place_changed', function() {
              infowindow.close();
              var address = '';
              if(!$setplace){
                    var place = autocomplete.getPlace();
                    if (!place || !place.geometry) {
                      alert("Autocomplete's returned place contains no geometry");
                      return;
                    }
              }
              else{
                  var place = $setplace;
                  $setplace = false;
              }

              // If the place has a geometry, then present it on a map.
              if (place.geometry.viewport) {
                map.fitBounds(place.geometry.viewport);
              } else {
                map.setCenter(place.geometry.location);
                map.setZoom(17);  // Why 17? Because it looks good.
              }
              marker.setIcon(({
                url: place.icon,
                size: new google.maps.Size(71, 71),
                origin: new google.maps.Point(0, 0),
                anchor: new google.maps.Point(17, 34),
                scaledSize: new google.maps.Size(35, 35)
              }));

              marker.setPosition(place.geometry.location);
              marker.setVisible(true);
              if(!$setplace && place.address_components){
                         address = [
                           (place.address_components[0] && place.address_components[0].short_name || ''),
                           (place.address_components[1] && place.address_components[1].short_name || ''),
                           (place.address_components[2] && place.address_components[2].short_name || '')
                         ].join(' ');
                       address = '<div><strong>' + place.name + '</strong><br>' + address;
              }
              infowindow.setContent(address);
              infowindow.open(map, marker);
              setvalue($place,place.place_id,place.geometry.location);
            });
        };
    
    var $maps = $('.ptb_extras_submission_map');
    $maps.each(function(){
        initialize($(this).data('id'));
    });
};
(function ($) {
        'use strict';
        
        function loadScript(src,callback){
            var script = document.createElement("script");
            script.type = "text/javascript";
            script.async  = 'async';
            script.defer = 'defer';
            if(callback)script.onload=callback;
            document.getElementsByTagName("head")[0].appendChild(script);
            script.src = src;
        }
        
        $(document).ready(function(){
            if(typeof google !== 'object' || typeof google.maps !== 'object' || typeof google.maps.places === 'undefined'){
                if(typeof google === 'object' && google!==null && typeof google.maps === 'object'  && typeof google.maps.places === 'undefined'){
                    google.maps = null;
                }
                loadScript('//maps.googleapis.com/maps/api/js?v=3.exp&signed_in=false&sensor=false&libraries=places&callback=PTB_Submission_Map&language='+ptb_submission.lng);
            }
            else{
                PTB_Submission_Map();
            }
            
        });
        
        $(document).on('ptb_submission_validate',function(e,$this){
           $this = $(this);
           if($this.closest('.ptb_extra_submission_map_control').length>0){
                return $this.is(':hidden') && !$.trim($this.val());
              
           }
        });
        
}(jQuery));