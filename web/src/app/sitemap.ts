import type { MetadataRoute } from 'next';
import { categories, products } from '@/lib/products';
import { pages, posts } from '@/lib/content';
import { site } from '@/lib/site';

export default function sitemap(): MetadataRoute.Sitemap {
  const u = (p: string) => site.url + p;
  return [
    { url: u('/') },
    { url: u('/san-pham') },
    { url: u('/tin-tuc') },
    { url: u('/lien-he') },
    ...categories.map((c) => ({ url: u(`/danh-muc/${c.slug}`) })),
    ...products.map((p) => ({ url: u(`/san-pham-chi-tiet/${p.slug}`), lastModified: p.createdAt })),
    ...posts.map((p) => ({ url: u(`/tin-tuc/${p.slug}`), lastModified: p.date })),
    ...pages.map((p) => ({ url: u(`/${p.slug}`) })),
  ];
}
