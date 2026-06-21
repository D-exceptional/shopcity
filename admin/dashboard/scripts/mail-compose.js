// ----------------------------------------------------
// Import Configs
// ----------------------------------------------------
import { makeRequest } from "../../../assets/js/core/index.js";
// ----------------------------------------------------
// Import UI Alerts
// ----------------------------------------------------
import { displayMessage } from "../../../assets/js/ui/index.js";

(async function ($) {
  "use strict";

  $(`#admin-section .user-card`).each(function () {
    const name = $(this).find(".user-card-name").text();
    if (name === $("#admin-name").val()) {
      $(`#admin-section`).prepend($(this));
    }
  });

  // Search
  $(".user-search").each(function () {
    $(this).on("input blur", function () {
      const searchValue = $(this).val().toLowerCase().trim(); // Normalize input
      const userScroll = $(this).closest(".user-view").find(".user-scroll");

      if (searchValue !== "") {
        userScroll.children(".user-card").each(function () {
          const cardText = $(this)
            .find(".user-card-name")
            .text()
            .toLowerCase()
            .trim();

          if (cardText.includes(searchValue)) {
            $(this).css("display", "flex"); // Show matching or all if empty
          } else {
            $(this).css("display", "none"); // Hide non-matching
          }
        });
      } else {
        userScroll.children(".user-card").each(function () {
          $(this).css("display", "flex"); // Show matching or all if empty
        });
      }
    });
  });

  // Get email
  $(".user-card").on("click", "img, .user-card-name", function () {
    const emailText = $(this)
      .closest(".user-card")
      .find(".email-address")
      .text();
    $(".email-preview")
      .css({ width: "80%", paddingLeft: "2%" })
      .text(`Email is: ${emailText}`);
    // Hide and reset
    setTimeout(function () {
      $(".email-preview").css({ width: "0%", paddingLeft: "0%" }).text(``);
    }, 2500);
  });

  //Preview selected file
  function previewFile(input) {
    if (input.files && input.files[0]) {
      let extension = input.files[0].name.split(".").pop().toLowerCase();
      //let sizeCal = input.files[0].size / 1024 / 1024;
      if (["zip", "jfif"].includes(extension)) {
        displayMessage(
          "Selected file format not supported. Choose a file with either .jpg, .jpeg, .png, .pdf, .docx, .mp4 and .mp3 extension!",
          "info"
        );
        $(".btn-send").attr("disabled", true);
      } else {
        displayMessage("Included attachment is supported", "success");
        $(".btn-send").attr("disabled", false);
      }
    }
  }

  $(".form-attachment").on("change", function () {
    previewFile(this);
  });

  /************* New codes begin here **************/
  $(".form-recipient").on("click", function () {
    $(this).blur();
    $(".mail-overlay").css({ display: "flex" });
  });

  function scrollToAdmin() {
    document.getElementById("admin-section-view").scrollIntoView({
      behavior: "smooth",
      inline: "nearest",
      block: "center",
    });
    $("#view-admins").css({ "border-bottom": "3px solid #181d38" });
    $("#view-customers, #view-vendors").css({ "border-bottom": "none" });
  }

  function scrollToCustomers() {
    document.getElementById("affiliate-section-view").scrollIntoView({
      behavior: "smooth",
      inline: "nearest",
      block: "center",
    });
    $("#view-customers").css({ "border-bottom": "3px solid #181d38" });
    $("#view-admins, #view-vendors").css({ "border-bottom": "none" });
  }

  function scrollToVendor() {
    document.getElementById("vendor-section-view").scrollIntoView({
      behavior: "smooth",
      inline: "nearest",
      block: "center",
    });
    $("#view-vendors").css({ "border-bottom": "3px solid #181d38" });
    $("#view-customers, #view-admins").css({ "border-bottom": "none" });
  }

  $("#view-admins").on("click", function () {
    scrollToAdmin();
  });

  $("#view-customers").on("click", function () {
    scrollToCustomers();
  });

  $("#view-vendors").on("click", function () {
    scrollToVendor();
  });

  //Storage arrays
  let mailRecipients = [];

  //Remove single recipient
  function removeRecipient(id) {
    // Remove the object with the given id
    let newArray = mailRecipients.filter((obj) => obj.id !== id);
    // Update the original array
    mailRecipients = newArray;
  }

  //Remove multiple recipients
  function removeMultiple(view) {
    let idsToRemove = [];
    // Check if the type is 'admin'
    $(`#${view}-section .user-card .add-user`).each(function () {
      const id = $(this).attr("id");
      idsToRemove.push(id);
    });
    // Remove objects from the array based on their IDs
    let newArray = mailRecipients.filter(
      (obj) => !idsToRemove.includes(obj.id)
    );
    // Update the original array
    mailRecipients = newArray;
  }

  //Add and remove items singly to admin array
  $("#admin-section")
    .find('input[type="checkbox"]')
    .each(function () {
      $(this).on("change", function () {
        const id = $(this).attr("id");
        const member = $(this).attr("data-member");
        const email = $(this).parent().find(".email-address").text();
        const name = $(this).parent().parent().find(".user-card-name").text();
        if (this.checked) {
          //Add item to array
          const receiver = {
            id: id,
            email: email,
            name: name,
            member: member,
          };
          mailRecipients.push(receiver);
        } else {
          //Remove master or sub-master check
          $("#send-to-all").prop("checked", false);
          $("#send-all-admin").prop("checked", false);
          //Remove item from array
          removeRecipient(id);
        }
      });
    });

  //Add and remove items singly to customers array
  $("#customer-section")
    .find('input[type="checkbox"]')
    .each(function () {
      $(this).on("change", function () {
        const id = $(this).attr("id");
        const member = $(this).attr("data-member");
        const email = $(this).parent().find(".email-address").text();
        const name = $(this).parent().parent().find(".user-card-name").text();
        if (this.checked) {
          //Add item to array
          const receiver = {
            id: id,
            email: email,
            name: name,
            member: member,
          };
          mailRecipients.push(receiver);
        } else {
          //Remove master or sub-master check
          $("#send-to-all").prop("checked", false);
          $("#send-all-affiliate").prop("checked", false);
          //Remove item from array
          removeRecipient(id);
        }
      });
    });

  //Add and remove items singly to vendors array
  $("#vendor-section")
    .find('input[type="checkbox"]')
    .each(function () {
      $(this).on("change", function () {
        const id = $(this).attr("id");
        const member = $(this).attr("data-member");
        const email = $(this).parent().find(".email-address").text();
        const name = $(this).parent().parent().find(".user-card-name").text();
        if (this.checked) {
          //Add item to array
          const receiver = {
            id: id,
            email: email,
            name: name,
            member: member,
          };
          mailRecipients.push(receiver);
        } else {
          //Remove master or sub-master check
          $("#send-to-all").prop("checked", false);
          $("#send-all-vendor").prop("checked", false);
          //Remove item from array
          removeRecipient(id);
        }
      });
    });

  //Custom function for mass adding / removing of members
  function addMembers(view) {
    removeMultiple(view);
    $(`#${view}-section .user-card`).each(function () {
      const id = $(this).find(".add-user").attr("id");
      const member = $(this).find(".add-user").attr("data-member");
      const email = $(this).find(".email-address").text();
      const name = $(this).find(".user-card-name").text();
      $(this).find(".add-user").prop("checked", true);
      //Add item to array
      const receiver = {
        id: id,
        email: email,
        name: name,
        member: member,
      };
      mailRecipients.push(receiver);
    });
  }

  //Custom fucntion for unchecking all checkboxes at once
  function uncheckBoxes(view) {
    $(`#${view}-section .user-card`).each(function () {
      $(this).find(".add-user").prop("checked", false);
    });
  }

  //Add or remove all admins at once
  $("#send-all-admin").on("change", function () {
    if (this.checked) {
      removeMultiple("admin");
      addMembers("admin");
    } else {
      removeMultiple("admin");
      uncheckBoxes("admin");
    }
  });

  //Add  or remove all affiliates at once
  $("#send-all-affiliate").on("change", function () {
    if (this.checked) {
      removeMultiple("affiliate");
      addMembers("affiliate");
    } else {
      removeMultiple("affiliate");
      uncheckBoxes("affiliate");
    }
  });

  //Add  or remove all vendors at once
  $("#send-all-vendor").on("change", function () {
    if (this.checked) {
      removeMultiple("vendor");
      addMembers("vendor");
    } else {
      removeMultiple("vendor");
      uncheckBoxes("vendor");
    }
  });

  //Add  or remove everyone at once
  $("#send-to-all").on("change", function () {
    if (this.checked) {
      $("#send-all-admin, #send-all-affiliate, #send-all-vendor").prop(
        "checked",
        true
      );
      //Clear the array
      mailRecipients = [];
      addMembers("admin");
      addMembers("affiliate");
      addMembers("vendor");
    } else {
      $("#send-all-admin, #send-all-affiliate, #send-all-vendor").prop(
        "checked",
        false
      );
      //Clear the array
      mailRecipients = [];
      uncheckBoxes("admin");
      uncheckBoxes("affiliate");
      uncheckBoxes("vendor");
    }
  });

  $("#close-modal").on("click", function () {
    $(".mail-overlay").css({ display: "none" });
  });

  //New code ends here

  $(".btn-send").on("click", async function () {
    const sender = "Admin";
    const subject = $(".form-subject").val().trim();
    const message = $(".form-message").val().trim();
    const attachment = $(".form-attachment").val();
    const recipients = JSON.stringify(mailRecipients);

    if (!sender || !subject || !message) {
      displayMessage("Fill out all text fields before submitting !");
      $(".btn-send").attr("disabled", false);
      return;
    } else if (mailRecipients.length === 0) {
      $(".mail-overlay").css({ display: "flex" });
      return;
    } else {
      if (!attachment || attachment === null) {
        if (confirm("Send without an attachment ?")) {
          $(".btn-send").text("Sending...").attr("disabled", true);
          //Prepare request parameters
          try {
            const payload = {
              recipients: recipients,
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
              $(
                "#send-to-all, #send-all-admin, #send-all-affiliate, #send-all-vendor"
              ).prop("checked", false);
              uncheckBoxes("admin");
              uncheckBoxes("affiliate");
              uncheckBoxes("vendor");
              mailRecipients = [];
              $(".form-control").val("");
              $(".btn-send")
                .html("<i class='fas fa-check'></i> Sent")
                .attr("disabled", true);
              setTimeout(() => {
                $(".btn-send")
                  .html("<i class='far fa-envelope'></i> Send")
                  .attr("disabled", false);
                window.location.reload();
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
          displayMessage("Select a file to continue..");
        }
      } else {
        const request = new FormData();
        request.append("recipients", recipients);
        request.append("sender", sender);
        request.append("subject", subject);
        request.append("message", message);
        request.append(
          "attachment",
          document.querySelector(".form-attachment").files[0]
        );
        $(".btn-send").text("Sending...").attr("disabled", true);

        try {
          const result = await makeRequest(
            `/mail/send`,
            "POST",
            request
          );

          if (
            result.status === 200 &&
            result.message === "Message sent successfully"
          ) {
            displayMessage(result.message, "success");
            $(
              "#send-to-all, #send-all-admin, #send-all-affiliate, #send-all-vendor"
            ).prop("checked", false);
            uncheckBoxes("admin");
            uncheckBoxes("affiliate");
            uncheckBoxes("vendor");
            mailRecipients = [];
            $(".form-control").val("");
            $(".btn-send")
              .html("<i class='fas fa-check'></i> Sent")
              .attr("disabled", true);
            setTimeout(() => {
              $(".btn-send")
                .html("<i class='far fa-envelope'></i> Send")
                .attr("disabled", false);
              window.location.reload();
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
      }
    }
  });

  //Indent all inner child navs
  $(".nav-sidebar").addClass("nav-child-indent");
})(jQuery);
