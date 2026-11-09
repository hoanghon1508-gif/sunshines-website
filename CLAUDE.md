# CLAUDE.md

Website bán hàng WordPress + WooCommerce cho **CÔNG TY TNHH TMDV SUN SHINES** (thương hiệu Sun Shines, sản phẩm Thảo Mộc Mogan).
Khách hàng giao tiếp bằng **tiếng Việt** (thường không dấu), nên trả lời bằng tiếng Việt, xưng "em", gọi khách là "anh".

## Đọc trước khi làm

1. [`TIEN-DO.md`](TIEN-DO.md): đã xong gì, còn gì.
2. [`docs/KNOWLEDGE.md`](docs/KNOWLEDGE.md): thông tin khách hàng, quyết định đã chốt, kiến trúc, bẫy đã gặp.
3. [`docs/PLAN.md`](docs/PLAN.md): các giai đoạn và bước tiếp theo.
4. [`README.md`](README.md): đối chiếu báo giá, hướng dẫn cài đặt và quản trị.

## Quy tắc

- Code: theme `wp-content/themes/xanh`, plugin `wp-content/plugins/xanh-core`. Không đổi tên thư mục hoặc prefix `xanh_`.
- Chức năng và dữ liệu đặt trong plugin; giao diện đặt trong theme. Theme phải chạy được khi plugin tắt.
- Escape output, sanitize input, kiểm tra nonce và quyền (WordPress Coding Standards). Comment và chuỗi giao diện viết tiếng Việt.
- Sản phẩm sức khoẻ: luôn giữ câu *"Sản phẩm này không phải là thuốc và không có tác dụng thay thế thuốc chữa bệnh."*. Không tự viết công dụng chữa bệnh.
- Figma đang **tạm dừng**: duyệt giao diện bằng ảnh chụp màn hình site chạy thử, không dùng Figma.
- Sau mỗi thay đổi: chạy thử theo `tools/test-local/README.md`, cập nhật `TIEN-DO.md`, commit bằng tiếng Việt và push `main`.

## Lệnh nhanh

```bash
# Kiểm tra cú pháp
for f in $(git ls-files '*.php'); do php -l "$f" | grep -v '^No syntax'; done; bash -n bin/setup-site.sh
```

```bash
# Chạy thử không cần Docker (xem tools/test-local/README.md)
bash tools/test-local/setup.sh /tmp/wptest && bash tools/test-local/serve.sh /tmp/wptest
```
