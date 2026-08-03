<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><?= isset($promo) ? 'Sửa mã khuyến mãi' : 'Tạo mã khuyến mãi mới' ?></h2>
    <a href="/admin/promotions" class="btn btn-outline-secondary">Quay lại</a>
</div>

<div class="card p-4 bg-white border rounded-3 style-form" style="max-width: 600px;">
    <form method="post" action="<?= isset($promo) ? '/admin/promotions/' . $promo['id'] . '/update' : '/admin/promotions' ?>">
        <?= csrf_field() ?>
        <div class="mb-3">
            <label for="code" class="form-label fw-bold">Mã khuyến mãi (Code)</label>
            <input type="text" name="code" id="code" class="form-control" value="<?= e($promo['code'] ?? old('code')) ?>" placeholder="Ví dụ: SALE10, BLACKFRIDAY" style="text-transform: uppercase;" required>
            <?php if (!isset($promo)): ?>
                <div class="form-text text-muted">Mã khuyến mãi viết hoa và không dấu.</div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="discount_percent" class="form-label fw-bold">Mức giảm giá (%)</label>
            <input type="number" name="discount_percent" id="discount_percent" class="form-control" min="1" max="100" value="<?= isset($promo) ? (int)$promo['discount_percent'] : (int)old('discount_percent') ?>" placeholder="10" required>
        </div>

        <div class="mb-3">
            <label for="expires_at" class="form-label fw-bold">Ngày hết hạn</label>
            <input type="date" name="expires_at" id="expires_at" class="form-control" value="<?= isset($promo) ? date('Y-m-d', strtotime($promo['expires_at'])) : old('expires_at') ?>" required>
        </div>

        <div class="mb-3">
            <label for="status" class="form-label fw-bold">Trạng thái</label>
            <select name="status" id="status" class="form-select">
                <option value="active" <?= (isset($promo) ? $promo['status'] : old('status', 'active')) === 'active' ? 'selected' : '' ?>>Đang hoạt động</option>
                <option value="inactive" <?= (isset($promo) ? $promo['status'] : old('status')) === 'inactive' ? 'selected' : '' ?>>Tạm tắt</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold"><?= isset($promo) ? 'Lưu thay đổi' : 'Tạo chương trình' ?></button>
    </form>
</div>
