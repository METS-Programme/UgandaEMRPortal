
function ptb_extra_map_init(e) {
    var $ = jQuery,
        $maps = $('.ptb_post_cmb_item_map');
    $maps.each(function(){
        
    
        var mapOptions = {
            center: new google.maps.LatLng(43.67023, -79.38676),
            zoom: 13
        },
        id = $(this).prop('id'),
        map = new google.maps.Map(document.getElementById('ptb_extra_' + id + '_canvas'), mapOptions),
        input = (document.getElementById('ptb_extra_' + id + '_location')),
        types = document.getElementById('ptb_extra_' + id + '_select');
        map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
        map.controls[google.maps.ControlPosition.TOP_LEFT].push(types);
        var autocomplete = new google.maps.places.Autocomplete(input);
        autocomplete.bindTo('bounds', map);
        var infowindow = new google.maps.InfoWindow();
        var marker = new google.maps.Marker({
            map: map,
            anchorPoint: new google.maps.Point(0, -29)
        });
        var $setplace = false;
        var $place = $('#ptb_extra_' + id + '_place');
        var $info = $('#ptb_extra_' + id + '_info');
        var $v = $place.val();
        if ($v) {
            $v = $.parseJSON($v);
            if ($v) {
                if ($v.place) {
                    var service = new google.maps.places.PlacesService(map);
                    service.getDetails({placeId: $v.place}, function (place, status) {
                        if (status == google.maps.places.PlacesServiceStatus.OK) {
                            $setplace = place;
                            google.maps.event.trigger(autocomplete, "place_changed", e);
                            if (place.formatted_address) {
                                $(input).val(place.formatted_address);
                            }
                            else {
                                $(input).val(place.name);
                            }
                        }
                    });
                }
                else if ($v.location) {
                    marker.setPosition({'lat': $v.location[0], 'lng': $v.location[1]});
                    map.setCenter({'lat': $v.location[0], 'lng': $v.location[1]});
                    if ($.trim($info.val())) {
                        infowindow.setContent($.trim($info.val()));
                        infowindow.open(map, marker);
                    }
                }
            }
        }
        google.maps.event.addListener(autocomplete, 'place_changed', function () {
            infowindow.close();
            marker.setVisible(false);
            var address = '';
            if (!$setplace) {
                var place = autocomplete.getPlace();
                if (!place || !place.geometry) {
                    alert("Autocomplete's returned place contains no geometry");
                    return;
                }
            }
            else {
                var place = $setplace;
                address = $.trim($info.val());
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

            if (!$setplace) {

                if (!$.trim($info.val())) {
                    if (place.address_components) {
                        address = [
                            (place.address_components[0] && place.address_components[0].short_name || ''),
                            (place.address_components[1] && place.address_components[1].short_name || ''),
                            (place.address_components[2] && place.address_components[2].short_name || '')
                        ].join(' ');
                    }
                    $info.val(address);
                    address = '<div><strong>' + place.name + '</strong><br>' + address;
                }
                else {
                    address = $.trim($info.val()).replace(/(?:\r\n|\r|\n)/ig, '<br />');
                }
            }

            infowindow.setContent(address);
            infowindow.open(map, marker);

            $place.val(JSON.stringify({'place': place.place_id, 'location': place.geometry.location}));
        });
        google.maps.event.addListener(marker, 'click', function () {
            infowindow.open(map, marker);
        });
        // Sets a listener on a radio button to change the filter type on Places
        // Autocomplete.
        $(types).change(function () {
            autocomplete.setTypes([$(this).val()]);
        });
        $info.keyup(function () {
            var $v = $.trim($(this).val()).replace(/(?:\r\n|\r|\n)/ig, '<br />');
            infowindow.setContent($v);
        });
    });
}
(function ($) {
    'use strict';

    /* Custom Meta Box Map*/

    $(document).on('ptb_metabox_create_map', function (e) {

    });

    $(document).on('ptb_add_metabox_map', function (e) {
        return {};
    });

    $(document).on('ptb_metabox_save_map', function (e) {

    });
    function loadScript(src, callback) {
        var script = document.createElement("script");
        script.type = "text/javascript";
        if (callback) script.onload = callback;
        document.getElementsByTagName("head")[0].appendChild(script);
        script.defer = true;
        script.async = true;
        script.src = src;
    }
    $(document).on('ptb_post_cmb_map_body_handle', function(e){
        
    });
   
   $(document).ready(function(){
      if($('.ptb_post_cmb_item_map').length>0){
        if (typeof google !== 'object' || typeof google.maps !== 'object' || typeof google.maps.places === 'undefined') {
            if (typeof google === 'object' && google !== null && typeof google.maps === 'object' && typeof google.maps.places === 'undefined') {
                google.maps = null;
            }
            loadScript('//maps.googleapis.com/maps/api/js?v=3&signed_in=false&libraries=places&callback=ptb_extra_map_init&language=' + ptb_extra.lng+'&key='+ptb_extra.map_key);
            } else {
                ptb_extra_map_init();
            }
      } 
   });

}(jQuery));