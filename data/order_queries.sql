-- ============================================================
-- Shop Hades — Truy vấn sản phẩm & đơn hàng
-- Database: shop_hades
-- Chạy: mysql -u root shop_hades < data/order_queries.sql
-- ============================================================

USE shop_hades;

-- ------------------------------------------------------------
-- 1. IN DANH SÁCH TẤT CẢ SẢN PHẨM
-- ------------------------------------------------------------
SELECT
    p.id,
    p.name            AS ten_san_pham,
    c.name            AS danh_muc,
    p.price           AS don_gia,
    p.stock           AS ton_kho,
    p.image           AS hinh_anh,
    p.status          AS trang_thai,
    p.created_at
FROM products p
JOIN categories c ON c.id = p.category_id
ORDER BY p.id;

-- ------------------------------------------------------------
-- 2. CHI TIẾT ĐƠN: MỖI DÒNG LÀ 1 SẢN PHẨM, GOM THEO order_id
-- ------------------------------------------------------------
SELECT
    o.id              AS order_id,
    o.customer_name   AS khach_hang,
    o.created_at      AS ngay_dat,
    oi.product_id,
    oi.product_name   AS ten_san_pham,
    oi.quantity       AS so_luong,
    oi.price          AS don_gia,
    (oi.quantity * oi.price) AS thanh_tien
FROM orders o
INNER JOIN order_items oi ON oi.order_id = o.id
ORDER BY o.id DESC, oi.id;

-- ------------------------------------------------------------
-- 3. GỘP TẤT CẢ SP VÀO 1 DÒNG / 1 ĐƠN (theo order_id)
-- ------------------------------------------------------------
SELECT
    order_id,
    customer_name     AS khach_hang,
    payment_method    AS thanh_toan,
    payment_status    AS trang_thai_tt,
    total_amount      AS tong_don,
    item_count        AS so_dong_sp,
    total_quantity    AS tong_so_luong,
    product_list      AS danh_sach_san_pham
FROM v_order_summary
ORDER BY order_id DESC;

-- ------------------------------------------------------------
-- 4. XEM 1 ĐƠN CỤ THỂ THEO order_id
-- ------------------------------------------------------------
SET @order_id = 1;

SELECT * FROM v_order_product_lines WHERE order_id = @order_id;
SELECT * FROM v_order_summary WHERE order_id = @order_id;

-- ------------------------------------------------------------
-- 5. SẢN PHẨM TRONG 1 ĐƠN (bảng order_items)
-- ------------------------------------------------------------
SELECT
    order_id,
    product_id,
    product_name,
    quantity,
    price,
    (quantity * price) AS thanh_tien
FROM order_items
WHERE order_id = @order_id
ORDER BY id;
