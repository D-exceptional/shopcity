// ----------------------------------------------------
// Import Configs
// ----------------------------------------------------
import {
 FLAT_RATE
} from "../core/index.js";

// -------------------------------
// Extract Numerical Amount Part
// -------------------------------
export function extractAmount(value) {
  // Remove any non-digit, non-dot, and non-comma characters
  let clean = value.replace(/[^0-9.,]/g, "");

  // Remove commas (thousand separators)
  clean = clean.replace(/,/g, "");

  // Convert to float
  return parseFloat(clean);
}

// -------------------------------
// Get Cart Total
// -------------------------------
export function getCartTotal() {
  let total = 0;

  $(".item-total").each(function () {
    let value;

    // Detect if element is an <input> (has .val())
    if ($(this).is("input, textarea, select")) {
      value = $(this).val();
    } else {
      value = $(this).text();
    }

    // Clean and convert the value to a number
    const amount = extractAmount(value?.trim() || "0");

    // Add to total only if it's a valid number
    if (!isNaN(amount)) {
      total += amount;
    }
  });

  // Return the rounded total
  return Math.round(total);
}

// -------------------------------------------
// Update Cart Page On Cart -> (Add/ Remove)
// -------------------------------------------
export function updateCartPage() {
  // Get total
  const cartTotal = getCartTotal();

  // Update sub total
  $(".sub-total").text(`₦ ${Math.round(cartTotal).toLocaleString()}`);

  // Update sub total
  $(".cart-sum").text(
    `₦ ${Math.round(cartTotal + FLAT_RATE).toLocaleString()}`
  );
}

// -------------------------------
// Filter By Amount Via Scroll
// -------------------------------
let currentValue = 1000;
let animationFrame;

export function updateAmount(value) {
  // Round to nearest 1000
  const rounded = Math.round(value / 1000) * 1000;

  // Animate number transition
  cancelAnimationFrame(animationFrame);
  const output = $(".output-range");

  const start = currentValue;
  const end = rounded;
  const duration = 250;
  const startTime = performance.now();

  function animate(time) {
    const progress = Math.min((time - startTime) / duration, 1);
    const eased = start + (end - start) * progress;

    // Format as currency
    output.text(`₦ ${Math.round(eased).toLocaleString()}`);

    if (progress < 1) {
      animationFrame = requestAnimationFrame(animate);
    } else {
      currentValue = end;
    }
  }

  animationFrame = requestAnimationFrame(animate);
}
