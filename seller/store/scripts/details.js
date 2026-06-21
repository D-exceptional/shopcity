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

(async function ($) {
  ("use strict");

  const params = new URLSearchParams(window.location.search);
  const storeId =
    params.has("id") && !isNaN(params.get("id"))
      ? parseInt(params.get("id"))
      : null;

  const initialName = $(".form-name").val().trim();
  const initialDescription = $(".form-description").val().trim();
  const initialDelivery = $(".form-delivery").val().trim();

  $(".form-text").on("input blur", function () {
    validateInput(this);
  });

  $(".btn-details").on("click", async function () {
    await updateDetails();
  });

  async function updateDetails() {
    const currentName = $(".form-name").val().trim();
   const currentDescription = $(".form-description").val().trim();
   const currentDelivery = $(".form-delivery").val().trim();

    if (
      currentDescription === initialDescription &&
      currentDelivery === initialDelivery &&
      currentName === initialName
    ) {
      displayMessage("No changes made", "info");
      return;
    }

    const inputs = {
      name: currentName,
      description: currentDescription,
      delivery: currentDelivery,
    };

    if (Object.values(inputs).some((val) => val === "")) {
      displayMessage("Some fields are empty", "info");
      return;
    }

    $(".btn-details").prop("disabled", true).text("Processing...");

    try {
      const payload = {
        ...inputs,
        id: storeId,
      };

      const result = await makeRequest(`/store/details`, "PUT", payload);

      if (
        result.status === 200 &&
        result.message === "Details updated successfully"
      ) {
        displayMessage(result.message, "success");
        $(".btn-details").text("Update").prop("disabled", false);
      } else {
        displayMessage(result.message, "info");
        $(".btn-details").text("Update").prop("disabled", false);
      }
    } catch (err) {
      displayMessage(`Network error occurred: ${err}`, "error");
      $(".btn-details").text("Update").prop("disabled", false);
    }
  }
})(jQuery);
