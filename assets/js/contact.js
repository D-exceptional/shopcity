// ----------------------------------------------------
// Import Configs
// ----------------------------------------------------
import {
  makeRequest
} from "./core/index.js";
// ----------------------------------------------------
// Import UI Alerts
// ----------------------------------------------------
import { displayMessage } from "./ui/index.js";
// ----------------------------------------------------
// Import Validations
// ----------------------------------------------------
import {
  validateInput,
  loadCountries,
} from "./utils/index.js";

(async function ($) {
    "use strict";
    
  let countriesArray = [];
  let dialingCode = "";

  countriesArray = await loadCountries();

  $(".form-name")
    .on("keypress", function (e) {
      if (/\d/.test(e.key)) {
        e.preventDefault();
        displayMessage("Numbers not allowed in name field", "info");
      }
    })
    .on("paste", function (e) {
      const paste = (e.clipboardData || window.clipboardData).getData("text");
      if (/\d/.test(paste)) {
        e.preventDefault();
        displayMessage("Numbers not allowed in name field", "info");
      }
    });

  $(".form-text .form-control").on("input blur", function () {
    validateInput(this);
  });

  $(".form-country").on("change", function () {
    updateDetails();
  });

  $("form.form-contact").on("submit", async function (e) {
    e.preventDefault();
    await sendMessage();
  });

  function updateDetails() {
    const selectedCountry = $(".form-country").val();
    const country = countriesArray.find((c) => c.name === selectedCountry);

    if (country) {
      dialingCode = country.code;
    } else {
      displayMessage("Country not supported.", "warning");
    }
  }
    
    function resetForm() {
        $(".form-text .form-control").val("");
    }

  async function sendMessage() {
    const inputs = {
      name: $(".form-name").val(),
      email: $(".form-email").val(),
      contact: $(".form-phone").val(),
      country: $(".form-country").val(),
      subject: $(".form-subject").val(),
      message: $(".form-message").val(),
    };

    if (Object.values(inputs).some((val) => val === "")) {
      displayMessage("Some fields are empty", "info");
      return;
    }

    $(".btn-contact").prop("disabled", true).text("Processing...");

    try {
    
      const payload = {
        ...inputs,
        code: dialingCode,
      };

      const result = await makeRequest(
        `/user/contact`,
        "POST",
        payload
      );

      if (
        result.status === 200 &&
        result.message === "Message sent successfully"
      ) {
          displayMessage(result.message, "success");
          resetForm();
        $(".btn-contact").text("Send Message");
      } else {
        displayMessage(result.message, "info");
        $(".btn-contact").text("Send Message").prop("disabled", false);
      }
    } catch (err) {
      displayMessage(`Network error occurred: ${err}`, "error");
      $(".btn-contact").text("Send Message").prop("disabled", false);
    }
  }
})(jQuery);
