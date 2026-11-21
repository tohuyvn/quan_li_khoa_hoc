<?php
$title = 'Chi tiết đơn hàng ' . ($order['order_code'] ?? ('#' . $order['id']));
$currentStatus = $order['status'] ?? 'pending';
$shouldTrackShipment = $currentStatus === 'shipping' && !empty($order['shipping_lat']) && !empty($order['shipping_lng']);

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

<div class="container-fluid py-2">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="/orders" class="text-white-50 text-decoration-none small hover-underline">
                    <i class="bi bi-arrow-left me-1"></i> Đơn hàng của tôi
                </a>
                <span class="text-white-50 small">/</span>
                <span class="badge bg-warning bg-opacity-25 text-warning border border-warning border-opacity-25 rounded-pill px-3 py-1 font-monospace">
                    <?= e($order['order_code'] ?? ('#' . $order['id'])) ?>
                </span>
            </div>
            <h1 class="h3 fw-bold text-white mb-1">Chi Tiết Đơn Hàng #<?= e($order['order_code'] ?? $order['id']) ?></h1>
            <p class="text-muted mb-0 small">Ngày tạo: <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?> • Trạng thái: <strong class="text-warning"><?= strtoupper($order['status'] ?? 'pending') ?></strong></p>
        </div>
        <div class="d-flex gap-2">
            <a href="/orders" class="btn btn-outline-secondary btn-sm rounded-pill px-3 d-flex align-items-center gap-2">
                <i class="bi bi-list-ul"></i> Danh sách đơn
            </a>
            <?php if (!empty($order['id'])): ?>
                <a href="/orders/<?= (int)$order['id'] ?>/invoice" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill px-3 d-flex align-items-center gap-2">
                    <i class="bi bi-printer"></i> In hóa đơn
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Order Timeline Progress (Refract Glass Style) -->
    <div class="refract-card p-4 mb-4">
        <h6 class="fw-bold text-white mb-3 d-flex align-items-center gap-2">
            <i class="bi bi-truck text-warning"></i> Tiến Trình Xử Lý Đơn Hàng
        </h6>

        <?php if ($isCancelled): ?>
            <div class="alert alert-danger py-2.5 text-center small fw-bold mb-0 rounded-3 bg-danger bg-opacity-25 text-danger border-0">
                <i class="bi bi-x-circle me-1"></i> Đơn hàng này đã bị hủy
            </div>
        <?php else: ?>
            <div class="row g-2 text-center my-2">
                <?php
                $activeCount = array_search($currentStatus, $steps);
                if ($activeCount === false) $activeCount = -1;
                ?>
                <?php foreach ($steps as $index => $step): 
                    $isActive = $index <= $activeCount;
                    $isCurrent = $index === $activeCount;
                ?>
                    <div class="col">
                        <div class="p-2.5 rounded-3 border transition-all <?= $isActive ? 'bg-primary bg-opacity-25 border-primary border-opacity-50 text-white' : 'bg-white bg-opacity-5 border-white border-opacity-10 text-white-50' ?>">
                            <div class="small fw-bold mb-1">
                                <?php if ($isActive): ?>
                                    <i class="bi bi-check-circle-fill text-success me-1"></i>
                                <?php else: ?>
                                    <span class="badge rounded-circle bg-white bg-opacity-10 text-white-50 px-1.5 py-0.5 me-1" style="font-size: 10px;"><?= $index + 1 ?></span>
                                <?php endif; ?>
                                <?= $stepLabels[$step] ?>
                            </div>
                            <?php if ($isCurrent): ?>
                                <span class="badge bg-warning text-dark px-2 py-0.5 rounded-pill" style="font-size: 10px;">Hiện tại</span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="row g-4">
        <!-- Left Column: Products List & Breakdown -->
        <div class="col-lg-8">
            <div class="refract-card p-0 overflow-hidden mb-4">
                <div class="p-4 border-bottom border-white border-opacity-10 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-white d-flex align-items-center gap-2">
                        <i class="bi bi-box-seam text-warning"></i> Danh Sách Mặt Hàng (<?= count($items) ?> mục)
                    </h5>
                    <span class="badge bg-secondary bg-opacity-25 text-light px-3 py-1 rounded-pill small">
                        Phương thức: <?= strtoupper(e($order['payment_method'] ?? 'COD')) ?>
                    </span>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Sản Phẩm / Khóa Học</th>
                                <th class="text-center">Số Lượng</th>
                                <th class="text-end d-none d-md-table-cell">Đơn Giá</th>
                                <th class="text-end pe-4">Thành Tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                                <?php
                                $pid = (int) ($item['product_id'] ?? 0);
                                $lineUnits = $unitsByProduct[$pid] ?? [];
                                ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="rounded-3 border border-secondary border-opacity-25 p-1 flex-shrink-0" style="width: 50px; height: 50px; background: rgba(255,255,255,0.05);">
                                                <img src="<?= e(imageUrl($item['product_image'] ?? 'logo.webp', 'thumb')) ?>"
                                                     alt="<?= e($item['product_name']) ?>"
                                                     style="width: 100%; height: 100%; object-fit: cover;"
                                                     class="rounded-2"
                                                     onerror="this.onerror=null;this.src='/image/logo.webp';">
                                            </div>
                                            <div>
                                                <div class="fw-bold text-white mb-0"><?= e($item['product_name']) ?></div>
                                                <?php if (!empty($item['course_id'])): ?>
                                                    <span class="badge bg-success bg-opacity-25 text-success small rounded-pill px-2 py-0.5">Khóa học trực tuyến</span>
                                                <?php endif; ?>
                                                <?php if (!empty($lineUnits)): ?>
                                                    <div class="small text-muted mt-1">
                                                        IMEI / Serial:
                                                        <?php foreach ($lineUnits as $unit): ?>
                                                            <code class="bg-dark text-info px-1.5 py-0.5 border border-secondary rounded me-1 font-monospace"><?= e($unit['imei']) ?></code>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center fw-bold text-white">x<?= (int)$item['quantity'] ?></td>
                                    <td class="text-end text-white-50 d-none d-md-table-cell small"><?= formatMoney($item['price']) ?></td>
                                    <td class="text-end pe-4 fw-bold text-warning"><?= formatMoney($item['price'] * $item['quantity']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-end ps-4 text-white-50 d-none d-md-table-cell">Tạm tính:</td>
                                <td colspan="2" class="text-end ps-4 text-white-50 d-md-none">Tạm tính:</td>
                                <td class="text-end pe-4 text-white fw-semibold"><?= formatMoney($order['total_amount'] + ($order['discount_amount'] ?? 0)) ?></td>
                            </tr>
                            <?php if (!empty($order['discount_amount'])): ?>
                                <tr>
                                    <td colspan="3" class="text-end ps-4 text-success d-none d-md-table-cell">Ưu đãi giảm giá:</td>
                                    <td colspan="2" class="text-end ps-4 text-success d-md-none">Giảm giá:</td>
                                    <td class="text-end pe-4 text-success fw-bold">-<?= formatMoney($order['discount_amount']) ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if (!empty($order['shipping_fee'])): ?>
                                <tr>
                                    <td colspan="3" class="text-end ps-4 text-white-50 d-none d-md-table-cell">Phí giao hàng:</td>
                                    <td colspan="2" class="text-end ps-4 text-white-50 d-md-none">Phí ship:</td>
                                    <td class="text-end pe-4 text-white"><?= formatMoney($order['shipping_fee']) ?></td>
                                </tr>
                            <?php endif; ?>
                            <tr class="border-top border-white border-opacity-15">
                                <td colspan="3" class="text-end ps-4 text-white fw-bold fs-6 d-none d-md-table-cell">Tổng thanh toán:</td>
                                <td colspan="2" class="text-end ps-4 text-white fw-bold fs-6 d-md-none">Tổng cộng:</td>
                                <td class="text-end pe-4"><span class="text-danger fw-bold fs-4"><?= formatMoney($order['total_amount']) ?></span></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Column: Shipping & Payment Meta -->
        <div class="col-lg-4">
            <!-- Customer & Delivery Info Card -->
            <div class="refract-card p-4 mb-4">
                <h5 class="fw-bold mb-3 text-white d-flex align-items-center gap-2">
                    <i class="bi bi-geo-alt-fill text-danger"></i> Thông Tin Giao Nhận
                </h5>
                <div class="small d-flex flex-column gap-2 text-white-50">
                    <div>
                        <span class="text-muted d-block">Họ và tên người nhận:</span>
                        <strong class="text-white fs-6"><?= e($order['customer_name']) ?></strong>
                    </div>
                    <div>
                        <span class="text-muted d-block">Số điện thoại liên hệ:</span>
                        <strong class="text-white"><?= e($order['phone'] ?? 'N/A') ?></strong>
                    </div>
                    <div>
                        <span class="text-muted d-block">Địa chỉ giao hàng:</span>
                        <span class="text-white"><?= e($order['shipping_address'] ?? 'Trực tuyến qua tài khoản Hades LMS') ?></span>
                    </div>
                    <div class="pt-2 border-top border-white border-opacity-10 d-flex justify-content-between align-items-center">
                        <span class="text-muted">Phương thức:</span>
                        <span class="badge bg-secondary bg-opacity-25 text-white border border-secondary border-opacity-25 px-2.5 py-1">
                            <?= strtoupper(e($order['payment_method'] ?? 'COD')) ?>
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Trạng thái thanh toán:</span>
                        <?php if (($order['payment_status'] ?? '') === 'paid'): ?>
                            <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 px-2.5 py-1">Đã thanh toán</span>
                        <?php else: ?>
                            <span class="badge bg-warning bg-opacity-25 text-warning border border-warning border-opacity-25 px-2.5 py-1">Chờ thanh toán</span>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($order['status'] === 'pending' || $order['status'] === 'confirmed'): ?>
                    <form action="/orders/<?= $order['id'] ?>/cancel" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này?');" class="mt-4 pt-3 border-top border-white border-opacity-10">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-outline-danger btn-sm w-100 rounded-pill py-2 fw-semibold">
                            <i class="bi bi-x-circle me-1"></i> Hủy Đơn Hàng Này
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
