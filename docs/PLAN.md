# Kế hoạch dự án – Website Sun Shines / Thảo Mộc Mogan

Cập nhật: 09/11/2026 · Trạng thái chi tiết từng việc: [`../TIEN-DO.md`](../TIEN-DO.md) · Kiến thức nền: [`KNOWLEDGE.md`](KNOWLEDGE.md)

```
Giai đoạn 1  Thiết kế & xây dựng      ██████████  XONG
Giai đoạn 2  Hosting & cài đặt        ░░░░░░░░░░  ĐANG CHỜ (khách đang thanh toán hosting)
Giai đoạn 3  Nội dung & hoàn thiện    ███░░░░░░░  MỘT PHẦN
Giai đoạn 4  Kiểm thử & ra mắt        ░░░░░░░░░░  CHƯA
Giai đoạn 5  Bàn giao & bảo hành      ░░░░░░░░░░  CHƯA
Giai đoạn 6  Mở rộng (ngoài báo giá)  ░░░░░░░░░░  CHƯA
```

---

## Giai đoạn 1: Thiết kế & xây dựng ✅

- [x] Thiết kế Figma: trang chủ, danh mục, chi tiết sản phẩm, giỏ hàng/thanh toán, liên hệ, mobile (tạm dừng từ 09/11)
- [x] Theme "Sun Shines" + plugin "Sun Shines Core", đủ các trang và module trong báo giá
- [x] Thông tin công ty, slogan, logo tạm, 2 fanpage, hotline, địa chỉ
- [x] Sản phẩm thật: Thảo Mộc Mogan 390.000 ₫ + câu lưu ý "không phải là thuốc"
- [x] Chạy thử trên WordPress thật (SQLite), sửa lỗi giao diện mobile và checkout
- [x] Code trên GitHub, tài liệu README / TIEN-DO / KNOWLEDGE / PLAN

## Giai đoạn 2: Hosting & cài đặt ⏳ (bước tiếp theo)

**Cần từ khách:** thông tin đăng nhập hosting (cPanel/DirectAdmin), tên miền đã trỏ về hosting.

1. Kiểm tra hosting: PHP ≥ 8.1, MySQL/MariaDB, SSL Let's Encrypt, có SSH và WP-CLI không.
2. Trỏ tên miền (bản ghi A / nameserver), bật SSL, ép HTTPS.
3. Cài WordPress (1-click của hosting hoặc thủ công).
4. Tải lên `wp-content/themes/xanh` và `wp-content/plugins/xanh-core`. Dùng Git deploy nếu hosting hỗ trợ, nếu không thì FTP.
5. Chạy `bin/setup-site.sh` (không dùng `--demo` khi lên thật). Nếu hosting không có WP-CLI thì làm thủ công theo README mục 3, và tạo sản phẩm bằng tay theo KNOWLEDGE mục 1.
6. Đổi mật khẩu admin mạnh, tạo tài khoản cho nhân viên với vai trò "Nhân viên tư vấn" hoặc "Shop Manager".
7. Kiểm tra gói tiếng Việt WooCommerce đã cài (các chữ "Add to cart"… thành tiếng Việt).
8. Thêm `.htaccess` cache/nén (README mục 5), cài plugin cache phù hợp hosting (LiteSpeed Cache / WP Super Cache).
9. Bật backup tự động của hosting.

**Xong khi:** website mở được bằng `https://<tên-miền>`, đặt thử 1 đơn COD thành công, form liên hệ gửi được email.

## Giai đoạn 3: Nội dung & hoàn thiện (một phần)

| Việc | Cần từ khách | Trạng thái |
|---|---|---|
| Logo gốc PNG nền trong suốt / SVG | File logo | Đang dùng logo tạm cắt từ JPG |
| Email công ty (hiển thị + nhận thông báo form/đơn hàng) | Địa chỉ email | Chưa có |
| Xác nhận số Zalo | Số Zalo | Đang để trùng hotline |
| Nội dung trang Giới thiệu | Bài giới thiệu công ty | Nội dung mẫu |
| 4 trang chính sách: đổi trả, giao hàng, bảo mật, hướng dẫn thanh toán | Nội dung hoặc đồng ý dùng mẫu chuẩn | Nội dung mẫu |
| Thông tin chuyển khoản (ngân hàng, số TK, chủ TK) | Thông tin TK | Chưa có |
| Phí vận chuyển, ngưỡng miễn phí ship (đang ghi "đơn từ 500.000 ₫") | Chính sách ship | Chưa xác nhận |
| Ảnh banner trang chủ chuyên nghiệp | Ảnh/thiết kế banner | Đang dùng ảnh hộp sản phẩm |
| Thêm sản phẩm khác từ Pancake POS | Ảnh chụp danh sách hoặc file Excel | Chờ |
| 3–5 bài tin tức đầu tiên (SEO) | Nội dung hoặc chủ đề | Bài mẫu |
| Link nhúng Google Maps | Xác nhận vị trí | Chưa có |
| reCAPTCHA v3 (tuỳ chọn) | Tài khoản Google | Chưa cấu hình |
| Thông tin pháp lý ở chân trang: MST, số giấy phép ĐKKD, thông báo Bộ Công Thương | MST, GPKD | Chưa có |

## Giai đoạn 4: Kiểm thử & ra mắt

- [ ] Kiểm tra trên điện thoại thật (iPhone, Android), các trình duyệt Chrome, Safari, Cốc Cốc
- [ ] Đặt thử đơn COD và chuyển khoản; kiểm tra email xác nhận gửi tới khách và tới shop
- [ ] Gửi thử 3 loại form (liên hệ, tư vấn sản phẩm, đăng ký trang chủ); kiểm tra danh sách và xuất CSV
- [ ] PageSpeed Insights: mục tiêu ≥ 85 trên mobile, Core Web Vitals đạt
- [ ] SEO: Google Search Console, gửi `/wp-sitemap.xml`; kiểm tra meta title/description từng trang; Facebook Sharing Debugger cho ảnh OG
- [ ] Bảo mật: thử đăng nhập sai 6 lần (bị khoá 15 phút), `?author=1` bị chuyển hướng, `/xmlrpc.php` bị tắt
- [ ] Gắn link website vào 2 fanpage Facebook
- [ ] (Nếu bán hàng online) Thông báo website TMĐT với Bộ Công Thương (online.gov.vn)

## Giai đoạn 5: Bàn giao & bảo hành (12 tháng)

- [ ] Bàn giao tài khoản admin, hướng dẫn quản trị (README mục 6): thêm sản phẩm, banner, xử lý khách hàng form, đơn hàng
- [ ] Bàn giao mã nguồn (GitHub) và tài liệu
- [ ] Lịch bảo trì: cập nhật WordPress/plugin hằng tháng, kiểm tra backup
- [ ] Bảo hành kỹ thuật 12 tháng theo báo giá (lỗi từ source code do đơn vị phát triển làm)

## Giai đoạn 6: Mở rộng (ngoài báo giá, báo giá riêng)

- Cổng thanh toán VNPay hoặc Casso (gói gồm 1 cổng; thêm cổng +2.000.000 ₫/cổng)
- Đồng bộ Pancake POS (sản phẩm, tồn kho, đơn hàng) qua API, khoá lưu trên server
- Hoàn thiện Figma (cần nâng cấp gói Figma)
- Thêm sản phẩm/dòng sản phẩm, landing page quảng cáo cho Thảo Mộc Mogan
- Tích hợp Facebook Pixel, Google Analytics 4, Zalo OA

---

## Rủi ro & lưu ý

| Rủi ro | Ảnh hưởng | Cách xử lý |
|---|---|---|
| Quảng cáo công dụng sản phẩm thảo mộc vượt quy định | Bị phạt, gỡ nội dung | Chỉ dùng thông tin trên bao bì + câu "không phải là thuốc"; không viết công dụng chữa bệnh khi chưa có giấy phép |
| Hosting không có SSH/WP-CLI | Không chạy được script tự động | Làm thủ công theo README; hoặc chạy `seed-product.php` qua plugin WP-CLI trên web |
| Logo tạm tách nền từ JPG | Viền logo kém sắc nét | Xin file logo gốc |
| Chưa có email công ty | Không nhận được thông báo form/đơn | Tạm dùng email admin; tạo email theo tên miền khi có hosting |
