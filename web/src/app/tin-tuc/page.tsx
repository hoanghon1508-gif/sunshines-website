import type { Metadata } from 'next';
import { PageHero, Pagination, PostCard } from '@/components/ui';
import { POSTS_PER_PAGE, sortedPosts } from '@/lib/content';

export const metadata: Metadata = {
  title: 'Tin tức',
  description: 'Tin tức và kiến thức hữu ích từ Sun Shines.',
  alternates: { canonical: '/tin-tuc' },
};

export default async function NewsPage({ searchParams }: { searchParams: Promise<{ paged?: string }> }) {
  const all = sortedPosts();
  const totalPages = Math.max(1, Math.ceil(all.length / POSTS_PER_PAGE));
  const page = Math.min(totalPages, Math.max(1, parseInt((await searchParams).paged ?? '1', 10) || 1));
  const list = all.slice((page - 1) * POSTS_PER_PAGE, page * POSTS_PER_PAGE);

  return (
    <>
      <PageHero title="Tin tức" subtitle="Cập nhật thông tin hữu ích mỗi tuần" crumbs={[{ label: 'Tin tức' }]} />
      <div className="section">
        <div className="container">
          <div className="post-grid">
            {list.map((p) => <PostCard key={p.slug} post={p} />)}
          </div>
          <Pagination page={page} total={totalPages} hrefFor={(n) => (n > 1 ? `/tin-tuc?paged=${n}` : '/tin-tuc')} />
        </div>
      </div>
    </>
  );
}
