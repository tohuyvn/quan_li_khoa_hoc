<?php

namespace App\Models;

use App\DB;
use PDO;

class SitePromotion
{
    private PDO $db;

    public function __construct()
    {
        $this->db = DB::connection();
    }

    public function all(): array
    {
        $stmt = $this->db->query('SELECT * FROM site_promotions ORDER BY created_at DESC');

        return array_map([$this, 'cast'], $stmt->fetchAll());
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM site_promotions WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        return $row ? $this->cast($row) : null;
    }

    public function getActive(): ?array
    {
        $today = date('Y-m-d');
        $stmt = $this->db->prepare(
            "SELECT * FROM site_promotions
             WHERE status = 'active'
               AND expires_at >= ?
               AND (starts_at IS NULL OR starts_at <= ?)
             ORDER BY discount_percent DESC, id DESC
             LIMIT 1"
        );
        $stmt->execute([$today, $today]);
        $row = $stmt->fetch();

        return $row ? $this->cast($row) : null;
    }

    public function create(array $data): array
    {
        $stmt = $this->db->prepare(
            'INSERT INTO site_promotions (name, discount_percent, status, starts_at, expires_at, created_at)
             VALUES (?, ?, ?, ?, ?, ?)'
        );
        $createdAt = date('Y-m-d H:i:s');
        $startsAt = ($data['starts_at'] ?? '') !== '' ? $data['starts_at'] : null;
        $stmt->execute([
            trim($data['name']),
            (int) $data['discount_percent'],
            $data['status'] ?? 'inactive',
            $startsAt,
            $data['expires_at'],
            $createdAt,
        ]);

        return $this->find((int) $this->db->lastInsertId());
    }

    public function update(int $id, array $data): ?array
    {
        $startsAt = ($data['starts_at'] ?? '') !== '' ? $data['starts_at'] : null;
        $stmt = $this->db->prepare(
            'UPDATE site_promotions
             SET name = ?, discount_percent = ?, status = ?, starts_at = ?, expires_at = ?
             WHERE id = ?'
        );
        $stmt->execute([
            trim($data['name']),
            (int) $data['discount_percent'],
            $data['status'] ?? 'inactive',
            $startsAt,
            $data['expires_at'],
            $id,
        ]);

        return $this->find($id);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM site_promotions WHERE id = ?');

        return $stmt->execute([$id]);
    }

    private function cast(array $row): array
    {
        $row['id'] = (int) $row['id'];
        $row['discount_percent'] = (int) $row['discount_percent'];

        return $row;
    }
}
