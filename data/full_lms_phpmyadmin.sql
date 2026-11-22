-- ========================================================
-- Hades Academy LMS - Clean Complete Database Export
-- ========================================================

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `admin_audit_logs`;
CREATE TABLE `admin_audit_logs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` int unsigned DEFAULT NULL,
  `admin_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `action` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `details` text COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `admin_audit_logs` (`id`, `admin_id`, `admin_name`, `action`, `details`, `ip_address`, `created_at`) VALUES
('1', '1', 'Admin', 'update_trade_in_status', 'Cập nhật yêu cầu Trade-in #1 sang trạng thái: completed', NULL, '2026-07-10 20:38:10');

DROP TABLE IF EXISTS `api_otps`;
CREATE TABLE `api_otps` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `action` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` text COLLATE utf8mb4_unicode_ci,
  `expires_at` int unsigned NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `api_otps_email_code` (`email`,`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `app_notifications`;
CREATE TABLE `app_notifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned DEFAULT NULL,
  `order_id` int unsigned DEFAULT NULL,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `body` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_notif_user_read` (`user_id`,`is_read`),
  KEY `idx_notif_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `cart_items`;
CREATE TABLE `cart_items` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `session_id` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int unsigned DEFAULT NULL,
  `cart_key` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_id` int unsigned NOT NULL,
  `variant_id` int unsigned DEFAULT '0',
  `quantity` int unsigned NOT NULL DEFAULT '1',
  `color` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'Tiêu chuẩn',
  `price` bigint unsigned NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_session_id` (`session_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `categories` (`id`, `name`, `description`, `status`, `created_at`) VALUES
('1', 'Điện thoại', 'Điện thoại di động chính hãng, trả góp 0%', 'active', '2026-06-22 00:43:56'),
('2', 'Laptop', 'Máy tính xách tay từ các thương hiệu hàng đầu', 'active', '2026-06-22 00:43:56'),
('3', 'Phụ kiện', 'Tai nghe, cáp sạc, sạc dự phòng chất lượng cao', 'active', '2026-06-22 00:43:56'),
('4', 'Smartwatch', 'Đồng hồ thông minh theo dõi sức khỏe', 'active', '2026-06-22 00:43:56'),
('5', 'Đồng hồ', 'Đồng hồ đeo tay Casio lịch lãm, cá tính', 'active', '2026-06-22 00:43:56'),
('6', 'Máy cũ', 'Điện thoại, Laptop đã qua sử dụng, bảo hành uy tín', 'active', '2026-06-22 00:43:56'),
('7', 'Tablet', 'Máy tính bảng Apple iPad giải trí, làm việc tiện lợi', 'active', '2026-06-22 00:43:56');

DROP TABLE IF EXISTS `chat_status`;
CREATE TABLE `chat_status` (
  `session_id` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_admin_typing` tinyint(1) NOT NULL DEFAULT '0',
  `is_user_typing` tinyint(1) NOT NULL DEFAULT '0',
  `admin_last_activity` datetime DEFAULT NULL,
  `user_last_activity` datetime DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `chat_status` (`session_id`, `is_admin_typing`, `is_user_typing`, `admin_last_activity`, `user_last_activity`, `updated_at`) VALUES
('4514baa5121baad24582da5d7a2d47c9', '0', '0', '2026-07-10 20:17:06', '2026-07-10 20:17:01', '2026-07-10 20:17:06');

DROP TABLE IF EXISTS `chatbot_messages`;
CREATE TABLE `chatbot_messages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned DEFAULT NULL,
  `session_key` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('user','model') COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `source` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_chatbot_user` (`user_id`),
  KEY `idx_chatbot_session` (`session_key`),
  KEY `idx_chatbot_created` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `chatbot_messages` (`id`, `user_id`, `session_key`, `role`, `content`, `source`, `created_at`) VALUES
('1', NULL, '2131ec433d5e3cd661277b7daae79082', 'user', 'xin chào iPhone nào dưới 15 triệu', 'fallback', '2026-07-10 20:09:42'),
('2', NULL, '2131ec433d5e3cd661277b7daae79082', 'model', '😅 Tiếc quá, hiện tại cửa hàng chưa có sản phẩm phù hợp với khoảng giá bạn yêu cầu. Bạn thử điều chỉnh khoảng giá hoặc hỏi về dòng sản phẩm khác nhé!', 'fallback', '2026-07-10 20:09:42'),
('3', NULL, '450846acc4ff79ea3701988d64113fe8', 'user', 'xin chào', 'gemini', '2026-11-22 15:32:47'),
('4', NULL, '450846acc4ff79ea3701988d64113fe8', 'model', 'Chào buổi tối bạn! 😊 \n\nRất vui được hỗ trợ bạn tại **Shop Hades**. Bạn đang quan tâm đến sản phẩm nào hay cần mình tư vấn thông tin gì không ạ?', 'gemini', '2026-11-22 15:32:47'),
('5', NULL, '450846acc4ff79ea3701988d64113fe8', 'user', 'cho tôi biết bạn đang sử dụng model nào', 'error', '2026-11-22 15:33:10'),
('6', NULL, '450846acc4ff79ea3701988d64113fe8', 'model', 'Rất tiếc, Hades AI hiện không thể kết nối tới máy chủ AI. Vui lòng kiểm tra lại cấu hình API Key hoặc thử lại sau giây lát! 📞 Hotline hỗ trợ: 1800 1234', 'error', '2026-11-22 15:33:10'),
('7', NULL, '450846acc4ff79ea3701988d64113fe8', 'user', '1+1 = ?', 'gemini', '2026-11-22 15:33:27'),
('8', NULL, '450846acc4ff79ea3701988d64113fe8', 'model', '1 + 1 = 2 ạ! 😄 \n\nBạn có cần mình hỗ trợ gì thêm về thông tin hay sản phẩm nào tại Shop Hades không nè?', 'gemini', '2026-11-22 15:33:27');

DROP TABLE IF EXISTS `device_units`;
CREATE TABLE `device_units` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `imei` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_id` int unsigned NOT NULL,
  `variant_id` int unsigned DEFAULT NULL,
  `store_id` int unsigned DEFAULT NULL,
  `order_id` int unsigned DEFAULT NULL,
  `status` enum('available','reserved','sold','rma') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'available',
  `note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_device_units_imei` (`imei`),
  KEY `idx_device_units_product_status` (`product_id`,`status`),
  KEY `idx_device_units_store_status` (`store_id`,`status`),
  KEY `idx_device_units_order` (`order_id`),
  CONSTRAINT `fk_device_units_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `device_units` (`id`, `imei`, `product_id`, `variant_id`, `store_id`, `order_id`, `status`, `note`, `created_at`, `updated_at`) VALUES
('1', '121313234234234', '15', NULL, '2', NULL, 'available', NULL, '2026-07-10 20:01:24', '2026-07-10 20:01:24');

DROP TABLE IF EXISTS `job_queue`;
CREATE TABLE `job_queue` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'default',
  `job_type` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL DEFAULT '0',
  `available_at` datetime NOT NULL,
  `reserved_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `last_error` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_jobs_pending` (`queue`,`completed_at`,`available_at`),
  KEY `idx_jobs_type` (`job_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `order_items`;
CREATE TABLE `order_items` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int unsigned NOT NULL,
  `product_id` int unsigned NOT NULL,
  `product_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int unsigned NOT NULL DEFAULT '1',
  `price` bigint unsigned NOT NULL DEFAULT '0',
  `variant_id` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_items_order_id` (`order_id`),
  KEY `order_items_product_id` (`product_id`),
  CONSTRAINT `order_items_order_fk` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_product_fk` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `quantity`, `price`, `variant_id`) VALUES
('1', '1', '1', 'iPhone 15', '1', '22000000', NULL),
('2', '1', '6', 'Tai nghe Bluetooth', '1', '5500000', NULL),
('3', '2', '2', 'iPhone 17', '3', '28990000', NULL),
('4', '2', '6', 'Tai nghe Bluetooth', '1', '5500000', NULL),
('5', '3', '2', 'iPhone 17', '1', '28990000', NULL),
('6', '4', '2', 'iPhone 17', '1', '28990000', NULL),
('7', '4', '1', 'iPhone 15', '1', '22000000', NULL),
('8', '5', '2', 'iPhone 17', '1', '28990000', NULL),
('9', '6', '7', 'Dây sạc nhanh USB-C', '1', '450000', NULL),
('10', '7', '2', 'iPhone 17', '1', '28990000', NULL),
('11', '8', '11', 'Cáp sạc nhanh USB-C bọc dù 60W', '1', '190000', NULL),
('12', '9', '33', 'iPhone 15 Pro Test', '1', '25000000', NULL),
('13', '10', '34', 'iPhone 15 Pro Test', '1', '25000000', NULL),
('14', '11', '35', 'iPhone 15 Pro Test', '1', '25000000', NULL),
('15', '12', '36', 'iPhone 15 Pro Test', '1', '25000000', NULL),
('16', '13', '37', 'iPhone 15 Pro Test', '1', '25000000', NULL),
('17', '14', '39', 'iPhone 15 Pro Test', '1', '25000000', NULL),
('18', '15', '41', 'iPhone 15 Pro Test', '1', '25000000', NULL),
('19', '16', '43', 'iPhone 15 Pro Test', '1', '25000000', NULL),
('20', '17', '45', 'iPhone 15 Pro Test', '1', '25000000', NULL),
('21', '18', '47', 'iPhone 15 Pro Test', '1', '25000000', NULL),
('22', '19', '49', 'iPhone 15 Pro Test', '1', '25000000', NULL),
('23', '20', '51', 'iPhone 15 Pro Test', '1', '25000000', NULL),
('26', '23', '55', 'iPhone 15 Pro Test', '1', '25000000', NULL),
('29', '26', '59', 'iPhone 15 Pro Test', '1', '25000000', NULL),
('32', '29', '63', 'iPhone 15 Pro Test', '1', '25000000', NULL),
('35', '32', '67', 'iPhone 15 Pro Test', '1', '25000000', NULL),
('38', '35', '71', 'iPhone 15 Pro Test', '1', '25000000', NULL),
('41', '38', '75', 'iPhone 15 Pro Test', '1', '25000000', NULL),
('44', '41', '79', 'iPhone 15 Pro Test', '1', '25000000', NULL),
('47', '44', '83', 'iPhone 15 Pro Test', '1', '25000000', NULL),
('48', '45', '30', 'iPad Gen 10 64GB WiFi', '1', '9490000', NULL),
('49', '46', '27', 'iPad Air 5 M1 Cũ 64GB WiFi', '1', '11490000', NULL),
('50', '47', '28', 'iPad Pro M4 11 inch 256GB', '1', '25990000', NULL),
('51', '47', '26', 'Dell XPS 13 Cũ Giá Tốt', '1', '18900000', NULL),
('52', '47', '29', 'iPad Air 6 M2 2026', '1', '16490000', NULL),
('53', '47', '24', 'iPhone 15 Pro 128GB Cũ Đẹp', '1', '21490000', NULL),
('54', '47', '8', 'ThinkPad X1 Carbon Gen 13', '60', '45990000', NULL),
('55', '48', '8', 'ThinkPad X1 Carbon Gen 13', '30', '45990000', NULL),
('56', '49', '84', 'Ô tô nè', '1', '999999999999999', NULL);

DROP TABLE IF EXISTS `order_status_logs`;
CREATE TABLE `order_status_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int unsigned NOT NULL,
  `from_status` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `to_status` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `actor_type` enum('admin','shipper','system','customer') COLLATE utf8mb4_unicode_ci NOT NULL,
  `actor_id` int unsigned DEFAULT NULL,
  `note` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_order_created` (`order_id`,`created_at`),
  CONSTRAINT `order_status_logs_fk` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=95 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `order_status_logs` (`id`, `order_id`, `from_status`, `to_status`, `actor_type`, `actor_id`, `note`, `created_at`) VALUES
('1', '9', 'pending', 'confirmed', 'system', NULL, NULL, '2026-07-10 04:22:01'),
('2', '9', 'confirmed', 'packing', 'system', NULL, NULL, '2026-07-10 04:22:01'),
('3', '9', 'packing', 'shipping', 'system', NULL, NULL, '2026-07-10 04:22:01'),
('4', '9', 'shipping', 'completed', 'system', NULL, NULL, '2026-07-10 04:22:02'),
('5', '10', 'pending', 'confirmed', 'system', NULL, NULL, '2026-07-10 04:24:55'),
('6', '10', 'confirmed', 'packing', 'system', NULL, NULL, '2026-07-10 04:24:55'),
('7', '10', 'packing', 'shipping', 'system', NULL, NULL, '2026-07-10 04:24:55'),
('8', '10', 'shipping', 'completed', 'system', NULL, NULL, '2026-07-10 04:24:55'),
('9', '11', 'pending', 'confirmed', 'system', NULL, NULL, '2026-07-10 04:26:03'),
('10', '11', 'confirmed', 'packing', 'system', NULL, NULL, '2026-07-10 04:26:03'),
('11', '11', 'packing', 'shipping', 'system', NULL, NULL, '2026-07-10 04:26:03'),
('12', '11', 'shipping', 'completed', 'system', NULL, NULL, '2026-07-10 04:26:03'),
('13', '12', 'pending', 'confirmed', 'system', NULL, NULL, '2026-07-10 04:26:54'),
('14', '12', 'confirmed', 'packing', 'system', NULL, NULL, '2026-07-10 04:26:54'),
('15', '12', 'packing', 'shipping', 'system', NULL, NULL, '2026-07-10 04:26:54'),
('16', '12', 'shipping', 'completed', 'system', NULL, NULL, '2026-07-10 04:26:54'),
('17', '13', 'pending', 'confirmed', 'system', NULL, NULL, '2026-07-10 04:32:11'),
('18', '13', 'confirmed', 'packing', 'system', NULL, NULL, '2026-07-10 04:32:11'),
('19', '13', 'packing', 'shipping', 'system', NULL, NULL, '2026-07-10 04:32:11'),
('20', '13', 'shipping', 'completed', 'system', NULL, NULL, '2026-07-10 04:32:11'),
('21', '14', 'pending', 'confirmed', 'system', NULL, NULL, '2026-07-10 04:32:29'),
('22', '14', 'confirmed', 'packing', 'system', NULL, NULL, '2026-07-10 04:32:29'),
('23', '14', 'packing', 'shipping', 'system', NULL, NULL, '2026-07-10 04:32:30'),
('24', '14', 'shipping', 'completed', 'system', NULL, NULL, '2026-07-10 04:32:30'),
('25', '15', 'pending', 'confirmed', 'system', NULL, NULL, '2026-07-10 04:46:57'),
('26', '15', 'confirmed', 'packing', 'system', NULL, NULL, '2026-07-10 04:46:57'),
('27', '15', 'packing', 'shipping', 'system', NULL, NULL, '2026-07-10 04:46:57'),
('28', '15', 'shipping', 'completed', 'system', NULL, NULL, '2026-07-10 04:46:57'),
('29', '16', 'pending', 'confirmed', 'system', NULL, NULL, '2026-07-10 04:47:12'),
('30', '16', 'confirmed', 'packing', 'system', NULL, NULL, '2026-07-10 04:47:12'),
('31', '16', 'packing', 'shipping', 'system', NULL, NULL, '2026-07-10 04:47:12'),
('32', '16', 'shipping', 'completed', 'system', NULL, NULL, '2026-07-10 04:47:12'),
('33', '17', 'pending', 'confirmed', 'system', NULL, NULL, '2026-07-10 04:49:43'),
('34', '17', 'confirmed', 'packing', 'system', NULL, NULL, '2026-07-10 04:49:43'),
('35', '17', 'packing', 'shipping', 'system', NULL, NULL, '2026-07-10 04:49:43'),
('36', '17', 'shipping', 'completed', 'system', NULL, NULL, '2026-07-10 04:49:43'),
('37', '18', 'pending', 'confirmed', 'system', NULL, NULL, '2026-07-10 04:52:29'),
('38', '18', 'confirmed', 'packing', 'system', NULL, NULL, '2026-07-10 04:52:29'),
('39', '18', 'packing', 'shipping', 'system', NULL, NULL, '2026-07-10 04:52:29'),
('40', '18', 'shipping', 'completed', 'system', NULL, NULL, '2026-07-10 04:52:29'),
('41', '19', 'pending', 'confirmed', 'system', NULL, NULL, '2026-07-10 04:58:10'),
('42', '19', 'confirmed', 'packing', 'system', NULL, NULL, '2026-07-10 04:58:10'),
('43', '19', 'packing', 'shipping', 'system', NULL, NULL, '2026-07-10 04:58:10'),
('44', '19', 'shipping', 'completed', 'system', NULL, NULL, '2026-07-10 04:58:10'),
('45', '20', 'pending', 'confirmed', 'system', NULL, NULL, '2026-07-10 05:00:23'),
('46', '20', 'confirmed', 'packing', 'system', NULL, NULL, '2026-07-10 05:00:23'),
('47', '20', 'packing', 'shipping', 'system', NULL, NULL, '2026-07-10 05:00:23'),
('48', '20', 'shipping', 'completed', 'system', NULL, NULL, '2026-07-10 05:00:23'),
('49', '23', 'pending', 'confirmed', 'system', NULL, NULL, '2026-07-10 05:01:08'),
('50', '23', 'confirmed', 'packing', 'system', NULL, NULL, '2026-07-10 05:01:08'),
('51', '23', 'packing', 'shipping', 'system', NULL, NULL, '2026-07-10 05:01:08'),
('52', '23', 'shipping', 'completed', 'system', NULL, NULL, '2026-07-10 05:01:08'),
('53', '26', 'pending', 'confirmed', 'system', NULL, NULL, '2026-07-10 05:02:38'),
('54', '26', 'confirmed', 'packing', 'system', NULL, NULL, '2026-07-10 05:02:38'),
('55', '26', 'packing', 'shipping', 'system', NULL, NULL, '2026-07-10 05:02:38'),
('56', '26', 'shipping', 'completed', 'system', NULL, NULL, '2026-07-10 05:02:38'),
('57', '29', 'pending', 'confirmed', 'system', NULL, NULL, '2026-07-10 05:03:08'),
('58', '29', 'confirmed', 'packing', 'system', NULL, NULL, '2026-07-10 05:03:08'),
('59', '29', 'packing', 'shipping', 'system', NULL, NULL, '2026-07-10 05:03:08'),
('60', '29', 'shipping', 'completed', 'system', NULL, NULL, '2026-07-10 05:03:08'),
('61', '32', 'pending', 'confirmed', 'system', NULL, NULL, '2026-07-10 19:35:54'),
('62', '32', 'confirmed', 'packing', 'system', NULL, NULL, '2026-07-10 19:35:54'),
('63', '32', 'packing', 'shipping', 'system', NULL, NULL, '2026-07-10 19:35:54'),
('64', '32', 'shipping', 'completed', 'system', NULL, NULL, '2026-07-10 19:35:54'),
('65', '35', 'pending', 'confirmed', 'system', NULL, NULL, '2026-07-10 19:42:45'),
('66', '35', 'confirmed', 'packing', 'system', NULL, NULL, '2026-07-10 19:42:45'),
('67', '35', 'packing', 'shipping', 'system', NULL, NULL, '2026-07-10 19:42:45'),
('68', '35', 'shipping', 'completed', 'system', NULL, NULL, '2026-07-10 19:42:45'),
('69', '38', 'pending', 'confirmed', 'system', NULL, NULL, '2026-07-10 19:43:35'),
('70', '38', 'confirmed', 'packing', 'system', NULL, NULL, '2026-07-10 19:43:35'),
('71', '38', 'packing', 'shipping', 'system', NULL, NULL, '2026-07-10 19:43:35'),
('72', '38', 'shipping', 'completed', 'system', NULL, NULL, '2026-07-10 19:43:35'),
('73', '41', 'pending', 'confirmed', 'system', NULL, NULL, '2026-07-10 19:45:04'),
('74', '41', 'confirmed', 'packing', 'system', NULL, NULL, '2026-07-10 19:45:04'),
('75', '41', 'packing', 'shipping', 'system', NULL, NULL, '2026-07-10 19:45:04'),
('76', '41', 'shipping', 'completed', 'system', NULL, NULL, '2026-07-10 19:45:04'),
('77', '44', 'pending', 'confirmed', 'system', NULL, NULL, '2026-07-10 19:51:25'),
('78', '44', 'confirmed', 'packing', 'system', NULL, NULL, '2026-07-10 19:51:25'),
('79', '44', 'packing', 'shipping', 'system', NULL, NULL, '2026-07-10 19:51:25'),
('80', '44', 'shipping', 'completed', 'system', NULL, NULL, '2026-07-10 19:51:25'),
('81', '45', NULL, 'pending', 'customer', '1', 'Hồ sơ trả góp 12 tháng, cọc 1898000', '2026-07-10 20:04:08'),
('82', '45', 'pending', 'confirmed', 'admin', '1', 'Admin cập nhật trạng thái đơn hàng.', '2026-07-10 20:05:14'),
('83', '46', NULL, 'pending', 'customer', '1', 'Tạo đơn hàng mới (Chuyển khoản VietQR MB Bank)', '2026-07-10 20:05:48'),
('84', '47', NULL, 'pending', 'customer', '2', 'Tạo đơn hàng mới (Chuyển khoản VietQR MB Bank)', '2026-07-10 20:10:19'),
('85', '47', 'pending', 'confirmed', 'admin', '1', 'Admin xác nhận đã nhận chuyển khoản VietQR', '2026-07-10 20:13:02'),
('86', '47', 'confirmed', 'packing', 'admin', '1', 'Admin cập nhật trạng thái đơn hàng.', '2026-07-10 20:14:58'),
('87', '47', 'packing', 'shipping', 'admin', '1', 'Admin cập nhật trạng thái đơn hàng.', '2026-07-10 20:17:18'),
('88', '48', NULL, 'pending', 'customer', '4', 'Tạo đơn hàng mới (Chuyển khoản VietQR MB Bank)', '2026-07-10 20:23:30'),
('89', '48', 'pending', 'confirmed', 'admin', '1', 'Admin cập nhật trạng thái đơn hàng.', '2026-07-10 20:24:36'),
('90', '48', 'confirmed', 'confirmed', 'admin', '1', 'Admin xác nhận đã nhận chuyển khoản VietQR', '2026-07-10 20:25:12'),
('91', '49', NULL, 'pending', 'customer', '4', 'Tạo đơn hàng mới (COD)', '2026-07-10 20:29:03'),
('92', '49', 'confirmed', 'packing', 'admin', '1', 'Admin cập nhật trạng thái đơn hàng.', '2026-07-10 20:31:01'),
('93', '49', 'packing', 'cancelled', 'admin', '1', 'Admin cập nhật trạng thái đơn hàng.', '2026-07-10 20:32:06'),
('94', '6', 'pending', 'confirmed', 'admin', '1', 'Admin cập nhật trạng thái đơn hàng.', '2026-07-10 20:36:12');

DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned DEFAULT NULL,
  `customer_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_address` text COLLATE utf8mb4_unicode_ci,
  `shipping_address` text COLLATE utf8mb4_unicode_ci,
  `payment_method` enum('cash','vnpay','momo','installment','bank_transfer') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cash',
  `payment_status` enum('pending','paid','refunded') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `total_amount` bigint unsigned NOT NULL DEFAULT '0',
  `status` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `vnpay_txn_ref` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vnpay_transaction_no` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vnpay_bank_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vnpay_pay_date` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `coupon_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discount_amount` bigint unsigned NOT NULL DEFAULT '0',
  `shipper_id` int unsigned DEFAULT NULL,
  `shipping_lat` decimal(10,8) DEFAULT NULL,
  `shipping_lng` decimal(11,8) DEFAULT NULL,
  `user_address_id` int unsigned DEFAULT NULL,
  `order_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `wallet_amount` bigint unsigned NOT NULL DEFAULT '0',
  `user_voucher_code` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `store_id` int unsigned DEFAULT NULL,
  `address_province` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_district` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_ward` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_street` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `preferred_time_slot` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_fee` bigint unsigned NOT NULL DEFAULT '0',
  `shipping_distance_km` decimal(8,2) DEFAULT NULL,
  `estimated_delivery_from` date DEFAULT NULL,
  `estimated_delivery_to` date DEFAULT NULL,
  `loyalty_discount_amount` bigint unsigned NOT NULL DEFAULT '0',
  `loyalty_tier` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `earned_points` int unsigned NOT NULL DEFAULT '0',
  `points_awarded` tinyint(1) NOT NULL DEFAULT '0',
  `delivery_proof_path` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipper_assigned_at` datetime DEFAULT NULL,
  `installment_months` tinyint unsigned DEFAULT NULL,
  `installment_deposit_amount` bigint unsigned NOT NULL DEFAULT '0',
  `installment_status` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_preorder` tinyint(1) NOT NULL DEFAULT '0',
  `preorder_deposit_paid` bigint unsigned NOT NULL DEFAULT '0',
  `preorder_status` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vat_company_name` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vat_tax_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vat_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vat_email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vat_invoice_no` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vat_invoice_issued` tinyint(1) NOT NULL DEFAULT '0',
  `vat_invoice_issued_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_orders_order_code` (`order_code`),
  KEY `orders_user_id` (`user_id`),
  KEY `orders_payment_status` (`payment_status`),
  KEY `orders_created_at` (`created_at`),
  KEY `idx_orders_user_id` (`user_id`),
  KEY `idx_orders_store_id` (`store_id`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `orders` (`id`, `user_id`, `customer_name`, `phone`, `email`, `contact_address`, `shipping_address`, `payment_method`, `payment_status`, `total_amount`, `status`, `vnpay_txn_ref`, `vnpay_transaction_no`, `vnpay_bank_code`, `vnpay_pay_date`, `created_at`, `coupon_code`, `discount_amount`, `shipper_id`, `shipping_lat`, `shipping_lng`, `user_address_id`, `order_code`, `wallet_amount`, `user_voucher_code`, `store_id`, `address_province`, `address_district`, `address_ward`, `address_street`, `preferred_time_slot`, `shipping_fee`, `shipping_distance_km`, `estimated_delivery_from`, `estimated_delivery_to`, `loyalty_discount_amount`, `loyalty_tier`, `earned_points`, `points_awarded`, `delivery_proof_path`, `shipper_assigned_at`, `installment_months`, `installment_deposit_amount`, `installment_status`, `is_preorder`, `preorder_deposit_paid`, `preorder_status`, `vat_company_name`, `vat_tax_code`, `vat_address`, `vat_email`, `vat_invoice_no`, `vat_invoice_issued`, `vat_invoice_issued_at`) VALUES
('1', '2', 'Nguyễn Văn A', '0912345678', 'khach@shop.test', '123 Lê Lợi, Hà Nội', '123 Lê Lợi, Hà Nội', 'cash', 'paid', '27500000', 'confirmed', NULL, NULL, NULL, NULL, '2026-06-16 10:00:00', NULL, '0', NULL, NULL, NULL, NULL, 'HD260616-IDGK0', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, NULL, '0', NULL, '0', '0', NULL, NULL, NULL, '0', NULL, '0', '0', NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL),
('2', '3', 'huy', '0781278321', 'huy28072k5@gmail.com', 'hn', 'hn', 'vnpay', 'paid', '92470000', 'completed', '2_1781583234', '52388410', 'NCB', '20260616111354', '2026-06-16 11:13:49', NULL, '0', NULL, NULL, NULL, NULL, 'HD260616-VXKV8', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, NULL, '0', NULL, '0', '0', NULL, NULL, NULL, '0', NULL, '0', '0', NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL),
('3', '3', 'huy', '0781278321', 'huy28072k5@gmail.com', 'hhh', 'hhh', 'vnpay', 'paid', '28990000', 'completed', '3_1781583345', '16708927', 'NCB', '20260616111545', '2026-06-16 11:15:41', NULL, '0', NULL, NULL, NULL, NULL, 'HD260616-CXEXL', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, NULL, '0', NULL, '0', '0', NULL, NULL, NULL, '0', NULL, '0', '0', NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL),
('4', '3', 'huy', '0781278321', 'huy28072k5@gmail.com', 'aa', 'aa', 'cash', 'pending', '50990000', 'completed', NULL, NULL, NULL, NULL, '2026-06-16 11:17:01', NULL, '0', NULL, NULL, NULL, NULL, 'HD260616-3CCH0', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, NULL, '0', NULL, '0', '0', NULL, NULL, NULL, '0', NULL, '0', '0', NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL),
('5', '4', 'huy', '0788415516', 'huy28072k5@gmail.com', 'hn', 'hn', 'momo', 'pending', '28990000', 'pending', NULL, NULL, NULL, NULL, '2026-06-21 08:43:40', NULL, '0', NULL, NULL, NULL, NULL, 'HD260621-BTFKA', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, NULL, '0', NULL, '0', '0', NULL, NULL, NULL, '0', NULL, '0', '0', NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL),
('6', '4', 'huy', '0788415516', 'huy28072k5@gmail.com', 'hn', 'hn', 'momo', 'pending', '450000', 'confirmed', NULL, NULL, NULL, NULL, '2026-06-21 08:50:54', NULL, '0', NULL, NULL, NULL, NULL, 'HD260621-QM6D0', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, NULL, '0', NULL, '0', '0', NULL, NULL, NULL, '0', NULL, '0', '0', NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL),
('7', '4', 'huy', '0788415516', 'huy28072k5@gmail.com', 'hn', 'hn', 'cash', 'pending', '28990000', 'pending', NULL, NULL, NULL, NULL, '2026-06-22 00:33:13', NULL, '0', NULL, NULL, NULL, NULL, 'HD260622-8D8J7', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, NULL, '0', NULL, '0', '0', NULL, NULL, NULL, '0', NULL, '0', '0', NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL),
('8', NULL, 'Test Customer', '0912345678', 'test@example.com', '123 Test Street', '123 Test Street', 'cash', 'pending', '152000', 'pending', NULL, NULL, NULL, NULL, '2026-06-22 01:43:39', 'SIEUOFFER', '38000', NULL, NULL, NULL, NULL, 'HD260622-D3N8X', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, NULL, '0', NULL, '0', '0', NULL, NULL, NULL, '0', NULL, '0', '0', NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL),
('45', '1', 'Admin', '0901234567', 'admin@shop.test', 'UBND phường Bưởi, 10A, Ngõ Cống Xanh, Yên Thái, Kẻ Bưởi, Phường Tây Hồ, Hà Nội, 11214, Việt Nam', 'UBND phường Bưởi, 10A, Ngõ Cống Xanh, Yên Thái, Kẻ Bưởi, Phường Tây Hồ, Hà Nội, 11214, Việt Nam', 'installment', 'pending', '9490000', 'confirmed', NULL, NULL, NULL, NULL, '2026-07-10 20:04:07', NULL, '0', NULL, '21.05085250', '105.81004360', NULL, 'HD260710-9WGCN', '0', NULL, '1', 'Hà Nội', 'Phường Tây Hồ', 'Kẻ Bưởi', 'Ngõ Cống Xanh', 'any', '0', '0.43', '2026-07-10', '2026-07-10', '0', 'Standard', '0', '0', NULL, NULL, '12', '1898000', 'pending_review', '0', '0', NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL),
('46', '1', 'Admin', '0901234567', 'admin@shop.test', 'UBND phường Bưởi, 10A, Ngõ Cống Xanh, Yên Thái, Kẻ Bưởi, Phường Tây Hồ, Hà Nội, 11214, Việt Nam', 'UBND phường Bưởi, 10A, Ngõ Cống Xanh, Yên Thái, Kẻ Bưởi, Phường Tây Hồ, Hà Nội, 11214, Việt Nam', 'bank_transfer', 'pending', '11490000', 'pending', NULL, NULL, NULL, NULL, '2026-07-10 20:05:48', NULL, '0', NULL, '21.05087150', '105.81000870', NULL, 'HD260710-IWCB1', '0', NULL, '1', 'Hà Nội', 'Phường Tây Hồ', 'Kẻ Bưởi', 'Ngõ Cống Xanh', 'any', '0', '0.43', '2026-07-10', '2026-07-10', '0', 'Standard', '0', '0', NULL, NULL, NULL, '0', NULL, '0', '0', NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL),
('47', '2', 'Nguyễn Văn A', '0912345678', 'khach@shop.test', 'FLC Complex, 36, Đường Phạm Hùng, Xuân Phong, Phường Từ Liêm, Hà Nội, 10051, Việt Nam', 'FLC Complex, 36, Đường Phạm Hùng, Xuân Phong, Phường Từ Liêm, Hà Nội, 10051, Việt Nam', 'bank_transfer', 'refunded', '2842270000', 'cancelled', NULL, NULL, NULL, NULL, '2026-07-10 20:10:19', NULL, '0', '21', '21.02722560', '105.77838080', NULL, 'HD260710-QG62M', '0', NULL, '2', 'Hà Nội', 'Xuân Phong', NULL, 'Đường Phạm Hùng', 'any', '0', '0.49', '2026-07-10', '2026-07-10', '0', 'Standard', '0', '0', NULL, '2026-07-10 20:14:58', NULL, '0', NULL, '0', '0', NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL),
('48', '4', 'huy', '0788415516', 'huy28072k5@gmail.com', 'UBND phường Bưởi, 10A, Ngõ Cống Xanh, Yên Thái, Kẻ Bưởi, Phường Tây Hồ, Hà Nội, 11214, Việt Nam', 'UBND phường Bưởi, 10A, Ngõ Cống Xanh, Yên Thái, Kẻ Bưởi, Phường Tây Hồ, Hà Nội, 11214, Việt Nam', 'bank_transfer', 'paid', '1379700000', 'confirmed', NULL, NULL, NULL, NULL, '2026-07-10 20:23:30', NULL, '0', NULL, '21.05087330', '105.81000790', NULL, 'HD260710-7ILB9', '0', NULL, '1', 'Hà Nội', 'Phường Tây Hồ', 'Kẻ Bưởi', 'Ngõ Cống Xanh', 'any', '0', '0.43', '2026-07-10', '2026-07-10', '0', 'Standard', '137970', '1', NULL, NULL, NULL, '0', NULL, '0', '0', NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL),
('49', '4', 'huy', '0788415516', 'huy28072k5@gmail.com', 'UBND phường Bưởi, 10A, Ngõ Cống Xanh, Yên Thái, Kẻ Bưởi, Phường Tây Hồ, Hà Nội, 11214, Việt Nam', 'UBND phường Bưởi, 10A, Ngõ Cống Xanh, Yên Thái, Kẻ Bưởi, Phường Tây Hồ, Hà Nội, 11214, Việt Nam', 'cash', 'refunded', '100000000000000', 'cancelled', NULL, NULL, NULL, NULL, '2026-07-10 20:29:03', NULL, '100000000000000', '21', '21.05087330', '105.81000790', NULL, 'HD260710-KTCKS', '0', NULL, '1', 'Hà Nội', 'Phường Tây Hồ', 'Kẻ Bưởi', 'Ngõ Cống Xanh', 'any', '0', '0.43', '2026-07-10', '2026-07-10', '100000000000000', 'Diamond', '0', '0', NULL, '2026-07-10 20:31:00', NULL, '0', NULL, '1', '0', 'completed', NULL, NULL, NULL, NULL, NULL, '0', NULL);

DROP TABLE IF EXISTS `password_reset_requests`;
CREATE TABLE `password_reset_requests` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `request_type` enum('ADMIN_RESET','FORGOT_PASSWORD') COLLATE utf8mb4_unicode_ci NOT NULL,
  `otp_hash` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('PENDING','USED','EXPIRED') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PENDING',
  `verify_attempts` int unsigned NOT NULL DEFAULT '0',
  `expired_at` datetime NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` enum('ADMIN','USER') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by_id` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `prr_token_unique` (`token`),
  KEY `prr_user_status` (`user_id`,`status`),
  KEY `prr_token` (`token`),
  CONSTRAINT `prr_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `product_review_replies`;
CREATE TABLE `product_review_replies` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `review_id` int unsigned NOT NULL,
  `user_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reply_text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `reply_review_id` (`review_id`),
  CONSTRAINT `fk_replies_review` FOREIGN KEY (`review_id`) REFERENCES `product_reviews` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `product_review_replies` (`id`, `review_id`, `user_name`, `reply_text`, `created_at`) VALUES
('1', '3', 'huy', 'á đùđù', '2026-06-22 01:01:06');

DROP TABLE IF EXISTS `product_reviews`;
CREATE TABLE `product_reviews` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int unsigned NOT NULL,
  `user_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rating` tinyint unsigned NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `likes_count` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `reviews_product_id` (`product_id`),
  CONSTRAINT `fk_reviews_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `product_reviews` (`id`, `product_id`, `user_name`, `rating`, `comment`, `created_at`, `likes_count`) VALUES
('1', '7', 'huy', '5', 'nice xừxừ', '2026-06-22 00:56:37', '0'),
('2', '1', 'huy', '5', 'hayhay', '2026-06-22 00:57:06', '0'),
('3', '1', 'hiển', '5', 'vip pro', '2026-06-22 00:57:16', '1'),
('4', '1', 'hadeshades', '1', 'đắt quá', '2026-06-22 01:01:56', '0');

DROP TABLE IF EXISTS `product_variants`;
CREATE TABLE `product_variants` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int unsigned NOT NULL,
  `color` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `storage` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` bigint unsigned NOT NULL DEFAULT '0',
  `stock` int unsigned NOT NULL DEFAULT '0',
  `image` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_variants_product` (`product_id`),
  CONSTRAINT `fk_variants_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int unsigned NOT NULL,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` bigint unsigned NOT NULL DEFAULT '0',
  `description` text COLLATE utf8mb4_unicode_ci,
  `detail` text COLLATE utf8mb4_unicode_ci,
  `stock` int unsigned NOT NULL DEFAULT '0',
  `image` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `discount_percent` int unsigned NOT NULL DEFAULT '0',
  `promo_active` tinyint(1) NOT NULL DEFAULT '0',
  `ram` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `storage_gb` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `chipset` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `screen_size` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `battery_mah` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `os` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_preorder` tinyint(1) NOT NULL DEFAULT '0',
  `preorder_deposit` bigint unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `products_category_id` (`category_id`),
  KEY `idx_products_category_id` (`category_id`),
  CONSTRAINT `products_category_fk` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=85 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `products` (`id`, `category_id`, `name`, `price`, `description`, `detail`, `stock`, `image`, `status`, `created_at`, `discount_percent`, `promo_active`, `ram`, `storage_gb`, `chipset`, `screen_size`, `battery_mah`, `os`, `is_preorder`, `preorder_deposit`) VALUES
('1', '1', 'iPhone 15 Pro Max 256GB', '29990000', 'Khung Titan cao cấp, chip A17 Pro siêu mạnh mẽ.', 'iPhone 15 Pro Max sở hữu thiết kế khung titan siêu bền và nhẹ, nút Action mới tiện dụng, hệ thống camera chuyên nghiệp 48MP và cổng kết nối USB-C tốc độ cao.', '12', 'ip15.webp', 'active', '2026-06-22 00:43:56', '12', '1', NULL, NULL, NULL, NULL, NULL, NULL, '0', '0'),
('2', '1', 'iPhone 16 Pro 128GB', '28990000', 'Nút điều khiển camera chuyên biệt, chip A18 Pro.', 'iPhone 16 Pro nâng tầm nhiếp ảnh di động với nút Camera Control mới, chip xử lý A18 Pro tối ưu hiệu năng AI và thiết kế tản nhiệt tốt hơn.', '8', 'ip16.webp', 'active', '2026-06-22 00:43:56', '0', '0', NULL, NULL, NULL, NULL, NULL, NULL, '0', '0'),
('3', '1', 'iPhone 17 Ultra Concept', '39990000', 'Thiết kế siêu mỏng tương lai, viền màn hình vô cực.', 'iPhone 17 Ultra là đỉnh cao công nghệ màn hình tràn viền hoàn toàn không khuyết điểm, camera ẩn dưới màn hình và camera zoom quang học 10x.', '3', 'ip17.webp', 'active', '2026-06-22 00:43:56', '0', '0', NULL, NULL, NULL, NULL, NULL, NULL, '0', '0'),
('4', '1', 'iPhone 15 128GB', '19990000', 'Màn hình Dynamic Island, mặt lưng kính pha màu độc đáo.', 'iPhone 15 trang bị chip A16 Bionic mượt mà, camera sau nâng cấp lên 48MP và cổng sạc USB-C phổ biến.', '15', 'ip15.webp', 'active', '2026-06-22 00:43:56', '0', '0', NULL, NULL, NULL, NULL, NULL, NULL, '0', '0'),
('5', '1', 'iPhone 16 Plus 256GB', '25990000', 'Màn hình 6.7 inch siêu rộng rãi, pin trâu vượt trội.', 'iPhone 16 Plus mang đến không gian trải nghiệm lớn cùng thời lượng pin tốt nhất dòng iPhone, chip A18 tối ưu chơi game đồ họa cao.', '10', 'ip16.webp', 'active', '2026-06-22 00:43:56', '0', '0', NULL, NULL, NULL, NULL, NULL, NULL, '0', '0'),
('6', '2', 'MacBook Pro M5 Max', '54990000', 'Siêu chip M5 Max thế hệ mới, màn hình Liquid Retina XDR.', 'Dành cho người dùng chuyên nghiệp đồ họa, lập trình và xử lý tác vụ nặng. MacBook Pro M5 Max cân mọi giới hạn hiệu năng với RAM khủng và tản nhiệt tối ưu.', '5', 'mac_m5.webp', 'active', '2026-06-22 00:43:56', '0', '0', NULL, NULL, NULL, NULL, NULL, NULL, '0', '0'),
('7', '2', 'Dell XPS 13 Plus 9320', '38990000', 'Vỏ nhôm nguyên khối, hàng phím chức năng cảm ứng độc đáo.', 'Thiết kế tràn viền thời thượng, touchpad ẩn sang trọng và bộ vi xử lý Intel Core i7 thế hệ mới mạnh mẽ.', '6', 'dell_xps.webp', 'active', '2026-06-22 00:43:56', '0', '0', NULL, NULL, NULL, NULL, NULL, NULL, '0', '0'),
('8', '2', 'ThinkPad X1 Carbon Gen 13', '45990000', 'Chất liệu sợi carbon siêu nhẹ siêu bền, bàn phím gõ đỉnh cao.', 'Lựa chọn số một cho doanh nhân và lập trình viên. ThinkPad X1 Carbon nổi tiếng với độ bền chuẩn quân đội và bàn phím huyền thoại êm ái.', '4', 'thinkpad_gen13.webp', 'active', '2026-06-22 00:43:56', '0', '0', NULL, NULL, NULL, NULL, NULL, NULL, '0', '0'),
('9', '2', 'MacBook Air M2 13\"', '24990000', 'Thiết kế vuông vắn siêu mỏng nhẹ, pin lên tới 18 giờ.', 'MacBook Air M2 mang lại sự cân bằng hoàn hảo giữa hiệu năng mượt mà và tính cơ động cực cao, phù hợp học sinh, sinh viên và văn phòng.', '11', 'mac_m5.webp', 'active', '2026-06-22 00:43:56', '0', '0', NULL, NULL, NULL, NULL, NULL, NULL, '0', '0'),
('10', '2', 'Dell Inspiron 14 Core i5', '16990000', 'Vỏ kim loại bền bỉ, hiệu năng văn phòng mượt mà.', 'Dell Inspiron 14 trang bị màn hình FHD+ sắc nét, chip Core i5 đời mới đáp ứng tốt mọi công việc học tập và giải trí cơ bản.', '9', 'dell_xps.webp', 'active', '2026-06-22 00:43:56', '0', '0', NULL, NULL, NULL, NULL, NULL, NULL, '0', '0'),
('11', '3', 'Cáp sạc nhanh USB-C bọc dù 60W', '190000', 'Dây bọc dù chống đứt, hỗ trợ sạc nhanh PD.', 'Cáp sạc nhanh USB-C sang USB-C hỗ trợ truyền dữ liệu tốc độ cao, chất liệu sợi nylon bọc dù siêu bền bỉ chống rối gập.', '100', 'day_sac.webp', 'active', '2026-06-22 00:43:56', '0', '0', NULL, NULL, NULL, NULL, NULL, NULL, '0', '0'),
('12', '3', 'Củ sạc nhanh Hades GaN 65W', '390000', 'Công nghệ GaN sạc siêu mát, 3 cổng ra tiện lợi.', 'Củ sạc sử dụng vật liệu bán dẫn Gallium Nitride giúp thu nhỏ kích thước tối đa, sạc nhanh đồng thời cho cả Laptop, iPad và điện thoại.', '80', 'day_sac.webp', 'active', '2026-06-22 00:43:56', '0', '0', NULL, NULL, NULL, NULL, NULL, NULL, '0', '0'),
('13', '3', 'Tai nghe True Wireless Hades Pro', '1290000', 'Chống ồn chủ động ANC, chống nước IPX4.', 'Tai nghe bluetooth không dây thiết kế công thái học ôm tai, chất âm bass sâu, pin kết hợp hộp sạc dùng đến 24 giờ liên tục.', '45', 'tai_nghe.webp', 'active', '2026-06-22 00:43:56', '0', '0', NULL, NULL, NULL, NULL, NULL, NULL, '0', '0'),
('14', '3', 'Tai nghe chụp tai Gaming Pro', '850000', 'Âm thanh vòm 7.1, mic khử tiếng ồn tiện lợi.', 'Tai nghe Over-ear đệm da êm ái, cách âm tuyệt đối, mang lại trải nghiệm âm thanh chiến game 3D chân thực.', '30', 'tai_nghe.webp', 'active', '2026-06-22 00:43:56', '0', '0', NULL, NULL, NULL, NULL, NULL, NULL, '0', '0'),
('15', '3', 'Sạc dự phòng Hades 20000mAh', '590000', 'Dung lượng khủng sạc nhiều lần, sạc nhanh 22.5W.', 'Tích hợp màn hình LED hiển thị % pin trực quan, hỗ trợ sạc nhanh đa thiết bị cùng lúc cực kỳ an toàn.', '60', 'day_sac.webp', 'active', '2026-06-22 00:43:56', '0', '0', NULL, NULL, NULL, NULL, NULL, NULL, '0', '0'),
('16', '4', 'Apple Watch Ultra 3', '21990000', 'Đồng hồ thám hiểm chuyên nghiệp, màn hình độ sáng 3000 nits.', 'Màn hình Sapphire chống trầy, vỏ Titan chuẩn quân đội, GPS tần số kép cực kỳ chính xác cho các hoạt động ngoài trời dã ngoại.', '7', 'smartwatch_apple.png', 'active', '2026-06-22 00:43:56', '0', '0', NULL, NULL, NULL, NULL, NULL, NULL, '0', '0'),
('17', '4', 'Apple Watch Series 9', '9990000', 'Chip S9 SiP thông minh, tính năng Double Tap chạm đúp.', 'Theo dõi sức khỏe nâng cao: điện tâm đồ ECG, nồng độ oxy trong máu SpO2, đo nhiệt độ cơ thể hỗ trợ dự báo chu kỳ sức khỏe.', '14', 'smartwatch_apple.png', 'active', '2026-06-22 00:43:56', '0', '0', NULL, NULL, NULL, NULL, NULL, NULL, '0', '0'),
('18', '4', 'Smartwatch Hades Active 2', '1990000', 'Đo nhịp tim 24/7, pin 14 ngày bền bỉ.', 'Đồng hồ thông minh thể thao hỗ trợ hơn 100 chế độ luyện tập, kháng nước 5ATM thoải mái đi bơi.', '25', 'smartwatch_apple.png', 'active', '2026-06-22 00:43:56', '0', '0', NULL, NULL, NULL, NULL, NULL, NULL, '0', '0'),
('19', '4', 'Apple Watch SE 2024', '5990000', 'Giá trị vượt trội, tính năng phát hiện đụng xe khẩn cấp.', 'Trang bị các tính năng theo dõi sức khỏe cơ bản, thiết kế thể thao trẻ trung, lựa chọn hoàn hảo cho các bạn trẻ và gia đình.', '18', 'smartwatch_apple.png', 'active', '2026-06-22 00:43:56', '0', '0', NULL, NULL, NULL, NULL, NULL, NULL, '0', '0'),
('20', '5', 'Đồng hồ Casio Edifice EFR', '3450000', 'Thiết kế kim lịch lãm, chống nước độ sâu 100 mét.', 'Dòng đồng hồ Casio cao cấp cho nam, vỏ thép không gỉ nguyên khối, mặt kính khoáng chống va đập cực tốt.', '10', 'watch_casio.png', 'active', '2026-06-22 00:43:56', '0', '0', NULL, NULL, NULL, NULL, NULL, NULL, '0', '0'),
('21', '5', 'Casio G-Shock G-Steel Tough Solar', '4990000', 'Sử dụng pin năng lượng mặt trời, tự động đồng bộ giờ.', 'Chống va đập cực đỉnh, tích hợp đèn LED đôi siêu sáng, kết nối bluetooth thông minh với điện thoại.', '6', 'watch_casio.png', 'active', '2026-06-22 00:43:56', '0', '0', NULL, NULL, NULL, NULL, NULL, NULL, '0', '0'),
('22', '5', 'Đồng hồ Casio Classic MTP Dây Da', '950000', 'Phong cách tối giản classic cổ điển, dây da bò thật.', 'Đồng hồ đeo tay thanh lịch cho nam nữ, phù hợp đi học, đi làm công sở, thời gian pin dùng đến 3 năm.', '20', 'watch_casio.png', 'active', '2026-06-22 00:43:56', '0', '0', NULL, NULL, NULL, NULL, NULL, NULL, '0', '0'),
('23', '5', 'G-Shock GA-2100 \"CasiOak\"', '3120000', 'Mặt bát giác cá tính, thiết kế mỏng nhẹ hiện đại.', 'Vỏ bảo vệ cấu trúc carbon bền nhẹ, thiết kế sang trọng pha nét thể thao bụi bặm bán chạy số 1 toàn cầu.', '15', 'watch_casio.png', 'active', '2026-06-22 00:43:56', '0', '0', NULL, NULL, NULL, NULL, NULL, NULL, '0', '0'),
('24', '6', 'iPhone 15 Pro 128GB Cũ Đẹp', '21490000', 'Máy cũ đẹp 99% likenew như mới, bảo hành 6 tháng.', 'Cam kết zin nguyên bản 100%, chưa qua sửa chữa thay thế linh kiện, pin trên 90%, đầy đủ phụ kiện sạc cáp.', '5', 'ip15.webp', 'active', '2026-06-22 00:43:56', '0', '0', NULL, NULL, NULL, NULL, NULL, NULL, '0', '0'),
('25', '6', 'MacBook Air M1 Cũ Likenew', '13990000', 'Lựa chọn giá rẻ tối ưu nhất của Apple, máy dùng lướt.', 'Ngoại hình đẹp keng không cấn móp, pin sạc ít lần còn rất trâu, bảo hành phần cứng 1 đổi 1 trong 30 ngày.', '4', 'mac_m5.webp', 'active', '2026-06-22 00:43:56', '0', '0', NULL, NULL, NULL, NULL, NULL, NULL, '0', '0'),
('26', '6', 'Dell XPS 13 Cũ Giá Tốt', '18900000', 'Laptop văn phòng sang chảnh, độ bền siêu ổn định.', 'Dell XPS cũ qua sử dụng, vỏ nhôm còn rất đẹp, màn hình IPS siêu sáng, hoạt động trơn tru mát mẻ.', '3', 'dell_xps.webp', 'active', '2026-06-22 00:43:56', '0', '0', NULL, NULL, NULL, NULL, NULL, NULL, '0', '0'),
('27', '6', 'iPad Air 5 M1 Cũ 64GB WiFi', '11490000', 'Hiệu năng chip M1 cực khỏe, máy nguyên bản.', 'iPad Air 5 cũ phục vụ vẽ đồ họa, ghi chú học tập mượt mà, cảm biến vân tay nhạy bén.', '8', 'tablet_ipad.png', 'active', '2026-06-22 00:43:56', '0', '0', NULL, NULL, NULL, NULL, NULL, NULL, '0', '0'),
('28', '7', 'iPad Pro M4 11 inch 256GB', '25990000', 'iPad mỏng nhất lịch sử, chip M4 siêu khủng, màn hình OLED.', 'iPad Pro cao cấp nhất của Apple với màn hình Tandem OLED rực rỡ, chip M4 tối ưu hóa hiệu năng làm việc đồ họa 3D.', '6', 'tablet_ipad.png', 'active', '2026-06-22 00:43:56', '0', '0', NULL, NULL, NULL, NULL, NULL, NULL, '0', '0'),
('29', '7', 'iPad Air 6 M2 2026', '16490000', 'Sức mạnh từ chip M2 mượt mà, hỗ trợ Apple Pencil Pro.', 'iPad Air thế hệ mới nâng cấp cấu hình vượt trội đáp ứng tốt mọi công việc đa nhiệm học tập của bạn.', '10', 'tablet_ipad.png', 'active', '2026-06-22 00:43:56', '0', '0', NULL, NULL, NULL, NULL, NULL, NULL, '0', '0'),
('30', '7', 'iPad Gen 10 64GB WiFi', '9490000', 'Thiết kế thời trang nhiều màu sắc trẻ trung, cổng USB-C.', 'iPad tiêu chuẩn màn hình 10.9 inch sắc nét Liquid Retina, nút nguồn tích hợp Touch ID, hỗ trợ bàn phím Magic Keyboard Folio.', '16', 'tablet_ipad.png', 'active', '2026-06-22 00:43:56', '0', '0', NULL, NULL, NULL, NULL, NULL, NULL, '0', '0'),
('84', '4', 'Ô tô nè', '999999999999999', '', '', '2', '/image/Screenshot_from_2026-07-10_16-46-13_1783690081.png', 'active', '2026-07-10 20:28:01', '0', '0', '16GB', '1TB', '3', '1000', '5000000', 'IOS', '1', '0');

DROP TABLE IF EXISTS `promotions`;
CREATE TABLE `promotions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `discount_percent` int NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `expires_at` date NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `discount_amount` bigint unsigned NOT NULL DEFAULT '0',
  `discount_type` enum('percent','fixed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'percent',
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `promotions` (`id`, `code`, `discount_percent`, `status`, `expires_at`, `created_at`, `discount_amount`, `discount_type`) VALUES
('1', 'SIEUOFFER', '20', 'inactive', '2026-12-31', '2026-06-22 01:12:54', '0', 'percent'),
('2', 'SUPER SALE', '50', 'active', '2026-12-22', '2026-06-22 21:37:19', '0', 'percent'),
('3', 'TI-65E07DB4', '0', 'active', '2026-08-09', '2026-07-10 20:36:57', '18000000', 'fixed');

DROP TABLE IF EXISTS `push_subscriptions`;
CREATE TABLE `push_subscriptions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned DEFAULT NULL,
  `endpoint` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `endpoint_hash` char(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `p256dh` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `auth` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_push_endpoint` (`endpoint_hash`),
  KEY `idx_push_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `rate_limits`;
CREATE TABLE `rate_limits` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hits` int unsigned NOT NULL DEFAULT '1',
  `expires_at` int unsigned NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `rate_limits` (`key`, `hits`, `expires_at`) VALUES
('login:127.0.0.1', '1', '1783690276');

DROP TABLE IF EXISTS `refund_requests`;
CREATE TABLE `refund_requests` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int unsigned NOT NULL,
  `user_id` int unsigned DEFAULT NULL,
  `method` enum('wallet','voucher','original') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'wallet',
  `amount` bigint unsigned NOT NULL,
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `admin_note` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `processed_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `refund_requests_order_id` (`order_id`),
  KEY `refund_requests_status` (`status`),
  CONSTRAINT `refund_requests_order_fk` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `refund_requests` (`id`, `order_id`, `user_id`, `method`, `amount`, `status`, `admin_note`, `created_at`, `processed_at`) VALUES
('1', '47', '2', 'wallet', '2842270000', 'approved', 'test fix', '2026-07-10 20:18:01', '2026-07-10 20:27:38'),
('2', '48', '4', 'wallet', '1379700000', 'rejected', NULL, '2026-07-10 20:25:33', '2026-07-10 20:28:25');

DROP TABLE IF EXISTS `shipper_locations`;
CREATE TABLE `shipper_locations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `shipper_id` int unsigned NOT NULL,
  `order_id` int unsigned DEFAULT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `accuracy` float DEFAULT NULL,
  `recorded_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_shipper_time` (`shipper_id`,`recorded_at`),
  KEY `idx_order_time` (`order_id`,`recorded_at`),
  CONSTRAINT `shipper_locations_shipper_fk` FOREIGN KEY (`shipper_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `site_promotions`;
CREATE TABLE `site_promotions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `discount_percent` int unsigned NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'inactive',
  `starts_at` date DEFAULT NULL,
  `expires_at` date NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `site_promotions` (`id`, `name`, `discount_percent`, `status`, `starts_at`, `expires_at`, `created_at`) VALUES
('1', 'sale tí nhề', '10', 'active', '2026-06-22', '2026-06-23', '2026-06-22 21:37:54');

DROP TABLE IF EXISTS `store_stock`;
CREATE TABLE `store_stock` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `store_id` int unsigned NOT NULL,
  `product_id` int unsigned NOT NULL,
  `variant_id` int unsigned NOT NULL DEFAULT '0',
  `quantity` int unsigned NOT NULL DEFAULT '0',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_store_stock` (`store_id`,`product_id`,`variant_id`),
  KEY `idx_store_stock_product` (`product_id`),
  CONSTRAINT `fk_store_stock_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_store_stock_store` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=151 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `store_stock` (`id`, `store_id`, `product_id`, `variant_id`, `quantity`, `updated_at`) VALUES
('1', '1', '1', '0', '2', '2026-07-10 19:35:49'),
('2', '1', '2', '0', '1', '2026-07-10 19:35:49'),
('3', '1', '3', '0', '1', '2026-07-10 19:35:49'),
('4', '1', '4', '0', '3', '2026-07-10 19:35:49'),
('5', '1', '5', '0', '2', '2026-07-10 19:35:49'),
('6', '1', '6', '0', '1', '2026-07-10 19:35:49'),
('7', '1', '7', '0', '1', '2026-07-10 19:35:49'),
('8', '1', '8', '0', '1', '2026-07-10 19:35:49'),
('9', '1', '9', '0', '2', '2026-07-10 19:35:49'),
('10', '1', '10', '0', '1', '2026-07-10 19:35:49'),
('11', '1', '11', '0', '20', '2026-07-10 19:35:49'),
('12', '1', '12', '0', '16', '2026-07-10 19:35:49'),
('13', '1', '13', '0', '9', '2026-07-10 19:35:49'),
('14', '1', '14', '0', '6', '2026-07-10 19:35:49'),
('15', '1', '15', '0', '12', '2026-07-10 19:35:49'),
('16', '1', '16', '0', '1', '2026-07-10 19:35:49'),
('17', '1', '17', '0', '2', '2026-07-10 19:35:49'),
('18', '1', '18', '0', '5', '2026-07-10 19:35:49'),
('19', '1', '19', '0', '3', '2026-07-10 19:35:49'),
('20', '1', '20', '0', '2', '2026-07-10 19:35:49'),
('21', '1', '21', '0', '1', '2026-07-10 19:35:49'),
('22', '1', '22', '0', '4', '2026-07-10 19:35:49'),
('23', '1', '23', '0', '3', '2026-07-10 19:35:49'),
('24', '1', '24', '0', '1', '2026-07-10 19:35:49'),
('25', '1', '25', '0', '1', '2026-07-10 19:35:49'),
('26', '1', '26', '0', '1', '2026-07-10 19:35:49'),
('27', '1', '27', '0', '1', '2026-07-10 19:35:49'),
('28', '1', '28', '0', '1', '2026-07-10 19:35:49'),
('29', '1', '29', '0', '2', '2026-07-10 19:35:49'),
('30', '1', '30', '0', '3', '2026-07-10 19:35:49'),
('31', '2', '1', '0', '2', '2026-07-10 19:35:49'),
('32', '2', '2', '0', '1', '2026-07-10 19:35:49'),
('33', '2', '3', '0', '1', '2026-07-10 19:35:49'),
('34', '2', '4', '0', '3', '2026-07-10 19:35:49'),
('35', '2', '5', '0', '2', '2026-07-10 19:35:49'),
('36', '2', '6', '0', '1', '2026-07-10 19:35:49'),
('37', '2', '7', '0', '1', '2026-07-10 19:35:49'),
('38', '2', '8', '0', '1', '2026-07-10 19:35:49'),
('39', '2', '9', '0', '2', '2026-07-10 19:35:49'),
('40', '2', '10', '0', '1', '2026-07-10 19:35:49'),
('41', '2', '11', '0', '20', '2026-07-10 19:35:49'),
('42', '2', '12', '0', '16', '2026-07-10 19:35:49'),
('43', '2', '13', '0', '9', '2026-07-10 19:35:49'),
('44', '2', '14', '0', '6', '2026-07-10 19:35:49'),
('45', '2', '15', '0', '12', '2026-07-10 19:35:49'),
('46', '2', '16', '0', '1', '2026-07-10 19:35:49'),
('47', '2', '17', '0', '2', '2026-07-10 19:35:49'),
('48', '2', '18', '0', '5', '2026-07-10 19:35:49'),
('49', '2', '19', '0', '3', '2026-07-10 19:35:49'),
('50', '2', '20', '0', '2', '2026-07-10 19:35:49'),
('51', '2', '21', '0', '1', '2026-07-10 19:35:49'),
('52', '2', '22', '0', '4', '2026-07-10 19:35:49'),
('53', '2', '23', '0', '3', '2026-07-10 19:35:49'),
('54', '2', '24', '0', '1', '2026-07-10 19:35:49'),
('55', '2', '25', '0', '1', '2026-07-10 19:35:49'),
('56', '2', '26', '0', '1', '2026-07-10 19:35:49'),
('57', '2', '27', '0', '1', '2026-07-10 19:35:49'),
('58', '2', '28', '0', '1', '2026-07-10 19:35:49'),
('59', '2', '29', '0', '2', '2026-07-10 19:35:49'),
('60', '2', '30', '0', '3', '2026-07-10 19:35:49'),
('61', '3', '1', '0', '2', '2026-07-10 19:35:49'),
('62', '3', '2', '0', '1', '2026-07-10 19:35:49'),
('63', '3', '3', '0', '1', '2026-07-10 19:35:49'),
('64', '3', '4', '0', '3', '2026-07-10 19:35:49'),
('65', '3', '5', '0', '2', '2026-07-10 19:35:49'),
('66', '3', '6', '0', '1', '2026-07-10 19:35:49'),
('67', '3', '7', '0', '1', '2026-07-10 19:35:49'),
('68', '3', '8', '0', '1', '2026-07-10 19:35:49'),
('69', '3', '9', '0', '2', '2026-07-10 19:35:49'),
('70', '3', '10', '0', '1', '2026-07-10 19:35:49'),
('71', '3', '11', '0', '20', '2026-07-10 19:35:49'),
('72', '3', '12', '0', '16', '2026-07-10 19:35:49'),
('73', '3', '13', '0', '9', '2026-07-10 19:35:49'),
('74', '3', '14', '0', '6', '2026-07-10 19:35:49'),
('75', '3', '15', '0', '12', '2026-07-10 19:35:49'),
('76', '3', '16', '0', '1', '2026-07-10 19:35:49'),
('77', '3', '17', '0', '2', '2026-07-10 19:35:49'),
('78', '3', '18', '0', '5', '2026-07-10 19:35:49'),
('79', '3', '19', '0', '3', '2026-07-10 19:35:49'),
('80', '3', '20', '0', '2', '2026-07-10 19:35:49'),
('81', '3', '21', '0', '1', '2026-07-10 19:35:49'),
('82', '3', '22', '0', '4', '2026-07-10 19:35:49'),
('83', '3', '23', '0', '3', '2026-07-10 19:35:49'),
('84', '3', '24', '0', '1', '2026-07-10 19:35:49'),
('85', '3', '25', '0', '1', '2026-07-10 19:35:49'),
('86', '3', '26', '0', '1', '2026-07-10 19:35:49'),
('87', '3', '27', '0', '1', '2026-07-10 19:35:49'),
('88', '3', '28', '0', '1', '2026-07-10 19:35:49'),
('89', '3', '29', '0', '2', '2026-07-10 19:35:49'),
('90', '3', '30', '0', '3', '2026-07-10 19:35:49'),
('91', '4', '1', '0', '2', '2026-07-10 19:35:49'),
('92', '4', '2', '0', '1', '2026-07-10 19:35:49'),
('93', '4', '3', '0', '1', '2026-07-10 19:35:49'),
('94', '4', '4', '0', '3', '2026-07-10 19:35:49'),
('95', '4', '5', '0', '2', '2026-07-10 19:35:49'),
('96', '4', '6', '0', '1', '2026-07-10 19:35:49'),
('97', '4', '7', '0', '1', '2026-07-10 19:35:49'),
('98', '4', '8', '0', '1', '2026-07-10 19:35:49'),
('99', '4', '9', '0', '2', '2026-07-10 19:35:49'),
('100', '4', '10', '0', '1', '2026-07-10 19:35:49'),
('101', '4', '11', '0', '20', '2026-07-10 19:35:49'),
('102', '4', '12', '0', '16', '2026-07-10 19:35:49'),
('103', '4', '13', '0', '9', '2026-07-10 19:35:49'),
('104', '4', '14', '0', '6', '2026-07-10 19:35:49'),
('105', '4', '15', '0', '12', '2026-07-10 19:35:49'),
('106', '4', '16', '0', '1', '2026-07-10 19:35:49'),
('107', '4', '17', '0', '2', '2026-07-10 19:35:49'),
('108', '4', '18', '0', '5', '2026-07-10 19:35:49'),
('109', '4', '19', '0', '3', '2026-07-10 19:35:49'),
('110', '4', '20', '0', '2', '2026-07-10 19:35:49'),
('111', '4', '21', '0', '1', '2026-07-10 19:35:49'),
('112', '4', '22', '0', '4', '2026-07-10 19:35:49'),
('113', '4', '23', '0', '3', '2026-07-10 19:35:49'),
('114', '4', '24', '0', '1', '2026-07-10 19:35:49'),
('115', '4', '25', '0', '1', '2026-07-10 19:35:49'),
('116', '4', '26', '0', '1', '2026-07-10 19:35:49'),
('117', '4', '27', '0', '1', '2026-07-10 19:35:49'),
('118', '4', '28', '0', '1', '2026-07-10 19:35:49'),
('119', '4', '29', '0', '2', '2026-07-10 19:35:49'),
('120', '4', '30', '0', '3', '2026-07-10 19:35:49'),
('121', '5', '1', '0', '2', '2026-07-10 19:35:49'),
('122', '5', '2', '0', '1', '2026-07-10 19:35:49'),
('123', '5', '3', '0', '1', '2026-07-10 19:35:49'),
('124', '5', '4', '0', '3', '2026-07-10 19:35:49'),
('125', '5', '5', '0', '2', '2026-07-10 19:35:49'),
('126', '5', '6', '0', '1', '2026-07-10 19:35:49'),
('127', '5', '7', '0', '1', '2026-07-10 19:35:49'),
('128', '5', '8', '0', '1', '2026-07-10 19:35:49'),
('129', '5', '9', '0', '2', '2026-07-10 19:35:49'),
('130', '5', '10', '0', '1', '2026-07-10 19:35:49'),
('131', '5', '11', '0', '20', '2026-07-10 19:35:49'),
('132', '5', '12', '0', '16', '2026-07-10 19:35:49'),
('133', '5', '13', '0', '9', '2026-07-10 19:35:49'),
('134', '5', '14', '0', '6', '2026-07-10 19:35:49'),
('135', '5', '15', '0', '12', '2026-07-10 19:35:49'),
('136', '5', '16', '0', '1', '2026-07-10 19:35:49'),
('137', '5', '17', '0', '2', '2026-07-10 19:35:49'),
('138', '5', '18', '0', '5', '2026-07-10 19:35:49'),
('139', '5', '19', '0', '3', '2026-07-10 19:35:49'),
('140', '5', '20', '0', '2', '2026-07-10 19:35:49'),
('141', '5', '21', '0', '1', '2026-07-10 19:35:49'),
('142', '5', '22', '0', '4', '2026-07-10 19:35:49'),
('143', '5', '23', '0', '3', '2026-07-10 19:35:49'),
('144', '5', '24', '0', '1', '2026-07-10 19:35:49'),
('145', '5', '25', '0', '1', '2026-07-10 19:35:49'),
('146', '5', '26', '0', '1', '2026-07-10 19:35:49'),
('147', '5', '27', '0', '1', '2026-07-10 19:35:49'),
('148', '5', '28', '0', '1', '2026-07-10 19:35:49'),
('149', '5', '29', '0', '2', '2026-07-10 19:35:49'),
('150', '5', '30', '0', '3', '2026-07-10 19:35:49');

DROP TABLE IF EXISTS `stores`;
CREATE TABLE `stores` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `lat` decimal(10,8) NOT NULL,
  `lng` decimal(11,8) NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `opening_hours` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `service_radius_km` decimal(6,2) NOT NULL DEFAULT '12.00',
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_stores_name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=3146 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `stores` (`id`, `name`, `address`, `lat`, `lng`, `phone`, `opening_hours`, `service_radius_km`, `status`, `created_at`) VALUES
('1', 'Trụ sở chính', 'Võng Thị, Tây Hồ, Hà Nội', '21.05440000', '105.81180000', '1800 1234', '08:00-21:30', '25.00', 'active', '2026-07-10 04:21:35'),
('2', 'Cơ sở 2', 'Số 3 Duy Tân, Cầu Giấy, Hà Nội', '21.02940000', '105.78250000', '1800 1234', '08:00-21:30', '25.00', 'active', '2026-07-10 04:21:35'),
('3', 'Cơ sở 3', 'Lê Duẩn, Hải Châu, Đà Nẵng', '16.05940000', '108.22080000', '1800 1234', '08:00-21:30', '25.00', 'active', '2026-07-10 04:21:35'),
('4', 'Cơ sở 4', 'Số 99 Nguyễn Huệ, Quận 1, TP. HCM', '10.77560000', '106.70190000', '1800 1234', '08:00-21:30', '25.00', 'active', '2026-07-10 04:21:35'),
('5', 'Cơ sở 5', 'Số 250 CMT8, Q.3, Sài Gòn', '10.78180000', '106.67980000', '1800 1234', '08:00-21:30', '25.00', 'active', '2026-07-10 04:21:35');

DROP TABLE IF EXISTS `support_messages`;
CREATE TABLE `support_messages` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `session_id` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sender` enum('user','admin') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `user_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Khách',
  `user_email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `sm_session` (`session_id`),
  KEY `sm_read` (`is_read`),
  KEY `idx_support_messages_session_id` (`session_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `support_messages` (`id`, `session_id`, `sender`, `user_name`, `user_email`, `message`, `is_read`, `created_at`) VALUES
('1', '4514baa5121baad24582da5d7a2d47c9', 'user', 'Nguyễn Văn A', 'khach@shop.test', 'chào', '1', '2026-07-10 20:16:42'),
('2', '4514baa5121baad24582da5d7a2d47c9', 'admin', 'Admin', '', 'chào', '1', '2026-07-10 20:16:52');

DROP TABLE IF EXISTS `trade_ins`;
CREATE TABLE `trade_ins` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned DEFAULT NULL,
  `customer_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `device_model` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `device_condition` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estimated_value` bigint unsigned NOT NULL,
  `voucher_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_trade_ins_phone` (`customer_phone`),
  KEY `fk_trade_ins_user` (`user_id`),
  CONSTRAINT `fk_trade_ins_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `trade_ins` (`id`, `user_id`, `customer_name`, `customer_phone`, `device_model`, `device_condition`, `estimated_value`, `voucher_code`, `status`, `created_at`) VALUES
('1', '4', 'huy', '0788415516', 'iPhone 14 Pro Max', 'Máy đẹp 99% (Không trầy xước)', '18000000', 'TI-65E07DB4', 'completed', '2026-07-10 20:36:57');

DROP TABLE IF EXISTS `user_addresses`;
CREATE TABLE `user_addresses` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `label` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `recipient` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address_line` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `ward` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `district` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`),
  CONSTRAINT `user_addresses_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `user_vouchers`;
CREATE TABLE `user_vouchers` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `code` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `discount_percent` int unsigned NOT NULL DEFAULT '10',
  `max_discount` bigint unsigned DEFAULT NULL,
  `expires_at` date NOT NULL,
  `status` enum('active','used','expired') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `source_order_id` int unsigned DEFAULT NULL,
  `used_order_id` int unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_user_vouchers_code` (`code`),
  KEY `user_vouchers_user_id` (`user_id`),
  KEY `user_vouchers_status` (`status`),
  CONSTRAINT `user_vouchers_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `user_wallets`;
CREATE TABLE `user_wallets` (
  `user_id` int unsigned NOT NULL,
  `balance` bigint unsigned NOT NULL DEFAULT '0',
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  CONSTRAINT `user_wallets_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `user_wallets` (`user_id`, `balance`, `updated_at`) VALUES
('1', '0', '2026-07-10 20:02:33'),
('2', '2842270000', '2026-07-10 20:27:38'),
('4', '100000000000000', '2026-07-10 20:34:24');

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','customer','shipper') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'customer',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `failed_login_attempts` int unsigned NOT NULL DEFAULT '0',
  `lockout_until` datetime DEFAULT NULL,
  `loyalty_points` int unsigned NOT NULL DEFAULT '0',
  `loyalty_tier` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Standard',
  `two_factor_secret` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `two_factor_enabled` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `password`, `role`, `created_at`, `failed_login_attempts`, `lockout_until`, `loyalty_points`, `loyalty_tier`, `two_factor_secret`, `two_factor_enabled`) VALUES
('1', 'Admin', 'admin@shop.test', '0901234567', '$2y$10$kdcd7bTl11qWkeuQlDjHd.wgsCUWuTjJnwtXad0WNKtB/9154yWwu', 'admin', '2026-06-16 01:46:26', '0', NULL, '0', 'Standard', NULL, '0'),
('2', 'Nguyễn Văn A', 'khach@shop.test', '0912345678', '$2y$10$Qrp7GX7/3abDVbnAZdVLX.1edRvM80TkGu4fyTWJPaFa27LA2WbAC', 'customer', '2026-06-16 01:46:26', '0', NULL, '0', 'Standard', NULL, '0'),
('4', 'huy', 'huy28072k5@gmail.com', '028282728', '$2y$10$J45pZjE.GViiV44Wup9YeOhIjyGh1p3Da0AWtTpjrSiqgHwds7m0G', 'customer', '2026-06-21 08:42:25', '0', NULL, '137970', 'Diamond', NULL, '0'),
('21', 'Nguyễn Văn Shipper', 'shipper@shop.test', '0987654321', '$2y$10$Ad7KPM/fgPDwS9pfcemkVO.Rj33Co7FWQUtjX3faWjor3mIavORK6', 'shipper', '2026-07-10 20:00:06', '0', NULL, '0', 'Standard', NULL, '0');

DROP TABLE IF EXISTS `wallet_transactions`;
CREATE TABLE `wallet_transactions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `type` enum('credit','debit') COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` bigint unsigned NOT NULL,
  `ref_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ref_id` int unsigned DEFAULT NULL,
  `note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `wallet_tx_user_id` (`user_id`),
  KEY `wallet_tx_created_at` (`created_at`),
  CONSTRAINT `wallet_tx_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `wallet_transactions` (`id`, `user_id`, `type`, `amount`, `ref_type`, `ref_id`, `note`, `created_at`) VALUES
('3', '2', 'credit', '2842270000', 'refund', '1', 'Hoàn tiền đơn HD260710-QG62M', '2026-07-10 20:27:38'),
('4', '4', 'credit', '100000000000000', 'order_cancel', '49', 'Hoàn tiền khi hủy đơn HD260710-KTCKS', '2026-07-10 20:34:24');

DROP TABLE IF EXISTS `warehouses`;
CREATE TABLE `warehouses` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `warehouses` (`id`, `name`, `latitude`, `longitude`, `is_active`) VALUES
('1', 'Kho chính Hà Nội', '21.02850000', '105.85420000', '1');

DROP TABLE IF EXISTS `warranties`;
CREATE TABLE `warranties` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int unsigned DEFAULT NULL,
  `product_id` int unsigned NOT NULL,
  `customer_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `imei` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `warranty_code` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `started_at` datetime NOT NULL,
  `expired_at` datetime NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `imei` (`imei`),
  UNIQUE KEY `warranty_code` (`warranty_code`),
  KEY `idx_warranties_phone` (`customer_phone`),
  KEY `idx_warranties_imei` (`imei`),
  KEY `fk_warranties_order` (`order_id`),
  KEY `fk_warranties_product` (`product_id`),
  CONSTRAINT `fk_warranties_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_warranties_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `warranties` (`id`, `order_id`, `product_id`, `customer_name`, `customer_phone`, `imei`, `warranty_code`, `status`, `started_at`, `expired_at`, `created_at`, `email`) VALUES
('1', '9', '1', 'Nguyen Van A', '0987654321', '866310356811401', 'WAR-H5MT2K2I', 'active', '2026-07-10 04:22:02', '2027-07-10 04:22:02', '2026-07-10 04:22:02', NULL),
('2', '10', '2', 'Nguyen Van A', '0987654321', '868304397803763', 'WAR-MS6A3ZBW', 'active', '2026-07-10 04:24:55', '2027-07-10 04:24:55', '2026-07-10 04:24:55', NULL),
('3', '11', '4', 'Nguyen Van A', '0987654321', '863913911242677', 'WAR-3DSRDJQG', 'active', '2026-07-10 04:26:03', '2027-07-10 04:26:03', '2026-07-10 04:26:03', NULL),
('4', '12', '36', 'Nguyen Van A', '0987654321', '869106745489005', 'WAR-BZJ1F9FC', 'active', '2026-07-10 04:26:54', '2027-07-10 04:26:54', '2026-07-10 04:26:54', NULL),
('5', '13', '37', 'Nguyen Van A', '0987654321', '864304033578474', 'WAR-JOZBVB4I', 'active', '2026-07-10 04:32:11', '2027-07-10 04:32:11', '2026-07-10 04:32:11', NULL),
('6', '14', '39', 'Nguyen Van A', '0987654321', '867793051195531', 'WAR-M4253A5H', 'active', '2026-07-10 04:32:30', '2027-07-10 04:32:30', '2026-07-10 04:32:30', NULL),
('7', '15', '41', 'Nguyen Van A', '0987654321', '866610891738443', 'WAR-33KGZGHG', 'active', '2026-07-10 04:46:57', '2027-07-10 04:46:57', '2026-07-10 04:46:57', NULL),
('8', '16', '43', 'Nguyen Van A', '0987654321', '864488235362118', 'WAR-Q4PNBHC4', 'active', '2026-07-10 04:47:12', '2027-07-10 04:47:12', '2026-07-10 04:47:12', NULL),
('9', '17', '45', 'Nguyen Van A', '0987654321', '862740937697977', 'WAR-B22EYRGX', 'active', '2026-07-10 04:49:43', '2027-07-10 04:49:43', '2026-07-10 04:49:43', NULL),
('10', '18', '47', 'Nguyen Van A', '0987654321', '863842277468548', 'WAR-SNM4BGFE', 'active', '2026-07-10 04:52:29', '2027-07-10 04:52:29', '2026-07-10 04:52:29', NULL),
('11', '19', '49', 'Nguyen Van A', '0987654321', '868689334926225', 'WAR-X4K5R13G', 'active', '2026-07-10 04:58:10', '2027-07-10 04:58:10', '2026-07-10 04:58:10', NULL),
('12', '20', '51', 'Nguyen Van A', '0987654321', '864448408236350', 'WAR-8LCCNPW1', 'active', '2026-07-10 05:00:23', '2027-07-10 05:00:23', '2026-07-10 05:00:23', NULL),
('13', '23', '55', 'Nguyen Van A', '0987654321', '860227122779213', 'WAR-3F464O4K', 'active', '2026-07-10 05:01:08', '2027-07-10 05:01:08', '2026-07-10 05:01:08', NULL),
('14', '26', '59', 'Nguyen Van A', '0987654321', '867418786652707', 'WAR-QCRMFE2F', 'active', '2026-07-10 05:02:38', '2027-07-10 05:02:38', '2026-07-10 05:02:38', NULL),
('15', '29', '63', 'Nguyen Van A', '0987654321', '862233312781525', 'WAR-SM3GB9OJ', 'active', '2026-07-10 05:03:08', '2027-07-10 05:03:08', '2026-07-10 05:03:08', NULL),
('16', '32', '67', 'Nguyen Van A', '0987654321', '863918212512837', 'WAR-I0XW6O2D', 'active', '2026-07-10 19:35:54', '2027-07-10 19:35:54', '2026-07-10 19:35:54', NULL),
('17', '35', '71', 'Nguyen Van A', '0987654321', '861793854804612', 'WAR-3INY4UO2', 'active', '2026-07-10 19:42:45', '2027-07-10 19:42:45', '2026-07-10 19:42:45', NULL),
('18', '38', '75', 'Nguyen Van A', '0987654321', '861078796456070', 'WAR-RIWKCA1K', 'active', '2026-07-10 19:43:35', '2027-07-10 19:43:35', '2026-07-10 19:43:35', NULL),
('19', '41', '79', 'Nguyen Van A', '0987654321', '860474138598756', 'WAR-MNA80UA4', 'active', '2026-07-10 19:45:04', '2027-07-10 19:45:04', '2026-07-10 19:45:04', NULL),
('20', '44', '83', 'Nguyen Van A', '0987654321', '865437811525430', 'WAR-EJGUV6RG', 'active', '2026-07-10 19:51:25', '2027-07-10 19:51:25', '2026-07-10 19:51:25', 'nva@example.com'),
('21', '47', '28', 'Nguyễn Văn A', '0912345678', '865122900450266', 'WAR-UR9UM08R', 'active', '2026-07-10 20:17:29', '2027-07-10 20:17:29', '2026-07-10 20:17:29', 'khach@shop.test'),
('22', '47', '26', 'Nguyễn Văn A', '0912345678', '863986954491743', 'WAR-FP5MTM4H', 'active', '2026-07-10 20:17:29', '2027-07-10 20:17:29', '2026-07-10 20:17:34', 'khach@shop.test'),
('23', '47', '29', 'Nguyễn Văn A', '0912345678', '868239826359720', 'WAR-P8QAX33N', 'active', '2026-07-10 20:17:29', '2027-07-10 20:17:29', '2026-07-10 20:17:39', 'khach@shop.test'),
('24', '47', '24', 'Nguyễn Văn A', '0912345678', '865750426261051', 'WAR-7QZXA1AO', 'active', '2026-07-10 20:17:29', '2027-07-10 20:17:29', '2026-07-10 20:17:44', 'khach@shop.test'),
('25', '47', '8', 'Nguyễn Văn A', '0912345678', '863510717734154', 'WAR-P2UC4WAL', 'active', '2026-07-10 20:17:29', '2027-07-10 20:17:29', '2026-07-10 20:17:49', 'khach@shop.test'),
('26', '47', '8', 'Nguyễn Văn A', '0912345678', '862442150095926', 'WAR-W39VZLIQ', 'active', '2026-07-10 20:17:29', '2027-07-10 20:17:29', '2026-07-10 20:17:54', 'khach@shop.test'),
('27', '47', '8', 'Nguyễn Văn A', '0912345678', '869676632770584', 'WAR-CDKUYIKO', 'active', '2026-07-10 20:17:29', '2027-07-10 20:17:29', '2026-07-10 20:18:00', 'khach@shop.test'),
('28', '47', '8', 'Nguyễn Văn A', '0912345678', '864232097793167', 'WAR-BFEZGB51', 'active', '2026-07-10 20:17:29', '2027-07-10 20:17:29', '2026-07-10 20:18:06', 'khach@shop.test'),
('29', '47', '8', 'Nguyễn Văn A', '0912345678', '865864148963046', 'WAR-VVVCWJAZ', 'active', '2026-07-10 20:17:29', '2027-07-10 20:17:29', '2026-07-10 20:18:11', 'khach@shop.test'),
('30', '47', '8', 'Nguyễn Văn A', '0912345678', '869298350609561', 'WAR-I9WMJXDQ', 'active', '2026-07-10 20:17:29', '2027-07-10 20:17:29', '2026-07-10 20:18:16', 'khach@shop.test'),
('31', '47', '8', 'Nguyễn Văn A', '0912345678', '864699292805092', 'WAR-QAPEAIGI', 'active', '2026-07-10 20:17:29', '2027-07-10 20:17:29', '2026-07-10 20:18:21', 'khach@shop.test'),
('32', '47', '8', 'Nguyễn Văn A', '0912345678', '869782477295160', 'WAR-0PT4LAG0', 'active', '2026-07-10 20:17:29', '2027-07-10 20:17:29', '2026-07-10 20:18:26', 'khach@shop.test'),
('33', '47', '8', 'Nguyễn Văn A', '0912345678', '868383424063364', 'WAR-OW1PNBGL', 'active', '2026-07-10 20:17:29', '2027-07-10 20:17:29', '2026-07-10 20:18:32', 'khach@shop.test'),
('34', '47', '8', 'Nguyễn Văn A', '0912345678', '867722938847331', 'WAR-ZN6PMBAT', 'active', '2026-07-10 20:17:29', '2027-07-10 20:17:29', '2026-07-10 20:18:37', 'khach@shop.test'),
('35', '47', '8', 'Nguyễn Văn A', '0912345678', '861744576655637', 'WAR-WLMDGYDV', 'active', '2026-07-10 20:17:29', '2027-07-10 20:17:29', '2026-07-10 20:18:43', 'khach@shop.test'),
('36', '47', '8', 'Nguyễn Văn A', '0912345678', '864050046370554', 'WAR-AM9ST8JG', 'active', '2026-07-10 20:17:29', '2027-07-10 20:17:29', '2026-07-10 20:18:48', 'khach@shop.test'),
('37', '47', '8', 'Nguyễn Văn A', '0912345678', '863152291379029', 'WAR-Q2L8NTWS', 'active', '2026-07-10 20:17:29', '2027-07-10 20:17:29', '2026-07-10 20:18:54', 'khach@shop.test'),
('38', '47', '8', 'Nguyễn Văn A', '0912345678', '861910281673136', 'WAR-LAM3LZ3Q', 'active', '2026-07-10 20:17:29', '2027-07-10 20:17:29', '2026-07-10 20:19:00', 'khach@shop.test'),
('39', '47', '8', 'Nguyễn Văn A', '0912345678', '864086314937666', 'WAR-HO9IRRG7', 'active', '2026-07-10 20:17:29', '2027-07-10 20:17:29', '2026-07-10 20:19:05', 'khach@shop.test'),
('40', '47', '8', 'Nguyễn Văn A', '0912345678', '869246761526031', 'WAR-JKWJ77PQ', 'active', '2026-07-10 20:17:29', '2027-07-10 20:17:29', '2026-07-10 20:19:10', 'khach@shop.test'),
('41', '47', '8', 'Nguyễn Văn A', '0912345678', '866731689362463', 'WAR-J8QXEDUF', 'active', '2026-07-10 20:17:29', '2027-07-10 20:17:29', '2026-07-10 20:19:15', 'khach@shop.test'),
('42', '47', '8', 'Nguyễn Văn A', '0912345678', '860355499366841', 'WAR-ALAR5H6X', 'active', '2026-07-10 20:17:29', '2027-07-10 20:17:29', '2026-07-10 20:19:20', 'khach@shop.test'),
('43', '47', '8', 'Nguyễn Văn A', '0912345678', '865024179928696', 'WAR-YUIVUGZP', 'active', '2026-07-10 20:17:29', '2027-07-10 20:17:29', '2026-07-10 20:19:27', 'khach@shop.test'),
('44', '47', '8', 'Nguyễn Văn A', '0912345678', '863750074041144', 'WAR-62GUG6HS', 'active', '2026-07-10 20:17:29', '2027-07-10 20:17:29', '2026-07-10 20:19:32', 'khach@shop.test'),
('45', '47', '8', 'Nguyễn Văn A', '0912345678', '867217733070389', 'WAR-K988RF6Z', 'active', '2026-07-10 20:17:29', '2027-07-10 20:17:29', '2026-07-10 20:19:38', 'khach@shop.test'),
('46', '47', '8', 'Nguyễn Văn A', '0912345678', '861501860695901', 'WAR-D6CILLFY', 'active', '2026-07-10 20:17:29', '2027-07-10 20:17:29', '2026-07-10 20:19:44', 'khach@shop.test'),
('47', '47', '8', 'Nguyễn Văn A', '0912345678', '860244692347896', 'WAR-UZR68QWB', 'active', '2026-07-10 20:17:29', '2027-07-10 20:17:29', '2026-07-10 20:19:50', 'khach@shop.test'),
('48', '47', '8', 'Nguyễn Văn A', '0912345678', '860048294194546', 'WAR-PC1DTJE5', 'active', '2026-07-10 20:17:29', '2027-07-10 20:17:29', '2026-07-10 20:19:56', 'khach@shop.test');

DROP TABLE IF EXISTS `warranty_claims`;
CREATE TABLE `warranty_claims` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `warranty_id` int unsigned NOT NULL,
  `customer_note` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `admin_note` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_claims_warranty` (`warranty_id`),
  CONSTRAINT `fk_claims_warranty` FOREIGN KEY (`warranty_id`) REFERENCES `warranties` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `warranty_claims` (`id`, `warranty_id`, `customer_note`, `admin_note`, `status`, `created_at`, `updated_at`) VALUES
('1', '1', 'Máy không lên nguồn', 'Đang thay thế IC nguồn chính hãng', 'repairing', '2026-07-10 04:22:02', '2026-07-10 04:22:02'),
('2', '2', 'Máy không lên nguồn', 'Đang thay thế IC nguồn chính hãng', 'repairing', '2026-07-10 04:24:55', '2026-07-10 04:24:55'),
('3', '3', 'Máy không lên nguồn', 'Đang thay thế IC nguồn chính hãng', 'repairing', '2026-07-10 04:26:03', '2026-07-10 04:26:03'),
('4', '4', 'Máy không lên nguồn', 'Đang thay thế IC nguồn chính hãng', 'repairing', '2026-07-10 04:26:54', '2026-07-10 04:26:54'),
('5', '5', 'Máy không lên nguồn', 'Đang thay thế IC nguồn chính hãng', 'repairing', '2026-07-10 04:32:11', '2026-07-10 04:32:11'),
('6', '6', 'Máy không lên nguồn', 'Đang thay thế IC nguồn chính hãng', 'repairing', '2026-07-10 04:32:30', '2026-07-10 04:32:30'),
('7', '7', 'Máy không lên nguồn', 'Đang thay thế IC nguồn chính hãng', 'repairing', '2026-07-10 04:46:57', '2026-07-10 04:46:57'),
('8', '8', 'Máy không lên nguồn', 'Đang thay thế IC nguồn chính hãng', 'repairing', '2026-07-10 04:47:12', '2026-07-10 04:47:12'),
('9', '9', 'Máy không lên nguồn', 'Đang thay thế IC nguồn chính hãng', 'repairing', '2026-07-10 04:49:43', '2026-07-10 04:49:43'),
('10', '10', 'Máy không lên nguồn', 'Đang thay thế IC nguồn chính hãng', 'repairing', '2026-07-10 04:52:29', '2026-07-10 04:52:29'),
('11', '11', 'Máy không lên nguồn', 'Đang thay thế IC nguồn chính hãng', 'repairing', '2026-07-10 04:58:10', '2026-07-10 04:58:10'),
('12', '12', 'Máy không lên nguồn', 'Đang thay thế IC nguồn chính hãng', 'repairing', '2026-07-10 05:00:23', '2026-07-10 05:00:23'),
('13', '13', 'Máy không lên nguồn', 'Đang thay thế IC nguồn chính hãng', 'repairing', '2026-07-10 05:01:08', '2026-07-10 05:01:08'),
('14', '14', 'Máy không lên nguồn', 'Đang thay thế IC nguồn chính hãng', 'repairing', '2026-07-10 05:02:38', '2026-07-10 05:02:38'),
('15', '15', 'Máy không lên nguồn', 'Đang thay thế IC nguồn chính hãng', 'repairing', '2026-07-10 05:03:08', '2026-07-10 05:03:08'),
('16', '16', 'Máy không lên nguồn', 'Đang thay thế IC nguồn chính hãng', 'repairing', '2026-07-10 19:35:54', '2026-07-10 19:35:54'),
('17', '17', 'Máy không lên nguồn', 'Đang thay thế IC nguồn chính hãng', 'repairing', '2026-07-10 19:42:45', '2026-07-10 19:42:45'),
('18', '18', 'Máy không lên nguồn', 'Đang thay thế IC nguồn chính hãng', 'repairing', '2026-07-10 19:43:35', '2026-07-10 19:43:35'),
('19', '19', 'Máy không lên nguồn', 'Đang thay thế IC nguồn chính hãng', 'repairing', '2026-07-10 19:45:04', '2026-07-10 19:45:04'),
('20', '20', 'Máy không lên nguồn', 'Đang thay thế IC nguồn chính hãng', 'repairing', '2026-07-10 19:51:25', '2026-07-10 19:51:25');

DROP VIEW IF EXISTS `v_order_product_lines`;
CREATE ALGORITHM=UNDEFINED  SQL SECURITY DEFINER VIEW `v_order_product_lines` AS select `o`.`id` AS `order_id`,`o`.`order_code` AS `order_code`,`o`.`customer_name` AS `customer_name`,`o`.`email` AS `email`,`o`.`phone` AS `phone`,`o`.`payment_method` AS `payment_method`,`o`.`payment_status` AS `payment_status`,`o`.`total_amount` AS `order_total`,`o`.`created_at` AS `order_date`,`oi`.`id` AS `line_id`,`oi`.`product_id` AS `product_id`,`oi`.`product_name` AS `product_name`,`oi`.`quantity` AS `quantity`,`oi`.`price` AS `unit_price`,(`oi`.`quantity` * `oi`.`price`) AS `line_total`,`p`.`image` AS `product_image`,`p`.`stock` AS `product_stock`,`c`.`name` AS `category_name` from (((`orders` `o` join `order_items` `oi` on((`oi`.`order_id` = `o`.`id`))) left join `products` `p` on((`p`.`id` = `oi`.`product_id`))) left join `categories` `c` on((`c`.`id` = `p`.`category_id`)));

DROP VIEW IF EXISTS `v_order_summary`;
CREATE ALGORITHM=UNDEFINED  SQL SECURITY DEFINER VIEW `v_order_summary` AS select `o`.`id` AS `order_id`,`o`.`order_code` AS `order_code`,`o`.`customer_name` AS `customer_name`,`o`.`email` AS `email`,`o`.`phone` AS `phone`,`o`.`payment_method` AS `payment_method`,`o`.`payment_status` AS `payment_status`,`o`.`total_amount` AS `total_amount`,`o`.`created_at` AS `created_at`,count(`oi`.`id`) AS `item_count`,coalesce(sum(`oi`.`quantity`),0) AS `total_quantity`,group_concat(concat(`oi`.`product_name`,' ×',`oi`.`quantity`,' @ ',format(`oi`.`price`,0),'đ') order by `oi`.`id` ASC separator ' | ') AS `product_list` from (`orders` `o` left join `order_items` `oi` on((`oi`.`order_id` = `o`.`id`))) group by `o`.`id`,`o`.`order_code`,`o`.`customer_name`,`o`.`email`,`o`.`phone`,`o`.`payment_method`,`o`.`payment_status`,`o`.`total_amount`,`o`.`created_at`;

SET FOREIGN_KEY_CHECKS = 1;
