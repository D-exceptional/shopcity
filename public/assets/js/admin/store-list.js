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
// Import Formatters
// ----------------------------------------------------
import { formatNum } from "./utils/index.js";

(async function ($) {
  ("use strict");

  countTotal("Stores");

  // -------------------------------------------------
  // Initialize Counter
  // -------------------------------------------------
  let offset = 1;
  const limit = 20;

  // -------------------------------------------------
  // Show All Stores
  // -------------------------------------------------
  $(".btn-all").on("click", function () {
    $(".content-row").each(function () {
      if ($(this).css("display") === "none") {
        $(this).css({ display: "table-row" });
      }
    });
  });

  // -------------------------------------------------
  // Show Pending Stores
  // -------------------------------------------------
  $(".btn-pending").on("click", function () {
    $(".content-row").each(function () {
      const statusText = $(this).find(".status button").text().trim();
      if (["Pending", "Deactivated"].includes(statusText)) {
        $(this).css({ display: "table-row" });
      } else {
        $(this).css({ display: "none" });
      }
    });
  });

  // -------------------------------------------------
  // Show Active Stores
  // -------------------------------------------------
  $(".btn-active").on("click", function () {
    $(".content-row").each(function () {
      const statusText = $(this).find(".status button").text().trim();
      if (statusText === "Active") {
        $(this).css({ display: "table-row" });
      } else {
        $(this).css({ display: "none" });
      }
    });
  });

  // -------------------------------------------------
  // Load More Stores
  // -------------------------------------------------
  $(".btn-load").on("click", async function () {
    $(this).text("Processing...");
    offset++;
   
    await fetchStores({ status: null, page: offset, total: limit });
  });

  // -------------------------------------------------
  // Toggle Store Status
  // -------------------------------------------------
  $(document).on("click", ".action button", async function () {
    const actionText = $(this).text().trim();
    const storeId = $(this).closest("tr").data("id");
    
    await manageStore({ action: actionText, id: storeId }, $(this));
  });

  // ----------------------------------------------------
  // Helper Functions Definitions
  // ----------------------------------------------------
  async function fetchStores(payload) {
    try {
      const result = await makeRequest(
        `/store/status/${payload.status}/page/${payload.page}`,
        "GET",
        {},
      );

      if (
        result.data &&
        result.data !== null &&
        result.data.stores.length > 0
      ) {
        // Display stores
        displayStores(result.data.stores);
      } else {
        displayMessage("No more stores found", "info");
        $(".btn-load").hide();
      }
    } catch (err) {
      displayMessage(`Network error occurred: ${err}`, "error");
    }
  }

  // Display Stores
  function displayStores(stores) {
    if (!stores || stores.length === 0) {
      displayMessage("No more stores found", "info");
      return;
    }

    stores.forEach((store) => {
      const {
        store_id,
        store_name,
        store_avatar,
        store_status,
        created_at,
        facebook,
        instagram,
        tiktok,
        twitter,
      } = store;

      const html = `
        <tr class='content-row' data-id='${store_id}>
          <td>#</td>
          <td>${store_name} ?></td>
          <td>
            <img src='${store_avatar}' class='profile-user-img img-fluid img-circle hmg-100' alt='Store Image'>
          </td>
          <td class='status'>
            <button class='btn ${
              store_status === "Active" ? "btn-success" : "btn-danger"
            } btn-sm'>
            ${store_status} ?>
            </button> 
          </td>
          <td>${created_at}</td>
          <td>${facebook}</td>
          <td>${instagram}</td>
          <td>${tiktok}</td>
          <td>${twitter}</td>
          <td class='action' style="display: flex; gap: 10px;">
            ${
              store_status === "Active"
                ? "<button class='btn bg-primary color-white btn-sm btn-action w-150'>Deactivate</button>"
                : "<button class='btn bg-primary color-white btn-sm btn-action w-150'>Activate</button>"
            }

            <button class='btn bg-primary color-white btn-sm btn-action w-150'>Delete</button>
          </td>
        </tr>
      `;
      $("tbody").append(html);
    });

    countTotal("Stores");
  }

  function countTotal(type = "Stores") {
    const total = $("tbody tr.content-row").length;
    $(".content-header h1 b").text(`${type} (${formatNum(total)})`);
  }

  async function manageStore(payload, el) {

    const statusMap = {
      Activate: "Active",
      Deactivate: "Deactivated",
    };

    if (["Activate", "Deactivate"].includes(payload.action)) {

      if (confirm(`Are you sure to ${payload.action} store?`)) {

        el.attr("disabled", true).text(
          `${payload.action === "Activate" ? "Activating..." : "Deactivating..."}`,
        );

        payload.action = statusMap[payload.action];

        try {
          const result = await makeRequest(
            `/store/${payload.id}/status/${payload.action}`,
            "PUT",
            {},
          );

          if (result && result.message === "Status updated successfully") {
           
            displayMessage(result.message, "success");

            $("tbody tr").each(function () {
              if ($(this).data("id") === payload.id) {

                $(this)
                  .find(".action button")
                  .text(
                    `${payload.action === "Active" ? "Deactivate" : "Activate"}`,
                  )
                  .attr("disabled", false);
                
                $(this)
                  .find(".status")
                  .empty()
                  .html(
                    ` <button class='btn ${
                        payload.action === "Active" ? "btn-success" : "btn-danger"
                      } btn-sm'>
                        ${payload.action}
                    </button> 
                  `);
              }
            });
          } else {

            displayMessage(result.message, "info");
            el.attr("disabled", false)
              .text(`${payload.action}`);
          }
        } catch (err) {

          displayMessage(`Network error occurred: ${err}`, "error");
          el.attr("disabled", false)
            .text(`${payload.action}`);
        }
      }
    }
    else {
      if (confirm(`Are you sure to delete store?`)) {

        el.attr("disabled", true)
          .text("Deleting...");

        try {
          const result = await makeRequest(
            `/store/${payload.id}`,
            "DELETE",
            {},
          );

          if (
            result &&
            result.message === "Store deleted successfully"
          ) {

            displayMessage(result.message, "success");

            $("tbody tr").each(function () {
              if ($(this).data("id") === payload.id) {
                $(this)
                  .remove();
              }
            });

          } else {
            displayMessage(result.message, "info");
            el.attr("disabled", false)
              .text('Delete');
          }
        } catch (err) {
          displayMessage(`Network error occurred: ${err}`, "error");
          el.attr("disabled", false)
            .text('Delete');
        }
      }
    }
  }
})(jQuery);