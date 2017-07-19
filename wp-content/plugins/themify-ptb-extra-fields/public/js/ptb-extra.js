var $mobile = /(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent) ||
    /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0, 4));
var PTB_MapInit = function() {
    var $ = jQuery;
    var $maps = $('.ptb_extra_map');

    function Initialize($map, $options) {
        if ($options.place.place || $options.place.location) {
            $($map).css('height', $options.height);
            $($map).closest('.ptb_map').css('width', $options.width + ($options.width_t == '%' ? '%' : ''));
              var $road = $options.mapTypeId;
            if ($road == 'ROADMAP') {
                  $road = google.maps.MapTypeId.ROADMAP;
            } else if ($road == 'SATELLITE') {
                  $road = google.maps.MapTypeId.SATELLITE;
            } else if ($road == 'HYBRID') {
                  $road = google.maps.MapTypeId.HYBRID;
            } else if ($road == 'TERRAIN') {
                  $road = google.maps.MapTypeId.TERRAIN;
              }
            if ($mobile && $options.drag_m) {
                  $options.drag = false;
              }
              var mapOptions = {
                center: new google.maps.LatLng(-34.397, 150.644),
                                zoom: $options.zoom,
                mapTypeId: $road,
                scrollwheel: $options.scroll ? true : false,
                draggable: $options.drag ? true : false
                            };
            var map = new google.maps.Map($map, mapOptions),
                $content = $options.info ? $options.info.replace(/(?:\r\n|\r|\n)/ig, '<br />') : '',
                  marker = new google.maps.Marker({
                    map: map,
                    anchorPoint: new google.maps.Point(0, -29)
                });
              marker.setVisible(false);
            if ($options.place.place) {
                    var service = new google.maps.places.PlacesService(map);
                service.getDetails({
                    placeId: $options.place.place
                }, function(place, status) {
                        if (status == google.maps.places.PlacesServiceStatus.OK) {
                            map.setCenter(place.geometry.location);
                            marker.setIcon(({
                              url: place.icon,
                              size: new google.maps.Size(71, 71),
                              origin: new google.maps.Point(0, 0),
                              anchor: new google.maps.Point(17, 34),
                              scaledSize: new google.maps.Size(35, 35)
                            }));
                            marker.setPosition(place.geometry.location);
                            if (place.geometry.viewport) {
                                map.fitBounds(place.geometry.viewport);
                            } else {
                                map.setCenter(place.geometry.location);
                            }
                            map.setZoom(mapOptions.zoom);
                            marker.setVisible(true);
                    } else {
                            return false;
                        }
                  });
            } else {
                $options.place.location[0] = parseFloat($options.place.location[0]);
                $options.place.location[1] = parseFloat($options.place.location[1]);
                marker.setPosition({
                    'lat': $options.place.location[0],
                    'lng': $options.place.location[1]
                });
                map.setCenter({
                    'lat': $options.place.location[0],
                    'lng': $options.place.location[1]
                });
                marker.setVisible(true);
              }
            if ($content) {
                var infowindow = new google.maps.InfoWindow({
                    content: $content
                });
                infowindow.open(map, marker);
                google.maps.event.addListener(marker, 'click', function() {
                    infowindow.open(map, marker);
                });
              }
          }
       };
    $maps.each(function() {
        var $data = $(this).data('map');
        Initialize(this, $data);
    });
};
(function($) {
    'use strict';
    $(document).on('ptb_loaded', function() {
    
        /*Gallery */
        if ($('.ptb_extra_showcase').length > 0) {
            $('.ptb_extra_showcase img').click(function(e) {
                 e.preventDefault();
                 var $main = $(this).closest('.ptb_extra_showcase').find('.ptb_extra_main_image');
                 var $img = $(this).clone();
                 $main.html($img);
             });
            $('.ptb_extra_showcase').each(function() {
                 $(this).find('img').first().trigger('click');
             });
         }
        /*Map*/ 
        
        function loadScript(src, callback) {
            var script = document.createElement("script");
            script.type = "text/javascript";
            if (callback) script.onload = callback;
            document.getElementsByTagName("head")[0].appendChild(script);
            script.defer = true;
            script.async = true;
            script.src = src;
        }
        if ($('.ptb_extra_map').length > 0) {
            if (typeof google !== 'object' || typeof google.maps !== 'object' || typeof google.maps.places === 'undefined') {
                if (typeof google === 'object' && google !== null && typeof google.maps === 'object' && typeof google.maps.places === 'undefined') {
                    google.maps = null;
                }
                loadScript('//maps.googleapis.com/maps/api/js?v=3.exp&signed_in=false&libraries=places&callback=PTB_MapInit&language=' + ptb_extra.lng+'&key='+ptb_extra.map_key);
            } else {
                PTB_MapInit();
            }
        }
       
       
        /*Progress Bar*/
        var progressCallback = function() {
            var $progress_bar = $('.ptb_extra_progress_bar').not('.ptb_extra_progress_bar_done');
            $progress_bar.each(function() {
                var $orientation = $(this).data('meterorientation');
                var $args = {
                    goal: '100',
                    raised: $(this).data('raised').toString(),
                    meterOrientation: $orientation,
                    bgColor: 'rgba(0,0,0,.1)',
                    width: $orientation == 'vertical' ? '60px' : '100%',
                    height: $orientation == 'vertical' ? '200px' : '3px',
                    displayTotal: !$(this).data('displaytotal') ? true : false,
                    animationSpeed: 2000
                         };
                if ($(this).data('barcolor')) {
                       $args.barColor = $(this).data('barcolor');
                    }
                $(this).jQMeter($args);
                $(this).addClass('ptb_extra_progress_bar_done');
            });
        };
        var $progress_bar = $('.ptb_extra_progress_bar').not('.ptb_extra_progress_bar_done');
        if ($progress_bar.length > 0) {
            if ($.fn.jQMeter) {
                progressCallback();
            } else {
                PTB.LoadAsync(ptb_extra.url + 'js/jqmeter.min.js', progressCallback, null, ptb_extra.ver, function() {
                    return ('undefined' !== typeof $.fn.jQMeter);
                });
        }
        }

        /*Slider*/
        var sliderCallback = function() {
             var $bxslider = $('.ptb_extra_bxslider');
            $bxslider.each(function() {
                if ($(this).find('li').length > 0) {
                        var $attr = $(this).attr('data-slider');
                    if ($attr) {
                           $attr = JSON.parse($attr);
                        $(this).addClass('ptb_extra_bxslider_'+$attr.mode);
                        $attr.controls = $attr.controls && parseInt($attr.controls) == 1 ? true : false;
                        $attr.pager = $attr.pager && parseInt($attr.pager) == 1 ? true : false;
                        $attr.autoHover = $attr.autoHover && parseInt($attr.autoHover) == 1 ? true : false;
                           $attr.adaptiveHeight = true;
                           $attr.useCSS = false;
                        if ($attr.pause == 0) {
                                   $attr.auto = false;
                                   $attr.pause = null;
                        } else {
                            $attr.pause = $attr.pause * 1000;
                                $attr.auto = true;
                           }
                           $attr.video = true;
                        if ($attr.slideHeight > 0) {
                            $(this).find('img').css('height', $attr.slideHeight);
                           }
                        if ($attr.mode == 'horizontal') {
                                $attr.maxSlides = $attr.minSlides;
                            if (!$attr.slideWidth) {
                                $attr.slideWidth = parseInt($(this).closest('.ptb_module').width() / $attr.minSlides);
                                }
                           }
                            $(this).bxSlider($attr); 
                       }
                     }
                $(this).addClass('ptb_extra_bxslider_done');
             });
        } 
        if ($('.ptb_extra_bxslider').not('.ptb_extra_bxslider_done').length > 0) {
            if ($.fn.bxSlider) {
                sliderCallback();
            } else {
                PTB.LoadCss(ptb_extra.url + 'css/jquery.bxslider.css', ptb_extra.ver);
                PTB.LoadAsync(ptb_extra.url + 'js/jquery.easing.1.3.js', function() {
                    PTB.LoadAsync(ptb_extra.url + 'js/jquery.fitvids.js', function() {
                        PTB.LoadAsync(ptb_extra.url + 'js/jquery.bxslider.min.js', sliderCallback, null, ptb_extra.ver, function() {
                            return ('undefined' !== typeof $.fn.bxSlider);
                        });
                    }, null, ptb_extra.ver);
                }, null, ptb_extra.ver);
            }
        }

        /*Rating Stars*/
        var $rating = $('.ptb_extra_rating');
        $rating.each(function() {
            var $hcolor = $(this).data('hcolor');
            var $vcolor = $(this).data('vcolor');
            var $id = $(this).data('id');
                var $class = 'ptb_extra_' + $id;
            $(this).addClass($class);
            var $style = '';
                if ($hcolor) {
                    $style += '.' + $class + ':not(.ptb_extra_readonly_rating) > span:hover:before,' +
                        '.' + $class + ':not(.ptb_extra_readonly_rating) > span:hover ~ span:before{color:' + $hcolor + ';}';
            }
                if ($vcolor) {
                    $style += '.' + $class + ' .ptb_extra_voted{color:' + $vcolor + ';}';
            }
                if ($style) {
                    $style = '<style type="text/css">' + $style + '</style>';
                $('body').append($style);
            }
        })
            .not('.ptb_extra_readonly_rating').not('.ptb_extra_not_vote').children('span').click(function(e) {
            e.preventDefault();
            var $self = $(this).closest('.ptb_extra_rating');
                var $spans = $self.children('span');
                var $value = $spans.length - $(this).index();
            var $post = $self.data('post');
                var $key = $self.data('key');
                var $same = $(".ptb_extra_rating[data-key='" + $key + "'][data-post='" + $post + "']");
            $.ajax({
                url: ajaxurl,
                    dataType: 'json',
                    data: {
                        id: $post,
                        value: $value,
                        key: $key,
                        action: 'ptb_extra_rate_voted'
                      },
                type: 'POST',
                    beforeSend: function() {
                        if ($self.data('before')) {
                            var $str = $self.data('before').replace(/#rated_value#/gi, $value);
                        if ($str && !confirm($str)) {
                           return false;
                        }
                        };
                  $same.addClass('ptb_extra_readonly_rating');  
                },
                    success: function(data) {
                        if (data && data.success) {
                        var $total = data.total;
                            $same.each(function() {
                                $($(this).children('span').get().reverse()).each(function($i) {
                                    if ($total > $i) {
                                    $(this).addClass('ptb_extra_voted');
                                }
                            });
                            var $count = $(this).next('.ptb_extra_vote_count');
                                if ($count.length > 0) {
                                    $count.html('( ' + data.count + ' )');
                            }
                        });
                            if ($self.data('after')) {
                                var $str = $self.data('after').replace(/#rated_value#/gi, $value);
                            if ($str) {
                                alert($str);
                            }
                        }
                    }
                }
            });
        });
        /*Video*/
        $('.ptb_extra_show_video').click(function(e) {
            e.preventDefault();
            var $url = $(this).data('url');
            if ($url) {
                var $img = $(this).next('img');
                $img.replaceWith('<iframe width="100%" style="height:' + $img.height() + 'px" src="' + $url + '" frameborder="0" ebkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>');
            } else {
                var $v = $(this).next('video');
                $v.prop('controls', 1);
                $v.get(0).play();
            }
            $(this).remove();
        });
        $('.ptb_extra_lightbox a,a.ptb_extra_lighbtox').lightcase({
            navigateEndless: false,
            showSequenceInfo: false,
            transition: 'elastic',
            slideshow: false,
            swipe: true,
            attr: 'data-rel',
            onStart: {
            bar: function() {
                    $.event.trigger({type: "ptb_ligthbox_close"});
                    $('body').addClass('ptb_hide_scroll');
                }
            },
            onClose: {
                qux: function () {
                    $.event.trigger({type: "ptb_ligthbox_close"});
                }
            }
        });
});
}(jQuery));