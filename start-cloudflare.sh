#!/bin/bash
# Shop Hades — chạy local + public qua Cloudflare Tunnel
cd "$(dirname "$0")"

PHP_BIN="/opt/lampp/bin/php"
PORT=8000
LOG_FILE="storage/logs/cloudflared.log"

if [ ! -x "$PHP_BIN" ]; then
    echo "Không tìm thấy XAMPP PHP tại $PHP_BIN"
    exit 1
fi

if ! command -v cloudflared >/dev/null 2>&1; then
    echo "Chưa cài cloudflared hoặc không tìm thấy trong PATH."
    exit 1
fi

# Đảm bảo thư mục logs tồn tại
mkdir -p storage/logs

# Dừng các instance cũ
pkill -f "php -S 127.0.0.1:${PORT}" 2>/dev/null || true
pkill -f "cloudflared tunnel --url http://127.0.0.1:${PORT}" 2>/dev/null || true
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

echo "▶ Khởi động Cloudflare Tunnel..."
rm -f "$LOG_FILE"
cloudflared tunnel --url "http://127.0.0.1:${PORT}" > "$LOG_FILE" 2>&1 &
CF_PID=$!

echo "▶ Đang chờ lấy Link Public từ Cloudflare..."
CF_URL=""
for i in {1..25}; do
    if [ -f "$LOG_FILE" ]; then
        CF_URL=$(grep -o 'https://[a-zA-Z0-9.-]*\.trycloudflare\.com' "$LOG_FILE" | head -n 1)
        if [ ! -z "$CF_URL" ]; then
            break
        fi
    fi
    sleep 1
done

if [ -z "$CF_URL" ]; then
    echo "Lỗi: Không lấy được Public URL từ Cloudflare. Chi tiết log:"
    cat "$LOG_FILE" 2>/dev/null || true
    kill $PHP_PID $CF_PID 2>/dev/null || true
    exit 1
fi

# Sao lưu file .env
BACKED_UP=false
if [ -f .env ] && [ ! -f .env.bak ]; then
    cp .env .env.bak
    BACKED_UP=true
fi

# Cập nhật cấu hình URL trong .env
if [ -f .env ]; then
    # Thay thế các URL cũ bằng URL Cloudflare mới
    sed -i "s|^APP_BASE_URL=.*|APP_BASE_URL=$CF_URL|" .env
    sed -i "s|^VNPAY_RETURN_URL=.*|VNPAY_RETURN_URL=$CF_URL/payment/vnpay/return|" .env
    sed -i "s|^MOMO_REDIRECT_URL=.*|MOMO_REDIRECT_URL=$CF_URL/payment/momo/return|" .env
    sed -i "s|^MOMO_IPN_URL=.*|MOMO_IPN_URL=$CF_URL/payment/momo/callback|" .env
    echo "▶ Đã tự động cập nhật .env thành công!"
fi

echo ""
echo "══════════════════════════════════════════"
echo "  Shop Hades — PUBLIC QUA CLOUDFLARE"
echo "══════════════════════════════════════════"
echo "  Local:  http://127.0.0.1:${PORT}/"
echo "  Public: ${CF_URL}/"
echo ""
echo "  Đăng nhập: ${CF_URL}/login"
echo "  Admin:     ${CF_URL}/admin/login"
echo "══════════════════════════════════════════"
echo "  Nhấn Ctrl+C để dừng cả PHP và Cloudflare"
echo ""

# Hàm dọn dẹp khi tắt script
cleanup() {
    echo -e "\n▶ Đang tắt PHP server và Cloudflare Tunnel..."
    kill $PHP_PID $CF_PID 2>/dev/null || true
    
    if [ "$BACKED_UP" = true ] && [ -f .env.bak ]; then
        echo "▶ Đang khôi phục lại file .env gốc..."
        mv .env.bak .env
    fi
    
    rm -f "$LOG_FILE"
    echo "▶ Hoàn tất dọn dẹp."
    exit
}

trap cleanup INT TERM

# Đợi cho các tiến trình con kết thúc
wait
