<?php
$title = 'Đơn hàng của tôi';
$statusOptions = $statusOptions ?? App\Controllers\OrderController::statusOptions();
$currentStatus = $filters['status'] ?? '';
$currentSearch = $filters['search'] ?? '';
?>

<div class="row g-4">
    <!-- Left Sidebar Filter & Support -->
    <div class="col-lg-3 d-none d-lg-block">
        <div class="content-card p-3 sticky-top" style="top: 90px; z-index: 10;">
            <!-- Search form -->
            <form action="/orders" method="GET" class="mb-4">
                <?php if (!empty($filters['status'])): ?>
                    <input type="hidden" name="status" value="<?= e($filters['status']) ?>">
                <?php endif; ?>
                <h6 class="fw-bold mb-3"><i class="bi bi-search me-2"></i>Tìm kiếm đơn</h6>
                <div class="input-group input-group-sm">
                    <input type="text" name="q" class="form-control" placeholder="Mã đơn, tên, sđt..." value="<?= e($filters['search'] ?? '') ?>">
                    <button type="submit" class="btn btn-dark"><i class="bi bi-search"></i></button>
                </div>
            </form>

            <!-- Status filter -->
            <div class="mb-4">
                <h6 class="fw-bold mb-3"><i class="bi bi-funnel me-2"></i>Trạng thái</h6>
                <div class="list-group list-group-flush">
                    <?php
                    foreach ($statusOptions as $key => $option):
                        $url = '/orders?' . http_build_query(array_filter([
                            'status' => $key,
                            'q' => $currentSearch
                        ]));
                        $isActive = $currentStatus === $key;
                    ?>
                        <a href="<?= $url ?>" class="list-group-item list-group-item-action border-0 px-0 py-2 bg-transparent d-flex align-items-center justify-content-between <?= $isActive ? 'text-primary fw-bold' : 'text-muted' ?>">
                            <span><i class="bi <?= e($option['icon']) ?> me-2"></i><?= e($option['label']) ?></span>
                            <?php if ($isActive): ?>
                                <i class="bi bi-check-lg"></i>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Refund Support Contact Info -->
            <div class="pt-3 border-top">
                <h6 class="fw-bold mb-2 text-warning"><i class="bi bi-arrow-counterclockwise me-1"></i> Hỗ trợ hoàn tiền</h6>
                <p class="small text-muted mb-2" style="font-size: 11px;">Bạn cần hỗ trợ xử lý hoặc hoàn trả tiền đơn hàng?</p>
                <div class="small" style="font-size: 11px; line-height: 1.5;">
                    <p class="mb-1"><i class="bi bi-telephone-fill me-1 text-primary"></i> Hotline: 1800 1234</p>
                    <p class="mb-1"><i class="bi bi-envelope-fill me-1 text-primary"></i> Email: support@hades.vn</p>
                    <p class="mb-0 text-muted">Hoặc gửi chat trực tuyến để được xử lý ngay lập tức.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content: Orders Table -->
    <div class="col-lg-9 col-12">
        <div class="content-card">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <h4 class="fw-bold mb-0">Lịch sử đơn hàng</h4>
                <div class="d-flex gap-2">
                    <!-- Mobile Search Trigger / Form -->
                    <form action="/orders" method="GET" class="d-lg-none d-flex gap-1">
                        <input type="text" name="q" class="form-control form-control-sm" placeholder="Tìm đơn..." value="<?= e($filters['search'] ?? '') ?>">
                        <button type="submit" class="btn btn-sm btn-dark"><i class="bi bi-search"></i></button>
                    </form>
                    <a href="/" class="btn btn-outline-primary btn-sm"><i class="bi bi-arrow-left"></i> Tiếp tục mua sắm</a>
                </div>
            </div>

            <div class="d-lg-none mb-3">
                <div class="d-flex gap-2 overflow-auto pb-2">
                    <?php foreach ($statusOptions as $key => $option):
                        $url = '/orders?' . http_build_query(array_filter([
                            'status' => $key,
                            'q' => $currentSearch
                        ]));
                        $isActive = $currentStatus === $key;
                    ?>
                        <a href="<?= $url ?>" class="btn btn-sm text-nowrap <?= $isActive ? 'btn-dark' : 'btn-outline-secondary' ?>">
                            <i class="bi <?= e($option['icon']) ?> me-1"></i><?= e($option['label']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <?php if (empty($orders)): ?>
                <div class="text-center py-5">
                    <div style="font-size: 48px; opacity: 0.3;"><i class="bi bi-box"></i></div>
                    <p class="text-muted mt-3">Không tìm thấy đơn hàng nào phù hợp.</p>
                    <a href="/" class="btn btn-primary-hades mt-2" style="width: auto;">Khám phá sản phẩm</a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Mã đơn</th>
                                <th>Ngày đặt</th>
                                <th>Tổng tiền</th>
                                <th>Trạng thái</th>
                                <th>Thanh toán</th>
                                <th class="text-end">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td style="min-width:160px">
                                        <div class="d-flex align-items-center gap-2">
                                            <?php
                                            $previews = $order['item_previews'] ?? [];
                                            $shown = array_slice($previews, 0, 3);
                                            ?>
                                            <div class="d-flex">
                                                <?php foreach ($shown as $i => $pv): ?>
                                                    <img src="<?= e(imageUrl($pv['product_image'] ?? '', 'thumb')) ?>"
                                                         alt="<?= e($pv['product_name'] ?? '') ?>"
                                                         title="<?= e($pv['product_name'] ?? '') ?>"
                                                         class="rounded border bg-white"
                                                         style="width:36px;height:36px;object-fit:contain;margin-left:<?= $i > 0 ? '-8px' : '0' ?>;position:relative;z-index:<?= 3 - $i ?>;"
                                                         loading="lazy"
                                                         onerror="this.onerror=null;this.src='/image/logo.webp';">
                                                <?php endforeach; ?>
                                                <?php if (empty($shown)): ?>
                                                    <img src="/image/logo.webp" alt="" class="rounded border" style="width:36px;height:36px;object-fit:contain;">
                                                <?php endif; ?>
                                            </div>
                                            <div class="small text-muted" style="line-height:1.2;">
                                                <?php if (!empty($previews[0]['product_name'])): ?>
                                                    <div class="text-dark fw-semibold text-truncate" style="max-width:120px;"><?= e($previews[0]['product_name']) ?></div>
                                                <?php endif; ?>
                                                <?php if (($order['item_count'] ?? 0) > 1): ?>
                                                    <span>+<?= (int) $order['item_count'] - 1 ?> SP khác</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="fw-bold text-primary" style="font-family: monospace;"><?= e($order['order_code'] ?? ('#' . $order['id'])) ?></span></td>
                                    <td><span class="text-muted small"><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></span></td>
                                    <td><span class="fw-bold text-danger"><?= formatMoney($order['total_amount']) ?></span></td>
                                    <td>
                                        <?php
                                        $statusMap = [
                                            'pending' => ['label' => 'Chờ xử lý', 'class' => 'bg-warning text-dark'],
                                            'confirmed' => ['label' => 'Đã xác nhận', 'class' => 'bg-info text-white'],
                                            'packing' => ['label' => 'Đang đóng gói', 'class' => 'bg-dark'],
                                            'shipping' => ['label' => 'Đang giao', 'class' => 'bg-primary text-white'],
                                            'completed' => ['label' => 'Hoàn thành', 'class' => 'bg-success text-white'],
                                            'cancelled' => ['label' => 'Đã hủy', 'class' => 'bg-secondary'],
                                        ];
                                        $s = $statusMap[$order['status'] ?? 'pending'] ?? ['label' => $order['status'], 'class' => 'bg-secondary'];
                                        ?>
                                        <span class="badge <?= $s['class'] ?>"><?= $s['label'] ?></span>
                                    </td>
                                    <td>
                                        <?php if (($order['payment_status'] ?? '') === 'paid'): ?>
                                            <span class="text-success small fw-bold"><i class="bi bi-check-circle-fill"></i> Đã thanh toán</span>
                                        <?php elseif (($order['payment_status'] ?? '') === 'refunded'): ?>
                                            <span class="text-danger small fw-bold"><i class="bi bi-arrow-counterclockwise"></i> Đã hoàn tiền</span>
                                        <?php else: ?>
                                            <span class="text-muted small">Chưa thanh toán</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <a href="/orders/<?= $order['id'] ?>" class="btn btn-sm btn-outline-dark">Chi tiết</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
