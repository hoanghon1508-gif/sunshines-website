import type { Metadata } from 'next';
import { CartView } from '@/components/CartView';
import { PageHero } from '@/components/ui';

export const metadata: Metadata = { title: 'Giỏ hàng', robots: { index: false } };

export default function CartPage() {
  return (
    <>
      <PageHero title="Giỏ hàng" crumbs={[{ label: 'Giỏ hàng' }]} />
      <div className="section section--tight">
        <div className="container"><CartView /></div>
      </div>
    </>
  );
}
