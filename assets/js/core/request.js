// ----------------------------------------------------
// Import Config
// ----------------------------------------------------
import { apiUrl } from "../core/index.js";

// ----------------------------------------------------
//  Make Network Requests
// ----------------------------------------------------
/**
 * Robust fetch wrapper with full response return
 * @param {string} url - The endpoint URL
 * @param {string} [method="GET"] - HTTP method (GET, POST, PUT, DELETE, PATCH)
 * @param {object} [payload={}] - Request body or query params
 * @param {object} [customHeaders={}] - Additional headers to include
 * @param {number} [timeout=15000] - Request timeout in milliseconds
 * @returns {Promise<{status: number, ok: boolean, headers: Headers, data: any, raw: Response, error: any}>}
 */

/**
 * Smart Request Handler (JSON + FormData + URLSearchParams)
 */
export async function makeRequest(
  url = "/",
  method = "GET",
  payload = null,
  customHeaders = {},
  timeout = 60000,
) {
  method = method.toUpperCase();

  const controller = new AbortController();
  const timeoutId = setTimeout(() => controller.abort(), timeout);

  let headers = { ...customHeaders };

  // ------------------------------------------
  // ATTACH CSRF HEADER FOR STATE-CHANGING METHODS
  // ------------------------------------------
  if (["POST", "PUT", "PATCH", "DELETE"].includes(method)) {
    const csrfToken = getCsrfToken();
    if (csrfToken) {
      headers["X-CSRF-TOKEN"] = csrfToken;
    } else {
      console.warn("CSRF token not found in meta tag.");
    }
  }

  // ------------------------------------------
  // HANDLE GET (query params)
  // ------------------------------------------
  if (method === "GET" && payload && typeof payload === "object") {
    const query = new URLSearchParams(payload).toString();
    if (query) url += (url.includes("?") ? "&" : "?") + query;
  }

  // ------------------------------------------
  // HANDLE BODY (POST/PUT/PATCH)
  // ------------------------------------------
  let body = null;

  if (method !== "GET" && method !== "HEAD") {
    if (payload instanceof FormData) {
      // Ensure no content-type is set for multipart
      delete headers["Content-Type"];
      delete headers["content-type"];
      delete headers["CONTENT-TYPE"];

      body = payload;
    } else if (payload instanceof URLSearchParams) {
      headers["Content-Type"] = "application/x-www-form-urlencoded";
      body = payload.toString();
    } else if (payload && typeof payload === "object") {
      headers["Content-Type"] = "application/json";
      body = JSON.stringify(payload);
    } else {
      body = payload;
    }
  }

  // ------------------------------------------
  // NOW create options (AFTER header fixes)
  // ------------------------------------------
  const options = {
    method,
    headers,
    signal: controller.signal,
    body,
  };

  try {
    // Build full endpoint
    const endpoint = apiUrl + url;
    
    const res = await fetch(endpoint, options);
    clearTimeout(timeoutId);

    const contentType = res.headers.get("content-type");
    let data;

    try {
      if (contentType?.includes("application/json")) {
        data = await res.json();
      } else {
        data = await res.text();
      }
    } catch (e) {
      data = null;
    }

    // Return server response object to caller
    return data;
  } catch (err) {
    clearTimeout(timeoutId);

    return {
      ok: false,
      status: 0,
      headers: {},
      data: null,
      raw: null,
      error: err.name === "AbortError" ? "Timeout" : "NetworkError",
      message:
        err.name === "AbortError"
          ? "Request timed out"
          : err.message || "Network error occurred",
    };
  }
}

/** * Get CSRF token from meta tag
 */
export function getCsrfToken() {
  const $meta = $('meta[name="csrf-token"]');
  return $meta.length ? $meta.attr("content") : null;
}
