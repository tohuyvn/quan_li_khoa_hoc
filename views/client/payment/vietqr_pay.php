<?php
$title = $title ?? 'Thanh toán VietQR';
$bank = $bank ?? \App\Services\VietQrService::config();
$qrUrl = $qrUrl ?? \App\Services\VietQrService::localFallbackUrl();
$qrFallback = $qrFallback ?? \App\Services\VietQrService::localFallbackUrl();
$transferContent = $transferContent ?? '';
$amount = (int) ($amount ?? ($order['total_amount'] ?? 0));
?>

<div class="section-title">
    <h2>🏦 Thanh toán chuyển khoản VietQR</h2>
</div>

<div class="row g-4 justify-content-center">
    <div class="col-lg-9">
        <div class="content-card border-top border-4" style="border-top-color:#d70018 !important;">
            <div class="text-center mb-4">
                <div class="fw-bold" style="font-size:22px;color:#d70018;">MB BANK · VIETQR</div>
                <p class="text-muted mb-0">
                    Đơn <span class="font-monospace fw-bold"><?= e($order['order_code'] ?? ('#' . $order['id'])) ?></span>
                    · Số tiền: <strong class="text-danger"><?= formatMoney($amount) ?></strong>
                </p>
            </div>

            <div class="row align-items-center g-4">
                <div class="col-md-5 text-center">
                    <div class="p-3 border rounded shadow-sm bg-white d-inline-block">
                        <img
                            src="<?= e($qrUrl) ?>"
                            alt="VietQR MB Bank"
                            class="img-fluid"
                            style="max-width:260px;width:100%;display:block;margin:0 auto;"
                            onerror="this.onerror=null;this.src='<?= e($qrFallback) ?>';"
                        >
                    </div>
                    <div class="mt-2 text-muted small">Mở app ngân hàng → Quét VietQR / NAPAS 247</div>
                </div>

                <div class="col-md-7">
                    <h5 class="fw-bold mb-3">Thông tin chuyển khoản</h5>
                    <table class="table table-sm table-borderless mb-3">
                        <tr>
                            <td class="text-muted" width="140">Ngân hàng</td>
                            <td class="fw-semibold"><?= e($bank['bank_name']) ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Số tài khoản</td>
                            <td>
                                <code class="fs-6" id="stkText"><?= e($bank['account_no']) ?></code>
                                <button type="button" class="btn btn-sm btn-outline-secondary ms-1" onclick="copyText('stkText')">Copy</button>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Chủ tài khoản</td>
                            <td class="fw-semibold"><?= e($bank['account_name_display']) ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Số tiền</td>
                            <td class="fw-bold text-danger"><?= formatMoney($amount) ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Nội dung CK</td>
                            <td>
                                <code id="ndckText"><?= e($transferContent) ?></code>
                                <button type="button" class="btn btn-sm btn-outline-secondary ms-1" onclick="copyText('ndckText')">Copy</button>
                            </td>
                        </tr>
                    </table>

                    <ol class="small text-muted ps-3 mb-4">
                        <li>Quét mã QR bằng app ngân hàng (MB, Vietcombank, Techcombank…).</li>
                        <li>Kiểm tra đúng tên <strong><?= e($bank['account_name_display']) ?></strong> và số tiền.</li>
                        <li>Giữ nguyên nội dung chuyển khoản để shop đối soát nhanh.</li>
                        <li>Bấm nút bên dưới sau khi chuyển thành công — admin sẽ xác nhận.</li>
                    </ol>

                    <form method="post" action="/payment/vietqr/confirm" class="d-grid gap-2">
                        <?= csrf_field() ?>
                        <input type="hidden" name="order_id" value="<?= (int)$order['id'] ?>">
                        <button type="submit" class="btn btn-danger btn-lg fw-bold">Tôi đã chuyển khoản</button>
                        <a href="/orders/<?= (int)$order['id'] ?>" class="btn btn-outline-secondary">Xem đơn hàng</a>
                    </form>

                    <?php require BASE_PATH . '/views/partials/order_items_thumbs.php'; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyText(id) {
    const el = document.getElementById(id);
    if (!el) return;
    navigator.clipboard.writeText(el.textContent.trim()).then(function () {
        alert('Đã copy: ' + el.textContent.trim());
    });
}
</script>
