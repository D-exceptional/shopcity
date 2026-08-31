// ----------------------------------------------------
// Import Configs
// ----------------------------------------------------
import { makeRequest } from "../../../assets/js/core/index.js";
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

  const params = new URLSearchParams(window.location.search);
  const email = params.has("email") ? params.get("email") : null;

  // Toggle password visibility
  $(document).on("click", ".fas.fa-lock", function () {
    togglePasswordVisibility(this);
  });

  //Check All Inputs
  $(".form-control").each(function () {
    $(this).on("input blur", function () {
      validateInput(this);
      if ($(this).val() !== "") {
        $(this).css({ border: "none" });
        $(".btn-update").attr("disabled", false);
      } else {
        $(this).css({ border: "2px solid red" });
        $(".btn-update").attr("disabled", true);
      }
    });
  });

  // Attempt Login
  $(".btn-update").on("click", function () {
    update();
  });

  async function update() {
    const otp = $(".form-otp").val().trim();
    const password = $(".form-password").val().trim();
    const changedPassword = $(".form-repassword").val().trim();

    if (!otp || !password || !changedPassword) {
      displayMessage("Some fields are empty", "info");
      return;
    } else if (password !== changedPassword) {
      displayMessage("Passwords do not match", "info");
      return;
    } else {
      $(".btn-update").text("Updating password...").prop("disabled", true);
      // Prepare data
      const payload = { email: email, password: password, otp: otp };

      try {
        // Send payment request to backend
        const result = await makeRequest(
          `/user/password/reset`,
          "PUT",
          payload
        );

        if (
          result.status === 200 &&
          result.message === "Password reset successful"
        ) {
          $(".btn-update").text("Redirecting...");
          //Redirect to dashboard
          redirect("./");
        } else {
          displayMessage(`${result.message}`, "info");
          $(".btn-update").text("Update Password").prop("disabled", false);
        }
      } catch (err) {
        console.log("Network error: ", err);
        displayMessage("Network error occured", "warning");
        $(".btn-update").text("Update Password").prop("disabled", false);
      }
    }
  }

  function togglePasswordVisibility(iconElement) {
    const $icon = $(iconElement);
    const $container = $icon.closest(".input-group.mb-3"); // Wrap with $
    const $passwordInput = $container.find("input");

    const isHidden = $passwordInput.attr("type") === "password";
    $passwordInput.attr("type", isHidden ? "text" : "password");
  }
  
})(jQuery);
