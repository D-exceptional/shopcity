// ----------------------------------------------------
// Import Configs
// ----------------------------------------------------
import {
  makeRequest,
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
  "use strict";

  const params = new URLSearchParams(window.location.search);
  const storeId =
    params.has("id") && !isNaN(params.get("id"))
      ? parseInt(params.get("id"))
      : null;

  $(".form-data").on("input blur", function () {
    validateInput(this);
  });
    
    // Create coupon discounts
    for (let index = 5; index < 105; index+=5) {
        const option = `<option value="${index}">${index}</option>`;
        $(".form-discount").append(option);
    }
    
    $(".form-discount").on("change", function () {
      if (!$(this).val()) {
        displayMessage(
          `Select a valid discount`,
          "info"
        );
      }
    });

  $(".btn-create").on("click", async function () {
    await createCoupon();
  });
    
  async function createCoupon() {
    const inputs = {
      code: $(".form-code").val().trim(),
      discount: parseInt($(".form-discount").val().trim()),
    };

    if (Object.values(inputs).some((val) => val === "")) {
      displayMessage("Some fields are empty", "info");
      return;
    }
      
    if (inputs.discount === 0 || inputs.discount > 100) {
        displayMessage("Select a valid discount", "info");
        return;
    }

    $(".btn-create").prop("disabled", true).val("Processing...");

    try {
      const payload = {
        ...inputs,
        storeId: storeId,
      };

      const result = await makeRequest(`/store/coupon`, "POST", payload);

      if (
        result.status === 200 &&
        result.message === "Coupon created successfully"
      ) {
        displayMessage(result.message, "success");
        $(".form-data").val("");
        $(".btn-create").val("Coupon created");
        setTimeout(() => {
          redirect(`./coupon-list?id=${storeId}`);
        }, 1200);
      } else {
        displayMessage(result.message, "info");
        $(".btn-create").val("Create Coupon").prop("disabled", false);
      }
    } catch (err) {
      displayMessage(`Network error occurred: ${err}`, "error");
      $(".btn-create").val("Create Coupon").prop("disabled", false);
    }
  }
})(jQuery);
