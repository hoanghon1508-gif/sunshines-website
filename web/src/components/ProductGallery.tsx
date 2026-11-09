'use client';

import Image from 'next/image';
import { useState } from 'react';

/** Ảnh chính + dải ảnh nhỏ (slide ảnh sản phẩm). */
export function ProductGallery({ images, alt }: { images: string[]; alt: string }) {
  const [i, setI] = useState(0);
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
