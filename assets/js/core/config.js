// -------------------------------------------------
// Base Urls
// -------------------------------------------------
export const apiUrl =
  window.location.hostname === "localhost"
    ? "http://localhost/projects/showcase/shopcity/api"
    : "";

export const baseUrl =
  window.location.hostname === "localhost"
    ? "http://localhost/projects/showcase/shopcity"
    : "";

// -------------------------------------------------
// Auto Detect Status
// -------------------------------------------------
export const isLoggedIn = $(".top-bar").hasClass("logged-in");

// -------------------------------------------------
// All Supported Countries
// -------------------------------------------------
export const supportedCountries = [
  "Benin",
  "Botswana",
  "Burkina Faso",
  "Burundi",
  "Cameroon",
  "Chad",
  "China",
  "Congo",
  "Congo (DRC)",
  "Ethiopia",
  "Ghana",
  "Guinea",
  "Guinea-Bissau",
  "India",
  "Ivory Coast",
  "Kenya",
  "Liberia",
  "Madagascar",
  "Malawi",
  "Mali",
  "Mozambique",
  "Niger",
  "Nigeria",
  "Philippines",
  "Rwanda",
  "Senegal",
  "Seychelles",
  "Sierra Leone",
  "Somalia",
  "South Africa",
  "South Sudan",
  "Swaziland",
  "Tanzania",
  "Togo",
  "Turkey",
  "Uganda",
  "Zambia",
  "Zimbabwe",
];

export const francophoneCountries = [
  "Cameroon", // Central Africa
  "Central African Republic",
  "Chad",
  "Republic of the Congo",
  "Democratic Republic of the Congo",
  "Gabon",
  "Equatorial Guinea",
  "Benin", // West Africa
  "Burkina Faso",
  "Ivory Coast",
  "Guinea",
  "Mali",
  "Niger",
  "Senegal",
  "Togo",
];

export const eurozoneCountries = [
  "Austria",
  "Belgium",
  "Croatia",
  "Cyprus",
  "Estonia",
  "Finland",
  "France",
  "Germany",
  "Greece",
  "Ireland",
  "Italy",
  "Latvia",
  "Lithuania",
  "Luxembourg",
  "Malta",
  "Netherlands",
  "Portugal",
  "Slovakia",
  "Slovenia",
  "Spain",
  "Andorra",
  "Kosovo",
  "Monaco",
  "Montenegro",
  "San Marino",
  "Vatican City",
];

// -------------------------------
// Flat Rate
// -------------------------------
export const FLAT_RATE = 100;

// -------------------------------
// Shipping Rate
// -------------------------------
export const SHIPPING_RATE = 100;

// -------------------------------
// Tax Rate
// -------------------------------
export const TAX_RATE = 100;

// -------------------------------
// Tax Rate
// -------------------------------
export const BASE_TOPUP = 1000;

// -------------------------------
// Tax Rate
// -------------------------------
export const BASE_CONVERSION_RATE = 100;

// -------------------------------
// Base Currency
// -------------------------------
export const BASE_CURRENCY = "NGN";

// -------------------------------
// Flutterwave Public Key
// -------------------------------
export const FLW_PUBLIC_KEY = "";

// -------------------------------
// Maximum Login Trials
// -------------------------------
export const MAX_TRIALS = 5;

// -------------------------------
// Cloudinry Name
// -------------------------------
export const CLOUDINARY_NAME = "";

// ---------------------------------
// Upload Size Limit For Images
// -------------------------------
export const MAX_IMAGE_SIZE_MB = 5;

// -------------------------------
//  Upload Size Limit For Videos
// ------------------------------
export const MAX_VIDEO_SIZE_MB = 50;

// -----------------------------------
//  Upload Size Limit For Documents
// ---------------------------------
export const MAX_DOCUMENT_SIZE_MB = 5;

// ----------------------------------------------------------------
//  Supported Image Extensions
// ----------------------------------------------------------------
export const SUPPORTED_IMAGE_EXTENSTIONS = ["jpg", "jpeg", "png"];

// ----------------------------------------------------------------------
//   Supported Video Extensions
// ----------------------------------------------------------------------
export const SUPPORTED_VIDEO_EXTENSTIONS = ["mp4", "mov", "avi", "mkv"];

// ------------------------------------------------------------------------------------
//   Supported Document Extensions
// ------------------------------------------------------------------------------------
export const SUPPORTED_DOCUMENT_EXTENSTIONS = [
  "jpg",
  "jpeg",
  "png",
  "pdf",
  "docx",
];

// -----------------------------------------------------------------------------
//   Supported Image Mime Types
// -----------------------------------------------------------------------------
export const SUPPORTED_IMAGE_MIMES = ["image/jpeg", "image/png", "image/jpg"];

// -----------------------------------
//   Supported Video Mime Types
// ---------------------------------
export const SUPPORTED_VIDEO_MIMES = [
  "video/mp4",
  "video/quicktime", // mov
  "video/x-msvideo", // avi
  "video/x-matroska", // mkv
];

// -----------------------------------
//   Supported Document Mime Types
// ---------------------------------
export const SUPPORTED_DOCUMENT_MIMES = [
  "application/pdf",
  "application/vnd.openxmlformats-officedocument.wordprocessingml.document", // docx
];

// -------------------------------
// Firebase Configurations
// -------------------------------
export const firebaseConfig = {
  apiKey: "",
  authDomain: "",
  projectId: "",
  storageBucket: "",
  messagingSenderId: "",
  appId: "",
  measurementId: "",
  vapidKey:
    "",
};
