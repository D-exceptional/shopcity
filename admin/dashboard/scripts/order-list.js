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
import { formatNum } from "../scripts/utils/index.js";
// ----------------------------------------------------
// Import Redirect
// ----------------------------------------------------
import { redirect } from "../../../assets/js/modules/index.js";

(async function ($) {
  "use strict";

  const params = new URLSearchParams(window.location.search);
    const view =
    params.has("view")
        ? params.get("view")
        : "All";

  // -------------------------------------------------
  // Initialize Counter
  // -------------------------------------------------
  let offset = 1;
  const limit = 20;

  // View order
  $(document).on("click", ".order-action button", async function () {
    const buttonText = $(this).text().trim();
    const parentContainer = $(this).closest("tr");
    const orderId = parseInt(parentContainer.data("id"));

    // Take action
    switch (buttonText) {
      case "View":
            redirect(`./order-view?id=${orderId}`);
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
        const filter = ["Pending", "Completed", "Cancelled"].includes(view) ? view : 'All';
        const endpoint = ["All"].includes(view)
          ? `order`
            : `order/find/status`;
        const data = {
            ...payload,
            status: view,
        };

        try {
            const result = await makeRequest(
              `/${endpoint}`,
              "GET",
              data
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
        // No orders
        displayMessage("No more orders found", "info");
        return;
      }

      // Set status maps
      const statusMaps = {
        Pending: { style: "btn-danger", text: "View" },
        Completed: { style: "btn-success", text: "View" },
        Cancelled: { style: "btn-danger", text: "View" },
      };

      orders.forEach((order) => {
        const {
          order_id,
          tracking_code,
          subtotal_amount,
          tax_amount,
          discount_amount,
          shipping_amount,
          total_amount,
          shipping_address,
          order_status,
          created_at,
        } = order;

        const html = `
            <tr class='content-row' data-id='${order_id}'>
                <td>#</td>
                <td class='order-code'>${tracking_code}</td>
                <td class='oder-subtotal'>${formatAmount(subtotal_amount)}</td>
                <td class='order-tax'>${formatAmount(tax_amount)}</td>
                <td class='order-discount'>${formatAmount(discount_amount)}</td>
                <td class='order-shipping'>${formatAmount(shipping_amount)}</td>
                <td class='order-total'>${formatAmount(total_amount)} </td>
                <td class='order-address'>${shipping_address}</td>
                <td class='item-status'>
                    <button class='btn ${
                      statusMaps[order_status]["style"]
                    } btn-sm'>
                    ${order_status}
                    </button> 
                </td>
                <td class='order-date'>${created_at}</td>
                <td class='order-action'>
                    <div style="display: flex; gap: 10px;">
                    <button class='btn btn-info btn-view btn-sm wmg-70'> ${
                      statusMaps[order_status]["text"]
                    }</button> 
                    </div>
                </td>
            </tr>
        `;
        $("tbody").append(html);
      });

      countTotal(view);
    }
  
  function countTotal(type = "Pending") {
    const total = $("tbody tr.content-row").length;
    $(".header-count h1 b").text(`${type} Orders (${formatNum(total)})`);
  }

})(jQuery);
