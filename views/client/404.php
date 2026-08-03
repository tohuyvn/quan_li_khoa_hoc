<?php 
$title = '404 - Không tìm thấy trang'; 
$productModel = new \App\Models\Product();
$hotProducts = array_slice($productModel->allActive(), 0, 4);
?>

<div class="content-card text-center py-5">
    <div style="font-size: 100px; line-height: 1; margin-bottom: 20px; font-weight: 800; color: var(--border);">404</div>
    <h2 class="fw-bold mb-3">Rất tiếc, trang bạn tìm kiếm không tồn tại!</h2>
    <p class="text-muted mb-4 mx-auto" style="max-width: 500px;">
        Có vẻ như đường dẫn đã bị hỏng hoặc trang web đã được di chuyển.
        Hãy thử tìm kiếm sản phẩm khác hoặc quay về trang chủ.
    </p>

    <div class="d-flex gap-2 justify-content-center flex-wrap mb-5">
        <a href="/" class="btn btn-primary-hades" style="width: auto; padding: 12px 40px;">Quay về trang chủ</a>
        <a href="javascript:history.back()" class="btn btn-outline-secondary" style="width: auto; padding: 12px 32px;">Quay lại trang trước</a>
    </div>

    <?php if (!empty($hotProducts)): ?>
        <div class="pt-5 border-top">
            <h5 class="fw-bold mb-4" style="color: var(--accent);">Sản phẩm nổi bật gợi ý cho bạn</h5>
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
