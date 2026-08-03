<?php
$stats = $stats ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h2 class="mb-0">Quản trị thanh toán</h2>
    <span class="text-muted">Cập nhật: <?= date('d/m/Y H:i') ?></span>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="admin-stat-card admin-stat-success">
            <div class="admin-stat-label">Tổng đã thanh toán</div>
            <div class="admin-stat-value"><?= formatMoney($stats['total_paid'] ?? 0) ?></div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="admin-stat-card admin-stat-primary">
            <div class="admin-stat-label">Đơn đã thanh toán</div>
            <div class="admin-stat-value"><?= (int) ($stats['paid_count'] ?? 0) ?></div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="admin-stat-card admin-stat-warning">
            <div class="admin-stat-label">Đơn chờ thanh toán</div>
            <div class="admin-stat-value"><?= (int) ($stats['pending_count'] ?? 0) ?></div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="admin-stat-card admin-stat-info">
            <div class="admin-stat-label">Giá trị chờ xử lý</div>
            <div class="admin-stat-value admin-stat-value-sm"><?= formatMoney($stats['total_pending'] ?? 0) ?></div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="admin-chart-card">
            <h5 class="mb-3">Doanh thu 7 ngày gần nhất</h5>
            <canvas id="revenueChart" height="120"></canvas>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="admin-chart-card">
            <h5 class="mb-3">Theo phương thức TT</h5>
            <canvas id="methodChart" height="200"></canvas>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="admin-chart-card">
            <h5 class="mb-3">Số đơn VNPay theo ngân hàng</h5>
            <canvas id="bankChart" height="160"></canvas>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="admin-chart-card">
            <h5 class="mb-3">Tóm tắt phương thức</h5>
            <table class="table table-sm mb-0">
                <thead>
                    <tr>
                        <th>Phương thức</th>
                        <th>Số đơn</th>
                        <th>Doanh thu</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>💳 VNPay</td>
                        <td><?= (int) ($stats['by_method_count']['vnpay'] ?? 0) ?></td>
                        <td><?= formatMoney($stats['by_method_amount']['vnpay'] ?? 0) ?></td>
                    </tr>
                    <tr>
                        <td>💵 COD</td>
                        <td><?= (int) ($stats['by_method_count']['cash'] ?? 0) ?></td>
                        <td><?= formatMoney($stats['by_method_amount']['cash'] ?? 0) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<h4 class="mb-3">Tất cả đơn hàng — danh sách sản phẩm gộp theo mã đơn (order_id)</h4>
<div class="table-responsive mb-4">
    <table class="table table-striped table-bordered align-middle">
        <thead class="table-dark">
            <tr>
                <th width="80">order_id</th>
                <th>Khách hàng</th>
                <th>Thanh toán</th>
                <th width="120">Tổng tiền</th>
                <th width="80">SL SP</th>
                <th>Danh sách sản phẩm (gộp 1 đơn)</th>
                <th width="80">Chi tiết</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($orderSummaries)): ?>
                <tr><td colspan="7" class="text-center text-muted">Chưa có đơn hàng</td></tr>
            <?php else: ?>
                <?php foreach ($orderSummaries as $row): ?>
                    <tr>
                        <td><strong>#<?= (int) $row['order_id'] ?></strong></td>
                        <td><?= e($row['customer_name'] ?? '') ?></td>
                        <td>
                            <?= match ($row['payment_method'] ?? '') {
                                'vnpay' => 'VNPay',
                                'momo' => 'MoMo',
                                'bank_transfer' => 'VietQR',
                                'installment' => 'Trả góp',
                                default => 'COD',
                            } ?>
                            · <?= ($row['payment_status'] ?? '') === 'paid' ? '✅' : '⏳' ?>
                        </td>
                        <td><?= formatMoney($row['total_amount'] ?? 0) ?></td>
                        <td><?= (int) ($row['total_quantity'] ?? 0) ?></td>
                        <td class="small"><?= e($row['product_list'] ?? '—') ?></td>
                        <td>
                            <a href="/admin/payments/<?= (int) $row['order_id'] ?>" class="btn btn-sm btn-outline-primary">Xem</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<h4 class="mb-3">Đơn hàng đã thanh toán</h4>
<div class="table-responsive">
    <table class="table table-striped table-bordered align-middle">
        <thead class="table-dark">
            <tr>
                <th width="70">Mã ĐH</th>
                <th>Khách hàng</th>
                <th>Email</th>
                <th>Phương thức</th>
                <th>Ngân hàng</th>
                <th>Số tiền</th>
                <th>Thời gian</th>
                <th width="100">Chi tiết</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($paidOrders)): ?>
                <tr><td colspan="8" class="text-center text-muted">Chưa có đơn hàng đã thanh toán</td></tr>
            <?php else: ?>
                <?php foreach ($paidOrders as $order): ?>
                    <tr>
                        <td>#<?= $order['id'] ?></td>
                        <td><?= e($order['customer_name'] ?? '') ?></td>
                        <td><?= e($order['email'] ?? '') ?></td>
                        <td>
                            <?php if (($order['payment_method'] ?? '') === 'vnpay'): ?>
                                <span class="badge bg-primary">VNPay</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">COD</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?= ($order['payment_method'] ?? '') === 'vnpay'
                                ? e(vnpayBankName($order['vnpay_bank_code'] ?? '—'))
                                : '—' ?>
                        </td>
                        <td><strong><?= formatMoney($order['total_amount'] ?? 0) ?></strong></td>
                        <td><?= e($order['created_at'] ?? '') ?></td>
                        <td>
                            <a href="/admin/payments/<?= $order['id'] ?>" class="btn btn-sm btn-outline-primary">Xem</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
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
                backgroundColor: 'rgba(215, 0, 24, 0.75)',
                borderColor: '#d70018',
                borderWidth: 1,
                borderRadius: 6
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
                backgroundColor: ['#005baa', '#6c757d']
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
                    backgroundColor: 'rgba(0, 91, 170, 0.75)',
                    borderColor: '#005baa',
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
            '<p class="text-muted mb-0">Chưa có dữ liệu VNPay theo ngân hàng</p>';
        document.getElementById('bankChart').remove();
    }
})();
</script>
