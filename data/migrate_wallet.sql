-- Ví Hades & hoàn tiền (Tuần 2 — Ngày 8–9)

CREATE TABLE IF NOT EXISTS user_wallets (
    user_id    INT UNSIGNED NOT NULL PRIMARY KEY,
    balance    BIGINT UNSIGNED NOT NULL DEFAULT 0,
    updated_at DATETIME NULL,
    CONSTRAINT user_wallets_user_fk FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS wallet_transactions (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id    INT UNSIGNED NOT NULL,
    type       ENUM('credit', 'debit') NOT NULL,
    amount     BIGINT UNSIGNED NOT NULL,
    ref_type   VARCHAR(50) NULL,
    ref_id     INT UNSIGNED NULL,
    note       VARCHAR(255) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY wallet_tx_user_id (user_id),
    KEY wallet_tx_created_at (created_at),
    CONSTRAINT wallet_tx_user_fk FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS user_vouchers (
    id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id           INT UNSIGNED NOT NULL,
    code              VARCHAR(30) NOT NULL,
    discount_percent  INT UNSIGNED NOT NULL DEFAULT 10,
    max_discount      BIGINT UNSIGNED NULL,
    expires_at        DATE NOT NULL,
    status            ENUM('active', 'used', 'expired') NOT NULL DEFAULT 'active',
    source_order_id   INT UNSIGNED NULL,
    used_order_id     INT UNSIGNED NULL,
    created_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_user_vouchers_code (code),
    KEY user_vouchers_user_id (user_id),
    KEY user_vouchers_status (status),
    CONSTRAINT user_vouchers_user_fk FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS refund_requests (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id     INT UNSIGNED NOT NULL,
    user_id      INT UNSIGNED NULL,
    method       ENUM('wallet', 'voucher', 'original') NOT NULL DEFAULT 'wallet',
    amount       BIGINT UNSIGNED NOT NULL,
    status       ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
    admin_note   VARCHAR(500) NULL,
    created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    processed_at DATETIME NULL,
    KEY refund_requests_order_id (order_id),
    KEY refund_requests_status (status),
    CONSTRAINT refund_requests_order_fk FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
