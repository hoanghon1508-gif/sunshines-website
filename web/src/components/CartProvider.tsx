'use client';

import { createContext, useCallback, useContext, useEffect, useMemo, useState } from 'react';
import { currentPrice, getProduct, type Product } from '@/lib/products';

export interface CartLine {
  product: Product;
  qty: number;
}

interface CartContextValue {
  lines: CartLine[];
  count: number;
  subtotal: number;
  ready: boolean;
  add: (slug: string, qty?: number) => void;
  setQty: (slug: string, qty: number) => void;
  remove: (slug: string) => void;
  clear: () => void;
}

const KEY = 'xanh_cart';
const MAX_QTY = 99;
const CartContext = createContext<CartContextValue | null>(null);

type Stored = Record<string, number>;

function read(): Stored {
  try {
    const raw = window.localStorage.getItem(KEY);
    const data = raw ? JSON.parse(raw) : {};
    return data && typeof data === 'object' ? data : {};
  } catch {
    return {};
  }
}

export function CartProvider({ children }: { children: React.ReactNode }) {
  const [items, setItems] = useState<Stored>({});
  const [ready, setReady] = useState(false);

  // Đọc giỏ hàng từ trình duyệt sau khi tải trang (tránh lệch HTML giữa server và client).
  useEffect(() => {
    setItems(read());
    setReady(true);
    const onStorage = (e: StorageEvent) => {
      if (e.key === KEY) setItems(read());
    };
    window.addEventListener('storage', onStorage);
    return () => window.removeEventListener('storage', onStorage);
  }, []);

  const commit = useCallback((next: Stored) => {
    setItems(next);
    try {
      window.localStorage.setItem(KEY, JSON.stringify(next));
    } catch {
      /* Bỏ qua khi trình duyệt chặn lưu trữ. */
    }
  }, []);

  const add = useCallback(
    (slug: string, qty = 1) => {
      if (!getProduct(slug)) return;
      const cur = read();
      cur[slug] = Math.min(MAX_QTY, (cur[slug] ?? 0) + Math.max(1, Math.floor(qty)));
      commit(cur);
    },
    [commit],
  );

  const setQty = useCallback(
    (slug: string, qty: number) => {
      const cur = read();
      if (!Number.isFinite(qty) || qty <= 0) delete cur[slug];
      else cur[slug] = Math.min(MAX_QTY, Math.floor(qty));
      commit(cur);
    },
    [commit],
  );

  const remove = useCallback(
    (slug: string) => {
      const cur = read();
      delete cur[slug];
      commit(cur);
    },
    [commit],
  );

  const clear = useCallback(() => commit({}), [commit]);

  const value = useMemo<CartContextValue>(() => {
    const lines: CartLine[] = [];
    for (const [slug, qty] of Object.entries(items)) {
      const product = getProduct(slug);
      if (product && qty > 0) lines.push({ product, qty });
    }
    return {
      lines,
      count: lines.reduce((n, l) => n + l.qty, 0),
      subtotal: lines.reduce((n, l) => n + currentPrice(l.product) * l.qty, 0),
      ready,
      add,
      setQty,
      remove,
      clear,
    };
  }, [items, ready, add, setQty, remove, clear]);

  return <CartContext.Provider value={value}>{children}</CartContext.Provider>;
}

export function useCart() {
  const ctx = useContext(CartContext);
  if (!ctx) throw new Error('useCart phải nằm trong CartProvider');
  return ctx;
}
