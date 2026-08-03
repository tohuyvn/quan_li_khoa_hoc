<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><?= isset($promo) ? 'Sửa ưu đãi chung' : 'Tạo ưu đãi chung' ?></h2>
    <a href="/admin/site-promotions" class="btn btn-outline-secondary">Quay lại</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="post" action="<?= isset($promo) ? '/admin/site-promotions/' . $promo['id'] . '/update' : '/admin/site-promotions' ?>">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label for="name" class="form-label fw-bold">Tên chương trình *</label>
                <input type="text" name="name" id="name" class="form-control" value="<?= isset($promo) ? old('name', $promo['name']) : old('name') ?>" required placeholder="VD: Sale cuối tuần">
            </div>
            <div class="mb-3">
                <label for="discount_percent" class="form-label fw-bold">Mức giảm giá (%) *</label>
                <input type="number" name="discount_percent" id="discount_percent" class="form-control" min="1" max="100" value="<?= isset($promo) ? old('discount_percent', (string)$promo['discount_percent']) : old('discount_percent', '10') ?>" required>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="starts_at" class="form-label fw-bold">Ngày bắt đầu</label>
                    <input type="date" name="starts_at" id="starts_at" class="form-control" value="<?= isset($promo) ? old('starts_at', $promo['starts_at'] ?? '') : old('starts_at') ?>">
                    <?php if (!isset($promo)): ?>
                        <div class="form-text">Để trống = áp dụng ngay</div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label for="expires_at" class="form-label fw-bold">Ngày kết thúc *</label>
                    <input type="date" name="expires_at" id="expires_at" class="form-control" value="<?= isset($promo) ? old('expires_at', $promo['expires_at']) : old('expires_at') ?>" required>
                </div>
            </div>
            <div class="mb-3 mt-3">
                <label for="status" class="form-label fw-bold">Trạng thái</label>
                <?php $status = isset($promo) ? old('status', $promo['status'] ?? 'inactive') : old('status', 'active'); ?>
                <select name="status" id="status" class="form-select">
                    <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Đang bật</option>
                    <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>Đã tắt (hủy ưu đãi)</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary"><?= isset($promo) ? 'Cập nhật' : 'Tạo ưu đãi' ?></button>
        </form>
    </div>
</div>
