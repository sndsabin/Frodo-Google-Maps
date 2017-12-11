jQuery(document).ready(function ($) {
    var pTag = $('.term-name-wrap > p');
    var slugDiv = $('.term-slug-wrap');
    var descriptionDiv = $('.term-description-wrap');
    var submitButton = $('#submit');
    var location = $('#location');
    var mapContainer = $('#map-container');
    var editPageDescription = $('.description');

    // Modify Content of Paragraph Tag
    pTag.text('Name of Marker');

    // Modify Content of Paragraph Tag in Edit page
    editPageDescription.text('Name of Marker');

    // Remove Slug div
    slugDiv.remove();

    // Remove Description div
    descriptionDiv.remove();

    // Event Listener
    submitButton.click(function (event) {
        event.preventDefault();

        if (location.val() == '') {
            mapContainer.addClass('form-invalid');
            location.focus();
            return false;
        }
    });


});