#!/usr/bin/env bash
# Dựng site WordPress chạy thử KHÔNG cần Docker/MySQL: PHP CLI + SQLite.
# Dùng khi máy không có Docker (ví dụ phiên Claude Code trên cloud).
#
#   bash tools/test-local/setup.sh /tmp/wptest      # dựng site (xoá dựng lại nếu đã có)
#   bash tools/test-local/serve.sh /tmp/wptest      # chạy http://127.0.0.1:8088
#
# Cần: php >= 8.1 (pdo_sqlite), composer, git, curl, unzip.
set -euo pipefail

WORK="${1:?Thiếu thư mục làm việc, ví dụ /tmp/wptest}"
REPO="$(cd "$(dirname "$0")/../.." && pwd)"
HERE="$REPO/tools/test-local"
SITE="$WORK/site"
mkdir -p "$WORK"

echo "==> WordPress core (Composer: johnpbloch/wordpress-core)"
if [[ ! -d "$WORK/wp" ]]; then
	composer create-project --no-interaction --no-install johnpbloch/wordpress-core "$WORK/wp"
fi

echo "==> Plugin SQLite (GitHub: WordPress/sqlite-database-integration)"
if [[ ! -d "$WORK/sqlite" ]]; then
	git clone -q --depth 1 https://github.com/WordPress/sqlite-database-integration.git "$WORK/sqlite"
fi
SQLITE_PKG="$WORK/sqlite/packages/plugin-sqlite-database-integration"

echo "==> WooCommerce (GitHub release mới nhất)"
if [[ ! -f "$WORK/woo.zip" ]]; then
	TAG=$(git ls-remote --tags --refs https://github.com/woocommerce/woocommerce.git | awk -F/ '{print $3}' | grep -E '^[0-9]+\.[0-9]+\.[0-9]+$' | sort -V | tail -1)
	curl -sSL -o "$WORK/woo.zip" "https://github.com/woocommerce/woocommerce/releases/download/$TAG/woocommerce.zip"
fi

echo "==> Lắp ráp site tại $SITE"
rm -rf "$SITE"
cp -r "$WORK/wp" "$SITE"
rm -f "$SITE/composer.json"
cp -rL "$SQLITE_PKG" "$SITE/wp-content/plugins/sqlite-database-integration"
# Windows: git clone biến symlink thành file chữ, thay bằng bản sao thư mục thật.
if [[ -f "$SITE/wp-content/plugins/sqlite-database-integration/wp-includes/database" ]]; then
	rm -f "$SITE/wp-content/plugins/sqlite-database-integration/wp-includes/database"
	cp -r "$WORK/sqlite/packages/mysql-on-sqlite/src" "$SITE/wp-content/plugins/sqlite-database-integration/wp-includes/database"
fi
sed "s#{SQLITE_IMPLEMENTATION_FOLDER_PATH}#$SITE/wp-content/plugins/sqlite-database-integration#; s#{SQLITE_PLUGIN}#sqlite-database-integration/load.php#g" \
	"$SQLITE_PKG/db.copy" > "$SITE/wp-content/db.php"
(cd "$SITE/wp-content/plugins" && unzip -q "$WORK/woo.zip")
# Liên kết thư mục: Linux/macOS dùng symlink; Windows (Git Bash) dùng junction vì symlink cần quyền admin.
link_dir() {
	if [[ "$(uname -s)" == MINGW* || "$(uname -s)" == MSYS* ]]; then
		cmd //c mklink //J "$(cygpath -w "$2")" "$(cygpath -w "$1")" > /dev/null
	else
		ln -s "$1" "$2"
	fi
}
link_dir "$REPO/wp-content/themes/xanh" "$SITE/wp-content/themes/xanh"
link_dir "$REPO/wp-content/plugins/xanh-core" "$SITE/wp-content/plugins/xanh-core"
mkdir -p "$SITE/wp-content/database"
cat > "$SITE/wp-config.php" <<'PHP'
<?php
define( 'DB_NAME', 'wp' ); define( 'DB_USER', '' ); define( 'DB_PASSWORD', '' ); define( 'DB_HOST', '' );
define( 'DB_CHARSET', 'utf8mb4' ); define( 'DB_COLLATE', '' );
define( 'DB_DIR', __DIR__ . '/wp-content/database/' ); define( 'DB_FILE', 'site.sqlite' );
define( 'AUTH_KEY', 'test-a' ); define( 'SECURE_AUTH_KEY', 'test-b' ); define( 'LOGGED_IN_KEY', 'test-c' ); define( 'NONCE_KEY', 'test-d' );
define( 'AUTH_SALT', 'test-e' ); define( 'SECURE_AUTH_SALT', 'test-f' ); define( 'LOGGED_IN_SALT', 'test-g' ); define( 'NONCE_SALT', 'test-h' );
$table_prefix = 'wp_';
define( 'WP_DEBUG', true ); define( 'WP_DEBUG_LOG', __DIR__ . '/debug.log' ); define( 'WP_DEBUG_DISPLAY', false );
define( 'WP_HOME', 'http://127.0.0.1:8088' ); define( 'WP_SITEURL', 'http://127.0.0.1:8088' );
define( 'DISABLE_WP_CRON', true ); define( 'AUTOMATIC_UPDATER_DISABLED', true ); define( 'WP_HTTP_BLOCK_EXTERNAL', true );
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', __DIR__ . '/' ); }
require_once ABSPATH . 'wp-settings.php';
PHP

echo "==> Cài đặt & cấu hình"
php -d memory_limit=512M "$HERE/install.php" "$SITE" 2>&1 | grep -v sendmail || true
php -d memory_limit=512M "$HERE/configure.php" "$SITE" "$REPO/bin/seed-product.php" 2>&1 | grep -v sendmail || true

echo "==> Xong. Chạy: bash tools/test-local/serve.sh $WORK   (admin / admin123)"
