<?php
$isEdit = !empty($product);
$p = $product ?? [];
?>

<h2 class="mb-4"><?= $isEdit ? 'Sửa sản phẩm' : 'Thêm sản phẩm' ?></h2>

<div class="card">
    <div class="card-body">
        <form method="post" enctype="multipart/form-data"
              action="<?= $isEdit ? '/admin/products/' . $product['id'] . '/update' : '/admin/products' ?>">
            <?= csrf_field() ?>
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Tên sản phẩm *</label>
                    <input type="text" name="name" class="form-control" value="<?= old('name', $p['name'] ?? '') ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Danh mục *</label>
                    <select name="category_id" class="form-select" required>
                        <option value="">-- Chọn danh mục --</option>
                        <?php
                        $selectedCat = old('category_id', (string) ($p['category_id'] ?? ''));
                        foreach ($categories as $cat):
                        ?>
                            <option value="<?= $cat['id'] ?>" <?= (string) $cat['id'] === $selectedCat ? 'selected' : '' ?>>
                                <?= e($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Giá gốc (VNĐ) *</label>
                    <input type="number" name="price" class="form-control" min="1" step="1"
                           placeholder="VD: 22000000"
                           value="<?= old('price', (string) ($p['price'] ?? '')) ?>" required>
                    <div class="form-text">Giá niêm yết trước khi giảm</div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Ưu đãi riêng sản phẩm</label>
                    <?php $promoActive = old('promo_active', !empty($p['promo_active']) ? '1' : ''); ?>
                    <div class="form-check mt-2">
                        <input type="checkbox" name="promo_active" id="promo_active" class="form-check-input" value="1"
                            <?= $promoActive ? 'checked' : '' ?>>
                        <label for="promo_active" class="form-check-label">Bật giảm giá riêng</label>
                    </div>
                    <input type="number" name="discount_percent" class="form-control mt-2" min="1" max="100"
                           placeholder="VD: 15"
                           value="<?= old('discount_percent', (string) ($p['discount_percent'] ?? '')) ?>">
                    <div class="form-text">Chỉ áp dụng khi bật checkbox. Nếu ưu đãi chung cao hơn sẽ dùng mức chung.</div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tồn kho *</label>
                    <input type="number" name="stock" class="form-control" min="0"
                           value="<?= old('stock', (string) ($p['stock'] ?? '0')) ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Trạng thái</label>
                    <?php $status = old('status', $p['status'] ?? 'active'); ?>
                    <select name="status" class="form-select">
                        <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Đang bán</option>
                        <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>Ẩn</option>
                    </select>
                </div>

                <div class="col-12"><hr class="my-1"><h6 class="fw-bold text-primary mb-0">Thông số kỹ thuật (điện thoại)</h6></div>
                <div class="col-md-2">
                    <label class="form-label">RAM</label>
                    <select name="ram" class="form-select">
                        <?php $ram = old('ram', $p['ram'] ?? ''); ?>
                        <option value="">--</option>
                        <?php foreach (['4GB','6GB','8GB','12GB','16GB'] as $opt): ?>
                            <option value="<?= $opt ?>" <?= $ram === $opt ? 'selected' : '' ?>><?= $opt ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Bộ nhớ</label>
                    <select name="storage_gb" class="form-select">
                        <?php $stg = old('storage_gb', $p['storage_gb'] ?? ''); ?>
                        <option value="">--</option>
                        <?php foreach (['64GB','128GB','256GB','512GB','1TB'] as $opt): ?>
                            <option value="<?= $opt ?>" <?= $stg === $opt ? 'selected' : '' ?>><?= $opt ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Chipset</label>
                    <input type="text" name="chipset" class="form-control" placeholder="VD: Snapdragon 8 Gen 3"
                           value="<?= old('chipset', $p['chipset'] ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Màn hình</label>
                    <input type="text" name="screen_size" class="form-control" placeholder="6.7&quot;"
                           value="<?= old('screen_size', $p['screen_size'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Pin (mAh)</label>
                    <input type="text" name="battery_mah" class="form-control" placeholder="5000"
                           value="<?= old('battery_mah', $p['battery_mah'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Hệ điều hành</label>
                    <input type="text" name="os" class="form-control" placeholder="Android 15 / iOS 18"
                           value="<?= old('os', $p['os'] ?? '') ?>">
                </div>

                <div class="col-12"><hr class="my-1"><h6 class="fw-bold text-danger mb-0">Đặt trước (Pre-order)</h6></div>
                <div class="col-md-4">
                    <?php $isPre = old('is_preorder', !empty($p['is_preorder']) ? '1' : ''); ?>
                    <div class="form-check mt-4">
                        <input type="checkbox" name="is_preorder" id="is_preorder" class="form-check-input" value="1" <?= $isPre ? 'checked' : '' ?>>
                        <label for="is_preorder" class="form-check-label fw-semibold">Bật đặt trước / sắp về hàng</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tiền cọc pre-order (VNĐ)</label>
                    <input type="number" name="preorder_deposit" class="form-control" min="0"
                           value="<?= old('preorder_deposit', (string) ($p['preorder_deposit'] ?? '0')) ?>">
                    <div class="form-text">Khách chỉ thanh toán số tiền cọc khi đặt trước.</div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Mô tả ngắn</label>
                    <textarea name="description" class="form-control" rows="3"><?= old('description', $p['description'] ?? '') ?></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Mô tả chi tiết</label>
                    <textarea name="detail" class="form-control" rows="3"><?= old('detail', $p['detail'] ?? '') ?></textarea>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Upload hình ảnh <?= $isEdit ? '' : '*' ?></label>
                    <input type="file" name="image_file" class="form-control" accept="image/jpeg,image/png,image/webp,image/gif">
                    <div class="form-text">JPG, PNG, WEBP, GIF — lưu vào thư mục <code>image/</code></div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Hoặc đường dẫn ảnh</label>
                    <input type="text" name="image" class="form-control" placeholder="/image/ten_anh.webp"
                           value="<?= old('image', $p['image'] ?? '') ?>">
                    <div class="form-text">VD: <code>/image/ip15.webp</code></div>
                </div>

                <?php if ($isEdit && !empty($product['image'])): ?>
                    <div class="col-12">
                        <label class="form-label">Ảnh hiện tại</label><br>
                        <img src="<?= e(imageUrl($product['image'])) ?>" alt="" style="max-height:120px;border-radius:8px;border:1px solid #ddd">
                    </div>
                <?php endif; ?>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Cập nhật' : 'Thêm mới' ?></button>
                <a href="/admin/products" class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>
</div>

<?php if ($isEdit): ?>
    <div class="card mt-4 border rounded-3 shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center py-3">
            <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-tags me-2 text-primary"></i>Quản lý biến thể (Màu sắc × Dung lượng)</h5>
            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="collapse" data-bs-target="#addVariantForm">
                <i class="bi bi-plus-lg me-1"></i>Thêm biến thể mới
            </button>
        </div>
        <div class="card-body">
            <!-- Add Variant Collapse Form -->
            <div class="collapse mb-4 p-3 bg-light border rounded-3" id="addVariantForm">
                <h6 class="fw-bold mb-3"><i class="bi bi-plus-circle-fill text-primary me-1"></i>Thêm biến thể mới</h6>
                <form action="/admin/products/<?= $product['id'] ?>/variants" method="POST">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Màu sắc *</label>
                            <input type="text" name="color" class="form-control form-control-sm" placeholder="VD: Titan tự nhiên" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Dung lượng *</label>
                            <input type="text" name="storage" class="form-control form-control-sm" placeholder="VD: 256GB" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Giá bán override (VNĐ) *</label>
                            <input type="number" name="price" class="form-control form-control-sm" placeholder="VD: 27990000" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Tồn kho *</label>
                            <input type="number" name="stock" class="form-control form-control-sm" min="0" placeholder="VD: 10" required>
                        </div>
                        <div class="col-12 text-end">
                            <button type="button" class="btn btn-sm btn-secondary me-2" data-bs-toggle="collapse" data-bs-target="#addVariantForm">Hủy</button>
                            <button type="submit" class="btn btn-sm btn-primary px-3">Lưu biến thể</button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Variants Table -->
            <?php if (empty($variants)): ?>
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-info-circle me-1"></i>Sản phẩm chưa có biến thể nào. Giá và tồn kho mặc định sẽ được sử dụng.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Màu sắc</th>
                                <th>Dung lượng</th>
                                <th>Giá override</th>
                                <th>Tồn kho</th>
                                <th>Trạng thái</th>
                                <th class="text-end">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($variants as $v): ?>
                                <tr>
                                    <form action="/admin/variants/<?= $v['id'] ?>/update" method="POST" class="d-inline">
                                        <td>
                                            <input type="text" name="color" class="form-control form-control-sm font-monospace" value="<?= htmlspecialchars($v['color']) ?>" required>
                                        </td>
                                        <td>
                                            <input type="text" name="storage" class="form-control form-control-sm font-monospace" value="<?= htmlspecialchars($v['storage']) ?>" required>
                                        </td>
                                        <td>
                                            <input type="number" name="price" class="form-control form-control-sm font-monospace" value="<?= (int)$v['price'] ?>" required>
                                        </td>
                                        <td>
                                            <input type="number" name="stock" class="form-control form-control-sm font-monospace" value="<?= (int)$v['stock'] ?>" min="0" required>
                                        </td>
                                        <td>
                                            <select name="status" class="form-select form-select-sm">
                                                <option value="active" <?= $v['status'] === 'active' ? 'selected' : '' ?>>Kích hoạt</option>
                                                <option value="inactive" <?= $v['status'] === 'inactive' ? 'selected' : '' ?>>Khóa</option>
                                            </select>
                                        </td>
                                        <td class="text-end">
                                            <button type="submit" class="btn btn-sm btn-success me-1" title="Cập nhật">
                                                <i class="bi bi-save"></i>
                                            </button>
                                            <button type="submit" formaction="/admin/variants/<?= (int) $v['id'] ?>/delete" formmethod="post"
                                                    class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc muốn xóa biến thể này?')" title="Xóa">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </form>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>
