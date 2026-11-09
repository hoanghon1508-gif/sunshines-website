import type { Metadata } from 'next';
import { ShopView } from '@/components/ShopView';

export const metadata: Metadata = {
  title: 'Sản phẩm',
  description: 'Danh sách sản phẩm thảo mộc Mogan của Sun Shines.',
  alternates: { canonical: '/san-pham' },
};

export default async function ShopPage({ searchParams }: { searchParams: Promise<Record<string, string | string[] | undefined>> }) {
  return <ShopView title="Sản phẩm" searchParams={await searchParams} crumbs={[{ label: 'Sản phẩm' }]} />;
}
