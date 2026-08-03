<?php

namespace App\Models;

use App\DB;
use PDO;

class DeviceUnit
{
    private PDO $db;

    public function __construct()
    {
        $this->db = DB::connection();
    }

    public function list(array $filters = [], int $page = 1, int $perPage = 30): array
    {
        $where = ['1=1'];
        $params = [];

        if (!empty($filters['status'])) {
            $where[] = 'd.status = ?';
            $params[] = $filters['status'];
        }
        if (!empty($filters['product_id'])) {
            $where[] = 'd.product_id = ?';
            $params[] = (int) $filters['product_id'];
        }
        if (!empty($filters['store_id'])) {
            $where[] = 'd.store_id = ?';
            $params[] = (int) $filters['store_id'];
        }
        if (!empty($filters['q'])) {
            $where[] = '(d.imei LIKE ? OR p.name LIKE ?)';
            $like = '%' . trim($filters['q']) . '%';
            $params[] = $like;
            $params[] = $like;
        }

        $whereSql = implode(' AND ', $where);
        $offset = max(0, ($page - 1) * $perPage);

        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM device_units d LEFT JOIN products p ON p.id = d.product_id WHERE {$whereSql}");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $sql = "SELECT d.*, p.name AS product_name, s.name AS store_name
                FROM device_units d
                LEFT JOIN products p ON p.id = d.product_id
                LEFT JOIN stores s ON s.id = d.store_id
                WHERE {$whereSql}
                ORDER BY d.id DESC
                LIMIT {$perPage} OFFSET {$offset}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return [
            'items' => $stmt->fetchAll(PDO::FETCH_ASSOC),
            'total' => $total,
            'page' => $page,
            'total_pages' => max(1, (int) ceil($total / $perPage)),
        ];
    }

    public function create(array $data): bool
    {
        $stmt = $this->db->prepare('
            INSERT INTO device_units (imei, product_id, variant_id, store_id, status, note)
            VALUES (?, ?, ?, ?, ?, ?)
        ');

        return $stmt->execute([
            trim($data['imei']),
            (int) $data['product_id'],
            !empty($data['variant_id']) ? (int) $data['variant_id'] : null,
            !empty($data['store_id']) ? (int) $data['store_id'] : null,
            $data['status'] ?? 'available',
            $data['note'] ?? null,
        ]);
    }

    public function findByImei(string $imei): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM device_units WHERE imei = ? LIMIT 1');
        $stmt->execute([trim($imei)]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM device_units WHERE id = ? AND status = 'available'");

        return $stmt->execute([$id]);
    }
}
