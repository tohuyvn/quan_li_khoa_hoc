<div class="container-fluid py-4">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-warning bg-opacity-25 text-warning border border-warning border-opacity-25 rounded-pill px-3 py-1">
                    <i class="bi bi-collection-play me-1"></i> LMS Curriculum
                </span>
                <span class="text-white-50 small">• <?= count($courses ?? []) ?> khóa học trực tuyến</span>
            </div>
            <h1 class="h3 fw-bold text-white mb-1">Quản Lý Khóa Học & Bài Học</h1>
            <p class="text-muted mb-0 small">Danh sách toàn bộ khóa học, lộ trình đào tạo và học liệu đa phương tiện</p>
        </div>
        <div class="d-flex gap-2">
            <a href="/admin/lms/courses/create" class="btn-refract-action d-inline-flex align-items-center gap-2">
                <i class="bi bi-plus-lg"></i>
                <span>Thêm Khóa Học Mới</span>
            </a>
        </div>
    </div>

    <!-- Glass Card Table -->
    <div class="refract-card p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Khóa Học</th>
                        <th>Giảng Viên</th>
                        <th>Học Phí</th>
                        <th>Cấp Độ</th>
                        <th>Trạng Thái</th>
                        <th class="text-center">Số Bài Học</th>
                        <th class="text-end pe-4">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($courses)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted py-3">
                                    <i class="bi bi-journal-x fs-1 d-block mb-3 text-secondary opacity-50"></i>
                                    <h5 class="fw-bold text-white">Chưa có khóa học nào</h5>
                                    <p class="small text-muted mb-3">Tạo khóa học đầu tiên để bắt đầu xây dựng giáo trình đào tạo.</p>
                                    <a href="/admin/lms/courses/create" class="btn-refract-action btn-sm d-inline-flex align-items-center gap-1.5">
                                        <i class="bi bi-plus-lg"></i> Thêm khóa học ngay
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($courses as $c): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="<?= e($c['thumbnail'] ?: '/image/banner1.png') ?>" class="rounded-3 border border-secondary border-opacity-25 object-fit-cover shadow-sm" style="width: 54px; height: 38px;" alt="">
                                        <div>
                                            <div class="fw-bold text-white mb-0"><?= e($c['title']) ?></div>
                                            <div class="small text-white-50" style="font-size: 11px;">
                                                <i class="bi bi-folder me-1 text-secondary"></i><?= e($c['category_name'] ?? 'Chưa phân loại') ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-white small fw-medium"><?= e($c['instructor_name'] ?? 'N/A') ?></span>
                                </td>
                                <td>
                                    <?php if ($c['price'] <= 0): ?>
                                        <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 rounded-pill px-2.5 py-1 small fw-semibold">Miễn phí</span>
                                    <?php else: ?>
                                        <span class="fw-bold text-danger fs-6"><?= formatMoney($c['sale_price'] ?? $c['price']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-white bg-opacity-10 text-light border border-white border-opacity-10 rounded-pill px-2.5 py-1 small text-uppercase fw-normal"><?= e($c['level']) ?></span>
                                </td>
                                <td>
                                    <?php if ($c['status'] === 'published'): ?>
                                        <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 rounded-pill px-2.5 py-1 small fw-semibold">
                                            <i class="bi bi-check-circle-fill me-1"></i> Công khai
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary bg-opacity-25 text-secondary border border-secondary border-opacity-25 rounded-pill px-2.5 py-1 small fw-semibold">
                                            <i class="bi bi-file-earmark me-1"></i> Bản nháp
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary bg-opacity-25 text-primary border border-primary border-opacity-25 rounded-pill px-2.5 py-1 small fw-semibold"><?= (int)$c['total_lessons'] ?> bài</span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-inline-flex align-items-center gap-1.5">
                                        <a href="/admin/lms/courses/<?= $c['id'] ?>/curriculum" class="btn btn-outline-primary btn-sm px-2.5 py-1 rounded-pill d-inline-flex align-items-center gap-1" title="Quản lý giáo trình">
                                            <i class="bi bi-diagram-3"></i> Giáo trình
                                        </a>
                                        <a href="/admin/lms/courses/<?= $c['id'] ?>/edit" class="btn btn-outline-warning btn-sm px-2.5 py-1 rounded-pill d-inline-flex align-items-center" title="Chỉnh sửa thông tin">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="/courses/<?= e($c['slug'] ?: $c['id']) ?>" target="_blank" class="btn btn-outline-info btn-sm px-2.5 py-1 rounded-pill d-inline-flex align-items-center" title="Xem trên web">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <form method="post" action="/admin/lms/courses/<?= $c['id'] ?>/delete" class="d-inline m-0" onsubmit="return confirm('Bạn có chắc muốn xóa khóa học này?');">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-outline-danger btn-sm px-2.5 py-1 rounded-pill" title="Xóa">
                                                <i class="bi bi-trash3"></i>
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
