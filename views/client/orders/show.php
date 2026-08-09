<?php
$title = 'Chi tiết đơn hàng ' . ($order['order_code'] ?? ('#' . $order['id']));
$currentStatus = $order['status'] ?? 'pending';
$shouldTrackShipment = $currentStatus === 'shipping' && !empty($order['shipping_lat']) && !empty($order['shipping_lng']);
?>

<style>
.order-detail-header {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    border-radius: 18px;
    padding: 24px 28px;
    color: #fff;
    margin-bottom: 24px;
    box-shadow: 0 10px 25px rgba(15, 23, 42, 0.12);
}
.order-detail-card {
    background: #ffffff;
    border-radius: 18px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04);
    padding: 24px;
    margin-bottom: 24px;
}
.timeline-step {
    flex: 1;
    text-align: center;
    position: relative;
}
.timeline-step-icon {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #f1f5f9;
    color: #94a3b8;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 13px;
    position: relative;
    z-index: 2;
}
.timeline-step.active .timeline-step-icon {
    background: #10b981;
    color: #ffffff;
    box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.2);
}
.timeline-line {
    position: absolute;
    top: 16px;
    left: 50%;
    width: 100%;
    height: 3px;
    background: #e2e8f0;
    z-index: 1;
}
.timeline-step.active .timeline-line {
    background: #10b981;
}
.timeline-step:last-child .timeline-line {
    display: none;
}
</style>

<!-- Header Banner -->
<div class="order-detail-header d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h2 class="h3 fw-bold mb-0 text-white">Mã đơn: <?= e($order['order_code'] ?? ('#' . $order['id'])) ?></h2>
            <span class="badge bg-danger rounded-pill px-3 py-1 fw-bold"><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></span>
        </div>
        <p class="mb-0 text-slate-300 small" style="color: #cbd5e1;">Cảm ơn bạn đã mua sắm tại Hades Shop. Dưới đây là thông tin chi tiết đơn hàng của bạn.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="/orders" class="btn btn-outline-light btn-sm rounded-3 px-3 py-2 fw-semibold">
            <i class="bi bi-arrow-left me-1"></i> Trở về danh sách
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: Products List & Invoice -->
    <div class="col-lg-8">
        <div class="order-detail-card">
            <h5 class="fw-bold mb-3 text-dark d-flex align-items-center gap-2">
                <i class="bi bi-bag-check-fill text-danger"></i> Danh Sách Sản Phẩm (<?= count($items) ?> mặt hàng)
            </h5>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Sản Phẩm</th>
                            <th class="text-center">Số Lượng</th>
                            <th class="text-end d-none d-md-table-cell">Đơn Giá</th>
                            <th class="text-end">Thành Tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <?php
                            $pid = (int) ($item['product_id'] ?? 0);
                            $lineUnits = $unitsByProduct[$pid] ?? [];
                            ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-3 border p-1 bg-light" style="width: 54px; height: 54px; flex-shrink:0;">
                                            <img src="<?= e(imageUrl($item['product_image'] ?? 'logo.webp', 'thumb')) ?>"
                                                 alt="<?= e($item['product_name']) ?>"
                                                 style="width: 100%; height: 100%; object-fit: contain;"
                                                 onerror="this.onerror=null;this.src='/image/logo.webp';">
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark mb-1"><?= e($item['product_name']) ?></div>
                                            <?php if (!empty($lineUnits)): ?>
                                                <div class="small text-muted">
                                                    IMEI / Serial:
                                                    <?php foreach ($lineUnits as $unit): ?>
                                                        <code class="bg-light text-primary px-1 border rounded me-1"><?= e($unit['imei']) ?></code>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center fw-bold">x<?= (int)$item['quantity'] ?></td>
                                <td class="text-end text-muted d-none d-md-table-cell small"><?= formatMoney($item['price']) ?></td>
                                <td class="text-end fw-bold text-dark"><?= formatMoney($item['price'] * $item['quantity']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="3" class="text-end d-none d-md-table-cell">Tạm tính:</td>
                            <td colspan="2" class="text-end d-md-none">Tạm tính:</td>
                            <td class="text-end fw-bold"><?= formatMoney($order['total_amount'] + ($order['discount_amount'] ?? 0)) ?></td>
                        </tr>
                        <?php if (!empty($order['discount_amount'])): ?>
                            <tr>
                                <td colspan="3" class="text-end d-none d-md-table-cell text-danger">Giảm giá:</td>
                                <td colspan="2" class="text-end d-md-none text-danger">Giảm giá:</td>
                                <td class="text-end text-danger fw-bold">-<?= formatMoney($order['discount_amount']) ?></td>
                            </tr>
                        <?php endif; ?>
                        <?php if (!empty($order['shipping_fee'])): ?>
                            <tr>
                                <td colspan="3" class="text-end d-none d-md-table-cell">Phí giao hàng:</td>
                                <td colspan="2" class="text-end d-md-none">Phí ship:</td>
                                <td class="text-end"><?= formatMoney($order['shipping_fee']) ?></td>
                            </tr>
                        <?php endif; ?>
                        <tr>
                            <td colspan="3" class="text-end d-none d-md-table-cell fw-bold fs-6">Tổng thanh toán:</td>
                            <td colspan="2" class="text-end d-md-none fw-bold fs-6">Tổng cộng:</td>
                            <td class="text-end"><h4 class="text-danger fw-bold mb-0"><?= formatMoney($order['total_amount']) ?></h4></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Column: Order Journey & Customer Shipping Info -->
    <div class="col-lg-4">
        <!-- Order Progress -->
        <div class="order-detail-card">
            <h5 class="fw-bold mb-3 text-dark d-flex align-items-center gap-2">
                <i class="bi bi-truck text-primary"></i> Hành Trình Đơn Hàng
            </h5>

            <?php
            $steps = ['pending', 'confirmed', 'packing', 'shipping', 'completed'];
            $stepLabels = [
                'pending' => 'Chờ xử lý',
                'confirmed' => 'Xác nhận',
                'packing' => 'Đóng gói',
                'shipping' => 'Đang giao',
                'completed' => 'Hoàn thành'
            ];
            $isCancelled = $currentStatus === 'cancelled';
            ?>

            <?php if ($isCancelled): ?>
                <div class="alert alert-danger py-2 text-center small fw-bold mb-0">Đơn hàng này đã bị hủy</div>
            <?php else: ?>
                <div class="d-flex justify-content-between my-3">
                    <?php
                    $activeCount = array_search($currentStatus, $steps);
                    if ($activeCount === false) $activeCount = -1;
                    ?>
                    <?php foreach ($steps as $index => $step): 
                        $isActive = $index <= $activeCount;
                    ?>
                        <div class="timeline-step <?= $isActive ? 'active' : '' ?>">
                            <div class="timeline-line"></div>
                            <div class="timeline-step-icon"><?= $index + 1 ?></div>
                            <div class="small mt-2" style="font-size: 10px; font-weight: 700;"><?= $stepLabels[$step] ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Shipping Info Card -->
        <div class="order-detail-card">
            <h5 class="fw-bold mb-3 text-dark d-flex align-items-center gap-2">
                <i class="bi bi-geo-alt-fill text-danger"></i> Thông Tin Giao Hàng
            </h5>
            <div class="small text-muted">
                <div class="mb-2"><strong>Người nhận:</strong> <span class="text-dark fw-semibold"><?= e($order['customer_name']) ?></span></div>
                <div class="mb-2"><strong>Số điện thoại:</strong> <span class="text-dark fw-semibold"><?= e($order['phone'] ?? 'N/A') ?></span></div>
                <div class="mb-2"><strong>Địa chỉ:</strong> <span class="text-dark"><?= e($order['shipping_address'] ?? 'N/A') ?></span></div>
                <div class="mb-2"><strong>Thanh toán:</strong> 
                    <span class="badge bg-light text-dark border ms-1">
                        <?= strtoupper(e($order['payment_method'] ?? 'COD')) ?>
                    </span>
                </div>
            </div>

            <?php if ($order['status'] === 'pending' || $order['status'] === 'confirmed'): ?>
                <form action="/orders/<?= $order['id'] ?>/cancel" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này?');" class="mt-3">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-outline-danger btn-sm w-100 rounded-3 fw-bold py-2">
                        <i class="bi bi-x-circle me-1"></i> Hủy Đơn Hàng
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>
