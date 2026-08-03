-- Migration Chương 5 — Shop Hades
-- Chạy trên database shop_hades đã tồn tại (phpMyAdmin hoặc mysql CLI)
-- mysql -u root shop_hades < data/migrate_ch5.sql

USE shop_hades;

-- 1. Thêm phương thức thanh toán MoMo vào bảng orders
ALTER TABLE orders
    MODIFY payment_method ENUM('cash', 'vnpay', 'momo') NOT NULL DEFAULT 'cash';

-- 2. Bảng OTP cho REST API (nếu chưa có — app cũng tự tạo khi khởi động)
CREATE TABLE IF NOT EXISTS api_otps (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(150) NOT NULL,
    code VARCHAR(10) NOT NULL,
    action VARCHAR(50) NOT NULL,
    payload TEXT NULL,
    expires_at INT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY api_otps_email_code (email, code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. (Tuỳ chọn) Cột MoMo trên orders — app hiện dùng payment_method + payment_status
-- ALTER TABLE orders ADD COLUMN momo_order_id VARCHAR(100) NULL AFTER vnpay_pay_date;
