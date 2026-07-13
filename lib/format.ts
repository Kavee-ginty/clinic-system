export function money(value: number | string | null | undefined) {
  return `Rs. ${Number(value ?? 0).toFixed(2)}`;
}

export function displayDate(value: string | null | undefined) {
  return (value ?? "").replaceAll("-", "/");
}

export function ageFromDob(dob: string) {
  const birth = new Date(dob);
  const today = new Date();
  let age = today.getFullYear() - birth.getFullYear();
  const monthDiff = today.getMonth() - birth.getMonth();
  if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) age -= 1;
  return Number.isFinite(age) ? age : 0;
}
