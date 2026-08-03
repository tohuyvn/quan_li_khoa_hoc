<?php

namespace App\Controllers;

use App\Models\Product;
use App\PriceCalculator;

class CartController
{
    private function getCart(): array
    {
        return $_SESSION['cart'] ?? [];
    }

    private function saveCart(array $cart): void
    {
        $_SESSION['cart'] = $cart;
    }

    private function refreshCart(array $cart): array
    {
        if ($cart === []) {
            return [];
        }

        $productModel = new Product();
        $variantModel = new \App\Models\ProductVariant();
        $globalDiscount = PriceCalculator::getGlobalDiscountPercent();
        $refreshed = [];

        foreach ($cart as $key => $item) {
            $productId = (int) ($item['product_id'] ?? 0);
            if ($productId <= 0) {
                $productId = (int)$key;
            }
            $quantity = (int) ($item['quantity'] ?? 1);
            if ($quantity <= 0) {
                continue;
            }

            $product = $productModel->find($productId);
            if (!$product || ($product['status'] ?? '') !== 'active') {
                continue;
            }

            $variantId = isset($item['variant_id']) ? (int)$item['variant_id'] : 0;
            $variant = $variantId > 0 ? $variantModel->find($variantId) : null;

            $priced = PriceCalculator::resolve($product, $globalDiscount);
            $price = $priced['sale_price'];
            $originalPrice = $priced['original_price'];
            $color = $item['color'] ?? 'Tiêu chuẩn';
            $name = $product['name'];

            if ($variant && $variant['status'] === 'active') {
                $variantPrice = (int)$variant['price'];
                if ($variantPrice > 0) {
                    if ($priced['has_discount']) {
                        $price = (int)($variantPrice * (1 - $priced['discount_percent'] / 100));
                        $originalPrice = $variantPrice;
                    } else {
                        $price = $variantPrice;
                        $originalPrice = $variantPrice;
                    }
                }
                $color = htmlspecialchars($variant['color'] . ' / ' . $variant['storage']);
                $name = $product['name'] . ' (' . $color . ')';
            }

            $refreshed[$key] = [
                'product_id' => $productId,
                'variant_id' => $variantId,
                'name' => $name,
                'price' => $price,
                'original_price' => $originalPrice,
                'quantity' => $quantity,
                'image' => $product['image'] ?? '',
                'has_discount' => $priced['has_discount'],
                'discount_percent' => $priced['discount_percent'],
                'color' => $color,
            ];
        }

        return $refreshed;
    }

    public function index(): void
    {
        $cart = $this->refreshCart($this->getCart());
        $this->saveCart($cart);

        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        view('client.cart.index', ['cart' => $cart, 'total' => $total]);
    }

    public function add(int $productId): void
    {
        $this->addToCartLogic($productId);

        flash('success', 'Đã thêm sản phẩm vào giỏ hàng.');
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        if (str_contains($referer, '/cart') || str_contains($referer, '/checkout')) {
            redirect('/cart');
        }
        redirect('/');
    }

    public function buyNow(int $productId): void
    {
        $this->addToCartLogic($productId);
        redirect('/checkout');
    }

    private function addToCartLogic(int $productId): void
    {
        $productModel = new Product();
        $product = $productModel->find($productId);
        if (!$product || ($product['status'] ?? '') !== 'active') {
            $msg = 'Sản phẩm không tồn tại.';
            if ($this->isAjax()) {
                echo json_encode(['success'=>false,'message'=>$msg]);
                exit;
            }
            flash('error', $msg);
            redirect('/');
        }

        $variantId = isset($_POST['variant_id']) ? (int)$_POST['variant_id'] : 0;
        $cartKey = $variantId > 0 ? "{$productId}-{$variantId}" : (string)$productId;

        $color = trim($_POST['color'] ?? 'Tiêu chuẩn');
        $cart = $this->refreshCart($this->getCart());

        $added = false;
        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity']++;
        } else {
            $added = true;
            $cart[$cartKey] = [
                'product_id' => $productId,
                'variant_id' => $variantId,
                'quantity' => 1,
                'color' => $color,
            ];
        }

        $cart = $this->refreshCart($cart);
        $this->saveCart($cart);

        if ($this->isAjax()) {
            echo json_encode([
                'success' => true,
                'added' => $added,
                'cartCount' => count($cart)
            ]);
            exit;
        }
    }

    public function update(): void
    {
        $quantities = $_POST['quantity'] ?? [];
        $cart = $this->getCart();

        foreach ($quantities as $cartKey => $qty) {
            $qty = (int) $qty;
            if (!isset($cart[$cartKey])) {
                continue;
            }
            if ($qty <= 0) {
                unset($cart[$cartKey]);
                continue;
            }
            $cart[$cartKey]['quantity'] = $qty;
        }

        $cart = $this->refreshCart($cart);
        $this->saveCart($cart);

        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        if ($this->isAjax()) {
            echo json_encode([
                'success' => true,
                'cartCount' => count($cart),
                'total' => $total,
                'totalFormatted' => formatMoney($total),
                'items' => array_map(function ($key, $item) {
                    return [
                        'key' => $key,
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'subtotalFormatted' => formatMoney($item['price'] * $item['quantity']),
                    ];
                }, array_keys($cart), $cart)
            ]);
            exit;
        }

        flash('success', 'Cập nhật giỏ hàng thành công.');
        redirect('/cart');
    }

    public function remove(string $key): void
    {
        $cart = $this->getCart();
        unset($cart[$key]);
        $this->saveCart($cart);

        if ($this->isAjax()) {
            echo json_encode([
                'success'   => true,
                'cartCount' => count($cart),
            ]);
            exit;
        }

        flash('success', 'Đã xóa sản phẩm khỏi giỏ hàng.');
        redirect('/cart');
    }

    public static function refreshedSessionCart(): array
    {
        $controller = new self();
        $cart = $controller->refreshCart($controller->getCart());
        $controller->saveCart($cart);

        return $cart;
    }

    private function isAjax(): bool
    {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
            || (stripos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false);
    }
}
