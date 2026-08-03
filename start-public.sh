#!/bin/bash
# Shop Hades — chạy local + public qua ngrok
cd "$(dirname "$0")"

PHP_BIN="/opt/lampp/bin/php"
NGROK_DOMAIN="dividend-stack-trustee.ngrok-free.dev"
PORT=8000

if [ ! -x "$PHP_BIN" ]; then
    echo "Không tìm thấy XAMPP PHP tại $PHP_BIN"
    exit 1
fi

if ! command -v ngrok >/dev/null 2>&1; then
    echo "Chưa cài ngrok: https://ngrok.com/download"
    exit 1
fi

# Dừng instance cũ trên cùng port
pkill -f "php -S 127.0.0.1:${PORT}" 2>/dev/null || true

echo "▶ Khởi động PHP server (port ${PORT})..."
# PHP_CLI_SERVER_WORKERS: tránh treo cả site khi có 1 client giữ kết nối SSE (chat/tracking) mở
PHP_CLI_SERVER_WORKERS=4 "$PHP_BIN" -S "127.0.0.1:${PORT}" -t public public/router.php &
PHP_PID=$!

sleep 1
if ! kill -0 "$PHP_PID" 2>/dev/null; then
    echo "Lỗi: không khởi động được PHP server"
    exit 1
fi

echo "▶ Khởi động ngrok..."
pkill -f "ngrok http ${PORT}" 2>/dev/null || true
sleep 1
ngrok http "$PORT" --domain="$NGROK_DOMAIN" &
NGROK_PID=$!

sleep 2

echo ""
echo "══════════════════════════════════════════"
echo "  Shop Hades — PUBLIC"
echo "══════════════════════════════════════════"
echo "  Local:  http://127.0.0.1:${PORT}/"
echo "  Public: https://${NGROK_DOMAIN}/"
echo ""
echo "  Đăng nhập: https://${NGROK_DOMAIN}/login"
echo "  Admin:     https://${NGROK_DOMAIN}/admin/login"
echo "══════════════════════════════════════════"
echo "  Nhấn Ctrl+C để dừng cả PHP và ngrok"
echo ""

trap 'kill $PHP_PID $NGROK_PID 2>/dev/null; exit' INT TERM
wait
