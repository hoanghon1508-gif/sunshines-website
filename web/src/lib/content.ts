/**
 * Tin tức và trang nội dung (tương đương bài viết và trang của WordPress).
 * Nội dung dưới đây là NỘI DUNG TẠM giống bản WordPress; thay khi khách gửi nội dung thật.
 * Không viết công dụng chữa bệnh khi khách chưa có giấy phép quảng cáo.
 */
export interface Post {
  slug: string;
  title: string;
  excerpt: string;
  /** HTML tĩnh. */
  content: string;
  date: string;
  category: string;
  image?: string;
}

export const posts: Post[] = [
  {
    slug: 'bi-quyet-chon-san-pham-phu-hop-cho-gia-dinh-ban',
    title: 'Bí quyết chọn sản phẩm phù hợp cho gia đình bạn',
    excerpt: 'Đoạn tóm tắt ngắn của bài viết giúp người đọc nắm nhanh nội dung chính.',
    content: '<p>Nội dung bài viết mẫu.</p>',
    date: '2026-11-09',
    category: 'Kiến thức',
  },
  {
    slug: 'loi-ich-cua-thao-moc-trong-cham-soc-suc-khoe-hang-ngay',
    title: 'Lợi ích của thảo mộc trong chăm sóc sức khỏe hằng ngày',
    excerpt: 'Đoạn tóm tắt ngắn của bài viết giúp người đọc nắm nhanh nội dung chính.',
    content: '<p>Nội dung bài viết mẫu.</p>',
    date: '2026-11-08',
    category: 'Kiến thức',
  },
  {
    slug: 'huong-dan-bao-quan-san-pham-dung-cach',
    title: 'Hướng dẫn bảo quản sản phẩm đúng cách',
    excerpt: 'Đoạn tóm tắt ngắn của bài viết giúp người đọc nắm nhanh nội dung chính.',
    content: '<p>Nội dung bài viết mẫu.</p>',
    date: '2026-11-07',
    category: 'Kiến thức',
  },
];

export const POSTS_PER_PAGE = 9;

export function getPost(slug: string) {
  return posts.find((p) => p.slug === slug);
}

export function sortedPosts() {
  return [...posts].sort((a, b) => b.date.localeCompare(a.date));
}

export function formatDate(iso: string) {
  const [y, m, d] = iso.split('-');
  return `${d}/${m}/${y}`;
}

export interface FaqItem {
  q: string;
  a: string;
}

export interface Page {
  slug: string;
  title: string;
  subtitle?: string;
  /** HTML tĩnh. */
  content: string;
  faq?: FaqItem[];
}

export const pages: Page[] = [
  {
    slug: 'gioi-thieu',
    title: 'Giới thiệu',
    content: '<p>Giới thiệu về công ty Sun Shines.</p>',
    faq: [
      { q: 'Bao lâu thì nhận được hàng?', a: 'Từ 2–4 ngày làm việc.' },
      { q: 'Có được đổi trả không?', a: 'Được đổi trả trong 7 ngày.' },
    ],
  },
  { slug: 'chinh-sach-doi-tra', title: 'Chính sách đổi trả', content: '<p>Nội dung chính sách đổi trả.</p>' },
  { slug: 'chinh-sach-giao-hang', title: 'Chính sách giao hàng', content: '<p>Nội dung chính sách giao hàng.</p>' },
  { slug: 'chinh-sach-bao-mat', title: 'Chính sách bảo mật', content: '<p>Nội dung chính sách bảo mật.</p>' },
  { slug: 'huong-dan-thanh-toan', title: 'Hướng dẫn thanh toán', content: '<p>Nội dung hướng dẫn thanh toán.</p>' },
];

export function getPage(slug: string) {
  return pages.find((p) => p.slug === slug);
}
