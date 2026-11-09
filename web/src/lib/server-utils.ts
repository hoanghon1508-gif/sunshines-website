import 'server-only';

/** Giới hạn tần suất theo IP, lưu trong bộ nhớ (đủ để chặn spam đơn giản; bản chạy nhiều máy chủ nên dùng Redis/Upstash). */
const hits = new Map<string, number[]>();

export function rateLimited(key: string, limit: number, windowMs: number): boolean {
  const now = Date.now();
  const recent = (hits.get(key) ?? []).filter((t) => now - t < windowMs);
  if (recent.length >= limit) {
    hits.set(key, recent);
    return true;
  }
  recent.push(now);
  hits.set(key, recent);
  if (hits.size > 5000) hits.clear();
  return false;
}

export function clientIp(req: Request): string {
  const fwd = req.headers.get('x-forwarded-for');
  return (fwd ? fwd.split(',')[0] : req.headers.get('x-real-ip') ?? 'unknown').trim();
}

/** Gửi dữ liệu tới webhook (nếu có cấu hình) và luôn ghi log để không mất dữ liệu. */
export async function deliver(kind: 'lead' | 'order', payload: unknown): Promise<boolean> {
  const url = kind === 'lead' ? process.env.LEAD_WEBHOOK_URL : process.env.ORDER_WEBHOOK_URL;
  console.log(`[${kind}]`, JSON.stringify(payload));
  if (!url) return true;
  try {
    const res = await fetch(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ kind, ...(payload as object) }),
      signal: AbortSignal.timeout(8000),
    });
    return res.ok;
  } catch (e) {
    console.error(`[${kind}] webhook lỗi`, e);
    return false;
  }
}
