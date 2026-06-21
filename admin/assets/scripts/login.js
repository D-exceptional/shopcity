// ----------------------------------------------------
// Import Configs
// ----------------------------------------------------
import {
  makeRequest,
  MAX_TRIALS,
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
// ----------------------------------------------------
// Import Push Client
// ----------------------------------------------------
import PushClient from "../../../assets/js/push/PushClient.js";

(function ($) {
  "use strict";

  let trials = MAX_TRIALS;

  $(".form-email").val("").focus();
  $(".form-password").val("");

  // Toggle password visibility
  $(".psw-span").on("click", function () {
    togglePasswordVisibility();
  });

  //Check All Inputs
  $(".form-control").each(function () {
    $(this).on("input blur", function () {
      validateInput(this);
      if ($(this).val() !== "") {
        $(this).css({ border: "none" });
        $(".btn-login").attr("disabled", false);
      } else {
        $(this).css({ border: "2px solid red" });
        $(".btn-login").attr("disabled", true);
      }
    });
  });

  // Attempt Login
  $(".btn-login").on("click", function () {
    login();
  });

  // -------------------------------------------------
  //  Login To Dashboard
  // -------------------------------------------------
  async function login() {
    const email = validateInput($(".form-email"));
    const password = validateInput($(".form-password"));

    if (!email || !password) {
      displayMessage("Some fields are empty", "info");
    } else {
      $(".btn-login").text("Logging in...").prop("disabled", true);
      // Prepare data
      const payload = { email: email, password: password };

      try {
        // Send payment request to backend
        const result = await makeRequest(
          `/user/login`,
          "POST",
          payload
        );

        if (result.status === 200 && result.message === "Login successful") {
          $(".btn-login").text("Redirecting...");
          // Sync push notification subscription
          await PushClient.sync(true);
          //Redirect to dashboard
          setTimeout(() => {
            redirect(result.data.dashboard);
          }, 2000);
        } else {
          displayMessage(`${result.message}`, "info");
          $(".btn-login").text("Sign In").prop("disabled", false);
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
            }, 7000);
          }
        }
      } catch (err) {
        console.log("Network error: ", err);
        displayMessage("Network error occured", "warning");
        $(".btn-login").text("Sign In").prop("disabled", false);
      }
    }
  }
    
  function togglePasswordVisibility() {
    const $passwordInput = $(".form-password");
    const isHidden = $passwordInput.attr("type") === "password";
    $passwordInput.attr("type", isHidden ? "text" : "password");
  }

})(jQuery);