jQuery(document).ready(function ($) {
  // Perform AJAX request
  $.ajax({
    url: ajax_object.ajax_url,
    type: "POST",
    data: {
      action: "load_simple_test",
    },
    beforeSend: function () {
      // Show loading indicator inside the container before sending request
      $("#simple-test-container #loading-indicator").show()
    },
    success: function (response) {
      if (response.success) {
        // Insert the content into the target element
        $("#simple-test-container").html(response.data)
        $("#gaimpress-points-displayer").append(response.data)
      } else {
        console.log("Error: " + response.data)
      }
    },
    error: function (xhr, status, error) {
      console.log("AJAX Error: " + error)
    },
    complete: function () {
      // Hide loading indicator after request completes
      $("#simple-test-container #loading-indicator").hide()
    },
  })
})
