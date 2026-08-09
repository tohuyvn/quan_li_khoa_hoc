<?php

namespace App\Controllers\Api;

use App\DB;
use PDO;

class ChatbotController
{
    // ─────────────────────────────────────────────
    // Tiện ích: lấy lời chào theo giờ Việt Nam
    // ─────────────────────────────────────────────
    private function getGreetingByTime(): string
    {
        // Múi giờ Việt Nam (UTC+7)
        $hour = (int) (new \DateTime('now', new \DateTimeZone('Asia/Ho_Chi_Minh')))->format('H');

        if ($hour >= 5 && $hour < 12) {
            return "Chào buổi sáng";
        } elseif ($hour >= 12 && $hour < 14) {
            return "Chào buổi trưa";
        } elseif ($hour >= 14 && $hour < 18) {
            return "Chào buổi chiều";
        } else {
            return "Chào buổi tối";
        }
    }

    // ─────────────────────────────────────────────
    // ENTRY POINT
    // ─────────────────────────────────────────────
    public function chat(): void
    {
        header('Content-Type: application/json');

        $input    = json_decode(file_get_contents('php://input'), true) ?: [];
        $message  = trim($input['message'] ?? '');
        $history  = $this->syncHistory($input['history'] ?? [], !empty($input['clearHistory']));

        if (!empty($input['clearHistory'])) {
            echo json_encode(['reply' => '', 'source' => 'cleared', 'history' => []]);
            exit;
        }

        // Tin nhắn rỗng → lời chào mở đầu có giờ
        if ($message === '') {
            $greeting = $this->getGreetingByTime();
            $reply    = "{$greeting}! 👋 Mình là <strong>Hades AI</strong> – trợ lý ảo của Shop Hades. Mình có thể giúp bạn tìm sản phẩm, xem khuyến mãi, hoặc giải đáp bất kỳ thắc mắc nào nhé!";
            $this->jsonResponse($reply, 'greeting', '', $history);
        }

        $db          = DB::connection();
        $messageLower = mb_strtolower($message, 'UTF-8');

        // ── Lấy dữ liệu khuyến mãi ──────────────
        $stmtCoupons = $db->query("SELECT code, discount_percent, expires_at FROM promotions WHERE status = 'active'");
        $coupons     = $stmtCoupons->fetchAll(PDO::FETCH_ASSOC);

        $stmtSitePromos = $db->query("SELECT name, discount_percent, expires_at FROM site_promotions WHERE status = 'active'");
        $sitePromos     = $stmtSitePromos->fetchAll(PDO::FETCH_ASSOC);

        $promosContext = "";
        if (!empty($sitePromos)) {
            $promosContext .= "Sự kiện ưu đãi tự động hiện tại:\n";
            foreach ($sitePromos as $sp) {
                $promosContext .= "- Event: {$sp['name']} | Giảm: {$sp['discount_percent']}% | Hạn dùng: {$sp['expires_at']}\n";
            }
        }
        if (!empty($coupons)) {
            $promosContext .= "Mã giảm giá (Coupon nhập tay khi thanh toán):\n";
            foreach ($coupons as $cp) {
                $promosContext .= "- Code: {$cp['code']} | Giảm: {$cp['discount_percent']}% | Hạn dùng: {$cp['expires_at']}\n";
            }
        }
        if ($promosContext === "") {
            $promosContext = "Hiện tại không có chương trình khuyến mãi nào đang kích hoạt.\n";
        }

        // ── Lấy sản phẩm liên quan (tối đa 20, không load toàn bộ catalog) ──
        $products        = $this->searchRelevantProducts($db, $messageLower);
        $productsContext = '';
        foreach ($products as $p) {
            $formattedPrice   = number_format($p['price'], 0, ',', '.') . 'đ';
            $productsContext .= "- ID: {$p['id']} | Tên: {$p['name']} | Giá: {$formattedPrice} | Danh mục: {$p['category_name']} | Tồn kho: {$p['stock']} sp\n";
        }
        if ($productsContext === '') {
            $productsContext = "Không có sản phẩm phù hợp trong ngữ cảnh hiện tại.\n";
        }

        // ── Thông tin shop ────────────────────────
        $hotline  = config('hotline', '1800 1234');
        $shopInfo = "Giới thiệu Shop Hades:\n"
            . "- Hotline: {$hotline} (Miễn phí)\n"
            . "- Email: huy28072k5@gmail.com\n"
            . "- Ship miễn phí toàn quốc đơn từ 500.000đ; dưới 500.000đ phí 30.000đ. Giao 2-4 ngày.\n"
            . "- Thanh toán: COD, VNPay, MoMo, chuyển khoản VietQR (MB Bank).\n"
            . "- Chính sách đổi trả: Trong 7 ngày nếu lỗi từ nhà sản xuất.\n"
            . "- Hệ thống cửa hàng / chi nhánh:\n"
            . "  + Trụ sở chính: <a href='https://www.google.com/maps/search/?api=1&query=Võng+Thị+Tây+Hồ+Hà+Nội' target='_blank' style='color:#d70018;font-weight:bold;text-decoration:underline;'>Võng Thị, Tây Hồ, HN</a>\n"
            . "  + Cơ sở 2: <a href='https://www.google.com/maps/search/?api=1&query=Số+3+Duy+Tân+Cầu+Giấy+Hà+Nội' target='_blank' style='color:#d70018;font-weight:bold;text-decoration:underline;'>Số 3 Duy Tân, Cầu Giấy, HN</a>\n"
            . "  + Cơ sở 3: <a href='https://www.google.com/maps/search/?api=1&query=Lê+Duẩn+Hải+Châu+Đà+Nẵng' target='_blank' style='color:#d70018;font-weight:bold;text-decoration:underline;'>Lê Duẩn, Hải Châu, Đà Nẵng</a>\n"
            . "  + Cơ sở 4: <a href='https://www.google.com/maps/search/?api=1&query=Số+99+Nguyễn+Huệ+Quận+1+Hồ+Chí+Minh' target='_blank' style='color:#d70018;font-weight:bold;text-decoration:underline;'>Số 99 Nguyễn Huệ, Quận 1, HCM</a>\n"
            . "  + Cơ sở 5: <a href='https://www.google.com/maps/search/?api=1&query=Số+250+Cách+Mạng+Tháng+Tám+Quận+3+Sài+Gòn' target='_blank' style='color:#d70018;font-weight:bold;text-decoration:underline;'>Số 250 CMT8, Q.3, Sài Gòn</a>\n";

        $systemPrompt = $this->buildSystemPrompt($shopInfo, $promosContext, $productsContext);

        // ── Gemini API (sử dụng gemini-3.5-flash) ────────────
        $apiKey = config('gemini_api_key') ?: getenv('GEMINI_API_KEY') ?: '';

        if ($apiKey !== '') {
            $model   = config('gemini_model', 'gemini-3.5-flash');
            $url     = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=" . urlencode($apiKey);
            $payload = [
                'contents'          => $this->buildGeminiContents($history, $message),
                'systemInstruction' => ['parts' => [['text' => $systemPrompt]]],
                'generationConfig'  => ['temperature' => 0.4, 'maxOutputTokens' => 1000],
            ];

            $reply = $this->callGemini($url, $payload);

            if ($reply !== '') {
                // Tự động chuyển đổi Markdown link [Tên](/products/id) thành thẻ HTML link nếu Gemini dùng Markdown
                $reply = preg_replace_callback(
                    '/\[([^\]]+)\]\(([^)]+)\)/u',
                    function ($m) {
                        $title = $m[1];
                        $url   = $m[2];
                        return "<a href='{$url}' style='color:#d70018;font-weight:bold;text-decoration:underline;'>{$title}</a>";
                    },
                    $reply
                );

                $this->jsonResponse($reply, 'gemini', $message, $history);
            }
        }

        // Fallback khi không thể gọi Gemini API
        $errorReply = "Rất tiếc, Hades AI hiện không thể kết nối tới máy chủ AI. Vui lòng kiểm tra lại cấu hình API Key hoặc thử lại sau giây lát! 📞 Hotline hỗ trợ: {$hotline}";
        $this->jsonResponse($errorReply, 'error', $message, $history);
    }

    // ─────────────────────────────────────────────
    // Lịch sử hội thoại (session + client sync)
    // ─────────────────────────────────────────────
    private function syncHistory(array $clientHistory, bool $cleared = false): array
    {
        $user = function_exists('authUser') ? authUser() : null;
        $userId = $user ? (int) ($user['id'] ?? 0) : null;
        $sessionKey = \App\Services\ChatHistoryService::sessionKey();

        if ($cleared) {
            $_SESSION['chatbot_history'] = [];
            try {
                \App\Services\ChatHistoryService::clear($userId ?: null, $sessionKey);
            } catch (\Throwable $e) {
            }
            return [];
        }

        $clientHistory  = $this->normalizeHistory($clientHistory);
        $sessionHistory = $this->normalizeHistory($_SESSION['chatbot_history'] ?? []);

        if (empty($clientHistory) && empty($sessionHistory)) {
            try {
                $dbHistory = \App\Services\ChatHistoryService::load($userId ?: null, $sessionKey, 20);
                $sessionHistory = $this->normalizeHistory($dbHistory);
            } catch (\Throwable $e) {
            }
        }

        if (empty($clientHistory)) {
            $history = $sessionHistory;
        } elseif (empty($sessionHistory)) {
            $history = $clientHistory;
        } else {
            $history = count($clientHistory) >= count($sessionHistory) ? $clientHistory : $sessionHistory;
        }

        $_SESSION['chatbot_history'] = $history;

        return $history;
    }

    private function normalizeHistory(array $items): array
    {
        $normalized = [];
        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }
            $role = $item['role'] ?? '';
            $text = trim($item['text'] ?? $item['content'] ?? '');
            if (!in_array($role, ['user', 'model'], true) || $text === '') {
                continue;
            }
            $normalized[] = ['role' => $role, 'text' => mb_substr($text, 0, 1000)];
        }

        return array_slice($normalized, -20);
    }

    private function jsonResponse(string $reply, string $source, string $userMessage, array &$history): void
    {
        if ($userMessage !== '') {
            $history[] = ['role' => 'user', 'text' => $userMessage];
        }
        $history[] = ['role' => 'model', 'text' => $reply];
        $history   = array_slice($history, -20);
        $_SESSION['chatbot_history'] = $history;

        try {
            $user = function_exists('authUser') ? authUser() : null;
            $userId = $user ? (int) ($user['id'] ?? 0) : null;
            $sessionKey = \App\Services\ChatHistoryService::sessionKey();
            if ($userMessage !== '') {
                \App\Services\ChatHistoryService::append($userId ?: null, $sessionKey, 'user', $userMessage, $source);
            }
            \App\Services\ChatHistoryService::append($userId ?: null, $sessionKey, 'model', $reply, $source);
        } catch (\Throwable $e) {
        }

        echo json_encode([
            'reply'   => $reply,
            'source'  => $source,
            'history' => $history,
        ]);
        exit;
    }

    private function buildSystemPrompt(string $shopInfo, string $promosContext, string $productsContext): string
    {
        $greeting = $this->getGreetingByTime();

        return <<<PROMPT
Bạn là **Hades AI** – Trợ lý tư vấn sản phẩm thông minh của cửa hàng công nghệ **Shop Hades**.
Múi giờ hiện tại là Asia/Ho_Chi_Minh. Lời chào đúng thời điểm hiện tại: "{$greeting}!".

== PHONG CÁCH TRẢ LỜI ==
- Ngôn ngữ: Tiếng Việt tự nhiên, thân thiện, súc tích, dùng emoji sinh động.
- Xưng "mình" với khách hàng, gọi khách là "bạn".
- ĐỌC KỸ lịch sử hội thoại trước đó — trả lời ĐÚNG TRỌNG TÂM câu hỏi hiện tại.
- Nếu khách CHỈ chào hỏi / trò chuyện / hỏi thăm → chào lại ấm áp, không ép mua hàng.

== THÔNG TIN SHOP ==
{$shopInfo}

== KHUYẾN MÃI HIỆN TẠI ==
{$promosContext}

== SẢN PHẨM LIÊN QUAN TRONG HỆ THỐNG SHOP HADES ==
{$productsContext}

== QUY TẮC QUAN TRỌNG VỀ SẢN PHẨM & LINK (BẮT BUỘC TUÂN THỦ) ==
1. KHI NGƯỜI DÙNG HỎI TÌM SẢN PHẨM, HỎI DÒNG SẢN PHẨM (VÍ DỤ "iphone 15", "macbook", "tai nghe", "laptop"...):
   - Bạn BẮT BUỘC phải liệt kê chi tiết 2-5 sản phẩm phù hợp nhất có trong phần [SẢN PHẨM LIÊN QUAN TRONG HỆ THỐNG SHOP HADES].
   - TÊN MỖI SẢN PHẨM BẮT BUỘC PHẢI CHỨA LINK HTML THEO ĐÚNG CÚ PHÁP: <a href='/products/{id}' style='color:#d70018;font-weight:bold;text-decoration:underline;'>Tên sản phẩm</a>
   - Ví dụ format chuẩn bắt buộc:
     • <a href='/products/1' style='color:#d70018;font-weight:bold;text-decoration:underline;'>iPhone 15 Pro Max 256GB</a> — Giá: 29.990.000đ (Còn hàng)
     • <a href='/products/4' style='color:#d70018;font-weight:bold;text-decoration:underline;'>iPhone 15 128GB</a> — Giá: 19.990.000đ (Còn hàng)
2. Không bao giờ trả lời lấp lửng dạng "bên mình có các dòng này:" mà lại KHÔNG liệt kê danh sách sản phẩm và link bên dưới!
3. Nếu khách yêu cầu giá (ví dụ "dưới 20tr"), chỉ đưa sản phẩm có giá phù hợp.
4. Cuối câu trả lời, hãy hỏi nhẹ nhàng xem khách cần hỗ trợ thêm thông tin gì không.
PROMPT;
    }

    private function searchRelevantProducts(PDO $db, string $messageLower, int $limit = 20): array
    {
        $maxPrice = null;
        $minPrice = null;

        // Xử lý giá max (dưới / nhỏ hơn / <)
        if (preg_match('/(?:dưới|nhỏ hơn|<|duoi|nho hon)\s*([0-9\.,]+)\s*(triệu|tr|trieu|k|đ|đồng)?/u', $messageLower, $m)) {
            $val = (float) str_replace(['.', ','], '', $m[1]);
            $unit = mb_strtolower($m[2] ?? 'triệu', 'UTF-8');
            if ($unit === 'k') {
                $maxPrice = (int) ($val * 1000);
            } elseif ($unit === 'đ' || $unit === 'đồng') {
                $maxPrice = (int) $val;
            } else {
                $maxPrice = (int) ($val * 1000000);
            }
        }

        // Xử lý giá min (trên / lớn hơn / > / từ)
        if (preg_match('/(?:trên|lớn hơn|>|tren|lon hon)\s*([0-9\.,]+)\s*(triệu|tr|trieu|k|đ|đồng)?/u', $messageLower, $m)) {
            $val = (float) str_replace(['.', ','], '', $m[1]);
            $unit = mb_strtolower($m[2] ?? 'triệu', 'UTF-8');
            if ($unit === 'k') {
                $minPrice = (int) ($val * 1000);
            } elseif ($unit === 'đ' || $unit === 'đồng') {
                $minPrice = (int) $val;
            } else {
                $minPrice = (int) ($val * 1000000);
            }
        }

        $cleanMsg = preg_replace('/(?:dưới|nhỏ hơn|<|duoi|nho hon|trên|lớn hơn|>|tren|lon hon|từ)\s*[0-9\.,]+\s*(?:triệu|tr|trieu|k|đ|đồng)?/u', '', $messageLower);
        $stopWords = ['tìm', 'kiếm', 'mua', 'bán', 'hỏi', 'cần', 'shop', 'hades', 'giá', 'sản', 'phẩm', 'có', 'cho', 'xem', 'thử', 'muốn', 'gợi', 'ý', 'chào', 'bạn', 'mình', 'nào', 'gì', 'không', 'nhỉ', 'tư', 'vấn'];
        $terms = array_filter(
            explode(' ', trim(preg_replace('/\s+/', ' ', $cleanMsg))),
            fn($t) => mb_strlen($t, 'UTF-8') > 1 && !in_array($t, $stopWords, true)
        );

        $sql = "
            SELECT p.id, p.name, p.price, p.stock, c.name AS category_name
            FROM products p
            JOIN categories c ON p.category_id = c.id
            WHERE p.status = 'active'
        ";
        $params = [];

        if ($maxPrice !== null) {
            $sql .= ' AND p.price <= ?';
            $params[] = $maxPrice;
        }
        if ($minPrice !== null) {
            $sql .= ' AND p.price >= ?';
            $params[] = $minPrice;
        }

        if (!empty($terms)) {
            $likes = [];
            foreach ($terms as $term) {
                $likes[] = "(p.name LIKE ? OR c.name LIKE ?)";
                $params[] = '%' . $term . '%';
                $params[] = '%' . $term . '%';
            }
            $sql .= ' AND (' . implode(' OR ', $likes) . ')';
        }

        $sql .= ' ORDER BY p.price ASC LIMIT ' . (int) $limit;
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($results)) {
            return $results;
        }

        $fallback = $db->query("
            SELECT p.id, p.name, p.price, p.stock, c.name AS category_name
            FROM products p
            JOIN categories c ON p.category_id = c.id
            WHERE p.status = 'active'
            ORDER BY p.price ASC
            LIMIT " . (int) $limit);

        return $fallback->fetchAll(PDO::FETCH_ASSOC);
    }

    private function buildGeminiContents(array $history, string $message): array
    {
        $contents = [];
        foreach ($history as $item) {
            $role = $item['role'] === 'model' ? 'model' : 'user';
            $contents[] = [
                'role'  => $role,
                'parts' => [['text' => $item['text']]],
            ];
        }
        $contents[] = [
            'role'  => 'user',
            'parts' => [['text' => $message]],
        ];

        return $contents;
    }

    private function callGemini(string $url, array $payload): string
    {
        for ($attempt = 0; $attempt < 2; $attempt++) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => json_encode($payload),
                CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
                CURLOPT_CONNECTTIMEOUT => 5,
                CURLOPT_TIMEOUT        => 18,
            ]);
            $response = curl_exec($ch);
            $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlErr  = curl_error($ch);
            curl_close($ch);

            if ($httpCode === 200 && $response) {
                $resData = json_decode($response, true);
                $reply   = trim($resData['candidates'][0]['content']['parts'][0]['text'] ?? '');
                if ($reply !== '') {
                    return $reply;
                }
            }

            if ($httpCode === 429 && $attempt === 0) {
                usleep(400000);
                continue;
            }

            if ($httpCode !== 200) {
                $logDir = BASE_PATH . '/storage/logs';
                if (!is_dir($logDir)) {
                    mkdir($logDir, 0775, true);
                }
                file_put_contents(
                    $logDir . '/gemini.log',
                    date('Y-m-d H:i:s') . " HTTP {$httpCode}: " . ($response ?: $curlErr) . "\n",
                    FILE_APPEND
                );
            }

            break;
        }

        return '';
    }
}
