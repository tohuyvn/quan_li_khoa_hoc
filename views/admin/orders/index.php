<?php
$queryString = http_build_query($_GET);
$exportUrl = "/admin/orders/export-csv" . ($queryString ? '?' . $queryString : '');
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0" style="letter-spacing: -0.02em;">🛒 Quản lý đơn hàng</h2>
    <a href="<?= $exportUrl ?>" class="btn btn-success fw-bold shadow-sm">
        <i class="bi bi-file-earmark-spreadsheet-fill me-1"></i> Xuất file CSV
    </a>
</div>

<div class="card p-4 mb-4 bg-white border rounded-3 shadow-sm">
    <form method="get" action="/admin/orders" class="row g-3">
        <!-- Search input -->
        <div class="col-md-4">
            <label class="form-label small fw-bold text-muted mb-1">Tìm kiếm</label>
            <input type="text" name="q" class="form-control form-control-sm" placeholder="ID đơn hàng, khách hàng, email..." value="<?= e($search ?? '') ?>">
        </div>

        <!-- Order status filter -->
        <div class="col-md-2">
            <label class="form-label small fw-bold text-muted mb-1">Trạng thái đơn</label>
            <select name="status" class="form-select form-select-sm">
                <option value="">Tất cả</option>
                <option value="pending" <?= ($statusFilter ?? '') === 'pending' ? 'selected' : '' ?>>Chờ xử lý</option>
                <option value="confirmed" <?= ($statusFilter ?? '') === 'confirmed' ? 'selected' : '' ?>>Đã xác nhận</option>
                <option value="packing" <?= ($statusFilter ?? '') === 'packing' ? 'selected' : '' ?>>Đang đóng gói</option>
                <option value="shipping" <?= ($statusFilter ?? '') === 'shipping' ? 'selected' : '' ?>>Đang giao hàng</option>
                <option value="completed" <?= ($statusFilter ?? '') === 'completed' ? 'selected' : '' ?>>Đã hoàn thành</option>
                <option value="cancelled" <?= ($statusFilter ?? '') === 'cancelled' ? 'selected' : '' ?>>Đã hủy</option>
            </select>
        </div>

        <!-- Payment method filter -->
        <div class="col-md-2">
            <label class="form-label small fw-bold text-muted mb-1">Cổng thanh toán</label>
            <select name="payment_method" class="form-select form-select-sm">
                <option value="">Tất cả</option>
                <option value="cash" <?= ($paymentMethodFilter ?? '') === 'cash' ? 'selected' : '' ?>>COD (Tiền mặt)</option>
                <option value="bank_transfer" <?= ($paymentMethodFilter ?? '') === 'bank_transfer' ? 'selected' : '' ?>>VietQR / CK</option>
                <option value="vnpay" <?= ($paymentMethodFilter ?? '') === 'vnpay' ? 'selected' : '' ?>>VNPay</option>
                <option value="momo" <?= ($paymentMethodFilter ?? '') === 'momo' ? 'selected' : '' ?>>Ví MoMo</option>
                <option value="installment" <?= ($paymentMethodFilter ?? '') === 'installment' ? 'selected' : '' ?>>Trả góp</option>
            </select>
        </div>

        <!-- Payment status filter -->
        <div class="col-md-2">
            <label class="form-label small fw-bold text-muted mb-1">Trạng thái TT</label>
            <select name="payment_status" class="form-select form-select-sm">
                <option value="">Tất cả</option>
                <option value="pending" <?= ($paymentStatusFilter ?? '') === 'pending' ? 'selected' : '' ?>>Chưa thanh toán</option>
                <option value="paid" <?= ($paymentStatusFilter ?? '') === 'paid' ? 'selected' : '' ?>>Đã thanh toán</option>
                <option value="refunded" <?= ($paymentStatusFilter ?? '') === 'refunded' ? 'selected' : '' ?>>Đã hoàn tiền</option>
                <option value="failed" <?= ($paymentStatusFilter ?? '') === 'failed' ? 'selected' : '' ?>>Thất bại</option>
            </select>
        </div>

        <!-- Date Range Filter -->
        <div class="col-md-2 col-sm-6">
            <label class="form-label small fw-bold text-muted mb-1">Từ ngày</label>
            <input type="date" name="date_from" class="form-control form-control-sm" value="<?= e($dateFromFilter ?? '') ?>">
        </div>
        <div class="col-md-2 col-sm-6">
            <label class="form-label small fw-bold text-muted mb-1">Đến ngày</label>
            <input type="date" name="date_to" class="form-control form-control-sm" value="<?= e($dateToFilter ?? '') ?>">
        </div>

        <!-- Action buttons -->
        <div class="col-md-2 align-self-end">
            <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold"><i class="bi bi-funnel"></i> Lọc kết quả</button>
        </div>
        <div class="col-md-2 align-self-end">
            <a href="/admin/orders" class="btn btn-outline-secondary btn-sm w-100"><i class="bi bi-arrow-counterclockwise"></i> Xóa lọc</a>
        </div>
    </form>
</div>

<div class="table-responsive">
    <table class="table table-hover table-striped border align-middle bg-white rounded-3 overflow-hidden shadow-sm" style="font-size: 13.5px;">
        <thead class="table-dark">
            <tr>
                <th width="80" class="text-center">Mã đơn</th>
                <th>Khách hàng</th>
                <th>Ngày đặt</th>
                <th>Tổng tiền</th>
                <th>Thanh toán</th>
                <th>Trạng thái TT</th>
                <th>Trạng thái đơn</th>
                <th width="100" class="text-center">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($orders)): ?>
                <tr><td colspan="8" class="text-center py-4 text-muted">Không tìm thấy đơn hàng nào phù hợp bộ lọc.</td></tr>
            <?php else: ?>
                <?php foreach ($orders as $o):
                    $statusClass = match ($o['status']) {
                        'confirmed' => 'info text-dark',
                        'packing' => 'dark',
                        'shipping' => 'primary',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'warning text-dark'
                    };
                    $statusText = match ($o['status']) {
                        'confirmed' => 'Đã xác nhận',
                        'packing' => 'Đang đóng gói',
                        'shipping' => 'Đang giao',
                        'completed' => 'Hoàn thành',
                        'cancelled' => 'Đã hủy',
                        default => 'Chờ xử lý'
                    };
                    
                    $payStatusClass = match ($o['payment_status']) {
                        'paid' => 'success',
                        'refunded' => 'info text-dark',
                        'failed' => 'danger',
                        default => 'warning text-dark'
                    };
                    $payStatusText = match ($o['payment_status']) {
                        'paid' => 'Đã thanh toán',
                        'refunded' => 'Đã hoàn tiền',
                        'failed' => 'Thất bại',
                        default => 'Chưa thanh toán'
                    };

                    $methodText = match ($o['payment_method'] ?? '') {
                        'vnpay' => 'VNPay',
                        'momo' => 'MoMo',
                        'bank_transfer' => 'VietQR / CK',
                        'installment' => 'Trả góp',
                        default => 'COD (Tiền mặt)',
                    };
                ?>
                    <tr>
                        <td class="text-center fw-bold text-muted" style="font-family: monospace;">
                            <?= e($o['order_code'] ?? ('#' . $o['id'])) ?>
                            <?php if (!empty($o['is_preorder'])): ?>
                                <br><span class="badge bg-danger" style="font-size: 9px; padding: 2px 4px;">PRE-ORDER</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong class="text-dark"><?= e($o['customer_name']) ?></strong>
                            <div class="small text-muted" style="font-size: 11px;"><?= e($o['email']) ?> | <?= e($o['phone']) ?></div>
                        </td>
                        <td><?= date('d/m/Y H:i', strtotime($o['created_at'])) ?></td>
                        <td class="fw-bold text-danger"><?= number_format($o['total_amount'], 0, ',', '.') ?> đ</td>
                        <td><span class="small fw-semibold"><?= $methodText ?></span></td>
                        <td>
                            <span class="badge bg-<?= $payStatusClass ?>"><?= $payStatusText ?></span>
                        </td>
                        <td>
                            <span class="badge bg-<?= $statusClass ?>"><?= $statusText ?></span>
                        </td>
                        <td class="text-center">
                            <a href="/admin/orders/<?= $o['id'] ?>" class="btn btn-sm btn-dark fw-bold">Chi tiết</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
