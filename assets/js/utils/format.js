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
