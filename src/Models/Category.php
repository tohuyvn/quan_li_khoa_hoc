<?php

namespace App\Models;

use App\DB;
use PDO;

class Category
{
    private PDO $db;

    public function __construct()
    {
        $this->db = DB::connection();
    }

    public function all(): array
    {
        $stmt = $this->db->query('SELECT * FROM categories ORDER BY id ASC');

        return array_map([$this, 'cast'], $stmt->fetchAll());
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM categories WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        return $row ? $this->cast($row) : null;
    }

    public function create(array $data): array
    {
        $stmt = $this->db->prepare(
            'INSERT INTO categories (name, description, status, created_at) VALUES (?, ?, ?, ?)'
        );
        $createdAt = date('Y-m-d H:i:s');
        $stmt->execute([
            trim($data['name']),
            trim($data['description'] ?? ''),
            $data['status'] ?? 'active',
            $createdAt,
        ]);

        return $this->find((int) $this->db->lastInsertId());
    }

    public function update(int $id, array $data): ?array
    {
        $stmt = $this->db->prepare(
            'UPDATE categories SET name = ?, description = ?, status = ? WHERE id = ?'
        );
        $stmt->execute([
            trim($data['name']),
            trim($data['description'] ?? ''),
            $data['status'] ?? 'active',
            $id,
        ]);

        return $this->find($id);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM categories WHERE id = ?');

        return $stmt->execute([$id]);
    }

    private function cast(array $row): array
    {
        $row['id'] = (int) $row['id'];

        return $row;
    }
}
