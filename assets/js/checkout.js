// ----------------------------------------------------
// Import Configs
// ----------------------------------------------------
import {
  baseUrl,
  FLAT_RATE,
  SHIPPING_RATE,
  TAX_RATE,
  makeRequest,
} from "./core/index.js";
// ----------------------------------------------------
// Import UI Alerts
// ----------------------------------------------------
import { displayMessage } from "./ui/index.js";
// ----------------------------------------------------
// Import Validations
// ----------------------------------------------------
import { validateInput, extractAmount } from "./utils/index.js";


(function ($) {
  ("use strict");

  // -------------------------------------------------
  // Initialize Coupon Store and Checkout Variables
  // -------------------------------------------------
  let couponStore = null; // Store ID that the coupon applies to
  let checkoutDiscount = 0; // Discount percentage from valid coupon
  $(".coupon-section").css("display", "none"); // Hide coupon area initially

  // -------------------------------------------------
  // Get Current Location Details
  // -------------------------------------------------
  const currentAddress = $(".delivery-address").val();
  const currentCity = $(".delivery-city").val();
  const currentCode = $(".delivery-postcode").val();

  // -------------------------------------------------
  // Detect if All Cart Items Belong to the Same Store
  // -------------------------------------------------
  const storeIds = [];
  $(".checkout-item").each(function () {
    const sid = $(this).data("sid"); // Read store ID from each product row
    storeIds.push(sid);
  });

  // Check if all store IDs are identical
  const allSame = storeIds.every((sid) => sid === storeIds[0]);
  if (allSame) {
    couponStore = storeIds[0]; // Save the single store ID
    $(".coupon-section").css("display", "block"); // Show coupon input section
  } else {
    $(".coupon-section").css("display", "none"); // Hide coupon input for multi-store checkout
  }

  // -------------------------------------------------
  // Validate Coupon Input on Typing
  // -------------------------------------------------
  $(document).on("input", ".coupon-code", function () {
    validateInput(this); // Validate based on input data-tag
  });

  // -------------------------------------------------
  // Validate Billing Form Inputs on Typing
  // -------------------------------------------------
  $(document).on("input", ".form-location .form-control", function () {
    validateInput(this);
  });

  // -------------------------------------------------
  // Update Billing Details via API (PUT /billing)
  // -------------------------------------------------
  $(document).on("click", ".btn-location", async function () {
    // Get latest details
    const address = validateInput($(".delivery-address"));
    const city = validateInput($(".delivery-city"));
    const code = validateInput($(".delivery-postcode"));

    // Check for any changes
    if (
      address === currentAddress &&
      city === currentCity &&
      code === currentCode
    ) {
      displayMessage("No changes made", "info");
      return;
    }

    // Flag to detect invalid fields
    let invalidFound = false;

    // Check all required inputs at once
    $(".form-location .form-control").each(function () {
      const name = $(this).data("name") || "field";
      const value = validateInput(this) || "";

      // Check for placeholder-like text (e.g., "Enter your city")
      const lowerValue = value.toLowerCase().trim();

      const invalidWords = ["enter", "your", "type", "input"];
      const containsInvalidWord = invalidWords.some((word) =>
        lowerValue.includes(word)
      );

      if (!value || containsInvalidWord || value.length < 2) {
        displayMessage(`Enter a valid value for: ${name}`, "warning");
        $(this).addClass("is-invalid"); // optional: red border
        invalidFound = true;
      } else {
        $(this).removeClass("is-invalid");
      }
    });

    if (invalidFound) {
      return; // stop further processing
    }

    // Final empty check
    if (!address || !city || !code) {
      displayMessage("Some fields are empty", "info");
      return;
    }

    // Update button state
    $(this).text("Processing...").prop("disabled", true);

    // Prepare data payload for API
    const payload = {
      address: address,
      city: city,
      code: parseInt(code),
    };

    try {
      // Send billing update to backend
      const result = await makeRequest(
        `/user/billing`,
        "PUT",
        payload
      );

      if (
        result.status === 200 &&
        [
          "Details created successfully",
          "Details updated successfully",
        ].includes(result.message)
      ) {
        displayMessage("Details updated", "success");
      } else {
        displayMessage("Could not update details", "warning");
      }

      $(this).text("Update Details").prop("disabled", false);
    } catch (err) {
      displayMessage(
        "Network error occured, updating offline wishlist",
        "warning"
      );
      $(this).text("Update Details").prop("disabled", false);
    }
  });

  // -------------------------------------------------
  // Apply Coupon (POST /store/coupon/check)
  // -------------------------------------------------
  $(document).on("click", ".btn-coupon", async function () {
    const coupon = validateInput($(".coupon-code"));

    if (!coupon) {
      displayMessage(`Please enter a valid coupon`, "info");
      return false;
    }

    // Build request payload
    const data = {
      coupon: coupon,
      storeId: couponStore,
    };

    // Show loading state
    $(this).text("Verifying...").prop("disabled", true);

    try {
      const result = await makeRequest(
        `/store/coupon/check`,
        "POST",
        data
      );

      if (
        result.status === 200 &&
        result.message === "Coupon fetched successfully"
      ) {
        checkoutDiscount = result.data.coupon_discount;
        const couponStatus = result.data.coupon_status;

        if (["Active"].includes(couponStatus)) {
          displayMessage("Coupon validated", "success");

          // Extract subtotal value from UI
          let currentSubtotal = extractAmount($(".subtotal").text());
          // Apply discount
          let discountAmount =
            currentSubtotal - (checkoutDiscount / 100) * currentSubtotal;

          // Update subtotal and total in DOM
          $(".subtotal").text(
            `₦ ${Math.round(discountAmount).toLocaleString()}`
          );
          $(".checkout-total").text(
            `₦ ${Math.round(discountAmount + FLAT_RATE).toLocaleString()}`
          );

          $(this).text("Apply Coupon").prop("disabled", false);
        } else {
          displayMessage("Could not validate coupon", "info");
          $(this).text("Apply Coupon").prop("disabled", true);
        }
      } else {
        displayMessage("Failed to fetch coupon details", "warning");
        $(this).text("Apply Coupon").prop("disabled", false);
      }
    } catch (err) {
      displayMessage("Network error occured", "warning");
      $(this).text("Apply Coupon").prop("disabled", false);
    }
  });

  // -------------------------------------------------
  // Checkout Handler (POST /order/checkout)
  // -------------------------------------------------
  $(document).on("click", ".btn-checkout", async function () {
    const title = $(this).text().trim();
    const balance = parseFloat($(".wallet-balance").text().trim());
    // Decide button text depending on balance
    const buttonText =
      balance >= extractAmount($(".checkout-total").text().trim())
        ? "Place Order"
        : "Topup Wallet";

    // Redirect to wallet if user doesn’t have enough funds
    if (title === "Topup Wallet") {
      window.location.href = `${baseUrl}/wallet`;
    } else {
      const cartItems = [];

      // Collect cart items and build array for API
      $(".checkout-item").each(function () {
        const item = {
          product_id: $(this).data("pid"),
          quantity: $(this).find(".item-quantity").text().trim(),
          price: extractAmount($(this).find(".item-price").text().trim()),
          store_id: $(this).data("sid"),
        };
        cartItems.push(item);
      });

      // Build full checkout payload
      const payload = {
        subtotal: extractAmount($(".subtotal").text().trim()),
        tax: TAX_RATE,
        discount: checkoutDiscount,
        shipping: SHIPPING_RATE,
        total: extractAmount($(".checkout-total").text().trim()),
        address: $(".delivery-address").val().trim(),
        items: cartItems,
      };

      // Check address before proceeding
      if (!payload?.address) {
        return displayMessage(
          "Specify a delivery address for this order",
          "warning"
        );
      }

      $(this).text("Processing...").prop("disabled", true);

      try {
        // Send checkout request to backend
        const result = await makeRequest(
          `/checkout`,
          "POST",
          payload
        );

        if (
          result.status === 200 &&
          result.message === "Your order has been created successfully"
        ) {
          displayMessage("Your order has been created successfully", "success");
          // Refresh page after 1.5s
          setTimeout(() => {
            window.location.reload();
          }, 1500);
        } else {
          displayMessage("Could not process order", "warning");
          $(this).text(`${buttonText}`).prop("disabled", false);
        }
      } catch (err) {
        displayMessage("Network error occured", "warning");
        $(this).text(`${buttonText}`).prop("disabled", false);
      }
    }
  });
})(jQuery);
