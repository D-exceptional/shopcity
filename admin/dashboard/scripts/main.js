// ----------------------------------------------------
// Import  External Utilities
// ----------------------------------------------------
import { makeRequest } from "../../../assets/js/core/index.js";
// ----------------------------------------------------
// Import UI Alerts
// ----------------------------------------------------
import { displayMessage } from "../../../assets/js/ui/index.js";
// ----------------------------------------------------
// Import Logout
// ----------------------------------------------------
import { logout } from "../../../assets/js/modules/index.js";
// ----------------------------------------------------
// Import Push Client
// ----------------------------------------------------
import PushClient from "../../../assets/js/push/PushClient.js";

(async function ($) {
  ("use strict");

  // -------------------------------------------------
  // Initialize push client (run only once per session)
  // -------------------------------------------------
  PushClient.bootstrap('admin');

  // -------------------------------------------------
  // Manual notification toggle
  // -------------------------------------------------
  let pushBusy = false;

  $(".notification-bell").on("click", async function () {
    if (!PushClient.initialized) {
      displayMessage("Initializing notifications, please try again shortly.", "info");
      return;
    }
    
    if (pushBusy) return;
    pushBusy = true;

    try {
      const state = await PushClient.state();

      if (state === "blocked") {
        displayMessage(
          "Notifications are blocked in your browser settings. Please enable them manually to continue.",
          "warning"
        );
        return;
      }

      if (state === "unsubscribed") {
        await PushClient.subscribe();
      } else if (state === "subscribed") {
        if (confirm("Are you sure to disable notifications?")) {
          await PushClient.unsubscribe();
        }
      }
    } finally {
      pushBusy = false;
    }
  });

  // -------------------------------------------------
  // Indentation
  // -------------------------------------------------
  $(".nav-sidebar").addClass("nav-child-indent");

  // -------------------------------------------------
  // Page Search
  // -------------------------------------------------
  $(document).on("input", ".page-search", function () {
    let searchValue = $(this).val().trim();
    if (searchValue !== "") {
      $(".col-lg-3.col-6").each(function (index, el) {
        if ($(el).find("p").text().toLowerCase().includes(searchValue)) {
          $(el).css({ display: "block" });
        } else {
          $(el).css({ display: "none" });
        }
      });
    } else {
      $(".col-lg-3.col-6").each(function (index, el) {
        if ($(el).css("display") === "none") {
          $(el).css({ display: "block" });
        }
      });
      // Notify user
      displayMessage("Enter a valid search", "info");
    }
  });

  // -------------------------------------------------
  // Page Refresh
  // -------------------------------------------------
  $(document).on("click", ".page-refresh", function (e) {
    e.preventDefault();
    window.location.reload();
  });

  // -------------------------------------------------
  // Notification Management
  // -------------------------------------------------
  $(document).on("click", ".notification-trigger", async function (e) {
    e.preventDefault();
    if ($(this).find("span").css("display") === "block") {
      $(this).find("span").hide().text("");
      //Update status
      try {
        const payload = {};

        const result = await makeRequest(
          `/notification/mark/read`,
          "PUT",
          payload
        );

        if (
          result.status === 200 &&
          result.message === "Notification marked as read"
        ) {
          $(".dropdown-menu.dropdown-menu-lg.dropdown-menu-right")
            .empty()
            .html(
              `<span class='dropdown-item dropdown-header'>No new notifications</span>
                 <a href='./notification' class='dropdown-item dropdown-footer'>View all</a>
                `
            );
        } else {
          displayMessage(result.message, "info");
        }
      } catch (err) {
        displayMessage(`Network error occurred: ${err}`, "error");
      }
    } else {
      $(this).css({ opacity: 1 });
    }
  });

  // -------------------------------------------------
  // Vendor Logout
  // -------------------------------------------------
  $(document).on("click", ".logout", function (e) {
    e.preventDefault();
    const linkText = $(this).text().trim();

    if (["Logout", "Log Out"].includes(linkText)) {
      if (confirm("Are you sure to log out?")) {
        logout();
      }
    }
  });
})(jQuery);