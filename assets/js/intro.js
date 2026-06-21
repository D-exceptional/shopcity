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
      /*
      try {
        // Prepare data
        const payload = { email: email };

        // Send payment request to backend
        const result = await makeRequest(
          `${apiUrl}/user/login`,
          "POST",
          payload
        );

        if (result.status === 200 && result.message === "Login successful") {
          // Sync cart and wishlist to server
          await syncCart();
          await syncWishlist();
          $(".btn-login").text("Redirecting...");
          //Redirect to dashboard
          redirect(result.data.dashboard);
        } else {
          displayMessage(`${result.message}`, "info");
          $(".btn-login").text("Login").prop("disabled", false);
          //Update counter
          trials--;
          if (trials === 0) {
            //Show modal
            setTimeout(() => {
              displayMessage(
                "You have entered incorrect password 5 times",
                "warning"
              );
            }, 4000);
            setTimeout(function () {
              displayMessage(
                "It seems you forgot your password; reset it now",
                "info"
              );
              $(".password-section, .info-p").css({ display: "none" });
              $(".form-email").val("");
              $(".btn-login").text("Verify Email");
            }, 7000);
          }
        }
      } catch (err) {
        console.log("Network error: ", err);
        displayMessage("Network error occured", "warning");
        $(".btn-login").text("Login").prop("disabled", false);
        }
        */
    }
  }

  // -------------------------------------------------
  // Initiate Actions Based On Button Text
  // -------------------------------------------------
  $(".btn-notify").on("click", function () {
    notify();
  });
})(jQuery);
