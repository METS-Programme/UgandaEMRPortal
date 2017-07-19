! function($, window, document, undefined) {
    function Map_Control(e) {
        this.options = e
    }

    function overlay_generator(e, o) {
        this.tileSize = e, this.overlay_options = o
    }

    function GoogleMaps(e, o) {
        var t;
        console.log(o), this.element = e, this.map_data = $.extend({}, {}, o), t = this.map_data.map_options, this.settings = $.extend({
            zoom: "5",
            map_type_id: "ROADMAP",
            scroll_wheel: !0,
            map_visual_refresh: !1,
            pan_control: "true",
            pan_control_position: "TOP_LEFT",
            zoom_control: !0,
            zoom_control_style: "SMALL",
            zoom_control_position: "TOP_LEFT",
            map_type_control: !0,
            map_type_control_style: "HORIZONTAL_BAR",
            map_type_control_position: "RIGHT_TOP",
            scale_control: !0,
            street_view_control: !0,
            street_view_control_position: "TOP_LEFT",
            overview_map_control: !0,
            center_lat: "40.6153983",
            center_lng: "-74.2535216",
            draggable: !0
        }, {}, t), this.container = $("div[rel='" + $(this.element).attr("id") + "']"), this.directionsService = new google.maps.DirectionsService, this.directionsDisplay = new google.maps.DirectionsRenderer, this.drawingmanager = new Object, this.geocoder = new google.maps.Geocoder, this.places = new Array, this.show_places = new Array, this.categories = new Object, this.all_shapes = [], this.wpgmp_polylines = [], this.wpgmp_polygons = [], this.wpgmp_circles = [], this.wpgmp_shape_events = [], this.wpgmp_rectangles = [], this.per_page_value, this.current_amenities = new Array, this.route_directions = new Array, this.init()
    }
    Map_Control.prototype.create_element = function(e, o, t) {
        var a = document.createElement("div");
        a.style.backgroundColor = "#fff", a.style.border = "2px solid #fff", a.style.borderRadius = "3px", a.style.boxShadow = "0 2px 6px rgba(0,0,0,.3)", a.style.cursor = "pointer", a.style.marginBottom = "22px", a.style.textAlign = "center", a.title = "Click to recenter the map", e.appendChild(a);
        var n = document.createElement("div");
        n.style.color = "rgb(25,25,25)", n.style.fontFamily = "Roboto,Arial,sans-serif", n.style.fontSize = "16px", n.style.lineHeight = "38px", n.style.paddingLeft = "5px", n.style.paddingRight = "5px", n.innerHTML = t, a.appendChild(n), google.maps.event.addDomListener(a, "click", function() {
            o.setCenter(new google.maps.LatLng(41.85, -87.65))
        })
    }, overlay_generator.prototype.getTile = function(e, o, t) {
        var a = t.createElement("div");
        return a.innerHTML = e, a.style.width = "200px", a.style.height = "300px", a.style.fontSize = this.overlay_options.font_size + "px", a.style.borderStyle = this.overlay_options.border_style, a.style.borderWidth = this.overlay_options.border_width + "px", a.style.borderColor = this.overlay_options.border_color, a
    }, GoogleMaps.prototype = {
        init: function() {
            var map_obj = this,
                center = new google.maps.LatLng(map_obj.settings.center_lat, map_obj.settings.center_lng);
            map_obj.map = new google.maps.Map(map_obj.element, {
                zoom: parseInt(map_obj.settings.zoom),
                center: center,
                scrollwheel: "false" != map_obj.settings.scroll_wheel,
                panControl: 1 == map_obj.settings.pan_control,
                panControlOptions: {
                    position: eval("google.maps.ControlPosition." + map_obj.settings.pan_control_position)
                },
                zoomControl: 1 == map_obj.settings.zoom_control,
                zoomControlOptions: {
                    style: eval("google.maps.ZoomControlStyle." + map_obj.settings.zoom_control_style),
                    position: eval("google.maps.ControlPosition." + map_obj.settings.zoom_control_position)
                },
                mapTypeControl: 1 == map_obj.settings.map_type_control,
                mapTypeControlOptions: {
                    style: eval("google.maps.MapTypeControlStyle." + map_obj.settings.map_type_control_style),
                    position: eval("google.maps.ControlPosition." + map_obj.settings.map_type_control_position)
                },
                scaleControl: 1 == map_obj.settings.scale_control,
                streetViewControl: 1 == map_obj.settings.street_view_control,
                streetViewControlOptions: {
                    position: eval("google.maps.ControlPosition." + map_obj.settings.street_view_control_position)
                },
                overviewMapControl: 1 == map_obj.settings.overview_map_control,
                overviewMapControlOptions: {
                    opened: map_obj.settings.overview_map_control
                },
                draggable: map_obj.settings.draggable,
                mapTypeId: eval("google.maps.MapTypeId." + map_obj.settings.map_type_id),
                styles: eval(map_obj.map_data.styles)
            }), map_obj.map_loaded(), map_obj.responsive_map(), map_obj.create_markers(), map_obj.display_markers(), "undefined" != typeof map_obj.map_data.street_view && map_obj.set_streetview(center), "undefined" != typeof map_obj.map_data.bicyle_layer && map_obj.set_bicyle_layer(), "undefined" != typeof map_obj.map_data.traffic_layer && map_obj.set_traffic_layer(), "undefined" != typeof map_obj.map_data.transit_layer && map_obj.set_transit_layer(), "45" == typeof map_obj.settings.display_45_imagery && map_obj.set_45_imagery(), typeof map_obj.map_data.map_visual_refresh === !0 && map_obj.set_visual_refresh(), $(map_obj.container).on("click", ".place_title", function() {
                map_obj.open_infowindow($(this).data("marker")), $("html, body").animate({
                    scrollTop: $(map_obj.container).offset().top
                }, 500)
            }), map_obj.google_auto_suggest($(".wpgmp_auto_suggest")), 1 == map_obj.settings.show_center_circle && map_obj.show_center_circle()
        },
        createMarker: function(e) {
            var o = this,
                t = o.map,
                a = (e.geometry.location, {
                    url: e.icon,
                    size: new google.maps.Size(25, 25),
                    scaledSize: new google.maps.Size(25, 25)
                });
            e.marker = new google.maps.Marker({
                map: t,
                position: e.geometry.location,
                icon: a
            }), google.maps.event.addListener(e.marker, "click", function() {
                o.amenity_infowindow.setContent(e.name), o.amenity_infowindow.open(t, this)
            }), o.current_amenities.push(e)
        },
        get_user_position: function(e, o) {
            var t = this;
            t.user_lat_lng ? e && e(t.user_lat_lng) : navigator.geolocation.getCurrentPosition(function(o) {
                t.user_lat_lng = new google.maps.LatLng(o.coords.latitude, o.coords.longitude), e && e(t.user_lat_lng)
            }, function(e) {
                o && o(e)
            }, {
                enableHighAccuracy: !0,
                timeout: 5e3,
                maximumAge: 0
            })
        },
        marker_bind: function(e) {
            map_obj = this, google.maps.event.addListener(e, "drag", function() {
                var o = e.getPosition();
                map_obj.geocoder.geocode({
                    latLng: o
                }, function(e, o) {
                    if (o == google.maps.GeocoderStatus.OK && ($("#googlemap_address").val(e[0].formatted_address), $(".google_city").val(map_obj.wpgmp_finddata(e[0], "administrative_area_level_3") || map_obj.wpgmp_finddata(e[0], "locality")), $(".google_state").val(map_obj.wpgmp_finddata(e[0], "administrative_area_level_1")), $(".google_country").val(map_obj.wpgmp_finddata(e[0], "country")), e[0].address_components))
                        for (var t = 0; t < e[0].address_components.length; t++)
                            for (var a = 0; a < e[0].address_components[t].types.length; a++) "postal_code" == e[0].address_components[t].types[a] && (wpgmp_zip_code = e[0].address_components[t].long_name, $(".google_postal_code").val(wpgmp_zip_code))
                }), $(".google_latitude").val(o.lat()), $(".google_longitude").val(o.lng())
            })
        },
        google_auto_suggest: function(e) {
            var o = this;
            e.each(function() {
                var e = this,
                    t = new google.maps.places.Autocomplete(this);
                if (t.bindTo("bounds", o.map), "location_address" == $(this).attr("name")) {
                    var a = (new google.maps.InfoWindow, new google.maps.Marker({
                        map: o.map,
                        draggable: !0,
                        anchorPoint: new google.maps.Point(0, -29)
                    }));
                    o.marker_bind(a), google.maps.event.addListener(t, "place_changed", function() {
                        var e = t.getPlace();
                        if (e.geometry) {
                            if (e.geometry.viewport ? o.map.fitBounds(e.geometry.viewport) : (o.map.setCenter(e.geometry.location), o.map.setZoom(17)), $(".google_latitude").val(e.geometry.location.lat()), $(".google_longitude").val(e.geometry.location.lng()), $(".google_city").val(o.wpgmp_finddata(e, "administrative_area_level_3") || o.wpgmp_finddata(e, "locality")), $(".google_state").val(o.wpgmp_finddata(e, "administrative_area_level_1")), $(".google_country").val(o.wpgmp_finddata(e, "country")), e.address_components)
                                for (var n = 0; n < e.address_components.length; n++)
                                    for (var i = 0; i < e.address_components[n].types.length; i++) "postal_code" == e.address_components[n].types[i] && (wpgmp_zip_code = e.address_components[n].long_name, $(".google_postal_code").val(wpgmp_zip_code));
                            a.setPosition(e.geometry.location), a.setVisible(!0)
                        }
                    })
                } else google.maps.event.addListener(t, "place_changed", function() {
                    var o = t.getPlace();
                    o.geometry && ($().val(o.geometry.location.lat()), $(e).data("longitude", o.geometry.location.lng()), $(e).data("latitude", o.geometry.location.lat()))
                })
            })
        },
        wpgmp_finddata: function(e, o) {
            var t = "";
            for (i = 0; i < e.address_components.length; ++i) {
                var a = e.address_components[i];
                $.each(a.types, function(e, n) {
                    n == o && (t = a.long_name)
                })
            }
            return t
        },
        open_infowindow: function(e) {
            var o = this;
            $.each(this.map_data.places, function(t, a) {
                parseInt(a.id) == parseInt(e) && 1 == a.marker.visible ? (a.infowindow.open(o.map, a.marker), console.log(a)) : a.infowindow.close()
            })
        },
        place_info: function(e) {
            var o;
            return $.each(this.places, function(t, a) {
                parseInt(a.id) == parseInt(e) && (o = a)
            }), o
        },
        event_listener: function(e, o, t) {
            google.maps.event.addListener(e, o, t)
        },
        set_visual_refresh: function() {
            google.maps.visualRefresh = !0
        },
        set_45_imagery: function() {
            this.map.setTilt(45)
        },
        set_bicyle_layer: function() {
            var e = new google.maps.BicyclingLayer;
            e.setMap(this.map)
        },
        set_traffic_layer: function() {
            var e = new google.maps.TrafficLayer;
            e.setMap(this.map)
        },
        set_transit_layer: function() {
            var e = new google.maps.TransitLayer;
            e.setMap(this.map)
        },
        set_streetview: function(e) {
            var o = {
                position: e,
                addressControlOptions: {
                    position: google.maps.ControlPosition.BOTTOM_CENTER
                },
                linksControl: this.map_data.street_view.links_control,
                panControl: this.map_data.street_view.street_view_pan_control,
                zoomControlOptions: {
                    style: google.maps.ZoomControlStyle.SMALL
                },
                enableCloseButton: this.map_data.street_view.street_view_close_button
            };
            "" != this.map_data.street_view.pov_heading && (o.pov = {
                heading: parseInt(this.map_data.street_view.pov_heading),
                pitch: parseInt(this.map_data.street_view.pov_pitch)
            }), new google.maps.StreetViewPanorama(this.element, o)
        },
        map_loaded: function() {
            var e = this,
                o = e.map;
            google.maps.event.addListenerOnce(o, "idle", function() {
                var e = o.getCenter();
                google.maps.event.trigger(o, "resize"), o.setCenter(e)
            }), "true" == e.settings.center_by_nearest && e.center_by_nearest()
        },
        responsive_map: function() {
            var e = this,
                o = e.map;
            google.maps.event.addDomListener(window, "resize", function() {
                var e = o.getCenter();
                google.maps.event.trigger(o, "resize"), o.setCenter(e), o.getBounds()
            })
        },
        create_markers: function() {
            var e = this,
                o = e.map_data.places;
            $.each(o, function(o, t) {
                if (t.location.lat && t.location.lng) {
                    t.marker = new google.maps.Marker({
                        position: new google.maps.LatLng(parseFloat(t.location.lat), parseFloat(t.location.lng)),
                        icon: t.location.icon,
                        url: t.url,
                        draggable: t.location.draggable,
                        animation: google.maps.Animation.DROP,
                        map: e.map
                    }), 1 == e.settings.infowindow_filter_only && (t.marker.visible = !1, t.marker.setVisible(!1)), "edit_location" == e.map_data.page && e.marker_bind(t.marker);
                    var a = [];
                    if ("undefined" != typeof t.categories)
                        for (var n in t.categories) a.push(t.categories[n].name);
                    var i = "";
                    if (e.settings.infowindow_setting) {
                        var s = {
                            "{location_id}": t.id,
                            "{location_title}": t.title,
                            "{location_address}": t.address,
                            "{location_latitude}": t.location.lat,
                            "{location_longitude}": t.location.lng,
                            "{location_city}": t.location.city,
                            "{location_state}": t.location.state,
                            "{location_country}": t.location.country,
                            "{location_postal_code}": t.location.postal_code,
                            "{location_zoom}": t.location.zoom,
                            "{location_icon}": t.location.icon,
                            "{location_categories}": a.join(","),
                            "{location_message}": t.content
                        };
                        if ("undefined" != typeof t.location.extra_fields)
                            for (var r in t.location.extra_fields) s["{" + r + "}"] = t.location.extra_fields[r];
                        var l = e.settings.infowindow_setting.replace(/{[^{}]+}/g, function(e) {
                            return e in s ? s[e] : ""
                        });
                        i = l
                    }
                    "" == i && (i = '<div class="wpgmp_infowindow">' + t.content + "</div>"), t.infowindow = new google.maps.InfoWindow({
                        content: i
                    }), "true" == t.location.infowindow_default_open ? e.openInfoWindow(t) : "true" == e.settings.default_infowindow_open && e.openInfoWindow(t);
                    var p = e.settings.infowindow_open_event;
                    e.event_listener(t.marker, p, function() {
                        e.openInfoWindow(t)
                    }), e.places.push(t)
                }
            })
        },
        display_markers: function() {
            var e = this;
            e.show_places = [], e.categories = [];
            for (var o = new Object, t = 0; t < e.places.length; t++) e.places[t].marker.setMap(e.map), 1 == e.places[t].marker.visible && e.show_places.push(this.places[t]), "undefined" != typeof e.places[t].categories && $.each(e.places[t].categories, function(e, t) {
                "undefined" == typeof o[t.name] && (o[t.name] = t)
            });
            this.categories = o
        },
        get_current_location: function(e, o) {
            var t = this;
            "undefined" == typeof t.user_location ? navigator.geolocation.getCurrentPosition(function(o) {
                t.user_location = new google.maps.LatLng(o.coords.latitude, o.coords.longitude), e && e(t.user_location)
            }, function(e) {
                o && o(e)
            }, {
                enableHighAccuracy: !0,
                timeout: 5e3,
                maximumAge: 0
            }) : e && e(t.user_location)
        },
        openInfoWindow: function(e) {
            $.each(this.places, function(e, o) {
                o.infowindow.close()
            }), "custom_link" == e.location.redirect_url ? "yes" == e.location.open_new_tab ? window.open(e.location.redirect_custom_link, "_blank") : window.open(e.location.redirect_custom_link, "_self") : e.infowindow.open(this.map, e.marker)
        }
    }, $.fn.maps = function(e, o) {
        return this.each(function() {
            $.data(this, "wpgmp_maps") || $.data(this, "wpgmp_maps", new GoogleMaps(this, e, o))
        }), this
    }
}(jQuery, window, document);
