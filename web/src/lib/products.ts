/**
 * Dữ liệu sản phẩm (tương đương WooCommerce).
 * Sau này có thể thay bằng gọi API (WooCommerce REST/Store API) mà không phải sửa giao diện:
 * chỉ cần giữ nguyên các hàm xuất ra bên dưới.
 */
export interface Product {
  id: number;
  slug: string;
  name: string;
  categorySlug: string;
  price: number;
  salePrice?: number;
  image: string;
  gallery: string[];
  /** HTML tĩnh do chúng ta viết (không lấy từ người dùng). */
  shortDescription: string;
  description: string;
  highlights: string[];
  inStock: boolean;
  featured: boolean;
  createdAt: string;
}

export interface Category {
  slug: string;
  name: string;
  description?: string;
  image?: string;
}

export const categories: Category[] = [
  { slug: 'thao-moc-mogan', name: 'Thảo Mộc Mogan' },
];

export const products: Product[] = [
  {
    id: 1,
    slug: 'thao-moc-mogan',
    name: 'Thảo Mộc Mogan',
    categorySlug: 'thao-moc-mogan',
    price: 390000,
    image: '/images/thao-moc-mogan.jpg',
    gallery: ['/images/thao-moc-mogan.jpg'],
    shortDescription:
      '<p>Trà thảo mộc túi lọc từ <strong>cà gai leo, diệp hạ châu, xạ đen, mướp đắng, lá đơn</strong>.</p><p>Quy cách: <strong>Hộp 30 gói × 5g</strong>.</p>',
    description: `<h2>Thành phần</h2>
<p>Cà gai leo, diệp hạ châu, xạ đen, mướp đắng, lá đơn.</p>
<h2>Quy cách</h2>
<p>Hộp 30 gói × 5g (túi lọc).</p>
<h2>Hướng dẫn sử dụng</h2>
<p>Cho 3 túi lọc vào bình giữ nhiệt, ấm hoặc cốc, đổ 0,5 – 1 lít nước sôi vào. Đợi 5 – 10 phút là có thể dùng.</p>
<p>Nên dùng sau ăn khoảng 30 phút và không dùng khi bụng quá đói.</p>
<h2>Hướng dẫn bảo quản</h2>
<p>Bảo quản nơi khô ráo, thoáng mát, tránh ánh nắng trực tiếp.</p>
<h2>Cảnh báo</h2>
<p>Không sử dụng sản phẩm quá hạn hoặc bao bì không còn nguyên vẹn.</p>
<p><strong>Sản phẩm này không phải là thuốc và không có tác dụng thay thế thuốc chữa bệnh.</strong></p>`,
    highlights: [
      'Thành phần: cà gai leo, diệp hạ châu, xạ đen, mướp đắng, lá đơn',
      'Quy cách: hộp 30 gói × 5g, dạng túi lọc tiện dùng',
      'Cách dùng: hãm 3 túi lọc với 0,5 – 1 lít nước sôi trong 5 – 10 phút',
    ],
    inStock: true,
    featured: true,
    createdAt: '2026-11-09',
  },
];

export function getProduct(slug: string) {
  return products.find((p) => p.slug === slug);
}

export function getCategory(slug: string) {
  return categories.find((c) => c.slug === slug);
}

export function categoryName(slug: string) {
  return getCategory(slug)?.name ?? '';
}

/** Giá đang bán (ưu tiên giá giảm). */
export function currentPrice(p: Product) {
  return p.salePrice && p.salePrice < p.price ? p.salePrice : p.price;
}

/** % giảm giá (0 nếu không giảm). */
export function discountPercent(p: Product) {
  if (!p.salePrice || p.salePrice >= p.price || p.price <= 0) return 0;
  return Math.round(((p.price - p.salePrice) / p.price) * 100);
}

export function formatPrice(n: number) {
  return new Intl.NumberFormat('vi-VN').format(n) + ' ₫';
}

export function featuredProducts(limit = 8) {
  const featured = products.filter((p) => p.featured);
  const list = featured.length >= 4 ? featured : [...products].sort((a, b) => b.createdAt.localeCompare(a.createdAt));
  return list.slice(0, limit);
}

export type ProductSort = 'default' | 'price' | 'price-desc' | 'date';

export interface ProductQuery {
  category?: string;
  min?: number;
  max?: number;
  onSale?: boolean;
  sort?: ProductSort;
  q?: string;
}

export function queryProducts(q: ProductQuery = {}) {
  let list = products.filter((p) => {
    const price = currentPrice(p);
    if (q.category && p.categorySlug !== q.category) return false;
    if (q.min != null && price < q.min) return false;
    if (q.max != null && price > q.max) return false;
    if (q.onSale && discountPercent(p) === 0) return false;
    if (q.q) {
      const needle = q.q.toLowerCase();
      if (!p.name.toLowerCase().includes(needle) && !p.shortDescription.toLowerCase().includes(needle)) return false;
    }
    return true;
  });
  switch (q.sort) {
    case 'price':
      list = [...list].sort((a, b) => currentPrice(a) - currentPrice(b));
      break;
    case 'price-desc':
      list = [...list].sort((a, b) => currentPrice(b) - currentPrice(a));
      break;
    case 'date':
      list = [...list].sort((a, b) => b.createdAt.localeCompare(a.createdAt));
      break;
  }
  return list;
}

/** Các khoảng giá trong bộ lọc (giữ như theme WordPress). */
export const priceRanges: { min?: number; max?: number; label: string }[] = [
  { max: 100000, label: 'Dưới 100.000 ₫' },
  { min: 100000, max: 300000, label: '100.000 – 300.000 ₫' },
  { min: 300000, max: 500000, label: '300.000 – 500.000 ₫' },
  { min: 500000, label: 'Trên 500.000 ₫' },
];
