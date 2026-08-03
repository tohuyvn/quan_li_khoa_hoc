<?php

namespace App\Models;

use App\DB;
use App\PriceCalculator;
use PDO;

class Product
{
    private PDO $db;

    public function __construct()
    {
        $this->db = DB::connection();
    }

    public function paginateActive(array $filters = [], int $page = 1, int $perPage = 12): array
    {
        $offset = ($page - 1) * $perPage;
        $params = [];

        $where = ["p.status = 'active'"];

        if (!empty($filters['category_id'])) {
            $where[] = "p.category_id = ?";
            $params[] = (int) $filters['category_id'];
        }

        if (!empty($filters['search'])) {
            $search = trim((string) $filters['search']);
            // "Trả góp" là một hình thức thanh toán, không phải tên sản phẩm.
            // Mọi sản phẩm đang bán đều có thể được hiển thị cho truy vấn này.
            if (!self::isInstallmentSearch($search)) {
                $where[] = "(p.name LIKE ? OR p.description LIKE ? OR p.detail LIKE ? OR c.name LIKE ?)";
                $like = '%' . $search . '%';
                $params = array_merge($params, [$like, $like, $like, $like]);
            }
        }

        if (isset($filters['min_price']) && $filters['min_price'] !== '') {
            $minPrice = self::normalizePriceFilter($filters['min_price']);
            if ($minPrice !== null) {
                $where[] = "p.price >= ?";
                $params[] = $minPrice;
            }
        }

        if (isset($filters['max_price']) && $filters['max_price'] !== '') {
            $maxPrice = self::normalizePriceFilter($filters['max_price']);
            if ($maxPrice !== null) {
                $where[] = "p.price <= ?";
                $params[] = $maxPrice;
            }
        }

        if (!empty($filters['in_stock'])) {
            $where[] = "p.stock > 0";
        }

        // Structured spec filters (columns) with text fallback
        if (!empty($filters['ram'])) {
            $ramOrs = [];
            $ramVals = is_array($filters['ram']) ? $filters['ram'] : [$filters['ram']];
            foreach ($ramVals as $ramVal) {
                if ($ramVal !== '') {
                    $ramOrs[] = "(p.ram = ? OR p.detail LIKE ? OR p.name LIKE ?)";
                    $params[] = $ramVal;
                    $params[] = '%' . $ramVal . '%';
                    $params[] = '%' . $ramVal . '%';
                }
            }
            if (!empty($ramOrs)) {
                $where[] = '(' . implode(' OR ', $ramOrs) . ')';
            }
        }

        if (!empty($filters['storage'])) {
            $storageOrs = [];
            $storageVals = is_array($filters['storage']) ? $filters['storage'] : [$filters['storage']];
            foreach ($storageVals as $storeVal) {
                if ($storeVal !== '') {
                    $storageOrs[] = "(p.storage_gb = ? OR p.detail LIKE ? OR p.name LIKE ? OR EXISTS (
                        SELECT 1 FROM product_variants pv WHERE pv.product_id = p.id AND pv.storage = ? AND pv.status = 'active'
                    ))";
                    $params[] = $storeVal;
                    $params[] = '%' . $storeVal . '%';
                    $params[] = '%' . $storeVal . '%';
                    $params[] = $storeVal;
                }
            }
            if (!empty($storageOrs)) {
                $where[] = '(' . implode(' OR ', $storageOrs) . ')';
            }
        }

        if (!empty($filters['chipset'])) {
            $chipOrs = [];
            $chipVals = is_array($filters['chipset']) ? $filters['chipset'] : [$filters['chipset']];
            foreach ($chipVals as $chipVal) {
                if ($chipVal !== '') {
                    $termOrs = [];
                    foreach (self::chipsetAliases((string) $chipVal) as $term) {
                        $termOrs[] = '(p.chipset LIKE ? OR p.detail LIKE ? OR p.name LIKE ?)';
                        $like = '%' . $term . '%';
                        array_push($params, $like, $like, $like);
                    }
                    if ($termOrs !== []) {
                        $chipOrs[] = '(' . implode(' OR ', $termOrs) . ')';
                    }
                }
            }
            if (!empty($chipOrs)) {
                $where[] = '(' . implode(' OR ', $chipOrs) . ')';
            }
        }

        $orderSql = "p.id DESC"; // Default: Newest
        if (!empty($filters['sort'])) {
            switch ($filters['sort']) {
                case 'price_asc':  $orderSql = "p.price ASC"; break;
                case 'price_desc': $orderSql = "p.price DESC"; break;
                case 'oldest':     $orderSql = "p.id ASC"; break;
                case 'bestseller':
                    $orderSql = "(SELECT COALESCE(SUM(oi.quantity), 0) FROM order_items oi WHERE oi.product_id = p.id) DESC, p.id DESC";
                    break;
                case 'rating':
                    $orderSql = "(SELECT COALESCE(AVG(pr.rating), 0) FROM product_reviews pr WHERE pr.product_id = p.id) DESC, p.id DESC";
                    break;
            }
        }

        $whereSql = implode(' AND ', $where);

        // Get total count
        $countSql = "SELECT COUNT(*) FROM products p LEFT JOIN categories c ON c.id = p.category_id WHERE $whereSql";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        // Get products
        $sql = "SELECT p.*, c.name AS category_name
                FROM products p
                LEFT JOIN categories c ON c.id = p.category_id
                WHERE $whereSql
                ORDER BY $orderSql
                LIMIT $perPage OFFSET $offset";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        $products = array_map(function (array $row): array {
            $product = $this->cast($row);
            $product['category_name'] = $row['category_name'] ?? 'Khác';
            return $product;
        }, $stmt->fetchAll());

        return [
            'items' => PriceCalculator::resolveMany($products),
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => ceil($total / $perPage)
        ];
    }

    public static function chipsetAliases(string $vendor): array
    {
        return match ($vendor) {
            'Apple' => ['Apple', 'MacBook', 'iPhone', 'iPad'],
            'Intel' => ['Intel', 'Core i', 'Xeon'],
            'AMD' => ['AMD', 'Ryzen'],
            'Snapdragon' => ['Snapdragon'],
            default => [$vendor],
        };
    }

    public function allActive(): array
    {
        $stmt = $this->db->query("SELECT * FROM products WHERE status = 'active' ORDER BY id DESC");

        return PriceCalculator::resolveMany(array_map([$this, 'cast'], $stmt->fetchAll()));
    }

    public function all(): array
    {
        $stmt = $this->db->query('SELECT * FROM products ORDER BY id ASC');

        return array_map([$this, 'cast'], $stmt->fetchAll());
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM products WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        return $row ? $this->cast($row) : null;
    }

    public function findWithPricing(int $id): ?array
    {
        $product = $this->find($id);

        return $product ? PriceCalculator::resolve($product) : null;
    }

    public function byCategory(?int $categoryId): array
    {
        if ($categoryId === null) {
            return $this->allActive();
        }

        $stmt = $this->db->prepare("SELECT * FROM products WHERE status = 'active' AND category_id = ? ORDER BY id DESC");
        $stmt->execute([$categoryId]);

        return PriceCalculator::resolveMany(array_map([$this, 'cast'], $stmt->fetchAll()));
    }

    public function create(array $data): array
    {
        $stmt = $this->db->prepare(
            'INSERT INTO products (category_id, name, price, description, detail, stock, image, status, discount_percent, promo_active,
             ram, storage_gb, chipset, screen_size, battery_mah, os, is_preorder, preorder_deposit, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $createdAt = date('Y-m-d H:i:s');
        $stmt->execute([
            (int) $data['category_id'],
            trim($data['name']),
            (int) $data['price'],
            trim($data['description'] ?? ''),
            trim($data['detail'] ?? ''),
            (int) ($data['stock'] ?? 0),
            $data['image'] ?? '',
            $data['status'] ?? 'active',
            (int) ($data['discount_percent'] ?? 0),
            !empty($data['promo_active']) ? 1 : 0,
            $data['ram'] ?? null,
            $data['storage_gb'] ?? null,
            $data['chipset'] ?? null,
            $data['screen_size'] ?? null,
            $data['battery_mah'] ?? null,
            $data['os'] ?? null,
            !empty($data['is_preorder']) ? 1 : 0,
            (int) ($data['preorder_deposit'] ?? 0),
            $createdAt,
        ]);

        return $this->find((int) $this->db->lastInsertId());
    }

    public function searchActive(string $search = '', ?int $categoryId = null): array
    {
        $search = trim($search);
        $sql = "SELECT p.*, c.name AS category_name
                FROM products p
                LEFT JOIN categories c ON c.id = p.category_id
                WHERE p.status = 'active'";
        $params = [];

        if ($categoryId !== null) {
            $sql .= ' AND p.category_id = ?';
            $params[] = $categoryId;
        }

        if ($search !== '' && !self::isInstallmentSearch($search)) {
            $sql .= ' AND (p.name LIKE ? OR p.description LIKE ? OR p.detail LIKE ? OR c.name LIKE ?)';
            $like = '%' . $search . '%';
            $params = array_merge($params, [$like, $like, $like, $like]);
        }

        $sql .= ' ORDER BY p.id DESC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return PriceCalculator::resolveMany(array_map(function (array $row): array {
            $product = $this->cast($row);
            $product['category_name'] = $row['category_name'] ?? 'Khác';

            return $product;
        }, $stmt->fetchAll()));
    }

    public static function matchesSpecialSearch(string $search, array $product): bool
    {
        $searchLower = mb_strtolower(trim($search), 'UTF-8');
        if ($searchLower === '') {
            return true;
        }

        if (self::isInstallmentSearch($searchLower)) {
            return true;
        }

        $nameLower = mb_strtolower($product['name'] ?? '', 'UTF-8');
        $descLower = mb_strtolower($product['description'] ?? '', 'UTF-8');
        $detailLower = mb_strtolower($product['detail'] ?? '', 'UTF-8');
        $catLower = mb_strtolower($product['category_name'] ?? '', 'UTF-8');

        if ($searchLower === 'sạc nhanh' || $searchLower === 'sạc') {
            return str_contains($nameLower, 'sạc') || str_contains($nameLower, 'cáp');
        }

        if ($searchLower === 'máy cũ') {
            return str_contains($nameLower, 'cũ') || str_contains($catLower, 'cũ');
        }

        return str_contains($nameLower, $searchLower)
            || str_contains($descLower, $searchLower)
            || str_contains($detailLower, $searchLower)
            || str_contains($catLower, $searchLower);
    }

    public static function isInstallmentSearch(string $search): bool
    {
        $searchLower = mb_strtolower(trim($search), 'UTF-8');

        return str_contains($searchLower, 'trả góp')
            || str_contains($searchLower, 'tra gop');
    }

    /**
     * Giá ngắn như 3, 9 hoặc 20 được hiểu theo triệu đồng.
     * Giá đã nhập đầy đủ (ví dụ 3000000) vẫn được giữ nguyên.
     */
    public static function normalizePriceFilter(mixed $value): ?int
    {
        if ($value === null || $value === '' || !is_numeric($value)) {
            return null;
        }

        $price = (float) $value;
        if ($price > 0 && $price <= 1000) {
            $price *= 1_000_000;
        }

        return max(0, (int) round($price));
    }

    public function update(int $id, array $data): ?array
    {
        $stmt = $this->db->prepare(
            'UPDATE products SET category_id = ?, name = ?, price = ?, description = ?, detail = ?,
             stock = ?, image = ?, status = ?, discount_percent = ?, promo_active = ?,
             ram = ?, storage_gb = ?, chipset = ?, screen_size = ?, battery_mah = ?, os = ?,
             is_preorder = ?, preorder_deposit = ?
             WHERE id = ?'
        );
        $stmt->execute([
            (int) $data['category_id'],
            trim($data['name']),
            (int) $data['price'],
            trim($data['description'] ?? ''),
            trim($data['detail'] ?? ''),
            (int) ($data['stock'] ?? 0),
            $data['image'] ?? '',
            $data['status'] ?? 'active',
            (int) ($data['discount_percent'] ?? 0),
            !empty($data['promo_active']) ? 1 : 0,
            $data['ram'] ?? null,
            $data['storage_gb'] ?? null,
            $data['chipset'] ?? null,
            $data['screen_size'] ?? null,
            $data['battery_mah'] ?? null,
            $data['os'] ?? null,
            !empty($data['is_preorder']) ? 1 : 0,
            (int) ($data['preorder_deposit'] ?? 0),
            $id,
        ]);

        return $this->find($id);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM products WHERE id = ?');

        return $stmt->execute([$id]);
    }

    public function allWithCategory(?string $search = null): array
    {
        $query = 'SELECT p.*, c.name AS category_name
                  FROM products p
                  LEFT JOIN categories c ON c.id = p.category_id';
        $params = [];

        if ($search !== null && trim($search) !== '') {
            $search = trim($search);
            $query .= ' WHERE p.name LIKE ? OR c.name LIKE ? OR p.id = ?';
            $params = ["%{$search}%", "%{$search}%", (int) $search];
        }

        $query .= ' ORDER BY p.id ASC';

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);

        return array_map(function (array $row): array {
            $product = $this->cast($row);
            $product['category_name'] = $row['category_name'] ?? '—';

            return $product;
        }, $stmt->fetchAll());
    }

    private function cast(array $row): array
    {
        $row['id'] = (int) $row['id'];
        $row['category_id'] = (int) $row['category_id'];
        $row['price'] = (int) $row['price'];
        $row['stock'] = (int) $row['stock'];
        $row['discount_percent'] = (int) ($row['discount_percent'] ?? 0);
        $row['promo_active'] = (int) ($row['promo_active'] ?? 0);

        return $row;
    }
}
