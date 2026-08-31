// ----------------------------------------------------
// Import Configs
// ----------------------------------------------------
import { baseUrl, makeRequest } from "./core/index.js";
// ----------------------------------------------------
// Import UI Alerts
// ----------------------------------------------------
import { displayMessage } from "./ui/index.js";
// ----------------------------------------------------
// ImportRedirect
// ----------------------------------------------------
import { redirect } from "./modules/index.js";

(function ($) {
  ("use strict");

  // -------------------------------------------------
  // Update Item Status
  // -------------------------------------------------
  $(document).on("click", ".btn-order-item", async function () {
    const buttonText = $(this).text().trim();
    const parentContainer = $(this).closest(".item-card");
    const itemId = parentContainer.data("id");

    if (buttonText === "I have received this item") {
      const isSure = confirm(`Do you confirm to have received this item ?`);
      if (isSure) {
        $(this).text("Processing...").prop("disabled", true);
        // Prepare data
        const payload = { status: "Delivered" };

        // Take action
        try {
          const result = await makeRequest(
            `/order/item/${itemId}/status/${payload.status}`,
            "PUT",
            {},
          );

          if (
            result.status === 200 &&
            result.message === "Status updated successfully"
          ) {
              $(this).text("Completed");
              parentContainer.find(".btn-status").removeClass("btn-danger").addClass("btn-success");
            //Redirect to dashboard
          } else {
            displayMessage(`${result.message}`, "info");
            $(this).text("I have received this item").prop("disabled", false);
          }
        } catch (err) {
          console.log("Network error: ", err);
          displayMessage("Network error occured", "warning");
          $(this).text("I have received this item").prop("disabled", false);
        }
      }
    }
  });

  // -------------------------------------------------
  // Cancel Order
  // -------------------------------------------------
  $(document).on("click", ".btn-order-cancel", async function () {
    const buttonText = $(this).text().trim();
    const orderId = $(this).data("id");

    if (buttonText === "Cancel Order") {
      const isSure = confirm(`Do you confirm to cancel this order ?`);
      if (isSure) {
        $(this).text("Processing...").prop("disabled", true);
    
        // Take action
        try {
          const result = await makeRequest(
            `/order/${orderId}/cancel`,
            "DELETE",
            {},
          );

          if (
            result.status === 200 &&
            result.message === "Order cancelled successfully"
          ) {
              displayMessage(`${result.message}`, "success");
              $(this).text("Order Cancelled");
              //Redirect to orders
              redirect(`${baseUrl}/orders`);
          } else {
            displayMessage(`${result.message}`, "info");
            $(this).text("Cancel Order").prop("disabled", false);
          }
        } catch (err) {
          console.log("Network error: ", err);
          displayMessage("Network error occured", "warning");
          $(this).text("Cancel Order").prop("disabled", false);
        }
      }
    }
  });
})(jQuery);
