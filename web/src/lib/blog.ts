import type { Post } from '@/lib/content';

/**
 * Blog: bài viết dài, có trang chi tiết riêng (khác "Tin tức" là bài dẫn sang fanpage).
 * Cách viết bài mới: thêm một mục vào mảng `blogPosts` bên dưới (slug không dấu, ngày YYYY-MM-DD,
 * nội dung là HTML: <h2>, <p>, <ul>…). Ảnh đặt trong `public/images/news/` rồi điền `image`.
 * Không viết công dụng chữa bệnh khi khách chưa có giấy phép quảng cáo.
 */
const COVER = '/images/news/sun-shines-cover.webp';

export const blogPosts: Post[] = [
  {
    slug: 'bai-viet-mau-thoi-quen-song-khoe',
    title: 'Bài viết mẫu: xây thói quen sống khoẻ mỗi ngày',
    excerpt: 'Bài viết mẫu để thử trang blog. Thay bằng nội dung thật khi khách gửi.',
    date: '2026-11-21',
    category: 'Kiến thức',
    image: COVER,
    content: `
<p>Đây là bài viết mẫu để kiểm tra giao diện blog. Nội dung thật sẽ được thay khi có bài từ Sun Shines.</p>
<h2>1. Ngủ đủ giấc</h2>
<p>Đi ngủ và thức dậy đúng giờ giúp cơ thể có nhịp sinh hoạt đều đặn.</p>
<h2>2. Ăn uống cân bằng</h2>
<ul>
  <li>Ăn nhiều rau xanh và trái cây.</li>
  <li>Uống đủ nước mỗi ngày.</li>
  <li>Hạn chế đồ chiên rán, đồ ngọt.</li>
</ul>
<h2>3. Vận động nhẹ nhàng</h2>
<p>Đi bộ 20–30 phút mỗi ngày là khởi đầu đơn giản.</p>
<p><em>Sản phẩm này không phải là thuốc và không có tác dụng thay thế thuốc chữa bệnh.</em></p>
`,
  },
];

export const BLOG_PER_PAGE = 9;

export function getBlogPost(slug: string) {
  return blogPosts.find((p) => p.slug === slug);
}

export function sortedBlogPosts() {
  return [...blogPosts].sort((a, b) => b.date.localeCompare(a.date));
}
