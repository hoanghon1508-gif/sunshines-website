import type { Metadata } from 'next';
import Link from 'next/link';
import { PageHero, PostCard, ProductCard } from '@/components/ui';
import { posts } from '@/lib/content';
import { queryProducts } from '@/lib/products';

export const metadata: Metadata = { title: 'Tìm kiếm', robots: { index: false } };

export default async function SearchPage({ searchParams }: { searchParams: Promise<{ s?: string }> }) {
  const q = ((await searchParams).s ?? '').trim().slice(0, 100);
  const found = q ? queryProducts({ q }) : [];
  const needle = q.toLowerCase();
  const foundPosts = q ? posts.filter((p) => p.title.toLowerCase().includes(needle) || p.excerpt.toLowerCase().includes(needle)) : [];

  return (
    <>
      <PageHero title={q ? `Kết quả cho “${q}”` : 'Tìm kiếm'} crumbs={[{ label: 'Tìm kiếm' }]} />
      <div className="section">
        <div className="container">
          {!q && <p>Nhập từ khoá vào ô tìm kiếm ở đầu trang.</p>}
          {q && found.length === 0 && foundPosts.length === 0 && (
            <div className="empty-state">
              <p>Không tìm thấy kết quả phù hợp.</p>
              <Link className="btn btn--primary" href="/san-pham">Xem tất cả sản phẩm</Link>
            </div>
          )}
          {found.length > 0 && (
            <>
              <h2 className="section__title">Sản phẩm</h2>
              <ul className="products columns-4">{found.map((p) => <ProductCard key={p.id} product={p} />)}</ul>
            </>
          )}
          {foundPosts.length > 0 && (
            <>
              <h2 className="section__title" style={{ marginTop: 40 }}>Bài viết</h2>
              <div className="post-grid">{foundPosts.map((p) => <PostCard key={p.slug} post={p} heading="h3" />)}</div>
            </>
          )}
        </div>
      </div>
    </>
  );
}
