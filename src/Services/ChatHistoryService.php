<?php

namespace App\Services;

use App\DB;

class ChatHistoryService
{
    public static function sessionKey(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['chatbot_session_key'])) {
            $_SESSION['chatbot_session_key'] = bin2hex(random_bytes(16));
        }

        return (string) $_SESSION['chatbot_session_key'];
    }

    public static function clear(?int $userId, string $sessionKey): void
    {
        $db = DB::connection();
        if ($userId) {
            $stmt = $db->prepare('DELETE FROM chatbot_messages WHERE user_id = ? OR session_key = ?');
            $stmt->execute([$userId, $sessionKey]);
            return;
        }
        $stmt = $db->prepare('DELETE FROM chatbot_messages WHERE session_key = ?');
        $stmt->execute([$sessionKey]);
    }

    public static function append(?int $userId, string $sessionKey, string $role, string $content, ?string $source = null): void
    {
        $db = DB::connection();
        $stmt = $db->prepare('
            INSERT INTO chatbot_messages (user_id, session_key, role, content, source)
            VALUES (?, ?, ?, ?, ?)
        ');
        $stmt->execute([
            $userId,
            $sessionKey,
            $role === 'model' ? 'model' : 'user',
            mb_substr(strip_tags($content), 0, 2000),
            $source,
        ]);
    }

    public static function load(?int $userId, string $sessionKey, int $limit = 40): array
    {
        $db = DB::connection();
        if ($userId) {
            $stmt = $db->prepare('
                SELECT role, content AS text FROM chatbot_messages
                WHERE user_id = ? OR session_key = ?
                ORDER BY id DESC
                LIMIT ' . (int) $limit
            );
            $stmt->execute([$userId, $sessionKey]);
        } else {
            $stmt = $db->prepare('
                SELECT role, content AS text FROM chatbot_messages
                WHERE session_key = ?
                ORDER BY id DESC
                LIMIT ' . (int) $limit
            );
            $stmt->execute([$sessionKey]);
        }

        return array_reverse($stmt->fetchAll(\PDO::FETCH_ASSOC) ?: []);
    }
}
