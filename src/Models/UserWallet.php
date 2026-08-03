<?php

namespace App\Models;

use App\DB;
use PDO;

class UserWallet
{
    private PDO $db;

    public function __construct()
    {
        $this->db = DB::connection();
    }

    public function getOrCreate(int $userId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM user_wallets WHERE user_id = ? LIMIT 1');
        $stmt->execute([$userId]);
        $row = $stmt->fetch();

        if ($row) {
            $row['balance'] = (int) $row['balance'];
            return $row;
        }

        $this->db->prepare('INSERT INTO user_wallets (user_id, balance, updated_at) VALUES (?, 0, ?)')
            ->execute([$userId, date('Y-m-d H:i:s')]);

        return ['user_id' => $userId, 'balance' => 0, 'updated_at' => date('Y-m-d H:i:s')];
    }

    public function balance(int $userId): int
    {
        return (int) ($this->getOrCreate($userId)['balance'] ?? 0);
    }
}
