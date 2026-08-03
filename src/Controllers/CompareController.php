<?php

namespace App\Controllers;

use App\Models\Product;
use App\Models\Category;

class CompareController
{
    public function index(): void
    {
        $productModel = new Product();
        $categoryModel = new Category();

        $idsString = trim($_GET['ids'] ?? '');
        $ids = [];
        if ($idsString !== '') {
            $ids = array_filter(
                array_map('intval', explode(',', $idsString)),
                fn($id) => $id > 0
            );
        }

        // Limit to maximum 3 products for comparison
        $ids = array_slice($ids, 0, 3);

        $products = [];
        foreach ($ids as $id) {
            $prod = $productModel->findWithPricing($id);
            if ($prod && ($prod['status'] ?? '') === 'active') {
                $category = $categoryModel->find((int)$prod['category_id']);
                $prod['category_name'] = $category['name'] ?? 'Khác';
                $prod['specs'] = self::extractSpecs($prod['detail']);
                $products[] = $prod;
            }
        }

        // Gather list of active products to allow adding to comparison
        $allProducts = $productModel->allActive();
        foreach ($allProducts as &$p) {
            $p['formatted_price'] = formatMoney($p['price']);
        }
        unset($p);

        // Get all unique specification keys across compared products
        $allSpecKeys = ['Màn hình', 'Chipset', 'RAM', 'Bộ nhớ trong', 'Pin', 'Camera'];
        foreach ($products as $prod) {
            foreach (array_keys($prod['specs']) as $key) {
                if (!in_array($key, $allSpecKeys, true)) {
                    $allSpecKeys[] = $key;
                }
            }
        }

        view('client.products.compare', [
            'products' => $products,
            'allProducts' => $allProducts,
            'allSpecKeys' => $allSpecKeys,
        ]);
    }

    public static function extractSpecs(?string $detail): array
    {
        $specs = [
            'Màn hình' => '-',
            'Chipset' => '-',
            'RAM' => '-',
            'Bộ nhớ trong' => '-',
            'Pin' => '-',
            'Camera' => '-',
        ];

        if (empty($detail)) {
            return $specs;
        }

        $lines = explode("\n", $detail);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }
            if (strpos($line, ':') !== false) {
                list($k, $v) = explode(':', $line, 2);
                $k = trim($k);
                $v = trim($v);
                
                // Match and map common keys
                if (stripos($k, 'Màn hình') !== false || stripos($k, 'Screen') !== false) {
                    $specs['Màn hình'] = $v;
                } elseif (stripos($k, 'Chip') !== false || stripos($k, 'CPU') !== false || stripos($k, 'Vi xử lý') !== false) {
                    $specs['Chipset'] = $v;
                } elseif (stripos($k, 'RAM') !== false) {
                    $specs['RAM'] = $v;
                } elseif (stripos($k, 'Bộ nhớ') !== false || stripos($k, 'Dung lượng') !== false || stripos($k, 'Storage') !== false) {
                    $specs['Bộ nhớ trong'] = $v;
                } elseif (stripos($k, 'Pin') !== false || stripos($k, 'Battery') !== false) {
                    $specs['Pin'] = $v;
                } elseif (stripos($k, 'Camera') !== false) {
                    $specs['Camera'] = $v;
                } else {
                    $specs[$k] = $v;
                }
            } else {
                // Heuristics for lines without colons
                if (preg_match('/(màn\s+hình|screen)/i', $line)) {
                    $specs['Màn hình'] = $line;
                } elseif (preg_match('/(chip|cpu|vi\s+xử\s+lý)/i', $line)) {
                    $specs['Chipset'] = $line;
                } elseif (preg_match('/(ram)/i', $line)) {
                    $specs['RAM'] = $line;
                } elseif (preg_match('/(bộ\s+nhớ|dung\s+lượng|storage)/i', $line)) {
                    $specs['Bộ nhớ trong'] = $line;
                } elseif (preg_match('/(pin|battery)/i', $line)) {
                    $specs['Pin'] = $line;
                } elseif (preg_match('/(camera)/i', $line)) {
                    $specs['Camera'] = $line;
                }
            }
        }
        return $specs;
    }
}
