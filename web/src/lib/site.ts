/**
 * Cài đặt website (tương đương trang "Cài đặt website" của plugin xanh-core).
 * Sửa thông tin công ty, hotline, mạng xã hội tại đây.
 */
export const site = {
  name: 'Sun Shines',
  company: 'CÔNG TY TNHH THƯƠNG MẠI DỊCH VỤ SUN SHINES',
  slogan: 'Trao sức khỏe, Gửi yêu thương',
  description:
    'Sun Shines mang đến các sản phẩm thảo mộc Mogan chọn lọc, an toàn và lành tính cho sức khỏe cả gia đình.',
  /** Câu bắt buộc phải hiển thị ở mọi chỗ giới thiệu sản phẩm. */
  disclaimer:
    'Sản phẩm này không phải là thuốc và không có tác dụng thay thế thuốc chữa bệnh.',
  hotline: '0868 800 440',
  zalo: '0868800440',
  email: '',
  address:
    '389/3/23 Quốc lộ 13, Khu phố 24, Phường Hiệp Bình, TP. Hồ Chí Minh, Việt Nam',
  workingHours: '8:00 – 18:00, Thứ 2 – Thứ 7',
  topbar: 'Miễn phí giao hàng cho đơn từ 500.000 ₫',
  messenger: '',
  /** Bản đồ Google Maps (chỉ nhận link https://www.google.com/maps/embed...). */
  mapEmbed: '',
  social: [
    { label: 'Facebook', url: 'https://www.facebook.com/share/19uVvCc2Fd/', icon: 'facebook' },
    { label: 'Thảo Mộc Mogan', url: 'https://www.facebook.com/share/1Guucfskuf/', icon: 'facebook' },
    { label: 'Zalo', url: 'https://zalo.me/0868800440', icon: 'zalo' },
  ],
  url: (process.env.NEXT_PUBLIC_SITE_URL ?? 'http://localhost:3000').replace(/\/$/, ''),
};

export const mainNav = [
  { label: 'Trang chủ', href: '/' },
  { label: 'Giới thiệu', href: '/gioi-thieu' },
  { label: 'Sản phẩm', href: '/san-pham' },
  { label: 'Tin tức', href: '/tin-tuc' },
  { label: 'Liên hệ', href: '/lien-he' },
];

export const footerMenus = [
  {
    title: 'Về chúng tôi',
    links: [
      { label: 'Giới thiệu', href: '/gioi-thieu' },
      { label: 'Tin tức', href: '/tin-tuc' },
      { label: 'Liên hệ', href: '/lien-he' },
    ],
  },
  {
    title: 'Chính sách',
    links: [
      { label: 'Chính sách đổi trả', href: '/chinh-sach-doi-tra' },
      { label: 'Chính sách giao hàng', href: '/chinh-sach-giao-hang' },
      { label: 'Chính sách bảo mật', href: '/chinh-sach-bao-mat' },
      { label: 'Hướng dẫn thanh toán', href: '/huong-dan-thanh-toan' },
    ],
  },
];

/** Banner slider trang chủ (tương đương CPT xanh_banner, vị trí home_slider). */
export const heroSlides = [
  {
    title: 'Trao sức khỏe, Gửi yêu thương',
    subtitle: site.description,
    link: '/san-pham',
    button: 'Mua sắm ngay',
    image: '/images/thao-moc-mogan.webp',
    alt: 'Hộp Thảo Mộc Mogan 30 gói x 5g',
  },
];

export function telHref(phone: string) {
  return 'tel:' + phone.replace(/[^0-9+]/g, '');
}
