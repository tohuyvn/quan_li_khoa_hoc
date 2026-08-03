<?php

namespace Tests\Feature;

use App\DB;
use App\Models\Order;
use App\OrderStateService;
use Tests\IntegrationTestCase;

class OrderStockReservationTest extends IntegrationTestCase
{
    private ?int $categoryId = null;
    private array $productIds = [];
    private array $orderIds = [];

    protected function tearDown(): void
    {
        $db = DB::connection();
        $db->exec('SET FOREIGN_KEY_CHECKS=0');
        foreach ($this->orderIds as $orderId) {
            $db->exec('DELETE FROM order_items WHERE order_id = ' . (int) $orderId);
            $db->exec('DELETE FROM orders WHERE id = ' . (int) $orderId);
        }
        foreach ($this->productIds as $productId) {
            $db->exec('DELETE FROM products WHERE id = ' . (int) $productId);
        }
        if ($this->categoryId !== null) {
            $db->exec('DELETE FROM categories WHERE id = ' . $this->categoryId);
        }
        $db->exec('SET FOREIGN_KEY_CHECKS=1');
        parent::tearDown();
    }

    public function test_order_reserves_stock_and_rejects_insufficient_quantity(): void
    {
        $productId = $this->createProduct(2, false);
        $order = (new Order())->create($this->orderData(), [[
            'product_id' => $productId,
            'name' => 'Stock Reservation Test',
            'quantity' => 2,
            'price' => 100000,
        ]]);
        $this->orderIds[] = (int) $order['id'];

        $stock = (int) DB::connection()->query("SELECT stock FROM products WHERE id = {$productId}")->fetchColumn();
        $this->assertSame(0, $stock);

        try {
            (new Order())->create($this->orderData(), [[
                'product_id' => $productId,
                'name' => 'Stock Reservation Test',
                'quantity' => 1,
                'price' => 100000,
            ]]);
            $this->fail('Expected insufficient stock exception.');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('không đủ số lượng', $e->getMessage());
        }

        $cancelled = OrderStateService::transition((int) $order['id'], 'cancelled');
        $this->assertTrue($cancelled['ok']);
        $this->assertSame(2, (int) DB::connection()->query("SELECT stock FROM products WHERE id = {$productId}")->fetchColumn());

        $repeated = OrderStateService::transition((int) $order['id'], 'cancelled');
        $this->assertTrue($repeated['ok']);
        $this->assertTrue($repeated['unchanged']);
        $this->assertSame(2, (int) DB::connection()->query("SELECT stock FROM products WHERE id = {$productId}")->fetchColumn());
    }

    public function test_preorder_does_not_reduce_physical_stock(): void
    {
        $productId = $this->createProduct(0, true);
        $order = (new Order())->create($this->orderData(), [[
            'product_id' => $productId,
            'name' => 'Preorder Reservation Test',
            'quantity' => 1,
            'price' => 100000,
        ]]);
        $this->orderIds[] = (int) $order['id'];

        $stock = (int) DB::connection()->query("SELECT stock FROM products WHERE id = {$productId}")->fetchColumn();
        $this->assertSame(0, $stock);
    }

    private function orderData(): array
    {
        return [
            'reserve_stock' => true,
            'customer_name' => 'Stock Test',
            'phone' => '0987654321',
            'email' => 'stock-test@example.com',
            'total_amount' => 100000,
            'status' => 'pending',
            'payment_status' => 'pending',
            'payment_method' => 'cash',
        ];
    }

    private function createProduct(int $stock, bool $preorder): int
    {
        $db = DB::connection();
        if ($this->categoryId === null) {
            $db->exec("INSERT INTO categories (name, status) VALUES ('Stock Reservation Category', 'active')");
            $this->categoryId = (int) $db->lastInsertId();
        }

        $stmt = $db->prepare(
            "INSERT INTO products (name, price, stock, category_id, status, is_preorder)
             VALUES ('Stock Reservation Test', 100000, ?, ?, 'active', ?)"
        );
        $stmt->execute([$stock, $this->categoryId, $preorder ? 1 : 0]);
        $productId = (int) $db->lastInsertId();
        $this->productIds[] = $productId;

        return $productId;
    }
}
