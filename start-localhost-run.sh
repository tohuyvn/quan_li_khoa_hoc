#!/bin/bash
# Shop Hades — chạy local + public qua localhost.run
cd "$(dirname "$0")"

PHP_BIN="/opt/lampp/bin/php"
PORT=8000

if [ ! -x "$PHP_BIN" ]; then
    echo "Không tìm thấy XAMPP PHP tại $PHP_BIN"
    exit 1
fi

# Dừng instance cũ trên cùng port
pkill -f "php -S 127.0.0.1:${PORT}" 2>/dev/null || true
sleep 1

echo "▶ Khởi động PHP server (port ${PORT})..."
# PHP_CLI_SERVER_WORKERS: tránh treo cả site khi có 1 client giữ kết nối SSE (chat/tracking) mở
PHP_CLI_SERVER_WORKERS=4 "$PHP_BIN" -S "127.0.0.1:${PORT}" -t public public/router.php &
PHP_PID=$!

sleep 1
if ! kill -0 "$PHP_PID" 2>/dev/null; then
    echo "Lỗi: không khởi động được PHP server"
    exit 1
fi

echo "▶ Đang kết nối tới localhost.run để lấy link public..."
echo "--------------------------------------------------------"
echo "Chú ý: Nếu đây là lần đầu chạy, hãy nhấn 'yes' nếu SSH hỏi."
echo "--------------------------------------------------------"

# Setup trap to clean up PHP server on Ctrl+C or script termination
trap 'kill $PHP_PID 2>/dev/null; echo -e "\n▶ Đã đóng PHP server và Tunnel."; exit' INT TERM

# Run SSH in foreground so user can see the URL
ssh -o StrictHostKeyChecking=no -R 80:127.0.0.1:${PORT} nokey@localhost.run

# Cleanup when SSH exits normally
kill $PHP_PID 2>/dev/null
echo "▶ Đã đóng PHP server và Tunnel."

