-- Shop Hades - Order Tracking & Shipping Migration

CREATE TABLE IF NOT EXISTS order_status_logs (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id    INT UNSIGNED NOT NULL,
    from_status VARCHAR(30) NULL,
    to_status   VARCHAR(30) NOT NULL,
    actor_type  ENUM('admin', 'shipper', 'system', 'customer') NOT NULL,
    actor_id    INT UNSIGNED NULL,
    note        VARCHAR(500) NULL,
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_order_created (order_id, created_at),
    CONSTRAINT order_status_logs_fk FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS user_addresses (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id      INT UNSIGNED NOT NULL,
    label        VARCHAR(50) NULL,          -- "Nhà", "Công ty"
    recipient    VARCHAR(100) NOT NULL,
    phone        VARCHAR(20) NOT NULL,
    address_line TEXT NOT NULL,
    ward         VARCHAR(100) NULL,
    district     VARCHAR(100) NULL,
    city         VARCHAR(100) NULL,
    latitude     DECIMAL(10, 8) NULL,
    longitude    DECIMAL(11, 8) NULL,
    is_default   TINYINT(1) NOT NULL DEFAULT 0,
    created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_user (user_id),
    CONSTRAINT user_addresses_user_fk FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS warehouses (
    id        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name      VARCHAR(100) NOT NULL,
    latitude  DECIMAL(10, 8) NOT NULL,
    longitude DECIMAL(11, 8) NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS shipper_locations (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    shipper_id  INT UNSIGNED NOT NULL,
    order_id    INT UNSIGNED NULL,       -- đơn đang giao (nếu có)
    latitude    DECIMAL(10, 8) NOT NULL,
    longitude   DECIMAL(11, 8) NOT NULL,
    accuracy    FLOAT NULL,
    recorded_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_shipper_time (shipper_id, recorded_at DESC),
    KEY idx_order_time (order_id, recorded_at DESC),
    CONSTRAINT shipper_locations_shipper_fk FOREIGN KEY (shipper_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed default warehouse if not exists
INSERT INTO warehouses (name, latitude, longitude, is_active)
SELECT 'Kho chính Hà Nội', 21.0285, 105.8542, 1
FROM dual
WHERE NOT EXISTS (SELECT 1 FROM warehouses WHERE name = 'Kho chính Hà Nội');
