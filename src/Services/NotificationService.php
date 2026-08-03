<?php

namespace App\Services;

use App\DB;
use App\Models\Order;

class NotificationService
{
    public static function notifyOrderStatus(int $orderId, string $status): void
    {
        $order = (new Order())->find($orderId);
        if (!$order) {
            return;
        }

        $labels = [
            'pending' => 'Chờ xử lý',
            'confirmed' => 'Đã xác nhận',
            'packing' => 'Đang đóng gói',
            'shipping' => 'Đang giao hàng',
            'completed' => 'Đã hoàn thành',
            'cancelled' => 'Đã hủy',
        ];
        $label = $labels[$status] ?? $status;
        $code = $order['order_code'] ?? ('#' . $orderId);
        $title = 'Cập nhật đơn ' . $code;
        $body = 'Trạng thái mới: ' . $label;
        $url = '/orders/' . $orderId;

        $userId = !empty($order['user_id']) ? (int) $order['user_id'] : null;
        self::create($userId, $orderId, $title, $body, $url);
    }

    public static function create(?int $userId, ?int $orderId, string $title, string $body, ?string $url = null): void
    {
        try {
            $db = DB::connection();
            $stmt = $db->prepare('
                INSERT INTO app_notifications (user_id, order_id, title, body, url)
                VALUES (?, ?, ?, ?, ?)
            ');
            $stmt->execute([$userId, $orderId, $title, $body, $url]);
        } catch (\Throwable) {
        }
    }

    public static function unreadForUser(int $userId, int $limit = 20): array
    {
        $db = DB::connection();
        $stmt = $db->prepare('
            SELECT * FROM app_notifications
            WHERE user_id = ? AND is_read = 0
            ORDER BY id DESC
            LIMIT ' . (int) $limit
        );
        $stmt->execute([$userId]);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function markRead(int $userId, ?int $id = null): void
    {
        $db = DB::connection();
        if ($id) {
            $stmt = $db->prepare('UPDATE app_notifications SET is_read = 1 WHERE id = ? AND user_id = ?');
            $stmt->execute([$id, $userId]);
            return;
        }
        $stmt = $db->prepare('UPDATE app_notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0');
        $stmt->execute([$userId]);
    }

    public static function saveSubscription(?int $userId, string $endpoint, ?string $p256dh, ?string $auth): void
    {
        $db = DB::connection();
        $hash = hash('sha256', $endpoint);
        $stmt = $db->prepare('
            INSERT INTO push_subscriptions (user_id, endpoint, endpoint_hash, p256dh, auth, user_agent)
            VALUES (?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE user_id = VALUES(user_id), p256dh = VALUES(p256dh), auth = VALUES(auth), user_agent = VALUES(user_agent)
        ');
        $stmt->execute([
            $userId,
            $endpoint,
            $hash,
            $p256dh,
            $auth,
            substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 250),
        ]);
    }
}
