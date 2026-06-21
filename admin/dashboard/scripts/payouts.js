// ----------------------------------------------------
// Import Configs
// ----------------------------------------------------
import { makeRequest } from "../../../assets/js/core/index.js";
// ----------------------------------------------------
// Import UI Alerts
// ----------------------------------------------------
import { displayMessage } from "../../../assets/js/ui/index.js";
// ----------------------------------------------------
// Import Validations
// ----------------------------------------------------
import {
  formatAmount,
  extractAmount,
} from "../../../assets/js/utils/index.js";
// ----------------------------------------------------
// Import Formatters
// ----------------------------------------------------
import { formatNum } from "../scripts/utils/index.js";

(async function ($) {
  ("use strict");

  const params = new URLSearchParams(window.location.search);
  const status = params.has("status")
    ? params.get("status")
    : "Pending";
  const payoutDay = "Tuesday";

  // -------------------------------------------------
  // Initialize Counter
  // -------------------------------------------------
  let offset = 1;
  const limit = 20;

  // Get total amount generated
  const totalAmount = $(".btn-tool").data("sum") || 0;

  // Remove zero value rows
  $("tbody tr").each(function () {
    if ($(this).hasClass("content-row")) {
      const value = $(this).find(".amount").text().trim();
      const amount = extractAmount(value);
      if (!amount || amount === 0 || isNaN(amount)) {
        $(this).remove();
        return; // or handle error
      }
    }
  });

  //Get currency
  updateCurrency();

  // Format counters
  countTotal(`${status}`);

  //Get current day and week
  const currentDate = new Date();
  const weekdays = [
    "Sunday",
    "Monday",
    "Tuesday",
    "Wednesday",
    "Thursday",
    "Friday",
    "Saturday",
  ];
  const currentDay = weekdays[currentDate.getDay()];
  const week = currentDate.toLocaleDateString("en-US", {
    month: "long",
    day: "numeric",
    year: "numeric",
  });
  const narration = `Mrsamase Payout`;
  const title = `Payout for ${currentDay}, ${week}`;

  /*
    SINGLE TRANSFER
  */
  $(".action button").each(function () {
    $(this).on("click", async function () {
      if ($(this).text().trim() === "View") {
        return;
      }

      const parentContainer = $(this).closest("tr.content-row");
      const fullname = parentContainer.find(".fullname").text().trim();
      const email = parentContainer.find(".email").text().trim();
      const account = parentContainer.find(".account").text().trim();
      const bank = parentContainer.find(".bank").text().trim();
      const code = parentContainer.find(".code").text().trim();
      const currency = parentContainer.find(".currency").text().trim();
      const reference = parentContainer.find(".reference").text().trim();
      const amount = extractAmount(parentContainer.find(".amount").text().trim());
      if (isNaN(amount)) {
        displayMessage(`Invalid amount ${amount}`, "info");
        return;
      }

      if (currentDay !== payoutDay) {
        displayMessage(`Payout is only done on ${payoutDay}s`, "info");
        $(this).text("Pay");
        return;
      } else {
        //Filter beneficiaries
        if (account && account !== 0 && account.length >= 10) {
          $(this).text("Processing...").attr("disabled", true);
          //Send transfer data to server
          try {
            const payload = {
              bank: code,
              account: account,
              amount: amount,
              narration: narration,
              currency: currency,
              reference: reference,
              name: fullname,
              email: email,
            };

            const result = await makeRequest(
              `/wallet/transfer/single`,
              "POST",
              payload
            );

            if (
              result.status === 200 &&
              result.message === "Transfer queued successfully"
            ) {
              displayMessage(result.message, "success");
              $("tbody tr").each(function (index, el) {
                if ($(this).find(".email").text() === email) {
                  $(this)
                    .find(".status button")
                    .removeClass("btn btn-danger btn-sm")
                    .addClass("btn btn-info btn-sm")
                    .text("Processing");
                  $(this)
                    .find(".action button")
                    .removeClass("btn btn-info btn-sm")
                    .addClass("btn btn-success btn-sm")
                    .html(
                      "<i class='fas fa-check' style='padding-right: 5px;'></i> Queued"
                    )
                    .attr("disabled", true)
                    .css({ background: "green", width: "80px" });
                }
              });
            } else {
              //displayMessage(result.message, "info");
              displayMessage(result.error.message, "info");
              $(this).text("Pay").attr("disabled", false);
            }
          } catch (err) {
            displayMessage(`Network error occurred: ${err}`, "error");
            $(this).text("Pay").attr("disabled", false);
          }
        } else {
          displayMessage("Invalid account details", "info");
          return;
        }
      }
    });
  });

  /*
    BULK TRANSFER
  */
  let beneficiaries = [];

  $(".btn-all").on("click", async function () {
    if (currentDay !== payoutDay) {
      displayMessage(`Payout is only done on ${payoutDay}s`, 'info');
      return;
    } else {
      $(this).text("Processing...").attr("disabled", true);
      //Loop through table row data
      $("tbody tr.content-row").each(function () {
        const fullname = $(this).find(".fullname").text().trim();
        const email = $(this).find(".email").text().trim();
        const account = $(this).find(".account").text().trim();
        const bank = $(this).find(".bank").text().trim();
        const code = $(this).find(".code").text().trim();
        const currency = $(this).find(".currency").text().trim();
        const reference = $(this).find(".reference").text().trim();
        const amount = extractAmount($(this).find(".amount").text().trim());
        if (isNaN(amount)) {
          displayMessage(`Invalid amount ${amount}`, "info");
          return;
        }

        if (account && account !== 0 && account.length >= 10) {
          const recipient = {
            bank_code: code,
            account_number: account,
            amount: amount,
            narration: narration,
            currency: currency,
            reference: reference,
            name: fullname,
            email: email,
          };
          beneficiaries.push(recipient);
        }
      });

      // const recipients = JSON.stringify(beneficiaries);

      try {
        const payload = {
          title: title,
          bulk_data: beneficiaries,
        };

        const result = await makeRequest(
          `/wallet/transfer/bulk`,
          "POST",
          payload
        );

        if (
          result.status === 200 &&
          result.message === "Bulk transfer queued successfully"
        ) {
          displayMessage(result.message, "success");
          $(this).text("Pay All").attr("disabled", true);
        } else {
          //displayMessage(result.message, "info");
          displayMessage(result.error.message, "info");
          $(this).text("Pay All").attr("disabled", false);
        }
      } catch (err) {
        displayMessage(`Network error occurred: ${err}`, "error");
        $(this).text("Pay All").attr("disabled", false);
      }
    }
  });

  //Search function
  $(".page-search").on("input blur", function () {
    const searchValue = $(this).val().toLowerCase();
    if (searchValue) {
      $(".content-row").each(function (index, el) {
        if ($(this).find("td").text().toLowerCase().includes(searchValue)) {
          $(this).css({ display: "table-row" });
        } else {
          $(this).css({ display: "none" });
        }
      });
    } else {
      displayMessage("Enter a valid search", "info");
      $(".content-row").each(function (index, el) {
        if ($(this).css("display") === "none") {
          $(this).css({ display: "table-row" });
        }
      });
    }
  });

  // -------------------------------------------------
  // Load More Notifications
  // -------------------------------------------------
  $(".btn-load").on("click", async function () {
    $(this).text("Processing...");
    offset++;
    // Fetch payments
    await fetchPayments({ status: status, page: offset, total: limit });
  });

  // ----------------------------------------------------
  // Helper Functions Definitions
  // ----------------------------------------------------
  function updateCurrency() {
    $.getJSON("../../countries.json", function (data) {
      for (const key in data) {
        const content = data[key];
        const country = content.country_name;
        const code = content.currency_code;
        //Loop through each row and update the currency by country
        $("tbody tr").each(function () {
          if ($(this).find(".country").text() === country) {
            $(this).find(".currency").empty().text(code);
          }
        });
      }
    });
  }

  async function fetchPayments(payload) {
    try {
      const result = await makeRequest(
        `/wallet/payouts/status`,
        "GET",
        payload
      );

      if (
        result.data &&
        result.data !== null &&
        result.data.payments.length > 0
      ) {
        // Display payments
        displayPayments(result.data.payments);
      } else {
        displayMessage("No more payments found", "info");
        $(".btn-load").hide();
      }
    } catch (err) {
      displayMessage(`Network error occurred: ${err}`, "error");
    }
  }

  // Timeframes filetr
  function displayPayments(payments) {
    if (!payments || payments.length === 0) {
      // No payments
      displayMessage("No more payments found", "info");
      return;
    }

    // Set status maps
    const statusMaps = {
      Pending: { style: "btn-danger", text: "Pay" },
      Completed: { style: "btn-success", text: "View" },
    };

    payments.forEach((payment) => {
      const {
        withdrawal_id,
        firstname,
        lastname,
        email,
        country,
        amount,
        account,
        bank,
        bank_code,
        currency_code,
        reference,
        created_at,
        withdrawal_status,
      } = payment;

      const html = `
        <tr class='content-row' data-id='${withdrawal_id}'>
          <td>#</td>
          <td class='fullname'>${firstname} ${lastname}</td>
          <td class='email'>${email}</td>
          <td class='country'>${country}</td>
          <td class='amount'> ${formatAmount(amount ?? 0)}</td>
          <td class='account'>${account}</td>
          <td class='bank'>${bank}</td>
          <td class='code'>${bank_code}</td>
          <td class='currency'>${currency_code}</td>
          <td class='reference'>${reference}</td>
          <td class='time'>${created_at}</td>
          <td class='status'>
            <button class='btn ${
              statusMaps[withdrawal_status]["style"]
            } btn-sm'>
              ${withdrawal_status}
            </button> 
          </td>
          <td class='action'><button class='btn btn-info btn-sm'>${withdrawal_status === "Pending" ? "Pay" : "View"}</button></td>
        </tr>
      `;
      $("tbody").append(html);
    });

    countTotal(`${status}`);
  }

  //Get count
  function countTotal(type = "Pending") {
    const totalPayout = formatAmount(totalAmount || 0);
    const total = $("tbody tr.content-row").length || 0;

    $(".content-header h1 b").text(
      `${type} Payouts (${formatNum(total)} Beneficiaries, ${totalPayout})`
    );
  }
})(jQuery);
