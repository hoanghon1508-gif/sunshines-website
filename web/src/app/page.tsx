import Link from 'next/link';
import Image from 'next/image';
import { Icon } from '@/components/Icon';
import { HeroSlider } from '@/components/HeroSlider';
import { LeadForm } from '@/components/LeadForm';
import { Disclaimer, Price, PostCard, ProductCard } from '@/components/ui';
import { categories, discountPercent, featuredProducts, products } from '@/lib/products';
import { sortedPosts } from '@/lib/content';
import { heroSlides, site } from '@/lib/site';

const usp = [
  { icon: 'truck', title: 'Giao hàng toàn quốc', text: 'Nhanh chóng 2–4 ngày' },
  { icon: 'refresh', title: 'Đổi trả 7 ngày', text: 'Miễn phí nếu lỗi sản phẩm' },
  { icon: 'shield', title: 'Hàng chính hãng', text: 'Cam kết chất lượng' },
  { icon: 'headset', title: 'Tư vấn miễn phí', text: `Hotline ${site.hotline}` },
];

export default function HomePage() {
  const featured = featuredProducts(8);
  const news = sortedPosts().slice(0, 3);

  return (
    <>
      <section className="hero" aria-roledescription="carousel" aria-label="Banner nổi bật">
        <div className="container">
          <HeroSlider slides={heroSlides} />
        </div>
      </section>

      <section className="usp" aria-label="Cam kết">
        <div className="container usp__grid">
          {usp.map((u) => (
            <div className="usp__item" key={u.title}>
              <span className="usp__icon"><Icon name={u.icon} /></span>
              <div>
                <p className="usp__title">{u.title}</p>
                <p className="usp__text">{u.text}</p>
              </div>
            </div>
          ))}
        </div>
      </section>

      {/* Chỉ hiện danh mục khi có từ 2 danh mục trở lên. */}
      {categories.length >= 2 && (
        <section className="section">
          <div className="container">
            <div className="section__head">
              <div>
                <h2 className="section__title">Danh mục sản phẩm</h2>
                <p className="section__sub">Chọn nhanh theo nhu cầu của bạn</p>
              </div>
            </div>
            <div className="cat-grid">
              {categories.slice(0, 4).map((c) => (
                <Link className="cat-card" href={`/danh-muc/${c.slug}`} key={c.slug}>
                  <span className="cat-card__media">
                    {c.image ? <Image src={c.image} alt={c.name} width={400} height={300} /> : <span className="img-placeholder"><Icon name="image" size={40} /></span>}
                  </span>
                  <span className="cat-card__name">{c.name}</span>
                  <span className="cat-card__count">{products.filter((p) => p.categorySlug === c.slug).length} sản phẩm</span>
                </Link>
              ))}
            </div>
          </div>
        </section>
      )}

      {featured.length === 1 ? <Spotlight product={featured[0]} /> : featured.length > 1 && (
        <section className="section section--white">
          <div className="container">
            <div className="section__head">
              <div>
                <h2 className="section__title">Sản phẩm nổi bật</h2>
                <p className="section__sub">Những sản phẩm được khách hàng yêu thích nhất</p>
              </div>
              <Link className="section__link" href="/san-pham">Xem tất cả <Icon name="arrow" size={16} /></Link>
            </div>
            <ul className="products columns-4">
              {featured.map((p) => <ProductCard key={p.id} product={p} />)}
            </ul>
          </div>
        </section>
      )}

      <section className="section section--tight" id="dang-ky">
        <div className="container">
          <div className="promo">
            <div className="promo__text">
              <h2 className="promo__title">Đăng ký nhận tư vấn miễn phí</h2>
              <p>Để lại số điện thoại, đội ngũ của chúng tôi sẽ liên hệ trong 24 giờ.</p>
            </div>
            <div style={{ flex: '0 1 460px' }}>
              <LeadForm source="promo" showName={false} button="Gửi đăng ký" layout="inline" className="xanh-form--promo" />
            </div>
          </div>
        </div>
      </section>

      {news.length > 0 && (
        <section className="section">
          <div className="container">
            <div className="section__head">
              <div>
                <h2 className="section__title">Tin tức &amp; Kiến thức</h2>
                <p className="section__sub">Cập nhật thông tin hữu ích mỗi tuần</p>
              </div>
              <Link className="section__link" href="/tin-tuc">Xem tất cả <Icon name="arrow" size={16} /></Link>
            </div>
            <div className="post-grid">
              {news.map((p) => <PostCard key={p.slug} post={p} heading="h3" />)}
            </div>
          </div>
        </section>
      )}
    </>
  );
}

/** Khối giới thiệu 1 sản phẩm nổi bật (khi cửa hàng chỉ có 1 sản phẩm). */
function Spotlight({ product }: { product: (typeof products)[number] }) {
  const pct = discountPercent(product);
  const href = `/san-pham-chi-tiet/${product.slug}`;
  return (
    <section className="section section--white spotlight" aria-labelledby="spotlight-title">
      <div className="container spotlight__grid">
        <Link className="spotlight__media" href={href} tabIndex={-1} aria-hidden="true">
          <Image src={product.image} alt="" width={560} height={560} sizes="(max-width: 960px) 100vw, 560px" />
          {pct > 0 && <span className="badge badge--sale spotlight__badge">-{pct}%</span>}
        </Link>
        <div className="spotlight__info">
          <p className="spotlight__eyebrow">Sản phẩm của Sun Shines</p>
          <h2 className="spotlight__title" id="spotlight-title"><Link href={href}>{product.name}</Link></h2>
          <div className="spotlight__price"><Price product={product} /></div>
          <div className="spotlight__desc" dangerouslySetInnerHTML={{ __html: product.shortDescription }} />
          {product.highlights.length > 0 && (
            <ul className="spotlight__features">
              {product.highlights.map((f) => (
                <li key={f}><Icon name="shield" size={18} /><span>{f}</span></li>
              ))}
            </ul>
          )}
          <Disclaimer className="disclaimer--spotlight" />
          <div className="spotlight__actions">
            <Link className="btn btn--primary btn--lg" href={`/thanh-toan?mua=${product.slug}`} rel="nofollow">Mua ngay</Link>
            <Link className="btn btn--outline btn--lg" href={href}>Xem chi tiết</Link>
          </div>
        </div>
      </div>
    </section>
  );
}
