<?php

namespace App\Services;

use App\DB;
use App\Models\Order;
use App\Models\Store;

class ShipperMatcher
{
    public const MAX_ACTIVE_ORDERS = 3;
    public const ONLINE_WINDOW_SECONDS = 900; // 15 minutes

    public static function assign(int $orderId): array
    {
        $db = DB::connection();
        $orderModel = new Order();
        $order = $orderModel->find($orderId);

        if (!$order) {
            return ['ok' => false, 'error' => 'Order not found'];
        }

        if (!empty($order['shipper_id'])) {
            return ['ok' => true, 'shipper_id' => (int) $order['shipper_id'], 'unchanged' => true];
        }

        $storeLat = null;
        $storeLng = null;
        if (!empty($order['store_id'])) {
            $store = (new Store())->find((int) $order['store_id']);
            if ($store) {
                $storeLat = (float) $store['lat'];
                $storeLng = (float) $store['lng'];
            }
        }
        if ($storeLat === null) {
            $storeLat = isset($order['shipping_lat']) ? (float) $order['shipping_lat'] : null;
            $storeLng = isset($order['shipping_lng']) ? (float) $order['shipping_lng'] : null;
        }

        $shippers = self::candidateShippers($db);
        if ($shippers === []) {
            return ['ok' => false, 'error' => 'No available shipper'];
        }

        $best = null;
        $bestDistance = PHP_FLOAT_MAX;

        foreach ($shippers as $shipper) {
            $active = self::activeOrderCount($db, (int) $shipper['id']);
            if ($active >= self::MAX_ACTIVE_ORDERS) {
                continue;
            }

            $distance = 0.0;
            if ($storeLat !== null && $storeLng !== null && isset($shipper['lat'], $shipper['lng'])) {
                $distance = StoreLocator::distanceKm($storeLat, $storeLng, (float) $shipper['lat'], (float) $shipper['lng']);
            }

            if ($distance < $bestDistance) {
                $bestDistance = $distance;
                $best = $shipper;
            }
        }

        if (!$best) {
            return ['ok' => false, 'error' => 'All shippers at capacity'];
        }

        $orderModel->update($orderId, [
            'shipper_id' => (int) $best['id'],
            'shipper_assigned_at' => date('Y-m-d H:i:s'),
        ]);

        return [
            'ok' => true,
            'shipper_id' => (int) $best['id'],
            'distance_km' => round($bestDistance, 2),
        ];
    }

    private static function candidateShippers(\PDO $db): array
    {
        $since = date('Y-m-d H:i:s', time() - self::ONLINE_WINDOW_SECONDS);

        // Prefer shippers with recent GPS ping
        $stmt = $db->prepare("
            SELECT u.id, u.name, loc.latitude AS lat, loc.longitude AS lng, loc.recorded_at
            FROM users u
            LEFT JOIN (
                SELECT sl.shipper_id, sl.latitude, sl.longitude, sl.recorded_at
                FROM shipper_locations sl
                INNER JOIN (
                    SELECT shipper_id, MAX(id) AS max_id
                    FROM shipper_locations
                    GROUP BY shipper_id
                ) latest ON latest.max_id = sl.id
            ) loc ON loc.shipper_id = u.id
            WHERE u.role = 'shipper'
              AND (loc.recorded_at IS NULL OR loc.recorded_at >= ?)
            ORDER BY (loc.recorded_at IS NULL) ASC, loc.recorded_at DESC
        ");
        $stmt->execute([$since]);
        $online = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if ($online !== []) {
            return $online;
        }

        // Fallback: any shipper account
        $all = $db->query("SELECT id, name, NULL AS lat, NULL AS lng, NULL AS recorded_at FROM users WHERE role = 'shipper'")->fetchAll(\PDO::FETCH_ASSOC);

        return $all ?: [];
    }

    private static function activeOrderCount(\PDO $db, int $shipperId): int
    {
        $stmt = $db->prepare("SELECT COUNT(*) FROM orders WHERE shipper_id = ? AND status IN ('packing', 'shipping')");
        $stmt->execute([$shipperId]);

        return (int) $stmt->fetchColumn();
    }
}
