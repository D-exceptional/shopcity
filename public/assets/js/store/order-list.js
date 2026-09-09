// ----------------------------------------------------
// Import Configs
// ----------------------------------------------------
import {
  makeRequest,
} from "../../js/shared/core/index.js";
// ----------------------------------------------------
// Import UI Alerts
// ----------------------------------------------------
import { displayMessage } from "../../js/shared/ui/index.js";
// ----------------------------------------------------
// Import Formatting
// ----------------------------------------------------
import { formatAmount } from "../../js/shared/utils/index.js";
// ----------------------------------------------------
// Import Formatters
// ----------------------------------------------------
import { formatNum } from "../../js/shared//utils/index.js";

(async function ($) {
  ("use strict");

  /*
  const params = new URLSearchParams(window.location.search);
  const storeId =
    params.has("id") && !isNaN(params.get("id"))
      ? parseInt(params.get("id"))
            : null;
    const status =
    params.has("status")
        ? params.get("status")
      : null;
  */
  const storeId = $(".content-wrapper").data("storeid");
  const status  = $(".content-wrapper").data("status");

  // -------------------------------------------------
  // Initialize Counter
  // -------------------------------------------------
  let offset = 1;
  const limit = 20;

  // View & edit product
  $(document).on("click", ".item-action button", async function () {
    const buttonText = $(this).text().trim();
    const parentContainer = $(this).closest("tr");
    const itemId = parseInt(parentContainer.data("id"));
    const productName = parentContainer.find(".item-name").text().trim();

    // Take action
    switch (buttonText) {
      case "Mark as shipped":

        const verify = confirm(
          `Are you sure to update order status for product "${productName}"?`
        );

        if (verify) {
          try {
            const payload = {
              status: "Shipped",
            };

            const result = await makeRequest(
              `/order/item/${itemId}/status/${payload.status}`,
              "PUT",
              {},
            );

            if (
              result.status === 200 &&
              result.message === "Status updated successfully"
            ) {
              displayMessage(result.message, "success");
                $(this).text("Item shipped");
                parentContainer.find(".item-status button").removeClass("btn-danger").addClass("btn-info").text("Shipped");
            } else {
              displayMessage(result.message, "info");
            }
          } catch (err) {
            displayMessage(`Network error occurred: ${err}`, "error");
          }
        }
        break;

      default:
        console.log("Status button clicked");
        break;
    }
  });

  // -------------------------------------------------
  // Load More Notifications
  // -------------------------------------------------
  $(".btn-load").on("click", async function () {
    $(this).text("Processing...");
    offset++;
    // Fetch notifications
    await fetchOrders({ page: offset, limit: limit });
  });
    
    // ----------------------------------------------------
    // Helper Functions Definitions
    // ----------------------------------------------------
    async function fetchOrders(payload) {
      try {
        const result = await makeRequest(
          `/order/store/${storeId}/status/${status}/page/${payload.page}`,
          "GET",
          {},
        );

        if (result.data && result.data !== null && result.data.orders.length > 0) {
          // Display Orders
          displayOrders(result.data.orders);
        } else {
          displayMessage("No more orders found", "info");
          $(".btn-load").hide();
        }
      } catch (err) {
        displayMessage(`Network error occurred: ${err}`, "error");
      }
    }

    // Display Orders
    function displayOrders(orders) {
      if (!orders || orders.length === 0) {
        // No products
        displayMessage("No more orders found", "info");
        return;
      }

      // Set status maps
      const statusMaps = {
        Pending: { style: "btn-danger", text: "Mark as shipped" },
        Shipped: { style: "btn-info", text: "Shipped" },
        Delivered: { style: "btn-success", text: "Completed" },
      };

      orders.forEach((order) => {
        const {
          item_id,
          product_image,
          product_name,
          price,
          quantity,
          stock,
          tracking_code,
          item_status,
          created_at,
        } = order;

        const html = `
          <tr class='content-row' data-id='${item_id} ?>'>
            <td>#</td>
            <td class='item-image'>
              <img class='img-fluid wmg-100 h-100' src='${product_image} ?>' alt='Order Image'>
            </td>
            <td class='item-name'>${product_name} ?></td>
            <td class='item-price'>${formatAmount(price ?? 0)}</td>
            <td class='item-quantity'>
              ${formatNum(quantity ?? 0)}
            </td>
            <td class='item-stock'>
              <button class='btn ${
                stock > 100 ? "btn-info" : "btn-danger"
              } btn-sm wmg-70'>
                ${formatNum(stock ?? 0)}
              </button> 
            </td>
            <td class='item-code'>${tracking_code} ?></td>
            <td class='item-status'>
              <button class='btn ${
                statusMaps[item_status]["style"]
              } btn-sm'>
                ${item_status}
              </button> 
            </td>
            <td>${created_at} ?></td>
            <td class='action'>
              <div style="display: flex; gap: 10px;">
                <button class='btn btn-info btn-view btn-sm w-150'>${
                  statusMaps[item_status]["text"]
                }</button> 
              </div>
            </td>
          </tr>
        `;

        $("tbody").append(html);
      });

      countTotal(status);
    }
  
  function countTotal(type = "Pending") {
    const total = $("tbody tr.content-row").length;
    $(".header-count h1 b").text(`${type} Orders (${formatNum(total)})`);
  }

})(jQuery);
