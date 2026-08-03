<?php
$queryString = http_build_query($_GET);
$exportUrl = "/admin/inventory/export-csv" . ($queryString ? '?' . $queryString : '');
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0" style="letter-spacing: -0.02em;">📊 Quản lý hàng tồn kho</h2>
    <a href="<?= $exportUrl ?>" class="btn btn-success fw-bold shadow-sm">
        <i class="bi bi-file-earmark-spreadsheet-fill me-1"></i> Xuất file CSV
    </a>
</div>

<div class="card p-3 mb-4 bg-white border rounded-3 shadow-sm">
    <form method="get" action="/admin/inventory" class="row g-3 align-items-end">
        <div class="col-md-5">
            <label class="form-label mb-1 small fw-semibold text-muted">Tìm kiếm</label>
            <input type="text" name="q" class="form-control form-control-sm" placeholder="Tên sản phẩm, danh mục..." value="<?= e($search) ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label mb-1 small fw-semibold text-muted">Trạng thái tồn kho</label>
            <select name="stock_status" class="form-select form-select-sm">
                <option value="" <?= ($stockStatus ?? '') === '' ? 'selected' : '' ?>>Tất cả</option>
                <option value="in_stock" <?= ($stockStatus ?? '') === 'in_stock' ? 'selected' : '' ?>>Còn hàng (tồn > 5)</option>
                <option value="low_stock" <?= ($stockStatus ?? '') === 'low_stock' ? 'selected' : '' ?>>Sắp hết hàng (tồn <= 5)</option>
                <option value="out_of_stock" <?= ($stockStatus ?? '') === 'out_of_stock' ? 'selected' : '' ?>>Hết hàng (tồn = 0)</option>
            </select>
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold"><i class="bi bi-funnel"></i> Lọc kết quả</button>
        </div>
    </form>
</div>

<div class="table-responsive">
    <table class="table table-striped table-bordered align-middle">
        <thead class="table-dark">
            <tr>
                <th width="60">ID</th>
                <th width="80">Ảnh</th>
                <th>Tên sản phẩm</th>
                <th>Danh mục</th>
                <th width="120">Đơn giá</th>
                <th width="150">Trạng thái tồn kho</th>
                <th width="200">Cập nhật số lượng tồn kho</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($products)): ?>
                <tr><td colspan="7" class="text-center">Không tìm thấy sản phẩm nào phù hợp.</td></tr>
            <?php else: ?>
                <?php foreach ($products as $product):
                    $stock = (int)$product['stock'];
                    $stockClass = 'success';
                    $stockText = 'Còn hàng (' . $stock . ')';
                    if ($stock === 0) {
                        $stockClass = 'danger';
                        $stockText = 'Hết hàng (0)';
                    } elseif ($stock <= 5) {
                        $stockClass = 'warning';
                        $stockText = 'Sắp hết hàng (' . $stock . ')';
                    }
                ?>
                    <tr>
                        <td><?= $product['id'] ?></td>
                        <td>
                            <img src="<?= e(imageUrl($product['image'] ?? '')) ?>" alt=""
                                 style="width:56px;height:56px;object-fit:cover;border-radius:6px;border:1px solid #eee">
                        </td>
                        <td>
                            <strong><?= e($product['name']) ?></strong>
                        </td>
                        <td><?= e($product['category_name']) ?></td>
                        <td><?= formatMoney($product['price']) ?></td>
                        <td>
                            <span class="badge bg-<?= $stockClass ?> fs-7">
                                <?= $stockText ?>
                            </span>
                        </td>
                        <td>
                            <form method="post" action="/admin/inventory/<?= $product['id'] ?>/update-stock" class="d-flex gap-2">
                                <?= csrf_field() ?>
                                <input type="number" name="stock" class="form-control form-control-sm" value="<?= $stock ?>" min="0" style="width: 80px;">
                                <button type="submit" class="btn btn-sm btn-primary">Lưu</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
