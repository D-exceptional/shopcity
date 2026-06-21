// ----------------------------------------------------
// Import Formatters
// ----------------------------------------------------
import { currentDay, week, formatNum } from "./utils/index.js";
// ----------------------------------------------------
// Import  External Utilities
// ----------------------------------------------------
import {
  makeRequest,
  BASE_CONVERSION_RATE
} from "../../assets/js/core/index.js";
// ----------------------------------------------------
// Import UI Alerts
// ----------------------------------------------------
import { displayMessage } from "../../assets/js/ui/index.js";


(async function ($) {
  ("use strict");

  // -------------------------------------------------
  // Initialize Counter
  // -------------------------------------------------
  let offset = 1;
  const limit = 20;

  const bank = $(".btn-withdraw").data("bank");
  const account = $(".btn-withdraw").data("account"); 

  // -------------------------------------------------
  // Validate Input
  // -------------------------------------------------
  $(".withdrawal_amount").on("input", function () {
    const availableAmount = parseFloat($(".available_amount").val());
    const withdrawalAmount = parseFloat($(this).val());
    const formattedAvailableAmount = formatNum(`${availableAmount} Coins`);

    // Check if the withdrawal amount is a valid number
    if (!withdrawalAmount) {
      notifyUser("Enter a valid amount");
      clearError();
    }
    // If the withdrawal amount is zero
    else if (withdrawalAmount === "0.00") {
      notifyUser("Enter a non-zero amount");
      clearError();
    }
    // If available balance is zero
    else if (availableAmount === "0.00") {
      notifyUser("Insufficient balance");
      clearError();
    }
    // If the withdrawal amount exceeds the available amount
    else if (withdrawalAmount > availableAmount) {
      notifyUser(
        `Enter an amount not greater than <b>${formattedAvailableAmount}</b>`
      );
      clearError();
    }
    // If the withdrawal amount is valid
    else {
      $(".info-span").text("");
      $(".btn-withdraw").attr("disabled", false);
    }
  });

  // -------------------------------------------------
  // Place Withdrawal
  // -------------------------------------------------
  $(".btn-withdraw").on("click", async function () {
    $(this).attr("disabled", true);
    const availableAmount = parseFloat($(".available_amount").val());
    const withdrawalAmount = parseFloat($(".withdrawal_amount").val());
    const amount = formatNum(`${availableAmount} Coins`);
    const withdraw = formatNum(`${withdrawalAmount} Coins`);

    if (
      ["None", "Null", "null", "none", undefined].includes(bank) ||
      !account ||
      account === 0 ||
      account.length < 10 ||
      account === undefined
    ) {
      notifyUser(
        "Add your bank details via your profile page to enable withdrawal"
      );
      clearError();
      return;
    }
    // Check if the withdrawal amount is a valid number
    else if (withdrawalAmount === "") {
      notifyUser("Enter a valid amount");
      clearError();
    }
    // If the withdrawal amount is zero
    else if (withdrawalAmount === 0) {
      notifyUser("Enter a non-zero amount");
      clearError();
    }
    // If available balance is zero
    else if (availableAmount === 0) {
      notifyUser("Insufficient balance");
      clearError();
    }
    // If the withdrawal amount exceeds the available amount
    else if (withdrawalAmount > availableAmount) {
      notifyUser(`Enter an amount not greater than <b>${amount}</b>`);
      clearError();
    } else {
      //Do something else
      $(".info-span").html(``).css({ color: "gray" });
      $(".btn-withdraw").attr("disabled", true);

      // Set narration
      const narration = `Withdrawal request of ${withdraw} for ${currentDay}, ${week}`;

      // Prepare data
      const payload = {
        amount: withdrawalAmount,
        bank: bank,
        account: account,
        narration: narration,
      };

      // Place withdrawal
      try {
        const result = await makeRequest(
          `/wallet/fund/request`,
          "POST",
          payload
        );

        if (
          result.status === 200 &&
          result.message === "Withdrawal successful"
        ) {
          notifyUser(
            `Your withdrawal request for <b>${withdraw}</b> was successful. Check your mail for more details`,
            "success"
          );

          setTimeout(function () {
            setTimeout(() => {
              $(".overlay").css({ display: "none" });
              $(".withdrawal_amount").val("");
              clearError();
            }, 2000);
            window.location.reload();
          }, 3000);
        } else {
          notifyUser(`${result.message}`);
        }
      } catch (err) {
        notifyUser(`Network error occurred: ${err}`);
      }
    }
  });

  $(".btn-start").on("click", function () {
    $(".overlay").css({ display: "flex" });
    clearError();
  });

  // -------------------------------------------------
  // Close Overlay
  // -------------------------------------------------
  $(".close-overlay").on("click", function () {
    $(".overlay").css({ display: "none" });
    $(".withdrawal_amount").val("");
    clearError();
  });

  // -------------------------------------------------
  // Load More Notifications
  // -------------------------------------------------
  $(".load-more").on("click", async function () {
    offset++;
    // Fetch notifications
    await fetchWithdrawals({ type: "withdrawals", page: offset, limit: limit });
  });

  // ----------------------------------------------------
  // Helper Functions Definitions
  // ----------------------------------------------------
  function clearError() {
    setTimeout(() => {
      $(".info-span").text(``).css({ color: "gray" });
      $(".btn-withdraw").attr("disabled", false);
    }, 1000);
  }

  function notifyUser(message, type = "warning") {
    $(".info-span")
      .html(message)
      .css({ color: type === "warning" ? "red" : "green" });
    $(".btn-withdraw").attr("disabled", type === "warning" ? true : false);
  }

  async function fetchWithdrawals(payload) {
    try {
      const result = await makeRequest(
        `/wallet/payments/user`,
        "GET",
        payload
      );

      if (result.data && result.data !== null && result.data.length > 0) {
        // Display withdrawals
        displayWithdrawals(result.data);
      } else {
        displayMessage("No more withdrawals found", "info");
        $(".load-more").hide();
      }
    } catch (err) {
      displayMessage(`Network error occurred: ${err}`, "error");
    }
  }

  // Timeframes filetr
  function displayWithdrawals(withdrawals) {
    if (!withdrawals || withdrawals.length === 0) {
      // No notifications
      displayMessage("No more withdrawals found", "info");
      return;
    }

    withdrawals.forEach((withdrawal) => {
      const {
        withdrawal_id,
        amount,
        bank,
        account,
        reference,
        narration,
        withdrawal_status,
        created_at,
      } = withdrawal;

      const html = `
        <tr data-id='${withdrawal_id}'>
          <td>#</td>
          <td>${formatNum(amount / BASE_CONVERSION_RATE)}</td>
          <td>${bank}</td>
          <td>${account}</td>
          <td>${reference}</td>
          <td>${narration}</td>
          <td>
            <button class='btn ${withdrawal_status === 'Completed' ? 'btn-success' : 'btn-danger'} btn-sm'>
              ${withdrawal_status}
            </button> 
          </td>
          <td>${created_at}</td>
          <td>
            <button class='btn btn-info btn-sm'>
              View
            </button> 
          </td>
        </tr>
      `;
      $("tbody").append(html);
    });
  }
})(jQuery);