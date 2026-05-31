/**
 * WP Send Message - Admin JavaScript
 *
 * Handles AJAX form submission for plugin admin pages.
 * Depends on: jQuery, UIkit (for notifications)
 *
 * Wrapped in an IIFE passing jQuery as $ to avoid global namespace conflicts.
 */
(function ($) {

    // Cache reference to the plugin admin page wrapper (reserved for future scoped operations).
    const container = $(".wp-send-message-page")

    /**
     * Intercept submit events on forms marked with data-ajax-form.
     * Prevents default form POST navigation and sends data via jQuery.ajax + FormData.
     */
    $('form[data-ajax-form]').on('submit', function(e){

        // Stop the browser from performing a full page POST reload.
        e.preventDefault()

        // Reference to the submitted form element.
        const form = $(this)

        // Read form action URL (admin-ajax.php) and HTTP method from HTML attributes.
        const url = form.attr('action')
        const type = form.attr('method')

        // FormData automatically collects all named inputs including hidden fields and nonce.
        const data = new FormData(form.get(0))

        // Send asynchronous request to WordPress admin-ajax.php endpoint.
        $.ajax({
            url: url,           // Typically: /wp-admin/admin-ajax.php
            type: type,         // 'post'
            data: data,         // Multipart form payload (action, nonce, name, phone, email)
            processData: false, // Required for FormData — do not convert to query string
            contentType: false, // Let browser set multipart/form-data boundary automatically

            /**
             * Called when server returns HTTP 200 with JSON body.
             * WordPress wp_send_json_* wraps payload as { success: bool, data: ... }.
             */
            success: function(response) {

                // On validation failure, display each error message as a UIkit toast notification.
                if(!response.success){
                    response.data.forEach(element => UIkit.notification(element,{pos: 'bottom-right'}));
                    return;
                }

                // TODO: Handle success — close modal, show success notification, refresh contact table.
            },

            /**
             * Called on HTTP errors (4xx/5xx) or network failure.
             */
            error: function(xhr, status, error) {
                console.log(xhr, status, error)
            }
        })
    })

})(jQuery)
