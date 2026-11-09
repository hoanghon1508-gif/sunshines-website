#!/usr/bin/env bash
# Chạy site thử tại http://127.0.0.1:8088 (dừng bằng Ctrl+C). Log lỗi PHP: <thư-mục>/site/debug.log
set -euo pipefail
WORK="${1:?Thiếu thư mục làm việc, ví dụ /tmp/wptest}"
HERE="$(cd "$(dirname "$0")" && pwd)"
exec php -d memory_limit=512M -S 127.0.0.1:8088 -t "$WORK/site" "$HERE/router.php"
