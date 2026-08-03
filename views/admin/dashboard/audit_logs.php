<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0" style="letter-spacing: -0.02em;">🛡️ Nhật ký hoạt động hệ thống</h2>
    <a href="/admin/dashboard" class="btn btn-outline-secondary btn-sm fw-bold">← Quay lại Tổng quan</a>
</div>

<div class="card p-0 border rounded-3 bg-white shadow-sm overflow-hidden">
    <div class="p-3 border-bottom bg-light">
        <span class="text-muted small fw-semibold"><i class="bi bi-info-circle me-1"></i> Hiển thị 100 hoạt động gần nhất của các tài khoản quản trị viên.</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover table-striped mb-0 align-middle" style="font-size: 13px;">
            <thead class="table-dark">
                <tr>
                    <th width="80" class="text-center">ID</th>
                    <th width="150">Thời gian</th>
                    <th width="150">Người thực hiện</th>
                    <th width="180">Hành động</th>
                    <th>Chi tiết hoạt động</th>
                    <th width="120" class="text-center">Địa chỉ IP</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($logs)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            Chưa có nhật ký hoạt động nào được ghi lại.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td class="text-center fw-bold text-muted">#<?= $log['id'] ?></td>
                            <td><?= date('d/m/Y H:i:s', strtotime($log['created_at'])) ?></td>
                            <td>
                                <strong class="text-dark"><?= e($log['admin_name']) ?></strong>
                                <?php if (!empty($log['admin_id'])): ?>
                                    <span class="badge bg-secondary" style="font-size: 9px;">UID: <?= (int)$log['admin_id'] ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                $actionClass = match ($log['action']) {
                                    'price_change' => 'warning text-dark',
                                    'password_reset' => 'danger',
                                    'user_password_changed_directly' => 'danger',
                                    'review_deletion' => 'dark',
                                    default => 'info text-dark'
                                };
                                ?>
                                <span class="badge bg-<?= $actionClass ?> text-uppercase" style="font-size: 10px;"><?= str_replace('_', ' ', e($log['action'])) ?></span>
                            </td>
                            <td class="text-dark fw-semibold"><?= e($log['details']) ?></td>
                            <td class="text-center text-muted"><?= e($log['ip_address']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
