// ----------------------------------------------------
// Import  External Utilities
// ----------------------------------------------------
import { makeRequest } from "../../assets/js/core/index.js";
// ----------------------------------------------------
// Import UI Alerts
// ----------------------------------------------------
import { displayMessage } from "../../assets/js/ui/index.js";
// ----------------------------------------------------
// Import Validations
// ----------------------------------------------------
import { formatAmount } from "../../assets/js/utils/index.js";
// ----------------------------------------------------
// Import Formatters
// ----------------------------------------------------
import { formatCurrency, formatNum } from "./utils/index.js";

(async function ($) {
  ("use strict");

  $(".totals").css({ display: "flex" });
  $(".modal.info, .range-section").css({ display: "none" });

  // -------------------------------------------------
  // Filter Earnings By Currencies
  // -------------------------------------------------
  $(".currency-filter").on("change", function () {
    const currency = $(this).val().trim();
    filterCurrency(currency);
  });

  // -------------------------------------------------
  // Filter Sales Summary By Timeframes
  // -------------------------------------------------
  $(".commission-filter").on("change", function () {
    const value = $(this).val().trim();
    if (value === "") {
      displayMessage("Select a filter from the dropdown menu", "info");
      return;
    } else {
      $(".modal").css({ display: "flex" });
      getSummary(value);
    }
  });

  // -------------------------------------------------
  // Get Sales Summary Form Custom Dates
  // -------------------------------------------------
  $(".range-check").on("click", function () {
    const from = $(".date-from").val();
    const to = $(".date-to").val();
    if (!from || !to) {
      displayMessage("Enter a valid start and end date", "info");
      return;
    }
    getRangeSales(from, to);
  });

  // -------------------------------------------------
  // Close Overlay
  // -------------------------------------------------
  $(".close-overlay").on("click", function () {
    $(".modal").css({ display: "none" });
    $(".totals").find("span").text("");
  });

  // ----------------------------------------------------
  // Helper Functions Definitions
  // ----------------------------------------------------
  async function fetchDetails(payload) {
    try {
      const result = await makeRequest(
        `/order/sales/summary`,
        "GET",
        payload
      );

      if (result.status === 200 && result.message === "Stats fetched") {
        displayMessage(result.message, "success");

        const totalRevenue = formatAmount(result.data.total_revenue);
        const totalSales = formatNum(result.data.total_orders);

        $(".modal").css({ display: "flex" });
        $(".modal.info, .range-section").css({ display: "none" });
        $(".totals").css({ display: "flex" });
        $(".amount-section span").text(`${totalRevenue}`);
        $(".sales-section span").text(`${totalSales}`);
      } else {
        displayMessage(result.message, "info");
      }
    } catch (err) {
      displayMessage(`Network error occurred: ${err}`, "error");
    }
  }

  // Timeframes filetr
  function getSummary(period) {
    switch (period) {
      case "today":
      case "yesterday":
      case "last_week":
      case "last_month":
      case "last_year":
        $(".modal.info, .range-section").css({ display: "none" });
        $(".totals").css({ display: "flex" });
        // Get details from server
        fetchDetails({
          view: "vendor",
          storeId: null,
          period: period,
          start: null,
          end: null,
        });
        break;
      case "custom":
        $(".modal.info, .range-section").css({ display: "block" });
        $(".totals").css({ display: "none" });
        break;
    }
  }

  // Custom sales reports
  function getRangeSales(from, to) {
    if (!from || !to) {
      displayMessage("Select a range of dates to continue", "info");
      return;
    } else {
      const start = from.split("/");
      const end = to.split("/");
      const formatOne = start[0].split("-");
      const formatTwo = end[0].split("-");
      const formattedStart =
        formatOne[0] + "-" + formatOne[1] + "-" + formatOne[2];
      const formattedEnd =
        formatTwo[0] + "-" + formatTwo[1] + "-" + formatTwo[2];
      // Prepare data
      const payload = {
        view: "vendor",
        storeId: null,
        period: "custom",
        start: formattedStart,
        end: formattedEnd,
      };
      //Get details from server
      fetchDetails(payload);
    }
  }

  // Currency filter
  function filterCurrency(currency) {
    if (!currency) {
      displayMessage("Select a currrency to proceed", "info");
      return;
    }
    switch (currency) {
      case "dollar":
        $(".dashboard-item").each(function () {
          if ($(this).find(".amount-view").length > 0) {
            const amount =
              Number($(this).find(".amount-view").data("amount")) / 1000;
            const outputAmount = formatCurrency(amount);
            $(this).find("h3").empty().html(`&#x24;${outputAmount}`);
          }
        });
        break;
      case "naira":
        $(".dashboard-item").each(function () {
          if ($(this).find(".amount-view").length > 0) {
            const amount = Number($(this).find(".amount-view").data("amount"));
            const outputAmount = formatCurrency(amount);
            $(this).find("h3").empty().html(`&#x20A6;${outputAmount}`);
          }
        });
        break;
      case "cedis":
        $(".dashboard-item").each(function () {
          if ($(this).find(".amount-view").length > 0) {
            const amount =
              Number($(this).find(".amount-view").data("amount")) / 100;
            const outputAmount = formatCurrency(amount);
            $(this).find("h3").empty().html(`&#x20B5;${outputAmount}`);
          }
        });
        break;
      case "shillings":
        $(".dashboard-item").each(function () {
          if ($(this).find(".amount-view").length > 0) {
            const amount =
              Number($(this).find(".amount-view").data("amount")) * 0.077;
            const outputAmount = formatCurrency(amount);
            $(this).find("h3").empty().html(`&#83;${outputAmount}`);
          }
        });
        break;
      case "cefa":
        $(".dashboard-item").each(function () {
          if ($(this).find(".amount-view").length > 0) {
            const amount =
              Number($(this).find(".amount-view").data("amount")) * 0.37;
            const outputAmount = formatCurrency(amount);
            $(this).find("h3").empty().html(`&#x20A3;${outputAmount}`);
          }
        });
        break;
      case "rand":
        $(".dashboard-item").each(function () {
          if ($(this).find(".amount-view").length > 0) {
            const amount =
              Number($(this).find(".amount-view").data("amount")) * 0.011;
            const outputAmount = formatCurrency(amount);
            $(this).find("h3").empty().html(`&#82;${outputAmount}`);
          }
        });
        break;
    }
  }
})(jQuery);