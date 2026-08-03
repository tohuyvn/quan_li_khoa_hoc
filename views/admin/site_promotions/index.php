<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Ưu đãi chung toàn shop</h2>
    <a href="/admin/site-promotions/create" class="btn btn-primary">+ Tạo ưu đãi chung</a>
</div>

<?php if (!empty($activePromo)): ?>
    <div class="alert alert-success">
        Đang áp dụng: <strong><?= e($activePromo['name']) ?></strong>
        — giảm <strong><?= (int) $activePromo['discount_percent'] ?>%</strong>
        (hết hạn <?= e($activePromo['expires_at']) ?>).
        Mức này chỉ áp dụng khi cao hơn ưu đãi riêng từng sản phẩm.
    </div>
<?php else: ?>
    <div class="alert alert-secondary">
        Hiện không có ưu đãi chung đang bật. Sản phẩm chỉ giảm giá nếu được bật ưu đãi riêng.
    </div>
<?php endif; ?>

<div class="table-responsive">
    <table class="table table-striped table-bordered align-middle">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Tên chương trình</th>
                <th>Giảm (%)</th>
                <th>Bắt đầu</th>
                <th>Kết thúc</th>
                <th>Trạng thái</th>
                <th width="160">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($promotions)): ?>
                <tr><td colspan="7" class="text-center">Chưa có ưu đãi chung</td></tr>
            <?php else: ?>
                <?php foreach ($promotions as $promo): ?>
                    <tr>
                        <td><?= (int) $promo['id'] ?></td>
                        <td class="fw-bold"><?= e($promo['name']) ?></td>
                        <td class="text-danger fw-bold"><?= (int) $promo['discount_percent'] ?>%</td>
                        <td><?= e($promo['starts_at'] ?? '—') ?></td>
                        <td><?= e($promo['expires_at']) ?></td>
                        <td>
                            <span class="badge bg-<?= ($promo['status'] ?? '') === 'active' ? 'success' : 'secondary' ?>">
                                <?= ($promo['status'] ?? '') === 'active' ? 'Đang bật' : 'Đã tắt' ?>
                            </span>
                        </td>
                        <td>
                            <a href="/admin/site-promotions/<?= $promo['id'] ?>/edit" class="btn btn-sm btn-warning">Sửa</a>
                            <form method="post" action="/admin/site-promotions/<?= (int) $promo['id'] ?>/delete" class="d-inline"
                                  onsubmit="return confirm('Xóa ưu đãi chung này?')">
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

<p class="text-muted small mb-0">
    Gợi ý: Đặt trạng thái <strong>Đã tắt</strong> để hủy ưu đãi chung mà không xóa dữ liệu.
    <a href="/admin/promotions">Mã giảm giá (coupon)</a> áp dụng thêm ở bước thanh toán.
</p>
