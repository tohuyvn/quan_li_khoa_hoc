<?php $title = 'Giỏ hàng của bạn'; ?>

<style>
.cart-header-banner {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    border-radius: 18px;
    padding: 24px 28px;
    color: #fff;
    margin-bottom: 24px;
    box-shadow: 0 10px 25px rgba(15, 23, 42, 0.12);
    position: relative;
    overflow: hidden;
}
.cart-header-banner::after {
    content: '';
    position: absolute;
    right: -40px;
    top: -40px;
    width: 180px;
    height: 180px;
    background: rgba(239, 68, 68, 0.15);
    border-radius: 50%;
    filter: blur(30px);
}

.checkout-steps-nav {
    display: flex;
    justify-content: center;
    gap: 12px;
    margin-bottom: 24px;
}
.checkout-step-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    border-radius: 50px;
    background: #f1f5f9;
    color: #64748b;
    font-size: 13px;
    font-weight: 700;
    transition: all 0.2s ease;
}
.checkout-step-item.active {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    color: #fff;
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.15);
}
.checkout-step-num {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
}
.checkout-step-item.active .checkout-step-num {
    background: #ef4444;
    color: #fff;
}

.cart-card {
    background: #ffffff;
    border-radius: 18px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04);
    overflow: hidden;
    padding: 24px;
}

.cart-table thead th {
    background: #f8fafc;
    color: #475569;
    font-size: 12.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    padding: 14px 16px;
    border-bottom: 2px solid #e2e8f0;
}

.cart-item-row {
    transition: background 0.15s ease;
}
.cart-item-row:hover {
    background-color: #f8fafc;
}

.cart-thumb-box {
    width: 64px;
    height: 64px;
    border-radius: 14px;
    overflow: hidden;
    border: 1px solid #e2e8f0;
    background: #f8fafc;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}
.cart-thumb-box img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.qty-stepper-wrap {
    display: flex;
    align-items: center;
    border: 1px solid #cbd5e1;
    border-radius: 10px;
    overflow: hidden;
    width: 105px;
}
.qty-stepper-btn {
    width: 32px;
    height: 32px;
    background: #f1f5f9;
    border: none;
    font-weight: 700;
    color: #334155;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background 0.15s ease;
}
.qty-stepper-btn:hover {
    background: #e2e8f0;
}
.qty-stepper-input {
    width: 41px;
    height: 32px;
    border: none;
    text-align: center;
    font-weight: 700;
    font-size: 13px;
    color: #0f172a;
    background: #fff;
}
.qty-stepper-input:focus {
    outline: none;
}

.cart-summary-box {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    padding: 24px;
}
</style>

<!-- Header Banner -->
<div class="cart-header-banner d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
        <h2 class="h3 fw-bold mb-1 text-white"><i class="bi bi-bag-check-fill me-2 text-danger"></i>Giỏ Hàng Của Bạn</h2>
        <p class="mb-0 text-slate-300 small" style="color: #cbd5e1;">Kiểm tra danh sách sản phẩm, điều chỉnh số lượng và sẵn sàng đặt hàng.</p>
    </div>
    <div>
        <a href="/" class="btn btn-outline-light btn-sm rounded-3 px-3 py-2 fw-semibold">
            <i class="bi bi-arrow-left me-1"></i> Tiếp tục mua sắm
        </a>
    </div>
</div>

<!-- Checkout Steps Progress -->
<div class="checkout-steps-nav">
    <div class="checkout-step-item active">
        <span class="checkout-step-num">1</span>
        <span>Giỏ hàng</span>
    </div>
    <div class="checkout-step-item">
        <span class="checkout-step-num">2</span>
        <span>Thanh toán</span>
    </div>
    <div class="checkout-step-item">
        <span class="checkout-step-num">3</span>
        <span>Hoàn tất</span>
    </div>
</div>

<?php if (empty($cart)): ?>
    <div class="cart-card text-center py-5">
        <div style="font-size: 72px; opacity: 0.25; color: #64748b;"><i class="bi bi-cart-x-fill"></i></div>
        <h4 class="fw-bold mt-3 text-dark">Giỏ hàng của bạn đang trống</h4>
        <p class="text-muted mb-4 mx-auto" style="max-width: 420px;">
            Hãy chọn các sản phẩm công nghệ cao cấp từ Hades Shop để bắt đầu trải nghiệm mua sắm tuyệt vời.
        </p>
        <a href="/" class="btn btn-danger btn-lg px-5 py-2.5 rounded-3 fw-bold shadow-sm" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); border: none;">
            <i class="bi bi-compass me-1"></i> Khám Phá Ngay
        </a>
    </div>
<?php else: ?>
    <div class="row g-4">
        <!-- Cart Items List Table -->
        <div class="col-lg-8" id="cart-table-wrap">
            <div class="cart-card">
                <form method="post" action="/cart/update">
                    <?= csrf_field() ?>
                    <div class="table-responsive">
                        <table class="table align-middle cart-table mb-0">
                            <thead>
                                <tr>
                                    <th>Sản Phẩm</th>
                                    <th>Đơn Giá</th>
                                    <th width="130" class="text-center">Số Lượng</th>
                                    <th class="text-end">Thành Tiền</th>
                                    <th width="50"></th>
                                </tr>
                            </thead>
                            <tbody id="cart-tbody">
                                <?php foreach ($cart as $id => $item): ?>
                                    <tr id="cart-row-<?= e($id) ?>" class="cart-item-row">
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <a href="/products/<?= (int) $item['product_id'] ?>" class="cart-thumb-box">
                                                    <img src="<?= e(imageUrl($item['image'] ?? '', 'thumb')) ?>" alt="<?= e($item['name']) ?>">
                                                </a>
                                                <div>
                                                    <a href="/products/<?= (int) $item['product_id'] ?>" class="fw-bold text-dark text-decoration-none hover-danger">
                                                        <?= e($item['name']) ?>
                                                    </a>
                                                    <?php if (!empty($item['color'])): ?>
                                                        <div class="small text-muted mt-1" style="font-size: 11.5px;">Phân loại: <span class="badge bg-light text-dark border"><?= e($item['color']) ?></span></div>
                                                    <?php endif; ?>
                                                    <?php if (empty($item['is_preorder'])): ?>
                                                        <div class="small text-muted mt-1" style="font-size: 11px;">
                                                            Kho khả dụng: <span class="fw-bold <?= ($item['max_stock'] ?? 999) <= 5 ? 'text-danger' : 'text-success' ?>"><?= (int) ($item['max_stock'] ?? 0) ?></span> sp
                                                        </div>
                                                    <?php else: ?>
                                                        <div class="small text-primary mt-1" style="font-size: 11px;">
                                                            <i class="bi bi-clock-history me-1"></i>Hàng Đặt Trước (Pre-order)
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-dark"><?= formatMoney($item['price']) ?></span>
                                            <?php if (!empty($item['has_discount'])): ?>
                                                <div class="small text-muted text-decoration-line-through" style="font-size: 11px;">
                                                    <?= formatMoney($item['original_price']) ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="qty-stepper-wrap mx-auto">
                                                <button class="qty-stepper-btn btn-qty-minus" type="button" data-id="<?= e($id) ?>">-</button>
                                                <input type="number" name="quantity[<?= e($id) ?>]" value="<?= (int) $item['quantity'] ?>"
                                                       min="0" max="<?= empty($item['is_preorder']) ? (int) ($item['max_stock'] ?? 999) : 999 ?>"
                                                       class="qty-stepper-input input-cart-qty" data-id="<?= e($id) ?>" data-stock="<?= empty($item['is_preorder']) ? (int) ($item['max_stock'] ?? 999) : 999 ?>">
                                                <button class="qty-stepper-btn btn-qty-plus" type="button" data-id="<?= e($id) ?>">+</button>
                                            </div>
                                        </td>
                                        <td class="text-end fw-bold fs-6 text-danger cart-row-subtotal" id="subtotal-<?= e($id) ?>">
                                            <?= formatMoney($item['price'] * $item['quantity']) ?>
                                        </td>
                                        <td class="text-end">
                                            <button type="button"
                                                class="btn btn-sm btn-light text-danger rounded-2 btn-remove-cart"
                                                data-product-id="<?= e($id) ?>"
                                                data-csrf="<?= e(csrf_token()) ?>"
                                                title="Xóa khỏi giỏ">
                                                <i class="bi bi-trash3-fill"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top flex-wrap gap-2">
                        <span class="text-muted small"><i class="bi bi-info-circle-fill me-1 text-primary"></i>Số lượng được tự động tính toán khi bạn bấm tăng/giảm.</span>
                        <button type="submit" class="btn btn-outline-secondary btn-sm rounded-2 fw-semibold" id="btnManualUpdate">
                            <i class="bi bi-arrow-repeat me-1"></i> Cập nhật giỏ
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Order Summary Column -->
        <div class="col-lg-4">
            <div class="cart-summary-box shadow-sm">
                <h5 class="fw-bold mb-3 text-dark d-flex align-items-center gap-2">
                    <i class="bi bi-receipt-cutoff text-danger"></i> Tóm Tắt Đơn Hàng
                </h5>

                <div class="d-flex justify-content-between py-2 text-muted">
                    <span>Số lượng sản phẩm</span>
                    <strong class="text-dark"><?= count($cart) ?> mặt hàng</strong>
                </div>

                <div class="d-flex justify-content-between py-2 text-muted">
                    <span>Tạm tính</span>
                    <strong class="text-dark" id="cart-subtotal-text"><?= formatMoney($total) ?></strong>
                </div>

                <div class="d-flex justify-content-between py-2 text-muted">
                    <span>Phí vận chuyển</span>
                    <span class="badge bg-success-subtle text-success fw-bold">Tính tại Checkout</span>
                </div>

                <hr class="my-3">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <span class="fw-bold text-dark fs-6">Tổng thanh toán</span>
                    <span class="fw-extrabold text-danger fs-4" id="cart-total-text"><?= formatMoney($total) ?></span>
                </div>

                <a href="/checkout" class="btn btn-danger btn-lg w-100 py-3 rounded-3 fw-bold shadow d-flex align-items-center justify-content-center gap-2" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); border: none;">
                    <span>Tiến Hành Thanh Toán</span>
                    <i class="bi bi-arrow-right fs-5"></i>
                </a>

                <div class="mt-3 text-center">
                    <span class="text-muted small"><i class="bi bi-shield-check text-success me-1"></i>Bảo mật thanh toán 100% SSL</span>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const cartForm = document.querySelector('form[action="/cart/update"]');
    
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
                if (data.warning) {
                    if (typeof showToast === 'function') {
                        showToast(data.warning, 'warning');
                    } else {
                        alert(data.warning);
                    }
                }
                const totalEl = document.getElementById('cart-total-text');
                const subtotalEl = document.getElementById('cart-subtotal-text');
                if (totalEl) totalEl.textContent = data.totalFormatted;
                if (subtotalEl) subtotalEl.textContent = data.totalFormatted;
                
                if (data.items) {
                    data.items.forEach(item => {
                        const itemSubtotalEl = document.getElementById('subtotal-' + item.key);
                        if (itemSubtotalEl) {
                            itemSubtotalEl.textContent = item.subtotalFormatted;
                        }
                        const inputEl = document.querySelector(`.input-cart-qty[data-id="${item.key}"]`);
                        if (inputEl) {
                            inputEl.value = item.quantity;
                        }
                    });
                }

                const badge = document.querySelector('#cart-icon .cart-badge');
                if (badge) {
                    if (data.cartCount > 0) {
                        badge.textContent = data.cartCount;
                        badge.style.display = 'flex';
                    } else {
                        badge.style.display = 'none';
                    }
                }

                if (data.cartCount === 0) {
                    location.reload();
                }
            }
        })
        .catch(err => console.error('Lỗi cập nhật giỏ:', err));
    }

    document.querySelectorAll('.btn-qty-minus').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const input = document.querySelector(`.input-cart-qty[data-id="${id}"]`);
            if (input) {
                let val = parseInt(input.value || '1', 10) - 1;
                if (val < 0) val = 0;
                input.value = val;
                triggerCartUpdate();
            }
        });
    });

    document.querySelectorAll('.btn-qty-plus').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const input = document.querySelector(`.input-cart-qty[data-id="${id}"]`);
            if (input) {
                const maxStock = parseInt(input.dataset.stock || input.getAttribute('max') || '999', 10);
                let val = parseInt(input.value || '0', 10) + 1;
                if (val > maxStock) {
                    const msg = 'Đã đạt số lượng tồn kho tối đa (' + maxStock + ' sp).';
                    if (typeof showToast === 'function') {
                        showToast(msg, 'warning');
                    } else {
                        alert(msg);
                    }
                    val = maxStock;
                }
                input.value = val;
                triggerCartUpdate();
            }
        });
    });

    document.querySelectorAll('.input-cart-qty').forEach(input => {
        input.addEventListener('change', function () {
            const maxStock = parseInt(this.dataset.stock || this.getAttribute('max') || '999', 10);
            let val = parseInt(this.value || '0', 10);
            if (val > maxStock) {
                const msg = 'Đã vượt quá số lượng tồn kho khả dụng (' + maxStock + ' sp).';
                if (typeof showToast === 'function') {
                    showToast(msg, 'warning');
                } else {
                    alert(msg);
                }
                this.value = maxStock;
            }
            triggerCartUpdate();
        });
    });

    document.querySelectorAll('.btn-remove-cart').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var productId = btn.dataset.productId;
            var csrf      = btn.dataset.csrf;
            var row       = document.getElementById('cart-row-' + productId);

            if (confirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?')) {
                fetch('/cart/remove/' + productId, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: '_csrf=' + encodeURIComponent(csrf),
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                });
            }
        });
    });
});
</script>
