jQuery(document).ready(function ($) {
    var pageTitle = $('div h1');
    var saveButton = $('#submit-google-api-key');


    saveButton.click(function (event) {
        event.preventDefault();
        var apiKey = $('#apiKey').val();

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'save_google_map_api_key',
                apiKey: apiKey,
                security: FRODO_GOOGLE_MAP.security
            },
            success: function (response) {
                $('div#message').remove();

                if(response.success == true) {

                    pageTitle.after('' +
                        '<div id="message" class="updated">' +
                        '<p>' + FRODO_GOOGLE_MAP.success + '</p>' +
                        '</div>');
                } else {
                    pageTitle.after('' +
                        '<div id="message" class="error">' +
                        '<p>' + FRODO_GOOGLE_MAP.error + '</p>' +
                        '</div>' );
                }

            },
            error: function (response) {
                $('div#message').remove();
                pageTitle.after( '' +
                    '<div id="message" class="error">' +
                    '<p>' + FRODO_GOOGLE_MAP.error + '</p>' +
                    '</div>' );

            }
        });
    });
});