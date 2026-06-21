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

  // -------------------------------------------------
  // Initialize Counter
  // -------------------------------------------------
  let offset = 1;
  const limit = 20;

  // -------------------------------------------------
  // Show All Coupons
  // -------------------------------------------------
  $(".btn-all").on("click", function () {
    $(".content-row").each(function () {
      if ($(this).css("display") === "none") {
        $(this).css({ display: "table-row" });
      } 
    });
  });

  // -------------------------------------------------
  // Show Active Coupons
  // -------------------------------------------------
  $(".btn-active").on("click", function () {
      $(".content-row").each(function () {
        const statusText = $(this).data("status");
        if (statusText === "Active") {
           $(this).css({ display: "table-row" });
        } else {
            $(this).css({ display: "none" });
      }
    });
  });

  // -------------------------------------------------
  // Show Inactive Coupons
  // -------------------------------------------------
  $(".btn-inactive").on("click", function () {
      $(".content-row").each(function () {
        const statusText = $(this).data("status");
      if (statusText === "Deactivated") {
        $(this).css({ display: "table-row" });
      } else {
        $(this).css({ display: "none" });
      }
    });
  });

  // View & edit product
  $(document).on("click", ".coupon-action button", async function () {
    const buttonText = $(this).text().trim();
    const parentContainer = $(this).closest("tr");
    const couponId = parseInt(parentContainer.data("id"));

    let initialData = {};

    // Take action
      switch (buttonText) {
        case "Edit":
          if (confirm(`Are you sure to edit coupon?`)) {
            $(this).text("Update");
            parentContainer.find(".coupon-code, .coupon-discount").prop("contenteditable", true);
            parentContainer.find("select").prop("disabled", false);

            initialData = {
              code: parentContainer.find(".coupon-code").text().trim(),
              discount: parentContainer.find(".coupon-discount").text().trim(),
              status: parentContainer.find("select").val().trim(),
            }
          }
          break;
        case "Update":
          const currentData = {
            code: parentContainer.find(".coupon-code").text().trim(),
            discount: parentContainer.find(".coupon-discount").text().trim(),
            status: parentContainer.find("select").val().trim(),
          }

          if (
            currentData.code === initialData.code && 
            currentData.discount === initialData.discount && 
            currentData.status === initialData.status) 
          {
            displayMessage("No changes made", "info");
            return;
          }

          if (Object.values(currentData).some((val) => val === "")) {
            displayMessage("Some fields are empty", "info");
            return;
          }

          if (currentData.discount < 5 || currentData.discount > 100) {
            displayMessage(
              "Enter a valid discount (between 5 and 100)",
              "info"
            );
            return;
          } else {
            if (confirm(`Are you sure to update coupon?`)) {
              try {
                const payload = {
                  ...currentData,
                  couponId: couponId,
                };

                const result = await makeRequest(
                  `/store/coupon`,
                  "PUT",
                  payload
                );

                if (
                  result.status === 200 &&
                  result.message === "Coupon updated successfully"
                ) {
                  displayMessage(result.message, "success");
                  $(this).text("Edit");
                  parentContainer
                    .data("status", currentData.status)
                    .attr("data-status", currentData.status);
                  parentContainer
                    .find(".coupon-code, .coupon-discount")
                    .prop("contenteditable", false);
                  parentContainer.find("select").prop("disabled", true);
                } else {
                  displayMessage(result.message, "info");
                }
              } catch (err) {
                displayMessage(`Network error occurred: ${err}`, "error");
              }
            }
          }
          break;
        case "Delete":
          if (confirm(`Are you sure to delete coupon?`)) {
            try {
              const payload = {
                id: couponId,
              };

              const result = await makeRequest(
                `/store/coupon`,
                "DELETE",
                payload
              );

              if (
                result.status === 200 &&
                result.message === "Coupon deleted successfully"
              ) {
                displayMessage(result.message, "success");
                // Update UI
                parentContainer.fadeOut(200);
                parentContainer.remove();
                setTimeout(() => {
                  countTotal("Coupons");
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
    await fetchCoupons({
      id: storeId,
      page: offset,
      limit: limit,
    });
  });

  // ----------------------------------------------------
  // Helper Functions Definitions
  // ----------------------------------------------------
  async function fetchCoupons(payload) {
    try {
      const result = await makeRequest(
        `/store/coupon`,
        "GET",
        payload
      );

      if (
        result.data &&
        result.data !== null &&
        result.data.coupons.length > 0
      ) {
        // Display coupons
        displayCoupons(result.data.coupons);
      } else {
        displayMessage("No more coupons found", "info");
        $(".btn-load").hide();
      }
    } catch (err) {
      displayMessage(`Network error occurred: ${err}`, "error");
    }
  }

  // Timeframes filetr
  function displayCoupons(coupons) {
    if (!coupons || coupons.length === 0) {
      // No products
      displayMessage("No more coupons found", "info");
      return;
    }

    // Set status maps
    const statusMaps = {
      Active: { style: "btn-success", text: "Active" },
      Deactivated: { style: "btn-danger", text: "Deactivated" },
    };

    coupons.forEach((coupon) => {
      const {
        coupon_id,
        coupon_code,
        coupon_discount,
        coupon_status,
        created_at,
      } = coupon;

      const html = `
        <tr class='content-row' data-id='${coupon_id}' data-status='${coupon_status}'>
          <td>#</td>
          <td class='coupon-code'>${coupon_code}</td>
          <td class='coupon-discount'>${formatNum(coupon_discount)}</td>
          <td class='coupon-status'>
            <select class='form-control' disabled>
              <option value='${coupon_status}'>${coupon_status}</option>
              <option value='${coupon_status === "Active" ? "Deactivated" : "Active"}'>${coupon_status === "Active" ? "Deactivated" : "Active"}</option>
            </select>
          </td>
          <td>${created_at}</td>
          <td class='item-action'>
            <div style="display: flex; gap: 10px;">
              <button class='btn btn-info btn-edit btn-sm wmg-70'>Edit</button> 
              <button class='btn btn-danger btn-delete btn-sm wmg-70'>Delete</button> 
            </div>
          </td>
        </tr>
      `;
      $("tbody").append(html);
    });

    countTotal("Coupons");
  }

  function countTotal(type = "Coupons") {
    const total = $("tbody tr.content-row").length;
    $(".header-count h1 b").text(`${type} (${formatNum(total)})`);
  }
})(jQuery);
