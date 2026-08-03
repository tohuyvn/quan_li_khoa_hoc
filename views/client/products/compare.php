<?php $title = 'So sánh sản phẩm'; ?>

<style>
    .compare-container {
        max-width: 1200px;
        margin: 0 auto;
    }
    .compare-header-row {
        background: var(--bg-card);
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        border: 1px solid rgba(0,0,0,0.08);
        overflow: hidden;
    }
    .compare-prod-card {
        transition: transform 0.2s, box-shadow 0.2s;
        height: 100%;
        position: relative;
    }
    .compare-prod-card:hover {
        transform: translateY(-4px);
    }
    .remove-compare-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        background: rgba(239, 68, 68, 0.1);
        color: #ef4444;
        border: none;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background 0.2s, color 0.2s;
        z-index: 10;
    }
    .remove-compare-btn:hover {
        background: #ef4444;
        color: white;
    }
    .compare-table {
        margin-top: 30px;
        background: var(--bg-card);
        border-radius: 16px;
        border: 1px solid rgba(0,0,0,0.08);
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        overflow: hidden;
        width: 100%;
        border-collapse: collapse;
    }
    .compare-table th, .compare-table td {
        padding: 16px 20px;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        vertical-align: middle;
    }
    .compare-table tr:last-child th, .compare-table tr:last-child td {
        border-bottom: none;
    }
    .compare-table th {
        background: rgba(0,0,0,0.02);
        font-weight: 700;
        color: var(--text-color);
        width: 20%;
    }
    .compare-table td {
        width: 26.66%;
    }
    .compare-search-results {
        max-height: 250px;
        overflow-y: auto;
        z-index: 1000;
        border: 1px solid rgba(0,0,0,0.1);
    }
    .compare-search-item {
        padding: 10px 12px;
        cursor: pointer;
        transition: background 0.2s;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .compare-search-item:hover {
        background: rgba(0,0,0,0.04);
    }
    .spec-group-title {
        background: rgba(0,0,0,0.04) !important;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 13px;
        color: var(--accent);
    }
</style>

<div class="compare-container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-shuffle me-2 text-primary"></i>So sánh sản phẩm</h2>
            <p class="text-muted mb-0">So sánh trực quan thông số kỹ thuật của tối đa 3 sản phẩm</p>
        </div>
        <a href="/" class="btn btn-outline-secondary rounded-pill btn-sm px-4"><i class="bi bi-arrow-left"></i> Trở về shop</a>
    </div>

    <!-- Product Columns Card List -->
    <div class="row g-4 compare-header-row p-4 align-items-stretch">
        <!-- Spec label column (hidden on mobile, placeholder on desktop) -->
        <div class="col-lg-3 d-none d-lg-flex flex-column justify-content-center text-start">
            <h4 class="fw-bold mb-2 text-primary">Hades Shop</h4>
            <p class="small text-muted mb-0">Hãy thêm các thiết bị cần so sánh thông số chi tiết cạnh nhau.</p>
        </div>

        <?php for ($i = 0; $i < 3; $i++): ?>
            <div class="col-md-4 col-lg-3">
                <?php if (isset($products[$i])): ?>
                    <?php $prod = $products[$i]; ?>
                    <div class="compare-prod-card card border-0 h-100 p-3 text-center bg-transparent">
                        <button type="button" class="remove-compare-btn" onclick="removeIdFromCompare(<?= $prod['id'] ?>)" title="Xóa khỏi danh sách">
                            <i class="bi bi-x"></i>
                        </button>
                        <a href="/products/<?= $prod['id'] ?>" class="d-block mb-3">
                            <img src="<?= e(imageUrl($prod['image'] ?? '')) ?>" alt="<?= e($prod['name']) ?>" class="img-fluid rounded-3" style="max-height: 150px; object-fit: contain;">
                        </a>
                        <h6 class="fw-bold text-dark text-truncate-2 mb-2" style="min-height: 40px; font-size: 14px;">
                            <a href="/products/<?= $prod['id'] ?>" class="text-decoration-none text-dark hover-text-primary"><?= e($prod['name']) ?></a>
                        </h6>
                        <div class="text-danger fw-bold mb-3"><?= formatMoney($prod['sale_price']) ?></div>
                        <div class="mt-auto d-grid gap-2">
                            <a href="/products/<?= $prod['id'] ?>" class="btn btn-sm btn-outline-primary rounded-pill">Xem chi tiết</a>
                            <form method="post" action="/cart/add/<?= $prod['id'] ?>" class="d-inline">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-primary-hades w-100" style="border-radius: var(--radius-sm);"><i class="bi bi-cart-plus me-1"></i> Thêm giỏ</button>
                            </form>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Add product slot -->
                    <div class="card border border-dashed rounded-4 p-4 h-100 d-flex flex-column justify-content-center align-items-center text-center bg-light" style="border-width: 2px; min-height: 250px;">
                        <div class="fs-1 text-muted mb-3"><i class="bi bi-plus-circle-dotted"></i></div>
                        <h6 class="fw-bold mb-1">Thêm sản phẩm</h6>
                        <p class="text-muted small mb-3">Chọn thiết bị so sánh</p>
                        <div class="position-relative w-100">
                            <input type="text" class="form-control form-control-sm compare-search-input" placeholder="Tìm sản phẩm..." oninput="searchProductToCompare(this)">
                            <div class="compare-search-results d-none position-absolute w-100 bg-white border rounded shadow-sm z-3 text-start"></div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endfor; ?>
    </div>

    <!-- Specs Comparison Table -->
    <?php if (!empty($products)): ?>
        <div class="table-responsive">
            <table class="compare-table">
                <thead>
                    <tr>
                        <th class="spec-group-title" colspan="4">Thông tin cơ bản</th>
                    </tr>
                    <tr>
                        <th>Sản phẩm</th>
                        <?php for ($i = 0; $i < 3; $i++): ?>
                            <td>
                                <?php if (isset($products[$i])): ?>
                                    <strong class="text-dark"><?= e($products[$i]['name']) ?></strong>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                        <?php endfor; ?>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th>Giá ưu đãi</th>
                        <?php for ($i = 0; $i < 3; $i++): ?>
                            <td>
                                <?php if (isset($products[$i])): ?>
                                    <span class="text-danger fw-bold"><?= formatMoney($products[$i]['sale_price']) ?></span>
                                    <?php if ($products[$i]['has_discount']): ?>
                                        <span class="text-muted text-decoration-line-through small ms-2"><?= formatMoney($products[$i]['original_price']) ?></span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                        <?php endfor; ?>
                    </tr>
                    <tr>
                        <th>Danh mục</th>
                        <?php for ($i = 0; $i < 3; $i++): ?>
                            <td>
                                <?php if (isset($products[$i])): ?>
                                    <span class="badge bg-light text-dark border"><?= e($products[$i]['category_name']) ?></span>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                        <?php endfor; ?>
                    </tr>
                    <tr>
                        <th>Tồn kho</th>
                        <?php for ($i = 0; $i < 3; $i++): ?>
                            <td>
                                <?php if (isset($products[$i])): ?>
                                    <?= (int)$products[$i]['stock'] > 0 ? '<span class="text-success"><i class="bi bi-check-circle-fill me-1"></i>Còn hàng ('.$products[$i]['stock'].')</span>' : '<span class="text-danger"><i class="bi bi-x-circle-fill me-1"></i>Hết hàng</span>' ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                        <?php endfor; ?>
                    </tr>

                    <tr>
                        <th class="spec-group-title" colspan="4">Thông số kỹ thuật</th>
                    </tr>

                    <?php foreach ($allSpecKeys as $specKey): ?>
                        <tr>
                            <th><?= e($specKey) ?></th>
                            <?php for ($i = 0; $i < 3; $i++): ?>
                                <td>
                                    <?php if (isset($products[$i])): ?>
                                        <?= e($products[$i]['specs'][$specKey] ?? '-') ?>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                            <?php endfor; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="card p-5 text-center mt-4 rounded-4 bg-light border-0">
            <div class="fs-1 text-muted mb-3"><i class="bi bi-shuffle"></i></div>
            <h4 class="fw-bold">Chưa chọn sản phẩm so sánh</h4>
            <p class="text-muted mx-auto" style="max-width: 400px;">Hãy tìm kiếm và thêm các sản phẩm ở trên hoặc từ trang chủ để bắt đầu so sánh thông số kỹ thuật.</p>
        </div>
    <?php endif; ?>
</div>

<script>
const allProductsForCompare = <?= json_encode($allProducts) ?>;
const comparedProducts = <?= json_encode(array_map(fn($p) => [
    'id' => (int)$p['id'],
    'name' => $p['name'],
    'image' => imageUrl($p['image'] ?? '', 'thumb')
], $products)) ?>;

// Sync sessionStorage list on load of compare page
sessionStorage.setItem('hades_compare_list', JSON.stringify(comparedProducts));

function getCompareIds() {
    const urlParams = new URLSearchParams(window.location.search);
    const idsString = urlParams.get('ids') || '';
    return idsString.split(',').map(Number).filter(id => id > 0);
}

function updateCompareQuery(ids) {
    if (ids.length === 0) {
        window.location.href = '/compare';
    } else {
        window.location.search = '?ids=' + ids.join(',');
    }
}

function addIdToCompare(id) {
    const ids = getCompareIds();
    if (!ids.includes(id)) {
        if (ids.length >= 3) {
            if (window.showToast) {
                showToast('warning', 'Chỉ so sánh tối đa 3 sản phẩm cùng lúc.');
            } else {
                alert('Chỉ so sánh tối đa 3 sản phẩm cùng lúc.');
            }
            return;
        }
        ids.push(id);
        updateCompareQuery(ids);
    }
}

function removeIdFromCompare(id) {
    const ids = getCompareIds().filter(x => x !== id);
    updateCompareQuery(ids);
}

function searchProductToCompare(input) {
    const query = input.value.trim().toLowerCase();
    const resultBox = input.nextElementSibling;
    if (query === '') {
        resultBox.classList.add('d-none');
        resultBox.innerHTML = '';
        return;
    }
    
    const currentIds = getCompareIds();
    const matches = allProductsForCompare.filter(p => 
        p.name.toLowerCase().includes(query) && !currentIds.includes(parseInt(p.id))
    );
    
    if (matches.length === 0) {
        resultBox.innerHTML = '<div class="p-3 text-muted text-center small">Không tìm thấy sản phẩm</div>';
    } else {
        let html = '';
        matches.forEach(p => {
            const imgPath = p.image ? '/uploads/' + p.image : '/image/no-image.webp';
            html += `
                <div class="compare-search-item border-bottom d-flex align-items-center" onclick="addIdToCompare(${p.id})">
                    <img src="${imgPath}" style="width:36px;height:36px;object-fit:cover;border-radius:6px;border:1px solid #eee;">
                    <div>
                        <div class="fw-bold small text-dark" style="font-size:12px;">${p.name}</div>
                        <div class="text-primary small" style="font-size:11px;">${p.formatted_price}</div>
                    </div>
                </div>
            `;
        });
        resultBox.innerHTML = html;
    }
    resultBox.classList.remove('d-none');
}

// Hide dropdown when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.classList.contains('compare-search-input')) {
        document.querySelectorAll('.compare-search-results').forEach(box => {
            box.classList.add('d-none');
        });
    }
});
</script>
