// ----------------------------------------------------
// Import UI Alerts
// ----------------------------------------------------
import { displayMessage } from "../ui/index.js";

// ---------------------------------------------------------------
// Create Dynamic Table Head
// ---------------------------------------------------------------
export function createTableHead(view) {
  const content =
    view === "Cart"
      ? ` <tr>
        <th scope="col">S/N</th>
        <th scope="col">Image</th>
        <th scope="col">Name</th>
        <th scope="col">Category</th>
        <th scope="col">Price</th>
        <th scope="col">Quantity</th>
        <th scope="col">Total</th>
        <th scope="col">Action</th>
    </tr>
        `
      : ` <tr>
        <th scope="col">S/N</th>
        <th scope="col">Image</th>
        <th scope="col">Name</th>
        <th scope="col">Category</th>
        <th scope="col">Price</th>
        <th scope="col">Action</th>
    </tr>`;

  $("thead").html(content);
}

// ---------------------------------------------------------------
// Toggle Password Visibilty
// ------------------------------------------------------------------
export function togglePasswordVisibility($icon, type) {
  const $passwordInput =
    type === "password" ? $(".form-password") : $(".form-password-new");

  const isHidden = $passwordInput.attr("type") === "password";
  $passwordInput.attr("type", isHidden ? "text" : "password");

  $icon
    .removeClass(isHidden ? "fa-eye" : "fa-eye-slash")
    .addClass(isHidden ? "fa-eye-slash" : "fa-eye");
}

// ---------------------------------------------------------------
// Load Countries
// ---------------------------------------------------------------
export async function loadCountries() {
  const response = await fetch("./countries.json");
  const data = await response.json();
  const array = [];

  for (const key in data) {
    if (Object.hasOwnProperty.call(data, key)) {
      const { country_name, currency_code, phone_code, country_code } =
        data[key];
      array.push({
        name: country_name,
        currency: currency_code,
        code: ["+"].includes(phone_code) ? phone_code : `+${phone_code}`,
        abbr: country_code,
      });

      $(".form-country").append(
        `<option value="${country_name}">${country_name}</option>`
      );
    }
  }

  return array;
}

// ---------------------------------------------------------------
// Capitalize First Letters Of A Word
// ---------------------------------------------------------------
export function capitalizeWords(str) {
  if (!str) return "";
  return str
    .split(" ")
    .map((word) => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
    .join(" ");
}

// ----------------------------------------------------
// Remove Cart From View
// ----------------------------------------------------
export async function shareContent({
  title = "",
  text = "",
  url = "",
  imageUrl = "",
} = {}) {
  // Default to current page if no URL provided
  url = url || window.location.href;

  try {
    // If an image is provided, attempt to attach it
    if (imageUrl && navigator.canShare) {
      const response = await fetch(imageUrl);
      const blob = await response.blob();
      const file = new File([blob], "product-image.jpg", { type: blob.type });

      const shareData = { title, text, url, files: [file] };

      if (navigator.canShare(shareData)) {
        await navigator.share(shareData);
        return displayMessage("Shared successfully with image!", "success");
      }
    }

    // If image sharing not supported, fallback to text+URL sharing
    if (navigator.share) {
      await navigator.share({ title, text, url });
      return displayMessage("Shared successfully!", "success");
    }

    // Final fallback: copy the link
    await navigator.clipboard.writeText(url);
    displayMessage("Sharing not supported — link copied to clipboard!", "info");
  } catch (err) {
    displayMessage("Share canceled or failed", "warning");
  }
}

// ----------------------------------------------------
// Preview Document For Mails
// ----------------------------------------------------
export function previewMailDocument(input) {
  if (input.files && input.files[0]) {
    const extension = input.files[0].name.split(".").pop().toLowerCase();
    const sizeCal = input.files[0].size / 1024 / 1024;
    let message = null;

    switch (extension) {
      case "zip":
      case "jfif":
        displayMessage(
          "Selected file format not supported. Choose a file with either .jpg, .jpeg, .png, .pdf, .docx, .mp4 and .mp3 extension",
          "info"
        );
        message = 'Unsupported';
        break;
      case "jpg":
      case "jpeg":
      case "png":
      case "pdf":
      case "docx":
      case "mp3":
      case "mp4":
        displayMessage("Included attachment is supported", "success");
        message = 'Supported';
        break;
    }

    return message;
  }
}

// ----------------------------------------------------
// Copy Link
// ----------------------------------------------------
export function copyLink(link) {
  // Use clipboard API directly to copy the text
  navigator.clipboard
    .writeText(link)
    .then(() => {
      displayMessage("Link copied successfully", "success");
    })
    .catch((err) => {
      // console.error("Error copying link: ", err);
      displayMessage("Error copying link", "warning");
    });
} 
