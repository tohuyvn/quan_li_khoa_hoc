<div class="container-fluid py-4" style="max-width: 1050px;">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="/admin/lms/dashboard" class="text-white-50 text-decoration-none">LMS</a></li>
                    <li class="breadcrumb-item"><a href="/admin/lms/courses" class="text-white-50 text-decoration-none">Khóa học</a></li>
                    <li class="breadcrumb-item active text-white" aria-current="page"><?= e($course['title']) ?></li>
                </ol>
            </nav>
            <h1 class="h3 fw-bold text-white mb-0">Quản Lý Giáo Trình & Bài Học</h1>
            <p class="text-white-50 small mb-0">Khóa học: <span class="text-warning fw-semibold"><?= e($course['title']) ?></span></p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="/admin/lms/courses/<?= $course['id'] ?>/edit" class="btn btn-outline-warning btn-sm rounded-pill px-3 d-inline-flex align-items-center gap-1.5">
                <i class="bi bi-pencil-square"></i> Sửa thông tin khóa
            </a>
            <button class="btn btn-refract-action btn-sm rounded-pill px-3 d-inline-flex align-items-center gap-1.5" data-bs-toggle="modal" data-bs-target="#modalAddSection">
                <i class="bi bi-folder-plus"></i> Thêm Chương Mới
            </button>
            <a href="/courses/<?= e($course['slug'] ?: $course['id']) ?>" target="_blank" class="btn btn-outline-secondary btn-sm rounded-pill px-3 d-inline-flex align-items-center gap-1.5 text-white-50">
                <i class="bi bi-box-arrow-up-right"></i> Xem trên web
            </a>
        </div>
    </div>

    <!-- Curriculum Tree -->
    <?php if (empty($curriculum)): ?>
        <div class="refract-card p-5 text-center">
            <i class="bi bi-diagram-3 display-3 text-secondary opacity-50 mb-3 d-block"></i>
            <h5 class="fw-bold text-white">Chưa có chương học nào</h5>
            <p class="text-white-50 small mb-3">Bắt đầu bằng việc thêm một chương (Section) mới cho khóa học để phân chia lộ trình đào tạo.</p>
            <div>
                <button class="btn btn-refract-action rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalAddSection">
                    <i class="bi bi-plus-lg me-1"></i> Thêm Chương Học Đầu Tiên
                </button>
            </div>
        </div>
    <?php else: ?>
        <div class="d-flex flex-column gap-4">
            <?php foreach ($curriculum as $sIdx => $section): ?>
                <div class="refract-card p-0 overflow-hidden">
                    <div class="p-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2 border-bottom border-white border-opacity-10 bg-white bg-opacity-5">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-warning bg-opacity-25 text-warning border border-warning border-opacity-25 rounded-pill px-2.5 py-1">Chương <?= $sIdx + 1 ?></span>
                            <h5 class="fw-bold text-white mb-0"><?= e($section['title']) ?></h5>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1" onclick="openEditSectionModal(<?= $section['id'] ?>, '<?= addslashes(e($section['title'])) ?>', <?= (int)$section['sort_order'] ?>)" title="Đổi tên chương">
                                <i class="bi bi-pencil-square"></i> Sửa tên
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-info rounded-pill px-3 d-inline-flex align-items-center gap-1" onclick="openAddLessonModal(<?= $section['id'] ?>, '<?= addslashes(e($section['title'])) ?>')">
                                <i class="bi bi-plus-circle"></i> Thêm bài học
                            </button>
                            <form method="post" action="/admin/lms/courses/<?= $course['id'] ?>/sections/<?= $section['id'] ?>/delete" class="m-0" onsubmit="return confirm('Bạn có chắc muốn xóa chương học này cùng các bài giảng bên trong?');">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2.5 py-1" title="Xóa chương học">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="p-0">
                        <?php if (empty($section['lessons'])): ?>
                            <div class="p-4 text-center text-white-50 small">Chưa có bài học nào trong chương này. Nhấn "Thêm bài học" để cập nhật.</div>
                        <?php else: ?>
                            <div class="list-group list-group-flush bg-transparent">
                                <?php foreach ($section['lessons'] as $les): ?>
                                    <div class="list-group-item bg-transparent border-bottom border-white border-opacity-5 d-flex justify-content-between align-items-center px-4 py-3 text-white">
                                        <div class="d-flex align-items-center gap-3">
                                            <?php if ($les['lesson_type'] === 'video'): ?>
                                                <i class="bi bi-play-circle-fill text-primary fs-4"></i>
                                            <?php elseif ($les['lesson_type'] === 'quiz'): ?>
                                                <i class="bi bi-patch-question-fill text-warning fs-4"></i>
                                            <?php else: ?>
                                                <i class="bi bi-file-text-fill text-secondary fs-4"></i>
                                            <?php endif; ?>
                                            <div>
                                                <div class="fw-bold text-white"><?= e($les['title']) ?></div>
                                                <div class="small text-white-50" style="font-size: 11px;">
                                                    Loại: <span class="badge bg-white bg-opacity-10 text-white-50 border border-white border-opacity-10"><?= e($les['lesson_type']) ?></span>
                                                    <?php if (!empty($les['is_preview'])): ?>
                                                        <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 ms-1">Xem thử</span>
                                                    <?php endif; ?>
                                                    <?php if (!empty($les['video_url'])): ?>
                                                        <span class="text-white-50 ms-2 text-truncate d-inline-block" style="max-width: 250px; vertical-align: middle;">
                                                            <i class="bi bi-link-45deg"></i> <?= e($les['video_url']) ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="text-white-50 small me-2">
                                                <?= $les['duration_seconds'] ? gmdate("i:s", $les['duration_seconds']) : '' ?>
                                            </span>
                                            <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-2.5 py-0.5 d-inline-flex align-items-center gap-1" onclick='openEditLessonModal(<?= json_encode([
                                                "id" => $les["id"],
                                                "section_id" => $section["id"],
                                                "title" => $les["title"],
                                                "lesson_type" => $les["lesson_type"],
                                                "video_url" => $les["video_url"] ?? "",
                                                "duration_seconds" => $les["duration_seconds"] ?? 600,
                                                "is_preview" => !empty($les["is_preview"]) ? 1 : 0,
                                                "content" => $les["content"] ?? ""
                                            ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' title="Chỉnh sửa bài học">
                                                <i class="bi bi-pencil-square" style="font-size: 12px;"></i> Sửa
                                            </button>
                                            <form method="post" action="/admin/lms/courses/<?= $course['id'] ?>/lessons/<?= $les['id'] ?>/delete" class="m-0" onsubmit="return confirm('Bạn có chắc muốn xóa bài học này?');">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-0.5" title="Xóa bài học">
                                                    <i class="bi bi-trash3" style="font-size: 11px;"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Modal Add Section -->
<div class="modal fade" id="modalAddSection" tabindex="-1">
    <div class="modal-dialog">
        <form method="post" action="/admin/lms/courses/<?= $course['id'] ?>/sections">
            <?= csrf_field() ?>
            <div class="modal-content refract-card border border-white border-opacity-10 text-white rounded-4 shadow-lg p-3">
                <div class="modal-header border-bottom border-white border-opacity-10 pb-3">
                    <h5 class="modal-title fw-bold text-white d-flex align-items-center gap-2">
                        <i class="bi bi-folder-plus text-warning"></i>
                        <span>Thêm Chương Học Mới</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-4">
                    <label class="form-label text-white small fw-bold">Tên chương / Module đào tạo <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" placeholder="VD: Chương 1: Kiến thức nền tảng & Cấu hình môi trường" required>
                </div>
                <div class="modal-footer border-top border-white border-opacity-10 pt-3">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-refract-action px-4 py-2">
                        <i class="bi bi-check2-circle me-1"></i> Lưu chương
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Section -->
<div class="modal fade" id="modalEditSection" tabindex="-1">
    <div class="modal-dialog">
        <form method="post" id="formEditSection" action="">
            <?= csrf_field() ?>
            <div class="modal-content refract-card border border-white border-opacity-10 text-white rounded-4 shadow-lg p-3">
                <div class="modal-header border-bottom border-white border-opacity-10 pb-3">
                    <h5 class="modal-title fw-bold text-white d-flex align-items-center gap-2">
                        <i class="bi bi-pencil-square text-warning"></i>
                        <span>Chỉnh Sửa Tên Chương Học</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-4">
                    <div class="mb-3">
                        <label class="form-label text-white small fw-bold">Tên chương / Module đào tạo <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="editSectionTitle" class="form-control" required>
                    </div>
                    <div>
                        <label class="form-label text-white small fw-bold">Thứ tự hiển thị (Sort Order)</label>
                        <input type="number" name="sort_order" id="editSectionSortOrder" class="form-control" min="0" step="1">
                    </div>
                </div>
                <div class="modal-footer border-top border-white border-opacity-10 pt-3">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-refract-action px-4 py-2">
                        <i class="bi bi-check2-circle me-1"></i> Cập nhật chương
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Add Lesson -->
<div class="modal fade" id="modalAddLesson" tabindex="-1">
    <div class="modal-dialog">
        <form method="post" action="/admin/lms/courses/<?= $course['id'] ?>/lessons">
            <?= csrf_field() ?>
            <input type="hidden" name="section_id" id="inputSectionId" value="">
            <div class="modal-content refract-card border border-white border-opacity-10 text-white rounded-4 shadow-lg p-3">
                <div class="modal-header border-bottom border-white border-opacity-10 pb-3">
                    <h5 class="modal-title fw-bold text-white d-flex align-items-center gap-2">
                        <i class="bi bi-plus-circle text-info"></i>
                        <span>Thêm Bài Học Mới</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-3">
                    <div class="mb-3">
                        <label class="form-label text-white-50 small mb-1">Thuộc chương:</label>
                        <div id="displaySectionName" class="text-warning fw-semibold"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white small fw-bold">Tiêu đề bài học <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" placeholder="VD: Bài 1: Cài đặt Composer và PHP 8.2" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white small fw-bold">Loại bài học</label>
                        <select name="lesson_type" class="form-select" id="selectLessonType" onchange="toggleLessonTypeFields(this.value, 'add')">
                            <option value="video">Video bài giảng</option>
                            <option value="document">Bài đọc tài liệu</option>
                            <option value="quiz">Bài thi trắc nghiệm (Quiz)</option>
                        </select>
                    </div>
                    <div class="mb-3" id="groupAddVideoUrl">
                        <label class="form-label text-white small fw-bold">Link Video (Youtube / Vimeo / Direct URL)</label>
                        <input type="text" name="video_url" class="form-control" placeholder="https://www.youtube.com/watch?v=...">
                    </div>
                    <div class="mb-3" id="groupAddDuration">
                        <label class="form-label text-white small fw-bold">Thời lượng ước tính (giây)</label>
                        <input type="number" name="duration_seconds" class="form-control" value="600" min="0" step="10">
                        <small class="text-white-50" style="font-size: 11px;">600 giây = 10 phút</small>
                    </div>
                    <div class="mb-3 d-none" id="groupAddContent">
                        <label class="form-label text-white small fw-bold">Nội dung bài học (HTML / Markdown)</label>
                        <textarea name="content" class="form-control" rows="4" placeholder="Nội dung bài học hoặc tài liệu hướng dẫn..."></textarea>
                    </div>
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" name="is_preview" value="1" id="checkIsPreview">
                        <label class="form-check-label text-white small fw-semibold" for="checkIsPreview">Cho phép học viên xem thử miễn phí</label>
                    </div>
                </div>
                <div class="modal-footer border-top border-white border-opacity-10 pt-3">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-refract-action px-4 py-2">
                        <i class="bi bi-check2-circle me-1"></i> Lưu bài học
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Lesson -->
<div class="modal fade" id="modalEditLesson" tabindex="-1">
    <div class="modal-dialog">
        <form method="post" id="formEditLesson" action="">
            <?= csrf_field() ?>
            <div class="modal-content refract-card border border-white border-opacity-10 text-white rounded-4 shadow-lg p-3">
                <div class="modal-header border-bottom border-white border-opacity-10 pb-3">
                    <h5 class="modal-title fw-bold text-white d-flex align-items-center gap-2">
                        <i class="bi bi-pencil-square text-info"></i>
                        <span>Chỉnh Sửa Bài Học</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-3">
                    <div class="mb-3">
                        <label class="form-label text-white small fw-bold">Chương chứa bài học</label>
                        <select name="section_id" id="editLessonSectionId" class="form-select">
                            <?php foreach ($curriculum as $secOption): ?>
                                <option value="<?= $secOption['id'] ?>"><?= e($secOption['title']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white small fw-bold">Tiêu đề bài học <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="editLessonTitle" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white small fw-bold">Loại bài học</label>
                        <select name="lesson_type" id="editLessonType" class="form-select" onchange="toggleLessonTypeFields(this.value, 'edit')">
                            <option value="video">Video bài giảng</option>
                            <option value="document">Bài đọc tài liệu</option>
                            <option value="quiz">Bài thi trắc nghiệm (Quiz)</option>
                        </select>
                    </div>
                    <div class="mb-3" id="groupEditVideoUrl">
                        <label class="form-label text-white small fw-bold">Link Video (Youtube / Vimeo / Direct URL)</label>
                        <input type="text" name="video_url" id="editLessonVideoUrl" class="form-control" placeholder="https://www.youtube.com/watch?v=...">
                    </div>
                    <div class="mb-3" id="groupEditDuration">
                        <label class="form-label text-white small fw-bold">Thời lượng ước tính (giây)</label>
                        <input type="number" name="duration_seconds" id="editLessonDuration" class="form-control" min="0" step="10">
                        <small class="text-white-50" style="font-size: 11px;">600 giây = 10 phút</small>
                    </div>
                    <div class="mb-3 d-none" id="groupEditContent">
                        <label class="form-label text-white small fw-bold">Nội dung bài học (HTML / Markdown)</label>
                        <textarea name="content" id="editLessonContent" class="form-control" rows="4"></textarea>
                    </div>
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" name="is_preview" value="1" id="editLessonIsPreview">
                        <label class="form-check-label text-white small fw-semibold" for="editLessonIsPreview">Cho phép học viên xem thử miễn phí</label>
                    </div>
                </div>
                <div class="modal-footer border-top border-white border-opacity-10 pt-3">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-refract-action px-4 py-2">
                        <i class="bi bi-check2-circle me-1"></i> Cập nhật bài học
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
const courseId = <?= (int)$course['id'] ?>;

function openAddLessonModal(sectionId, sectionName) {
    document.getElementById('inputSectionId').value = sectionId;
    document.getElementById('displaySectionName').textContent = sectionName;
    const modal = new bootstrap.Modal(document.getElementById('modalAddLesson'));
    modal.show();
}

function openEditSectionModal(sectionId, title, sortOrder) {
    document.getElementById('formEditSection').action = '/admin/lms/courses/' + courseId + '/sections/' + sectionId + '/update';
    document.getElementById('editSectionTitle').value = title;
    document.getElementById('editSectionSortOrder').value = sortOrder || 0;
    const modal = new bootstrap.Modal(document.getElementById('modalEditSection'));
    modal.show();
}

function openEditLessonModal(les) {
    document.getElementById('formEditLesson').action = '/admin/lms/courses/' + courseId + '/lessons/' + les.id + '/update';
    document.getElementById('editLessonSectionId').value = les.section_id;
    document.getElementById('editLessonTitle').value = les.title || '';
    document.getElementById('editLessonType').value = les.lesson_type || 'video';
    document.getElementById('editLessonVideoUrl').value = les.video_url || '';
    document.getElementById('editLessonDuration').value = les.duration_seconds || 600;
    document.getElementById('editLessonContent').value = les.content || '';
    document.getElementById('editLessonIsPreview').checked = !!les.is_preview;

    toggleLessonTypeFields(les.lesson_type || 'video', 'edit');

    const modal = new bootstrap.Modal(document.getElementById('modalEditLesson'));
    modal.show();
}

function toggleLessonTypeFields(type, mode) {
    const isEdit = mode === 'edit';
    const videoGroup = document.getElementById(isEdit ? 'groupEditVideoUrl' : 'groupAddVideoUrl');
    const durationGroup = document.getElementById(isEdit ? 'groupEditDuration' : 'groupAddDuration');
    const contentGroup = document.getElementById(isEdit ? 'groupEditContent' : 'groupAddContent');

    if (type === 'video') {
        videoGroup?.classList.remove('d-none');
        durationGroup?.classList.remove('d-none');
        contentGroup?.classList.add('d-none');
    } else if (type === 'document') {
        videoGroup?.classList.add('d-none');
        durationGroup?.classList.remove('d-none');
        contentGroup?.classList.remove('d-none');
    } else {
        videoGroup?.classList.add('d-none');
        durationGroup?.classList.add('d-none');
        contentGroup?.classList.add('d-none');
    }
}
</script>