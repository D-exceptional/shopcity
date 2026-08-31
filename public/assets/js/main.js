// ----------------------------------------------------
// Import Configs
// ----------------------------------------------------
import {
  baseUrl,
  isLoggedIn,
} from "./core/index.js";
// ----------------------------------------------------
// Import Extraction
// ----------------------------------------------------
import {
  extractAmount,
  updateCartPage
} from "./utils/index.js";
// ----------------------------------------------------
// Import UI Alerts
// ----------------------------------------------------
import { displayMessage } from "./ui/index.js";
// ----------------------------------------------------
// Import Push Client
// ----------------------------------------------------
import PushClient from "./push/PushClient.js";

(function ($) {
  ("use strict");

  // -------------------------------------------------
  // Get current page
  // -------------------------------------------------
  const urlPath = window.location.pathname;
  const urlContent = urlPath.split("/")[4].toLowerCase();
  const isHome = ["", null].includes(urlContent);
  const activePage = isHome ? $(".nav-home") : $(`.nav-${urlContent}`);
  // ----- Update pointer ----- //
  $(".nav-item").removeClass("active");
  activePage.addClass("active");
  // ----- Hide `Home` link on home page ----- //
  $(".nav-home").css({ display: isHome ? "none" : "block" });

  // -------------------------------------------------
  // Initialize push client (run only once per session)
  // -------------------------------------------------
  PushClient.bootstrap("customer");

  // -------------------------------------------------
  // Hide Notification Button If Not Logged In
  // -------------------------------------------------
  $(".notification-bell").css("display", isLoggedIn ? "flex" : "none");

  // -------------------------------------------------
  // Manual notification toggle
  // -------------------------------------------------
  let pushBusy = false;

  $(".notification-bell").on("click", async function () {
    if (!PushClient.initialized) {
      displayMessage(
        "Initializing notifications, please try again shortly.",
        "info",
      );
      return;
    }

    if (pushBusy) return;
    pushBusy = true;

    try {
      const state = await PushClient.state();

      if (state === "blocked") {
        displayMessage(
          "Notifications are blocked in your browser settings. Please enable them manually to continue.",
          "warning",
        );
        return;
      }

      if (state === "unsubscribed") {
        if (!isLoggedIn) {
          displayMessage("Please log in to enable notifications.", "info");
          return;
        } else {
          await PushClient.subscribe();
        }
      } else if (state === "subscribed") {
        if (confirm("Are you sure to disable notifications?")) {
          await PushClient.unsubscribe();
        }
      }
    } finally {
      pushBusy = false;
    }
  });

  // Spinner
  var spinner = function () {
    setTimeout(function () {
      if ($("#spinner").length > 0) {
        $("#spinner").removeClass("show");
      }
    }, 1);
  };
  spinner(0);

  // Initiate the wowjs
  new WOW().init();

  // Sticky Navbar
  $(window).scroll(function () {
    if ($(this).scrollTop() > 45) {
      $(".nav-bar").addClass("sticky-top shadow-sm");
    } else {
      $(".nav-bar").removeClass("sticky-top shadow-sm");
    }
  });

  // Hero Header carousel
  $(".header-carousel").owlCarousel({
    items: 1,
    autoplay: true,
    smartSpeed: 2000,
    center: false,
    dots: true,
    loop: true,
    margin: 0,
    nav: true,
    navText: [
      '<i class="bi bi-arrow-left"></i>',
      '<i class="bi bi-arrow-right"></i>',
    ],
  });

  // ProductList carousel
  $(".productList-carousel").owlCarousel({
    autoplay: true,
    smartSpeed: 2000,
    dots: false,
    loop: true,
    margin: 25,
    nav: true,
    navText: [
      '<i class="fas fa-chevron-left"></i>',
      '<i class="fas fa-chevron-right"></i>',
    ],
    responsiveClass: true,
    responsive: {
      0: {
        items: 1,
      },
      576: {
        items: 1,
      },
      768: {
        items: 2,
      },
      992: {
        items: 2,
      },
      1200: {
        items: 3,
      },
    },
  });

  // ProductList categories carousel
  $(".productImg-carousel").owlCarousel({
    autoplay: true,
    smartSpeed: 1500,
    dots: false,
    loop: true,
    items: 1,
    margin: 25,
    nav: true,
    navText: [
      '<i class="bi bi-arrow-left"></i>',
      '<i class="bi bi-arrow-right"></i>',
    ],
  });

  // Single Products carousel
  $(".single-carousel").owlCarousel({
    autoplay: true,
    smartSpeed: 1500,
    dots: true,
    dotsData: true,
    loop: true,
    items: 1,
    nav: true,
    navText: [
      '<i class="bi bi-arrow-left"></i>',
      '<i class="bi bi-arrow-right"></i>',
    ],
  });

  // ProductList carousel
  $(".related-carousel").owlCarousel({
    autoplay: true,
    smartSpeed: 1500,
    dots: false,
    loop: true,
    margin: 25,
    nav: true,
    navText: [
      '<i class="fas fa-chevron-left"></i>',
      '<i class="fas fa-chevron-right"></i>',
    ],
    responsiveClass: true,
    responsive: {
      0: {
        items: 1,
      },
      576: {
        items: 1,
      },
      768: {
        items: 2,
      },
      992: {
        items: 3,
      },
      1200: {
        items: 4,
      },
    },
  });

  // Product Quantity
  // Handle typing
  $(".quantity input").on("input", function () {
    this.value = this.value.replace(/[^0-9]/g, "");
    if (this.value === "" || parseInt(this.value) < 1) this.value = 1;
  });

  // Handle + / - buttons
  $(document).on("click", ".quantity button", function () {
    const button = $(this);
    const buttonParent = $(this).closest(".quantity");
    const buttonContainer = $(this).closest("tr.cart-item");
    const quantityCount = buttonParent.find("input");
    const itemPrice = extractAmount(buttonContainer.find(".item-price").text());

    let oldValue = parseFloat(quantityCount.val()) || 1;

    let newVal = button.hasClass("btn-plus")
      ? oldValue + 1
      : Math.max(1, oldValue - 1);

    //input.val(newVal).trigger("change");
    quantityCount.val(newVal);

    // Calculate new total
    const updatedItemTotal = newVal * itemPrice;
    buttonContainer
      .find(".item-total")
      .text(`₦ ${Math.round(updatedItemTotal).toLocaleString()}`);

    // Update page
    updateCartPage();
  });

  // Handle quantity input
  $(document).on("input", ".quantity input", function () {
    const quantityCount = $(this);
    const buttonContainer = $(this).closest("tr.cart-item");
    const itemPrice = extractAmount(buttonContainer.find(".item-price").text());

    let newVal = quantityCount.val().replace(/[^0-9]/g, "");

    //input.val(newVal).trigger("change");
    quantityCount.val(newVal);

    // Validation check
    if (!newVal?.trim()) {
      return displayMessage("Enter a valid number", "info");
    }

    if (isNaN(newVal)) {
      return displayMessage("Enter only numbers", "info");
    }

    // Calculate new total
    const updatedItemTotal = newVal * itemPrice;
    buttonContainer
      .find(".item-total")
      .text(`₦ ${Math.round(updatedItemTotal).toLocaleString()}`);

    // Update page
    updateCartPage();
  });

  // Back to top button
  $(window).scroll(function () {
    if ($(this).scrollTop() > 300) {
      $(".back-to-top").fadeIn("slow");
    } else {
      $(".back-to-top").fadeOut("slow");
    }
  });
  $(".back-to-top").click(function () {
    $("html, body").animate({ scrollTop: 0 }, 1500, "easeInOutExpo");
    return false;
  });

  // View policy documents
  $(document).on("click", ".btn-doc", function () {
    const file = $(this).data("doc");
    const url = `${baseUrl}/assets/docs/${file}`;
    const ext = file.split(".").pop().toLowerCase();
    const overlay = $(".overlay");

    if (url) {
      if (["pdf", "doc", "docx", "xls", "xlsx", "ppt", "pptx"].includes(ext)) {
        // Use Google Docs Viewer
        const gdocUrl = `https://docs.google.com/gview?url=${encodeURIComponent(
          url,
        )}&embedded=true`;
        $("<iframe>", {
          src: gdocUrl,
          width: "100%",
          height: "100%",
          frameborder: 0,
          css: { border: "none" },
        }).appendTo(overlay);
        $(".overlay").css({ display: "flex" });
      } else if (["jpg", "jpeg", "png", "gif", "webp"].includes(ext)) {
        // Use native image viewer
        $("<img>", {
          src: url,
          width: "100%",
          height: "100%",
          css: { objectFit: "contain" },
        }).appendTo(overlay);
        $(".overlay").css({ display: "flex" });
      } else {
        alert("Unsupported file type");
      }
    } else {
      displayMessage("File not found", "warn");
    }
  });

  // -------------------------------------------------
  // Close Overlay
  // -------------------------------------------------
  $(".close-overlay").on("click", function () {
    $(".overlay").css({ display: "none" });
    $(".overlay").find("iframe").remove();
  });

})(jQuery);
