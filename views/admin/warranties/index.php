<h2 class="mb-4">Quản lý Bảo hành Điện tử</h2>

<div class="mb-3 d-flex gap-2">
    <a href="/admin/warranties/claims" class="btn btn-outline-primary">
        <i class="bi bi-wrench-adjustable me-1"></i>Xem các yêu cầu sửa chữa
    </a>
</div>

<div class="card border rounded-3 shadow-sm mb-4">
    <div class="card-header bg-light fw-bold">Kích hoạt bảo hành tại quầy (offline)</div>
    <div class="card-body">
        <form method="post" action="/admin/warranties/activate" class="row g-3">
            <?= csrf_field() ?>
            <div class="col-md-4">
                <label class="form-label">Sản phẩm *</label>
                <select name="product_id" class="form-select" required>
                    <option value="">-- Chọn --</option>
                    <?php foreach (($products ?? []) as $p): ?>
                        <option value="<?= (int)$p['id'] ?>"><?= e($p['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">IMEI *</label>
                <input type="text" name="imei" class="form-control font-monospace" required placeholder="15 chữ số">
            </div>
            <div class="col-md-4">
                <label class="form-label">Thời hạn (tháng)</label>
                <input type="number" name="months" class="form-control" value="12" min="1" max="36">
            </div>
            <div class="col-md-4">
                <label class="form-label">Tên khách *</label>
                <input type="text" name="customer_name" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">SĐT *</label>
                <input type="text" name="customer_phone" class="form-control" required placeholder="09xxxxxxxx">
            </div>
            <div class="col-md-4">
                <label class="form-label">Email (gửi thông báo)</label>
                <input type="email" name="email" class="form-control">
            </div>
            <div class="col-12">
                <button class="btn btn-primary">Kích hoạt bảo hành</button>
            </div>
        </form>
    </div>
</div>

<div class="card border rounded-3 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th width="70" class="ps-3">ID</th>
                        <th width="120">Đơn hàng</th>
                        <th>Sản phẩm</th>
                        <th>Khách hàng</th>
                        <th>SĐT</th>
                        <th>IMEI</th>
                        <th>Mã BH</th>
                        <th>Trạng thái</th>
                        <th width="150" class="pe-3">Hạn BH</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($warranties)): ?>
                        <tr><td colspan="9" class="text-center py-4 text-muted">Chưa có bản ghi bảo hành nào</td></tr>
                    <?php else: ?>
                        <?php foreach ($warranties as $w): ?>
                            <tr>
                                <td class="ps-3 fw-bold"><?= (int)$w['id'] ?></td>
                                <td>
                                    <?php if (!empty($w['order_code'])): ?>
                                        <span class="badge bg-secondary font-monospace"><?= e($w['order_code']) ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-info text-dark">Tại quầy</span>
                                    <?php endif; ?>
                                </td>
                                <td class="fw-semibold">
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="<?= e(imageUrl($w['product_image'] ?? '', 'thumb')) ?>"
                                             alt="<?= e($w['product_name'] ?? '') ?>"
                                             style="width:36px;height:36px;object-fit:contain;border-radius:6px;border:1px solid #dee2e6;background:#fff;flex-shrink:0"
                                             loading="lazy"
                                             onerror="this.onerror=null;this.src='/image/logo.webp';">
                                        <span><?= e($w['product_name'] ?? '') ?></span>
                                    </div>
                                </td>
                                <td><?= e($w['customer_name'] ?? '') ?></td>
                                <td><?= e($w['customer_phone'] ?? '') ?></td>
                                <td class="font-monospace small"><?= e($w['imei'] ?? '') ?></td>
                                <td class="font-monospace"><?= e($w['warranty_code'] ?? '') ?></td>
                                <td><span class="badge bg-success"><?= e($w['status'] ?? '') ?></span></td>
                                <td class="pe-3 small"><?= e($w['expired_at'] ?? '') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
