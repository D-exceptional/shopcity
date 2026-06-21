// ----------------------------------------------------
// Import Configs
// ----------------------------------------------------
import {
  makeRequest,
} from "../../../assets/js/core/index.js";
// ----------------------------------------------------
// Import UI Alerts
// ----------------------------------------------------
import { displayMessage } from "../../../assets/js/ui/index.js";
// ----------------------------------------------------
// Import Validations
// ----------------------------------------------------
import {
  validateInput,
} from "../../../assets/js/utils/index.js";
// ----------------------------------------------------
// Import Redirect
// ----------------------------------------------------
import { redirect } from "../../../assets/js/modules/index.js";

(function ($) {
  "use strict";

  //Check All Inputs
  $(".form-control").each(function () {
    $(this).on("input blur", function () {
      validateInput(this);
      if ($(this).val() !== "") {
        $(this).css({ border: "none" });
        $(".btn-verify").attr("disabled", false);
      } else {
        $(this).css({ border: "2px solid red" });
        $(".btn-verify").attr("disabled", true);
      }
    });
  });

  // Attempt Verification
  $(".btn-verify").on("click", function () {
    verify();
  });

  async function verify() {
    const email = $(".form-email").val().trim();

    if (!email) {
      displayMessage("Enter your email address", "info");
    } else {
      $(".btn-verify").text("Verifying...").prop("disabled", true);
      // Prepare data
      const payload = { email: email };

      try {
        // Send payment request to backend
        const result = await makeRequest(`/user/otp`, "POST", payload);

        if (
          result.status === 200 &&
          result.message === "OTP sent to your email"
        ) {
          $(".btn-verify").text("Redirecting...");
          //Redirect to dashboard
          redirect(`recover-password?email=${email}`);
        } else {
          displayMessage(`${result.message}`, "info");
          $(".btn-verify").text("Verify Email").prop("disabled", false);
        }
      } catch (err) {
        console.log("Network error: ", err);
        displayMessage("Network error occured", "warning");
        $(".btn-verify").text("Verify Email").prop("disabled", false);
      }
    }
  }

})(jQuery);
