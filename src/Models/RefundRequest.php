<?php

namespace App\Models;

use App\DB;
use PDO;

class RefundRequest
{
    private PDO $db;

    public function __construct()
    {
        $this->db = DB::connection();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM refund_requests WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        return $row ? $this->cast($row) : null;
    }

    public function findPendingByOrder(int $orderId): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM refund_requests WHERE order_id = ? AND status = 'pending' LIMIT 1"
        );
        $stmt->execute([$orderId]);
        $row = $stmt->fetch();

        return $row ? $this->cast($row) : null;
    }

    public function create(array $data): array
    {
        $stmt = $this->db->prepare(
            'INSERT INTO refund_requests (order_id, user_id, method, amount, status, created_at)
             VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            (int) $data['order_id'],
            $data['user_id'] ?? null,
            $data['method'] ?? 'wallet',
            (int) $data['amount'],
            'pending',
            date('Y-m-d H:i:s'),
        ]);

        return $this->find((int) $this->db->lastInsertId());
    }

    public function updateStatus(int $id, string $status, ?string $adminNote = null): ?array
    {
        $stmt = $this->db->prepare(
            'UPDATE refund_requests SET status = ?, admin_note = ?, processed_at = ? WHERE id = ?'
        );
        $stmt->execute([$status, $adminNote, date('Y-m-d H:i:s'), $id]);

        return $this->find($id);
    }

    public function pendingList(): array
    {
        $stmt = $this->db->query(
            "SELECT rr.*, o.order_code, o.customer_name, o.email
             FROM refund_requests rr
             INNER JOIN orders o ON o.id = rr.order_id
             WHERE rr.status = 'pending'
             ORDER BY rr.created_at ASC"
        );

        return array_map([$this, 'cast'], $stmt->fetchAll());
    }

    public function forOrder(int $orderId): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM refund_requests WHERE order_id = ? ORDER BY created_at DESC'
        );
        $stmt->execute([$orderId]);

        return array_map([$this, 'cast'], $stmt->fetchAll());
    }

    private function cast(array $row): array
    {
        $row['id'] = (int) $row['id'];
        $row['order_id'] = (int) $row['order_id'];
        $row['user_id'] = isset($row['user_id']) ? (int) $row['user_id'] : null;
        $row['amount'] = (int) $row['amount'];

        return $row;
    }
}
