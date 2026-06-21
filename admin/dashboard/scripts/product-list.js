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
// Import Redirect
// ----------------------------------------------------
import { redirect } from "../../../assets/js/modules/index.js";
// ----------------------------------------------------
// Import Formatting
// ----------------------------------------------------
import { formatAmount } from "../../../assets/js/utils/index.js";
// ----------------------------------------------------
// Import Formatters
// ----------------------------------------------------
import { formatNum } from "../scripts/utils/index.js";

(async function ($) {
  ("use strict");

  // -------------------------------------------------
  // Initialize Counter
  // -------------------------------------------------
  let offset = 1;
  const limit = 20;

  // View & edit product
  $(document).on("click", ".product-action button", async function () {
    const buttonText = $(this).text().trim();
    const parentContainer = $(this).closest("tr");
    const productId = parseInt(parentContainer.data("id"));
    const productName = parentContainer.find(".product-name").text().trim();

    // Take action
    switch (buttonText) {
      case "Details":
        redirect(`./product-view?productId=${productId}`);
        break;

      case "Delete":
        const verify = confirm(
          `Are you sure to delete product "${productName}"?`
        );
        if (verify) {
          try {
            const payload = {
              id: productId,
            };

            const result = await makeRequest(
              `/products`,
              "DELETE",
              payload
            );

            if (
              result.status === 200 &&
              result.message === "Product deleted successfully"
            ) {
              displayMessage(result.message, "success");
              // Remove from UI
              $("tbody tr").each(function () {
                if ($(this).data("id") === productId) {
                  $(this).fadeOut(200);
                  $(this).remove();
                }
              });
              setTimeout(() => {
                countTotal("Products");
              }, 1500);
            } else {
              displayMessage(result.message, "info");
            }
          } catch (err) {
            displayMessage(`Network error occurred: ${err}`, "error");
          }
        }
        break;

      default:
        redirect(`./product-view?productId=${productId}`);
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
    await fetchProducts({ page: offset, total: limit, view: "admin" });
  });
    
    // ----------------------------------------------------
    // Helper Functions Definitions
    // ----------------------------------------------------
    async function fetchProducts(payload) {
      try {
        const result = await makeRequest(
          `/products`,
          "GET",
          payload
        );

        if (result.data && result.data !== null && result.data.products.length > 0) {
          // Display products
          displayProducts(result.data.products);
        } else {
          displayMessage("No more products found", "info");
          $(".btn-load").hide();
        }
      } catch (err) {
        displayMessage(`Network error occurred: ${err}`, "error");
      }
    }

    // Timeframes filetr
    function displayProducts(products) {
      if (!products || products.length === 0) {
        // No products
        displayMessage("No more products found", "info");
        return;
      }

      products.forEach((product) => {
        const { 
          product_id,
          product_name,
          product_description,
          category,
          sub_category,
          product_price,
          slash_price,
          stock,
          color,
          visibility,
          created_at,
          updated_at,
        } =
        product;
        // Format description
        const description = product_description.length > 50 ? product_description.substring(0, 50) + "..." : product_description;

        const html = `
          <tr class="content-row" data-id='${product_id}'>
            <td>#</td>
            <td class='product-name'>${product_name}</td>
            <td class='product-description'>${description}</td>
            <td class='product-category'>${category}</td>
            <td class='product-subcategory'>${sub_category}</td>
            <td class='product-price'>${formatAmount(product_price ?? 0)}</td>
            <td class='product-slash'>${formatAmount(slash_price ?? 0)}</td>
            <td class='product-stock'>
              <button class='btn ${stock > 100 ? 'btn-info' : 'btn-danger'} btn-sm wmg-70'>
                ${formatNum(stock ?? 0)}
              </button> 
            </td>
            <td class='product-color'>${color}</td>
            <td>
              <button class='btn ${visibility === 'Visible' ? 'btn-info' : 'btn-danger' } btn-sm'>
                ${visibility}
              </button> 
            </td>
            <td>${created_at}</td>
            <td>${updated_at}</td>
            <td class='product-action'>
              <div style="display: flex; gap: 10px;">
                <button class='btn btn-info btn-view btn-sm wmg-70'>Details</button> 
                <button class='btn btn-danger btn-delete btn-sm wmg-70'>Delete</button> 
              </div>
            </td>
          </tr>
        `;
        $("tbody").append(html);
      });

    countTotal("Products");
  }
  
  function countTotal(type = "Products") {
    const total = $("tbody tr.content-row").length;
    $(".header-count h1 b").text(`All ${type} (${formatNum(total)})`);
  }

})(jQuery);
