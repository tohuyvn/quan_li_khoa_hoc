<?php

namespace Tests\Unit;

use App\DB;
use App\Models\Wishlist;
use PDO;
use Tests\IntegrationTestCase;

class WishlistTest extends IntegrationTestCase
{
    private PDO $db;
    private int $userId;
    private int $categoryId;
    private int $productId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->db = DB::connection();
        $suffix = bin2hex(random_bytes(5));

        $stmt = $this->db->prepare(
            'INSERT INTO users (name, email, password, role, created_at)
             VALUES (?, ?, ?, "customer", NOW())'
        );
        $stmt->execute(['Wishlist Test', "wishlist-{$suffix}@shop.test", password_hash('secret123', PASSWORD_DEFAULT)]);
        $this->userId = (int) $this->db->lastInsertId();

        $stmt = $this->db->prepare(
            'INSERT INTO categories (name, status, created_at) VALUES (?, "active", NOW())'
        );
        $stmt->execute(["Wishlist {$suffix}"]);
        $this->categoryId = (int) $this->db->lastInsertId();

        $stmt = $this->db->prepare(
            'INSERT INTO products
                (category_id, name, price, discount_percent, promo_active, stock, image, status, created_at)
             VALUES (?, ?, 10000000, 10, 1, 5, ?, "active", NOW())'
        );
        $stmt->execute([$this->categoryId, "Phone Wishlist {$suffix}", '/image/logo.webp']);
        $this->productId = (int) $this->db->lastInsertId();
    }

    protected function tearDown(): void
    {
        if (isset($this->db)) {
            $this->db->prepare('DELETE FROM wishlists WHERE user_id = ?')->execute([$this->userId]);
            $this->db->prepare('DELETE FROM products WHERE id = ?')->execute([$this->productId]);
            $this->db->prepare('DELETE FROM categories WHERE id = ?')->execute([$this->categoryId]);
            $this->db->prepare('DELETE FROM users WHERE id = ?')->execute([$this->userId]);
        }
        parent::tearDown();
    }

    public function test_toggle_adds_and_removes_a_product(): void
    {
        $wishlist = new Wishlist();

        $this->assertSame('added', $wishlist->toggle($this->userId, $this->productId));
        $this->assertTrue($wishlist->has($this->userId, $this->productId));
        $this->assertSame(1, $wishlist->count($this->userId));
        $this->assertSame([$this->productId], $wishlist->getProductIds($this->userId));

        $this->assertSame('removed', $wishlist->toggle($this->userId, $this->productId));
        $this->assertFalse($wishlist->has($this->userId, $this->productId));
    }

    public function test_get_by_user_returns_active_product_with_resolved_price(): void
    {
        $wishlist = new Wishlist();
        $wishlist->toggle($this->userId, $this->productId);

        $items = $wishlist->getByUser($this->userId);

        $this->assertCount(1, $items);
        $this->assertSame($this->productId, $items[0]['id']);
        $this->assertSame($this->productId, $items[0]['product_id']);
        $this->assertSame(10_000_000, $items[0]['original_price']);
        $this->assertSame(9_000_000, $items[0]['sale_price']);
    }

    public function test_sync_ignores_duplicates_and_invalid_products(): void
    {
        $wishlist = new Wishlist();

        $count = $wishlist->sync($this->userId, [$this->productId, $this->productId, -1, 999999999]);
        $this->assertSame(1, $count);

        $count = $wishlist->sync($this->userId, [$this->productId]);
        $this->assertSame(1, $count);
    }
}
