<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký - <?= e(config('app_name')) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/shop.min.css" rel="stylesheet">
</head>
<body>
<div class="auth-page">
    <div class="auth-card">
        <div class="text-center mb-3">
            <img src="/image/logo.webp" alt="Logo" class="logo-img" style="height:60px;">
        </div>
        <h2>Đăng ký thành viên</h2>
        <p class="subtitle">Tạo tài khoản — xác thực OTP qua Gmail</p>

        <?php if ($msg = flash('error')): ?><div class="alert-hades alert-danger"><?= e($msg) ?></div><?php endif; ?>

        <form method="post" action="/register">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label">Họ tên</label>
                <input type="text" name="name" class="form-control" value="<?= old('name') ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?= old('email') ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Số điện thoại</label>
                <input type="text" name="phone" class="form-control" value="<?= old('phone') ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Mật khẩu</label>
                <input type="password" name="password" id="registerPasswordInput" class="form-control" required oninput="checkPasswordStrength(this.value)">
                <div class="progress mt-2" style="height: 6px; display: none;" id="strengthProgressWrap">
                    <div class="progress-bar" role="progressbar" style="width: 0%;" id="strengthProgressBar"></div>
                </div>
                <div class="small mt-1" id="strengthText" style="font-size: 11px;"></div>
            </div>
            <div class="mb-3">
                <label class="form-label">Xác nhận mật khẩu</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>
            <?= \App\Services\CaptchaService::fieldHtml() ?>
            <button type="submit" class="btn-primary-hades">Đăng ký → Nhận OTP</button>
        </form>
        <p class="text-center mt-3 mb-0 small">
            Đã có tài khoản? <a href="/login" style="color:var(--accent)">Đăng nhập</a> ·
            <a href="/admin/login" style="color:var(--accent)">Admin</a> ·
            <a href="/">Trang chủ</a>
        </p>
    </div>
</div>
<script>
function checkPasswordStrength(password) {
    const wrap = document.getElementById('strengthProgressWrap');
    const bar = document.getElementById('strengthProgressBar');
    const txt = document.getElementById('strengthText');
    
    if (!password) {
        wrap.style.display = 'none';
        txt.textContent = '';
        return;
    }
    
    wrap.style.display = 'flex';
    let score = 0;
    
    // Length
    if (password.length >= 6) score++;
    if (password.length >= 10) score++;
    // Lower/Upper case
    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) score++;
    // Numbers
    if (/\d/.test(password)) score++;
    // Special chars
    if (/[^A-Za-z0-9]/.test(password)) score++;
    
    let pct = (score / 5) * 100;
    bar.style.width = pct + '%';
    
    if (score <= 1) {
        bar.className = 'progress-bar bg-danger';
        txt.textContent = 'Mật khẩu yếu 🔴';
        txt.className = 'small mt-1 text-danger';
    } else if (score <= 3) {
        bar.className = 'progress-bar bg-warning';
        txt.textContent = 'Mật khẩu trung bình 🟡';
        txt.className = 'small mt-1 text-warning';
    } else {
        bar.className = 'progress-bar bg-success';
        txt.textContent = 'Mật khẩu mạnh 🟢';
        txt.className = 'small mt-1 text-success';
    }
}
</script>
</body>
</html>
