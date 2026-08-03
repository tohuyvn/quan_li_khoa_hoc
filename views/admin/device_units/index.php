<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h2 class="mb-0">Kho IMEI / Serial</h2>
    <form method="post" action="/admin/device-units/auto-seed" class="d-flex gap-2 align-items-center"
          onsubmit="return confirm('Tự sinh IMEI cho mọi sản phẩm theo số tồn kho (chỉ bổ sung phần thiếu)?');">
        <?= csrf_field() ?>
        <select name="store_id" class="form-select form-select-sm" style="min-width:180px">
            <option value="">Cửa hàng mặc định</option>
            <?php foreach ($stores as $s): ?>
                <option value="<?= (int)$s['id'] ?>"><?= e($s['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-danger btn-sm fw-bold">⚡ Sinh IMEI tự động (tất cả SP)</button>
    </form>
</div>

<div class="card mb-4">
    <div class="card-body">
        <h5 class="fw-bold mb-3">Nhập IMEI (mỗi dòng / cách nhau bằng dấu phẩy)</h5>
        <form method="post" action="/admin/device-units">
            <?= csrf_field() ?>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Sản phẩm *</label>
                    <select name="product_id" class="form-select" required>
                        <option value="">-- Chọn --</option>
                        <?php foreach ($products as $p): ?>
                            <option value="<?= (int)$p['id'] ?>"><?= e($p['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Cửa hàng</label>
                    <select name="store_id" class="form-select">
                        <option value="">-- Không gắn --</option>
                        <?php foreach ($stores as $s): ?>
                            <option value="<?= (int)$s['id'] ?>"><?= e($s['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label">Danh sách IMEI *</label>
                    <textarea name="imei" class="form-control" rows="3" required placeholder="860123456789012&#10;860123456789013"></textarea>
                </div>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Thêm vào kho</button>
        </form>
    </div>
</div>

<form class="row g-2 mb-3" method="get">
    <div class="col-md-3">
        <input type="text" name="q" class="form-control" placeholder="IMEI / tên SP" value="<?= e($filters['q'] ?? '') ?>">
    </div>
    <div class="col-md-2">
        <select name="status" class="form-select">
            <option value="">Mọi trạng thái</option>
            <?php foreach (['available','reserved','sold','rma'] as $st): ?>
                <option value="<?= $st ?>" <?= ($filters['status'] ?? '') === $st ? 'selected' : '' ?>><?= $st ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2">
        <button class="btn btn-outline-secondary w-100">Lọc</button>
    </div>
</form>

<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>IMEI</th>
                <th>Sản phẩm</th>
                <th>Cửa hàng</th>
                <th>Trạng thái</th>
                <th>Đơn</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($result['items'] as $row): ?>
                <tr>
                    <td class="font-monospace"><?= e($row['imei']) ?></td>
                    <td><?= e($row['product_name'] ?? '') ?></td>
                    <td><?= e($row['store_name'] ?? '—') ?></td>
                    <td><span class="badge bg-secondary"><?= e($row['status']) ?></span></td>
                    <td><?= $row['order_id'] ? '#' . (int)$row['order_id'] : '—' ?></td>
                    <td class="text-end">
                        <?php if ($row['status'] === 'available'): ?>
                            <form method="post" action="/admin/device-units/<?= (int)$row['id'] ?>/delete" class="d-inline"
                                  onsubmit="return confirm('Xóa IMEI này?')">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger">Xóa</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($result['items'])): ?>
                <tr><td colspan="6" class="text-muted text-center py-4">Chưa có IMEI nào.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
