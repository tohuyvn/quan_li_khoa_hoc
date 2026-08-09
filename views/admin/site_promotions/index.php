<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h2 class="h3 fw-bold mb-1 text-dark"><i class="bi bi-percent text-danger me-2"></i>Ưu Đãi Giảm Giá Toàn Shop (Site Promotions)</h2>
        <p class="text-muted small mb-0">Cấu hình chương trình khuyến mãi tự động áp dụng cho toàn bộ danh mục sản phẩm.</p>
    </div>
    <a href="/admin/site-promotions/create" class="btn btn-danger btn-sm rounded-3 px-3 py-2 fw-bold shadow-sm" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); border: none;">
        <i class="bi bi-plus-lg me-1"></i> Tạo Ưu Đãi Mới
    </a>
</div>

<?php if (!empty($activePromo)): ?>
    <div class="alert alert-success border-0 shadow-sm rounded-4 d-flex align-items-center gap-3 mb-4 p-3" style="background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0 !important;">
        <i class="bi bi-check-circle-fill fs-3 text-success"></i>
        <div>
            <strong class="d-block mb-0">Đang áp dụng toàn shop: <?= e($activePromo['name']) ?></strong>
            <span class="small opacity-90">Mức giảm: <strong class="text-danger">-<?= (int) $activePromo['discount_percent'] ?>%</strong> · Hết hạn: <?= date('d/m/Y H:i', strtotime($activePromo['expires_at'])) ?>.</span>
        </div>
    </div>
<?php else: ?>
    <div class="alert alert-secondary border-0 shadow-sm rounded-4 d-flex align-items-center gap-3 mb-4 p-3">
        <i class="bi bi-info-circle-fill fs-4 text-muted"></i>
        <div>
            <strong class="d-block mb-0">Hiện không có ưu đãi chung đang bật</strong>
            <span class="small text-muted">Sản phẩm sẽ giữ giá gốc trừ khi có chương trình giảm giá riêng từng mã.</span>
        </div>
    </div>
<?php endif; ?>

<div class="content-card p-0 overflow-hidden shadow-sm mb-4">
    <div class="p-3 bg-light border-bottom d-flex align-items-center justify-content-between">
        <h5 class="fw-bold mb-0 text-dark" style="font-size: 15px;">Chương Trình Khuyến Mãi Toàn Shop</h5>
        <span class="badge bg-dark rounded-pill px-3 py-1 fw-bold"><?= count($promotions ?? []) ?> chương trình</span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th width="70" class="ps-3">ID</th>
                    <th>Tên Chương Trình</th>
                    <th>Mức Giảm (%)</th>
                    <th>Ngày Bắt Đầu</th>
                    <th>Ngày Kết Thúc</th>
                    <th class="text-center">Trạng Thái</th>
                    <th width="140" class="text-end pe-3">Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($promotions)): ?>
                    <tr><td colspan="7" class="text-center py-5 text-muted">Chưa có chương trình ưu đãi chung nào được tạo.</td></tr>
                <?php else: ?>
                    <?php foreach ($promotions as $promo): ?>
                        <tr>
                            <td class="ps-3 fw-bold text-muted" style="font-size: 12px;"><?= (int) $promo['id'] ?></td>
                            <td class="fw-bold text-dark"><?= e($promo['name']) ?></td>
                            <td class="text-danger fw-extrabold fs-6">-<?= (int) $promo['discount_percent'] ?>%</td>
                            <td class="small text-muted"><?= !empty($promo['starts_at']) ? date('d/m/Y H:i', strtotime($promo['starts_at'])) : '—' ?></td>
                            <td class="small text-muted fw-medium"><?= date('d/m/Y H:i', strtotime($promo['expires_at'])) ?></td>
                            <td class="text-center">
                                <?php if (($promo['status'] ?? '') === 'active'): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1" style="font-size: 11px;">
                                        <i class="bi bi-check-circle-fill me-1"></i>Đang bật
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2.5 py-1" style="font-size: 11px;">
                                        Đã tắt
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end pe-3">
                                <a href="/admin/site-promotions/<?= $promo['id'] ?>/edit" class="btn btn-sm btn-outline-primary rounded-circle p-1 me-1" style="width: 32px; height: 32px;" title="Sửa">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="post" action="/admin/site-promotions/<?= (int) $promo['id'] ?>/delete" class="d-inline"
                                      onsubmit="return confirm('Xóa ưu đãi chung này?')">
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

<div class="alert alert-light border rounded-3 p-3 text-muted small">
    <i class="bi bi-lightbulb text-warning me-1"></i> <strong>Gợi ý:</strong> Đặt trạng thái <strong>Đã tắt</strong> để tạm dừng áp dụng ưu đãi toàn shop mà không xóa dữ liệu. 
    Để quản lý mã giảm giá riêng ở bước thanh toán, xem tại <a href="/admin/promotions" class="fw-bold text-danger text-decoration-none">Quản lý mã coupon</a>.
</div>
