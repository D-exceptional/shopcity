// ----------------------------------------------------
// Import Configs
// ----------------------------------------------------
import {
  makeRequest,
} from "../../js/shared/core/index.js";
// ----------------------------------------------------
// Import UI Alerts
// ----------------------------------------------------
import { displayMessage } from "../../js/shared/ui/index.js";
// ----------------------------------------------------
// Import Validations
// ----------------------------------------------------
import { uploadToCloudinary, formatAmount, extractAmount } from "../../js/shared/utils/index.js";

(async function ($) {
    ("use strict");

    /*const params = new URLSearchParams(window.location.search);
    const productId =
      params.has("productId") && !isNaN(params.get("productId"))
        ? parseInt(params.get("productId"))
      : null;
  */
   const productId = $(".content-wrapper").data("productid");
  
  // Load Commissions
  loadCommissions();

    // Get initial data
    const initialData = {
      name: $(".form-name").val().trim(),
      description: $(".form-description").val().trim(),
      category: $(".form-category").val().trim(),
      subcategory: $(".form-sub-category").val().trim(),
      price: $(".form-price").val().trim(),
      slash: $(".form-slash-price").val().trim(),
      stock: $(".form-stock").val().trim(),
      color: $(".form-color").val().trim(),
      reselling: $(".form-reselling").val().trim(),
      visibility: $(".form-visibility").val().trim(),
    };
  
  $(document).on("change", ".form-reselling", function () {
    const value = $(this).val().trim();

    if (value === 'enable') {
      $(".commission-section").removeClass("d-none");
    }
    else {
      $(".commission-section").addClass("d-none");
    }
  });
    
    // Zomm images on click
    $(document).on(
      "click",
      ".media-card img, .media-card video",
      async function () {
        const parentContainer = $(this).closest(".media-card");
        const mediaType = parentContainer.data("type");
        const mediaUrl =
          parentContainer.find("img").attr("src") ||
          parentContainer.find("video").attr("src");
          
           $(".overlay").css({ display: "flex" });
           const element = [
             "image",
             "image/jpeg",
             "image/jpg",
             "image/png",
           ].includes(mediaType)
             ? `<img src='${mediaUrl}' alt='Product Image'>`
             : `<video src='${mediaUrl}' controls></video>`;
           $(".zoom-container").html(element);
      }
    );

    // Take actions on image icons click
  $(document).on("click", ".media-icons i", async function () {
    const parentContainer = $(this).closest(".media-card");
    const mediaId = parseInt(parentContainer.data("id"));
    const mediaType = parentContainer.data("type");
    const mediaUrl =
      parentContainer.find("img").attr("src") ||
      parentContainer.find("video").attr("src");
    const classId = $(this).attr("class");

    switch (classId) {
      case "fa fa-expand":
        $(".overlay").css({ display: "flex" });
        const element = [
          "image",
          "image/jpeg",
          "image/jpg",
          "image/png",
        ].includes(mediaType)
          ? `<img src='${mediaUrl}' alt='Product Image'>`
          : `<video src='${mediaUrl}' controls></video>`;
        $(".zoom-container").html(element);
        break;

      case "fa fa-retweet":
        parentContainer.find("input").click();
        break;

      case "fa fa-trash":
        if (confirm("Are you sure to delete this media?")) {
              
          try {
            const result = await makeRequest(
              `/media/${mediaId}`,
                "DELETE",
              {}
            );

              parentContainer.find(".card-overlay").css({ zIndex: 20 });

            if (
              result.status === 200 &&
              result.message === "Media deleted successfully"
            ) {
                displayMessage(result.message, "success");
                parentContainer.find(".card-overlay").css({ zIndex: -10 });
              parentContainer.fadeOut(200, () => parentContainer.remove());
            } else {
                displayMessage(result.message, "info");
                 parentContainer.find(".card-overlay").css({ zIndex: -10 });
            }
          } catch (err) {
              displayMessage(`Network error occurred: ${err}`, "error");
               parentContainer.find(".card-overlay").css({ zIndex: -10 });
          }
        }
        break;
    }
  });

    // Capture image file change
  $(document).on("change", ".media-card input", function () {
    updateMedia(this);
  });

    // Upate image media
  async function updateMedia(input) {
    const fileInput = $(input)[0];
    const file = fileInput?.files?.[0];
    const parentContainer = $(fileInput).closest(".media-card");
    const mediaId = parentContainer.data("id");
    const allowedTypes = ["image/", "video/"];

    if (!file) {
        return displayMessage("Upload a valid image", "info");
    }

    if (!allowedTypes.some((type) => file.type.startsWith(type))) {
      displayMessage(`"${file.name}" is not an image or video file.`, "info");
      return;
    }

    try {
        const fileUrl = await uploadToCloudinary(file, "products");
        
        // Update UI
        parentContainer.find(".card-overlay").css({ zIndex: 20 });

      if (parentContainer.find("img").length)
        parentContainer.find("img").attr("src", fileUrl);
      else parentContainer.find("video").attr("src", fileUrl);

      const result = await makeRequest(
        `/media/${mediaId}`,
        "PUT",
        {
          url: fileUrl,
        }
      );

      if (
        result.status === 200 &&
        result.message === "Media updated successfully"
      ) {
          displayMessage(result.message, "success");
           parentContainer.find(".card-overlay").css({ zIndex: -10 });
      } else {
          displayMessage(result.message, "info");
           parentContainer.find(".card-overlay").css({ zIndex: -10 });
      }
    } catch (err) {
        displayMessage(`Network error occurred: ${err}`, "error");
         parentContainer.find(".card-overlay").css({ zIndex: -10 });
    }
  }

  // -------------------------------------------------
  // Close Overlay
  // -------------------------------------------------
  $(".close-overlay").on("click", function () {
      $(".overlay").css({ display: "none" });
      $(".zoom-container").html("");
  });

    $(".btn-update").on("click", async function () {
    await updateDetails();
  });

  async function updateDetails() {
    // Get initial data
    const currentData = {
      name: $(".form-name").val().trim(),
      description: $(".form-description").val().trim(),
      category: $(".form-category").val().trim(),
      subcategory: $(".form-sub-category").val().trim(),
      price: $(".form-price").val().trim(),
      slash: $(".form-slash-price").val().trim(),
      stock: $(".form-stock").val().trim(),
      color: $(".form-color").val().trim(),
      reselling: $(".form-reselling").val().trim(),
      visibility: $(".form-visibility").val().trim(),
    };
      
    if (
      initialData.name === currentData.name &&
      initialData.description === currentData.description &&
      initialData.category === currentData.category &&
      initialData.subcategory === currentData.subcategory &&
      initialData.price === currentData.price &&
      initialData.slash === currentData.slash &&
      initialData.stock === currentData.stock &&
      initialData.color === currentData.color &&
      initialData.reselling === currentData.reselling &&
      initialData.visibility === currentData.visibility
    ) {
      displayMessage("No changes made", "info");
      return;
    }

    if (Object.values(currentData).some((val) => val === "")) {
      displayMessage("Some fields are empty", "info");
      return;
    }
      
    // Get price 
    const price = extractAmount(currentData.price);
    if (price < 1000) {
      displayMessage(`Enter a valid amount...starting from ${formatAmount(1000)}`, "info");
      return;
    }

    const stock = extractAmount(currentData.stock);
    if (stock <= 1) {
      displayMessage(
        `Stock count is too low`,
        "info"
      );
      return;
    }

    const commission = currentData.reselling === 'enable'
      ? parseInt($(".form-commission").val())
      : 0;
      
    // Update object
    currentData.price = price;
    currentData.stock = stock;
    currentData.slash = extractAmount(currentData.slash) ?? 0;

    $(".btn-update").prop("disabled", true).val("Processing...");

    try {
     // Build payload
      const payload = {
        ...currentData,
        commission: commission,
      };

      const result = await makeRequest(`/product/${productId}`, "PUT", payload);

      if (
        result.status === 200 &&
        result.message === "Product updated successfully"
      ) {
        displayMessage(result.message, "success");
        $(".form-text").val("");
        $(".btn-update").val("Details updated");
        setTimeout(() => {
          $(".btn-update").val("Update Details").prop("disabled", false);
        }, 1200);
      } else {
        displayMessage(result.message, "info");
        $(".btn-update").val("Update Details").prop("disabled", false);
      }
    } catch (err) {
      displayMessage(`Network error occurred: ${err}`, "error");
      $(".btn-update").val("Update Details").prop("disabled", false);
    }
  }

  // Load Commissions
  function loadCommissions() {
    for (let index = 10; index < 110; index += 10) {
      
      const option = `
        <option value="${index}">
          ${index}
        </option>
      `;
      
      $(".form-commission").append(option);
    }
  }
  
})(jQuery);
