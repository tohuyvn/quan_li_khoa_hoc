<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h2 class="h3 fw-bold mb-1 text-dark"><i class="bi bi-ticket-perforated text-danger me-2"></i>Quản Lý Mã Khuyến Mãi (Voucher)</h2>
        <p class="text-muted small mb-0">Tạo mã coupon giảm giá % trực tiếp khi đặt hàng cho khách hàng.</p>
    </div>
    <a href="/admin/promotions/create" class="btn btn-danger btn-sm rounded-3 px-3 py-2 fw-bold shadow-sm" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); border: none;">
        <i class="bi bi-plus-lg me-1"></i> Tạo Mã Khuyến Mãi Mới
    </a>
</div>

<div class="content-card p-0 overflow-hidden shadow-sm">
    <div class="p-3 bg-light border-bottom d-flex align-items-center justify-content-between">
        <h5 class="fw-bold mb-0 text-dark" style="font-size: 15px;">Danh Sách Mã Coupon Khuyến Mãi</h5>
        <span class="badge bg-dark rounded-pill px-3 py-1 fw-bold"><?= count($promotions ?? []) ?> mã</span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th width="70" class="ps-3">ID</th>
                    <th>Mã Coupon</th>
                    <th>Mức Giảm Giá (%)</th>
                    <th>Trạng Thái</th>
                    <th>Ngày Hết Hạn</th>
                    <th width="160" class="text-end pe-3">Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($promotions)): ?>
                    <tr><td colspan="6" class="text-center py-5 text-muted">Chưa có mã khuyến mãi nào được tạo.</td></tr>
                <?php else: ?>
                    <?php foreach ($promotions as $promo):
                        $isExpired = strtotime($promo['expires_at']) < time();
                        $statusClass = 'success';
                        $statusText = 'Đang hoạt động';
                        
                        if ($promo['status'] !== 'active') {
                            $statusClass = 'secondary';
                            $statusText = 'Đang tắt';
                        } elseif ($isExpired) {
                            $statusClass = 'danger';
                            $statusText = 'Đã hết hạn';
                        }
                    ?>
                        <tr>
                            <td class="ps-3 fw-bold text-muted" style="font-size: 12px;"><?= $promo['id'] ?></td>
                            <td>
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle font-monospace px-3 py-1.5 fw-bold" style="font-size: 13px; letter-spacing: 0.5px;">
                                    <i class="bi bi-ticket-fill me-1"></i><?= e($promo['code']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="fs-6 fw-extrabold text-danger">-<?= (int)$promo['discount_percent'] ?>%</span>
                            </td>
                            <td>
                                <?php if ($statusClass === 'success'): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1" style="font-size: 11px;">
                                        <i class="bi bi-check-circle-fill me-1"></i>Đang chạy
                                    </span>
                                <?php elseif ($statusClass === 'danger'): ?>
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2.5 py-1" style="font-size: 11px;">
                                        Hết hạn
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2.5 py-1" style="font-size: 11px;">
                                        Đang tắt
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="small text-muted fw-medium"><?= date('d/m/Y', strtotime($promo['expires_at'])) ?></td>
                            <td class="text-end pe-3">
                                <a href="/admin/promotions/<?= $promo['id'] ?>/edit" class="btn btn-sm btn-outline-primary rounded-circle p-1 me-1" style="width: 32px; height: 32px;" title="Sửa">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="post" action="/admin/promotions/<?= (int) $promo['id'] ?>/delete" class="d-inline"
                                      onsubmit="return confirm('Bạn có chắc chắn muốn xóa mã khuyến mãi này?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle p-1" style="width: 32px; height: 32px;" title="Xóa">
                                        <i class="bi bi-trash"></i>
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
