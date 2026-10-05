// ----------------------------------------------------
// Import Configs
// ----------------------------------------------------
import { makeRequest } from "../../js/shared/core/index.js";
// ----------------------------------------------------
// Import UI Alerts
// ----------------------------------------------------
import { displayMessage } from "../../js/shared/ui/index.js";
// ----------------------------------------------------
// Import Validations
// ----------------------------------------------------
import {
  uploadToCloudinary,
} from "../../js/shared/utils/index.js";
// ----------------------------------------------------
// Import Configs
// ----------------------------------------------------
import {
  SUPPORTED_IMAGE_EXTENSTIONS,
  MAX_IMAGE_SIZE_MB,
} from "../../js/shared/core/index.js";

(async function ($) {
  ("use strict");

  const params = new URLSearchParams(window.location.search);
  const storeId =
    params.has("id") && !isNaN(params.get("id"))
      ? parseInt(params.get("id"))
      : null;

  //Initialize Profile Submit
  $(".file-click").on("click", function () {
    $(".form-file").click();
  });

  $(".form-file").on("change", async function () {
      //previewFile(this, "Image Only");
      await updateProfile();
  });
    
    async function updateProfile() {
      const fileInput = $(".form-file")[0];
      const file = fileInput?.files?.[0];
      const extension = file.name.split(".").pop().toLowerCase();
      const sizeInMB = file.size / (1024 * 1024); // Convert bytes to MB

      if (!file) {
        displayMessage("Upload a valid image", "info");
        return;
      }

      if (!SUPPORTED_IMAGE_EXTENSTIONS.includes(extension)) {
            displayMessage("Select a valid image file (JPG, JPEG, PNG)", "warning");
            return;
        }
        
        if (sizeInMB > MAX_IMAGE_SIZE_MB) {
           displayMessage(
             `Image file too large. Max allowed is ${MAX_IMAGE_SIZE_MB} MB`,
             `warning`
           );
           return;
        }
        
        const verify = confirm("Are you sure to update avatar?");
        if (verify) {
            $(".file-click").text("Processing...").prop("disabled", true);

            try {
              const fileUrl = await uploadToCloudinary(file, "shops");

              const payload = {
                url: fileUrl,
              };

              const result = await makeRequest(
                `/store/${storeId}/avatar`,
                "PUT",
                payload
              );

              if (
                result.status === 200 &&
                result.message === "Avatar updated successfully"
              ) {
                displayMessage(result.message, "success");
                $(".file-click").text("Avatar updated").prop("disabled", true);
                setTimeout(() => {
                  window.location.reload();
                }, 1200);
              } else {
                displayMessage(result.message, "info");
                $(".file-click").text("Update Avatar").prop("disabled", false);
              }
            } catch (err) {
              displayMessage(`Network error occurred: ${err}`, "error");
              $(".file-click").text("Update Avatar").prop("disabled", false);
            }
        }
    }

  //Toggle tab view
  $(".nav-item a").each(function () {
    $(this).on("click", function () {
      const linkText = $(this).text().trim();

      switch (linkText) {

        case "Details":
          $("#details").addClass('d-block');
          $("#bank, #socials, #security").removeClass('d-block');
          break;
        
        case "Bank":
          $("#bank").addClass('d-block');
          $("#details, #socials, #security").removeClass('d-block');
          break;
        
        case "Socials":
          $("#socials").addClass('d-block');
          $("#details, #bank, #security").removeClass('d-block');
          break;
        
        case "Security":
          $("#security").addClass('d-block');
          $("#details, #bank, #socials").removeClass('d-block');
          break;
      }

    });
  });
})(jQuery);