// ----------------------------------------------------
// Import Configs
// ----------------------------------------------------
import {
  makeRequest,
} from "../../../assets/js/core/index.js";
// ----------------------------------------------------
// Import UI Alerts
// ----------------------------------------------------
import { displayMessage } from "../../../assets/js/ui/index.js";

(async function ($) {
  "use strict";

  // View order
  $(document).on("click", ".item-action button", async function () {
    const buttonText = $(this).text().trim();
    const parentContainer = $(this).closest("tr");
      const itemId = parseInt(parentContainer.data("id"));
      const storeId = parseInt(parentContainer.data("sid"));

    // Take action
    switch (buttonText) {
        case "Finalize":
          const payload = { itemId: itemId, storeId: storeId, status: "Yes" };
          await finalizeOrder(payload, itemId, $(this));
        break;

      case "View":
        // Do something here
        break;

      default:
        console.log("Status button clicked");
        break;
    }
  });
    
    // ----------------------------------------------------
    // Helper Functions Definitions
    // ----------------------------------------------------
  async function finalizeOrder(payload, id, el) {
    if (confirm("Are you sure to finalize?")) {
      el.text("Finalizing...").attr("disabled", true);
      try {
        const result = await makeRequest(
          `/wallet/fund/redeem`,
          "POST",
          payload
        );

        if (
          result &&
          result.message ===
            "Item completed and funds redeemed successfully"
        ) {
          // Display message
          displayMessage(result.message, "success");
          $(".content-row").each(function () {
            if ($(this).data("id") === id) {
              el.html(`<i class="fa fa-check" aria-hidden="true"></i> Finalized`).addClass("w-150");
            }
          });
        } else {
          displayMessage(result.message, "info");
          el.text("Finalize").attr("disabled", false);
        }
      } catch (err) {
        displayMessage(`Network error occurred: ${err}`, "error");
        el.text("Finalize").attr("disabled", false);
      }
    }
  }

})(jQuery);
