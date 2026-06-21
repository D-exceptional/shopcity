// ----------------------------------------------------
// Import Request Builder
// ----------------------------------------------------
import {
  makeRequest,
  FLW_PUBLIC_KEY,
  BASE_CURRENCY,
  eurozoneCountries,
  francophoneCountries,
} from "../core/index.js";
// ----------------------------------------------------
// Import UI Alerts
// ----------------------------------------------------
import { displayMessage } from "../ui/index.js";

// ---------------------------------------------------------------
// Set Payment Option
// ---------------------------------------------------------------
export function setPaymentOptions(country) {
  let paymentOptions = null;
  if (country === "Nigeria") {
    paymentOptions =
      "card, ussd, banktransfer, account, internetbanking, nqr, applepay, googlepay, enaira, opay";
  } else if (
    country === "United States" ||
    country === "United Kingdom" ||
    eurozoneCountries.includes(country)
  ) {
    paymentOptions = "card, account, googlepay, applepay";
  } else if (country === "Ghana") {
    paymentOptions = "card, ghanamobilemoney";
  } else if (francophoneCountries.includes(country)) {
    paymentOptions = "card, mobilemoneyfranco";
  } else if (country === "South Africa") {
    paymentOptions = "card, account, 1voucher, googlepay, applepay";
  } else if (country === "Malawi") {
    paymentOptions = "card, mobilemoneymalawi";
  } else if (country === "Kenya") {
    paymentOptions = "card, mpesa";
  } else if (country === "Uganda") {
    paymentOptions = "card, mobilemoneyuganda";
  } else if (country === "Rwanda") {
    paymentOptions = "card, mobilemoneyrwanda";
  } else if (country === "Tanzania") {
    paymentOptions = "card, mobilemoneytanzania";
  } else {
    displayMessage("Country not supported", "info");
    paymentOptions = "";
  }

  return paymentOptions;
}

// ---------------------------------------------------------------
// Verify Payment
// ---------------------------------------------------------------
export async function verifyPayment(id, reference, amount, currency) {
  if (!id || !reference || !amount || !currency)
    return displayMessage("Missing transaction details", "info");

  // Prepare data
  const payload = { id: id, reference: reference };

  try {
    // Send payment request to backend
    const result = await makeRequest(
      `/wallet/payment/verify`,
      "POST",
      payload
    );

    if (
      result.status === 200 &&
      result.message === "Your wallet has been credited successfully"
    ) {
      displayMessage(
        "Payment was successful. Check your mail for more details",
        "success"
      );
      // Update UI
      $(".topup-amount").val("");
      $(".wallet-balance").text(result.data.balance);
      $(".btn-topup").text("Topup Wallet").prop("disabled", false);
    } else {
      displayMessage("Could not verify payment", "warning");
      $(this).text("Topup Wallet").prop("disabled", false);
    }
  } catch (err) {
    displayMessage("Network error occured", "warning");
    $(this).text("Topup Wallet").prop("disabled", false);
  }
}

// ---------------------------------------------------------------
// Make Payment
// ---------------------------------------------------------------
export function makePayment(options, amount, reference, user) {
  if ([null, ""].includes(options)) {
    return displayMessage("No payment option found", "info");
  }
  // Process payment
  FlutterwaveCheckout({
    public_key: FLW_PUBLIC_KEY,
    tx_ref: reference, // Should be unique for every payment
    amount: amount, // Specify amount here
    currency: BASE_CURRENCY, // Default currency of platform account
    payment_options: options,
    customer: {
      email: user.email,
      phone_number: user.phone,
      name: user.name,
    },
    customizations: {
      title: "Checkout Payment",
      description: "Payment for products on Mrsamase e-commerce",
      logo: "https://www.logolynx.com/images/logolynx/22/2239ca38f5505fbfce7e55bbc0604386.jpeg",
    },
    configurations: {
      session_duration: 30, //Session timeout in minutes (maxValue: 1440 minutes)
      max_retry_attempt: 5, //Max retry (int)
    },
    // Callback after payment
    callback: function (response) {
      if (response.status === "completed") {
        verifyPayment(
          response.transaction_id,
          response.tx_ref,
          response.amount,
          response.currency
        );
        displayMessage("Verifying payment...", "success");
      } else {
        displayMessage("Payment not successful.", "info");
      }
    },
    // Called when user closes the payment modal
    onclose: function (incomplete) {
      console.log("Payment modal closed:", incomplete);
      if (incomplete === true) {
        // Modal closed before completing payment
        displayMessage(
          "User closed payment modal without completing transaction.",
          "info"
        );
      }
    },
  });
}
