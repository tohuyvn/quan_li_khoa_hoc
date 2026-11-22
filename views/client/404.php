<?php 
$title = '404 - Không tìm thấy trang'; 
$courseModel = new \App\Models\Course();
$featuredCourses = $courseModel->all(['limit' => 3]);
?>

<div class="content-card text-center py-5 position-relative overflow-hidden" style="border-radius: 20px;">
    <div style="position: absolute; top: -100px; left: 50%; transform: translateX(-50%); width: 300px; height: 300px; background: radial-gradient(circle, rgba(239, 68, 68, 0.15) 0%, rgba(0,0,0,0) 70%); pointer-events: none;"></div>

    <div class="mb-3 d-inline-block px-4 py-2 rounded-pill bg-danger bg-opacity-10 text-danger fw-extrabold" style="font-size: 1.2rem; letter-spacing: 2px;">
        ⚠️ ERROR 404
    </div>

    <h1 style="font-size: 120px; line-height: 1; margin-bottom: 10px; font-weight: 900; background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
        404
    </h1>

    <h2 class="fw-bold mb-3 text-white">Rất tiếc, trang bạn tìm kiếm không tồn tại!</h2>
    <p class="text-white-50 mb-4 mx-auto" style="max-width: 540px; font-size: 0.95rem;">
        Đường dẫn bài học hoặc khóa học này không còn tồn tại trên Hades Academy. Bạn có thể khám phá các khóa học nổi bật bên dưới hoặc quay lại trang chủ.
    </p>

    <div class="d-flex justify-content-center gap-3 mb-5">
        <a href="/courses" class="btn btn-primary px-4 py-2 rounded-pill">
            <i class="fas fa-graduation-cap me-2"></i>Xem tất cả khóa học
        </a>
        <a href="/" class="btn btn-outline-light px-4 py-2 rounded-pill">
            <i class="fas fa-home me-2"></i>Trang chủ
        </a>
    </div>

    <?php if (!empty($featuredCourses)): ?>
    <div class="text-start mt-5 pt-4 border-top border-secondary border-opacity-25">
        <h4 class="text-white fw-bold mb-3"><i class="fas fa-fire text-warning me-2"></i>Khóa học đề xuất cho bạn</h4>
        <div class="row g-3">
            <?php foreach ($featuredCourses as $c): ?>
            <div class="col-md-4">
                <div class="card bg-dark border border-secondary border-opacity-25 text-white h-100 rounded-3 overflow-hidden">
                    <?php if (!empty($c['thumbnail'])): ?>
                    <img src="<?= htmlspecialchars($c['thumbnail']) ?>" class="card-img-top" style="height: 140px; object-fit: cover;" alt="<?= htmlspecialchars($c['title']) ?>">
                    <?php endif; ?>
                    <div class="card-body d-flex flex-column">
                        <h6 class="fw-bold text-white mb-2"><?= htmlspecialchars($c['title']) ?></h6>
                        <p class="small text-white-50 flex-grow-1"><?= htmlspecialchars(mb_strimwidth($c['summary'] ?? '', 0, 80, '...')) ?></p>
                        <a href="/courses/<?= htmlspecialchars($c['slug'] ?? $c['id']) ?>" class="btn btn-sm btn-outline-primary mt-2">Xem khóa học</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>
