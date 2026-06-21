// ----------------------------------------------------
// Import Cart Saving Functions From js/core/storage
// ----------------------------------------------------
import {
  getGuestCart,
  saveGuestCart,
  clearGuestCart,
  isLoggedIn,
  makeRequest
} from "../core/index.js";
// ----------------------------------------------------
// Import UI Alerts
// ----------------------------------------------------
import { displayMessage } from "../ui/index.js";
// ----------------------------------------------------
// Import Extraction
// ----------------------------------------------------
import { extractAmount, updateCartPage, createTableHead } from "../utils/index.js";


export function guestCartAdd(
  productId,
  name,
  category,
  price,
  slash,
  image = "assets/img/product-1.png",
  quantity = 1
) {
  const cart = getGuestCart();
  const existing = cart.find((item) => item.productId === productId);
  if (existing) {
    existing.quantity = (existing.quantity || 0) + quantity;
  } else {
    cart.push({ productId, name, category, price, slash, image, quantity });
  }
  saveGuestCart(cart);
}

export function guestCartUpdate(productId, quantity) {
  const cart = getGuestCart();
  const existing = cart.find((item) => item.productId === productId);
  if (existing) {
    existing.quantity = quantity;
  }
  saveGuestCart(cart);
}

export function guestCartRemove(productId) {
  const cart = getGuestCart();
  // Keep all items except the one being removed
  const updatedCart = cart.filter(
    (item) => item.productId !== Number(productId)
  );

  saveGuestCart(updatedCart);
}

// Public function used by Add to Cart buttons
export async function addToCart(
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
      const result = await makeRequest(`/cart`, "POST", {
        productId: productId,
        quantity: quantity,
      });

      if (result.status === 200 && result.message === "Item added to cart") {
        // Server accepted
        updateCounter(result.data.count);
        displayMessage("Item added to cart", "success");
        updateCartPage();
      } else {
        displayMessage("Could not add item to cart", "warning");
      }
    } catch (err) {
      console.log(err);
      // Network error — fallback to guest cart for offline reliability
      displayMessage(
        "Network error occured, saving to offline cart",
        "warning"
      );
    }
    guestCartAdd(productId, name, category, price, slash, image, quantity);
  } else {
    guestCartAdd(productId, name, category, price, slash, image, quantity);
    displayMessage("Item added to cart", "success");
    updateCounter();
    updateCartPage();
  }
}

// ----------------------------------------------------
// Update Cart (Logged-in AND Non Logged-in Users)
// ----------------------------------------------------
export async function updateCart(productId, quantity) {
  if (isLoggedIn) {
    // Try server-side add
    try {
      const result = await makeRequest(`/cart`, "PUT", {
        productId: productId,
        quantity: quantity,
      });

      if (
        result.status === 200 &&
        result.message === "Cart updated successfully"
      ) {
        // Server accepted
        displayMessage("Cart updated", "success");
      } else {
        displayMessage("Could not update cart", "warning");
      }
    } catch (err) {
      // Network error — fallback to guest cart for offline reliability
      displayMessage("Network error occured, updating offline cart", "warning");
    }
    guestCartUpdate(productId, quantity);
  } else {
    guestCartUpdate(productId, quantity);
    displayMessage("Cart updated", "success");
  }
}

// ----------------------------------------------------
// Remove Cart From View
// ----------------------------------------------------
export function removeCartFromUI(productId) {
  $(".cart-item").each(function () {
    if ($(this).data("pid") === productId) {
      $(this).remove();
    }
  });

  // Check if any cart items remain
  const remainingItems = $(".cart-item").length;
  if (remainingItems === 0) {
    $("thead").remove();
    $(".table-responsive").addClass("nmt-25");
    $("tbody")
      .empty()
      .html(
      ` <tr>
          <td colspan="8" class="text-center py-5">
            <i class="fa fa-shopping-cart fa-3x text-muted mb-3"></i>
            <p class="lead mb-2">Your cart is empty.</p>
            <a href="./" class="btn btn-primary btn-sm">
              <i class="fa fa-shopping-bag me-1"></i> Start Shopping
            </a>
          </td>
        </tr>
      `);
  }
}

// ----------------------------------------------------
// Remove From Cart (Logged-in AND Non Logged-in Users)
// ----------------------------------------------------
export async function removeFromCart(productId) {
  if (isLoggedIn) {
    // Try server-side add
    try {
      const result = await makeRequest(`/cart`, "DELETE", {
        productId: productId,
      });

      if (
        result.status === 200 &&
        result.message === "Item removed from cart"
      ) {
        // Server accepted
        displayMessage("Item removed from cart", "success");
        updateCounter(result.data.count);
        removeCartFromUI(productId);
        setTimeout(() => {
          updateCartPage();
        }, 1000);
      } else {
        displayMessage("Could not remove item from cart", "warning");
      }
    } catch (err) {
      // Network error — fallback to guest cart for offline reliability
      displayMessage("Network error occured, updating offline cart", "warning");
    }
    guestCartRemove(productId);
  } else {
    displayMessage("Item removed from cart", "success");
    removeCartFromUI(productId);
    guestCartRemove(productId);
    updateCounter();
    setTimeout(() => {
      updateCartPage();
    }, 1000);
  }
}

// ---------------------------------------------------------------
// Sync Cart To Backend (Just Logged-in Users) At Login
// ------------------------------------------------------------------
export async function syncCart() {
  const cart = getGuestCart();
  if (!cart || cart.length === 0) {
    // Nothing to merge
    return;
  }

  try {
    const result = await makeRequest(`/cart/merge`, "PUT", {
      cart: cart,
    });

    if (
      result.status === 200 &&
      result.message === "Cart merged successfully"
    ) {
      clearGuestCart();
      //displayMessage("Cart synced successfully", "success");
    } else {
      displayMessage("Cart sync failed", "info");
    }
  } catch (e) {
    displayMessage("Sync network error", "warning");
  }
}

// ---------------------------------------------------------------
// Display Cart (Logged-out Users)
// ------------------------------------------------------------------
export async function displayCart() {
  if (!isLoggedIn) {
    const cart = getGuestCart();
    if (!cart || cart.length === 0) {
      // Nothing to merge
      return;
    }

    // Display table head
    createTableHead("Cart");

    // Clear old content
    $("tbody").empty();

    // Loop through cart and render
    cart.forEach((item) => {
      const { productId, name, category, price, image, quantity } = item;

      const itemHTML = `
                <tr class="cart-item table-item item-shopping" data-id="cart-${productId}" data-pid="${productId}">
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
                        <p class="mb-0 py-4 item-price">${price}</p>
                    </td>
                    <td>
                        <div class="input-group quantity py-4" style="width: 100px;">
                            <div class="input-group-btn">
                                <button class="btn btn-sm btn-minus rounded-circle bg-light border">
                                    <i class="fa fa-minus"></i>
                                </button>
                            </div>
                            <input type="text" class="form-control form-control-sm text-center border-0 item-quantity"
                                value="${quantity}">
                            <div class="input-group-btn">
                                <button class="btn btn-sm btn-plus rounded-circle bg-light border">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </td>
                    <td>
                        <p class="mb-0 py-4 item-total">₦ ${(
                          extractAmount(price) * quantity
                        ).toLocaleString()}</p>
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

// UI updater (accepts number or uses localStorage fallback)
export function updateCounter(count) {
  const container = $(".cart-total");
  if (!container) {
    return displayMessage("Cart counter element not found", "warning");
  }
  if (isLoggedIn) {
    if (count) {
      container.text(count);
      return;
    } else {
      return displayMessage(
        `Invalid count value provided as: ${count}`,
        "warning"
      );
    }
  } else {
    // If no count passed, determine from server or guest cart
    const guest = getGuestCart();
    // Count total number of products in cart
    /*
      if (guest.length) {
        const sum = guest.reduce((s, it) => s + (it.quantity || 0), 0);
        container.text(sum);
      } 
      else {
        container.text("0");
      }
      */

    // Count unique number of products in cart
    if (guest && guest.length) {
      container.text(guest.length);
    } else {
      container.text("0");
    }
  }
}
