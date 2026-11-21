<?php
$title = 'Đơn hàng & Giao dịch - Hades Academy';
$statusOptions = $statusOptions ?? App\Controllers\OrderController::statusOptions();
$currentStatus = $filters['status'] ?? '';
$currentSearch = $filters['search'] ?? '';

$totalOrdersCount = count($orders ?? []);
$pendingCount = 0;
$shippingCount = 0;
$completedCount = 0;

if (!empty($orders)) {
    foreach ($orders as $o) {
        $st = $o['status'] ?? '';
        if ($st === 'pending') $pendingCount++;
        elseif ($st === 'shipping' || $st === 'packing') $shippingCount++;
        elseif ($st === 'completed') $completedCount++;
    }
}
?>

<div class="container-fluid py-2">
    <!-- Breadcrumb & Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-warning bg-opacity-25 text-warning border border-warning border-opacity-25 rounded-pill px-3 py-1">
                    <i class="bi bi-receipt-cutoff me-1"></i> Quản lý giao dịch
                </span>
                <span class="text-white-50 small">• <?= number_format($totalOrdersCount) ?> đơn hàng</span>
            </div>
            <h1 class="h3 fw-bold text-white mb-1">Lịch Sử Đơn Hàng Của Tôi</h1>
            <p class="text-muted mb-0 small">Theo dõi chi tiết các đơn đăng ký khóa học, học phí và sản phẩm công nghệ</p>
        </div>
        <div class="d-flex gap-2">
            <a href="/my-courses" class="btn btn-outline-primary btn-sm rounded-pill px-3 d-flex align-items-center gap-2">
                <i class="bi bi-journal-bookmark-fill text-warning"></i> Khóa học của tôi
            </a>
            <a href="/courses" class="btn-refract-action btn-sm">
                <i class="bi bi-compass me-1"></i> Khám phá khóa học
            </a>
        </div>
    </div>

    <!-- KPI Metric Cards (Refract Glass Style) -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="refract-card p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="small text-muted fw-semibold">Tổng số đơn</span>
                    <span class="badge rounded-pill bg-primary bg-opacity-25 text-primary small px-2 py-0.5">All</span>
                </div>
                <div class="fs-4 fw-bold text-white"><?= number_format($totalOrdersCount) ?> <span class="fs-6 fw-normal text-muted">đơn</span></div>
                <div class="small text-white-50 mt-1"><i class="bi bi-receipt me-1 text-primary"></i>Toàn bộ lịch sử đặt hàng</div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="refract-card p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="small text-muted fw-semibold">Chờ thanh toán / duyệt</span>
                    <span class="badge rounded-pill bg-warning bg-opacity-25 text-warning small px-2 py-0.5">Pending</span>
                </div>
                <div class="fs-4 fw-bold text-warning"><?= number_format($pendingCount) ?> <span class="fs-6 fw-normal text-muted">đơn</span></div>
                <div class="small text-white-50 mt-1"><i class="bi bi-hourglass-split me-1 text-warning"></i>Cần xử lý hoàn tất</div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="refract-card p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="small text-muted fw-semibold">Đang vận chuyển</span>
                    <span class="badge rounded-pill bg-info bg-opacity-25 text-info small px-2 py-0.5">Shipping</span>
                </div>
                <div class="fs-4 fw-bold text-info"><?= number_format($shippingCount) ?> <span class="fs-6 fw-normal text-muted">đơn</span></div>
                <div class="small text-white-50 mt-1"><i class="bi bi-truck me-1 text-info"></i>Đang trên đường giao</div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="refract-card p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="small text-muted fw-semibold">Đã hoàn thành</span>
                    <span class="badge rounded-pill bg-success bg-opacity-25 text-success small px-2 py-0.5">Completed</span>
                </div>
                <div class="fs-4 fw-bold text-success"><?= number_format($completedCount) ?> <span class="fs-6 fw-normal text-muted">đơn</span></div>
                <div class="small text-white-50 mt-1"><i class="bi bi-check-circle-fill me-1 text-success"></i>Kích hoạt & nhận thành công</div>
            </div>
        </div>
    </div>

    <!-- Filter Pills & Search Bar (Frosted Glass Pill) -->
    <div class="refract-card p-3 mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <!-- Filter Pills -->
            <div class="d-flex gap-2 overflow-auto pb-1 flex-grow-1" style="white-space: nowrap;">
                <?php foreach ($statusOptions as $key => $option):
                    $url = '/orders?' . http_build_query(array_filter([
                        'status' => $key,
                        'q' => $currentSearch
                    ]));
                    $isActive = $currentStatus === $key;
                ?>
                    <a href="<?= $url ?>" class="nav-pill-link <?= $isActive ? 'active' : '' ?>">
                        <i class="bi <?= e($option['icon']) ?> me-1"></i>
                        <span><?= e($option['label']) ?></span>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Search Bar -->
            <form action="/orders" method="GET" class="refract-search-pill" style="width: 260px;">
                <?php if (!empty($filters['status'])): ?>
                    <input type="hidden" name="status" value="<?= e($filters['status']) ?>">
                <?php endif; ?>
                <i class="bi bi-search text-muted me-2"></i>
                <input type="text" name="q" class="border-0 bg-transparent text-white" style="outline: none; font-size: 0.85rem; width: 100%;" placeholder="Tìm theo mã đơn, sản phẩm..." value="<?= e($filters['search'] ?? '') ?>">
            </form>
        </div>
    </div>

    <!-- Glass Orders Table -->
    <div class="refract-card p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Sản Phẩm & Khóa Học</th>
                        <th class="text-center" width="140">Mã Đơn</th>
                        <th>Ngày Đặt</th>
                        <th>Tổng Tiền</th>
                        <th class="text-center">Trạng Thái Đơn</th>
                        <th class="text-center">Thanh Toán</th>
                        <th class="text-end pe-4" width="130">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted py-3">
                                    <i class="bi bi-box-seam fs-1 d-block mb-3 text-secondary opacity-50"></i>
                                    <h5 class="fw-bold text-white">Chưa có đơn hàng nào</h5>
                                    <p class="small text-muted mb-3">Bạn chưa có đơn đặt hàng nào phù hợp với bộ lọc hiện tại.</p>
                                    <a href="/courses" class="btn-refract-action btn-sm d-inline-flex align-items-center gap-2">
                                        <i class="bi bi-mortarboard-fill"></i> Khám phá khóa học ngay
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($orders as $order):
                            $st = $order['status'] ?? 'pending';
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
                        ?>
                            <tr>
                                <td class="ps-4" style="min-width: 260px;">
                                    <div class="d-flex align-items-center gap-3">
                                        <?php
                                        $previews = $order['item_previews'] ?? [];
                                        $shown = array_slice($previews, 0, 3);
                                        ?>
                                        <div class="d-flex flex-shrink-0">
                                            <?php foreach ($shown as $i => $pv): ?>
                                                <img src="<?= e(imageUrl($pv['product_image'] ?? '', 'thumb')) ?>"
                                                     alt="<?= e($pv['product_name'] ?? '') ?>"
                                                     title="<?= e($pv['product_name'] ?? '') ?>"
                                                     class="rounded-3 border border-secondary border-opacity-25 shadow-sm"
                                                     style="width:44px; height:44px; object-fit:cover; margin-left:<?= $i > 0 ? '-14px' : '0' ?>; position:relative; z-index:<?= 3 - $i ?>; background: rgba(255,255,255,0.05);"
                                                     loading="lazy">
                                            <?php endforeach; ?>
                                        </div>
                                        <div class="small overflow-hidden">
                                            <?php if (!empty($previews[0]['product_name'])): ?>
                                                <div class="fw-bold text-white text-truncate mb-0" style="max-width: 220px;" title="<?= e($previews[0]['product_name']) ?>">
                                                    <?= e($previews[0]['product_name']) ?>
                                                </div>
                                            <?php else: ?>
                                                <div class="fw-bold text-white">Đơn hàng #<?= e($order['id']) ?></div>
                                            <?php endif; ?>
                                            <?php if (($order['item_count'] ?? 0) > 1): ?>
                                                <span class="text-white-50 small" style="font-size: 11px;">+<?= (int) $order['item_count'] - 1 ?> sản phẩm khác</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold font-monospace text-warning px-2.5 py-1 rounded-pill bg-white bg-opacity-10 border border-white border-opacity-10 small">
                                        <?= e($order['order_code'] ?? ('#' . $order['id'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="text-white-50 small">
                                        <i class="bi bi-clock-history me-1 text-secondary"></i>
                                        <span><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></span>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-bold text-danger fs-6"><?= formatMoney($order['total_amount']) ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge <?= $statusBadgeClass ?> rounded-pill px-3 py-1.5 fw-semibold small">
                                        <?= $statusText ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?php if (($order['payment_status'] ?? '') === 'paid'): ?>
                                        <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 rounded-pill px-2.5 py-1 small fw-semibold">
                                            <i class="bi bi-check-circle-fill me-1"></i> Đã thanh toán
                                        </span>
                                    <?php elseif (($order['payment_status'] ?? '') === 'refunded'): ?>
                                        <span class="badge bg-info bg-opacity-25 text-info border border-info border-opacity-25 rounded-pill px-2.5 py-1 small fw-semibold">
                                            <i class="bi bi-arrow-counterclockwise me-1"></i> Đã hoàn tiền
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-warning bg-opacity-25 text-warning border border-warning border-opacity-25 rounded-pill px-2.5 py-1 small fw-semibold">
                                            <i class="bi bi-hourglass-split me-1"></i> Chưa thanh toán
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="/orders/<?= $order['id'] ?>" class="btn btn-outline-secondary btn-sm px-3 d-inline-flex align-items-center gap-1.5" title="Xem chi tiết đơn hàng">
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
