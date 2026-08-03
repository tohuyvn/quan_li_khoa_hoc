<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0" style="letter-spacing: -0.02em;">📊 Bảng điều khiển admin</h2>
    <span class="text-muted small">Thời gian thực: <?= date('d/m/Y H:i') ?></span>
</div>

<!-- Widgets Row -->
<div class="row g-3 mb-4">
    <!-- Today's Revenue -->
    <div class="col-md-6 col-lg-3">
        <div class="card border-0 h-100 shadow-sm text-white" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); border-radius: 12px;">
            <div class="card-body p-4 d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-white-50 text-uppercase small fw-bold mb-2">Doanh thu hôm nay</h6>
                    <h4 class="fw-bold mb-0"><?= number_format($todayRevenue, 0, ',', '.') ?> đ</h4>
                </div>
                <div style="font-size: 2.2rem; opacity: 0.35;"><i class="bi bi-currency-dollar"></i></div>
            </div>
        </div>
    </div>

    <!-- Weekly Revenue -->
    <div class="col-md-6 col-lg-3">
        <div class="card border-0 h-100 shadow-sm text-white" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); border-radius: 12px;">
            <div class="card-body p-4 d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-white-50 text-uppercase small fw-bold mb-2">Doanh thu tuần này</h6>
                    <h4 class="fw-bold mb-0"><?= number_format($weeklyRevenue, 0, ',', '.') ?> đ</h4>
                </div>
                <div style="font-size: 2.2rem; opacity: 0.35;"><i class="bi bi-calendar-check"></i></div>
            </div>
        </div>
    </div>

    <!-- Orders Pending -->
    <div class="col-md-6 col-lg-3">
        <div class="card border-0 h-100 shadow-sm text-white" style="background: linear-gradient(135deg, #ff9966 0%, #ff5e62 100%); border-radius: 12px;">
            <div class="card-body p-4 d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-white-50 text-uppercase small fw-bold mb-2">Đơn chờ xử lý</h6>
                    <h4 class="fw-bold mb-0"><?= $pendingOrders ?></h4>
                    <?php if ($pendingOrders > 0): ?>
                        <span class="badge bg-white text-danger mt-2 small fw-bold" style="font-size: 10px;">Cần xử lý gấp</span>
                    <?php endif; ?>
                </div>
                <div style="font-size: 2.2rem; opacity: 0.35;"><i class="bi bi-cart-dash-fill"></i></div>
            </div>
        </div>
    </div>

    <!-- Low Stock Alert -->
    <div class="col-md-6 col-lg-3">
        <div class="card border-0 h-100 shadow-sm text-white" style="background: linear-gradient(135deg, #f12711 0%, #f5af19 100%); border-radius: 12px;">
            <div class="card-body p-4 d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-white-50 text-uppercase small fw-bold mb-2">Sản phẩm sắp hết</h6>
                    <h4 class="fw-bold mb-0"><?= $lowStock ?></h4>
                    <?php if ($lowStock > 0): ?>
                        <a href="/admin/inventory?stock_status=low_stock" class="badge bg-white text-dark mt-2 small fw-bold text-decoration-none" style="font-size: 10px;">Xem tồn kho</a>
                    <?php endif; ?>
                </div>
                <div style="font-size: 2.2rem; opacity: 0.35;"><i class="bi bi-exclamation-triangle"></i></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Chart Column -->
    <div class="col-lg-8">
        <div class="card border rounded-3 p-4 bg-white h-100 shadow-sm">
            <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-graph-up-arrow text-danger"></i> Doanh thu & Đơn hàng (7 ngày qua)
            </h5>
            <div style="position: relative; height: 320px; width: 100%;">
                <canvas id="salesChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Quick Navigation & Unread Messages -->
    <div class="col-lg-4">
        <div class="card border rounded-3 p-4 bg-white h-100 shadow-sm">
            <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-lightning-charge-fill text-warning"></i> Thao tác nhanh
            </h5>
            
            <div class="d-grid gap-2 mb-4">
                <a href="/admin/products/create" class="btn btn-outline-dark text-start py-2"><i class="bi bi-plus-circle me-2"></i> Thêm sản phẩm mới</a>
                <a href="/admin/orders" class="btn btn-outline-dark text-start py-2"><i class="bi bi-receipt me-2"></i> Xem danh sách đơn hàng</a>
                <a href="/admin/promotions/create" class="btn btn-outline-dark text-start py-2"><i class="bi bi-tag-fill me-2"></i> Tạo mã coupon mới</a>
            </div>

            <hr class="my-3">

            <h6 class="fw-bold d-flex justify-content-between align-items-center mb-3">
                <span>💬 Tin nhắn hỗ trợ</span>
                <?php if ($unreadMessages > 0): ?>
                    <span class="badge bg-danger rounded-pill"><?= $unreadMessages ?> mới</span>
                <?php endif; ?>
            </h6>

            <?php if ($unreadMessages > 0): ?>
                <div class="alert alert-warning border-0 p-3 mb-0" style="border-radius: 10px; background-color: #fff9db;">
                    <p class="small mb-2 text-dark"><i class="bi bi-info-circle-fill me-2 text-warning"></i>Bạn đang có <strong><?= $unreadMessages ?></strong> tin nhắn từ khách hàng chưa trả lời.</p>
                    <a href="/admin/messages" class="btn btn-sm btn-dark w-100 fw-bold">Đi tới phòng Chat</a>
                </div>
            <?php else: ?>
                <div class="text-center py-4 text-muted small bg-light rounded-3">
                    <i class="bi bi-check-circle-fill text-success d-block mb-2" style="font-size: 1.5rem;"></i>
                    Mọi thắc mắc đã được giải đáp!
                </div>
            <?php endif; ?>
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
                    backgroundColor: 'rgba(215, 0, 24, 0.75)',
                    borderColor: 'rgba(215, 0, 24, 1)',
                    borderWidth: 1,
                    yAxisID: 'yRevenue',
                    order: 2
                },
                {
                    label: 'Số đơn hàng',
                    data: counts,
                    type: 'line',
                    borderColor: '#2a5298',
                    backgroundColor: '#2a5298',
                    borderWidth: 2,
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
                        color: '#f0f0f0'
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
                        drawOnChartArea: false // prevent overlay grids
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
