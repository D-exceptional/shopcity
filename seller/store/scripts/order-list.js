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
// ----------------------------------------------------
// Import Formatting
// ----------------------------------------------------
import { formatAmount } from "../../../assets/js/utils/index.js";
// ----------------------------------------------------
// Import Formatters
// ----------------------------------------------------
import { formatNum } from "../../scripts/utils/index.js";

(async function ($) {
  ("use strict");

  const params = new URLSearchParams(window.location.search);
  const storeId =
    params.has("id") && !isNaN(params.get("id"))
      ? parseInt(params.get("id"))
            : null;
    const status =
    params.has("status")
        ? params.get("status")
        : null;

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
                id: itemId,
                status: "Shipped",
            };

            const result = await makeRequest(
              `/order/item/status`,
              "PUT",
              payload
            );

            if (
              result.status === 200 &&
              result.message === "Status updated successfully"
            ) {
              displayMessage(result.message, "success");
                // Update UI
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
    await fetchOrders({ id: storeId, status: status, page: offset, limit: limit });
  });
    
    // ----------------------------------------------------
    // Helper Functions Definitions
    // ----------------------------------------------------
    async function fetchOrders(payload) {
        try {
            const result = await makeRequest(
              `/order/store/status`,
              "GET",
              payload
            );

            if (result.data && result.data !== null && result.data.orders.length > 0) {
                // Display products
                displayOrders(result.data.orders);
            } else {
                displayMessage("No more orders found", "info");
                $(".btn-load").hide();
            }
        } catch (err) {
            displayMessage(`Network error occurred: ${err}`, "error");
        }
    }

    // Timeframes filetr
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
