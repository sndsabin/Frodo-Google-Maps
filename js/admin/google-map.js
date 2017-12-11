function initMap() {
    // Check if FRODO_GOOGLE_MAP_USER_SAVED_MARKER is defined
    if (typeof FRODO_GOOGLE_MAP_USER_SAVED_MARKER !== 'undefined') {
        var userSavedMarker = JSON.parse(FRODO_GOOGLE_MAP_USER_SAVED_MARKER.data);

        if (userSavedMarker.latitude && userSavedMarker.longitude) {
            var userSavedLatitude = parseFloat(userSavedMarker.latitude);
            var userSavedLongitude = parseFloat(userSavedMarker.longitude);
        }

        if (userSavedMarker.marker_animation) {
            var markerAnimation = userSavedMarker.marker_animation;
        }

    }

    var map = new google.maps.Map(document.getElementById('map'), {
        center: (userSavedLatitude && userSavedLongitude) ? {lat: userSavedLatitude, lng: userSavedLongitude} : {lat: 28.3949, lng: 84.1240},
        zoom: 7
    });

    var card = document.getElementById('map-container');
    var input = document.getElementById('location');

    var autocomplete = new google.maps.places.Autocomplete(input);

    // Bind the map's bounds (viewport) property to the autocomplete object,
    // so that the autocomplete requests use the current map bounds for the
    // bounds option in the request.
    autocomplete.bindTo('bounds', map);

    var infowindow = new google.maps.InfoWindow();
    var infowindowContent = document.getElementById('infowindow-content');
    infowindow.setContent(infowindowContent);

    if (userSavedMarker) {
        var latlng = new google.maps.LatLng(userSavedLatitude, userSavedLongitude);
        var animation = (markerAnimation !== '') ? markerAnimation : null;

        var marker = new google.maps.Marker({
            map: map,
            position: latlng
        });

        // Marker Animation
        if (animation == 'DROP') {
            marker.setAnimation(google.maps.Animation.DROP);
        }

        if (animation == 'BOUNCE') {
            marker.setAnimation(google.maps.Animation.BOUNCE);
        }
    }else {
        var marker = new google.maps.Marker({
            map: map,
            anchorPoint: new google.maps.Point(0, -29)
        });
    }


    autocomplete.addListener('place_changed', function() {
        infowindow.close();
        marker.setVisible(false);
        var place = autocomplete.getPlace();
        if (!place.geometry) {
            // User entered the name of a Place that was not suggested and
            // pressed the Enter key, or the Place Details request failed.
            window.alert("No details available for input: '" + place.name + "'");
            return;
        }

        // If the place has a geometry, then present it on a map.
        if (place.geometry.viewport) {
            map.fitBounds(place.geometry.viewport);
        } else {
            map.setCenter(place.geometry.location);
            map.setZoom(17);  // Why 17? Because it looks good.
        }
        marker.setPosition(place.geometry.location);
        marker.setVisible(true);


        var address = '';
        if (place.address_components) {
            address = [
                (place.address_components[0] && place.address_components[0].short_name || ''),
                (place.address_components[1] && place.address_components[1].short_name || ''),
                (place.address_components[2] && place.address_components[2].short_name || '')
            ].join(' ');
        }

        if (infowindowContent !== null) {
            if (infowindowContent.children !== undefined) {
                infowindowContent.children['place-icon'].src = place.icon;
                infowindowContent.children['place-name'].textContent = place.name;
                infowindowContent.children['place-address'].textContent = address;
            }

            infowindow.open(map, marker);
        }


        // Add latitude and longitude to the hidden field

        (function setLatitudeAndLongitude () {
            var latLong = marker.getPosition().toString();
            latLong= latLong.replace('(', ''); // Remove the preceding parenthesis
            latLong = latLong.replace(')', ''); // Remove the following parenthesis

            var latitude = latLong.split(',')[0];
            var longitude = latLong.split(',')[1];

            var latitudeInput = document.getElementById('latitude');
            latitudeInput.setAttribute('value', latitude)
            var longitudeInput = document.getElementById('longitude');
            longitudeInput.setAttribute('value', longitude);
        })();

    });

}

