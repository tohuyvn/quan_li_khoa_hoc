<?php

namespace App\Services;

use App\OrderStateService;

class DeliveryAutoCompletion
{
    private const NEAR_DISTANCE_KM = 1.0;
    private const REQUIRED_SECONDS = 120;

    public static function evaluate(\PDO $db, array $order): array
    {
        $result = [
            'completed' => false,
            'eligible' => false,
            'distance_km' => null,
            'near_since' => null,
            'seconds_near' => 0,
            'remaining_seconds' => self::REQUIRED_SECONDS,
        ];

        if (($order['status'] ?? '') !== 'shipping' || empty($order['shipping_lat']) || empty($order['shipping_lng'])) {
            return $result;
        }

        $stmt = $db->prepare('
            SELECT latitude, longitude, recorded_at
            FROM shipper_locations
            WHERE order_id = ?
            ORDER BY recorded_at ASC, id ASC
            LIMIT 300
        ');
        $stmt->execute([(int) $order['id']]);
        $locations = $stmt->fetchAll();

        if (!$locations) {
            return $result;
        }

        $nearSince = null;
        $latestDistance = null;

        foreach ($locations as $loc) {
            $distance = self::haversineDistance(
                (float) $order['shipping_lat'],
                (float) $order['shipping_lng'],
                (float) $loc['latitude'],
                (float) $loc['longitude']
            );
            $latestDistance = $distance;

            if ($distance < self::NEAR_DISTANCE_KM) {
                $nearSince ??= (string) $loc['recorded_at'];
                continue;
            }

            $nearSince = null;
        }

        $result['distance_km'] = $latestDistance === null ? null : round($latestDistance, 2);

        if ($latestDistance === null || $latestDistance >= self::NEAR_DISTANCE_KM || $nearSince === null) {
            return $result;
        }

        $secondsNear = max(0, time() - strtotime($nearSince));
        $result['eligible'] = true;
        $result['near_since'] = date('c', strtotime($nearSince));
        $result['seconds_near'] = $secondsNear;
        $result['remaining_seconds'] = max(0, self::REQUIRED_SECONDS - $secondsNear);

        if ($secondsNear < self::REQUIRED_SECONDS) {
            return $result;
        }

        $transition = OrderStateService::transition(
            (int) $order['id'],
            'completed',
            'system',
            null,
            'Tự động hoàn thành: shipper ở trong bán kính 1km quá 2 phút.'
        );

        $result['completed'] = (bool) ($transition['ok'] ?? false);

        return $result;
    }

    private static function haversineDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) * sin($dLat / 2)
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
            * sin($dLng / 2) * sin($dLng / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
