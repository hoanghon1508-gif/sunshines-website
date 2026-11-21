import type { Metadata } from 'next';
import { PageHero, Pagination, PostCard } from '@/components/ui';
import { BLOG_PER_PAGE, sortedBlogPosts } from '@/lib/blog';

export const metadata: Metadata = {
  title: 'Blog',
  description: 'Bài viết chia sẻ kiến thức sống khoẻ từ Sun Shines.',
  alternates: { canonical: '/blog' },
};

export default async function BlogPage({ searchParams }: { searchParams: Promise<{ paged?: string }> }) {
  const all = sortedBlogPosts();
  const totalPages = Math.max(1, Math.ceil(all.length / BLOG_PER_PAGE));
  const page = Math.min(totalPages, Math.max(1, parseInt((await searchParams).paged ?? '1', 10) || 1));
  const list = all.slice((page - 1) * BLOG_PER_PAGE, page * BLOG_PER_PAGE);

  return (
    <>
      <PageHero title="Blog" subtitle="Chia sẻ kiến thức sống khoẻ" crumbs={[{ label: 'Blog' }]} />
      <div className="section">
        <div className="container">
          {list.length === 0 ? (
            <p style={{ textAlign: 'center' }}>Chưa có bài viết nào.</p>
          ) : (
            <div className="post-grid">
              {list.map((p) => <PostCard key={p.slug} post={p} basePath="/blog" />)}
            </div>
          )}
          <Pagination page={page} total={totalPages} hrefFor={(n) => (n > 1 ? `/blog?paged=${n}` : '/blog')} />
        </div>
      </div>
    </>
  );
}
