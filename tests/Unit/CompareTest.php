<?php

namespace Tests\Unit;

use App\Controllers\CompareController;
use Tests\TestCase;

class CompareTest extends TestCase
{
    public function test_spec_extraction_from_structured_colons(): void
    {
        $detail = "Màn hình: 6.7 inches Super Retina XDR\nChipset: Apple A17 Pro\nRAM: 8GB\nBộ nhớ trong: 256GB\nPin: 4422 mAh\nCamera: 48MP + 12MP + 12MP";
        $specs = CompareController::extractSpecs($detail);

        $this->assertEquals("6.7 inches Super Retina XDR", $specs['Màn hình']);
        $this->assertEquals("Apple A17 Pro", $specs['Chipset']);
        $this->assertEquals("8GB", $specs['RAM']);
        $this->assertEquals("256GB", $specs['Bộ nhớ trong']);
        $this->assertEquals("4422 mAh", $specs['Pin']);
        $this->assertEquals("48MP + 12MP + 12MP", $specs['Camera']);
    }

    public function test_spec_extraction_from_unstructured_text(): void
    {
        $detail = "Máy sở hữu màn hình OLED 6.1 inch sắc nét\nChip A16 Bionic cực mượt\nTrang bị RAM 6GB đa nhiệm thoải mái\nBộ nhớ trong dung lượng 128GB\nThời lượng pin trâu 3279 mAh";
        $specs = CompareController::extractSpecs($detail);

        $this->assertEquals("Máy sở hữu màn hình OLED 6.1 inch sắc nét", $specs['Màn hình']);
        $this->assertEquals("Chip A16 Bionic cực mượt", $specs['Chipset']);
        $this->assertEquals("Trang bị RAM 6GB đa nhiệm thoải mái", $specs['RAM']);
        $this->assertEquals("Bộ nhớ trong dung lượng 128GB", $specs['Bộ nhớ trong']);
        $this->assertEquals("Thời lượng pin trâu 3279 mAh", $specs['Pin']);
    }

    public function test_spec_extraction_with_custom_keys(): void
    {
        $detail = "Hệ điều hành: iOS 17\nTrọng lượng: 187g";
        $specs = CompareController::extractSpecs($detail);

        $this->assertEquals("iOS 17", $specs['Hệ điều hành']);
        $this->assertEquals("187g", $specs['Trọng lượng']);
        $this->assertEquals("-", $specs['RAM']);
    }

    public function test_product_spec_filtering_by_ram(): void
    {
        $productModel = new \App\Models\Product();
        
        $result = $productModel->paginateActive([
            'ram' => ['8GB']
        ], 1, 10);
        
        foreach ($result['items'] as $product) {
            $matched = (stripos($product['name'], '8GB') !== false) || (stripos($product['detail'] ?? '', '8GB') !== false);
            $this->assertTrue($matched, "Product {$product['name']} did not match 8GB RAM filter");
        }
    }

    public function test_product_spec_filtering_by_storage(): void
    {
        $productModel = new \App\Models\Product();
        
        $result = $productModel->paginateActive([
            'storage' => ['128GB', '256GB']
        ], 1, 10);
        
        foreach ($result['items'] as $product) {
            $matched = false;
            foreach (['128GB', '256GB'] as $storageVal) {
                if ((stripos($product['name'], $storageVal) !== false) || (stripos($product['detail'] ?? '', $storageVal) !== false)) {
                    $matched = true;
                    break;
                }
            }
            $this->assertTrue($matched, "Product {$product['name']} did not match Storage filters");
        }
    }

    public function test_product_spec_filtering_by_chipset(): void
    {
        $productModel = new \App\Models\Product();
        
        $result = $productModel->paginateActive([
            'chipset' => ['Apple', 'Snapdragon']
        ], 1, 10);
        
        foreach ($result['items'] as $product) {
            $matched = false;
            foreach (['Apple', 'Snapdragon'] as $chipVal) {
                foreach (\App\Models\Product::chipsetAliases($chipVal) as $alias) {
                    if ((stripos($product['name'], $alias) !== false)
                        || (stripos($product['detail'] ?? '', $alias) !== false)
                        || (stripos($product['chipset'] ?? '', $alias) !== false)
                    ) {
                        $matched = true;
                        break 2;
                    }
                }
            }
            $this->assertTrue($matched, "Product {$product['name']} did not match Chipset filters");
        }
    }
}
