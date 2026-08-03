<?php

namespace Tests\Unit;

use App\Models\Product;
use PHPUnit\Framework\TestCase;

class ProductFilterTest extends TestCase
{
    public function test_short_price_values_are_interpreted_as_millions(): void
    {
        $this->assertSame(3_000_000, Product::normalizePriceFilter('3'));
        $this->assertSame(9_500_000, Product::normalizePriceFilter('9.5'));
    }

    public function test_full_price_values_are_kept_unchanged(): void
    {
        $this->assertSame(3_000_000, Product::normalizePriceFilter('3000000'));
        $this->assertNull(Product::normalizePriceFilter(''));
    }

    public function test_installment_service_queries_are_recognized(): void
    {
        $this->assertTrue(Product::isInstallmentSearch('trả góp'));
        $this->assertTrue(Product::isInstallmentSearch('Trả góp 0%'));
        $this->assertTrue(Product::isInstallmentSearch('tra gop'));
        $this->assertFalse(Product::isInstallmentSearch('iPhone 16'));
    }

    public function test_chipset_vendor_aliases_match_real_product_names(): void
    {
        $this->assertContains('MacBook', Product::chipsetAliases('Apple'));
        $this->assertContains('iPhone', Product::chipsetAliases('Apple'));
        $this->assertContains('Core i', Product::chipsetAliases('Intel'));
        $this->assertSame(['Snapdragon'], Product::chipsetAliases('Snapdragon'));
    }
}
