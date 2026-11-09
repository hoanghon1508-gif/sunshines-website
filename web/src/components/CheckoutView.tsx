'use client';

import Link from 'next/link';
import { useRouter, useSearchParams } from 'next/navigation';
import { useEffect, useRef, useState } from 'react';
import { useCart } from './CartProvider';
import { currentPrice, formatPrice, getProduct } from '@/lib/products';
import { isValidEmail, isValidPhone, normalizePhone } from '@/lib/validation';

type Errors = Partial<Record<'name' | 'phone' | 'email' | 'address' | 'city', string>>;

const ERRORS: Record<string, string> = {
  limit: 'Bạn gửi quá nhiều lần. Vui lòng thử lại sau ít phút.',
  empty: 'Giỏ hàng trống hoặc sản phẩm không còn bán.',
  invalid: 'Thông tin chưa hợp lệ. Vui lòng kiểm tra lại.',
  error: 'Chưa đặt được hàng. Vui lòng thử lại hoặc gọi hotline.',
};

export function CheckoutView() {
  const { lines, subtotal, ready, add, clear } = useCart();
  const router = useRouter();
  const search = useSearchParams();
  const handledBuy = useRef(false);
  const [errors, setErrors] = useState<Errors>({});
  const [formError, setFormError] = useState('');
  const [sending, setSending] = useState(false);
  const [payment, setPayment] = useState<'cod' | 'bacs'>('cod');

  // "Mua ngay": bảo đảm có 1 sản phẩm trong giỏ (không cộng dồn khi tải lại trang), rồi bỏ tham số khỏi URL.
  useEffect(() => {
    const slug = search.get('mua');
    if (!ready || !slug || handledBuy.current) return;
    handledBuy.current = true;
    if (getProduct(slug) && !lines.some((l) => l.product.slug === slug)) add(slug, 1);
    router.replace('/thanh-toan');
  }, [ready, search, lines, add, router]);

  if (!ready) return <div className="cart-box" aria-busy="true">Đang tải…</div>;

  if (lines.length === 0 && !search.get('mua')) {
    return (
      <div className="empty-state">
        <p className="woocommerce-info">Giỏ hàng trống, chưa thể thanh toán.</p>
        <Link className="btn btn--primary" href="/san-pham">Tiếp tục mua sắm</Link>
      </div>
    );
  }

  async function onSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    const d = new FormData(e.currentTarget);
    const v = (k: string) => String(d.get(k) ?? '').trim();
    const phone = normalizePhone(v('phone'));
    const errs: Errors = {};
    if (!v('name')) errs.name = 'Vui lòng nhập họ tên.';
    if (!isValidPhone(phone)) errs.phone = 'Số điện thoại chưa đúng.';
    if (!isValidEmail(v('email'))) errs.email = 'Email chưa đúng.';
    if (!v('address')) errs.address = 'Vui lòng nhập địa chỉ nhận hàng.';
    if (!v('city')) errs.city = 'Vui lòng nhập tỉnh/thành phố.';
    setErrors(errs);
    setFormError('');
    if (Object.keys(errs).length) return;

    setSending(true);
    try {
      const res = await fetch('/api/order', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          customer: { name: v('name'), phone, email: v('email'), address: v('address'), city: v('city'), note: v('note') },
          payment,
          website: v('website'),
          items: lines.map((l) => ({ slug: l.product.slug, qty: l.qty })),
        }),
      });
      const json = await res.json().catch(() => ({}));
      if (res.ok && json.ok) {
        clear();
        router.push(`/dat-hang-thanh-cong?ma=${encodeURIComponent(json.code)}&pt=${payment}`);
        return;
      }
      setFormError(ERRORS[json.error] ?? ERRORS.error);
    } catch {
      setFormError(ERRORS.error);
    }
    setSending(false);
  }

  const field = (id: keyof Errors, label: string, props: React.InputHTMLAttributes<HTMLInputElement>, required = true) => (
    <div className="xanh-field">
      <label htmlFor={`co-${id}`}>{label}{required ? ' *' : ''}</label>
      <input id={`co-${id}`} name={id} aria-invalid={!!errors[id]} aria-describedby={errors[id] ? `co-${id}-err` : undefined} {...props} />
      {errors[id] && <span className="field-error" id={`co-${id}-err`}>{errors[id]}</span>}
    </div>
  );

  return (
    <form className="cart-layout" onSubmit={onSubmit} noValidate>
      <div className="cart-box checkout-form">
        <div>
          <h2>Thông tin nhận hàng</h2>
        </div>
        <div className="xanh-form__hp" aria-hidden="true">
          <label htmlFor="co-web">Website</label>
          <input id="co-web" type="text" name="website" tabIndex={-1} autoComplete="off" />
        </div>
        <div className="xanh-form__fields">
          {field('name', 'Họ và tên', { type: 'text', autoComplete: 'name', maxLength: 120 })}
          {field('phone', 'Số điện thoại', { type: 'tel', autoComplete: 'tel', inputMode: 'tel' })}
          {field('email', 'Email', { type: 'email', autoComplete: 'email' }, false)}
          {field('city', 'Tỉnh / Thành phố', { type: 'text', autoComplete: 'address-level1' })}
          <div className="xanh-field--full">{field('address', 'Địa chỉ nhận hàng', { type: 'text', autoComplete: 'street-address', placeholder: 'Số nhà, đường, phường/xã, quận/huyện' })}</div>
          <div className="xanh-field xanh-field--full">
            <label htmlFor="co-note">Ghi chú đơn hàng</label>
            <textarea id="co-note" name="note" rows={3} maxLength={1000} placeholder="Ví dụ: giao giờ hành chính" />
          </div>
        </div>

        <div>
          <h2>Phương thức thanh toán</h2>
          <div className="pay-list">
            <label className="pay-option">
              <input type="radio" name="payment" checked={payment === 'cod'} onChange={() => setPayment('cod')} />
              <div><strong>Thanh toán khi nhận hàng (COD)</strong><span>Thanh toán bằng tiền mặt khi nhận hàng.</span></div>
            </label>
            <label className="pay-option">
              <input type="radio" name="payment" checked={payment === 'bacs'} onChange={() => setPayment('bacs')} />
              <div><strong>Chuyển khoản ngân hàng</strong><span>Nhân viên sẽ gửi thông tin tài khoản khi xác nhận đơn.</span></div>
            </label>
          </div>
        </div>
      </div>

      <aside className="cart-box summary" aria-label="Đơn hàng của bạn">
        <h2 className="summary__title">Đơn hàng của bạn</h2>
        <ul className="mini-items">
          {lines.map(({ product, qty }) => (
            <li key={product.slug}>
              <span>{product.name} × {qty}</span>
              <span>{formatPrice(currentPrice(product) * qty)}</span>
            </li>
          ))}
        </ul>
        <div className="summary__row"><span>Tạm tính</span><span>{formatPrice(subtotal)}</span></div>
        <div className="summary__row summary__row--total"><span>Tổng cộng</span><span>{formatPrice(subtotal)}</span></div>
        <p className="summary__note">Phí giao hàng (nếu có) sẽ được nhân viên báo khi xác nhận đơn.</p>
        {formError && <div className="woocommerce-error" role="alert">{formError}</div>}
        <button type="submit" className="btn btn--primary btn--lg" disabled={sending}>
          {sending ? 'Đang gửi…' : 'Đặt hàng'}
        </button>
        <p className="summary__note"><Link href="/gio-hang">← Quay lại giỏ hàng</Link></p>
      </aside>
    </form>
  );
}
