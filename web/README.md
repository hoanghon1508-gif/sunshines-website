# Sun Shines – bản Next.js

Bản chuyển từ theme WordPress `wp-content/themes/xanh` sang **Next.js 15 (App Router) + React 19 + TypeScript**.
Giao diện giữ nguyên (dùng lại CSS của theme). Code WordPress cũ vẫn còn trong repo để đối chiếu.

## Chạy trên máy

Cần Node.js 20 trở lên (`winget install OpenJS.NodeJS.LTS`).

```bash
cd web
npm install
npm run dev        # http://localhost:3000
```

Kiểm tra trước khi đưa lên: `npm run lint` (kiểm tra kiểu) và `npm run build`.

## Đưa lên Vercel

1. Đẩy repo lên GitHub, vào Vercel → **Add New Project** → chọn repo.
2. **Root Directory**: `web`. Các mục còn lại để mặc định (Next.js tự nhận).
3. Thêm biến môi trường (xem `.env.example`), tối thiểu `NEXT_PUBLIC_SITE_URL` = tên miền thật.
4. Bấm Deploy.

## Cấu trúc

| Đường dẫn | Việc |
|---|---|
| `src/lib/site.ts` | Cài đặt website: công ty, hotline, địa chỉ, mạng xã hội, menu, banner trang chủ |
| `src/lib/products.ts` | Sản phẩm, danh mục, giá, lọc/sắp xếp (thay cho WooCommerce) |
| `src/lib/content.ts` | Tin tức và các trang (giới thiệu, chính sách) |
| `src/app/` | Các trang: `/`, `/san-pham`, `/danh-muc/[slug]`, `/san-pham-chi-tiet/[slug]`, `/gio-hang`, `/thanh-toan`, `/tin-tuc`, `/lien-he`, `/tim-kiem`, trang đơn giản `/[slug]` |
| `src/app/api/lead`, `api/order` | Nhận form tư vấn/liên hệ và đơn hàng |
| `src/components/` | Header, Footer, giỏ hàng, form, bộ lọc… |
| `src/app/globals.css` | CSS của theme cũ (giữ nguyên) |
| `src/app/extra.css` | CSS bổ sung cho phần trước đây do WooCommerce tự sinh |

URL giữ như bản WordPress (`/san-pham`, `/san-pham-chi-tiet/...`, `/gio-hang`, `/thanh-toan`, `/tin-tuc`...), nên chuyển tên miền không mất SEO.

## Thêm sản phẩm / bài viết

Sửa mảng `products` trong `src/lib/products.ts` hoặc `posts` trong `src/lib/content.ts`, rồi deploy lại. Ảnh đặt trong `public/images/`.
Mọi sản phẩm phải giữ câu *"Sản phẩm này không phải là thuốc và không có tác dụng thay thế thuốc chữa bệnh."* (giá trị `site.disclaimer`, đã hiện ở trang chi tiết, trang chủ, chân trang).

## Đơn hàng và khách đăng ký: đi đâu?

Bản WordPress lưu vào CSDL và có trang quản trị. Bản Next.js **không có CSDL và trang quản trị**, nên:

- Form tư vấn/liên hệ gọi `/api/lead`, đơn hàng gọi `/api/order`. Máy chủ kiểm tra dữ liệu, chống spam (honeypot, gửi quá nhanh, giới hạn 5 lần/10 phút/IP), tính lại giá, rồi:
  - ghi log (xem ở Vercel → Logs), và
  - gửi JSON bằng POST tới `LEAD_WEBHOOK_URL` / `ORDER_WEBHOOK_URL` nếu đặt. Nối webhook với Google Sheets (Apps Script), Zapier, Make, hoặc Zalo/Telegram bot để nhận đơn.
- **Việc cần làm trước khi chạy thật:** đặt ít nhất `ORDER_WEBHOOK_URL` (và `LEAD_WEBHOOK_URL`), nếu không đơn chỉ nằm trong log của Vercel.
- Giỏ hàng lưu ở trình duyệt khách (`localStorage`).

## Chưa có so với bản WordPress

- Trang quản trị (sửa sản phẩm, banner, tin tức, xem khách/đơn): hiện sửa trong code. Nếu cần quản trị, nên dùng WordPress làm backend (headless) hoặc CMS (Sanity, Strapi).
- Gửi email thông báo, phân quyền, tài khoản khách, trạng thái đơn.
- Cổng thanh toán (khách chưa yêu cầu): đơn đang là COD hoặc chuyển khoản, nhân viên gọi xác nhận.
- Giới hạn tần suất lưu trong bộ nhớ từng máy chủ, chống spam ở mức cơ bản.
