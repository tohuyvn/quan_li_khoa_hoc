<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác thực OTP - <?= e(config('app_name')) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/shop.min.css" rel="stylesheet">
</head>
<body>
<div class="auth-page">
    <div class="auth-card">
        <div class="text-center mb-3">
            <img src="/image/logo.webp" alt="Logo" class="logo-img" style="height:60px;">
        </div>
        <h2>Xác thực OTP</h2>
        <p class="subtitle">
            Mã 6 số đã gửi tới <strong><?= e($email) ?></strong><br>
            <small class="text-muted">Hành động: <?= e($action) ?></small>
        </p>

        <?php if ($msg = flash('success')): ?><div class="alert-hades alert-success"><?= e($msg) ?></div><?php endif; ?>
        <?php if ($msg = flash('error')): ?><div class="alert-hades alert-danger"><?= e($msg) ?></div><?php endif; ?>

        <form method="post" action="/verify-otp" id="otpForm">
            <?= csrf_field() ?>
            <div class="mb-4">
                <label class="form-label text-center d-block mb-3">Nhập mã OTP</label>
                <div class="otp-box-container" id="otpBoxContainer">
                    <input type="text" class="otp-box-digit" maxlength="1" pattern="[0-9]" inputmode="numeric" required autocomplete="off">
                    <input type="text" class="otp-box-digit" maxlength="1" pattern="[0-9]" inputmode="numeric" required autocomplete="off">
                    <input type="text" class="otp-box-digit" maxlength="1" pattern="[0-9]" inputmode="numeric" required autocomplete="off">
                    <input type="text" class="otp-box-digit" maxlength="1" pattern="[0-9]" inputmode="numeric" required autocomplete="off">
                    <input type="text" class="otp-box-digit" maxlength="1" pattern="[0-9]" inputmode="numeric" required autocomplete="off">
                    <input type="text" class="otp-box-digit" maxlength="1" pattern="[0-9]" inputmode="numeric" required autocomplete="off">
                </div>
                <input type="hidden" name="otp" id="hiddenOtpInput">
            </div>
            <button type="submit" class="btn-primary-hades">Xác nhận</button>
        </form>

        <form method="post" action="/resend-otp" class="mt-3 text-center" id="resendOtpForm">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-link" id="btnResendOtp" style="color:var(--accent); display:none; text-decoration:none; font-weight:600;">Gửi lại mã OTP</button>
            <span id="countdownText" class="text-muted small">Gửi lại sau <strong id="countdownSeconds">60</strong> giây</span>
        </form>

        <p class="text-center mt-4 mb-0 small text-muted">
            <a href="/login" style="color:var(--accent); text-decoration:none;">← Quay lại đăng nhập</a> ·
            <a href="/" style="color:var(--accent); text-decoration:none;">Trang chủ</a>
        </p>

        <?php if (config('app_env') === 'local'): ?>
            <?php 
                $localOtp = $_SESSION['pending_otp']['code'] ?? '';
            ?>
            <div class="alert alert-info mt-3 p-3 small text-center" style="font-size:13px; border-radius: 8px; border: 1px dashed #38bdf8; background: rgba(56, 189, 248, 0.1); color: #38bdf8;">
                <strong>🛠️ Chế độ Local:</strong> Mã OTP của bạn là: <br>
                <span style="font-size: 22px; font-weight: 800; letter-spacing: 4px; color: #38bdf8; display: inline-block; margin-top: 4px; font-family: monospace;"><?= e($localOtp) ?></span>
                <div style="font-size: 11px; color: #94a3b8; margin-top: 4px;">(Hiển thị trực tiếp khi chạy localhost để test tiện lợi)</div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('otpBoxContainer');
    if (!container) return;
    const inputs = container.querySelectorAll('.otp-box-digit');
    const hiddenInput = document.getElementById('hiddenOtpInput');
    const form = document.getElementById('otpForm');

    // Auto focus first input
    inputs[0].focus();

    inputs.forEach((input, index) => {
        // Handle paste event
        input.addEventListener('paste', function (e) {
            e.preventDefault();
            const pastedData = (e.clipboardData || window.clipboardData).getData('text').trim();
            if (/^\d{6}$/.test(pastedData)) {
                for (let i = 0; i < 6; i++) {
                    inputs[i].value = pastedData[i];
                }
                updateHiddenInput();
                triggerCheckmark();
                setTimeout(() => form.submit(), 600);
            }
        });

        // Handle typing input
        input.addEventListener('input', function (e) {
            const val = input.value;
            if (!/^\d*$/.test(val)) {
                input.value = '';
                return;
            }
            if (val.length > 0) {
                if (index < inputs.length - 1) {
                    inputs[index + 1].focus();
                } else {
                    input.blur();
                }
            }
            updateHiddenInput();
            
            // Auto-submit when all 6 fields are filled
            if (getOtpString().length === 6) {
                triggerCheckmark();
                setTimeout(() => form.submit(), 600);
            }
        });

        // Handle backspace or navigation
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Backspace' && input.value === '') {
                if (index > 0) {
                    inputs[index - 1].focus();
                    inputs[index - 1].value = '';
                    updateHiddenInput();
                }
            } else if (e.key === 'ArrowLeft') {
                if (index > 0) {
                    inputs[index - 1].focus();
                }
            } else if (e.key === 'ArrowRight') {
                if (index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            }
        });
    });

    function getOtpString() {
        let otp = '';
        inputs.forEach(input => {
            otp += input.value;
        });
        return otp;
    }

    function updateHiddenInput() {
        hiddenInput.value = getOtpString();
    }

    function triggerCheckmark() {
        inputs.forEach(input => {
            input.classList.add('otp-checkmark');
        });
    }

    // Shake animation if there's an error alert
    const alertDanger = document.querySelector('.alert-danger');
    if (alertDanger) {
        container.classList.add('otp-shake');
        inputs.forEach(input => {
            input.style.borderColor = 'var(--accent)';
        });
        setTimeout(() => {
            container.classList.remove('otp-shake');
        }, 500);
    }

    // Resend Countdown
    let secondsLeft = 60;
    const btnResend = document.getElementById('btnResendOtp');
    const countdownText = document.getElementById('countdownText');
    const countdownSeconds = document.getElementById('countdownSeconds');
    const initialCanResend = <?= json_encode($canResend) ?>;

    if (initialCanResend) {
        showResendButton();
    } else {
        const timer = setInterval(() => {
            secondsLeft--;
            if (secondsLeft <= 0) {
                clearInterval(timer);
                showResendButton();
            } else {
                if (countdownSeconds) countdownSeconds.textContent = secondsLeft;
            }
        }, 1000);
    }

    function showResendButton() {
        if (btnResend) btnResend.style.display = 'inline-block';
        if (countdownText) countdownText.style.display = 'none';
    }
});
</script>
</body>
</html>
