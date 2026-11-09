'use client';

import { useRouter } from 'next/navigation';

const OPTIONS = [
  { value: 'default', label: 'Thứ tự mặc định' },
  { value: 'date', label: 'Mới nhất' },
  { value: 'price', label: 'Giá: thấp đến cao' },
  { value: 'price-desc', label: 'Giá: cao đến thấp' },
];

/** Sắp xếp sản phẩm: giữ nguyên các bộ lọc khác trên URL. */
export function SortSelect({ value, base, query }: { value: string; base: string; query: string }) {
  const router = useRouter();
  return (
    <form className="woocommerce-ordering" onSubmit={(e) => e.preventDefault()}>
      <label className="screen-reader-text" htmlFor="orderby">Sắp xếp</label>
      <select
        id="orderby"
        className="orderby"
        value={value}
        onChange={(e) => {
          const p = new URLSearchParams(query);
          if (e.target.value === 'default') p.delete('orderby');
          else p.set('orderby', e.target.value);
          const s = p.toString();
          router.push(base + (s ? `?${s}` : ''));
        }}
      >
        {OPTIONS.map((o) => (
          <option key={o.value} value={o.value}>{o.label}</option>
        ))}
      </select>
    </form>
  );
}
