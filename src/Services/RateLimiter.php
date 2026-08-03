<?php

namespace App\Services;

use App\DB;

class RateLimiter
{
    /**
     * Check if a request for a given key is allowed under the rate limit.
     * Increment hits if allowed.
     *
     * @param string $key Unique identifier for the rate limit (e.g. "otp_ip:127.0.0.1")
     * @param int $maxAttempts Maximum allowed requests in the time window
     * @param int $decaySeconds Duration of the rate limit window in seconds
     * @return bool True if allowed, false if rate limit exceeded
     */
    public static function check(string $key, int $maxAttempts, int $decaySeconds): bool
    {
        try {
            $db = DB::connection();
            $now = time();

            // 1. Delete expired limits to save DB space
            $stmtDel = $db->prepare('DELETE FROM rate_limits WHERE expires_at < ?');
            $stmtDel->execute([$now]);

            // 2. Fetch current record
            $stmtSelect = $db->prepare('SELECT hits, expires_at FROM rate_limits WHERE `key` = ?');
            $stmtSelect->execute([$key]);
            $record = $stmtSelect->fetch(\PDO::FETCH_ASSOC);

            if (!$record) {
                // If it doesn't exist, create it with 1 hit and future expiration
                $stmtInsert = $db->prepare('INSERT INTO rate_limits (`key`, hits, expires_at) VALUES (?, 1, ?)');
                $stmtInsert->execute([$key, $now + $decaySeconds]);
                return true;
            }

            if ((int)$record['hits'] >= $maxAttempts) {
                return false;
            }

            // 3. Otherwise, increment hits
            $stmtUpdate = $db->prepare('UPDATE rate_limits SET hits = hits + 1 WHERE `key` = ?');
            $stmtUpdate->execute([$key]);
            return true;
        } catch (\Throwable $e) {
            // Fail open in case of database issues so we don't break the application
            return true;
        }
    }

    /**
     * Get the remaining seconds until the rate limit for the key resets.
     *
     * @param string $key
     * @return int
     */
    public static function remainingSeconds(string $key): int
    {
        try {
            $db = DB::connection();
            $stmt = $db->prepare('SELECT expires_at FROM rate_limits WHERE `key` = ?');
            $stmt->execute([$key]);
            $expiresAt = (int)$stmt->fetchColumn();

            if (!$expiresAt) {
                return 0;
            }

            return max(0, $expiresAt - time());
        } catch (\Throwable $e) {
            return 0;
        }
    }
}
