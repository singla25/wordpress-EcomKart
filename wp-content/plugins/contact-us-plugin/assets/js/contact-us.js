jQuery(document).ready(function($) {
    $("#contact-form").on('submit', function(e) {
        e.preventDefault();

        var formArray = $(this).serializeArray();
        formArray.push({ name: "action", value: "submit_contact_form" });
        formArray.push({ name: "nonce", value: contact_ajax.nonce });

        $.ajax({
            url: contact_ajax.ajax_url,
            type: "POST",
            data: formArray,
            success: function(response) {
                if (response.success) {
                    alert(response.data.message);  // Show success message
                    window.location.href = contact_ajax.home_url;  // Redirect to home
                } else {
                    alert(response.data.message || 'Something went wrong.');
                }
            },
            error: function(err) {
                console.log("AJAX error:", err);
            }
        });
    });
});
