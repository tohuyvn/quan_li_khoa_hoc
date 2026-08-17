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

<style>
.admin-page-header {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    border-radius: 16px;
    padding: 24px 28px;
    color: #fff;
    margin-bottom: 24px;
    box-shadow: 0 10px 25px rgba(15, 23, 42, 0.12);
    position: relative;
    overflow: hidden;
}
.admin-page-header::after {
    content: '';
    position: absolute;
    right: -40px;
    top: -40px;
    width: 180px;
    height: 180px;
    background: rgba(239, 68, 68, 0.15);
    border-radius: 50%;
    filter: blur(30px);
}
.admin-stat-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 18px 20px;
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.03);
    display: flex;
    align-items: center;
    gap: 16px;
}
.admin-stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 24px rgba(15, 23, 42, 0.08);
    border-color: #cbd5e1;
}
.admin-stat-icon {
    width: 52px;
    height: 52px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    flex-shrink: 0;
}
.admin-stat-icon.blue { background: rgba(37, 99, 235, 0.1); color: #2563eb; }
.admin-stat-icon.yellow { background: rgba(234, 179, 8, 0.1); color: #ca8a04; }
.admin-stat-icon.purple { background: rgba(147, 51, 234, 0.1); color: #9333ea; }
.admin-stat-icon.green { background: rgba(16, 185, 129, 0.1); color: #10b981; }

.admin-stat-number {
    font-size: 1.5rem;
    font-weight: 800;
    line-height: 1.1;
    color: #0f172a;
    letter-spacing: -0.02em;
}
.admin-stat-label {
    font-size: 12.5px;
    color: #64748b;
    font-weight: 600;
    margin-top: 2px;
}

.table-custom-card {
    background: #ffffff;
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04);
    overflow: hidden;
}
.table-custom-card thead th {
    background: #f8fafc;
    color: #475569;
    font-size: 12.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    padding: 14px 16px;
    border-bottom: 2px solid #e2e8f0;
}
.order-row-item {
    transition: background 0.15s ease;
}
.order-row-item:hover {
    background-color: #f1f5f9 !important;
}

.customer-avatar-badge {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    background: linear-gradient(135deg, #0f172a 0%, #334155 100%);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 14px;
    flex-shrink: 0;
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

.pay-badge {
    font-size: 11px;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 6px;
}
.pay-badge.momo { background: #fee2e2; color: #d97706; }
.pay-badge.vnpay { background: #e0f2fe; color: #0284c7; }
.pay-badge.bank_transfer { background: #dcfce7; color: #166534; }
.pay-badge.cash { background: #f1f5f9; color: #475569; }
</style>

<!-- Header Banner -->
<div class="admin-page-header d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h2 class="h3 fw-bold mb-0 text-white"><i class="bi bi-cart-check-fill me-2 text-danger"></i>Quản Lý Đơn Hàng</h2>
            <span class="badge bg-danger rounded-pill px-3 py-1 text-uppercase" style="font-size: 10px; letter-spacing: 0.05em;">Order Processing</span>
        </div>
        <p class="mb-0 text-slate-300 small" style="color: #cbd5e1;">Theo dõi đơn hàng real-time, xác nhận vận chuyển, kiểm soát thanh toán & xuất dữ liệu báo cáo.</p>
    </div>
    <div>
        <a href="<?= $exportUrl ?>" class="btn btn-success btn-lg px-4 py-2 rounded-3 fw-bold shadow-sm d-inline-flex align-items-center gap-2" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: none;">
            <i class="bi bi-file-earmark-spreadsheet-fill fs-5"></i>
            <span>Xuất File CSV</span>
        </a>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="admin-stat-card">
            <div class="admin-stat-icon blue"><i class="bi bi-receipt"></i></div>
            <div>
                <div class="admin-stat-number"><?= number_format($totalOrdersCount) ?></div>
                <div class="admin-stat-label">Tổng Số Đơn Hàng</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="admin-stat-card">
            <div class="admin-stat-icon yellow"><i class="bi bi-hourglass-split"></i></div>
            <div>
                <div class="admin-stat-number"><?= number_format($pendingCount) ?></div>
                <div class="admin-stat-label">Chờ Xử Lý</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="admin-stat-card">
            <div class="admin-stat-icon purple"><i class="bi bi-truck"></i></div>
            <div>
                <div class="admin-stat-number"><?= number_format($shippingCount) ?></div>
                <div class="admin-stat-label">Đang Giao Hàng</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="admin-stat-card">
            <div class="admin-stat-icon green"><i class="bi bi-currency-dollar"></i></div>
            <div>
                <div class="admin-stat-number" style="font-size: 1.25rem;"><?= number_format($totalRevenueSum, 0, ',', '.') ?> đ</div>
                <div class="admin-stat-label">Doanh Thu Đơn Xong</div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Toolbar -->
<div class="bg-white p-3 rounded-4 border mb-4 shadow-sm">
    <form method="get" action="/admin/orders" class="row g-3">
        <div class="col-md-2">
            <label class="form-label small fw-bold text-muted mb-1"><i class="bi bi-search me-1"></i>Tìm kiếm</label>
            <input type="text" name="q" class="form-control form-control-sm" placeholder="Mã đơn, KH, SĐT..." value="<?= e($search ?? '') ?>">
        </div>
        <div class="col-md-2">
            <label class="form-label small fw-bold text-muted mb-1"><i class="bi bi-calendar3 me-1"></i>Thời gian (Kỳ)</label>
            <select name="period" class="form-select form-select-sm fw-bold border-primary text-primary">
                <option value="">Tất cả thời gian</option>
                <option value="day" <?= ($periodFilter ?? '') === 'day' ? 'selected' : '' ?>>📅 Hôm nay (Ngày)</option>
                <option value="week" <?= ($periodFilter ?? '') === 'week' ? 'selected' : '' ?>>🗓️ Tuần này</option>
                <option value="month" <?= ($periodFilter ?? '') === 'month' ? 'selected' : '' ?>>📊 Tháng này</option>
                <option value="quarter" <?= ($periodFilter ?? '') === 'quarter' ? 'selected' : '' ?>>📈 Quý này</option>
                <option value="year" <?= ($periodFilter ?? '') === 'year' ? 'selected' : '' ?>>🏆 Năm nay</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label small fw-bold text-muted mb-1"><i class="bi bi-funnel me-1"></i>Trạng thái đơn</label>
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
        <div class="col-md-2">
            <label class="form-label small fw-bold text-muted mb-1"><i class="bi bi-credit-card me-1"></i>Cổng thanh toán</label>
            <select name="payment_method" class="form-select form-select-sm">
                <option value="">Tất cả</option>
                <option value="cash" <?= ($paymentMethodFilter ?? '') === 'cash' ? 'selected' : '' ?>>COD (Tiền mặt)</option>
                <option value="bank_transfer" <?= ($paymentMethodFilter ?? '') === 'bank_transfer' ? 'selected' : '' ?>>VietQR / CK</option>
                <option value="vnpay" <?= ($paymentMethodFilter ?? '') === 'vnpay' ? 'selected' : '' ?>>VNPay</option>
                <option value="momo" <?= ($paymentMethodFilter ?? '') === 'momo' ? 'selected' : '' ?>>Ví MoMo</option>
                <option value="installment" <?= ($paymentMethodFilter ?? '') === 'installment' ? 'selected' : '' ?>>Trả góp</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label small fw-bold text-muted mb-1"><i class="bi bi-shield-check me-1"></i>Trạng thái TT</label>
            <select name="payment_status" class="form-select form-select-sm">
                <option value="">Tất cả</option>
                <option value="pending" <?= ($paymentStatusFilter ?? '') === 'pending' ? 'selected' : '' ?>>Chưa thanh toán</option>
                <option value="paid" <?= ($paymentStatusFilter ?? '') === 'paid' ? 'selected' : '' ?>>Đã thanh toán</option>
                <option value="refunded" <?= ($paymentStatusFilter ?? '') === 'refunded' ? 'selected' : '' ?>>Đã hoàn tiền</option>
                <option value="failed" <?= ($paymentStatusFilter ?? '') === 'failed' ? 'selected' : '' ?>>Thất bại</option>
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2 align-self-end">
            <button type="submit" class="btn btn-primary btn-sm flex-grow-1 fw-bold"><i class="bi bi-filter"></i> Lọc</button>
            <a href="/admin/orders" class="btn btn-outline-secondary btn-sm"><i class="bi bi-x-circle"></i> Đặt lại</a>
        </div>
    </form>
</div>

<!-- Table Card -->
<div class="table-custom-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th width="110" class="text-center">Mã Đơn</th>
                    <th>Khách Hàng</th>
                    <th>Thời Gian</th>
                    <th>Tổng Tiền</th>
                    <th>Phương Thức</th>
                    <th class="text-center">Thanh Toán</th>
                    <th class="text-center">Trạng Thái Đơn</th>
                    <th width="100" class="text-center">Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orders)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <div class="text-muted">
                                <i class="bi bi-cart-x fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                Không tìm thấy đơn hàng nào phù hợp với bộ lọc
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
                        <tr class="order-row-item">
                            <td class="text-center">
                                <span class="fw-bold text-primary font-monospace"><?= e($o['order_code'] ?? ('#' . $o['id'])) ?></span>
                                <?php if (!empty($o['is_preorder'])): ?>
                                    <br><span class="badge bg-danger rounded-1 px-1 py-0" style="font-size: 9px;">PRE-ORDER</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="customer-avatar-badge"><?= $nameInitial ?></div>
                                    <div>
                                        <div class="fw-bold text-dark mb-0"><?= e($o['customer_name']) ?></div>
                                        <div class="small text-muted" style="font-size: 11.5px;"><?= e($o['phone']) ?> · <?= e($o['email']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="small text-muted">
                                <i class="bi bi-clock me-1"></i><?= date('d/m/Y H:i', strtotime($o['created_at'])) ?>
                            </td>
                            <td>
                                <span class="fw-bold text-danger fs-6"><?= number_format($o['total_amount'], 0, ',', '.') ?> đ</span>
                            </td>
                            <td>
                                <span class="pay-badge <?= $method ?>"><?= $methodText ?></span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-<?= $paySt === 'paid' ? 'success' : ($paySt === 'failed' ? 'danger' : 'warning text-dark') ?> rounded-pill px-2 py-1" style="font-size: 11px;">
                                    <?= $payStatusText ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge-status-pill <?= $st ?>">
                                    <span class="spinner-grow spinner-grow-sm p-0" style="width:6px;height:6px;" role="status"></span>
                                    <?= $statusText ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="/admin/orders/<?= $o['id'] ?>" class="btn btn-sm btn-dark px-3 rounded-2 fw-bold" style="font-size: 12px;">
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
