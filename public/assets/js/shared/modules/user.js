// ----------------------------------------------------
// Import Request Builder
// ----------------------------------------------------
import { makeRequest } from "../core/index.js";
// ----------------------------------------------------
// Import UI Alerts
// ----------------------------------------------------
import { displayMessage } from "../ui/index.js";

// ---------------------------------------------------------------
// Redirect
// ---------------------------------------------------------------
export function redirect(path) {
  window.location.href = path;
}

// ---------------------------------------------------------------
// Logout
// ---------------------------------------------------------------
export async function logout() {
  try {
    // Send payment request to backend
    const result = await makeRequest(`/user/logout`, "POST", {});

    if (result.status === 200 && result.message === "Logout successful") {
      //Redirect to dashboard
      redirect(result.data.dashboard);
    } else {
      displayMessage(`${result.message}`, "info");
    }
  } catch (err) {
    displayMessage("Network error occured", "warning");
  }
}
