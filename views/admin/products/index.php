<h2 class="mb-4">Quản lý sản phẩm</h2>

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <a href="/admin/products/create" class="btn btn-primary">+ Thêm sản phẩm</a>
    
    <form action="/admin/products" method="GET" class="d-flex gap-2 align-items-center" style="max-width: 400px; width: 100%;">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Tìm theo tên, danh mục hoặc ID..." value="<?= e($search ?? '') ?>">
            <?php if (!empty($search)): ?>
                <a href="/admin/products" class="btn btn-outline-secondary" title="Xóa tìm kiếm">✕</a>
            <?php endif; ?>
            <button class="btn btn-outline-primary" type="submit">Tìm kiếm</button>
        </div>
    </form>
</div>

<!-- Bulk Actions Form -->
<form id="bulk-edit-form" action="/admin/products/bulk-action" method="POST">
    <div class="table-responsive">
        <table class="table table-striped table-bordered align-middle">
            <thead class="table-dark">
                <tr>
                    <th width="40" class="text-center">
                        <input type="checkbox" class="form-check-input" id="check-all" style="cursor: pointer;">
                    </th>
                    <th width="60">ID</th>
                    <th width="80">Ảnh</th>
                    <th>Tên sản phẩm</th>
                    <th>Danh mục</th>
                    <th width="120">Giá gốc</th>
                    <th width="120">Giá bán</th>
                    <th width="90">Tồn kho</th>
                    <th width="100">Trạng thái</th>
                    <th width="120" class="text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($products)): ?>
                    <tr><td colspan="10" class="text-center">Chưa có sản phẩm</td></tr>
                <?php else: ?>
                    <?php
                    $globalDiscount = \App\PriceCalculator::getGlobalDiscountPercent();
                    foreach ($products as $product):
                        $priced = \App\PriceCalculator::resolve($product, $globalDiscount);
                    ?>
                        <tr class="product-row" data-id="<?= $product['id'] ?>">
                            <td class="text-center">
                                <input type="checkbox" name="product_ids[]" value="<?= $product['id'] ?>" class="form-check-input product-select" style="cursor: pointer;">
                            </td>
                            <td><?= $product['id'] ?></td>
                            <td>
                                <img src="<?= e(imageUrl($product['image'] ?? '')) ?>" alt=""
                                     style="width:56px;height:56px;object-fit:cover;border-radius:6px;border:1px solid #eee">
                            </td>
                            <td>
                                <strong><?= e($product['name']) ?></strong>
                                <?php if (!empty($product['description'])): ?>
                                    <div class="small text-muted"><?= e(mb_strimwidth($product['description'], 0, 60, '...')) ?></div>
                                <?php endif; ?>
                            </td>
                            <td><?= e($product['category_name']) ?></td>
                            <td><?= formatMoney($priced['original_price']) ?></td>
                            <td>
                                <?= formatMoney($priced['sale_price']) ?>
                                <?php if ($priced['has_discount']): ?>
                                    <div class="small text-danger">-<?= (int) $priced['discount_percent'] ?>%
                                        (<?= $priced['discount_source'] === 'global' ? 'chung' : 'SP' ?>)
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td><?= (int) $product['stock'] ?></td>
                            <td>
                                <span class="badge bg-<?= ($product['status'] ?? '') === 'active' ? 'success' : 'secondary' ?>">
                                    <?= ($product['status'] ?? '') === 'active' ? 'Đang bán' : 'Ẩn' ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="/admin/products/<?= $product['id'] ?>/edit" class="btn btn-sm btn-warning">Sửa</a>
                                <form method="post" action="/admin/products/<?= (int) $product['id'] ?>/delete" class="d-inline"
                                      onsubmit="return confirm('Xóa sản phẩm này?')">
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

    <!-- Sticky Floating Bulk Action Bar -->
    <div id="bulk-action-bar" class="d-none position-fixed bottom-0 start-50 translate-middle-x bg-dark text-white p-3 rounded-top shadow-lg border border-secondary" style="z-index: 1050; min-width: 600px; max-width: 90%;">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <span class="fw-bold fs-5 text-warning" id="selected-count">0</span> sản phẩm đang được chọn
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm btn-outline-light" id="deselect-all-btn">Bỏ chọn tất cả</button>
                <button type="button" class="btn btn-sm btn-danger" id="bulk-delete-trigger">Xóa hàng loạt</button>
                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#bulkEditModal">
                    ⚡ Chỉnh sửa hàng loạt
                </button>
            </div>
        </div>
    </div>

    <!-- Bulk Edit Modal -->
    <div class="modal fade" id="bulkEditModal" tabindex="-1" aria-labelledby="bulkEditModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content bg-dark text-white border-secondary">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title" id="bulkEditModalLabel">⚡ Chỉnh sửa <span id="modal-selected-count">0</span> sản phẩm</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Hidden field for action -->
                    <input type="hidden" name="bulk_action" id="bulk-action-input" value="change_category">

                    <div class="mb-3">
                        <label class="form-label text-info fw-bold">Chọn thuộc tính muốn thay đổi:</label>
                        <select class="form-select bg-secondary text-white border-secondary" id="bulk-attribute-selector" style="cursor: pointer;">
                            <option value="change_category" selected>📁 Thay đổi Danh mục</option>
                            <option value="change_status">👁️ Thay đổi Trạng thái</option>
                            <option value="update_price">💰 Điều chỉnh Giá bán</option>
                            <option value="update_stock">📦 Điều chỉnh Số lượng Tồn kho</option>
                            <option value="update_promo">🔥 Cấu hình Flash Sale / Khuyến mãi</option>
                        </select>
                    </div>

                    <hr class="border-secondary">

                    <!-- Dynamic sections based on selected attribute -->
                    
                    <!-- Section: Change Category -->
                    <div id="section-change_category" class="bulk-section">
                        <label class="form-label">Chọn danh mục mới:</label>
                        <select name="bulk_category_id" class="form-select bg-secondary text-white border-secondary">
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>"><?= e($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Section: Change Status -->
                    <div id="section-change_status" class="bulk-section d-none">
                        <label class="form-label">Chọn trạng thái hiển thị mới:</label>
                        <select name="bulk_status" class="form-select bg-secondary text-white border-secondary">
                            <option value="active">Đang bán (Hiển thị công khai)</option>
                            <option value="inactive">Ẩn (Không hiển thị)</option>
                        </select>
                    </div>

                    <!-- Section: Update Price -->
                    <div id="section-update_price" class="bulk-section d-none">
                        <label class="form-label">Cách thức cập nhật giá:</label>
                        <select name="price_action" id="price-action-select" class="form-select bg-secondary text-white border-secondary mb-3">
                            <option value="set">Thiết lập giá mới</option>
                            <option value="adjust_percent">Tăng / Giảm theo phần trăm (%)</option>
                            <option value="adjust_amount">Tăng / Giảm theo số tiền cụ thể (đ)</option>
                        </select>
                        <label class="form-label" id="price-value-label">Giá trị mới (đ):</label>
                        <input type="text" name="price_value" class="form-control bg-secondary text-white border-secondary" placeholder="Ví dụ: 250000 hoặc -15 (giảm 15%)">
                    </div>

                    <!-- Section: Update Stock -->
                    <div id="section-update_stock" class="bulk-section d-none">
                        <label class="form-label">Cách thức cập nhật tồn kho:</label>
                        <select name="stock_action" id="stock-action-select" class="form-select bg-secondary text-white border-secondary mb-3">
                            <option value="set">Thiết lập số tồn kho mới</option>
                            <option value="adjust">Cộng / Trừ thêm số lượng</option>
                        </select>
                        <label class="form-label" id="stock-value-label">Số lượng:</label>
                        <input type="number" name="stock_value" class="form-control bg-secondary text-white border-secondary" placeholder="Ví dụ: 50 hoặc -10">
                    </div>

                    <!-- Section: Update Promo -->
                    <div id="section-update_promo" class="bulk-section d-none">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="bulk_promo_active" id="bulk_promo_active" style="cursor: pointer;">
                            <label class="form-check-label" for="bulk_promo_active">Kích hoạt chương trình Khuyến mãi / Flash Sale</label>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mức giảm giá sản phẩm (%):</label>
                            <input type="number" name="bulk_discount_percent" min="0" max="100" class="form-control bg-secondary text-white border-secondary" placeholder="Ví dụ: 10 (giảm 10%)">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Hủy bỏ</button>
                    <button type="submit" class="btn btn-success" id="confirm-bulk-edit-btn">Cập nhật ngay</button>
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
    
    // Modal selectors
    const attributeSelector = document.getElementById('bulk-attribute-selector');
    const bulkActionInput = document.getElementById('bulk-action-input');
    const sections = document.querySelectorAll('.bulk-section');
    
    // Price / Stock Dynamic Sublabels
    const priceActionSelect = document.getElementById('price-action-select');
    const priceValueLabel = document.getElementById('price-value-label');
    const stockActionSelect = document.getElementById('stock-action-select');
    const stockValueLabel = document.getElementById('stock-value-label');

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

    // Row click handler (clicks checkbox if click happens on row, except on buttons/links/checkbox itself)
    const productRows = document.querySelectorAll('.product-row');
    productRows.forEach(row => {
        row.addEventListener('click', function(e) {
            if (e.target.tagName !== 'INPUT' && e.target.tagName !== 'A' && e.target.tagName !== 'BUTTON') {
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

        // Hide all sections, then show the matching one
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
