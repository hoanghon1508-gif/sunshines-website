import type { Metadata } from 'next';
import { Icon } from '@/components/Icon';
import { LeadForm } from '@/components/LeadForm';
import { PageHero } from '@/components/ui';
import { site, telHref } from '@/lib/site';

export const metadata: Metadata = {
  title: 'Liên hệ',
  description: 'Liên hệ Sun Shines: hotline, địa chỉ, giờ làm việc và form gửi tin nhắn.',
  alternates: { canonical: '/lien-he' },
};

export default function ContactPage() {
  const items = [
    { icon: 'pin', label: 'Địa chỉ', value: site.address },
    { icon: 'phone', label: 'Hotline', value: site.hotline, href: telHref(site.hotline) },
    { icon: 'mail', label: 'Email', value: site.email, href: `mailto:${site.email}` },
    { icon: 'clock', label: 'Giờ làm việc', value: site.workingHours },
  ].filter((i) => i.value);

  const map = site.mapEmbed.startsWith('https://www.google.com/maps/embed') ? site.mapEmbed : '';

  return (
    <>
      <PageHero title="Liên hệ" subtitle="Chúng tôi luôn sẵn sàng hỗ trợ bạn." crumbs={[{ label: 'Liên hệ' }]} />
      <div className="section">
        <div className="container contact-grid">
          <div className="contact-info">
            {items.map((it) => (
              <div className="contact-card" key={it.label}>
                <span className="contact-card__icon"><Icon name={it.icon} /></span>
                <div>
                  <p className="contact-card__label">{it.label}</p>
                  {it.href ? (
                    <a className="contact-card__value" href={it.href}>{it.value}</a>
                  ) : (
                    <p className="contact-card__value">{it.value}</p>
                  )}
                </div>
              </div>
            ))}
          </div>
          <div className="card contact-form" id="lien-he-form">
            <h2 className="card__title">Gửi tin nhắn cho chúng tôi</h2>
            <LeadForm source="contact" showEmail showMessage button="Gửi liên hệ" />
          </div>
        </div>
        {map && (
          <div className="container">
            <div className="map-embed">
              <iframe src={map} title="Bản đồ" loading="lazy" referrerPolicy="no-referrer-when-downgrade" allowFullScreen />
            </div>
          </div>
        )}
      </div>
    </>
  );
}
