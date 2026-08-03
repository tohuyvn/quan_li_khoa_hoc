<?php

namespace App\Services;

use App\DB;

class LoyaltyService
{
    /**
     * Tiers config: name => min_points
     */
    public const TIERS = [
        'Diamond'  => 10000,
        'Gold'     => 5000,
        'Silver'   => 1000,
        'Standard' => 0,
    ];

    /**
     * Tier discounts: name => discount_percent
     */
    public const TIER_DISCOUNTS = [
        'Diamond'  => 10,
        'Gold'     => 5,
        'Silver'   => 2,
        'Standard' => 0,
    ];

    /** Trần điểm / đơn — tránh overflow INT UNSIGNED khi total_amount test quá lớn */
    public const MAX_POINTS_PER_ORDER = 1_000_000;

    /** Trần điểm tích lũy trên user (INT UNSIGNED max) */
    public const MAX_USER_POINTS = 4_294_967_295;

    /**
     * Determine tier based on points
     */
    public static function determineTier(int $points): string
    {
        foreach (self::TIERS as $tier => $minPoints) {
            if ($points >= $minPoints) {
                return $tier;
            }
        }
        return 'Standard';
    }

    /**
     * Get discount percentage for a tier
     */
    public static function getDiscountPercent(string $tier): int
    {
        return self::TIER_DISCOUNTS[$tier] ?? 0;
    }

    /**
     * Award points to user for a paid/completed order
     */
    public static function awardPointsForOrder(int $orderId): bool
    {
        $db = DB::connection();
        $startedHere = false;
        if (!$db->inTransaction()) {
            $db->beginTransaction();
            $startedHere = true;
        }

        try {
            // Fetch order
            $stmt = $db->prepare('SELECT id, user_id, total_amount, points_awarded, status, payment_status FROM orders WHERE id = ?');
            $stmt->execute([$orderId]);
            $order = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$order) {
                if ($startedHere) {
                    $db->rollBack();
                }
                return false;
            }

            // Only award if user exists, and points not already awarded
            if (empty($order['user_id']) || (int)$order['points_awarded'] === 1) {
                if ($startedHere) {
                    $db->rollBack();
                }
                return false;
            }

            // Only award if order is either paid or completed
            $isEligible = ($order['payment_status'] === 'paid' || $order['status'] === 'completed');
            if (!$isEligible) {
                if ($startedHere) {
                    $db->rollBack();
                }
                return false;
            }

            // Calculate points: 1 point per 10,000 VND spent (cap để tránh overflow DB)
            $totalAmount = max(0, (int) $order['total_amount']);
            $pointsEarned = (int) floor($totalAmount / 10000);
            $pointsEarned = min($pointsEarned, self::MAX_POINTS_PER_ORDER);
            if ($pointsEarned <= 0) {
                // Mark order as processed for points anyway to avoid re-evaluating
                $up = $db->prepare('UPDATE orders SET points_awarded = 1, earned_points = 0 WHERE id = ?');
                $up->execute([$orderId]);
                if ($startedHere) {
                    $db->commit();
                }
                return true;
            }

            // Update user points (không vượt INT UNSIGNED)
            $upUser = $db->prepare('UPDATE users SET loyalty_points = LEAST(?, loyalty_points + ?) WHERE id = ?');
            $upUser->execute([self::MAX_USER_POINTS, $pointsEarned, $order['user_id']]);

            // Re-fetch user points to determine new tier
            $getUser = $db->prepare('SELECT loyalty_points FROM users WHERE id = ?');
            $getUser->execute([$order['user_id']]);
            $currentPoints = (int)$getUser->fetchColumn();

            $newTier = self::determineTier($currentPoints);

            // Update user tier
            $upTier = $db->prepare('UPDATE users SET loyalty_tier = ? WHERE id = ?');
            $upTier->execute([$newTier, $order['user_id']]);

            // Update order record
            $upOrder = $db->prepare('UPDATE orders SET points_awarded = 1, earned_points = ? WHERE id = ?');
            $upOrder->execute([$pointsEarned, $orderId]);

            if ($startedHere) {
                $db->commit();
            }
            return true;
        } catch (\Throwable $e) {
            if ($startedHere && $db->inTransaction()) {
                $db->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Deduct/revert points if order is cancelled or refunded
     */
    public static function revertPointsForOrder(int $orderId): bool
    {
        $db = DB::connection();
        $startedHere = false;
        if (!$db->inTransaction()) {
            $db->beginTransaction();
            $startedHere = true;
        }

        try {
            $stmt = $db->prepare('SELECT id, user_id, earned_points, points_awarded FROM orders WHERE id = ?');
            $stmt->execute([$orderId]);
            $order = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$order || empty($order['user_id']) || (int)$order['points_awarded'] === 0) {
                if ($startedHere) {
                    $db->rollBack();
                }
                return false;
            }

            $pointsToDeduct = (int)$order['earned_points'];
            if ($pointsToDeduct > 0) {
                // Deduct points (cannot go below 0)
                $upUser = $db->prepare('UPDATE users SET loyalty_points = GREATEST(0, CAST(loyalty_points AS SIGNED) - ?) WHERE id = ?');
                $upUser->execute([$pointsToDeduct, $order['user_id']]);

                // Re-fetch points to update tier
                $getUser = $db->prepare('SELECT loyalty_points FROM users WHERE id = ?');
                $getUser->execute([$order['user_id']]);
                $currentPoints = (int)$getUser->fetchColumn();

                $newTier = self::determineTier($currentPoints);

                $upTier = $db->prepare('UPDATE users SET loyalty_tier = ? WHERE id = ?');
                $upTier->execute([$newTier, $order['user_id']]);
            }

            // Reset order record
            $upOrder = $db->prepare('UPDATE orders SET points_awarded = 0, earned_points = 0 WHERE id = ?');
            $upOrder->execute([$orderId]);

            if ($startedHere) {
                $db->commit();
            }
            return true;
        } catch (\Throwable $e) {
            if ($startedHere && $db->inTransaction()) {
                $db->rollBack();
            }
            throw $e;
        }
    }
}
