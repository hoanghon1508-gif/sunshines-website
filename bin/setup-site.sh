#!/usr/bin/env bash
# Cài đặt & cấu hình website bằng WP-CLI.
#
# Cách dùng (trong thư mục WordPress, hoặc qua docker compose):
#   bash bin/setup-site.sh            # cấu hình cơ bản
#   bash bin/setup-site.sh --demo     # cấu hình + tạo dữ liệu mẫu (sản phẩm, bài viết, banner)
#
# Biến môi trường tuỳ chọn: SITE_URL, SITE_TITLE, ADMIN_USER, ADMIN_PASS, ADMIN_EMAIL
set -euo pipefail

DEMO=0
[[ "${1:-}" == "--demo" ]] && DEMO=1

SITE_URL="${SITE_URL:-http://localhost:8080}"
SITE_TITLE="${SITE_TITLE:-Sun Shines}"
ADMIN_USER="${ADMIN_USER:-admin}"
ADMIN_PASS="${ADMIN_PASS:-admin123}"
ADMIN_EMAIL="${ADMIN_EMAIL:-admin@example.com}"

echo "==> Cài WordPress"
if ! command wp core is-installed 2>/dev/null; then
	command wp core install --url="$SITE_URL" --title="$SITE_TITLE" \
		--admin_user="$ADMIN_USER" --admin_password="$ADMIN_PASS" --admin_email="$ADMIN_EMAIL" --skip-email
fi

echo "==> Tiếng Việt, múi giờ, đường dẫn đẹp"
command wp language core install vi --activate || true
command wp option update timezone_string "Asia/Ho_Chi_Minh"
command wp option update date_format "d/m/Y"
command wp option update time_format "H:i"
command wp option update blogdescription "Trao sức khỏe, Gửi yêu thương"
command wp rewrite structure '/%postname%/' --hard
command wp option update default_comment_status closed

echo "==> Plugin & theme"
command wp plugin install woocommerce --activate
command wp language plugin install woocommerce vi || true
command wp plugin activate xanh-core
command wp theme activate xanh
command wp plugin delete hello akismet 2>/dev/null || true

echo "==> WooCommerce: tiền tệ VNĐ, Việt Nam, thanh toán COD + chuyển khoản"
command wp option update woocommerce_default_country "VN"
command wp option update woocommerce_currency "VND"
command wp option update woocommerce_currency_pos "right_space"
command wp option update woocommerce_price_thousand_sep "."
command wp option update woocommerce_price_decimal_sep ","
command wp option update woocommerce_price_num_decimals 0
command wp option update woocommerce_weight_unit "kg"
command wp option update woocommerce_dimension_unit "cm"
command wp option update woocommerce_calc_taxes "no"
command wp option update woocommerce_enable_guest_checkout "yes"
command wp option update woocommerce_checkout_phone_field "required"
command wp option update woocommerce_checkout_company_field "hidden"
command wp option update woocommerce_checkout_address_2_field "hidden"
command wp option update woocommerce_coming_soon "no"
command wp option update woocommerce_onboarding_profile --format=json '{"skipped":true}'
command wp option update woocommerce_cod_settings --format=json \
	'{"enabled":"yes","title":"Thanh toán khi nhận hàng (COD)","description":"Thanh toán bằng tiền mặt khi nhận hàng.","instructions":"Bạn sẽ thanh toán khi nhận hàng.","enable_for_methods":[],"enable_for_virtual":"yes"}'
command wp option update woocommerce_bacs_settings --format=json \
	'{"enabled":"yes","title":"Chuyển khoản ngân hàng","description":"Chuyển khoản theo thông tin bên dưới. Đơn hàng được xử lý sau khi nhận được thanh toán.","instructions":"Vui lòng ghi mã đơn hàng trong nội dung chuyển khoản."}'

echo "==> Trang WooCommerce (Cửa hàng, Giỏ hàng, Thanh toán, Tài khoản)"
command wp wc tool run install_pages --user="$ADMIN_USER" >/dev/null || true
rename_page() { # option slug title
	local id
	id=$(command wp option get "$1" 2>/dev/null || echo 0)
	if [[ "$id" =~ ^[0-9]+$ && "$id" -gt 0 ]]; then
		command wp post update "$id" --post_name="$2" --post_title="$3" >/dev/null
	fi
}
rename_page woocommerce_shop_page_id "san-pham" "Sản phẩm"
rename_page woocommerce_cart_page_id "gio-hang" "Giỏ hàng"
rename_page woocommerce_checkout_page_id "thanh-toan" "Thanh toán"
rename_page woocommerce_myaccount_page_id "tai-khoan" "Tài khoản"
command wp option update woocommerce_permalinks --format=json \
	'{"product_base":"/san-pham-chi-tiet","category_base":"danh-muc","tag_base":"the-san-pham","attribute_base":"","use_verbose_page_rules":false}'

echo "==> Trang nội dung"
page_id() { command wp post list --post_type=page --name="$1" --field=ID --posts_per_page=1 2>/dev/null | head -n1; }
ensure_page() { # slug title content [template]
	local id
	id=$(page_id "$1")
	if [[ -z "$id" ]]; then
		id=$(command wp post create --post_type=page --post_status=publish --post_name="$1" --post_title="$2" --post_content="$3" --porcelain)
	fi
	if [[ -n "${4:-}" ]]; then
		command wp post meta update "$id" _wp_page_template "$4" >/dev/null
	fi
	echo "$id"
}
HOME_ID=$(ensure_page "trang-chu" "Trang chủ" "")
NEWS_ID=$(ensure_page "tin-tuc" "Tin tức" "")
ABOUT_ID=$(ensure_page "gioi-thieu" "Giới thiệu" "<!-- wp:paragraph --><p>Giới thiệu về công ty, sứ mệnh, tầm nhìn và giá trị cốt lõi. Nội dung trang này được chỉnh sửa trong trang quản trị.</p><!-- /wp:paragraph -->")
CONTACT_ID=$(ensure_page "lien-he" "Liên hệ" "" "page-templates/template-contact.php")
P1=$(ensure_page "chinh-sach-doi-tra" "Chính sách đổi trả" "<!-- wp:paragraph --><p>Nội dung chính sách đổi trả.</p><!-- /wp:paragraph -->")
P2=$(ensure_page "chinh-sach-giao-hang" "Chính sách giao hàng" "<!-- wp:paragraph --><p>Nội dung chính sách giao hàng.</p><!-- /wp:paragraph -->")
P3=$(ensure_page "chinh-sach-bao-mat" "Chính sách bảo mật" "<!-- wp:paragraph --><p>Nội dung chính sách bảo mật thông tin.</p><!-- /wp:paragraph -->")
P4=$(ensure_page "huong-dan-thanh-toan" "Hướng dẫn thanh toán" "<!-- wp:paragraph --><p>Hướng dẫn các hình thức thanh toán.</p><!-- /wp:paragraph -->")
command wp option update show_on_front page
command wp option update page_on_front "$HOME_ID"
command wp option update page_for_posts "$NEWS_ID"
command wp option update wp_page_for_privacy_policy "$P3"

echo "==> Menu"
make_menu() { # name location
	command wp menu delete "$1" >/dev/null 2>&1 || true
	command wp menu create "$1" >/dev/null
	command wp menu location assign "$1" "$2"
}
SHOP_ID=$(command wp option get woocommerce_shop_page_id)
make_menu "Menu chính" primary
command wp menu item add-post "Menu chính" "$HOME_ID" --title="Trang chủ" >/dev/null
command wp menu item add-post "Menu chính" "$ABOUT_ID" >/dev/null
command wp menu item add-post "Menu chính" "$SHOP_ID" >/dev/null
command wp menu item add-post "Menu chính" "$NEWS_ID" >/dev/null
command wp menu item add-post "Menu chính" "$CONTACT_ID" >/dev/null
make_menu "Về chúng tôi" footer_about
for id in "$ABOUT_ID" "$NEWS_ID" "$CONTACT_ID"; do command wp menu item add-post "Về chúng tôi" "$id" >/dev/null; done
make_menu "Chính sách" footer_policy
for id in "$P1" "$P2" "$P3" "$P4"; do command wp menu item add-post "Chính sách" "$id" >/dev/null; done

if [[ "$DEMO" == "1" ]]; then
	echo "==> Dữ liệu mẫu"
	# Tạm thời chỉ có dòng sản phẩm Thảo Mộc Mogan.
	MOGAN=$(command wp wc product_cat list --user="$ADMIN_USER" --slug="thao-moc-mogan" --field=id 2>/dev/null | head -n1)
	if [[ -z "$MOGAN" ]]; then
		MOGAN=$(command wp wc product_cat create --user="$ADMIN_USER" --name="Thảo Mộc Mogan" --slug="thao-moc-mogan" --porcelain)
	fi
	PRICES=(320000 200000 560000 120000 350000 190000 650000 300000)
	SALES=(250000 180000 420000 95000 310000 150000 520000 275000)
	for i in $(seq 0 7); do
		name="Thảo Mộc Mogan – Sản phẩm $((i + 1))"
		if [[ -z "$(command wp post list --post_type=product --title="$name" --field=ID 2>/dev/null)" ]]; then
			command wp wc product create --user="$ADMIN_USER" --name="$name" --type=simple \
				--regular_price="${PRICES[$i]}" --sale_price="${SALES[$i]}" \
				--featured=true --status=publish \
				--short_description="Giới thiệu tóm tắt về sản phẩm: công dụng chính, chất liệu/thành phần, quy cách đóng gói." \
				--description="Nội dung mô tả chi tiết sản phẩm được soạn trong trang quản trị." \
				--categories="[{\"id\":${MOGAN}}]" >/dev/null
		fi
	done
	for t in "Bí quyết chọn sản phẩm phù hợp cho gia đình bạn" "Lợi ích của thảo mộc trong chăm sóc sức khỏe hằng ngày" "Hướng dẫn bảo quản sản phẩm đúng cách"; do
		if [[ -z "$(command wp post list --post_type=post --title="$t" --field=ID 2>/dev/null)" ]]; then
			command wp post create --post_type=post --post_status=publish --post_title="$t" \
				--post_excerpt="Đoạn tóm tắt ngắn của bài viết giúp người đọc nắm nhanh nội dung chính." \
				--post_content="<!-- wp:paragraph --><p>Nội dung bài viết mẫu.</p><!-- /wp:paragraph -->" >/dev/null
		fi
	done
	if [[ -z "$(command wp post list --post_type=xanh_banner --field=ID 2>/dev/null)" ]]; then
		bid=$(command wp post create --post_type=xanh_banner --post_status=publish --post_title="Trao sức khỏe, Gửi yêu thương" --menu_order=1 --porcelain)
		command wp post meta update "$bid" _xanh_location home_slider >/dev/null
		command wp post meta update "$bid" _xanh_active 1 >/dev/null
		command wp post meta update "$bid" _xanh_subtitle "Sun Shines mang đến các sản phẩm thảo mộc Mogan chọn lọc, an toàn và lành tính cho sức khỏe cả gia đình." >/dev/null
		command wp post meta update "$bid" _xanh_link "$SITE_URL/san-pham/" >/dev/null
		command wp post meta update "$bid" _xanh_button "Mua sắm ngay" >/dev/null
	fi
fi

command wp rewrite flush --hard
command wp cache flush >/dev/null || true
echo "==> Xong! Mở $SITE_URL – quản trị: $SITE_URL/wp-admin ($ADMIN_USER / $ADMIN_PASS)"
