<?php
$title = 'Sản phẩm yêu thích - Shop Hades';
$items = $wishlistItems ?? $items ?? [];
$isGuestWishlist = $isGuestWishlist ?? !authUser();
?>

<style>
.wishlist-banner-header {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    border-radius: 20px;
    padding: 28px 32px;
    color: #fff;
    margin-bottom: 28px;
    box-shadow: 0 10px 25px rgba(15, 23, 42, 0.12);
    position: relative;
    overflow: hidden;
}
.wishlist-banner-header::after {
    content: '';
    position: absolute;
    right: -30px;
    top: -30px;
    width: 200px;
    height: 200px;
    background: rgba(239, 68, 68, 0.15);
    border-radius: 50%;
    filter: blur(40px);
}
.wishlist-card-modern {
    background: #ffffff;
    border-radius: 18px;
    border: 1px solid #e2e8f0;
    transition: all 0.25s ease;
    overflow: hidden;
    height: 100%;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.03);
}
.wishlist-card-modern:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
    border-color: #cbd5e1;
}
.wishlist-img-wrap {
    position: relative;
    padding-top: 80%;
    overflow: hidden;
    background: #f8fafc;
}
.wishlist-img-wrap img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: contain;
    padding: 16px;
    transition: transform 0.3s ease;
}
.wishlist-card-modern:hover .wishlist-img-wrap img {
    transform: scale(1.06);
}
</style>

<section class="wishlist-page py-4">
    <div class="container">
        <!-- Banner Header -->
        <div class="wishlist-banner-header d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <span class="badge bg-danger rounded-pill px-3 py-1 fw-bold mb-2">DANH SÁCH CỦA BẠN</span>
                <h2 class="h3 fw-bold mb-1 text-white"><i class="bi bi-heart-fill me-2 text-danger"></i>Sản Phẩm Yêu Thích</h2>
                <p class="mb-0 text-slate-300 small" style="color: #cbd5e1;">Lưu lại sản phẩm bạn quan tâm để dễ dàng so sánh và mua sau.</p>
            </div>
            <div>
                <a href="/products" class="btn btn-outline-light btn-sm rounded-3 px-3 py-2 fw-semibold">
                    <i class="bi bi-shop me-1"></i> Khám phá sản phẩm khác
                </a>
            </div>
        </div>

        <?php if ($isGuestWishlist): ?>
            <div class="alert alert-info border-0 rounded-4 shadow-sm d-flex gap-3 align-items-center mb-4 p-3" style="background: #e0f2fe; color: #0369a1;">
                <i class="bi bi-cloud-arrow-up fs-3 text-primary"></i>
                <div>
                    <strong class="d-block mb-0">Danh sách đang được lưu trên trình duyệt này.</strong>
                    <span class="small opacity-90"><a href="/login" class="fw-bold text-primary text-decoration-underline">Đăng nhập ngay</a> để đồng bộ danh sách yêu thích lên mọi thiết bị.</span>
                </div>
            </div>
        <?php endif; ?>

        <div class="d-flex align-items-center justify-content-between mb-4 bg-white p-3 rounded-4 border shadow-sm">
            <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                <i class="bi bi-bookmark-heart text-danger"></i> Đã Lưu Chi Tiết
            </h5>
            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-1.5 fw-bold" style="font-size: 13px;">
                <strong id="wishlist-page-count"><?= count($items) ?></strong> sản phẩm
            </span>
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
                    <article class="wishlist-card-modern d-flex flex-column">
                        <a href="/products/<?= $productId ?>" class="wishlist-img-wrap text-decoration-none">
                            <img src="<?= e($item['image_url'] ?? imageUrl($item['image'] ?? '', 'medium')) ?>"
                                 alt="<?= e($item['name'] ?? 'Sản phẩm') ?>"
                                 loading="lazy">
                            <?php if ($discountPercent > 0): ?>
                                <span class="badge bg-danger position-absolute top-0 start-0 m-3 px-2.5 py-1 rounded-pill fw-bold" style="font-size: 11px;">-<?= $discountPercent ?>%</span>
                            <?php endif; ?>
                        </a>
                        <div class="p-3 d-flex flex-column flex-grow-1">
                            <?php if (!empty($item['category_name'])): ?>
                                <span class="small text-muted mb-1" style="font-size: 11px;"><?= e($item['category_name']) ?></span>
                            <?php endif; ?>
                            <h3 class="h6 fw-bold text-truncate mb-2" style="font-size: 14px;">
                                <a href="/products/<?= $productId ?>" class="text-decoration-none text-dark">
                                    <?= e($item['name'] ?? 'Sản phẩm') ?>
                                </a>
                            </h3>
                            <div class="mt-auto pt-2">
                                <div class="d-flex align-items-baseline gap-2 mb-3">
                                    <strong class="text-danger fs-6 fw-bold"><?= number_format($salePrice, 0, ',', '.') ?>đ</strong>
                                    <?php if ($originalPrice > $salePrice): ?>
                                        <del class="small text-muted" style="font-size: 11px;"><?= number_format($originalPrice, 0, ',', '.') ?>đ</del>
                                    <?php endif; ?>
                                </div>
                                <div class="d-grid gap-2">
                                    <a href="/products/<?= $productId ?>" class="btn btn-dark btn-sm rounded-3 fw-bold">
                                        <i class="bi bi-eye me-1"></i> Xem Chi Tiết
                                    </a>
                                    <button type="button"
                                            class="btn btn-outline-danger btn-sm rounded-3 fw-semibold js-wishlist-remove"
                                            data-product-id="<?= $productId ?>">
                                        <i class="bi bi-trash3 me-1"></i> Bỏ yêu thích
                                    </button>
                                </div>
                            </div>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>

        <div id="wishlist-empty" class="wishlist-empty text-center bg-white border rounded-4 py-5 px-3 shadow-sm<?= $items ? ' d-none' : '' ?>">
            <i class="bi bi-heartbreak text-secondary opacity-50 display-3 d-block mb-2"></i>
            <h3 class="h5 fw-bold text-dark mt-2">Danh sách yêu thích đang trống</h3>
            <p class="text-muted small mb-4">Nhấn biểu tượng trái tim trên các sản phẩm để lưu lại tại đây.</p>
            <a href="/products" class="btn btn-danger rounded-pill px-4 py-2 fw-bold" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); border: none;">
                Khám Phá Sản Phẩm Ngay
            </a>
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
                <article class="wishlist-card-modern d-flex flex-column">
                    <a href="/products/${id}" class="wishlist-img-wrap text-decoration-none">
                        <img src="${image}" alt="${name}" loading="lazy">
                    </a>
                    <div class="p-3 d-flex flex-column flex-grow-1">
                        <h3 class="h6 fw-bold text-truncate mb-2" style="font-size: 14px;">
                            <a href="/products/${id}" class="text-decoration-none text-dark">${name}</a>
                        </h3>
                        <div class="mt-auto pt-2">
                            <div class="d-flex align-items-baseline gap-2 mb-3">
                                <strong class="text-danger fs-6 fw-bold">${formatPrice(salePrice)}</strong>
                                ${originalPrice > salePrice ? `<del class="small text-muted" style="font-size: 11px;">${formatPrice(originalPrice)}</del>` : ''}
                            </div>
                            <div class="d-grid gap-2">
                                <a href="/products/${id}" class="btn btn-dark btn-sm rounded-3 fw-bold">
                                    <i class="bi bi-eye me-1"></i> Xem Chi Tiết
                                </a>
                                <button type="button" class="btn btn-outline-danger btn-sm rounded-3 fw-semibold js-wishlist-remove" data-product-id="${id}">
                                    <i class="bi bi-trash3 me-1"></i> Bỏ yêu thích
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
