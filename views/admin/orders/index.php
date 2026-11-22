<?php
$queryString = http_build_query($_GET);
$exportUrl = "/admin/orders/export-csv" . ($queryString ? '?' . $queryString : '');

$totalOrdersCount = count($orders ?? []);
$pendingCount = 0;
$shippingCount = 0;
$completedCount = 0;
$totalRevenueSum = 0;

if (!empty($orders)) {
    foreach ($orders as $o) {
        $st = $o['status'] ?? '';
        if ($st === 'pending') $pendingCount++;
        elseif ($st === 'shipping' || $st === 'packing') $shippingCount++;
        elseif ($st === 'completed') {
            $completedCount++;
            $totalRevenueSum += (float)($o['total_amount'] ?? 0);
        }
    }
}
?>

<div class="container-fluid py-4">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-danger bg-opacity-25 text-danger border border-danger border-opacity-25 rounded-pill px-3 py-1">
                    <i class="bi bi-cart-check-fill me-1"></i> Order Processing
                </span>
                <span class="text-white-50 small">• <?= number_format($totalOrdersCount) ?> đơn hàng</span>
            </div>
            <h1 class="h3 fw-bold text-white mb-1">Quản Lý Đơn Hàng & Khóa Học</h1>
            <p class="text-muted mb-0 small">Theo dõi đơn hàng real-time, xác nhận vận chuyển, kiểm soát thanh toán & xuất báo cáo</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= $exportUrl ?>" class="btn-refract-action d-inline-flex align-items-center gap-2">
                <i class="bi bi-file-earmark-spreadsheet"></i>
                <span>Xuất File CSV</span>
            </a>
        </div>
    </div>

    <!-- Stat Cards (Refract Glass Style) -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="refract-card p-3 h-100">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="small text-muted fw-semibold">Tổng Số Đơn</span>
                    <i class="bi bi-receipt text-primary"></i>
                </div>
                <div class="fs-4 fw-bold text-white"><?= number_format($totalOrdersCount) ?> <span class="fs-6 fw-normal text-muted">đơn</span></div>
                <div class="small text-white-50" style="font-size: 11px;">Toàn bộ đơn hàng</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="refract-card p-3 h-100">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="small text-muted fw-semibold">Chờ Xử Lý</span>
                    <i class="bi bi-hourglass-split text-warning"></i>
                </div>
                <div class="fs-4 fw-bold text-warning"><?= number_format($pendingCount) ?> <span class="fs-6 fw-normal text-muted">đơn</span></div>
                <div class="small text-white-50" style="font-size: 11px;">Cần xác nhận / thanh toán</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="refract-card p-3 h-100">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="small text-muted fw-semibold">Đang Giao Hàng</span>
                    <i class="bi bi-truck text-info"></i>
                </div>
                <div class="fs-4 fw-bold text-info"><?= number_format($shippingCount) ?> <span class="fs-6 fw-normal text-muted">đơn</span></div>
                <div class="small text-white-50" style="font-size: 11px;">Đang trên đường vận chuyển</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="refract-card p-3 h-100">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="small text-muted fw-semibold">Doanh Thu Đơn Xong</span>
                    <i class="bi bi-currency-dollar text-success"></i>
                </div>
                <div class="fs-4 fw-bold text-success"><?= number_format($totalRevenueSum, 0, ',', '.') ?> đ</div>
                <div class="small text-white-50" style="font-size: 11px;">Đơn hoàn thành</div>
            </div>
        </div>
    </div>

    <!-- Filter Toolbar (Refract Pill Card) -->
    <div class="refract-card p-3 mb-4">
        <form method="get" action="/admin/orders" class="row g-2 align-items-center">
            <div class="col-md-3">
                <div class="refract-search-pill w-100">
                    <i class="bi bi-search text-muted me-2"></i>
                    <input type="text" name="q" class="border-0 bg-transparent text-white w-100" style="outline: none; font-size: 0.85rem;" placeholder="Mã đơn, KH, SĐT..." value="<?= e($search ?? '') ?>">
                </div>
            </div>
            <div class="col-md-2">
                <select name="period" class="form-select form-select-sm">
                    <option value="">Tất cả thời gian</option>
                    <option value="day" <?= ($periodFilter ?? '') === 'day' ? 'selected' : '' ?>>📅 Hôm nay</option>
                    <option value="week" <?= ($periodFilter ?? '') === 'week' ? 'selected' : '' ?>>🗓️ Tuần này</option>
                    <option value="month" <?= ($periodFilter ?? '') === 'month' ? 'selected' : '' ?>>📊 Tháng này</option>
                    <option value="quarter" <?= ($periodFilter ?? '') === 'quarter' ? 'selected' : '' ?>>📈 Quý này</option>
                    <option value="year" <?= ($periodFilter ?? '') === 'year' ? 'selected' : '' ?>>🏆 Năm nay</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Tất cả trạng thái</option>
                    <option value="pending" <?= ($statusFilter ?? '') === 'pending' ? 'selected' : '' ?>>Chờ xử lý</option>
                    <option value="confirmed" <?= ($statusFilter ?? '') === 'confirmed' ? 'selected' : '' ?>>Đã xác nhận</option>
                    <option value="packing" <?= ($statusFilter ?? '') === 'packing' ? 'selected' : '' ?>>Đang đóng gói</option>
                    <option value="shipping" <?= ($statusFilter ?? '') === 'shipping' ? 'selected' : '' ?>>Đang giao hàng</option>
                    <option value="completed" <?= ($statusFilter ?? '') === 'completed' ? 'selected' : '' ?>>Đã hoàn thành</option>
                    <option value="cancelled" <?= ($statusFilter ?? '') === 'cancelled' ? 'selected' : '' ?>>Đã hủy</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="payment_method" class="form-select form-select-sm">
                    <option value="">Tất cả cổng TT</option>
                    <option value="cash" <?= ($paymentMethodFilter ?? '') === 'cash' ? 'selected' : '' ?>>COD / Tiền mặt</option>
                    <option value="bank_transfer" <?= ($paymentMethodFilter ?? '') === 'bank_transfer' ? 'selected' : '' ?>>VietQR / CK</option>
                    <option value="vnpay" <?= ($paymentMethodFilter ?? '') === 'vnpay' ? 'selected' : '' ?>>VNPay</option>
                    <option value="momo" <?= ($paymentMethodFilter ?? '') === 'momo' ? 'selected' : '' ?>>Ví MoMo</option>
                    <option value="installment" <?= ($paymentMethodFilter ?? '') === 'installment' ? 'selected' : '' ?>>Trả góp</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2 justify-content-end">
                <button type="submit" class="btn-refract-action btn-sm">
                    <i class="bi bi-filter me-1"></i> Lọc
                </button>
                <a href="/admin/orders" class="btn btn-outline-secondary btn-sm px-3 rounded-pill d-inline-flex align-items-center">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Đặt lại
                </a>
            </div>
        </form>
    </div>

    <!-- Table Card (Refract Glass Table) -->
    <div class="refract-card p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th width="120" class="ps-4">Mã Đơn</th>
                        <th>Khách Hàng</th>
                        <th>Thời Gian</th>
                        <th>Tổng Tiền</th>
                        <th>Phương Thức</th>
                        <th class="text-center">Thanh Toán</th>
                        <th class="text-center">Trạng Thái Đơn</th>
                        <th width="110" class="text-end pe-4">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="text-muted py-3">
                                    <i class="bi bi-cart-x fs-1 d-block mb-3 text-secondary opacity-50"></i>
                                    <h5 class="fw-bold text-white">Không tìm thấy đơn hàng</h5>
                                    <p class="small text-muted mb-0">Không có đơn hàng nào phù hợp với bộ lọc hiện tại.</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($orders as $o):
                            $st = $o['status'] ?? 'pending';
                            $statusText = match ($st) {
                                'confirmed' => 'Đã xác nhận',
                                'packing' => 'Đang đóng gói',
                                'shipping' => 'Đang giao',
                                'completed' => 'Hoàn thành',
                                'cancelled' => 'Đã hủy',
                                default => 'Chờ xử lý'
                            };

                            $statusBadgeClass = match ($st) {
                                'confirmed' => 'bg-info bg-opacity-25 text-info border border-info border-opacity-25',
                                'packing' => 'bg-primary bg-opacity-25 text-primary border border-primary border-opacity-25',
                                'shipping' => 'bg-cyan text-cyan border border-info border-opacity-25',
                                'completed' => 'bg-success bg-opacity-25 text-success border border-success border-opacity-25',
                                'cancelled' => 'bg-danger bg-opacity-25 text-danger border border-danger border-opacity-25',
                                default => 'bg-warning bg-opacity-25 text-warning border border-warning border-opacity-25'
                            };
                            
                            $paySt = $o['payment_status'] ?? 'pending';
                            $payStatusText = match ($paySt) {
                                'paid' => 'Đã thanh toán',
                                'refunded' => 'Đã hoàn tiền',
                                'failed' => 'Thất bại',
                                default => 'Chưa thanh toán'
                            };

                            $method = $o['payment_method'] ?? 'cash';
                            $methodText = match ($method) {
                                'vnpay' => 'VNPay',
                                'momo' => 'MoMo',
                                'bank_transfer' => 'VietQR / CK',
                                'installment' => 'Trả góp',
                                default => 'COD (Tiền mặt)',
                            };

                            $nameInitial = mb_strtoupper(mb_substr($o['customer_name'] ?? 'K', 0, 1));
                        ?>
                            <tr>
                                <td class="ps-4">
                                    <span class="fw-bold font-monospace text-warning px-2 py-0.5 rounded-pill bg-white bg-opacity-10 border border-white border-opacity-10 small">
                                        <?= e($o['order_code'] ?? ('#' . $o['id'])) ?>
                                    </span>
                                    <?php if (!empty($o['is_preorder'])): ?>
                                        <br><span class="badge bg-danger rounded-pill px-1 py-0 mt-1" style="font-size: 9px;">PRE-ORDER</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-refract-pill" style="width: 34px; height: 34px; font-size: 12px;">
                                            <?= $nameInitial ?>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-white mb-0"><?= e($o['customer_name']) ?></div>
                                            <div class="small text-white-50" style="font-size: 11px;">
                                                <?= e($o['phone']) ?> · <?= e($o['email']) ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-white-50 small">
                                        <i class="bi bi-clock-history me-1 text-secondary"></i>
                                        <span><?= date('d/m/Y H:i', strtotime($o['created_at'])) ?></span>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-bold text-danger fs-6"><?= number_format($o['total_amount'], 0, ',', '.') ?> đ</span>
                                </td>
                                <td>
                                    <span class="badge bg-white bg-opacity-10 text-light border border-white border-opacity-10 rounded-pill px-2.5 py-1 small fw-normal">
                                        <?= $methodText ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?php if ($paySt === 'paid'): ?>
                                        <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 rounded-pill px-2.5 py-1 small fw-semibold">
                                            <i class="bi bi-check-circle-fill me-1"></i> Đã thanh toán
                                        </span>
                                    <?php elseif ($paySt === 'refunded'): ?>
                                        <span class="badge bg-info bg-opacity-25 text-info border border-info border-opacity-25 rounded-pill px-2.5 py-1 small fw-semibold">
                                            <i class="bi bi-arrow-counterclockwise me-1"></i> Đã hoàn tiền
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-warning bg-opacity-25 text-warning border border-warning border-opacity-25 rounded-pill px-2.5 py-1 small fw-semibold">
                                            <i class="bi bi-hourglass-split me-1"></i> Chưa thanh toán
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge <?= $statusBadgeClass ?> rounded-pill px-2.5 py-1 small fw-semibold">
                                        <?= $statusText ?>
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="/admin/orders/<?= $o['id'] ?>" class="btn btn-outline-secondary btn-sm px-3 d-inline-flex align-items-center gap-1.5" title="Chi tiết đơn">
                                        <i class="bi bi-eye-fill"></i> Chi tiết
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
