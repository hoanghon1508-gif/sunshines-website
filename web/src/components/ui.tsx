import Link from 'next/link';
import Image from 'next/image';
import { Icon } from './Icon';
import { site, telHref } from '@/lib/site';
import { discountPercent, formatPrice, currentPrice, type Product } from '@/lib/products';
import { formatDate, type Post } from '@/lib/content';
import { AddToCartButton } from './AddToCart';

export function Logo() {
  return (
    <Link className="logo" href="/" rel="home">
      <Image className="logo__mark" src="/images/logo-mark.webp" width={54} height={48} alt="" priority />
      <span className="logo__text">{site.name}</span>
    </Link>
  );
}

export function Disclaimer({ className = '' }: { className?: string }) {
  return (
    <p className={`disclaimer ${className}`}>
      <Icon name="shield" size={16} />
      <span>{site.disclaimer}</span>
    </p>
  );
}

export interface Crumb {
  label: string;
  href?: string;
}

export function Breadcrumbs({ items }: { items: Crumb[] }) {
  const all: Crumb[] = [{ label: 'Trang chủ', href: '/' }, ...items];
  const schema = {
    '@context': 'https://schema.org',
    '@type': 'BreadcrumbList',
    itemListElement: all.map((c, i) => ({
      '@type': 'ListItem',
      position: i + 1,
      name: c.label,
      ...(c.href ? { item: site.url + c.href } : {}),
    })),
  };
  return (
    <>
      <nav className="breadcrumbs" aria-label="Breadcrumb">
        {all.map((c, i) => (
          <span key={i} style={{ display: 'contents' }}>
            {i > 0 && <span className="breadcrumbs__sep" aria-hidden="true">/</span>}
            {c.href ? <Link href={c.href}>{c.label}</Link> : <span aria-current="page">{c.label}</span>}
          </span>
        ))}
      </nav>
      <script type="application/ld+json" dangerouslySetInnerHTML={{ __html: JSON.stringify(schema) }} />
    </>
  );
}

export function PageHero({ title, subtitle, crumbs }: { title: string; subtitle?: string; crumbs: Crumb[] }) {
  return (
    <header className="page-hero">
      <div className="container">
        <Breadcrumbs items={crumbs} />
        <h1 className="page-hero__title">{title}</h1>
        {subtitle && <p className="page-hero__sub">{subtitle}</p>}
      </div>
    </header>
  );
}

export function Price({ product }: { product: Product }) {
  const sale = discountPercent(product) > 0;
  return (
    <>
      <span className="woocommerce-Price-amount amount">{formatPrice(currentPrice(product))}</span>
      {sale && <del>{formatPrice(product.price)}</del>}
    </>
  );
}

export function ProductCard({ product }: { product: Product }) {
  const pct = discountPercent(product);
  const href = `/san-pham-chi-tiet/${product.slug}`;
  return (
    <li className="product-card">
      <Link className="product-card__media" href={href} tabIndex={-1} aria-hidden="true">
        <Image src={product.image} alt="" fill sizes="(max-width: 600px) 50vw, 300px" style={{ objectFit: 'cover' }} />
        {!product.inStock && <span className="badge badge--muted product-card__stock">Hết hàng</span>}
      </Link>
      <div className="product-card__body">
        <h3 className="product-card__title">
          <Link href={href}>{product.name}</Link>
        </h3>
        <div className="product-card__price">
          <Price product={product} />
          {pct > 0 && <span className="badge badge--sale">-{pct}%</span>}
        </div>
        <div className="product-card__action">
          <AddToCartButton slug={product.slug} inStock={product.inStock} />
        </div>
      </div>
    </li>
  );
}

export function PostCard({ post, heading = 'h2' }: { post: Post; heading?: 'h2' | 'h3' }) {
  const H = heading;
  const href = `/tin-tuc/${post.slug}`;
  return (
    <article className="post-card">
      <Link className="post-card__media" href={href} tabIndex={-1} aria-hidden="true">
        {post.image ? (
          <Image src={post.image} alt="" width={768} height={440} sizes="(max-width: 768px) 100vw, 400px" />
        ) : (
          <span className="img-placeholder"><Icon name="image" size={40} /></span>
        )}
      </Link>
      <div className="post-card__body">
        <time className="post-card__date" dateTime={post.date}>{formatDate(post.date)}</time>
        <H className="post-card__title"><Link href={href}>{post.title}</Link></H>
        <p className="post-card__excerpt">{post.excerpt}</p>
      </div>
    </article>
  );
}

export function Pagination({ page, total, hrefFor }: { page: number; total: number; hrefFor: (p: number) => string }) {
  if (total <= 1) return null;
  const nums = Array.from({ length: total }, (_, i) => i + 1);
  return (
    <nav className="pagination" aria-label="Phân trang">
      <ul>
        {page > 1 && (
          <li>
            <Link className="page-numbers" href={hrefFor(page - 1)}>
              <Icon name="chevron-l" size={18} /><span className="screen-reader-text">Trang trước</span>
            </Link>
          </li>
        )}
        {nums.map((n) => (
          <li key={n}>
            {n === page ? (
              <span className="page-numbers current" aria-current="page">{n}</span>
            ) : (
              <Link className="page-numbers" href={hrefFor(n)}>{n}</Link>
            )}
          </li>
        ))}
        {page < total && (
          <li>
            <Link className="page-numbers" href={hrefFor(page + 1)}>
              <Icon name="chevron-r" size={18} /><span className="screen-reader-text">Trang sau</span>
            </Link>
          </li>
        )}
      </ul>
    </nav>
  );
}

export function QuickContact() {
  const hotline = telHref(site.hotline).slice(4);
  const zalo = site.zalo.replace(/\D/g, '');
  return (
    <div className="quick-contact" aria-label="Liên hệ nhanh">
      {site.messenger && (
        <a className="quick-contact__btn quick-contact__btn--messenger" href={site.messenger} target="_blank" rel="noopener" aria-label="Messenger">
          <Icon name="messenger" />
          <span className="quick-contact__label">Messenger</span>
        </a>
      )}
      {zalo && (
        <a className="quick-contact__btn quick-contact__btn--zalo" href={`https://zalo.me/${zalo}`} target="_blank" rel="noopener" aria-label="Nhắn tin Zalo">
          <span className="quick-contact__zalo">Zalo</span>
          <span className="quick-contact__label">Chat Zalo</span>
        </a>
      )}
      {hotline && (
        <a className="quick-contact__btn quick-contact__btn--phone" href={`tel:${hotline}`} aria-label="Gọi điện">
          <Icon name="phone" />
          <span className="quick-contact__label">{site.hotline}</span>
        </a>
      )}
    </div>
  );
}
