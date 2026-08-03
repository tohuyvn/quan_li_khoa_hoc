<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu - <?= e(config('app_name')) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/shop.min.css" rel="stylesheet">
</head>
<body>
<div class="auth-page">
    <div class="auth-card">
        <div class="text-center mb-3">
            <img src="/image/logo.webp" alt="Logo" class="logo-img" style="height:60px;">
        </div>
        <h2>Quên mật khẩu</h2>
        <p class="subtitle">Nhập email hoặc số điện thoại đã đăng ký để nhận mã OTP và liên kết đặt lại mật khẩu</p>

        <?php if ($msg = flash('success')): ?><div class="alert-hades alert-success"><?= e($msg) ?></div><?php endif; ?>
        <?php if ($msg = flash('error')): ?><div class="alert-hades alert-danger"><?= e($msg) ?></div><?php endif; ?>

        <form method="post" action="/forgot-password">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label">Email hoặc số điện thoại</label>
                <input type="text" name="identifier" class="form-control" value="<?= old('identifier', old('email')) ?>" required placeholder="email@example.com hoặc 09xxxxxxxx">
            </div>
            <?= \App\Services\CaptchaService::fieldHtml() ?>
            <button type="submit" class="btn-primary-hades">Gửi hướng dẫn đặt lại mật khẩu →</button>
        </form>
        <p class="text-center mt-3 mb-0 small text-muted">
            Vì lý do bảo mật, hệ thống không xác nhận tài khoản có tồn tại hay không.
        </p>
        <p class="text-center mt-2 mb-0 small">
            Quay lại <a href="/login" style="color:var(--accent)">Đăng nhập</a> hoặc
            <a href="/" style="color:var(--accent)">Trang chủ</a>
        </p>
    </div>
</div>
</body>
</html>
