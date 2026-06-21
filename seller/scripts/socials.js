// ----------------------------------------------------
// Import Configs
// ----------------------------------------------------
import { makeRequest } from "../../assets/js/core/index.js";
// ----------------------------------------------------
// Import UI Alerts
// ----------------------------------------------------
import { displayMessage } from "../../assets/js/ui/index.js";
// ----------------------------------------------------
// Import Validations
// ----------------------------------------------------
import {
  validateInput,
} from "../../assets/js/utils/index.js";

(async function ($) {
  ("use strict");

  const initialFacebook = $(".form-facebook").val();
  const initialInstagram = $(".form-instagram").val();
  const initialTiktok = $(".form-tiktok").val();
  const initialTwitter = $(".form-twitter").val();

  /*
    $(".form-social").on("input", function (e) {
        const input = $(this);
        const value = input.val();

        if (/\d/.test(value)) {
        // Remove digits
        input.val(value.replace(/\d+/g, ""));
        displayMessage(
            "Numbers are not allowed in name or description fields",
            "info"
        );
        }
    });
    

  $(".form-social").on("paste", function (e) {
    e.preventDefault();
    const paste = (
      e.originalEvent.clipboardData || window.clipboardData
    ).getData("text");
    const cleanPaste = paste.replace(/\d+/g, "");
    document.execCommand("insertText", false, cleanPaste);
    if (paste !== cleanPaste) {
      displayMessage("Numbers removed from pasted text", "info");
    }
  });
  */

  $(".form-social").on("input blur", function () {
    validateInput(this);
  });

  $(".btn-socials").on("click", async function () {
    await updateSocials();
  });

  async function updateSocials() {
    const currentFacebook = $(".form-facebook").val();
    const currentInstagram = $(".form-instagram").val();
    const currentTiktok = $(".form-tiktok").val();
    const currentTwitter = $(".form-twitter").val();

    if (
      currentFacebook === initialFacebook &&
      currentInstagram === initialInstagram &&
      currentTiktok === initialTiktok &&
      currentTwitter === initialTwitter
    ) {
      displayMessage("No changes made", "info");
      return;
    }

    const inputs = {
      facebook: currentFacebook,
      instagram: currentInstagram,
      tiktok: currentTiktok,
      twitter: currentTwitter,
    };

    if (Object.values(inputs).some((val) => val === "")) {
      displayMessage("Some fields are empty", "info");
      return;
    }

    $(".btn-socials").prop("disabled", true).val("Processing...");

    try {
      const payload = {
        ...inputs,
      };

      const result = await makeRequest(`/user/social`, "PUT", payload);

      if (
        result.status === 200 &&
        result.message === "Socials updated successfully"
      ) {
        displayMessage(result.message, "success");
        $(".btn-socials").val("Update").prop("disabled", false);
      } else {
        displayMessage(result.message, "info");
        $(".btn-socials").val("Update").prop("disabled", false);
      }
    } catch (err) {
      displayMessage(`Network error occurred: ${err}`, "error");
      $(".btn-socials").val("Update").prop("disabled", false);
    }
  }
})(jQuery);
