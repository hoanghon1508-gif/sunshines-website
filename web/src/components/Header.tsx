'use client';

import Link from 'next/link';
import { usePathname, useRouter } from 'next/navigation';
import { useEffect, useRef, useState } from 'react';
import { Icon } from './Icon';
import { Logo } from './ui';
import { useCart } from './CartProvider';
import { mainNav, site, telHref } from '@/lib/site';

export function Header() {
  const pathname = usePathname();
  const router = useRouter();
  const { count } = useCart();
  const [navOpen, setNavOpen] = useState(false);
  const [searchOpen, setSearchOpen] = useState(false);
  const [q, setQ] = useState('');
  const toggleRef = useRef<HTMLButtonElement>(null);
  const closeRef = useRef<HTMLButtonElement>(null);
  const searchRef = useRef<HTMLInputElement>(null);

  // Đóng menu khi chuyển trang.
  useEffect(() => {
    setNavOpen(false);
    setSearchOpen(false);
  }, [pathname]);

  useEffect(() => {
    document.body.style.overflow = navOpen ? 'hidden' : '';
    if (navOpen) closeRef.current?.focus();
    const onKey = (e: KeyboardEvent) => {
      if (e.key === 'Escape') {
        setNavOpen(false);
        toggleRef.current?.focus();
      }
    };
    document.addEventListener('keydown', onKey);
    return () => {
      document.removeEventListener('keydown', onKey);
      document.body.style.overflow = '';
    };
  }, [navOpen]);

  useEffect(() => {
    if (searchOpen) searchRef.current?.focus();
  }, [searchOpen]);

  const isActive = (href: string) => (href === '/' ? pathname === '/' : pathname.startsWith(href));
  const hotline = site.hotline;

  return (
    <header className="site-header">
      <div className="topbar">
        <div className="container topbar__inner">
          <p className="topbar__text">
            Hotline: <a href={telHref(hotline)}>{hotline}</a>
            {site.topbar && (
              <span className="topbar__note">
                <span className="topbar__dot" aria-hidden="true">·</span>
                {site.topbar}
              </span>
            )}
          </p>
          <ul className="topbar__social">
            {site.social.map((s) => (
              <li key={s.label}>
                <a href={s.url} target="_blank" rel="noopener">{s.label}</a>
              </li>
            ))}
          </ul>
        </div>
      </div>

      <div className="navbar">
        <div className="container navbar__inner">
          <button
            ref={toggleRef}
            className="navbar__toggle icon-btn"
            type="button"
            aria-expanded={navOpen}
            aria-controls="site-nav"
            aria-label="Mở menu"
            onClick={() => setNavOpen(true)}
          >
            <Icon name="menu" />
          </button>

          <div className="navbar__brand"><Logo /></div>

          <nav className={`site-nav${navOpen ? ' is-open' : ''}`} id="site-nav" aria-label="Menu chính">
            <div className="site-nav__head">
              <Logo />
              <button
                ref={closeRef}
                className="site-nav__close icon-btn"
                type="button"
                aria-label="Đóng menu"
                onClick={() => {
                  setNavOpen(false);
                  toggleRef.current?.focus();
                }}
              >
                <Icon name="close" />
              </button>
            </div>
            <ul className="menu">
              {mainNav.map((n) => (
                <li key={n.href} className={`menu-item${isActive(n.href) ? ' current-menu-item' : ''}`}>
                  <Link href={n.href} aria-current={isActive(n.href) ? 'page' : undefined}>{n.label}</Link>
                </li>
              ))}
            </ul>
          </nav>

          <div className="navbar__actions">
            <button
              className="icon-btn"
              type="button"
              data-search-toggle
              aria-expanded={searchOpen}
              aria-controls="header-search"
              aria-label="Tìm kiếm"
              onClick={() => setSearchOpen((o) => !o)}
            >
              <Icon name="search" />
            </button>
            <Link className="icon-btn cart-link" href="/gio-hang" aria-label={`Giỏ hàng (${count} sản phẩm)`}>
              <Icon name="cart" />
              <span className="cart-count">{count}</span>
            </Link>
          </div>
        </div>
        <div className="header-search" id="header-search" hidden={!searchOpen}>
          <div className="container">
            <form
              role="search"
              className="search-form"
              onSubmit={(e) => {
                e.preventDefault();
                const term = q.trim();
                if (term) router.push(`/tim-kiem?s=${encodeURIComponent(term)}`);
              }}
            >
              <label className="screen-reader-text" htmlFor="header-search-input">Tìm kiếm</label>
              <input
                ref={searchRef}
                id="header-search-input"
                type="search"
                className="search-form__input"
                placeholder="Tìm sản phẩm, bài viết…"
                value={q}
                onChange={(e) => setQ(e.target.value)}
              />
              <button type="submit" className="btn btn--primary">Tìm</button>
            </form>
          </div>
        </div>
      </div>
      <div className="nav-overlay" hidden={!navOpen} onClick={() => setNavOpen(false)} />
    </header>
  );
}
