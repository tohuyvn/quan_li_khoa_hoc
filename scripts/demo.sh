#!/usr/bin/env bash
# Demo script — Shop Hades roadmap (Tuần 1–2)
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT"

echo "=== Shop Hades — Demo checklist ==="
echo ""
echo "1. Khởi động server: ./start.sh → http://127.0.0.1:8000"
echo "2. Đăng ký → OTP (xem storage/logs/otp.log nếu chưa cấu hình SMTP)"
echo "3. Thêm SP vào giỏ → Checkout (thanh bước 1→2→3)"
echo "4. MoMo sandbox: quét QR → đơn paid + confetti success"
echo "5. Ví Hades: /wallet — hoàn tiền qua /admin/refunds"
echo "6. Chat: tab Trợ lý AI (Gemini + lịch sử) · Chat trực tuyến"
echo ""
echo "=== Kiểm tra nhanh ==="

PHP="${PHP_BIN:-/opt/lampp/bin/php}"

"$PHP" -r "
require 'src/bootstrap.php';
\$pdo = App\DB::connection();
\$tables = ['user_wallets', 'wallet_transactions', 'refund_requests', 'user_vouchers'];
foreach (\$tables as \$t) {
    \$ok = \$pdo->query(\"SHOW TABLES LIKE '\$t'\")->fetch();
    echo (\$ok ? '✓' : '✗') . \" \$t\n\";
}
echo 'Mail configured: ' . (App\Mailer::isConfigured() ? 'yes' : 'no (điền .env MAIL_*)') . PHP_EOL;
"

echo ""
echo "PHPUnit (nếu có extensions): vendor/bin/phpunit --testsuite Unit"
