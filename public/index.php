<?php

try {
    require dirname(__DIR__) . '/src/bootstrap.php';

    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

    if ($uri === '/health') {
        $health = \App\DB::health();
        if (!config('app_debug', false) && !$health['ok']) {
            unset($health['host'], $health['port'], $health['error']);
        }
        http_response_code($health['ok'] ? 200 : 503);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode([
            'ok' => $health['ok'],
            'app' => config('app_name', 'Shop Hades'),
            'time' => date(DATE_ATOM),
            'database' => $health,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return;
    }

    require dirname(__DIR__) . '/routes/web.php';

    $router = $GLOBALS['router'];
    $method = $_SERVER['REQUEST_METHOD'];
    $uri = $_SERVER['REQUEST_URI'] ?? '/';

    $router->dispatch($method, $uri);
} catch (Throwable $e) {
    error_log('Unhandled application error: ' . $e);
    $isDbError = class_exists(\App\DB::class) && \App\DB::isConnectivityError($e);
    $isDebug = function_exists('config') && (bool) config('app_debug', false);
    http_response_code($isDbError ? 503 : 500);
    echo '<!DOCTYPE html><html lang="vi"><head><meta charset="UTF-8"><title>Lỗi Shop Hades</title>';
    echo '<style>body{font-family:sans-serif;max-width:760px;margin:40px auto;padding:20px;line-height:1.6}code{background:#f5f5f5;padding:2px 6px;border-radius:4px}pre{background:#f8f8f8;padding:12px;border-radius:8px;overflow:auto}</style></head><body>';
    echo '<h1>' . ($isDbError ? 'Cơ sở dữ liệu chưa sẵn sàng' : 'Không thể tải trang') . '</h1>';
    if ($isDebug) {
        $message = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        echo '<p><strong>Lỗi:</strong> ' . $message . '</p>';
    } else {
        echo '<p>Yêu cầu chưa thể xử lý. Vui lòng thử lại sau.</p>';
    }
    if ($isDebug && str_contains($e->getMessage(), 'pdo_mysql')) {
        echo '<p>Chạy lại bằng lệnh: <code>./start.sh</code> hoặc <code>/opt/lampp/bin/php -S 127.0.0.1:8000 -t public public/router.php</code></p>';
        echo '<p>Đảm bảo MySQL XAMPP đang chạy: <code>sudo /opt/lampp/lampp startmysql</code></p>';
    }
    if ($isDebug && $isDbError) {
        echo '<p>Web vẫn khởi động được, nhưng các trang cần dữ liệu sẽ tạm thời không dùng được cho tới khi MySQL hoạt động.</p>';
        echo '<pre>Kiểm tra nhanh:
- Health check: /health
- Khởi động MySQL XAMPP: sudo /opt/lampp/lampp startmysql
- Hoặc chạy Docker DB: docker compose up -d db</pre>';
    }
    echo '</body></html>';
}
