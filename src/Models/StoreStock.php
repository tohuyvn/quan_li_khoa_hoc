<?php

namespace App\Models;

use App\DB;
use PDO;

class StoreStock
{
    private PDO $db;

    public function __construct()
    {
        $this->db = DB::connection();
    }

    public function quantity(int $storeId, int $productId, int $variantId = 0): int
    {
        $stmt = $this->db->prepare('
            SELECT quantity FROM store_stock
            WHERE store_id = ? AND product_id = ? AND variant_id = ?
            LIMIT 1
        ');
        $stmt->execute([$storeId, $productId, $variantId]);
        $qty = $stmt->fetchColumn();

        return $qty === false ? 0 : (int) $qty;
    }

    public function canFulfill(int $storeId, array $cartItems): bool
    {
        foreach ($cartItems as $item) {
            $productId = (int) ($item['product_id'] ?? $item['id'] ?? 0);
            $variantId = (int) ($item['variant_id'] ?? 0);
            $need = (int) ($item['quantity'] ?? 1);
            if ($productId <= 0 || $need <= 0) {
                continue;
            }
            if ($this->quantity($storeId, $productId, $variantId) < $need) {
                // Fallback: product-level stock (variant_id = 0)
                if ($variantId > 0 && $this->quantity($storeId, $productId, 0) >= $need) {
                    continue;
                }
                return false;
            }
        }

        return true;
    }

    public function upsert(int $storeId, int $productId, int $quantity, int $variantId = 0): bool
    {
        $stmt = $this->db->prepare('
            INSERT INTO store_stock (store_id, product_id, variant_id, quantity)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE quantity = VALUES(quantity)
        ');

        return $stmt->execute([$storeId, $productId, $variantId, max(0, $quantity)]);
    }

    public function decrement(int $storeId, int $productId, int $qty, int $variantId = 0): bool
    {
        $stmt = $this->db->prepare('
            UPDATE store_stock
            SET quantity = GREATEST(0, CAST(quantity AS SIGNED) - ?)
            WHERE store_id = ? AND product_id = ? AND variant_id = ?
        ');

        return $stmt->execute([$qty, $storeId, $productId, $variantId]);
    }

    public function forStore(int $storeId): array
    {
        $stmt = $this->db->prepare('
            SELECT ss.*, p.name AS product_name, p.stock AS global_stock
            FROM store_stock ss
            JOIN products p ON p.id = ss.product_id
            WHERE ss.store_id = ?
            ORDER BY p.name ASC
        ');
        $stmt->execute([$storeId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
