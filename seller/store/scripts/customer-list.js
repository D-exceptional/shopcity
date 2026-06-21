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
    const customerType = params.has("type") ? params.get("type") : "unique";

  // -------------------------------------------------
  // Initialize Counter
  // -------------------------------------------------
  let offset = 1;
  const limit = 20;

  // View & edit product
  $(document).on("click", ".customer-action button", async function () {
      const buttonText = $(this).text().trim();
       const parentContainer = $(this).closest("tr");
      const customerFirstName = parentContainer.find(".customer-firstname").text().trim();
      const customerLastName = parentContainer.find(".customer-lastname").text().trim();
      const customerEmail = parentContainer.find(".customer-email").text().trim();
      const customerContact = parentContainer.find(".customer-contact").text().trim();
      const fullName = `${customerFirstName}  ${customerLastName}`;
      const message = "Hello there!";

    // Take action
    switch (buttonText) {
      case "Message":
        if (confirm(`Are you sure to message "${fullName}"?`)) {
            openApp("sms", { to: customerContact, body: message });
        }
        break;
      case "Email":
            if (confirm(`Are you sure to email "${fullName}"?`)) {
                openApp("email", {
                  to: customerEmail,
                  subject: message,
                  body: message,
                  cc: "cc@example.com",
                  bcc: "bcc@example.com",
                });
            }
        break;

      case "Call":
            if (confirm(`Are you sure to call "${fullName}"?`)) {
                openApp("call", { to: customerContact });
            }
        break;

      default:
        displayMessage("Select an action", "info");
        break;
    }
  });

  // -------------------------------------------------
  // Load More Customers
  // -------------------------------------------------
  $(".btn-load").on("click", async function () {
    $(this).text("Processing...");
    offset++;
    // Fetch customers
    await fetchCustomers({ id: storeId, type: customerType, page: offset, total: limit });
  });
    
    // ----------------------------------------------------
    // Helper Functions Definitions
    // ----------------------------------------------------
    function openApp(type, options = {}) {
      let url = "";

      switch (type.toLowerCase()) {
        case "sms":
          if (!options.to) {
            displayMessage("SMS requires 'to' parameter", "info");
            return;
          }
          url = `sms:${options.to}`;
          if (options.body) {
            url += `?body=${encodeURIComponent(options.body)}`;
          }
          break;

        case "call":
        case "tel":
          if (!options.to) {
            displayMessage("Call requires 'to' parameter", "info");
            return;
          }
          url = `tel:${options.to}`;
          break;

        case "email":
        case "mailto":
          if (!options.to) {
            displayMessage("Email requires 'to' parameter", "info");
            return;
          }
          url = `mailto:${options.to}`;
          const params = [];
          if (options.subject)
            params.push(`subject=${encodeURIComponent(options.subject)}`);
          if (options.body)
            params.push(`body=${encodeURIComponent(options.body)}`);
          if (options.cc) params.push(`cc=${encodeURIComponent(options.cc)}`);
          if (options.bcc)
            params.push(`bcc=${encodeURIComponent(options.bcc)}`);
          if (params.length) url += `?${params.join("&")}`;
          break;

        default:
            displayMessage(
                "Unsupported action. Use 'sms', 'call', or 'email'",
                "info"
            );
          return;
      }

      window.location.href = url;
    }

    async function fetchCustomers(payload) {
        try {
            const result = await makeRequest(
                `/store/customer`,
                "GET",
                payload
            );

            if (result.data && result.data !== null && result.data.customers.length > 0) {
                // Display customers
                displayCustomers(result.data.customers);
            } else {
                displayMessage("No more customers found", "info");
                $(".btn-load").hide();
            }
        } catch (err) {
            displayMessage(`Network error occurred: ${err}`, "error");
        }
    }

    // Timeframes filetr
    function displayCustomers(customers) {
      if (!customers || customers.length === 0) {
        // No customers
        displayMessage("No more customers found", "info");
        return;
      }

      customers.forEach((customer) => {
        const {
          user_id,
          firstname,
          lastname,
          email,
          contact,
          country,
          total_orders,
        } = customer;

        const html = `
            <tr class='content-row' data-id='${user_id} ?>'>
                <td>#</td>
                <td class='customer-firstname'>${firstname} ?></td>
                <td class='customer-lastname'>${lastname} ?></td>
                <td class='customer-email'>${email} ?></td>
                <td class='customer-contact'>${contact} ?></td>
                <td class='customer-country'>${country} ?></td>
                <td class='customer-orders'>${formatNum(total_orders ?? 0)}</td>
                <td class='customer-action'>
                    <div style="display: flex; gap: 10px;">
                        <button class='btn btn-info btn-edit btn-sm wmg-70'>Message</button> 
                        <button class='btn btn-info btn-edit btn-sm wmg-70'>Email</button> 
                        <button class='btn btn-danger btn-delete btn-sm wmg-70'>Call</button> 
                    </div>
                </td>
            </tr>
        `;
        $("tbody").append(html);
      });

    countTotal("Customers");
  }
  
  function countTotal(type = "Customers") {
    const total = $("tbody tr.content-row").length;
    $(".header-count h1 b").text(`All ${type} (${formatNum(total)})`);
  }

})(jQuery);
