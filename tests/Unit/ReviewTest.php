<?php

namespace Tests\Unit;

use App\DB;
use App\Models\Review;
use PDO;
use Tests\IntegrationTestCase;

class ReviewTest extends IntegrationTestCase
{
    private PDO $db;
    private int $userId;
    private int $categoryId;
    private int $productId;
    private int $orderId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->db = DB::connection();
        $suffix = bin2hex(random_bytes(5));

        $stmt = $this->db->prepare(
            'INSERT INTO users (name, email, password, role, created_at)
             VALUES (?, ?, ?, "customer", NOW())'
        );
        $stmt->execute(['Review Test', "review-{$suffix}@shop.test", password_hash('secret123', PASSWORD_DEFAULT)]);
        $this->userId = (int) $this->db->lastInsertId();

        $stmt = $this->db->prepare(
            'INSERT INTO categories (name, status, created_at) VALUES (?, "active", NOW())'
        );
        $stmt->execute(["Review {$suffix}"]);
        $this->categoryId = (int) $this->db->lastInsertId();

        $stmt = $this->db->prepare(
            'INSERT INTO products (category_id, name, price, stock, status, created_at)
             VALUES (?, ?, 12000000, 3, "active", NOW())'
        );
        $stmt->execute([$this->categoryId, "Phone Review {$suffix}"]);
        $this->productId = (int) $this->db->lastInsertId();

        $stmt = $this->db->prepare(
            'INSERT INTO orders
                (user_id, customer_name, phone, email, payment_method, payment_status,
                 total_amount, status, created_at)
             VALUES (?, ?, ?, ?, "cash", "pending", 12000000, "pending", NOW())'
        );
        $stmt->execute([$this->userId, 'Review Test', '0900000000', "review-{$suffix}@shop.test"]);
        $this->orderId = (int) $this->db->lastInsertId();

        $stmt = $this->db->prepare(
            'INSERT INTO order_items (order_id, product_id, product_name, quantity, price)
             VALUES (?, ?, ?, 1, 12000000)'
        );
        $stmt->execute([$this->orderId, $this->productId, "Phone Review {$suffix}"]);
    }

    protected function tearDown(): void
    {
        if (isset($this->db)) {
            $this->db->prepare('DELETE FROM product_reviews WHERE user_id = ?')->execute([$this->userId]);
            $this->db->prepare('DELETE FROM order_items WHERE order_id = ?')->execute([$this->orderId]);
            $this->db->prepare('DELETE FROM orders WHERE id = ?')->execute([$this->orderId]);
            $this->db->prepare('DELETE FROM products WHERE id = ?')->execute([$this->productId]);
            $this->db->prepare('DELETE FROM categories WHERE id = ?')->execute([$this->categoryId]);
            $this->db->prepare('DELETE FROM users WHERE id = ?')->execute([$this->userId]);
        }
        parent::tearDown();
    }

    public function test_only_paid_active_orders_are_eligible_for_review(): void
    {
        $review = new Review();
        // confirmed nhưng chưa paid → chưa đủ điều kiện
        $this->assertNull($review->findEligibleOrderId($this->userId, $this->productId));

        $this->db->prepare('UPDATE orders SET payment_status = "paid", status = "confirmed" WHERE id = ?')
            ->execute([$this->orderId]);

        $this->assertSame(
            $this->orderId,
            $review->findEligibleOrderId($this->userId, $this->productId)
        );

        $this->db->prepare('UPDATE orders SET status = "cancelled" WHERE id = ?')->execute([$this->orderId]);
        $this->assertNull($review->findEligibleOrderId($this->userId, $this->productId));

        $this->db->prepare('UPDATE orders SET payment_status = "paid", status = "completed" WHERE id = ?')
            ->execute([$this->orderId]);
        $this->assertSame(
            $this->orderId,
            $review->findEligibleOrderId($this->userId, $this->productId)
        );
    }

    public function test_verified_review_is_linked_to_user_and_order(): void
    {
        $this->db->prepare('UPDATE orders SET payment_status = "paid", status = "completed" WHERE id = ?')
            ->execute([$this->orderId]);
        $review = new Review();

        $reviewId = $review->create([
            'product_id' => $this->productId,
            'user_id' => $this->userId,
            'order_id' => $this->orderId,
            'user_name' => 'Review Test',
            'rating' => 5,
            'comment' => 'Sản phẩm tốt và giao hàng nhanh.',
            'is_verified' => true,
        ]);

        $this->assertGreaterThan(0, $reviewId);
        $this->assertTrue($review->hasReviewed($this->userId, $this->productId));
        $reviews = $review->byProduct($this->productId);
        $this->assertCount(1, $reviews);
        $this->assertSame(1, (int) $reviews[0]['is_verified']);
        $this->assertSame($this->orderId, (int) $reviews[0]['order_id']);
    }
}
