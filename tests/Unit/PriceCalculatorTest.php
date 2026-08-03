<?php

namespace Tests\Unit;

use App\PriceCalculator;
use Tests\TestCase;

class PriceCalculatorTest extends TestCase
{
    public function test_resolve_standard_product_no_promo(): void
    {
        $product = [
            'id' => 1,
            'price' => 100000,
            'promo_active' => 0,
            'discount_percent' => 10
        ];

        $resolved = PriceCalculator::resolve($product, 0);

        $this->assertEquals(100000, $resolved['original_price']);
        $this->assertEquals(100000, $resolved['sale_price']);
        $this->assertEquals(0, $resolved['discount_percent']);
        $this->assertFalse($resolved['has_discount']);
        $this->assertEquals('none', $resolved['discount_source']);
    }

    public function test_resolve_product_with_custom_promo_active(): void
    {
        $product = [
            'id' => 2,
            'price' => 200000,
            'promo_active' => 1,
            'discount_percent' => 20
        ];

        // Even if global discount is 50%, the product's custom promo should take precedence
        $resolved = PriceCalculator::resolve($product, 50);

        $this->assertEquals(200000, $resolved['original_price']);
        $this->assertEquals(160000, $resolved['sale_price']);
        $this->assertEquals(20, $resolved['discount_percent']);
        $this->assertTrue($resolved['has_discount']);
        $this->assertEquals('product', $resolved['discount_source']);
    }

    public function test_resolve_product_with_global_discount(): void
    {
        $product = [
            'id' => 3,
            'price' => 300000,
            'promo_active' => 0,
            'discount_percent' => 20 // ignored because promo_active is 0
        ];

        $resolved = PriceCalculator::resolve($product, 15);

        $this->assertEquals(300000, $resolved['original_price']);
        $this->assertEquals(255000, $resolved['sale_price']);
        $this->assertEquals(15, $resolved['discount_percent']);
        $this->assertTrue($resolved['has_discount']);
        $this->assertEquals('global', $resolved['discount_source']);
    }

    public function test_resolve_many_products(): void
    {
        $products = [
            [
                'id' => 10,
                'price' => 100000,
                'promo_active' => 0
            ],
            [
                'id' => 11,
                'price' => 200000,
                'promo_active' => 1,
                'discount_percent' => 30
            ]
        ];

        $resolvedList = PriceCalculator::resolveMany($products, 10);

        $this->assertCount(2, $resolvedList);
        
        // First product should have global discount of 10%
        $this->assertEquals(90000, $resolvedList[0]['sale_price']);
        $this->assertEquals('global', $resolvedList[0]['discount_source']);

        // Second product should have custom product discount of 30%
        $this->assertEquals(140000, $resolvedList[1]['sale_price']);
        $this->assertEquals('product', $resolvedList[1]['discount_source']);
    }
}
