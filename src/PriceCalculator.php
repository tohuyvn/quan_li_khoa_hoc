<?php

namespace App;

use App\Models\SitePromotion;

class PriceCalculator
{
    public static function getActiveSitePromotion(): ?array
    {
        return (new SitePromotion())->getActive();
    }

    public static function getGlobalDiscountPercent(): int
    {
        $promo = self::getActiveSitePromotion();

        return $promo ? (int) $promo['discount_percent'] : 0;
    }

    public static function resolve(array $product, ?int $globalDiscount = null): array
    {
        $original = (int) ($product['price'] ?? 0);

        $productDiscount = 0;
        $hasProductPromo = !empty($product['promo_active']);
        if ($hasProductPromo) {
            $productDiscount = (int) ($product['discount_percent'] ?? 0);
        }

        if ($globalDiscount === null) {
            $globalDiscount = self::getGlobalDiscountPercent();
        }

        // If product has admin custom setup, respect it and ignore global promotion
        if ($hasProductPromo) {
            $effectiveDiscount = $productDiscount;
            $source = $effectiveDiscount > 0 ? 'product' : 'none';
        } else {
            $effectiveDiscount = $globalDiscount;
            $source = $effectiveDiscount > 0 ? 'global' : 'none';
        }

        $salePrice = $effectiveDiscount > 0
            ? (int) round($original * (100 - $effectiveDiscount) / 100)
            : $original;

        if ($salePrice < 0) {
            $salePrice = 0;
        }

        $hasDiscount = $effectiveDiscount > 0 && $salePrice < $original;
        if (!$hasDiscount) {
            $source = 'none';
        }

        return array_merge($product, [
            'original_price' => $original,
            'sale_price' => $salePrice,
            'discount_percent' => $effectiveDiscount,
            'has_discount' => $hasDiscount,
            'discount_source' => $source,
        ]);
    }

    public static function resolveMany(array $products, ?int $globalDiscount = null): array
    {
        if ($globalDiscount === null) {
            $globalDiscount = self::getGlobalDiscountPercent();
        }

        return array_map(
            fn (array $product): array => self::resolve($product, $globalDiscount),
            $products
        );
    }
}
