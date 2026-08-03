<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác thực 2FA - <?= e(config('app_name')) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/shop.min.css" rel="stylesheet">
</head>
<body>
<div class="auth-page">
    <div class="auth-card">
        <h2>Xác thực 2 lớp</h2>
        <p class="subtitle">Nhập mã 6 số từ Google Authenticator / Authy</p>
        <?php if ($msg = flash('error')): ?><div class="alert-hades alert-danger"><?= e($msg) ?></div><?php endif; ?>
        <?php if ($msg = flash('success')): ?><div class="alert-hades alert-success"><?= e($msg) ?></div><?php endif; ?>
        <form method="post" action="/admin/2fa">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label">Mã OTP</label>
                <input type="text" name="code" class="form-control text-center font-monospace fs-4" maxlength="6" pattern="\d{6}" required autofocus autocomplete="one-time-code">
            </div>
            <button type="submit" class="btn-primary-hades">Xác nhận</button>
        </form>
        <p class="text-center mt-3 mb-0 small"><a href="/admin/login" style="color:var(--accent)">← Đăng nhập lại</a></p>
    </div>
</div>
</body>
</html>
