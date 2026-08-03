<?php

namespace App\Models;

use App\DB;
use App\PriceCalculator;
use PDO;

class Wishlist
{
    private PDO $db;

    public function __construct()
    {
        $this->db = DB::connection();
    }

    public function getByUser(int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT w.id AS wishlist_id, w.user_id, w.product_id, w.created_at AS wishlisted_at,
                    p.*, c.name AS category_name
             FROM wishlists w
             JOIN products p ON p.id = w.product_id
             LEFT JOIN categories c ON c.id = p.category_id
             WHERE w.user_id = ? AND p.status = "active"
             ORDER BY w.created_at DESC'
        );
        $stmt->execute([$userId]);
        $items = PriceCalculator::resolveMany($stmt->fetchAll(PDO::FETCH_ASSOC));

        foreach ($items as &$item) {
            $item['product_id'] = (int) $item['product_id'];
            $item['id'] = (int) $item['product_id'];
        }
        unset($item);

        return $items;
    }

    public function getProductIds(int $userId): array
    {
        $stmt = $this->db->prepare('SELECT product_id FROM wishlists WHERE user_id = ?');
        $stmt->execute([$userId]);
        return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
    }

    public function has(int $userId, int $productId): bool
    {
        $stmt = $this->db->prepare('SELECT 1 FROM wishlists WHERE user_id = ? AND product_id = ?');
        $stmt->execute([$userId, $productId]);
        return (bool) $stmt->fetchColumn();
    }

    public function toggle(int $userId, int $productId): string
    {
        if ($this->has($userId, $productId)) {
            $stmt = $this->db->prepare('DELETE FROM wishlists WHERE user_id = ? AND product_id = ?');
            $stmt->execute([$userId, $productId]);
            return 'removed';
        }

        $stmt = $this->db->prepare(
            'INSERT IGNORE INTO wishlists (user_id, product_id, created_at) VALUES (?, ?, ?)'
        );
        $stmt->execute([$userId, $productId, date('Y-m-d H:i:s')]);
        return 'added';
    }

    public function count(int $userId): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM wishlists WHERE user_id = ?');
        $stmt->execute([$userId]);
        return (int) $stmt->fetchColumn();
    }

    public function countByUser(int $userId): int
    {
        return $this->count($userId);
    }

    public function remove(int $userId, int $productId): bool
    {
        $stmt = $this->db->prepare('DELETE FROM wishlists WHERE user_id = ? AND product_id = ?');
        return $stmt->execute([$userId, $productId]);
    }

    /** @param array<int, int|string> $productIds */
    public function sync(int $userId, array $productIds): int
    {
        $ids = array_values(array_unique(array_filter(
            array_map('intval', $productIds),
            static fn (int $id): bool => $id > 0
        )));
        if ($ids === []) {
            return $this->count($userId);
        }

        $stmt = $this->db->prepare(
            'INSERT IGNORE INTO wishlists (user_id, product_id, created_at)
             SELECT ?, p.id, ? FROM products p WHERE p.id = ? AND p.status = "active"'
        );
        $now = date('Y-m-d H:i:s');
        foreach ($ids as $productId) {
            $stmt->execute([$userId, $now, $productId]);
        }

        return $this->count($userId);
    }
}
