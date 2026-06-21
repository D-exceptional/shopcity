// ----------------------------------------------------
// Import Configs
// ----------------------------------------------------
import { makeRequest } from "../../assets/js/core/index.js";
// ----------------------------------------------------
// Import UI Alerts
// ----------------------------------------------------
import { displayMessage } from "../../assets/js/ui/index.js";

(async function ($) {
  "use strict";
  let bankCode = "";

  const initialText = $(".btn-bank").text();

  $(".form-account").on("input", function () {
    const account = $(this).val();
    const bank = $(".form-bank").val().trim();

    if (!bank || bank == "None") {
      displayMessage("Select a bank to proceed", "info");
      $(".btn-bank").attr("disabled", true);
      return;
    }
    if (account === "") {
      displayMessage("Enter a valid account number", "info");
      $(".btn-bank").attr("disabled", true);
      return;
    }
    else if (account.length === 10) {
      $(this).blur();
      $(".btn-bank").attr("disabled", false);
      //Get bank code
      $.each(jsonData, function (index, obj) {
        if (obj.name === bank) {
          bankCode = obj.code;
        }
      });
    }
  });

  $(".form-bank").on("change", function () {
    $(".btn-bank").attr("disabled", true);
    const name = $(this).val().trim();
    const account = $(".form-account").val();

    if (!name || name == "None") {
      displayMessage("Select a valid bank", "info");
      $(".btn-bank").attr("disabled", true);
      return;
    }
    else if (!account || account === 0) {
      displayMessage("Enter a valid account number", "info");
      $(".btn-bank").attr("disabled", true);
      return;
    } else if (account === "") {
      displayMessage("Account number field is empty", "info");
      $(".btn-bank").attr("disabled", true);
      return;
    } else if (account.length < 10) {
      displayMessage("Account number must be ten digits", "info");
      $(".btn-bank").attr("disabled", true);
      return;
    } else if (account.length > 10) {
      displayMessage("Account number must not exceed ten digits", "info");
      $(".btn-bank").attr("disabled", true);
      return;
    } else {
      $(".btn-bank").attr("disabled", false);
      //Get bank code
      $.each(jsonData, function (index, obj) {
        if (obj.name === name) {
          bankCode = obj.code;
        }
      });
    }
  });

  $(".btn-bank").on("click", async function () {
    const account = $(".form-account").val();
    const bank = $(".form-bank").val().trim();
    const code = bankCode;

    if (!bank || bank == "None") {
      displayMessage("Select a valid bank", "info");
      $(".btn-bank").attr("disabled", true);
      return;
    }
    else if (!account || account === 0) {
      displayMessage("Enter a valid account number", "info");
      $(".btn-bank").attr("disabled", true);
      return;
    } else if (account === "") {
      displayMessage("Account number field is empty", "info");
      $(".btn-bank").attr("disabled", true);
      return;
    } else if (account.length < 10) {
      displayMessage("Account number must be ten digits", "info");
      $(".btn-bank").attr("disabled", true);
      return;
    } else if (account.length > 10) {
      displayMessage("Account number must not exceed ten digits", "info");
      $(".btn-bank").attr("disabled", true);
      return;
    } else {
      $(".btn-bank").prop("disabled", true).val("Processing...");
      
      try {
       const payload = {
         account: parseInt(account),
         bank: bank,
         code: code,
       };

        const result = await makeRequest(
          `/wallet/details/update`,
          "PUT",
          payload
        );

        if (
          result.status === 200 &&
          result.message === "Details updated successfully"
        ) {
          displayMessage(result.message, "success");
          $(".btn-bank").prop("disabled", false).text(initialText);
        } else {
          displayMessage(result.message, "info");
          $(".btn-bank").prop("disabled", false).text(initialText);
        }
      } catch (err) {
        displayMessage(`Network error occurred: ${err}`, "error");
       $(".btn-bank").prop("disabled", false).text(initialText);
      }
    }
  });
})(jQuery);