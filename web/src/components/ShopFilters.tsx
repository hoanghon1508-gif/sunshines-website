'use client';

import Link from 'next/link';
import { useState } from 'react';
import { categories, priceRanges } from '@/lib/products';
import { shopQuery as qs, type ShopParams } from '@/lib/shop';

/** Thanh lọc sản phẩm: danh mục, khoảng giá, đang giảm giá. */
export function ShopFilters({ params }: { params: ShopParams }) {
  const [open, setOpen] = useState(false);
  const base = params.category ? `/danh-muc/${params.category}` : '/san-pham';

  return (
    <aside className={`shop-filters${open ? ' is-open' : ''}`} aria-label="Bộ lọc sản phẩm">
      <button className="shop-filters__toggle btn btn--outline" type="button" aria-expanded={open} aria-controls="shop-filters-body" onClick={() => setOpen((o) => !o)}>
        Bộ lọc
      </button>
      <div className="shop-filters__body" id="shop-filters-body">
        {categories.length >= 2 && (
          <div className="filter-group">
            <h2 className="filter-group__title">Danh mục</h2>
            <ul>
              <li>
                <Link className={`filter-link${params.category ? '' : ' is-active'}`} href="/san-pham">Tất cả sản phẩm</Link>
              </li>
              {categories.map((c) => (
                <li key={c.slug}>
                  <Link className={`filter-link${params.category === c.slug ? ' is-active' : ''}`} href={`/danh-muc/${c.slug}`}>{c.name}</Link>
                </li>
              ))}
            </ul>
          </div>
        )}
        <div className="filter-group">
          <h2 className="filter-group__title">Khoảng giá</h2>
          <ul>
            {priceRanges.map((r) => {
              const active = params.min === r.min && params.max === r.max;
              const href = base + qs(params, active ? { min: undefined, max: undefined } : { min: r.min, max: r.max });
              return (
                <li key={r.label}>
                  <Link className={`filter-link filter-link--check${active ? ' is-active' : ''}`} href={href} rel="nofollow">{r.label}</Link>
                </li>
              );
            })}
          </ul>
        </div>
        <div className="filter-group">
          <h2 className="filter-group__title">Khuyến mãi</h2>
          <Link className={`filter-link filter-link--check${params.onSale ? ' is-active' : ''}`} href={base + qs(params, { onSale: !params.onSale })} rel="nofollow">
            Đang giảm giá
          </Link>
        </div>
      </div>
    </aside>
  );
}
