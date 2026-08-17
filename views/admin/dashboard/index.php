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
    background: rgba(37, 99, 235, 0.15);
    border-radius: 50%;
    filter: blur(30px);
}

.dashboard-stat-card {
    border: none;
    border-radius: 16px;
    padding: 22px;
    color: #fff;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    position: relative;
    overflow: hidden;
}
.dashboard-stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 14px 28px rgba(0, 0, 0, 0.15);
}
.dashboard-stat-card::after {
    content: '';
    position: absolute;
    right: -20px;
    bottom: -20px;
    width: 100px;
    height: 100px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
}

.dashboard-stat-card.blue { background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); }
.dashboard-stat-card.green { background: linear-gradient(135deg, #059669 0%, #10b981 100%); }
.dashboard-stat-card.orange { background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%); }
.dashboard-stat-card.red { background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%); }

.dashboard-stat-icon {
    font-size: 2.8rem;
    opacity: 0.25;
    position: absolute;
    right: 20px;
    top: 20px;
}

.quick-action-btn {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    color: #0f172a;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s ease;
}
.quick-action-btn:hover {
    background: #f8fafc;
    border-color: #2563eb;
    color: #2563eb;
    transform: translateX(3px);
}
.quick-action-icon {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    background: #eff6ff;
    color: #2563eb;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
}
</style>

<!-- Header Banner -->
<div class="admin-page-header d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h2 class="h3 fw-bold mb-0 text-white"><i class="bi bi-speedometer2 me-2 text-primary"></i>Bảng Điều Khiển Admin</h2>
            <span class="badge bg-danger rounded-pill px-3 py-1 text-uppercase" style="font-size: 10px; letter-spacing: 0.05em;">Overview Dashboard</span>
        </div>
        <p class="mb-0 text-slate-300 small" style="color: #cbd5e1;">Tổng quan doanh thu shop, phân tích thống kê theo Ngày / Tuần / Tháng / Quý / Năm & biểu đồ real-time.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <form method="get" action="/admin/dashboard" class="d-flex align-items-center gap-2 bg-dark p-2 rounded-3 border border-secondary shadow-sm">
            <label class="text-white small fw-bold mb-0 ms-1 d-none d-sm-inline"><i class="bi bi-funnel-fill text-warning me-1"></i>Quản lý theo:</label>
            <select name="period" class="form-select form-select-sm bg-white text-dark fw-bold border-0" onchange="this.form.submit()" style="min-width: 150px; cursor: pointer;">
                <option value="day" <?= ($selectedPeriod ?? 'week') === 'day' ? 'selected' : '' ?>>📅 Theo Ngày</option>
                <option value="week" <?= ($selectedPeriod ?? 'week') === 'week' ? 'selected' : '' ?>>🗓️ Theo Tuần</option>
                <option value="month" <?= ($selectedPeriod ?? 'week') === 'month' ? 'selected' : '' ?>>📊 Theo Tháng</option>
                <option value="quarter" <?= ($selectedPeriod ?? 'week') === 'quarter' ? 'selected' : '' ?>>📈 Theo Quý</option>
                <option value="year" <?= ($selectedPeriod ?? 'week') === 'year' ? 'selected' : '' ?>>🏆 Theo Năm</option>
            </select>
        </form>
        <span class="badge bg-dark border border-secondary px-3 py-2 text-white font-monospace d-none d-md-inline" style="font-size: 12px;">
            <i class="bi bi-clock-fill text-warning me-1"></i><?= date('d/m/Y H:i') ?>
        </span>
    </div>
</div>

<!-- Widgets Row -->
<div class="row g-3 mb-4">
    <!-- Selected Period Revenue Card -->
    <div class="col-md-6 col-lg-3">
        <div class="dashboard-stat-card blue">
            <div class="dashboard-stat-icon"><i class="bi bi-graph-up-arrow"></i></div>
            <h6 class="text-white-50 text-uppercase small fw-bold mb-2">Doanh Thu <?= e($periodLabel ?? 'Kỳ Chọn') ?></h6>
            <h3 class="fw-extrabold mb-0" style="font-weight: 800;"><?= number_format($selectedPeriodRevenue ?? 0, 0, ',', '.') ?> đ</h3>
            <span class="badge bg-white text-primary mt-2 small fw-bold" style="font-size: 10px;">⚡ Kỳ đang chọn</span>
        </div>
    </div>

    <!-- Today & Weekly Revenue summary card -->
    <div class="col-md-6 col-lg-3">
        <div class="dashboard-stat-card green">
            <div class="dashboard-stat-icon"><i class="bi bi-calendar-check"></i></div>
            <h6 class="text-white-50 text-uppercase small fw-bold mb-2">Hôm Nay & Tuần Này</h6>
            <h3 class="fw-extrabold mb-0" style="font-weight: 800; font-size: 1.35rem;"><?= number_format($todayRevenue ?? 0, 0, ',', '.') ?> đ</h3>
            <div class="mt-2 text-white-50 small fw-bold" style="font-size: 11px;">Tuần: <?= number_format($weeklyRevenue ?? 0, 0, ',', '.') ?> đ</div>
        </div>
    </div>

    <!-- Orders Pending -->
    <div class="col-md-6 col-lg-3">
        <div class="dashboard-stat-card orange">
            <div class="dashboard-stat-icon"><i class="bi bi-cart-dash-fill"></i></div>
            <h6 class="text-white-50 text-uppercase small fw-bold mb-2">Đơn Chờ Xử Lý</h6>
            <h3 class="fw-extrabold mb-0" style="font-weight: 800;"><?= number_format($pendingOrders) ?></h3>
            <?php if ($pendingOrders > 0): ?>
                <span class="badge bg-white text-danger mt-2 small fw-bold" style="font-size: 10px;">Cần xử lý gấp</span>
            <?php else: ?>
                <span class="badge bg-white text-dark mt-2 small fw-bold" style="font-size: 10px;">Đã xử lý hết</span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Monthly / Yearly / Low Stock -->
    <div class="col-md-6 col-lg-3">
        <div class="dashboard-stat-card red">
            <div class="dashboard-stat-icon"><i class="bi bi-cash-stack"></i></div>
            <h6 class="text-white-50 text-uppercase small fw-bold mb-2">Doanh Thu Tháng & Năm</h6>
            <h3 class="fw-extrabold mb-0" style="font-weight: 800; font-size: 1.25rem;"><?= number_format($monthlyRevenue ?? 0, 0, ',', '.') ?> đ</h3>
            <div class="mt-2 text-white-50 small fw-bold" style="font-size: 11px;">Năm <?= date('Y') ?>: <?= number_format($yearlyRevenue ?? 0, 0, ',', '.') ?> đ</div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Chart Column -->
    <div class="col-lg-8">
        <div class="bg-white border rounded-4 p-4 h-100 shadow-sm">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                <h5 class="fw-bold mb-0 d-flex align-items-center gap-2 text-dark">
                    <i class="bi bi-graph-up-arrow text-danger"></i> Biểu Đồ Doanh Thu & Đơn Hàng
                </h5>
                <form method="get" action="/admin/dashboard" class="d-flex align-items-center gap-2">
                    <span class="small text-muted fw-bold">Kỳ thống kê:</span>
                    <select name="period" class="form-select form-select-sm border-primary fw-bold" onchange="this.form.submit()" style="width: auto;">
                        <option value="day" <?= ($selectedPeriod ?? 'week') === 'day' ? 'selected' : '' ?>>📅 Theo Ngày (24h)</option>
                        <option value="week" <?= ($selectedPeriod ?? 'week') === 'week' ? 'selected' : '' ?>>🗓️ Theo Tuần (7 ngày)</option>
                        <option value="month" <?= ($selectedPeriod ?? 'week') === 'month' ? 'selected' : '' ?>>📊 Theo Tháng (Các ngày)</option>
                        <option value="quarter" <?= ($selectedPeriod ?? 'week') === 'quarter' ? 'selected' : '' ?>>📈 Theo Quý (Các tháng)</option>
                        <option value="year" <?= ($selectedPeriod ?? 'week') === 'year' ? 'selected' : '' ?>>🏆 Theo Năm (12 tháng)</option>
                    </select>
                </form>
            </div>
            <div class="alert alert-light border p-2 mb-3 rounded-3 small text-muted d-flex justify-content-between align-items-center">
                <span><i class="bi bi-info-circle text-primary me-1"></i>Đang hiển thị dữ liệu: <strong><?= e($periodLabel ?? '') ?></strong></span>
                <span class="badge bg-primary rounded-pill">Tổng doanh thu kỳ: <?= number_format($selectedPeriodRevenue ?? 0, 0, ',', '.') ?> đ</span>
            </div>
            <div style="position: relative; height: 320px; width: 100%;">
                <canvas id="salesChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Quick Navigation & Unread Messages -->
    <div class="col-lg-4">
        <div class="bg-white border rounded-4 p-4 h-100 shadow-sm d-flex flex-column justify-content-between">
            <div>
                <h5 class="fw-bold mb-3 d-flex align-items-center gap-2 text-dark">
                    <i class="bi bi-lightning-charge-fill text-warning"></i> Thao Tác Nhanh
                </h5>
                
                <div class="d-flex flex-column gap-2 mb-4">
                    <a href="/admin/products/create" class="quick-action-btn">
                        <div class="quick-action-icon"><i class="bi bi-plus-lg"></i></div>
                        <span>Thêm sản phẩm mới</span>
                    </a>
                    <a href="/admin/orders" class="quick-action-btn">
                        <div class="quick-action-icon" style="background: #fef3c7; color: #d97706;"><i class="bi bi-receipt"></i></div>
                        <span>Quản lý đơn hàng</span>
                    </a>
                    <a href="/admin/promotions/create" class="quick-action-btn">
                        <div class="quick-action-icon" style="background: #fce7f3; color: #db2777;"><i class="bi bi-tag-fill"></i></div>
                        <span>Tạo mã coupon mới</span>
                    </a>
                </div>
            </div>

            <div>
                <hr class="my-3">

                <h6 class="fw-bold d-flex justify-content-between align-items-center mb-3 text-dark">
                    <span>💬 Tin nhắn hỗ trợ</span>
                    <?php if ($unreadMessages > 0): ?>
                        <span class="badge bg-danger rounded-pill px-2 py-1"><?= $unreadMessages ?> mới</span>
                    <?php endif; ?>
                </h6>

                <?php if ($unreadMessages > 0): ?>
                    <div class="alert alert-warning border-0 p-3 mb-0 rounded-3" style="background-color: #fff9db;">
                        <p class="small mb-2 text-dark"><i class="bi bi-info-circle-fill me-2 text-warning"></i>Bạn đang có <strong><?= $unreadMessages ?></strong> tin nhắn chưa trả lời.</p>
                        <a href="/admin/messages" class="btn btn-sm btn-dark w-100 fw-bold">Vào phòng Chat ngay</a>
                    </div>
                <?php else: ?>
                    <div class="text-center py-3 text-muted small bg-light rounded-3">
                        <i class="bi bi-check-circle-fill text-success d-block mb-1 fs-4"></i>
                        Mọi thắc mắc đã được xử lý xong!
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('salesChart').getContext('2d');
    
    const dates = <?= json_encode($dates) ?>;
    const revenues = <?= json_encode($revenues) ?>;
    const counts = <?= json_encode($counts) ?>;
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: dates,
            datasets: [
                {
                    label: 'Doanh thu (đ)',
                    data: revenues,
                    backgroundColor: 'rgba(215, 0, 24, 0.85)',
                    borderColor: 'rgba(215, 0, 24, 1)',
                    borderWidth: 1,
                    borderRadius: 6,
                    yAxisID: 'yRevenue',
                    order: 2
                },
                {
                    label: 'Số đơn hàng',
                    data: counts,
                    type: 'line',
                    borderColor: '#2563eb',
                    backgroundColor: '#2563eb',
                    borderWidth: 3,
                    pointRadius: 4,
                    fill: false,
                    tension: 0.3,
                    yAxisID: 'yCount',
                    order: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                yRevenue: {
                    type: 'linear',
                    position: 'left',
                    grid: {
                        color: '#f1f5f9'
                    },
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString() + ' đ';
                        }
                    },
                    title: {
                        display: true,
                        text: 'Doanh thu (VNĐ)',
                        font: {
                            weight: 'bold'
                        }
                    }
                },
                yCount: {
                    type: 'linear',
                    position: 'right',
                    grid: {
                        drawOnChartArea: false
                    },
                    title: {
                        display: true,
                        text: 'Số đơn hàng',
                        font: {
                            weight: 'bold'
                        }
                    },
                    ticks: {
                        stepSize: 1,
                        precision: 0
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        boxWidth: 15,
                        font: {
                            weight: '600'
                        }
                    }
                }
            }
        }
    });
});
</script>
