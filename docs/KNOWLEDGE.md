# Kiến thức dự án – Website Sun Shines / Thảo Mộc Mogan

Tài liệu gốc về dự án: thông tin khách hàng, các quyết định đã chốt, kiến trúc code, cách làm việc và các lưu ý.
Ai tham gia dự án (người hoặc AI) nên đọc file này trước. Tiến độ hằng ngày xem [`../TIEN-DO.md`](../TIEN-DO.md), kế hoạch xem [`PLAN.md`](PLAN.md).

---

## 1. Khách hàng & thương hiệu

| Mục | Giá trị |
|---|---|
| Công ty | **CÔNG TY TNHH THƯƠNG MẠI DỊCH VỤ SUN SHINES** |
| Thương hiệu hiển thị trên web | **Sun Shines** (logo chữ in hoa "SUN SHINES") |
| Slogan | *Trao sức khỏe, Gửi yêu thương* |
| Dòng sản phẩm | **Thảo Mộc Mogan** (hiện chỉ có 1 sản phẩm) |
| Hotline | 0868 800 440 |
| Zalo | 0868800440 (đang để trùng hotline, **chưa xác nhận**) |
| Địa chỉ | 389/3/23 Quốc lộ 13, Khu phố 24, Phường Hiệp Bình, TP. Hồ Chí Minh, Việt Nam |
| Email | **chưa có** |
| Fanpage 1 (Sun Shines) | https://www.facebook.com/share/19uVvCc2Fd/ |
| Fanpage 2 (Thảo Mộc Mogan) | https://www.facebook.com/share/1Guucfskuf/ |
| Màu thương hiệu | Xanh lá (theo logo: xanh lá chuyển sắc `#1F9A43 → #45C95A`, điểm nhấn vàng chanh `#D9E021`) |
| Hosting | **iNET** (Công ty TNHH Phần mềm iNET, inet.vn), thường dùng DirectAdmin; đã mua 09/11, chưa cài WordPress |
| Quản lý bán hàng | Pancake POS (chưa kết nối API, khách không cung cấp API key) |
| GitHub | https://github.com/hoanghon1508-gif/sunshines-website (nhánh `main`) |
| Figma | https://www.figma.com/design/ZuQnD2KZLclZsbiW2IsxdY (**tạm dừng**, xem mục 3) |

### Sản phẩm hiện có

**Thảo Mộc Mogan**, hộp 30 gói × 5g (túi lọc), **390.000 ₫**, không giảm giá.

- Thành phần: Cà gai leo, diệp hạ châu, xạ đen, mướp đắng, lá đơn.
- Hướng dẫn sử dụng: Cho 3 túi lọc vào bình giữ nhiệt, ấm hoặc cốc, đổ 0,5 – 1 lít nước sôi vào. Đợi 5 – 10 phút là có thể dùng. Nên dùng sau ăn khoảng 30 phút và không dùng khi bụng quá đói.
- Bảo quản: Nơi khô ráo, thoáng mát, tránh ánh nắng trực tiếp.
- Cảnh báo: Không sử dụng sản phẩm quá hạn hoặc bao bì không còn nguyên vẹn.
- Nguồn thông tin: chữ in trên bao bì (ảnh `wp-content/themes/xanh/assets/images/thao-moc-mogan.jpg`).

> **Bắt buộc về pháp lý:** mọi chỗ giới thiệu sản phẩm phải có câu
> *"Sản phẩm này không phải là thuốc và không có tác dụng thay thế thuốc chữa bệnh."*
> Khách đã xác nhận phải ghi. **Không** viết nội dung quảng cáo công dụng chữa bệnh (gan, mỡ máu…) khi khách chưa có giấy phép quảng cáo.

---

## 2. Phạm vi hợp đồng (báo giá)

Gói **"Thiết kế website theo yêu cầu – Gói nâng cao"**: **18.000.000 ₫** (đã gồm VAT), nền tảng **WordPress**.
Nguồn: Google Sheet khách gửi (`docs.google.com/spreadsheets/d/1covbWsZqUHmIr7nKLZILHlbDFmKK9k0SIacNp6hOPoQ`, tab `gid=1617448825`).

- **Trang:** Trang chủ, Giới thiệu, Danh mục sản phẩm, Chi tiết sản phẩm, Danh sách tin tức, Chi tiết tin tức, Liên hệ, Giỏ hàng/Thanh toán, Trang đơn giản (chính sách).
- **Module:** CMS, Banner (ảnh/link/thứ tự/bật-tắt), E-commerce (danh mục, chi tiết, slide ảnh, giá gốc/giá giảm/% sale, đăng ký tư vấn, giỏ hàng & thanh toán), Form liên hệ, Liên kết mạng xã hội, Phân quyền admin, Bảo mật, Quick contact (gọi, Zalo).
- **Cam kết:** tốc độ, chuẩn mobile, SEO kỹ thuật, bảo mật, bảo hành 12 tháng.
- **Cổng thanh toán:** gói gồm 1 cổng (VNPay **hoặc** Casso); thêm cổng: +2.000.000 ₫/cổng. **Khách yêu cầu chưa làm** ở giai đoạn này.
- **Ngoài báo giá:** tên miền, hosting, email doanh nghiệp, API trả phí, viết nội dung, dịch thuật, chụp ảnh, app, loyalty…

Bảng đối chiếu từng hạng mục với code: xem `README.md`, mục 1.

---

## 3. Các quyết định đã chốt (nhật ký)

| Ngày | Quyết định | Lý do / ghi chú |
|---|---|---|
| 08/11 | WordPress + WooCommerce, theme riêng + plugin chức năng riêng | Theo báo giá; tách chức năng khỏi giao diện để đổi theme không mất dữ liệu |
| 08/11 | Chưa làm cổng thanh toán online; checkout dùng COD + chuyển khoản | Khách yêu cầu |
| 08/11 | Code đặt tên kỹ thuật `xanh` / `xanh-core` (tên tạm trước khi có thương hiệu); tên hiển thị "Sun Shines" / "Sun Shines Core" | Đổi slug thư mục sẽ phá đường dẫn và tuỳ biến, nên giữ nguyên |
| 08/11 | Sun Shines là thương hiệu chính; Thảo Mộc Mogan là dòng sản phẩm | Theo slogan "Sun Shines – Trao sức khỏe, Gửi yêu thương" |
| 08/11 | Tạm thời chỉ bán dòng Mogan; trang chủ ẩn khối danh mục khi có < 2 danh mục | Khách: "tạm thời chỉ để Mogan" |
| 08/11 | Không lấy dữ liệu trực tiếp từ Pancake POS | Khách từ chối cung cấp API key; Claude không truy cập được trình duyệt của khách |
| 09/11 | 1 sản phẩm Thảo Mộc Mogan 390.000 ₫; trang chủ hiện khối "spotlight" khi chỉ có 1 sản phẩm, có từ 2 sản phẩm thì tự chuyển sang lưới | Khách: "tạm thời thay sp này trước" |
| 09/11 | Mobile: thanh liên hệ cố định ở đáy (Gọi \| Zalo) thay nút nổi | Nút nổi che nội dung khi chạy thử |
| 09/11 | SĐT bắt buộc khi thanh toán; ẩn công ty và địa chỉ 2 | Giao hàng ở Việt Nam cần SĐT |
| 09/11 | Hiển thị câu "không phải là thuốc" ở trang sản phẩm, spotlight, mô tả, chân trang | Khách xác nhận bắt buộc |
| 09/11 | **Tạm dừng Figma**, làm và duyệt trực tiếp trên WordPress bằng ảnh chụp màn hình | Gói Figma Starter hết lượt MCP, khách không nâng cấp |
| 09/11 | Logo tạm: cắt phần biểu tượng từ ảnh JPG khách gửi, tự tách nền | Chờ file logo gốc PNG trong suốt hoặc SVG |

---

## 4. Kiến trúc code

```
sunshines-website/
├── CLAUDE.md                     # Hướng dẫn cho phiên AI
├── README.md                     # Tổng quan, đối chiếu báo giá, hướng dẫn cài & quản trị
├── TIEN-DO.md                    # Checklist tiến độ
├── docs/KNOWLEDGE.md, PLAN.md    # (file này) + kế hoạch
├── docker-compose.yml            # Chạy thử bằng Docker (MariaDB + WordPress + WP-CLI)
├── bin/
│   ├── setup-site.sh             # Cấu hình site bằng WP-CLI (tiếng Việt, VNĐ, trang, menu, sản phẩm…)
│   └── seed-product.php          # Tạo/cập nhật sản phẩm Thảo Mộc Mogan (wp eval-file), chạy lại an toàn
├── tools/test-local/             # Chạy thử không cần Docker/MySQL (PHP built-in server + SQLite)
└── wp-content/
    ├── plugins/xanh-core/        # "Sun Shines Core": chức năng, độc lập với giao diện
    │   └── includes/
    │       ├── class-settings.php     # Trang "Cài đặt website" (option `xanh_settings`)
    │       ├── class-roles.php        # Cap `manage_xanh_leads`, `manage_xanh_banners`; role `xanh_sales`
    │       ├── class-banners.php      # CPT `xanh_banner` + meta _xanh_location/_xanh_link/_xanh_active…
    │       ├── class-leads.php        # CPT `xanh_lead`, xử lý form (admin-post action `xanh_lead`), CSV
    │       ├── class-security.php     # Giới hạn đăng nhập, chặn dò user, tắt XML-RPC, headers
    │       ├── class-seo.php          # Meta title/desc, OG, canonical, Schema Org/FAQ, robots.txt
    │       ├── class-performance.php  # WebP, bỏ emoji, dequeue CSS block
    │       └── functions.php          # API cho theme: xanh_setting(), xanh_banners(), xanh_lead_form()…
    └── themes/xanh/              # "Sun Shines": giao diện (classic theme, không phụ thuộc jQuery)
        ├── assets/css/main.css        # Toàn bộ CSS; biến màu ở :root
        ├── assets/js/main.js          # Menu mobile, slider, bộ lọc
        ├── assets/images/             # logo-mark.webp/png, logo-full, site-icon.png, thao-moc-mogan.jpg/webp
        ├── inc/setup.php              # theme supports, menus, enqueue
        ├── inc/template-tags.php      # xanh_opt(), xanh_logo(), xanh_disclaimer(), breadcrumbs, pagination
        ├── inc/woocommerce.php        # % giảm giá, form tư vấn, bộ lọc shop, trường checkout VN
        ├── inc/icons.php              # Icon SVG inline: xanh_icon()
        ├── front-page.php             # Trang chủ
        ├── woocommerce.php            # Khung cho shop/danh mục/chi tiết sản phẩm
        ├── woocommerce/content-product.php   # Thẻ sản phẩm (override WC 9.4.0)
        ├── page-templates/template-contact.php
        └── template-parts/            # card-post, quick-contact, product-spotlight
```

### Luồng dữ liệu chính

- **Cài đặt website** (`xanh_settings`) → theme đọc qua `xanh_opt( $key, $fallback )`. Giá trị mặc định (tên công ty, hotline, địa chỉ, fanpage, slogan, câu lưu ý) nằm trong `Xanh_Settings::defaults()`.
- **Banner:** `xanh_banners( 'home_slider' | 'home_middle' | 'shop_top' | 'sidebar' )` trả về banner đang bật, đã sắp xếp theo `menu_order`.
- **Form khách hàng:** `xanh_lead_form( 'contact' | 'consult' | 'promo', $args )` gửi tới `admin-post.php?action=xanh_lead`, lưu thành CPT `xanh_lead`, gửi email thông báo, rồi redirect về `?xanh_lead=success|invalid|limit|captcha|error#anchor`. Thông báo hiển thị bằng `xanh_lead_notice()`.
- **Chống spam form** (không dùng nonce để chạy được khi trang bị cache): honeypot `xanh_website`, time-trap có chữ ký HMAC `xanh_ts` (tối thiểu 3 giây), giới hạn 5 lần / 10 phút / IP, reCAPTCHA v3 tuỳ chọn.
- **Trang chủ:** có 1 sản phẩm thì hiện `template-parts/product-spotlight.php`; có từ 2 sản phẩm thì hiện lưới `ul.products`.

### Quy ước code

- Prefix: hàm/option/meta `xanh_` / `_xanh_`; class `Xanh_`; text domain `xanh` (theme), `xanh-core` (plugin).
- Theo WordPress Coding Standards: escape mọi output, sanitize mọi input, kiểm tra nonce và quyền cho thao tác quản trị.
- Theme phải chạy được khi plugin tắt (kiểm tra `function_exists`).
- Comment và chuỗi giao diện viết **tiếng Việt**.
- Không đưa model AI hay thông tin bí mật vào code hoặc commit.

---

## 5. Cách chạy thử & kiểm tra

**Docker** (máy có Docker): xem `README.md` mục 2.

**Không có Docker** (dùng trong phiên cloud của Claude): xem `tools/test-local/README.md`. WordPress core lấy qua Composer (`johnpbloch/wordpress-core`), WooCommerce lấy từ GitHub release, cơ sở dữ liệu SQLite (`WordPress/sqlite-database-integration`), chạy bằng `php -S` với router.

Checklist kiểm tra sau mỗi thay đổi:
1. `php -l` cho mọi file PHP vừa sửa; `bash -n bin/setup-site.sh`.
2. Mở các URL chính (trang chủ, /san-pham/, chi tiết sản phẩm, /gio-hang/, /thanh-toan/, /tin-tuc/, /lien-he/, 404) và xem `debug.log` không có lỗi PHP.
3. Playwright: chụp desktop 1440px và iPhone 13; kiểm tra không có lỗi JS và không tràn ngang.
4. Gửi thử form liên hệ (chờ hơn 3 giây) và xác nhận khách được lưu vào `xanh_lead`.

---

## 6. Lưu ý & bẫy đã gặp

- **CSS WooCommerce đè ảnh logo:** `.woocommerce img` làm logo phóng to; phải dùng selector `.site-header .logo__mark` kèm `!important`.
- **Gói tiếng Việt:** máy cloud không tải được bản dịch từ wordpress.org nên chữ WooCommerce còn tiếng Anh. Trên hosting, `setup-site.sh` chạy `wp language core install vi` và `wp language plugin install woocommerce vi`.
- **Mạng của phiên cloud:** wordpress.org, facebook.com, docs pancake bị chặn. Đã cho phép: `docs.google.com`, `www.facebook.com`, `mcp.figma.com`. WP-CLI phar tải từ ngoài bị chế độ an toàn chặn; dùng script PHP trong `tools/test-local` thay thế.
- **GitHub từ phiên cloud:** không tạo repo được (chỉ thao tác repo đã gắn). Push cần Claude GitHub App được cài cho repo. Thông báo `expected 'acknowledgments'` khi push là vô hại.
- **Figma MCP (gói Starter):** giới hạn lượt gọi rất thấp; lệnh lỗi giữa chừng có thể để file ở trạng thái dở.
- **Ảnh khách gửi trong chat** nằm ở thư mục `images/` của phiên cloud (không phải trên máy khách).
- **Checkout block của WooCommerce:** tuỳ chỉnh trường bằng option `woocommerce_checkout_phone_field` (required), `woocommerce_checkout_company_field` / `woocommerce_checkout_address_2_field` (hidden).
- **Nút "Mua ngay"** dùng link `thanh-toan/?add-to-cart=ID`; tải lại trang đó sẽ thêm sản phẩm lần nữa (hành vi mặc định của WooCommerce).
