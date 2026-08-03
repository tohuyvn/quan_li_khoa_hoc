<?php
$isEdit = !empty($store);
$s = $store ?? [];
?>
<h2 class="mb-4"><?= $isEdit ? 'Sửa cửa hàng' : 'Thêm cửa hàng' ?></h2>

<form method="post" action="<?= $isEdit ? '/admin/stores/' . (int)$store['id'] . '/update' : '/admin/stores' ?>" class="mb-4">
    <?= csrf_field() ?>
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Tên *</label>
            <input type="text" name="name" class="form-control" required value="<?= old('name', $s['name'] ?? '') ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label">Điện thoại</label>
            <input type="text" name="phone" class="form-control" value="<?= old('phone', $s['phone'] ?? '') ?>">
        </div>
        <div class="col-12">
            <label class="form-label">Địa chỉ *</label>
            <input type="text" name="address" class="form-control" required value="<?= old('address', $s['address'] ?? '') ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label">Lat *</label>
            <input type="text" name="lat" class="form-control" required value="<?= old('lat', (string)($s['lat'] ?? '')) ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label">Lng *</label>
            <input type="text" name="lng" class="form-control" required value="<?= old('lng', (string)($s['lng'] ?? '')) ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label">Bán kính phục vụ (km)</label>
            <input type="number" step="0.1" name="service_radius_km" class="form-control" value="<?= old('service_radius_km', (string)($s['service_radius_km'] ?? '12')) ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label">Trạng thái</label>
            <?php $st = old('status', $s['status'] ?? 'active'); ?>
            <select name="status" class="form-select">
                <option value="active" <?= $st === 'active' ? 'selected' : '' ?>>active</option>
                <option value="inactive" <?= $st === 'inactive' ? 'selected' : '' ?>>inactive</option>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Giờ mở cửa</label>
            <input type="text" name="opening_hours" class="form-control" value="<?= old('opening_hours', $s['opening_hours'] ?? '08:00-21:30') ?>">
        </div>
    </div>
    <div class="mt-3 d-flex gap-2">
        <button class="btn btn-primary"><?= $isEdit ? 'Cập nhật' : 'Thêm' ?></button>
        <a href="/admin/stores" class="btn btn-secondary">Quay lại</a>
    </div>
</form>

<?php if ($isEdit): ?>
<hr>
<h4 class="mb-3">Tồn kho tại cửa hàng</h4>
<form method="post" action="/admin/stores/<?= (int)$store['id'] ?>/stock" class="row g-2 align-items-end mb-4">
    <?= csrf_field() ?>
    <div class="col-md-6">
        <label class="form-label">Sản phẩm</label>
        <select name="product_id" class="form-select" required>
            <?php foreach ($products as $p): ?>
                <option value="<?= (int)$p['id'] ?>"><?= e($p['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">Số lượng</label>
        <input type="number" name="quantity" class="form-control" min="0" value="0" required>
    </div>
    <div class="col-md-3">
        <button class="btn btn-outline-primary w-100">Lưu tồn</button>
    </div>
</form>

<div class="table-responsive">
    <table class="table table-sm">
        <thead><tr><th>Sản phẩm</th><th>Tồn CH</th><th>Tồn global</th></tr></thead>
        <tbody>
            <?php foreach (($stock ?? []) as $row): ?>
                <tr>
                    <td><?= e($row['product_name']) ?></td>
                    <td><?= (int)$row['quantity'] ?></td>
                    <td><?= (int)$row['global_stock'] ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($stock)): ?>
                <tr><td colspan="3" class="text-muted">Chưa có dòng tồn kho.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
