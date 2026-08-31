// Current date
const currentDate = new Date();

// Week days details
export const weekdays = [
  "Sunday",
  "Monday",
  "Tuesday",
  "Wednesday",
  "Thursday",
  "Friday",
  "Saturday",
];

// Current day details
export const currentDay = weekdays[currentDate.getDay()];

// Full week details
export const week = currentDate.toLocaleDateString("en-US", {
  month: "long",
  day: "numeric",
  year: "numeric",
});

// With custom settings, forcing a "US" locale to guarantee commas in output
export function formatCurrency(amount, decimalPrecision = 2) {
  return amount.toLocaleString(undefined, {
    minimumFractionDigits: decimalPrecision,
    maximumFractionDigits: decimalPrecision,
  });
}

// With custom settings, forcing a "US" locale to guarantee commas in output
export function formatNum(num) {
  return num.toLocaleString();
}

/**
 * Generate a human readable version of the given timestamp
 *
 * @param {string} time - Timestamp in "YYYY-MM-DD HH:mm:ss" format
 * @returns {string} - Formatted time (e.g. "2 hours ago")
 */
export function formatTimeAgo(time) {
  const timeAgo = new Date(time.replace(" ", "T")); // Convert to valid ISO format
  const curTime = new Date();
  const elapsedTime = Math.floor((curTime - timeAgo) / 1000); // in seconds

  const seconds = elapsedTime;
  const minutes = Math.round(elapsedTime / 60);
  const hours = Math.round(elapsedTime / 3600);
  const days = Math.round(elapsedTime / 86400);
  const weeks = Math.round(elapsedTime / 604800);
  const months = Math.round(elapsedTime / 2600640);
  const years = Math.round(elapsedTime / 31207680);

  if (seconds <= 60) {
    return "Just now";
  } else if (minutes <= 60) {
    return minutes === 1 ? "1 minute ago" : `${minutes} minutes ago`;
  } else if (hours <= 24) {
    return hours === 1 ? "1 hour ago" : `${hours} hours ago`;
  } else if (days <= 7) {
    return days === 1 ? "Yesterday" : `${days} days ago`;
  } else if (weeks <= 4.3) {
    return weeks === 1 ? "1 week ago" : `${weeks} weeks ago`;
  } else if (months <= 12) {
    return months === 1 ? "1 month ago" : `${months} months ago`;
  } else {
    return years === 1 ? "1 year ago" : `${years} years ago`;
  }
}
