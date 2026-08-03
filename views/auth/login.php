<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - <?= e(config('app_name')) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/shop.min.css" rel="stylesheet">
</head>
<body>
<div class="auth-page">
    <div class="auth-card">
        <div class="text-center mb-3">
            <img src="/image/logo.webp" alt="Logo" class="logo-img" style="height:60px;">
        </div>
        <h2>Đăng nhập</h2>
        <p class="subtitle">Nhập thông tin đăng nhập của bạn</p>

        <?php if ($msg = flash('success')): ?><div class="alert-hades alert-success"><?= e($msg) ?></div><?php endif; ?>
        <?php if ($msg = flash('error')): ?><div class="alert-hades alert-danger"><?= e($msg) ?></div><?php endif; ?>

        <form method="post" action="/login">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?= old('email') ?>" required placeholder="email@example.com">
            </div>
            <div class="mb-3">
                <label class="form-label">Mật khẩu</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <?= \App\Services\CaptchaService::fieldHtml() ?>
            <button type="submit" class="btn-primary-hades">Đăng nhập</button>
        </form>
        <div class="d-flex align-items-center my-3">
            <hr class="flex-grow-1 text-muted">
            <span class="mx-2 text-muted small">Hoặc</span>
            <hr class="flex-grow-1 text-muted">
        </div>
        <a href="/login/google" class="btn btn-outline-dark w-100 d-flex align-items-center justify-content-center gap-2 py-2" style="border-radius: 8px; font-weight: 500; border-color: #cbd5e1; color: #334155; text-decoration: none;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-google" viewBox="0 0 16 16">
                <path d="M15.545 6.558a9.42 9.42 0 0 1 .139 1.626c0 2.434-.87 4.492-2.384 5.885h.002C11.978 15.292 10.158 16 8 16A8 8 0 1 1 8 0c2.181 0 4.027.818 5.485 2.183l-2.25 2.25C10.22 3.3 9.22 3 8 3a5 5 0 1 0 0 10c1.848 0 3.32-.98 3.882-2.45h-3.882V6.558h7.545z"/>
            </svg>
            Đăng nhập bằng Google
        </a>

        <p class="text-center mt-3 mb-0 small">
            Chưa có tài khoản? <a href="/register" style="color:var(--accent)">Đăng ký</a> ·
            <a href="/forgot-password" style="color:var(--accent)">Quên mật khẩu?</a> ·
            <a href="/admin/login" style="color:var(--accent)">Đăng nhập Admin</a> ·
            <a href="/">Trang chủ</a>
        </p>
    </div>
</div>

<?php if (isset($_GET['google_demo'])): ?>
<!-- Google Demo Login Modal -->
<div class="modal fade show d-block" id="googleModal" tabindex="-1" style="background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px);">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                <div class="d-flex align-items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 48 48">
                        <path fill="#FFC107" d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12c0-6.627,5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24c0,11.045,8.955,20,20,20c11.045,0,20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z"/>
                        <path fill="#FF3D00" d="M6.306,14.691l6.571,4.819C14.655,15.108,18.961,12,24,12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z"/>
                        <path fill="#4CAF50" d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.025C9.505,39.556,16.227,44,24,44z"/>
                        <path fill="#1976D2" d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.571c0.001-0.001,0.002-0.001,0.003-0.002l6.19,5.238C36.971,39.205,44,34,44,24C44,22.659,43.862,21.35,43.611,20.083z"/>
                    </svg>
                    <h5 class="modal-title fw-bold mb-0" style="font-size: 1.15rem;">Đăng nhập với Google</h5>
                </div>
                <a href="/login" class="btn-close"></a>
            </div>
            <div class="modal-body px-4 py-3">
                <p class="text-muted small mb-3">Chọn tài khoản Google để đăng nhập vào <strong>Shop Hades</strong>:</p>

                <div class="list-group gap-2 mb-3">
                    <a href="/login/google?email=khach@shop.test&name=Khách+Hàng+Demo" class="list-group-item list-group-item-action border rounded-3 p-3 d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width:40px;height:40px;">K</div>
                        <div class="flex-grow-1">
                            <div class="fw-bold small text-dark">Khách Hàng (Tài khoản mẫu)</div>
                            <div class="text-muted" style="font-size: 11px;">khach@shop.test</div>
                        </div>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </a>
                    <a href="/login/google?email=user.google@gmail.com&name=Google+User" class="list-group-item list-group-item-action border rounded-3 p-3 d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center fw-bold" style="width:40px;height:40px;">G</div>
                        <div class="flex-grow-1">
                            <div class="fw-bold small text-dark">Tài khoản Google cá nhân</div>
                            <div class="text-muted" style="font-size: 11px;">user.google@gmail.com</div>
                        </div>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </a>
                </div>

                <form method="get" action="/login/google" class="mt-3">
                    <label class="form-label small fw-bold text-secondary mb-1">Hoặc nhập email Google khác:</label>
                    <div class="input-group">
                        <input type="email" name="email" class="form-control form-control-sm" placeholder="example@gmail.com" required>
                        <button type="submit" class="btn btn-dark btn-sm fw-bold">Đăng nhập</button>
                    </div>
                </form>

                <div class="mt-4 p-3 bg-light rounded-3 border" style="font-size: 11px; color: #64748b;">
                    <div class="fw-bold text-dark mb-1">💡 Hướng dẫn cấu hình Google OAuth 2.0 thật:</div>
                    Để bật đăng nhập qua nút Google OAuth chính thức từ Google Cloud Platform, hãy thêm 2 dòng sau vào file <code>.env</code>:
                    <pre class="bg-dark text-white p-2 rounded mt-1 mb-0" style="font-size: 10px;">GOOGLE_CLIENT_ID=your-client-id.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=your-client-secret</pre>
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-0 px-4 pb-4">
                <a href="/login" class="btn btn-outline-secondary btn-sm w-100 rounded-3">Đóng</a>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
</body>
</html>
