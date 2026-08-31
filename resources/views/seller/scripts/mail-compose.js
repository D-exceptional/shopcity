// ----------------------------------------------------
// Import Configs
// ----------------------------------------------------
import { makeRequest } from "../../assets/js/core/index.js";
// ----------------------------------------------------
// Import UI Alerts
// ----------------------------------------------------
import { displayMessage } from "../../assets/js/ui/index.js";

(async function ($) {
  ("use strict");

  $(".btn-send").on("click", async function () {
    $(this).attr("disabled", true);

    const sender = $(this).data("name");
    const email = $(".form-recipient").val().trim();
    const subject = $(".form-subject").val().trim();
    const message = $(".form-message").val().trim();

    if (!sender || !email || !subject || !message) {
      displayMessage("Some fields are empty", "info");
      $(".btn-send").attr("disabled", false);
    } else {
      const decision = confirm("Are you sure to send mail?");
      if (decision === true) {
        $(".btn-send").text("Sending...").attr("disabled", true);

        try {
          const payload = {
            recipients: JSON.stringify([{ name: "User", email: email, id: null }]),
            subject: subject,
            message: message,
            sender: sender,
          };

          const result = await makeRequest(
            `/mail/send`,
            "POST",
            payload
          );

          if (
            result.status === 200 &&
            result.message === "Message sent successfully"
          ) {
            displayMessage(result.message, "success");
            $(".form-control").val("");
            $(".btn-send")
              .html("<i class='fas fa-check'></i> Sent")
              .attr("disabled", true);
            setTimeout(() => {
              $(".btn-send")
                .html("<i class='far fa-envelope'></i> Send")
                .attr("disabled", false);
            }, 2000);
          } else {
            displayMessage(result.message, "info");
            $(".btn-send")
              .html("<i class='far fa-envelope'></i> Send")
              .attr("disabled", false);
          }
        } catch (err) {
          displayMessage(`Network error occurred: ${err}`, "error");
          $(".btn-send")
            .html("<i class='far fa-envelope'></i> Send")
            .attr("disabled", false);
        }
      } else {
        displayMessage("Action cancelled", "info");
      }
    }
  });
})(jQuery);
