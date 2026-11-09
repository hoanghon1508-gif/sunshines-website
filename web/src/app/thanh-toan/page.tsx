import type { Metadata } from 'next';
import { Suspense } from 'react';
import { CheckoutView } from '@/components/CheckoutView';
import { PageHero } from '@/components/ui';

export const metadata: Metadata = { title: 'Thanh toán', robots: { index: false } };

export default function CheckoutPage() {
  return (
    <>
      <PageHero title="Thanh toán" crumbs={[{ label: 'Giỏ hàng', href: '/gio-hang' }, { label: 'Thanh toán' }]} />
      <div className="section section--tight">
        <div className="container">
          <Suspense fallback={<div className="cart-box">Đang tải…</div>}>
            <CheckoutView />
          </Suspense>
        </div>
      </div>
    </>
  );
}
