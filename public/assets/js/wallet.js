// ----------------------------------------------------
// Import Configs
// ----------------------------------------------------
import { BASE_TOPUP, BASE_CONVERSION_RATE, makeRequest } from "./core/index.js";
// ----------------------------------------------------
// Import UI Alerts
// ----------------------------------------------------
import { displayMessage } from "./ui/index.js";
// ----------------------------------------------------
// Import Validations
// ----------------------------------------------------
import { validateInput } from "./utils/index.js";
// ----------------------------------------------------
// Import Payment
// ----------------------------------------------------
import { setPaymentOptions, makePayment } from "./modules/index.js";


(function ($) {
  ("use strict");

  // -------------------------------------------------
  // Validate Topup Form Inputs on Typing
  // -------------------------------------------------
  $(document).on("input", ".topup-amount", function () {
    validateInput(this);
  });

  // -------------------------------------------------
  // View Product Details From Cart Or Wishlist
  // -------------------------------------------------
  $(document).on("click", ".btn-topup", async function () {
    const country = $(".country").val();
    const amount = $(".topup-amount").val().trim();

    if (
      !amount ||
      amount == "" ||
      amount == 0 ||
      amount === "Enter the amount to topup"
    ) {
      return displayMessage(`Please enter a valid amount`, "warning");
    }

    if (amount < BASE_TOPUP) {
      return displayMessage(
        `Minimum topup amount is: ₦ ${Math.round(BASE_TOPUP).toLocaleString()}`,
        "warning"
      );
    }

    // Set Payment Options
    let paymentOptions = setPaymentOptions(country);
    if (!paymentOptions) {
      return displayMessage(
        `No payment option available for the country: ${country}`,
        "warning"
      );
    }

    // Prepare data
    const payload = { amount: amount };

    $(this).text("Processing...").prop("disabled", true);

    // Generate Payment Receipt
    try {
      // Send payment request to backend
      const result = await makeRequest(
        `/wallet/fund`,
        "POST",
        payload
      );

      if (
        result.status === 200 &&
        result.message === "Payment reference generated"
      ) {
        displayMessage(
          "Payment reference generated. Opening payment modal...",
          "success"
        );
        // Open modal after 1s
        setTimeout(() => {
          makePayment(
            paymentOptions,
            amount,
            result.data.reference,
            result.data.user
          );
        }, 1000);
      } else {
        displayMessage("Could not generate reference", "warning");
        $(this).text("Topup Wallet").prop("disabled", false);
      }
    } catch (err) {
      displayMessage("Network error occured", "warning");
      $(this).text("Topup Wallet").prop("disabled", false);
    }
  });

  // -------------------------------------------------
  // Validate Conversion Form Inputs on Typing
  // -------------------------------------------------
  $(document).on("input", ".test-amount", function () {
    const input = $(this).val();
    const cleaned = input.replace(/[^0-9]/g, "");
    // Update content
    $(this).val(cleaned);
    if (cleaned && cleaned !== 0) {
      const coin = cleaned * BASE_CONVERSION_RATE;
      $(".result-amount").val(coin.toLocaleString());
    }
    else {
      $(this).val("");
      displayMessage("Enter a valid amount", "warning");
      $(".result-amount").val("");
    }
  });
})(jQuery);
