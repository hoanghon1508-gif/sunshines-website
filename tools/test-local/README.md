# Chạy thử không cần Docker

Dựng WordPress + WooCommerce + theme/plugin của repo bằng **PHP CLI + SQLite**, phục vụ bằng `php -S`.
Dùng cho phiên Claude Code trên cloud, hoặc máy không cài Docker/MySQL. Máy có Docker thì dùng `docker-compose.yml` (README chính, mục 2).

```bash
bash tools/test-local/setup.sh /tmp/wptest
```

```bash
bash tools/test-local/serve.sh /tmp/wptest
```

Mở http://127.0.0.1:8088, trang quản trị ở `/wp-admin` (admin / admin123).

- Theme và plugin được **symlink** từ repo, nên sửa code là thấy thay đổi ngay, không cần dựng lại.
- Dữ liệu: các trang, menu, VNĐ, COD + chuyển khoản, sản phẩm Thảo Mộc Mogan (`bin/seed-product.php`), 3 bài tin mẫu, 1 banner.
- Lỗi PHP ghi vào `/tmp/wptest/site/debug.log`. Các cảnh báo `wp_version_check` / `wp_update_*` / `sendmail` là do máy thử bị chặn mạng, bỏ qua được.
- Máy thử không tải được gói tiếng Việt, nên chữ WooCommerce còn tiếng Anh. Trên hosting thật thì sẽ có tiếng Việt.

| File | Vai trò |
|---|---|
| `setup.sh` | Tải WordPress (Composer), SQLite plugin, WooCommerce (GitHub), lắp ráp site, cài đặt |
| `install.php` | `wp_install`, kích hoạt plugin, theme |
| `configure.php` | WooCommerce, trang, menu, nội dung mẫu, rồi gọi `bin/seed-product.php` |
| `router.php` | Router cho `php -S` (thay `.htaccess`) |
| `serve.sh` | Chạy máy chủ ở cổng 8088 |

Kiểm tra giao diện bằng Playwright (Chromium có sẵn trong phiên cloud): chụp desktop 1440px và `devices['iPhone 13']`, kiểm tra lỗi JS (`pageerror`) và tràn ngang (`scrollWidth > innerWidth`).
