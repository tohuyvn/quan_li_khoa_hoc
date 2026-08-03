<h2 class="mb-4">Yêu cầu Bảo hành & Sửa chữa</h2>

<div class="mb-3 d-flex gap-2">
    <a href="/admin/warranties" class="btn btn-outline-secondary">
        <i class="bi bi-shield-check me-1"></i>Xem danh sách bảo hành
    </a>
</div>

<div class="card border rounded-3 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th width="70" class="ps-3">ID</th>
                        <th>Sản phẩm</th>
                        <th>Khách hàng</th>
                        <th>Mã BH</th>
                        <th>Yêu cầu của khách</th>
                        <th>Trạng thái</th>
                        <th>Ngày yêu cầu</th>
                        <th width="150" class="pe-3 text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($claims)): ?>
                        <tr><td colspan="8" class="text-center py-4 text-muted">Chưa có yêu cầu sửa chữa/bảo hành nào</td></tr>
                    <?php else: ?>
                        <?php foreach ($claims as $c): ?>
                            <tr>
                                <td class="ps-3 fw-bold"><?= $c['id'] ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="<?= e(imageUrl($c['product_image'] ?? '', 'thumb')) ?>"
                                             alt="<?= e($c['product_name'] ?? '') ?>"
                                             style="width:36px;height:36px;object-fit:contain;border-radius:6px;border:1px solid #dee2e6;background:#fff;flex-shrink:0"
                                             loading="lazy"
                                             onerror="this.onerror=null;this.src='/image/logo.webp';">
                                        <div class="fw-semibold text-dark"><?= htmlspecialchars($c['product_name']) ?></div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold"><?= htmlspecialchars($c['customer_name']) ?></div>
                                    <div class="text-muted small"><?= htmlspecialchars($c['customer_phone']) ?></div>
                                </td>
                                <td>
                                    <div class="font-monospace text-primary fw-semibold"><?= htmlspecialchars($c['warranty_code']) ?></div>
                                </td>
                                <td>
                                    <div class="text-truncate" style="max-width: 250px;" title="<?= htmlspecialchars($c['customer_note']) ?>">
                                        <?= htmlspecialchars($c['customer_note']) ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($c['status'] === 'pending'): ?>
                                        <span class="badge bg-warning text-dark"><i class="bi bi-clock me-1"></i>Chờ tiếp nhận</span>
                                    <?php elseif ($c['status'] === 'receiving'): ?>
                                        <span class="badge bg-info text-dark"><i class="bi bi-search me-1"></i>Đang kiểm tra</span>
                                    <?php elseif ($c['status'] === 'repairing'): ?>
                                        <span class="badge bg-primary"><i class="bi bi-tools me-1"></i>Đang sửa chữa</span>
                                    <?php elseif ($c['status'] === 'completed'): ?>
                                        <span class="badge bg-success"><i class="bi bi-check-circle-fill me-1"></i>Hoàn thành</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger"><i class="bi bi-x-circle-fill me-1"></i>Từ chối</span>
                                    <?php endif; ?>
                                </td>
                                <td class="font-monospace small"><?= date('d/m/Y H:i', strtotime($c['created_at'])) ?></td>
                                <td class="pe-3 text-end">
                                    <button class="btn btn-sm btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#editClaim-<?= $c['id'] ?>" aria-expanded="false">
                                        <i class="bi bi-pencil-square me-1"></i>Cập nhật
                                    </button>
                                </td>
                            </tr>
                            <!-- Collapsible edit form -->
                            <tr class="collapse bg-light-subtle" id="editClaim-<?= $c['id'] ?>">
                                <td colspan="8" class="p-4 border-bottom">
                                    <div class="p-3 bg-white border rounded-3 shadow-sm mx-auto" style="max-width: 700px;">
                                        <h6 class="fw-bold mb-3 text-primary"><i class="bi bi-pencil-square me-1"></i>Cập nhật trạng thái sửa chữa / bảo hành #<?= $c['id'] ?></h6>
                                        <form action="/admin/warranties/claims/<?= $c['id'] ?>/update" method="POST">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold small text-muted">Trạng thái xử lý</label>
                                                    <select name="status" class="form-select" required>
                                                        <option value="pending" <?= $c['status'] === 'pending' ? 'selected' : '' ?>>Chờ tiếp nhận (Pending)</option>
                                                        <option value="receiving" <?= $c['status'] === 'receiving' ? 'selected' : '' ?>>Đang kiểm tra (Checking)</option>
                                                        <option value="repairing" <?= $c['status'] === 'repairing' ? 'selected' : '' ?>>Đang sửa chữa (Repairing)</option>
                                                        <option value="completed" <?= $c['status'] === 'completed' ? 'selected' : '' ?>>Hoàn thành (Completed)</option>
                                                        <option value="rejected" <?= $c['status'] === 'rejected' ? 'selected' : '' ?>>Từ chối (Rejected)</option>
                                                    </select>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label fw-bold small text-muted">Ghi chú của kỹ thuật viên</label>
                                                    <textarea name="admin_note" class="form-control" rows="3" placeholder="Ghi chú chi tiết kết quả kiểm tra hoặc sửa chữa..."><?= htmlspecialchars($c['admin_note'] ?? '') ?></textarea>
                                                </div>
                                                <div class="col-12 text-end">
                                                    <button type="button" class="btn btn-sm btn-secondary me-2" data-bs-toggle="collapse" data-bs-target="#editClaim-<?= $c['id'] ?>">Hủy</button>
                                                    <button type="submit" class="btn btn-sm btn-primary px-3">Lưu thay đổi</button>
                                                </div>
                                            </div>
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
</div>
