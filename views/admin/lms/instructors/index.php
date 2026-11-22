<div class="container-fluid py-4">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-info bg-opacity-25 text-info border border-info border-opacity-25 rounded-pill px-3 py-1">
                    <i class="bi bi-person-video3 me-1"></i> LMS Instructors
                </span>
                <span class="text-white-50 small">• <?= count($instructors ?? []) ?> giảng viên & chuyên gia</span>
            </div>
            <h1 class="h3 fw-bold text-white mb-1">Quản Lý Giảng Viên</h1>
            <p class="text-muted mb-0 small">Danh sách đội ngũ giảng viên, mentor và chuyên gia giảng dạy trên nền tảng</p>
        </div>
    </div>

    <!-- Glass Card Table -->
    <div class="refract-card p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Giảng Viên</th>
                        <th>Chức Danh / Chuyên Môn</th>
                        <th class="text-center">Kinh Nghiệm</th>
                        <th class="text-center">Khóa Học Phụ Trách</th>
                        <th class="text-end pe-4">Email Liên Hệ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($instructors)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="text-muted py-3">
                                    <i class="bi bi-person-video3 fs-1 d-block mb-3 text-secondary opacity-50"></i>
                                    <h5 class="fw-bold text-white">Chưa có giảng viên nào</h5>
                                    <p class="small text-muted mb-0">Hệ thống chưa ghi nhận thông tin giảng viên.</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($instructors as $ins): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-refract-pill" style="width: 38px; height: 38px; font-size: 13px; border-color: #00f2fe; color: #38e1ff;">
                                            <i class="bi bi-person-video3"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-white mb-0"><?= e($ins['name']) ?></div>
                                            <span class="badge bg-primary bg-opacity-25 text-primary border border-primary border-opacity-25 rounded-pill small" style="font-size: 10px;">Instructor</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-white small fw-medium"><?= e($ins['headline'] ?? 'Chuyên gia đào tạo') ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-white bg-opacity-10 text-light border border-white border-opacity-10 rounded-pill px-2.5 py-1 small fw-normal"><?= (int)($ins['experience_years'] ?? 0) ?> năm</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-warning bg-opacity-25 text-warning border border-warning border-opacity-25 rounded-pill px-2.5 py-1 small fw-semibold"><?= (int)$ins['total_courses'] ?> khóa</span>
                                </td>
                                <td class="text-end pe-4 text-white-50 small">
                                    <i class="bi bi-envelope me-1 text-secondary"></i><?= e($ins['email']) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
