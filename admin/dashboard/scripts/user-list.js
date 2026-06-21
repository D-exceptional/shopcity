// ----------------------------------------------------
// Import Configs
// ----------------------------------------------------
import { makeRequest } from "../../../assets/js/core/index.js";
// ----------------------------------------------------
// Import UI Alerts
// ----------------------------------------------------
import { displayMessage } from "../../../assets/js/ui/index.js";
// ----------------------------------------------------
// Import Formatters
// ----------------------------------------------------
import { formatNum } from "../scripts/utils/index.js";

(async function ($) {
  ("use strict");

  const params = new URLSearchParams(window.location.search);
  const role = params.has("role") ? params.get("role") : null;

  countTotal(`${role}s`);

  // -------------------------------------------------
  // Initialize Counter
  // -------------------------------------------------
  let offset = 1;
  const limit = 20;

  // -------------------------------------------------
  // Show All Users
  // -------------------------------------------------
  $(".btn-all").on("click", function () {
    $(".content-row").each(function () {
      if ($(this).css("display") === "none") {
        $(this).css({ display: "table-row" });
      }
    });
  });

  // -------------------------------------------------
  // Show Pending Users
  // -------------------------------------------------
  $(".btn-pending").on("click", function () {
    $(".content-row").each(function () {
      const statusText = $(this).find(".status button").text().trim();
      if (["Pending", "Deactivated"].includes(statusText)) {
        $(this).css({ display: "table-row" });
      } else {
        $(this).css({ display: "none" });
      }
    });
  });

  // -------------------------------------------------
  // Show Active Users
  // -------------------------------------------------
  $(".btn-active").on("click", function () {
    $(".content-row").each(function () {
      const statusText = $(this).find(".status button").text().trim();
      if (statusText === "Active") {
        $(this).css({ display: "table-row" });
      } else {
        $(this).css({ display: "none" });
      }
    });
  });

  // -------------------------------------------------
  // Load More Users
  // -------------------------------------------------
  $(".btn-load").on("click", async function () {
    $(this).text("Processing...");
    offset++;
    // Fetch users
    await fetchUsers({ role: role, page: offset, total: limit });
  });

  // -------------------------------------------------
  // Toggle Users Status
  // -------------------------------------------------
  $(document).on("click", ".action .btn-action", async function () {
    const actionText = $(this).text().trim();
    const userId = $(this).closest("tr").data("id");
    // Fetch users
    await manageStatus({ status: actionText, id: userId }, $(this));
  });

  // -------------------------------------------------
  // Delete Account
  // -------------------------------------------------
  $(document).on("click", ".action .btn-delete", async function () {
    const userId = $(this).closest("tr").data("id");
    // Delete account
    await deleteAccount({ id: userId }, $(this));
  });

  // -------------------------------------------------
  // View Document
  // -------------------------------------------------
  $(document).on("click", ".action .btn-doc", async function () {
    const userId = $(this).closest("tr").data("id");
    // Fetch document
    await fetchDocument({ id: userId }, $(this));
  });

  // -------------------------------------------------
  // Page Search
  // -------------------------------------------------
  $(document).on("input", ".page-search", function () {
    let searchValue = $(this).val().trim().toLowerCase();
    if (searchValue) {
      $("tbody tr").each(function (index, el) {
        if ($(el).find("td").text().toLowerCase().includes(searchValue)) {
          $(el).css({ display: "table-row" });
        } else {
          $(el).css({ display: "none" });
        }
      });
    } else {
      $("tbody tr").each(function (index, el) {
        if ($(el).css("display") === "none") {
          $(el).css({ display: "table-row" });
        }
      });
      // Notify user
      displayMessage("Enter a valid search", "info");
    }
  });

  // -------------------------------------------------
  // Close Overlay
  // -------------------------------------------------
  $(".close-overlay").on("click", function () {
    $(".overlay").css({ display: "none" });
    $(".overlay").find("iframe").remove();
  });

  // ----------------------------------------------------
  // Helper Functions Definitions
  // ----------------------------------------------------
  async function fetchUsers(payload) {
    try {
      const result = await makeRequest(`/user/fetch`, "GET", payload);

      if (result.data && result.data !== null && result.data.users.length > 0) {
        // Display users
        displayUsers(result.data.users);
      } else {
        displayMessage("No more users found", "info");
        $(".btn-load").hide();
      }
    } catch (err) {
      displayMessage(`Network error occurred: ${err}`, "error");
    }
  }

  // Timeframes filetr
  function displayUsers(users) {
    if (!users || users.length === 0) {
      // No products
      displayMessage("No more users found", "info");
      return;
    }

    users.forEach((user) => {
      const {
        user_id,
        avatar,
        firstname,
        lastname,
        email,
        contact,
        country,
        user_state,
        user_role,
        user_status,
        created_at,
      } = user;

      const html = `
        <tr class='content-row' data-id='${user_id}'>
          <td>#</td>
          <td>
            <img src='${
              avatar !== "None" ? avatar : "../../../assets/img/avatar.jpg"
            }' class='profile-user-img img-fluid img-circle' alt='User Image'>
          </td>
          <td class='first-name'>${firstname}</td>
          <td class='last-name'>${lastname}</td>
          <td class='email'>${email}</td>
          <td class='contact'>${contact}</td>
          <td class='country'>${country}</td>
          <td class='state'>${user_state}</td>
          <td class='role'>${user_role}</td>
          <td class='status'>
            <button class='btn ${
              user_status === "Active" ? "btn-success" : "btn-danger"
            } btn-sm'>
              ${user_status}
            </button> 
          </td>
          <td>${created_at}</td>
          <td class='action'>
            <div style="display: flex; gap: 10px;">
              ${
                role === "Vendor"
                  ? "<button class='btn bg-primary color-white btn-sm btn-doc wmg-70'>View ID</button>"
                  : ""
              }
              ${
                user_status === "Active"
                  ? "<button class='btn bg-primary color-white btn-sm btn-action w-150'>Deactivate</button>"
                  : "<button class='btn bg-primary color-white btn-sm btn-action w-150'>Activate</button>"
              }
              <button class='btn btn-danger btn-delete btn-sm wmg-70'>Delete</button> 
            </div>
          </td>
        </tr>
      `;
      $("tbody").append(html);
    });

    countTotal(`${role}s`);
  }

  function countTotal(type = "Users") {
    const total = $("tbody tr.content-row").length;
    $(".content-header h1 b").text(`${type} (${formatNum(total)})`);
  }

  async function manageStatus(payload, el) {
    const statusMap = {
      Activate: "Active",
      Deactivate: "Deactivated",
    };
    if (confirm(`Are you sure to ${payload.status} account?`)) {
      el.attr("disabled", true).text(
        `${payload.status === "Activate" ? "Activating..." : "Deactivating..."}`
      );
      payload.status = statusMap[payload.status];

      try {
        const result = await makeRequest(
          `/user/status`,
          "PUT",
          payload
        );

        if (
          result &&
          result.message === "Account status updated successfully"
        ) {
          // Display stores
          displayMessage(result.message, "success");
          $("tbody tr").each(function () {
            if ($(this).data("id") === payload.id) {
              $(this)
                .find(".action .btn-action")
                .text(
                  `${payload.status === "Active" ? "Deactivate" : "Activate"}`
                )
                .attr("disabled", false);
              $(this)
                .find(".status")
                .empty()
                .html(
                  ` <button class='btn ${
                    payload.status === "Active" ? "btn-success" : "btn-danger"
                  } btn-sm'>
                      ${payload.status}
                    </button> 
                  `
                );
            }
          });
        } else {
          displayMessage(result.message, "info");
          el.attr("disabled", false).text(`${payload.status}`);
        }
      } catch (err) {
        displayMessage(`Network error occurred: ${err}`, "error");
        el.attr("disabled", false).text(`${payload.status}`);
      }
    }
  }

  async function deleteAccount(payload, el) {
    if (confirm(`Are you sure to delete account?`)) {
      el.attr("disabled", true).text("Deleting...");

      try {
        const result = await makeRequest(
          `/user/delete`,
          "DELETE",
          payload
        );

        if (result && result.message === "User deleted successfully") {
          // Display stores
          displayMessage(result.message, "success");
          $("tbody tr").each(function () {
            if ($(this).data("id") === payload.id) {
              $(this).fadeOut(200);
              $(this).remove();
              // Update counter
              setTimeout(() => {
                countTotal(`${role}s`);
              }, 2000);
            }
          });
        } else {
          displayMessage(result.message, "info");
          el.attr("disabled", false).text("Delete");
        }
      } catch (err) {
        displayMessage(`Network error occurred: ${err}`, "error");
        el.attr("disabled", false).text("Delete");
      }
    }
  }

  function viewFile(url) {
    // Hide the overlay first (or show, depending on your logic)
    $(".overlay").hide();

    // Set paren container
    const container = $(".overlay");

    // Get extension
    const ext = url.split(".").pop().toLowerCase();

    if (["jpg", "jpeg", "png", "gif", "webp"].includes(ext)) {
      // Just use <img> for images
      $("<img>", {
        src: url,
        width: "100%",
        height: "100%",
        css: { objectFit: "contain" },
      }).appendTo(container);
    } else {
      // Use Google Docs Viewer for PDFs / Docs
      const encodedUrl = encodeURIComponent(url);
      const gdocUrl = `https://docs.google.com/gview?url=${encodedUrl}&embedded=true`;

      $("<iframe>", {
        src: gdocUrl,
        width: "100%",
        height: "100%",
        frameborder: 0,
        css: { border: "none" },
      }).appendTo(container);
    }

    // Show the overlay
    $(".overlay").css({ display: "flex" });
  }

  async function fetchDocument(payload, el) {
    el.text("Fetching ID...")
      .attr("diabled", true)
      .removeClass("wmg-70")
      .addClass("w-150");
    try {
      const result = await makeRequest(`/user/id`, "GET", payload);

      if (result.data && result.data.file) {
        // View file
        viewFile(result.data.file);
        el.text("View ID")
          .attr("diabled", false)
          .removeClass("w-150")
          .addClass("wmg-70");
      } else {
        displayMessage("No document found", "info");
        el.text("View ID")
          .attr("diabled", false)
          .removeClass("w-150")
          .addClass("wmg-70");
      }
    } catch (err) {
      displayMessage(`Network error occurred: ${err}`, "error");
      el.text("View ID")
        .attr("diabled", false)
        .removeClass("w-150")
        .addClass("wmg-70");
    }
  }
})(jQuery);
