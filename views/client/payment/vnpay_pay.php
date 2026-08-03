<?php
$title = 'Thanh toán VNPay';
$test = config('vnpay.test_card');
?>

<div class="section-title"><h2>💳 Thanh toán VNPay</h2></div>

<div class="row g-4 justify-content-center">
    <div class="col-lg-8">
        <div class="content-card">
            <div class="text-center mb-4">
                <div style="font-size:28px;font-weight:700;color:#005baa">CỔNG THANH TOÁN VNPAY</div>
                <p class="text-muted mb-0">Đơn hàng #<?= $order['id'] ?> · <?= formatMoney($order['total_amount']) ?></p>
            </div>

            <div class="p-3 border rounded mb-4" style="background:#f8f9fa">
                <p class="mb-1"><strong>Ngân hàng:</strong> <?= e($card['bank_name'] ?? vnpayBankName($card['bank'] ?? '')) ?></p>
                <p class="mb-1"><strong>Số thẻ:</strong> <?= e($card['card_number'] ?? '') ?></p>
                <p class="mb-1"><strong>Chủ thẻ:</strong> <?= e($card['cardholder'] ?? '') ?></p>
                <p class="mb-0"><strong>Ngày phát hành:</strong> <?= e($card['issue_date'] ?? '') ?></p>
            </div>

            <?php require BASE_PATH . '/views/partials/order_items_thumbs.php'; ?>

            <form method="post" action="/payment/vnpay/process" class="mt-3">
            <?= csrf_field() ?>
                <input type="hidden" name="bank" value="<?= e($card['bank'] ?? '') ?>">
                <input type="hidden" name="card_number" value="<?= e($card['card_number'] ?? '') ?>">
                <input type="hidden" name="card_holder" value="<?= e($card['cardholder'] ?? '') ?>">
                <input type="hidden" name="issue_date" value="<?= e($card['issue_date'] ?? '') ?>">

                <div class="mb-3">
                    <label class="form-label fw-semibold">Mật khẩu OTP *</label>
                    <input type="text" name="otp" class="form-control form-control-lg text-center"
                           placeholder="Nhập mã OTP" maxlength="6" inputmode="numeric" required autofocus>
                    <div class="form-text">OTP test sandbox: <code><?= e($test['otp']) ?></code></div>
                </div>

                <button type="submit" class="btn btn-primary-hades w-100 py-2">Xác nhận thanh toán</button>
            </form>

            <div class="text-center mt-3">
                <a href="/payment/vnpay/gateway" class="small text-muted">Hoặc thanh toán qua cổng VNPay Sandbox</a> ·
                <a href="/checkout" class="small text-muted">← Quay lại</a>
            </div>
        </div>
    </div>
</div>
