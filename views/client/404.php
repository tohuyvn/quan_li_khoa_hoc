<?php 
$title = '404 - Không tìm thấy trang'; 
$productModel = new \App\Models\Product();
$hotProducts = array_slice($productModel->allActive(), 0, 4);
?>

<div class="content-card text-center py-5 position-relative overflow-hidden" style="border-radius: 20px;">
    <!-- Decorative background glow -->
    <div style="position: absolute; top: -100px; left: 50%; transform: translateX(-50%); width: 300px; height: 300px; background: radial-gradient(circle, rgba(239, 68, 68, 0.15) 0%, rgba(0,0,0,0) 70%); pointer-events: none;"></div>

    <div class="mb-3 d-inline-block px-4 py-2 rounded-pill bg-danger bg-opacity-10 text-danger fw-extrabold" style="font-size: 1.2rem; letter-spacing: 2px;">
        ⚠️ ERROR 404
    </div>

    <h1 style="font-size: 120px; line-height: 1; margin-bottom: 10px; font-weight: 900; background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; text-shadow: 0 10px 20px rgba(239, 68, 68, 0.2);">
        404
    </h1>

    <h2 class="fw-bold mb-3 text-dark">Rất tiếc, trang bạn tìm kiếm không tồn tại!</h2>
    <p class="text-muted mb-4 mx-auto" style="max-width: 540px; font-size: 0.95rem;">
        Có vẻ như đường dẫn đã bị hỏng hoặc sản phẩm này không còn tồn tại trên hệ thống Shop Hades.
        Hãy thử tìm kiếm sản phẩm khác bên dưới hoặc quay về trang chủ.
    </p>

    <!-- Interactive Search Bar -->
    <div class="mx-auto mb-4" style="max-width: 480px;">
        <form action="/" method="get" class="d-flex gap-2 shadow-sm p-1.5 bg-light rounded-pill border">
            <input type="text" name="q" class="form-control border-0 bg-transparent ps-3 text-dark" placeholder="Nhập tên sản phẩm cần tìm..." style="box-shadow: none;">
            <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold flex-shrink-0">
                <i class="bi bi-search me-1"></i> Tìm kiếm
            </button>
        </form>
    </div>

    <div class="d-flex gap-2 justify-content-center flex-wrap mb-5">
        <a href="/" class="btn btn-primary-hades rounded-pill" style="width: auto; padding: 12px 36px; font-weight: 600;">
            <i class="bi bi-house-fill me-1"></i> Quay về trang chủ
        </a>
        <a href="/cart" class="btn btn-outline-danger rounded-pill" style="width: auto; padding: 12px 28px; font-weight: 600;">
            <i class="bi bi-bag-fill me-1"></i> Xem giỏ hàng
        </a>
        <a href="javascript:history.back()" class="btn btn-outline-secondary rounded-pill" style="width: auto; padding: 12px 28px; font-weight: 600;">
            <i class="bi bi-arrow-left me-1"></i> Quay lại
        </a>
    </div>

    <?php if (!empty($hotProducts)): ?>
        <div class="pt-5 border-top text-start">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-fire text-danger me-2"></i>Sản phẩm nổi bật dành cho bạn
                </h5>
                <a href="/" class="btn btn-sm btn-link text-decoration-none">Xem tất cả <i class="bi bi-chevron-right ms-1"></i></a>
            </div>
            <div class="product-grid">
                <?php 
                $catModel = new \App\Models\Category();
                $categories = $catModel->all();
                $categoryMap = [];
                foreach ($categories as $cat) {
                    $categoryMap[$cat['id']] = $cat['name'];
                }
                $formattedProducts = enrichProductsForDisplay($hotProducts, $categoryMap);
                foreach ($formattedProducts as $product):
                    require BASE_PATH . '/views/partials/product_card.php';
                endforeach;
                ?>
            </div>
        </div>
    <?php endif; ?>
</div>

