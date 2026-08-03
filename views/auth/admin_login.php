<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - <?= e(config('app_name')) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/shop.min.css" rel="stylesheet">
</head>
<body>
<div class="auth-page">
    <div class="auth-card">
        <h2>Admin Panel</h2>
        <p class="subtitle"><?= e(config('app_name')) ?></p>

        <?php if ($msg = flash('error')): ?><div class="alert-hades alert-danger"><?= e($msg) ?></div><?php endif; ?>
        <?php if ($msg = flash('success')): ?><div class="alert-hades alert-success"><?= e($msg) ?></div><?php endif; ?>

        <form method="post" action="/admin/login">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label">Email admin</label>
                <input type="email" name="email" class="form-control" value="<?= old('email', 'admin@shop.test') ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Mật khẩu</label>
                <input type="password" name="password" class="form-control">
            </div>
            <button type="submit" class="btn-primary-hades">Đăng nhập Admin</button>
        </form>
        <p class="text-center mt-3 mb-0">
            <a href="/" style="color:var(--accent)">← Về trang chủ</a> ·
            <a href="/login" style="color:var(--accent)">Đăng nhập khách</a> ·
            <a href="/forgot-password" style="color:var(--accent)">Quên mật khẩu?</a>
        </p>
        <p class="text-center mt-2 mb-0 small text-muted">
            Tài khoản mẫu: admin@shop.test / admin123
        </p>
    </div>
</div>
</body>
</html>
