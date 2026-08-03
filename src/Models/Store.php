<?php

namespace App\Models;

use App\DB;
use PDO;

class Store
{
    private PDO $db;

    public function __construct()
    {
        $this->db = DB::connection();
    }

    public function all(): array
    {
        try {
            $stmt = $this->db->query('SELECT * FROM stores ORDER BY status ASC, id ASC');

            return array_map([$this, 'cast'], $stmt->fetchAll());
        } catch (\Throwable) {
            return [];
        }
    }

    public function active(): array
    {
        try {
            $stmt = $this->db->query("SELECT * FROM stores WHERE status = 'active' ORDER BY id ASC");

            return array_map([$this, 'cast'], $stmt->fetchAll());
        } catch (\Throwable) {
            return [];
        }
    }

    public function find(int $id): ?array
    {
        try {
            $stmt = $this->db->prepare('SELECT * FROM stores WHERE id = ? LIMIT 1');
            $stmt->execute([$id]);
            $row = $stmt->fetch();

            return $row ? $this->cast($row) : null;
        } catch (\Throwable) {
            return null;
        }
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare('
            INSERT INTO stores (name, address, lat, lng, phone, opening_hours, service_radius_km, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ');
        $stmt->execute([
            trim($data['name']),
            trim($data['address']),
            (float) $data['lat'],
            (float) $data['lng'],
            $data['phone'] ?? null,
            $data['opening_hours'] ?? null,
            (float) ($data['service_radius_km'] ?? 12),
            ($data['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active',
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare('
            UPDATE stores SET name = ?, address = ?, lat = ?, lng = ?, phone = ?,
                opening_hours = ?, service_radius_km = ?, status = ?
            WHERE id = ?
        ');

        return $stmt->execute([
            trim($data['name']),
            trim($data['address']),
            (float) $data['lat'],
            (float) $data['lng'],
            $data['phone'] ?? null,
            $data['opening_hours'] ?? null,
            (float) ($data['service_radius_km'] ?? 12),
            ($data['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active',
            $id,
        ]);
    }

    private function cast(array $row): array
    {
        $row['id'] = (int) $row['id'];
        $row['lat'] = (float) $row['lat'];
        $row['lng'] = (float) $row['lng'];
        $row['service_radius_km'] = (float) ($row['service_radius_km'] ?? 10);

        return $row;
    }
}
