// ----------------------------------------------------
// Import Redirect
// ----------------------------------------------------
import { baseUrl } from "../../js/shared/core/index.js";
import { redirect } from "../../js/shared/modules/index.js";

(async function ($) {
  ("use strict");

  $(".store-logout").on("click", function (e) {
    e.preventDefault();
    if (confirm("Are you sure to log out?")) {
      redirect(`${baseUrl}/vendor/dashboard`);
    }
  });

  // ----- Menu Sub-Items Indentation ----- //
  $(".nav-sidebar").addClass("nav-child-indent");

})(jQuery);
