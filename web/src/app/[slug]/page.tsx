import type { Metadata } from 'next';
import { notFound } from 'next/navigation';
import { PageHero } from '@/components/ui';
import { getPage, pages } from '@/lib/content';

export const dynamicParams = false;

export function generateStaticParams() {
  return pages.map((p) => ({ slug: p.slug }));
}

export async function generateMetadata({ params }: { params: Promise<{ slug: string }> }): Promise<Metadata> {
  const p = getPage((await params).slug);
  if (!p) return {};
  return { title: p.title, alternates: { canonical: `/${p.slug}` } };
}

/** Trang đơn giản: Giới thiệu, các trang chính sách. */
export default async function StaticPage({ params }: { params: Promise<{ slug: string }> }) {
  const p = getPage((await params).slug);
  if (!p) notFound();

  const faqSchema = p.faq && {
    '@context': 'https://schema.org',
    '@type': 'FAQPage',
    mainEntity: p.faq.map((f) => ({ '@type': 'Question', name: f.q, acceptedAnswer: { '@type': 'Answer', text: f.a } })),
  };

  return (
    <>
      <PageHero title={p.title} subtitle={p.subtitle} crumbs={[{ label: p.title }]} />
      <div className="section">
        <div className="container container--narrow">
          <div className="entry-content" dangerouslySetInnerHTML={{ __html: p.content }} />
          {p.faq && (
            <div className="xanh-faq" style={{ marginTop: 32 }}>
              {p.faq.map((f) => (
                <details className="xanh-faq__item" key={f.q}>
                  <summary>{f.q}</summary>
                  <div className="xanh-faq__answer"><p>{f.a}</p></div>
                </details>
              ))}
            </div>
          )}
        </div>
      </div>
      {faqSchema && <script type="application/ld+json" dangerouslySetInnerHTML={{ __html: JSON.stringify(faqSchema) }} />}
    </>
  );
}
