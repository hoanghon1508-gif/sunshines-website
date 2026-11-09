'use client';

import Image from 'next/image';
import Link from 'next/link';
import { useCart } from './CartProvider';
import { currentPrice, formatPrice } from '@/lib/products';

export function CartView() {
  const { lines, subtotal, ready, setQty, remove } = useCart();

  if (!ready) return <div className="cart-box" aria-busy="true">Đang tải giỏ hàng…</div>;

  if (lines.length === 0) {
    return (
      <div className="empty-state">
        <p className="woocommerce-info">Giỏ hàng của bạn đang trống.</p>
        <Link className="btn btn--primary" href="/san-pham">Tiếp tục mua sắm</Link>
      </div>
    );
  }

  return (
    <div className="cart-layout">
      <div className="cart-box">
        {lines.map(({ product, qty }) => (
          <div className="cart-line" key={product.slug}>
            <Link className="cart-line__img" href={`/san-pham-chi-tiet/${product.slug}`}>
              <Image src={product.image} alt={product.name} fill sizes="88px" />
            </Link>
            <div>
              <Link className="cart-line__name" href={`/san-pham-chi-tiet/${product.slug}`}>{product.name}</Link>
              <p className="cart-line__price">{formatPrice(currentPrice(product))}</p>
              <div className="qty-stepper">
                <button type="button" aria-label="Giảm số lượng" onClick={() => setQty(product.slug, qty - 1)}>−</button>
                <input
                  type="number"
                  min={1}
                  max={99}
                  value={qty}
                  aria-label={`Số lượng ${product.name}`}
                  onChange={(e) => setQty(product.slug, Number(e.target.value))}
                />
                <button type="button" aria-label="Tăng số lượng" onClick={() => setQty(product.slug, qty + 1)}>+</button>
              </div>
            </div>
            <div>
              <p className="cart-line__total">{formatPrice(currentPrice(product) * qty)}</p>
              <button type="button" className="cart-line__remove" onClick={() => remove(product.slug)}>Xoá</button>
            </div>
          </div>
        ))}
      </div>

      <aside className="cart-box summary" aria-label="Tổng giỏ hàng">
        <h2 className="summary__title">Tổng giỏ hàng</h2>
        <div className="summary__row"><span>Tạm tính</span><span>{formatPrice(subtotal)}</span></div>
        <div className="summary__row summary__row--total"><span>Tổng cộng</span><span>{formatPrice(subtotal)}</span></div>
        <p className="summary__note">Phí giao hàng (nếu có) sẽ được nhân viên báo khi xác nhận đơn.</p>
        <Link className="btn btn--primary btn--lg" href="/thanh-toan">Tiến hành thanh toán</Link>
      </aside>
    </div>
  );
}
