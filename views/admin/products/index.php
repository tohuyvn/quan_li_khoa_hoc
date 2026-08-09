<?php
$totalProducts = count($products ?? []);
$activeProducts = 0;
$promoProducts = 0;
$outOfStock = 0;

if (!empty($products)) {
    foreach ($products as $p) {
        if (($p['status'] ?? '') === 'active') $activeProducts++;
        if (!empty($p['discount_percent']) || !empty($p['promo_active'])) $promoProducts++;
        if ((int)($p['stock'] ?? 0) <= 3) $outOfStock++;
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
    background: rgba(59, 130, 246, 0.15);
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
.table-custom-card .table {
    margin-bottom: 0;
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
.product-row-item {
    transition: background 0.15s ease, transform 0.15s ease;
}
.product-row-item:hover {
    background-color: #f1f5f9 !important;
}
.product-img-wrapper {
    position: relative;
    width: 52px;
    height: 52px;
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
    transition: transform 0.3s ease;
}
.product-row-item:hover .product-img-wrapper img {
    transform: scale(1.15);
}
.badge-stock {
    padding: 4px 10px;
    border-radius: 50px;
    font-size: 12px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.badge-stock.high { background: #dcfce7; color: #15803d; }
.badge-stock.low { background: #fef3c7; color: #b45309; }
.badge-stock.empty { background: #fee2e2; color: #b91c1c; }

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

.glass-bulk-bar {
    background: rgba(15, 23, 42, 0.92) !important;
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 18px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.35);
}
</style>

<!-- Header Banner -->
<div class="admin-page-header d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h2 class="h3 fw-bold mb-0 text-white"><i class="bi bi-box-seam-fill me-2 text-primary"></i>Quản Lý Sản Phẩm</h2>
            <span class="badge bg-primary rounded-pill px-3 py-1 text-uppercase" style="font-size: 10px; letter-spacing: 0.05em;">Admin Control</span>
        </div>
        <p class="mb-0 text-slate-300 small" style="color: #cbd5e1;">Quản lý kho hàng, cập nhật giá bán, cấu hình khuyến mãi & biến thể sản phẩm real-time.</p>
    </div>
    <div>
        <a href="/admin/products/create" class="btn btn-danger btn-lg px-4 py-2 rounded-3 fw-bold shadow-sm d-inline-flex align-items-center gap-2" style="background: linear-gradient(135deg, #d70018 0%, #ff4757 100%); border: none;">
            <i class="bi bi-plus-circle-fill fs-5"></i>
            <span>Thêm Sản Phẩm Mới</span>
        </a>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="admin-stat-card">
            <div class="admin-stat-icon blue"><i class="bi bi-boxes"></i></div>
            <div>
                <div class="admin-stat-number"><?= number_format($totalProducts) ?></div>
                <div class="admin-stat-label">Tổng Sản Phẩm</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="admin-stat-card">
            <div class="admin-stat-icon green"><i class="bi bi-check-circle-fill"></i></div>
            <div>
                <div class="admin-stat-number"><?= number_format($activeProducts) ?></div>
                <div class="admin-stat-label">Đang Kinh Doanh</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="admin-stat-card">
            <div class="admin-stat-icon orange"><i class="bi bi-lightning-charge-fill"></i></div>
            <div>
                <div class="admin-stat-number"><?= number_format($promoProducts) ?></div>
                <div class="admin-stat-label">Đang Khuyến Mãi</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="admin-stat-card">
            <div class="admin-stat-icon red"><i class="bi bi-exclamation-triangle-fill"></i></div>
            <div>
                <div class="admin-stat-number"><?= number_format($outOfStock) ?></div>
                <div class="admin-stat-label">Tồn Kho Ít (≤3)</div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Toolbar -->
<div class="bg-white p-3 rounded-4 border mb-4 shadow-sm d-flex justify-content-between align-items-center flex-wrap gap-3">
    <form action="/admin/products" method="GET" class="d-flex gap-2 align-items-center flex-grow-1" style="max-width: 500px;">
        <div class="input-group">
            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
            <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Tìm tên sản phẩm, danh mục hoặc ID..." value="<?= e($search ?? '') ?>">
            <?php if (!empty($search)): ?>
                <a href="/admin/products" class="btn btn-light border" title="Xóa tìm kiếm"><i class="bi bi-x-lg"></i></a>
            <?php endif; ?>
            <button class="btn btn-primary px-4 fw-bold" type="submit">Tìm kiếm</button>
        </div>
    </form>

    <div class="d-flex align-items-center gap-2">
        <span class="text-muted small fw-semibold"><i class="bi bi-funnel-fill me-1"></i>Lọc nhanh:</span>
        <select class="form-select form-select-sm border-secondary-subtle" id="filter-status-select" style="width: auto; cursor: pointer;">
            <option value="all">Tất cả trạng thái</option>
            <option value="active">🟢 Đang bán</option>
            <option value="inactive">⚪ Ẩn</option>
        </select>
    </div>
</div>

<!-- Bulk Actions Form & Data Table -->
<form id="bulk-edit-form" action="/admin/products/bulk-action" method="POST">
    <div class="table-custom-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th width="40" class="text-center">
                            <input type="checkbox" class="form-check-input" id="check-all" style="cursor: pointer; width: 18px; height: 18px;">
                        </th>
                        <th width="60" class="text-center">ID</th>
                        <th width="76">Hình Ảnh</th>
                        <th>Thông Tin Sản Phẩm</th>
                        <th>Danh Mục</th>
                        <th>Giá Niêm Yết</th>
                        <th>Giá Khuyến Mãi</th>
                        <th width="110" class="text-center">Tồn Kho</th>
                        <th width="110" class="text-center">Trạng Thái</th>
                        <th width="110" class="text-center">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($products)): ?>
                        <tr>
                            <td colspan="10" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                    Không tìm thấy sản phẩm phù hợp
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php
                        $globalDiscount = \App\PriceCalculator::getGlobalDiscountPercent();
                        foreach ($products as $product):
                            $priced = \App\PriceCalculator::resolve($product, $globalDiscount);
                            $stock = (int) ($product['stock'] ?? 0);
                            $stockClass = $stock > 10 ? 'high' : ($stock > 0 ? 'low' : 'empty');
                            $stockIcon = $stock > 10 ? 'bi-check-circle-fill' : ($stock > 0 ? 'bi-exclamation-circle-fill' : 'bi-x-circle-fill');
                            $status = $product['status'] ?? 'active';
                        ?>
                            <tr class="product-row-item" data-id="<?= $product['id'] ?>" data-status="<?= $status ?>">
                                <td class="text-center">
                                    <input type="checkbox" name="product_ids[]" value="<?= $product['id'] ?>" class="form-check-input product-select" style="cursor: pointer; width: 18px; height: 18px;">
                                </td>
                                <td class="text-center fw-bold text-secondary">#<?= $product['id'] ?></td>
                                <td>
                                    <div class="product-img-wrapper">
                                        <img src="<?= e(imageUrl($product['image'] ?? '')) ?>" alt="" loading="lazy">
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark mb-1"><?= e($product['name']) ?></div>
                                    <?php if (!empty($product['description'])): ?>
                                        <div class="small text-muted text-truncate" style="max-width: 280px;"><?= e($product['description']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border px-2 py-1 rounded-2 fw-semibold">
                                        📁 <?= e($product['category_name'] ?? 'Chưa phân loại') ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="<?= $priced['has_discount'] ? 'text-decoration-line-through text-muted small' : 'fw-bold text-dark' ?>">
                                        <?= formatMoney($priced['original_price']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-bold text-danger fs-6"><?= formatMoney($priced['sale_price']) ?></span>
                                    <?php if ($priced['has_discount']): ?>
                                        <div class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-0" style="font-size: 10px;">
                                            -<?= (int) $priced['discount_percent'] ?>% (<?= $priced['discount_source'] === 'global' ? 'Ưu đãi chung' : 'Khuyến mãi' ?>)
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge-stock <?= $stockClass ?>">
                                        <i class="bi <?= $stockIcon ?>"></i> <?= $stock ?> sp
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge-status-pill <?= $status ?>">
                                        <span class="spinner-grow spinner-grow-sm p-0" style="width:6px;height:6px;" role="status"></span>
                                        <?= $status === 'active' ? 'Đang bán' : 'Ẩn' ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="/admin/products/<?= $product['id'] ?>/edit" class="btn-action-icon edit" title="Chỉnh sửa sản phẩm">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form method="post" action="/admin/products/<?= (int) $product['id'] ?>/delete" class="d-inline"
                                              onsubmit="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn-action-icon delete" title="Xóa sản phẩm">
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

    <!-- Sticky Floating Glass Bulk Action Bar -->
    <div id="bulk-action-bar" class="d-none position-fixed bottom-0 start-50 translate-middle-x glass-bulk-bar text-white p-3 shadow-lg" style="z-index: 1050; min-width: 620px; max-width: 90%; margin-bottom: 24px;">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-check2-all text-warning fs-4"></i>
                <div>
                    <span class="fw-bold fs-5 text-warning" id="selected-count">0</span>
                    <span class="text-slate-300">sản phẩm đã chọn</span>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm btn-outline-light rounded-3 px-3" id="deselect-all-btn">Bỏ chọn</button>
                <button type="button" class="btn btn-sm btn-danger rounded-3 px-3 fw-bold" id="bulk-delete-trigger"><i class="bi bi-trash-fill me-1"></i>Xóa hàng loạt</button>
                <button type="button" class="btn btn-sm btn-success rounded-3 px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#bulkEditModal">
                    ⚡ Chỉnh sửa hàng loạt
                </button>
            </div>
        </div>
    </div>

    <!-- Bulk Edit Modal -->
    <div class="modal fade" id="bulkEditModal" tabindex="-1" aria-labelledby="bulkEditModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header bg-dark text-white p-3">
                    <h5 class="modal-title fw-bold" id="bulkEditModalLabel"><i class="bi bi-lightning-charge-fill text-warning me-2"></i>Chỉnh sửa <span id="modal-selected-count" class="text-warning">0</span> sản phẩm</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" name="bulk_action" id="bulk-action-input" value="change_category">

                    <div class="mb-3">
                        <label class="form-label fw-bold text-primary">Chọn thuộc tính muốn thay đổi:</label>
                        <select class="form-select border-primary-subtle fw-semibold" id="bulk-attribute-selector" style="cursor: pointer;">
                            <option value="change_category" selected>📁 Thay đổi Danh mục</option>
                            <option value="change_status">👁️ Thay đổi Trạng thái hiển thị</option>
                            <option value="update_price">💰 Điều chỉnh Giá bán</option>
                            <option value="update_stock">📦 Điều chỉnh Số lượng Tồn kho</option>
                            <option value="update_promo">🔥 Cấu hình Flash Sale / Khuyến mãi</option>
                        </select>
                    </div>

                    <hr class="my-3">

                    <!-- Section: Change Category -->
                    <div id="section-change_category" class="bulk-section">
                        <label class="form-label fw-semibold">Chọn danh mục mới:</label>
                        <select name="bulk_category_id" class="form-select">
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>"><?= e($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Section: Change Status -->
                    <div id="section-change_status" class="bulk-section d-none">
                        <label class="form-label fw-semibold">Chọn trạng thái mới:</label>
                        <select name="bulk_status" class="form-select">
                            <option value="active">🟢 Đang bán (Hiển thị công khai)</option>
                            <option value="inactive">⚪ Ẩn (Không hiển thị)</option>
                        </select>
                    </div>

                    <!-- Section: Update Price -->
                    <div id="section-update_price" class="bulk-section d-none">
                        <label class="form-label fw-semibold">Cách thức cập nhật giá:</label>
                        <select name="price_action" id="price-action-select" class="form-select mb-3">
                            <option value="set">Thiết lập giá mới</option>
                            <option value="adjust_percent">Tăng / Giảm theo phần trăm (%)</option>
                            <option value="adjust_amount">Tăng / Giảm theo số tiền cụ thể (đ)</option>
                        </select>
                        <label class="form-label fw-semibold" id="price-value-label">Giá trị mới (đ):</label>
                        <input type="text" name="price_value" class="form-control" placeholder="Ví dụ: 250000 hoặc -15 (giảm 15%)">
                    </div>

                    <!-- Section: Update Stock -->
                    <div id="section-update_stock" class="bulk-section d-none">
                        <label class="form-label fw-semibold">Cách thức cập nhật tồn kho:</label>
                        <select name="stock_action" id="stock-action-select" class="form-select mb-3">
                            <option value="set">Thiết lập số tồn kho mới</option>
                            <option value="adjust">Cộng / Trừ thêm số lượng</option>
                        </select>
                        <label class="form-label fw-semibold" id="stock-value-label">Số lượng:</label>
                        <input type="number" name="stock_value" class="form-control" placeholder="Ví dụ: 50 hoặc -10">
                    </div>

                    <!-- Section: Update Promo -->
                    <div id="section-update_promo" class="bulk-section d-none">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="bulk_promo_active" id="bulk_promo_active" style="cursor: pointer;">
                            <label class="form-check-label fw-semibold" for="bulk_promo_active">Kích hoạt Flash Sale / Khuyến mãi</label>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Mức giảm giá sản phẩm (%):</label>
                            <input type="number" name="bulk_discount_percent" min="0" max="100" class="form-control" placeholder="Ví dụ: 10 (giảm 10%)">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light p-3 border-0">
                    <button type="button" class="btn btn-outline-secondary rounded-3" data-bs-dismiss="modal">Hủy bỏ</button>
                    <button type="submit" class="btn btn-success rounded-3 px-4 fw-bold" id="confirm-bulk-edit-btn"><i class="bi bi-check-lg me-1"></i>Cập nhật ngay</button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkAll = document.getElementById('check-all');
    const productCheckboxes = document.querySelectorAll('.product-select');
    const bulkActionBar = document.getElementById('bulk-action-bar');
    const selectedCount = document.getElementById('selected-count');
    const modalSelectedCount = document.getElementById('modal-selected-count');
    const deselectAllBtn = document.getElementById('deselect-all-btn');
    const bulkDeleteTrigger = document.getElementById('bulk-delete-trigger');
    const bulkEditForm = document.getElementById('bulk-edit-form');
    const filterStatusSelect = document.getElementById('filter-status-select');
    
    // Modal selectors
    const attributeSelector = document.getElementById('bulk-attribute-selector');
    const bulkActionInput = document.getElementById('bulk-action-input');
    const sections = document.querySelectorAll('.bulk-section');
    
    // Price / Stock Dynamic Sublabels
    const priceActionSelect = document.getElementById('price-action-select');
    const priceValueLabel = document.getElementById('price-value-label');
    const stockActionSelect = document.getElementById('stock-action-select');
    const stockValueLabel = document.getElementById('stock-value-label');

    // Filter status client-side
    if (filterStatusSelect) {
        filterStatusSelect.addEventListener('change', function () {
            const val = filterStatusSelect.value;
            const rows = document.querySelectorAll('.product-row-item');
            rows.forEach(r => {
                const st = r.getAttribute('data-status');
                if (val === 'all' || st === val) {
                    r.style.display = '';
                } else {
                    r.style.display = 'none';
                }
            });
        });
    }

    // Update floating bar state
    function updateActionBar() {
        const checked = document.querySelectorAll('.product-select:checked');
        const count = checked.length;
        
        if (count > 0) {
            selectedCount.textContent = count;
            modalSelectedCount.textContent = count;
            bulkActionBar.classList.remove('d-none');
        } else {
            bulkActionBar.classList.add('d-none');
        }
        
        checkAll.checked = (count === productCheckboxes.length && productCheckboxes.length > 0);
    }

    // Check All handler
    checkAll.addEventListener('change', function () {
        productCheckboxes.forEach(cb => {
            cb.checked = checkAll.checked;
        });
        updateActionBar();
    });

    // Individual checkbox handler
    productCheckboxes.forEach(cb => {
        cb.addEventListener('change', function () {
            updateActionBar();
        });
    });

    // Row click handler
    const productRows = document.querySelectorAll('.product-row-item');
    productRows.forEach(row => {
        row.addEventListener('click', function(e) {
            if (e.target.tagName !== 'INPUT' && e.target.tagName !== 'A' && e.target.tagName !== 'BUTTON' && !e.target.closest('button') && !e.target.closest('a')) {
                const cb = row.querySelector('.product-select');
                if (cb) {
                    cb.checked = !cb.checked;
                    updateActionBar();
                }
            }
        });
    });

    // Deselect All button
    deselectAllBtn.addEventListener('click', function () {
        productCheckboxes.forEach(cb => {
            cb.checked = false;
        });
        updateActionBar();
    });

    // Bulk Delete Trigger
    bulkDeleteTrigger.addEventListener('click', function () {
        const checkedCount = document.querySelectorAll('.product-select:checked').length;
        if (confirm(`Bạn chắc chắn muốn xóa ${checkedCount} sản phẩm đã chọn? Hành động này không thể hoàn tác!`)) {
            bulkActionInput.value = 'delete';
            bulkEditForm.submit();
        }
    });

    // Attribute Selector Change inside Modal
    attributeSelector.addEventListener('change', function () {
        const selectedValue = attributeSelector.value;
        bulkActionInput.value = selectedValue;

        sections.forEach(sec => {
            sec.classList.add('d-none');
        });
        const activeSection = document.getElementById('section-' + selectedValue);
        if (activeSection) {
            activeSection.classList.remove('d-none');
        }
    });

    // Dynamic Price Sublabels
    priceActionSelect.addEventListener('change', function () {
        const val = priceActionSelect.value;
        if (val === 'set') {
            priceValueLabel.textContent = 'Giá bán mới (đ):';
        } else if (val === 'adjust_percent') {
            priceValueLabel.textContent = 'Số phần trăm tăng hoặc giảm (Ví dụ: -10 để giảm 10%, 15 để tăng 15%):';
        } else {
            priceValueLabel.textContent = 'Số tiền tăng hoặc giảm (Ví dụ: -50000 để giảm 50k, 20000 để tăng 20k):';
        }
    });

    // Dynamic Stock Sublabels
    stockActionSelect.addEventListener('change', function () {
        const val = stockActionSelect.value;
        if (val === 'set') {
            stockValueLabel.textContent = 'Số lượng tồn kho mới:';
        } else {
            stockValueLabel.textContent = 'Số lượng tăng hoặc giảm (Ví dụ: 10 để cộng thêm 10, -5 để giảm đi 5):';
        }
    });
});
</script>
