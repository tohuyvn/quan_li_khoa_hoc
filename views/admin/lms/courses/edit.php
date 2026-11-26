<div class="container-fluid py-4" style="max-width: 1050px;">
    <!-- Breadcrumb & Header -->
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="/admin/lms/dashboard" class="text-white-50 text-decoration-none">LMS</a></li>
                <li class="breadcrumb-item"><a href="/admin/lms/courses" class="text-white-50 text-decoration-none">Khóa học</a></li>
                <li class="breadcrumb-item active text-white" aria-current="page">Chỉnh sửa</li>
            </ol>
        </nav>
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h1 class="h3 fw-bold text-white mb-1">Chỉnh Sửa Khóa Học</h1>
                <p class="text-white-50 small mb-0">Cập nhật thông tin chi tiết, biểu phí học phí và cấu hình xuất bản</p>
            </div>
            <div class="d-flex gap-2">
                <a href="/admin/lms/courses/<?= $course['id'] ?>/curriculum" class="btn btn-outline-info rounded-pill px-3 py-1.5 small d-inline-flex align-items-center gap-1.5">
                    <i class="bi bi-diagram-3"></i> Quản lý giáo trình
                </a>
                <a href="/courses/<?= e($course['slug'] ?: $course['id']) ?>" target="_blank" class="btn btn-outline-secondary rounded-pill px-3 py-1.5 small d-inline-flex align-items-center gap-1.5 text-white-50">
                    <i class="bi bi-box-arrow-up-right"></i> Xem trên web
                </a>
            </div>
        </div>
    </div>

    <!-- Form Glass Card -->
    <div class="refract-card p-4 p-md-5">
        <form method="post" action="/admin/lms/courses/<?= $course['id'] ?>/update" id="formEditCourse">
            <?= csrf_field() ?>

            <div class="row g-4">
                <!-- Tiêu đề -->
                <div class="col-md-8">
                    <label class="form-label text-white small fw-bold">Tiêu đề khóa học <span class="text-danger">*</span></label>
                    <input type="text" name="title" id="courseTitle" class="form-control" value="<?= e($course['title']) ?>" required>
                </div>

                <!-- Slug -->
                <div class="col-md-4">
                    <label class="form-label text-white small fw-bold">Đường dẫn thân thiện (Slug)</label>
                    <input type="text" name="slug" id="courseSlug" class="form-control" value="<?= e($course['slug']) ?>">
                    <small class="text-white-50" style="font-size: 11px;">Định danh URL duy nhất của khóa học</small>
                </div>

                <!-- Giảng viên -->
                <div class="col-md-6">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label text-white small fw-bold mb-0">Giảng viên phụ trách <span class="text-danger">*</span></label>
                        <a href="/admin/lms/instructors" target="_blank" class="small text-info text-decoration-none">
                            <i class="bi bi-person-plus me-1"></i>Quản lý giảng viên
                        </a>
                    </div>
                    <select name="instructor_id" class="form-select" required>
                        <?php foreach ($instructors as $ins): ?>
                            <option value="<?= $ins['id'] ?>" <?= ((int)$course['instructor_id'] === (int)$ins['id']) ? 'selected' : '' ?>>
                                <?= e($ins['name']) ?> (<?= ($ins['role'] === 'instructor') ? 'Giảng viên' : 'Admin' ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Danh mục -->
                <div class="col-md-6">
                    <label class="form-label text-white small fw-bold">Danh mục lĩnh vực</label>
                    <select name="category_id" class="form-select">
                        <option value="">-- Chọn danh mục khóa học --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= ((int)($course['category_id'] ?? 0) === (int)$cat['id']) ? 'selected' : '' ?>>
                                <?= e($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Giá gốc -->
                <div class="col-md-4">
                    <label class="form-label text-white small fw-bold">Học phí gốc (VNĐ)</label>
                    <input type="number" name="price" class="form-control" value="<?= (float)$course['price'] ?>" min="0" step="any">
                    <small class="text-white-50" style="font-size: 11px;">Nhập 0 nếu là khóa học miễn phí</small>
                </div>

                <!-- Giá khuyến mãi -->
                <div class="col-md-4">
                    <label class="form-label text-white small fw-bold">Giá ưu đãi (VNĐ)</label>
                    <input type="number" name="sale_price" class="form-control" value="<?= ($course['sale_price'] !== null && $course['sale_price'] !== '') ? (float)$course['sale_price'] : '' ?>" placeholder="Để trống nếu không ưu đãi" min="0" step="any">
                </div>

                <!-- Cấp độ -->
                <div class="col-md-4">
                    <label class="form-label text-white small fw-bold">Cấp độ tiếp cận</label>
                    <select name="level" class="form-select">
                        <option value="all" <?= ($course['level'] === 'all') ? 'selected' : '' ?>>Mọi đối tượng (All Levels)</option>
                        <option value="beginner" <?= ($course['level'] === 'beginner') ? 'selected' : '' ?>>Người mới bắt đầu (Beginner)</option>
                        <option value="intermediate" <?= ($course['level'] === 'intermediate') ? 'selected' : '' ?>>Trung cấp (Intermediate)</option>
                        <option value="advanced" <?= ($course['level'] === 'advanced') ? 'selected' : '' ?>>Nâng cao (Advanced)</option>
                    </select>
                </div>

                <!-- Ảnh Thumbnail -->
                <div class="col-12">
                    <label class="form-label text-white small fw-bold">Ảnh Thumbnail URL</label>
                    <div class="input-group">
                        <input type="text" name="thumbnail" id="inputThumbnail" class="form-control" value="<?= e($course['thumbnail']) ?>">
                    </div>
                    <div class="d-flex align-items-center gap-2 mt-2 flex-wrap">
                        <span class="text-white-50 small" style="font-size: 11px;">Gợi ý nhanh:</span>
                        <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 rounded-pill small text-white-50" style="font-size: 11px;" onclick="document.getElementById('inputThumbnail').value='/image/courses/web_fullstack.jpg'">Fullstack Web</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 rounded-pill small text-white-50" style="font-size: 11px;" onclick="document.getElementById('inputThumbnail').value='/image/courses/python_ai.jpg'">Python & AI</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 rounded-pill small text-white-50" style="font-size: 11px;" onclick="document.getElementById('inputThumbnail').value='/image/courses/reactjs.jpg'">ReactJS</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 rounded-pill small text-white-50" style="font-size: 11px;" onclick="document.getElementById('inputThumbnail').value='/image/courses/uiux_figma.jpg'">UI/UX Figma</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 rounded-pill small text-white-50" style="font-size: 11px;" onclick="document.getElementById('inputThumbnail').value='/image/courses/devops_docker.jpg'">DevOps & Docker</button>
                    </div>
                </div>

                <!-- Mô tả ngắn -->
                <div class="col-12">
                    <label class="form-label text-white small fw-bold">Mô tả ngắn (Summary)</label>
                    <textarea name="summary" class="form-control" rows="2"><?= e($course['summary']) ?></textarea>
                </div>

                <!-- Nội dung chi tiết -->
                <div class="col-12">
                    <label class="form-label text-white small fw-bold">Nội dung chi tiết & Mục tiêu khóa học (HTML / Markdown)</label>
                    <textarea name="description" class="form-control" rows="6"><?= e($course['description']) ?></textarea>
                </div>

                <!-- Trạng thái xuất bản -->
                <div class="col-md-6">
                    <label class="form-label text-white small fw-bold">Trạng thái xuất bản</label>
                    <select name="status" class="form-select">
                        <option value="published" <?= ($course['status'] === 'published') ? 'selected' : '' ?>>Công khai (Published)</option>
                        <option value="draft" <?= ($course['status'] === 'draft') ? 'selected' : '' ?>>Lưu bản nháp (Draft)</option>
                        <option value="archived" <?= ($course['status'] === 'archived') ? 'selected' : '' ?>>Lưu trữ (Archived)</option>
                    </select>
                </div>

                <!-- Footer Buttons -->
                <div class="col-12 d-flex justify-content-between align-items-center pt-4 border-top border-white border-opacity-10 mt-3 flex-wrap gap-2">
                    <a href="/admin/lms/courses" class="btn btn-outline-secondary rounded-pill px-4">
                        <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách
                    </a>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <a href="/admin/lms/courses/<?= $course['id'] ?>/curriculum" class="btn btn-outline-info rounded-pill px-3 py-2">
                            <i class="bi bi-diagram-3 me-1"></i> Soạn giáo trình
                        </a>
                        <button type="submit" name="action" value="save_and_continue" class="btn btn-outline-warning rounded-pill px-4 py-2 fw-semibold" style="cursor: pointer;">
                            <i class="bi bi-arrow-clockwise me-1"></i> Lưu & Tiếp tục sửa
                        </button>
                        <button type="submit" name="action" value="save" class="btn btn-refract-action px-5 py-2 fw-bold" style="cursor: pointer;">
                            <i class="bi bi-check2-circle me-1"></i> Lưu thay đổi
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
// Auto transliterate slug suggestion on title blur if slug is empty
document.getElementById('courseTitle')?.addEventListener('blur', function() {
    const slugInput = document.getElementById('courseSlug');
    if (slugInput && slugInput.value.trim() === '') {
        let val = this.value.toLowerCase().trim();
        val = val.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
        val = val.replace(/[đĐ]/g, 'd');
        val = val.replace(/[^a-z0-9\s-]/g, '');
        val = val.replace(/[\s-]+/g, '-');
        slugInput.value = val.replace(/^-+|-+$/g, '');
    }
});
</script>
