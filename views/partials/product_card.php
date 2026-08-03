<?php
/** @var array $product */
$extraClass = $extraClass ?? '';
$isCardWishlisted = false;
if ($uCard = authUser()) {
    static $userWishlistIds = null;
    if ($userWishlistIds === null) {
        $userWishlistIds = (new \App\Models\Wishlist())->getProductIds((int)$uCard['id']);
    }
    $isCardWishlisted = in_array((int)$product['id'], $userWishlistIds, true);
}
?>
<div class="product-card <?= e($extraClass) ?> position-relative">
    <button type="button" class="btn-card-wishlist position-absolute top-0 end-0 m-2 border-0 bg-white rounded-circle shadow-sm p-0"
            style="width: 32px; height: 32px; z-index: 3; color: #dc3545; display: grid; place-content: center; cursor: pointer; transition: transform 0.2s ease;"
            onclick="toggleCardWishlist(event, <?= (int)$product['id'] ?>, this)"
            data-wishlist-product-id="<?= (int)$product['id'] ?>"
            data-product-name="<?= e($product['name']) ?>"
            data-product-image="<?= e(imageUrl($product['image'] ?? '', 'thumb')) ?>"
            data-product-price="<?= (int)($product['sale_price'] ?? $product['price'] ?? 0) ?>"
            data-product-original-price="<?= (int)($product['original_price'] ?? $product['price'] ?? 0) ?>"
            data-wishlisted="<?= $isCardWishlisted ? '1' : '0' ?>"
            title="<?= $isCardWishlisted ? 'Xóa khỏi yêu thích' : 'Thêm vào yêu thích' ?>">
        <i class="bi <?= $isCardWishlisted ? 'bi-heart-fill' : 'bi-heart' ?>"></i>
    </button>
    <a href="/products/<?= (int) $product['id'] ?>" class="img-wrap" style="display:block;text-decoration:none">
        <?php if (!empty($product['has_discount'])): ?>
            <span class="product-badge">-<?= (int) $product['discount_percent'] ?>%</span>
        <?php endif; ?>
        <img src="<?= e(imageUrl($product['image'] ?? '', 'medium')) ?>" alt="<?= e($product['name']) ?>" loading="lazy">
    </a>
    <div class="product-body">
        <div class="product-cat"><?= e($product['category_name'] ?? 'Khác') ?></div>
        <a href="/products/<?= (int) $product['id'] ?>" class="product-name" style="text-decoration:none;color:inherit">
            <?= e($product['name']) ?>
        </a>
        <div class="product-rating d-flex align-items-center mb-1 text-warning">
            <span>
                <?php
                $avg = $product['rating_avg'] ?? 5.0;
                for ($i = 1; $i <= 5; $i++) {
                    echo $i <= round($avg) ? '★' : '☆';
                }
                ?>
            </span>
            <?php if (($product['rating_count'] ?? 0) > 0): ?>
                <span class="text-muted">(<?= (int) $product['rating_count'] ?> đánh giá)</span>
            <?php endif; ?>
        </div>
        <div class="product-desc"><?= e($product['description'] ?? '') ?></div>
        <?php require BASE_PATH . '/views/partials/product_price.php'; ?>
        <div class="d-flex align-items-center mb-2 mt-1">
            <label class="compare-checkbox-label small text-muted d-flex align-items-center gap-1.5 cursor-pointer" style="font-size: 11px;">
                <input type="checkbox" class="compare-checkbox form-check-input" style="width: 13px; height: 13px;"
                       data-product-id="<?= (int)$product['id'] ?>" 
                       data-product-name="<?= e($product['name']) ?>" 
                       data-product-image="<?= e(imageUrl($product['image'] ?? '', 'thumb')) ?>" 
                       onchange="toggleCompareProduct(this)">
                So sánh sản phẩm
            </label>
        </div>
        <div class="product-actions">
            <form method="post" action="/cart/add/<?= (int) $product['id'] ?>">
                <?= csrf_field() ?>
                <button type="submit" class="btn-buy"><i class="bi bi-bag-plus"></i> Thêm vào giỏ</button>
            </form>
            <a href="/products/<?= (int) $product['id'] ?>" class="btn-view-more" aria-label="Xem chi tiết <?= e($product['name']) ?>"><i class="bi bi-arrow-right"></i></a>
        </div>
    </div>
</div>
