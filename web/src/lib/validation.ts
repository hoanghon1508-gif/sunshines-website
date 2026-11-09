/** Chuẩn hoá và kiểm tra dữ liệu form (dùng chung cho trình duyệt và máy chủ). */

export function normalizePhone(input: string): string {
  let phone = String(input ?? '').replace(/[\s.\-()]/g, '');
  if (phone.startsWith('+84')) phone = '0' + phone.slice(3);
  else if (phone.startsWith('84') && phone.length === 11) phone = '0' + phone.slice(2);
  return phone;
}

/** SĐT Việt Nam: bắt đầu bằng 0, 10–11 số. */
export function isValidPhone(phone: string): boolean {
  return /^0[1-9][0-9]{8,9}$/.test(phone);
}

export function isValidEmail(email: string): boolean {
  return email === '' || /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

export function clean(value: unknown, max: number): string {
  return String(value ?? '')
    .replace(/[\u0000-\u0008\u000B\u000C\u000E-\u001F]/g, '')
    .trim()
    .slice(0, max);
}
