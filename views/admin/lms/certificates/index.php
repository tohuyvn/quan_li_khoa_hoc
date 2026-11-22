<div class="container-fluid py-4">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-warning bg-opacity-25 text-warning border border-warning border-opacity-25 rounded-pill px-3 py-1">
                    <i class="bi bi-award me-1"></i> QR Certificate
                </span>
                <span class="text-white-50 small">• <?= count($certificates ?? []) ?> chứng chỉ đã cấp</span>
            </div>
            <h1 class="h3 fw-bold text-white mb-1">Quản Lý Chứng Chỉ Cấp Phát</h1>
            <p class="text-muted mb-0 small">Danh sách các chứng chỉ hoàn thành khóa học được hệ thống xác thực trực tuyến qua mã QR</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn-refract-action d-inline-flex align-items-center gap-1.5" data-bs-toggle="modal" data-bs-target="#modalManualIssue">
                <i class="bi bi-award"></i>
                <span>Cấp Chứng Chỉ Thủ Công</span>
            </button>
            <a href="/verify-certificate" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill px-3 d-inline-flex align-items-center gap-1.5">
                <i class="bi bi-patch-check"></i> Tra Cứu Công Khai
            </a>
        </div>
    </div>

    <!-- Glass Card Table -->
    <div class="refract-card p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Mã Chứng Chỉ</th>
                        <th>Học Viên</th>
                        <th>Khóa Học Hoàn Thành</th>
                        <th>Ngày Cấp</th>
                        <th class="text-center">Trạng Thái</th>
                        <th class="text-end pe-4">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($certificates)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted py-3">
                                    <i class="bi bi-award fs-1 d-block mb-3 text-secondary opacity-50"></i>
                                    <h5 class="fw-bold text-white">Chưa có chứng chỉ nào</h5>
                                    <p class="small text-muted mb-0">Chưa có học viên nào hoàn thành hoặc được cấp chứng chỉ.</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($certificates as $cert): ?>
                            <tr>
                                <td class="ps-4">
                                    <span class="font-monospace fw-bold text-warning px-2.5 py-1 rounded-pill bg-white bg-opacity-10 border border-white border-opacity-10 small">
                                        <?= e($cert['certificate_code']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-bold text-white mb-0"><?= e($cert['student_name']) ?></div>
                                    <div class="text-white-50 small" style="font-size: 11px;"><?= e($cert['student_email']) ?></div>
                                </td>
                                <td>
                                    <span class="text-white small fw-medium"><?= e($cert['course_title']) ?></span>
                                </td>
                                <td>
                                    <div class="text-white-50 small">
                                        <i class="bi bi-clock-history me-1 text-secondary"></i>
                                        <span><?= date('d/m/Y H:i', strtotime($cert['issued_at'])) ?></span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <?php if ($cert['status'] === 'valid'): ?>
                                        <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 px-3 py-1 rounded-pill small fw-semibold">
                                            <i class="bi bi-check-circle-fill me-1"></i> Hợp lệ
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-danger bg-opacity-25 text-danger border border-danger border-opacity-25 px-3 py-1 rounded-pill small fw-semibold">
                                            <i class="bi bi-x-circle-fill me-1"></i> Đã thu hồi
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-inline-flex align-items-center gap-1.5">
                                        <a href="/certificates/<?= e($cert['certificate_code']) ?>" target="_blank" class="btn btn-outline-primary btn-sm px-2.5 py-1 rounded-pill d-inline-flex align-items-center gap-1" title="Xem / In chứng chỉ">
                                            <i class="bi bi-printer"></i> Xem / In
                                        </a>
                                        <form method="post" action="/admin/lms/certificates/<?= $cert['id'] ?>/toggle" class="d-inline m-0" onsubmit="return confirm('Bạn có chắc muốn đổi trạng thái chứng chỉ này?');">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn <?= $cert['status'] === 'valid' ? 'btn-outline-danger' : 'btn-outline-success' ?> btn-sm px-2.5 py-1 rounded-pill" title="<?= $cert['status'] === 'valid' ? 'Thu hồi chứng chỉ' : 'Khôi phục chứng chỉ' ?>">
                                                <?= $cert['status'] === 'valid' ? 'Thu hồi' : 'Khôi phục' ?>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Manual Issue -->
<div class="modal fade" id="modalManualIssue" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form method="post" action="/admin/lms/certificates/issue" class="w-100">
            <?= csrf_field() ?>
            <div class="modal-content refract-card border-0 p-3 shadow-lg">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-white"><i class="bi bi-award text-warning me-2"></i>Cấp Chứng Chỉ Thủ Công</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-3">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-white-50">Chọn Học viên nhận chứng chỉ</label>
                        <select name="user_id" class="form-select form-control" required>
                            <?php foreach ($students as $stu): ?>
                                <option value="<?= $stu['id'] ?>"><?= e($stu['name']) ?> (<?= e($stu['email']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-white-50">Chọn Khóa học hoàn thành</label>
                        <select name="course_id" class="form-select form-control" required>
                            <?php foreach ($courses as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= e($c['title']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <p class="small text-white-50 mb-0" style="font-size: 11.5px;">Hệ thống sẽ tự động sinh mã chứng chỉ chuẩn và kích hoạt trạng thái 100% hoàn thành cho học viên.</p>
                </div>
                <div class="modal-footer border-0 pt-0 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn-refract-action btn-sm">Cấp chứng chỉ ngay</button>
                </div>
            </div>
        </form>
    </div>
</div>
