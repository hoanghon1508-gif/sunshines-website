# Hướng dẫn đưa website lên hosting (không cần SSH)

Làm lần lượt từ bước 1 đến bước 8. Cần 2 file cài đặt: `sunshines-theme-*.zip` và `sunshines-core-*.zip`.
Lấy file bằng lệnh `bash bin/build-zips.sh` (tạo trong thư mục `dist/`), hoặc xin file từ người phát triển.

## 1. Trỏ tên miền về hosting
- Trong email kích hoạt hosting có **địa chỉ IP** hoặc **nameserver** (ví dụ `ns1.tenhosting.vn`).
- Vào trang quản lý tên miền (nơi mua tên miền), làm **một trong hai**:
  - Đổi **nameserver** thành nameserver của hosting, **hoặc**
  - Tạo bản ghi **A** `@` và `www` trỏ về IP hosting.
- Chờ 15 phút đến vài giờ để tên miền nhận.

## 2. Cài WordPress
- Đăng nhập trang quản trị hosting (cPanel / DirectAdmin).
- Tìm **Softaculous** / **WordPress Manager** / **Cài đặt WordPress 1-click**, rồi chọn tên miền.
- **Ngôn ngữ: Tiếng Việt**. Đặt **tên đăng nhập admin khác "admin"**, mật khẩu mạnh, email của công ty.
- Bỏ chọn các plugin cài kèm nếu được hỏi.

## 3. Bật SSL (https)
- Trong hosting: **SSL/TLS** → **Let's Encrypt** (hoặc AutoSSL), cấp cho tên miền và `www`.
- Vào `https://tên-miền/wp-admin` → **Cài đặt → Tổng quan**: đổi cả 2 địa chỉ sang `https://`.

## 4. Cài WooCommerce
- **Plugin → Cài mới** → tìm **WooCommerce** → Cài đặt → Kích hoạt.
- Nếu WooCommerce mở trang "Thiết lập cửa hàng" thì bấm **Bỏ qua**.

## 5. Tải giao diện và plugin Sun Shines
- **Giao diện → Thêm mới → Tải giao diện lên** → chọn `sunshines-theme-*.zip` → Cài đặt → **Kích hoạt**.
- **Plugin → Cài mới → Tải plugin lên** → chọn `sunshines-core-*.zip` → Cài đặt → **Kích hoạt**.

## 6. Bấm "Thiết lập nhanh"
- Menu trái: **Cài đặt website → Thiết lập nhanh** → bấm **Chạy thiết lập**.
- Tự động: múi giờ VN, tiền VNĐ, COD + chuyển khoản, SĐT bắt buộc khi đặt hàng, các trang, menu, sản phẩm Thảo Mộc Mogan 390.000 ₫ kèm ảnh.
- Chạy lại nhiều lần vẫn an toàn.

## 7. Kiểm tra và điền thông tin
- **Cài đặt website**: kiểm tra hotline, Zalo, địa chỉ, email nhận thông báo form, fanpage.
- **Cài đặt → Ngôn ngữ trang**: Tiếng Việt. Vào **Bảng tin → Cập nhật** → bấm **Cập nhật bản dịch** (để WooCommerce hiện tiếng Việt).
- **WooCommerce → Cài đặt → Thanh toán → Chuyển khoản ngân hàng**: nhập số tài khoản nhận tiền.
- **WooCommerce → Cài đặt → Giao hàng**: tạo khu vực "Việt Nam" và phí ship.
- **Giao diện → Tuỳ biến → Nhận dạng website**: tải logo (nếu có file đẹp hơn), Biểu tượng trang web.

## 8. Thử đặt hàng
- Mở trang chủ → **Mua ngay** → điền thông tin → **Thanh toán khi nhận hàng** → Đặt hàng.
- Vào **WooCommerce → Đơn hàng** để xem đơn. Gửi thử form **Liên hệ**, rồi xem ở **Khách hàng (form)**.

Xong bước 8 là website đã chạy được. Báo lại người phát triển để kiểm tra tốc độ, SEO và bảo mật (PLAN.md, giai đoạn 4).
