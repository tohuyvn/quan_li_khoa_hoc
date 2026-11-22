<?php

namespace App;

use App\Models\Order;

class OrderStateService
{
    private const TRANSITIONS = [
        'pending'   => ['confirmed', 'cancelled'],
        'confirmed' => ['packing', 'cancelled'],
        'packing'   => ['shipping', 'cancelled'],
        'shipping'  => ['completed', 'cancelled'],
        'completed' => [],
        'cancelled' => [],
    ];

    public static function canTransition(string $from, string $to): bool
    {
        if ($from === $to) {
            return true;
        }

        return in_array($to, self::TRANSITIONS[$from] ?? [], true);
    }

    public static function allowedNext(string $status): array
    {
        return self::TRANSITIONS[$status] ?? [];
    }

    public static function logStatus(int $orderId, ?string $fromStatus, string $toStatus, string $actorType, ?int $actorId, ?string $note = null): bool
    {
        try {
            $db = DB::connection();
            $stmt = $db->prepare('INSERT INTO order_status_logs (order_id, from_status, to_status, actor_type, actor_id, note) VALUES (?, ?, ?, ?, ?, ?)');
            return $stmt->execute([$orderId, $fromStatus, $toStatus, $actorType, $actorId, $note]);
        } catch (\Throwable $e) {
            return false;
        }
    }

    public static function transition(int $orderId, string $newStatus, string $actorType = 'system', ?int $actorId = null, ?string $note = null): array
    {
        $orderModel = new Order();
        $order = $orderModel->find($orderId);

        if (!$order) {
            return ['ok' => false, 'error' => 'Không tìm thấy đơn hàng.'];
        }

        $current = $order['status'] ?? 'pending';
        if ($current === $newStatus) {
            return ['ok' => true, 'order' => $order, 'unchanged' => true];
        }

        if (!self::canTransition($current, $newStatus)) {
            return [
                'ok'    => false,
                'error' => "Không thể chuyển từ \"{$current}\" sang \"{$newStatus}\".",
            ];
        }

        $items = null;
        if (in_array($newStatus, ['packing', 'shipping', 'completed'], true)) {
            $items = $orderModel->itemsWithProduct($orderId);
        }

        if ($newStatus === 'packing') {
            try {
                // Legacy physical shop handler removed
                if (!$reservation['ok']) {
                // Legacy physical shop handler removed
                    return [
                        'ok' => false,
                        'error' => $reservation['error']
                            ?? 'Không thể đóng gói vì chưa gắn đủ IMEI / Serial.',
                    ];
                }
            } catch (\Throwable $e) {
                // Legacy physical shop handler removed
                return ['ok' => false, 'error' => 'Không thể cấp IMEI / Serial: ' . $e->getMessage()];
            }
        }

        if (in_array($newStatus, ['shipping', 'completed'], true)) {
            try {
                // Legacy physical shop handler removed
                if ($summary['missing'] > 0) {
                    return [
                        'ok' => false,
                        'error' => "Đơn còn thiếu {$summary['missing']} IMEI / Serial; chưa thể chuyển sang bước tiếp theo.",
                    ];
                }
            } catch (\Throwable $e) {
                return ['ok' => false, 'error' => 'Không kiểm tra được IMEI / Serial: ' . $e->getMessage()];
            }
        }

        $fields = ['status' => $newStatus];
        $wasPaid = ($order['payment_status'] ?? '') === 'paid';
        if ($newStatus === 'cancelled' && $wasPaid) {
            $fields['payment_status'] = 'refunded';
        }

        $db = DB::connection();
        try {
            $db->beginTransaction();
            if (isset($fields['payment_status'])) {
                $statusStmt = $db->prepare(
                    'UPDATE orders SET status = ?, payment_status = ? WHERE id = ? AND status = ?'
                );
                $statusStmt->execute([$newStatus, $fields['payment_status'], $orderId, $current]);
            } else {
                $statusStmt = $db->prepare('UPDATE orders SET status = ? WHERE id = ? AND status = ?');
                $statusStmt->execute([$newStatus, $orderId, $current]);
            }

            if ($statusStmt->rowCount() !== 1) {
                $db->rollBack();
                $freshOrder = $orderModel->find($orderId);
                if (($freshOrder['status'] ?? null) === $newStatus) {
                    return ['ok' => true, 'order' => $freshOrder, 'unchanged' => true];
                }
                return ['ok' => false, 'error' => 'Trạng thái đơn hàng vừa được thay đổi. Vui lòng tải lại trang.'];
            }

            if ($newStatus === 'cancelled' && !empty($order['stock_reserved'])) {
                self::restoreReservedStock($db, $orderId);
                $db->prepare('UPDATE orders SET stock_reserved = 0 WHERE id = ?')->execute([$orderId]);
            }

            $db->commit();
            $updated = $orderModel->find($orderId);
        } catch (\Throwable $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            error_log('Order status transition failed: ' . $e->getMessage());
            return ['ok' => false, 'error' => 'Không thể cập nhật trạng thái đơn hàng lúc này.'];
        }

        if ($newStatus === 'packing') {
            try {
                // Legacy physical shop handler removed
            } catch (\Throwable $e) {
                // non-blocking
            }
        }

        if ($newStatus === 'cancelled') {
            try {
                // Legacy physical shop handler removed
            } catch (\Throwable $e) {
            }

            // Đơn đã thanh toán → hoàn tiền về Ví Hades (idempotent)
            if ($wasPaid) {
                try {
                    self::refundPaidOrderToWallet($order);
                } catch (\Throwable $e) {
                    // Không chặn hủy đơn; log để admin xử lý tay nếu cần
                    $logDir = BASE_PATH . '/storage/logs';
                    if (!is_dir($logDir)) {
                        @mkdir($logDir, 0775, true);
                    }
                    @file_put_contents(
                        $logDir . '/refund.log',
                        sprintf("[%s] order #%d wallet refund failed: %s\n", date('Y-m-d H:i:s'), $orderId, $e->getMessage()),
                        FILE_APPEND
                    );
                }
            }
        }

        if ($newStatus === 'completed') {
            // Course order completed
        }

        self::logStatus($orderId, $current, $newStatus, $actorType, $actorId, $note);
        $updated = $orderModel->find($orderId);

        return ['ok' => true, 'order' => $updated, 'unchanged' => false];
    }

    private static function restoreReservedStock(\PDO $db, int $orderId): void
    {
        $hasVariantColumn = (bool) $db->query("SHOW COLUMNS FROM order_items LIKE 'variant_id'")->fetch();
        $variantSelect = $hasVariantColumn ? 'oi.variant_id' : 'NULL AS variant_id';
        $groupVariant = $hasVariantColumn ? 'oi.variant_id' : 'p.id';
        $stmt = $db->prepare(
            "SELECT oi.product_id, {$variantSelect}, SUM(oi.quantity) AS quantity, p.is_preorder
             FROM order_items oi
             INNER JOIN products p ON p.id = oi.product_id
             WHERE oi.order_id = ?
             GROUP BY oi.product_id, {$groupVariant}, p.is_preorder"
        );
        $stmt->execute([$orderId]);

        $restoreProduct = $db->prepare('UPDATE products SET stock = stock + ? WHERE id = ?');
        $restoreVariant = $db->prepare(
            'UPDATE product_variants SET stock = stock + ? WHERE id = ? AND product_id = ?'
        );
        foreach ($stmt->fetchAll() as $item) {
            if ((int) ($item['is_preorder'] ?? 0) === 1) {
                continue;
            }
            $quantity = (int) $item['quantity'];
            $productId = (int) $item['product_id'];
            $restoreProduct->execute([$quantity, $productId]);
            if ($hasVariantColumn && !empty($item['variant_id'])) {
                $restoreVariant->execute([$quantity, (int) $item['variant_id'], $productId]);
            }
        }
    }

    /**
     * Hoàn tiền đơn đã paid về ví khi hủy (tránh cộng trùng).
     */
    private static function refundPaidOrderToWallet(array $order): void
    {
        $userId = (int) ($order['user_id'] ?? 0);
        $orderId = (int) ($order['id'] ?? 0);
        $amount = (int) ($order['total_amount'] ?? 0);

        if ($userId <= 0 || $orderId <= 0 || $amount <= 0) {
            return;
        }

        $db = DB::connection();

        // Đã hoàn qua hủy đơn trước đó?
        $stmt = $db->prepare(
            "SELECT id FROM wallet_transactions
             WHERE user_id = ? AND type = 'credit' AND ref_type = 'order_cancel' AND ref_id = ?
             LIMIT 1"
        );
        $stmt->execute([$userId, $orderId]);
        if ($stmt->fetch()) {
            return;
        }

        // Đã duyệt hoàn tiền (RefundService) cho đơn này?
        $stmt = $db->prepare(
            "SELECT id FROM refund_requests WHERE order_id = ? AND status = 'approved' LIMIT 1"
        );
        $stmt->execute([$orderId]);
        if ($stmt->fetch()) {
            return;
        }

        $code = $order['order_code'] ?? ('#' . $orderId);
        // Legacy physical shop refund handler removed
        return;

        // Đóng yêu cầu hoàn tiền đang chờ (đã cộng ví ở trên)
        $db->prepare(
            "UPDATE refund_requests
             SET status = 'approved',
                 admin_note = COALESCE(NULLIF(admin_note, ''), 'Tự động hoàn về ví khi hủy đơn'),
                 processed_at = ?
             WHERE order_id = ? AND status = 'pending'"
        )->execute([date('Y-m-d H:i:s'), $orderId]);
    }
}
