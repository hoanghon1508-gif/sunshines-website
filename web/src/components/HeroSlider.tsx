'use client';

import Image from 'next/image';
import Link from 'next/link';
import { useCallback, useEffect, useRef, useState } from 'react';
import { Icon } from './Icon';

export interface Slide {
  title: string;
  subtitle?: string;
  link?: string;
  button?: string;
  image?: string;
  alt?: string;
}

/** Slider banner trang chủ: tự chạy 6 giây, dừng khi rê chuột/focus, vuốt trên điện thoại. */
export function HeroSlider({ slides }: { slides: Slide[] }) {
  const [current, setCurrent] = useState(0);
  const timer = useRef<ReturnType<typeof setInterval> | null>(null);
  const x0 = useRef<number | null>(null);
  const n = slides.length;

  const stop = useCallback(() => {
    if (timer.current) clearInterval(timer.current);
    timer.current = null;
  }, []);

  const start = useCallback(() => {
    stop();
    if (n < 2 || window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
    timer.current = setInterval(() => setCurrent((c) => (c + 1) % n), 6000);
  }, [n, stop]);

  useEffect(() => {
    start();
    return stop;
  }, [start, stop]);

  const go = (i: number) => setCurrent(((i % n) + n) % n);

  return (
    <div
      className="hero__slider"
      onMouseEnter={stop}
      onMouseLeave={start}
      onFocus={stop}
      onTouchStart={(e) => {
        x0.current = e.touches[0].clientX;
      }}
      onTouchEnd={(e) => {
        if (x0.current == null) return;
        const dx = e.changedTouches[0].clientX - x0.current;
        if (Math.abs(dx) > 40) {
          go(current + (dx < 0 ? 1 : -1));
          start();
        }
        x0.current = null;
      }}
    >
      {slides.map((s, i) => {
        const H = i === 0 ? 'h1' : 'h2';
        return (
          <article
            key={i}
            className={`hero__slide${i === current ? ' is-active' : ''}`}
            aria-roledescription="slide"
            hidden={i !== current}
          >
            <div className="hero__content">
              <H className="hero__title">{s.title}</H>
              {s.subtitle && <p className="hero__text">{s.subtitle}</p>}
              {s.link && (
                <div className="hero__actions">
                  <Link className="btn btn--primary btn--lg" href={s.link}>
                    {s.button || 'Xem ngay'}
                  </Link>
                </div>
              )}
            </div>
            <div className="hero__media">
              {s.image ? (
                <Image
                  src={s.image}
                  alt={s.alt ?? s.title}
                  width={1120}
                  height={880}
                  priority={i === 0}
                  sizes="(max-width: 960px) 100vw, 560px"
                />
              ) : (
                <span className="img-placeholder"><Icon name="image" size={48} /></span>
              )}
            </div>
          </article>
        );
      })}
      {n > 1 && (
        <div className="hero__dots" role="tablist">
          {slides.map((_, i) => (
            <button
              key={i}
              type="button"
              className={`hero__dot${i === current ? ' is-active' : ''}`}
              aria-label={`Banner ${i + 1}`}
              onClick={() => {
                go(i);
                start();
              }}
            />
          ))}
        </div>
      )}
    </div>
  );
}
