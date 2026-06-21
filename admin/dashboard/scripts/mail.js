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
// Import UI Alerts
// ----------------------------------------------------
import { extractAmount } from "../../../assets/js/utils/index.js";
// ----------------------------------------------------
// Import Formatters
// ----------------------------------------------------
import { formatNum } from "../scripts/utils/index.js";

(async function ($) {
  ("use strict");

  // -------------------------------------------------
  // Initialize Counter
  // -------------------------------------------------
  let inboxTracker = 1;
  let outboxTracker = 1;
  const limit = 20;
  // const inboxCount = extractAmount($(".inbox-count").text());
  // const outboxCount = extractAmount($(".outbox-count").text());

  // -------------------------------------------------
  // Load Previous Inbox
  // -------------------------------------------------
    $(".view-link").on("click", async function (e) {
        e.preventDefault();
        const totalPages = parseInt($(this).data("total"));
        let url = null;
        let payload = {};

        if ($(this).hasClass("inbox-previous")) {
            if (inboxTracker === 1) {
                return;
            }
            else {
                inboxTracker--;
                url = "mail/inbox";
                payload = { page: inboxTracker, total: limit };
                // Fetch mails
                await fetchMails(payload, url);
            }
        }
        if ($(this).hasClass("inbox-next")) {
            if (inboxTracker === totalPages) {
                return;
            } else {
              inboxTracker++;
              url = "mail/inbox";
              payload = { page: inboxTracker, total: limit };
              // Fetch mails
              await fetchMails(payload, url);
            }
        }

        if ($(this).hasClass("outbox-previous")) {
            if (outboxTracker === 1) {
              return;
            } else {
              outboxTracker--;
              url = "mail/outbox";
              payload = { page: outboxTracker, total: limit };
              // Fetch mails
              await fetchMails(payload, url);
            }
        }

        if ($(this).hasClass("outbox-next")) {
            if (outboxTracker === totalPages) {
              return;
            } else {
              outboxTracker++;
              url = "mail/outbox";
              payload = { page: outboxTracker, total: limit };
              // Fetch mails
              await fetchMails(payload, url);
            }
        }
  });

  // ----------------------------------------------------
  // Helper Functions Definitions
  // ----------------------------------------------------
  async function fetchMails(payload, endpoint) {
    try {
      const result = await makeRequest(`/${endpoint}`, "GET", payload);

      if (
        result.data &&
        result.data !== null &&
        result.data.mails.length > 0
      ) {
        // Display mails
        displayMails(result.data.mails);
      } else {
        displayMessage("No more mails found", "info");
      }
    } catch (err) {
      displayMessage(`Network error occurred: ${err}`, "error");
    }
  }

  // Timeframes filetr
  function displayMails(mails) {
    if (!mails || mails.length === 0) {
      // No products
      displayMessage("No more mails found", "info");
      return;
    }

    mails.forEach((mail) => {
      const {
        mail_id,
        mail_message,
        mail_filename,
        mail_sender,
        mail_date,
        mail_time,
      } = mail;
      // Format description
      const message =
        mail_message.length > 50
          ? mail_message.substring(0, 50) + "..."
          : mail_message;
        const media = ["None", "Null", "null", null].includes(mail_filename) ? "" : "<i class='fas fa-paperclip'></i>";

      const html = `
            <tr class='mail-list content-row' data-id='${mail_id}'>
                <td>
                    <div class="icheck-primary">
                        <input type="checkbox" value="">
                        <label></label>
                    </div>
                </td>
                <td class="mailbox-star">
                    <a href="#"><i class="fas fa-star-o text-warning"></i></a>
                </td>
                <td class="mailbox-name">
                    <a href="./mail-read?id=${mail_id}">${mail_sender}</a>
                </td>
                <td class="mailbox-subject">${message}</td>
                <td class="mailbox-attachment">${media}</td>
                <td class="mailbox-date">${mail_date} ${mail_time}</td>
            </tr>
        `;
      $("tbody").append(html);
    });

    countTotal("Mails");
  }

  function countTotal(type = "Mails") {
    const total = $("tbody tr.content-row").length;
    $(".header-count h1 b").text(`${type} (${formatNum(total)})`);
  }

  //Search function
  $(".page-search, .mail-search").on("input blur", function () {
    let searchValue = $(this).val().trim().toLowerCase();
    if (searchValue) {
      $(".mail-list").each(function () {
        const content = $(this).find("td").text().trim().toLowerCase();
        if (content.includes(searchValue)) {
          $(this).css({ display: "table-row" });
        } else {
          $(this).css({ display: "none" });
        }
      });
    } else {
      $(".mail-list").each(function () {
        if ($(this).css("display") === "none") {
          $(this).css({ display: "table-row" });
        }
      });
    }
  });
})(jQuery);