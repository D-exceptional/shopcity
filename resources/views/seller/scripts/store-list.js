// ----------------------------------------------------
// Import Redirect
// ----------------------------------------------------
import { redirect } from "../../assets/js/modules/index.js";
// ----------------------------------------------------
// Import UI Alerts
// ----------------------------------------------------
import { copyLink } from "../../assets/js/utils/index.js";

// -------------------------------------------------
// View Store
// -------------------------------------------------
$(document).on("click", ".btn-view", function () {
    const storeId = $(this).closest("tr").data("id");
    redirect(`./store/?id=${storeId}`);
});

// -------------------------------------------------
// Copy Link
// -------------------------------------------------
$(document).on("click", ".btn-link", function () {
    const storeId = $(this).closest("tr").data("id");
    const link = `https://shop.mrsamase.com/shop?id=${storeId}`;
    copyLink(link);
});
