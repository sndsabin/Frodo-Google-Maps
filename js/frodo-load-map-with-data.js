var map;
var infoWindow;
var width;
var widthMetric;
var height;
var heightMetric;
var zoomLevel;
var draggable;
var scroll;
var mapType;

var postData = JSON.parse(FRODO_GOOGLE_MAP_POST_DATA.data);
var markersData = JSON.parse(FRODO_GOOGLE_MAP_MARKERS_DATA.data);

function initMap() {

    // Parses postData
    retrievePostData();

    // Apply Style to Map Container
    applyStyles(width, height);

    var mapOptions = {
        center: new google.maps.LatLng(28.3949, 84.1240),
        zoom: zoomLevel,
        mapTypeId: mapType,
        draggable: (draggable) ? true : false,
        scrollwheel: (scroll) ? true :false
    };

    map = new google.maps.Map(document.getElementById('map'), mapOptions);

    // new Instance of InfoWindow
    infoWindow = new google.maps.InfoWindow();

    // Event Listener
    google.maps.event.addListener(map, 'click', function() {
        infoWindow.close();
    });


    displayMarkers();
}

// Retrieves Map Properties
function retrievePostData() {
    if ( postData ) {

        width = (postData.width !== '') ? parseInt(postData.width) : '100%';
        widthMetric = (postData.width_metric !== '') ? postData.width_metric : '%';
        height = (postData.height !== '') ? parseInt(postData.height) : '500px';
        heightMetric = (postData.height_metric !== '') ? postData.height_metric : 'px';
        zoomLevel = parseInt((postData.zoom_level !== '') ? postData.zoom_level : 7);
        draggable = (postData.draggable !== '') ? parseInt(postData.draggable) : 1;
        scroll = (postData.scroll !== '') ? parseInt(postData.scroll) : 1;
        mapType = (postData.map_type !== '') ? postData.map_type : 'roadmap' ;
    }
}

// Shows Markers
function displayMarkers(){

    // Sets the bounds according to latitude and longitude
    var bounds = new google.maps.LatLngBounds();

    if (markersData.length >= 1) {
        // loop through each of the data
        for (var i = 0; i < markersData.length; i++){

            var latlng = new google.maps.LatLng(markersData[i].latitude, markersData[i].longitude);
            var location = markersData[i].location;
            var description = markersData[i].marker_description;
            var isInfoWindowOpen = markersData[i].is_info_window_open;
            var animation = (markersData[i].marker_animation !== '') ? markersData[i].marker_animation : null;

            plotMarkers(latlng, location, description, animation, isInfoWindowOpen);

            // extends the bound to position
            bounds.extend(latlng);
        }
    }


    // Set the map bounds
    // if enabled setting zoom level doesn't work
    //map.fitBounds(bounds);
}

// Plots the marker with specified details
function plotMarkers(latlng, location, description, animation, isInfoWindowOpen){

    var marker = new google.maps.Marker({
        map: map,
        position: latlng,
        title: location
    });

    // Marker Animation

    if (animation == 'DROP') {
        marker.setAnimation(google.maps.Animation.DROP);
    }

    if (animation == 'BOUNCE') {
        marker.setAnimation(google.maps.Animation.BOUNCE);
    }

   // Event Listener
    var infoWindowContent = '<div id="info-window-container">' +
        '<div class="info-window-content">' + description + '<br />' +
        '</div>';

    if (isInfoWindowOpen == 'Yes') {
        // Set content for info window
        infoWindow.setContent(infoWindowContent);
        // Open Info Window
        infoWindow.open(map, marker);
    }

    // Event Listener
    google.maps.event.addListener(marker, 'click', function() {

        var infoWindowContent = '<div id="info-window-container">' +
            '<div class="info-window-content">' + description + '<br />' +
            '</div>';

        // Set content for info window
        infoWindow.setContent(infoWindowContent);

        infoWindow.open(map, marker);
    });



}

// Set Map Width and Height
function applyStyles (width, height) {
    var mapDiv = document.getElementById('map');

    mapDiv.style.width = width + widthMetric;
    mapDiv.style.height = height + heightMetric;
}