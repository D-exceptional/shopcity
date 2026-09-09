// ----------------------------------------------------
// Import Configs
// ----------------------------------------------------
import { baseUrl, makeRequest } from "../../js/shared/core/index.js";
// ----------------------------------------------------
// Import UI Alerts
// ----------------------------------------------------
import { displayMessage } from "../../js/shared/ui/index.js";
// ----------------------------------------------------
// Import Validations
// ----------------------------------------------------
import { validateInput } from "../../js/shared/utils/index.js";
// ----------------------------------------------------
// ImportRedirect
// ----------------------------------------------------
import { redirect } from "../../js/shared/modules/index.js";

(function ($) {
  ("use strict");

  // -------------------------------------------------
  // Check Inputs
  // -------------------------------------------------
  $(".form-control").each(function () {
    $(this).on("input blur", function () {
      validateInput(this);
    });
  });

  // -------------------------------------------------
  //  Track Order
  // -------------------------------------------------
  async function trackOrder() {
   
    const code      = validateInput($(".form-code"));
    const orderCode = `#${code}`;

    if (!orderCode) {
      displayMessage("Enter Order Tracking Code", "info");
    } else {
      $(".btn-track").text("Fetching data...").prop("disabled", true);

      // Take action
      try {
        const result = await makeRequest(
          `/order/track/${orderCode}`,
          "GET",
          {},
        );
          
        if (
          result.status === 200 &&
          ["Order fetched"].includes(result.message)
        ) {
            displayMessage(`${result.message}. Opening full details...`, "success");
           redirect(`/order/${result.data.order.order_id}`);
        } else {
          displayMessage(`${result.message}`, "info");
        }
        $(".btn-track").text("Track Order").prop("disabled", false);
      } catch (err) {
        console.log("Network error: ", err);
        displayMessage("Network error occured", "warning");
        $(".btn-track").text("Track Order").prop("disabled", false);
      }
    }
  }

  // -------------------------------------------------
  // Initiate Actions Based On Button Text
  // -------------------------------------------------
  $(".form-track").on("submit", function (e) {
    e.preventDefault();
    trackOrder();
  });
})(jQuery);
