<?php

namespace App\Controllers\Admin;

use App\Models\Category;
use App\Models\Product;

class ProductController
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
        $this->guard();
        $search = isset($_GET['search']) ? trim((string) $_GET['search']) : '';
        view('admin.products.index', [
            'products' => $this->products->allWithCategory($search),
            'search'   => $search,
            'categories' => $this->categories->all(),
        ]);
    }

    public function createForm(): void
    {
        $this->guard();
        view('admin.products.form', [
            'product' => null,
            'categories' => $this->categories->all(),
        ]);
    }

    public function store(): void
    {
        $this->guard();
        $data = $this->validatedProductData();
        if ($data === null) {
            redirect('/admin/products/create');
        }

        $this->products->create($data);
        flash('success', 'Thêm sản phẩm thành công.');
        redirect('/admin/products');
    }

    public function editForm(int $id): void
    {
        $this->guard();
        $product = $this->products->find($id);
        if (!$product) {
            flash('error', 'Sản phẩm không tồn tại.');
            redirect('/admin/products');
        }

        $variantsModel = new \App\Models\ProductVariant();
        $variants = $variantsModel->getForProduct($id, true);

        view('admin.products.form', [
            'product' => $product,
            'categories' => $this->categories->all(),
            'variants' => $variants
        ]);
    }

    public function update(int $id): void
    {
        $this->guard();
        $product = $this->products->find($id);
        if (!$product) {
            flash('error', 'Sản phẩm không tồn tại.');
            redirect('/admin/products');
        }

        $data = $this->validatedProductData($product['image'] ?? '');
        if ($data === null) {
            redirect("/admin/products/{$id}/edit");
        }

        $oldPrice = (int) ($product['price'] ?? 0);
        $newPrice = (int) ($data['price'] ?? 0);
        if ($oldPrice !== $newPrice) {
            logAdminAction('price_change', "Price updated for product '" . ($product['name'] ?? '') . "' (ID: {$id}): " . formatMoney($oldPrice) . " -> " . formatMoney($newPrice));
        }

        $this->products->update($id, $data);
        flash('success', 'Cập nhật sản phẩm thành công.');
        redirect('/admin/products');
    }

    public function destroy(int $id): void
    {
        $this->guard();
        
        $db = \App\DB::connection();
        try {
            $stmtCheck = $db->prepare("SELECT COUNT(*) FROM order_items WHERE product_id = ?");
            $stmtCheck->execute([$id]);
            $hasOrders = (int)$stmtCheck->fetchColumn() > 0;
            
            if ($hasOrders) {
                $stmtUpdate = $db->prepare("UPDATE products SET status = 'inactive' WHERE id = ?");
                $stmtUpdate->execute([$id]);
                flash('warning', 'Sản phẩm này đã có trong đơn hàng của khách, không thể xóa hoàn toàn. Trạng thái sản phẩm đã được chuyển sang "Ngưng hoạt động" (inactive) để ẩn khỏi cửa hàng.');
            } else {
                $this->products->delete($id);
                flash('success', 'Đã xóa sản phẩm thành công.');
            }
        } catch (\Throwable $e) {
            flash('error', 'Lỗi khi xóa sản phẩm: ' . $e->getMessage());
        }
        
        redirect('/admin/products');
    }

    private function guard(): void
    {
        if (!isAdmin()) {
            redirect('/admin/login');
        }
    }

    private function validatedProductData(string $currentImage = ''): ?array
    {
        $name = trim($_POST['name'] ?? '');
        $categoryId = (int) ($_POST['category_id'] ?? 0);
        $price = (int) preg_replace('/\D+/', '', (string) ($_POST['price'] ?? '0'));
        $stock = (int) ($_POST['stock'] ?? 0);
        $description = trim($_POST['description'] ?? '');
        $detail = trim($_POST['detail'] ?? '');
        $status = ($_POST['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active';
        $promoActive = isset($_POST['promo_active']);
        $discountPercent = (int) ($_POST['discount_percent'] ?? 0);

        if ($name === '') {
            setOld($_POST);
            flash('error', 'Tên sản phẩm không được để trống.');
            return null;
        }

        if ($categoryId <= 0 || !$this->categories->find($categoryId)) {
            setOld($_POST);
            flash('error', 'Vui lòng chọn danh mục hợp lệ.');
            return null;
        }

        if ($price <= 0) {
            setOld($_POST);
            flash('error', 'Đơn giá phải lớn hơn 0.');
            return null;
        }

        if ($stock < 0) {
            setOld($_POST);
            flash('error', 'Tồn kho không được âm.');
            return null;
        }

        if ($promoActive && ($discountPercent <= 0 || $discountPercent > 100)) {
            setOld($_POST);
            flash('error', 'Mức giảm giá sản phẩm phải từ 1% đến 100%.');
            return null;
        }

        if (!$promoActive) {
            $discountPercent = 0;
        }

        $image = $this->resolveImagePath($currentImage);
        if ($image === null) {
            setOld($_POST);
            return null;
        }

        return [
            'category_id' => $categoryId,
            'name' => $name,
            'price' => $price,
            'description' => $description,
            'detail' => $detail,
            'stock' => $stock,
            'image' => $image,
            'status' => $status,
            'discount_percent' => $discountPercent,
            'promo_active' => $promoActive,
            'ram' => trim($_POST['ram'] ?? '') ?: null,
            'storage_gb' => trim($_POST['storage_gb'] ?? '') ?: null,
            'chipset' => trim($_POST['chipset'] ?? '') ?: null,
            'screen_size' => trim($_POST['screen_size'] ?? '') ?: null,
            'battery_mah' => trim($_POST['battery_mah'] ?? '') ?: null,
            'os' => trim($_POST['os'] ?? '') ?: null,
            'is_preorder' => isset($_POST['is_preorder']),
            'preorder_deposit' => max(0, (int) preg_replace('/\D+/', '', (string) ($_POST['preorder_deposit'] ?? '0'))),
        ];
    }

    private function resolveImagePath(string $currentImage = ''): ?string
    {
        if (!empty($_FILES['image_file']['name']) && ($_FILES['image_file']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            if (($_FILES['image_file']['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
                flash('error', 'Upload ảnh thất bại.');
                return null;
            }

            try {
                $imageInfo = \App\ImageService::inspectUploadedImage($_FILES['image_file']);
            } catch (\InvalidArgumentException $e) {
                flash('error', $e->getMessage());
                return null;
            }

            $baseName = preg_replace('/[^a-z0-9_-]+/i', '_', pathinfo($_FILES['image_file']['name'], PATHINFO_FILENAME));
            $tempFilename = 'temp_' . bin2hex(random_bytes(12)) . '.' . $imageInfo['extension'];
            $dir = BASE_PATH . '/image';

            if (!is_dir($dir)) {
                mkdir($dir, 0775, true);
            }

            $tempFullPath = $dir . '/' . $tempFilename;
            if (!move_uploaded_file($_FILES['image_file']['tmp_name'], $tempFullPath)) {
                flash('error', 'Không lưu được file ảnh.');
                return null;
            }

            $safeBaseName = trim((string) $baseName, '_') ?: 'product';
            $baseNameUnique = $safeBaseName . '_' . bin2hex(random_bytes(6));
            $result = \App\ImageService::processImage($tempFullPath, $dir, $baseNameUnique);
            @unlink($tempFullPath);

            return $result['full'];
        }

        $path = trim($_POST['image'] ?? '');
        if ($path !== '') {
            return $path;
        }

        if ($currentImage !== '') {
            return $currentImage;
        }

        flash('error', 'Vui lòng upload ảnh hoặc nhập đường dẫn ảnh.');
        return null;
    }

    public function bulkAction(): void
    {
        $this->guard();

        $ids = $_POST['product_ids'] ?? [];
        if (!is_array($ids) || empty($ids)) {
            flash('error', 'Vui lòng chọn ít nhất một sản phẩm.');
            redirect('/admin/products');
        }

        $ids = array_map('intval', $ids);
        $action = $_POST['bulk_action'] ?? '';

        $db = \App\DB::connection();

        try {
            $db->beginTransaction();

            if ($action === 'delete') {
                $deletedCount = 0;
                $inactivatedCount = 0;
                foreach ($ids as $id) {
                    $stmtCheck = $db->prepare("SELECT COUNT(*) FROM order_items WHERE product_id = ?");
                    $stmtCheck->execute([$id]);
                    $hasOrders = (int)$stmtCheck->fetchColumn() > 0;

                    if ($hasOrders) {
                        $stmtUpdate = $db->prepare("UPDATE products SET status = 'inactive' WHERE id = ?");
                        $stmtUpdate->execute([$id]);
                        $inactivatedCount++;
                    } else {
                        $stmtDelete = $db->prepare("DELETE FROM products WHERE id = ?");
                        $stmtDelete->execute([$id]);
                        $deletedCount++;
                    }
                }
                logAdminAction('bulk_delete', "Bulk processed " . count($ids) . " products. Deleted: {$deletedCount}, Inactivated: {$inactivatedCount}.");
                if ($inactivatedCount > 0) {
                    flash('warning', "Đã xóa thành công {$deletedCount} sản phẩm. Có {$inactivatedCount} sản phẩm đã phát sinh đơn hàng nên không thể xóa và đã được chuyển sang trạng thái 'Ngưng hoạt động' (inactive).");
                } else {
                    flash('success', "Đã xóa hàng loạt {$deletedCount} sản phẩm thành công.");
                }
            } 
            elseif ($action === 'change_category') {
                $categoryId = (int)($_POST['bulk_category_id'] ?? 0);
                if ($categoryId <= 0 || !$this->categories->find($categoryId)) {
                    throw new \Exception('Danh mục lựa chọn không hợp lệ.');
                }
                $placeholders = implode(',', array_fill(0, count($ids), '?'));
                $stmt = $db->prepare("UPDATE products SET category_id = ? WHERE id IN ($placeholders)");
                $stmt->execute(array_merge([$categoryId], $ids));
                logAdminAction('bulk_change_category', "Bulk changed category to ID {$categoryId} for " . count($ids) . " products.");
                flash('success', 'Đã cập nhật danh mục cho ' . count($ids) . ' sản phẩm.');
            }
            elseif ($action === 'change_status') {
                $status = ($_POST['bulk_status'] ?? 'active') === 'inactive' ? 'inactive' : 'active';
                $placeholders = implode(',', array_fill(0, count($ids), '?'));
                $stmt = $db->prepare("UPDATE products SET status = ? WHERE id IN ($placeholders)");
                $stmt->execute(array_merge([$status], $ids));
                logAdminAction('bulk_change_status', "Bulk changed status to '{$status}' for " . count($ids) . " products.");
                flash('success', 'Đã cập nhật trạng thái cho ' . count($ids) . ' sản phẩm.');
            }
            elseif ($action === 'update_price') {
                $priceAction = $_POST['price_action'] ?? 'set';
                $priceValue = (int)preg_replace('/[^\d-]+/', '', (string)($_POST['price_value'] ?? '0'));

                foreach ($ids as $id) {
                    $product = $this->products->find($id);
                    if (!$product) continue;

                    $oldPrice = (int)$product['price'];
                    $newPrice = $oldPrice;

                    if ($priceAction === 'set') {
                        $newPrice = max(0, $priceValue);
                    } elseif ($priceAction === 'adjust_percent') {
                        $newPrice = max(0, (int)round($oldPrice * (1 + $priceValue / 100)));
                    } elseif ($priceAction === 'adjust_amount') {
                        $newPrice = max(0, $oldPrice + $priceValue);
                    }

                    $stmt = $db->prepare("UPDATE products SET price = ? WHERE id = ?");
                    $stmt->execute([$newPrice, $id]);

                    if ($oldPrice !== $newPrice) {
                        logAdminAction('price_change', "Price updated for product '" . $product['name'] . "' (ID: {$id}): " . formatMoney($oldPrice) . " -> " . formatMoney($newPrice));
                    }
                }
                flash('success', 'Đã cập nhật giá hàng loạt cho ' . count($ids) . ' sản phẩm.');
            }
            elseif ($action === 'update_stock') {
                $stockAction = $_POST['stock_action'] ?? 'set';
                $stockValue = (int)($_POST['stock_value'] ?? 0);

                foreach ($ids as $id) {
                    $product = $this->products->find($id);
                    if (!$product) continue;

                    $oldStock = (int)$product['stock'];
                    $newStock = $oldStock;

                    if ($stockAction === 'set') {
                        $newStock = max(0, $stockValue);
                    } elseif ($stockAction === 'adjust') {
                        $newStock = max(0, $oldStock + $stockValue);
                    }

                    $stmt = $db->prepare("UPDATE products SET stock = ? WHERE id = ?");
                    $stmt->execute([$newStock, $id]);
                }
                flash('success', 'Đã cập nhật tồn kho hàng loạt cho ' . count($ids) . ' sản phẩm.');
            }
            elseif ($action === 'update_promo') {
                $promoActive = isset($_POST['bulk_promo_active']) ? 1 : 0;
                $discountPercent = max(0, min(100, (int)($_POST['bulk_discount_percent'] ?? 0)));

                $placeholders = implode(',', array_fill(0, count($ids), '?'));
                $stmt = $db->prepare("UPDATE products SET promo_active = ?, discount_percent = ? WHERE id IN ($placeholders)");
                $stmt->execute(array_merge([$promoActive, $discountPercent], $ids));

                logAdminAction('bulk_update_promo', "Bulk updated promo settings for " . count($ids) . " products.");
                flash('success', 'Đã cập nhật khuyến mãi hàng loạt cho ' . count($ids) . ' sản phẩm.');
            }
            else {
                throw new \Exception('Hành động không hợp lệ.');
            }

            $db->commit();
        } catch (\Throwable $e) {
            $db->rollBack();
            flash('error', 'Lỗi thực thi: ' . $e->getMessage());
        }

        redirect('/admin/products');
    }

    public function addVariant(int $productId): void
    {
        $this->guard();
        $color = trim($_POST['color'] ?? '');
        $storage = trim($_POST['storage'] ?? '');
        $price = (int)($_POST['price'] ?? 0);
        $stock = (int)($_POST['stock'] ?? 0);

        if ($color === '' || $storage === '') {
            flash('error', 'Màu sắc và dung lượng không được để trống.');
            redirect("/admin/products/{$productId}/edit");
        }

        $variantsModel = new \App\Models\ProductVariant();
        $variantsModel->create([
            'product_id' => $productId,
            'color' => $color,
            'storage' => $storage,
            'price' => $price,
            'stock' => $stock,
            'image' => '',
            'status' => 'active'
        ]);

        flash('success', 'Thêm biến thể thành công.');
        redirect("/admin/products/{$productId}/edit");
    }

    public function updateVariant(int $id): void
    {
        $this->guard();
        $variantsModel = new \App\Models\ProductVariant();
        $variant = $variantsModel->find($id);
        if (!$variant) {
            flash('error', 'Biến thể không tồn tại.');
            redirect('/admin/products');
        }

        $color = trim($_POST['color'] ?? $variant['color']);
        $storage = trim($_POST['storage'] ?? $variant['storage']);
        $price = isset($_POST['price']) ? (int)$_POST['price'] : (int)$variant['price'];
        $stock = isset($_POST['stock']) ? (int)$_POST['stock'] : (int)$variant['stock'];
        $status = $_POST['status'] ?? $variant['status'];

        $variantsModel->update($id, [
            'color' => $color,
            'storage' => $storage,
            'price' => $price,
            'stock' => $stock,
            'image' => $variant['image'],
            'status' => $status
        ]);

        flash('success', 'Cập nhật biến thể thành công.');
        redirect("/admin/products/{$variant['product_id']}/edit");
    }

    public function deleteVariant(int $id): void
    {
        $this->guard();
        $variantsModel = new \App\Models\ProductVariant();
        $variant = $variantsModel->find($id);
        if (!$variant) {
            flash('error', 'Biến thể không tồn tại.');
            redirect('/admin/products');
        }

        $productId = $variant['product_id'];
        $variantsModel->delete($id);

        flash('success', 'Đã xóa biến thể.');
        redirect("/admin/products/{$productId}/edit");
    }
}
