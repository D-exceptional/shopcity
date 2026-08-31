// ----------------------------------------------------
// Import Redirect
// ----------------------------------------------------
import { redirect } from "../../../assets/js/modules/index.js";

(async function ($) {
  ("use strict");

  $(".store-logout").on("click", function (e) {
    e.preventDefault();
    if (confirm("Are you sure to log out?")) {
      redirect("./../");
    }
  });

})(jQuery);
