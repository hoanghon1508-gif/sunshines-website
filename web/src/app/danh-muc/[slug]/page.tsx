import type { Metadata } from 'next';
import { notFound } from 'next/navigation';
import { ShopView } from '@/components/ShopView';
import { categories, getCategory } from '@/lib/products';

export function generateStaticParams() {
  return categories.map((c) => ({ slug: c.slug }));
}

export async function generateMetadata({ params }: { params: Promise<{ slug: string }> }): Promise<Metadata> {
  const cat = getCategory((await params).slug);
  if (!cat) return {};
  return { title: cat.name, description: cat.description, alternates: { canonical: `/danh-muc/${cat.slug}` } };
}

export default async function CategoryPage({
  params,
  searchParams,
}: {
  params: Promise<{ slug: string }>;
  searchParams: Promise<Record<string, string | string[] | undefined>>;
}) {
  const cat = getCategory((await params).slug);
  if (!cat) notFound();
  return (
    <ShopView
      title={cat.name}
      description={cat.description}
      category={cat.slug}
      searchParams={await searchParams}
      crumbs={[{ label: 'Sản phẩm', href: '/san-pham' }, { label: cat.name }]}
    />
  );
}
