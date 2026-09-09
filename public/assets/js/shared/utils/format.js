// ----------------------------------------------------
//  Format Amounts Correctly
// ----------------------------------------------------
export function formatAmount(amount) {
  //return `₦ ${Math.round(amount).toLocaleString()}`;
  return `₦ ${amount.toLocaleString("en-NG", {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  })
}`;
}

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
