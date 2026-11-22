<?php 
$title = 'Hồ sơ học viên - Hades Academy';
$user = $user ?? authUser();
$ordersCount = $ordersCount ?? 0;
$totalSpent = $totalSpent ?? 0;
$enrolledCount = $enrolledCount ?? 0;
$certsCount = $certsCount ?? 0;
$quizAttemptsCount = $quizAttemptsCount ?? 0;
$recentOrders = $recentOrders ?? [];

$points = (int)($user['loyalty_points'] ?? 0);
$tier = $user['loyalty_tier'] ?? 'Standard';
$initial = mb_strtoupper(mb_substr($user['name'] ?? 'H', 0, 1, 'UTF-8'), 'UTF-8');
?>

<div class="container-fluid py-2">
    <!-- Header Page Profile (Refract Glass Style) -->
    <div class="refract-card p-4 mb-4">
        <div class="row align-items-center g-3">
            <div class="col-auto">
                <div class="avatar-refract-pill" style="width: 72px; height: 72px; font-size: 1.8rem; border-width: 2px;">
                    <?= e($initial) ?>
                </div>
            </div>
            <div class="col">
                <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                    <h2 class="h3 mb-0 fw-bold text-white"><?= e($user['name']) ?></h2>
                    <span class="badge bg-primary bg-opacity-25 text-primary border border-primary border-opacity-25 rounded-pill px-3 py-1 small fw-semibold">
                        <i class="bi bi-mortarboard-fill me-1"></i> Học Viên Chính Thức
                    </span>
                    <span class="badge bg-warning bg-opacity-25 text-warning border border-warning border-opacity-25 rounded-pill px-2.5 py-1 small fw-semibold">
                        ⭐ <?= e($tier) ?> Member
                    </span>
                </div>
                <div class="d-flex align-items-center gap-3 text-white-50 small flex-wrap">
                    <span><i class="bi bi-envelope me-1 text-secondary"></i> <?= e($user['email']) ?></span>
                    <?php if (!empty($user['phone'])): ?>
                        <span><i class="bi bi-telephone me-1 text-secondary"></i> <?= e($user['phone']) ?></span>
                    <?php endif; ?>
                    <span><i class="bi bi-award me-1 text-warning"></i> <strong><?= number_format($points) ?></strong> điểm học tập</span>
                </div>
            </div>
            <div class="col-md-auto text-md-end pt-2 pt-md-0 d-flex gap-2">
                <a href="/my-courses" class="btn-refract-action btn-sm d-inline-flex align-items-center gap-1.5">
                    <i class="bi bi-play-circle-fill"></i> Vào phòng học
                </a>
                <form method="post" action="/logout" class="d-inline m-0" onsubmit="return confirm('Bạn chắc chắn muốn đăng xuất?')">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-outline-secondary btn-sm px-3 rounded-pill d-inline-flex align-items-center gap-1.5">
                        <i class="bi bi-box-arrow-right"></i> Đăng xuất
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Quick Stats Cards (4 Cards: Khóa học, Chứng chỉ, Bài thi, Đơn hàng) -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <a href="/my-courses" class="refract-card p-3 d-block text-decoration-none h-100">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="small text-muted fw-semibold">Khóa Học Đã Đăng Ký</span>
                    <i class="bi bi-journal-bookmark-fill text-primary fs-5"></i>
                </div>
                <div class="fs-4 fw-bold text-white"><?= number_format($enrolledCount) ?> <span class="fs-6 fw-normal text-muted">khóa</span></div>
                <div class="small text-white-50" style="font-size: 11px;">Đang theo học</div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="/verify-certificate" class="refract-card p-3 d-block text-decoration-none h-100">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="small text-muted fw-semibold">Chứng Chỉ Đạt Được</span>
                    <i class="bi bi-award-fill text-warning fs-5"></i>
                </div>
                <div class="fs-4 fw-bold text-warning"><?= number_format($certsCount) ?> <span class="fs-6 fw-normal text-muted">chứng chỉ</span></div>
                <div class="small text-white-50" style="font-size: 11px;">Xác thực QR công khai</div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="/my-courses" class="refract-card p-3 d-block text-decoration-none h-100">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="small text-muted fw-semibold">Lượt Thi Trắc Nghiệm</span>
                    <i class="bi bi-patch-question-fill text-info fs-5"></i>
                </div>
                <div class="fs-4 fw-bold text-info"><?= number_format($quizAttemptsCount) ?> <span class="fs-6 fw-normal text-muted">lượt làm bài</span></div>
                <div class="small text-white-50" style="font-size: 11px;">Quiz đánh giá năng lực</div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="/orders" class="refract-card p-3 d-block text-decoration-none h-100">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="small text-muted fw-semibold">Giao Dịch Đã Đặt</span>
                    <i class="bi bi-receipt text-success fs-5"></i>
                </div>
                <div class="fs-4 fw-bold text-success"><?= number_format($ordersCount) ?> <span class="fs-6 fw-normal text-muted">đơn hàng</span></div>
                <div class="small text-white-50" style="font-size: 11px;">Lịch sử hóa đơn học phí</div>
            </a>
        </div>
    </div>

    <!-- Main Content Area: Sidebar & Content Tabs -->
    <div class="row g-4">
        <!-- Left Tab Navigation -->
        <div class="col-lg-3">
            <div class="refract-card p-2">
                <div class="px-3 py-2 text-uppercase text-muted fw-bold border-bottom border-white border-opacity-10" style="font-size: 11px; letter-spacing: 0.5px;">
                    Hồ sơ học tập
                </div>
                <div class="nav flex-column gap-1 mt-2" id="profileTab" role="tablist">
                    <button class="sidebar-nav-item active border-0 text-start w-100 bg-transparent" id="tab-info-btn" data-bs-toggle="pill" data-bs-target="#tab-info" type="button" role="tab">
                        <i class="bi bi-person-gear"></i>
                        <span>Thông tin cá nhân</span>
                    </button>
                    <button class="sidebar-nav-item border-0 text-start w-100 bg-transparent" id="tab-certs-btn" data-bs-toggle="pill" data-bs-target="#tab-certs" type="button" role="tab">
                        <i class="bi bi-award-fill text-warning"></i>
                        <span>Chứng chỉ của tôi</span>
                        <?php if (!empty($certsCount)): ?>
                            <span class="badge bg-warning text-dark rounded-pill ms-auto small"><?= $certsCount ?></span>
                        <?php endif; ?>
                    </button>
                    <button class="sidebar-nav-item border-0 text-start w-100 bg-transparent" id="tab-password-btn" data-bs-toggle="pill" data-bs-target="#tab-password" type="button" role="tab">
                        <i class="bi bi-shield-lock"></i>
                        <span>Đổi mật khẩu</span>
                    </button>
                    <button class="sidebar-nav-item border-0 text-start w-100 bg-transparent" id="tab-orders-btn" data-bs-toggle="pill" data-bs-target="#tab-orders" type="button" role="tab">
                        <i class="bi bi-receipt"></i>
                        <span>Đơn hàng gần đây</span>
                    </button>
                </div>

                <div class="px-3 py-2 text-uppercase text-muted fw-bold border-top border-bottom border-white border-opacity-10 mt-3" style="font-size: 11px; letter-spacing: 0.5px;">
                    Lối tắt học tập
                </div>
                <div class="d-flex flex-column gap-1 mt-2">
                    <a href="/my-courses" class="sidebar-nav-item text-decoration-none">
                        <i class="bi bi-journal-bookmark text-warning"></i>
                        <span>Khóa học của tôi</span>
                    </a>
                    <a href="/courses" class="sidebar-nav-item text-decoration-none">
                        <i class="bi bi-compass text-info"></i>
                        <span>Khám phá khóa học</span>
                    </a>
                    <a href="/orders" class="sidebar-nav-item text-decoration-none">
                        <i class="bi bi-receipt text-primary"></i>
                        <span>Quản lý giao dịch</span>
                    </a>
                    <a href="/" class="sidebar-nav-item text-decoration-none text-white-50">
                        <i class="bi bi-house"></i>
                        <span>Về trang chủ</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Right Content Tab Panes -->
        <div class="col-lg-9">
            <?php if ($msg = flash('success')): ?>
                <div class="alert alert-success border-0 rounded-4 small py-2 px-3 mb-3 fw-medium bg-success bg-opacity-25 text-white">
                    <i class="bi bi-check-circle-fill me-1 text-success"></i><?= e($msg) ?>
                </div>
            <?php endif; ?>
            <?php if ($msg = flash('error')): ?>
                <div class="alert alert-danger border-0 rounded-4 small py-2 px-3 mb-3 fw-medium bg-danger bg-opacity-25 text-white">
                    <i class="bi bi-exclamation-triangle-fill me-1 text-danger"></i><?= e($msg) ?>
                </div>
            <?php endif; ?>

            <div class="tab-content" id="profileTabContent">
                <!-- TAB 1: Thông tin cá nhân -->
                <div class="tab-pane fade show active" id="tab-info" role="tabpanel">
                    <div class="refract-card p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom border-white border-opacity-10 flex-wrap gap-2">
                            <div>
                                <h5 class="fw-bold mb-1 text-white">Chỉnh Sửa Thông Tin Liên Hệ</h5>
                                <p class="text-muted small mb-0">Cập nhật họ tên và số điện thoại liên lạc học viên của bạn</p>
                            </div>
                            <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 px-3 py-1.5 rounded-pill fw-semibold small">
                                <i class="bi bi-shield-check me-1"></i> Tài khoản học viên xác thực
                            </span>
                        </div>

                        <form method="post" action="/profile">
                            <?= csrf_field() ?>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-white-50 mb-1">Họ và tên học viên *</label>
                                    <div class="form-floating-custom mb-0">
                                        <input type="text" name="name" class="form-control" value="<?= e($user['name']) ?>" required placeholder="Nguyễn Văn A">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-white-50 mb-1">Số điện thoại liên hệ</label>
                                    <div class="form-floating-custom mb-0">
                                        <input type="text" name="phone" class="form-control" value="<?= e($user['phone'] ?? '') ?>" placeholder="0912345678">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-semibold text-white-50 mb-1">Địa chỉ Email đăng nhập (Không thể thay đổi)</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <input type="email" class="form-control bg-dark bg-opacity-50 text-white-50" value="<?= e($user['email']) ?>" readonly disabled>
                                        <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 p-2 rounded-3"><i class="bi bi-check-circle-fill"></i></span>
                                    </div>
                                    <div class="form-text small text-white-50 mt-1" style="font-size: 11.5px;">Email này dùng để nhận mã OTP đăng nhập và thông báo hoàn thành chứng chỉ.</div>
                                </div>
                                <div class="col-12 pt-3">
                                    <button type="submit" class="btn-refract-action d-inline-flex align-items-center gap-2">
                                        <i class="bi bi-check-lg"></i>
                                        <span>Lưu Thông Tin</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- TAB: Chứng chỉ của tôi -->
                <div class="tab-pane fade" id="tab-certs" role="tabpanel">
                    <div class="refract-card p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom border-white border-opacity-10 flex-wrap gap-2">
                            <div>
                                <h5 class="fw-bold mb-1 text-white">Chứng Chỉ Khóa Học Của Tôi</h5>
                                <p class="text-muted small mb-0">Danh sách các văn bằng & chứng chỉ số bạn đã hoàn thành và đạt tiêu chuẩn tốt nghiệp</p>
                            </div>
                            <a href="/verify-certificate" class="btn btn-outline-info btn-sm rounded-pill px-3">
                                <i class="bi bi-patch-check me-1"></i> Tra cứu QR chứng chỉ
                            </a>
                        </div>

                        <?php if (empty($certificates)): ?>
                            <div class="text-center py-5">
                                <i class="bi bi-award text-muted mb-3" style="font-size: 56px;"></i>
                                <h6 class="text-white fw-bold">Chưa có chứng chỉ nào</h6>
                                <p class="text-muted small mb-4">Hãy hoàn thành 100% bài học và vượt qua bài kiểm tra cuối khóa để nhận chứng chỉ chính thức.</p>
                                <a href="/my-courses" class="btn-refract-action d-inline-flex align-items-center gap-2">
                                    <i class="bi bi-play-circle-fill"></i> Tiếp tục học ngay
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="row g-3">
                                <?php foreach ($certificates as $cert): ?>
                                    <div class="col-md-6">
                                        <div class="p-3 rounded-4 bg-dark bg-opacity-50 border border-warning border-opacity-25 h-100 d-flex flex-column justify-content-between">
                                            <div>
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <span class="badge bg-warning bg-opacity-25 text-warning border border-warning border-opacity-25 rounded-pill px-2.5 py-1 small fw-bold font-monospace">
                                                        <i class="bi bi-qr-code me-1"></i> <?= e($cert['certificate_code']) ?>
                                                    </span>
                                                    <span class="badge bg-success bg-opacity-25 text-success rounded-pill px-2 py-0.5 small">
                                                        <i class="bi bi-check-circle-fill me-1"></i> Hợp lệ
                                                    </span>
                                                </div>
                                                <h6 class="text-white fw-bold mb-1"><?= e($cert['course_title']) ?></h6>
                                                <div class="small text-white-50 mb-3">
                                                    <i class="bi bi-calendar-check me-1 text-warning"></i> Cấp ngày: <?= date('d/m/Y', strtotime($cert['issued_at'])) ?>
                                                </div>
                                            </div>
                                            <div class="d-flex gap-2 pt-2 border-top border-white border-opacity-10">
                                                <a href="/certificates/<?= e($cert['certificate_code']) ?>" class="btn btn-warning btn-sm fw-bold rounded-pill flex-grow-1 d-inline-flex justify-content-center align-items-center gap-1.5 text-dark">
                                                    <i class="bi bi-award-fill"></i> Xem & Tải Chứng Chỉ
                                                </a>
                                                <a href="https://www.linkedin.com/profile/add?startTask=CERTIFICATION_NAME&name=<?= urlencode($cert['course_title']) ?>&organizationName=Hades+Academy&certId=<?= urlencode($cert['certificate_code']) ?>" target="_blank" class="btn btn-outline-light btn-sm rounded-pill px-2.5" title="Thêm vào LinkedIn">
                                                    <i class="bi bi-linkedin text-info"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- TAB 2: Đổi mật khẩu -->
                <div class="tab-pane fade" id="tab-password" role="tabpanel">
                    <div class="refract-card p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom border-white border-opacity-10">
                            <div>
                                <h5 class="fw-bold mb-1 text-white">Đổi Mật Khẩu Tài Khoản</h5>
                                <p class="text-muted small mb-0">Thiết lập mật khẩu mới có độ bảo mật cao</p>
                            </div>
                        </div>

                        <form method="post" action="/profile">
                            <?= csrf_field() ?>
                            <input type="hidden" name="name" value="<?= e($user['name']) ?>">
                            <input type="hidden" name="phone" value="<?= e($user['phone'] ?? '') ?>">

                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label small fw-semibold text-white-50 mb-1">Mật khẩu hiện tại *</label>
                                    <input type="password" name="current_password" class="form-control" placeholder="Nhập mật khẩu hiện tại" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-white-50 mb-1">Mật khẩu mới *</label>
                                    <input type="password" name="new_password" class="form-control" placeholder="Tối thiểu 6 ký tự" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-white-50 mb-1">Xác nhận mật khẩu mới *</label>
                                    <input type="password" name="confirm_password" class="form-control" placeholder="Nhập lại mật khẩu mới" required>
                                </div>
                                <div class="col-12 pt-3 d-flex gap-2">
                                    <button type="submit" class="btn-refract-action d-inline-flex align-items-center gap-2">
                                        <i class="bi bi-key-fill"></i>
                                        <span>Cập Nhật Mật Khẩu</span>
                                    </button>
                                    <a href="/forgot-password" class="btn btn-outline-secondary btn-sm px-3 rounded-pill d-inline-flex align-items-center">Quên mật khẩu?</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- TAB: Chứng chỉ của tôi -->
                <div class="tab-pane fade" id="tab-certs" role="tabpanel">
                    <div class="refract-card p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom border-white border-opacity-10 flex-wrap gap-2">
                            <div>
                                <h5 class="fw-bold mb-1 text-white">Chứng Chỉ Khóa Học Của Tôi</h5>
                                <p class="text-muted small mb-0">Danh sách các văn bằng & chứng chỉ số bạn đã hoàn thành và đạt tiêu chuẩn tốt nghiệp</p>
                            </div>
                            <a href="/verify-certificate" class="btn btn-outline-info btn-sm rounded-pill px-3">
                                <i class="bi bi-patch-check me-1"></i> Tra cứu QR chứng chỉ
                            </a>
                        </div>

                        <?php if (empty($certificates)): ?>
                            <div class="text-center py-5">
                                <i class="bi bi-award text-muted mb-3" style="font-size: 56px;"></i>
                                <h6 class="text-white fw-bold">Chưa có chứng chỉ nào</h6>
                                <p class="text-muted small mb-4">Hãy hoàn thành 100% bài học và vượt qua bài kiểm tra cuối khóa để nhận chứng chỉ chính thức.</p>
                                <a href="/my-courses" class="btn-refract-action d-inline-flex align-items-center gap-2">
                                    <i class="bi bi-play-circle-fill"></i> Tiếp tục học ngay
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="row g-3">
                                <?php foreach ($certificates as $cert): ?>
                                    <div class="col-md-6">
                                        <div class="p-3 rounded-4 bg-dark bg-opacity-50 border border-warning border-opacity-25 h-100 d-flex flex-column justify-content-between">
                                            <div>
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <span class="badge bg-warning bg-opacity-25 text-warning border border-warning border-opacity-25 rounded-pill px-2.5 py-1 small fw-bold font-monospace">
                                                        <i class="bi bi-qr-code me-1"></i> <?= e($cert['certificate_code']) ?>
                                                    </span>
                                                    <span class="badge bg-success bg-opacity-25 text-success rounded-pill px-2 py-0.5 small">
                                                        <i class="bi bi-check-circle-fill me-1"></i> Hợp lệ
                                                    </span>
                                                </div>
                                                <h6 class="text-white fw-bold mb-1"><?= e($cert['course_title']) ?></h6>
                                                <div class="small text-white-50 mb-3">
                                                    <i class="bi bi-calendar-check me-1 text-warning"></i> Cấp ngày: <?= date('d/m/Y', strtotime($cert['issued_at'])) ?>
                                                </div>
                                            </div>
                                            <div class="d-flex gap-2 pt-2 border-top border-white border-opacity-10">
                                                <a href="/certificates/<?= e($cert['certificate_code']) ?>" class="btn btn-warning btn-sm fw-bold rounded-pill flex-grow-1 d-inline-flex justify-content-center align-items-center gap-1.5 text-dark">
                                                    <i class="bi bi-award-fill"></i> Xem & Tải Chứng Chỉ
                                                </a>
                                                <a href="https://www.linkedin.com/profile/add?startTask=CERTIFICATION_NAME&name=<?= urlencode($cert['course_title']) ?>&organizationName=Hades+Academy&certId=<?= urlencode($cert['certificate_code']) ?>" target="_blank" class="btn btn-outline-light btn-sm rounded-pill px-2.5" title="Thêm vào LinkedIn">
                                                    <i class="bi bi-linkedin text-info"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- TAB 3: Đơn hàng gần đây -->
                <div class="tab-pane fade" id="tab-orders" role="tabpanel">
                    <div class="refract-card p-0 overflow-hidden">
                        <div class="p-4 d-flex justify-content-between align-items-center border-bottom border-white border-opacity-10 flex-wrap gap-2">
                            <div>
                                <h5 class="fw-bold mb-1 text-white">Đơn Hàng & Giao Dịch Gần Đây</h5>
                                <p class="text-muted small mb-0">Theo dõi các giao dịch thanh toán học phí và khóa học</p>
                            </div>
                            <a href="/orders" class="btn btn-outline-primary btn-sm rounded-pill px-3 d-inline-flex align-items-center gap-1.5">
                                Xem tất cả <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>

                        <?php if (empty($recentOrders)): ?>
                            <div class="text-center py-5">
                                <div class="text-muted py-3">
                                    <i class="bi bi-box-seam fs-1 d-block mb-3 text-secondary opacity-50"></i>
                                    <h5 class="fw-bold text-white">Bạn chưa có đơn hàng nào</h5>
                                    <p class="small text-muted mb-3">Khám phá ngay các khóa học chất lượng tại Hades Academy.</p>
                                    <a href="/courses" class="btn-refract-action btn-sm d-inline-flex align-items-center gap-1.5">
                                        <i class="bi bi-compass"></i> Khám phá khóa học
                                    </a>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th class="ps-4">Mã Đơn</th>
                                            <th>Ngày Đặt</th>
                                            <th>Tổng Tiền</th>
                                            <th class="text-center">Thanh Toán</th>
                                            <th class="text-center">Trạng Thái</th>
                                            <th class="text-end pe-4">Thao Tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recentOrders as $ord): 
                                            $st = $ord['status'] ?? 'pending';
                                            $paySt = $ord['payment_status'] ?? 'pending';
                                        ?>
                                            <tr>
                                                <td class="ps-4">
                                                    <span class="fw-bold font-monospace text-warning px-2.5 py-1 rounded-pill bg-white bg-opacity-10 border border-white border-opacity-10 small">
                                                        <?= e($ord['order_code'] ?? ('#' . $ord['id'])) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="text-white-50 small">
                                                        <i class="bi bi-clock-history me-1 text-secondary"></i>
                                                        <span><?= date('d/m/Y H:i', strtotime($ord['created_at'])) ?></span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="fw-bold text-danger fs-6"><?= formatMoney($ord['total_amount']) ?></span>
                                                </td>
                                                <td class="text-center">
                                                    <?php if ($paySt === 'paid'): ?>
                                                        <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 rounded-pill px-2.5 py-1 small fw-semibold">
                                                            <i class="bi bi-check-circle-fill me-1"></i> Đã thanh toán
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge bg-warning bg-opacity-25 text-warning border border-warning border-opacity-25 rounded-pill px-2.5 py-1 small fw-semibold">
                                                            <i class="bi bi-hourglass-split me-1"></i> Chưa thanh toán
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-primary bg-opacity-25 text-primary border border-primary border-opacity-25 rounded-pill px-2.5 py-1 small fw-semibold">
                                                        <?= e($st) ?>
                                                    </span>
                                                </td>
                                                <td class="text-end pe-4">
                                                    <a href="/orders/<?= $ord['id'] ?>" class="btn btn-outline-secondary btn-sm px-3 d-inline-flex align-items-center gap-1.5">
                                                        <i class="bi bi-eye-fill"></i> Chi tiết
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
