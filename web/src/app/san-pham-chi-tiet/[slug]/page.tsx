import type { Metadata } from 'next';
import { notFound } from 'next/navigation';
import { ProductBuyBox } from '@/components/AddToCart';
import { LeadForm } from '@/components/LeadForm';
import { ProductGallery } from '@/components/ProductGallery';
import { Breadcrumbs, Disclaimer, Price, ProductCard } from '@/components/ui';
import { categoryName, currentPrice, discountPercent, getProduct, products } from '@/lib/products';
import { site } from '@/lib/site';

export function generateStaticParams() {
  return products.map((p) => ({ slug: p.slug }));
}

export async function generateMetadata({ params }: { params: Promise<{ slug: string }> }): Promise<Metadata> {
  const p = getProduct((await params).slug);
  if (!p) return {};
  const desc = p.shortDescription.replace(/<[^>]+>/g, ' ').replace(/\s+/g, ' ').trim();
  return {
    title: p.name,
    description: desc,
    alternates: { canonical: `/san-pham-chi-tiet/${p.slug}` },
    openGraph: { title: p.name, description: desc, images: [{ url: p.image }], type: 'website' },
  };
}

export default async function ProductPage({ params }: { params: Promise<{ slug: string }> }) {
  const p = getProduct((await params).slug);
  if (!p) notFound();
  const pct = discountPercent(p);
  const related = products.filter((x) => x.id !== p.id && x.categorySlug === p.categorySlug).slice(0, 4);

  const schema = {
    '@context': 'https://schema.org',
    '@type': 'Product',
    name: p.name,
    image: p.gallery.map((g) => site.url + g),
    description: p.shortDescription.replace(/<[^>]+>/g, ' ').replace(/\s+/g, ' ').trim(),
    brand: { '@type': 'Brand', name: site.name },
    offers: {
      '@type': 'Offer',
      priceCurrency: 'VND',
      price: currentPrice(p),
      availability: p.inStock ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
      url: `${site.url}/san-pham-chi-tiet/${p.slug}`,
    },
  };

  return (
    <div className="product-page">
      <div className="container">
        <Breadcrumbs
          items={[
            { label: 'Sản phẩm', href: '/san-pham' },
            { label: categoryName(p.categorySlug), href: `/danh-muc/${p.categorySlug}` },
            { label: p.name },
          ]}
        />
        <div className="product-detail">
          <ProductGallery images={p.gallery} alt={p.name} />
          <div className="summary-col">
            <h1 className="product_title">{p.name}</h1>
            <p className="price">
              <Price product={p} />
              {pct > 0 && <span className="badge badge--sale single-discount">Giảm {pct}%</span>}
            </p>
            <div className="short-description" dangerouslySetInnerHTML={{ __html: p.shortDescription }} />
            <Disclaimer />
            <ProductBuyBox slug={p.slug} inStock={p.inStock} />
            <div className="product-meta">
              <span>Danh mục: {categoryName(p.categorySlug)}</span>
            </div>
            <div className="consult-box" id="tu-van">
              <h2 className="consult-box__title">Đăng ký nhận tư vấn về sản phẩm</h2>
              <LeadForm source="consult" product={p.slug} button="Gửi" layout="inline" />
            </div>
          </div>

          <div className="product-detail__tabs">
            <h2 className="tab-title">Mô tả</h2>
            <div className="panel" dangerouslySetInnerHTML={{ __html: p.description }} />
          </div>

          {related.length > 0 && (
            <section className="product-detail__related">
              <h2>Sản phẩm liên quan</h2>
              <ul className="products columns-4">
                {related.map((r) => <ProductCard key={r.id} product={r} />)}
              </ul>
            </section>
          )}
        </div>
      </div>
      <script type="application/ld+json" dangerouslySetInnerHTML={{ __html: JSON.stringify(schema) }} />
    </div>
  );
}
