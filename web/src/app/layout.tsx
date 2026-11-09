import type { Metadata, Viewport } from 'next';
import { Be_Vietnam_Pro } from 'next/font/google';
import './globals.css';
import './extra.css';
import { CartProvider } from '@/components/CartProvider';
import { Header } from '@/components/Header';
import { Footer } from '@/components/Footer';
import { QuickContact } from '@/components/ui';
import { site } from '@/lib/site';

const font = Be_Vietnam_Pro({
  subsets: ['vietnamese', 'latin'],
  weight: ['400', '500', '600', '700', '800'],
  variable: '--font-be',
  display: 'swap',
});

export const viewport: Viewport = { themeColor: '#15803D', width: 'device-width', initialScale: 1 };

export const metadata: Metadata = {
  metadataBase: new URL(site.url),
  title: { default: `${site.name} – ${site.slogan}`, template: `%s – ${site.name}` },
  description: site.description,
  openGraph: {
    type: 'website',
    locale: 'vi_VN',
    siteName: site.name,
    title: `${site.name} – ${site.slogan}`,
    description: site.description,
    images: [{ url: '/images/thao-moc-mogan.jpg' }],
  },
  alternates: { canonical: '/' },
};

export default function RootLayout({ children }: { children: React.ReactNode }) {
  const org = {
    '@context': 'https://schema.org',
    '@type': 'Organization',
    name: site.company,
    alternateName: site.name,
    url: site.url,
    logo: site.url + '/images/logo-mark.png',
    telephone: site.hotline,
    address: { '@type': 'PostalAddress', streetAddress: site.address, addressCountry: 'VN' },
    sameAs: site.social.map((s) => s.url),
  };
  return (
    <html lang="vi" className={font.variable}>
      <body>
        <a className="skip-link screen-reader-text" href="#main">Chuyển đến nội dung</a>
        <CartProvider>
          <Header />
          <main id="main" className="site-main">{children}</main>
          <Footer />
          <QuickContact />
        </CartProvider>
        <script type="application/ld+json" dangerouslySetInnerHTML={{ __html: JSON.stringify(org) }} />
      </body>
    </html>
  );
}
