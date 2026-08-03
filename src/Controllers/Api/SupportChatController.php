<?php

namespace App\Controllers\Api;

use App\DB;

/**
 * SupportChatController – Xử lý tin nhắn hỗ trợ từ client.
 */
class SupportChatController
{
    private function sessionId(): string
    {
        if (empty($_SESSION['support_session_id'])) {
            $_SESSION['support_session_id'] = bin2hex(random_bytes(16));
        }
        return $_SESSION['support_session_id'];
    }

    /** GET /api/support/messages – Lấy tin nhắn (hỗ trợ ?after=id để poll incremental) */
    public function getMessages(): void
    {
        $sid     = $this->sessionId();
        $afterId = (int) ($_GET['after'] ?? 0);
        $db      = DB::connection();

        if ($afterId > 0) {
            $stmt = $db->prepare(
                'SELECT id, sender, message, user_name, created_at
                 FROM support_messages
                 WHERE session_id = ? AND id > ?
                 ORDER BY id ASC'
            );
            $stmt->execute([$sid, $afterId]);
        } else {
            $stmt = $db->prepare(
                'SELECT id, sender, message, user_name, created_at
                 FROM support_messages
                 WHERE session_id = ?
                 ORDER BY id ASC'
            );
            $stmt->execute([$sid]);
        }

        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if (!empty($rows)) {
            $db->prepare('UPDATE support_messages SET is_read=1 WHERE session_id=? AND sender="admin"')
               ->execute([$sid]);
        }

        $this->json(['success' => true, 'messages' => $rows]);
    }

    /** POST /api/support/send – Gửi tin nhắn từ user */
    public function sendMessage(): void
    {
        $sid     = $this->sessionId();
        $message = trim($_POST['message'] ?? '');
        $name    = trim($_POST['name'] ?? (authUser()['name'] ?? 'Khách'));
        $email   = trim($_POST['email'] ?? (authUser()['email'] ?? ''));

        if ($message === '') {
            $this->json(['success' => false, 'error' => 'Tin nhắn trống.'], 422);
            return;
        }

        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $limitKey = 'chat_send:' . $sid . '_' . $ip;
        if (!\App\Services\RateLimiter::check($limitKey, 10, 10)) {
            $this->json(['success' => false, 'error' => 'Bạn đang gửi tin nhắn quá nhanh. Vui lòng thử lại sau.'], 429);
            return;
        }

        $db = DB::connection();
        $db->prepare(
            'INSERT INTO support_messages (session_id, sender, user_name, user_email, message)
             VALUES (?, "user", ?, ?, ?)'
        )->execute([$sid, $name, $email, $message]);

        // Lưu tên/email vào session để dùng lại
        $_SESSION['support_user_name']  = $name;
        $_SESSION['support_user_email'] = $email;

        $this->json(['success' => true, 'id' => $db->lastInsertId()]);
    }

    /** GET /api/support/unread – Đếm tin nhắn admin chưa đọc (để hiển thị badge) */
    public function unreadCount(): void
    {
        $sid  = $this->sessionId();
        $db   = DB::connection();
        $stmt = $db->prepare(
            'SELECT COUNT(*) FROM support_messages WHERE session_id=? AND sender="admin" AND is_read=0'
        );
        $stmt->execute([$sid]);
        $this->json(['count' => (int)$stmt->fetchColumn()]);
    }

    /** GET /api/support/sse – Server-Sent Events stream for user */
    public function sse(): void
    {
        set_time_limit(0);
        
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('X-Accel-Buffering: no');
        
        $sid = $this->sessionId();
        $db = DB::connection();
        
        $this->updateUserActivity($sid);
        
        session_write_close();
        
        $lastId = (int)($_GET['last_id'] ?? 0);
        $lastIsAdminTyping = null;
        $lastAdminOnline = null;
        
        while (ob_get_level()) {
            ob_end_flush();
        }
        flush();
        
        $loopCount = 0;
        
        while (true) {
            if (connection_aborted()) {
                break;
            }
            
            if ($loopCount % 4 === 0) {
                $this->updateUserActivity($sid);
            }
            
            // 1. Fetch new messages
            $stmt = $db->prepare('SELECT id, sender, message, user_name, created_at FROM support_messages WHERE session_id = ? AND id > ? ORDER BY id ASC');
            $stmt->execute([$sid, $lastId]);
            $messages = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            if (!empty($messages)) {
                $db->prepare('UPDATE support_messages SET is_read=1 WHERE session_id=? AND sender="admin"')
                   ->execute([$sid]);
                   
                foreach ($messages as $msg) {
                    echo "event: message\n";
                    echo "data: " . json_encode($msg, JSON_UNESCAPED_UNICODE) . "\n\n";
                    $lastId = max($lastId, (int)$msg['id']);
                }
                flush();
            }
            
            // 2. Fetch typing & online status of admin
            $stmt = $db->prepare('SELECT is_admin_typing, admin_last_activity FROM chat_status WHERE session_id = ?');
            $stmt->execute([$sid]);
            $status = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if ($status) {
                $isAdminTyping = (int)$status['is_admin_typing'];
                $adminLastActivity = $status['admin_last_activity'];
                
                $isAdminOnline = 0;
                if ($adminLastActivity) {
                    $isAdminOnline = (time() - strtotime($adminLastActivity)) < 10 ? 1 : 0;
                }
                
                if ($isAdminTyping !== $lastIsAdminTyping) {
                    echo "event: typing\n";
                    echo "data: " . json_encode(['typing' => $isAdminTyping]) . "\n\n";
                    $lastIsAdminTyping = $isAdminTyping;
                    flush();
                }
                
                if ($isAdminOnline !== $lastAdminOnline) {
                    echo "event: status\n";
                    echo "data: " . json_encode(['online' => $isAdminOnline]) . "\n\n";
                    $lastAdminOnline = $isAdminOnline;
                    flush();
                }
            } else {
                $db->prepare('INSERT IGNORE INTO chat_status (session_id) VALUES (?)')->execute([$sid]);
            }
            
            echo ": ping\n\n";
            flush();
            
            sleep(1);
            $loopCount++;
        }
    }

    private function updateUserActivity(string $sid): void
    {
        $db = DB::connection();
        $db->prepare('
            INSERT INTO chat_status (session_id, user_last_activity)
            VALUES (?, NOW())
            ON DUPLICATE KEY UPDATE user_last_activity = NOW()
        ')->execute([$sid]);
    }

    /** POST /api/support/typing – User reports typing status */
    public function typing(): void
    {
        $sid = $this->sessionId();
        $typing = !empty($_POST['typing']) ? 1 : 0;
        
        $db = DB::connection();
        $db->prepare('
            INSERT INTO chat_status (session_id, is_user_typing, user_last_activity)
            VALUES (?, ?, NOW())
            ON DUPLICATE KEY UPDATE is_user_typing = ?, user_last_activity = NOW()
        ')->execute([$sid, $typing, $typing]);
        
        $this->json(['success' => true]);
    }

    private function json(array $data, int $code = 200): void
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
