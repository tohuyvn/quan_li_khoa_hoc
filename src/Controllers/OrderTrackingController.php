<?php

namespace App\Controllers;

use App\DB;
use App\Models\Order;
use App\Services\DeliveryAutoCompletion;

class OrderTrackingController
{
    public static function haversineDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371; // km
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLng/2) * sin($dLng/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return $earthRadius * $c;
    }

    public function snapshot(int $orderId): void
    {
        ini_set('serialize_precision', '-1');
        header('Content-Type: application/json; charset=utf-8');

        $db = DB::connection();
        $stmt = $db->prepare('SELECT * FROM orders WHERE id = ? LIMIT 1');
        $stmt->execute([$orderId]);
        $order = $stmt->fetch();

        if (!$order) {
            http_response_code(404);
            echo json_encode(['error' => 'Đơn hàng không tồn tại']);
            return;
        }

        if (!$this->isAuthorized($order)) {
            http_response_code(403);
            echo json_encode(['error' => 'Bạn không có quyền truy cập thông tin đơn hàng này.']);
            return;
        }

        echo json_encode($this->trackingPayload($db, $order));
    }

    public function sse(int $orderId): void
    {
        // Disable execution time limit
        set_time_limit(0);
        ini_set('serialize_precision', '-1');

        $db = DB::connection();
        $stmt = $db->prepare('SELECT * FROM orders WHERE id = ? LIMIT 1');
        $stmt->execute([$orderId]);
        $order = $stmt->fetch();

        if (!$order) {
            header('HTTP/1.1 404 Not Found');
            echo json_encode(['error' => 'Đơn hàng không tồn tại']);
            return;
        }

        if (!$this->isAuthorized($order)) {
            header('HTTP/1.1 403 Forbidden');
            echo json_encode(['error' => 'Bạn không có quyền truy cập thông tin đơn hàng này.']);
            return;
        }

        // Set SSE Headers
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('X-Accel-Buffering: no');

        $response = $this->trackingPayload($db, $order);
        echo "retry: 5000\n\n";
        echo "data: " . json_encode($response) . "\n\n";
        ob_flush();
        flush();
    }

    private function isAuthorized(array $order): bool
    {
        $user = authUser();
        if ($user && (int)$order['user_id'] === (int)$user['id']) {
            return true;
        }

        $token = $_GET['token'] ?? '';
        $expectedToken = md5(($order['order_code'] ?? '') . ($order['phone'] ?? ''));

        return $token !== '' && hash_equals($expectedToken, $token);
    }

    private function trackingPayload(\PDO $db, array $order): array
    {
        $autoCompletion = DeliveryAutoCompletion::evaluate($db, $order);
        if ($autoCompletion['completed']) {
            $order['status'] = 'completed';
        }

        $visibility = $this->trackingVisibility($order);
        $response = [
            'status' => $order['status'],
            'shipping_lat' => $order['shipping_lat'] ? round((float)$order['shipping_lat'], 8) : null,
            'shipping_lng' => $order['shipping_lng'] ? round((float)$order['shipping_lng'], 8) : null,
            'tracking_visible' => $visibility['visible'],
            'tracking_message' => $this->trackingMessage($visibility['message'], $autoCompletion),
            'tracking_reveal_at' => $visibility['reveal_at'],
            'shipper' => null,
            'route' => [],
            'auto_complete' => [
                'eligible' => $autoCompletion['eligible'],
                'near_since' => $autoCompletion['near_since'],
                'seconds_near' => $autoCompletion['seconds_near'],
                'remaining_seconds' => $autoCompletion['remaining_seconds'],
                'distance_km' => $autoCompletion['distance_km'],
            ],
        ];

        if ($order['status'] !== 'shipping' || !$visibility['visible']) {
            return $response;
        }

        $stmtLoc = $db->prepare('SELECT * FROM shipper_locations WHERE order_id = ? ORDER BY recorded_at DESC, id DESC LIMIT 1');
        $stmtLoc->execute([(int) $order['id']]);
        $loc = $stmtLoc->fetch();

        if (!$loc || !$order['shipping_lat'] || !$order['shipping_lng']) {
            return $response;
        }

        $shipperLat = (float)$loc['latitude'];
        $shipperLng = (float)$loc['longitude'];
        $distance = self::haversineDistance(
            (float)$order['shipping_lat'],
            (float)$order['shipping_lng'],
            $shipperLat,
            $shipperLng
        );

        if ($distance <= 3.0) {
            $response['shipper'] = [
                'lat' => round($shipperLat, 8),
                'lng' => round($shipperLng, 8),
                'distance' => round($distance, 2),
            ];
            $response['route'] = [
                ['lat' => round($shipperLat, 8), 'lng' => round($shipperLng, 8)],
                ['lat' => round((float)$order['shipping_lat'], 8), 'lng' => round((float)$order['shipping_lng'], 8)],
            ];
        }

        return $response;
    }

    private function trackingMessage(string $defaultMessage, array $autoCompletion): string
    {
        if ($autoCompletion['completed']) {
            return 'Đơn hàng đã tự động hoàn thành vì shipper ở gần điểm giao hơn 2 phút.';
        }

        if ($autoCompletion['eligible']) {
            $remaining = (int) $autoCompletion['remaining_seconds'];
            if ($remaining > 0) {
                return 'Shipper đang ở trong bán kính 1km. Đơn sẽ tự hoàn thành sau khoảng ' . $remaining . ' giây nếu shipper vẫn ở gần điểm giao.';
            }
        }

        return $defaultMessage;
    }

    private function trackingVisibility(array $order): array
    {
        if (($order['status'] ?? '') !== 'shipping') {
            return ['visible' => false, 'message' => 'Đơn hàng chưa ở trạng thái đang giao.', 'reveal_at' => null];
        }

        $date = $order['estimated_delivery_from'] ?: date('Y-m-d');
        $slot = $order['preferred_time_slot'] ?: 'any';
        $slots = [
            'morning' => ['08:00', '11:30', 'Sáng'],
            'afternoon' => ['13:30', '17:30', 'Chiều'],
            'evening' => ['18:00', '21:00', 'Tối'],
            'any' => ['00:00', '23:59', 'Linh hoạt'],
        ];

        [$start, $end, $label] = $slots[$slot] ?? $slots['any'];
        $startAt = strtotime($date . ' ' . $start);
        $endAt = strtotime($date . ' ' . $end);
        $revealAt = $slot === 'any' ? strtotime($date . ' 00:00') : strtotime('-30 minutes', $startAt);
        $now = time();

        if ($now < $revealAt) {
            return [
                'visible' => false,
                'message' => 'Vị trí shipper sẽ hiển thị từ ' . date('H:i d/m/Y', $revealAt) . ' (trước khung giờ ' . $label . ' khoảng 30 phút).',
                'reveal_at' => date('c', $revealAt),
            ];
        }

        if ($now > $endAt) {
            return [
                'visible' => false,
                'message' => 'Đã quá khung giờ giao dự kiến. Vị trí shipper sẽ cập nhật lại khi shipper gửi tọa độ mới.',
                'reveal_at' => date('c', $revealAt),
            ];
        }

        return [
            'visible' => true,
            'message' => 'Shipper đang trên đường giao hàng.',
            'reveal_at' => date('c', $revealAt),
        ];
    }
}
