// ----------------------------------------------------
// Import  External Utilities
// ----------------------------------------------------
import { makeRequest } from "../../../assets/js/core/index.js";
// ----------------------------------------------------
// Import UI Alerts
// ----------------------------------------------------
import { displayMessage } from "../../../assets/js/ui/index.js";
// ----------------------------------------------------
// Import Validations
// ----------------------------------------------------
import { formatTimeAgo } from "./utils/index.js";

(async function ($) {
  ("use strict");

  // -------------------------------------------------
  // Initialize Counter
    // -------------------------------------------------
    let offset = 1;
    const limit = 20;

  // -------------------------------------------------
  // Load More Notifications
  // -------------------------------------------------
  $(".load-more").on("click", async function () {
      offset++;
      // Fetch notifications
      await fetchNotifications({ offset: offset, limit: limit });
  });

  // ----------------------------------------------------
  // Helper Functions Definitions
  // ----------------------------------------------------
  async function fetchNotifications(payload) {
    try {
      const result = await makeRequest(
        `/notification`,
        "GET",
        payload
      );

      if (result.data && result.data !== null && result.data.length > 0) {
        // Display notifications
        displayNotifications(result.data);
      } else {
        displayMessage("No more notification found", "info");
        $(".load-more").hide();
      }
    } catch (err) {
      displayMessage(`Network error occurred: ${err}`, "error");
    }
  }

  // Timeframes filetr
    function displayNotifications(notifications) {
       if (!notifications || notifications.length === 0) {
          // No notifications
          displayMessage("No more notification found", "info");
        return;
       }

       notifications.forEach((notification) => {
         const { notification_type, notification_details, notification_date } =
           notification;
         const timeAgo = formatTimeAgo(notification_date);

         // Select icon and color based on type
         let icon = "fas fa-refresh bg-pink";
         switch (notification_type) {
           case "New Order":
             icon = "fas fa-shopping-cart bg-yellow";
             break;
            case "Item Update":
             icon = "fas fa-rss bg-blue";
             break;
            case "New Message":
             icon = "fas fa-envelope bg-blue";
             break;
           case "Order Completion":
             icon = "fas fa-shopping-cart bg-green";
             break;
           case "Order Cancellation":
             icon = "fas fa-shopping-cart bg-red";
             break;
           case "New Registration":
             icon = "fas fa-user-plus bg-blue";
             break;
           case "New Store":
             icon = "fas fa-university bg-blue";
             break;
           case "New Product":
             icon = "fas fa-layer-group bg-yellow";
             break;
         }

         const html = `
      <div>
        <i class="${icon}"></i>
        <div class="timeline-item">
          <span class="time"><i class="fas fa-clock"></i> ${timeAgo}</span>
          <h3 class="timeline-header"><a href="#">${notification_type}</a></h3>
          <div class="timeline-body">${notification_details}</div>
          <div class="timeline-footer">
            <!--<a href="#" class="btn btn-primary btn-sm">View</a>-->
          </div>
        </div>
      </div>
    `;
        $(".notification-list").append(html);
    });
  }
})(jQuery);