import type { Metadata } from 'next';
import Image from 'next/image';
import { notFound } from 'next/navigation';
import { Icon } from '@/components/Icon';
import { Breadcrumbs, PostCard } from '@/components/ui';
import { formatDate, getPost, posts, sortedPosts } from '@/lib/content';
import { site } from '@/lib/site';

export function generateStaticParams() {
  return posts.map((p) => ({ slug: p.slug }));
}

export async function generateMetadata({ params }: { params: Promise<{ slug: string }> }): Promise<Metadata> {
  const p = getPost((await params).slug);
  if (!p) return {};
  return {
    title: p.title,
    description: p.excerpt,
    alternates: { canonical: `/tin-tuc/${p.slug}` },
    openGraph: { type: 'article', title: p.title, description: p.excerpt, publishedTime: p.date },
  };
}

export default async function PostPage({ params }: { params: Promise<{ slug: string }> }) {
  const p = getPost((await params).slug);
  if (!p) notFound();
  const related = sortedPosts().filter((x) => x.slug !== p.slug && x.category === p.category).slice(0, 3);
  const url = `${site.url}/tin-tuc/${p.slug}`;
  const schema = {
    '@context': 'https://schema.org',
    '@type': 'Article',
    headline: p.title,
    datePublished: p.date,
    description: p.excerpt,
    mainEntityOfPage: url,
    author: { '@type': 'Organization', name: site.company },
    publisher: { '@type': 'Organization', name: site.company },
  };

  return (
    <article className="article">
      <header className="page-hero page-hero--article">
        <div className="container container--narrow">
          <Breadcrumbs items={[{ label: 'Tin tức', href: '/tin-tuc' }, { label: p.category }, { label: p.title }]} />
          <h1 className="page-hero__title">{p.title}</h1>
          <p className="article__meta">
            <time dateTime={p.date}>{formatDate(p.date)}</time> · {p.category}
          </p>
        </div>
      </header>

      <div className="section section--tight">
        <div className="container container--narrow">
          {p.image && (
            <figure className="article__cover">
              <Image src={p.image} alt={p.title} width={1200} height={700} priority />
            </figure>
          )}
          <div className="entry-content" dangerouslySetInnerHTML={{ __html: p.content }} />
          <div className="share">
            <span>Chia sẻ:</span>
            <a href={`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`} target="_blank" rel="noopener">
              <Icon name="facebook" size={18} /> Facebook
            </a>
          </div>
        </div>
      </div>

      {related.length > 0 && (
        <section className="section section--white">
          <div className="container">
            <h2 className="section__title">Bài viết liên quan</h2>
            <div className="post-grid">
              {related.map((r) => <PostCard key={r.slug} post={r} heading="h3" />)}
            </div>
          </div>
        </section>
      )}
      <script type="application/ld+json" dangerouslySetInnerHTML={{ __html: JSON.stringify(schema) }} />
    </article>
  );
}
