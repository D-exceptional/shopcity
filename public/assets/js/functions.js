// ----------------------------------------------------
// Import Configs
// ----------------------------------------------------
import {
  baseUrl,
  isLoggedIn,
} from "./core/index.js";
// ----------------------------------------------------
// Import UI Alerts
// ----------------------------------------------------
import { displayMessage } from "./ui/index.js";
// ----------------------------------------------------
// Import Product Searches, Filters And Amount Updates
// ----------------------------------------------------
import {
  searchProducts,
  filterProducts,
  searchCollection,
  updateAmount,
  shareContent,
  validateInput,
  toggleState,
  rateProduct
} from "./utils/index.js";
// ----------------------------------------------------------
// Import Cart And Wishlist Management + Logout And Redirect
// ----------------------------------------------------------
import {
  addToCart,
  updateCart,
  removeFromCart,
  updateCounter,
  addToWishlist,
  removeFromWishlist,
  logout,
  redirect,
} from "./modules/index.js";

(function ($) {
  ("use strict");

  // -------------------------------------------------
  // Update Count
  // -------------------------------------------------
  if (!isLoggedIn) {
    updateCounter(0);
  }

  // -------------------------------------------------
  // Main Search Functionality
  // -------------------------------------------------
  $(document).on("input", ".main-search", function () {
    const search = validateInput(this);
    if (!search) {
      return displayMessage("Enter a valid search value", "info");
    }
  });

  $(document).on("click", ".start-search", function () {
    const searchValue = validateInput($(".main-search"));
    if (!searchValue) {
      return displayMessage("Enter a valid search value", "info");
    }

    // Proceed if valid
    const resultPage = `${baseUrl}/product-search?search=${searchValue}`;
    window.location.href = resultPage;
  });

  // -------------------------------------------------
  // Main Category Filter
  // -------------------------------------------------
  $(document).on("change", ".main-category", function () {
    const category = $(this).val()?.trim();

    if (!category) {
      return displayMessage("Please select a category", "info");
    }

    const resultPage = `${baseUrl}/product-list?view=category&data=${encodeURIComponent(
      category
    )}`;
    window.location.href = resultPage;
  });

  // --------------------------------------------------------------------------
  // Sub Search In Home / Product List / Product Search / Product Store / Shop
  // --------------------------------------------------------------------------
  $(document).on("input", ".sub-search", function () {
    searchProducts();
  });

  $(document).on("click", ".search-click", function () {
    searchProducts();
  });

  // ----------------------------------------------------------------------
  // Filter In Home / Product List / Product Search / Product Store / Shop
  // ----------------------------------------------------------------------
  $(document).on("change", ".product-filter", function () {
    filterProducts();
  });

  // --------------------------------------------------------------------------
  // Sub Search In Product Details
  // --------------------------------------------------------------------------
  $(document).on("input", ".collection-search", function () {
    searchCollection();
  });

  $(document).on("click", ".collection-click", function () {
    searchCollection();
  });

  // --------------------------------------------------------------------------
  // Filter Products By Amount
  // --------------------------------------------------------------------------
  $(document).on("input", ".price-range", function () {
    const value = $(this).val();
    updateAmount(value);
  });

  // -------------------------------------------------
  // Add To (Cart + Wishlist) Functionality
  // -------------------------------------------------
  $(document).on("click", "a.cart-add, a.wishlist-add", function (e) {
    e.preventDefault();

    //const isLoggedIn      = $(this).hasClass("cart-user");
    const parentContainer = $(this).closest(".product-card");
    const productId = parentContainer.data("id");
    const productName = parentContainer.find(".product-name").text().trim();
    const productCategory = parentContainer
      .find(".product-category")
      .text()
      .trim();
    const productPrice = parentContainer.find(".price-tag").text().trim();
    const slashPrice = parentContainer.find(".price-slash").text().trim();
    const productImage = parentContainer.hasClass("product-details")
      ? $(".single-carousel .single-inner").first().find("img").attr("src")
      : parentContainer.find(".product-image").attr("src");
    const quantity = 1;

    // Take action
    if ($(e.target).hasClass("cart-add")) {
      // Add item to cart
      addToCart(
        productId,
        productName,
        productCategory,
        productPrice,
        slashPrice,
        productImage,
        quantity
      );
    } else {
      // Add item to wishlist
      addToWishlist(
        productId,
        productName,
        productCategory,
        productPrice,
        slashPrice,
        productImage,
        quantity
      );
    }
  });

  // -------------------------------------------------
  // Update Cart Functionality
  // -------------------------------------------------
  $(document).on("blur", ".quantity button, .quantity input", function () {
    const itemContainer = $(this).closest(".cart-item");
    const productId = itemContainer.data("pid");
    const input = itemContainer.find(".item-quantity");
    const newQuantity = parseInt(input.val()) || 1;

    // Update cart
    updateCart(productId, newQuantity);
  });

  // -------------------------------------------------
  // Remove From Cart Functionality
  // -------------------------------------------------
  $(document).on("click", ".item-remove", function (e) {
    const itemContainer = $(this).closest(".table-item");
    const productId = itemContainer.data("pid");
    const isSure = confirm(`Are you sure to remove item ?`);

    if (isSure) {
      // Take action
      if (itemContainer.hasClass("item-shopping")) {
        // Remove item from cart
        removeFromCart(productId);
      } else {
        // Remove item from wishlist
        removeFromWishlist(productId);
      }
    }
  });

  // -------------------------------------------------
  // View Product Details From Cart Or Wishlist
  // -------------------------------------------------
  $(document).on(
    "click",
    ".table-item .product-image, .table-item .product-name",
    function () {
      const productId = $(this).closest(".table-item").data("pid");

      const linkPath = `${baseUrl}/product-details?id=${encodeURIComponent(
        productId
      )}`;
      window.location.href = linkPath;
    }
  );

  // -------------------------------------------------
  // Add To (Cart + Wishlist) Functionality
  // -------------------------------------------------
  $(document).on("click", "a.product-share", function (e) {
    e.preventDefault();

    const parentContainer = $(this).closest(".product-card");
    const productId = parentContainer.data("id");
    const productName = parentContainer.find(".product-name").text().trim();
    const productImage = parentContainer.hasClass("product-details")
      ? $(".single-carousel .single-inner").first().find("img").attr("src")
      : parentContainer.find(".product-image").attr("src");
    /*const productCategory = parentContainer
      .find(".product-category")
      .text()
      .trim();
    const productPrice = parentContainer.find(".price-tag").text().trim();
    const slashPrice = parentContainer.find(".price-slash").text().trim();
    const productImage = parentContainer.hasClass("product-details")
      ? $(".single-carousel .single-inner").first().find("img").attr("src")
      : parentContainer.find(".product-image").attr("src");
    */

    shareContent({
      title: productName,
      text: "Check out this awesome product on Mrsamase marketplace!",
      url: `${baseUrl}/product-details?id=${productId}`,
      imageUrl: productImage,
    });
  });

  // -------------------------------------------------
  // Add Review
  // -------------------------------------------------
  $(document).on("click", ".rating-stars i", function () {
    toggleState($(this));
  });

  $(document).on("input", ".form-review", function () {
    validateInput(this);
  });

  $(document).on("click", ".btn-review", async function (e) {
    e.preventDefault();
    const linkText = $(this).text().trim();

    if (["Login", "Log In"].includes(linkText)) {
      redirect(`${baseUrl}/login`);
    } else {
      await rateProduct();
    }
  });

  // -------------------------------------------------
  // Logout For Users
  // -------------------------------------------------
  $(document).on("click", ".btn-call, .header-log", function (e) {
    e.preventDefault();
    const linkText = $(this).text().trim();

    if (["Logout", "Log Out"].includes(linkText)) {
      logout();
    } else {
      redirect(`${baseUrl}/login`);
    }
  });
})(jQuery);
