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
  previewFile,
  uploadToCloudinary,
} from "../../../assets/js/utils/index.js";

(async function ($) {
  ("use strict");

  //Initialize Profile Submit
  $(".file-click").on("click", function () {
    $(".form-file").click();
  });

  $(".form-file").on("change", function () {
    updateProfile(this);
  });
    
  async function updateProfile(input) {
    // Preview and validate file
    const check = previewFile(input, "image", null);

    // Notify user
    displayMessage(`${check.message}`, `${check.ok ? "success" : "info"}`);

    // Ensure full validation
    if (!check.ok) {
      return;
    }
      
    if (confirm("Are you sure to update profile?")) {
      $(".file-click").text("Processing...").prop("disabled", true);

      try {
        const fileUrl = await uploadToCloudinary(check.file, "uploads");

        const payload = {
          avatar: fileUrl,
        };

        const result = await makeRequest(
          `/user/profile`,
          "PUT",
          payload
        );

        if (
          result.status === 200 &&
          result.message === "Profile updated successfully"
        ) {
          displayMessage(result.message, "success");
          $(".file-click").text("Profile updated").prop("disabled", true);
          setTimeout(() => {
            window.location.reload();
          }, 1200);
        } else {
          displayMessage(result.message, "info");
          $(".file-click").text("Update Profile").prop("disabled", false);
        }
      } catch (err) {
        displayMessage(`Network error occurred: ${err}`, "error");
        $(".file-click").text("Update Profile").prop("disabled", false);
      }
    }
  }

  //Toggle tab view
  $(".nav-item a").each(function () {
    $(this).on("click", function () {
      const linkText = $(this).text();
      switch (linkText) {
        case "Details":
          $("#details").css({ display: "block" });
          $("#security").css({ display: "none" });
          break;
        case "Security":
          $("#security").css({ display: "block" });
          $("#details").css({ display: "none" });
          break;
      }
    });
  });
})(jQuery);