<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký thành viên - Hades Academy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: #030712;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            overflow-x: hidden;
            position: relative;
            user-select: none;
            padding: 30px 16px;
        }

        /* Canvas Bụi sáng & Đom đóm lơ lửng */
        #firefliesCanvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1;
        }

        /* Ambient glowing background aura */
        .ambient-glow-1 {
            position: fixed;
            top: -15%;
            left: 50%;
            transform: translateX(-50%);
            width: 650px;
            height: 450px;
            background: radial-gradient(circle, rgba(255, 106, 0, 0.18) 0%, rgba(238, 9, 121, 0.08) 50%, transparent 75%);
            filter: blur(80px);
            pointer-events: none;
            z-index: 2;
        }

        .ambient-glow-2 {
            position: fixed;
            bottom: -10%;
            right: 10%;
            width: 450px;
            height: 350px;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.12) 0%, transparent 70%);
            filter: blur(70px);
            pointer-events: none;
            z-index: 2;
        }

        /* Wrapper & Card */
        .register-wrapper {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 490px;
            margin: auto;
            animation: fadeInCard 0.7s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes fadeInCard {
            from {
                opacity: 0;
                transform: translateY(22px) scale(0.97);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .register-card {
            background: rgba(24, 25, 32, 0.65);
            backdrop-filter: blur(30px) saturate(190%);
            -webkit-backdrop-filter: blur(30px) saturate(190%);
            border-radius: 28px;
            padding: 36px 32px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            box-shadow: 
                0 0 0 1px rgba(255, 255, 255, 0.14),
                inset 0 1.5px 2px rgba(255, 255, 255, 0.45),
                inset 0 -1.5px 3px rgba(0, 0, 0, 0.4),
                0 30px 65px -12px rgba(0, 0, 0, 0.8),
                0 0 45px rgba(255, 106, 0, 0.14);
            position: relative;
            overflow: hidden;
        }

        /* Rainbow / Refractive Top Edge */
        .register-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2.5px;
            background: linear-gradient(90deg, 
                transparent 0%, 
                rgba(255, 106, 0, 0.8) 20%, 
                rgba(255, 255, 255, 0.95) 50%, 
                rgba(0, 229, 255, 0.8) 80%, 
                transparent 100%
            );
            opacity: 0.9;
            pointer-events: none;
        }

        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            background: rgba(245, 158, 11, 0.12);
            border: 1px solid rgba(245, 158, 11, 0.35);
            border-radius: 9999px;
            color: #fbbf24;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            margin-bottom: 12px;
            text-decoration: none;
        }

        .brand-title {
            color: #ffffff;
            font-size: 26px;
            font-weight: 800;
            letter-spacing: -0.02em;
            margin-bottom: 4px;
        }

        .brand-subtitle {
            color: #94a3b8;
            font-size: 13px;
            margin-bottom: 22px;
        }

        .form-label-custom {
            font-size: 12.5px;
            font-weight: 600;
            color: #cbd5e1;
            margin-bottom: 6px;
            display: block;
        }

        .form-floating-custom {
            position: relative;
            margin-bottom: 15px;
        }

        .form-control-lamp {
            width: 100%;
            background: rgba(15, 17, 24, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.16);
            border-radius: 14px;
            padding: 12px 14px 12px 42px;
            color: #ffffff;
            font-size: 14px;
            backdrop-filter: blur(14px);
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.35);
            transition: all 0.25s ease;
            outline: none;
        }

        .form-control-lamp::placeholder {
            color: #64748b;
            font-size: 13.5px;
        }

        .form-control-lamp:focus {
            background: rgba(18, 20, 30, 0.88);
            border-color: #ff6a00;
            box-shadow: 0 0 0 3px rgba(255, 106, 0, 0.25), inset 0 1px 2px rgba(255, 255, 255, 0.2);
            color: #ffffff;
        }

        .form-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 16px;
            pointer-events: none;
            transition: color 0.2s ease;
        }

        .form-control-lamp:focus ~ .form-icon {
            color: #ff6a00;
        }

        .toggle-password-btn {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 4px;
            font-size: 15px;
            transition: color 0.2s;
        }
        .toggle-password-btn:hover {
            color: #f1f5f9;
        }

        .btn-lamp-primary {
            width: 100%;
            padding: 13px;
            border-radius: 14px;
            font-weight: 700;
            font-size: 14.5px;
            color: #ffffff;
            background: linear-gradient(135deg, #ff6a00 0%, #ee0979 100%);
            border: none;
            box-shadow: 0 6px 20px rgba(255, 106, 0, 0.4), inset 0 1px 1px rgba(255, 255, 255, 0.3);
            transition: all 0.25s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            margin-top: 10px;
        }

        .btn-lamp-primary:hover {
            background: linear-gradient(135deg, #ff7a1a 0%, #f41e8a 100%);
            box-shadow: 0 8px 25px rgba(255, 106, 0, 0.55), inset 0 1px 1px rgba(255, 255, 255, 0.4);
            transform: translateY(-1px);
            color: #ffffff;
        }

        .btn-google-lamp {
            width: 100%;
            padding: 11px;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.16);
            color: #f1f5f9;
            font-weight: 600;
            font-size: 13.5px;
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .btn-google-lamp:hover {
            background: rgba(255, 255, 255, 0.12);
            border-color: rgba(255, 255, 255, 0.3);
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.35);
        }

        /* Password strength meter */
        .strength-meter-wrap {
            margin-top: -6px;
            margin-bottom: 14px;
        }
        .strength-progress {
            height: 5px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 99px;
            overflow: hidden;
            display: none;
        }
        .strength-bar {
            height: 100%;
            width: 0%;
            transition: width 0.3s ease, background-color 0.3s ease;
        }
        .strength-label {
            font-size: 11px;
            margin-top: 4px;
            font-weight: 500;
        }

        .footer-links {
            text-align: center;
            margin-top: 20px;
            font-size: 13px;
            color: #94a3b8;
        }
        .footer-links a {
            color: #fbbf24;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s;
        }
        .footer-links a:hover {
            color: #fde047;
            text-decoration: underline;
        }
        .footer-sublinks {
            margin-top: 8px;
            font-size: 12px;
            color: #64748b;
        }
        .footer-sublinks a {
            color: #94a3b8;
            text-decoration: none;
        }
        .footer-sublinks a:hover {
            color: #e2e8f0;
        }
    </style>
</head>
<body>

<!-- Canvas Hiệu ứng Bụi sáng & Đom đóm -->
<canvas id="firefliesCanvas"></canvas>

<!-- Hiệu ứng quầng sáng nền (Ambient Aura) -->
<div class="ambient-glow-1"></div>
<div class="ambient-glow-2"></div>

<div class="register-wrapper">
    <div class="register-card">
        <!-- Logo & Header -->
        <div class="text-center">
            <a href="/" class="brand-badge text-decoration-none">
                <i class="bi bi-mortarboard-fill"></i> Hades Academy
            </a>
            <h1 class="brand-title">Đăng Ký Thành Viên</h1>
            <p class="brand-subtitle">Tạo tài khoản học viên — xác thực OTP qua Gmail an toàn</p>
        </div>

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

        <!-- Form Đăng ký -->
        <form method="post" action="/register">
            <?= csrf_field() ?>

            <!-- Họ và tên -->
            <div class="mb-3">
                <label class="form-label-custom">Họ và tên</label>
                <div class="form-floating-custom mb-0">
                    <input type="text" name="name" class="form-control-lamp" value="<?= old('name') ?>" required placeholder="Ví dụ: Nguyễn Văn A">
                    <i class="bi bi-person form-icon"></i>
                </div>
            </div>

            <!-- Email -->
            <div class="mb-3">
                <label class="form-label-custom">Địa chỉ Gmail / Email</label>
                <div class="form-floating-custom mb-0">
                    <input type="email" name="email" class="form-control-lamp" value="<?= old('email') ?>" required placeholder="name@example.com">
                    <i class="bi bi-envelope form-icon"></i>
                </div>
            </div>

            <!-- Số điện thoại -->
            <div class="mb-3">
                <label class="form-label-custom">Số điện thoại <span class="text-secondary fw-normal">(tùy chọn)</span></label>
                <div class="form-floating-custom mb-0">
                    <input type="tel" name="phone" class="form-control-lamp" value="<?= old('phone') ?>" placeholder="09xxxxxxxx">
                    <i class="bi bi-telephone form-icon"></i>
                </div>
            </div>

            <!-- Mật khẩu -->
            <div class="mb-2">
                <label class="form-label-custom">Mật khẩu</label>
                <div class="form-floating-custom mb-1">
                    <input type="password" name="password" id="registerPasswordInput" class="form-control-lamp" required placeholder="Tối thiểu 6 ký tự" oninput="checkPasswordStrength(this.value)">
                    <i class="bi bi-lock form-icon"></i>
                    <button type="button" class="toggle-password-btn" onclick="togglePass('registerPasswordInput', this)" tabindex="-1">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
                <!-- Strength meter bar -->
                <div class="strength-meter-wrap">
                    <div class="strength-progress" id="strengthProgressWrap">
                        <div class="strength-bar" id="strengthProgressBar"></div>
                    </div>
                    <div class="strength-label" id="strengthText"></div>
                </div>
            </div>

            <!-- Xác nhận mật khẩu -->
            <div class="mb-3">
                <label class="form-label-custom">Xác nhận mật khẩu</label>
                <div class="form-floating-custom mb-0">
                    <input type="password" name="password_confirmation" id="confirmPasswordInput" class="form-control-lamp" required placeholder="Nhập lại mật khẩu ở trên">
                    <i class="bi bi-shield-check form-icon"></i>
                    <button type="button" class="toggle-password-btn" onclick="togglePass('confirmPasswordInput', this)" tabindex="-1">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            <?= \App\Services\CaptchaService::fieldHtml() ?>

            <!-- Submit Button -->
            <button type="submit" class="btn-lamp-primary">
                <span>Đăng Ký → Nhận Mã OTP</span>
                <i class="bi bi-arrow-right"></i>
            </button>
        </form>

        <div class="d-flex align-items-center my-3">
            <hr class="flex-grow-1 border-secondary opacity-25 m-0">
            <span class="mx-3 text-secondary small fw-semibold" style="font-size: 11px; letter-spacing: 0.05em; text-transform: uppercase;">Hoặc tiếp tục với</span>
            <hr class="flex-grow-1 border-secondary opacity-25 m-0">
        </div>

        <!-- Official Google OAuth Button -->
        <a href="/login/google" class="btn-google-lamp">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 48 48">
                <path fill="#FFC107" d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12c0-6.627,5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24c0,11.045,8.955,20,20,20c11.045,0,20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z"/>
                <path fill="#FF3D00" d="M6.306,14.691l6.571,4.819C14.655,15.108,18.961,12,24,12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z"/>
                <path fill="#4CAF50" d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.025C9.505,39.556,16.227,44,24,44z"/>
                <path fill="#1976D2" d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.571c0.001-0.001,0.002-0.001,0.003-0.002l6.19,5.238C36.971,39.205,44,34,44,24C44,22.659,43.862,21.35,43.611,20.083z"/>
            </svg>
            <span>Đăng nhập nhanh qua Google</span>
        </a>

        <!-- Footer Links -->
        <div class="footer-links">
            <div>Đã có tài khoản? <a href="/login">Đăng nhập ngay</a></div>
            <div class="footer-sublinks">
                <a href="/admin/login">Khu vực Quản trị</a> ·
                <a href="/">Về Trang chủ</a>
            </div>
        </div>
    </div>
</div>

<script>
// Toggle Password Visibility
function togglePass(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'bi bi-eye';
    }
}

// Password Strength Checker
function checkPasswordStrength(password) {
    const wrap = document.getElementById('strengthProgressWrap');
    const bar = document.getElementById('strengthProgressBar');
    const txt = document.getElementById('strengthText');
    
    if (!password) {
        wrap.style.display = 'none';
        txt.textContent = '';
        return;
    }
    
    wrap.style.display = 'block';
    let score = 0;
    
    if (password.length >= 6) score++;
    if (password.length >= 10) score++;
    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) score++;
    if (/\d/.test(password)) score++;
    if (/[^A-Za-z0-9]/.test(password)) score++;
    
    const pct = (score / 5) * 100;
    bar.style.width = pct + '%';
    
    if (score <= 1) {
        bar.style.backgroundColor = '#ef4444';
        txt.textContent = 'Mật khẩu yếu 🔴 (nên kết hợp số và chữ hoa)';
        txt.style.color = '#f87171';
    } else if (score <= 3) {
        bar.style.backgroundColor = '#f59e0b';
        txt.textContent = 'Mật khẩu trung bình 🟡';
        txt.style.color = '#fbbf24';
    } else {
        bar.style.backgroundColor = '#10b981';
        txt.textContent = 'Mật khẩu mạnh 🟢 (rất an toàn)';
        txt.style.color = '#34d399';
    }
}

// Fireflies Background Particle Animation
const canvas = document.getElementById('firefliesCanvas');
const ctx = canvas.getContext('2d');
let width = canvas.width = window.innerWidth;
let height = canvas.height = window.innerHeight;

window.addEventListener('resize', () => {
    width = canvas.width = window.innerWidth;
    height = canvas.height = window.innerHeight;
});

const particles = [];
const particleCount = 40;

for (let i = 0; i < particleCount; i++) {
    particles.push({
        x: Math.random() * width,
        y: Math.random() * height,
        radius: Math.random() * 2 + 0.5,
        speedX: (Math.random() - 0.5) * 0.5,
        speedY: (Math.random() - 0.5) * 0.5 - 0.15,
        alpha: Math.random() * 0.7 + 0.2,
        pulseSpeed: Math.random() * 0.02 + 0.01,
        pulseVal: Math.random() * Math.PI
    });
}

function animateParticles() {
    ctx.clearRect(0, 0, width, height);

    particles.forEach(p => {
        p.x += p.speedX;
        p.y += p.speedY;
        p.pulseVal += p.pulseSpeed;

        if (p.x < 0) p.x = width;
        if (p.x > width) p.x = 0;
        if (p.y < 0) p.y = height;
        if (p.y > height) p.y = 0;

        const currentAlpha = p.alpha * 0.45 + Math.sin(p.pulseVal) * 0.25;

        ctx.beginPath();
        ctx.arc(p.x, p.y, p.radius, 0, Math.PI * 2);
        ctx.fillStyle = `rgba(251, 191, 36, ${Math.max(0.05, currentAlpha)})`;
        ctx.shadowBlur = 8;
        ctx.shadowColor = '#f59e0b';
        ctx.fill();
    });

    requestAnimationFrame(animateParticles);
}

animateParticles();
</script>

</body>
</html>
