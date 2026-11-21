<div class="container-fluid py-4">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-warning bg-opacity-25 text-warning border border-warning border-opacity-25 rounded-pill px-3 py-1">
                    <i class="bi bi-patch-question me-1"></i> Quiz Engine
                </span>
                <span class="text-white-50 small">• <?= count($quizzes ?? []) ?> đề trắc nghiệm</span>
            </div>
            <h1 class="h3 fw-bold text-white mb-1">Quản Lý Bài Kiểm Tra & Câu Hỏi</h1>
            <p class="text-muted mb-0 small">Quản lý ngân hàng đề thi, bài trắc nghiệm và thang điểm các khóa học trực tuyến</p>
        </div>
        <div class="d-flex gap-2">
            <a href="/admin/lms/quiz-results" class="btn btn-outline-primary btn-sm rounded-pill px-3 d-flex align-items-center gap-2">
                <i class="bi bi-file-earmark-check"></i> Xem Kết Quả Thi Của Học Viên
            </a>
        </div>
    </div>

    <!-- Glass Card Table -->
    <div class="refract-card p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Bài kiểm tra</th>
                        <th>Thuộc khóa học</th>
                        <th>Thời gian làm bài</th>
                        <th>Điểm chuẩn</th>
                        <th class="text-center">Số câu hỏi</th>
                        <th class="text-center">Lượt học viên thi</th>
                        <th class="text-end pe-4">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($quizzes)): ?>
                        <tr><td colspan="7" class="text-center py-5 text-muted">Chưa có bài kiểm tra nào trong hệ thống.</td></tr>
                    <?php else: ?>
                        <?php foreach ($quizzes as $quiz): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-white fs-6 mb-1"><?= e($quiz['title']) ?></div>
                                    <div class="text-muted small d-flex align-items-center gap-1">
                                        <i class="bi bi-journal-text text-secondary"></i>
                                        <span><?= e($quiz['lesson_title'] ?? 'Bài kiểm tra cuối khóa') ?></span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary bg-opacity-25 text-light border border-secondary border-opacity-25 px-2.5 py-1.5 rounded-pill small fw-normal">
                                        <i class="bi bi-mortarboard me-1 text-warning"></i> <?= e($quiz['course_title']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-inline-flex align-items-center gap-1.5 text-white-50 small">
                                        <i class="bi bi-clock-history text-info"></i>
                                        <span class="fw-semibold text-white"><?= (int)$quiz['duration_minutes'] ?></span> phút
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-warning bg-opacity-25 text-warning border border-warning border-opacity-50 px-2.5 py-1 rounded-pill fw-semibold">
                                        <i class="bi bi-trophy-fill me-1"></i> <?= (float)$quiz['passing_score'] ?>%
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary bg-opacity-25 text-primary border border-primary border-opacity-25 px-2.5 py-1 rounded-pill fw-semibold">
                                        <?= (int)$quiz['question_count'] ?> câu
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="text-white-50 small fw-semibold">
                                        <i class="bi bi-people me-1 text-secondary"></i> <?= (int)$quiz['attempt_count'] ?> lượt
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-inline-flex gap-2">
                                        <a href="/admin/lms/quizzes/<?= $quiz['id'] ?>/questions" class="btn btn-outline-primary btn-sm px-3 d-inline-flex align-items-center gap-1.5" title="Ngân hàng câu hỏi & đáp án">
                                            <i class="bi bi-pencil-square"></i> Soạn câu hỏi
                                        </a>
                                        <a href="/quiz/<?= $quiz['id'] ?>" target="_blank" class="btn btn-outline-secondary btn-sm px-3 d-inline-flex align-items-center gap-1.5" title="Làm thử bài thi">
                                            <i class="bi bi-play-circle-fill text-success"></i> Thi thử
                                        </a>
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
