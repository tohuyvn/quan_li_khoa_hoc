<h2 class="mb-4">Quản lý Yêu cầu Thu Cũ Đổi Mới</h2>

<div class="table-responsive">
    <table class="table table-striped table-bordered align-middle">
        <thead class="table-dark">
            <tr>
                <th width="60">ID</th>
                <th>Khách hàng</th>
                <th>Số điện thoại</th>
                <th>Thiết bị cũ</th>
                <th>Tình trạng</th>
                <th width="150">Định giá thu hồi</th>
                <th>Mã Voucher</th>
                <th width="150">Trạng thái</th>
                <th width="200" class="text-center">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($tradeIns)): ?>
                <tr><td colspan="9" class="text-center">Chưa có yêu cầu thu cũ đổi mới nào</td></tr>
            <?php else: ?>
                <?php foreach ($tradeIns as $item): ?>
                    <tr>
                        <td><?= (int)$item['id'] ?></td>
                        <td>
                            <strong><?= e($item['customer_name']) ?></strong>
                            <?php if ($item['user_id']): ?>
                                <span class="badge bg-info-subtle text-info small ms-1">Thành viên</span>
                            <?php endif; ?>
                        </td>
                        <td><?= e($item['customer_phone']) ?></td>
                        <td><?= e($item['device_model']) ?></td>
                        <td><span class="text-muted small"><?= e($item['device_condition']) ?></span></td>
                        <td class="fw-bold text-success"><?= formatMoney($item['estimated_value']) ?></td>
                        <td>
                            <code class="text-danger fw-bold"><?= e($item['voucher_code']) ?></code>
                        </td>
                        <td>
                            <?php
                            $badgeClass = 'secondary';
                            $statusText = $item['status'];
                            if ($item['status'] === 'pending') { $badgeClass = 'warning'; $statusText = 'Chờ duyệt'; }
                            elseif ($item['status'] === 'approved') { $badgeClass = 'success'; $statusText = 'Đã duyệt'; }
                            elseif ($item['status'] === 'completed') { $badgeClass = 'primary'; $statusText = 'Hoàn tất'; }
                            elseif ($item['status'] === 'cancelled') { $badgeClass = 'danger'; $statusText = 'Đã hủy'; }
                            ?>
                            <span class="badge bg-<?= $badgeClass ?>"><?= $statusText ?></span>
                        </td>
                        <td class="text-center">
                            <form action="/admin/trade-ins/<?= (int)$item['id'] ?>/status" method="POST" class="d-inline">
                                <?= csrf_field() ?>
                                <select name="status" class="form-select form-select-sm d-inline-block w-auto me-1" onchange="this.form.submit()" style="cursor: pointer;">
                                    <option value="pending" <?= $item['status'] === 'pending' ? 'selected' : '' ?>>Chờ duyệt</option>
                                    <option value="approved" <?= $item['status'] === 'approved' ? 'selected' : '' ?>>Duyệt</option>
                                    <option value="completed" <?= $item['status'] === 'completed' ? 'selected' : '' ?>>Hoàn tất</option>
                                    <option value="cancelled" <?= $item['status'] === 'cancelled' ? 'selected' : '' ?>>Hủy</option>
                                </select>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
