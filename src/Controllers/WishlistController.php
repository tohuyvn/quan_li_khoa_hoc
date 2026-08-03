<?php

namespace App\Controllers;

use App\Models\Wishlist;
use App\Models\Product;

class WishlistController
{
    private function isAjax(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest' ||
               stripos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false;
    }

    public function index(): void
    {
        $user = authUser();
        $items = $user ? (new Wishlist())->getByUser((int) $user['id']) : [];

        // Enrich with formatted price
        foreach ($items as &$item) {
            $item['formatted_price'] = formatMoney($item['sale_price']);
            $item['image_url'] = imageUrl($item['image'] ?? '', 'thumb');
        }
        unset($item);

        view('client.wishlist.index', [
            'wishlistItems' => $items,
            'wishlistCount' => count($items),
            'isGuestWishlist' => !$user,
        ]);
    }

    public function toggle(int $productId): void
    {
        header('Content-Type: application/json');

        $user = authUser();
        if (!$user) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập để sử dụng tính năng này.', 'requireLogin' => true]);
            exit;
        }

        $productModel = new Product();
        $product = $productModel->find($productId);
        if (!$product || ($product['status'] ?? '') !== 'active') {
            echo json_encode(['success' => false, 'message' => 'Sản phẩm không tồn tại.']);
            exit;
        }

        $wishlistModel = new Wishlist();
        $action = $wishlistModel->toggle((int) $user['id'], $productId);
        $count  = $wishlistModel->count((int) $user['id']);

        echo json_encode([
            'success' => true,
            'action'  => $action, // 'added' or 'removed'
            'count'   => $count,
            'message' => $action === 'added'
                ? '❤️ Đã thêm vào danh sách yêu thích!'
                : '💔 Đã xóa khỏi danh sách yêu thích.',
        ]);
        exit;
    }

    public function remove(int $productId): void
    {
        $user = authUser();
        if (!$user) {
            redirect('/login');
        }

        $wishlistModel = new Wishlist();
        $wishlistModel->remove((int) $user['id'], $productId);

        if ($this->isAjax()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'count' => $wishlistModel->count((int) $user['id'])]);
            exit;
        }

        flash('success', 'Đã xóa sản phẩm khỏi danh sách yêu thích.');
        redirect('/wishlist');
    }

    public function sync(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        $user = authUser();
        if (!$user) {
            http_response_code(401);
            echo json_encode(['success' => false, 'requireLogin' => true]);
            exit;
        }

        $rawIds = $_POST['product_ids'] ?? [];
        if (is_string($rawIds)) {
            $decoded = json_decode($rawIds, true);
            $rawIds = is_array($decoded) ? $decoded : [];
        }

        $count = (new Wishlist())->sync((int) $user['id'], is_array($rawIds) ? $rawIds : []);
        echo json_encode([
            'success' => true,
            'count' => $count,
            'message' => 'Đã đồng bộ sản phẩm yêu thích vào tài khoản.',
        ]);
        exit;
    }
}
