<?php

namespace App\Models;

use App\DB;
use PDO;

class UserVoucher
{
    private PDO $db;

    public function __construct()
    {
        $this->db = DB::connection();
    }

    public function findByCode(string $code): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM user_vouchers WHERE code = ? LIMIT 1');
        $stmt->execute([strtoupper(trim($code))]);
        $row = $stmt->fetch();

        return $row ? $this->cast($row) : null;
    }

    public function activeForUser(int $userId): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM user_vouchers
             WHERE user_id = ? AND status = 'active' AND expires_at >= CURDATE()
             ORDER BY expires_at ASC"
        );
        $stmt->execute([$userId]);

        return array_map([$this, 'cast'], $stmt->fetchAll());
    }

    public function create(array $data): array
    {
        $code = $data['code'] ?? self::generateCode($data['source_order_id'] ?? null);
        $stmt = $this->db->prepare(
            'INSERT INTO user_vouchers
             (user_id, code, discount_percent, max_discount, expires_at, status, source_order_id, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            (int) $data['user_id'],
            strtoupper($code),
            (int) ($data['discount_percent'] ?? 10),
            isset($data['max_discount']) ? (int) $data['max_discount'] : null,
            $data['expires_at'] ?? date('Y-m-d', strtotime('+30 days')),
            $data['status'] ?? 'active',
            $data['source_order_id'] ?? null,
            date('Y-m-d H:i:s'),
        ]);

        return $this->findByCode($code);
    }

    public function markUsed(int $id, int $orderId): void
    {
        $stmt = $this->db->prepare(
            "UPDATE user_vouchers SET status = 'used', used_order_id = ? WHERE id = ?"
        );
        $stmt->execute([$orderId, $id]);
    }

    public static function generateCode(?int $orderId = null): string
    {
        $suffix = strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));
        $prefix = $orderId ? 'RF' . $orderId : 'HV';

        return $prefix . '-' . $suffix;
    }

    private function cast(array $row): array
    {
        $row['id'] = (int) $row['id'];
        $row['user_id'] = (int) $row['user_id'];
        $row['discount_percent'] = (int) $row['discount_percent'];
        $row['max_discount'] = isset($row['max_discount']) ? (int) $row['max_discount'] : null;

        return $row;
    }
}
