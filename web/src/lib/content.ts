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
  /** Link bài viết trên fanpage Facebook. Có link này thì thẻ tin tức mở thẳng bài trên Facebook (tab mới), không có trang chi tiết riêng. */
  externalUrl?: string;
}

/**
 * Tin tức = bài viết trên fanpage. Thêm bài: copy link bài trên Facebook vào `externalUrl`,
 * ghi tiêu đề, ngày đăng (YYYY-MM-DD) và 1–2 câu tóm tắt; ảnh đặt trong `public/images/news/` rồi điền `image`.
 */
export const posts: Post[] = [
  /*
   * 3 bài dưới đây (18/11) mới có LINK, chưa có tiêu đề/ngày/tóm tắt thật vì Facebook không cho đọc khi chưa đăng nhập.
   * date: '' = chưa rõ ngày (thẻ sẽ không hiện ngày). Cần thay tiêu đề, ngày, tóm tắt khi khách gửi.
   * Lưu ý: 2 bài đầu thuộc trang cá nhân/khác (cothamsongkhoe), cần kiểm tra không có câu nói công dụng chữa bệnh.
   */
  {
    slug: 'bai-fanpage-1',
    title: 'Bài viết trên Facebook',
    excerpt: 'Nhấn để xem bài viết trên Facebook.',
    content: '',
    date: '',
    category: 'Fanpage',
    externalUrl: 'https://www.facebook.com/reel/1145278314658720',
  },
  {
    slug: 'bai-fanpage-2',
    title: 'Bài viết trên Facebook',
    excerpt: 'Nhấn để xem bài viết trên Facebook.',
    content: '',
    date: '',
    category: 'Fanpage',
    externalUrl: 'https://www.facebook.com/cothamsongkhoe/posts/pfbid02hj6y4oivLmsCaxXy6s7UBJa4FVFcUD1vvCJ2QNScAK2CJdUhsk8j1mqkL5QuPwjHl',
  },
  {
    slug: 'bai-fanpage-3',
    title: 'Bài viết trên Facebook',
    excerpt: 'Nhấn để xem bài viết trên Facebook.',
    content: '',
    date: '',
    category: 'Fanpage',
    externalUrl: 'https://www.facebook.com/cothamsongkhoe/posts/pfbid0pBocwP7q1QY6eo6hbravuvKLnKLc4ksPysSWgRzPzmqB5fHZZRy7544RSVEq77rvl',
  },
  {
    slug: 'teambuilding-2026',
    title: 'Sun Shines – Teambuilding 2026',
    excerpt: 'Sun Shines – Trao sức khỏe, Gửi yêu thương. Hình ảnh hoạt động teambuilding 2026 của đội ngũ Sun Shines.',
    content: '',
    date: '2026-09-19',
    category: 'Hoạt động',
    externalUrl: 'https://www.facebook.com/reel/892992433893506/',
  },
];

/** Bài có trang chi tiết riêng trên web (không tính bài dẫn sang Facebook). */
export const localPosts = posts.filter((p) => !p.externalUrl);

export const POSTS_PER_PAGE = 9;

export function getPost(slug: string) {
  return localPosts.find((p) => p.slug === slug);
}

export function sortedPosts() {
  return [...posts].sort((a, b) => b.date.localeCompare(a.date));
}

export function formatDate(iso: string) {
  if (!iso) return '';
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
