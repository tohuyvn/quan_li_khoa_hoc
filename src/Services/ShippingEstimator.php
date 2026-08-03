<?php

namespace App\Services;

class ShippingEstimator
{
    public function estimate(?float $distanceKm, ?string $preferredSlot = null): array
    {
        $distanceKm = $distanceKm !== null ? max(0, $distanceKm) : null;
        $fee = $this->fee($distanceKm);
        $range = $this->deliveryRange($distanceKm);

        if ($distanceKm !== null) {
            if ($distanceKm > 100) {
                $etaLabel = 'mất 3 ngày';
            } elseif ($distanceKm > 50) {
                $etaLabel = 'mất 2 ngày';
            } elseif ($distanceKm > 20) {
                $etaLabel = 'mất 1 ngày';
            } elseif ($distanceKm < 10) {
                $etaLabel = 'mất 1h';
            } else {
                $etaLabel = 'mất 1 ngày';
            }
        } else {
            $etaLabel = $this->formatRange($range['from'], $range['to']);
        }

        return [
            'distance_km' => $distanceKm !== null ? round($distanceKm, 2) : null,
            'shipping_fee' => $fee,
            'eta_from' => $range['from'],
            'eta_to' => $range['to'],
            'eta_label' => $etaLabel,
            'preferred_time_slot' => $this->normalizeSlot($preferredSlot),
        ];
    }

    private function fee(?float $distanceKm): int
    {
        if ($distanceKm === null || $distanceKm <= 3) {
            return 0;
        }

        if ($distanceKm <= 10) {
            return 25000;
        }

        return 25000 + (int) ceil($distanceKm - 10) * 5000;
    }

    private function deliveryRange(?float $distanceKm): array
    {
        $now = time();

        if ($distanceKm === null) {
            return ['from' => date('Y-m-d', strtotime('+3 days', $now)), 'to' => date('Y-m-d', strtotime('+5 days', $now))];
        }

        if ($distanceKm > 100) {
            $date = date('Y-m-d', strtotime('+3 days', $now));
            return ['from' => $date, 'to' => $date];
        }

        if ($distanceKm > 50) {
            $date = date('Y-m-d', strtotime('+2 days', $now));
            return ['from' => $date, 'to' => $date];
        }

        if ($distanceKm > 20) {
            $date = date('Y-m-d', strtotime('+1 day', $now));
            return ['from' => $date, 'to' => $date];
        }

        if ($distanceKm < 10) {
            $date = date('Y-m-d', $now);
            return ['from' => $date, 'to' => $date];
        }

        // 10 <= distance <= 20
        $date = date('Y-m-d', strtotime('+1 day', $now));
        return ['from' => $date, 'to' => $date];
    }

    private function formatRange(string $from, string $to): string
    {
        $fromLabel = date('d/m', strtotime($from));
        $toLabel = date('d/m', strtotime($to));

        return $from === $to ? "Dự kiến giao hôm nay ({$fromLabel})" : "Dự kiến giao {$fromLabel} - {$toLabel}";
    }

    private function normalizeSlot(?string $slot): string
    {
        return in_array($slot, ['morning', 'afternoon', 'evening'], true) ? $slot : 'any';
    }
}
