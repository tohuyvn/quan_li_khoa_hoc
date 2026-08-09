<?php
$title = 'Đơn hàng của tôi';
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

<style>
.orders-page-header {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    border-radius: 18px;
    padding: 24px 28px;
    color: #fff;
    margin-bottom: 24px;
    box-shadow: 0 10px 25px rgba(15, 23, 42, 0.12);
    position: relative;
    overflow: hidden;
}
.orders-page-header::after {
    content: '';
    position: absolute;
    right: -40px;
    top: -40px;
    width: 180px;
    height: 180px;
    background: rgba(37, 99, 235, 0.15);
    border-radius: 50%;
    filter: blur(30px);
}

.order-stat-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 16px 20px;
    transition: all 0.2s ease;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.03);
    display: flex;
    align-items: center;
    gap: 14px;
}
.order-stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
    border-color: #cbd5e1;
}
.order-stat-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
}
.order-stat-icon.blue { background: rgba(37, 99, 235, 0.1); color: #2563eb; }
.order-stat-icon.yellow { background: rgba(234, 179, 8, 0.1); color: #ca8a04; }
.order-stat-icon.purple { background: rgba(147, 51, 234, 0.1); color: #9333ea; }
.order-stat-icon.green { background: rgba(16, 185, 129, 0.1); color: #10b981; }

.status-pill-filter {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    border-radius: 50px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    color: #475569;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none !important;
    transition: all 0.2s ease;
}
.status-pill-filter:hover {
    background: #f8fafc;
    color: #0f172a;
    border-color: #cbd5e1;
}
.status-pill-filter.active {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    color: #ffffff;
    border-color: #0f172a;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.15);
}

.orders-table-card {
    background: #ffffff;
    border-radius: 18px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04);
    overflow: hidden;
}
.orders-table-card thead th {
    background: #f8fafc;
    color: #475569;
    font-size: 12.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    padding: 14px 16px;
    border-bottom: 2px solid #e2e8f0;
}
.client-order-row {
    transition: background 0.15s ease;
}
.client-order-row:hover {
    background-color: #f8fafc !important;
}

.badge-status-pill {
    padding: 5px 12px;
    border-radius: 50px;
    font-size: 11.5px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}
.badge-status-pill.pending { background: rgba(234, 179, 8, 0.15); color: #a16207; border: 1px solid rgba(234, 179, 8, 0.3); }
.badge-status-pill.confirmed { background: rgba(14, 165, 233, 0.15); color: #0369a1; border: 1px solid rgba(14, 165, 233, 0.3); }
.badge-status-pill.packing { background: rgba(99, 102, 241, 0.15); color: #4338ca; border: 1px solid rgba(99, 102, 241, 0.3); }
.badge-status-pill.shipping { background: rgba(168, 85, 247, 0.15); color: #6b21a8; border: 1px solid rgba(168, 85, 247, 0.3); }
.badge-status-pill.completed { background: rgba(16, 185, 129, 0.15); color: #047857; border: 1px solid rgba(16, 185, 129, 0.3); }
.badge-status-pill.cancelled { background: rgba(239, 68, 68, 0.15); color: #b91c1c; border: 1px solid rgba(239, 68, 68, 0.3); }
</style>

<!-- Header Banner -->
<div class="orders-page-header d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
        <h2 class="h3 fw-bold mb-1 text-white"><i class="bi bi-box-seam-fill me-2 text-primary"></i>Lịch Sử Đơn Hàng</h2>
        <p class="mb-0 text-slate-300 small" style="color: #cbd5e1;">Theo dõi hành trình vận chuyển, kiểm tra chi tiết hóa đơn & yêu cầu bảo hành/hoàn tiền.</p>
    </div>
    <div>
        <a href="/" class="btn btn-outline-light btn-sm rounded-3 px-3 py-2 fw-semibold">
            <i class="bi bi-shop me-1"></i> Mua sắm thêm
        </a>
    </div>
</div>

<!-- Stat Cards Row -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="order-stat-card">
            <div class="order-stat-icon blue"><i class="bi bi-receipt-cutoff"></i></div>
            <div>
                <div class="fw-bold fs-5 text-dark mb-0"><?= number_format($totalOrdersCount) ?></div>
                <div class="small text-muted" style="font-size: 11.5px;">Tổng số đơn</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="order-stat-card">
            <div class="order-stat-icon yellow"><i class="bi bi-hourglass-split"></i></div>
            <div>
                <div class="fw-bold fs-5 text-dark mb-0"><?= number_format($pendingCount) ?></div>
                <div class="small text-muted" style="font-size: 11.5px;">Chờ xử lý</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="order-stat-card">
            <div class="order-stat-icon purple"><i class="bi bi-truck"></i></div>
            <div>
                <div class="fw-bold fs-5 text-dark mb-0"><?= number_format($shippingCount) ?></div>
                <div class="small text-muted" style="font-size: 11.5px;">Đang giao hàng</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="order-stat-card">
            <div class="order-stat-icon green"><i class="bi bi-check-circle-fill"></i></div>
            <div>
                <div class="fw-bold fs-5 text-dark mb-0"><?= number_format($completedCount) ?></div>
                <div class="small text-muted" style="font-size: 11.5px;">Đã hoàn thành</div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Pills & Search Bar -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3 bg-white p-3 rounded-4 border shadow-sm">
    <!-- Horizontal Filter Pills -->
    <div class="d-flex gap-2 overflow-auto pb-1 flex-grow-1" style="white-space: nowrap;">
        <?php foreach ($statusOptions as $key => $option):
            $url = '/orders?' . http_build_query(array_filter([
                'status' => $key,
                'q' => $currentSearch
            ]));
            $isActive = $currentStatus === $key;
        ?>
            <a href="<?= $url ?>" class="status-pill-filter <?= $isActive ? 'active' : '' ?>">
                <i class="bi <?= e($option['icon']) ?>"></i>
                <span><?= e($option['label']) ?></span>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Search Form -->
    <form action="/orders" method="GET" style="min-width: 240px;">
        <?php if (!empty($filters['status'])): ?>
            <input type="hidden" name="status" value="<?= e($filters['status']) ?>">
        <?php endif; ?>
        <div class="input-group input-group-sm">
            <input type="text" name="q" class="form-control border-end-0" placeholder="Mã đơn hàng, tên..." value="<?= e($filters['search'] ?? '') ?>">
            <button type="submit" class="btn btn-dark px-3"><i class="bi bi-search"></i></button>
        </div>
    </form>
</div>

<!-- Table / Cards List -->
<div class="orders-table-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Sản Phẩm</th>
                    <th width="120" class="text-center">Mã Đơn</th>
                    <th>Ngày Đặt</th>
                    <th>Tổng Tiền</th>
                    <th class="text-center">Trạng Thái Đơn</th>
                    <th class="text-center">Thanh Toán</th>
                    <th width="120" class="text-center">Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orders)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="text-muted">
                                <i class="bi bi-box-seam fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                Bạn chưa có đơn hàng nào phù hợp bộ lọc
                            </div>
                            <a href="/" class="btn btn-danger btn-sm rounded-3 px-4 py-2 mt-2 fw-bold" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); border: none;">
                                Khám Phá Sản Phẩm Ngay
                            </a>
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
                    ?>
                        <tr class="client-order-row">
                            <td style="min-width:220px">
                                <div class="d-flex align-items-center gap-3">
                                    <?php
                                    $previews = $order['item_previews'] ?? [];
                                    $shown = array_slice($previews, 0, 3);
                                    ?>
                                    <div class="d-flex">
                                        <?php foreach ($shown as $i => $pv): ?>
                                            <img src="<?= e(imageUrl($pv['product_image'] ?? '', 'thumb')) ?>"
                                                 alt="<?= e($pv['product_name'] ?? '') ?>"
                                                 title="<?= e($pv['product_name'] ?? '') ?>"
                                                 class="rounded-3 border bg-white shadow-sm"
                                                 style="width:42px;height:42px;object-fit:cover;margin-left:<?= $i > 0 ? '-12px' : '0' ?>;position:relative;z-index:<?= 3 - $i ?>;"
                                                 loading="lazy">
                                        <?php endforeach; ?>
                                    </div>
                                    <div class="small">
                                        <?php if (!empty($previews[0]['product_name'])): ?>
                                            <div class="fw-bold text-dark text-truncate" style="max-width:160px;"><?= e($previews[0]['product_name']) ?></div>
                                        <?php endif; ?>
                                        <?php if (($order['item_count'] ?? 0) > 1): ?>
                                            <span class="text-muted" style="font-size: 11px;">+<?= (int) $order['item_count'] - 1 ?> sản phẩm khác</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="fw-bold text-primary font-monospace"><?= e($order['order_code'] ?? ('#' . $order['id'])) ?></span>
                            </td>
                            <td class="small text-muted">
                                <i class="bi bi-clock me-1"></i><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?>
                            </td>
                            <td>
                                <span class="fw-bold text-danger fs-6"><?= formatMoney($order['total_amount']) ?></span>
                            </td>
                            <td class="text-center">
                                <span class="badge-status-pill <?= $st ?>">
                                    <span class="spinner-grow spinner-grow-sm p-0" style="width:6px;height:6px;" role="status"></span>
                                    <?= $statusText ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <?php if (($order['payment_status'] ?? '') === 'paid'): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1" style="font-size: 11px;">
                                        <i class="bi bi-check-circle-fill me-1"></i>Đã thanh toán
                                    </span>
                                <?php elseif (($order['payment_status'] ?? '') === 'refunded'): ?>
                                    <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2.5 py-1" style="font-size: 11px;">
                                        <i class="bi bi-arrow-counterclockwise me-1"></i>Đã hoàn tiền
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2.5 py-1" style="font-size: 11px;">
                                        Chưa thanh toán
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <a href="/orders/<?= $order['id'] ?>" class="btn btn-sm btn-dark px-3 rounded-2 fw-bold" style="font-size: 12px;">
                                    <i class="bi bi-eye-fill me-1"></i>Chi tiết
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
