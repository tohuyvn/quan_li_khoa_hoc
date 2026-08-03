<?php

namespace Tests\Unit;

use App\DB;
use App\Services\LoyaltyService;
use App\Models\Order;
use Tests\IntegrationTestCase;

class LoyaltyTest extends IntegrationTestCase
{
    private ?int $testUserId = null;
    private ?int $testCategoryId = null;
    private ?int $testProductId = null;
    private array $orderIds = [];

    protected function tearDown(): void
    {
        $db = DB::connection();
        $db->exec("SET FOREIGN_KEY_CHECKS=0");

        foreach ($this->orderIds as $orderId) {
            $db->exec("DELETE FROM order_items WHERE order_id = {$orderId}");
            $db->exec("DELETE FROM orders WHERE id = {$orderId}");
        }
        if ($this->testUserId) {
            $db->exec("DELETE FROM users WHERE id = {$this->testUserId}");
        }
        if ($this->testProductId) {
            $db->exec("DELETE FROM products WHERE id = {$this->testProductId}");
        }
        if ($this->testCategoryId) {
            $db->exec("DELETE FROM categories WHERE id = {$this->testCategoryId}");
        }

        $db->exec("SET FOREIGN_KEY_CHECKS=1");
        parent::tearDown();
    }

    public function test_loyalty_tier_determination(): void
    {
        $this->assertEquals('Standard', LoyaltyService::determineTier(0));
        $this->assertEquals('Standard', LoyaltyService::determineTier(999));
        $this->assertEquals('Silver', LoyaltyService::determineTier(1000));
        $this->assertEquals('Silver', LoyaltyService::determineTier(4999));
        $this->assertEquals('Gold', LoyaltyService::determineTier(5000));
        $this->assertEquals('Gold', LoyaltyService::determineTier(9999));
        $this->assertEquals('Diamond', LoyaltyService::determineTier(10000));
        $this->assertEquals('Diamond', LoyaltyService::determineTier(50000));
    }

    public function test_loyalty_tier_discount_percent(): void
    {
        $this->assertEquals(0, LoyaltyService::getDiscountPercent('Standard'));
        $this->assertEquals(2, LoyaltyService::getDiscountPercent('Silver'));
        $this->assertEquals(5, LoyaltyService::getDiscountPercent('Gold'));
        $this->assertEquals(10, LoyaltyService::getDiscountPercent('Diamond'));
    }

    public function test_automated_points_awarded_on_completed_order(): void
    {
        $db = DB::connection();

        // 1. Create a dummy user
        $email = 'loyalty_test_' . uniqid() . '@shop.test';
        $db->exec("INSERT INTO users (name, email, password, role, loyalty_points, loyalty_tier) 
                   VALUES ('Loyalty Test User', '{$email}', '123456', 'customer', 0, 'Standard')");
        $this->testUserId = (int)$db->lastInsertId();

        // 2. Create category and product
        $db->exec("INSERT INTO categories (name, status) VALUES ('Loyalty Test Category', 'active')");
        $this->testCategoryId = (int)$db->lastInsertId();

        $db->exec("INSERT INTO products (name, price, stock, category_id, status) 
                   VALUES ('Loyalty Test Product', 10000000, 10, {$this->testCategoryId}, 'active')");
        $this->testProductId = (int)$db->lastInsertId();

        // 3. Create an order with total amount = 25,000,000 VND (should award 2500 points)
        $orderModel = new Order();
        $cartItems = [
            [
                'product_id' => $this->testProductId,
                'name' => 'Loyalty Test Product',
                'quantity' => 2,
                'price' => 10000000
            ]
        ];
        
        $orderData = [
            'user_id' => $this->testUserId,
            'customer_name' => 'Loyalty Test User',
            'email' => $email,
            'phone' => '0987654321',
            'total_amount' => 25000000,
            'payment_method' => 'cash',
            'payment_status' => 'pending',
            'status' => 'pending',
        ];

        $order = $orderModel->create($orderData, $cartItems);
        $orderId = (int)$order['id'];
        $this->orderIds[] = $orderId;

        // Verify points not awarded yet
        $orderFetched = $orderModel->find($orderId);
        $this->assertEquals(0, (int)$orderFetched['points_awarded']);
        $this->assertEquals(0, (int)$orderFetched['earned_points']);

        // Update status to 'completed'
        $orderModel->update($orderId, ['status' => 'completed']);

        // Verify points awarded and order updated
        $orderUpdated = $orderModel->find($orderId);
        $this->assertEquals(1, (int)$orderUpdated['points_awarded']);
        $this->assertEquals(2500, (int)$orderUpdated['earned_points']);

        // Verify user received points and tier upgraded to Silver
        $stmt = $db->prepare('SELECT loyalty_points, loyalty_tier FROM users WHERE id = ?');
        $stmt->execute([$this->testUserId]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        $this->assertEquals(2500, (int)$user['loyalty_points']);
        $this->assertEquals('Silver', $user['loyalty_tier']);
    }

    public function test_points_revert_on_order_cancelled(): void
    {
        $db = DB::connection();

        // 1. Create a dummy user
        $email = 'loyalty_test_revert_' . uniqid() . '@shop.test';
        $db->exec("INSERT INTO users (name, email, password, role, loyalty_points, loyalty_tier) 
                   VALUES ('Loyalty Test User Revert', '{$email}', '123456', 'customer', 2500, 'Silver')");
        $this->testUserId = (int)$db->lastInsertId();

        // 2. Create category and product
        $db->exec("INSERT INTO categories (name, status) VALUES ('Loyalty Revert Category', 'active')");
        $this->testCategoryId = (int)$db->lastInsertId();

        $db->exec("INSERT INTO products (name, price, stock, category_id, status) 
                   VALUES ('Loyalty Revert Product', 10000000, 10, {$this->testCategoryId}, 'active')");
        $this->testProductId = (int)$db->lastInsertId();

        // 3. Create an order with total amount = 25,000,000 (points already awarded)
        $orderModel = new Order();
        $cartItems = [
            [
                'product_id' => $this->testProductId,
                'name' => 'Loyalty Revert Product',
                'quantity' => 2,
                'price' => 10000000
            ]
        ];
        
        $orderData = [
            'user_id' => $this->testUserId,
            'customer_name' => 'Loyalty Test User Revert',
            'email' => $email,
            'phone' => '0987654321',
            'total_amount' => 25000000,
            'payment_method' => 'cash',
            'payment_status' => 'paid', // paid means points awarded automatically on create
            'status' => 'pending',
            'points_awarded' => 1,
            'earned_points' => 2500,
        ];

        $order = $orderModel->create($orderData, $cartItems);
        $orderId = (int)$order['id'];
        $this->orderIds[] = $orderId;

        // Cancel the order
        $orderModel->update($orderId, ['status' => 'cancelled']);

        // Verify points reverted
        $orderUpdated = $orderModel->find($orderId);
        $this->assertEquals(0, (int)$orderUpdated['points_awarded']);
        $this->assertEquals(0, (int)$orderUpdated['earned_points']);

        // Verify user points went down to 0 and tier downgraded back to Standard
        $stmt = $db->prepare('SELECT loyalty_points, loyalty_tier FROM users WHERE id = ?');
        $stmt->execute([$this->testUserId]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        $this->assertEquals(0, (int)$user['loyalty_points']);
        $this->assertEquals('Standard', $user['loyalty_tier']);
    }
}
