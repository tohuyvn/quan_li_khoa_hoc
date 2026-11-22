<div class="container-fluid py-4">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-secondary bg-opacity-25 text-secondary border border-secondary border-opacity-25 rounded-pill px-3 py-1">
                    <i class="bi bi-shield-check me-1"></i> Security & Audit
                </span>
                <span class="text-white-50 small">• 100 sự kiện gần nhất</span>
            </div>
            <h1 class="h3 fw-bold text-white mb-1">Nhật Ký Hoạt Động Hệ Thống (Audit Logs)</h1>
            <p class="text-muted mb-0 small">Theo dõi chi tiết các thao tác quản trị, bảo mật và thay đổi cấu hình trên hệ thống</p>
        </div>
        <div class="d-flex gap-2">
            <a href="/admin/lms/dashboard" class="btn btn-outline-secondary btn-sm px-3 rounded-pill d-inline-flex align-items-center gap-1.5">
                <i class="bi bi-arrow-left"></i> Quay lại Tổng quan
            </a>
        </div>
    </div>

    <!-- Glass Card Table -->
    <div class="refract-card p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th width="75" class="ps-4">ID</th>
                        <th width="160">Thời Gian</th>
                        <th width="160">Người Thực Hiện</th>
                        <th width="180">Hành Động</th>
                        <th>Chi Tiết Hoạt Động</th>
                        <th width="130" class="text-end pe-4">Địa Chỉ IP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($logs)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted py-3">
                                    <i class="bi bi-shield-slash fs-1 d-block mb-3 text-secondary opacity-50"></i>
                                    <h5 class="fw-bold text-white">Chưa có nhật ký hoạt động</h5>
                                    <p class="small text-muted mb-0">Hệ thống chưa ghi nhận thao tác quản trị nào.</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td class="ps-4">
                                    <span class="text-white-50 font-monospace small">#<?= $log['id'] ?></span>
                                </td>
                                <td>
                                    <div class="text-white-50 small">
                                        <i class="bi bi-clock-history me-1 text-secondary"></i>
                                        <span><?= date('d/m/Y H:i:s', strtotime($log['created_at'])) ?></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-refract-pill" style="width: 32px; height: 32px; font-size: 11px;">
                                            <?= strtoupper(substr($log['admin_name'] ?? 'AD', 0, 2)) ?>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-white small"><?= e($log['admin_name']) ?></div>
                                            <?php if (!empty($log['admin_id'])): ?>
                                                <span class="badge bg-white bg-opacity-10 text-white-50 border border-white border-opacity-10 rounded-pill" style="font-size: 9px;">UID: <?= (int)$log['admin_id'] ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php
                                    $actionBadgeClass = match ($log['action']) {
                                        'price_change' => 'bg-warning bg-opacity-25 text-warning border border-warning border-opacity-25',
                                        'password_reset', 'user_password_changed_directly' => 'bg-danger bg-opacity-25 text-danger border border-danger border-opacity-25',
                                        'review_deletion' => 'bg-secondary bg-opacity-25 text-light border border-secondary border-opacity-25',
                                        default => 'bg-info bg-opacity-25 text-info border border-info border-opacity-25'
                                    };
                                    ?>
                                    <span class="badge <?= $actionBadgeClass ?> rounded-pill px-2.5 py-1 small fw-semibold text-uppercase" style="font-size: 10px;">
                                        <?= str_replace('_', ' ', e($log['action'])) ?>
                                    </span>
                                </td>
                                <td class="text-white-50 small">
                                    <span class="text-white fw-medium"><?= e($log['details']) ?></span>
                                </td>
                                <td class="text-end pe-4">
                                    <span class="font-monospace text-white-50 small"><?= e($log['ip_address']) ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
