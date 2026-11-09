import { NextResponse } from 'next/server';
import { clean, isValidEmail, isValidPhone, normalizePhone } from '@/lib/validation';
import { clientIp, deliver, rateLimited } from '@/lib/server-utils';
import { getProduct } from '@/lib/products';

const SOURCES = ['contact', 'consult', 'promo'];
const MIN_SECONDS = 3; // Form gửi nhanh hơn 3 giây coi là bot.
const RATE_LIMIT = 5; // Tối đa 5 lần / 10 phút / IP.
const RATE_WINDOW = 10 * 60 * 1000;

export async function POST(req: Request) {
  let body: Record<string, unknown>;
  try {
    body = await req.json();
  } catch {
    return NextResponse.json({ ok: false, error: 'invalid' }, { status: 400 });
  }

  // 1. Honeypot: giả vờ thành công để bot không thử lại.
  if (body.website) return NextResponse.json({ ok: true });

  // 2. Time-trap.
  const startedAt = Number(body.startedAt);
  if (!Number.isFinite(startedAt) || Date.now() - startedAt < MIN_SECONDS * 1000) {
    return NextResponse.json({ ok: false, error: 'error' }, { status: 400 });
  }

  // 3. Giới hạn tần suất.
  if (rateLimited('lead:' + clientIp(req), RATE_LIMIT, RATE_WINDOW)) {
    return NextResponse.json({ ok: false, error: 'limit' }, { status: 429 });
  }

  // 4. Kiểm tra dữ liệu.
  const source = SOURCES.includes(String(body.source)) ? String(body.source) : 'contact';
  const name = clean(body.name, 120);
  const phone = normalizePhone(clean(body.phone, 30));
  const email = clean(body.email, 120);
  const message = clean(body.message, 3000);
  const productSlug = clean(body.product, 80);

  if (!isValidPhone(phone) || (source === 'contact' && !name) || !isValidEmail(email)) {
    return NextResponse.json({ ok: false, error: 'invalid' }, { status: 422 });
  }

  const ok = await deliver('lead', {
    createdAt: new Date().toISOString(),
    source,
    name,
    phone,
    email,
    message,
    product: getProduct(productSlug)?.name ?? '',
    page: req.headers.get('referer') ?? '',
  });
  if (!ok) return NextResponse.json({ ok: false, error: 'error' }, { status: 502 });
  return NextResponse.json({ ok: true });
}
