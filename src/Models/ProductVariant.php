<?php

namespace App\Models;

use App\DB;
use PDO;

class ProductVariant
{
    private PDO $db;

    public function __construct()
    {
        $this->db = DB::connection();
    }

    public function getForProduct(int $productId, bool $includeInactive = false): array
    {
        $sql = "SELECT * FROM product_variants WHERE product_id = ?";
        if (!$includeInactive) {
            $sql .= " AND status = 'active'";
        }
        $sql .= " ORDER BY storage ASC, color ASC, id ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$productId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM product_variants WHERE id = ?");
        $stmt->execute([$id]);
        $variant = $stmt->fetch(PDO::FETCH_ASSOC);
        return $variant ?: null;
    }

    public function create(array $data): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO product_variants (product_id, color, storage, price, stock, image, status)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            (int) $data['product_id'],
            trim($data['color']),
            trim($data['storage']),
            (int) $data['price'],
            (int) $data['stock'],
            trim($data['image'] ?? ''),
            $data['status'] ?? 'active'
        ]);
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE product_variants 
            SET color = ?, storage = ?, price = ?, stock = ?, image = ?, status = ?
            WHERE id = ?
        ");
        return $stmt->execute([
            trim($data['color']),
            trim($data['storage']),
            (int) $data['price'],
            (int) $data['stock'],
            trim($data['image'] ?? ''),
            $data['status'] ?? 'active',
            $id
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM product_variants WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function adjustStock(int $id, int $quantity): bool
    {
        $stmt = $this->db->prepare("UPDATE product_variants SET stock = stock + ? WHERE id = ?");
        return $stmt->execute([$quantity, $id]);
    }
}
