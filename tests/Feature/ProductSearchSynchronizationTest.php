<?php

namespace Tests\Feature;

use App\DB;
use App\Models\Product;
use Tests\IntegrationTestCase;

class ProductSearchSynchronizationTest extends IntegrationTestCase
{
    public function test_laptop_search_combined_with_apple_chipset_returns_macbooks(): void
    {
        $stmt = DB::connection()->prepare('SELECT id FROM categories WHERE name = ? LIMIT 1');
        $stmt->execute(['Laptop']);
        $categoryId = (int) $stmt->fetchColumn();
        $this->assertGreaterThan(0, $categoryId);

        $result = (new Product())->paginateActive([
            'search' => 'Laptop',
            'category_id' => $categoryId,
            'chipset' => ['Apple'],
            'in_stock' => true,
            'sort' => 'newest',
        ], 1, 12);

        $this->assertGreaterThan(0, $result['total']);
        foreach ($result['items'] as $product) {
            $this->assertSame($categoryId, (int) $product['category_id']);
            $this->assertStringContainsString('MacBook', $product['name']);
            $this->assertGreaterThan(0, (int) $product['stock']);
        }
    }
}
