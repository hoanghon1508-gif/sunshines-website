import Link from 'next/link';
import { PageHero } from '@/components/ui';

export default function NotFound() {
  return (
    <>
      <PageHero
        title="Không tìm thấy trang"
        subtitle="Trang bạn tìm có thể đã bị xoá hoặc đổi địa chỉ."
        crumbs={[{ label: 'Không tìm thấy trang' }]}
      />
      <div className="section">
        <div className="container container--narrow empty-state">
          <p><Link className="btn btn--primary" href="/">Về trang chủ</Link></p>
        </div>
      </div>
    </>
  );
}
