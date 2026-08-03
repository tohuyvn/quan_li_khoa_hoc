<h2 class="mb-4">Quản lý mã coupon (giảm giá khi thanh toán)</h2>

<div class="mb-3">
    <a href="/admin/promotions/create" class="btn btn-primary">+ Tạo mã khuyến mãi mới</a>
</div>

<div class="table-responsive">
    <table class="table table-striped table-bordered align-middle">
        <thead class="table-dark">
            <tr>
                <th width="60">ID</th>
                <th>Mã khuyến mãi</th>
                <th>Mức giảm giá (%)</th>
                <th>Trạng thái</th>
                <th>Ngày hết hạn</th>
                <th width="160">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($promotions)): ?>
                <tr><td colspan="6" class="text-center">Chưa có chương trình khuyến mãi nào được tạo.</td></tr>
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
                        <td><?= $promo['id'] ?></td>
                        <td><strong class="text-primary fs-6"><?= e($promo['code']) ?></strong></td>
                        <td class="fw-bold text-danger"><?= (int)$promo['discount_percent'] ?>%</td>
                        <td>
                            <span class="badge bg-<?= $statusClass ?>"><?= $statusText ?></span>
                        </td>
                        <td><?= date('d/m/Y', strtotime($promo['expires_at'])) ?></td>
                        <td>
                            <a href="/admin/promotions/<?= $promo['id'] ?>/edit" class="btn btn-sm btn-warning">Sửa</a>
                            <form method="post" action="/admin/promotions/<?= (int) $promo['id'] ?>/delete" class="d-inline"
                                  onsubmit="return confirm('Xóa mã khuyến mãi này?')">
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
