<?php

namespace App\Services;

use App\Models\Store;
use App\Models\StoreStock;

class StoreLocator
{
    public function nearest(float $lat, float $lng, array $cartItems = []): ?array
    {
        $stores = (new Store())->active();
        $candidates = [];
        $stock = new StoreStock();

        foreach ($stores as $store) {
            $distance = self::distanceKm($lat, $lng, (float) $store['lat'], (float) $store['lng']);
            if ($distance <= (float) $store['service_radius_km']) {
                $store['distance_km'] = round($distance, 2);
                $store['has_stock'] = $cartItems === [] || $stock->canFulfill((int) $store['id'], $cartItems);
                $candidates[] = $store;
            }
        }

        if ($candidates === []) {
            foreach ($stores as $store) {
                $store['distance_km'] = round(self::distanceKm($lat, $lng, (float) $store['lat'], (float) $store['lng']), 2);
                $store['has_stock'] = $cartItems === [] || $stock->canFulfill((int) $store['id'], $cartItems);
                $candidates[] = $store;
            }
        }

        usort($candidates, function (array $a, array $b): int {
            // Prefer stores with stock, then nearest
            if (($a['has_stock'] ?? false) !== ($b['has_stock'] ?? false)) {
                return ($b['has_stock'] ?? false) <=> ($a['has_stock'] ?? false);
            }

            return $a['distance_km'] <=> $b['distance_km'];
        });

        // Prefer first with stock; else nearest anyway
        foreach ($candidates as $store) {
            if (!empty($store['has_stock'])) {
                return $store;
            }
        }

        return $candidates[0] ?? null;
    }

    public static function distanceKm(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadiusKm = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return $earthRadiusKm * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}
