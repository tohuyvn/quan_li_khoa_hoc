<?php $title = 'Giỏ hàng'; ?>

<div class="section-title"><h2>Giỏ hàng của bạn</h2></div>

<?php if (empty($cart)): ?>
    <div class="content-card text-center py-5">
        <div style="font-size: 64px; opacity: 0.3; color: var(--text-muted);"><i class="bi bi-cart-x"></i></div>
        <h4 class="fw-bold mt-3">Giỏ hàng của bạn đang trống</h4>
        <p class="text-muted mb-4 mx-auto" style="max-width: 400px;">
            Hãy chọn các sản phẩm chất lượng cao từ Hades Shop để bắt đầu trải nghiệm mua sắm tuyệt vời.
        </p>
        <a href="/" class="btn btn-primary-hades" style="width:auto;display:inline-block;padding:12px 40px;">Khám phá ngay</a>
    </div>
<?php else: ?>
    <nav class="checkout-steps mb-4" aria-label="Tiến trình đặt hàng">
        <ol class="checkout-steps-list">
            <li class="checkout-step active" aria-current="step">
                <span class="checkout-step-num">1</span>
                <span class="checkout-step-label">Giỏ hàng</span>
            </li>
            <li class="checkout-step">
                <span class="checkout-step-num">2</span>
                <span class="checkout-step-label">Thanh toán</span>
            </li>
            <li class="checkout-step">
                <span class="checkout-step-num">3</span>
                <span class="checkout-step-label">Xác nhận</span>
            </li>
        </ol>
    </nav>
    <p class="mb-3">
        <a href="/" style="color:var(--accent);text-decoration:none"><i class="bi bi-arrow-left"></i> Tiếp tục mua sắm</a>
    </p>
    <div class="content-card" id="cart-table-wrap">
        <form method="post" action="/cart/update">
            <?= csrf_field() ?>
            <div class="table-responsive">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Đơn giá</th>
                            <th width="120">Số lượng</th>
                            <th>Thành tiền</th>
                            <th width="60"></th>
                        </tr>
                    </thead>
                    <tbody id="cart-tbody">
                        <?php foreach ($cart as $id => $item): ?>
                            <tr id="cart-row-<?= e($id) ?>">
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <a href="/products/<?= (int) $item['product_id'] ?>" class="cart-thumb">
                                            <img src="<?= e(imageUrl($item['image'] ?? '', 'thumb')) ?>" alt="<?= e($item['name']) ?>">
                                        </a>
                                        <div>
                                            <a href="/products/<?= (int) $item['product_id'] ?>" class="fw-bold text-decoration-none" style="color:inherit">
                                                <?= e($item['name']) ?>
                                            </a>
                                            <?php if (!empty($item['color'])): ?>
                                                <div class="small text-muted mt-1" style="font-size: 11px;">Phân loại: <span class="badge bg-light text-dark border"><?= e($item['color']) ?></span></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?= formatMoney($item['price']) ?>
                                    <?php if (!empty($item['has_discount'])): ?>
                                        <div class="small text-muted text-decoration-line-through">
                                            <?= formatMoney($item['original_price']) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="input-group input-group-sm cart-qty-stepper" style="width: 110px;">
                                        <button class="btn btn-outline-secondary btn-qty-minus" type="button" data-id="<?= e($id) ?>">-</button>
                                        <input type="number" name="quantity[<?= e($id) ?>]" value="<?= (int) $item['quantity'] ?>"
                                               min="0" class="form-control text-center input-cart-qty" data-id="<?= e($id) ?>" style="padding: 2px 4px;">
                                        <button class="btn btn-outline-secondary btn-qty-plus" type="button" data-id="<?= e($id) ?>">+</button>
                                    </div>
                                </td>
                                <td class="fw-bold cart-row-subtotal" id="subtotal-<?= e($id) ?>" style="color:var(--price); transition: color 0.3s ease;"><?= formatMoney($item['price'] * $item['quantity']) ?></td>
                                <td>
                                    <button type="button"
                                        class="btn btn-sm btn-outline-danger btn-remove-cart"
                                        data-product-id="<?= e($id) ?>"
                                        data-csrf="<?= e(csrf_token()) ?>"
                                        title="Xóa sản phẩm">✕</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p class="text-muted small mt-2 mb-0">💡 Tự động cập nhật số lượng khi bấm <strong>+</strong> / <strong>-</strong> hoặc chỉnh số trực tiếp.</p>
            <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-3">
                <span class="cart-total" id="cart-total-text">Tổng cộng: <?= formatMoney($total) ?></span>
                <div class="d-flex gap-2 flex-wrap">
                    <button type="submit" class="btn btn-secondary" id="btnManualUpdate">🔄 Cập nhật số lượng</button>
                    <a href="/checkout" class="btn btn-primary-hades" style="width:auto;padding:12px 28px;">Đặt hàng →</a>
                </div>
            </div>
        </form>
    </div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const cartForm = document.querySelector('form[action="/cart/update"]');
    
    // Auto AJAX update on quantity change
    function triggerCartUpdate() {
        if (!cartForm) return;
        const formData = new FormData(cartForm);
        
        fetch('/cart/update', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Update total
                const totalEl = document.getElementById('cart-total-text');
                if (totalEl) totalEl.textContent = 'Tổng cộng: ' + data.totalFormatted;
                
                // Update items subtotals
                if (data.items) {
                    data.items.forEach(item => {
                        const subtotalEl = document.getElementById('subtotal-' + item.key);
                        if (subtotalEl) {
                            subtotalEl.textContent = item.subtotalFormatted;
                            subtotalEl.style.color = 'var(--accent)';
                            setTimeout(() => subtotalEl.style.color = 'var(--price)', 400);
                        }
                    });
                }

                // Update navbar badge
                const badge = document.querySelector('#cart-icon .cart-badge');
                if (badge) {
                    if (data.cartCount > 0) {
                        badge.textContent = data.cartCount;
                        badge.style.display = 'flex';
                        badge.classList.remove('bounce');
                        void badge.offsetWidth;
                        badge.classList.add('bounce');
                    } else {
                        badge.style.display = 'none';
                    }
                }

                // Empty state if count 0
                if (data.cartCount === 0) {
                    const wrap = document.getElementById('cart-table-wrap');
                    if (wrap) {
                        wrap.outerHTML = '<div class="content-card text-center py-5" id="cart-empty-state">'
                            + '<div style="font-size:64px;opacity:0.3;color:var(--text-muted)"><i class="bi bi-cart-x"></i></div>'
                            + '<h4 class="fw-bold mt-3">Giỏ hàng của bạn đang trống</h4>'
                            + '<p class="text-muted mb-4 mx-auto" style="max-width:400px">Hãy chọn sản phẩm từ Hades Shop để bắt đầu mua sắm.</p>'
                            + '<a href="/" class="btn btn-primary-hades" style="width:auto;display:inline-block;padding:12px 40px">Khám phá ngay</a>'
                            + '</div>';
                    }
                }
            }
        })
        .catch(err => console.error('Lỗi cập nhật giỏ:', err));
    }

    // Plus & Minus stepper button click
    document.querySelectorAll('.btn-qty-minus').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const input = document.querySelector(`.input-cart-qty[data-id="${id}"]`);
            if (input) {
                let val = parseInt(input.value || '1', 10) - 1;
                if (val < 0) val = 0;
                input.value = val;
                if (val === 0) {
                    const row = document.getElementById('cart-row-' + id);
                    if (row) {
                        row.style.transition = 'all 0.3s ease';
                        row.style.opacity = '0.3';
                    }
                }
                triggerCartUpdate();
            }
        });
    });

    document.querySelectorAll('.btn-qty-plus').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const input = document.querySelector(`.input-cart-qty[data-id="${id}"]`);
            if (input) {
                let val = parseInt(input.value || '0', 10) + 1;
                input.value = val;
                triggerCartUpdate();
            }
        });
    });

    document.querySelectorAll('.input-cart-qty').forEach(input => {
        input.addEventListener('change', triggerCartUpdate);
    });

    // Remove item listener
    document.querySelectorAll('.btn-remove-cart').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var productId = btn.dataset.productId;
            var csrf      = btn.dataset.csrf;
            var row       = document.getElementById('cart-row-' + productId);

            if (row) {
                row.style.transition = 'opacity 0.3s, transform 0.3s';
                row.style.opacity    = '0.3';
                row.style.transform  = 'translateX(25px)';
            }

            fetch('/cart/remove/' + productId, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: '_csrf=' + encodeURIComponent(csrf),
            })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (!data.success) {
                    if (row) { row.style.opacity = '1'; row.style.transform = ''; }
                    if (window.showToast) showToast('error', 'Không thể xóa sản phẩm.');
                    return;
                }
                if (row) {
                    row.style.opacity   = '0';
                    row.style.transform = 'translateX(50px)';
                    setTimeout(function () { row.remove(); }, 350);
                }

                var badge = document.querySelector('#cart-icon .cart-badge');
                if (badge) {
                    if (data.cartCount > 0) {
                        badge.textContent = data.cartCount;
                        badge.classList.remove('bounce');
                        void badge.offsetWidth;
                        badge.classList.add('bounce');
                    } else {
                        badge.style.display = 'none';
                        badge.textContent = '';
                    }
                }

                if (data.cartCount === 0) {
                    var wrap = document.getElementById('cart-table-wrap');
                    if (wrap) {
                        wrap.outerHTML = '<div class="content-card text-center py-5" id="cart-empty-state">'
                            + '<div style="font-size:64px;opacity:0.3;color:var(--text-muted)"><i class="bi bi-cart-x"></i></div>'
                            + '<h4 class="fw-bold mt-3">Giỏ hàng của bạn đang trống</h4>'
                            + '<p class="text-muted mb-4 mx-auto" style="max-width:400px">Hãy chọn sản phẩm từ Hades Shop để bắt đầu mua sắm.</p>'
                            + '<a href="/" class="btn btn-primary-hades" style="width:auto;display:inline-block;padding:12px 40px">Khám phá ngay</a>'
                            + '</div>';
                    }
                }

                if (window.showToast) showToast('success', 'Đã xóa sản phẩm khỏi giỏ hàng.');
            })
            .catch(function () {
                if (row) { row.style.opacity = '1'; row.style.transform = ''; }
                if (window.showToast) showToast('error', 'Có lỗi xảy ra. Thử lại sau.');
            });
        });
    });
});
</script>

