// ----------------------------------------------------
// Add To Cart (Logged-in AND Non Logged-in Users)
// ----------------------------------------------------
export const GUEST_CART_KEY = "cart";

export function getGuestCart() {
  const cart = JSON.parse(localStorage.getItem(GUEST_CART_KEY) || "[]");
  return Array.isArray(cart) ? cart : [];
}

export function saveGuestCart(cart) {
  localStorage.setItem(GUEST_CART_KEY, JSON.stringify(cart));
}

export function clearGuestCart() {
  localStorage.removeItem(GUEST_CART_KEY);
}

// ----------------------------------------------------
// Add To Wishlist (Logged-in AND Non Logged-in Users)
// ----------------------------------------------------
export const GUEST_WISHLIST_KEY = "wishlist";

export function getGuestWishlist() {
  const wishlist = JSON.parse(localStorage.getItem(GUEST_WISHLIST_KEY) || "[]");
  return Array.isArray(wishlist) ? wishlist : [];
}

export function saveGuestWishlist(wishlist) {
  localStorage.setItem(GUEST_WISHLIST_KEY, JSON.stringify(wishlist));
}

export function clearGuestWishlist() {
  localStorage.removeItem(GUEST_WISHLIST_KEY);
}

// ----------------------------------------------------
// Manage Token
// ----------------------------------------------------

export function getApiToken() {
  return localStorage.getItem("api_token");
}

export function saveApiToken(token) {
  
  if (typeof token !== "string" || !token.trim()) {
    console.error("Invalid API token:", token);
    return false;
  }

  localStorage.setItem("api_token", token);

  return true;
}

export function clearApiToken() {
  localStorage.removeItem("api_token");
}
