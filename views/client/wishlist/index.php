<?php
$title = 'Sản phẩm yêu thích - Shop Hades';
$items = $wishlistItems ?? $items ?? [];
$isGuestWishlist = $isGuestWishlist ?? !authUser();
?>

<section class="wishlist-page py-4 py-lg-5">
    <div class="container">
        <div class="wishlist-heading d-flex flex-column flex-md-row align-items-md-end justify-content-between gap-3 mb-4">
            <div>
                <span class="wishlist-eyebrow">Danh sách của bạn</span>
                <h1 class="h2 fw-bold mb-1">Sản phẩm yêu thích</h1>
                <p class="text-muted mb-0">
                    Lưu lại sản phẩm bạn quan tâm để dễ dàng so sánh và mua sau.
                </p>
            </div>
            <a href="/products" class="btn btn-outline-primary">
                <i class="bi bi-arrow-left me-2"></i>Tiếp tục mua sắm
            </a>
        </div>

        <?php if ($isGuestWishlist): ?>
            <div class="alert alert-primary-subtle border-0 rounded-4 d-flex gap-3 align-items-start mb-4" role="status">
                <i class="bi bi-cloud-arrow-up fs-4"></i>
                <div>
                    <strong>Danh sách đang được lưu trên thiết bị này.</strong>
                    <div class="small mt-1">
                        <a href="/login" class="alert-link">Đăng nhập</a> để tự động đồng bộ và xem lại trên mọi thiết bị.
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="d-flex align-items-center justify-content-between mb-3">
            <h2 class="h5 fw-bold mb-0">Đã lưu</h2>
            <span class="text-muted"><strong id="wishlist-page-count"><?= count($items) ?></strong> sản phẩm</span>
        </div>

        <div id="wishlist-grid" class="row g-3 g-lg-4" data-guest="<?= $isGuestWishlist ? '1' : '0' ?>">
            <?php foreach ($items as $item): ?>
                <?php
                $productId = (int) ($item['product_id'] ?? $item['id'] ?? 0);
                $salePrice = (float) ($item['sale_price'] ?? $item['price'] ?? 0);
                $originalPrice = (float) ($item['original_price'] ?? $salePrice);
                $discountPercent = (int) ($item['discount_percent'] ?? 0);
                ?>
                <div class="col-6 col-md-4 col-xl-3 wishlist-item" data-product-id="<?= $productId ?>">
                    <article class="card h-100 border-0 shadow-sm wishlist-card">
                        <a href="/products/<?= $productId ?>" class="wishlist-card-image">
                            <img src="<?= e($item['image_url'] ?? imageUrl($item['image'] ?? '', 'medium')) ?>"
                                 alt="<?= e($item['name'] ?? 'Sản phẩm') ?>"
                                 loading="lazy">
                            <?php if ($discountPercent > 0): ?>
                                <span class="badge text-bg-danger">-<?= $discountPercent ?>%</span>
                            <?php endif; ?>
                        </a>
                        <div class="card-body d-flex flex-column">
                            <?php if (!empty($item['category_name'])): ?>
                                <span class="small text-muted mb-1"><?= e($item['category_name']) ?></span>
                            <?php endif; ?>
                            <h3 class="h6 fw-bold line-clamp-2 mb-3">
                                <a href="/products/<?= $productId ?>" class="text-decoration-none text-body">
                                    <?= e($item['name'] ?? 'Sản phẩm') ?>
                                </a>
                            </h3>
                            <div class="mt-auto">
                                <div class="d-flex flex-wrap align-items-baseline gap-2 mb-3">
                                    <strong class="text-danger"><?= number_format($salePrice, 0, ',', '.') ?>đ</strong>
                                    <?php if ($originalPrice > $salePrice): ?>
                                        <del class="small text-muted"><?= number_format($originalPrice, 0, ',', '.') ?>đ</del>
                                    <?php endif; ?>
                                </div>
                                <div class="d-grid gap-2">
                                    <a href="/products/<?= $productId ?>" class="btn btn-primary">Xem sản phẩm</a>
                                    <button type="button"
                                            class="btn btn-light border js-wishlist-remove"
                                            data-product-id="<?= $productId ?>">
                                        <i class="bi bi-trash3 me-1"></i>Xoá khỏi danh sách
                                    </button>
                                </div>
                            </div>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>

        <div id="wishlist-empty" class="wishlist-empty text-center bg-white border rounded-4 py-5 px-3<?= $items ? ' d-none' : '' ?>">
            <span class="wishlist-empty-icon"><i class="bi bi-heart"></i></span>
            <h2 class="h4 fw-bold mt-3">Danh sách yêu thích đang trống</h2>
            <p class="text-muted mb-4">Nhấn biểu tượng trái tim trên sản phẩm để lưu lại tại đây.</p>
            <a href="/products" class="btn btn-primary px-4">Khám phá sản phẩm</a>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const grid = document.getElementById('wishlist-grid');
    const emptyState = document.getElementById('wishlist-empty');
    const countNode = document.getElementById('wishlist-page-count');
    const isGuest = grid?.dataset.guest === '1';

    const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (character) => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    })[character]);

    const formatPrice = (value) => new Intl.NumberFormat('vi-VN').format(Number(value) || 0) + 'đ';

    const refreshState = () => {
        const total = grid?.querySelectorAll('.wishlist-item').length || 0;
        if (countNode) countNode.textContent = String(total);
        emptyState?.classList.toggle('d-none', total > 0);
    };

    const cardTemplate = (item) => {
        const id = Number(item.id || item.product_id || 0);
        const name = escapeHtml(item.name || 'Sản phẩm');
        const image = escapeHtml(item.image || '/images/placeholder.svg');
        const salePrice = Number(item.sale_price || item.salePrice || item.price || 0);
        const originalPrice = Number(item.original_price || item.originalPrice || salePrice);

        return `
            <div class="col-6 col-md-4 col-xl-3 wishlist-item" data-product-id="${id}">
                <article class="card h-100 border-0 shadow-sm wishlist-card">
                    <a href="/products/${id}" class="wishlist-card-image">
                        <img src="${image}" alt="${name}" loading="lazy">
                    </a>
                    <div class="card-body d-flex flex-column">
                        <h3 class="h6 fw-bold line-clamp-2 mb-3">
                            <a href="/products/${id}" class="text-decoration-none text-body">${name}</a>
                        </h3>
                        <div class="mt-auto">
                            <div class="d-flex flex-wrap align-items-baseline gap-2 mb-3">
                                <strong class="text-danger">${formatPrice(salePrice)}</strong>
                                ${originalPrice > salePrice ? `<del class="small text-muted">${formatPrice(originalPrice)}</del>` : ''}
                            </div>
                            <div class="d-grid gap-2">
                                <a href="/products/${id}" class="btn btn-primary">Xem sản phẩm</a>
                                <button type="button" class="btn btn-light border js-wishlist-remove" data-product-id="${id}">
                                    <i class="bi bi-trash3 me-1"></i>Xoá khỏi danh sách
                                </button>
                            </div>
                        </div>
                    </div>
                </article>
            </div>`;
    };

    if (isGuest && window.HadesWishlist && grid) {
        const guestItems = window.HadesWishlist.getGuestItems();
        grid.innerHTML = guestItems.map(cardTemplate).join('');
        refreshState();
    }

    grid?.addEventListener('click', async function (event) {
        const button = event.target.closest('.js-wishlist-remove');
        if (!button) return;

        const productId = Number(button.dataset.productId);
        button.disabled = true;

        try {
            if (isGuest) {
                window.HadesWishlist?.removeGuest(productId);
            } else {
                const response = await fetch(`/wishlist/toggle/${productId}`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    }
                });
                const data = await response.json();
                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Không thể cập nhật danh sách yêu thích.');
                }
            }

            button.closest('.wishlist-item')?.remove();
            refreshState();
            window.showToast?.('info', 'Đã xoá sản phẩm khỏi danh sách yêu thích.');
        } catch (error) {
            button.disabled = false;
            window.showToast?.('error', error.message);
        }
    });
});
</script>
