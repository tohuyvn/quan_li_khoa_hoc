#!/bin/bash
# Chạy Shop Hades với XAMPP PHP (có pdo_mysql)
cd "$(dirname "$0")"
PHP_BIN="/opt/lampp/bin/php"

if [ ! -x "$PHP_BIN" ]; then
    echo "Không tìm thấy XAMPP PHP tại $PHP_BIN"
    echo "Cài XAMPP hoặc: sudo apt install php-mysql"
    exit 1
fi

if ! "$PHP_BIN" -m | grep -q pdo_mysql; then
    echo "PHP thiếu extension pdo_mysql"
    exit 1
fi

echo "Shop Hades: http://127.0.0.1:8000"
echo "Dùng Ctrl+C để dừng"
# PHP_CLI_SERVER_WORKERS: server dev mặc định chỉ xử lý 1 request tại 1 thời điểm.
# Trang có kết nối SSE (chat, theo dõi đơn hàng) giữ request mở vô hạn -> nếu chỉ 1
# worker, cả site bị "treo" với mọi client khác cho đến khi tab đó đóng lại.
exec env PHP_CLI_SERVER_WORKERS=10 "$PHP_BIN" -S 127.0.0.1:8000 -t public public/router.php
