<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf" content="<?= e(csrf_token()) ?>">
    <title>Admin - <?= e(config('app_name')) ?> - Quản trị hệ thống</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="/css/shop.min.css" rel="stylesheet">
</head>
<body>
<div class="side-banner left-banner d-none d-xl-block d-print-none">
    <a href="/?q=iphone">
        <img src="/image/banner_left.png" alt="Banner Trái">
    </a>
</div>
<div class="side-banner right-banner d-none d-xl-block d-print-none">
    <a href="/?q=laptop">
        <img src="/image/banner_right.png" alt="Banner Phải">
    </a>
</div>
<div class="top-promo-banner d-print-none">
    <a href="/?q=sale">
        <img src="/image/banner_top.png" alt="Siêu sale Hades">
    </a>
</div>
<div class="top-bar d-print-none">
    <div class="container d-flex justify-content-between">
        <span>🔥 Hotline: <a href="tel:<?= e(config('hotline', '18001234')) ?>"><?= e(config('hotline', '1800 1234')) ?></a></span>
        <span>Miễn phí giao hàng đơn từ 500.000đ | Bảo hành chính hãng</span>
    </div>
</div>

<header class="site-header d-print-none">
    <div class="container header-inner">
        <a href="/admin/products" class="logo">
            <img src="/image/logo.webp" alt="Logo" class="logo-img">
            <span><?= e(config('app_name')) ?> Admin</span>
        </a>
        <div class="d-flex align-items-center ms-auto" style="gap: 16px;">
            <a href="/admin/profile" class="fw-bold text-dark text-decoration-none hover-underline">👤 Quản trị viên: <?= e(authUser()['name'] ?? 'Admin') ?></a>
            <a href="/" class="btn btn-sm btn-dark text-white fw-bold">Xem trang chủ</a>
            <form method="post" action="/logout" class="d-inline">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-sm btn-danger text-white fw-bold">Đăng xuất</button>
            </form>
        </div>
    </div>
</header>

<nav class="category-nav d-print-none">
    <div class="container d-flex flex-wrap gap-2 py-1">
        <?php
        $uri = $_SERVER['REQUEST_URI'];
        $isActive = fn($path) => str_starts_with($uri, $path) ? 'active' : '';
        
        // Query unread message count dynamically
        $db = \App\DB::connection();
        $unreadMsgsStmt = $db->query("SELECT COUNT(*) FROM support_messages WHERE sender='user' AND is_read=0");
        $unreadMsgsCount = (int) ($unreadMsgsStmt ? $unreadMsgsStmt->fetchColumn() : 0);
        ?>
        <a href="/admin/dashboard" class="<?= ($uri === '/admin' || str_starts_with($uri, '/admin/dashboard')) ? 'active' : '' ?>">📈 Tổng quan</a>
        <a href="/admin/products" class="<?= $isActive('/admin/products') ?>">📦 Sản phẩm</a>
        <a href="/admin/categories" class="<?= $isActive('/admin/categories') ?>">🗂️ Danh mục</a>
        <a href="/admin/inventory" class="<?= $isActive('/admin/inventory') ?>">📊 Tồn kho</a>
        <a href="/admin/stores" class="<?= $isActive('/admin/stores') ?>">🏪 Cửa hàng</a>
        <a href="/admin/device-units" class="<?= $isActive('/admin/device-units') ?>">📱 IMEI</a>
        <a href="/admin/orders" class="<?= $isActive('/admin/orders') ?>">🛒 Đơn hàng</a>
        <a href="/admin/refunds" class="<?= $isActive('/admin/refunds') ?>">↩ Hoàn tiền</a>
        <a href="/admin/users" class="<?= $isActive('/admin/users') ?>">👥 Người dùng</a>
        <a href="/admin/payments" class="<?= $isActive('/admin/payments') ?>">💳 Thanh toán</a>
        <a href="/admin/promotions" class="<?= $isActive('/admin/promotions') ?>">🎫 Mã coupon</a>
        <a href="/admin/site-promotions" class="<?= $isActive('/admin/site-promotions') ?>">🎁 Ưu đãi chung</a>
        <a href="/admin/trade-ins" class="<?= $isActive('/admin/trade-ins') ?>">🔄 Trade-in</a>
        <a href="/admin/messages" class="<?= $isActive('/admin/messages') ?>">
            💬 Tin nhắn
            <?php if ($unreadMsgsCount > 0): ?>
                <span class="badge bg-danger ms-1" style="font-size: 10px; padding: 3px 6px; border-radius: 50px;"><?= $unreadMsgsCount ?></span>
            <?php endif; ?>
        </a>
        <a href="/admin/warranties" class="<?= $isActive('/admin/warranties') ?>">🛡️ Bảo hành</a>
        <a href="/admin/audit-logs" class="<?= $isActive('/admin/audit-logs') ?>">🛡️ Nhật ký hệ thống</a>
        <a href="/admin/profile" class="<?= $isActive('/admin/profile') ?>">⚙️ Hồ sơ</a>
    </div>
</nav>

<main class="container py-4">
    <?php if ($msg = flash('success')): ?>
        <div class="alert-hades alert-success shadow-sm rounded-3 border-0 d-flex align-items-center gap-2"><i class="bi bi-check-circle-fill fs-5"></i><span><?= e($msg) ?></span></div>
    <?php endif; ?>
    <?php if ($msg = flash('error')): ?>
        <div class="alert-hades alert-danger shadow-sm rounded-3 border-0 d-flex align-items-center gap-2"><i class="bi bi-exclamation-triangle-fill fs-5"></i><span><?= e($msg) ?></span></div>
    <?php endif; ?>

    <div class="admin-main-wrapper">
        <?= $content ?? '' ?>
    </div>
</main>

<footer class="site-footer d-print-none">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-3">
                <h5><?= e(config('app_name')) ?></h5>
                <p>Hệ thống bán lẻ điện thoại, laptop, phụ kiện chính hãng. Giá tốt mỗi ngày.</p>
            </div>
            <div class="col-md-4 mb-3">
                <h5>Quản lý hệ thống</h5>
                <p>
                    <a href="/admin/products" class="text-white text-decoration-none">Sản phẩm</a> ·
                    <a href="/admin/categories" class="text-white text-decoration-none">Danh mục</a> ·
                    <a href="/admin/orders" class="text-white text-decoration-none">Đơn hàng</a> ·
                    <a href="/admin/inventory" class="text-white text-decoration-none">Tồn kho</a>
                </p>
            </div>
            <div class="col-md-4 mb-3">
                <h5>Liên hệ & Hỗ trợ</h5>
                <p>
                    Email: huy28072k5@gmail.com<br>
                    Hotline: <?= e(config('hotline')) ?><br>
                    <a href="/" class="text-warning text-decoration-none fw-bold">Quay lại Trang chủ</a>
                </p>
            </div>
        </div>
        <hr style="border-color:#444">
        <p class="text-center mb-0">© <?= date('Y') ?> <?= e(config('app_name')) ?>. All rights reserved.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
