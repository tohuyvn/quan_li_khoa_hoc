<?php

namespace App\Models;

use App\DB;
use PDO;

class PasswordResetRequest
{
    public const TYPE_ADMIN_RESET = 'ADMIN_RESET';
    public const TYPE_FORGOT_PASSWORD = 'FORGOT_PASSWORD';

    public const STATUS_PENDING = 'PENDING';
    public const STATUS_USED = 'USED';
    public const STATUS_EXPIRED = 'EXPIRED';

    public const CREATED_BY_ADMIN = 'ADMIN';
    public const CREATED_BY_USER = 'USER';

    private PDO $db;

    public function __construct()
    {
        $this->db = DB::connection();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM password_reset_requests WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        return $row ? $this->cast($row) : null;
    }

    public function findByToken(string $token): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM password_reset_requests WHERE token = ? AND status = ? LIMIT 1'
        );
        $stmt->execute([$token, self::STATUS_PENDING]);
        $row = $stmt->fetch();

        return $row ? $this->cast($row) : null;
    }

    public function findPendingByUser(int $userId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM password_reset_requests
             WHERE user_id = ? AND status = ?
             ORDER BY id DESC LIMIT 1'
        );
        $stmt->execute([$userId, self::STATUS_PENDING]);
        $row = $stmt->fetch();

        return $row ? $this->cast($row) : null;
    }

    public function countRecentByUser(int $userId, int $hours = 1): int
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM password_reset_requests
             WHERE user_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL ? HOUR)'
        );
        $stmt->execute([$userId, $hours]);

        return (int) $stmt->fetchColumn();
    }

    public function expirePendingForUser(int $userId): void
    {
        $stmt = $this->db->prepare(
            'UPDATE password_reset_requests
             SET status = ?
             WHERE user_id = ? AND status = ?'
        );
        $stmt->execute([self::STATUS_EXPIRED, $userId, self::STATUS_PENDING]);
    }

    public function expireAllPending(): void
    {
        $this->db->exec(
            "UPDATE password_reset_requests SET status = '" . self::STATUS_EXPIRED . "'
             WHERE status = '" . self::STATUS_PENDING . "' AND expired_at < NOW()"
        );
    }

    public function create(array $data): array
    {
        $stmt = $this->db->prepare(
            'INSERT INTO password_reset_requests
             (user_id, request_type, otp_hash, token, status, expired_at, created_by, created_by_id)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['user_id'],
            $data['request_type'],
            $data['otp_hash'] ?? null,
            $data['token'] ?? null,
            self::STATUS_PENDING,
            $data['expired_at'],
            $data['created_by'],
            $data['created_by_id'] ?? null,
        ]);

        return $this->find((int) $this->db->lastInsertId());
    }

    public function markUsed(int $id): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE password_reset_requests SET status = ? WHERE id = ? AND status = ?'
        );

        return $stmt->execute([self::STATUS_USED, $id, self::STATUS_PENDING]);
    }

    public function incrementVerifyAttempts(int $id): void
    {
        $stmt = $this->db->prepare(
            'UPDATE password_reset_requests SET verify_attempts = verify_attempts + 1 WHERE id = ?'
        );
        $stmt->execute([$id]);
    }

    public function markExpired(int $id): void
    {
        $stmt = $this->db->prepare(
            'UPDATE password_reset_requests SET status = ? WHERE id = ?'
        );
        $stmt->execute([self::STATUS_EXPIRED, $id]);
    }

    public function isExpired(array $request): bool
    {
        return strtotime($request['expired_at']) < time();
    }

    private function cast(array $row): array
    {
        $row['id'] = (int) $row['id'];
        $row['user_id'] = (int) $row['user_id'];
        $row['verify_attempts'] = (int) $row['verify_attempts'];
        $row['created_by_id'] = $row['created_by_id'] !== null ? (int) $row['created_by_id'] : null;

        return $row;
    }
}
