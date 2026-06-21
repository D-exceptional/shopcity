// ----------------------------------------------------
// Import Wishlist Saving Functions From js/core/storage
// ----------------------------------------------------
import {
  getGuestWishlist,
  saveGuestWishlist,
  clearGuestWishlist,
  isLoggedIn,
  makeRequest
} from "../core/index.js";
// ----------------------------------------------------
// Import UI Alerts
// ----------------------------------------------------
import { displayMessage } from "../ui/index.js";
// ----------------------------------------------------
// Import Table Header
// ----------------------------------------------------
import { createTableHead } from "../utils/index.js";

export function guestWishlistAdd(
  productId,
  name,
  category,
  price,
  slash,
  image = "assets/img/product-1.png",
  quantity = 1
) {
  const wishlist = getGuestWishlist();
  const existing = wishlist.find((item) => item.productId === productId);
  if (existing) {
    existing.quantity = (existing.quantity || 0) + quantity;
  } else {
    wishlist.push({
      productId,
      name,
      category,
      price,
      slash,
      image,
      quantity,
    });
  }
  saveGuestWishlist(wishlist);
}

export function guestWishlistRemove(productId) {
  const wishlist = getGuestWishlist();
  // Keep all items except the one being removed
  const updatedWishlist = wishlist.filter(
    (item) => item.productId !== Number(productId)
  );

  saveGuestWishlist(updatedWishlist);
}

// Public function used by Add to Wishlist buttons
export async function addToWishlist(
  productId,
  name,
  category,
  price,
  slash,
  image,
  quantity = 1
) {
  if (isLoggedIn) {
    // Try server-side add
    try {
      const result = await makeRequest(`/wishlist`, "POST", {
        productId: productId,
        quantity: quantity,
      });

      if (
        result.status === 200 &&
        result.message === "Item added to wishlist"
      ) {
        // Server accepted
        displayMessage("Item added to wishlist", "success");
      } else {
        displayMessage("Could not add item to wishlist", "warning");
      }
    } catch (err) {
      // Network error — fallback to guest cart for offline reliability
      displayMessage(
        "Network error occured, saving to offline wishlist",
        "warning"
      );
    }
    guestWishlistAdd(productId, name, category, price, slash, image, quantity);
  } else {
    guestWishlistAdd(productId, name, category, price, slash, image, quantity);
    displayMessage("Item added to wishlist", "success");
  }
}

// ----------------------------------------------------
// Remove Wishlist From View
// ----------------------------------------------------
export function removeWishlistFromUI(productId) {
  $(".table-item").each(function () {
    if ($(this).data("pid") === productId) {
      $(this).remove();
    }
  });

  // Check if any cart items remain
  const remainingItems = $(".table-item").length;
  if (remainingItems === 0) {
    $("thead").remove();
    $(".table-responsive").addClass("nmt-25");
    $("tbody")
      .empty()
      .html(
        `<tr>
          <td colspan="7" class="text-center py-5">
            <i class="fa fa-shopping-cart fa-3x text-muted mb-3"></i>
            <p class="lead mb-2">Your wishlist is empty.</p>
            <a href="/" class="btn btn-primary btn-sm">
              <i class="fa fa-shopping-bag me-1"></i> Start Shopping
            </a>
          </td>
        </tr>
      `
      );
  }
}

// ----------------------------------------------------
// Remove From Wishlist (Logged-in AND Non Logged-in Users)
// ----------------------------------------------------
export async function removeFromWishlist(productId) {
  if (isLoggedIn) {
    // Try server-side add
    try {
      const result = await makeRequest(`/wishlist`, "DELETE", {
        productId: productId,
      });

      if (
        result.status === 200 &&
        result.message === "Item removed from wishlist"
      ) {
        // Server accepted
        displayMessage("Item removed from wishlist", "success");
      } else {
        displayMessage("Could not remove item from wishlist", "warning");
      }
    } catch (err) {
      // Network error — fallback to guest cart for offline reliability
      displayMessage(
        "Network error occured, saving to offline wishlist",
        "warning"
      );
    }
    removeWishlistFromUI(productId);
    guestWishlistRemove(productId);
  } else {
    displayMessage("Item removed from wishlist", "success");
    removeWishlistFromUI(productId);
    guestWishlistRemove(productId);
  }
}

// ---------------------------------------------------------------
// Sync Wishlist To Backend (Just Logged-in Users) At Login
// ------------------------------------------------------------------
export async function syncWishlist() {
  const wishlist = getGuestWishlist();
  if (!wishlist || wishlist.length === 0) {
    // Nothing to merge
    return;
  }

  try {
    const result = await makeRequest(`/wishlist/merge`, "PUT", {
      wishlist: wishlist,
    });

    if (
      result.status === 200 &&
      result.message === "Wishlist merged successfully"
    ) {
      clearGuestWishlist();
      //displayMessage("Wishlist synced successfully", "success");
    } else {
      displayMessage("Wishlist sync failed", "info");
    }
  } catch (e) {
    displayMessage("Sync network error", "warning");
  }
}

// ---------------------------------------------------------------
// Display Wishlist (Logged-out Users)
// ------------------------------------------------------------------
export async function displayWishlist() {
  if (!isLoggedIn) {
    const wishlist = getGuestWishlist();
    if (!wishlist || wishlist.length === 0) {
      // Nothing to merge
      return;
    }

    // Display table head
    createTableHead("Wishlist");

    // Clear old content
    $("tbody").empty();

    // Loop through cart and render
    wishlist.forEach((item) => {
      const { productId, name, category, price, image } = item;

      const itemHTML = `
        <tr class="table-item item-wishlist" data-id="wishlist-${productId}" data-pid="${productId}">
          <td>
              <p class="mb-0 py-4">#</p>
          </td>
          <td>
              <img src="${
                image || "assets/img/product-1.png"
              }" class="img-fluid rounded w-50 h-50 product-image" alt="Product Image"/>
          </td>
          <th scope="row">
              <p class="mb-0 py-4 product-name">${name}</p>
          </th>
          <td>
              <p class="mb-0 py-4">${category}</p>
          </td>
          <td>
              <p class="mb-0 py-4">${price}</p>
          </td>
          <td class="py-4">
              <button class="btn btn-md rounded-circle bg-light border item-remove">
                  <i class="fa fa-times text-danger"></i>
              </button>
          </td>
        </tr>
      `;

      $("tbody").append(itemHTML);
    });
  }
}
