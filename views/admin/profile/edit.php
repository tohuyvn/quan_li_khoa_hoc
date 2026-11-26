<div class="container-fluid py-4" style="max-width: 960px;">
    <!-- Breadcrumb & Header -->
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="/admin/lms/dashboard" class="text-white-50 text-decoration-none">LMS</a></li>
                <li class="breadcrumb-item"><a href="/admin/lms/dashboard" class="text-white-50 text-decoration-none">Hệ thống</a></li>
                <li class="breadcrumb-item active text-white" aria-current="page">Cài đặt Admin</li>
            </ol>
        </nav>
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h1 class="h3 fw-bold text-white mb-1 d-flex align-items-center gap-2">
                    <i class="bi bi-gear-wide-connected text-warning"></i>
                    <span>Cài Đặt Quản Trị & Hồ Sơ Cá Nhân</span>
                </h1>
                <p class="text-white-50 small mb-0">Quản lý thông tin quản trị viên, đổi mật khẩu và thiết lập xác thực 2 lớp (2FA)</p>
            </div>
            <div>
                <a href="/admin/lms/dashboard" class="btn btn-outline-secondary btn-sm px-3 rounded-pill d-inline-flex align-items-center gap-1.5">
                    <i class="bi bi-arrow-left"></i> Quay lại Tổng quan
                </a>
            </div>
        </div>
    </div>

    <!-- Form Glass Card: Thông tin & Đổi mật khẩu -->
    <div class="refract-card p-4 p-md-5 mb-4">
        <form method="post" action="/admin/profile">
            <?= csrf_field() ?>

            <!-- Section 1: Thông tin liên hệ -->
            <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom border-white border-opacity-10">
                <i class="bi bi-person-badge-fill text-info fs-5"></i>
                <h5 class="fw-bold text-white mb-0">1. Thông tin tài khoản quản trị</h5>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label text-white small fw-bold">Họ và tên <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="<?= e($user['name']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-white small fw-bold">Số điện thoại liên hệ</label>
                    <input type="text" name="phone" class="form-control" value="<?= e($user['phone'] ?? '') ?>" placeholder="Ví dụ: 0912345678">
                </div>
                <div class="col-md-8">
                    <label class="form-label text-white small fw-bold">Email đăng nhập</label>
                    <input type="email" class="form-control bg-dark bg-opacity-50 text-white-50" value="<?= e($user['email']) ?>" readonly disabled>
                    <small class="text-white-50" style="font-size: 11px;">Email đăng nhập cố định của hệ thống quản trị</small>
                </div>
                <div class="col-md-4">
                    <label class="form-label text-white small fw-bold">Vai trò phân quyền</label>
                    <div>
                        <span class="badge bg-danger bg-opacity-25 text-danger border border-danger border-opacity-25 rounded-pill px-3 py-2 small fw-semibold text-uppercase">
                            <i class="bi bi-shield-check me-1"></i> <?= e($user['role'] ?? 'admin') ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Section 2: Đổi mật khẩu -->
            <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom border-white border-opacity-10 pt-2">
                <i class="bi bi-key-fill text-warning fs-5"></i>
                <h5 class="fw-bold text-white mb-0">2. Đổi mật khẩu đăng nhập</h5>
            </div>
            <p class="text-white-50 small mb-3">Chỉ điền các trường bên dưới nếu bạn muốn thay đổi mật khẩu hiện tại. Để trống nếu muốn giữ nguyên mật khẩu cũ.</p>

            <div class="row g-3 mb-4">
                <div class="col-12">
                    <label class="form-label text-white small fw-bold">Mật khẩu hiện tại</label>
                    <input type="password" name="current_password" class="form-control" placeholder="Nhập mật khẩu hiện tại nếu muốn đổi">
                </div>
                <div class="col-md-6">
                    <label class="form-label text-white small fw-bold">Mật khẩu mới</label>
                    <input type="password" name="new_password" class="form-control" placeholder="Mật khẩu mới (tối thiểu 6 ký tự)">
                </div>
                <div class="col-md-6">
                    <label class="form-label text-white small fw-bold">Xác nhận mật khẩu mới</label>
                    <input type="password" name="confirm_password" class="form-control" placeholder="Nhập lại mật khẩu mới">
                </div>
            </div>

            <!-- Footer Action Buttons -->
            <div class="d-flex justify-content-between align-items-center pt-3 border-top border-white border-opacity-10 flex-wrap gap-2">
                <a href="/admin/lms/dashboard" class="btn btn-outline-secondary rounded-pill px-4">
                    <i class="bi bi-arrow-left me-1"></i> Quay lại Dashboard
                </a>
                <button type="submit" class="btn btn-refract-action px-5 py-2.5 fw-bold" style="cursor: pointer;">
                    <i class="bi bi-check2-circle me-1"></i> Lưu thông tin Admin
                </button>
            </div>
        </form>
    </div>

    <!-- Form Glass Card: Xác thực 2 lớp (2FA) -->
    <div class="refract-card p-4 p-md-5">
        <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom border-white border-opacity-10">
            <i class="bi bi-shield-lock-fill text-success fs-5"></i>
            <h5 class="fw-bold text-white mb-0">3. Bảo mật hai lớp (Two-Factor Authentication - 2FA)</h5>
        </div>

        <?php
        $enabled = !empty($user['two_factor_enabled']);
        $pendingSecret = $_SESSION['pending_2fa_secret'] ?? null;
        ?>

        <?php if ($enabled): ?>
            <div class="alert alert-success bg-success bg-opacity-15 border-success border-opacity-25 text-success rounded-4 p-3 d-flex align-items-center gap-2 mb-4">
                <i class="bi bi-shield-fill-check fs-4"></i>
                <div>
                    <div class="fw-bold">Xác thực 2 lớp đang BẬT</div>
                    <div class="small text-white-50">Tài khoản của bạn đang được bảo vệ an toàn bằng mã TOTP Authenticator.</div>
                </div>
            </div>

            <form method="post" action="/admin/profile/2fa/disable" class="row g-3 align-items-end" onsubmit="return confirm('Bạn có chắc chắn muốn tắt xác thực 2 lớp? Tài khoản sẽ giảm mức độ bảo mật.');">
                <?= csrf_field() ?>
                <div class="col-md-5">
                    <label class="form-label text-white small fw-bold">Nhập mã OTP 6 số để xác nhận tắt 2FA</label>
                    <input type="text" name="code" class="form-control font-monospace text-center fs-5" maxlength="6" placeholder="000000" required>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-outline-danger rounded-pill px-4 py-2 fw-semibold w-100">
                        <i class="bi bi-shield-x me-1"></i> Tắt xác thực 2FA
                    </button>
                </div>
            </form>
        <?php elseif ($pendingSecret): ?>
            <?php
            $uri = \App\Services\TotpService::provisioningUri($pendingSecret, $user['email'], config('app_name', 'Hades LMS'));
            $qr = \App\Services\TotpService::qrImageUrl($uri);
            ?>
            <div class="mb-4">
                <p class="text-white-50 small mb-3">Dùng ứng dụng <strong>Google Authenticator</strong> hoặc <strong>Microsoft Authenticator</strong> quét mã QR dưới đây:</p>
                <div class="d-flex align-items-center gap-4 flex-wrap">
                    <div class="bg-white p-3 rounded-4 shadow-sm border border-secondary border-opacity-25">
                        <img src="<?= e($qr) ?>" alt="QR 2FA" width="180" height="180" class="d-block">
                    </div>
                    <div>
                        <div class="small text-white-50 mb-1">Mã khóa bí mật (Secret Key):</div>
                        <code class="d-inline-block px-3 py-1.5 rounded-3 bg-white bg-opacity-10 text-warning fs-6 font-monospace mb-2"><?= e($pendingSecret) ?></code>
                        <div class="small text-white-50">Nhập mã bí mật thủ công nếu không thể quét QR.</div>
                    </div>
                </div>
            </div>

            <form method="post" action="/admin/profile/2fa/confirm" class="row g-3 align-items-end">
                <?= csrf_field() ?>
                <div class="col-md-5">
                    <label class="form-label text-white small fw-bold">Nhập mã xác thực 6 số trên app để kích hoạt</label>
                    <input type="text" name="code" class="form-control font-monospace text-center fs-5" maxlength="6" required placeholder="000000">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-success rounded-pill px-4 py-2 fw-semibold w-100">
                        <i class="bi bi-shield-check me-1"></i> Xác nhận & Kích hoạt 2FA
                    </button>
                </div>
            </form>
        <?php else: ?>
            <p class="text-white-50 small mb-3">Tăng cường lớp bảo vệ cho tài khoản Quản trị viên bằng cách yêu cầu mã xác thực OTP từ ứng dụng điện thoại mỗi khi đăng nhập.</p>
            <form method="post" action="/admin/profile/2fa/enable">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-outline-warning rounded-pill px-4 py-2 fw-semibold d-inline-flex align-items-center gap-2">
                    <i class="bi bi-qr-code-scan"></i> Bật xác thực hai lớp (2FA)
                </button>
            </form>
        <?php endif; ?>
    </div>
</div>
