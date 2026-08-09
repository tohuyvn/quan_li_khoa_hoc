<?php
$totalCategories = count($categories ?? []);
$activeCategories = 0;
$inactiveCategories = 0;

if (!empty($categories)) {
    foreach ($categories as $cat) {
        if (($cat['status'] ?? '') === 'active') {
            $activeCategories++;
        } else {
            $inactiveCategories++;
        }
    }
}

// Fetch count of products per category from DB if available
$db = \App\DB::connection();
$catCounts = [];
try {
    $stmt = $db->query("SELECT category_id, COUNT(*) as total FROM products GROUP BY category_id");
    if ($stmt) {
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $catCounts[(int)$row['category_id']] = (int)$row['total'];
        }
    }
} catch (\Throwable $e) {}
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
    background: rgba(16, 185, 129, 0.15);
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
.admin-stat-icon.purple { background: rgba(147, 51, 234, 0.1); color: #9333ea; }
.admin-stat-icon.green { background: rgba(16, 185, 129, 0.1); color: #10b981; }
.admin-stat-icon.gray { background: rgba(100, 116, 139, 0.1); color: #64748b; }

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
.category-row-item {
    transition: background 0.15s ease;
}
.category-row-item:hover {
    background-color: #f1f5f9 !important;
}
.category-avatar {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    color: #2563eb;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    font-weight: 800;
}

.badge-status-pill {
    padding: 5px 12px;
    border-radius: 50px;
    font-size: 11.5px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}
.badge-status-pill.active {
    background: rgba(16, 185, 129, 0.12);
    color: #059669;
    border: 1px solid rgba(16, 185, 129, 0.25);
}
.badge-status-pill.inactive {
    background: rgba(100, 116, 139, 0.12);
    color: #64748b;
    border: 1px solid rgba(100, 116, 139, 0.2);
}

.btn-action-icon {
    width: 34px;
    height: 34px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    transition: all 0.2s ease;
    border: none;
    text-decoration: none !important;
}
.btn-action-icon.edit { background: #fef3c7; color: #d97706; }
.btn-action-icon.edit:hover { background: #d97706; color: #fff; transform: translateY(-2px); }
.btn-action-icon.delete { background: #fee2e2; color: #dc2626; }
.btn-action-icon.delete:hover { background: #dc2626; color: #fff; transform: translateY(-2px); }
</style>

<!-- Header Banner -->
<div class="admin-page-header d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h2 class="h3 fw-bold mb-0 text-white"><i class="bi bi-folder-fill me-2 text-warning"></i>Quản Lý Danh Mục</h2>
            <span class="badge bg-success rounded-pill px-3 py-1 text-uppercase" style="font-size: 10px; letter-spacing: 0.05em;">Structure & Catalog</span>
        </div>
        <p class="mb-0 text-slate-300 small" style="color: #cbd5e1;">Phân loại sản phẩm, cấu hình cấu trúc cửa hàng & theo dõi số lượng tồn kho theo nhóm.</p>
    </div>
    <div>
        <a href="/admin/categories/create" class="btn btn-primary btn-lg px-4 py-2 rounded-3 fw-bold shadow-sm d-inline-flex align-items-center gap-2" style="background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%); border: none;">
            <i class="bi bi-plus-circle-fill fs-5"></i>
            <span>Thêm Danh Mục Mới</span>
        </a>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-12 col-md-4">
        <div class="admin-stat-card">
            <div class="admin-stat-icon purple"><i class="bi bi-folder-symlink-fill"></i></div>
            <div>
                <div class="admin-stat-number"><?= number_format($totalCategories) ?></div>
                <div class="admin-stat-label">Tổng Số Danh Mục</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="admin-stat-card">
            <div class="admin-stat-icon green"><i class="bi bi-check-circle-fill"></i></div>
            <div>
                <div class="admin-stat-number"><?= number_format($activeCategories) ?></div>
                <div class="admin-stat-label">Đang Hoạt Động</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="admin-stat-card">
            <div class="admin-stat-icon gray"><i class="bi bi-eye-slash-fill"></i></div>
            <div>
                <div class="admin-stat-number"><?= number_format($inactiveCategories) ?></div>
                <div class="admin-stat-label">Đang Ẩn</div>
            </div>
        </div>
    </div>
</div>

<!-- Data Table Card -->
<div class="table-custom-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th width="70" class="text-center">ID</th>
                    <th width="60">Biểu Tượng</th>
                    <th>Tên Danh Mục</th>
                    <th>Mô Tả Danh Mục</th>
                    <th width="150" class="text-center">Sản Phẩm Trong Shop</th>
                    <th width="120" class="text-center">Trạng Thái</th>
                    <th width="120" class="text-center">Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($categories)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="text-muted">
                                <i class="bi bi-folder-x fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                Chưa có danh mục nào được khởi tạo
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($categories as $cat):
                        $catId = (int)$cat['id'];
                        $prodCount = $catCounts[$catId] ?? 0;
                        $status = $cat['status'] ?? 'active';
                    ?>
                        <tr class="category-row-item">
                            <td class="text-center fw-bold text-secondary">#<?= $catId ?></td>
                            <td>
                                <div class="category-avatar">
                                    <i class="bi bi-tag-fill"></i>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark fs-6"><?= e($cat['name']) ?></div>
                            </td>
                            <td>
                                <?php if (!empty($cat['description'])): ?>
                                    <div class="text-muted small"><?= e($cat['description']) ?></div>
                                <?php else: ?>
                                    <span class="text-muted fst-italic small">Không có mô tả</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-primary border border-primary-subtle px-3 py-2 rounded-pill fw-bold" style="font-size: 12px;">
                                    <i class="bi bi-box-seam me-1"></i> <?= $prodCount ?> sản phẩm
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge-status-pill <?= $status ?>">
                                    <span class="spinner-grow spinner-grow-sm p-0" style="width:6px;height:6px;" role="status"></span>
                                    <?= $status === 'active' ? 'Hoạt động' : 'Ẩn' ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="/admin/categories/<?= $catId ?>/edit" class="btn-action-icon edit" title="Sửa danh mục">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form method="post" action="/admin/categories/<?= $catId ?>/delete" class="d-inline"
                                          onsubmit="return confirm('Bạn có chắc chắn muốn xóa danh mục này?')">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn-action-icon delete" title="Xóa danh mục">
                                            <i class="bi bi-trash3-fill"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
