<?php
$stats = $stats ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h2 class="h3 fw-bold mb-1 text-dark"><i class="bi bi-credit-card-2-front text-primary me-2"></i>Quản Trị Thanh Toán & Doanh Thu</h2>
        <p class="text-muted small mb-0">Theo dõi dòng tiền, doanh thu theo ngày và trạng thái giao dịch thanh toán.</p>
    </div>
    <span class="badge bg-light text-dark border px-3 py-2 rounded-pill fw-semibold"><i class="bi bi-clock-history me-1"></i> Cập nhật: <?= date('d/m/Y H:i') ?></span>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="content-card p-3 border-start border-4 border-success shadow-sm">
            <div class="text-muted small fw-bold text-uppercase mb-1" style="font-size: 11px;">Tổng Đã Thanh Toán</div>
            <div class="fs-4 fw-extrabold text-success"><?= formatMoney($stats['total_paid'] ?? 0) ?></div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="content-card p-3 border-start border-4 border-primary shadow-sm">
            <div class="text-muted small fw-bold text-uppercase mb-1" style="font-size: 11px;">Đơn Đã Thanh Toán</div>
            <div class="fs-4 fw-extrabold text-primary"><?= number_format((int) ($stats['paid_count'] ?? 0)) ?> đơn</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="content-card p-3 border-start border-4 border-warning shadow-sm">
            <div class="text-muted small fw-bold text-uppercase mb-1" style="font-size: 11px;">Đơn Chờ Thanh Toán</div>
            <div class="fs-4 fw-extrabold text-warning-emphasis"><?= number_format((int) ($stats['pending_count'] ?? 0)) ?> đơn</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="content-card p-3 border-start border-4 border-info shadow-sm">
            <div class="text-muted small fw-bold text-uppercase mb-1" style="font-size: 11px;">Giá Trị Chờ Xử Lý</div>
            <div class="fs-5 fw-bold text-info"><?= formatMoney($stats['total_pending'] ?? 0) ?></div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="content-card shadow-sm">
            <h5 class="fw-bold mb-3 text-dark d-flex align-items-center gap-2" style="font-size: 15px;">
                <i class="bi bi-graph-up-arrow text-danger"></i> Doanh Thu 7 Ngày Gần Nhất
            </h5>
            <canvas id="revenueChart" height="120"></canvas>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="content-card shadow-sm">
            <h5 class="fw-bold mb-3 text-dark d-flex align-items-center gap-2" style="font-size: 15px;">
                <i class="bi bi-pie-chart text-primary"></i> Phương Thức Thanh Toán
            </h5>
            <canvas id="methodChart" height="200"></canvas>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="content-card shadow-sm h-100">
            <h5 class="fw-bold mb-3 text-dark" style="font-size: 15px;">Số Đơn VNPay Theo Ngân Hàng</h5>
            <canvas id="bankChart" height="160"></canvas>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="content-card shadow-sm h-100 p-0 overflow-hidden">
            <div class="p-3 bg-light border-bottom">
                <h5 class="fw-bold mb-0 text-dark" style="font-size: 15px;">Tóm Tắt Doanh Thu Phương Thức</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Phương Thức</th>
                            <th>Số Đơn</th>
                            <th class="text-end pe-3">Doanh Thu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="fw-bold text-primary">💳 VNPay / VNPAY-QR</td>
                            <td><span class="badge bg-primary-subtle text-primary fw-bold"><?= (int) ($stats['by_method_count']['vnpay'] ?? 0) ?></span></td>
                            <td class="text-end pe-3 fw-bold text-dark"><?= formatMoney($stats['by_method_amount']['vnpay'] ?? 0) ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-dark">💵 Thanh Toán COD (Tiền mặt)</td>
                            <td><span class="badge bg-secondary-subtle text-secondary fw-bold"><?= (int) ($stats['by_method_count']['cash'] ?? 0) ?></span></td>
                            <td class="text-end pe-3 fw-bold text-dark"><?= formatMoney($stats['by_method_amount']['cash'] ?? 0) ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Order Summary Table -->
<h4 class="h5 fw-bold mb-3 text-dark"><i class="bi bi-receipt me-1 text-primary"></i>Tất Cả Đơn Hàng & Sản Phẩm Gộp</h4>
<div class="content-card p-0 overflow-hidden mb-4 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th width="90" class="ps-3">Order ID</th>
                    <th>Khách Hàng</th>
                    <th>Phương Thức</th>
                    <th width="130">Tổng Tiền</th>
                    <th width="70" class="text-center">SL SP</th>
                    <th>Danh Sách Sản Phẩm</th>
                    <th width="80" class="text-end pe-3">Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orderSummaries)): ?>
                    <tr><td colspan="7" class="text-center py-4 text-muted">Chưa có đơn hàng</td></tr>
                <?php else: ?>
                    <?php foreach ($orderSummaries as $row): ?>
                        <tr>
                            <td class="ps-3"><strong class="font-monospace text-dark">#<?= (int) $row['order_id'] ?></strong></td>
                            <td class="fw-bold text-dark"><?= e($row['customer_name'] ?? '') ?></td>
                            <td>
                                <span class="badge bg-light text-dark border px-2 py-1">
                                    <?= match ($row['payment_method'] ?? '') {
                                        'vnpay' => '💳 VNPay',
                                        'momo' => '📱 MoMo',
                                        'bank_transfer' => '🏦 VietQR',
                                        'installment' => '🤝 Trả góp',
                                        default => '💵 COD',
                                    } ?>
                                </span>
                                <?php if (($row['payment_status'] ?? '') === 'paid'): ?>
                                    <span class="badge bg-success-subtle text-success ms-1"><i class="bi bi-check-circle-fill"></i> Đã TT</span>
                                <?php else: ?>
                                    <span class="badge bg-warning-subtle text-warning ms-1"><i class="bi bi-clock"></i> Chờ TT</span>
                                <?php endif; ?>
                            </td>
                            <td class="fw-bold text-danger"><?= formatMoney($row['total_amount'] ?? 0) ?></td>
                            <td class="text-center"><span class="badge bg-secondary rounded-pill"><?= (int) ($row['total_quantity'] ?? 0) ?></span></td>
                            <td class="small text-muted"><span class="text-truncate d-inline-block" style="max-width: 320px;"><?= e($row['product_list'] ?? '—') ?></span></td>
                            <td class="text-end pe-3">
                                <a href="/admin/payments/<?= (int) $row['order_id'] ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1">Xem</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function () {
    const dayLabels = <?= json_encode($stats['by_day_labels'] ?? [], JSON_UNESCAPED_UNICODE) ?>;
    const dayValues = <?= json_encode($stats['by_day_values'] ?? []) ?>;
    const methodLabels = ['VNPay', 'COD'];
    const methodValues = [
        <?= (int) ($stats['by_method_amount']['vnpay'] ?? 0) ?>,
        <?= (int) ($stats['by_method_amount']['cash'] ?? 0) ?>
    ];
    const bankLabels = <?= json_encode($stats['by_bank_labels'] ?? [], JSON_UNESCAPED_UNICODE) ?>;
    const bankValues = <?= json_encode($stats['by_bank_values'] ?? []) ?>;

    new Chart(document.getElementById('revenueChart'), {
        type: 'bar',
        data: {
            labels: dayLabels,
            datasets: [{
                label: 'Doanh thu (đ)',
                data: dayValues,
                backgroundColor: 'rgba(239, 68, 68, 0.85)',
                borderColor: '#ef4444',
                borderWidth: 1,
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function (value) {
                            return new Intl.NumberFormat('vi-VN').format(value) + 'đ';
                        }
                    }
                }
            }
        }
    });

    new Chart(document.getElementById('methodChart'), {
        type: 'doughnut',
        data: {
            labels: methodLabels,
            datasets: [{
                data: methodValues,
                backgroundColor: ['#3b82f6', '#64748b']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    if (bankLabels.length > 0) {
        new Chart(document.getElementById('bankChart'), {
            type: 'bar',
            data: {
                labels: bankLabels,
                datasets: [{
                    label: 'Số đơn',
                    data: bankValues,
                    backgroundColor: 'rgba(59, 130, 246, 0.85)',
                    borderColor: '#3b82f6',
                    borderWidth: 1,
                    borderRadius: 6
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { x: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });
    } else {
        document.getElementById('bankChart').parentElement.innerHTML +=
            '<p class="text-muted mb-0 small text-center py-4">Chưa có dữ liệu VNPay theo ngân hàng</p>';
        document.getElementById('bankChart').remove();
    }
})();
</script>
