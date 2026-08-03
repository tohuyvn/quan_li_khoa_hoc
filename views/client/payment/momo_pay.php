<?php
$title = 'Thanh toán MoMo';
$hasRealQr = !empty($momoData['qrCodeUrl']) || !empty($payUrl);
$qrData = !empty($momoData['qrCodeUrl']) ? $momoData['qrCodeUrl'] : ($payUrl ?? '');
if ($qrData !== '' && preg_match('/^https?:\/\//i', $qrData)) {
    $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=' . urlencode($qrData);
} elseif ($qrData !== '') {
    $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=' . urlencode($qrData);
} else {
    $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=' . urlencode(config('base_url') . '/payment/momo/return?resultCode=0&extraData=' . urlencode(base64_encode(json_encode(['orderId' => (int)$order['id']]))));
}
?>

<div class="section-title">
    <h2><span style="color:#ae2070">💗</span> Thanh toán qua MoMo</h2>
</div>

<div class="row g-4 justify-content-center">
    <div class="col-lg-8">
        <div class="content-card border-top border-4" style="border-top-color: #ae2070 !important;">
            <div class="text-center mb-4">
                <div style="font-size:28px;font-weight:700;color:#ae2070;letter-spacing:1px;">MOMO E-WALLET</div>
                <p class="text-muted mb-0">Đơn hàng <span style="font-family: monospace; font-weight: bold;"><?= e($order['order_code'] ?? ('#' . $order['id'])) ?></span> · <?= formatMoney($order['total_amount']) ?></p>
            </div>

            <div class="row align-items-center g-4">
                <div class="col-md-5 text-center">
                    <div class="p-3 border rounded shadow-sm bg-white d-inline-block qr-pulse-ring">
                        <img src="<?= e($qrCodeUrl) ?>" alt="MoMo QR Code" class="img-fluid" style="max-width:220px; width:100%; display:block; margin:0 auto;">
                        <span class="badge bg-danger mt-2">MÔI TRƯỜNG THỬ NGHIỆM</span>
                    </div>
                    <div class="mt-2 text-muted small">
                        <?= $hasRealQr ? 'Quét mã bằng app <strong>MoMo Sandbox</strong> trên điện thoại' : 'Quét mã để mở liên kết thanh toán' ?>
                    </div>
                </div>

                <div class="col-md-7">
                    <h5 class="fw-bold mb-3" style="color: #ae2070;">Hướng dẫn thanh toán QR:</h5>
                    <ol class="ps-3 text-muted mb-4" style="line-height: 1.6;">
                        <?php if ($hasRealQr): ?>
                        <li class="mb-2">Mở app <strong>MoMo (Sandbox)</strong> trên điện thoại.</li>
                        <li class="mb-2">Chọn <strong>Quét mã QR</strong> và quét mã bên cạnh.</li>
                        <li class="mb-2">Xác nhận thanh toán trong app MoMo.</li>
                        <li class="mb-0">Trang này sẽ <strong>tự động chuyển hướng</strong> khi thanh toán thành công.</li>
                        <?php else: ?>
                        <li class="mb-2">Mở ứng dụng <strong>Camera</strong> hoặc trình quét mã QR.</li>
                        <li class="mb-2">Quét mã QR bên cạnh để truy cập liên kết thanh toán.</li>
                        <li class="mb-0">Màn hình sẽ tự cập nhật sau khi hoàn tất.</li>
                        <?php endif; ?>
                    </ol>

                    <?php if (!empty($payUrl)): ?>
                    <div class="p-3 rounded mb-3" style="background-color: #fff0f6; border: 1px solid #ffd3e2; color: #ae2070; font-size: 0.9rem;">
                        <strong>Gợi ý:</strong> Hoặc bấm nút bên dưới để mở cổng MoMo Sandbox trực tiếp trên trình duyệt.
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <hr class="my-4">

            <?php require BASE_PATH . '/views/partials/order_items_thumbs.php'; ?>

            <?php if (!empty($payUrl)): ?>
            <a href="/payment/momo/gateway" class="btn w-100 py-2 fw-semibold text-white mb-2 mt-3" style="background-color: #ae2070; border-color: #ae2070;">
                Mở cổng MoMo Sandbox
            </a>
            <?php endif; ?>

            <?php if (!$hasRealQr): ?>
            <form method="post" action="/payment/momo/process">
                <?= csrf_field() ?>
                <button type="submit" class="btn w-100 py-2 fw-semibold btn-outline-secondary">
                    Xác nhận đã thanh toán (Giả lập — chỉ khi chưa cấu hình MoMo)
                </button>
            </form>
            <?php endif; ?>

            <div class="text-center mt-3">
                <a href="/checkout" class="small text-muted">← Quay lại</a>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const orderId = <?= (int)$order['id'] ?>;
    let redirected = false;
    let pollTimer = null;

    function checkPaymentStatus() {
        if (redirected) return;
        fetch('/payment/momo/status?order_id=' + orderId)
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.payment_status === 'paid' || data.status === 'confirmed') {
                    redirected = true;
                    if (pollTimer) clearInterval(pollTimer);
                    window.location.replace('/orders/success');
                }
            })
            .catch(function () {});
    }

    checkPaymentStatus();
    pollTimer = setInterval(checkPaymentStatus, 4000);

    document.addEventListener('visibilitychange', function () {
        if (document.hidden && pollTimer) {
            clearInterval(pollTimer);
            pollTimer = null;
        } else if (!document.hidden && !redirected && !pollTimer) {
            checkPaymentStatus();
            pollTimer = setInterval(checkPaymentStatus, 4000);
        }
    });
})();
</script>
