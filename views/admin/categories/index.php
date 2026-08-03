<h2 class="mb-4">Quản lý danh mục sản phẩm</h2>

<div class="mb-3">
    <a href="/admin/categories/create" class="btn btn-primary">+ Thêm danh mục</a>
</div>

<div class="table-responsive">
    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th width="60">ID</th>
                <th>Tên danh mục</th>
                <th>Mô tả</th>
                <th width="100">Trạng thái</th>
                <th width="180">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($categories)): ?>
                <tr><td colspan="5" class="text-center">Chưa có danh mục</td></tr>
            <?php else: ?>
                <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td><?= $cat['id'] ?></td>
                        <td><?= e($cat['name']) ?></td>
                        <td><?= e($cat['description']) ?></td>
                        <td>
                            <span class="badge bg-<?= ($cat['status'] ?? '') === 'active' ? 'success' : 'secondary' ?>">
                                <?= ($cat['status'] ?? '') === 'active' ? 'Hoạt động' : 'Ẩn' ?>
                            </span>
                        </td>
                        <td>
                            <a href="/admin/categories/<?= $cat['id'] ?>/edit" class="btn btn-sm btn-warning">Sửa</a>
                            <form method="post" action="/admin/categories/<?= (int) $cat['id'] ?>/delete" class="d-inline"
                                  onsubmit="return confirm('Xóa danh mục này?')">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
