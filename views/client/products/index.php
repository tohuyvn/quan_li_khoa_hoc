<?php $title = 'Điện thoại, Laptop, Phụ kiện chính hãng'; ?>

<?php
if (!function_exists('getCategoryIcon')) {
    function getCategoryIcon(string $name): string
    {
        $nameLower = strtolower($name);
        if (str_contains($nameLower, 'thoại') || str_contains($nameLower, 'phone')) return 'bi-phone';
        if (str_contains($nameLower, 'laptop') || str_contains($nameLower, 'máy tính')) return 'bi-laptop';
        if (str_contains($nameLower, 'phụ kiện') || str_contains($nameLower, 'tai nghe') || str_contains($nameLower, 'sạc') || str_contains($nameLower, 'dây')) return 'bi-headphones';
        if (str_contains($nameLower, 'watch') || str_contains($nameLower, 'đồng hồ thông minh')) return 'bi-smartwatch';
        if (str_contains($nameLower, 'đồng hồ')) return 'bi-watch';
        if (str_contains($nameLower, 'tablet') || str_contains($nameLower, 'ipad')) return 'bi-tablet';
        return 'bi-plug';
    }
}

$isSearch = !empty($filters['search']);
$isFiltered = !empty($filters['category_id'])
    || !empty($filters['min_price'])
    || !empty($filters['max_price'])
    || !empty($filters['in_stock'])
    || !empty($filters['chipset'])
    || !empty($filters['ram'])
    || !empty($filters['storage'])
    || $filters['sort'] !== 'newest';
$isHome = !$isSearch && !$isFiltered;

$removeFilterUrl = static function (string $key, mixed $value = null): string {
    $params = $_GET;
    unset($params['page']);
    if ($value === null || !isset($params[$key]) || !is_array($params[$key])) {
        unset($params[$key]);
    } else {
        $params[$key] = array_values(array_filter(
            $params[$key],
            static fn (mixed $item): bool => (string) $item !== (string) $value
        ));
        if ($params[$key] === []) {
            unset($params[$key]);
        }
    }
    $query = http_build_query($params);
    return '/' . ($query !== '' ? '?' . $query : '');
};

$activeFilterChips = [];
if ($filters['search'] !== '') {
    $activeFilterChips[] = ['label' => 'Từ khóa: ' . $filters['search'], 'url' => $removeFilterUrl('q')];
}
if (!empty($_GET['category']) && !empty($filters['category_id'])) {
    $categoryNames = array_column($categories, 'name', 'id');
    $activeFilterChips[] = [
        'label' => 'Danh mục: ' . ($categoryNames[$filters['category_id']] ?? 'Khác'),
        'url' => $removeFilterUrl('category'),
    ];
}
if ($filters['min_price'] !== null && $filters['min_price'] !== '') {
    $minPriceLabel = \App\Models\Product::normalizePriceFilter($filters['min_price']);
    $activeFilterChips[] = ['label' => 'Giá từ: ' . formatMoney($minPriceLabel ?? 0), 'url' => $removeFilterUrl('min_price')];
}
if ($filters['max_price'] !== null && $filters['max_price'] !== '') {
    $maxPriceLabel = \App\Models\Product::normalizePriceFilter($filters['max_price']);
    $activeFilterChips[] = ['label' => 'Giá đến: ' . formatMoney($maxPriceLabel ?? 0), 'url' => $removeFilterUrl('max_price')];
}
foreach (['chipset' => 'Chip', 'ram' => 'RAM', 'storage' => 'Bộ nhớ'] as $key => $label) {
    foreach ($filters[$key] as $value) {
        $activeFilterChips[] = ['label' => $label . ': ' . $value, 'url' => $removeFilterUrl($key, $value)];
    }
}
if ($filters['in_stock']) {
    $activeFilterChips[] = ['label' => 'Chỉ còn hàng', 'url' => $removeFilterUrl('in_stock')];
}
?>

<?php if ($isHome): ?>
    <!-- Home View: Carousel, Flash Sale, and Sections -->
    <section class="home-hero-shell" aria-label="Ưu đãi nổi bật">
    <div class="home-hero-main">
    <div id="promoCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="4000" data-bs-pause="false">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#promoCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#promoCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#promoCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
            <button type="button" data-bs-target="#promoCarousel" data-bs-slide-to="3" aria-label="Slide 4"></button>
            <button type="button" data-bs-target="#promoCarousel" data-bs-slide-to="4" aria-label="Slide 5"></button>
            <button type="button" data-bs-target="#promoCarousel" data-bs-slide-to="5" aria-label="Slide 6"></button>
            <button type="button" data-bs-target="#promoCarousel" data-bs-slide-to="6" aria-label="Slide 7"></button>
        </div>
        <div class="carousel-inner rounded-4 shadow-sm overflow-hidden">
            <div class="carousel-item active">
                <a href="/?q=iphone">
                    <img src="/image/promo_iphone.png" class="d-block w-100" alt="Siêu sale iPhone" style="aspect-ratio: 16/7; object-fit: cover;">
                </a>
            </div>
            <div class="carousel-item">
                <a href="/?q=laptop">
                    <img src="/image/promo_laptop.png" class="d-block w-100" alt="Siêu sale Laptop" style="aspect-ratio: 16/7; object-fit: cover;">
                </a>
            </div>
            <div class="carousel-item">
                <a href="/?q=tai+nghe">
                    <img src="/image/promo_accessories.png" class="d-block w-100" alt="Siêu sale Phụ kiện" style="aspect-ratio: 16/7; object-fit: cover;">
                </a>
            </div>
            <div class="carousel-item">
                <a href="/?q=smartwatch">
                    <img src="/image/banner1.png!sw800" class="d-block w-100" alt="Siêu sale Smartwatch" style="aspect-ratio: 16/7; object-fit: cover;">
                </a>
            </div>
            <div class="carousel-item">
                <a href="/?q=ipad">
                    <img src="/image/banner2.avif" class="d-block w-100" alt="Siêu sale iPad" style="aspect-ratio: 16/7; object-fit: cover;">
                </a>
            </div>
            <div class="carousel-item">
                <a href="/?q=casio">
                    <img src="/image/banner3.jpg" class="d-block w-100" alt="Siêu sale Casio" style="aspect-ratio: 16/7; object-fit: cover;">
                </a>
            </div>
            <div class="carousel-item">
                <a href="/?q=gaming">
                    <img src="/image/promo_gaming.png" class="d-block w-100" alt="Siêu sale Gaming Gear" style="aspect-ratio: 16/7; object-fit: cover;">
                </a>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#promoCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#promoCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
    </div>
    <aside class="home-hero-aside">
        <a href="/?q=iphone" class="hero-benefit-card hero-benefit-primary">
            <span class="hero-benefit-icon"><i class="bi bi-phone"></i></span>
            <span>
                <small>Sản phẩm Apple</small>
                <strong>Ưu đãi đến 5 triệu</strong>
                <em>Khám phá ngay <i class="bi bi-arrow-up-right"></i></em>
            </span>
        </a>
        <a href="/trade-in" class="hero-benefit-card">
            <span class="hero-benefit-icon"><i class="bi bi-arrow-repeat"></i></span>
            <span>
                <small>Thu cũ đổi mới</small>
                <strong>Lên đời tiết kiệm</strong>
                <em>Định giá thiết bị <i class="bi bi-arrow-up-right"></i></em>
            </span>
        </a>
        <a href="/?q=trả+góp" class="hero-benefit-card">
            <span class="hero-benefit-icon"><i class="bi bi-credit-card"></i></span>
            <span>
                <small>Thanh toán linh hoạt</small>
                <strong>Trả góp 0%</strong>
                <em>Duyệt hồ sơ nhanh <i class="bi bi-arrow-up-right"></i></em>
            </span>
        </a>
    </aside>
    </section>

    <section class="home-trust-strip" aria-label="Cam kết dịch vụ">
        <div class="home-trust-item">
            <i class="bi bi-patch-check"></i>
            <span><strong>100% chính hãng</strong><small>Nguồn gốc minh bạch</small></span>
        </div>
        <div class="home-trust-item">
            <i class="bi bi-truck"></i>
            <span><strong>Giao hàng miễn phí</strong><small>Cho đơn từ 500.000đ</small></span>
        </div>
        <div class="home-trust-item">
            <i class="bi bi-arrow-counterclockwise"></i>
            <span><strong>Đổi trả dễ dàng</strong><small>Hỗ trợ tận tâm</small></span>
        </div>
        <div class="home-trust-item">
            <i class="bi bi-headset"></i>
            <span><strong>Tư vấn 24/7</strong><small>Luôn sẵn sàng hỗ trợ</small></span>
        </div>
    </section>

    <?php if (!empty($categories)): ?>
    <section class="home-category-section">
        <div class="section-title">
            <div>
                <span class="section-eyebrow">Khám phá nhanh</span>
                <h2>Danh mục nổi bật</h2>
            </div>
        </div>
        <div class="home-category-grid">
            <?php foreach ($categories as $cat): ?>
                <a href="/?category=<?= (int) $cat['id'] ?>" class="home-category-card">
                    <span><i class="bi <?= getCategoryIcon($cat['name']) ?>"></i></span>
                    <strong><?= e($cat['name']) ?></strong>
                    <i class="bi bi-arrow-right"></i>
                </a>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

<?php if ($activePromo): ?>
    <div class="flash-sale-section mb-4">
        <div class="flash-sale-header">
            <div class="flash-sale-title-main"><i class="bi bi-lightning-charge-fill"></i> <?= e($activePromo['name']) ?></div>
            <div class="flash-sale-slot-active">
                <span class="flash-sale-title">Kết thúc sau:</span>
                <div class="flash-sale-timer" id="flashSaleCountdown" data-expire="<?= e($activePromo['expires_at']) ?> 23:59:59">
                    <span class="timer-box" id="timer-hour">00</span>
                    <span class="timer-colon">:</span>
                    <span class="timer-box" id="timer-min">00</span>
                    <span class="timer-colon">:</span>
                    <span class="timer-box" id="timer-sec">00</span>
                </div>
            </div>
        </div>
        <div class="flash-sale-bar">
            <div class="flash-sale-slot-upcoming">
                <span class="upcoming-status">Đang diễn ra</span>
                <span class="upcoming-time"><?= (int)$activePromo['discount_percent'] ?>% OFF</span>
            </div>
            <div class="flash-sale-slot-upcoming">
                <span class="upcoming-status">Áp dụng cho</span>
                <span class="upcoming-time">Toàn sàn</span>
            </div>
        </div>
    </div>
<?php endif; ?>

    <?php
    $onlinePromo = array_values(array_filter($products, fn ($p) => (int) $p['price'] < 10000000));
    $hotDeals = array_values(array_filter($products, fn ($p) => (int) $p['price'] >= 10000000));
    $suggestions = $products;

    $sections = [
        'san-pham-moi' => [
            'title' => 'Sản phẩm mới',
            'subtitle' => 'Cập nhật những thiết bị mới nhất',
            'items' => array_slice($products, 0, 12),
        ],
        'khuyen-mai-online' => [
            'title' => 'Khuyến mãi online',
            'subtitle' => 'Mức giá tốt chỉ có trên website',
            'items' => $onlinePromo,
        ],
        'uu-dai-khung' => [
            'title' => 'Ưu đãi khủng',
            'subtitle' => 'Sản phẩm cao cấp với ưu đãi hấp dẫn',
            'items' => $hotDeals,
        ],
        'goi-y-cho-ban' => [
            'title' => 'Gợi ý cho bạn',
            'subtitle' => 'Lựa chọn được nhiều khách hàng quan tâm',
            'items' => $suggestions,
        ],
    ];

    foreach ($sections as $sectionId => $sect):
        if (empty($sect['items'])) continue;
    ?>
        <div class="section-container mb-5">
            <div class="section-title section-header-line" id="title-<?= $sectionId ?>">
                <div>
                    <span class="section-eyebrow">Hades tuyển chọn</span>
                    <h2><?= e($sect['title']) ?></h2>
                    <p><?= e($sect['subtitle']) ?></p>
                </div>
                <a href="/?sort=newest" class="section-link">Xem tất cả <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="product-grid">
                <?php
                $idx = 0;
                foreach ($sect['items'] as $product):
                    $isExtra = $idx >= 8;
                    $extraClass = $isExtra ? 'd-none extra-product-' . $sectionId : '';
                    $idx++;
                    require BASE_PATH . '/views/partials/product_card.php';
                endforeach;
                ?>
            </div>
            <?php if (count($sect['items']) > 8): ?>
                <div class="text-center mt-3">
                    <button class="btn btn-xem-them-custom" onclick="toggleMoreProducts('<?= $sectionId ?>', this)">Xem thêm</button>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>

<?php else: ?>
    <!-- Shopping View: Sidebar + Grid + Pagination -->
    <div class="row g-4">
        <!-- Sidebar Filter -->
        <div class="col-lg-3 d-none d-lg-block">
            <div class="content-card p-3 sticky-top" style="top: 90px; z-index: 10;">
                <form action="/" method="GET" id="filterForm">
                    <input type="hidden" name="sort" value="<?= e($filters['sort'] ?? 'newest') ?>">
                    <div class="mb-4">
                        <h6 class="fw-bold mb-3"><i class="bi bi-search me-2"></i>Tìm kiếm</h6>
                        <div class="input-group input-group-sm mb-2">
                            <input type="text" name="q" class="form-control" placeholder="Tìm sản phẩm..." value="<?= e($filters['search'] ?? '') ?>">
                            <button type="submit" class="btn btn-dark btn-sm"><i class="bi bi-search"></i></button>
                        </div>
                        <div class="d-flex flex-wrap gap-1 mt-2">
                            <?php 
                            $popularKeywords = ['iPhone', 'Laptop', 'Tai nghe', 'Smartwatch', 'Đồng hồ', 'Samsung'];
                            foreach ($popularKeywords as $kw):
                            ?>
                                <a href="/?q=<?= urlencode($kw) ?>" class="badge bg-light text-dark border text-decoration-none py-1.5 px-2.5 small" style="font-size: 11px;"><?= e($kw) ?></a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="fw-bold mb-3"><i class="bi bi-grid-fill me-2"></i>Danh mục</h6>
                        <div class="d-flex flex-column gap-2">
                            <label class="form-check-label small cursor-pointer">
                                <input type="radio" name="category" value="" class="form-check-input me-2" <?= empty($selectedCategory) ? 'checked' : '' ?> onchange="this.form.requestSubmit()"> Tất cả
                            </label>
                            <?php foreach ($categories as $cat): ?>
                                <label class="form-check-label small cursor-pointer">
                                    <input type="radio" name="category" value="<?= $cat['id'] ?>" class="form-check-input me-2" <?= (int)$selectedCategory === (int)$cat['id'] ? 'checked' : '' ?> onchange="this.form.requestSubmit()"> <?= e($cat['name']) ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="fw-bold mb-2"><i class="bi bi-currency-dollar me-2"></i>Khoảng giá</h6>
                        <p class="small text-muted mb-2" style="font-size: 11px;">Nhập theo triệu đồng, ví dụ: 3–9</p>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="number" name="min_price" class="form-control form-control-sm" placeholder="Từ" value="<?= e($filters['min_price']) ?>">
                            </div>
                            <div class="col-6">
                                <input type="number" name="max_price" class="form-control form-control-sm" placeholder="Đến" value="<?= e($filters['max_price']) ?>">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-accent btn-sm w-100 mt-2">Áp dụng</button>
                    </div>

                    <div class="mb-4">
                        <h6 class="fw-bold mb-2"><i class="bi bi-cpu-fill me-2"></i>Bộ vi xử lý</h6>
                        <div class="d-flex flex-column gap-2 overflow-auto ps-1" style="max-height: 150px;">
                            <?php foreach (['Apple', 'Snapdragon', 'Intel', 'AMD'] as $chip): ?>
                                <label class="form-check-label small cursor-pointer d-flex align-items-center gap-2">
                                    <input type="checkbox" name="chipset[]" value="<?= $chip ?>" class="form-check-input"
                                           <?= in_array($chip, $filters['chipset'] ?? []) ? 'checked' : '' ?>
                                           onchange="this.form.requestSubmit()">
                                    <?= $chip ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="fw-bold mb-2"><i class="bi bi-memory me-2"></i>Bộ nhớ RAM</h6>
                        <div class="d-flex flex-column gap-2 overflow-auto ps-1" style="max-height: 150px;">
                            <?php foreach (['4GB', '6GB', '8GB', '12GB', '16GB'] as $ramVal): ?>
                                <label class="form-check-label small cursor-pointer d-flex align-items-center gap-2">
                                    <input type="checkbox" name="ram[]" value="<?= $ramVal ?>" class="form-check-input"
                                           <?= in_array($ramVal, $filters['ram'] ?? []) ? 'checked' : '' ?>
                                           onchange="this.form.requestSubmit()">
                                    <?= $ramVal ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="fw-bold mb-2"><i class="bi bi-hdd-fill me-2"></i>Bộ nhớ trong</h6>
                        <div class="d-flex flex-column gap-2 overflow-auto ps-1" style="max-height: 150px;">
                            <?php foreach (['64GB', '128GB', '256GB', '512GB', '1TB'] as $storeVal): ?>
                                <label class="form-check-label small cursor-pointer d-flex align-items-center gap-2">
                                    <input type="checkbox" name="storage[]" value="<?= $storeVal ?>" class="form-check-input"
                                           <?= in_array($storeVal, $filters['storage'] ?? []) ? 'checked' : '' ?>
                                           onchange="this.form.requestSubmit()">
                                    <?= $storeVal ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="fw-bold mb-3"><i class="bi bi-box-seam me-2"></i>Trạng thái</h6>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="in_stock" id="inStockCheck" value="1" <?= $filters['in_stock'] ? 'checked' : '' ?> onchange="this.form.requestSubmit()">
                            <label class="form-check-label small" for="inStockCheck">Chỉ còn hàng</label>
                        </div>
                    </div>

                    <div class="pt-3 border-top">
                        <a href="/" class="text-decoration-none small text-danger"><i class="bi bi-x-circle me-1"></i> Xóa tất cả bộ lọc</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Product Grid & Toolbar -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                <h5 class="fw-bold mb-0">
                    <?php if ($isSearch): ?>
                        Kết quả cho "<?= e($filters['search']) ?>"
                    <?php elseif (!empty($filters['category_id'])): ?>
                        <?php
                        $currentCat = array_filter($categories, fn($c) => (int)$c['id'] === (int)$filters['category_id']);
                        echo e(current($currentCat)['name'] ?? 'Danh mục');
                        ?>
                    <?php else: ?>
                        Tất cả sản phẩm
                    <?php endif; ?>
                    <span class="text-muted small fw-normal ms-2">(<?= $pagination['total'] ?> sản phẩm)</span>
                </h5>

                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-outline-secondary btn-sm d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileFilters" aria-controls="mobileFilters">
                        <i class="bi bi-funnel-fill me-1"></i> Bộ lọc
                    </button>
                    <span class="small text-muted d-none d-md-inline">Sắp xếp:</span>
                    <select class="form-select form-select-sm" style="width: auto;" onchange="window.location.href = updateQueryString('sort', this.value)">
                        <option value="newest" <?= $filters['sort'] === 'newest' ? 'selected' : '' ?>>Mới nhất</option>
                        <option value="bestseller" <?= $filters['sort'] === 'bestseller' ? 'selected' : '' ?>>Bán chạy nhất</option>
                        <option value="rating" <?= $filters['sort'] === 'rating' ? 'selected' : '' ?>>Đánh giá cao</option>
                        <option value="price_asc" <?= $filters['sort'] === 'price_asc' ? 'selected' : '' ?>>Giá thấp đến cao</option>
                        <option value="price_desc" <?= $filters['sort'] === 'price_desc' ? 'selected' : '' ?>>Giá cao đến thấp</option>
                        <option value="oldest" <?= $filters['sort'] === 'oldest' ? 'selected' : '' ?>>Cũ nhất</option>
                    </select>
                </div>
            </div>

            <?php if ($activeFilterChips !== []): ?>
                <div class="d-flex flex-wrap align-items-center gap-2 mb-4" aria-label="Bộ lọc đang áp dụng">
                    <span class="small text-muted fw-semibold">Đang lọc:</span>
                    <?php foreach ($activeFilterChips as $chip): ?>
                        <a href="<?= e($chip['url']) ?>" class="badge rounded-pill text-bg-light border text-decoration-none text-dark px-3 py-2"
                           title="Bỏ bộ lọc này">
                            <?= e($chip['label']) ?> <i class="bi bi-x ms-1"></i>
                        </a>
                    <?php endforeach; ?>
                    <a href="/" class="small text-danger text-decoration-none ms-1">Xóa tất cả</a>
                </div>
            <?php endif; ?>

            <?php if (empty($products)): ?>
                <div class="content-card text-center py-5">
                    <div style="font-size: 48px; opacity: 0.2;"><i class="bi bi-search"></i></div>
                    <p class="text-muted mt-3">Không tìm thấy sản phẩm nào phù hợp với bộ lọc của bạn.</p>
                    <a href="/" class="btn btn-outline-primary btn-sm mt-2">Xem tất cả sản phẩm</a>
                </div>
            <?php else: ?>
                <div class="product-grid mb-4">
                    <?php foreach ($products as $product): ?>
                        <?php require BASE_PATH . '/views/partials/product_card.php'; ?>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($pagination['total_pages'] > 1): ?>
                    <nav aria-label="Page navigation" class="mt-5">
                        <ul class="pagination justify-content-center">
                            <li class="page-item <?= $pagination['page'] <= 1 ? 'disabled' : '' ?>">
                                <a class="page-link" href="<?= updateQueryString('page', $pagination['page'] - 1) ?>" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                            <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                                <li class="page-item <?= $pagination['page'] === $i ? 'active' : '' ?>">
                                    <a class="page-link" href="<?= updateQueryString('page', $i) ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            <li class="page-item <?= $pagination['page'] >= $pagination['total_pages'] ? 'disabled' : '' ?>">
                                <a class="page-link" href="<?= updateQueryString('page', $pagination['page'] + 1) ?>" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Mobile Filter Offcanvas -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="mobileFilters" aria-labelledby="mobileFiltersLabel">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title fw-bold" id="mobileFiltersLabel"><i class="bi bi-funnel-fill me-2"></i>Bộ lọc tìm kiếm</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form action="/" method="GET" id="mobileFilterForm">
                <input type="hidden" name="sort" value="<?= e($filters['sort'] ?? 'newest') ?>">
                
                <div class="mb-4">
                    <h6 class="fw-bold mb-3"><i class="bi bi-search me-2"></i>Tìm kiếm</h6>
                    <div class="input-group input-group-sm mb-2">
                        <input type="text" name="q" class="form-control" placeholder="Tìm sản phẩm..." value="<?= e($filters['search'] ?? '') ?>">
                        <button type="submit" class="btn btn-dark btn-sm"><i class="bi bi-search"></i></button>
                    </div>
                    <div class="d-flex flex-wrap gap-1 mt-2">
                        <?php 
                        $popularKeywords = ['iPhone', 'Laptop', 'Tai nghe', 'Smartwatch', 'Đồng hồ', 'Samsung'];
                        foreach ($popularKeywords as $kw):
                        ?>
                            <a href="/?q=<?= urlencode($kw) ?>" class="badge bg-light text-dark border text-decoration-none py-1.5 px-2.5 small" style="font-size: 11px;"><?= e($kw) ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="mb-4">
                    <h6 class="fw-bold mb-3"><i class="bi bi-grid-fill me-2"></i>Danh mục</h6>
                    <div class="d-flex flex-column gap-2">
                        <label class="form-check-label small cursor-pointer">
                            <input type="radio" name="category" value="" class="form-check-input me-2" <?= empty($selectedCategory) ? 'checked' : '' ?> onchange="this.form.requestSubmit()"> Tất cả
                        </label>
                        <?php foreach ($categories as $cat): ?>
                            <label class="form-check-label small cursor-pointer">
                                <input type="radio" name="category" value="<?= $cat['id'] ?>" class="form-check-input me-2" <?= (int)$selectedCategory === (int)$cat['id'] ? 'checked' : '' ?> onchange="this.form.requestSubmit()"> <?= e($cat['name']) ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="mb-4">
                    <h6 class="fw-bold mb-2"><i class="bi bi-currency-dollar me-2"></i>Khoảng giá</h6>
                    <p class="small text-muted mb-2" style="font-size: 11px;">Nhập theo triệu đồng, ví dụ: 3–9</p>
                    <div class="row g-2">
                        <div class="col-6">
                            <input type="number" name="min_price" class="form-control form-control-sm" placeholder="Từ" value="<?= e($filters['min_price']) ?>">
                        </div>
                        <div class="col-6">
                            <input type="number" name="max_price" class="form-control form-control-sm" placeholder="Đến" value="<?= e($filters['max_price']) ?>">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-accent btn-sm w-100 mt-2">Áp dụng</button>
                </div>

                <div class="mb-4">
                    <h6 class="fw-bold mb-2"><i class="bi bi-cpu-fill me-2"></i>Bộ vi xử lý</h6>
                    <div class="d-flex flex-column gap-2 overflow-auto ps-1" style="max-height: 150px;">
                        <?php foreach (['Apple', 'Snapdragon', 'Intel', 'AMD'] as $chip): ?>
                            <label class="form-check-label small cursor-pointer d-flex align-items-center gap-2">
                                <input type="checkbox" name="chipset[]" value="<?= $chip ?>" class="form-check-input"
                                       <?= in_array($chip, $filters['chipset'] ?? []) ? 'checked' : '' ?>
                                       onchange="this.form.requestSubmit()">
                                <?= $chip ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="mb-4">
                    <h6 class="fw-bold mb-2"><i class="bi bi-memory me-2"></i>Bộ nhớ RAM</h6>
                    <div class="d-flex flex-column gap-2 overflow-auto ps-1" style="max-height: 150px;">
                        <?php foreach (['4GB', '6GB', '8GB', '12GB', '16GB'] as $ramVal): ?>
                            <label class="form-check-label small cursor-pointer d-flex align-items-center gap-2">
                                <input type="checkbox" name="ram[]" value="<?= $ramVal ?>" class="form-check-input"
                                       <?= in_array($ramVal, $filters['ram'] ?? []) ? 'checked' : '' ?>
                                       onchange="this.form.requestSubmit()">
                                <?= $ramVal ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="mb-4">
                    <h6 class="fw-bold mb-2"><i class="bi bi-hdd-fill me-2"></i>Bộ nhớ trong</h6>
                    <div class="d-flex flex-column gap-2 overflow-auto ps-1" style="max-height: 150px;">
                        <?php foreach (['64GB', '128GB', '256GB', '512GB', '1TB'] as $storeVal): ?>
                            <label class="form-check-label small cursor-pointer d-flex align-items-center gap-2">
                                <input type="checkbox" name="storage[]" value="<?= $storeVal ?>" class="form-check-input"
                                       <?= in_array($storeVal, $filters['storage'] ?? []) ? 'checked' : '' ?>
                                       onchange="this.form.requestSubmit()">
                                <?= $storeVal ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="mb-4">
                    <h6 class="fw-bold mb-3"><i class="bi bi-box-seam me-2"></i>Trạng thái</h6>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="in_stock" id="inStockCheckMobile" value="1" <?= $filters['in_stock'] ? 'checked' : '' ?> onchange="this.form.requestSubmit()">
                        <label class="form-check-label small" for="inStockCheckMobile">Chỉ còn hàng</label>
                    </div>
                </div>

                <div class="pt-3 border-top d-flex gap-2">
                    <a href="/" class="btn btn-outline-danger btn-sm w-50 d-flex align-items-center justify-content-center"><i class="bi bi-x-circle me-1"></i> Xóa bộ lọc</a>
                    <button type="submit" class="btn btn-dark btn-sm w-50">Lọc</button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>

<!-- Phần sản phẩm đã xem (luôn hiển thị nếu có) -->
<?php
$recentlyViewedIds = $_SESSION['recently_viewed'] ?? [];
if (!empty($recentlyViewedIds)):
    $productModel = new \App\Models\Product();
    $catModel = new \App\Models\Category();
    $categories = $catModel->all();
    $categoryMap = [];
    foreach ($categories as $cat) {
        $categoryMap[$cat['id']] = $cat['name'];
    }

    $recentlyViewedProducts = [];
    foreach ($recentlyViewedIds as $pId) {
        $p = $productModel->findWithPricing((int) $pId);
        if ($p && ($p['status'] ?? 'active') === 'active') {
            $recentlyViewedProducts[] = $p;
        }
    }
    $recentlyViewedProducts = enrichProductsForDisplay($recentlyViewedProducts, $categoryMap);

    if (!empty($recentlyViewedProducts)):
?>
    <div class="section-container mb-5 mt-5">
        <div class="section-title section-header-line">
            <h2>Sản phẩm đã xem gần đây</h2>
        </div>
        <div class="product-grid">
            <?php foreach ($recentlyViewedProducts as $product): ?>
                <?php require BASE_PATH . '/views/partials/product_card.php'; ?>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; endif; ?>

<?php if ($isHome): ?>
<div class="trust-bar">
    <div class="trust-item"><i class="bi bi-shield-check"></i> Hàng chính hãng 100%</div>
    <div class="trust-item"><i class="bi bi-arrow-repeat"></i> Đổi trả trong 7 ngày</div>
    <div class="trust-item"><i class="bi bi-truck"></i> Giao hàng nhanh toàn quốc</div>
    <div class="trust-item"><i class="bi bi-headset"></i> Hỗ trợ 24/7</div>
</div>
<?php endif; ?>

<!-- Floating Promo Badge & Modal ... (Giữ nguyên như cũ) -->
<div class="floating-promo-badge" id="floatingPromo">
    <button class="close-promo-btn" onclick="closePromo(event)">×</button>
    <div class="promo-badge-content" onclick="handlePromoClick()">
        <div class="promo-gift-icon"><i class="bi bi-gift"></i></div>
        <div class="promo-text">Ưu đãi<br>đặc biệt</div>
    </div>
</div>

<div class="modal fade" id="specialPromoModal" tabindex="-1" aria-labelledby="specialPromoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 16px; border: none; overflow: hidden; box-shadow: var(--shadow-md);">
            <div class="modal-header" style="background: var(--accent); border: none; color: #fff; padding: 20px;">
                <h5 class="modal-title" id="specialPromoModalLabel" style="font-weight: 700; font-size: 1.2rem;">Ưu đãi đặc biệt dành cho bạn</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="background: var(--bg-page); padding: 24px;">
                <p class="text-muted mb-4 text-center">Chúc mừng bạn đã đăng nhập! Dưới đây là các sản phẩm đang có mức ưu đãi khủng nhất hôm nay:</p>
                <div class="row g-3">
                    <?php
                    $promoSource = (new \App\Models\Product())->allActive();
                    $promoProducts = array_values(array_filter($promoSource, fn ($item) => !empty($item['has_discount'])));
                    usort($promoProducts, fn ($a, $b) => (int) $b['discount_percent'] <=> (int) $a['discount_percent']);
                    $promoProducts = array_slice($promoProducts, 0, 3);
                    foreach ($promoProducts as $p):
                    ?>
                        <div class="col-md-4">
                            <div class="product-card h-100" style="background: #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.06); border-radius: 12px; overflow: hidden; display: flex; flex-direction: column;">
                                <div style="position: relative; padding-top: 75%; background: #fafafa;">
                                    <span class="product-badge" style="background: #d70018; color: #fff; position: absolute; top: 10px; left: 10px; font-size: 11px; font-weight: 700; padding: 3px 8px; border-radius: 4px;">-<?= (int) $p['discount_percent'] ?>%</span>
                                    <img src="<?= e(imageUrl($p['image'] ?? '')) ?>" alt="<?= e($p['name']) ?>" style="position: absolute; inset:0; width:100%; height:100%; object-fit:cover;">
                                </div>
                                <div class="p-3 d-flex flex-column justify-content-between" style="flex: 1;">
                                    <div>
                                        <h6 style="font-weight: 700; font-size: 14px; margin-bottom: 4px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;"><?= e($p['name']) ?></h6>
                                        <div style="color: #d70018; font-weight: 800; font-size: 15px; margin-bottom: 2px;">
                                            <?= formatMoney($p['sale_price']) ?>
                                        </div>
                                        <div style="color: #999; font-size: 12px; text-decoration: line-through; margin-bottom: 10px;">
                                            <?= formatMoney($p['original_price']) ?>
                                        </div>
                                    </div>
                                    <form method="post" action="/cart/add/<?= $p['id'] ?>">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn-buy" style="font-size: 12px; padding: 8px; width: 100%;"><i class="bi bi-bag-plus"></i> Thêm vào giỏ</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="modal-footer" style="background: #f8f9fa; border: none; padding: 15px 24px;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 8px; font-weight: 600; padding: 8px 16px;">Đóng</button>
            </div>
        </div>
    </div>
</div>

<script>
// Helper function to update query string
function updateQueryString(key, value) {
    const url = new URL(window.location.href);
    if (value) {
        url.searchParams.set(key, value);
    } else {
        url.searchParams.delete(key);
    }
    if (key !== 'page') url.searchParams.delete('page');
    return url.pathname + url.search;
}

document.querySelectorAll('#filterForm, #mobileFilterForm').forEach(function (form) {
    form.addEventListener('submit', function () {
        form.querySelectorAll('input, select').forEach(function (control) {
            if (!control.name) return;
            if ((control.type === 'checkbox' || control.type === 'radio') && !control.checked) return;
            if (String(control.value).trim() === '' || (control.name === 'sort' && control.value === 'newest')) {
                control.disabled = true;
            }
        });
    });
});

document.addEventListener('DOMContentLoaded', function() {
    // Carousel autoplay
    var myCarousel = document.querySelector('#promoCarousel');
    if (myCarousel) {
        var carousel = new bootstrap.Carousel(myCarousel, {
            interval: 10000, ride: 'carousel', pause: false
        });
        carousel.cycle();
    }

    // Countdown timer
    (function() {
        const timerEl = document.getElementById('flashSaleCountdown');
        if (!timerEl) return;

        const expireStr = timerEl.dataset.expire;
        const targetTime = new Date(expireStr).getTime();

        function updateTimer() {
            const now = Date.now();
            const diff = targetTime - now;

            if (diff <= 0) {
                timerEl.innerHTML = '<span class="text-white small fw-bold">KHUYẾN MÃI ĐÃ KẾT THÚC</span>';
                return;
            }

            const hours = Math.floor(diff / 3600000);
            const mins = Math.floor((diff % 3600000) / 60000);
            const secs = Math.floor((diff % 60000) / 1000);

            if (document.getElementById('timer-hour')) {
                document.getElementById('timer-hour').textContent = String(hours).padStart(2, '0');
                document.getElementById('timer-min').textContent = String(mins).padStart(2, '0');
                document.getElementById('timer-sec').textContent = String(secs).padStart(2, '0');
            }
        }
        updateTimer();
        setInterval(updateTimer, 1000);
    })();
});

const isLoggedIn = <?= authUser() ? 'true' : 'false' ?>;
function handlePromoClick() {
    const el = document.getElementById('floatingPromo');
    if (el && el.getAttribute('data-dragged') === 'true') return;
    if (!isLoggedIn) window.location.href = '/login';
    else (new bootstrap.Modal(document.getElementById('specialPromoModal'))).show();
}
function closePromo(event) {
    event.stopPropagation();
    document.getElementById('floatingPromo').style.display = 'none';
}
function toggleMoreProducts(sectionId, btn) {
    const extras = document.querySelectorAll('.extra-product-' + sectionId);
    let isHidden = false;
    extras.forEach(el => {
        if (el.classList.contains('d-none')) { el.classList.remove('d-none'); isHidden = true; }
        else el.classList.add('d-none');
    });
    btn.innerText = isHidden ? 'Thu gọn' : 'Xem thêm';
}
</script>
