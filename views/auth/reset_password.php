<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lại mật khẩu - <?= e(config('app_name')) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/shop.min.css" rel="stylesheet">
</head>
<body>
<div class="auth-page">
    <div class="auth-card">
        <div class="text-center mb-3">
            <img src="/image/logo.webp" alt="Logo" class="logo-img" style="height:60px;">
        </div>
        <h2>Đặt lại mật khẩu</h2>
        <p class="subtitle">Thiết lập mật khẩu mới cho <br><strong><?= e($email) ?></strong></p>

        <?php if ($msg = flash('success')): ?><div class="alert-hades alert-success"><?= e($msg) ?></div><?php endif; ?>
        <?php if ($msg = flash('error')): ?><div class="alert-hades alert-danger"><?= e($msg) ?></div><?php endif; ?>

        <form method="post" action="/reset-password">
            <?= csrf_field() ?>
            <?php if (!empty($token)): ?>
                <input type="hidden" name="token" value="<?= e($token) ?>">
            <?php elseif (!empty($requestId)): ?>
                <input type="hidden" name="request_id" value="<?= (int) $requestId ?>">
            <?php endif; ?>
            <div class="mb-3">
                <label class="form-label">Mật khẩu mới</label>
                <input type="password" name="password" class="form-control" required placeholder="Tối thiểu 6 ký tự" minlength="6">
            </div>
            <div class="mb-3">
                <label class="form-label">Xác nhận mật khẩu mới</label>
                <input type="password" name="password_confirmation" class="form-control" required placeholder="Nhập lại mật khẩu mới" minlength="6">
            </div>
            <button type="submit" class="btn-primary-hades">Cập nhật mật khẩu</button>
        </form>
    </div>
</div>
</body>
</html>
