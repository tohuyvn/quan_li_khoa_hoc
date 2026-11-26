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
            <p class="text-white-50 mb-0 small">Danh sách đội ngũ giảng viên, mentor và chuyên gia giảng dạy trên nền tảng Hades Academy</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn-refract-action d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalAddInstructor">
                <i class="bi bi-person-plus-fill"></i>
                <span>Thêm Giảng Viên Mới</span>
            </button>
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
                        <th>Email & Số Điện Thoại</th>
                        <th class="text-center">Trạng Thái</th>
                        <th class="text-end pe-4">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($instructors)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted py-3">
                                    <i class="bi bi-person-video3 fs-1 d-block mb-3 text-secondary opacity-50"></i>
                                    <h5 class="fw-bold text-white">Chưa có giảng viên nào</h5>
                                    <p class="small text-muted mb-3">Hệ thống chưa ghi nhận thông tin giảng viên.</p>
                                    <button class="btn-refract-action btn-sm d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalAddInstructor">
                                        <i class="bi bi-person-plus-fill"></i> Thêm giảng viên đầu tiên
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($instructors as $ins): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-refract-pill" style="width: 40px; height: 40px; font-size: 13px; border-color: #00f2fe; color: #38e1ff;">
                                            <i class="bi bi-person-video3"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-white mb-0"><?= e($ins['name']) ?></div>
                                            <div class="small text-white-50" style="font-size: 11px;">
                                                <?= e($ins['qualification'] ?? 'Chuyên gia') ?>
                                            </div>
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
                                <td>
                                    <div class="text-white small"><i class="bi bi-envelope me-1 text-secondary"></i><?= e($ins['email']) ?></div>
                                    <?php if (!empty($ins['phone'])): ?>
                                        <div class="text-white-50 small" style="font-size: 11px;"><i class="bi bi-telephone me-1 text-secondary"></i><?= e($ins['phone']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if (($ins['status'] ?? 'active') === 'active'): ?>
                                        <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 rounded-pill px-2.5 py-1 small">Đang hoạt động</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary bg-opacity-25 text-secondary border border-secondary border-opacity-25 rounded-pill px-2.5 py-1 small">Tạm khóa</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-inline-flex align-items-center gap-1.5">
                                        <button class="btn btn-outline-warning btn-sm px-2.5 py-1 rounded-pill d-inline-flex align-items-center" title="Sửa thông tin" data-bs-toggle="modal" data-bs-target="#modalEditInstructor_<?= $ins['id'] ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form method="post" action="/admin/lms/instructors/<?= $ins['id'] ?>/delete" class="d-inline m-0" onsubmit="return confirm('Bạn có chắc muốn xóa giảng viên này?');">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-outline-danger btn-sm px-2.5 py-1 rounded-pill" title="Xóa giảng viên" <?= ((int)$ins['total_courses'] > 0) ? 'disabled title="Đang có khóa học phụ trách"' : '' ?>>
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            <!-- Modal Edit Instructor -->
                            <div class="modal fade" id="modalEditInstructor_<?= $ins['id'] ?>" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <form method="post" action="/admin/lms/instructors/<?= $ins['id'] ?>/update">
                                        <?= csrf_field() ?>
                                        <div class="modal-content refract-card border border-white border-opacity-10 text-white rounded-4 shadow-lg p-3">
                                            <div class="modal-header border-bottom border-white border-opacity-10 pb-3">
                                                <h5 class="modal-title fw-bold text-white d-flex align-items-center gap-2">
                                                    <i class="bi bi-pencil-square text-warning"></i>
                                                    <span>Chỉnh Sửa Giảng Viên: <?= e($ins['name']) ?></span>
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body py-4">
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label text-white small fw-bold">Họ và tên <span class="text-danger">*</span></label>
                                                        <input type="text" name="name" class="form-control" value="<?= e($ins['name']) ?>" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label text-white small fw-bold">Số điện thoại</label>
                                                        <input type="text" name="phone" class="form-control" value="<?= e($ins['phone'] ?? '') ?>">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label text-white small fw-bold">Chức danh / Headline</label>
                                                        <input type="text" name="headline" class="form-control" value="<?= e($ins['headline'] ?? '') ?>" placeholder="VD: Senior Backend Engineer">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label text-white small fw-bold">Học vị / Trình độ chuyên môn</label>
                                                        <input type="text" name="qualification" class="form-control" value="<?= e($ins['qualification'] ?? '') ?>" placeholder="VD: Thạc sĩ Khoa học Máy tính">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label text-white small fw-bold">Số năm kinh nghiệm</label>
                                                        <input type="number" name="experience_years" class="form-control" value="<?= (int)($ins['experience_years'] ?? 0) ?>" min="0" max="60">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label text-white small fw-bold">Trạng thái</label>
                                                        <select name="status" class="form-select">
                                                            <option value="active" <?= (($ins['status'] ?? 'active') === 'active') ? 'selected' : '' ?>>Đang hoạt động (Active)</option>
                                                            <option value="inactive" <?= (($ins['status'] ?? '') === 'inactive') ? 'selected' : '' ?>>Tạm dừng (Inactive)</option>
                                                            <option value="banned" <?= (($ins['status'] ?? '') === 'banned') ? 'selected' : '' ?>>Khóa tài khoản (Banned)</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-12">
                                                        <label class="form-label text-white small fw-bold">Tiểu sử & Giới thiệu bản thân (Bio)</label>
                                                        <textarea name="bio" class="form-control" rows="3"><?= e($ins['bio'] ?? '') ?></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-top border-white border-opacity-10 pt-3">
                                                <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Đóng</button>
                                                <button type="submit" class="btn-refract-action px-4 py-2">
                                                    <i class="bi bi-check2-circle me-1"></i> Lưu thông tin
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Add Instructor -->
<div class="modal fade" id="modalAddInstructor" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="post" action="/admin/lms/instructors">
            <?= csrf_field() ?>
            <div class="modal-content refract-card border border-white border-opacity-10 text-white rounded-4 shadow-lg p-3">
                <div class="modal-header border-bottom border-white border-opacity-10 pb-3">
                    <h5 class="modal-title fw-bold text-white d-flex align-items-center gap-2">
                        <i class="bi bi-person-plus-fill text-info"></i>
                        <span>Thêm Giảng Viên Mới</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-white small fw-bold">Họ và tên giảng viên <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="VD: ThS. Hoàng Minh Đức" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-white small fw-bold">Email đăng nhập <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" placeholder="giangvien@lms.local" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-white small fw-bold">Số điện thoại</label>
                            <input type="text" name="phone" class="form-control" placeholder="0988776655">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-white small fw-bold">Mật khẩu ban đầu</label>
                            <input type="text" name="password" class="form-control" value="123456" placeholder="Mặc định: 123456">
                            <small class="text-white-50" style="font-size: 11px;">Giảng viên có thể đổi mật khẩu sau khi đăng nhập</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-white small fw-bold">Chức danh / Headline</label>
                            <input type="text" name="headline" class="form-control" placeholder="VD: Senior Fullstack Engineer & Tech Lead">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-white small fw-bold">Học vị / Bằng cấp chuyên môn</label>
                            <input type="text" name="qualification" class="form-control" placeholder="VD: Thạc sĩ Khoa học Máy tính">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-white small fw-bold">Số năm kinh nghiệm</label>
                            <input type="number" name="experience_years" class="form-control" value="5" min="0" max="60">
                        </div>
                        <div class="col-12">
                            <label class="form-label text-white small fw-bold">Tiểu sử & Giới thiệu bản thân (Bio)</label>
                            <textarea name="bio" class="form-control" rows="3" placeholder="Tóm tắt kinh nghiệm làm việc, dự án thực chiến và các giải thưởng đạt được..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top border-white border-opacity-10 pt-3">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Hủy bỏ</button>
                    <button type="submit" class="btn-refract-action px-5 py-2">
                        <i class="bi bi-person-check me-1"></i> Tạo Giảng Viên
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
