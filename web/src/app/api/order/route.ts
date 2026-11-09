import { NextResponse } from 'next/server';
import { clean, isValidEmail, isValidPhone, normalizePhone } from '@/lib/validation';
import { clientIp, deliver, rateLimited } from '@/lib/server-utils';
import { currentPrice, getProduct } from '@/lib/products';

const PAYMENTS: Record<string, string> = {
  cod: 'Thanh toán khi nhận hàng (COD)',
  bacs: 'Chuyển khoản ngân hàng',
};

function orderCode() {
  const d = new Date();
  const ymd = `${d.getFullYear()}${String(d.getMonth() + 1).padStart(2, '0')}${String(d.getDate()).padStart(2, '0')}`;
  return `SS${ymd}-${Math.random().toString(36).slice(2, 6).toUpperCase()}`;
}

export async function POST(req: Request) {
  let body: Record<string, any>;
  try {
    body = await req.json();
  } catch {
    return NextResponse.json({ ok: false, error: 'invalid' }, { status: 400 });
  }

  if (body.website) return NextResponse.json({ ok: true, code: orderCode() }); // honeypot
  if (rateLimited('order:' + clientIp(req), 5, 10 * 60 * 1000)) {
    return NextResponse.json({ ok: false, error: 'limit' }, { status: 429 });
  }

  const c = body.customer ?? {};
  const customer = {
    name: clean(c.name, 120),
    phone: normalizePhone(clean(c.phone, 30)),
    email: clean(c.email, 120),
    address: clean(c.address, 300),
    city: clean(c.city, 120),
    note: clean(c.note, 1000),
  };
  if (!customer.name || !customer.address || !customer.city || !isValidPhone(customer.phone) || !isValidEmail(customer.email)) {
    return NextResponse.json({ ok: false, error: 'invalid' }, { status: 422 });
  }
  const payment = PAYMENTS[String(body.payment)] ? String(body.payment) : 'cod';

  // Tính lại giá ở máy chủ, không tin giá từ trình duyệt.
  const items = (Array.isArray(body.items) ? body.items : [])
    .map((i: any) => ({ product: getProduct(String(i?.slug)), qty: Math.min(99, Math.floor(Number(i?.qty)) || 0) }))
    .filter((i: any) => i.product && i.product.inStock && i.qty > 0)
    .map((i: any) => ({
      slug: i.product.slug as string,
      name: i.product.name as string,
      qty: i.qty as number,
      price: currentPrice(i.product),
    }));
  if (items.length === 0) return NextResponse.json({ ok: false, error: 'empty' }, { status: 422 });

  const total = items.reduce((n: number, i: { price: number; qty: number }) => n + i.price * i.qty, 0);
  const code = orderCode();

  const ok = await deliver('order', {
    code,
    createdAt: new Date().toISOString(),
    customer,
    payment: PAYMENTS[payment],
    items,
    total,
  });
  if (!ok) return NextResponse.json({ ok: false, error: 'error' }, { status: 502 });
  return NextResponse.json({ ok: true, code, total });
}
