<?php

namespace App\Controllers\Admin;

use App\Models\Product;
use App\Models\Category;

class InventoryController
{
    private Product $products;
    private Category $categories;

    public function __construct()
    {
        $this->products = new Product();
        $this->categories = new Category();
    }

    public function index(): void
    {
        if (!isAdmin()) {
            redirect('/admin/login');
        }

        $allProducts = $this->products->all();
        $cats = $this->categories->all();
        $catMap = [];
        foreach ($cats as $c) {
            $catMap[$c['id']] = $c['name'];
        }

        // Apply filters
        $search = trim($_GET['q'] ?? '');
        $stockStatus = $_GET['stock_status'] ?? '';

        $filteredProducts = [];
        foreach ($allProducts as $p) {
            $p['category_name'] = $catMap[$p['category_id']] ?? 'Khác';
            $stock = (int) ($p['stock'] ?? 0);

            if ($search !== '' && stripos($p['name'], $search) === false && stripos($p['category_name'], $search) === false) {
                continue;
            }

            if ($stockStatus === 'in_stock' && $stock <= 5) {
                continue;
            }
            if ($stockStatus === 'low_stock' && ($stock === 0 || $stock > 5)) {
                continue;
            }
            if ($stockStatus === 'out_of_stock' && $stock !== 0) {
                continue;
            }

            $filteredProducts[] = $p;
        }

        view('admin.inventory.index', [
            'products' => $filteredProducts,
            'search' => $search,
            'stockStatus' => $stockStatus,
        ]);
    }

    public function exportCsv(): void
    {
        if (!isAdmin()) {
            redirect('/admin/login');
        }

        $allProducts = $this->products->all();
        $cats = $this->categories->all();
        $catMap = [];
        foreach ($cats as $c) {
            $catMap[$c['id']] = $c['name'];
        }

        $search = trim($_GET['q'] ?? '');
        $stockStatus = $_GET['stock_status'] ?? '';

        $filteredProducts = [];
        foreach ($allProducts as $p) {
            $p['category_name'] = $catMap[$p['category_id']] ?? 'Khác';
            $stock = (int) ($p['stock'] ?? 0);

            if ($search !== '' && stripos($p['name'], $search) === false && stripos($p['category_name'], $search) === false) {
                continue;
            }
            if ($stockStatus === 'in_stock' && $stock <= 5) {
                continue;
            }
            if ($stockStatus === 'low_stock' && ($stock === 0 || $stock > 5)) {
                continue;
            }
            if ($stockStatus === 'out_of_stock' && $stock !== 0) {
                continue;
            }

            $filteredProducts[] = $p;
        }

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="inventory_export_' . date('Ymd_His') . '.csv"');

        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        fputcsv($output, ['Mã SP', 'Tên sản phẩm', 'Danh mục', 'Đơn giá (đ)', 'Số lượng tồn kho', 'Trạng thái']);

        foreach ($filteredProducts as $product) {
            $stock = (int)$product['stock'];
            $stockText = 'Còn hàng';
            if ($stock === 0) {
                $stockText = 'Hết hàng';
            } elseif ($stock <= 5) {
                $stockText = 'Sắp hết hàng';
            }

            fputcsv($output, [
                $product['id'],
                $product['name'],
                $product['category_name'],
                $product['price'],
                $stock,
                $stockText,
            ]);
        }
        fclose($output);
        exit;
    }

    public function updateStock(int $id): void
    {
        if (!isAdmin()) {
            redirect('/admin/login');
        }

        $newStock = (int)($_POST['stock'] ?? 0);
        if ($newStock < 0) {
            $newStock = 0;
        }

        $db = \App\DB::connection();
        $stmt = $db->prepare('UPDATE products SET stock = ? WHERE id = ?');
        $success = $stmt->execute([$newStock, $id]);

        if ($success) {
            flash('success', 'Cập nhật số lượng tồn kho thành công!');
        } else {
            flash('error', 'Có lỗi xảy ra khi cập nhật tồn kho.');
        }

        redirect($_SERVER['HTTP_REFERER'] ?? '/admin/inventory');
    }
}
