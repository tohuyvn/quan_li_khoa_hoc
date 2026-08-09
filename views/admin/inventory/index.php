<?php
$queryString = http_build_query($_GET);
$exportUrl = "/admin/inventory/export-csv" . ($queryString ? '?' . $queryString : '');

$totalItemsCount = count($products ?? []);
$inStockCount = 0;
$lowStockCount = 0;
$outOfStockCount = 0;

if (!empty($products)) {
    foreach ($products as $p) {
        $st = (int)($p['stock'] ?? 0);
        if ($st === 0) $outOfStockCount++;
        elseif ($st <= 5) $lowStockCount++;
        else $inStockCount++;
    }
}
?>

<style>
.admin-page-header {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    border-radius: 16px;
    padding: 24px 28px;
    color: #fff;
    margin-bottom: 24px;
    box-shadow: 0 10px 25px rgba(15, 23, 42, 0.12);
    position: relative;
    overflow: hidden;
}
.admin-page-header::after {
    content: '';
    position: absolute;
    right: -40px;
    top: -40px;
    width: 180px;
    height: 180px;
    background: rgba(245, 158, 11, 0.15);
    border-radius: 50%;
    filter: blur(30px);
}
.admin-stat-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 18px 20px;
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.03);
    display: flex;
    align-items: center;
    gap: 16px;
}
.admin-stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 24px rgba(15, 23, 42, 0.08);
    border-color: #cbd5e1;
}
.admin-stat-icon {
    width: 52px;
    height: 52px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    flex-shrink: 0;
}
.admin-stat-icon.blue { background: rgba(37, 99, 235, 0.1); color: #2563eb; }
.admin-stat-icon.green { background: rgba(16, 185, 129, 0.1); color: #10b981; }
.admin-stat-icon.orange { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
.admin-stat-icon.red { background: rgba(239, 68, 68, 0.1); color: #ef4444; }

.admin-stat-number {
    font-size: 1.6rem;
    font-weight: 800;
    line-height: 1.1;
    color: #0f172a;
    letter-spacing: -0.02em;
}
.admin-stat-label {
    font-size: 12.5px;
    color: #64748b;
    font-weight: 600;
    margin-top: 2px;
}

.table-custom-card {
    background: #ffffff;
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04);
    overflow: hidden;
}
.table-custom-card thead th {
    background: #f8fafc;
    color: #475569;
    font-size: 12.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    padding: 14px 16px;
    border-bottom: 2px solid #e2e8f0;
}
.inventory-row-item {
    transition: background 0.15s ease;
}
.inventory-row-item:hover {
    background-color: #f1f5f9 !important;
}

.product-img-wrapper {
    position: relative;
    width: 50px;
    height: 50px;
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid #e2e8f0;
    background: #f8fafc;
    flex-shrink: 0;
}
.product-img-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.badge-stock-pill {
    padding: 6px 14px;
    border-radius: 50px;
    font-size: 12px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.badge-stock-pill.in_stock { background: #dcfce7; color: #15803d; border: 1px solid rgba(34, 197, 94, 0.3); }
.badge-stock-pill.low_stock { background: #fef3c7; color: #b45309; border: 1px solid rgba(245, 158, 11, 0.3); }
.badge-stock-pill.out_of_stock { background: #fee2e2; color: #b91c1c; border: 1px solid rgba(239, 68, 68, 0.3); }
</style>

<!-- Header Banner -->
<div class="admin-page-header d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h2 class="h3 fw-bold mb-0 text-white"><i class="bi bi-pie-chart-fill me-2 text-warning"></i>Quản Lý Tồn Kho</h2>
            <span class="badge bg-warning text-dark rounded-pill px-3 py-1 text-uppercase" style="font-size: 10px; letter-spacing: 0.05em;">Stock Control</span>
        </div>
        <p class="mb-0 text-slate-300 small" style="color: #cbd5e1;">Kiểm kê hàng hóa, cập nhật số lượng tồn kho trực tiếp & cảnh báo hết hàng.</p>
    </div>
    <div>
        <a href="<?= $exportUrl ?>" class="btn btn-success btn-lg px-4 py-2 rounded-3 fw-bold shadow-sm d-inline-flex align-items-center gap-2" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: none;">
            <i class="bi bi-file-earmark-spreadsheet-fill fs-5"></i>
            <span>Xuất File CSV</span>
        </a>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="admin-stat-card">
            <div class="admin-stat-icon blue"><i class="bi bi-archive-fill"></i></div>
            <div>
                <div class="admin-stat-number"><?= number_format($totalItemsCount) ?></div>
                <div class="admin-stat-label">Tổng Mặt Hàng</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="admin-stat-card">
            <div class="admin-stat-icon green"><i class="bi bi-shield-check"></i></div>
            <div>
                <div class="admin-stat-number"><?= number_format($inStockCount) ?></div>
                <div class="admin-stat-label">Kho An Toàn (>5)</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="admin-stat-card">
            <div class="admin-stat-icon orange"><i class="bi bi-exclamation-triangle-fill"></i></div>
            <div>
                <div class="admin-stat-number"><?= number_format($lowStockCount) ?></div>
                <div class="admin-stat-label">Sắp Hết (≤5)</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="admin-stat-card">
            <div class="admin-stat-icon red"><i class="bi bi-x-circle-fill"></i></div>
            <div>
                <div class="admin-stat-number"><?= number_format($outOfStockCount) ?></div>
                <div class="admin-stat-label">Đã Hết Hàng (0)</div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Toolbar -->
<div class="bg-white p-3 rounded-4 border mb-4 shadow-sm">
    <form method="get" action="/admin/inventory" class="row g-3 align-items-end">
        <div class="col-md-5">
            <label class="form-label mb-1 small fw-bold text-muted"><i class="bi bi-search me-1"></i>Tìm kiếm sản phẩm</label>
            <input type="text" name="q" class="form-control form-control-sm" placeholder="Tên sản phẩm, danh mục..." value="<?= e($search) ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label mb-1 small fw-bold text-muted"><i class="bi bi-funnel me-1"></i>Trạng thái tồn kho</label>
            <select name="stock_status" class="form-select form-select-sm">
                <option value="" <?= ($stockStatus ?? '') === '' ? 'selected' : '' ?>>Tất cả trạng thái</option>
                <option value="in_stock" <?= ($stockStatus ?? '') === 'in_stock' ? 'selected' : '' ?>>🟢 Còn hàng (tồn > 5)</option>
                <option value="low_stock" <?= ($stockStatus ?? '') === 'low_stock' ? 'selected' : '' ?>>🟡 Sắp hết hàng (tồn ≤ 5)</option>
                <option value="out_of_stock" <?= ($stockStatus ?? '') === 'out_of_stock' ? 'selected' : '' ?>>🔴 Hết hàng (tồn = 0)</option>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary btn-sm flex-grow-1 fw-bold"><i class="bi bi-filter"></i> Lọc tồn kho</button>
            <a href="/admin/inventory" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-counterclockwise"></i></a>
        </div>
    </form>
</div>

<!-- Data Table Card -->
<div class="table-custom-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th width="60" class="text-center">ID</th>
                    <th width="70">Hình Ảnh</th>
                    <th>Tên Sản Phẩm</th>
                    <th>Danh Mục</th>
                    <th>Đơn Giá Bán</th>
                    <th class="text-center">Trạng Thái Kho</th>
                    <th width="220" class="text-center">Cập Nhật Tồn Kho Tức Thì</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($products)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="text-muted">
                                <i class="bi bi-box-seam fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                Không tìm thấy sản phẩm nào trong kho
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($products as $product):
                        $stock = (int)$product['stock'];
                        $statusKey = $stock === 0 ? 'out_of_stock' : ($stock <= 5 ? 'low_stock' : 'in_stock');
                        $stockIcon = $stock === 0 ? 'bi-x-circle-fill' : ($stock <= 5 ? 'bi-exclamation-triangle-fill' : 'bi-check-circle-fill');
                        $stockText = $stock === 0 ? 'Hết hàng (0 sp)' : ($stock <= 5 ? "Sắp hết ({$stock} sp)" : "An toàn ({$stock} sp)");
                    ?>
                        <tr class="inventory-row-item">
                            <td class="text-center fw-bold text-secondary">#<?= $product['id'] ?></td>
                            <td>
                                <div class="product-img-wrapper">
                                    <img src="<?= e(imageUrl($product['image'] ?? '')) ?>" alt="" loading="lazy">
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark mb-0"><?= e($product['name']) ?></div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border px-2 py-1 rounded-2">
                                    📁 <?= e($product['category_name'] ?? 'Khác') ?>
                                </span>
                            </td>
                            <td>
                                <span class="fw-bold text-danger fs-6"><?= formatMoney($product['price']) ?></span>
                            </td>
                            <td class="text-center">
                                <span class="badge-stock-pill <?= $statusKey ?>">
                                    <i class="bi <?= $stockIcon ?>"></i> <?= $stockText ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <form method="post" action="/admin/inventory/<?= $product['id'] ?>/update-stock" class="d-flex justify-content-center gap-2">
                                    <?= csrf_field() ?>
                                    <input type="number" name="stock" class="form-control form-control-sm text-center fw-bold border-primary-subtle" value="<?= $stock ?>" min="0" style="width: 90px; border-radius: 8px;">
                                    <button type="submit" class="btn btn-sm btn-primary rounded-2 px-3 fw-bold shadow-sm">
                                        <i class="bi bi-save me-1"></i>Lưu
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
