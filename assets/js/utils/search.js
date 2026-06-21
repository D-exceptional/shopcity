// ----------------------------------------------------
// Import UI Alerts
// ----------------------------------------------------
import { displayMessage } from "../ui/index.js";
// ----------------------------------------------------
// Import Validations
// ----------------------------------------------------
import { validateInput, extractAmount } from "../utils/index.js";


// -------------------------------
  // Search Products
  // -------------------------------
  export function searchProducts() {
    const searchValue = validateInput($(".sub-search"));
    const productContainer = $(".product, .products-mini");
    const cards = productContainer.find(".product-card");
    const pagination = productContainer.find(".pagination-row");
    const hasCard = cards.length > 0;
    let tracker = 0;

    // If the search box is empty
    if (!searchValue) {
      if (hasCard) {
        productContainer.find(".no-match").remove();
        cards.each(function () {
          if(!$(this).hasClass("no-match")) {
            $(this).css("display", "block"); // show all cards again
          }
        });
        pagination.css("display", "block");
      } else {
        pagination.css("display", "none");
      }
      return displayMessage("Enter a valid search value", "info");
    }
    else {
      // Filter cards based on search value
      if (hasCard) {
        const query = searchValue.toLowerCase();
        pagination.css("display", "block");
        productContainer.find(".no-match").remove();

        cards.each(function () {
          const card = $(this);
          const productName = card
            .find("a.d-block.h4")
            .text()
            .trim()
            .toLowerCase();

          if (productName.includes(query)) {
            card.css("display", "block");
            tracker++;
          } else {
            card.css("display", "none");
          }
        });

        // After looping, check if there was a match
        if (tracker === 0) {
          pagination.css("display", "none");
          productContainer.find(".no-match").remove();
          productContainer.prepend(
            `<div class="d-flex justify-content-center align-items-center no-match"><p>No product matched</p></div>`
          );
          displayMessage("No match found for search", "info");
        }
      } else {
        pagination.css("display", "none");
      }
    }
  }

  // -------------------------------
  // Filter Products
  // -------------------------------
  export function filterProducts() {
    const filterValue = $(".product-filter").val();
    const productContainer = $(".product-main, .products-mini-main");
    const cards = productContainer.find(".product-card.card-main");
    const pagination = productContainer.find(".pagination-row");
    const hasCard = cards.length > 0;

    // If the search box is empty
    if (!filterValue) {
      if (hasCard) {
        productContainer.find(".no-match").remove();
        cards.each(function () {
          $(this).css("display", "block"); // show all cards again
        });
        pagination.css("display", "block");
      } else {
        productContainer.prepend(
          `<div class="d-flex justify-content-center align-items-center no-match"><p>No product available</p></div>`
        );
        pagination.css("display", "none");
      }
      return displayMessage("Select a filter from the dropdown menu", "info");
    }

    let sorted = [];

    // Filter cards based on search value
    if (hasCard) {
      switch (filterValue) {
        case "Ascending":
          sorted = cards.sort((a, b) =>
            $(a)
              .find("a.d-block.h4")
              .text()
              .localeCompare($(b).find("a.d-block.h4").text())
          );
          break;

        case "Descending":
          sorted = cards.sort((a, b) =>
            $(b)
              .find("a.d-block.h4")
              .text()
              .localeCompare($(a).find("a.d-block.h4").text())
          );
          break;

        case "Recent":
          sorted = cards.sort((a, b) => $(b).data("id") - $(a).data("id"));
          break;

        case "Rating":
          sorted = cards.sort(
            (a, b) => $(b).data("rating") - $(a).data("rating")
          );
          break;

        case "PriceUp":
          sorted = cards.sort(
            (a, b) =>
              extractAmount($(a).find(".price-tag").text()) -
              extractAmount($(b).find(".price-tag").text())
          );
          break;

        case "PriceDown":
          sorted = cards.sort(
            (a, b) =>
              extractAmount($(b).find(".price-tag").text()) -
              extractAmount($(a).find(".price-tag").text())
          );
          break;
      }

      // ✅ Preserve pagination by detaching it first, then re-adding
      pagination.detach();

      // Reattach sorted cards
      productContainer.fadeOut(200, function () {
        //$(this).empty().append(sorted).fadeIn(200);
        // Remove only the cards
        $(this).find(".product-card.card-main").remove();
        // Append sorted cards + pagination back
        $(this).append(sorted).append(pagination).fadeIn(200);
      });
      //pagination.css("display", "block");
    } else {
      pagination.css("display", "none");
      productContainer
        .empty()
        .prepend(
          `<div class="d-flex justify-content-center align-items-center no-match"><p>No product available</p></div>`
        );
    }
  }

  // -------------------------------
  // Search Collections
  // -------------------------------
  export function searchCollection() {
    const searchValue = validateInput($(".collection-search"));
    const itemContainer = $(".list-unstyled");
    const cards = itemContainer.find("li");
    const hasCard = cards.length > 0;
    let tracker = 0;

    // If the search box is empty
    if (!searchValue) {
      if (hasCard) {
        cards.each(function () {
          $(this).css("display", "block"); // show all cards again
        });
      } else {
        // Do something else here
      }
      return displayMessage("Enter a valid search value", "info");
    }

    // Filter cards based on search value
    if (hasCard) {
      const query = searchValue.toLowerCase();

      cards.each(function () {
        const card = $(this);
        const itemName = card
          .clone() // clone the element
          .children("i") // select the icon
          .remove() // remove the icon
          .end() // go back to the <a>
          .text() // get remaining text
          .trim() // clean whitespace
          .toLowerCase(); // convert to lower case

        if (itemName.includes(query)) {
          card.css("display", "block");
          tracker++;
        } else {
          card.css("display", "none");
        }
      });

      // After looping, check if there was a match
      if (tracker === 0) {
        displayMessage("No match found for search", "info");
      }
    } else {
      // Do something else here
    }
  }