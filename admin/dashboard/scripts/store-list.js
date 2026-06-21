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
import { formatNum } from "../scripts/utils/index.js";

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
    // Fetch stores
    await fetchStores({ status: null, page: offset, total: limit });
  });

  // -------------------------------------------------
  // Toggle Store Status
  // -------------------------------------------------
  $(document).on("click", ".action button", async function () {
    const actionText = $(this).text().trim();
    const storeId = $(this).closest("tr").data("id");
    // Fetch stores
    await manageStatus({ status: actionText, id: storeId }, $(this));
  });

  // ----------------------------------------------------
  // Helper Functions Definitions
  // ----------------------------------------------------
  async function fetchStores(payload) {
    try {
      const result = await makeRequest(
        `/store/list/status`,
        "GET",
        payload
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

  // Timeframes filetr
  function displayStores(stores) {
    if (!stores || stores.length === 0) {
      // No products
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

  async function manageStatus(payload, el) {
    const statusMap = {
      Activate: "Active",
      Deactivate: "Deactivated",
    };
    if (confirm(`Are you sure to ${payload.status} store?`)) {
      el.attr("disabled", true).text(`${payload.status === "Activate" ? "Activating..." : "Deactivating..."}`);
      payload.status = statusMap[payload.status];
      
      try {
        const result = await makeRequest(
          `/store/status`,
          "PUT",
          payload
        );

        if (result && result.message === "Status updated successfully") {
          // Display stores
          displayMessage(result.message, "success");
          $("tbody tr").each(function () {
            if ($(this).data("id") === payload.id) {
              $(this)
                .find(".action button")
                .text(
                  `${payload.status === "Active" ? "Deactivate" : "Activate"}`
                )
                .attr("disabled", false);
              $(this)
                .find(".status")
                .empty()
                .html(
                  ` <button class='btn ${
                    payload.status === "Active" ? "btn-success" : "btn-danger"
                  } btn-sm'>
                      ${payload.status}
                    </button> 
                  `
                );
            }
          });
        } else {
          displayMessage(result.message, "info");
          el.attr("disabled", false).text(`${payload.status}`);
        }
      } catch (err) {
        displayMessage(`Network error occurred: ${err}`, "error");
        el.attr("disabled", false).text(`${payload.status}`);
      }
    }
  }
})(jQuery);