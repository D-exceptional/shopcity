// ----------------------------------------------------
// Import UI Alerts
// ----------------------------------------------------
import { displayMessage } from "../ui/index.js";
// ----------------------------------------------------
// Import Request Builder
// ----------------------------------------------------
import { makeRequest, apiUrl } from "../core/index.js";

// ---------------------------------------------------------------
// Format Stars
// ---------------------------------------------------------------
function renderStars(ratingAverage, maxStars = 5) {
  // Ensure rating is within valid range
  const rating = Math.max(0, Math.min(ratingAverage, maxStars));

  let html = '<div class="d-flex">';
  const fullStars = Math.floor(rating); // number of full stars
  const halfStar = rating - fullStars >= 0.5; // whether to show a half star
  const emptyStars = maxStars - fullStars - (halfStar ? 1 : 0);

  // Add full stars
  for (let i = 0; i < fullStars; i++) {
    html += '<i class="fas fa-star text-primary"></i>';
  }

  // Add half star (if applicable)
  if (halfStar) {
    html += '<i class="fas fa-star-half text-primary"></i>';
  }

  // Add empty stars
  for (let i = 0; i < emptyStars; i++) {
    html += '<i class="far fa-star"></i>'; // Outline star
  }

  html += "</div>";
  return html;
}

// ---------------------------------------------------------------
// Get ISO Date
// ---------------------------------------------------------------
function getCurrentDateTimeISO() {
  const now = new Date();

  const year = now.getFullYear();
  const month = String(now.getMonth() + 1).padStart(2, '0'); // months are 0-based
  const day = String(now.getDate()).padStart(2, '0');
  const hours = String(now.getHours()).padStart(2, '0');
  const minutes = String(now.getMinutes()).padStart(2, '0');
  const seconds = String(now.getSeconds()).padStart(2, '0');

  return `${year}-${month}-${day}T${hours}:${minutes}:${seconds}`;
}

// ---------------------------------------------------------------
// Format Date
// ---------------------------------------------------------------
function formatDate(date) {
  const d = new Date(date);

  // Format the date part with weekday, month, day, and year
  const options = {
    weekday: "long",
    month: "long",
    day: "numeric",
    year: "numeric",
  };
  const datePart = d.toLocaleDateString("en-US", options);

  // Format the time part (12-hour format with AM/PM)
  const hours = d.getHours();
  const minutes = d.getMinutes();
  const ampm = hours >= 12 ? "PM" : "AM";
  const hour12 = hours % 12 || 12;
  const minutePadded = minutes.toString().padStart(2, "0");

  const timePart = `${hour12}:${minutePadded}${ampm}`;

  return `${datePart} at ${timePart}`;
}

// ---------------------------------------------------------------
// Create New Review
// ---------------------------------------------------------------
function createReview(name, date, rating, review) {
    // Remove unwanted text
    $(".review-list").find(".text-muted").remove();

    // Create review card
    const reviewCard = `
        <div class="d-flex border-bottom pb-3 mb-3">
            <!-- Avatar -->
            <img src="assets/img/avatar.jpg"
                class="img-fluid rounded-circle p-3"
                style="width: 100px; height: 100px;"
                alt="Profile">

            <!-- Review Content -->
            <div class="flex-grow-1">
                <!-- Date -->
                <p class="mb-2" style="font-size: 14px;">
                    ${formatDate(date)}
                </p>

                <!-- Reviewer Name and Rating -->
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        ${name}
                    </h5>
                    <div class="d-flex mb-3">
                        ${renderStars(rating)}
                    </div>
                </div>

                <!-- Review Comment -->
                <p class="mb-0">
                    ${review}
                </p>
            </div>
        </div>
    `;
    // Append review
    $(".review-list").append(reviewCard);
}

// ---------------------------------------------------------------
// Reset Star State
// ---------------------------------------------------------------
function resetStarState() {
    $(".rating-stars i").removeClass("active-rate");
}

// ---------------------------------------------------------------
// Toggle Star State
// ---------------------------------------------------------------
export function toggleState(elem) {
  if (elem.hasClass("active-rate")) {
    elem.removeClass("active-rate");
  } else {
    elem.addClass("active-rate");
  }
}

// ---------------------------------------------------------------
// Count Star Rate
// ---------------------------------------------------------------
function getRate() {
    const rating = $(".active-rate").length;
    return parseInt(rating);
}

// ---------------------------------------------------------------
// Add Rating
// ---------------------------------------------------------------
export async function rateProduct() {
    const name = $(".form-name").val();
    const productId = $(".product-card").data("id");
    const review = $(".form-review").val();
    const rating = getRate();
    const date = getCurrentDateTimeISO();

    if (!productId || !review) {
        displayMessage("Some fields are empty", "info");
    }
    else if (!rating || rating === 0) {
        displayMessage("Please add your star rating using the star icons below", "warning");
        return;
    }
    else {
        $(".btn-review").text("Processing...").prop("disabled", true);
        // Prepare data
        const payload = { productId: productId, review: review, rating: rating };

        try {
            // Send payment request to backend
            const result = await makeRequest(
                `${apiUrl}/products/review`,
                "POST",
                payload
            );

            if (
                result.status === 200 &&
                result.message === "Review added successfully"
            ) {
                // Append new review
                $(".form-review").val("");
                resetStarState();
                createReview(name, date, rating, review);
                $(".btn-review").text("Post Review");
            } else {
                displayMessage(`${result.message}`, "info");
                $(".btn-review").text("Post Review").prop("disabled", false);
            }
        } catch (err) {
            console.log("Network error: ", err);
            displayMessage("Network error occured", "warning");
            $(".btn-review").text("Post Review").prop("disabled", false);
        }
    }
}
