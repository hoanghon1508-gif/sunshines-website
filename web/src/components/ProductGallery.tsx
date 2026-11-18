'use client';

import Image from 'next/image';
import { useState } from 'react';

/** Ảnh chính + dải ảnh nhỏ (slide ảnh sản phẩm). */
export function ProductGallery({ images, alt }: { images: string[]; alt: string }) {
  const [i, setI] = useState(0);
  if (images.length === 0) {
    return (
      <figure className="product-detail__images">
        <div className="product-detail__main">
          <span className="img-placeholder" role="img" aria-label={`Ảnh ${alt} sẽ được cập nhật`}>
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
              <rect x="3" y="3" width="18" height="18" rx="2" /><circle cx="9" cy="9" r="2" /><path d="m21 15-5-5L5 21" />
            </svg>
          </span>
        </div>
      </figure>
    );
  }
  return (
    <figure className="product-detail__images">
      <div className="product-detail__main">
        <Image src={images[i]} alt={alt} fill sizes="(max-width: 960px) 100vw, 560px" priority />
      </div>
      {images.length > 1 && (
        <div className="product-detail__thumbs">
          {images.map((src, n) => (
            <button
              key={src}
              type="button"
              className={`product-detail__thumb${n === i ? ' is-active' : ''}`}
              aria-label={`Xem ảnh ${n + 1}`}
              aria-pressed={n === i}
              onClick={() => setI(n)}
            >
              <Image src={src} alt="" fill sizes="100px" style={{ objectFit: 'cover' }} />
            </button>
          ))}
        </div>
      )}
    </figure>
  );
}
