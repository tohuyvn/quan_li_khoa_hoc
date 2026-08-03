<?php

namespace App;

use App\Models\UserWallet;
use PDO;

class WalletService
{
    public static function balance(int $userId): int
    {
        return (new UserWallet())->balance($userId);
    }

    public static function credit(int $userId, int $amount, string $refType, ?int $refId, string $note = '', bool $ownTransaction = true): bool
    {
        if ($amount <= 0) {
            return false;
        }

        $db = DB::connection();
        if ($ownTransaction) {
            $db->beginTransaction();
        }

        try {
            $wallet = new UserWallet();
            $wallet->getOrCreate($userId);

            $db->prepare(
                'UPDATE user_wallets SET balance = balance + ?, updated_at = ? WHERE user_id = ?'
            )->execute([$amount, date('Y-m-d H:i:s'), $userId]);

            $db->prepare(
                'INSERT INTO wallet_transactions (user_id, type, amount, ref_type, ref_id, note, created_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?)'
            )->execute([$userId, 'credit', $amount, $refType, $refId, $note, date('Y-m-d H:i:s')]);

            if ($ownTransaction) {
                $db->commit();
            }

            return true;
        } catch (\Throwable $e) {
            if ($ownTransaction) {
                $db->rollBack();
            }
            throw $e;
        }
    }

    public static function debit(int $userId, int $amount, string $refType, ?int $refId, string $note = '', bool $ownTransaction = true): bool
    {
        if ($amount <= 0) {
            return false;
        }

        $db = DB::connection();
        if ($ownTransaction) {
            $db->beginTransaction();
        }

        try {
            $wallet = new UserWallet();
            if ($wallet->balance($userId) < $amount) {
                if ($ownTransaction) {
                    $db->rollBack();
                }
                return false;
            }

            $stmt = $db->prepare(
                'UPDATE user_wallets SET balance = balance - ?, updated_at = ? WHERE user_id = ? AND balance >= ?'
            );
            $stmt->execute([$amount, date('Y-m-d H:i:s'), $userId, $amount]);

            if ($stmt->rowCount() === 0) {
                if ($ownTransaction) {
                    $db->rollBack();
                }
                return false;
            }

            $db->prepare(
                'INSERT INTO wallet_transactions (user_id, type, amount, ref_type, ref_id, note, created_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?)'
            )->execute([$userId, 'debit', $amount, $refType, $refId, $note, date('Y-m-d H:i:s')]);

            if ($ownTransaction) {
                $db->commit();
            }

            return true;
        } catch (\Throwable $e) {
            if ($ownTransaction) {
                $db->rollBack();
            }
            throw $e;
        }
    }

    public static function transactions(int $userId, int $limit = 20): array
    {
        $db = DB::connection();
        $stmt = $db->prepare(
            'SELECT * FROM wallet_transactions WHERE user_id = ? ORDER BY created_at DESC LIMIT ?'
        );
        $stmt->bindValue(1, $userId, PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
