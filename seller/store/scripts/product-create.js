// ----------------------------------------------------
// Import Configs
// ----------------------------------------------------
import {
  makeRequest,
  CLOUDINARY_NAME,
} from "../../../assets/js/core/index.js";
// ----------------------------------------------------
// Import UI Alerts
// ----------------------------------------------------
import { displayMessage } from "../../../assets/js/ui/index.js";
// ----------------------------------------------------
// Import Validations
// ----------------------------------------------------
import {
  validateInput,
} from "../../../assets/js/utils/index.js";
// ----------------------------------------------------
// Import Redirect
// ----------------------------------------------------
import { redirect } from "../../../assets/js/modules/index.js";

(async function ($) {
  ("use strict");

  const params = new URLSearchParams(window.location.search);
  const storeId =
    params.has("id") && !isNaN(params.get("id"))
      ? parseInt(params.get("id"))
      : null;
  const UPLOAD_PRESET = "preset_products";
  let productMedia = [];

  $(".form-data").on("input blur", function () {
    validateInput(this);
  });

  $(".form-category, .form-sub-category").on("change", async function () {
    const isCategory = $(this).hasClass("form-category");
    const value = $(this).val().trim();
    if (!value) {
      displayMessage(
        `Select a valid ${isCategory ? "category" : "sub category"}`,
        "info"
      );
      return;
    }
    else {
      if (isCategory) {
        await fetchSubcategories(value);
      }
    }
  });

  $("#mediaInput").on("change", function (event) {
    uploadMedia(event);
  });

  $(".btn-create").on("click", async function () {
    await createProduct();
  });

  async function uploadMedia(event) {
    const files = event.target.files;
    const allowedTypes = ["image/", "video/"]; // MIME type prefixes
    const uploadPromises = [];

    // Validate all files first before uploading
    for (const file of files) {
      const isValidType = allowedTypes.some((type) =>
        file.type.startsWith(type)
      );

      if (!isValidType) {
        displayMessage(`"${file.name}" is not an image or video file.`, "info");
        return; // Stop the entire upload
      }
    }

    // If all files are valid, proceed to upload
    for (const file of files) {
      const formData = new FormData();
      formData.append("file", file);
      formData.append("upload_preset", UPLOAD_PRESET);

      uploadPromises.push(
        fetch(`https://api.cloudinary.com/v1_1/${CLOUDINARY_NAME}/upload`, {
          method: "POST",
          body: formData,
        }).then((res) => res.json())
      );
    }

    // Wait for all uploads to finish
    const results = await Promise.all(uploadPromises);

    // Clear media array
    productMedia = [];

    // Process and process result
    if (results && results.length > 0) {
      $.each(results, function (index, obj) {
        productMedia.push(obj.secure_url);
      });
    }

    return results;
  }

  async function createProduct() {
    const inputs = {
      name: $(".form-name").val(),
      description: $(".form-description").val(),
      category: $(".form-category").val(),
      sub: $(".form-sub-category").val(),
      price: parseInt($(".form-price").val()),
      slash: parseInt($(".form-slash-price").val()) ?? 0,
      stock: parseInt($(".form-stock").val()) ?? 1,
      color: $(".form-color").val(),
    };

    if (Object.values(inputs).some((val) => val === "")) {
      displayMessage("Some fields are empty", "info");
      return;
    }

    if (inputs.price === 0 || inputs.price < 1000) {
      displayMessage("Enter a valid price", "info");
      return;
    }

    if (productMedia.length === 0) {
      displayMessage("Upload product media (images or videos)", "info");
      return;
    }

    $(".btn-create").prop("disabled", true).val("Processing...");

    try {
      const payload = {
        ...inputs,
        id: storeId,
        media: productMedia,
      };

      const result = await makeRequest(`/products`, "POST", payload);

      if (
        result.status === 200 &&
        result.message === "Product created successfully"
      ) {
        displayMessage(result.message, "success");
        $(".form-data").val("");
        $(".btn-create").val("Product created");
        setTimeout(() => {
          redirect(`./product-list?id=${storeId}`);
        }, 1200);
      } else {
        displayMessage(result.message, "info");
        $(".btn-create").val("Create Product").prop("disabled", false);
      }
    } catch (err) {
      displayMessage(`Network error occurred: ${err}`, "error");
      $(".btn-create").val("Create Product").prop("disabled", false);
    }
  }

  // Timeframes filetr
  function displaySubcatgeories(subcategories) {
    if (!subcategories || subcategories.length === 0) {
      // No notifications
      displayMessage("No more notification found", "info");
      return;
    }
    $(".form-sub-category").empty();

    subcategories.forEach((subcategory) => {
      const { subcategory_name } = subcategory;

      const html = `
      <option value="${subcategory_name}">${subcategory_name}</option>
    `;
      $(".form-sub-category").append(html);
    });
    $(".form-sub-category").prepend(
        `<option value="">Select sub category</option>`
    );
  }

  async function fetchSubcategories(category) {
    $(".form-sub-category").empty().html(`<option value="">Loading subcategories...</option>`);
    
    try {
      const payload = {
        category: category,
      };

      const result = await makeRequest(
        `/categories/fetch`,
        "GET",
        payload
      );

      if (result.status === 200 && result.data.subcategories.length > 0) {
        displaySubcatgeories(result.data.subcategories);
      } else {
        displayMessage(result.message, "info");
        $(".form-sub-category").empty().html(`<option value="">Select sub category</option>`);
      }
    } catch (err) {
      displayMessage(`Network error occurred: ${err}`, "error");
      $(".form-sub-category").empty().html(`<option value="">Select sub category</option>`);
    }
  }
})(jQuery);
