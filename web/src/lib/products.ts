/**
 * Dữ liệu sản phẩm (tương đương WooCommerce).
 * Sau này có thể thay bằng gọi API (WooCommerce REST/Store API) mà không phải sửa giao diện:
 * chỉ cần giữ nguyên các hàm xuất ra bên dưới.
 */
export interface Product {
  id: number;
  /** Mã sản phẩm bên Pancake POS (dùng để đối chiếu/đồng bộ sau này). */
  sku?: string;
  slug: string;
  name: string;
  categorySlug: string;
  /** Giá niêm yết (₫). Để 0 nếu chưa có giá: web hiện "Liên hệ" và không cho thêm vào giỏ. */
  price: number;
  salePrice?: number;
  /** Ảnh đại diện; bỏ trống thì hiện khung ảnh tạm. */
  image?: string;
  gallery: string[];
  /** false: không hiện câu "không phải là thuốc" ở trang chi tiết (vd. mỹ phẩm). Mặc định true. */
  showDisclaimer?: boolean;
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
  { slug: 'thao-moc-mogan', name: 'Thảo Mộc Mogan', image: '/images/categories/thao-moc-mogan.webp' },
  { slug: 'thuc-pham-bao-ve-suc-khoe', name: 'Thực phẩm bảo vệ sức khỏe', image: '/images/categories/thuc-pham-bao-ve-suc-khoe.webp' },
  { slug: 'cham-soc-ca-nhan', name: 'Chăm sóc cá nhân', image: '/images/categories/cham-soc-ca-nhan.webp' },
  { slug: 'ca-phe', name: 'Cà phê', image: '/images/categories/ca-phe.webp' },
];

export const products: Product[] = [
  {
    id: 1,
    sku: '80',
    slug: 'thao-moc-mogan',
    name: 'Thảo Mộc Mogan',
    categorySlug: 'thao-moc-mogan',
    price: 390000,
    image: '/images/products/thao-moc-mogan.webp',
    gallery: ['/images/products/thao-moc-mogan.webp', '/images/products/thao-moc-mogan-2.webp'],
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
  /*
   * 4 sản phẩm thêm ngày 17/11 theo danh sách Pancake POS của khách.
   * CHƯA CÓ giá, ảnh thật và thông tin chính thức: mô tả dưới đây chỉ nêu thông tin chung tìm trên mạng,
   * KHÔNG ghi công dụng. Cần khách xác nhận giá, quy cách, ảnh bao bì và giấy công bố trước khi bán.
   */
  {
    id: 2,
    sku: '02',
    slug: 'motemaslim',
    name: 'MOTEMASLIM',
    categorySlug: 'thuc-pham-bao-ve-suc-khoe',
    price: 0,
    image: '/images/products/motemaslim.webp',
    gallery: ['/images/products/motemaslim.webp'],
    shortDescription:
      '<p>Thực phẩm bảo vệ sức khỏe dạng viên uống có xuất xứ từ <strong>Nhật Bản</strong>.</p><p>Quy cách tham khảo: <strong>hộp 120 viên</strong>.</p>',
    description: `<h2>Giới thiệu</h2>
<p>MOTEMASLIM là thực phẩm bảo vệ sức khỏe dạng viên uống, có xuất xứ từ Nhật Bản.</p>
<h2>Thông tin sản phẩm</h2>
<p>Dạng bào chế: viên uống. Quy cách tham khảo: hộp 120 viên.</p>
<p>Vui lòng đọc kỹ nhãn trên bao bì và hướng dẫn sử dụng của nhà sản xuất. Liên hệ hotline để được tư vấn cách dùng phù hợp.</p>
<p>Phụ nữ mang thai, đang cho con bú, trẻ em và người đang dùng thuốc điều trị nên hỏi ý kiến bác sĩ trước khi sử dụng.</p>
<p><strong>Sản phẩm này không phải là thuốc và không có tác dụng thay thế thuốc chữa bệnh.</strong></p>`,
    highlights: ['Xuất xứ: Nhật Bản', 'Dạng viên uống, quy cách tham khảo hộp 120 viên'],
    inStock: true,
    featured: false,
    createdAt: '2026-11-17',
  },
  {
    id: 3,
    sku: '111',
    slug: 'nano-intestines-beauty-queen',
    name: 'Nichiei Bussan Chou Beauty Queen (Nano Intestines Beauty Queen) 30 gói',
    categorySlug: 'thuc-pham-bao-ve-suc-khoe',
    // Giá lấy từ bản WordPress cũ: giá niêm yết 1.500.000, đang bán 1.290.000.
    price: 1500000,
    salePrice: 1290000,
    image: '/images/products/nano-intestines-beauty-queen.webp',
    gallery: ['/images/products/nano-intestines-beauty-queen.webp'],
    shortDescription:
      '<p>Thực phẩm bảo vệ sức khỏe dạng bột uống, xuất xứ <strong>Nhật Bản</strong>.</p><p>Quy cách tham khảo: <strong>hộp 30 gói</strong>.</p>',
    description: `<h2>Giới thiệu</h2>
<p>Nano Intestines Beauty Queen là thực phẩm bảo vệ sức khỏe dạng bột uống, xuất xứ Nhật Bản.</p>
<h2>Thông tin sản phẩm</h2>
<p>Dạng bào chế: bột uống đóng gói. Quy cách tham khảo: hộp 30 gói.</p>
<p>Vui lòng đọc kỹ nhãn trên bao bì và hướng dẫn sử dụng của nhà sản xuất. Liên hệ hotline để được tư vấn cách dùng phù hợp.</p>
<p>Phụ nữ mang thai, đang cho con bú, trẻ em và người đang dùng thuốc điều trị nên hỏi ý kiến bác sĩ trước khi sử dụng.</p>
<p><strong>Sản phẩm này không phải là thuốc và không có tác dụng thay thế thuốc chữa bệnh.</strong></p>`,
    highlights: ['Xuất xứ: Nhật Bản', 'Dạng bột uống, quy cách tham khảo hộp 30 gói'],
    inStock: true,
    featured: false,
    createdAt: '2026-11-17',
  },
  {
    id: 4,
    sku: '03',
    slug: 'lan-khu-mui-near-me',
    name: 'Lăn Khử Mùi Near Me',
    categorySlug: 'cham-soc-ca-nhan',
    price: 0,
    image: '/images/products/lan-khu-mui-near-me.webp',
    gallery: [
      '/images/products/lan-khu-mui-near-me.webp',
      '/images/products/lan-khu-mui-near-me-2.webp',
      '/images/products/lan-khu-mui-near-me-3.webp',
    ],
    showDisclaimer: false,
    shortDescription:
      '<p>Lăn khử mùi dạng serum cho vùng da dưới cánh tay, thương hiệu <strong>HUTA – Near Me</strong>.</p><p>Dung tích: <strong>10ml</strong>, đầu bi lăn.</p>',
    description: `<h2>Giới thiệu</h2>
<p>Near Me Underarms Deodorant Serum (thương hiệu HUTA) là sản phẩm chăm sóc cá nhân dạng serum lăn, dùng cho vùng da dưới cánh tay.</p>
<h2>Thông tin sản phẩm</h2>
<p>Dung tích: 10ml. Thiết kế nhỏ gọn, đầu bi lăn.</p>
<h2>Lưu ý</h2>
<p>Vui lòng đọc kỹ nhãn trên bao bì và hướng dẫn sử dụng. Ngừng dùng nếu da bị kích ứng. Liên hệ hotline để được tư vấn.</p>`,
    highlights: ['Thương hiệu: HUTA – Near Me', 'Dung tích: 10ml, đầu bi lăn'],
    inStock: true,
    featured: false,
    createdAt: '2026-11-17',
  },
  {
    id: 5,
    sku: '93',
    slug: 'ca-phe-bot',
    name: 'Cà phê bột G Land',
    categorySlug: 'ca-phe',
    price: 650000, // Giá khách chốt 18/11 (bản WordPress cũ ghi 100.000).
    image: '/images/products/ca-phe-bot.webp',
    gallery: ['/images/products/ca-phe-bot.webp', '/images/products/ca-phe-bot-2.webp'],
    shortDescription:
      '<p>Cà phê bột thương hiệu <strong>G Land</strong>, túi giấy <strong>500g</strong>.</p><p>Phân phối độc quyền bởi Sun Shines.</p>',
    description: `<h2>Giới thiệu</h2>
<p>Cà phê bột G Land (Premium quality), đóng túi giấy kraft, khối lượng tịnh 500g. Sản phẩm do Sun Shines phân phối độc quyền.</p>
<p>Thông tin chi tiết (nguồn gốc, hạn dùng, cách pha) sẽ được cập nhật. Liên hệ hotline để được tư vấn và báo giá.</p>`,
    highlights: ['Thương hiệu: G Land', 'Khối lượng tịnh: 500g', 'Phân phối độc quyền bởi Sun Shines'],
    inStock: true,
    featured: false,
    createdAt: '2026-11-17',
  },
  /*
   * 7 sản phẩm chuyển từ bản WordPress cũ (18/11): chỉ lấy tên, thành phần, quy cách và giá.
   * KHÔNG chép phần "công dụng" (nhiều câu về điều trị bệnh, trái quy định). Chưa có ảnh.
   * Giá giảm quá sâu (>60%) nghi nhập thử nên để 0 = "Liên hệ" cho tới khi khách xác nhận:
   * Ladi Ceek (cũ 90.000, gốc 1.200.000), Marine Omega (390.000/1.200.000),
   * Gland 3 tháng (90.000/2.253.000), Gland 1,5 tháng (390.000/2.253.000).
   */
  {
    id: 6,
    slug: 'da-day-minh-nguyet',
    name: 'Dạ Dày Minh Nguyệt',
    categorySlug: 'thuc-pham-bao-ve-suc-khoe',
    price: 650000,
    salePrice: 390000,
    gallery: [],
    shortDescription: '<p>Sản phẩm từ thảo dược dạng bột, thương hiệu <strong>Minh Nguyệt</strong>.</p>',
    description: `<h2>Thành phần</h2>
<p>Bột chè dây, bột khổ sâm cho lá, bột bồ công anh, bột dạ cẩm, bột hoàn ngọc, bột cây đuôi chuột, bột phèn đen, bột cam thảo, cao đặc lá khôi, bột rau mương.</p>
<h2>Lưu ý</h2>
<p>Vui lòng đọc kỹ nhãn trên bao bì và hướng dẫn sử dụng. Liên hệ hotline để được tư vấn cách dùng phù hợp. Người đang dùng thuốc điều trị nên hỏi ý kiến bác sĩ trước khi sử dụng.</p>
<p><strong>Sản phẩm này không phải là thuốc và không có tác dụng thay thế thuốc chữa bệnh.</strong></p>`,
    highlights: ['Thành phần từ thảo dược (chè dây, khổ sâm, bồ công anh, lá khôi…)'],
    inStock: true,
    featured: false,
    createdAt: '2026-11-18',
  },
  {
    id: 7,
    slug: 'vien-uong-ladi-gree',
    name: 'Viên uống Ladi Gree',
    categorySlug: 'thuc-pham-bao-ve-suc-khoe',
    price: 100000,
    salePrice: 90000,
    gallery: [],
    shortDescription: '<p>Viên uống từ thảo dược, thương hiệu <strong>Ladi</strong>.</p>',
    description: `<h2>Thành phần</h2>
<p>Sinh địa, huyền sâm, hương phụ, uất kim, ích mẫu, đỗ đen sao, thanh bì, chỉ thực, cam thảo và một số thành phần khác.</p>
<h2>Lưu ý</h2>
<p>Vui lòng đọc kỹ nhãn trên bao bì và hướng dẫn sử dụng. Phụ nữ mang thai, đang cho con bú và người đang dùng thuốc điều trị nên hỏi ý kiến bác sĩ trước khi sử dụng. Liên hệ hotline để được tư vấn.</p>
<p><strong>Sản phẩm này không phải là thuốc và không có tác dụng thay thế thuốc chữa bệnh.</strong></p>`,
    highlights: ['Thành phần từ thảo dược (sinh địa, huyền sâm, ích mẫu…)'],
    inStock: true,
    featured: false,
    createdAt: '2026-11-18',
  },
  {
    id: 8,
    slug: 'dung-dich-ve-sinh-t-ladi',
    name: 'Dung dịch vệ sinh T-Ladi',
    categorySlug: 'cham-soc-ca-nhan',
    price: 100000,
    gallery: [],
    showDisclaimer: false,
    shortDescription: '<p>Dung dịch vệ sinh cá nhân hằng ngày, thương hiệu <strong>Ladi</strong>. Phù hợp cho cả nam và nữ.</p>',
    description: `<h2>Giới thiệu</h2>
<p>Dung dịch vệ sinh T-Ladi dùng để vệ sinh cá nhân hằng ngày, dành cho cả nam và nữ.</p>
<h2>Thành phần</h2>
<p>Aqua, Sodium, chiết xuất quả Cranberry, Glycol distearate, Lactobacillus, Alpha Glucan, Aloe Barbadensis Leaf Extract (chiết xuất lô hội), Glycerin, Polyquaternium 39, Citric acid, Alcohol, Sodium Benzoate, Potassium Sorbate, Parfum.</p>
<h2>Lưu ý</h2>
<p>Chỉ dùng ngoài da. Ngừng dùng nếu da bị kích ứng. Vui lòng đọc kỹ nhãn trên bao bì. Liên hệ hotline để được tư vấn.</p>`,
    highlights: ['Dùng cho vệ sinh cá nhân hằng ngày, cả nam và nữ'],
    inStock: true,
    featured: false,
    createdAt: '2026-11-18',
  },
  {
    id: 9,
    slug: 'vien-dat-ladi-ceek',
    name: 'Viên đặt Ladi Ceek',
    categorySlug: 'cham-soc-ca-nhan',
    price: 0, // Cũ: 90.000 (gốc 1.200.000), nghi nhập thử, chờ khách xác nhận.
    gallery: [],
    shortDescription: '<p>Sản phẩm chiết xuất từ thảo dược, thương hiệu <strong>Ladi</strong>.</p>',
    description: `<h2>Thành phần</h2>
<p>Bạch chỉ, chiết xuất nghệ tây, Galla Chinensis, ngũ vị tử, Cornus officinalis, hạt hướng dương, phong lữ, Phellodendron Phellodendri, tơ hồng, Evodia, ngải cứu.</p>
<h2>Lưu ý</h2>
<p>Vui lòng đọc kỹ nhãn trên bao bì và hướng dẫn sử dụng. Phụ nữ mang thai, đang cho con bú và người đang dùng thuốc điều trị nên hỏi ý kiến bác sĩ trước khi sử dụng. Liên hệ hotline để được tư vấn.</p>
<p><strong>Sản phẩm này không phải là thuốc và không có tác dụng thay thế thuốc chữa bệnh.</strong></p>`,
    highlights: ['Chiết xuất từ thảo dược (bạch chỉ, nghệ tây, ngũ vị tử, ngải cứu…)'],
    inStock: true,
    featured: false,
    createdAt: '2026-11-18',
  },
  {
    id: 10,
    slug: 'marine-omega-120-vien',
    name: 'Thực phẩm bảo vệ sức khỏe Marine Omega – Hộp 120 viên',
    categorySlug: 'thuc-pham-bao-ve-suc-khoe',
    price: 0, // Cũ: 390.000 (gốc 1.200.000), nghi nhập thử, chờ khách xác nhận.
    gallery: [],
    shortDescription: '<p>Thực phẩm bảo vệ sức khỏe dạng viên nang bổ sung <strong>Omega-3</strong> và dầu nhuyễn thể Krill Oil.</p><p>Quy cách: <strong>hộp 120 viên</strong>.</p>',
    description: `<h2>Thành phần</h2>
<p>Dầu cá, axit béo Omega-3 (EPA, DHA) và các axit béo Omega-3 khác, gelatin, glycerin, nước, dầu nhuyễn thể Krill Oil, tinh dầu chanh tự nhiên, vitamin E, màu caramel.</p>
<h2>Quy cách</h2>
<p>Hộp 120 viên.</p>
<h2>Lưu ý</h2>
<p>Vui lòng đọc kỹ nhãn trên bao bì và hướng dẫn sử dụng. Người đang dùng thuốc điều trị nên hỏi ý kiến bác sĩ trước khi sử dụng. Liên hệ hotline để được tư vấn.</p>
<p><strong>Sản phẩm này không phải là thuốc và không có tác dụng thay thế thuốc chữa bệnh.</strong></p>`,
    highlights: ['Bổ sung Omega-3 (EPA, DHA) và dầu nhuyễn thể Krill Oil', 'Quy cách: hộp 120 viên'],
    inStock: true,
    featured: false,
    createdAt: '2026-11-18',
  },
  {
    id: 11,
    slug: 'bo-ca-phe-gland-3-thang',
    name: 'Bộ cà phê Gland 3 tháng',
    categorySlug: 'ca-phe',
    price: 0, // Cũ: 90.000 (gốc 2.253.000), nghi nhập thử, chờ khách xác nhận.
    gallery: [],
    shortDescription: '<p>Bộ sản phẩm cà phê <strong>Gland</strong>, liệu trình dùng 3 tháng.</p>',
    description: `<h2>Giới thiệu</h2>
<p>Bộ sản phẩm cà phê Gland, liệu trình 3 tháng. Theo thông tin trên trang cũ, sản phẩm sử dụng hạt cà phê hữu cơ, sản xuất theo tiêu chuẩn hữu cơ của Mỹ (USA Organic) (cần xác nhận lại).</p>
<p>Liên hệ hotline để được tư vấn và báo giá.</p>`,
    highlights: ['Bộ sản phẩm liệu trình 3 tháng'],
    inStock: true,
    featured: false,
    createdAt: '2026-11-18',
  },
  // Combo 4 hộp Thảo Mộc Mogan: giá khách chốt 18/11 (1.480.000 cho 4 hộp, tương đương 370.000 ₫/hộp).
  {
    id: 13,
    slug: 'combo-4-hop-thao-moc-mogan',
    name: 'Combo 4 hộp Thảo Mộc Mogan',
    categorySlug: 'thao-moc-mogan',
    price: 1480000,
    image: '/images/products/thao-moc-mogan.webp',
    gallery: ['/images/products/thao-moc-mogan.webp', '/images/products/thao-moc-mogan-2.webp'],
    shortDescription:
      '<p>Combo <strong>4 hộp</strong> trà thảo mộc túi lọc Thảo Mộc Mogan (mỗi hộp 30 gói × 5g).</p><p>Tương đương <strong>370.000 ₫/hộp</strong>.</p>',
    description: `<h2>Thành phần</h2>
<p>Cà gai leo, diệp hạ châu, xạ đen, mướp đắng, lá đơn.</p>
<h2>Quy cách</h2>
<p>Combo 4 hộp, mỗi hộp 30 gói × 5g (túi lọc).</p>
<h2>Hướng dẫn sử dụng</h2>
<p>Cho 3 túi lọc vào bình giữ nhiệt, ấm hoặc cốc, đổ 0,5 – 1 lít nước sôi vào. Đợi 5 – 10 phút là có thể dùng.</p>
<p>Nên dùng sau ăn khoảng 30 phút và không dùng khi bụng quá đói.</p>
<h2>Hướng dẫn bảo quản</h2>
<p>Bảo quản nơi khô ráo, thoáng mát, tránh ánh nắng trực tiếp.</p>
<h2>Cảnh báo</h2>
<p>Không sử dụng sản phẩm quá hạn hoặc bao bì không còn nguyên vẹn.</p>
<p><strong>Sản phẩm này không phải là thuốc và không có tác dụng thay thế thuốc chữa bệnh.</strong></p>`,
    highlights: [
      'Combo 4 hộp, mỗi hộp 30 gói × 5g, dạng túi lọc tiện dùng',
      'Tương đương 370.000 ₫/hộp',
      'Thành phần: cà gai leo, diệp hạ châu, xạ đen, mướp đắng, lá đơn',
    ],
    inStock: true,
    featured: false,
    createdAt: '2026-11-18',
  },
  {
    id: 12,
    slug: 'bo-ca-phe-gland-1-5-thang',
    name: 'Bộ cà phê Gland 1,5 tháng',
    categorySlug: 'ca-phe',
    price: 0, // Cũ: 390.000 (gốc 2.253.000), nghi nhập thử, chờ khách xác nhận.
    gallery: [],
    shortDescription: '<p>Bộ sản phẩm cà phê <strong>Gland</strong>, liệu trình dùng 1,5 tháng.</p>',
    description: `<h2>Giới thiệu</h2>
<p>Bộ sản phẩm cà phê Gland, liệu trình 1,5 tháng. Theo thông tin trên trang cũ, sản phẩm sử dụng hạt cà phê hữu cơ, sản xuất theo tiêu chuẩn hữu cơ của Mỹ (USA Organic) (cần xác nhận lại).</p>
<p>Liên hệ hotline để được tư vấn và báo giá.</p>`,
    highlights: ['Bộ sản phẩm liệu trình 1,5 tháng'],
    inStock: true,
    featured: false,
    createdAt: '2026-11-18',
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

/** Sản phẩm đã có giá. */
export function hasPrice(p: Product) {
  return currentPrice(p) > 0;
}

/** Có thể thêm vào giỏ / đặt hàng (đã có giá và còn hàng). */
export function isBuyable(p: Product) {
  return hasPrice(p) && p.inStock;
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
  // Ít hơn 4 sản phẩm nổi bật thì lấy thêm sản phẩm khác (nổi bật xếp trước, rồi mới nhất).
  const list =
    featured.length >= 4
      ? featured
      : [...products].sort((a, b) => Number(b.featured) - Number(a.featured) || b.createdAt.localeCompare(a.createdAt));
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
    // Sản phẩm chưa có giá không nằm trong bộ lọc theo giá.
    if ((q.min != null || q.max != null) && price <= 0) return false;
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
    // Sắp xếp theo giá: sản phẩm chưa có giá xếp cuối.
    case 'price':
      list = [...list].sort((a, b) => (currentPrice(a) || Infinity) - (currentPrice(b) || Infinity));
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
