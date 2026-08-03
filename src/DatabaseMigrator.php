<?php

namespace App;

class DatabaseMigrator
{
    private const SCHEMA_VERSION = 17;

    public static function run(): void
    {
        if (!self::shouldRun()) {
            return;
        }

        $flagFile = self::versionFlagFile();
        $applied = is_file($flagFile) ? (int) file_get_contents($flagFile) : 0;

        if ($applied >= self::SCHEMA_VERSION) {
            return;
        }

        if (self::isDatabaseEmpty()) {
            self::importShopHadesSql();
        }
        self::ensureSchema();
        file_put_contents($flagFile, (string) self::SCHEMA_VERSION);
    }

    private static function versionFlagFile(): string
    {
        $database = preg_replace('/[^a-z0-9_-]+/i', '_', (string) config('database.name', 'default'));
        return BASE_PATH . '/storage/.schema_version.' . ($database ?: 'default');
    }

    private static function shouldRun(): bool
    {
        try {
            return DB::isAvailable() || PHP_SAPI === 'cli';
        } catch (\Throwable) {
            return PHP_SAPI === 'cli';
        }
    }

    private static function shopHadesSqlPath(): string
    {
        $candidates = [
            dirname(BASE_PATH) . '/shop_hades.sql',
            BASE_PATH . '/data/shop_hades.sql',
        ];

        foreach ($candidates as $path) {
            if (is_file($path)) {
                return $path;
            }
        }

        throw new \RuntimeException('Missing database file: shop_hades.sql');
    }

    private static function isDatabaseEmpty(): bool
    {
        $pdo = DB::connection();

        try {
            return (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn() === 0;
        } catch (\Throwable) {
            return true;
        }
    }

    public static function importShopHadesSql(): void
    {
        $pdo = DB::connection();
        $sqlFile = self::shopHadesSqlPath();
        $sql = file_get_contents($sqlFile);

        if ($sql === false || trim($sql) === '') {
            throw new \RuntimeException('shop_hades.sql is empty or unreadable');
        }

        $sql = preg_replace('/^CREATE DATABASE.*?;/ims', '', $sql);
        $sql = preg_replace('/^USE\s+.*?;/ims', '', $sql);

        $pdo->exec('SET FOREIGN_KEY_CHECKS=0');

        foreach (array_filter(array_map('trim', explode(';', $sql))) as $statement) {
            if ($statement === '' || str_starts_with($statement, '--') || str_starts_with($statement, '/*')) {
                continue;
            }
            try {
                $pdo->exec($statement);
            } catch (\Throwable $e) {
                // Bỏ qua lỗi view/constraint khi import lại trên DB đã có cấu trúc tương tự
                if (!str_contains($e->getMessage(), 'Duplicate')
                    && !str_contains($e->getMessage(), 'already exists')) {
                    throw $e;
                }
            }
        }

        $pdo->exec('SET FOREIGN_KEY_CHECKS=1');
    }

    public static function ensureSchema(): void
    {
        $pdo = DB::connection();
        $sqlFile = BASE_PATH . '/data/install.sql';

        if (!is_file($sqlFile)) {
            throw new \RuntimeException('Missing schema file: data/install.sql');
        }

        $sql = file_get_contents($sqlFile);
        $sql = preg_replace('/^CREATE DATABASE.*?;/ims', '', $sql);
        $sql = preg_replace('/^USE\s+.*?;/ims', '', $sql);

        foreach (array_filter(array_map('trim', explode(';', $sql))) as $statement) {
            if ($statement !== '') {
                $pdo->exec($statement);
            }
        }

        self::upgradeSchema($pdo);
    }

    private static function upgradeSchema(\PDO $pdo): void
    {
        $pdo->exec("CREATE TABLE IF NOT EXISTS api_otps (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(150) NOT NULL,
            code VARCHAR(10) NOT NULL,
            action VARCHAR(50) NOT NULL,
            payload TEXT NULL,
            expires_at INT UNSIGNED NOT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            KEY api_otps_email_code (email, code)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $pdo->exec("CREATE TABLE IF NOT EXISTS product_reviews (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            product_id INT UNSIGNED NOT NULL,
            user_id INT UNSIGNED NULL,
            order_id INT UNSIGNED NULL,
            user_name VARCHAR(100) NOT NULL,
            rating TINYINT UNSIGNED NOT NULL,
            comment TEXT,
            is_verified TINYINT(1) NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            KEY reviews_product_id (product_id),
            KEY idx_reviews_user_product (user_id, product_id),
            KEY idx_reviews_order (order_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $pdo->exec("CREATE TABLE IF NOT EXISTS product_review_replies (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            review_id INT UNSIGNED NOT NULL,
            user_name VARCHAR(100) NOT NULL,
            reply_text TEXT NOT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            KEY reply_review_id (review_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $pdo->exec("CREATE TABLE IF NOT EXISTS wishlists (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id INT UNSIGNED NOT NULL,
            product_id INT UNSIGNED NOT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY uq_wishlists_user_product (user_id, product_id),
            KEY idx_wishlists_user (user_id),
            KEY idx_wishlists_product (product_id),
            CONSTRAINT fk_wishlists_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            CONSTRAINT fk_wishlists_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $pdo->exec("CREATE TABLE IF NOT EXISTS promotions (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            code VARCHAR(50) NOT NULL UNIQUE,
            discount_percent INT NOT NULL,
            status VARCHAR(20) NOT NULL DEFAULT 'active',
            expires_at DATE NOT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $pdo->exec("CREATE TABLE IF NOT EXISTS site_promotions (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(150) NOT NULL,
            discount_percent INT UNSIGNED NOT NULL,
            status VARCHAR(20) NOT NULL DEFAULT 'inactive',
            starts_at DATE NULL,
            expires_at DATE NOT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $pdo->exec("CREATE TABLE IF NOT EXISTS password_reset_requests (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id INT UNSIGNED NOT NULL,
            request_type ENUM('ADMIN_RESET', 'FORGOT_PASSWORD') NOT NULL,
            otp_hash VARCHAR(255) NULL,
            token VARCHAR(64) NULL,
            status ENUM('PENDING', 'USED', 'EXPIRED') NOT NULL DEFAULT 'PENDING',
            verify_attempts INT UNSIGNED NOT NULL DEFAULT 0,
            expired_at DATETIME NOT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            created_by ENUM('ADMIN', 'USER') NOT NULL,
            created_by_id INT UNSIGNED NULL,
            KEY prr_user_status (user_id, status),
            KEY prr_token (token),
            UNIQUE KEY prr_token_unique (token),
            CONSTRAINT prr_user_fk FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $pdo->exec("CREATE TABLE IF NOT EXISTS support_messages (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            session_id VARCHAR(64) NOT NULL,
            sender ENUM('user','admin') NOT NULL DEFAULT 'user',
            user_name VARCHAR(100) NOT NULL DEFAULT 'Khách',
            user_email VARCHAR(150) NOT NULL DEFAULT '',
            message TEXT NOT NULL,
            is_read TINYINT(1) NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            KEY sm_session (session_id),
            KEY sm_read (is_read)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $pdo->exec("CREATE TABLE IF NOT EXISTS admin_audit_logs (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            admin_id INT UNSIGNED NULL,
            admin_name VARCHAR(100) NOT NULL,
            action VARCHAR(100) NOT NULL,
            details TEXT NULL,
            ip_address VARCHAR(45) NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $pdo->exec("CREATE TABLE IF NOT EXISTS chat_status (
            session_id VARCHAR(64) PRIMARY KEY,
            is_admin_typing TINYINT(1) NOT NULL DEFAULT 0,
            is_user_typing TINYINT(1) NOT NULL DEFAULT 0,
            admin_last_activity DATETIME NULL,
            user_last_activity DATETIME NULL,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $pdo->exec("CREATE TABLE IF NOT EXISTS rate_limits (
            `key` VARCHAR(255) NOT NULL PRIMARY KEY,
            `hits` INT UNSIGNED NOT NULL DEFAULT 1,
            `expires_at` INT UNSIGNED NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        try {
            $pdo->exec("ALTER TABLE products DROP FOREIGN KEY products_category_fk");
        } catch (\Throwable $e) {}
        try {
            $pdo->exec("ALTER TABLE products ADD CONSTRAINT products_category_fk FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE");
        } catch (\Throwable $e) {}

        // Clean orphaned reviews and replies, then add cascade constraints
        try {
            $pdo->exec("DELETE FROM product_reviews WHERE product_id NOT IN (SELECT id FROM products)");
            $pdo->exec("ALTER TABLE product_reviews ADD CONSTRAINT fk_reviews_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE");
        } catch (\Throwable $e) {}

        try {
            $pdo->exec("DELETE FROM product_review_replies WHERE review_id NOT IN (SELECT id FROM product_reviews)");
            $pdo->exec("ALTER TABLE product_review_replies ADD CONSTRAINT fk_replies_review FOREIGN KEY (review_id) REFERENCES product_reviews(id) ON DELETE CASCADE");
        } catch (\Throwable $e) {}


        $columns = [
            'users' => [
                'failed_login_attempts' => 'INT UNSIGNED NOT NULL DEFAULT 0',
                'lockout_until' => 'DATETIME NULL',
                'loyalty_points' => 'INT UNSIGNED NOT NULL DEFAULT 0',
                'loyalty_tier' => "VARCHAR(30) NOT NULL DEFAULT 'Standard'",
            ],
            'orders' => [
                'vnpay_transaction_no' => 'VARCHAR(50) NULL',
                'vnpay_bank_code' => 'VARCHAR(20) NULL',
                'vnpay_pay_date' => 'VARCHAR(20) NULL',
                'coupon_code' => 'VARCHAR(50) NULL',
                'discount_amount' => 'BIGINT UNSIGNED NOT NULL DEFAULT 0',
                'shipper_id' => 'INT UNSIGNED NULL',
                'shipping_lat' => 'DECIMAL(10, 8) NULL',
                'shipping_lng' => 'DECIMAL(11, 8) NULL',
                'user_address_id' => 'INT UNSIGNED NULL',
                'loyalty_discount_amount' => 'BIGINT UNSIGNED NOT NULL DEFAULT 0',
                'loyalty_tier' => 'VARCHAR(30) NULL',
                'earned_points' => 'INT UNSIGNED NOT NULL DEFAULT 0',
                'points_awarded' => 'TINYINT(1) NOT NULL DEFAULT 0',
            ],
            'product_reviews' => [
                'likes_count' => 'INT UNSIGNED NOT NULL DEFAULT 0',
                'user_id' => 'INT UNSIGNED NULL',
                'order_id' => 'INT UNSIGNED NULL',
                'is_verified' => 'TINYINT(1) NOT NULL DEFAULT 0',
            ],
            'products' => [
                'discount_percent' => 'INT UNSIGNED NOT NULL DEFAULT 0',
                'promo_active' => 'TINYINT(1) NOT NULL DEFAULT 0',
                'ram' => 'VARCHAR(20) NULL',
                'storage_gb' => 'VARCHAR(20) NULL',
                'chipset' => 'VARCHAR(80) NULL',
                'screen_size' => 'VARCHAR(20) NULL',
                'battery_mah' => 'VARCHAR(20) NULL',
                'os' => 'VARCHAR(40) NULL',
            ],
            'order_items' => [
                'variant_id' => 'INT UNSIGNED NULL',
            ],
        ];

        foreach ($columns as $table => $defs) {
            foreach ($defs as $column => $definition) {
                if (!self::columnExists($pdo, $table, $column)) {
                    $pdo->exec("ALTER TABLE {$table} ADD COLUMN {$column} {$definition}");
                }
            }
        }

        try {
            $pdo->exec(
                'UPDATE product_reviews pr
                 LEFT JOIN users u ON u.id = pr.user_id
                 SET pr.user_id = NULL
                 WHERE pr.user_id IS NOT NULL AND u.id IS NULL'
            );
            $pdo->exec(
                'ALTER TABLE product_reviews
                 ADD CONSTRAINT fk_reviews_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL'
            );
        } catch (\Throwable $e) {}

        try {
            $pdo->exec(
                'UPDATE product_reviews pr
                 LEFT JOIN orders o ON o.id = pr.order_id
                 SET pr.order_id = NULL
                 WHERE pr.order_id IS NOT NULL AND o.id IS NULL'
            );
            $pdo->exec(
                'ALTER TABLE product_reviews
                 ADD CONSTRAINT fk_reviews_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE SET NULL'
            );
        } catch (\Throwable $e) {}

        try {
            $pdo->exec('ALTER TABLE product_reviews ADD KEY idx_reviews_user_product (user_id, product_id)');
        } catch (\Throwable $e) {}

        try {
            $pdo->exec('ALTER TABLE product_reviews ADD KEY idx_reviews_order (order_id)');
        } catch (\Throwable $e) {}

        if (!self::columnExists($pdo, 'orders', 'order_code')) {
            $pdo->exec("ALTER TABLE orders ADD COLUMN order_code VARCHAR(20) NULL");
            $stmt = $pdo->query("SELECT id, created_at FROM orders WHERE order_code IS NULL");
            $ordersList = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $updateStmt = $pdo->prepare("UPDATE orders SET order_code = ? WHERE id = ?");
            foreach ($ordersList as $order) {
                $dateStr = date('ymd', strtotime($order['created_at'] ?? 'now'));
                $randomChars = '';
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                for ($i = 0; $i < 5; $i++) {
                    $randomChars .= $chars[random_int(0, strlen($chars) - 1)];
                }
                $code = "HD{$dateStr}-{$randomChars}";
                $updateStmt->execute([$code, $order['id']]);
            }
            try {
                $pdo->exec("ALTER TABLE orders ADD UNIQUE KEY uq_orders_order_code (order_code)");
            } catch (\Throwable $e) {}
        }

        self::ensureViews($pdo);
        self::upgradePaymentMethodEnum($pdo);
        self::ensureWalletSchema($pdo);
        self::ensureIndexes($pdo);
        self::ensureTrackingSchema($pdo);
        self::ensureLogisticsSchema($pdo);
        self::ensureWarrantySchema($pdo);
        self::ensureProductVariantsSchema($pdo);
        self::ensurePhoneRetailSchema($pdo);
        self::ensurePhaseBUpgrades($pdo);
        self::ensurePhaseCUpgrades($pdo);
    }

    private static function ensurePhaseCUpgrades(\PDO $pdo): void
    {
        $pdo->exec("CREATE TABLE IF NOT EXISTS chatbot_messages (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id INT UNSIGNED NULL,
            session_key VARCHAR(64) NOT NULL,
            role ENUM('user','model') NOT NULL,
            content TEXT NOT NULL,
            source VARCHAR(30) NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            KEY idx_chatbot_user (user_id),
            KEY idx_chatbot_session (session_key),
            KEY idx_chatbot_created (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $pdo->exec("CREATE TABLE IF NOT EXISTS push_subscriptions (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id INT UNSIGNED NULL,
            endpoint TEXT NOT NULL,
            endpoint_hash CHAR(64) NOT NULL,
            p256dh VARCHAR(255) NULL,
            auth VARCHAR(255) NULL,
            user_agent VARCHAR(255) NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY uq_push_endpoint (endpoint_hash),
            KEY idx_push_user (user_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $pdo->exec("CREATE TABLE IF NOT EXISTS app_notifications (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id INT UNSIGNED NULL,
            order_id INT UNSIGNED NULL,
            title VARCHAR(200) NOT NULL,
            body TEXT NOT NULL,
            url VARCHAR(255) NULL,
            is_read TINYINT(1) NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            KEY idx_notif_user_read (user_id, is_read),
            KEY idx_notif_created (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $pdo->exec("CREATE TABLE IF NOT EXISTS job_queue (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            queue VARCHAR(50) NOT NULL DEFAULT 'default',
            job_type VARCHAR(80) NOT NULL,
            payload LONGTEXT NOT NULL,
            attempts TINYINT UNSIGNED NOT NULL DEFAULT 0,
            available_at DATETIME NOT NULL,
            reserved_at DATETIME NULL,
            completed_at DATETIME NULL,
            last_error TEXT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            KEY idx_jobs_pending (queue, completed_at, available_at),
            KEY idx_jobs_type (job_type)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    }

    private static function ensurePhaseBUpgrades(\PDO $pdo): void
    {
        // 1. Trade-in columns on promotions
        if (!self::columnExists($pdo, 'promotions', 'discount_amount')) {
            $pdo->exec("ALTER TABLE promotions ADD COLUMN discount_amount BIGINT UNSIGNED NOT NULL DEFAULT 0");
        }
        if (!self::columnExists($pdo, 'promotions', 'discount_type')) {
            $pdo->exec("ALTER TABLE promotions ADD COLUMN discount_type ENUM('percent', 'fixed') NOT NULL DEFAULT 'percent'");
        }

        // Trade-ins table
        $pdo->exec("CREATE TABLE IF NOT EXISTS trade_ins (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id INT UNSIGNED NULL,
            customer_name VARCHAR(100) NOT NULL,
            customer_phone VARCHAR(20) NOT NULL,
            device_model VARCHAR(150) NOT NULL,
            device_condition VARCHAR(50) NOT NULL,
            estimated_value BIGINT UNSIGNED NOT NULL,
            voucher_code VARCHAR(50) NULL,
            status VARCHAR(30) NOT NULL DEFAULT 'pending',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            KEY idx_trade_ins_phone (customer_phone),
            CONSTRAINT fk_trade_ins_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        // 2. Pre-order columns
        if (!self::columnExists($pdo, 'products', 'is_preorder')) {
            $pdo->exec("ALTER TABLE products ADD COLUMN is_preorder TINYINT(1) NOT NULL DEFAULT 0");
        }
        if (!self::columnExists($pdo, 'products', 'preorder_deposit')) {
            $pdo->exec("ALTER TABLE products ADD COLUMN preorder_deposit BIGINT UNSIGNED NOT NULL DEFAULT 0");
        }
        if (!self::columnExists($pdo, 'orders', 'is_preorder')) {
            $pdo->exec("ALTER TABLE orders ADD COLUMN is_preorder TINYINT(1) NOT NULL DEFAULT 0");
        }
        if (!self::columnExists($pdo, 'orders', 'preorder_deposit_paid')) {
            $pdo->exec("ALTER TABLE orders ADD COLUMN preorder_deposit_paid BIGINT UNSIGNED NOT NULL DEFAULT 0");
        }
        if (!self::columnExists($pdo, 'orders', 'preorder_status')) {
            $pdo->exec("ALTER TABLE orders ADD COLUMN preorder_status VARCHAR(30) NULL");
        }
        if (!self::columnExists($pdo, 'orders', 'stock_reserved')) {
            $pdo->exec("ALTER TABLE orders ADD COLUMN stock_reserved TINYINT(1) NOT NULL DEFAULT 0");
        }

        // 3. Warranty order_id nullability
        try {
            $pdo->exec("ALTER TABLE warranties MODIFY COLUMN order_id INT UNSIGNED NULL");
        } catch (\Throwable $e) {}

        // 6. 2FA columns for users
        if (!self::columnExists($pdo, 'users', 'two_factor_secret')) {
            $pdo->exec("ALTER TABLE users ADD COLUMN two_factor_secret VARCHAR(100) NULL");
        }
        if (!self::columnExists($pdo, 'users', 'two_factor_enabled')) {
            $pdo->exec("ALTER TABLE users ADD COLUMN two_factor_enabled TINYINT(1) NOT NULL DEFAULT 0");
        }

        // 7. VAT Invoice columns
        if (!self::columnExists($pdo, 'orders', 'vat_company_name')) {
            $pdo->exec("ALTER TABLE orders ADD COLUMN vat_company_name VARCHAR(200) NULL");
        }
        if (!self::columnExists($pdo, 'orders', 'vat_tax_code')) {
            $pdo->exec("ALTER TABLE orders ADD COLUMN vat_tax_code VARCHAR(50) NULL");
        }
        if (!self::columnExists($pdo, 'orders', 'vat_address')) {
            $pdo->exec("ALTER TABLE orders ADD COLUMN vat_address VARCHAR(255) NULL");
        }
        if (!self::columnExists($pdo, 'orders', 'vat_email')) {
            $pdo->exec("ALTER TABLE orders ADD COLUMN vat_email VARCHAR(150) NULL");
        }
        if (!self::columnExists($pdo, 'orders', 'vat_invoice_no')) {
            $pdo->exec("ALTER TABLE orders ADD COLUMN vat_invoice_no VARCHAR(50) NULL");
        }
        if (!self::columnExists($pdo, 'orders', 'vat_invoice_issued')) {
            $pdo->exec("ALTER TABLE orders ADD COLUMN vat_invoice_issued TINYINT(1) NOT NULL DEFAULT 0");
        }
        if (!self::columnExists($pdo, 'orders', 'vat_invoice_issued_at')) {
            $pdo->exec("ALTER TABLE orders ADD COLUMN vat_invoice_issued_at DATETIME NULL");
        }

        // customer email on warranties for offline activation notifications
        if (!self::columnExists($pdo, 'warranties', 'email')) {
            $pdo->exec("ALTER TABLE warranties ADD COLUMN email VARCHAR(150) NULL");
        }
    }

    private static function ensurePhoneRetailSchema(\PDO $pdo): void
    {
        $pdo->exec("CREATE TABLE IF NOT EXISTS device_units (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            imei VARCHAR(50) NOT NULL,
            product_id INT UNSIGNED NOT NULL,
            variant_id INT UNSIGNED NULL,
            store_id INT UNSIGNED NULL,
            order_id INT UNSIGNED NULL,
            status ENUM('available','reserved','sold','rma') NOT NULL DEFAULT 'available',
            note VARCHAR(255) NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY uq_device_units_imei (imei),
            KEY idx_device_units_product_status (product_id, status),
            KEY idx_device_units_store_status (store_id, status),
            KEY idx_device_units_order (order_id),
            CONSTRAINT fk_device_units_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $pdo->exec("CREATE TABLE IF NOT EXISTS store_stock (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            store_id INT UNSIGNED NOT NULL,
            product_id INT UNSIGNED NOT NULL,
            variant_id INT UNSIGNED NOT NULL DEFAULT 0,
            quantity INT UNSIGNED NOT NULL DEFAULT 0,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY uq_store_stock (store_id, product_id, variant_id),
            KEY idx_store_stock_product (product_id),
            CONSTRAINT fk_store_stock_store FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE,
            CONSTRAINT fk_store_stock_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $orderCols = [
            'delivery_proof_path' => 'VARCHAR(500) NULL',
            'shipper_assigned_at' => 'DATETIME NULL',
            'installment_months' => 'TINYINT UNSIGNED NULL',
            'installment_deposit_amount' => 'BIGINT UNSIGNED NOT NULL DEFAULT 0',
            'installment_status' => "VARCHAR(30) NULL",
        ];
        foreach ($orderCols as $column => $definition) {
            if (!self::columnExists($pdo, 'orders', $column)) {
                $pdo->exec("ALTER TABLE orders ADD COLUMN {$column} {$definition}");
            }
        }

        // Seed store_stock from global product stock for active stores (once per product/store)
        try {
            $stores = $pdo->query("SELECT id FROM stores WHERE status = 'active'")->fetchAll(\PDO::FETCH_COLUMN);
            $products = $pdo->query("SELECT id, stock FROM products")->fetchAll(\PDO::FETCH_ASSOC);
            $check = $pdo->prepare('SELECT COUNT(*) FROM store_stock WHERE store_id = ? AND product_id = ? AND variant_id = 0');
            $insert = $pdo->prepare('INSERT INTO store_stock (store_id, product_id, variant_id, quantity) VALUES (?, ?, 0, ?)');
            foreach ($stores as $storeId) {
                foreach ($products as $product) {
                    $check->execute([(int) $storeId, (int) $product['id']]);
                    if ((int) $check->fetchColumn() === 0) {
                        $qty = (int) ($product['stock'] ?? 0);
                        // Spread stock across stores: first store gets full, others get 0 until admin sets
                        $insert->execute([(int) $storeId, (int) $product['id'], $qty > 0 ? max(1, intdiv($qty, max(1, count($stores)))) : 0]);
                    }
                }
            }
        } catch (\Throwable $e) {
            // ignore seed errors on partial schemas
        }
    }

    private static function ensureProductVariantsSchema(\PDO $pdo): void
    {
        $pdo->exec("CREATE TABLE IF NOT EXISTS product_variants (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            product_id INT UNSIGNED NOT NULL,
            color VARCHAR(100) NOT NULL,
            storage VARCHAR(50) NOT NULL,
            price BIGINT UNSIGNED NOT NULL DEFAULT 0,
            stock INT UNSIGNED NOT NULL DEFAULT 0,
            image VARCHAR(500) DEFAULT '',
            status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            KEY idx_variants_product (product_id),
            CONSTRAINT fk_variants_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    }

    private static function ensureWarrantySchema(\PDO $pdo): void
    {
        $pdo->exec("CREATE TABLE IF NOT EXISTS warranties (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            order_id INT UNSIGNED NOT NULL,
            product_id INT UNSIGNED NOT NULL,
            customer_name VARCHAR(100) NOT NULL,
            customer_phone VARCHAR(20) NOT NULL,
            imei VARCHAR(50) NOT NULL UNIQUE,
            warranty_code VARCHAR(30) NOT NULL UNIQUE,
            status VARCHAR(20) NOT NULL DEFAULT 'active',
            started_at DATETIME NOT NULL,
            expired_at DATETIME NOT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            KEY idx_warranties_phone (customer_phone),
            KEY idx_warranties_imei (imei),
            CONSTRAINT fk_warranties_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
            CONSTRAINT fk_warranties_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $pdo->exec("CREATE TABLE IF NOT EXISTS warranty_claims (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            warranty_id INT UNSIGNED NOT NULL,
            customer_note TEXT NOT NULL,
            admin_note TEXT NULL,
            status VARCHAR(30) NOT NULL DEFAULT 'pending',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            CONSTRAINT fk_claims_warranty FOREIGN KEY (warranty_id) REFERENCES warranties(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    }

    private static function ensureLogisticsSchema(\PDO $pdo): void
    {
        $pdo->exec("CREATE TABLE IF NOT EXISTS stores (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(120) NOT NULL,
            address TEXT NOT NULL,
            lat DECIMAL(10, 8) NOT NULL,
            lng DECIMAL(11, 8) NOT NULL,
            phone VARCHAR(20) NULL,
            opening_hours VARCHAR(120) NULL,
            service_radius_km DECIMAL(6, 2) NOT NULL DEFAULT 12.00,
            status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $orderCols = [
            'store_id' => 'INT UNSIGNED NULL',
            'address_province' => 'VARCHAR(100) NULL',
            'address_district' => 'VARCHAR(100) NULL',
            'address_ward' => 'VARCHAR(100) NULL',
            'address_street' => 'VARCHAR(255) NULL',
            'preferred_time_slot' => 'VARCHAR(30) NULL',
            'shipping_fee' => 'BIGINT UNSIGNED NOT NULL DEFAULT 0',
            'shipping_distance_km' => 'DECIMAL(8, 2) NULL',
            'estimated_delivery_from' => 'DATE NULL',
            'estimated_delivery_to' => 'DATE NULL',
        ];

        foreach ($orderCols as $column => $definition) {
            if (!self::columnExists($pdo, 'orders', $column)) {
                $pdo->exec("ALTER TABLE orders ADD COLUMN {$column} {$definition}");
            }
        }

        try {
            $pdo->exec("CREATE INDEX idx_orders_store_id ON orders(store_id)");
        } catch (\Throwable $e) {}

        $stores = [
            ['Trụ sở chính', 'Võng Thị, Tây Hồ, Hà Nội', 21.05440, 105.81180, '1800 1234', '08:00-21:30', 25, 'active'],
            ['Cơ sở 2', 'Số 3 Duy Tân, Cầu Giấy, Hà Nội', 21.02940, 105.78250, '1800 1234', '08:00-21:30', 25, 'active'],
            ['Cơ sở 3', 'Lê Duẩn, Hải Châu, Đà Nẵng', 16.05940, 108.22080, '1800 1234', '08:00-21:30', 25, 'active'],
            ['Cơ sở 4', 'Số 99 Nguyễn Huệ, Quận 1, TP. HCM', 10.77560, 106.70190, '1800 1234', '08:00-21:30', 25, 'active'],
            ['Cơ sở 5', 'Số 250 CMT8, Q.3, Sài Gòn', 10.78180, 106.67980, '1800 1234', '08:00-21:30', 25, 'active'],
        ];

        $pdo->exec("UPDATE stores SET status = 'inactive' WHERE name NOT IN ('Trụ sở chính', 'Cơ sở 2', 'Cơ sở 3', 'Cơ sở 4', 'Cơ sở 5')");
        $stmt = $pdo->prepare("
            INSERT INTO stores (name, address, lat, lng, phone, opening_hours, service_radius_km, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                address = VALUES(address),
                lat = VALUES(lat),
                lng = VALUES(lng),
                phone = VALUES(phone),
                opening_hours = VALUES(opening_hours),
                service_radius_km = VALUES(service_radius_km),
                status = VALUES(status)
        ");
        try {
            $pdo->exec("ALTER TABLE stores ADD UNIQUE KEY uq_stores_name (name)");
        } catch (\Throwable $e) {}
        foreach ($stores as $store) {
            $stmt->execute($store);
        }
    }

    private static function ensureTrackingSchema(\PDO $pdo): void
    {
        try {
            $pdo->exec("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'customer', 'shipper') NOT NULL DEFAULT 'customer'");
        } catch (\Throwable $e) {}

        $sqlFile = BASE_PATH . '/data/migrate_tracking.sql';
        if (is_file($sqlFile)) {
            self::execSqlStatements($pdo, file_get_contents($sqlFile));
        }
    }

    private static function ensureWalletSchema(\PDO $pdo): void
    {
        $sqlFile = BASE_PATH . '/data/migrate_wallet.sql';
        if (is_file($sqlFile)) {
            self::execSqlStatements($pdo, file_get_contents($sqlFile));
        }

        if (!self::tableExists($pdo, 'user_wallets')) {
            $pdo->exec("CREATE TABLE IF NOT EXISTS user_wallets (
                user_id    INT UNSIGNED NOT NULL PRIMARY KEY,
                balance    BIGINT UNSIGNED NOT NULL DEFAULT 0,
                updated_at DATETIME NULL,
                CONSTRAINT user_wallets_user_fk FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        }

        $orderCols = [
            'wallet_amount'      => 'BIGINT UNSIGNED NOT NULL DEFAULT 0',
            'user_voucher_code'  => 'VARCHAR(30) NULL',
        ];
        foreach ($orderCols as $column => $definition) {
            if (!self::columnExists($pdo, 'orders', $column)) {
                $pdo->exec("ALTER TABLE orders ADD COLUMN {$column} {$definition}");
            }
        }

        try {
            $pdo->exec("ALTER TABLE orders MODIFY payment_status ENUM('pending', 'paid', 'refunded') NOT NULL DEFAULT 'pending'");
        } catch (\Throwable) {
        }
    }

    private static function ensureIndexes(\PDO $pdo): void
    {
        try {
            $pdo->exec("CREATE INDEX idx_orders_user_id ON orders(user_id)");
        } catch (\Throwable $e) {}
        try {
            $pdo->exec("CREATE INDEX idx_support_messages_session_id ON support_messages(session_id)");
        } catch (\Throwable $e) {}
        try {
            $pdo->exec("CREATE INDEX idx_products_category_id ON products(category_id)");
        } catch (\Throwable $e) {}
    }


    private static function upgradePaymentMethodEnum(\PDO $pdo): void
    {
        try {
            $pdo->exec("ALTER TABLE orders MODIFY payment_method ENUM('cash', 'vnpay', 'momo', 'installment', 'bank_transfer') NOT NULL DEFAULT 'cash'");
        } catch (\Throwable) {
            // Column may already include values
        }
    }

    private static function ensureViews(\PDO $pdo): void
    {
        $views = [
            'v_order_product_lines' => <<<'SQL'
CREATE OR REPLACE VIEW v_order_product_lines AS
SELECT
    o.id AS order_id,
    o.order_code,
    o.customer_name,
    o.email,
    o.phone,
    o.payment_method,
    o.payment_status,
    o.total_amount AS order_total,
    o.created_at AS order_date,
    oi.id AS line_id,
    oi.product_id,
    oi.product_name,
    oi.quantity,
    oi.price AS unit_price,
    (oi.quantity * oi.price) AS line_total,
    p.image AS product_image,
    p.stock AS product_stock,
    c.name AS category_name
FROM orders o
INNER JOIN order_items oi ON oi.order_id = o.id
LEFT JOIN products p ON p.id = oi.product_id
LEFT JOIN categories c ON c.id = p.category_id
SQL,
            'v_order_summary' => <<<'SQL'
CREATE OR REPLACE VIEW v_order_summary AS
SELECT
    o.id AS order_id,
    o.order_code,
    o.customer_name,
    o.email,
    o.phone,
    o.payment_method,
    o.payment_status,
    o.total_amount,
    o.created_at,
    COUNT(oi.id) AS item_count,
    COALESCE(SUM(oi.quantity), 0) AS total_quantity,
    GROUP_CONCAT(
        CONCAT(oi.product_name, ' ×', oi.quantity, ' @ ', FORMAT(oi.price, 0), 'đ')
        ORDER BY oi.id
        SEPARATOR ' | '
    ) AS product_list
FROM orders o
LEFT JOIN order_items oi ON oi.order_id = o.id
GROUP BY o.id, o.order_code, o.customer_name, o.email, o.phone, o.payment_method,
         o.payment_status, o.total_amount, o.created_at
SQL,
        ];

        foreach ($views as $sql) {
            $pdo->exec($sql);
        }
    }

    private static function columnExists(\PDO $pdo, string $table, string $column): bool
    {
        $stmt = $pdo->prepare(
            'SELECT COUNT(*) FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?'
        );
        $stmt->execute([$table, $column]);

        return (int) $stmt->fetchColumn() > 0;
    }

    private static function tableExists(\PDO $pdo, string $table): bool
    {
        $stmt = $pdo->prepare(
            'SELECT COUNT(*) FROM information_schema.TABLES
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?'
        );
        $stmt->execute([$table]);

        return (int) $stmt->fetchColumn() > 0;
    }

    private static function stripSqlComments(string $sql): string
    {
        $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);
        $lines = explode("\n", $sql);

        return implode("\n", array_map(
            static fn (string $line) => preg_replace('/--.*$/', '', $line) ?? '',
            $lines
        ));
    }

    private static function execSqlStatements(\PDO $pdo, string $sql): void
    {
        $sql = self::stripSqlComments($sql);
        foreach (array_filter(array_map('trim', explode(';', $sql))) as $statement) {
            if ($statement === '') {
                continue;
            }
            try {
                $pdo->exec($statement);
            } catch (\Throwable $e) {
                if (!str_contains($e->getMessage(), 'already exists')
                    && !str_contains($e->getMessage(), 'Duplicate')) {
                    // ignore on re-run
                }
            }
        }
    }
}
