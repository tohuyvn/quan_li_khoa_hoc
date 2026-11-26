<div class="container-fluid py-4" style="max-width: 1050px;">
    <!-- Breadcrumb & Header -->
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="/admin/lms/dashboard" class="text-white-50 text-decoration-none">LMS</a></li>
                <li class="breadcrumb-item"><a href="/admin/lms/courses" class="text-white-50 text-decoration-none">Khóa học</a></li>
                <li class="breadcrumb-item active text-white" aria-current="page">Thêm mới</li>
            </ol>
        </nav>
        <h1 class="h3 fw-bold text-white mb-1">Thêm Khóa Học Mới</h1>
        <p class="text-white-50 small mb-0">Điền thông tin chi tiết để khởi tạo khóa học và tiến hành xây dựng giáo trình bài giảng</p>
    </div>

    <!-- Form Glass Card -->
    <div class="refract-card p-4 p-md-5">
        <form method="post" action="/admin/lms/courses/store" id="formCreateCourse">
            <?= csrf_field() ?>

            <div class="row g-4">
                <!-- Tiêu đề -->
                <div class="col-md-8">
                    <label class="form-label text-white small fw-bold">Tiêu đề khóa học <span class="text-danger">*</span></label>
                    <input type="text" name="title" id="courseTitle" class="form-control" placeholder="VD: Lập trình PHP & MySQL Chuyên Sâu Thực Chiến" required>
                </div>

                <!-- Slug -->
                <div class="col-md-4">
                    <label class="form-label text-white small fw-bold">Đường dẫn thân thiện (Slug)</label>
                    <input type="text" name="slug" id="courseSlug" class="form-control" placeholder="Tự động tạo theo tiêu đề">
                    <small class="text-white-50" style="font-size: 11px;">Hệ thống tự động chuẩn hóa tiếng Việt không dấu</small>
                </div>

                <!-- Giảng viên -->
                <div class="col-md-6">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label text-white small fw-bold mb-0">Giảng viên phụ trách <span class="text-danger">*</span></label>
                        <a href="/admin/lms/instructors" target="_blank" class="small text-info text-decoration-none">
                            <i class="bi bi-person-plus me-1"></i>Thêm giảng viên mới
                        </a>
                    </div>
                    <select name="instructor_id" class="form-select" required>
                        <?php foreach ($instructors as $ins): ?>
                            <option value="<?= $ins['id'] ?>">
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
                            <option value="<?= $cat['id'] ?>"><?= e($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Giá gốc -->
                <div class="col-md-4">
                    <label class="form-label text-white small fw-bold">Học phí gốc (VNĐ)</label>
                    <input type="number" name="price" id="inputPrice" class="form-control" value="0" min="0" step="1000">
                    <small class="text-white-50" style="font-size: 11px;">Nhập 0 nếu là khóa học miễn phí</small>
                </div>

                <!-- Giá khuyến mãi -->
                <div class="col-md-4">
                    <label class="form-label text-white small fw-bold">Giá ưu đãi (VNĐ)</label>
                    <input type="number" name="sale_price" class="form-control" placeholder="Để trống nếu không ưu đãi" min="0" step="1000">
                </div>

                <!-- Cấp độ -->
                <div class="col-md-4">
                    <label class="form-label text-white small fw-bold">Cấp độ tiếp cận</label>
                    <select name="level" class="form-select">
                        <option value="all">Mọi đối tượng (All Levels)</option>
                        <option value="beginner">Người mới bắt đầu (Beginner)</option>
                        <option value="intermediate">Trung cấp (Intermediate)</option>
                        <option value="advanced">Nâng cao (Advanced)</option>
                    </select>
                </div>

                <!-- Ảnh Thumbnail -->
                <div class="col-12">
                    <label class="form-label text-white small fw-bold">Ảnh Thumbnail URL</label>
                    <div class="input-group">
                        <input type="text" name="thumbnail" id="inputThumbnail" class="form-control" value="/image/courses/web_fullstack.jpg">
                    </div>
                    <div class="d-flex align-items-center gap-2 mt-2 flex-wrap">
                        <span class="text-white-50 small" style="font-size: 11px;">Gợi ý nhanh:</span>
                        <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 rounded-pill small text-white-50" style="font-size: 11px;" onclick="document.getElementById('inputThumbnail').value='/image/courses/web_fullstack.jpg'">Fullstack Web</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 rounded-pill small text-white-50" style="font-size: 11px;" onclick="document.getElementById('inputThumbnail').value='/image/courses/python_ai.jpg'">Python & AI</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 rounded-pill small text-white-50" style="font-size: 11px;" onclick="document.getElementById('inputThumbnail').value='/image/courses/reactjs.jpg'">ReactJS</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 rounded-pill small text-white-50" style="font-size: 11px;" onclick="document.getElementById('inputThumbnail').value='/image/courses/uiux_figma.jpg'">UI/UX Figma</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 rounded-pill small text-white-50" style="font-size: 11px;" onclick="document.getElementById('inputThumbnail').value='/image/courses/devops_docker.jpg'">DevOps & Docker</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 rounded-pill small text-white-50" style="font-size: 11px;" onclick="document.getElementById('inputThumbnail').value='/image/banner1.png'">Default Banner</button>
                    </div>
                </div>

                <!-- Mô tả ngắn -->
                <div class="col-12">
                    <label class="form-label text-white small fw-bold">Mô tả ngắn (Summary)</label>
                    <textarea name="summary" class="form-control" rows="2" placeholder="Tóm tắt ngắn gọn 1-2 câu về nội dung khóa học hiển thị trên danh mục"></textarea>
                </div>

                <!-- Nội dung chi tiết -->
                <div class="col-12">
                    <label class="form-label text-white small fw-bold">Nội dung chi tiết & Mục tiêu khóa học (HTML / Markdown)</label>
                    <textarea name="description" class="form-control" rows="6" placeholder="Giới thiệu mục tiêu khóa học, các kiến thức cốt lõi đạt được sau lộ trình..."></textarea>
                </div>

                <!-- Trạng thái xuất bản -->
                <div class="col-md-6">
                    <label class="form-label text-white small fw-bold">Trạng thái xuất bản</label>
                    <select name="status" class="form-select">
                        <option value="published">Công khai ngay (Published)</option>
                        <option value="draft">Lưu bản nháp (Draft)</option>
                    </select>
                </div>

                <!-- Footer Buttons -->
                <div class="col-12 d-flex justify-content-between align-items-center pt-4 border-top border-white border-opacity-10 mt-3 flex-wrap gap-2">
                    <a href="/admin/lms/courses" class="btn btn-outline-secondary rounded-pill px-4">
                        <i class="bi bi-arrow-left me-1"></i> Hủy & Quay lại
                    </a>
                    <button type="submit" class="btn-refract-action px-5 py-2.5 fw-bold">
                        <i class="bi bi-check2-circle me-1"></i> Tạo khóa học & Soạn giáo trình
                    </button>
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
