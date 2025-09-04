jQuery(document).ready(function($) {

  // Loop each form (in case you add multiple later)
  $(".filter-form").each(function(index, form) {
    var $form = $(form);
    var $button = $form.find("#filter-button");

    console.log($form);
    console.log($button);

    // --- On Page Load ---
    var ptype = $form.find('input[name="ptype"]').val();
    if (ptype) {
      var formArray = [
        { name: "action", value: "filter" },
        { name: "nonce", value: filter_ajax.nonce },
        { name: "ptype", value: ptype }
      ];
      submit_form(formArray, $form);
    }

    // --- On Apply Filter Button Click ---
    $button.on("click", function(e) {
      e.preventDefault();

      // Serialize the entire form into an array Example: [ {name: "category[]", value: "fiction"}, {name: "tag[]", value: "harry"} ]
      var formArray = $form.serializeArray();
      formArray.push({ name: "action", value: "filter" });
      formArray.push({ name: "nonce", value: filter_ajax.nonce });

      submit_form(formArray, $form);
    });
  });

  // --- AJAX Submit Function ---
  function submit_form(formArray, $form) {
    $.ajax({
      url: filter_ajax.ajax_url, // WordPress will handle this URL (/wp-admin/admin-ajax.php)
      type: "POST",              // Send data via POST request
      data: formArray,           // Pass the serialized form + extra data directly
      success: function(response) {
        console.log("Server says:", response);
        if (response.status === "success") {
          var ptype = $form.find('input[name="ptype"]').val();
          var container_id = "#results-container-" + ptype;

          // insert returned HTML into that container
          $(container_id).html(response.html);
        }
      },
      error: function(err) {
        console.log("AJAX error:", err);
      }
    });
  }
});






