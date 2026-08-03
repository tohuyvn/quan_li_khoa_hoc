<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
        <h3 class="fw-bold text-dark mb-0"><i class="bi bi-truck me-2 text-primary"></i>Khu Vực Shipper</h3>
        <span class="badge bg-primary px-3 py-2 text-white">Shipper: <?= e($user['name'] ?? 'N/A') ?></span>
    </div>

    <!-- Flash message -->
    <?php if ($msg = flash('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= e($msg) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if ($msg = flash('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= e($msg) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- 1. Active deliveries -->
    <div class="mb-5">
        <h4 class="fw-bold text-success mb-3"><i class="bi bi-geo-alt-fill me-2"></i>Đơn hàng bạn đang giao</h4>
        <?php
        $activeOrders = array_filter($orders, fn($o) => $o['status'] === 'shipping');
        ?>

        <?php if (empty($activeOrders)): ?>
            <div class="card card-body text-center py-4 bg-light text-muted border-0">
                <i class="bi bi-inbox-fill fs-1 mb-2"></i>
                Bạn chưa nhận đơn hàng nào hoặc tất cả các đơn hàng đã được giao thành công.
            </div>
        <?php else: ?>
            <div class="row g-3">
                <?php foreach ($activeOrders as $o): ?>
                    <div class="col-md-6">
                        <div class="card border-success shadow-sm h-100">
                            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                                <span class="fw-bold small"><?= e($o['order_code']) ?></span>
                                <span class="badge bg-light text-success fw-bold">ĐANG GIAO</span>
                            </div>
                            <div class="card-body">
                                <p class="mb-1 text-muted small"><i class="bi bi-person-circle me-1"></i> Khách hàng: <strong><?= e($o['customer_name'] ?: ($o['email'] ?? 'N/A')) ?></strong></p>
                                <p class="mb-1 text-muted small"><i class="bi bi-telephone-fill me-1"></i> SĐT: <strong><?= e($o['phone'] ?? 'N/A') ?></strong></p>
                                <p class="mb-1 text-muted small"><i class="bi bi-geo-alt-fill me-1"></i> Địa chỉ giao: <strong><?= e($o['shipping_address'] ?? 'N/A') ?></strong></p>
                                <p class="mb-0 text-muted small"><i class="bi bi-currency-dollar me-1"></i> Số tiền COD: <strong class="text-danger"><?= formatMoney((int)$o['total_amount']) ?></strong></p>
                            </div>
                            <div class="card-footer bg-white border-0 d-flex gap-2">
                                <a href="/shipper/orders/<?= (int)$o['id'] ?>/track" class="btn btn-primary btn-sm flex-fill fw-bold"><i class="bi bi-compass-fill me-1"></i> Mô phỏng & Định vị</a>
                <form action="/shipper/orders/<?= (int)$o['id'] ?>/complete" method="POST" enctype="multipart/form-data" class="flex-fill" onsubmit="return confirm('Xác nhận giao hàng thành công và thu tiền?')">
                                    <?= csrf_field() ?>
                                    <input type="file" name="delivery_proof" accept="image/*" class="form-control form-control-sm mb-1" required>
                                    <button type="submit" class="btn btn-success btn-sm w-100 fw-bold"><i class="bi bi-check-circle-fill me-1"></i> Giao thành công + ảnh</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- 2. Available deliveries -->
    <div>
        <h4 class="fw-bold text-dark mb-3"><i class="bi bi-hourglass-split me-2 text-warning"></i>Đơn hàng chưa ai nhận</h4>
        <?php
        $availableOrders = array_filter($orders, fn($o) => $o['status'] === 'packing');
        ?>

        <?php if (empty($availableOrders)): ?>
            <div class="card card-body text-center py-4 bg-light text-muted border-0">
                <i class="bi bi-emoji-smile fs-1 mb-2"></i>
                Hiện tại không có đơn hàng nào cần nhận giao.
            </div>
        <?php else: ?>
            <div class="row g-3">
                <?php foreach ($availableOrders as $o): ?>
                    <div class="col-md-6">
                        <div class="card shadow-sm h-100">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-dark small"><?= e($o['order_code']) ?></span>
                                <span class="badge bg-warning text-dark fw-bold">SẴN SÀNG GIAO</span>
                            </div>
                            <div class="card-body">
                                <p class="mb-1 text-muted small"><i class="bi bi-person-circle me-1"></i> Khách hàng: <strong><?= e($o['customer_name'] ?: ($o['email'] ?? 'N/A')) ?></strong></p>
                                <p class="mb-1 text-muted small"><i class="bi bi-telephone-fill me-1"></i> SĐT: <strong><?= e($o['phone'] ?? 'N/A') ?></strong></p>
                                <p class="mb-1 text-muted small"><i class="bi bi-geo-alt-fill me-1"></i> Địa chỉ giao: <strong><?= e($o['shipping_address'] ?? 'N/A') ?></strong></p>
                                <p class="mb-0 text-muted small"><i class="bi bi-currency-dollar me-1"></i> Số tiền: <strong class="text-danger"><?= formatMoney((int)$o['total_amount']) ?></strong></p>
                            </div>
                            <div class="card-footer bg-white border-0">
                                <form action="/shipper/orders/<?= (int)$o['id'] ?>/accept" method="POST" onsubmit="return confirm('Bạn chắc chắn muốn nhận giao đơn hàng này?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-warning btn-sm w-100 fw-bold"><i class="bi bi-truck me-1"></i> Nhận đơn hàng này</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
