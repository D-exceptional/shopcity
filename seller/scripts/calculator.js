// ----------------------------------------------------
// Import  External Utilities
// ----------------------------------------------------
import {
  BASE_CONVERSION_RATE
} from "../../assets/js/core/index.js";

(async function ($) {
  ("use strict");

  // -------------------------------------------------
  // Validate Input
  // -------------------------------------------------
  $(".input_amount").on("input", function () {
    const input = $(this).val();
    const cleaned = input.replace(/[^0-9]/g, "");
    // Update content
    $(this).val(cleaned);

    // Check if the withdrawal amount is a valid number
    if (!cleaned) {
        notifyUser("Enter a valid amount");
      clearError();
    }
    // If the withdrawal amount is zero
    else if (cleaned === 0) {
        notifyUser("Enter a non-zero amount");
      clearError();
    }
    // If the withdrawal amount is valid
    else {
      $(".info-span").text("");
      const coin = cleaned * BASE_CONVERSION_RATE;
      $(".output_amount").val(coin.toLocaleString());
    }
  });

  $(".btn-calculator").on("click", function () {
    $(".overlay").css({ display: "flex" });
    clearError();
  });

  // -------------------------------------------------
  // Close Overlay
  // -------------------------------------------------
  $(".close-overlay").on("click", function () {
    $(".overlay").css({ display: "none" });
    $(".input_amount, .output_amount").val("");
    clearError();
  });

  // ----------------------------------------------------
  // Helper Functions Definitions
  // ----------------------------------------------------
  function clearError() {
    setTimeout(() => {
      $(".info-span").text(``).css({ color: "gray" });
      $(".input_amount, .output_amount").val("");
    }, 1000);
  }

  function notifyUser(message, type = "warning") {
    $(".info-span")
      .html(message)
          .css({ color: type === "warning" ? "red" : "green" });
      $(".output_amount").val("");
  }

})(jQuery);