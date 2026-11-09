import Link from 'next/link';
import { Icon } from './Icon';
import { footerMenus, site, telHref } from '@/lib/site';

export function Footer() {
  return (
    <footer className="site-footer">
      <div className="container site-footer__grid">
        <div className="site-footer__col site-footer__brand">
          <p className="site-footer__name">{site.name}</p>
          <p className="site-footer__slogan">{site.slogan}</p>
          <ul className="contact-list">
            <li>{site.company}</li>
            <li>
              <Icon name="pin" size={16} />
              <span>{site.address}</span>
            </li>
            <li>
              <Icon name="phone" size={16} />
              <a href={telHref(site.hotline)}>{site.hotline}</a>
            </li>
            {site.email && (
              <li>
                <Icon name="mail" size={16} />
                <a href={`mailto:${site.email}`}>{site.email}</a>
              </li>
            )}
          </ul>
        </div>

        {footerMenus.map((m) => (
          <div className="site-footer__col" key={m.title}>
            <p className="site-footer__heading">{m.title}</p>
            <ul className="footer-menu">
              {m.links.map((l) => (
                <li key={l.href}><Link href={l.href}>{l.label}</Link></li>
              ))}
            </ul>
          </div>
        ))}

        <div className="site-footer__col">
          <p className="site-footer__heading">Kết nối</p>
          <ul className="footer-menu footer-social">
            {site.social.map((s) => (
              <li key={s.label}>
                <a href={s.url} target="_blank" rel="noopener">
                  <Icon name={s.icon} size={18} />
                  {s.label}
                </a>
              </li>
            ))}
          </ul>
        </div>
      </div>
      <div className="container site-footer__bottom">
        <p className="site-footer__disclaimer">{site.disclaimer}</p>
        <p>
          &copy; {new Date().getFullYear()} {site.name} – {site.company}. Bảo lưu mọi quyền.
        </p>
      </div>
    </footer>
  );
}
