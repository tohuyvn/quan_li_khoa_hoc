<?php

namespace App\Models;

use App\DB;
use PDO;

class User
{
    private PDO $db;

    public function __construct()
    {
        $this->db = DB::connection();
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([trim($email)]);
        $row = $stmt->fetch();

        return $row ? $this->cast($row) : null;
    }

    public function findByPhone(string $phone): ?array
    {
        $phone = preg_replace('/\s+/', '', trim($phone));
        if ($phone === '') {
            return null;
        }

        $stmt = $this->db->prepare('SELECT * FROM users WHERE phone = ? LIMIT 1');
        $stmt->execute([$phone]);
        $row = $stmt->fetch();

        return $row ? $this->cast($row) : null;
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        return $row ? $this->cast($row) : null;
    }

    public function create(array $data): array
    {
        $stmt = $this->db->prepare(
            'INSERT INTO users (name, email, phone, password, role, created_at) VALUES (?, ?, ?, ?, ?, ?)'
        );
        $createdAt = date('Y-m-d H:i:s');
        $stmt->execute([
            trim($data['name']),
            trim($data['email']),
            trim($data['phone'] ?? ''),
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['role'] ?? 'customer',
            $createdAt,
        ]);

        return $this->find((int) $this->db->lastInsertId());
    }

    public function verify(string $email, string $password): ?array
    {
        $user = $this->findByEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }

        return null;
    }

    public function all(): array
    {
        $stmt = $this->db->query(
            'SELECT id, name, email, phone, role, created_at FROM users ORDER BY id DESC'
        );

        return array_map([$this, 'cast'], $stmt->fetchAll());
    }

    public function update(int $id, array $data): bool
    {
        $user = $this->find($id);
        if (!$user) {
            return false;
        }

        $fields = ['name = ?', 'email = ?', 'phone = ?'];
        $params = [
            trim($data['name']),
            strtolower(trim($data['email'])),
            trim($data['phone'] ?? ''),
        ];

        $role = $data['role'] ?? $user['role'];
        if ($user['role'] !== 'admin') {
            $fields[] = 'role = ?';
            $params[] = in_array($role, ['customer', 'admin'], true) ? $role : 'customer';
        }

        if (!empty($data['password'])) {
            $fields[] = 'password = ?';
            $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        if (array_key_exists('two_factor_secret', $data)) {
            $fields[] = 'two_factor_secret = ?';
            $params[] = $data['two_factor_secret'];
        }
        if (array_key_exists('two_factor_enabled', $data)) {
            $fields[] = 'two_factor_enabled = ?';
            $params[] = !empty($data['two_factor_enabled']) ? 1 : 0;
        }

        $params[] = $id;
        $sql = 'UPDATE users SET ' . implode(', ', $fields) . ' WHERE id = ?';
        $stmt = $this->db->prepare($sql);

        return $stmt->execute($params);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'");

        return $stmt->execute([$id]);
    }

    private function cast(array $row): array
    {
        $row['id'] = (int) $row['id'];

        return $row;
    }
}
