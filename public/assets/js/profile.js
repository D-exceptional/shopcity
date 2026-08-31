// ----------------------------------------------------
// Import Configs
// ----------------------------------------------------
import {  makeRequest } from "./core/index.js";
// ----------------------------------------------------
// Import UI Alerts
// ----------------------------------------------------
import { displayMessage } from "./ui/index.js";
// ----------------------------------------------------
// Import Validations
// ----------------------------------------------------
import { validateInput } from "./utils/index.js";

(function ($) {
  ("use strict");

  // -------------------------------------------------
  // Get Current Location Details
  // -------------------------------------------------
  const currentAddress = $(".form-address").val();
  const currentCity = $(".form-city").val();
  const currentCode = $(".form-code").val();

  // -------------------------------------------------
  // Check All Inputs
  // -------------------------------------------------
  $(".form-control").each(function () {
    $(this).on("input blur", function () {
      validateInput(this);
    });
  });

  // -------------------------------------------------
  //  Update Details
  // -------------------------------------------------
async function updateDetails() {
  // Get latest details
  const address = validateInput($(".form-address"));
  const city = validateInput($(".form-city"));
  const code = validateInput($(".form-code"));

  // Check for any changes
  if (
    address === currentAddress &&
    city === currentCity &&
    code === currentCode
  ) {
    displayMessage("No changes made", "info");
    return;
  }

  // Flag to detect invalid fields
  let invalidFound = false;

  // Check all required inputs at once
  $(".form-location").each(function () {
    const name = $(this).data("name") || "field";
    const value = validateInput(this) || "";

    // Check for placeholder-like text (e.g., "Enter your city")
    const lowerValue = value.toLowerCase().trim();

    const invalidWords = ["enter", "your", "type", "input"];
    const containsInvalidWord = invalidWords.some((word) =>
      lowerValue.includes(word)
    );

    if (!value || containsInvalidWord || value.length < 2) {
      displayMessage(`Enter a valid value for: ${name}`, "warning");
      $(this).addClass("is-invalid"); // optional: red border
      invalidFound = true;
    } else {
      $(this).removeClass("is-invalid");
    }
  });

  if (invalidFound) {
    return; // stop further processing
  }

  // Final empty check
  if (!address || !city || !code) {
    displayMessage("Some fields are empty", "info");
    return;
  }

  // Continue update
  $(".btn-update").text("Updating details...").prop("disabled", true);

  const payload = {
    address: address,
    city: city,
    code: parseInt(code),
  };

  try {
    const result = await makeRequest(`/user/billing`, "PUT", payload);

    if (
      result.status === 200 &&
      ["Details created successfully", "Details updated successfully"].includes(
        result.message
      )
    ) {
      displayMessage(result.message, "success");
    } else {
      displayMessage(result.message, "info");
    }
  } catch (err) {
    console.log("Network error:", err);
    displayMessage("Network error occurred", "warning");
  } finally {
    $(".btn-update").text("Update Details").prop("disabled", false);
  }
}

  // -------------------------------------------------
  // Initiate Actions Based On Button Text
  // -------------------------------------------------
  $(".form-details").on("submit", function (e) {
    e.preventDefault();
    updateDetails();
  });
})(jQuery);
