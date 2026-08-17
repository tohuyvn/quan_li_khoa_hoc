<?php

namespace App\Services;

use App\DB;
use App\Models\Order;

class DeviceUnitService
{
    public static function reserveForOrder(int $orderId): array
    {
        $db = DB::connection();
        $orderModel = new Order();
        $order = $orderModel->find($orderId);
        if (!$order) {
            return ['ok' => false, 'reserved' => 0, 'error' => 'Order not found'];
        }

        $items = $orderModel->itemsWithProduct($orderId);
        $storeId = !empty($order['store_id']) ? (int) $order['store_id'] : null;
        $reserved = 0;
        $required = 0;
        $assignedByProduct = [];

        $assignedStmt = $db->prepare("
            SELECT product_id, COUNT(*) AS qty
            FROM device_units
            WHERE order_id = ? AND status IN ('reserved', 'sold')
            GROUP BY product_id
        ");
        $assignedStmt->execute([$orderId]);
        foreach ($assignedStmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            $assignedByProduct[(int) $row['product_id']] = (int) $row['qty'];
        }

        foreach ($items as $item) {
            $lineQty = max(1, (int) ($item['quantity'] ?? 1));
            $required += $lineQty;
            $productId = (int) $item['product_id'];
            $variantId = !empty($item['variant_id']) ? (int) $item['variant_id'] : null;
            $alreadyAssigned = min($lineQty, $assignedByProduct[$productId] ?? 0);
            $assignedByProduct[$productId] = max(0, ($assignedByProduct[$productId] ?? 0) - $alreadyAssigned);
            $need = $lineQty - $alreadyAssigned;

            for ($i = 0; $i < $need; $i++) {
                $unit = self::pickAvailable($db, $productId, $variantId, $storeId);
                if (!$unit) {
                    // Try without store constraint
                    $unit = self::pickAvailable($db, $productId, $variantId, null);
                }
                if (!$unit) {
                    // Tự động sinh IMEI mới cho sản phẩm nếu chưa có trong kho
                    $autoImei = self::generateFallbackImei($db);
                    $ins = $db->prepare("INSERT INTO device_units (imei, product_id, variant_id, store_id, status, note) VALUES (?, ?, ?, ?, 'available', 'Tự sinh cho đơn hàng #" . $orderId . "')");
                    $ins->execute([$autoImei, $productId, $variantId, $storeId]);
                    $unit = ['id' => (int) $db->lastInsertId(), 'imei' => $autoImei];
                }

                $upd = $db->prepare("UPDATE device_units SET status = 'reserved', order_id = ?, store_id = COALESCE(store_id, ?) WHERE id = ? AND status = 'available'");
                $upd->execute([$orderId, $storeId, (int) $unit['id']]);
                if ($upd->rowCount() > 0) {
                    $reserved++;
                }
            }
        }

        $summary = self::assignmentSummary($orderId, $items);

        return [
            'ok' => $summary['missing'] === 0,
            'reserved' => $reserved,
            'assigned' => $summary['assigned'],
            'required' => $required,
            'missing' => $summary['missing'],
            'error' => $summary['missing'] > 0
                ? "Kho thiếu {$summary['missing']} IMEI / Serial cho đơn hàng."
                : null,
        ];
    }

    public static function releaseForOrder(int $orderId): void
    {
        $db = DB::connection();
        $stmt = $db->prepare("UPDATE device_units SET status = 'available', order_id = NULL WHERE order_id = ? AND status = 'reserved'");
        $stmt->execute([$orderId]);
    }

    public static function markSoldAndBuildWarranties(int $orderId, array $order, array $items): int
    {
        $db = DB::connection();
        $created = 0;

        $checkStmt = $db->prepare('SELECT COUNT(*) FROM warranties WHERE order_id = ?');
        $checkStmt->execute([$orderId]);
        if ((int) $checkStmt->fetchColumn() > 0) {
            return 0;
        }

        $unitsStmt = $db->prepare("SELECT * FROM device_units WHERE order_id = ? AND status = 'reserved' ORDER BY id ASC");
        $unitsStmt->execute([$orderId]);
        $units = $unitsStmt->fetchAll(\PDO::FETCH_ASSOC);
        $unitByProduct = [];
        foreach ($units as $unit) {
            $pid = (int) $unit['product_id'];
            $unitByProduct[$pid][] = $unit;
        }

        $insertStmt = $db->prepare('
            INSERT INTO warranties (order_id, product_id, customer_name, customer_phone, email, imei, warranty_code, status, started_at, expired_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, \'active\', ?, ?)
        ');
        $markSold = $db->prepare("UPDATE device_units SET status = 'sold' WHERE id = ?");
        $startedAt = date('Y-m-d H:i:s');
        $expiredAt = date('Y-m-d H:i:s', strtotime('+12 months'));
        $customerEmail = $order['email'] ?? null;

        foreach ($items as $item) {
            $productId = (int) $item['product_id'];
            $qty = max(1, (int) ($item['quantity'] ?? 1));
            $productName = $item['product_name'] ?? $item['name'] ?? 'Sản phẩm';

            for ($i = 0; $i < $qty; $i++) {
                $imei = null;
                $unitId = null;
                if (!empty($unitByProduct[$productId])) {
                    $unit = array_shift($unitByProduct[$productId]);
                    $imei = $unit['imei'];
                    $unitId = (int) $unit['id'];
                } else {
                    throw new \RuntimeException("Thiếu IMEI / Serial cho sản phẩm {$productName}.");
                }

                $wCode = self::generateWarrantyCode($db);
                $insertStmt->execute([
                    $orderId,
                    $productId,
                    $order['customer_name'] ?? 'Khách hàng',
                    $order['phone'] ?? '',
                    $customerEmail,
                    $imei,
                    $wCode,
                    $startedAt,
                    $expiredAt,
                ]);
                $created++;

                if ($unitId) {
                    $markSold->execute([$unitId]);
                }

                if (!empty($customerEmail)) {
                    try {
                        \App\WarrantyMailer::sendActivated([
                            'customer_name' => $order['customer_name'] ?? 'Quý khách',
                            'warranty_code' => $wCode,
                            'imei' => $imei,
                            'product_name' => $productName,
                            'expired_at' => $expiredAt,
                            'email' => $customerEmail,
                        ], $customerEmail);
                    } catch (\Throwable) {
                    }
                }
            }
        }

        // Any leftover reserved units for this order → available again
        self::releaseForOrder($orderId);

        return $created;
    }

    /**
     * @return array{required:int,assigned:int,missing:int}
     */
    public static function assignmentSummary(int $orderId, ?array $items = null): array
    {
        $db = DB::connection();
        if ($items === null) {
            $items = (new Order())->itemsWithProduct($orderId);
        }

        $required = 0;
        foreach ($items as $item) {
            $required += max(1, (int) ($item['quantity'] ?? 1));
        }

        $stmt = $db->prepare("
            SELECT COUNT(*)
            FROM device_units
            WHERE order_id = ? AND status IN ('reserved', 'sold')
        ");
        $stmt->execute([$orderId]);
        $assigned = (int) $stmt->fetchColumn();

        return [
            'required' => $required,
            'assigned' => $assigned,
            'missing' => max(0, $required - $assigned),
        ];
    }

    private static function pickAvailable(\PDO $db, int $productId, ?int $variantId, ?int $storeId): ?array
    {
        $sql = "SELECT * FROM device_units WHERE product_id = ? AND status = 'available'";
        $params = [$productId];

        if ($variantId) {
            $sql .= ' AND (variant_id = ? OR variant_id IS NULL)';
            $params[] = $variantId;
        }
        if ($storeId) {
            $sql .= ' AND (store_id = ? OR store_id IS NULL)';
            $params[] = $storeId;
        }

        $sql .= ' ORDER BY (store_id IS NULL) ASC, id ASC LIMIT 1';
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    private static function generateFallbackImei(\PDO $db): string
    {
        do {
            $imei = '86' . str_pad((string) random_int(0, 9999999999999), 13, '0', STR_PAD_LEFT);
            $check = $db->prepare('SELECT COUNT(*) FROM warranties WHERE imei = ?');
            $check->execute([$imei]);
            $wExists = (int) $check->fetchColumn() > 0;
            $check2 = $db->prepare('SELECT COUNT(*) FROM device_units WHERE imei = ?');
            $check2->execute([$imei]);
            $dExists = (int) $check2->fetchColumn() > 0;
        } while ($wExists || $dExists);

        return $imei;
    }

    /**
     * Tự sinh IMEI available cho mọi sản phẩm theo tồn kho (stock).
     * Chỉ bổ sung phần thiếu — không đụng máy reserved/sold.
     *
     * @return array{products:int, created:int, skipped:int}
     */
    public static function seedImeisForAllProducts(?int $storeId = null, int $maxPerProduct = 200): array
    {
        $db = DB::connection();

        if ($storeId === null) {
            try {
                $storeId = (int) $db->query("SELECT id FROM stores WHERE status = 'active' ORDER BY id ASC LIMIT 1")->fetchColumn();
                if ($storeId <= 0) {
                    $storeId = null;
                }
            } catch (\Throwable) {
                $storeId = null;
            }
        }

        $products = $db->query('SELECT id, name, stock FROM products ORDER BY id ASC')->fetchAll(\PDO::FETCH_ASSOC);
        $countAvail = $db->prepare(
            "SELECT COUNT(*) FROM device_units WHERE product_id = ? AND status = 'available'"
        );
        $insert = $db->prepare('
            INSERT INTO device_units (imei, product_id, variant_id, store_id, status, note)
            VALUES (?, ?, NULL, ?, \'available\', ?)
        ');

        $created = 0;
        $skipped = 0;
        $touched = 0;

        foreach ($products as $product) {
            $productId = (int) $product['id'];
            $target = min(max(0, (int) $product['stock']), $maxPerProduct);
            if ($target <= 0) {
                $skipped++;
                continue;
            }

            $countAvail->execute([$productId]);
            $have = (int) $countAvail->fetchColumn();
            $need = $target - $have;
            if ($need <= 0) {
                $skipped++;
                continue;
            }

            $touched++;
            for ($i = 0; $i < $need; $i++) {
                $imei = self::generateFallbackImei($db);
                $insert->execute([
                    $imei,
                    $productId,
                    $storeId,
                    'Auto-seed theo tồn kho',
                ]);
                $created++;
            }
        }

        return [
            'products' => $touched,
            'created' => $created,
            'skipped' => $skipped,
        ];
    }

    private static function generateWarrantyCode(\PDO $db): string
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        do {
            $wCode = 'WAR-';
            for ($i = 0; $i < 8; $i++) {
                $wCode .= $chars[random_int(0, strlen($chars) - 1)];
            }
            $check = $db->prepare('SELECT COUNT(*) FROM warranties WHERE warranty_code = ?');
            $check->execute([$wCode]);
        } while ((int) $check->fetchColumn() > 0);

        return $wCode;
    }
}
