import type { Metadata } from 'next';
import Link from 'next/link';
import { Icon } from '@/components/Icon';
import { PageHero } from '@/components/ui';
import { site, telHref } from '@/lib/site';

export const metadata: Metadata = { title: 'Đặt hàng thành công', robots: { index: false } };

export default async function OrderDonePage({ searchParams }: { searchParams: Promise<Record<string, string | undefined>> }) {
  const sp = await searchParams;
  const code = (sp.ma ?? '').replace(/[^A-Za-z0-9-]/g, '').slice(0, 24);
  return (
    <>
      <PageHero title="Đặt hàng thành công" crumbs={[{ label: 'Đặt hàng thành công' }]} />
      <div className="section">
        <div className="container success-box">
          <span className="success-box__icon"><Icon name="shield" size={36} /></span>
          <h2>Cảm ơn bạn đã đặt hàng!</h2>
          {code && <p className="success-box__code">Mã đơn: {code}</p>}
          <p>
            Nhân viên Sun Shines sẽ gọi xác nhận đơn trong thời gian sớm nhất
            {sp.pt === 'bacs' ? ' và gửi thông tin chuyển khoản cho bạn' : ''}.
          </p>
          <p>
            Cần hỗ trợ ngay? Gọi <a href={telHref(site.hotline)}>{site.hotline}</a>.
          </p>
          <p><Link className="btn btn--primary" href="/san-pham">Tiếp tục mua sắm</Link></p>
        </div>
      </div>
    </>
  );
}
