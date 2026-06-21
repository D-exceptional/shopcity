// ----------------------------------------------------
// Import Configs
// ----------------------------------------------------
import { makeRequest } from "../../assets/js/core/index.js";
// ----------------------------------------------------
// Import UI Alerts
// ----------------------------------------------------
import { displayMessage } from "../../assets/js/ui/index.js";
// ----------------------------------------------------
// Import Validations
// ----------------------------------------------------
import {
  validateInput,
  previewFile,
  uploadToCloudinary,
} from "../../assets/js/utils/index.js";
// ----------------------------------------------------
// Import Redirect
// ----------------------------------------------------
import {
  redirect
} from "../../assets/js/modules/index.js";

(async function ($) {
  "use strict";

  $(".form-name, .form-description").on("input", function (e) {
    const input = $(this);
    const value = input.val();

    if (/\d/.test(value)) {
      // Remove digits
      input.val(value.replace(/\d+/g, ""));
      displayMessage(
        "Numbers are not allowed in name or description fields",
        "info"
      );
    }
  });

  $(".form-name, .form-description").on("paste", function (e) {
    e.preventDefault();
    const paste = (
      e.originalEvent.clipboardData || window.clipboardData
    ).getData("text");
    const cleanPaste = paste.replace(/\d+/g, "");
    document.execCommand("insertText", false, cleanPaste);
    if (paste !== cleanPaste) {
      displayMessage("Numbers removed from pasted text", "info");
    }
  });

  $(".form-text").on("input blur", function () {
    validateInput(this);
  });

  $(".form-file").on("change", function () {
    previewFile(this, "Image Only");
  });

  $(".btn-create").on("click", async function () {
    await createStore();
  });

  async function createStore() {

    const inputs = {
      name: $(".form-name").val(),
      description: $(".form-description").val(),
      delivery: $(".form-delivery").val(),
    };

    if (Object.values(inputs).some((val) => val === "")) {
      displayMessage("Some fields are empty", "info");
      return;
    }

    const fileInput = $(".form-file")[0];
    const file = fileInput?.files?.[0];
    if (!file) {
      displayMessage("Upload a valid image", "info");
      return;
    }

    $(".btn-create").prop("disabled", true).val("Processing...");

    try {
      const fileInput = $(".form-file")[0];
      const file = fileInput?.files?.[0];
      const fileUrl = await uploadToCloudinary(file, "shops");

      const payload = {
        ...inputs,
        avatar: fileUrl,
        facebook: "None",
        instagram: "None",
        tiktok: "None",
        twitter: "None",
      };

      const result = await makeRequest(
        `/store`,
        "POST",
        payload
      );

      if (
        result.status === 200 &&
        result.message === "Store created successfully"
      ) {
        displayMessage(result.message, "success");
        $(".form-text").val("");
        $(".btn-create").val("Store created");
        setTimeout(() => {
          redirect('./store-list');
        }, 1200);
      } else {
        displayMessage(result.message, "info");
        $(".btn-create").val("Create Store").prop("disabled", false);
      }
    } catch (err) {
      displayMessage(`Network error occurred: ${err}`, "error");
      $(".btn-create").val("Create Store").prop("disabled", false);
    }
  }
})(jQuery);
