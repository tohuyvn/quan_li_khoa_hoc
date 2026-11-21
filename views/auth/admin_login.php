<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal - Hades Academy</title>
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

        /* Ambient glowing background aura - Tone Admin Red & Orange */
        .ambient-glow-1 {
            position: fixed;
            top: -15%;
            left: 50%;
            transform: translateX(-50%);
            width: 650px;
            height: 450px;
            background: radial-gradient(circle, rgba(239, 68, 68, 0.16) 0%, rgba(245, 158, 11, 0.08) 50%, transparent 75%);
            filter: blur(85px);
            pointer-events: none;
            z-index: 2;
        }

        .ambient-glow-2 {
            position: fixed;
            bottom: -10%;
            right: 15%;
            width: 450px;
            height: 350px;
            background: radial-gradient(circle, rgba(220, 38, 38, 0.12) 0%, transparent 70%);
            filter: blur(75px);
            pointer-events: none;
            z-index: 2;
        }

        /* Wrapper & Card */
        .admin-wrapper {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 440px;
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

        .admin-card {
            background: rgba(24, 25, 32, 0.65);
            backdrop-filter: blur(30px) saturate(190%);
            -webkit-backdrop-filter: blur(30px) saturate(190%);
            border-radius: 28px;
            padding: 38px 32px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            box-shadow: 
                0 0 0 1px rgba(255, 255, 255, 0.14),
                inset 0 1.5px 2px rgba(255, 255, 255, 0.45),
                inset 0 -1.5px 3px rgba(0, 0, 0, 0.4),
                0 30px 65px -12px rgba(0, 0, 0, 0.8),
                0 0 45px rgba(239, 68, 68, 0.15);
            position: relative;
            overflow: hidden;
        }

        /* Rainbow / Refractive Top Edge */
        .admin-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2.5px;
            background: linear-gradient(90deg, 
                transparent 0%, 
                rgba(239, 68, 68, 0.8) 20%, 
                rgba(255, 255, 255, 0.95) 50%, 
                rgba(245, 158, 11, 0.8) 80%, 
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
            background: rgba(239, 68, 68, 0.12);
            border: 1px solid rgba(239, 68, 68, 0.35);
            border-radius: 9999px;
            color: #f87171;
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
            margin-bottom: 24px;
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
            margin-bottom: 18px;
        }

        .form-control-lamp {
            width: 100%;
            background: rgba(15, 17, 24, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.16);
            border-radius: 14px;
            padding: 13px 14px 13px 44px;
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
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.25), inset 0 1px 2px rgba(255, 255, 255, 0.2);
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
            color: #ef4444;
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

        .btn-admin-primary {
            width: 100%;
            padding: 13px;
            border-radius: 14px;
            font-weight: 700;
            font-size: 14.5px;
            color: #ffffff;
            background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);
            border: none;
            box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4), inset 0 1px 1px rgba(255, 255, 255, 0.3);
            transition: all 0.25s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            margin-top: 6px;
        }

        .btn-admin-primary:hover {
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            box-shadow: 0 8px 25px rgba(239, 68, 68, 0.55), inset 0 1px 1px rgba(255, 255, 255, 0.4);
            transform: translateY(-1px);
            color: #ffffff;
        }

        /* Demo credentials pill */
        .demo-box {
            background: rgba(255, 255, 255, 0.04);
            border: 1px dashed rgba(255, 255, 255, 0.16);
            border-radius: 14px;
            padding: 10px 14px;
            margin-top: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            font-size: 12px;
            color: #94a3b8;
        }
        .demo-box code {
            color: #fca5a5;
            background: rgba(239, 68, 68, 0.12);
            padding: 2px 6px;
            border-radius: 6px;
            font-size: 11.5px;
        }
        .copy-btn {
            background: none;
            border: none;
            color: #cbd5e1;
            cursor: pointer;
            padding: 4px;
            font-size: 13px;
            transition: color 0.2s;
        }
        .copy-btn:hover {
            color: #ffffff;
        }

        .footer-links {
            text-align: center;
            margin-top: 20px;
            font-size: 12.5px;
            color: #64748b;
        }
        .footer-links a {
            color: #94a3b8;
            text-decoration: none;
            transition: color 0.2s;
        }
        .footer-links a:hover {
            color: #e2e8f0;
            text-decoration: underline;
        }
    </style>
</head>
<body>

<!-- Canvas Hiệu ứng Bụi sáng & Đom đóm -->
<canvas id="firefliesCanvas"></canvas>

<!-- Hiệu ứng quầng sáng nền (Ambient Aura) -->
<div class="ambient-glow-1"></div>
<div class="ambient-glow-2"></div>

<div class="admin-wrapper">
    <div class="admin-card">
        <!-- Logo & Header -->
        <div class="text-center">
            <a href="/" class="brand-badge text-decoration-none">
                <i class="bi bi-shield-lock-fill"></i> Quản Trị Hệ Thống
            </a>
            <h1 class="brand-title">Admin Portal</h1>
            <p class="brand-subtitle">Cổng quản trị đào tạo LMS & Hades Academy</p>
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

        <!-- Form Đăng nhập Admin -->
        <form method="post" action="/admin/login">
            <?= csrf_field() ?>

            <!-- Email Admin -->
            <div class="mb-3">
                <label class="form-label-custom">Email quản trị viên</label>
                <div class="form-floating-custom mb-0">
                    <input type="email" name="email" id="adminEmail" class="form-control-lamp" value="<?= old('email', 'admin@shop.test') ?>" required placeholder="admin@shop.test">
                    <i class="bi bi-envelope form-icon"></i>
                </div>
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label class="form-label-custom">Mật khẩu bảo mật</label>
                <div class="form-floating-custom mb-0">
                    <input type="password" name="password" id="adminPassword" class="form-control-lamp" required placeholder="••••••••">
                    <i class="bi bi-key-fill form-icon"></i>
                    <button type="button" class="toggle-password-btn" onclick="togglePass('adminPassword', this)" tabindex="-1">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn-admin-primary">
                <i class="bi bi-box-arrow-in-right"></i>
                <span>Đăng Nhập Quản Trị</span>
            </button>
        </form>

        <!-- Demo account helper -->
        <div class="demo-box">
            <div>
                <i class="bi bi-info-circle me-1 text-danger opacity-75"></i> Mẫu: <code>admin@shop.test</code> / <code>admin123</code>
            </div>
            <button type="button" class="copy-btn" title="Điền nhanh thông tin mẫu" onclick="fillDemo()">
                <i class="bi bi-lightning-charge-fill text-warning"></i> Điền nhanh
            </button>
        </div>

        <!-- Footer Links -->
        <div class="footer-links">
            <a href="/">← Về Trang chủ</a> ·
            <a href="/login">Đăng nhập Học viên</a> ·
            <a href="/forgot-password">Quên mật khẩu?</a>
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

// Quick fill demo account
function fillDemo() {
    document.getElementById('adminEmail').value = 'admin@shop.test';
    document.getElementById('adminPassword').value = 'admin123';
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
const particleCount = 35;

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
        ctx.fillStyle = `rgba(248, 113, 113, ${Math.max(0.05, currentAlpha)})`;
        ctx.shadowBlur = 8;
        ctx.shadowColor = '#ef4444';
        ctx.fill();
    });

    requestAnimationFrame(animateParticles);
}

animateParticles();
</script>

</body>
</html>
