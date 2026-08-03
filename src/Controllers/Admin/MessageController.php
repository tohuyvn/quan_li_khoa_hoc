<?php

namespace App\Controllers\Admin;

use App\DB;

/**
 * MessageController – Quản lý tin nhắn hỗ trợ (Hai cột) và đánh giá khách hàng.
 */
class MessageController
{
    /* ── Trang chính: danh sách cuộc hội thoại ── */
    public function index(): void
    {
        requireAdmin();
        $db = DB::connection();

        // Lấy danh sách session chat
        $stmt = $db->query(
            "SELECT
                session_id,
                MAX(user_name)  AS user_name,
                MAX(user_email) AS user_email,
                MAX(created_at) AS last_at,
                SUM(CASE WHEN sender='user' AND is_read=0 THEN 1 ELSE 0 END) AS unread
             FROM support_messages
             GROUP BY session_id
             ORDER BY last_at DESC"
        );
        $conversations = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        view('admin.messages.index', [
            'title'         => 'Hỗ trợ khách hàng trực tuyến',
            'conversations' => $conversations,
            'activeSessionId' => null,
            'messages'      => [],
            'userName'      => '',
            'userEmail'     => '',
            'recentOrder'   => null,
            'recentOrderItems' => [],
        ]);
    }

    /* ── Chi tiết cuộc hội thoại (Hiển thị cùng một layout 2 cột) ── */
    public function show(string $sessionId): void
    {
        requireAdmin();
        $db = DB::connection();

        // Lấy danh sách session chat
        $stmt = $db->query(
            "SELECT
                session_id,
                MAX(user_name)  AS user_name,
                MAX(user_email) AS user_email,
                MAX(created_at) AS last_at,
                SUM(CASE WHEN sender='user' AND is_read=0 THEN 1 ELSE 0 END) AS unread
             FROM support_messages
             GROUP BY session_id
             ORDER BY last_at DESC"
        );
        $conversations = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Lấy danh sách tin nhắn của session active
        $stmt2 = $db->prepare(
            'SELECT * FROM support_messages WHERE session_id=? ORDER BY id ASC'
        );
        $stmt2->execute([$sessionId]);
        $messages = $stmt2->fetchAll(\PDO::FETCH_ASSOC);

        // Đánh dấu user messages là đã đọc
        $db->prepare('UPDATE support_messages SET is_read=1 WHERE session_id=? AND sender="user"')
           ->execute([$sessionId]);

        $userName  = $messages[0]['user_name']  ?? 'Khách';
        $userEmail = $messages[0]['user_email'] ?? '';

        // Lấy đơn hàng gần nhất của khách hàng qua Email
        $recentOrder = null;
        $recentOrderItems = [];
        if (!empty($userEmail)) {
            $orderStmt = $db->prepare('SELECT * FROM orders WHERE email=? ORDER BY id DESC LIMIT 1');
            $orderStmt->execute([$userEmail]);
            $recentOrder = $orderStmt->fetch(\PDO::FETCH_ASSOC);
            if ($recentOrder) {
                $itemsStmt = $db->prepare('SELECT * FROM order_items WHERE order_id=?');
                $itemsStmt->execute([$recentOrder['id']]);
                $recentOrderItems = $itemsStmt->fetchAll(\PDO::FETCH_ASSOC);
            }
        }

        view('admin.messages.index', [
            'title'           => "Chat với {$userName}",
            'conversations'   => $conversations,
            'activeSessionId' => $sessionId,
            'messages'        => $messages,
            'userName'        => $userName,
            'userEmail'       => $userEmail,
            'recentOrder'     => $recentOrder,
            'recentOrderItems' => $recentOrderItems,
        ]);
    }

    /* ── Gửi tin nhắn từ admin (AJAX POST) ── */
    public function reply(string $sessionId): void
    {
        requireAdmin();
        $message = trim($_POST['message'] ?? '');
        if ($message === '') {
            echo json_encode(['success' => false, 'error' => 'Tin nhắn trống.']);
            exit;
        }

        $db = DB::connection();
        $db->prepare(
            'INSERT INTO support_messages (session_id, sender, user_name, user_email, message, is_read)
             VALUES (?, "admin", "Admin", "", ?, 0)'
        )->execute([$sessionId, $message]);

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'id' => $db->lastInsertId()]);
        exit;
    }

    /* ── Poll: Admin lấy tin nhắn mới từ session (AJAX) ── */
    public function poll(string $sessionId): void
    {
        requireAdmin();
        $afterId = (int)($_GET['after'] ?? 0);
        $db      = DB::connection();
        $stmt    = $db->prepare(
            'SELECT id, sender, message, user_name, created_at
             FROM support_messages
             WHERE session_id=? AND id>? ORDER BY id ASC'
        );
        $stmt->execute([$sessionId, $afterId]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Đánh dấu user messages đã đọc
        if (!empty($rows)) {
            $db->prepare('UPDATE support_messages SET is_read=1 WHERE session_id=? AND sender="user"')
               ->execute([$sessionId]);
        }

        header('Content-Type: application/json');
        echo json_encode(['messages' => $rows]);
        exit;
    }

    /* ── Xóa toàn bộ cuộc hội thoại ── */
    public function deleteConversation(string $sessionId): void
    {
        requireAdmin();
        DB::connection()
            ->prepare('DELETE FROM support_messages WHERE session_id=?')
            ->execute([$sessionId]);
        flash('success', 'Đã xóa cuộc hội thoại.');
        redirect('/admin/messages');
    }

    /* ── ĐÁNH GIÁ KHÁCH HÀNG ── */
    public function reviews(): void
    {
        requireAdmin();
        $db = DB::connection();

        $q      = trim($_GET['q'] ?? '');
        $rating = (int)($_GET['rating'] ?? 0);

        $sql    = 'SELECT r.*, p.name AS product_name
                   FROM product_reviews r
                   LEFT JOIN products p ON p.id = r.product_id
                   WHERE 1=1';
        $params = [];

        if ($q !== '') {
            $sql    .= ' AND (r.user_name LIKE ? OR r.comment LIKE ? OR p.name LIKE ?)';
            $like    = "%{$q}%";
            $params  = array_merge($params, [$like, $like, $like]);
        }
        if ($rating > 0) {
            $sql    .= ' AND r.rating = ?';
            $params[] = $rating;
        }

        $sql .= ' ORDER BY r.created_at DESC';

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $reviews = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        view('admin.messages.reviews', [
            'title'   => 'Đánh giá khách hàng',
            'reviews' => $reviews,
            'q'       => $q,
            'rating'  => $rating,
        ]);
    }

    public function deleteReview(int $id): void
    {
        requireAdmin();
        $db = DB::connection();
        
        $stmt = $db->prepare('SELECT * FROM product_reviews WHERE id=?');
        $stmt->execute([$id]);
        $review = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($review) {
            logAdminAction('review_deletion', "Review by '{$review['user_name']}' deleted for product ID: {$review['product_id']} (Comment: " . substr($review['comment'] ?? '', 0, 55) . ")");
        }

        $db->prepare('DELETE FROM product_reviews WHERE id=?')
            ->execute([$id]);
        flash('success', 'Đã xóa đánh giá.');
        redirect('/admin/messages/reviews');
    }

    /** GET /admin/messages/{sid}/sse – Server-Sent Events stream for admin */
    public function sse(string $sessionId): void
    {
        requireAdmin();
        set_time_limit(0);
        
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('X-Accel-Buffering: no');
        
        $db = DB::connection();
        
        $this->updateAdminActivity($sessionId);
        
        session_write_close();
        
        $lastId = (int)($_GET['last_id'] ?? 0);
        $lastIsUserTyping = null;
        $lastUserOnline = null;
        
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
                $this->updateAdminActivity($sessionId);
            }
            
            // 1. Fetch new messages
            $stmt = $db->prepare('SELECT id, sender, message, user_name, created_at FROM support_messages WHERE session_id = ? AND id > ? ORDER BY id ASC');
            $stmt->execute([$sessionId, $lastId]);
            $messages = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            if (!empty($messages)) {
                $db->prepare('UPDATE support_messages SET is_read=1 WHERE session_id=? AND sender="user"')
                   ->execute([$sessionId]);
                   
                foreach ($messages as $msg) {
                    echo "event: message\n";
                    echo "data: " . json_encode($msg, JSON_UNESCAPED_UNICODE) . "\n\n";
                    $lastId = max($lastId, (int)$msg['id']);
                }
                flush();
            }
            
            // 2. Fetch typing & online status of user
            $stmt = $db->prepare('SELECT is_user_typing, user_last_activity FROM chat_status WHERE session_id = ?');
            $stmt->execute([$sessionId]);
            $status = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if ($status) {
                $isUserTyping = (int)$status['is_user_typing'];
                $userLastActivity = $status['user_last_activity'];
                
                $isUserOnline = 0;
                if ($userLastActivity) {
                    $isUserOnline = (time() - strtotime($userLastActivity)) < 10 ? 1 : 0;
                }
                
                if ($isUserTyping !== $lastIsUserTyping) {
                    echo "event: typing\n";
                    echo "data: " . json_encode(['typing' => $isUserTyping]) . "\n\n";
                    $lastIsUserTyping = $isUserTyping;
                    flush();
                }
                
                if ($isUserOnline !== $lastUserOnline) {
                    echo "event: status\n";
                    echo "data: " . json_encode(['online' => $isUserOnline]) . "\n\n";
                    $lastUserOnline = $isUserOnline;
                    flush();
                }
            } else {
                $db->prepare('INSERT IGNORE INTO chat_status (session_id) VALUES (?)')->execute([$sessionId]);
            }
            
            echo ": ping\n\n";
            flush();
            
            sleep(1);
            $loopCount++;
        }
    }

    private function updateAdminActivity(string $sid): void
    {
        $db = DB::connection();
        $db->prepare('
            INSERT INTO chat_status (session_id, admin_last_activity)
            VALUES (?, NOW())
            ON DUPLICATE KEY UPDATE admin_last_activity = NOW()
        ')->execute([$sid]);
    }

    /** POST /admin/messages/{sid}/typing – Admin reports typing status */
    public function typing(string $sessionId): void
    {
        requireAdmin();
        $typing = !empty($_POST['typing']) ? 1 : 0;
        
        $db = DB::connection();
        $db->prepare('
            INSERT INTO chat_status (session_id, is_admin_typing, admin_last_activity)
            VALUES (?, ?, NOW())
            ON DUPLICATE KEY UPDATE is_admin_typing = ?, admin_last_activity = NOW()
        ')->execute([$sessionId, $typing, $typing]);
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit;
    }
}
