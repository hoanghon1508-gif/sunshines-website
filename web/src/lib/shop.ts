/** Tham số bộ lọc cửa hàng và hàm dựng query string (dùng được ở cả server và client). */
export interface ShopParams {
  category?: string;
  min?: number;
  max?: number;
  onSale?: boolean;
  sort?: string;
}

export function shopQuery(p: ShopParams, over: Partial<ShopParams> = {}) {
  const m = { ...p, ...over };
  const s = new URLSearchParams();
  if (m.min != null) s.set('min_price', String(m.min));
  if (m.max != null) s.set('max_price', String(m.max));
  if (m.onSale) s.set('on_sale', '1');
  if (m.sort && m.sort !== 'default') s.set('orderby', m.sort);
  const t = s.toString();
  return t ? `?${t}` : '';
}
