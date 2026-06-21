// ----------------------------------------------------
// Import Configs
// ----------------------------------------------------
import { MAX_TRIALS, makeRequest } from "./core/index.js";
// ----------------------------------------------------
// Import UI Alerts
// ----------------------------------------------------
import { displayMessage } from "./ui/index.js";
// ----------------------------------------------------
// Import Validations
// ----------------------------------------------------
import { validateInput, togglePasswordVisibility } from "./utils/index.js";
// ----------------------------------------------------
// Import Cart And Wishlist Syncer
// ----------------------------------------------------
import { syncCart, syncWishlist, redirect } from "./modules/index.js";
// ----------------------------------------------------
// Import Push Client
// ----------------------------------------------------
import PushClient from "./push/PushClient.js";

(function ($) {
  ("use strict");

  let trials = MAX_TRIALS;

  $(".otp-section, .new-password-section").hide();

  $(".form-email").val("");
  $(".form-password").val("");

  // Toggle password visibility
  $(".password-section i").on("click", function () {
    togglePasswordVisibility($(this), "password");
  });

  $(".new-password-section i").on("click", function () {
    togglePasswordVisibility($(this), "re-password");
  });

  //Check All Inputs
  $(".form-control").each(function () {
    $(this).on("blur", function () {
      validateInput(this);
    });
  });

  //Quick password reset
  $(".reset").on("click", function () {
    $(".password-section, .info-p").hide();
    $(".form-email").val("");
    $(".btn-login").text("Verify Email");
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
          // Sync cart and wishlist to server
          if (
            ["customer", "Customer", "CUSTOMER"].includes(
              result.data.connection
            )
          ) {
            await syncCart();
            await syncWishlist();
          }
          // Sync push notification subscription
          await PushClient.sync(true);
          $(".btn-login").text("Redirecting...");
          //Redirect to dashboard
          setTimeout(() => {
            redirect(result.data.dashboard);
          }, 2000);
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
    }
  }

  // -------------------------------------------------
  //  Get OTP For Password Reset
  // -------------------------------------------------
  async function getOTP(email) {
    // Prepare data
    const payload = { email: email };
    try {
      // Send payment request to backend
      const result = await makeRequest(`/user/otp`, "POST", payload);

      if (
        result.status === 200 &&
        result.message === "OTP sent to your email"
      ) {
        $(
          ".email-section, .password-section, .new-password-section, .otp-section"
        ).show();
        $(".info-p").hide();
        $(".form-email").val(email);
        $(".btn-login").text("Update Password").prop("disabled", false);
      } else {
        displayMessage("Could not get otp", "warning");
        $(".btn-login").text("Verify Email").prop("disabled", false);
      }
    } catch (err) {
      console.log(err);
      displayMessage("Network error occured", "warning");
      $(".btn-login").text("Verify Email").prop("disabled", false);
    }
  }

  // -------------------------------------------------
  // Verify Email Address
  // -------------------------------------------------
  function verify() {
    const email = validateInput($(".form-email"));
    if (!email) {
      return displayMessage("Enter your email", "info");
    } else {
      $(".btn-login").text("Verifying...").prop("disabled", true);
      getOTP(email);
    }
  }

  // -------------------------------------------------
  // Update Password
  // -------------------------------------------------
  async function update() {
    const email = $(".form-email").val();
    const password = validateInput($(".form-password"));
    const changedPassword = validateInput($(".form-password-new"));
    const otp = validateInput($(".form-otp"));

    if (!otp) {
      return displayMessage(
        "Enter the OTP sent to your email address",
        "warning"
      );
    } else if (!password) {
      return displayMessage("Type a password", "warning");
    } else if (!changedPassword) {
      return displayMessage("Re-type password", "warning");
    }
    if (password !== changedPassword) {
      return displayMessage("Passwords do not match", "warning");
    } else {
      $(".btn-login").text("Updating password...").prop("disabled", true);

      // Prepare data
      const payload = {
        email: email,
        password: changedPassword,
        otp: otp,
      };

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
          setTimeout(function () {
            $(".password-section, .email-section, .info-p").show();
            $(".new-password-section, .otp-section").hide();
            $(".btn-login").text("Login");
          }, 2000);
        } else {
          displayMessage("Check your email or password", "info");
          $(".btn-login").text("Update password");
        }

        $(".btn-login").prop("disabled", false);
      } catch (err) {
        displayMessage("Network error occured", "warning");
        $(".btn-login").text("Update password").prop("disabled", false);
      }
    }
  }

  // -------------------------------------------------
  // Initiate Actions Based On Button Text
  // -------------------------------------------------
  $(".form-login").on("submit", function (e) {
    e.preventDefault();
    const loginButtonText = $(".btn-login").text().trim();
    switch (loginButtonText) {
      case "Login":
        login();
        break;
      case "Verify Email":
        verify();
        break;
      case "Update Password":
        update();
        break;
    }
  });
})(jQuery);
