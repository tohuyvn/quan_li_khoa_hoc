<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h2 class="h3 fw-bold mb-1 text-dark"><i class="bi bi-shield-check text-primary me-2"></i>Quản Lý Bảo Hành Điện Tử</h2>
        <p class="text-muted small mb-0">Kích hoạt bảo hành tại quầy, theo dõi danh sách thiết bị và mã IMEI sản phẩm.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="/admin/warranties/claims" class="btn btn-primary btn-sm rounded-3 px-3 py-2 fw-bold shadow-sm">
            <i class="bi bi-wrench-adjustable me-1"></i> Yêu Cầu Sửa Chữa
        </a>
    </div>
</div>

<!-- Form Kích Hoạt Tại Quầy -->
<div class="content-card mb-4">
    <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
        <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
            <i class="bi bi-plus-circle-fill text-success"></i> Kích Hoạt Bảo Hành Tại Quầy (Offline)
        </h5>
        <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill" style="font-size: 11px;">Mã BH Tự Động</span>
    </div>
    <form method="post" action="/admin/warranties/activate" class="row g-3">
        <?= csrf_field() ?>
        <div class="col-md-4">
            <label class="form-label small fw-bold text-dark">Sản phẩm *</label>
            <select name="product_id" class="form-select form-select-sm" required>
                <option value="">-- Chọn sản phẩm --</option>
                <?php foreach (($products ?? []) as $p): ?>
                    <option value="<?= (int)$p['id'] ?>"><?= e($p['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label small fw-bold text-dark">Số IMEI / Serial *</label>
            <input type="text" name="imei" class="form-control form-control-sm font-monospace" required placeholder="15 chữ số IMEI">
        </div>
        <div class="col-md-4">
            <label class="form-label small fw-bold text-dark">Thời hạn (Tháng)</label>
            <input type="number" name="months" class="form-control form-control-sm" value="12" min="1" max="36">
        </div>
        <div class="col-md-4">
            <label class="form-label small fw-bold text-dark">Tên khách hàng *</label>
            <input type="text" name="customer_name" class="form-control form-control-sm" required placeholder="Nguyễn Văn A">
        </div>
        <div class="col-md-4">
            <label class="form-label small fw-bold text-dark">Số điện thoại *</label>
            <input type="text" name="customer_phone" class="form-control form-control-sm" required placeholder="09xxxxxxxx">
        </div>
        <div class="col-md-4">
            <label class="form-label small fw-bold text-dark">Email thông báo</label>
            <input type="email" name="email" class="form-control form-control-sm" placeholder="khach@gmail.com">
        </div>
        <div class="col-12 pt-2">
            <button type="submit" class="btn btn-dark btn-sm px-4 py-2 fw-bold rounded-3">
                <i class="bi bi-shield-lock-fill me-1"></i> Kích Hoạt Ngay
            </button>
        </div>
    </form>
</div>

<!-- Table Danh Sách Bảo Hành -->
<div class="content-card p-0 overflow-hidden">
    <div class="p-3 bg-light border-bottom d-flex align-items-center justify-content-between">
        <h5 class="fw-bold mb-0 text-dark" style="font-size: 15px;">Danh Sách Thiết Bị Bảo Hành</h5>
        <span class="badge bg-dark rounded-pill px-3 py-1 fw-bold"><?= count($warranties ?? []) ?> bản ghi</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th width="60" class="ps-3">ID</th>
                    <th width="120">Nguồn Đơn</th>
                    <th>Sản Phẩm</th>
                    <th>Khách Hàng</th>
                    <th>SĐT</th>
                    <th>Mã IMEI</th>
                    <th>Mã Bảo Hành</th>
                    <th class="text-center">Trạng Thái</th>
                    <th width="140" class="pe-3">Ngày Hết Hạn</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($warranties)): ?>
                    <tr><td colspan="9" class="text-center py-5 text-muted">Chưa có bản ghi bảo hành nào</td></tr>
                <?php else: ?>
                    <?php foreach ($warranties as $w): ?>
                        <tr>
                            <td class="ps-3 fw-bold text-muted" style="font-size: 12px;"><?= (int)$w['id'] ?></td>
                            <td>
                                <?php if (!empty($w['order_code'])): ?>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace px-2 py-1"><?= e($w['order_code']) ?></span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1">Tại quầy</span>
                                <?php endif; ?>
                            </td>
                            <td class="fw-semibold">
                                <div class="d-flex align-items-center gap-2">
                                    <img src="<?= e(imageUrl($w['product_image'] ?? '', 'thumb')) ?>"
                                         alt="<?= e($w['product_name'] ?? '') ?>"
                                         style="width:38px;height:38px;object-fit:contain;border-radius:8px;border:1px solid #e2e8f0;background:#fff;padding:2px;flex-shrink:0"
                                         loading="lazy"
                                         onerror="this.onerror=null;this.src='/image/logo.webp';">
                                    <span class="text-truncate" style="max-width: 180px;"><?= e($w['product_name'] ?? '') ?></span>
                                </div>
                            </td>
                            <td class="fw-semibold text-dark"><?= e($w['customer_name'] ?? '') ?></td>
                            <td class="small text-muted"><?= e($w['customer_phone'] ?? '') ?></td>
                            <td><code class="bg-light text-primary border rounded px-1.5 py-0.5" style="font-size: 11px;"><?= e($w['imei'] ?? 'N/A') ?></code></td>
                            <td><span class="fw-bold font-monospace text-dark" style="font-size: 12px;"><?= e($w['warranty_code'] ?? '') ?></span></td>
                            <td class="text-center">
                                <?php if (($w['status'] ?? '') === 'active'): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1" style="font-size: 11px;">
                                        <i class="bi bi-check-circle-fill me-1"></i>Còn hạn
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2.5 py-1" style="font-size: 11px;">
                                        Hết hạn
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="pe-3 small text-muted"><?= date('d/m/Y', strtotime($w['expired_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
