#!/usr/bin/env bash
# Đóng gói theme và plugin thành file .zip để tải lên trong trang quản trị WordPress:
#   Giao diện → Thêm mới → Tải lên giao diện   (sunshines-theme-*.zip)
#   Plugin    → Thêm mới → Tải plugin lên      (sunshines-core-*.zip)
#
#   bash bin/build-zips.sh            # tạo trong thư mục dist/
set -euo pipefail
REPO="$(cd "$(dirname "$0")/.." && pwd)"
OUT="$REPO/dist"
VER="$(git -C "$REPO" describe --tags --always 2>/dev/null || date +%Y%m%d)"
mkdir -p "$OUT"
rm -f "$OUT"/sunshines-*.zip
# Tên thư mục bên trong zip phải giữ đúng "xanh" / "xanh-core" (WordPress dùng làm slug).
(cd "$REPO/wp-content/themes" && zip -qr -X "$OUT/sunshines-theme-$VER.zip" xanh -x '*.DS_Store')
(cd "$REPO/wp-content/plugins" && zip -qr -X "$OUT/sunshines-core-$VER.zip" xanh-core -x '*.DS_Store')
cp "$REPO/bin/seed-product.php" "$OUT/seed-product.php"
ls -la "$OUT"
