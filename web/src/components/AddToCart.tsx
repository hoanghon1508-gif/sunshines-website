'use client';

import Link from 'next/link';
import { useRouter } from 'next/navigation';
import { useState } from 'react';
import { useCart } from './CartProvider';

/** Nút "Thêm vào giỏ" trong danh sách sản phẩm. */
export function AddToCartButton({ slug, inStock }: { slug: string; inStock: boolean }) {
  const { add } = useCart();
  const [added, setAdded] = useState(false);

  if (!inStock) {
    return (
      <button type="button" className="button" disabled>
        Hết hàng
      </button>
    );
  }
  return (
    <>
      <button
        type="button"
        className="button"
        onClick={() => {
          add(slug, 1);
          setAdded(true);
        }}
      >
        Thêm vào giỏ
      </button>
      {added && (
        <Link className="added_to_cart" href="/gio-hang">
          Xem giỏ hàng
        </Link>
      )}
    </>
  );
}

/** Khối số lượng + nút Thêm vào giỏ / Mua ngay ở trang chi tiết. */
export function ProductBuyBox({ slug, inStock }: { slug: string; inStock: boolean }) {
  const { add } = useCart();
  const router = useRouter();
  const [qty, setQty] = useState(1);
  const [added, setAdded] = useState(false);

  if (!inStock) return <p className="stock out-of-stock">Hết hàng</p>;

  const clamp = (n: number) => Math.min(99, Math.max(1, Math.floor(n) || 1));

  return (
    <div className="buybox">
      <form
        className="cart"
        onSubmit={(e) => {
          e.preventDefault();
          add(slug, qty);
          setAdded(true);
        }}
      >
        <div className="quantity">
          <label className="screen-reader-text" htmlFor="qty">
            Số lượng
          </label>
          <input
            id="qty"
            className="qty"
            type="number"
            min={1}
            max={99}
            inputMode="numeric"
            value={qty}
            onChange={(e) => setQty(clamp(Number(e.target.value)))}
          />
        </div>
        <button type="submit" className="button alt">
          Thêm vào giỏ
        </button>
        <button
          type="button"
          className="button btn--outline"
          style={{ background: 'transparent', color: 'var(--c-primary-700)' }}
          onClick={() => {
            add(slug, qty);
            router.push('/thanh-toan');
          }}
        >
          Mua ngay
        </button>
      </form>
      {added && (
        <div className="woocommerce-message" role="status">
          Đã thêm sản phẩm vào giỏ hàng. <Link href="/gio-hang">Xem giỏ hàng</Link>
        </div>
      )}
    </div>
  );
}
