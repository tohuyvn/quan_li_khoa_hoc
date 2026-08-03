<h2 class="mb-4"><?= $category ? 'Sửa danh mục' : 'Thêm danh mục' ?></h2>

<div class="card">
    <div class="card-body">
        <form method="post" action="<?= $category ? '/admin/categories/' . $category['id'] . '/update' : '/admin/categories' ?>">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label">Tên danh mục *</label>
                <input type="text" name="name" class="form-control"
                       value="<?= e($category['name'] ?? old('name')) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Mô tả</label>
                <textarea name="description" class="form-control" rows="3"><?= e($category['description'] ?? old('description')) ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Trạng thái</label>
                <select name="status" class="form-select">
                    <option value="active" <?= ($category['status'] ?? old('status', 'active')) === 'active' ? 'selected' : '' ?>>Hoạt động</option>
                    <option value="inactive" <?= ($category['status'] ?? old('status')) === 'inactive' ? 'selected' : '' ?>>Ẩn</option>
                </select>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><?= $category ? 'Cập nhật' : 'Thêm mới' ?></button>
                <a href="/admin/categories" class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>
</div>
