<?php

namespace Tests\Unit;

use App\DB;
use App\Models\Order;
use App\Models\Product;
use App\OrderStateService;
use Tests\IntegrationTestCase;

class WarrantyTest extends IntegrationTestCase
{
    private ?int $testCategoryId = null;
    private ?int $testProductId = null;
    private ?int $testOrderId = null;

    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        $db = DB::connection();
        $db->exec("SET FOREIGN_KEY_CHECKS=0");
        
        if ($this->testOrderId) {
            $db->exec("DELETE FROM orders WHERE id = {$this->testOrderId}");
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

    public function test_warranty_lifecycle(): void
    {
        $db = DB::connection();

        // 1. Create a dummy category & product dynamically
        $db->exec("INSERT INTO categories (name, status) VALUES ('Điện thoại Test', 'active')");
        $this->testCategoryId = (int)$db->lastInsertId();

        $db->exec("INSERT INTO products (name, price, stock, category_id, status) VALUES ('iPhone 15 Pro Test', 25000000, 10, {$this->testCategoryId}, 'active')");
        $this->testProductId = (int)$db->lastInsertId();
        do {
            $testImei = '86' . str_pad((string) random_int(0, 9_999_999_999_999), 13, '0', STR_PAD_LEFT);
            $imeiStmt = $db->prepare("
                INSERT IGNORE INTO device_units (imei, product_id, status, note)
                VALUES (?, ?, 'available', 'PHPUnit warranty lifecycle')
            ");
            $imeiStmt->execute([$testImei, $this->testProductId]);
        } while ($imeiStmt->rowCount() === 0);

        // Count current warranties before test execution
        $beforeCount = (int)$db->query("SELECT COUNT(*) FROM warranties")->fetchColumn();

        // 2. Create an order with the test product
        $orderModel = new Order();
        $orderData = [
            'customer_name' => 'Nguyen Van A',
            'phone' => '0987654321',
            'email' => 'nva@example.com',
            'total_amount' => 25000000,
            'status' => 'pending',
            'payment_status' => 'pending',
            'payment_method' => 'cash'
        ];
        $cartItems = [
            [
                'product_id' => $this->testProductId,
                'name' => 'iPhone 15 Pro Test',
                'quantity' => 1,
                'price' => 25000000
            ]
        ];
        $order = $orderModel->create($orderData, $cartItems);
        $this->assertNotEmpty($order);
        $this->testOrderId = $order['id'];
        $this->assertEquals('pending', $order['status']);

        // 3. Transition order: pending -> confirmed -> packing -> shipping -> completed
        $res = OrderStateService::transition($order['id'], 'confirmed');
        $this->assertTrue($res['ok']);
        $res = OrderStateService::transition($order['id'], 'packing');
        $this->assertTrue($res['ok']);
        $assignedImei = $db->query("
            SELECT imei FROM device_units
            WHERE order_id = {$this->testOrderId} AND status = 'reserved'
            LIMIT 1
        ")->fetchColumn();
        $this->assertSame($testImei, $assignedImei);
        $res = OrderStateService::transition($order['id'], 'shipping');
        $this->assertTrue($res['ok']);
        
        // Before completion, check that the warranty count hasn't changed
        $count = (int)$db->query("SELECT COUNT(*) FROM warranties")->fetchColumn();
        $this->assertEquals($beforeCount, $count);

        // Transition to completed
        $res = OrderStateService::transition($order['id'], 'completed');
        $this->assertTrue($res['ok']);

        // 4. Verify warranty was automatically created for this order
        $stmt = $db->prepare("SELECT * FROM warranties WHERE order_id = ?");
        $stmt->execute([$order['id']]);
        $warranty = $stmt->fetch(\PDO::FETCH_ASSOC);

        $this->assertNotEmpty($warranty);
        $this->assertEquals('active', $warranty['status']);
        $this->assertEquals('Nguyen Van A', $warranty['customer_name']);
        $this->assertEquals('0987654321', $warranty['customer_phone']);
        $this->assertSame($testImei, $warranty['imei']);
        $this->assertStringStartsWith('WAR-', $warranty['warranty_code']);

        // 5. Submit a warranty repair request (claim)
        $claimStmt = $db->prepare("INSERT INTO warranty_claims (warranty_id, customer_note, status) VALUES (?, ?, 'pending')");
        $claimStmt->execute([$warranty['id'], 'Máy không lên nguồn']);
        $claimId = (int)$db->lastInsertId();

        // Verify claim creation
        $cStmt = $db->prepare("SELECT * FROM warranty_claims WHERE id = ?");
        $cStmt->execute([$claimId]);
        $claim = $cStmt->fetch(\PDO::FETCH_ASSOC);

        $this->assertNotEmpty($claim);
        $this->assertEquals('pending', $claim['status']);
        $this->assertEquals('Máy không lên nguồn', $claim['customer_note']);

        // 6. Admin updates claim status to 'repairing' and adds technician note
        $updateStmt = $db->prepare("UPDATE warranty_claims SET status = ?, admin_note = ? WHERE id = ?");
        $updateStmt->execute(['repairing', 'Đang thay thế IC nguồn chính hãng', $claimId]);

        // Verify claim update
        $cStmt->execute([$claimId]);
        $updatedClaim = $cStmt->fetch(\PDO::FETCH_ASSOC);
        $this->assertEquals('repairing', $updatedClaim['status']);
        $this->assertEquals('Đang thay thế IC nguồn chính hãng', $updatedClaim['admin_note']);
    }

    public function test_order_cannot_be_packed_without_enough_imei_units(): void
    {
        $db = DB::connection();
        $db->exec("INSERT INTO categories (name, status) VALUES ('Thiết bị thiếu IMEI Test', 'active')");
        $this->testCategoryId = (int) $db->lastInsertId();

        $db->exec("INSERT INTO products (name, price, stock, category_id, status) VALUES ('Máy thiếu IMEI Test', 12000000, 1, {$this->testCategoryId}, 'active')");
        $this->testProductId = (int) $db->lastInsertId();

        $order = (new Order())->create([
            'customer_name' => 'Khách thiếu IMEI',
            'phone' => '0900000000',
            'email' => 'missing-imei@example.com',
            'total_amount' => 12000000,
            'status' => 'pending',
            'payment_status' => 'paid',
            'payment_method' => 'cash',
        ], [[
            'product_id' => $this->testProductId,
            'name' => 'Máy thiếu IMEI Test',
            'quantity' => 1,
            'price' => 12000000,
        ]]);
        $this->testOrderId = (int) $order['id'];

        $this->assertTrue(OrderStateService::transition($this->testOrderId, 'confirmed')['ok']);
        $packing = OrderStateService::transition($this->testOrderId, 'packing');

        $this->assertFalse($packing['ok']);
        $this->assertStringContainsString('IMEI', $packing['error']);
        $this->assertSame('confirmed', (new Order())->find($this->testOrderId)['status']);
    }
}
