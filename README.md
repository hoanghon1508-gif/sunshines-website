# Website Sun Shines – Thảo Mộc Mogan (WordPress + WooCommerce)

**CÔNG TY TNHH THƯƠNG MẠI DỊCH VỤ SUN SHINES** · *Sun Shines – Trao sức khỏe, Gửi yêu thương*

Website bán hàng tông xanh lá cho thương hiệu Sun Shines và dòng sản phẩm Thảo Mộc Mogan, làm theo gói "Thiết kế website theo yêu cầu – Gói nâng cao".

- **Thiết kế UI (Figma):** https://www.figma.com/design/ZuQnD2KZLclZsbiW2IsxdY
- **Nền tảng:** WordPress 6.5+ · WooCommerce · PHP 8.1+
- **Tài liệu dự án:** [`docs/KNOWLEDGE.md`](docs/KNOWLEDGE.md) (kiến thức, quyết định, kiến trúc) · [`docs/PLAN.md`](docs/PLAN.md) (kế hoạch) · [`TIEN-DO.md`](TIEN-DO.md) (tiến độ)
- **Mã nguồn trong repo:**
  - `wp-content/themes/xanh`: theme giao diện (desktop, tablet, mobile)
  - `wp-content/plugins/xanh-core`: plugin chức năng (banner, form khách hàng, cài đặt, phân quyền, bảo mật, SEO)
  - `bin/setup-site.sh`: script tự cấu hình website (tiếng Việt, VNĐ, các trang, menu, dữ liệu mẫu)
  - `docker-compose.yml`: môi trường chạy thử trên máy
  - `tools/test-local`: chạy thử không cần Docker (PHP + SQLite)

> Tên công ty, slogan và 2 fanpage đã điền sẵn. Hotline, địa chỉ và Zalo đã điền sẵn. Email, sản phẩm và hình ảnh hiện là **dữ liệu mẫu**, chỉnh trong trang quản trị (*Cài đặt website*, *Sản phẩm*). Thư mục code vẫn mang tên kỹ thuật `xanh`/`xanh-core`; tên hiển thị là "Sun Shines".

---

## 1. Đối chiếu với báo giá

### Các trang

| Trang | Template / nơi quản lý |
|---|---|
| Trang chủ | `front-page.php` (slider banner, cam kết, danh mục, sản phẩm nổi bật, form tư vấn, tin tức) |
| Trang giới thiệu | `page.php`, soạn nội dung trong *Trang → Giới thiệu* |
| Trang danh mục sản phẩm | `woocommerce.php` (bộ lọc danh mục, khoảng giá, đang giảm giá; sắp xếp; phân trang) |
| Trang chi tiết sản phẩm | WooCommerce + `inc/woocommerce.php` (slide ảnh, giá gốc/giá giảm/% sale, mô tả tóm tắt, form đăng ký tư vấn, sản phẩm liên quan) |
| Trang danh sách tin tức | `index.php` (lọc theo chuyên mục, phân trang) |
| Trang chi tiết tin tức | `single.php` (bài liên quan, chia sẻ Facebook) |
| Trang liên hệ | `page-templates/template-contact.php` (thông tin, form, Google Maps) |
| Giỏ hàng, thanh toán | WooCommerce Cart/Checkout, đã đổi giao diện sang tông xanh |
| Trang đơn giản (chính sách) | `page.php` |

### Tính năng quản trị

| Module | Vị trí trong trang quản trị |
|---|---|
| CMS: trang, danh mục, bài viết, thư viện ảnh/video | WordPress có sẵn: *Trang*, *Bài viết*, *Chuyên mục*, *Media* |
| Quản lý banner: ảnh, link đích, sắp xếp thứ tự, bật/tắt, vị trí | *Banner* (plugin xanh-core) |
| E-commerce: danh mục, chi tiết sản phẩm, slide ảnh, giá gốc/giá giảm/% sale | *Sản phẩm* (WooCommerce) |
| Đăng ký nhận tư vấn về sản phẩm | Form trên trang chi tiết; danh sách lưu ở *Khách hàng (form)* |
| Giỏ hàng & thanh toán | WooCommerce: COD + chuyển khoản ngân hàng |
| Quản lý form liên hệ | *Khách hàng (form)*: trạng thái (Mới / Đã liên hệ / Hoàn tất), ghi chú nội bộ, tìm theo SĐT, **xuất Excel (CSV)**, email thông báo |
| Liên kết mạng xã hội | *Cài đặt website* → Facebook, YouTube, Instagram, TikTok, Zalo |
| Phân quyền user admin | *Thành viên*: Administrator, Shop Manager, Editor và vai trò mới **Nhân viên tư vấn** (chỉ xem/xử lý khách hàng form) |
| Bảo mật hệ thống | Xem mục 4 |
| Quick contact (gọi điện, Zalo, Messenger) | *Cài đặt website* → Nút liên hệ nhanh |

### Cam kết kỹ thuật

- **Tốc độ:** ảnh tải lên tự chuyển sang WebP (nếu hosting hỗ trợ). Ảnh lazy-load, riêng ảnh banner đầu trang được ưu tiên tải trước (LCP). JS thuần không cần jQuery, nạp `defer`. Đã bỏ emoji script và CSS block không dùng. Icon dùng SVG inline. Cache trình duyệt cấu hình bằng `.htaccess` (mục 5).
- **Chuẩn mobile:** responsive cho desktop, tablet và mobile. Menu trượt, bộ lọc thu gọn, nút bấm cao tối thiểu 44px, form tối ưu cho bàn phím điện thoại (`type=tel`, `autocomplete`).
- **SEO kỹ thuật:** HTML ngữ nghĩa, mỗi trang một H1, Meta Title và Meta Description (ô *SEO* dưới mỗi bài, trang, sản phẩm), URL tiếng Việt không dấu, sitemap `/wp-sitemap.xml`, robots.txt, Open Graph, canonical, Schema Organization, BreadcrumbList và FAQ (shortcode `[xanh_faq]`). Nếu cài Yoast SEO hoặc Rank Math, phần meta của theme tự tắt để không bị trùng.
- **Bảo mật:** xem mục 4.

### Chưa làm (theo yêu cầu)

- **Cổng thanh toán online (VNPay / Casso):** để giai đoạn sau. Hiện checkout có COD và chuyển khoản.

---

## 2. Chạy thử trên máy (Docker)

Yêu cầu: Docker Desktop.

```bash
docker compose up -d
docker compose run --rm cli bash /scripts/setup-site.sh --demo
```

Mở http://localhost:8080. Trang quản trị ở http://localhost:8080/wp-admin, tài khoản `admin` / `admin123`.

Code theme và plugin được gắn trực tiếp vào container, sửa file là thấy thay đổi ngay.

## 3. Đưa lên hosting thật

1. Cài WordPress mới trên hosting (đa số hosting có nút cài 1-click).
2. Tải 2 thư mục lên qua FTP hoặc File Manager:
   - `wp-content/themes/xanh` → `wp-content/themes/`
   - `wp-content/plugins/xanh-core` → `wp-content/plugins/`
3. Nếu hosting có WP-CLI (SSH):
   ```bash
   SITE_URL=https://tenmien.vn ADMIN_USER=<tài-khoản-admin> bash setup-site.sh
   ```
   (bỏ `--demo` để không tạo dữ liệu mẫu). Nếu không có WP-CLI thì làm thủ công: cài WooCommerce, kích hoạt plugin **XANH Core** và theme **XANH**, rồi vào *Cài đặt → Đọc* chọn trang chủ tĩnh "Trang chủ", trang bài viết "Tin tức".
4. Vào **Cài đặt website**, điền hotline, địa chỉ, email, Zalo, mạng xã hội, link Google Maps, reCAPTCHA (nếu dùng).
5. Vào **Giao diện → Tuỳ biến → Nhận dạng website** để tải logo lên.
6. Bật SSL (Let's Encrypt) trên hosting, rồi đổi địa chỉ website sang `https://`.

## 4. Bảo mật đã cấu hình

- Giới hạn đăng nhập sai: 5 lần trong 15 phút theo IP. Thông báo lỗi chung chung để không lộ tên đăng nhập có tồn tại hay không.
- Chặn dò tên đăng nhập (`?author=`, REST `/wp/v2/users` cho khách).
- Tắt XML-RPC, ẩn phiên bản WordPress, tắt sửa code trong trang quản trị (`DISALLOW_FILE_EDIT`).
- Header bảo mật: `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`, `Permissions-Policy`, và HSTS khi chạy HTTPS.
- Form chống spam: honeypot, time-trap có chữ ký, giới hạn 5 lần / 10 phút / IP, reCAPTCHA v3 tuỳ chọn.
- Mọi dữ liệu nhập vào đều được làm sạch, mọi dữ liệu xuất ra đều được escape. Thao tác quản trị kiểm tra nonce và quyền. File CSV xuất ra được chống CSV injection.

Khuyến nghị thêm khi lên hosting: đặt mật khẩu admin mạnh, bật backup tự động của hosting, cập nhật WordPress và plugin định kỳ.

## 5. `.htaccess` gợi ý cho cache và nén (Apache/LiteSpeed)

Thêm vào **trên** khối `# BEGIN WordPress`:

```apache
<IfModule mod_expires.c>
  ExpiresActive On
  ExpiresByType image/webp "access plus 1 year"
  ExpiresByType image/avif "access plus 1 year"
  ExpiresByType image/jpeg "access plus 1 year"
  ExpiresByType image/png "access plus 1 year"
  ExpiresByType image/svg+xml "access plus 1 year"
  ExpiresByType text/css "access plus 1 year"
  ExpiresByType application/javascript "access plus 1 year"
  ExpiresByType font/woff2 "access plus 1 year"
</IfModule>
<IfModule mod_deflate.c>
  AddOutputFilterByType DEFLATE text/html text/css application/javascript application/json image/svg+xml
</IfModule>
```

Nên cài thêm một plugin cache trang (LiteSpeed Cache nếu hosting dùng LiteSpeed, hoặc WP Super Cache). Form của theme vẫn hoạt động bình thường khi trang được cache.

## 6. Hướng dẫn nhanh cho người quản trị

- **Thêm banner:** *Banner → Thêm banner*. Nhập tiêu đề, mô tả, link, chữ trên nút, chọn vị trí, đặt ảnh đại diện, nhập thứ tự (ô *Thuộc tính → Thứ tự*), tick *Bật banner*. Ở danh sách banner có nút **Bật/Tắt** nhanh.
- **Thêm sản phẩm:** *Sản phẩm → Thêm mới*. Nhập giá gốc và giá khuyến mãi, % giảm sẽ tự tính. Thêm ảnh vào *Album hình ảnh sản phẩm* để có slide ảnh. Tick ⭐ để sản phẩm hiện ở mục "Sản phẩm nổi bật" trên trang chủ.
- **Xem khách hàng gửi form:** *Khách hàng (form)*. Số trên menu là số khách hàng mới. Bấm vào một khách để xem chi tiết, đổi trạng thái và ghi chú. Nút **Xuất Excel (CSV)** ở đầu danh sách.
- **Câu hỏi thường gặp (có Schema FAQ):** trong nội dung trang, thêm khối Shortcode:
  ```
  [xanh_faq]
  Q: Bao lâu thì nhận được hàng?
  A: Từ 2–4 ngày làm việc.
  Q: Có được đổi trả không?
  A: Được đổi trả trong 7 ngày.
  [/xanh_faq]
  ```

## 7. Cấu trúc thư mục

```
wp-content/
├── plugins/xanh-core/
│   ├── xanh-core.php
│   └── includes/
│       ├── class-settings.php     # Trang "Cài đặt website"
│       ├── class-roles.php        # Phân quyền, vai trò "Nhân viên tư vấn"
│       ├── class-banners.php      # Module banner
│       ├── class-leads.php        # Module form khách hàng
│       ├── class-security.php     # Bảo mật
│       ├── class-seo.php          # SEO kỹ thuật
│       ├── class-performance.php  # Tối ưu tốc độ
│       └── functions.php          # Hàm dùng trong theme
└── themes/xanh/
    ├── assets/css/main.css        # Toàn bộ giao diện (biến màu ở :root)
    ├── assets/js/main.js          # Menu mobile, slider, bộ lọc
    ├── inc/                       # setup, icon, template tags, WooCommerce
    ├── page-templates/template-contact.php
    ├── template-parts/
    ├── woocommerce/content-product.php
    ├── front-page.php, index.php, single.php, page.php, woocommerce.php, 404.php
    ├── header.php, footer.php, searchform.php
    └── style.css, functions.php
```

**Đổi màu thương hiệu:** sửa các biến `--c-primary-*` ở đầu `assets/css/main.css`.
