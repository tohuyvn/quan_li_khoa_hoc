<h2 class="mb-4">⚙️ Hồ sơ cá nhân (Quản trị viên)</h2>

<div class="card shadow-sm border rounded-3">
    <div class="card-body p-4">
        <form method="post" action="/admin/profile">
            <?= csrf_field() ?>
            <h5 class="mb-3 border-bottom pb-2 text-dark">1. Thông tin liên hệ</h5>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Họ tên *</label>
                    <input type="text" name="name" class="form-control" value="<?= e($user['name']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Số điện thoại</label>
                    <input type="text" name="phone" class="form-control" value="<?= e($user['phone'] ?? '') ?>" placeholder="Ví dụ: 0912345678">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Email đăng nhập (không thể thay đổi)</label>
                    <input type="email" class="form-control bg-light" value="<?= e($user['email']) ?>" readonly disabled>
                    <div class="form-text text-muted">Email được sử dụng để đăng nhập vào trang quản trị hệ thống.</div>
                </div>
            </div>

            <h5 class="mb-3 border-bottom pb-2 text-dark">2. Đổi mật khẩu (để trống nếu không muốn đổi)</h5>
            
            <div class="row g-3 mb-4">
                <div class="col-12">
                    <label class="form-label fw-semibold">Mật khẩu hiện tại</label>
                    <input type="password" name="current_password" class="form-control" placeholder="Nhập mật khẩu hiện tại để thay đổi">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Mật khẩu mới</label>
                    <input type="password" name="new_password" class="form-control" placeholder="Mật khẩu mới (tối thiểu 6 ký tự)">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Xác nhận mật khẩu mới</label>
                    <input type="password" name="confirm_password" class="form-control" placeholder="Nhập lại mật khẩu mới">
                </div>
            </div>

            <div class="pt-3 border-top d-flex gap-2">
                <button type="submit" class="btn btn-primary px-4 py-2 fw-bold">Lưu thay đổi</button>
                <a href="/forgot-password" class="btn btn-outline-danger px-4 py-2 fw-bold">Quên mật khẩu?</a>
                <a href="/admin/products" class="btn btn-secondary px-4 py-2 fw-bold">Quay lại</a>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm border rounded-3 mt-4">
    <div class="card-body p-4">
        <h5 class="mb-3 border-bottom pb-2">3. Xác thực 2 lớp (2FA)</h5>
        <?php
        $enabled = !empty($user['two_factor_enabled']);
        $pendingSecret = $_SESSION['pending_2fa_secret'] ?? null;
        ?>
        <?php if ($enabled): ?>
            <p class="text-success fw-semibold">2FA đang bật.</p>
            <form method="post" action="/admin/profile/2fa/disable" class="row g-2 align-items-end">
                <?= csrf_field() ?>
                <div class="col-md-4">
                    <label class="form-label">Mã OTP để tắt</label>
                    <input type="text" name="code" class="form-control" maxlength="6" required>
                </div>
                <div class="col-md-4">
                    <button class="btn btn-outline-danger">Tắt 2FA</button>
                </div>
            </form>
        <?php elseif ($pendingSecret): ?>
            <?php
            $uri = \App\Services\TotpService::provisioningUri($pendingSecret, $user['email'], config('app_name', 'Shop Hades'));
            $qr = \App\Services\TotpService::qrImageUrl($uri);
            ?>
            <p>Quét QR bằng Google Authenticator / Authy, rồi nhập mã 6 số:</p>
            <img src="<?= e($qr) ?>" alt="QR 2FA" class="mb-3 border rounded p-2 bg-white" width="200" height="200">
            <p class="small font-monospace">Secret: <?= e($pendingSecret) ?></p>
            <form method="post" action="/admin/profile/2fa/confirm" class="row g-2 align-items-end">
                <?= csrf_field() ?>
                <div class="col-md-4">
                    <input type="text" name="code" class="form-control" maxlength="6" required placeholder="000000">
                </div>
                <div class="col-md-4">
                    <button class="btn btn-success">Xác nhận bật 2FA</button>
                </div>
            </form>
        <?php else: ?>
            <p class="text-muted">Tăng bảo mật đăng nhập admin bằng Authenticator.</p>
            <form method="post" action="/admin/profile/2fa/enable">
                <?= csrf_field() ?>
                <button class="btn btn-primary">Bật 2FA</button>
            </form>
        <?php endif; ?>
    </div>
</div>
