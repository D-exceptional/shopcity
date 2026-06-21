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
    "use strict";

  $(".form-security").on("input blur", function () {
    validateInput(this);
  });

  $(".btn-password").on("click", async function () {
    await updatePassword();
  });

  async function updatePassword() {
      const inputs = {
        password: $(".form-password").val().trim(),
        repassword: $(".form-repassword").val().trim(),
      };

    if (Object.values(inputs).some((val) => val === "")) {
      displayMessage("Some fields are empty", "info");
      return;
    }

    $(".btn-password").prop("disabled", true).val("Processing...");

    try {
      const payload = {
        ...inputs,
      };

      const result = await makeRequest(
        `/user/password/change`,
        "PUT",
        payload
      );

      if (
        result.status === 200 &&
        result.message === "Password changed successfully"
      ) {
        displayMessage(result.message, "success");
        $(".btn-password").val("Update").prop("disabled", false);
      } else {
        displayMessage(result.message, "info");
        $(".btn-password").val("Update").prop("disabled", false);
      }
    } catch (err) {
      displayMessage(`Network error occurred: ${err}`, "error");
      $(".btn-password").val("Update").prop("disabled", false);
    }
  }
})(jQuery);


