<?php
$title = 'Thanh toán';
$isMember = $isMember ?? (authUser() !== null);
$oldPayment = old('payment_method', 'cash');
?>

<!-- Leaflet Map CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<style>
    .vat-auto-card {
        position: relative;
        overflow: hidden;
        margin-top: 1.5rem;
        border: 1px solid var(--border, #dbe3ef);
        border-radius: 16px;
        background: linear-gradient(135deg, var(--bg-card, #fff) 0%, var(--accent-light, #edf4ff) 145%);
        box-shadow: 0 8px 24px rgba(23, 59, 120, .06);
        transition: border-color .2s ease, box-shadow .2s ease, transform .2s ease;
    }
    .vat-auto-card::before {
        content: '';
        position: absolute;
        inset: 0 auto 0 0;
        width: 4px;
        background: transparent;
        transition: background .2s ease;
    }
    .vat-auto-card.is-selected {
        border-color: rgba(23, 59, 120, .45);
        box-shadow: 0 12px 30px rgba(23, 59, 120, .12);
    }
    .vat-auto-card.is-selected::before { background: var(--accent, #173b78); }
    .vat-auto-head { display: flex; align-items: center; gap: 14px; padding: 18px 20px; }
    .vat-auto-icon {
        width: 46px;
        height: 46px;
        flex: 0 0 46px;
        display: grid;
        place-items: center;
        border-radius: 14px;
        color: var(--accent, #173b78);
        background: var(--accent-light, #edf4ff);
        font-size: 21px;
    }
    .vat-auto-copy { min-width: 0; flex: 1; }
    .vat-auto-title { display: block; margin: 0 0 3px; color: var(--text-primary, #111827); font-weight: 800; cursor: pointer; }
    .vat-auto-copy p { margin: 0; color: var(--text-muted, #64748b); font-size: 12px; }
    .vat-auto-switch { margin: 0; padding-left: 2.9rem; }
    .vat-auto-switch .form-check-input { width: 2.65rem; height: 1.4rem; margin-left: -2.9rem; cursor: pointer; }
    .vat-auto-details {
        display: none;
        margin: 0 20px 18px;
        padding: 15px 16px;
        border: 1px solid rgba(23, 59, 120, .12);
        border-radius: 12px;
        background: rgba(255, 255, 255, .72);
    }
    .vat-auto-card.is-selected .vat-auto-details { animation: vatReveal .22s ease both; }
    .vat-auto-status { display: flex; align-items: center; gap: 7px; margin-bottom: 12px; color: #15803d; font-size: 12px; font-weight: 700; }
    .vat-auto-status i { font-size: 15px; }
    .vat-preview-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px 18px; }
    .vat-preview-item { min-width: 0; }
    .vat-preview-item:last-child { grid-column: 1 / -1; }
    .vat-preview-item small { display: block; margin-bottom: 2px; color: var(--text-muted, #64748b); font-size: 10px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; }
    .vat-preview-item strong { display: block; overflow: hidden; color: var(--text-primary, #111827); font-size: 12px; text-overflow: ellipsis; white-space: nowrap; }
    @keyframes vatReveal { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }
    [data-theme="dark"] .vat-auto-details { background: rgba(15, 23, 42, .36); }
    @media (max-width: 575px) {
        .vat-auto-head { align-items: flex-start; padding: 16px; }
        .vat-auto-icon { width: 40px; height: 40px; flex-basis: 40px; border-radius: 12px; font-size: 18px; }
        .vat-auto-switch { margin-top: 6px; }
        .vat-auto-details { margin: 0 16px 16px; }
        .vat-preview-grid { grid-template-columns: 1fr; }
        .vat-preview-item:last-child { grid-column: auto; }
    }
</style>

<div class="section-title"><h2>💳 Thanh toán & Đặt hàng</h2></div>

<nav class="checkout-steps mb-4" aria-label="Tiến trình đặt hàng">
    <ol class="checkout-steps-list">
        <li class="checkout-step done">
            <span class="checkout-step-num">1</span>
            <span class="checkout-step-label">Giỏ hàng</span>
        </li>
        <li class="checkout-step active" aria-current="step">
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
    <a href="/cart" style="color:var(--accent);text-decoration:none">← Quay lại giỏ hàng</a> ·
    <a href="/" style="color:var(--accent);text-decoration:none">Trang chủ</a>
</p>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="content-card">
            <form method="post" action="/checkout" id="checkout-form">
            <?= csrf_field() ?>
                <input type="hidden" name="coupon_code" id="hidden-coupon-code" value="">
                <input type="hidden" name="shipping_lat" id="shipping_lat" value="<?= old('shipping_lat') ?>">
                <input type="hidden" name="shipping_lng" id="shipping_lng" value="<?= old('shipping_lng') ?>">
                <input type="hidden" name="address_province" id="address_province" value="<?= old('address_province') ?>">
                <input type="hidden" name="address_district" id="address_district" value="<?= old('address_district') ?>">
                <input type="hidden" name="address_ward" id="address_ward" value="<?= old('address_ward') ?>">
                <input type="hidden" name="address_street" id="address_street" value="<?= old('address_street') ?>">
                <?php if (!empty($isPreorderOrder)): ?>
                    <div class="alert-hades alert-warning mb-4">
                        <h6 class="fw-bold mb-1"><i class="bi bi-info-circle-fill"></i> ĐƠN HÀNG ĐẶT TRƯỚC (PRE-ORDER)</h6>
                        <p class="mb-0 small">
                            Đơn hàng của bạn chứa sản phẩm đang trong giai đoạn đặt trước.
                            <?php if ($totalRequiredDeposit > 0): ?>
                                Cần đặt cọc tối thiểu: <strong class="text-danger fs-6"><?= formatMoney($totalRequiredDeposit) ?></strong>.
                                Vui lòng thực hiện thanh toán online (VNPay/MoMo/Ví) hoặc liên hệ cửa hàng để chuyển khoản cọc.
                            <?php else: ?>
                                Sản phẩm này hỗ trợ <strong>Đặt trước không cần cọc</strong>!
                            <?php endif; ?>
                        </p>
                    </div>
                <?php endif; ?>

                <h5 class="mb-3" style="color:var(--accent)">1. Chọn phương thức thanh toán</h5>
                <div class="mb-4">
                    <div class="form-check mb-2 p-3 border rounded">
                        <input class="form-check-input" type="radio" name="payment_method" id="cash" value="cash"
                            <?= !in_array($oldPayment, ['vnpay', 'momo', 'installment', 'bank_transfer'], true) ? 'checked' : '' ?>>
                        <label class="form-check-label fw-semibold" for="cash">
                            💵 Thanh toán trực tiếp bằng tiền mặt (COD)
                        </label>
                        <div class="small text-muted mt-1">Nhận hàng và thanh toán tại nhà. Khách vãng lai có thể đặt hàng.</div>
                    </div>
                    <div class="form-check p-3 border rounded <?= !$isMember ? 'opacity-75' : '' ?> mb-2">
                        <input class="form-check-input" type="radio" name="payment_method" id="vnpay" value="vnpay"
                            <?= $oldPayment === 'vnpay' ? 'checked' : '' ?>
                            <?= !$isMember ? 'disabled' : '' ?>>
                        <label class="form-check-label fw-semibold" for="vnpay">
                            💳 Thanh toán trực tuyến qua VNPay
                        </label>
                        <div class="small text-muted mt-1">Thẻ ATM / ngân hàng nội địa qua cổng VNPay Sandbox.</div>
                    </div>
                    <div class="form-check p-3 border rounded <?= !$isMember ? 'opacity-75' : '' ?> mb-2">
                        <input class="form-check-input" type="radio" name="payment_method" id="momo" value="momo"
                            <?= $oldPayment === 'momo' ? 'checked' : '' ?>
                            <?= !$isMember ? 'disabled' : '' ?>>
                        <label class="form-check-label fw-semibold" for="momo">
                            🟣 Thanh toán trực tuyến qua MoMo
                        </label>
                        <div class="small text-muted mt-1">Ví MoMo test API. Giới hạn sandbox: <strong>20.000.000đ</strong>/giao dịch.</div>
                    </div>
                    <div class="form-check p-3 border rounded mb-2">
                        <input class="form-check-input" type="radio" name="payment_method" id="bank_transfer" value="bank_transfer"
                            <?= $oldPayment === 'bank_transfer' ? 'checked' : '' ?>>
                        <label class="form-check-label fw-semibold" for="bank_transfer">
                            🏦 Chuyển khoản VietQR (MB Bank)
                        </label>
                        <div class="small text-muted mt-1">
                            Quét mã VietQR → chuyển khoản tới <strong>MB · 0788415516 · Tô Quang Huy</strong>.
                            Nội dung CK tự điền theo mã đơn.
                        </div>
                    </div>
                    <div id="bank-transfer-preview" class="mt-2 p-3 border rounded bg-light mb-2" style="display:none">
                        <div class="row align-items-center g-3">
                            <div class="col-auto">
                                <img src="/image/vietqr_mb_toquanghuy.png" alt="VietQR MB" style="width:120px;height:auto;border-radius:8px;border:1px solid #ddd">
                            </div>
                            <div class="col small">
                                <div><strong>MB Bank</strong></div>
                                <div>STK: <code>0788415516</code></div>
                                <div>Chủ TK: <strong>Tô Quang Huy</strong></div>
                                <div class="text-muted mt-1">Sau khi đặt hàng, hệ thống hiện QR đúng số tiền + nội dung đơn.</div>
                            </div>
                        </div>
                    </div>
                    <div class="form-check p-3 border rounded <?= !$isMember ? 'opacity-75' : '' ?>">
                        <input class="form-check-input" type="radio" name="payment_method" id="installment" value="installment"
                            <?= $oldPayment === 'installment' ? 'checked' : '' ?>
                            <?= !$isMember ? 'disabled' : '' ?>>
                        <label class="form-check-label fw-semibold" for="installment">
                            📅 Trả góp (duyệt hồ sơ nội bộ)
                        </label>
                        <div class="small text-muted mt-1">Đặt cọc 20%, chọn kỳ hạn 3/6/9/12 tháng. Admin duyệt trước khi giao máy.</div>
                    </div>
                    <div id="installment-options" class="mt-3 p-3 border rounded bg-light" style="display:none">
                        <label class="form-label">Kỳ hạn trả góp *</label>
                        <select name="installment_months" class="form-select">
                            <?php foreach ([3,6,9,12] as $m): ?>
                                <option value="<?= $m ?>" <?= old('installment_months', '6') === (string)$m ? 'selected' : '' ?>><?= $m ?> tháng</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php if (!$isMember): ?>
                        <div class="alert-hades alert-danger mt-2 mb-0">
                            ⚠️ <strong>VNPay/MoMo/Trả góp chỉ dành cho thành viên.</strong>
                            Vui lòng <a href="/login" style="color:inherit;font-weight:700">đăng nhập</a> hoặc
                            <a href="/register" style="color:inherit;font-weight:700">đăng ký</a> để thanh toán online.
                        </div>
                    <?php endif; ?>

                    <div id="vnpay-options" class="mt-3 p-3 border rounded bg-light" style="display:none">
                        <h6 class="mb-3" style="color:var(--accent)">Thông tin thẻ ngân hàng</h6>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label" for="bank">Ngân hàng *</label>
                                <select name="bank" id="bank" class="form-select">
                                    <option value="">-- Chọn ngân hàng --</option>
                                    <?php foreach ($banks as $bank): ?>
                                        <option value="<?= e($bank['code']) ?>" <?= old('bank') === $bank['code'] ? 'selected' : '' ?>>
                                            <?= e($bank['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="card_number">Số thẻ *</label>
                                <input type="text" name="card_number" id="card_number" class="form-control"
                                       placeholder="Nhập số thẻ ATM (16–19 số)"
                                       value="<?= old('card_number') ?>" inputmode="numeric" autocomplete="off">
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="card_holder">Tên chủ thẻ *</label>
                                <input type="text" name="card_holder" id="card_holder" class="form-control"
                                       placeholder="VD: NGUYEN VAN A"
                                       value="<?= old('card_holder') ?>" autocomplete="off"
                                       style="text-transform:uppercase">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="issue_date">Ngày phát hành (MM/YY) *</label>
                                <input type="text" name="issue_date" id="issue_date" class="form-control"
                                       placeholder="MM/YY" value="<?= old('issue_date') ?>"
                                       maxlength="5" inputmode="numeric">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="issue_date_picker">Chọn từ lịch</label>
                                <input type="month" id="issue_date_picker" class="form-control">
                            </div>
                        </div>
                        <p class="text-muted small mt-2 mb-0">Thẻ test NCB: <code><?= e(config('vnpay.test_card.card_number')) ?></code> · OTP: <code><?= e(config('vnpay.test_card.otp')) ?></code></p>
                    </div>
                </div>

                <h5 class="mb-3" style="color:var(--accent)">2. Thông tin khách hàng & giao hàng</h5>
                <div id="shipping-fields" class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="customer_name">Họ tên khách hàng *</label>
                        <input type="text" name="customer_name" id="customer_name" class="form-control shipping-required"
                               value="<?= old('customer_name', $user['name'] ?? '') ?>" placeholder="Nguyễn Văn A">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="phone">Số điện thoại *</label>
                        <input type="text" name="phone" id="phone" class="form-control shipping-required"
                               value="<?= old('phone', $user['phone'] ?? '') ?>" placeholder="0912345678">
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="email">Email *</label>
                        <input type="email" name="email" id="email" class="form-control shipping-required"
                               value="<?= old('email', $user['email'] ?? '') ?>" placeholder="email@gmail.com">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="preferred_time_slot">Khung giờ nhận hàng</label>
                        <select name="preferred_time_slot" id="preferred_time_slot" class="form-select">
                            <option value="any" <?= old('preferred_time_slot', 'any') === 'any' ? 'selected' : '' ?>>Linh hoạt</option>
                            <option value="morning" <?= old('preferred_time_slot') === 'morning' ? 'selected' : '' ?>>Sáng (08:00-11:30)</option>
                            <option value="afternoon" <?= old('preferred_time_slot') === 'afternoon' ? 'selected' : '' ?>>Chiều (13:30-17:30)</option>
                            <option value="evening" <?= old('preferred_time_slot') === 'evening' ? 'selected' : '' ?>>Tối (18:00-21:00)</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label mb-0" for="shipping_address">Địa chỉ giao hàng *</label>
                            <span class="d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="use-current-location-btn" style="font-size: 12px; font-weight: 600; border-radius: 4px; padding: 2px 8px;">
                                    Vị trí hiện tại
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger" id="open-map-btn" style="font-size: 12px; font-weight: 600; border-radius: 4px; padding: 2px 8px;">
                                    📍 Bản đồ
                                </button>
                            </span>
                        </div>
                        <div class="position-relative">
                            <input type="text" name="shipping_address" id="shipping_address" class="form-control shipping-required"
                                   value="<?= old('shipping_address') ?>" placeholder="Nhập địa chỉ giao hàng hoặc chọn trên bản đồ" autocomplete="off">
                            <div id="address-suggestions" class="list-group position-absolute w-100 mt-1 shadow" style="display: none; z-index: 1050; max-height: 250px; overflow-y: auto;"></div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div id="shipping-estimate-box" class="alert-hades alert-info mb-0" style="display:none"></div>
                    </div>
                </div>
                <!-- VAT Invoice Option -->
                <section class="vat-auto-card mb-3" id="vat-auto-card">
                    <div class="vat-auto-head">
                        <div class="vat-auto-icon" aria-hidden="true"><i class="bi bi-receipt-cutoff"></i></div>
                        <div class="vat-auto-copy">
                            <label class="vat-auto-title" for="vat_required">Xuất hóa đơn điện tử</label>
                            <p>Thông tin được lấy tự động từ đơn hàng, không cần nhập lại.</p>
                        </div>
                        <div class="form-check form-switch vat-auto-switch">
                            <input class="form-check-input" type="checkbox" role="switch" name="vat_required" id="vat_required" value="1"
                                   aria-controls="vat-details" aria-expanded="false"
                                   <?= !empty($_SESSION['_old']['vat_required']) ? 'checked' : '' ?>>
                        </div>
                    </div>
                    <div id="vat-details" class="vat-auto-details" aria-live="polite">
                        <div class="vat-auto-status"><i class="bi bi-check-circle-fill"></i> Sẵn sàng phát hành và tự động mở bản in</div>
                        <div class="vat-preview-grid">
                            <div class="vat-preview-item">
                                <small>Người nhận</small>
                                <strong id="vat-preview-name"><?= e(old('customer_name', $user['name'] ?? 'Chưa có thông tin')) ?></strong>
                            </div>
                            <div class="vat-preview-item">
                                <small>Email nhận hóa đơn</small>
                                <strong id="vat-preview-email"><?= e(old('email', $user['email'] ?? 'Chưa có thông tin')) ?></strong>
                            </div>
                            <div class="vat-preview-item">
                                <small>Địa chỉ trên hóa đơn</small>
                                <strong id="vat-preview-address"><?= e(old('shipping_address', 'Chưa có thông tin')) ?></strong>
                            </div>
                        </div>
                    </div>
                </section>

                <p id="cash-note" class="text-muted small mt-2">* Bắt buộc khi thanh toán tiền mặt (COD).</p>
                <p id="vnpay-note" class="text-muted small mt-2" style="display:none">* Bắt buộc khi thanh toán VNPay.</p>

                <button type="submit" class="btn btn-primary-hades mt-3" id="submit-btn">✅ Xác nhận đặt hàng</button>
            </form>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="content-card">
            <h5 class="mb-3">Đơn hàng (<?= count($cart) ?> sản phẩm)</h5>
            <?php foreach ($cart as $item): ?>
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom gap-2">
                    <div class="d-flex align-items-center gap-2 min-w-0">
                        <img src="<?= e(imageUrl($item['image'] ?? '', 'thumb')) ?>"
                             alt="<?= e($item['name'] ?? '') ?>"
                             style="width:44px;height:44px;object-fit:contain;border-radius:8px;border:1px solid #e2e8f0;background:#fff;flex-shrink:0"
                             loading="lazy"
                             onerror="this.onerror=null;this.src='/image/logo.webp';">
                        <span class="small"><?= e($item['name']) ?> × <?= (int) $item['quantity'] ?></span>
                    </div>
                    <strong class="text-nowrap"><?= formatMoney($item['price'] * $item['quantity']) ?></strong>
                </div>
            <?php endforeach; ?>
            <div class="d-flex justify-content-between pt-3">
                <span class="fw-bold">Tạm tính</span>
                <span id="subtotal-label"><?= formatMoney($total) ?></span>
            </div>
            <div class="d-flex justify-content-between pt-2 text-danger" id="discount-row" style="display:none !important">
                <span>Khuyến mãi (<span id="discount-percent-label">0</span>%)</span>
                <strong id="discount-amount-label">-0 đ</strong>
            </div>
            <!-- Personal Voucher Discount Row -->
            <div class="d-flex justify-content-between pt-2 text-danger" id="voucher-discount-row" style="display:none !important">
                <span>Voucher cá nhân (<span id="voucher-percent-label">0</span>%)</span>
                <strong id="voucher-amount-label">-0 đ</strong>
            </div>
            <!-- Loyalty Member Discount Row -->
            <div class="d-flex justify-content-between pt-2 text-danger" id="loyalty-discount-row" style="display:none !important">
                <span>Ưu đãi thành viên (<?= e($isMember ? (authUser()['loyalty_tier'] ?? 'Standard') : 'Standard') ?>: <span id="loyalty-percent-label">0</span>%)</span>
                <strong id="loyalty-amount-label">-0 đ</strong>
            </div>
            <div class="d-flex justify-content-between pt-2" id="shipping-fee-row" style="display:none !important">
                <span>Phí giao hàng</span>
                <strong id="shipping-fee-label">0 đ</strong>
            </div>
            <div class="d-flex justify-content-between pt-2 border-top mt-2">
                <span class="fw-bold">Tổng thanh toán</span>
                <span class="cart-total fw-bold text-danger fs-5" id="final-total-label"><?= formatMoney($total) ?></span>
            </div>

            <!-- Coupon Code input -->
            <div class="mt-4 pt-3 border-top">
                <label class="form-label fw-bold small mb-1">Mã giảm giá (Khuyến mãi)</label>
                <div class="input-group">
                    <input type="text" id="coupon-code-input" class="form-control form-control-sm" placeholder="Nhập mã (VD: SIEUOFFER)">
                    <button type="button" id="apply-coupon-btn" class="btn btn-sm btn-dark">Áp dụng</button>
                </div>
                <div id="coupon-message" class="small mt-2" style="display:none"></div>
            </div>

            <?php if ($isMember && !empty($userVouchers)): ?>
            <div class="mt-3 pt-3 border-top">
                <label class="form-label fw-bold small mb-1">Voucher cá nhân</label>
                <select name="user_voucher_code" class="form-select form-select-sm" form="checkout-form">
                    <option value="">-- Không dùng voucher --</option>
                    <?php foreach ($userVouchers as $v): ?>
                        <option value="<?= e($v['code']) ?>">
                            <?= e($v['code']) ?> — giảm <?= (int) $v['discount_percent'] ?>%
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>

            <?php if ($isMember): ?>
            <div class="mt-3 pt-3 border-top">
                <label class="form-label fw-bold small mb-1"><i class="bi bi-wallet2"></i> Ví Hades</label>
                <?php if (($walletBalance ?? 0) > 0): ?>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="use_wallet" value="1" id="use_wallet" form="checkout-form">
                    <label class="form-check-label small" for="use_wallet">
                        Dùng số dư ví (<?= formatMoney($walletBalance) ?>)
                    </label>
                </div>
                <?php else: ?>
                <p class="small text-muted mb-1">Số dư: <?= formatMoney(0) ?> — hoàn tiền đơn hàng sẽ được cộng vào ví.</p>
                <?php endif; ?>
                <a href="/wallet" class="small" style="color:var(--accent)">Xem lịch sử ví →</a>
            </div>
            <?php endif; ?>

            <a href="/cart" class="btn btn-outline-secondary w-100 mt-3">← Sửa giỏ hàng</a>
        </div>
    </div>
</div>

<script>
(function () {
    const cashRadio = document.getElementById('cash');
    const vnpayRadio = document.getElementById('vnpay');
    const momoRadio = document.getElementById('momo');
    const installmentRadio = document.getElementById('installment');
    const bankTransferRadio = document.getElementById('bank_transfer');
    const vnpayBox = document.getElementById('vnpay-options');
    const installmentBox = document.getElementById('installment-options');
    const bankPreview = document.getElementById('bank-transfer-preview');
    const cashNote = document.getElementById('cash-note');
    const vnpayNote = document.getElementById('vnpay-note');
    const submitBtn = document.getElementById('submit-btn');
    const shippingFields = document.querySelectorAll('.shipping-required');
    const vnpayFields = vnpayBox.querySelectorAll('input, select');

    function isVnpay() {
        return vnpayRadio.checked && !vnpayRadio.disabled;
    }

    function isMomo() {
        return momoRadio.checked && !momoRadio.disabled;
    }

    function isInstallment() {
        return installmentRadio && installmentRadio.checked && !installmentRadio.disabled;
    }

    function isBankTransfer() {
        return bankTransferRadio && bankTransferRadio.checked;
    }

    function isOnline() {
        return isVnpay() || isMomo() || isInstallment() || isBankTransfer();
    }

    function toggleFields() {
        const vnpay = isVnpay();
        const momo = isMomo();
        const installment = isInstallment();
        const bank = isBankTransfer();
        vnpayBox.style.display = vnpay ? 'block' : 'none';
        if (installmentBox) installmentBox.style.display = installment ? 'block' : 'none';
        if (bankPreview) bankPreview.style.display = bank ? 'block' : 'none';
        cashNote.style.display = isOnline() ? 'none' : 'block';
        vnpayNote.style.display = vnpay ? 'block' : 'none';
        if (momo) {
            submitBtn.textContent = '✅ Tiếp tục thanh toán MoMo';
        } else if (vnpay) {
            submitBtn.textContent = '✅ Tiếp tục thanh toán VNPay';
        } else if (installment) {
            submitBtn.textContent = '✅ Gửi hồ sơ trả góp';
        } else if (bank) {
            submitBtn.textContent = '✅ Đặt hàng & hiện mã VietQR';
        } else {
            submitBtn.textContent = '✅ Xác nhận đặt hàng (COD)';
        }

        shippingFields.forEach(function (el) {
            el.required = true;
        });

        vnpayFields.forEach(function (el) {
            if (el.id === 'issue_date_picker') {
                el.disabled = !vnpay;
                return;
            }
            el.disabled = !vnpay;
            el.required = vnpay;
        });
    }

    function formatIssueDate(value) {
        const digits = value.replace(/\D/g, '').slice(0, 4);
        if (digits.length <= 2) return digits;
        return digits.slice(0, 2) + '/' + digits.slice(2);
    }

    document.querySelectorAll('input[name="payment_method"]').forEach(function (radio) {
        radio.addEventListener('change', toggleFields);
    });

    const issueDateInput = document.getElementById('issue_date');
    const issueDatePicker = document.getElementById('issue_date_picker');
    const cardHolderInput = document.getElementById('card_holder');

    issueDateInput.addEventListener('input', function () {
        this.value = formatIssueDate(this.value);
        const match = /^(\d{2})\/(\d{2})$/.exec(this.value);
        if (match) issueDatePicker.value = '20' + match[2] + '-' + match[1];
    });
    issueDatePicker.addEventListener('change', function () {
        if (!this.value) return;
        const parts = this.value.split('-');
        issueDateInput.value = parts[1] + '/' + parts[0].slice(-2);
    });
    cardHolderInput.addEventListener('input', function () {
        this.value = this.value.toUpperCase();
    });

    // Coupon handler
    const couponInput = document.getElementById('coupon-code-input');
    const applyCouponBtn = document.getElementById('apply-coupon-btn');
    const couponMsg = document.getElementById('coupon-message');
    const hiddenCoupon = document.getElementById('hidden-coupon-code');
    const discountRow = document.getElementById('discount-row');
    const discountPercentLabel = document.getElementById('discount-percent-label');
    const discountAmountLabel = document.getElementById('discount-amount-label');
    const finalTotalLabel = document.getElementById('final-total-label');
    const shippingFeeRow = document.getElementById('shipping-fee-row');
    const shippingFeeLabel = document.getElementById('shipping-fee-label');
    const baseTotal = <?= (int) $total ?>;
    let activeShippingFee = 0;
    let activeDiscountPercent = 0;

    const loyaltyPercent = <?= $isMember ? \App\Services\LoyaltyService::getDiscountPercent(authUser()['loyalty_tier'] ?? 'Standard') : 0 ?>;
    const loyaltyDiscountRow = document.getElementById('loyalty-discount-row');
    const loyaltyPercentLabel = document.getElementById('loyalty-percent-label');
    const loyaltyAmountLabel = document.getElementById('loyalty-amount-label');

    const voucherDiscountRow = document.getElementById('voucher-discount-row');
    const voucherPercentLabel = document.getElementById('voucher-percent-label');
    const voucherAmountLabel = document.getElementById('voucher-amount-label');
    const userVoucherSelect = document.querySelector('select[name="user_voucher_code"]');

    function formatVND(amount) {
        return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount).replace(/₫/g, 'đ');
    }

    window.hadesSetShippingFee = function (fee) {
        activeShippingFee = Math.max(0, parseInt(fee || 0, 10));
        if (activeShippingFee > 0) {
            shippingFeeLabel.textContent = formatVND(activeShippingFee);
            shippingFeeRow.style.setProperty('display', 'flex', 'important');
        } else {
            shippingFeeRow.style.setProperty('display', 'none', 'important');
        }
        recalculateCheckoutTotal();
    };

    function recalculateCheckoutTotal() {
        let currentTotal = baseTotal;

        // 1. Coupon code discount
        let couponDiscountVal = 0;
        if (activeDiscountPercent > 0) {
            couponDiscountVal = Math.round((currentTotal * activeDiscountPercent) / 100);
            currentTotal -= couponDiscountVal;
            
            discountPercentLabel.textContent = activeDiscountPercent;
            discountAmountLabel.textContent = '-' + formatVND(couponDiscountVal);
            discountRow.style.setProperty('display', 'flex', 'important');
        } else {
            discountRow.style.setProperty('display', 'none', 'important');
        }

        // 2. Personal voucher discount
        let voucherDiscountVal = 0;
        let voucherPercent = 0;
        if (userVoucherSelect && userVoucherSelect.value !== '') {
            const selectedOpt = userVoucherSelect.options[userVoucherSelect.selectedIndex];
            if (selectedOpt) {
                const text = selectedOpt.textContent;
                const match = text.match(/giảm\s*(\d+)%/i);
                if (match) {
                    voucherPercent = parseInt(match[1], 10);
                }
            }
        }
        if (voucherPercent > 0) {
            voucherDiscountVal = Math.round((currentTotal * voucherPercent) / 100);
            currentTotal -= voucherDiscountVal;

            voucherPercentLabel.textContent = voucherPercent;
            voucherAmountLabel.textContent = '-' + formatVND(voucherDiscountVal);
            voucherDiscountRow.style.setProperty('display', 'flex', 'important');
        } else {
            voucherDiscountRow.style.setProperty('display', 'none', 'important');
        }

        // 3. Loyalty discount (calculated on remaining total)
        let loyaltyDiscountVal = 0;
        if (loyaltyPercent > 0) {
            loyaltyDiscountVal = Math.round((currentTotal * loyaltyPercent) / 100);
            currentTotal -= loyaltyDiscountVal;

            loyaltyPercentLabel.textContent = loyaltyPercent;
            loyaltyAmountLabel.textContent = '-' + formatVND(loyaltyDiscountVal);
            loyaltyDiscountRow.style.setProperty('display', 'flex', 'important');
        } else {
            loyaltyDiscountRow.style.setProperty('display', 'none', 'important');
        }

        // 4. Final total includes shipping fee
        const finalTotal = Math.max(0, currentTotal + activeShippingFee);
        finalTotalLabel.textContent = formatVND(finalTotal);
    }

    if (userVoucherSelect) {
        userVoucherSelect.addEventListener('change', function () {
            recalculateCheckoutTotal();
        });
    }

    // Initialize layout with loyalty percent if user is logged in
    document.addEventListener('DOMContentLoaded', function () {
        recalculateCheckoutTotal();
    });

    if (applyCouponBtn && couponInput) {
        applyCouponBtn.addEventListener('click', function () {
            const code = couponInput.value.trim();
            if (code === '') {
                couponMsg.className = 'small mt-2 text-danger';
                couponMsg.textContent = 'Vui lòng nhập mã giảm giá';
                couponMsg.style.display = 'block';
                return;
            }

            applyCouponBtn.disabled = true;
            fetch('/api/promotions/check?code=' + encodeURIComponent(code))
                .then(res => res.json())
                .then(data => {
                    applyCouponBtn.disabled = false;
                    if (data.valid) {
                        couponMsg.className = 'small mt-2 text-success fw-bold';
                        couponMsg.textContent = data.message;
                        couponMsg.style.display = 'block';

                        // Apply to UI
                        activeDiscountPercent = data.discount_percent;
                        hiddenCoupon.value = data.code;
                        recalculateCheckoutTotal();
                    } else {
                        couponMsg.className = 'small mt-2 text-danger';
                        couponMsg.textContent = data.message;
                        couponMsg.style.display = 'block';

                        // Reset UI
                        hiddenCoupon.value = '';
                        activeDiscountPercent = 0;
                        recalculateCheckoutTotal();
                    }
                })
                .catch(err => {
                    applyCouponBtn.disabled = false;
                    console.error(err);
                    couponMsg.className = 'small mt-2 text-danger';
                    couponMsg.textContent = 'Lỗi kết nối máy chủ';
                    couponMsg.style.display = 'block';
                });
        });
    }

    // VAT checkbox toggle
    const vatCheckbox = document.getElementById('vat_required');
    const vatDetails = document.getElementById('vat-details');
    const vatCard = document.getElementById('vat-auto-card');
    if (vatCheckbox && vatDetails) {
        const previewFields = {
            customer_name: document.getElementById('vat-preview-name'),
            email: document.getElementById('vat-preview-email'),
            shipping_address: document.getElementById('vat-preview-address')
        };
        const refreshVatPreview = function() {
            Object.keys(previewFields).forEach(function(fieldId) {
                const source = document.getElementById(fieldId);
                if (source && previewFields[fieldId]) {
                    previewFields[fieldId].textContent = source.value.trim() || 'Chưa có thông tin';
                }
            });
        };
        const syncVatFields = function() {
            vatDetails.style.display = vatCheckbox.checked ? 'block' : 'none';
            vatCheckbox.setAttribute('aria-expanded', vatCheckbox.checked ? 'true' : 'false');
            if (vatCard) vatCard.classList.toggle('is-selected', vatCheckbox.checked);
            refreshVatPreview();
        };
        Object.keys(previewFields).forEach(function(fieldId) {
            const source = document.getElementById(fieldId);
            if (source) source.addEventListener('input', refreshVatPreview);
        });
        vatCheckbox.addEventListener('change', syncVatFields);
        syncVatFields();
    }

    toggleFields();
})();
</script>

<!-- Map Selection Modal -->
<div class="modal fade" id="mapModal" tabindex="-1" aria-labelledby="mapModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="mapModalLabel">📍 Chọn địa chỉ giao hàng trên bản đồ</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">
                <div class="input-group mb-3">
                    <input type="text" id="map-search-input" class="form-control" placeholder="Nhập địa chỉ cần tìm kiếm (Ví dụ: Landmark 81, TP.HCM)...">
                    <button class="btn btn-danger text-white px-4 fw-bold" type="button" id="map-search-btn">Tìm kiếm</button>
                </div>
                <div id="map-container" style="height: 380px; width: 100%; border-radius: 8px; border: 1px solid #ddd; z-index: 1;"></div>
                <div class="mt-3">
                    <label class="form-label fw-bold small text-muted">Địa điểm đã chọn:</label>
                    <input type="text" id="selected-address-preview" class="form-control bg-light fw-semibold" readonly placeholder="Nhấp chuột chọn điểm trên bản đồ hoặc gõ tìm kiếm...">
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-danger px-4 fw-bold" id="confirm-address-btn" disabled>Xác nhận địa chỉ này</button>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    let map, marker, selectedLat = null, selectedLng = null, selectedAddr = "";
    const stores = [
        { name: 'Trụ sở chính', address: 'Võng Thị, Tây Hồ, Hà Nội', lat: 21.05440, lng: 105.81180, radius: 25 },
        { name: 'Cơ sở 2', address: 'Số 3 Duy Tân, Cầu Giấy, Hà Nội', lat: 21.02940, lng: 105.78250, radius: 25 },
        { name: 'Cơ sở 3', address: 'Lê Duẩn, Hải Châu, Đà Nẵng', lat: 16.05940, lng: 108.22080, radius: 25 },
        { name: 'Cơ sở 4', address: 'Số 99 Nguyễn Huệ, Quận 1, TP. HCM', lat: 10.77560, lng: 106.70190, radius: 25 },
        { name: 'Cơ sở 5', address: 'Số 250 CMT8, Q.3, Sài Gòn', lat: 10.78180, lng: 106.67980, radius: 25 }
    ];

    const openMapBtn = document.getElementById('open-map-btn');
    const currentLocationBtn = document.getElementById('use-current-location-btn');
    if (openMapBtn) {
        openMapBtn.addEventListener('click', function() {
            const modal = new bootstrap.Modal(document.getElementById('mapModal'));
            modal.show();
        });
    }

    // Debounced autocomplete/geocoding khi người dùng gõ địa chỉ thủ công
    const shippingAddressInput = document.getElementById('shipping_address');
    const suggestionsContainer = document.getElementById('address-suggestions');
    const goongKey = '<?= config('goong.api_key') ?>';
    const hasGoong = goongKey && goongKey !== 'dummy_goong_key' && !goongKey.startsWith('t6QOq57e3Y24k5F4lJpZ5M7B2XyCw9v8o7n6m5l4');
    let geocodeTimer = null;

    function parseGoongAddressComponents(components) {
        let province = '';
        let district = '';
        let ward = '';
        let street = '';

        if (Array.isArray(components)) {
            components.forEach(comp => {
                const types = comp.types || [];
                if (types.includes('administrative_area_level_1')) {
                    province = comp.long_name;
                } else if (types.includes('administrative_area_level_2')) {
                    district = comp.long_name;
                } else if (types.includes('administrative_area_level_3') || types.includes('sublocality')) {
                    ward = comp.long_name;
                } else if (types.includes('street_number') || types.includes('route')) {
                    if (street) street += ' ';
                    street += comp.long_name;
                }
            });
        }

        document.getElementById('address_province').value = province;
        document.getElementById('address_district').value = district;
        document.getElementById('address_ward').value = ward;
        document.getElementById('address_street').value = street;
    }

    function parseNominatimAddressComponents(address) {
        if (!address) return;
        const province = address.city || address.province || address.state || address.municipality || '';
        const district = address.suburb || address.district || address.county || '';
        const ward = address.quarter || address.subdistrict || address.town || address.village || '';
        const street = address.road || address.street || address.house_number || '';

        document.getElementById('address_province').value = province;
        document.getElementById('address_district').value = district;
        document.getElementById('address_ward').value = ward;
        document.getElementById('address_street').value = street;
    }

    function showSuggestions(list) {
        if (!suggestionsContainer) return;
        suggestionsContainer.innerHTML = '';
        if (list.length === 0) {
            suggestionsContainer.style.display = 'none';
            return;
        }

        list.forEach(item => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'list-group-item list-group-item-action text-start text-dark p-2 small';
            btn.style.cursor = 'pointer';
            btn.textContent = item.description;

            btn.addEventListener('click', function () {
                shippingAddressInput.value = item.description;
                suggestionsContainer.style.display = 'none';

                if (item.place_id) {
                    // Goong Autocomplete selected
                    fetch(`https://rsapi.goong.io/v2/place/detail?place_id=${item.place_id}&api_key=${goongKey}`)
                        .then(r => r.json())
                        .then(detail => {
                            if (detail && detail.result && detail.result.geometry && detail.result.geometry.location) {
                                const lat = parseFloat(detail.result.geometry.location.lat);
                                const lng = parseFloat(detail.result.geometry.location.lng);
                                document.getElementById('shipping_lat').value = lat.toFixed(8);
                                document.getElementById('shipping_lng').value = lng.toFixed(8);
                                
                                parseGoongAddressComponents(detail.result.address_components);
                                updateShippingPreview(lat, lng);
                                
                                if (map) {
                                    updateMarker(lat, lng);
                                    map.setView([lat, lng], 16);
                                }
                            }
                        })
                        .catch(() => {});
                } else if (item.lat && item.lon) {
                    // Nominatim selected
                    const lat = parseFloat(item.lat);
                    const lng = parseFloat(item.lon);
                    document.getElementById('shipping_lat').value = lat.toFixed(8);
                    document.getElementById('shipping_lng').value = lng.toFixed(8);

                    parseNominatimAddressComponents(item.address);
                    updateShippingPreview(lat, lng);

                    if (map) {
                        updateMarker(lat, lng);
                        map.setView([lat, lng], 16);
                    }
                }
            });
            suggestionsContainer.appendChild(btn);
        });

        suggestionsContainer.style.display = 'block';
    }

    if (shippingAddressInput) {
        shippingAddressInput.addEventListener('input', function () {
            clearTimeout(geocodeTimer);
            const query = this.value.trim();
            if (query.length < 4) {
                if (suggestionsContainer) suggestionsContainer.style.display = 'none';
                return;
            }

            geocodeTimer = setTimeout(function () {
                if (hasGoong) {
                    // Goong API Autocomplete
                    fetch(`https://rsapi.goong.io/Place/Autocomplete?api_key=${goongKey}&input=${encodeURIComponent(query)}&limit=5`)
                        .then(r => r.json())
                        .then(data => {
                            if (data && data.predictions) {
                                const list = data.predictions.map(p => ({
                                    place_id: p.place_id,
                                    description: p.description
                                }));
                                showSuggestions(list);
                            }
                        })
                        .catch(() => {});
                } else {
                    // Nominatim Search fallback
                    fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=5&addressdetails=1&accept-language=vi`)
                        .then(r => r.json())
                        .then(results => {
                            if (results && results.length > 0) {
                                const list = results.map(r => ({
                                    description: r.display_name,
                                    lat: r.lat,
                                    lon: r.lon,
                                    address: r.address
                                }));
                                showSuggestions(list);
                            }
                        })
                        .catch(() => {});
                }
            }, 500);
        });

        // Hide suggestions when clicking outside
        document.addEventListener('click', function (e) {
            if (suggestionsContainer && !shippingAddressInput.contains(e.target) && !suggestionsContainer.contains(e.target)) {
                suggestionsContainer.style.display = 'none';
            }
        });
    }

    if (currentLocationBtn) {
        currentLocationBtn.addEventListener('click', function () {
            if (!navigator.geolocation) {
                alert('Trình duyệt không hỗ trợ lấy vị trí hiện tại.');
                return;
            }

            currentLocationBtn.disabled = true;
            currentLocationBtn.textContent = 'Đang lấy...';
            navigator.geolocation.getCurrentPosition(function (position) {
                currentLocationBtn.disabled = false;
                currentLocationBtn.textContent = 'Vị trí hiện tại';
                updateMarker(position.coords.latitude, position.coords.longitude);
            }, function () {
                currentLocationBtn.disabled = false;
                currentLocationBtn.textContent = 'Vị trí hiện tại';
                alert('Không lấy được vị trí. Vui lòng cấp quyền hoặc chọn trên bản đồ.');
            }, { enableHighAccuracy: true, timeout: 10000 });
        });
    }

    document.getElementById('mapModal').addEventListener('shown.bs.modal', function () {
        if (!map) {
            // Mặc định tâm bản đồ ở Hà Nội: 21.0285, 105.8542
            map = L.map('map-container').setView([21.0285, 105.8542], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            // Click trên bản đồ để chọn tọa độ
            map.on('click', function (e) {
                updateMarker(e.latlng.lat, e.latlng.lng);
            });
        } else {
            // Cập nhật lại kích thước hiển thị của map tránh lỗi lệch tiles
            map.invalidateSize();
        }

        // Nếu đã có sẵn địa chỉ trong ô text, thử tìm kiếm địa chỉ đó trước
        const currentAddr = document.getElementById('shipping_address').value.trim();
        if (currentAddr !== "" && !selectedLat) {
            document.getElementById('map-search-input').value = currentAddr;
            performSearch(true);
        }
    });

    function updateMarker(lat, lng) {
        selectedLat = lat;
        selectedLng = lng;
        document.getElementById('shipping_lat').value = lat.toFixed(8);
        document.getElementById('shipping_lng').value = lng.toFixed(8);
        updateShippingPreview(lat, lng);
        
        if (map) {
            if (marker) {
                marker.setLatLng([lat, lng]);
            } else {
                marker = L.marker([lat, lng], { draggable: true }).addTo(map);
                marker.on('dragend', function (e) {
                    const pos = marker.getLatLng();
                    updateMarker(pos.lat, pos.lng);
                });
            }

            map.panTo([lat, lng]);
        }

        const previewInput = document.getElementById('selected-address-preview');
        const confirmBtn = document.getElementById('confirm-address-btn');
        if (previewInput) {
            previewInput.value = "Đang xác thực tọa độ và dịch địa chỉ...";
        }
        if (confirmBtn) {
            confirmBtn.disabled = true;
        }

        // Gọi Nominatim Reverse Geocoding API để dịch tọa độ thành địa chỉ chữ
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&accept-language=vi`)
            .then(res => res.json())
            .then(data => {
                if (data && data.display_name) {
                    selectedAddr = data.display_name;
                    if (previewInput) {
                        previewInput.value = selectedAddr;
                    }
                    if (document.getElementById('shipping_address').value.trim() === '') {
                        document.getElementById('shipping_address').value = selectedAddr;
                    }
                    if (confirmBtn) {
                        confirmBtn.disabled = false;
                    }
                    if (data.address) {
                        parseNominatimAddressComponents(data.address);
                    }
                } else {
                    if (previewInput) {
                        previewInput.value = `Tọa độ: ${lat.toFixed(5)}, ${lng.toFixed(5)} (Không dịch được địa chỉ)`;
                    }
                    selectedAddr = `Tọa độ: ${lat.toFixed(5)}, ${lng.toFixed(5)}`;
                    if (confirmBtn) {
                        confirmBtn.disabled = false;
                    }
                }
            })
            .catch(err => {
                console.error(err);
                if (previewInput) {
                    previewInput.value = `Tọa độ: ${lat.toFixed(5)}, ${lng.toFixed(5)} (Lỗi mạng kết nối địa chỉ)`;
                }
                selectedAddr = `Tọa độ: ${lat.toFixed(5)}, ${lng.toFixed(5)}`;
                if (confirmBtn) {
                    confirmBtn.disabled = false;
                }
            });
    }

    function performSearch(quiet = false) {
        const query = document.getElementById('map-search-input').value.trim();
        if (query === "") return;

        const searchBtn = document.getElementById('map-search-btn');
        searchBtn.disabled = true;
        searchBtn.textContent = "Đang tìm...";

        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=1&accept-language=vi`)
            .then(res => res.json())
            .then(results => {
                searchBtn.disabled = false;
                searchBtn.textContent = "Tìm kiếm";
                
                if (results && results.length > 0) {
                    const lat = parseFloat(results[0].lat);
                    const lon = parseFloat(results[0].lon);
                    updateMarker(lat, lon);
                    map.setView([lat, lon], 16);
                } else {
                    if (!quiet) {
                        alert("Không tìm thấy địa điểm nào phù hợp. Vui lòng nhập địa chỉ cụ thể hơn.");
                    }
                }
            })
            .catch(err => {
                searchBtn.disabled = false;
                searchBtn.textContent = "Tìm kiếm";
                console.error(err);
                if (!quiet) {
                    alert("Gặp sự cố kết nối máy chủ tìm kiếm.");
                }
            });
    }

    document.getElementById('map-search-btn').addEventListener('click', function() {
        performSearch(false);
    });
    
    document.getElementById('map-search-input').addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            performSearch(false);
        }
    });

    document.getElementById('confirm-address-btn').addEventListener('click', function () {
        if (selectedAddr) {
            document.getElementById('shipping_address').value = selectedAddr;
            // Ẩn modal Bootstrap
            const modalEl = document.getElementById('mapModal');
            const modalInstance = bootstrap.Modal.getInstance(modalEl);
            if (modalInstance) {
                modalInstance.hide();
            }
        }
    });

    function distanceKm(lat1, lng1, lat2, lng2) {
        const r = 6371;
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLng = (lng2 - lng1) * Math.PI / 180;
        const a = Math.sin(dLat / 2) ** 2
            + Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180)
            * Math.sin(dLng / 2) ** 2;
        return r * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    }

    function shippingFee(distance) {
        if (distance <= 3) return 0;
        if (distance <= 10) return 25000;
        return 25000 + Math.ceil(distance - 10) * 5000;
    }

    function etaLabel(distance) {
        if (distance > 100) return 'mất 3 ngày';
        if (distance > 50) return 'mất 2 ngày';
        if (distance > 20) return 'mất 1 ngày';
        if (distance < 10) return 'mất 1h';
        return 'mất 1 ngày';
    }

    function updateShippingPreview(lat, lng) {
        const box = document.getElementById('shipping-estimate-box');
        if (!box) return;

        const nearest = stores
            .map(store => ({ ...store, distance: distanceKm(lat, lng, store.lat, store.lng) }))
            .sort((a, b) => a.distance - b.distance)[0];

        const fee = shippingFee(nearest.distance);
        if (window.hadesSetShippingFee) {
            window.hadesSetShippingFee(fee);
        }

        box.style.display = 'block';
        box.innerHTML = '<strong>Giao từ ' + nearest.name + '</strong> — cách bạn khoảng '
            + nearest.distance.toFixed(1) + ' km. Phí ship: <strong>' + formatMoneyClient(fee)
            + '</strong>. ' + etaLabel(nearest.distance) + '.<br><span class="small text-muted">'
            + nearest.address + '</span>';
    }

    function formatMoneyClient(amount) {
        return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount).replace(/₫/g, 'đ');
    }
})();
</script>
