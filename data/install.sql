-- Shop Hades — MySQL schema
-- Chạy: mysql -u root < data/install.sql
-- Hoặc để app tự tạo khi khởi động (DatabaseMigrator)

CREATE DATABASE IF NOT EXISTS shop_hades
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE shop_hades;

CREATE TABLE IF NOT EXISTS users (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    email       VARCHAR(150) NOT NULL,
    phone       VARCHAR(20)  NOT NULL DEFAULT '',
    password    VARCHAR(255) NOT NULL,
    role        ENUM('admin', 'customer') NOT NULL DEFAULT 'customer',
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY users_email_unique (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS categories (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(150) NOT NULL,
    description TEXT,
    status      ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS products (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id INT UNSIGNED NOT NULL,
    name        VARCHAR(200) NOT NULL,
    price       BIGINT UNSIGNED NOT NULL DEFAULT 0,
    discount_percent INT UNSIGNED NOT NULL DEFAULT 0,
    promo_active TINYINT(1) NOT NULL DEFAULT 0,
    description TEXT,
    detail      TEXT,
    stock       INT UNSIGNED NOT NULL DEFAULT 0,
    image       VARCHAR(500) NOT NULL DEFAULT '',
    status      ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY products_category_id (category_id),
    CONSTRAINT products_category_fk FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS orders (
    id                   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id              INT UNSIGNED NULL,
    customer_name        VARCHAR(100) NOT NULL,
    phone                VARCHAR(20)  NOT NULL DEFAULT '',
    email                VARCHAR(150) NOT NULL,
    contact_address      TEXT,
    shipping_address     TEXT,
    payment_method       ENUM('cash', 'vnpay', 'momo') NOT NULL DEFAULT 'cash',
    payment_status       ENUM('pending', 'paid') NOT NULL DEFAULT 'pending',
    total_amount         BIGINT UNSIGNED NOT NULL DEFAULT 0,
    status               VARCHAR(30) NOT NULL DEFAULT 'pending',
    stock_reserved       TINYINT(1) NOT NULL DEFAULT 0,
    vnpay_txn_ref        VARCHAR(100) NULL,
    vnpay_transaction_no VARCHAR(50)  NULL,
    vnpay_bank_code      VARCHAR(20)  NULL,
    vnpay_pay_date       VARCHAR(20)  NULL,
    coupon_code          VARCHAR(50) NULL,
    discount_amount      BIGINT UNSIGNED NOT NULL DEFAULT 0,
    created_at           DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY orders_user_id (user_id),
    KEY orders_payment_status (payment_status),
    KEY orders_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS order_items (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id     INT UNSIGNED NOT NULL,
    product_id   INT UNSIGNED NOT NULL,
    product_name VARCHAR(200) NOT NULL,
    quantity     INT UNSIGNED NOT NULL DEFAULT 1,
    price        BIGINT UNSIGNED NOT NULL DEFAULT 0,
    KEY order_items_order_id (order_id),
    KEY order_items_product_id (product_id),
    CONSTRAINT order_items_order_fk FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    CONSTRAINT order_items_product_fk FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS promotions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    discount_percent INT NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'active',
    expires_at DATE NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS product_reviews (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id  INT UNSIGNED NOT NULL,
    user_id     INT UNSIGNED NULL,
    order_id    INT UNSIGNED NULL,
    user_name   VARCHAR(100) NOT NULL,
    rating      TINYINT UNSIGNED NOT NULL,
    comment     TEXT,
    likes_count INT UNSIGNED NOT NULL DEFAULT 0,
    is_verified TINYINT(1) NOT NULL DEFAULT 0,
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY reviews_product_id (product_id),
    KEY idx_reviews_user_product (user_id, product_id),
    KEY idx_reviews_order (order_id),
    CONSTRAINT fk_reviews_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    CONSTRAINT fk_reviews_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT fk_reviews_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS wishlists (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id    INT UNSIGNED NOT NULL,
    product_id INT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_wishlists_user_product (user_id, product_id),
    KEY idx_wishlists_user (user_id),
    KEY idx_wishlists_product (product_id),
    CONSTRAINT fk_wishlists_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_wishlists_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS site_promotions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    discount_percent INT UNSIGNED NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'inactive',
    starts_at DATE NULL,
    expires_at DATE NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS password_reset_requests (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id         INT UNSIGNED NOT NULL,
    request_type    ENUM('ADMIN_RESET', 'FORGOT_PASSWORD') NOT NULL,
    otp_hash        VARCHAR(255) NULL,
    token           VARCHAR(64) NULL,
    status          ENUM('PENDING', 'USED', 'EXPIRED') NOT NULL DEFAULT 'PENDING',
    verify_attempts INT UNSIGNED NOT NULL DEFAULT 0,
    expired_at      DATETIME NOT NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_by      ENUM('ADMIN', 'USER') NOT NULL,
    created_by_id   INT UNSIGNED NULL,
    KEY prr_user_status (user_id, status),
    KEY prr_token (token),
    UNIQUE KEY prr_token_unique (token),
    CONSTRAINT prr_user_fk FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- VIEW: Chi tiết từng dòng SP trong đơn (1 dòng = 1 SP thuộc 1 order_id)
-- ============================================================
CREATE OR REPLACE VIEW v_order_product_lines AS
SELECT
    o.id              AS order_id,
    o.customer_name,
    o.email,
    o.phone,
    o.payment_method,
    o.payment_status,
    o.total_amount    AS order_total,
    o.created_at      AS order_date,
    oi.id             AS line_id,
    oi.product_id,
    oi.product_name,
    oi.quantity,
    oi.price          AS unit_price,
    (oi.quantity * oi.price) AS line_total,
    p.image           AS product_image,
    p.stock           AS product_stock,
    c.name            AS category_name
FROM orders o
INNER JOIN order_items oi ON oi.order_id = o.id
LEFT JOIN products p ON p.id = oi.product_id
LEFT JOIN categories c ON c.id = p.category_id
ORDER BY o.id DESC, oi.id ASC;

-- ============================================================
-- VIEW: Gộp tất cả SP của 1 đơn vào 1 dòng (theo order_id)
-- ============================================================
CREATE OR REPLACE VIEW v_order_summary AS
SELECT
    o.id              AS order_id,
    o.customer_name,
    o.email,
    o.phone,
    o.payment_method,
    o.payment_status,
    o.total_amount,
    o.created_at,
    COUNT(oi.id)                    AS item_count,
    COALESCE(SUM(oi.quantity), 0) AS total_quantity,
    GROUP_CONCAT(
        CONCAT(oi.product_name, ' ×', oi.quantity, ' @ ', FORMAT(oi.price, 0), 'đ')
        ORDER BY oi.id
        SEPARATOR ' | '
    ) AS product_list
FROM orders o
LEFT JOIN order_items oi ON oi.order_id = o.id
GROUP BY o.id, o.customer_name, o.email, o.phone, o.payment_method,
         o.payment_status, o.total_amount, o.created_at
ORDER BY o.id DESC;
