-- Migration: password_reset_requests table
-- Chạy: mysql -u root shop_hades < data/migrate_password_reset.sql

USE shop_hades;

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
