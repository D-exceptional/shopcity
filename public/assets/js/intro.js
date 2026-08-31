// ----------------------------------------------------
// Import Configs
// ----------------------------------------------------
import { apiUrl, makeRequest } from "./core/index.js";
// ----------------------------------------------------
// Import UI Alerts
// ----------------------------------------------------
import { displayMessage } from "./ui/index.js";
// ----------------------------------------------------
// Import Validations
// ----------------------------------------------------
import { validateInput } from "./utils/index.js";

(function ($) {
  "use strict";

  $(".form-email").val("");

  //Check All Inputs
  $(".form-control").each(function () {
    $(this).on("input blur", function () {
      validateInput(this);
    });
  });

  // -------------------------------------------------
  //  Apply For Notification
  // -------------------------------------------------
  async function notify() {
    const email = validateInput($(".form-email"));

    if (!email) {
        displayMessage("Enter your email address", "info");
        return;
    } else {
      $(".btn-notify").text("Submitting...").prop("disabled", true);

      setTimeout(() => {
        $(".form-email").val("");
        $(".btn-notify").text("Notify").prop("disabled", false);
        
        displayMessage(
          "Thanks for subscribing. We'll keep you updated",
          "success"
        );
      }, 2000);
    }
  }

  // -------------------------------------------------
  // Initiate Actions Based On Button Text
  // -------------------------------------------------
  $(".btn-notify").on("click", function () {
    notify();
  });
})(jQuery);
