# Tiến độ dự án – Website Sun Shines / Thảo Mộc Mogan

Cập nhật: 09/11/2026

## Thông tin khách hàng
- Công ty: **CÔNG TY TNHH THƯƠNG MẠI DỊCH VỤ SUN SHINES**
- Thương hiệu hiển thị: **Sun Shines**. Slogan: *Trao sức khỏe, Gửi yêu thương*
- Dòng sản phẩm (tạm thời chỉ có): **Thảo Mộc Mogan**
- Hotline/Zalo: 0868 800 440 (Zalo đang để trùng hotline, chờ xác nhận)
- Địa chỉ: 389/3/23 Quốc lộ 13, Khu phố 24, Phường Hiệp Bình, TP. Hồ Chí Minh, Việt Nam
- Fanpage: https://www.facebook.com/share/19uVvCc2Fd/ · https://www.facebook.com/share/1Guucfskuf/ (Thảo Mộc Mogan)
- Báo giá: Gói thiết kế web nâng cao 18.000.000 ₫ (WordPress)
- Màu chủ đạo: xanh lá (theo logo: xanh lá + vàng chanh)

## Đã xong
- [x] Thiết kế Figma: https://www.figma.com/design/ZuQnD2KZLclZsbiW2IsxdY
  - Desktop: Trang chủ, Danh mục sản phẩm, Chi tiết sản phẩm, Giỏ hàng & Thanh toán, Liên hệ
  - Mobile: Trang chủ
  - Thành phần dùng chung: Button, Product Card, News Card, Header, Footer, Quick Contact, bộ màu (Variables)
- [x] Theme `wp-content/themes/xanh` (tên hiển thị: Sun Shines) và plugin `wp-content/plugins/xanh-core` (Sun Shines Core)
- [x] Script `bin/setup-site.sh` (tiếng Việt, VNĐ, COD + chuyển khoản, trang, menu, dữ liệu mẫu) và `docker-compose.yml`
- [x] Kiểm tra cú pháp PHP, JS và shell
- [x] Đẩy code lên GitHub
- [x] **Chạy thử trên WordPress thật** (WordPress + WooCommerce + SQLite, máy thử cloud, 09/11):
  - Mọi trang trả về 200, không có lỗi PHP/JS, không tràn ngang trên mobile (iPhone 13).
  - Thêm vào giỏ → giỏ hàng → thanh toán chạy được (COD + chuyển khoản).
  - Form liên hệ lưu khách vào *Khách hàng (form)*; form gửi kiểu bot bị chặn.
  - Đã sửa: thanh liên hệ cố định đáy màn hình trên mobile, topbar mobile gọn, nhãn % giảm cùng dòng giá,
    SĐT bắt buộc khi thanh toán, bỏ viền đôi trang giỏ hàng/thanh toán.

## Việc còn lại
- [ ] Kiểm tra lại trên hosting thật với gói tiếng Việt: máy thử không tải được bản dịch, nên các chữ của WooCommerce ("Add to cart", "Related products"…) còn tiếng Anh. `setup-site.sh` có bước cài tiếng Việt.
- [x] Logo Sun Shines (tạm): đã gắn vào theme (`assets/images/logo-mark.webp`, favicon `site-icon.png`) và Figma. Khi có file logo gốc chất lượng cao (PNG nền trong suốt/SVG) thì thay vào hoặc tải lên ở *Giao diện → Tuỳ biến → Logo*.
- [x] Sản phẩm thật (tạm thời 1 sản phẩm): **Thảo Mộc Mogan – hộp 30 gói × 5g – 390.000 ₫**. Thành phần, cách dùng, bảo quản, cảnh báo lấy từ bao bì. Ảnh `assets/images/thao-moc-mogan.jpg`. Tạo bằng `bin/seed-product.php` (setup-site.sh tự chạy). Trang chủ tự hiện khối giới thiệu khi chỉ có 1 sản phẩm, có từ 2 sản phẩm thì chuyển sang lưới.
- [x] Câu "Sản phẩm này không phải là thuốc và không có tác dụng thay thế thuốc chữa bệnh." hiện ở trang sản phẩm (dưới mô tả ngắn), khối giới thiệu trang chủ, mô tả chi tiết và chân trang. Sửa câu trong *Cài đặt website → Câu lưu ý sản phẩm*.
- [ ] Thêm sản phẩm khác từ Pancake POS (khi khách gửi ảnh chụp hoặc file Excel).
- [~] **Figma: TẠM DỪNG** (gói Starter hết lượt MCP, khách chọn không nâng cấp). Từ 09/11 làm trực tiếp trên WordPress, duyệt bằng ảnh chụp màn hình site chạy thử. File Figma giữ làm tham khảo; một số frame (trang chủ, chi tiết sản phẩm) đang cập nhật dở, không còn khớp 100% với web.
- [ ] Email công ty
- [ ] Nội dung trang Giới thiệu, Chính sách (đổi trả, giao hàng, bảo mật, thanh toán): làm trực tiếp trên WordPress khi khách gửi nội dung
- [ ] Hosting + tên miền, SSL, đưa web lên
- [ ] Giai đoạn sau (ngoài phạm vi hiện tại): cổng thanh toán VNPay/Casso (+2.000.000 ₫/cổng nếu thêm), đồng bộ Pancake POS
