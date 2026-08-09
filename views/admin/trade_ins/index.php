<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h2 class="h3 fw-bold mb-1 text-dark"><i class="bi bi-arrow-repeat text-success me-2"></i>Quản Lý Thu Cũ Đổi Mới (Trade-In)</h2>
        <p class="text-muted small mb-0">Duyệt định giá máy cũ và cấp Voucher trợ giá lên đời cho khách hàng.</p>
    </div>
</div>

<div class="content-card p-0 overflow-hidden shadow-sm">
    <div class="p-3 bg-light border-bottom d-flex align-items-center justify-content-between">
        <h5 class="fw-bold mb-0 text-dark" style="font-size: 15px;">Danh Sách Yêu Cầu Thu Cũ</h5>
        <span class="badge bg-dark rounded-pill px-3 py-1 fw-bold"><?= count($tradeIns ?? []) ?> yêu cầu</span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th width="60" class="ps-3">ID</th>
                    <th>Khách Hàng</th>
                    <th>Số Điện Thoại</th>
                    <th>Thiết Bị Cũ</th>
                    <th>Tình Trạng Thiết Bị</th>
                    <th width="150">Định Giá Thu Hồi</th>
                    <th>Mã Voucher Cấp</th>
                    <th width="140">Trạng Thái</th>
                    <th width="160" class="text-end pe-3">Cập Nhật Trạng Thái</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($tradeIns)): ?>
                    <tr><td colspan="9" class="text-center py-5 text-muted">Chưa có yêu cầu thu cũ đổi mới nào.</td></tr>
                <?php else: ?>
                    <?php foreach ($tradeIns as $item): ?>
                        <tr>
                            <td class="ps-3 fw-bold text-muted" style="font-size: 12px;"><?= (int)$item['id'] ?></td>
                            <td>
                                <div class="fw-bold text-dark"><?= e($item['customer_name']) ?></div>
                                <?php if ($item['user_id']): ?>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-0.5" style="font-size: 10px;">Thành viên</span>
                                <?php endif; ?>
                            </td>
                            <td class="small text-muted font-monospace"><?= e($item['customer_phone']) ?></td>
                            <td class="fw-bold text-dark"><?= e($item['device_model']) ?></td>
                            <td><span class="text-muted small text-truncate d-inline-block" style="max-width: 180px;"><?= e($item['device_condition']) ?></span></td>
                            <td class="fw-bold text-success fs-6"><?= formatMoney($item['estimated_value']) ?></td>
                            <td>
                                <code class="bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 rounded fw-bold" style="font-size: 12px;"><?= e($item['voucher_code']) ?></code>
                            </td>
                            <td>
                                <?php
                                $badgeClass = 'secondary';
                                $statusText = $item['status'];
                                if ($item['status'] === 'pending') { $badgeClass = 'warning-subtle text-warning border border-warning-subtle'; $statusText = 'Chờ duyệt'; }
                                elseif ($item['status'] === 'approved') { $badgeClass = 'success-subtle text-success border border-success-subtle'; $statusText = 'Đã duyệt'; }
                                elseif ($item['status'] === 'completed') { $badgeClass = 'primary-subtle text-primary border border-primary-subtle'; $statusText = 'Hoàn tất'; }
                                elseif ($item['status'] === 'cancelled') { $badgeClass = 'danger-subtle text-danger border border-danger-subtle'; $statusText = 'Đã hủy'; }
                                ?>
                                <span class="badge bg-<?= $badgeClass ?> rounded-pill px-2.5 py-1" style="font-size: 11px;"><?= $statusText ?></span>
                            </td>
                            <td class="text-end pe-3">
                                <form action="/admin/trade-ins/<?= (int)$item['id'] ?>/status" method="POST" class="d-inline">
                                    <?= csrf_field() ?>
                                    <select name="status" class="form-select form-select-sm d-inline-block w-auto rounded-3" onchange="this.form.submit()" style="cursor: pointer; font-size: 12px;">
                                        <option value="pending" <?= $item['status'] === 'pending' ? 'selected' : '' ?>>Chờ duyệt</option>
                                        <option value="approved" <?= $item['status'] === 'approved' ? 'selected' : '' ?>>Duyệt giá</option>
                                        <option value="completed" <?= $item['status'] === 'completed' ? 'selected' : '' ?>>Hoàn tất</option>
                                        <option value="cancelled" <?= $item['status'] === 'cancelled' ? 'selected' : '' ?>>Hủy bỏ</option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
