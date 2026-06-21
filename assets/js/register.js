// ----------------------------------------------------
// Import Configs
// ----------------------------------------------------
import {
  supportedCountries,
  baseUrl,
  BASE_CURRENCY,
  makeRequest,
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
  togglePasswordVisibility,
  capitalizeWords,
  previewFile,
  loadCountries,
  uploadToCloudinary,
} from "./utils/index.js";
// ----------------------------------------------------
// Import Cart And Wishlist Syncer
// ----------------------------------------------------
import { redirect } from "./modules/index.js";

(async function ($) {
  "use strict";

  const queryString = new URL(window.location);
  const urlParams = new URLSearchParams(queryString.search);
  const userRole = capitalizeWords(urlParams.get("type") || "customer");

  let currency = BASE_CURRENCY;
  let countriesArray = [];
  let dialingCode = "+234";
  let shortCode = "NG";

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

  $(".password-section i").on("click", function () {
    togglePasswordVisibility($(this), "password");
  });

  $(".form-text .form-control").on("input blur", function () {
    validateInput(this);
  });

  $(".form-country").on("change", function () {
    updateDetails();
  });

  $(".form-file").on("change", function () {
    previewFile(this, "Document Only");
  });

  $("form.form-signup").on("submit", async function (e) {
    e.preventDefault();
    await register();
  });

  function updateDetails() {
    const selectedCountry = $(".form-country").val();
    const country = countriesArray.find((c) => c.name === selectedCountry);

    if (country && supportedCountries.includes(country.name)) {
      currency = country.currency;
      dialingCode = country.code;
      shortCode = country.abbr;
    } else {
      displayMessage("Country not supported.", "warning");
    }
  }

  async function register() {
    if (!supportedCountries.includes($(".form-country").val())) {
      displayMessage("Country not supported", "info");
      return;
    }

    const inputs = {
      avatar: "None",
      firstname: $(".form-firstname").val(),
      lastname: $(".form-lastname").val(),
      email: $(".form-email").val(),
      contact: $(".form-phone").val(),
      country: $(".form-country").val(),
      password: $(".form-password").val(),
      state: $(".form-state").val() ?? 'Not Specified',
    };

    if (Object.values(inputs).some((val) => val === "")) {
      displayMessage("Some fields are empty", "info");
      return;
    }

    if (userRole === "Vendor") {
      const fileInput = $(".form-file")[0];
      const file = fileInput?.files?.[0];
      if (!file) {
        displayMessage("Upload a valid government-issued ID", "info");
        return;
      }
    }

    $(".btn-signup").prop("disabled", true).text("Processing...");

    try {
      const fileInput = $(".form-file")[0];
      const file = fileInput?.files?.[0];
      const fileUrl =
        userRole === "Vendor" && file
          ? await uploadToCloudinary(file, "documents")
          : "None";

      const payload = {
        ...inputs,
        role: userRole,
        code: dialingCode,
        abbr: shortCode,
        creator: "System",
        currency,
        file: fileUrl,
      };

      const result = await makeRequest(
        `/user/register`,
        "POST",
        payload
      );

      if (
        result.status === 200 &&
        result.message === "Registration successful"
      ) {
        displayMessage(result.message, "success");
        $(".btn-signup").text("Redirecting...");
        setTimeout(() => {
          redirect(`${baseUrl}/login`);
        }, 1200);
      } else {
        displayMessage(result.message, "info");
        $(".btn-signup").text("Sign Up").prop("disabled", false);
      }
    } catch (err) {
      displayMessage(`Network error occurred: ${err}`, "error");
      $(".btn-signup").text("Sign Up").prop("disabled", false);
    }
  }
})(jQuery);
