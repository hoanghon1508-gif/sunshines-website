import type { MetadataRoute } from 'next';
import { site } from '@/lib/site';

export default function robots(): MetadataRoute.Robots {
  return {
    rules: { userAgent: '*', allow: '/', disallow: ['/api/', '/gio-hang', '/thanh-toan', '/dat-hang-thanh-cong', '/tim-kiem'] },
    sitemap: `${site.url}/sitemap.xml`,
  };
}
