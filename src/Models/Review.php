<?php

namespace App\Models;

use App\DB;
use PDO;

class Review
{
    private PDO $db;

    public function __construct()
    {
        $this->db = DB::connection();
    }

    public function byProduct(int $productId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM product_reviews WHERE product_id = ? ORDER BY created_at DESC');
        $stmt->execute([$productId]);
        $reviews = $stmt->fetchAll();
        foreach ($reviews as &$review) {
            $review['replies'] = $this->getReplies((int) $review['id']);
        }
        return $reviews;
    }

    public function getAverageRating(int $productId): array
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) as count, COALESCE(AVG(rating), 0) as average FROM product_reviews WHERE product_id = ?');
        $stmt->execute([$productId]);
        $result = $stmt->fetch();
        return [
            'count' => (int) $result['count'],
            'average' => round((float) $result['average'], 1)
        ];
    }

    public function getAllAverageRatings(): array
    {
        $stmt = $this->db->query('SELECT product_id, COUNT(*) as count, AVG(rating) as average FROM product_reviews GROUP BY product_id');
        $results = $stmt->fetchAll();
        $ratings = [];
        foreach ($results as $row) {
            $ratings[$row['product_id']] = [
                'count' => (int) $row['count'],
                'average' => round((float) $row['average'], 1)
            ];
        }
        return $ratings;
    }

    /**
     * Đơn đủ điều kiện đánh giá: đã thanh toán và không bị hủy/chờ thanh toán.
     * (confirmed → completed đều được — khớp luồng VietQR/MoMo đã markPaid)
     */
    public function findEligibleOrderId(int $userId, int $productId): ?int
    {
        $stmt = $this->db->prepare(
            'SELECT o.id
             FROM orders o
             JOIN order_items oi ON oi.order_id = o.id
             WHERE o.user_id = ?
               AND oi.product_id = ?
               AND o.payment_status = \'paid\'
               AND o.status IN (\'confirmed\', \'packing\', \'shipping\', \'completed\')
             ORDER BY o.created_at DESC
             LIMIT 1'
        );
        $stmt->execute([$userId, $productId]);
        $orderId = $stmt->fetchColumn();

        return $orderId !== false ? (int) $orderId : null;
    }

    public function hasReviewed(int $userId, int $productId): bool
    {
        $stmt = $this->db->prepare(
            'SELECT 1 FROM product_reviews WHERE user_id = ? AND product_id = ? LIMIT 1'
        );
        $stmt->execute([$userId, $productId]);

        return (bool) $stmt->fetchColumn();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO product_reviews
                (product_id, user_id, order_id, user_name, rating, comment, is_verified, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            (int) $data['product_id'],
            isset($data['user_id']) ? (int) $data['user_id'] : null,
            isset($data['order_id']) ? (int) $data['order_id'] : null,
            trim($data['user_name']),
            (int) $data['rating'],
            trim($data['comment'] ?? ''),
            !empty($data['is_verified']) ? 1 : 0,
            date('Y-m-d H:i:s'),
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function like(int $reviewId): bool
    {
        $stmt = $this->db->prepare('UPDATE product_reviews SET likes_count = likes_count + 1 WHERE id = ?');
        return $stmt->execute([$reviewId]);
    }

    public function getReplies(int $reviewId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM product_review_replies WHERE review_id = ? ORDER BY created_at ASC');
        $stmt->execute([$reviewId]);
        return $stmt->fetchAll();
    }

    public function addReply(int $reviewId, string $userName, string $replyText): bool
    {
        $stmt = $this->db->prepare('INSERT INTO product_review_replies (review_id, user_name, reply_text, created_at) VALUES (?, ?, ?, ?)');
        return $stmt->execute([
            $reviewId,
            trim($userName),
            trim($replyText),
            date('Y-m-d H:i:s')
        ]);
    }
}
