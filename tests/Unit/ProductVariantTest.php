<?php

namespace Tests\Unit;

use App\DB;
use App\Models\ProductVariant;
use App\Controllers\CartController;
use Tests\IntegrationTestCase;

class ProductVariantTest extends IntegrationTestCase
{
    private ?int $testCategoryId = null;
    private ?int $testProductId = null;
    private array $variantIds = [];

    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        $db = DB::connection();
        $db->exec("SET FOREIGN_KEY_CHECKS=0");

        foreach ($this->variantIds as $vId) {
            $db->exec("DELETE FROM product_variants WHERE id = {$vId}");
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

    public function test_variant_crud_and_cart_integration(): void
    {
        $db = DB::connection();

        // 1. Create a dummy category & product dynamically
        $db->exec("INSERT INTO categories (name, status) VALUES ('Phone Test Category', 'active')");
        $this->testCategoryId = (int)$db->lastInsertId();

        $db->exec("INSERT INTO products (name, price, stock, category_id, status) VALUES ('iPhone 15 Pro Variant Test', 25000000, 10, {$this->testCategoryId}, 'active')");
        $this->testProductId = (int)$db->lastInsertId();

        // 2. Create Product Variants
        $variantModel = new ProductVariant();

        $res1 = $variantModel->create([
            'product_id' => $this->testProductId,
            'color' => 'Titan sa mạc',
            'storage' => '256GB',
            'price' => 28000000,
            'stock' => 5,
            'image' => '',
            'status' => 'active'
        ]);
        $this->assertTrue($res1);
        $v1Id = (int)$db->lastInsertId();
        $this->variantIds[] = $v1Id;

        $res2 = $variantModel->create([
            'product_id' => $this->testProductId,
            'color' => 'Titan tự nhiên',
            'storage' => '128GB',
            'price' => 25000000,
            'stock' => 0,
            'image' => '',
            'status' => 'active'
        ]);
        $this->assertTrue($res2);
        $v2Id = (int)$db->lastInsertId();
        $this->variantIds[] = $v2Id;

        // 3. Test Retrieval
        $variants = $variantModel->getForProduct($this->testProductId);
        $this->assertCount(2, $variants);
        
        $v1 = $variantModel->find($v1Id);
        $this->assertEquals('Titan sa mạc', $v1['color']);
        $this->assertEquals(28000000, (int)$v1['price']);
        $this->assertEquals(5, (int)$v1['stock']);

        // 4. Test Update
        $updateRes = $variantModel->update($v1Id, [
            'color' => 'Titan sa mạc (Cập nhật)',
            'storage' => '256GB',
            'price' => 29000000,
            'stock' => 12,
            'image' => '',
            'status' => 'active'
        ]);
        $this->assertTrue($updateRes);

        $v1Updated = $variantModel->find($v1Id);
        $this->assertEquals('Titan sa mạc (Cập nhật)', $v1Updated['color']);
        $this->assertEquals(29000000, (int)$v1Updated['price']);
        $this->assertEquals(12, (int)$v1Updated['stock']);

        // 5. Test Cart Integration (reflecting variant price & label)
        // Simulate session cart structure with a variant
        $cart = [
            "{$this->testProductId}-{$v1Id}" => [
                'product_id' => $this->testProductId,
                'variant_id' => $v1Id,
                'quantity' => 2,
                'color' => 'Titan sa mạc (Cập nhật) / 256GB'
            ]
        ];

        // Call the refreshCart method using reflection since it's private
        $cartController = new CartController();
        $reflection = new \ReflectionClass(CartController::class);
        $method = $reflection->getMethod('refreshCart');
        $method->setAccessible(true);
        $refreshedCart = $method->invoke($cartController, $cart);

        // Assert that the item under key has resolved name, price, and color details
        $cartKey = "{$this->testProductId}-{$v1Id}";
        $this->assertArrayHasKey($cartKey, $refreshedCart);
        
        $cartItem = $refreshedCart[$cartKey];
        $this->assertEquals('iPhone 15 Pro Variant Test (Titan sa mạc (Cập nhật) / 256GB)', $cartItem['name']);
        $this->assertEquals(29000000, $cartItem['price']);
        $this->assertEquals(2, $cartItem['quantity']);
        $this->assertEquals('Titan sa mạc (Cập nhật) / 256GB', $cartItem['color']);

        // 6. Test Deletion
        $delRes = $variantModel->delete($v2Id);
        $this->assertTrue($delRes);
        $this->assertNull($variantModel->find($v2Id));
    }
}
