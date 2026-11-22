<div class="container-fluid py-4">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-primary bg-opacity-25 text-primary border border-primary border-opacity-25 rounded-pill px-3 py-1">
                    <i class="bi bi-mortarboard me-1"></i> LMS Students
                </span>
                <span class="text-white-50 small">• <?= count($students ?? []) ?> học viên</span>
            </div>
            <h1 class="h3 fw-bold text-white mb-1">Quản Lý Học Viên</h1>
            <p class="text-muted mb-0 small">Danh sách tài khoản học viên và tình trạng tham gia học tập trên hệ thống</p>
        </div>
    </div>

    <!-- Glass Card Table -->
    <div class="refract-card p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Học Viên</th>
                        <th>Email / Số Điện Thoại</th>
                        <th class="text-center">Số Khóa Đang Học</th>
                        <th class="text-center">Chứng Chỉ Đạt Được</th>
                        <th class="text-center">Trạng Thái</th>
                        <th class="text-end pe-4">Ngày Đăng Ký</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($students)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted py-3">
                                    <i class="bi bi-people fs-1 d-block mb-3 text-secondary opacity-50"></i>
                                    <h5 class="fw-bold text-white">Chưa có học viên nào</h5>
                                    <p class="small text-muted mb-0">Hệ thống chưa ghi nhận tài khoản học viên nào.</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($students as $stu): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-refract-pill" style="width: 38px; height: 38px; font-size: 13px;">
                                            <?= mb_substr($stu['name'], 0, 1) ?>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-white mb-0"><?= e($stu['name']) ?></div>
                                            <div class="small text-white-50" style="font-size: 11px;">ID: #<?= $stu['id'] ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-white small fw-medium"><?= e($stu['email']) ?></div>
                                    <div class="text-white-50 small" style="font-size: 11px;"><?= e($stu['phone'] ?: 'Chưa cập nhật') ?></div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary bg-opacity-25 text-primary border border-primary border-opacity-25 rounded-pill px-2.5 py-1 small fw-semibold"><?= (int)$stu['enrolled_courses_count'] ?> khóa</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-warning bg-opacity-25 text-warning border border-warning border-opacity-25 rounded-pill px-3 py-1 small fw-semibold">
                                        <i class="bi bi-award-fill me-1"></i> <?= (int)$stu['certificates_count'] ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 rounded-pill px-2.5 py-1 small fw-semibold">
                                        <i class="bi bi-check-circle-fill me-1"></i> Hoạt động
                                    </span>
                                </td>
                                <td class="text-end pe-4 text-white-50 small">
                                    <i class="bi bi-calendar3 me-1 text-secondary"></i><?= date('d/m/Y', strtotime($stu['created_at'])) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
