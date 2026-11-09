import { PageHero, Pagination, ProductCard } from './ui';
import { ShopFilters } from './ShopFilters';
import { shopQuery } from '@/lib/shop';
import { SortSelect } from './SortSelect';
import { queryProducts, type ProductSort } from '@/lib/products';

const PER_PAGE = 12;

type SP = Record<string, string | string[] | undefined>;

function num(v: string | string[] | undefined) {
  const s = Array.isArray(v) ? v[0] : v;
  if (s == null || s === '') return undefined;
  const n = Number(s);
  return Number.isFinite(n) && n >= 0 ? Math.floor(n) : undefined;
}

/** Trang danh sách sản phẩm (cửa hàng và danh mục): bộ lọc, sắp xếp, phân trang. */
export function ShopView({
  title,
  description,
  category,
  searchParams,
  crumbs,
}: {
  title: string;
  description?: string;
  category?: string;
  searchParams: SP;
  crumbs: { label: string; href?: string }[];
}) {
  const sortRaw = Array.isArray(searchParams.orderby) ? searchParams.orderby[0] : searchParams.orderby;
  const sort = (['price', 'price-desc', 'date'].includes(sortRaw ?? '') ? sortRaw : 'default') as ProductSort;
  const min = num(searchParams.min_price);
  const max = num(searchParams.max_price);
  const onSale = !!searchParams.on_sale;
  const page = Math.max(1, num(searchParams.paged) ?? 1);

  const all = queryProducts({ category, min, max, onSale, sort });
  const totalPages = Math.max(1, Math.ceil(all.length / PER_PAGE));
  const current = Math.min(page, totalPages);
  const list = all.slice((current - 1) * PER_PAGE, current * PER_PAGE);

  const params = { category, min, max, onSale, sort };
  const base = category ? `/danh-muc/${category}` : '/san-pham';
  const query = shopQuery(params).replace(/^\?/, '');

  return (
    <>
      <PageHero title={title} subtitle={description} crumbs={crumbs} />
      <div className="section section--tight">
        <div className="container">
          <div className="shop-layout">
            <ShopFilters params={params} />
            <div className="shop-main">
              {list.length > 0 ? (
                <>
                  <div className="shop-toolbar">
                    <p className="woocommerce-result-count">
                      {all.length} sản phẩm
                    </p>
                    <SortSelect value={sort} base={base} query={query} />
                  </div>
                  <ul className="products columns-3">
                    {list.map((p) => <ProductCard key={p.id} product={p} />)}
                  </ul>
                  <Pagination
                    page={current}
                    total={totalPages}
                    hrefFor={(n) => {
                      const s = new URLSearchParams(query);
                      if (n > 1) s.set('paged', String(n));
                      const t = s.toString();
                      return base + (t ? `?${t}` : '');
                    }}
                  />
                </>
              ) : (
                <p className="woocommerce-info">Không tìm thấy sản phẩm phù hợp với bộ lọc.</p>
              )}
            </div>
          </div>
        </div>
      </div>
    </>
  );
}
