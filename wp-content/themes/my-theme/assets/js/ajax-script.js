// Filter
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
        { name: "nonce", value: ajax_ajax.nonce },
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
      formArray.push({ name: "nonce", value: ajax_ajax.nonce });

      submit_form(formArray, $form);
    });
  });

  // --- AJAX Submit Function ---
  function submit_form(formArray, $form) {
    $.ajax({
      url: ajax_ajax.ajax_url, // WordPress will handle this URL (/wp-admin/admin-ajax.php)
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

// Add a Book in Vendor
jQuery(document).ready(function($){

  $(document).on("submit", "#addBook-vendor-form", function(e) {
    e.preventDefault();

    let formData = new FormData(this);  // Automatically captures all fields + files
    formData.append("action", "addBook_vendor");
    formData.append("nonce", ajax_ajax.nonce);

    $.ajax({
      url: ajax_ajax.ajax_url,
      type: "POST",
      data: formData,
      processData: false,   // Important for FormData
      contentType: false,   // Important for FormData
      success: function(response) {
        console.log("Success message:", response);

        if (response.success) {
          alert(response.data.message);
          window.location.href = response.data.url;  // Redirect to vendor page
        } else {
          alert("❌ " + response.data.message);
        }
      },
      error: function(err) {
        console.log("AJAX error:", err);
      }
    });
  });
});

// Edit Book in Vendor
jQuery(document).ready(function($){
  var modal = $("#edit-page");
  var btn = $("#edit-button");
  var span = $(".close");

  btn.on("click", function(e) {
    console.log('hi');
    modal.show();
  });

  span.on("click", function(e) {
    modal.hide();
  });

  // When the user clicks anywhere outside of the modal, close it
  $(window).on('click', function(e) {
    if (e.target == modal[0]) {
      modal.hide();
    }
  });

  $('.edit-button').each(
    $(document).on('click', '[data-type="edit"]', function(e) {
      e.preventDefault();

      let id = $(this).data('id');
      console.log(id);

      $.ajax({
        url: ajax_ajax.ajax_url, // WordPress will handle this URL (/wp-admin/admin-ajax.php)
        type: "POST",              // Send data via POST request
        data: {
          action: "edit_book",  // must match your PHP hook
          id: id,               // send ID
          nonce: ajax_ajax.nonce // optional: security
        },
        success: function(response) {
          console.log("Server says:", response);
          if (response.status === "success") {
            $('#edit-form').html(response.html);
            modal.show();
          }
        },
        error: function(err) {
          console.log("AJAX error:", err);
        }
      });
    })
  )

  $(document).on("submit", "#edit-form", function(e) {
    e.preventDefault();

    let formData = new FormData(this);
    formData.append("action", "editBook_vendor");
    formData.append("nonce", ajax_ajax.nonce);

    $.ajax({
      url: ajax_ajax.ajax_url,
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function(response) {
        if (response.success) {
          alert(response.data.message);
          modal.hide();
          window.location.href = response.data.url; // use URL from PHP
        } else {
          alert("❌ " + response.data.message);
        }
      },
      error: function(err) {
        console.log("AJAX error:", err);
      }
    });
  });
});

// Delete a Book
jQuery(document).ready(function($){
  
  $(document).on("submit", "#delete-book", function(e) {
    e.preventDefault();

    let formData = new FormData(this);
    formData.append("action", "delete_book");
    formData.append("nonce", ajax_ajax.nonce);

    $.ajax({
      url: ajax_ajax.ajax_url,
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function(response) {
        console.log(response);  // Debug the response

        if (response.success) {
          alert(response.data.message);
          window.location.href = response.data.url;
        } else {
          alert(response.data.message);
        }
      },
      error: function(err) {
        console.log("AJAX error:", err);
      }
    });
  });
});






// Contact Form
jQuery(document).ready(function($) {

  $("#contact-form").on('submit', function(e) {
    e.preventDefault()
    
    var formArray = $(this).serializeArray();
    formArray.push({ name: "action", value: "contact_form" });
    formArray.push({ name: "nonce", value: ajax_ajax.nonce });

    console.log(formArray);

    $.ajax({
      url: ajax_ajax.ajax_url,   
      type: "POST",              
      data: formArray,
      success: function(response) {
        console.log("Server says: ", response);

        if (response.success) {
            alert(response.data.message);
            window.location.href = ajax_ajax.home_url;  // Redirect after success
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




