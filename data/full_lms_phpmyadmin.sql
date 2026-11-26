-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Linux (x86_64)
--
-- Host: 172.18.0.2    Database: shop_hades
-- ------------------------------------------------------
-- Server version	8.0.46

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `admin_audit_logs`
--

DROP TABLE IF EXISTS `admin_audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_audit_logs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` int unsigned DEFAULT NULL,
  `admin_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `action` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `details` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_audit_logs`
--

LOCK TABLES `admin_audit_logs` WRITE;
/*!40000 ALTER TABLE `admin_audit_logs` DISABLE KEYS */;
INSERT INTO `admin_audit_logs` (`id`, `admin_id`, `admin_name`, `action`, `details`, `ip_address`, `created_at`) VALUES (1,1,'Admin','update_trade_in_status','Cập nhật yêu cầu Trade-in #1 sang trạng thái: completed',NULL,'2026-07-10 20:38:10'),(2,1,'Admin','price_change','Price updated for product \'Cáp sạc nhanh USB-C bọc dù 60W\' (ID: 11): 190.000 đ -> 5.000 đ','172.18.0.1','2026-08-12 06:40:06');
/*!40000 ALTER TABLE `admin_audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `api_otps`
--

DROP TABLE IF EXISTS `api_otps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `api_otps` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `action` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `expires_at` int unsigned NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `api_otps_email_code` (`email`,`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `api_otps`
--

LOCK TABLES `api_otps` WRITE;
/*!40000 ALTER TABLE `api_otps` DISABLE KEYS */;
/*!40000 ALTER TABLE `api_otps` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `app_notifications`
--

DROP TABLE IF EXISTS `app_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `app_notifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned DEFAULT NULL,
  `order_id` int unsigned DEFAULT NULL,
  `title` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `body` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_notif_user_read` (`user_id`,`is_read`),
  KEY `idx_notif_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `app_notifications`
--

LOCK TABLES `app_notifications` WRITE;
/*!40000 ALTER TABLE `app_notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `app_notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cart_items`
--

DROP TABLE IF EXISTS `cart_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cart_items`
--

LOCK TABLES `cart_items` WRITE;
/*!40000 ALTER TABLE `cart_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `cart_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=91 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` (`id`, `name`, `description`, `status`, `created_at`) VALUES (1,'Điện thoại','Điện thoại di động chính hãng, trả góp 0%','active','2026-11-26 08:24:57'),(2,'Laptop','Máy tính xách tay từ các thương hiệu hàng đầu','active','2026-11-26 08:24:57'),(3,'Phụ kiện','Tai nghe, cáp sạc, sạc dự phòng chất lượng cao','active','2026-11-26 08:24:57'),(4,'Smartwatch','Đồng hồ thông minh theo dõi sức khỏe','active','2026-11-26 08:24:57'),(5,'Đồng hồ','Đồng hồ đeo tay Casio lịch lãm, cá tính','active','2026-11-26 08:24:57'),(6,'Máy cũ','Điện thoại, Laptop đã qua sử dụng, bảo hành uy tín','active','2026-11-26 08:24:57'),(7,'Tablet','Máy tính bảng Apple iPad giải trí, làm việc tiện lợi','active','2026-11-26 08:24:57'),(79,'Lập trình & Phát triển Phần mềm','Khóa học lập trình và phát triển phần mềm','active','2026-11-26 14:17:17'),(80,'Khoa học Dữ liệu & Trí tuệ Nhân tạo','Khoa học dữ liệu, AI, Machine Learning','active','2026-11-26 14:17:17'),(81,'Thiết kế Đồ họa & UI/UX','Thiết kế UI/UX, đồ họa chuyên nghiệp','active','2026-11-26 14:17:17'),(82,'Quản trị Hệ thống, DevOps & Cloud','Quản trị hệ thống, DevOps, AWS, Cloud','active','2026-11-26 14:17:17'),(83,'Kinh doanh & Marketing Kỹ thuật số','Digital marketing và kinh doanh trực tuyến','active','2026-11-26 14:17:17'),(84,'Ngoại ngữ & Kỹ năng Mềm','Tiếng Anh chuyên ngành IT, kỹ năng mềm','active','2026-11-26 14:17:17'),(87,'Lập trình Web & Frontend','HTML, CSS, JS, PHP, React, Next.js','active','2026-11-26 14:17:17'),(88,'Khoa học Dữ liệu & AI','Python, Machine Learning, Deep Learning, SQL','active','2026-11-26 14:17:17'),(89,'Thiết kế UI/UX','Figma, Design System, User Research','active','2026-11-26 14:17:17'),(90,'Hệ thống & DevOps Cloud','Docker, Kubernetes, Linux, CI/CD','active','2026-11-26 14:17:17');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `certificates`
--

DROP TABLE IF EXISTS `certificates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `certificates` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `certificate_code` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int unsigned NOT NULL,
  `course_id` int unsigned NOT NULL,
  `issued_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `pdf_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('valid','revoked') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'valid',
  PRIMARY KEY (`id`),
  UNIQUE KEY `certificate_code` (`certificate_code`),
  UNIQUE KEY `uq_user_course_cert` (`user_id`,`course_id`),
  KEY `course_id` (`course_id`),
  KEY `idx_cert_code` (`certificate_code`),
  CONSTRAINT `certificates_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `certificates_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `certificates`
--

LOCK TABLES `certificates` WRITE;
/*!40000 ALTER TABLE `certificates` DISABLE KEYS */;
INSERT INTO `certificates` (`id`, `certificate_code`, `user_id`, `course_id`, `issued_at`, `pdf_path`, `status`) VALUES (1,'CERT-2026-X8B9K2',2,1,'2026-11-21 11:29:15',NULL,'valid'),(2,'CERT-2026-706CF3BC',1,1,'2026-11-21 11:38:44',NULL,'valid'),(3,'CERT-2026-882D3383',43,1,'2026-11-21 11:47:30',NULL,'valid'),(4,'CERT-2026-7B1F4939',45,3,'2026-11-21 11:51:24',NULL,'valid'),(5,'CERT-2026-2034C86F',45,4,'2026-11-21 11:51:24',NULL,'valid'),(6,'CERT-2026-CCA8C0B0',45,5,'2026-11-21 11:51:24',NULL,'valid'),(7,'CERT-2026-4AF648B6',46,4,'2026-11-21 11:51:24',NULL,'valid'),(8,'CERT-2026-07EE4BBD',46,5,'2026-11-21 11:51:24',NULL,'valid'),(9,'CERT-2026-8063B4BB',46,6,'2026-11-21 11:51:24',NULL,'valid'),(10,'CERT-2026-2AAA51BC',47,5,'2026-11-21 11:51:24',NULL,'valid'),(11,'CERT-2026-14FA3F86',47,6,'2026-11-21 11:51:24',NULL,'valid'),(12,'CERT-2026-C6D8B1D1',47,7,'2026-11-21 11:51:24',NULL,'valid'),(13,'CERT-2026-F0EB7673',48,6,'2026-11-21 11:51:24',NULL,'valid'),(14,'CERT-2026-C12FC5D8',48,7,'2026-11-21 11:51:24',NULL,'valid'),(15,'CERT-2026-82F61ACD',48,8,'2026-11-21 11:51:24',NULL,'valid');
/*!40000 ALTER TABLE `certificates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chat_status`
--

DROP TABLE IF EXISTS `chat_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chat_status` (
  `session_id` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_admin_typing` tinyint(1) NOT NULL DEFAULT '0',
  `is_user_typing` tinyint(1) NOT NULL DEFAULT '0',
  `admin_last_activity` datetime DEFAULT NULL,
  `user_last_activity` datetime DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chat_status`
--

LOCK TABLES `chat_status` WRITE;
/*!40000 ALTER TABLE `chat_status` DISABLE KEYS */;
INSERT INTO `chat_status` (`session_id`, `is_admin_typing`, `is_user_typing`, `admin_last_activity`, `user_last_activity`, `updated_at`) VALUES ('4514baa5121baad24582da5d7a2d47c9',0,0,'2026-07-10 20:17:06','2026-07-10 20:17:01','2026-07-10 20:17:06');
/*!40000 ALTER TABLE `chat_status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chatbot_messages`
--

DROP TABLE IF EXISTS `chatbot_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chatbot_messages`
--

LOCK TABLES `chatbot_messages` WRITE;
/*!40000 ALTER TABLE `chatbot_messages` DISABLE KEYS */;
INSERT INTO `chatbot_messages` (`id`, `user_id`, `session_key`, `role`, `content`, `source`, `created_at`) VALUES (1,NULL,'2131ec433d5e3cd661277b7daae79082','user','xin chào iPhone nào dưới 15 triệu','fallback','2026-07-10 20:09:42'),(2,NULL,'2131ec433d5e3cd661277b7daae79082','model','😅 Tiếc quá, hiện tại cửa hàng chưa có sản phẩm phù hợp với khoảng giá bạn yêu cầu. Bạn thử điều chỉnh khoảng giá hoặc hỏi về dòng sản phẩm khác nhé!','fallback','2026-07-10 20:09:42'),(3,NULL,'450846acc4ff79ea3701988d64113fe8','user','xin chào','gemini','2026-11-22 15:32:47'),(4,NULL,'450846acc4ff79ea3701988d64113fe8','model','Chào buổi tối bạn! 😊 \n\nRất vui được hỗ trợ bạn tại **Shop Hades**. Bạn đang quan tâm đến sản phẩm nào hay cần mình tư vấn thông tin gì không ạ?','gemini','2026-11-22 15:32:47'),(5,NULL,'450846acc4ff79ea3701988d64113fe8','user','cho tôi biết bạn đang sử dụng model nào','error','2026-11-22 15:33:10'),(6,NULL,'450846acc4ff79ea3701988d64113fe8','model','Rất tiếc, Hades AI hiện không thể kết nối tới máy chủ AI. Vui lòng kiểm tra lại cấu hình API Key hoặc thử lại sau giây lát! 📞 Hotline hỗ trợ: 1800 1234','error','2026-11-22 15:33:10'),(7,NULL,'450846acc4ff79ea3701988d64113fe8','user','1+1 = ?','gemini','2026-11-22 15:33:27'),(8,NULL,'450846acc4ff79ea3701988d64113fe8','model','1 + 1 = 2 ạ! 😄 \n\nBạn có cần mình hỗ trợ gì thêm về thông tin hay sản phẩm nào tại Shop Hades không nè?','gemini','2026-11-22 15:33:27');
/*!40000 ALTER TABLE `chatbot_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `course_categories`
--

DROP TABLE IF EXISTS `course_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `course_categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(180) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `icon` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'fas fa-graduation-cap',
  `sort_order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `course_categories`
--

LOCK TABLES `course_categories` WRITE;
/*!40000 ALTER TABLE `course_categories` DISABLE KEYS */;
INSERT INTO `course_categories` (`id`, `name`, `slug`, `description`, `icon`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES (1,'Lập trình Web & Mobile','lap-trinh-web-mobile',NULL,'fas fa-code',0,1,'2026-11-22 04:51:01','2026-11-22 04:51:01'),(2,'Trí tuệ Nhân tạo & Data','ai-machine-learning-data',NULL,'fas fa-brain',0,1,'2026-11-22 04:51:01','2026-11-22 04:51:01'),(3,'DevOps & Điện toán đám mây','devops-cloud-computing',NULL,'fas fa-server',0,1,'2026-11-22 04:51:01','2026-11-22 04:51:01'),(4,'Thiết kế UI/UX','thiet-ke-ui-ux',NULL,'fas fa-palette',0,1,'2026-11-22 04:51:01','2026-11-22 04:51:01'),(5,'Khoa học Máy tính','khoa-hoc-may-tinh',NULL,'fas fa-laptop-code',0,1,'2026-11-22 04:51:01','2026-11-22 04:51:01'),(10,'ss','ss','ss','fas fa-graduation-cap',0,1,'2026-11-26 15:24:54','2026-11-26 08:24:54');
/*!40000 ALTER TABLE `course_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `course_discussions`
--

DROP TABLE IF EXISTS `course_discussions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `course_discussions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `lesson_id` int unsigned NOT NULL,
  `user_id` int unsigned NOT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `idx_lesson_discuss` (`lesson_id`),
  CONSTRAINT `course_discussions_ibfk_1` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE CASCADE,
  CONSTRAINT `course_discussions_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `course_discussions`
--

LOCK TABLES `course_discussions` WRITE;
/*!40000 ALTER TABLE `course_discussions` DISABLE KEYS */;
INSERT INTO `course_discussions` (`id`, `lesson_id`, `user_id`, `content`, `created_at`) VALUES (1,1,2,'Chào thầy, em đã hoàn thành xong bài 1 và cấu hình môi trường thành công! Em sẵn sàng sang bài tiếp theo.','2026-11-21 11:45:35'),(2,1,2,'Em có câu hỏi về phần cài đặt môi trường trong bài học này.','2026-11-21 11:47:06'),(3,1,2,'Em có câu hỏi về phần cài đặt môi trường trong bài học này.','2026-11-21 11:47:30'),(4,1,2,'Em có câu hỏi về phần cài đặt môi trường trong bài học này.','2026-11-21 11:47:35'),(5,1,2,'Em có câu hỏi về phần cài đặt môi trường trong bài học này.','2026-11-21 11:48:19'),(6,1,2,'Em có câu hỏi về phần cài đặt môi trường trong bài học này.','2026-11-21 11:53:59'),(7,1,2,'Em có câu hỏi về phần cài đặt môi trường trong bài học này.','2026-11-21 11:54:34'),(8,1,2,'Em có câu hỏi về phần cài đặt môi trường trong bài học này.','2026-11-21 11:54:48'),(9,1,2,'Em có câu hỏi về phần cài đặt môi trường trong bài học này.','2026-11-21 12:06:40'),(10,1,2,'Em có câu hỏi về phần cài đặt môi trường trong bài học này.','2026-11-21 12:14:27'),(11,1,2,'Em có câu hỏi về phần cài đặt môi trường trong bài học này.','2026-11-21 13:10:30'),(12,1,2,'Em có câu hỏi về phần cài đặt môi trường trong bài học này.','2026-11-21 13:36:15'),(13,1,2,'Em có câu hỏi về phần cài đặt môi trường trong bài học này.','2026-11-21 13:36:38'),(14,1,2,'Em có câu hỏi về phần cài đặt môi trường trong bài học này.','2026-11-21 13:42:34'),(15,1,2,'Em có câu hỏi về phần cài đặt môi trường trong bài học này.','2026-11-21 14:07:17'),(16,1,2,'Em có câu hỏi về phần cài đặt môi trường trong bài học này.','2026-11-21 14:08:37'),(17,1,2,'Em có câu hỏi về phần cài đặt môi trường trong bài học này.','2026-11-21 14:09:50'),(18,1,2,'Em có câu hỏi về phần cài đặt môi trường trong bài học này.','2026-11-21 14:09:56'),(19,1,2,'Em có câu hỏi về phần cài đặt môi trường trong bài học này.','2026-11-21 14:15:27'),(20,1,2,'Em có câu hỏi về phần cài đặt môi trường trong bài học này.','2026-11-21 14:17:07'),(21,1,2,'Em có câu hỏi về phần cài đặt môi trường trong bài học này.','2026-11-21 14:18:15'),(22,1,2,'Em có câu hỏi về phần cài đặt môi trường trong bài học này.','2026-11-22 04:47:15'),(23,1,2,'Em có câu hỏi về phần cài đặt môi trường trong bài học này.','2026-11-22 04:49:51'),(24,1,2,'Em có câu hỏi về phần cài đặt môi trường trong bài học này.','2026-11-22 04:51:04'),(25,1,2,'Em có câu hỏi về phần cài đặt môi trường trong bài học này.','2026-11-22 04:54:52'),(26,1,2,'Em có câu hỏi về phần cài đặt môi trường trong bài học này.','2026-11-22 05:00:59'),(27,1,2,'Em có câu hỏi về phần cài đặt môi trường trong bài học này.','2026-11-22 05:05:38'),(28,1,2,'Em có câu hỏi về phần cài đặt môi trường trong bài học này.','2026-11-22 05:34:09');
/*!40000 ALTER TABLE `course_discussions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `course_reviews`
--

DROP TABLE IF EXISTS `course_reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `course_reviews` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `course_id` int unsigned NOT NULL,
  `user_id` int unsigned NOT NULL,
  `rating` tinyint unsigned NOT NULL DEFAULT '5',
  `comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `idx_course_rating` (`course_id`,`rating`),
  CONSTRAINT `course_reviews_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `course_reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `course_reviews`
--

LOCK TABLES `course_reviews` WRITE;
/*!40000 ALTER TABLE `course_reviews` DISABLE KEYS */;
INSERT INTO `course_reviews` (`id`, `course_id`, `user_id`, `rating`, `comment`, `created_at`) VALUES (1,1,2,5,'Khóa học cực kỳ chi tiết! Giảng viên dạy rất có tâm, giải thích mô hình MVC và bảo mật dễ hiểu.','2026-11-21 11:45:35'),(2,1,2,5,'Nhờ khóa học mà mình đã tự xây dựng được đồ án website hoàn chỉnh nộp tốt nghiệp.','2026-11-21 11:45:35'),(3,2,2,5,'Lộ trình Machine Learning rất logic, bài tập thực hành sát với yêu cầu tuyển dụng.','2026-11-21 11:45:35'),(4,3,2,5,'Phần React Hooks và Context API giảng rất sâu sắc, giải quyết triệt để vấn đề re-render.','2026-11-21 11:45:35'),(5,4,2,5,'Tuyệt vời! Khóa học hoàn toàn miễn phí nhưng chất lượng còn hơn cả nhiều khóa có phí.','2026-11-21 11:45:35'),(6,5,2,5,'Hướng dẫn Docker và CI/CD thực chiến, triển khai mượt mà lên server.','2026-11-21 11:45:35'),(7,6,2,5,'Khóa học rất hay và trực quan! Đã học được rất nhiều kiến thức bổ ích.','2026-11-21 11:47:06'),(8,12,2,5,'Khóa học rất hay và trực quan! Đã học được rất nhiều kiến thức bổ ích.','2026-11-21 11:47:30'),(9,12,2,5,'Khóa học rất hay và trực quan! Đã học được rất nhiều kiến thức bổ ích.','2026-11-21 11:47:35'),(10,12,2,5,'Khóa học rất hay và trực quan! Đã học được rất nhiều kiến thức bổ ích.','2026-11-21 11:48:19'),(11,12,2,5,'Khóa học rất hay và trực quan! Đã học được rất nhiều kiến thức bổ ích.','2026-11-21 11:53:59'),(12,12,2,5,'Khóa học rất hay và trực quan! Đã học được rất nhiều kiến thức bổ ích.','2026-11-21 11:54:34'),(13,12,2,5,'Khóa học rất hay và trực quan! Đã học được rất nhiều kiến thức bổ ích.','2026-11-21 11:54:48'),(14,12,2,5,'Khóa học rất hay và trực quan! Đã học được rất nhiều kiến thức bổ ích.','2026-11-21 12:06:40'),(15,12,2,5,'Khóa học rất hay và trực quan! Đã học được rất nhiều kiến thức bổ ích.','2026-11-21 12:14:27'),(16,12,2,5,'Khóa học rất hay và trực quan! Đã học được rất nhiều kiến thức bổ ích.','2026-11-21 13:10:30'),(17,12,2,5,'Khóa học rất hay và trực quan! Đã học được rất nhiều kiến thức bổ ích.','2026-11-21 13:36:15'),(18,12,2,5,'Khóa học rất hay và trực quan! Đã học được rất nhiều kiến thức bổ ích.','2026-11-21 13:36:38'),(19,12,2,5,'Khóa học rất hay và trực quan! Đã học được rất nhiều kiến thức bổ ích.','2026-11-21 13:42:34'),(20,12,2,5,'Khóa học rất hay và trực quan! Đã học được rất nhiều kiến thức bổ ích.','2026-11-21 14:07:17'),(21,12,2,5,'Khóa học rất hay và trực quan! Đã học được rất nhiều kiến thức bổ ích.','2026-11-21 14:08:37'),(22,12,2,5,'Khóa học rất hay và trực quan! Đã học được rất nhiều kiến thức bổ ích.','2026-11-21 14:09:50'),(23,12,2,5,'Khóa học rất hay và trực quan! Đã học được rất nhiều kiến thức bổ ích.','2026-11-21 14:09:56'),(24,12,2,5,'Khóa học rất hay và trực quan! Đã học được rất nhiều kiến thức bổ ích.','2026-11-21 14:15:27'),(25,12,2,5,'Khóa học rất hay và trực quan! Đã học được rất nhiều kiến thức bổ ích.','2026-11-21 14:17:07'),(26,12,2,5,'Khóa học rất hay và trực quan! Đã học được rất nhiều kiến thức bổ ích.','2026-11-21 14:18:15'),(27,12,2,5,'Khóa học rất hay và trực quan! Đã học được rất nhiều kiến thức bổ ích.','2026-11-22 04:47:14'),(28,12,2,5,'Khóa học rất hay và trực quan! Đã học được rất nhiều kiến thức bổ ích.','2026-11-22 04:51:04'),(29,12,2,5,'Khóa học rất hay và trực quan! Đã học được rất nhiều kiến thức bổ ích.','2026-11-22 04:54:52'),(30,12,2,5,'Khóa học rất hay và trực quan! Đã học được rất nhiều kiến thức bổ ích.','2026-11-22 05:00:59'),(31,12,2,5,'Khóa học rất hay và trực quan! Đã học được rất nhiều kiến thức bổ ích.','2026-11-22 05:05:38'),(32,12,2,5,'Khóa học rất hay và trực quan! Đã học được rất nhiều kiến thức bổ ích.','2026-11-22 05:34:09');
/*!40000 ALTER TABLE `course_reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `courses`
--

DROP TABLE IF EXISTS `courses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `courses` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `instructor_id` int unsigned NOT NULL,
  `category_id` int unsigned DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `summary` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `thumbnail` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `sale_price` decimal(12,2) DEFAULT NULL,
  `level` enum('all','beginner','intermediate','advanced') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'all',
  `status` enum('draft','published','archived') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'draft',
  `total_duration_minutes` int unsigned DEFAULT '0',
  `total_lessons` int unsigned DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `instructor_id` (`instructor_id`),
  KEY `category_id` (`category_id`),
  KEY `idx_course_status` (`status`),
  KEY `idx_course_price` (`price`,`sale_price`),
  CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`instructor_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `courses_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `courses`
--

LOCK TABLES `courses` WRITE;
/*!40000 ALTER TABLE `courses` DISABLE KEYS */;
INSERT INTO `courses` (`id`, `instructor_id`, `category_id`, `title`, `slug`, `summary`, `description`, `thumbnail`, `price`, `sale_price`, `level`, `status`, `total_duration_minutes`, `total_lessons`, `created_at`, `updated_at`) VALUES (1,25,87,'Khóa học Lập trình Web Fullstack từ Zero đến Hero','khoa-hoc-lap-trinh-web-fullstack-tu-zero-den-hero','Khoá học toàn diện giúp bạn trở thành lập trình viên web chuyên nghiệp.','Nội dung khóa học đầy đủ và nâng cao','/image/courses/web_fullstack.jpg',650000.00,320000.00,'all','published',57,6,'2026-11-21 11:19:46','2026-11-26 15:13:35'),(2,25,87,'Chinh Phục Python &amp; Trí Tuệ Nhân Tạo (AI / Machine Learning)','chinh-phuc-python-va-tri-tue-nhan-tao','Summary test','Description test','/image/banner1.png',500000.00,250000.00,'all','published',28,4,'2026-11-21 11:33:12','2026-11-26 15:02:31'),(3,25,87,'Lập trình Frontend Hiện đại với ReactJS &amp; Next.js','lap-trinh-frontend-hien-dai-reactjs','Summary test','Description test','/image/banner1.png',500000.00,250000.00,'all','published',24,4,'2026-11-21 11:33:12','2026-11-26 15:02:31'),(4,25,87,'Thiết kế Giao diện UI/UX Chuyên nghiệp với Figma','thiet-ke-giao-dien-uiux-chuyen-nghiep-figma','Summary test','Description test','/image/banner1.png',500000.00,250000.00,'all','published',24,4,'2026-11-21 11:33:12','2026-11-26 15:02:31'),(5,25,87,'DevOps Toàn Diện: Docker, Kubernetes &amp; CI/CD Pipeline','devops-toan-dien-docker-kubernetes-cicd','Summary test','Description test','/image/banner1.png',500000.00,250000.00,'all','published',27,4,'2026-11-21 11:33:12','2026-11-26 15:02:31'),(6,25,87,'Phân tích Dữ liệu Kinh doanh (Data Analytics &amp; SQL Mastery)','phan-tich-du-lieu-kinh-doanh-sql-mastery','Summary test','Description test','/image/banner1.png',500000.00,250000.00,'all','published',23,4,'2026-11-21 11:33:12','2026-11-26 15:02:31'),(7,25,87,'Chuyên Sâu Golang &amp; Kiến Trúc Microservices Hiệu Năng Cao','chuyen-sau-golang-kien-truc-microservices-hieu-nang-cao','Summary test','Description test','/image/banner1.png',500000.00,250000.00,'all','published',780,8,'2026-11-21 11:47:30','2026-11-26 15:02:31'),(8,25,87,'Lập Trình Di Động Đa Nền Tảng với Flutter &amp; Dart 2026','lap-trinh-di-dong-da-nen-tang-voi-flutter-dart-2026','Summary test','Description test','/image/banner1.png',500000.00,250000.00,'all','published',620,6,'2026-11-21 11:47:30','2026-11-26 15:02:31'),(9,25,87,'Chinh Phục Điện Toán Đám Mây AWS Solutions Architect','chinh-phuc-dien-toan-dam-may-aws-solutions-architect','Summary test','Description test','/image/banner1.png',500000.00,250000.00,'all','published',950,8,'2026-11-21 11:47:30','2026-11-26 15:02:31'),(10,25,87,'An Toàn Thông Tin &amp; Thực Hành Ethical Hacking (Pentest Web)','an-toan-thong-tin-thuc-hanh-ethical-hacking-pentest-web','Summary test','Description test','/image/banner1.png',500000.00,250000.00,'all','published',540,5,'2026-11-21 11:47:30','2026-11-26 15:02:31'),(11,25,87,'Digital Marketing Toàn Diện &amp; Tăng Trưởng Doanh Thu với AI','digital-marketing-toan-dien-tang-truong-doanh-thu-voi-ai','Summary test','Description testssss','/image/banner1.png',500000.00,250000.00,'all','published',420,5,'2026-11-21 11:47:30','2026-11-26 15:34:04'),(12,25,87,'Tiếng Anh Giao Tiếp &amp; Phỏng Vấn Dành Cho Dân IT (Tech English)','tieng-anh-giao-tiep-phong-van-danh-cho-dan-it-tech-english','Summary test','Description test1','/image/banner1.png',500000.00,250000.00,'all','published',360,4,'2026-11-21 11:47:30','2026-11-26 15:15:56');
/*!40000 ALTER TABLE `courses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `device_units`
--

DROP TABLE IF EXISTS `device_units`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `device_units`
--

LOCK TABLES `device_units` WRITE;
/*!40000 ALTER TABLE `device_units` DISABLE KEYS */;
INSERT INTO `device_units` (`id`, `imei`, `product_id`, `variant_id`, `store_id`, `order_id`, `status`, `note`, `created_at`, `updated_at`) VALUES (1,'121313234234234',15,NULL,2,NULL,'available',NULL,'2026-07-10 20:01:24','2026-07-10 20:01:24'),(2,'869842629936715',29,NULL,NULL,175,'sold','Tự sinh cho đơn hàng #175','2026-11-26 07:44:16','2026-11-26 07:46:21');
/*!40000 ALTER TABLE `device_units` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `enrollments`
--

DROP TABLE IF EXISTS `enrollments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `enrollments` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `course_id` int unsigned NOT NULL,
  `status` enum('active','expired','revoked') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `progress_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `enrolled_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `completed_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_user_course` (`user_id`,`course_id`),
  KEY `course_id` (`course_id`),
  KEY `idx_enroll_user` (`user_id`),
  CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `enrollments_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=110 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `enrollments`
--

LOCK TABLES `enrollments` WRITE;
/*!40000 ALTER TABLE `enrollments` DISABLE KEYS */;
INSERT INTO `enrollments` (`id`, `user_id`, `course_id`, `status`, `progress_percent`, `enrolled_at`, `completed_at`) VALUES (2,1,1,'active',0.00,'2026-11-21 11:38:44',NULL),(3,2,6,'active',25.00,'2026-11-21 11:39:21',NULL),(8,43,1,'active',100.00,'2026-11-21 11:47:30','2026-11-21 18:47:30'),(9,43,2,'active',25.00,'2026-11-21 11:47:30',NULL),(10,43,3,'active',25.00,'2026-11-21 11:47:30',NULL),(14,44,2,'active',70.00,'2026-11-21 11:50:40',NULL),(15,44,3,'active',70.00,'2026-11-21 11:50:40',NULL),(16,44,4,'active',70.00,'2026-11-21 11:50:40',NULL),(17,45,3,'active',100.00,'2026-11-21 11:51:24','2026-11-21 18:51:24'),(18,45,4,'active',100.00,'2026-11-21 11:51:24','2026-11-21 18:51:24'),(19,45,5,'active',100.00,'2026-11-21 11:51:24','2026-11-21 18:51:24'),(20,46,4,'active',100.00,'2026-11-21 11:51:24','2026-11-21 18:51:24'),(21,46,5,'active',100.00,'2026-11-21 11:51:24','2026-11-21 18:51:24'),(22,46,6,'active',100.00,'2026-11-21 11:51:24','2026-11-21 18:51:24'),(23,47,5,'active',100.00,'2026-11-21 11:51:24','2026-11-21 18:51:24'),(24,47,6,'active',100.00,'2026-11-21 11:51:24','2026-11-21 18:51:24'),(25,47,7,'active',100.00,'2026-11-21 11:51:24','2026-11-21 18:51:24'),(26,48,6,'active',100.00,'2026-11-21 11:51:24','2026-11-21 18:51:24'),(27,48,7,'active',100.00,'2026-11-21 11:51:24','2026-11-21 18:51:24'),(28,48,8,'active',100.00,'2026-11-21 11:51:24','2026-11-21 18:51:24'),(36,1,8,'active',0.00,'2026-11-21 13:12:03',NULL),(39,1,5,'active',0.00,'2026-11-21 13:12:34',NULL),(67,2,11,'active',0.00,'2026-11-21 14:10:52',NULL),(100,2,1,'active',16.67,'2026-11-22 05:04:10','2026-11-22 05:04:10'),(105,2,10,'active',0.00,'2026-11-22 05:29:19',NULL);
/*!40000 ALTER TABLE `enrollments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `instructor_profiles`
--

DROP TABLE IF EXISTS `instructor_profiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `instructor_profiles` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `headline` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bio` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `qualification` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `experience_years` tinyint unsigned DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  CONSTRAINT `instructor_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `instructor_profiles`
--

LOCK TABLES `instructor_profiles` WRITE;
/*!40000 ALTER TABLE `instructor_profiles` DISABLE KEYS */;
INSERT INTO `instructor_profiles` (`id`, `user_id`, `headline`, `bio`, `qualification`, `experience_years`, `created_at`) VALUES (1,25,'Senior Fullstack Engineer & Giảng viên Công nghệ','Hơn 8 năm kinh nghiệm phát triển hệ thống và giảng dạy cho hàng ngàn học viên.','Thạc sĩ Khoa học Máy tính',8,'2026-11-21 11:19:27'),(2,26,'AI Research Scientist & Data Lead','Tiến sĩ Trí tuệ Nhân tạo tại ĐH Quốc gia, chuyên gia Machine Learning và phân tích dữ liệu lớn.','Tiến sĩ Trí tuệ Nhân tạo',10,'2026-11-21 11:33:11'),(3,27,'Lead Product Designer & UX Specialist','Hơn 6 năm thiết kế sản phẩm số cho các tập đoàn công nghệ, chuyên sâu về Design System trên Figma.','Cử nhân Thiết kế Đồ họa & Tương tác',6,'2026-11-21 11:33:11'),(4,28,'DevOps & Cloud Solutions Architect','Chuyên gia tư vấn hạ tầng đám mây, Kubernetes, Docker và tự động hóa quy trình CI/CD cho doanh nghiệp.','Kỹ sư Hệ thống & AWS Certified Solutions Architect',7,'2026-11-21 11:33:11'),(5,40,'Senior Backend Engineer & Go Specialist tại VNG','Hơn 9 năm kinh nghiệm thiết kế hệ thống Microservices xử lý hàng chục triệu requests mỗi ngày với Golang, Docker, Kafka.','Thạc sĩ Khoa học Máy tính (ĐH Bách Khoa)',9,'2026-11-21 11:47:30'),(6,41,'AWS Certified Solutions Architect - Professional','Từng tư vấn di chuyển hạ tầng đám mây cho hơn 40 tập đoàn lớn, chuyên sâu về Kubernetes, Terraform và kiến trúc Serverless.','Kỹ sư Hệ thống Viễn thông & Đám mây',11,'2026-11-21 11:47:30'),(7,42,'Lead Growth Marketer & Digital Strategist','Chuyên gia tối ưu tỷ lệ chuyển đổi, quản lý ngân sách quảng cáo hơn 2 triệu USD/năm trên nền tảng Meta và Google Ads.','Cử nhân Kinh tế Đối ngoại (ĐH Ngoại Thương)',7,'2026-11-21 11:47:30'),(10,60,'GV','Giảng viên giàu kinh nghiệm thực chiến trong ngành.','GV',5,'2026-11-26 14:27:16');
/*!40000 ALTER TABLE `instructor_profiles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_queue`
--

DROP TABLE IF EXISTS `job_queue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_queue` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'default',
  `job_type` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL DEFAULT '0',
  `available_at` datetime NOT NULL,
  `reserved_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `last_error` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_jobs_pending` (`queue`,`completed_at`,`available_at`),
  KEY `idx_jobs_type` (`job_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_queue`
--

LOCK TABLES `job_queue` WRITE;
/*!40000 ALTER TABLE `job_queue` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_queue` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lesson_discussions`
--

DROP TABLE IF EXISTS `lesson_discussions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lesson_discussions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `lesson_id` int unsigned NOT NULL,
  `user_id` int unsigned NOT NULL,
  `parent_id` int unsigned DEFAULT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `parent_id` (`parent_id`),
  KEY `idx_lesson_parent` (`lesson_id`,`parent_id`),
  CONSTRAINT `lesson_discussions_ibfk_1` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lesson_discussions_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lesson_discussions_ibfk_3` FOREIGN KEY (`parent_id`) REFERENCES `lesson_discussions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lesson_discussions`
--

LOCK TABLES `lesson_discussions` WRITE;
/*!40000 ALTER TABLE `lesson_discussions` DISABLE KEYS */;
INSERT INTO `lesson_discussions` (`id`, `lesson_id`, `user_id`, `parent_id`, `content`, `created_at`) VALUES (1,1,2,NULL,'Em chào thầy, cho em hỏi thêm về cách cấu hình biến môi trường .env trên server ạ?','2026-11-21 11:52:22');
/*!40000 ALTER TABLE `lesson_discussions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lesson_materials`
--

DROP TABLE IF EXISTS `lesson_materials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lesson_materials` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `lesson_id` int unsigned NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_size_bytes` bigint unsigned DEFAULT '0',
  `download_count` int unsigned DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `lesson_id` (`lesson_id`),
  CONSTRAINT `lesson_materials_ibfk_1` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lesson_materials`
--

LOCK TABLES `lesson_materials` WRITE;
/*!40000 ALTER TABLE `lesson_materials` DISABLE KEYS */;
INSERT INTO `lesson_materials` (`id`, `lesson_id`, `title`, `file_path`, `file_type`, `file_size_bytes`, `download_count`, `created_at`) VALUES (1,1,'Giao_trinh_tong_quan_khoa_hoc.pdf','/storage/materials/sample_doc.pdf','pdf',1048576,0,'2026-11-21 11:19:46'),(2,3,'Giao_trinh_tong_quan_fullstack.pdf','/storage/materials/Giao_trinh_tong_quan_fullstack.pdf','pdf',1048576,0,'2026-11-21 11:33:11'),(3,4,'Source_code_oop_mvc.zip','/storage/materials/Source_code_oop_mvc.zip','zip',1048576,0,'2026-11-21 11:33:12'),(4,6,'Python_CheatSheet_LMS.pdf','/storage/materials/Python_CheatSheet_LMS.pdf','pdf',1048576,0,'2026-11-21 11:33:12'),(5,7,'Machine_Learning_Datasets.zip','/storage/materials/Machine_Learning_Datasets.zip','zip',1048576,0,'2026-11-21 11:33:12'),(6,9,'React_Core_Slides.pdf','/storage/materials/React_Core_Slides.pdf','pdf',1048576,0,'2026-11-21 11:33:12'),(7,10,'Hooks_Exercise_Starter.zip','/storage/materials/Hooks_Exercise_Starter.zip','zip',1048576,0,'2026-11-21 11:33:12'),(8,12,'Figma_UI_Kit_Template.fig','/storage/materials/Figma_UI_Kit_Template.fig','fig',1048576,0,'2026-11-21 11:33:12'),(9,13,'UX_Research_Framework.pdf','/storage/materials/UX_Research_Framework.pdf','pdf',1048576,0,'2026-11-21 11:33:12'),(10,15,'Dockerfile_Best_Practices.pdf','/storage/materials/Dockerfile_Best_Practices.pdf','pdf',1048576,0,'2026-11-21 11:33:12'),(11,16,'github-actions-workflow.yml','/storage/materials/github-actions-workflow.yml','yml',1048576,0,'2026-11-21 11:33:12'),(12,18,'SQL_Practice_Database.sql','/storage/materials/SQL_Practice_Database.sql','sql',1048576,0,'2026-11-21 11:33:12'),(13,19,'Query_Optimization_Guide.pdf','/storage/materials/Query_Optimization_Guide.pdf','pdf',1048576,0,'2026-11-21 11:33:12'),(14,22,'Giao-trinh-thuc-hanh.pdf','/storage/docs/sample.pdf','pdf',2450000,0,'2026-11-21 11:47:30'),(15,26,'Giao-trinh-thuc-hanh.pdf','/storage/docs/sample.pdf','pdf',2450000,0,'2026-11-21 11:47:30'),(16,30,'Giao-trinh-thuc-hanh.pdf','/storage/docs/sample.pdf','pdf',2450000,0,'2026-11-21 11:47:30'),(17,34,'Giao-trinh-thuc-hanh.pdf','/storage/docs/sample.pdf','pdf',2450000,0,'2026-11-21 11:47:30'),(18,38,'Giao-trinh-thuc-hanh.pdf','/storage/docs/sample.pdf','pdf',2450000,1,'2026-11-21 11:47:30'),(19,42,'Giao-trinh-thuc-hanh.pdf','/storage/docs/sample.pdf','pdf',2450000,0,'2026-11-21 11:47:30');
/*!40000 ALTER TABLE `lesson_materials` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lesson_notes`
--

DROP TABLE IF EXISTS `lesson_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lesson_notes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `lesson_id` int unsigned NOT NULL,
  `user_id` int unsigned NOT NULL,
  `timestamp_seconds` int unsigned NOT NULL DEFAULT '0',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `lesson_id` (`lesson_id`),
  KEY `idx_user_lesson` (`user_id`,`lesson_id`),
  CONSTRAINT `lesson_notes_ibfk_1` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lesson_notes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lesson_notes`
--

LOCK TABLES `lesson_notes` WRITE;
/*!40000 ALTER TABLE `lesson_notes` DISABLE KEYS */;
INSERT INTO `lesson_notes` (`id`, `lesson_id`, `user_id`, `timestamp_seconds`, `content`, `created_at`, `updated_at`) VALUES (1,1,2,45,'Lưu ý về cấu trúc thư mục MVC và cách gọi Database connection.','2026-11-21 11:52:22','2026-11-21 11:52:22');
/*!40000 ALTER TABLE `lesson_notes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lesson_progress`
--

DROP TABLE IF EXISTS `lesson_progress`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lesson_progress` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `lesson_id` int unsigned NOT NULL,
  `is_completed` tinyint(1) NOT NULL DEFAULT '0',
  `last_watch_second` int unsigned NOT NULL DEFAULT '0',
  `completed_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_user_lesson` (`user_id`,`lesson_id`),
  KEY `lesson_id` (`lesson_id`),
  CONSTRAINT `lesson_progress_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lesson_progress_ibfk_2` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=226 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lesson_progress`
--

LOCK TABLES `lesson_progress` WRITE;
/*!40000 ALTER TABLE `lesson_progress` DISABLE KEYS */;
INSERT INTO `lesson_progress` (`id`, `user_id`, `lesson_id`, `is_completed`, `last_watch_second`, `completed_at`, `updated_at`) VALUES (1,2,18,1,120,'2026-11-21 18:39:21','2026-11-21 11:39:21'),(2,2,5,1,0,'2026-11-21 18:39:21','2026-11-21 11:39:21'),(23,1,41,0,450,NULL,'2026-11-21 11:54:48'),(29,1,1,0,76,NULL,'2026-11-21 12:32:45'),(51,1,2,0,9,NULL,'2026-11-21 12:32:54'),(58,1,25,0,8,NULL,'2026-11-21 13:12:25'),(63,1,15,0,5,NULL,'2026-11-21 13:14:38'),(69,2,15,0,6,NULL,'2026-11-21 13:28:45'),(72,2,6,0,4,NULL,'2026-11-21 14:11:49'),(73,2,21,0,29,NULL,'2026-11-21 13:29:39'),(93,2,33,0,3,NULL,'2026-11-21 14:01:58'),(110,2,37,0,193,NULL,'2026-11-22 04:45:42'),(112,2,46,0,0,NULL,'2026-11-21 14:11:42'),(129,2,50,0,0,NULL,'2026-11-22 04:40:37'),(174,2,38,0,108,NULL,'2026-11-22 04:47:30'),(222,2,41,0,180,NULL,'2026-11-22 05:34:09'),(224,2,19,0,4,NULL,'2026-11-22 05:36:42');
/*!40000 ALTER TABLE `lesson_progress` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lesson_videos`
--

DROP TABLE IF EXISTS `lesson_videos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lesson_videos` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `lesson_id` int unsigned NOT NULL,
  `provider` enum('local','youtube','vimeo','s3') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'local',
  `video_url` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `duration_seconds` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lesson_id` (`lesson_id`),
  CONSTRAINT `lesson_videos_ibfk_1` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lesson_videos`
--

LOCK TABLES `lesson_videos` WRITE;
/*!40000 ALTER TABLE `lesson_videos` DISABLE KEYS */;
INSERT INTO `lesson_videos` (`id`, `lesson_id`, `provider`, `video_url`, `duration_seconds`, `created_at`) VALUES (1,1,'youtube','https://www.youtube.com/watch?v=dQw4w9WgXcQ',900,'2026-11-21 11:19:46'),(2,3,'youtube','https://www.youtube.com/embed/zJSY8tbf_ys',600,'2026-11-21 11:33:11'),(3,4,'youtube','https://www.youtube.com/embed/2eebptXfEvw',720,'2026-11-21 11:33:11'),(4,6,'youtube','https://www.youtube.com/embed/kqtD5dpn9C8',750,'2026-11-21 11:33:12'),(5,7,'youtube','https://www.youtube.com/embed/i_LwzRVP7bg',900,'2026-11-21 11:33:12'),(6,9,'youtube','https://www.youtube.com/embed/bMknfKXIFA8',680,'2026-11-21 11:33:12'),(7,10,'youtube','https://www.youtube.com/embed/W6NZfCO5SIk',750,'2026-11-21 11:33:12'),(8,12,'youtube','https://www.youtube.com/embed/FTFaQWZBqQ8',640,'2026-11-21 11:33:12'),(9,13,'youtube','https://www.youtube.com/embed/c9Wg6Cb_YlU',800,'2026-11-21 11:33:12'),(10,15,'youtube','https://www.youtube.com/embed/3c-iBn73dDE',820,'2026-11-21 11:33:12'),(11,16,'youtube','https://www.youtube.com/embed/pg19Z8LL0vQ',780,'2026-11-21 11:33:12'),(12,18,'youtube','https://www.youtube.com/embed/ua-CiDNNj30',650,'2026-11-21 11:33:12'),(13,19,'youtube','https://www.youtube.com/embed/2eebptXfEvw',700,'2026-11-21 11:33:12'),(14,21,'youtube','https://www.youtube.com/embed/YS4e4q9oBaU',900,'2026-11-21 11:47:30'),(15,23,'youtube','https://www.youtube.com/embed/YS4e4q9oBaU',1500,'2026-11-21 11:47:30'),(16,25,'youtube','https://www.youtube.com/embed/VPvVD8t02U8',900,'2026-11-21 11:47:30'),(17,27,'youtube','https://www.youtube.com/embed/VPvVD8t02U8',1500,'2026-11-21 11:47:30'),(18,29,'youtube','https://www.youtube.com/embed/Ia-UEYYR44s',900,'2026-11-21 11:47:30'),(19,31,'youtube','https://www.youtube.com/embed/Ia-UEYYR44s',1500,'2026-11-21 11:47:30'),(20,33,'youtube','https://www.youtube.com/embed/3Kq1MIfTWCE',900,'2026-11-21 11:47:30'),(21,35,'youtube','https://www.youtube.com/embed/3Kq1MIfTWCE',1500,'2026-11-21 11:47:30'),(22,37,'youtube','https://www.youtube.com/embed/nU-IIXBWlS4',900,'2026-11-21 11:47:30'),(23,39,'youtube','https://www.youtube.com/embed/nU-IIXBWlS4',1500,'2026-11-21 11:47:30'),(24,41,'youtube','https://www.youtube.com/embed/f02mOEt11OQ',600,'2026-11-21 11:47:30'),(25,43,'youtube','https://www.youtube.com/embed/0fYi8SGA20k',1500,'2026-11-21 11:47:30'),(26,42,'youtube','https://www.youtube.com/embed/8pDqJVdNa44',900,'2026-11-21 12:05:36'),(27,2,'youtube','https://www.youtube.com/embed/2eebptXfEvw',1200,'2026-11-21 12:05:36'),(28,22,'youtube','https://www.youtube.com/embed/YS4e4q9oBaU',1200,'2026-11-21 12:05:36'),(29,26,'youtube','https://www.youtube.com/embed/VPvVD8t02U8',1200,'2026-11-21 12:05:36'),(30,30,'youtube','https://www.youtube.com/embed/Ia-UEYYR44s',1200,'2026-11-21 12:05:36'),(31,34,'youtube','https://www.youtube.com/embed/3Kq1MIfTWCE',1200,'2026-11-21 12:05:36'),(32,38,'youtube','https://www.youtube.com/embed/nU-IIXBWlS4',1200,'2026-11-21 12:05:36');
/*!40000 ALTER TABLE `lesson_videos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lessons`
--

DROP TABLE IF EXISTS `lessons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lessons` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `section_id` int unsigned NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lesson_type` enum('video','document','quiz') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'video',
  `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `is_preview` tinyint(1) DEFAULT '0',
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_lesson_sort` (`section_id`,`sort_order`),
  CONSTRAINT `lessons_ibfk_1` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lessons`
--

LOCK TABLES `lessons` WRITE;
/*!40000 ALTER TABLE `lessons` DISABLE KEYS */;
INSERT INTO `lessons` (`id`, `section_id`, `title`, `slug`, `lesson_type`, `content`, `is_preview`, `sort_order`, `created_at`) VALUES (1,1,'Bài 1: Tổng quan Khóa học & Cài đặt VS Code (Đã sửa)','bai-1-gioi-thieu-khoa-hoc','video','',1,1,'2026-11-21 11:19:46'),(2,1,'Bài 2: Hiểu rõ mô hình MVC trong thực tế','bai-2-hieu-ro-mo-hinh-mvc','video',NULL,1,2,'2026-11-21 11:19:46'),(3,2,'Bài 1: Tổng quan lộ trình Fullstack & Thiết lập môi trường','b-i-1-t-ng-quan-l-tr-nh-fullstack-thi-t-l-p-m-i-tr-ng','video',NULL,1,1,'2026-11-21 11:33:11'),(4,2,'Bài 2: Làm chủ kiến trúc Backend MVC & Hướng đối tượng OOP','b-i-2-l-m-ch-ki-n-tr-c-backend-mvc-h-ng-i-t-ng-oop','video',NULL,0,2,'2026-11-21 11:33:11'),(5,2,'Bài 3: Bài kiểm tra đánh giá kiến thức Chương 1','b-i-3-b-i-ki-m-tra-nh-gi-ki-n-th-c-ch-ng-1','quiz',NULL,0,3,'2026-11-21 11:33:12'),(6,3,'Bài 1: Cú pháp Python, List, Dict và Hàm xử lý','b-i-1-c-ph-p-python-list-dict-v-h-m-x-l-','video',NULL,1,1,'2026-11-21 11:33:12'),(7,3,'Bài 2: Tổng quan về Machine Learning & Học có giám sát','b-i-2-t-ng-quan-v-machine-learning-h-c-c-gi-m-s-t','video',NULL,0,3,'2026-11-21 11:33:12'),(8,3,'Bài 3: Bài kiểm tra Kiến thức Python & Thuật toán Học máy','b-i-3-b-i-ki-m-tra-ki-n-th-c-python-thu-t-to-n-h-c-m-y','quiz',NULL,0,4,'2026-11-21 11:33:12'),(9,4,'Bài 1: JSX, Props, State và Vòng đời Component','b-i-1-jsx-props-state-v-v-ng-i-component','video',NULL,1,1,'2026-11-21 11:33:12'),(10,4,'Bài 2: Làm chủ React Hooks: useState, useEffect, useRef','b-i-2-l-m-ch-react-hooks-usestate-useeffect-useref','video',NULL,0,3,'2026-11-21 11:33:12'),(11,4,'Bài 3: Bài thi trắc nghiệm ReactJS & Virtual DOM','b-i-3-b-i-thi-tr-c-nghi-m-reactjs-virtual-dom','quiz',NULL,0,4,'2026-11-21 11:33:12'),(12,5,'Bài 1: Làm quen giao diện Figma, Frame, Auto Layout và Variants','b-i-1-l-m-quen-giao-di-n-figma-frame-auto-layout-v-variants','video',NULL,1,1,'2026-11-21 11:33:12'),(13,5,'Bài 2: Tư duy UX, Quy trình Phỏng vấn Người dùng & Wireframing','b-i-2-t-duy-ux-quy-tr-nh-ph-ng-v-n-ng-i-d-ng-wireframing','video',NULL,0,3,'2026-11-21 11:33:12'),(14,5,'Bài 3: Trắc nghiệm Kiến thức UI/UX & Nguyên lý Thiết kế','b-i-3-tr-c-nghi-m-ki-n-th-c-ui-ux-nguy-n-l-thi-t-k-','quiz',NULL,0,4,'2026-11-21 11:33:12'),(15,6,'Bài 1: Docker Architecture, Images, Containers & Viết Dockerfile','b-i-1-docker-architecture-images-containers-vi-t-dockerfile','video',NULL,1,1,'2026-11-21 11:33:12'),(16,6,'Bài 2: GitOps & Tự động hóa Pipeline CI/CD với GitHub Actions','b-i-2-gitops-t-ng-h-a-pipeline-ci-cd-v-i-github-actions','video',NULL,0,3,'2026-11-21 11:33:12'),(17,6,'Bài 3: Trắc nghiệm Kiến thức Docker & Containerization','b-i-3-tr-c-nghi-m-ki-n-th-c-docker-containerization','quiz',NULL,0,4,'2026-11-21 11:33:12'),(18,7,'Bài 1: Cú pháp SELECT, GROUP BY, HAVING & Kỹ thuật JOIN nhiều bảng','b-i-1-c-ph-p-select-group-by-having-k-thu-t-join-nhi-u-b-ng','video',NULL,1,1,'2026-11-21 11:33:12'),(19,7,'Bài 2: Đánh chỉ mục Index (B-Tree) & Tối ưu hiệu năng câu truy vấn','b-i-2-nh-ch-m-c-index-b-tree-t-i-u-hi-u-n-ng-c-u-truy-v-n','video',NULL,0,3,'2026-11-21 11:33:12'),(20,7,'Bài 3: Trắc nghiệm Kiến thức SQL & Phân tích Dữ liệu','b-i-3-tr-c-nghi-m-ki-n-th-c-sql-ph-n-t-ch-d-li-u','quiz',NULL,0,4,'2026-11-21 11:33:12'),(21,8,'01. Giới thiệu tổng quan & Lộ trình học tập','chuyen-sau-golang-kien-truc-microservices-hieu-nang-cao-bai-1','video','Chào mừng các bạn đến với khóa học!',1,1,'2026-11-21 11:47:30'),(22,8,'02. Cài đặt công cụ và Hướng dẫn thực hành','chuyen-sau-golang-kien-truc-microservices-hieu-nang-cao-bai-2','document','Tài liệu hướng dẫn chi tiết từng bước cài đặt.',0,2,'2026-11-21 11:47:30'),(23,9,'03. Kỹ thuật nâng cao và Best Practices','chuyen-sau-golang-kien-truc-microservices-hieu-nang-cao-bai-3','video','Video phân tích chi tiết các kỹ thuật thực chiến trong dự án thực tế.',0,1,'2026-11-21 11:47:30'),(24,9,'04. Bài kiểm tra trắc nghiệm đánh giá năng lực','chuyen-sau-golang-kien-truc-microservices-hieu-nang-cao-bai-4','quiz','Hoàn thành bài kiểm tra để đủ điều kiện nhận chứng chỉ.',0,2,'2026-11-21 11:47:30'),(25,10,'01. Giới thiệu tổng quan & Lộ trình học tập','lap-trinh-di-dong-da-nen-tang-voi-flutter-dart-2026-bai-1','video','Chào mừng các bạn đến với khóa học!',1,1,'2026-11-21 11:47:30'),(26,10,'02. Cài đặt công cụ và Hướng dẫn thực hành','lap-trinh-di-dong-da-nen-tang-voi-flutter-dart-2026-bai-2','document','Tài liệu hướng dẫn chi tiết từng bước cài đặt.',0,2,'2026-11-21 11:47:30'),(27,11,'03. Kỹ thuật nâng cao và Best Practices','lap-trinh-di-dong-da-nen-tang-voi-flutter-dart-2026-bai-3','video','Video phân tích chi tiết các kỹ thuật thực chiến trong dự án thực tế.',0,1,'2026-11-21 11:47:30'),(28,11,'04. Bài kiểm tra trắc nghiệm đánh giá năng lực','lap-trinh-di-dong-da-nen-tang-voi-flutter-dart-2026-bai-4','quiz','Hoàn thành bài kiểm tra để đủ điều kiện nhận chứng chỉ.',0,2,'2026-11-21 11:47:30'),(29,12,'01. Giới thiệu tổng quan & Lộ trình học tập','chinh-phuc-dien-toan-dam-may-aws-solutions-architect-bai-1','video','Chào mừng các bạn đến với khóa học!',1,1,'2026-11-21 11:47:30'),(30,12,'02. Cài đặt công cụ và Hướng dẫn thực hành','chinh-phuc-dien-toan-dam-may-aws-solutions-architect-bai-2','document','Tài liệu hướng dẫn chi tiết từng bước cài đặt.',0,2,'2026-11-21 11:47:30'),(31,13,'03. Kỹ thuật nâng cao và Best Practices','chinh-phuc-dien-toan-dam-may-aws-solutions-architect-bai-3','video','Video phân tích chi tiết các kỹ thuật thực chiến trong dự án thực tế.',0,1,'2026-11-21 11:47:30'),(32,13,'04. Bài kiểm tra trắc nghiệm đánh giá năng lực','chinh-phuc-dien-toan-dam-may-aws-solutions-architect-bai-4','quiz','Hoàn thành bài kiểm tra để đủ điều kiện nhận chứng chỉ.',0,2,'2026-11-21 11:47:30'),(33,14,'01. Giới thiệu tổng quan & Lộ trình học tập','an-toan-thong-tin-thuc-hanh-ethical-hacking-pentest-web-bai-1','video','Chào mừng các bạn đến với khóa học!',1,1,'2026-11-21 11:47:30'),(34,14,'02. Cài đặt công cụ và Hướng dẫn thực hành','an-toan-thong-tin-thuc-hanh-ethical-hacking-pentest-web-bai-2','document','Tài liệu hướng dẫn chi tiết từng bước cài đặt.',0,2,'2026-11-21 11:47:30'),(35,15,'03. Kỹ thuật nâng cao và Best Practices','an-toan-thong-tin-thuc-hanh-ethical-hacking-pentest-web-bai-3','video','Video phân tích chi tiết các kỹ thuật thực chiến trong dự án thực tế.',0,1,'2026-11-21 11:47:30'),(36,15,'04. Bài kiểm tra trắc nghiệm đánh giá năng lực','an-toan-thong-tin-thuc-hanh-ethical-hacking-pentest-web-bai-4','quiz','Hoàn thành bài kiểm tra để đủ điều kiện nhận chứng chỉ.',0,2,'2026-11-21 11:47:30'),(37,16,'01. Giới thiệu tổng quan & Lộ trình học tập','digital-marketing-toan-dien-tang-truong-doanh-thu-voi-ai-bai-1','video','Chào mừng các bạn đến với khóa học!',1,1,'2026-11-21 11:47:30'),(38,16,'02. Cài đặt công cụ và Hướng dẫn thực hành','digital-marketing-toan-dien-tang-truong-doanh-thu-voi-ai-bai-2','document','Tài liệu hướng dẫn chi tiết từng bước cài đặt.',0,2,'2026-11-21 11:47:30'),(39,17,'03. Kỹ thuật nâng cao và Best Practices','digital-marketing-toan-dien-tang-truong-doanh-thu-voi-ai-bai-3','video','Video phân tích chi tiết các kỹ thuật thực chiến trong dự án thực tế.',0,1,'2026-11-21 11:47:30'),(40,17,'04. Bài kiểm tra trắc nghiệm đánh giá năng lực','digital-marketing-toan-dien-tang-truong-doanh-thu-voi-ai-bai-4','quiz','Hoàn thành bài kiểm tra để đủ điều kiện nhận chứng chỉ.',0,2,'2026-11-21 11:47:30'),(41,18,'01. Giới thiệu tổng quan & Lộ trình học tập','tieng-anh-giao-tiep-phong-van-danh-cho-dan-it-tech-english-bai-1','video','Chào mừng các bạn đến với khóa học!',1,1,'2026-11-21 11:47:30'),(42,18,'02. Cài đặt công cụ và Hướng dẫn thực hành','tieng-anh-giao-tiep-phong-van-danh-cho-dan-it-tech-english-bai-2','document','Tài liệu hướng dẫn chi tiết từng bước cài đặt.',0,2,'2026-11-21 11:47:30'),(43,19,'03. Kỹ thuật nâng cao và Best Practices','tieng-anh-giao-tiep-phong-van-danh-cho-dan-it-tech-english-bai-3','video','Video phân tích chi tiết các kỹ thuật thực chiến trong dự án thực tế.',0,1,'2026-11-21 11:47:30'),(44,19,'04. Bài kiểm tra trắc nghiệm đánh giá năng lực','tieng-anh-giao-tiep-phong-van-danh-cho-dan-it-tech-english-bai-4','quiz','Hoàn thành bài kiểm tra để đủ điều kiện nhận chứng chỉ.',0,2,'2026-11-21 11:47:30'),(45,1,'Kiểm tra 15 phút: Kiến thức Nền tảng Web & Cấu trúc MVC',NULL,'quiz',NULL,0,3,'2026-11-21 14:07:38'),(46,3,'Kiểm tra 15 phút: Cú pháp Python & Cấu trúc dữ liệu',NULL,'quiz',NULL,0,2,'2026-11-21 14:08:32'),(47,4,'Kiểm tra 15 phút: JSX, Props và Component React',NULL,'quiz',NULL,0,2,'2026-11-21 14:08:32'),(48,5,'Kiểm tra 15 phút: Auto Layout & Hệ thống Variants Figma',NULL,'quiz',NULL,0,2,'2026-11-21 14:08:32'),(49,6,'Kiểm tra 15 phút: Khái niệm Docker, Image và Container',NULL,'quiz',NULL,0,2,'2026-11-21 14:08:32'),(50,7,'Kiểm tra 15 phút: Kỹ thuật Truy vấn SQL & JOIN Bảng',NULL,'quiz',NULL,0,2,'2026-11-21 14:08:32');
/*!40000 ALTER TABLE `lessons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_items` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int unsigned NOT NULL,
  `product_id` int unsigned DEFAULT NULL,
  `course_id` int unsigned DEFAULT NULL,
  `product_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int unsigned NOT NULL DEFAULT '1',
  `price` bigint unsigned NOT NULL DEFAULT '0',
  `variant_id` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_items_order_id` (`order_id`),
  KEY `order_items_product_id` (`product_id`),
  CONSTRAINT `order_items_order_fk` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=190 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `course_id`, `product_name`, `quantity`, `price`, `variant_id`) VALUES (12,9,33,NULL,'iPhone 15 Pro Test',1,25000000,NULL),(13,10,34,NULL,'iPhone 15 Pro Test',1,25000000,NULL),(14,11,35,NULL,'iPhone 15 Pro Test',1,25000000,NULL),(15,12,36,NULL,'iPhone 15 Pro Test',1,25000000,NULL),(16,13,37,NULL,'iPhone 15 Pro Test',1,25000000,NULL),(17,14,39,NULL,'iPhone 15 Pro Test',1,25000000,NULL),(18,15,41,NULL,'iPhone 15 Pro Test',1,25000000,NULL),(19,16,43,NULL,'iPhone 15 Pro Test',1,25000000,NULL),(20,17,45,NULL,'iPhone 15 Pro Test',1,25000000,NULL),(21,18,47,NULL,'iPhone 15 Pro Test',1,25000000,NULL),(22,19,49,NULL,'iPhone 15 Pro Test',1,25000000,NULL),(23,20,51,NULL,'iPhone 15 Pro Test',1,25000000,NULL),(26,23,55,NULL,'iPhone 15 Pro Test',1,25000000,NULL),(29,26,59,NULL,'iPhone 15 Pro Test',1,25000000,NULL),(32,29,63,NULL,'iPhone 15 Pro Test',1,25000000,NULL),(35,32,67,NULL,'iPhone 15 Pro Test',1,25000000,NULL),(38,35,71,NULL,'iPhone 15 Pro Test',1,25000000,NULL),(41,38,75,NULL,'iPhone 15 Pro Test',1,25000000,NULL),(44,41,79,NULL,'iPhone 15 Pro Test',1,25000000,NULL),(47,44,83,NULL,'iPhone 15 Pro Test',1,25000000,NULL),(103,83,90,NULL,'iPhone 15 Pro Test',1,25000000,NULL),(104,84,91,NULL,'Máy thiếu IMEI Test',1,12000000,NULL),(113,93,99,NULL,'iPhone 15 Pro Test',1,25000000,NULL),(114,94,100,NULL,'Máy thiếu IMEI Test',1,12000000,NULL),(119,99,106,NULL,'iPhone 15 Pro Test',1,25000000,NULL),(120,100,107,NULL,'Máy thiếu IMEI Test',1,12000000,NULL),(123,105,3,3,'Lập trình Frontend Hiện đại với ReactJS & Next.js',1,320000,NULL),(124,106,4,4,'Thiết kế Giao diện UI/UX Chuyên nghiệp với Figma',1,450000,NULL),(125,107,5,5,'DevOps Toàn Diện: Docker, Kubernetes & CI/CD Pipeline',1,799000,NULL),(126,108,6,6,'Phân tích Dữ liệu Kinh doanh (Data Analytics & SQL Mastery)',1,0,NULL),(129,111,8,8,'Lập Trình Di Động Đa Nền Tảng với Flutter & Dart 2026',1,390000,NULL),(130,114,5,5,'DevOps Toàn Diện: Docker, Kubernetes & CI/CD Pipeline',1,799000,NULL),(131,115,5,5,'DevOps Toàn Diện: Docker, Kubernetes & CI/CD Pipeline',1,799000,NULL),(132,116,2,2,'Chinh Phục Python & Trí Tuệ Nhân Tạo (AI / Machine Learning)',1,499000,NULL),(133,117,7,7,'Chuyên Sâu Golang & Kiến Trúc Microservices Hiệu Năng Cao',1,450000,NULL),(137,121,2,2,'Chinh Phục Python & Trí Tuệ Nhân Tạo (AI / Machine Learning)',1,499000,NULL),(138,122,3,3,'Lập trình Frontend Hiện đại với ReactJS & Next.js',1,320000,NULL),(139,123,3,3,'Lập trình Frontend Hiện đại với ReactJS & Next.js',1,320000,NULL),(142,126,10,10,'An Toàn Thông Tin & Thực Hành Ethical Hacking (Pentest Web)',1,490000,NULL),(145,129,10,10,'An Toàn Thông Tin & Thực Hành Ethical Hacking (Pentest Web)',1,490000,NULL),(153,137,2,2,'Chinh Phục Python & Trí Tuệ Nhân Tạo (AI / Machine Learning)',1,499000,NULL),(182,168,10,10,'An Toàn Thông Tin & Thực Hành Ethical Hacking (Pentest Web)',1,490000,NULL),(186,172,22,NULL,'Đồng hồ Casio Classic MTP Dây Da',1,2640000,NULL),(187,173,29,NULL,'iPad Air 6 M2 2026',1,16490000,NULL),(188,174,29,NULL,'iPad Air 6 M2 2026',1,16490000,NULL),(189,175,29,NULL,'iPad Air 6 M2 2026',1,16490000,NULL);
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_status_logs`
--

DROP TABLE IF EXISTS `order_status_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_status_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int unsigned NOT NULL,
  `from_status` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `to_status` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `actor_type` enum('admin','shipper','system','customer') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `actor_id` int unsigned DEFAULT NULL,
  `note` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_order_created` (`order_id`,`created_at`),
  CONSTRAINT `order_status_logs_fk` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=163 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_status_logs`
--

LOCK TABLES `order_status_logs` WRITE;
/*!40000 ALTER TABLE `order_status_logs` DISABLE KEYS */;
INSERT INTO `order_status_logs` (`id`, `order_id`, `from_status`, `to_status`, `actor_type`, `actor_id`, `note`, `created_at`) VALUES (1,9,'pending','confirmed','system',NULL,NULL,'2026-07-10 04:22:01'),(2,9,'confirmed','packing','system',NULL,NULL,'2026-07-10 04:22:01'),(3,9,'packing','shipping','system',NULL,NULL,'2026-07-10 04:22:01'),(4,9,'shipping','completed','system',NULL,NULL,'2026-07-10 04:22:02'),(5,10,'pending','confirmed','system',NULL,NULL,'2026-07-10 04:24:55'),(6,10,'confirmed','packing','system',NULL,NULL,'2026-07-10 04:24:55'),(7,10,'packing','shipping','system',NULL,NULL,'2026-07-10 04:24:55'),(8,10,'shipping','completed','system',NULL,NULL,'2026-07-10 04:24:55'),(9,11,'pending','confirmed','system',NULL,NULL,'2026-07-10 04:26:03'),(10,11,'confirmed','packing','system',NULL,NULL,'2026-07-10 04:26:03'),(11,11,'packing','shipping','system',NULL,NULL,'2026-07-10 04:26:03'),(12,11,'shipping','completed','system',NULL,NULL,'2026-07-10 04:26:03'),(13,12,'pending','confirmed','system',NULL,NULL,'2026-07-10 04:26:54'),(14,12,'confirmed','packing','system',NULL,NULL,'2026-07-10 04:26:54'),(15,12,'packing','shipping','system',NULL,NULL,'2026-07-10 04:26:54'),(16,12,'shipping','completed','system',NULL,NULL,'2026-07-10 04:26:54'),(17,13,'pending','confirmed','system',NULL,NULL,'2026-07-10 04:32:11'),(18,13,'confirmed','packing','system',NULL,NULL,'2026-07-10 04:32:11'),(19,13,'packing','shipping','system',NULL,NULL,'2026-07-10 04:32:11'),(20,13,'shipping','completed','system',NULL,NULL,'2026-07-10 04:32:11'),(21,14,'pending','confirmed','system',NULL,NULL,'2026-07-10 04:32:29'),(22,14,'confirmed','packing','system',NULL,NULL,'2026-07-10 04:32:29'),(23,14,'packing','shipping','system',NULL,NULL,'2026-07-10 04:32:30'),(24,14,'shipping','completed','system',NULL,NULL,'2026-07-10 04:32:30'),(25,15,'pending','confirmed','system',NULL,NULL,'2026-07-10 04:46:57'),(26,15,'confirmed','packing','system',NULL,NULL,'2026-07-10 04:46:57'),(27,15,'packing','shipping','system',NULL,NULL,'2026-07-10 04:46:57'),(28,15,'shipping','completed','system',NULL,NULL,'2026-07-10 04:46:57'),(29,16,'pending','confirmed','system',NULL,NULL,'2026-07-10 04:47:12'),(30,16,'confirmed','packing','system',NULL,NULL,'2026-07-10 04:47:12'),(31,16,'packing','shipping','system',NULL,NULL,'2026-07-10 04:47:12'),(32,16,'shipping','completed','system',NULL,NULL,'2026-07-10 04:47:12'),(33,17,'pending','confirmed','system',NULL,NULL,'2026-07-10 04:49:43'),(34,17,'confirmed','packing','system',NULL,NULL,'2026-07-10 04:49:43'),(35,17,'packing','shipping','system',NULL,NULL,'2026-07-10 04:49:43'),(36,17,'shipping','completed','system',NULL,NULL,'2026-07-10 04:49:43'),(37,18,'pending','confirmed','system',NULL,NULL,'2026-07-10 04:52:29'),(38,18,'confirmed','packing','system',NULL,NULL,'2026-07-10 04:52:29'),(39,18,'packing','shipping','system',NULL,NULL,'2026-07-10 04:52:29'),(40,18,'shipping','completed','system',NULL,NULL,'2026-07-10 04:52:29'),(41,19,'pending','confirmed','system',NULL,NULL,'2026-07-10 04:58:10'),(42,19,'confirmed','packing','system',NULL,NULL,'2026-07-10 04:58:10'),(43,19,'packing','shipping','system',NULL,NULL,'2026-07-10 04:58:10'),(44,19,'shipping','completed','system',NULL,NULL,'2026-07-10 04:58:10'),(45,20,'pending','confirmed','system',NULL,NULL,'2026-07-10 05:00:23'),(46,20,'confirmed','packing','system',NULL,NULL,'2026-07-10 05:00:23'),(47,20,'packing','shipping','system',NULL,NULL,'2026-07-10 05:00:23'),(48,20,'shipping','completed','system',NULL,NULL,'2026-07-10 05:00:23'),(49,23,'pending','confirmed','system',NULL,NULL,'2026-07-10 05:01:08'),(50,23,'confirmed','packing','system',NULL,NULL,'2026-07-10 05:01:08'),(51,23,'packing','shipping','system',NULL,NULL,'2026-07-10 05:01:08'),(52,23,'shipping','completed','system',NULL,NULL,'2026-07-10 05:01:08'),(53,26,'pending','confirmed','system',NULL,NULL,'2026-07-10 05:02:38'),(54,26,'confirmed','packing','system',NULL,NULL,'2026-07-10 05:02:38'),(55,26,'packing','shipping','system',NULL,NULL,'2026-07-10 05:02:38'),(56,26,'shipping','completed','system',NULL,NULL,'2026-07-10 05:02:38'),(57,29,'pending','confirmed','system',NULL,NULL,'2026-07-10 05:03:08'),(58,29,'confirmed','packing','system',NULL,NULL,'2026-07-10 05:03:08'),(59,29,'packing','shipping','system',NULL,NULL,'2026-07-10 05:03:08'),(60,29,'shipping','completed','system',NULL,NULL,'2026-07-10 05:03:08'),(61,32,'pending','confirmed','system',NULL,NULL,'2026-07-10 19:35:54'),(62,32,'confirmed','packing','system',NULL,NULL,'2026-07-10 19:35:54'),(63,32,'packing','shipping','system',NULL,NULL,'2026-07-10 19:35:54'),(64,32,'shipping','completed','system',NULL,NULL,'2026-07-10 19:35:54'),(65,35,'pending','confirmed','system',NULL,NULL,'2026-07-10 19:42:45'),(66,35,'confirmed','packing','system',NULL,NULL,'2026-07-10 19:42:45'),(67,35,'packing','shipping','system',NULL,NULL,'2026-07-10 19:42:45'),(68,35,'shipping','completed','system',NULL,NULL,'2026-07-10 19:42:45'),(69,38,'pending','confirmed','system',NULL,NULL,'2026-07-10 19:43:35'),(70,38,'confirmed','packing','system',NULL,NULL,'2026-07-10 19:43:35'),(71,38,'packing','shipping','system',NULL,NULL,'2026-07-10 19:43:35'),(72,38,'shipping','completed','system',NULL,NULL,'2026-07-10 19:43:35'),(73,41,'pending','confirmed','system',NULL,NULL,'2026-07-10 19:45:04'),(74,41,'confirmed','packing','system',NULL,NULL,'2026-07-10 19:45:04'),(75,41,'packing','shipping','system',NULL,NULL,'2026-07-10 19:45:04'),(76,41,'shipping','completed','system',NULL,NULL,'2026-07-10 19:45:04'),(77,44,'pending','confirmed','system',NULL,NULL,'2026-07-10 19:51:25'),(78,44,'confirmed','packing','system',NULL,NULL,'2026-07-10 19:51:25'),(79,44,'packing','shipping','system',NULL,NULL,'2026-07-10 19:51:25'),(80,44,'shipping','completed','system',NULL,NULL,'2026-07-10 19:51:25'),(133,83,'pending','confirmed','system',NULL,NULL,'2026-11-21 11:37:44'),(134,83,'confirmed','packing','system',NULL,NULL,'2026-11-21 11:37:44'),(135,83,'packing','shipping','system',NULL,NULL,'2026-11-21 11:37:44'),(136,83,'shipping','completed','system',NULL,NULL,'2026-11-21 11:37:49'),(137,84,'pending','confirmed','system',NULL,NULL,'2026-11-21 11:37:49'),(138,84,'confirmed','packing','system',NULL,NULL,'2026-11-21 11:37:49'),(139,85,'pending','cancelled','system',NULL,NULL,'2026-11-21 11:37:50'),(140,89,'pending','cancelled','system',NULL,NULL,'2026-11-21 11:38:47'),(141,93,'pending','confirmed','system',NULL,NULL,'2026-11-21 11:42:14'),(142,93,'confirmed','packing','system',NULL,NULL,'2026-11-21 11:42:14'),(143,93,'packing','shipping','system',NULL,NULL,'2026-11-21 11:42:14'),(144,93,'shipping','completed','system',NULL,NULL,'2026-11-21 11:42:20'),(145,94,'pending','confirmed','system',NULL,NULL,'2026-11-21 11:42:20'),(146,94,'confirmed','packing','system',NULL,NULL,'2026-11-21 11:42:20'),(147,99,'pending','confirmed','system',NULL,NULL,'2026-11-21 11:47:25'),(148,99,'confirmed','packing','system',NULL,NULL,'2026-11-21 11:47:25'),(149,99,'packing','shipping','system',NULL,NULL,'2026-11-21 11:47:25'),(150,99,'shipping','completed','system',NULL,NULL,'2026-11-21 11:47:30'),(151,100,'pending','confirmed','system',NULL,NULL,'2026-11-21 11:47:30'),(152,100,'confirmed','packing','system',NULL,NULL,'2026-11-21 11:47:30'),(153,101,'pending','cancelled','system',NULL,NULL,'2026-11-21 11:47:30'),(154,121,'pending','confirmed','system',NULL,'SePay webhook xác nhận chuyển khoản VietQR (ref: SIMULATE-1795268283)','2026-11-21 13:38:07'),(156,172,NULL,'pending','customer',2,'Tạo đơn hàng mới (Thanh toán qua VNPay)','2026-11-26 07:34:50'),(157,173,NULL,'pending','customer',2,'Tạo đơn hàng mới (Thanh toán qua VNPay)','2026-11-26 07:36:08'),(158,174,NULL,'pending','customer',2,'Tạo đơn hàng mới (Thanh toán qua VNPay)','2026-11-26 07:39:22'),(159,175,NULL,'pending','customer',2,'Tạo đơn hàng mới (Thanh toán qua VNPay)','2026-11-26 07:41:15'),(160,175,'confirmed','packing','admin',1,'Admin cập nhật trạng thái đơn hàng.','2026-11-26 07:44:16'),(161,175,'packing','shipping','admin',1,'Admin cập nhật trạng thái đơn hàng.','2026-11-26 07:46:09'),(162,175,'shipping','completed','admin',1,'Admin cập nhật trạng thái đơn hàng.','2026-11-26 07:46:27');
/*!40000 ALTER TABLE `order_status_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned DEFAULT NULL,
  `customer_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `shipping_address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payment_method` enum('cash','vnpay','momo','installment','bank_transfer') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cash',
  `payment_status` enum('pending','paid','refunded') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `total_amount` bigint unsigned NOT NULL DEFAULT '0',
  `status` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `vnpay_txn_ref` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vnpay_transaction_no` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vnpay_bank_code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vnpay_pay_date` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `coupon_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discount_amount` bigint unsigned NOT NULL DEFAULT '0',
  `shipper_id` int unsigned DEFAULT NULL,
  `shipping_lat` decimal(10,8) DEFAULT NULL,
  `shipping_lng` decimal(11,8) DEFAULT NULL,
  `user_address_id` int unsigned DEFAULT NULL,
  `order_code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `wallet_amount` bigint unsigned NOT NULL DEFAULT '0',
  `user_voucher_code` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `store_id` int unsigned DEFAULT NULL,
  `address_province` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_district` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_ward` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_street` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `preferred_time_slot` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_fee` bigint unsigned NOT NULL DEFAULT '0',
  `shipping_distance_km` decimal(8,2) DEFAULT NULL,
  `estimated_delivery_from` date DEFAULT NULL,
  `estimated_delivery_to` date DEFAULT NULL,
  `loyalty_discount_amount` bigint unsigned NOT NULL DEFAULT '0',
  `loyalty_tier` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `earned_points` int unsigned NOT NULL DEFAULT '0',
  `points_awarded` tinyint(1) NOT NULL DEFAULT '0',
  `delivery_proof_path` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipper_assigned_at` datetime DEFAULT NULL,
  `installment_months` tinyint unsigned DEFAULT NULL,
  `installment_deposit_amount` bigint unsigned NOT NULL DEFAULT '0',
  `installment_status` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_preorder` tinyint(1) NOT NULL DEFAULT '0',
  `preorder_deposit_paid` bigint unsigned NOT NULL DEFAULT '0',
  `preorder_status` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vat_company_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vat_tax_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vat_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vat_email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vat_invoice_no` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vat_invoice_issued` tinyint(1) NOT NULL DEFAULT '0',
  `vat_invoice_issued_at` datetime DEFAULT NULL,
  `stock_reserved` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_orders_order_code` (`order_code`),
  KEY `orders_user_id` (`user_id`),
  KEY `orders_payment_status` (`payment_status`),
  KEY `orders_created_at` (`created_at`),
  KEY `idx_orders_user_id` (`user_id`),
  KEY `idx_orders_store_id` (`store_id`)
) ENGINE=InnoDB AUTO_INCREMENT=176 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` (`id`, `user_id`, `customer_name`, `phone`, `email`, `contact_address`, `shipping_address`, `payment_method`, `payment_status`, `total_amount`, `status`, `vnpay_txn_ref`, `vnpay_transaction_no`, `vnpay_bank_code`, `vnpay_pay_date`, `created_at`, `coupon_code`, `discount_amount`, `shipper_id`, `shipping_lat`, `shipping_lng`, `user_address_id`, `order_code`, `wallet_amount`, `user_voucher_code`, `store_id`, `address_province`, `address_district`, `address_ward`, `address_street`, `preferred_time_slot`, `shipping_fee`, `shipping_distance_km`, `estimated_delivery_from`, `estimated_delivery_to`, `loyalty_discount_amount`, `loyalty_tier`, `earned_points`, `points_awarded`, `delivery_proof_path`, `shipper_assigned_at`, `installment_months`, `installment_deposit_amount`, `installment_status`, `is_preorder`, `preorder_deposit_paid`, `preorder_status`, `vat_company_name`, `vat_tax_code`, `vat_address`, `vat_email`, `vat_invoice_no`, `vat_invoice_issued`, `vat_invoice_issued_at`, `stock_reserved`) VALUES (103,43,'Nguyễn Đình Quân','0988776655','quan.nguyen@gmail.com',NULL,NULL,'bank_transfer','paid',499000,'delivered',NULL,NULL,NULL,NULL,'2026-11-21 11:49:51',NULL,0,NULL,NULL,NULL,NULL,'LMS-20261121-12A879',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,0,NULL,0,0,NULL,NULL,NULL,0,NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0),(104,44,'Phạm Thuỳ Linh','0988776655','linh.pham@gmail.com',NULL,NULL,'bank_transfer','paid',499000,'delivered',NULL,NULL,NULL,NULL,'2026-11-21 11:50:40',NULL,0,NULL,NULL,NULL,NULL,'LMS-20261121-ED9881',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,0,NULL,0,0,NULL,NULL,NULL,0,NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0),(105,45,'Hoàng Minh Tuấn','0988776655','tuan.hoang@gmail.com',NULL,NULL,'bank_transfer','paid',320000,'delivered',NULL,NULL,NULL,NULL,'2026-11-21 11:51:24',NULL,0,NULL,NULL,NULL,NULL,'LMS-20261121-E42157',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,0,NULL,0,0,NULL,NULL,NULL,0,NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0),(106,46,'Đỗ Phương Anh','0988776655','anh.do@gmail.com',NULL,NULL,'bank_transfer','paid',450000,'delivered',NULL,NULL,NULL,NULL,'2026-11-21 11:51:24',NULL,0,NULL,NULL,NULL,NULL,'LMS-20261121-EE8652',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,0,NULL,0,0,NULL,NULL,NULL,0,NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0),(107,47,'Trịnh Quốc Bảo','0988776655','bao.trinh@gmail.com',NULL,NULL,'bank_transfer','paid',799000,'delivered',NULL,NULL,NULL,NULL,'2026-11-21 11:51:24',NULL,0,NULL,NULL,NULL,NULL,'LMS-20261121-D4104D',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,0,NULL,0,0,NULL,NULL,NULL,0,NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0),(108,48,'Ngô Thị Cẩm Tú','0988776655','tu.ngo@gmail.com',NULL,NULL,'bank_transfer','paid',0,'delivered',NULL,NULL,NULL,NULL,'2026-11-21 11:51:24',NULL,0,NULL,NULL,NULL,NULL,'LMS-20261121-595BCF',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,0,NULL,0,0,NULL,NULL,NULL,0,NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0),(111,1,'Admin','0901234567','admin@shop.test',NULL,NULL,'vnpay','paid',390000,'completed',NULL,NULL,NULL,NULL,'2026-11-21 13:12:03',NULL,0,NULL,NULL,NULL,NULL,'LMS2611217AF0B2',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,0,NULL,0,0,NULL,NULL,NULL,0,NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0),(114,1,'Admin','0901234567','admin@shop.test',NULL,NULL,'vnpay','paid',799000,'completed',NULL,NULL,NULL,NULL,'2026-11-21 13:12:34',NULL,0,NULL,NULL,NULL,NULL,'LMS2611219E4F30',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,0,NULL,0,0,NULL,NULL,NULL,0,NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0),(115,2,'Nguyễn Văn A','0912345678','khach@shop.test',NULL,NULL,'vnpay','paid',799000,'completed',NULL,NULL,NULL,NULL,'2026-11-21 13:21:10',NULL,0,NULL,NULL,NULL,NULL,'LMS261121466960',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,0,NULL,0,0,NULL,NULL,NULL,0,NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0),(116,2,'Nguyễn Văn A','0912345678','khach@shop.test',NULL,NULL,'bank_transfer','paid',499000,'completed',NULL,NULL,NULL,NULL,'2026-11-21 13:24:30',NULL,0,NULL,NULL,NULL,NULL,'LMS261121TEST02',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,0,NULL,0,0,NULL,NULL,NULL,0,NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0),(117,2,'Nguyễn Văn A','0912345678','khach@shop.test',NULL,NULL,'vnpay','paid',450000,'completed',NULL,NULL,NULL,NULL,'2026-11-21 13:29:04',NULL,0,NULL,NULL,NULL,NULL,'LMS2611217F6499',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,0,NULL,0,0,NULL,NULL,NULL,0,NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0),(121,4,'Tô Quang Huy','0912345678','huy28072k5@gmail.com',NULL,NULL,'bank_transfer','paid',499000,'confirmed',NULL,NULL,NULL,NULL,'2026-11-21 13:37:48',NULL,0,NULL,NULL,NULL,NULL,'HD261121-VSAYZ',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,0,NULL,49,1,NULL,NULL,NULL,0,NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0),(122,4,'Tô Quang Huy','0912345678','huy28072k5@gmail.com',NULL,NULL,'vnpay','pending',320000,'pending',NULL,NULL,NULL,NULL,'2026-11-21 13:38:29',NULL,0,NULL,NULL,NULL,NULL,'HD261121-HSPGR',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,0,NULL,0,0,NULL,NULL,NULL,0,NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0),(123,4,'huy','0912345678','huy28072k5@gmail.com',NULL,NULL,'vnpay','paid',320000,'confirmed',NULL,NULL,NULL,NULL,'2026-11-21 13:41:49',NULL,0,NULL,NULL,NULL,NULL,'HD261121-P3M35',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,0,NULL,32,1,NULL,NULL,NULL,0,NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0),(126,2,'Nguyễn Văn A','0912345678','khach@shop.test',NULL,NULL,'cash','paid',490000,'completed',NULL,NULL,NULL,NULL,'2026-11-21 14:01:55',NULL,0,NULL,NULL,NULL,NULL,'HD261121-9WQ3U',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,0,NULL,0,0,NULL,NULL,NULL,0,NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0),(129,2,'Nguyễn Văn A','0912345678','khach@shop.test',NULL,NULL,'vnpay','pending',490000,'pending',NULL,NULL,NULL,NULL,'2026-11-21 14:07:20',NULL,0,NULL,NULL,NULL,NULL,'HD261121-1ILH3',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,0,NULL,0,0,NULL,NULL,NULL,0,NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0),(137,2,'Nguyễn Văn A','0912345678','khach@shop.test',NULL,NULL,'vnpay','paid',499000,'confirmed','137_1795270291','50370217','NCB','20261121211131','2026-11-21 14:11:27',NULL,0,NULL,NULL,NULL,NULL,'HD261121-BVMUO',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,0,NULL,49,1,NULL,NULL,NULL,0,NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0),(150,2,'Nguyễn Văn A','0912345678','khach@shop.test',NULL,NULL,'bank_transfer','pending',450000,'pending',NULL,NULL,NULL,NULL,'2026-11-21 14:46:12',NULL,0,NULL,NULL,NULL,NULL,'TEST12345',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,0,NULL,0,0,NULL,NULL,NULL,0,NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0),(168,2,'Nguyễn Văn A','0912345678','khach@shop.test',NULL,NULL,'vnpay','paid',490000,'confirmed','168_1795325359','75458727','NCB','20261122122919','2026-11-22 05:29:12',NULL,0,NULL,NULL,NULL,NULL,'HD261122-NAMHY',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,0,NULL,0,0,NULL,NULL,NULL,0,NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0),(172,2,'Nguyễn Văn A','0912345678','khach@shop.test','Trụ sở chính: Võng Thị, Tây Hồ, Hà Nội','Trụ sở chính: Võng Thị, Tây Hồ, Hà Nội','vnpay','paid',2376000,'confirmed','172_1795678495','47181368','NCB','20261126143455','2026-11-26 14:34:50',NULL,264000,NULL,NULL,NULL,NULL,'HD261126-DA2LE',0,NULL,NULL,NULL,NULL,NULL,NULL,'any',0,NULL,'2026-11-29','2026-12-01',264000,'Diamond',237,1,NULL,NULL,NULL,0,NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,1),(173,2,'Nguyễn Văn A','0912345678','khach@shop.test','Trụ sở chính: Võng Thị, Tây Hồ, Hà Nội','Trụ sở chính: Võng Thị, Tây Hồ, Hà Nội','vnpay','paid',14841000,'confirmed','173_1795678572','93255512','NCB','20261126143612','2026-11-26 14:36:08',NULL,1649000,NULL,NULL,NULL,NULL,'HD261126-JY0WC',0,NULL,NULL,NULL,NULL,NULL,NULL,'any',0,NULL,'2026-11-29','2026-12-01',1649000,'Diamond',1484,1,NULL,NULL,NULL,0,NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,1),(174,2,'Nguyễn Văn A','0912345678','khach@shop.test','Trụ sở chính: Võng Thị, Tây Hồ, Hà Nội','Trụ sở chính: Võng Thị, Tây Hồ, Hà Nội','vnpay','paid',14841000,'confirmed','174_1795678764','68039575','NCB','20261126143924','2026-11-26 14:39:22',NULL,1649000,NULL,NULL,NULL,NULL,'HD261126-KS2R2',0,NULL,NULL,NULL,NULL,NULL,NULL,'any',0,NULL,'2026-11-29','2026-12-01',1649000,'Diamond',1484,1,NULL,NULL,NULL,0,NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,1),(175,2,'Nguyễn Văn A','0912345678','khach@shop.test','Trụ sở chính: Võng Thị, Tây Hồ, Hà Nội','Trụ sở chính: Võng Thị, Tây Hồ, Hà Nội','vnpay','paid',14841000,'completed','175_1795678880','32472255','NCB','20261126144120','2026-11-26 14:41:15',NULL,1649000,21,NULL,NULL,NULL,'HD261126-6GLWZ',0,NULL,NULL,NULL,NULL,NULL,NULL,'any',0,NULL,'2026-11-29','2026-12-01',1649000,'Diamond',1484,1,NULL,'2026-11-26 14:44:16',NULL,0,NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,1);
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_requests`
--

DROP TABLE IF EXISTS `password_reset_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_requests` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `request_type` enum('ADMIN_RESET','FORGOT_PASSWORD') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `otp_hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('PENDING','USED','EXPIRED') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PENDING',
  `verify_attempts` int unsigned NOT NULL DEFAULT '0',
  `expired_at` datetime NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` enum('ADMIN','USER') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by_id` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `prr_token_unique` (`token`),
  KEY `prr_user_status` (`user_id`,`status`),
  KEY `prr_token` (`token`),
  CONSTRAINT `prr_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_requests`
--

LOCK TABLES `password_reset_requests` WRITE;
/*!40000 ALTER TABLE `password_reset_requests` DISABLE KEYS */;
INSERT INTO `password_reset_requests` (`id`, `user_id`, `request_type`, `otp_hash`, `token`, `status`, `verify_attempts`, `expired_at`, `created_at`, `created_by`, `created_by_id`) VALUES (2,57,'FORGOT_PASSWORD','$2y$10$PVJNkEqPsfczsSXDvKZvsegQf6DngYJfT./IJ0vS70GXPZsCN/JxS','e8bbfc23c9cd82c9a72f7240186d2a59e916c4feb0e73d0726e3d8a181cbc3e1','EXPIRED',0,'2026-11-26 16:26:55','2026-11-26 09:11:55','USER',57),(3,57,'FORGOT_PASSWORD','$2y$10$hIe5syC0jiktTf0QPT6x6u5h.4ymt/7bqto0HtOnbkmivOisE.Sl2','ae176dee1495fd62a0dd19a5352384e0e244c53cee3bccec75a1f3be738219a6','EXPIRED',0,'2026-11-26 16:27:01','2026-11-26 09:12:01','USER',57),(4,57,'FORGOT_PASSWORD','$2y$10$S3OBwuWf8gFStF1W3hkwQ.fOtOPfIm3aDNR69G1rCJ.SCM54qlpdq','1a7684d6c61d5e986d9157a52fc5e3f1a4c50046fd2112b395bedbb4a973eeb8','EXPIRED',0,'2026-11-26 16:27:51','2026-11-26 09:12:51','USER',57),(5,57,'FORGOT_PASSWORD','$2y$10$nexYiHxoutSJv3WOfh4OteduLL3p/.BAy9ygFRJegz3pdxHrdO8xe','cda65547b54f53977941dfd870402d7fb9b089c5425dca4d944324c393826423','EXPIRED',0,'2026-11-26 16:28:56','2026-11-26 09:13:56','USER',57),(6,57,'FORGOT_PASSWORD','$2y$10$TXidbzilMF37FAAsBJu10eToH8oVs9njvrX0UXKDbrJYwOAqBm7oG','4a7c17834be069086c5774e3dc352658ae9138b7af0766035531fa48f17f2db4','PENDING',0,'2026-11-26 16:29:02','2026-11-26 09:14:02','USER',57);
/*!40000 ALTER TABLE `password_reset_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_review_replies`
--

DROP TABLE IF EXISTS `product_review_replies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_review_replies`
--

LOCK TABLES `product_review_replies` WRITE;
/*!40000 ALTER TABLE `product_review_replies` DISABLE KEYS */;
INSERT INTO `product_review_replies` (`id`, `review_id`, `user_name`, `reply_text`, `created_at`) VALUES (1,3,'huy','á đùđù','2026-06-22 01:01:06');
/*!40000 ALTER TABLE `product_review_replies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_reviews`
--

DROP TABLE IF EXISTS `product_reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_reviews` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int unsigned NOT NULL,
  `user_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rating` tinyint unsigned NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `likes_count` int unsigned NOT NULL DEFAULT '0',
  `user_id` int unsigned DEFAULT NULL,
  `order_id` int unsigned DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `reviews_product_id` (`product_id`),
  KEY `idx_reviews_user_product` (`user_id`,`product_id`),
  KEY `idx_reviews_order` (`order_id`),
  CONSTRAINT `fk_reviews_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_reviews_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_reviews_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_reviews`
--

LOCK TABLES `product_reviews` WRITE;
/*!40000 ALTER TABLE `product_reviews` DISABLE KEYS */;
INSERT INTO `product_reviews` (`id`, `product_id`, `user_name`, `rating`, `comment`, `created_at`, `likes_count`, `user_id`, `order_id`, `is_verified`) VALUES (1,7,'huy',5,'nice xừxừ','2026-06-22 00:56:37',0,NULL,NULL,0),(2,1,'huy',5,'hayhay','2026-06-22 00:57:06',0,NULL,NULL,0),(3,1,'hiển',5,'vip pro','2026-06-22 00:57:16',1,NULL,NULL,0),(4,1,'hadeshades',1,'đắt quá','2026-06-22 01:01:56',0,NULL,NULL,0);
/*!40000 ALTER TABLE `product_reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_variants`
--

DROP TABLE IF EXISTS `product_variants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_variants`
--

LOCK TABLES `product_variants` WRITE;
/*!40000 ALTER TABLE `product_variants` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_variants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=86 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` (`id`, `category_id`, `name`, `price`, `description`, `detail`, `stock`, `image`, `status`, `created_at`, `discount_percent`, `promo_active`, `ram`, `storage_gb`, `chipset`, `screen_size`, `battery_mah`, `os`, `is_preorder`, `preorder_deposit`) VALUES (1,1,'iPhone 15 Pro Max 256GB',29990000,'Khung Titan cao cấp, chip A17 Pro siêu mạnh mẽ.','iPhone 15 Pro Max sở hữu thiết kế khung titan siêu bền và nhẹ, nút Action mới tiện dụng, hệ thống camera chuyên nghiệp 48MP và cổng kết nối USB-C tốc độ cao.',12,'ip15.webp','active','2026-06-22 00:43:56',12,1,NULL,NULL,NULL,NULL,NULL,NULL,0,0),(2,1,'iPhone 16 Pro 128GB',28990000,'Nút điều khiển camera chuyên biệt, chip A18 Pro.','iPhone 16 Pro nâng tầm nhiếp ảnh di động với nút Camera Control mới, chip xử lý A18 Pro tối ưu hiệu năng AI và thiết kế tản nhiệt tốt hơn.',8,'ip16.webp','active','2026-06-22 00:43:56',0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,0),(3,1,'iPhone 17 Ultra Concept',39990000,'Thiết kế siêu mỏng tương lai, viền màn hình vô cực.','iPhone 17 Ultra là đỉnh cao công nghệ màn hình tràn viền hoàn toàn không khuyết điểm, camera ẩn dưới màn hình và camera zoom quang học 10x.',3,'ip17.webp','active','2026-06-22 00:43:56',0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,0),(4,1,'iPhone 15 128GB',19990000,'Màn hình Dynamic Island, mặt lưng kính pha màu độc đáo.','iPhone 15 trang bị chip A16 Bionic mượt mà, camera sau nâng cấp lên 48MP và cổng sạc USB-C phổ biến.',15,'ip15.webp','active','2026-06-22 00:43:56',0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,0),(5,1,'iPhone 16 Plus 256GB',25990000,'Màn hình 6.7 inch siêu rộng rãi, pin trâu vượt trội.','iPhone 16 Plus mang đến không gian trải nghiệm lớn cùng thời lượng pin tốt nhất dòng iPhone, chip A18 tối ưu chơi game đồ họa cao.',10,'ip16.webp','active','2026-06-22 00:43:56',0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,0),(6,2,'MacBook Pro M5 Max',54990000,'Siêu chip M5 Max thế hệ mới, màn hình Liquid Retina XDR.','Dành cho người dùng chuyên nghiệp đồ họa, lập trình và xử lý tác vụ nặng. MacBook Pro M5 Max cân mọi giới hạn hiệu năng với RAM khủng và tản nhiệt tối ưu.',5,'mac_m5.webp','active','2026-06-22 00:43:56',0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,0),(7,2,'Dell XPS 13 Plus 9320',38990000,'Vỏ nhôm nguyên khối, hàng phím chức năng cảm ứng độc đáo.','Thiết kế tràn viền thời thượng, touchpad ẩn sang trọng và bộ vi xử lý Intel Core i7 thế hệ mới mạnh mẽ.',6,'dell_xps.webp','active','2026-06-22 00:43:56',0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,0),(8,2,'ThinkPad X1 Carbon Gen 13',45990000,'Chất liệu sợi carbon siêu nhẹ siêu bền, bàn phím gõ đỉnh cao.','Lựa chọn số một cho doanh nhân và lập trình viên. ThinkPad X1 Carbon nổi tiếng với độ bền chuẩn quân đội và bàn phím huyền thoại êm ái.',4,'thinkpad_gen13.webp','active','2026-06-22 00:43:56',0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,0),(9,2,'MacBook Air M2 13\"',24990000,'Thiết kế vuông vắn siêu mỏng nhẹ, pin lên tới 18 giờ.','MacBook Air M2 mang lại sự cân bằng hoàn hảo giữa hiệu năng mượt mà và tính cơ động cực cao, phù hợp học sinh, sinh viên và văn phòng.',11,'mac_m5.webp','active','2026-06-22 00:43:56',0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,0),(10,2,'Dell Inspiron 14 Core i5',16990000,'Vỏ kim loại bền bỉ, hiệu năng văn phòng mượt mà.','Dell Inspiron 14 trang bị màn hình FHD+ sắc nét, chip Core i5 đời mới đáp ứng tốt mọi công việc học tập và giải trí cơ bản.',9,'dell_xps.webp','active','2026-06-22 00:43:56',0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,0),(11,3,'Cáp sạc nhanh USB-C bọc dù 60W',190000,'Dây bọc dù chống đứt, hỗ trợ sạc nhanh PD.','Cáp sạc nhanh USB-C sang USB-C hỗ trợ truyền dữ liệu tốc độ cao, chất liệu sợi nylon bọc dù siêu bền bỉ chống rối gập.',100,'day_sac.webp','active','2026-06-22 00:43:56',0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,0),(12,3,'Củ sạc nhanh Hades GaN 65W',390000,'Công nghệ GaN sạc siêu mát, 3 cổng ra tiện lợi.','Củ sạc sử dụng vật liệu bán dẫn Gallium Nitride giúp thu nhỏ kích thước tối đa, sạc nhanh đồng thời cho cả Laptop, iPad và điện thoại.',80,'day_sac.webp','active','2026-06-22 00:43:56',0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,0),(13,3,'Tai nghe True Wireless Hades Pro',1290000,'Chống ồn chủ động ANC, chống nước IPX4.','Tai nghe bluetooth không dây thiết kế công thái học ôm tai, chất âm bass sâu, pin kết hợp hộp sạc dùng đến 24 giờ liên tục.',45,'tai_nghe.webp','active','2026-06-22 00:43:56',0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,0),(14,3,'Tai nghe chụp tai Gaming Pro',850000,'Âm thanh vòm 7.1, mic khử tiếng ồn tiện lợi.','Tai nghe Over-ear đệm da êm ái, cách âm tuyệt đối, mang lại trải nghiệm âm thanh chiến game 3D chân thực.',30,'tai_nghe.webp','active','2026-06-22 00:43:56',0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,0),(15,3,'Sạc dự phòng Hades 20000mAh',590000,'Dung lượng khủng sạc nhiều lần, sạc nhanh 22.5W.','Tích hợp màn hình LED hiển thị % pin trực quan, hỗ trợ sạc nhanh đa thiết bị cùng lúc cực kỳ an toàn.',60,'day_sac.webp','active','2026-06-22 00:43:56',0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,0),(16,4,'Apple Watch Ultra 3',21990000,'Đồng hồ thám hiểm chuyên nghiệp, màn hình độ sáng 3000 nits.','Màn hình Sapphire chống trầy, vỏ Titan chuẩn quân đội, GPS tần số kép cực kỳ chính xác cho các hoạt động ngoài trời dã ngoại.',7,'smartwatch_apple.png','active','2026-06-22 00:43:56',0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,0),(17,4,'Apple Watch Series 9',9990000,'Chip S9 SiP thông minh, tính năng Double Tap chạm đúp.','Theo dõi sức khỏe nâng cao: điện tâm đồ ECG, nồng độ oxy trong máu SpO2, đo nhiệt độ cơ thể hỗ trợ dự báo chu kỳ sức khỏe.',14,'smartwatch_apple.png','active','2026-06-22 00:43:56',0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,0),(18,4,'Smartwatch Hades Active 2',1990000,'Đo nhịp tim 24/7, pin 14 ngày bền bỉ.','Đồng hồ thông minh thể thao hỗ trợ hơn 100 chế độ luyện tập, kháng nước 5ATM thoải mái đi bơi.',25,'smartwatch_apple.png','active','2026-06-22 00:43:56',0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,0),(19,4,'Apple Watch SE 2024',5990000,'Giá trị vượt trội, tính năng phát hiện đụng xe khẩn cấp.','Trang bị các tính năng theo dõi sức khỏe cơ bản, thiết kế thể thao trẻ trung, lựa chọn hoàn hảo cho các bạn trẻ và gia đình.',18,'smartwatch_apple.png','active','2026-06-22 00:43:56',0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,0),(20,5,'Đồng hồ Casio Edifice EFR',3450000,'Thiết kế kim lịch lãm, chống nước độ sâu 100 mét.','Dòng đồng hồ Casio cao cấp cho nam, vỏ thép không gỉ nguyên khối, mặt kính khoáng chống va đập cực tốt.',10,'watch_casio.png','active','2026-06-22 00:43:56',0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,0),(21,5,'Casio G-Shock G-Steel Tough Solar',4990000,'Sử dụng pin năng lượng mặt trời, tự động đồng bộ giờ.','Chống va đập cực đỉnh, tích hợp đèn LED đôi siêu sáng, kết nối bluetooth thông minh với điện thoại.',6,'watch_casio.png','active','2026-06-22 00:43:56',0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,0),(22,5,'Đồng hồ Casio Classic MTP Dây Da',950000,'Phong cách tối giản classic cổ điển, dây da bò thật.','Đồng hồ đeo tay thanh lịch cho nam nữ, phù hợp đi học, đi làm công sở, thời gian pin dùng đến 3 năm.',19,'watch_casio.png','active','2026-06-22 00:43:56',0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,0),(23,5,'G-Shock GA-2100 \"CasiOak\"',3120000,'Mặt bát giác cá tính, thiết kế mỏng nhẹ hiện đại.','Vỏ bảo vệ cấu trúc carbon bền nhẹ, thiết kế sang trọng pha nét thể thao bụi bặm bán chạy số 1 toàn cầu.',15,'watch_casio.png','active','2026-06-22 00:43:56',0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,0),(24,6,'iPhone 15 Pro 128GB Cũ Đẹp',21490000,'Máy cũ đẹp 99% likenew như mới, bảo hành 6 tháng.','Cam kết zin nguyên bản 100%, chưa qua sửa chữa thay thế linh kiện, pin trên 90%, đầy đủ phụ kiện sạc cáp.',5,'ip15.webp','active','2026-06-22 00:43:56',0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,0),(25,6,'MacBook Air M1 Cũ Likenew',13990000,'Lựa chọn giá rẻ tối ưu nhất của Apple, máy dùng lướt.','Ngoại hình đẹp keng không cấn móp, pin sạc ít lần còn rất trâu, bảo hành phần cứng 1 đổi 1 trong 30 ngày.',4,'mac_m5.webp','active','2026-06-22 00:43:56',0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,0),(26,6,'Dell XPS 13 Cũ Giá Tốt',18900000,'Laptop văn phòng sang chảnh, độ bền siêu ổn định.','Dell XPS cũ qua sử dụng, vỏ nhôm còn rất đẹp, màn hình IPS siêu sáng, hoạt động trơn tru mát mẻ.',3,'dell_xps.webp','active','2026-06-22 00:43:56',0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,0),(27,6,'iPad Air 5 M1 Cũ 64GB WiFi',11490000,'Hiệu năng chip M1 cực khỏe, máy nguyên bản.','iPad Air 5 cũ phục vụ vẽ đồ họa, ghi chú học tập mượt mà, cảm biến vân tay nhạy bén.',8,'tablet_ipad.png','active','2026-06-22 00:43:56',0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,0),(28,7,'iPad Pro M4 11 inch 256GB',25990000,'iPad mỏng nhất lịch sử, chip M4 siêu khủng, màn hình OLED.','iPad Pro cao cấp nhất của Apple với màn hình Tandem OLED rực rỡ, chip M4 tối ưu hóa hiệu năng làm việc đồ họa 3D.',6,'tablet_ipad.png','active','2026-06-22 00:43:56',0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,0),(29,7,'iPad Air 6 M2 2026',16490000,'Sức mạnh từ chip M2 mượt mà, hỗ trợ Apple Pencil Pro.','iPad Air thế hệ mới nâng cấp cấu hình vượt trội đáp ứng tốt mọi công việc đa nhiệm học tập của bạn.',7,'tablet_ipad.png','active','2026-06-22 00:43:56',0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,0),(30,7,'iPad Gen 10 64GB WiFi',9490000,'Thiết kế thời trang nhiều màu sắc trẻ trung, cổng USB-C.','iPad tiêu chuẩn màn hình 10.9 inch sắc nét Liquid Retina, nút nguồn tích hợp Touch ID, hỗ trợ bàn phím Magic Keyboard Folio.',16,'tablet_ipad.png','active','2026-06-22 00:43:56',0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,0),(84,4,'Ô tô nè',999999999999999,'','',2,'/image/Screenshot_from_2026-07-10_16-46-13_1783690081.png','active','2026-07-10 20:28:01',0,0,'16GB','1TB','3','1000','5000000','IOS',1,0),(85,4,'zzzz',11111,'','',50,'/image/hochanh_847db56e93c6.jpg','active','2026-11-26 15:21:19',0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,0);
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `promotions`
--

DROP TABLE IF EXISTS `promotions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `promotions`
--

LOCK TABLES `promotions` WRITE;
/*!40000 ALTER TABLE `promotions` DISABLE KEYS */;
INSERT INTO `promotions` (`id`, `code`, `discount_percent`, `status`, `expires_at`, `created_at`, `discount_amount`, `discount_type`) VALUES (1,'SIEUOFFER',20,'inactive','2026-12-31','2026-06-22 01:12:54',0,'percent'),(2,'SUPER SALE',50,'active','2026-12-22','2026-06-22 21:37:19',0,'percent'),(3,'TI-65E07DB4',0,'active','2026-08-09','2026-07-10 20:36:57',18000000,'fixed');
/*!40000 ALTER TABLE `promotions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `push_subscriptions`
--

DROP TABLE IF EXISTS `push_subscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `push_subscriptions`
--

LOCK TABLES `push_subscriptions` WRITE;
/*!40000 ALTER TABLE `push_subscriptions` DISABLE KEYS */;
/*!40000 ALTER TABLE `push_subscriptions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `quiz_answers`
--

DROP TABLE IF EXISTS `quiz_answers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `quiz_answers` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `question_id` int unsigned NOT NULL,
  `answer_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_correct` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `question_id` (`question_id`),
  CONSTRAINT `quiz_answers_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `quiz_questions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=145 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `quiz_answers`
--

LOCK TABLES `quiz_answers` WRITE;
/*!40000 ALTER TABLE `quiz_answers` DISABLE KEYS */;
INSERT INTO `quiz_answers` (`id`, `question_id`, `answer_text`, `is_correct`, `created_at`) VALUES (1,1,'Model',1,'2026-11-21 11:33:12'),(2,1,'View',0,'2026-11-21 11:33:12'),(3,1,'Controller',0,'2026-11-21 11:33:12'),(4,1,'Router',0,'2026-11-21 11:33:12'),(5,2,'Sử dụng Prepared Statements (PDO)',1,'2026-11-21 11:33:12'),(6,2,'Chỉ dùng hàm md5()',0,'2026-11-21 11:33:12'),(7,2,'Tắt firewall máy chủ',0,'2026-11-21 11:33:12'),(8,2,'Nối chuỗi trực tiếp vào câu lệnh SQL',0,'2026-11-21 11:33:12'),(9,3,'Yêu cầu xử lý thành công (OK)',1,'2026-11-21 11:33:12'),(10,3,'Không tìm thấy trang (Not Found)',0,'2026-11-21 11:33:12'),(11,3,'Lỗi máy chủ nội bộ (Internal Server Error)',0,'2026-11-21 11:33:12'),(12,3,'Yêu cầu bị từ chối (Forbidden)',0,'2026-11-21 11:33:12'),(13,4,'Pandas',1,'2026-11-21 11:33:12'),(14,4,'Matplotlib',0,'2026-11-21 11:33:12'),(15,4,'Flask',0,'2026-11-21 11:33:12'),(16,4,'Requests',0,'2026-11-21 11:33:12'),(17,5,'Linear Regression',1,'2026-11-21 11:33:12'),(18,5,'K-Means Clustering',0,'2026-11-21 11:33:12'),(19,5,'PCA (Giảm chiều dữ liệu)',0,'2026-11-21 11:33:12'),(20,5,'Apriori',0,'2026-11-21 11:33:12'),(21,6,'Overfitting (Quá khớp)',1,'2026-11-21 11:33:12'),(22,6,'Underfitting (Chưa khớp)',0,'2026-11-21 11:33:12'),(23,6,'Regularization',0,'2026-11-21 11:33:12'),(24,6,'Cross-validation',0,'2026-11-21 11:33:12'),(25,7,'useEffect',1,'2026-11-21 11:33:12'),(26,7,'useState',0,'2026-11-21 11:33:12'),(27,7,'useMemo',0,'2026-11-21 11:33:12'),(28,7,'useReducer',0,'2026-11-21 11:33:12'),(29,8,'Component được lên lịch để Re-render',1,'2026-11-21 11:33:12'),(30,8,'Trình duyệt reload lại toàn bộ trang',0,'2026-11-21 11:33:12'),(31,8,'Xóa toàn bộ bộ nhớ Cache',0,'2026-11-21 11:33:12'),(32,8,'Không có thay đổi nào xảy ra',0,'2026-11-21 11:33:12'),(33,9,'CSS Flexbox',1,'2026-11-21 11:33:12'),(34,9,'CSS Float',0,'2026-11-21 11:33:12'),(35,9,'CSS Position Absolute',0,'2026-11-21 11:33:12'),(36,9,'CSS Z-Index',0,'2026-11-21 11:33:12'),(37,10,'Tối thiểu 44x44 hoặc 48x48 px',1,'2026-11-21 11:33:12'),(38,10,'Tối đa 10x10 px',0,'2026-11-21 11:33:12'),(39,10,'Không cần quan tâm kích thước',0,'2026-11-21 11:33:12'),(40,10,'Càng nhỏ càng tiết kiệm diện tích',0,'2026-11-21 11:33:12'),(41,11,'FROM',1,'2026-11-21 11:33:12'),(42,11,'RUN',0,'2026-11-21 11:33:12'),(43,11,'COPY',0,'2026-11-21 11:33:12'),(44,11,'EXPOSE',0,'2026-11-21 11:33:12'),(45,12,'docker compose up',1,'2026-11-21 11:33:12'),(46,12,'docker build',0,'2026-11-21 11:33:12'),(47,12,'docker ps',0,'2026-11-21 11:33:12'),(48,12,'docker commit',0,'2026-11-21 11:33:12'),(49,13,'HAVING',1,'2026-11-21 11:33:12'),(50,13,'WHERE',0,'2026-11-21 11:33:12'),(51,13,'ORDER BY',0,'2026-11-21 11:33:12'),(52,13,'LIMIT',0,'2026-11-21 11:33:12'),(53,14,'LEFT JOIN',1,'2026-11-21 11:33:12'),(54,14,'INNER JOIN',0,'2026-11-21 11:33:12'),(55,14,'CROSS JOIN',0,'2026-11-21 11:33:12'),(56,14,'RIGHT JOIN',0,'2026-11-21 11:33:12'),(57,15,'Hiệu năng cao, khả năng mở rộng tốt và cộng đồng hỗ trợ mạnh mẽ',1,'2026-11-21 11:47:30'),(58,15,'Chỉ chạy được trên môi trường Windows',0,'2026-11-21 11:47:30'),(59,15,'Không cần viết kiểm thử phần mềm',0,'2026-11-21 11:47:30'),(60,16,'Bật chứng chỉ mã hóa SSL/TLS (HTTPS) và bảo vệ dữ liệu bí mật qua Biến môi trường (.env)',1,'2026-11-21 11:47:30'),(61,16,'Lưu trực tiếp mật khẩu và API Key vào mã nguồn Git công khai',0,'2026-11-21 11:47:30'),(62,16,'Tắt tường lửa Firewall máy chủ',0,'2026-11-21 11:47:30'),(63,17,'Hiệu năng cao, khả năng mở rộng tốt và cộng đồng hỗ trợ mạnh mẽ',1,'2026-11-21 11:47:30'),(64,17,'Chỉ chạy được trên môi trường Windows',0,'2026-11-21 11:47:30'),(65,17,'Không cần viết kiểm thử phần mềm',0,'2026-11-21 11:47:30'),(66,18,'Bật chứng chỉ mã hóa SSL/TLS (HTTPS) và bảo vệ dữ liệu bí mật qua Biến môi trường (.env)',1,'2026-11-21 11:47:30'),(67,18,'Lưu trực tiếp mật khẩu và API Key vào mã nguồn Git công khai',0,'2026-11-21 11:47:30'),(68,18,'Tắt tường lửa Firewall máy chủ',0,'2026-11-21 11:47:30'),(69,19,'Hiệu năng cao, khả năng mở rộng tốt và cộng đồng hỗ trợ mạnh mẽ',1,'2026-11-21 11:47:30'),(70,19,'Chỉ chạy được trên môi trường Windows',0,'2026-11-21 11:47:30'),(71,19,'Không cần viết kiểm thử phần mềm',0,'2026-11-21 11:47:30'),(72,20,'Bật chứng chỉ mã hóa SSL/TLS (HTTPS) và bảo vệ dữ liệu bí mật qua Biến môi trường (.env)',1,'2026-11-21 11:47:30'),(73,20,'Lưu trực tiếp mật khẩu và API Key vào mã nguồn Git công khai',0,'2026-11-21 11:47:30'),(74,20,'Tắt tường lửa Firewall máy chủ',0,'2026-11-21 11:47:30'),(75,21,'Hiệu năng cao, khả năng mở rộng tốt và cộng đồng hỗ trợ mạnh mẽ',1,'2026-11-21 11:47:30'),(76,21,'Chỉ chạy được trên môi trường Windows',0,'2026-11-21 11:47:30'),(77,21,'Không cần viết kiểm thử phần mềm',0,'2026-11-21 11:47:30'),(78,22,'Bật chứng chỉ mã hóa SSL/TLS (HTTPS) và bảo vệ dữ liệu bí mật qua Biến môi trường (.env)',1,'2026-11-21 11:47:30'),(79,22,'Lưu trực tiếp mật khẩu và API Key vào mã nguồn Git công khai',0,'2026-11-21 11:47:30'),(80,22,'Tắt tường lửa Firewall máy chủ',0,'2026-11-21 11:47:30'),(81,23,'Hiệu năng cao, khả năng mở rộng tốt và cộng đồng hỗ trợ mạnh mẽ',1,'2026-11-21 11:47:30'),(82,23,'Chỉ chạy được trên môi trường Windows',0,'2026-11-21 11:47:30'),(83,23,'Không cần viết kiểm thử phần mềm',0,'2026-11-21 11:47:30'),(84,24,'Bật chứng chỉ mã hóa SSL/TLS (HTTPS) và bảo vệ dữ liệu bí mật qua Biến môi trường (.env)',1,'2026-11-21 11:47:30'),(85,24,'Lưu trực tiếp mật khẩu và API Key vào mã nguồn Git công khai',0,'2026-11-21 11:47:30'),(86,24,'Tắt tường lửa Firewall máy chủ',0,'2026-11-21 11:47:30'),(87,25,'Hiệu năng cao, khả năng mở rộng tốt và cộng đồng hỗ trợ mạnh mẽ',1,'2026-11-21 11:47:30'),(88,25,'Chỉ chạy được trên môi trường Windows',0,'2026-11-21 11:47:30'),(89,25,'Không cần viết kiểm thử phần mềm',0,'2026-11-21 11:47:30'),(90,26,'Bật chứng chỉ mã hóa SSL/TLS (HTTPS) và bảo vệ dữ liệu bí mật qua Biến môi trường (.env)',1,'2026-11-21 11:47:30'),(91,26,'Lưu trực tiếp mật khẩu và API Key vào mã nguồn Git công khai',0,'2026-11-21 11:47:30'),(92,26,'Tắt tường lửa Firewall máy chủ',0,'2026-11-21 11:47:30'),(93,27,'View',0,'2026-11-21 14:07:38'),(94,27,'Controller',0,'2026-11-21 14:07:38'),(95,27,'Model',1,'2026-11-21 14:07:38'),(96,27,'Router',0,'2026-11-21 14:07:38'),(97,28,'Request - Response (Yêu cầu và Phản hồi)',1,'2026-11-21 14:07:38'),(98,28,'Publish - Subscribe (Phát và Đăng ký)',0,'2026-11-21 14:07:38'),(99,28,'Peer to Peer (Ngang hàng)',0,'2026-11-21 14:07:38'),(100,28,'Broadcast không đồng bộ',0,'2026-11-21 14:07:38'),(101,29,'Tăng dung lượng lưu trữ của máy chủ',0,'2026-11-21 14:07:38'),(102,29,'Tách biệt hoàn toàn xử lý nghiệp vụ và hiển thị giao diện HTML',1,'2026-11-21 14:07:38'),(103,29,'Giúp file mã nguồn chạy nhanh hơn gấp 10 lần',0,'2026-11-21 14:07:38'),(104,29,'Bắt buộc người dùng phải đăng ký tài khoản',0,'2026-11-21 14:07:38'),(105,30,'List',0,'2026-11-21 14:08:32'),(106,30,'Tuple',1,'2026-11-21 14:08:32'),(107,30,'Dictionary',0,'2026-11-21 14:08:32'),(108,30,'Set',0,'2026-11-21 14:08:32'),(109,31,'[x for x in range(10) if x % 2 == 0]',1,'2026-11-21 14:08:32'),(110,31,'[x if x % 2 == 0 for x in range(10)]',0,'2026-11-21 14:08:32'),(111,31,'{x: x % 2 == 0 for x in range(10)}',0,'2026-11-21 14:08:32'),(112,31,'(x for x in range(10) if x % 2 == 0)',0,'2026-11-21 14:08:32'),(113,32,'Props có thể thay đổi trực tiếp, State thì không',0,'2026-11-21 14:08:32'),(114,32,'Props nhận từ bên ngoài (read-only), State được quản lý nội bộ component',1,'2026-11-21 14:08:32'),(115,32,'State chỉ dùng được trong class component',0,'2026-11-21 14:08:32'),(116,32,'Props bắt buộc phải là chuỗi ký tự String',0,'2026-11-21 14:08:32'),(117,33,'Phải có một phần tử cha duy nhất bao bọc',1,'2026-11-21 14:08:32'),(118,33,'Không được phép sử dụng thẻ div',0,'2026-11-21 14:08:32'),(119,33,'Bắt buộc phải nhúng CSS inline',0,'2026-11-21 14:08:32'),(120,33,'Phải khai báo kiểu dữ liệu TypeScript',0,'2026-11-21 14:08:32'),(121,34,'Ctrl + G',0,'2026-11-21 14:08:32'),(122,34,'Shift + A',1,'2026-11-21 14:08:32'),(123,34,'Alt + L',0,'2026-11-21 14:08:32'),(124,34,'Ctrl + Alt + K',0,'2026-11-21 14:08:32'),(125,35,'Tự động co giãn theo kích thước của frame cha',1,'2026-11-21 14:08:32'),(126,35,'Cố định kích thước không bao giờ thay đổi',0,'2026-11-21 14:08:32'),(127,35,'Tự động tô màu nền ngẫu nhiên',0,'2026-11-21 14:08:32'),(128,35,'Ẩn layer khi màn hình nhỏ lại',0,'2026-11-21 14:08:32'),(129,36,'Image là thể hiện đang chạy của Container',0,'2026-11-21 14:08:32'),(130,36,'Container là một instance đang chạy được tạo ra từ Image',1,'2026-11-21 14:08:32'),(131,36,'Image và Container là hai thuật ngữ hoàn toàn giống nhau',0,'2026-11-21 14:08:32'),(132,36,'Image chỉ dùng cho hệ điều hành Windows',0,'2026-11-21 14:08:32'),(133,37,'docker run -d .',0,'2026-11-21 14:08:32'),(134,37,'docker build -t my-app .',1,'2026-11-21 14:08:32'),(135,37,'docker compose up',0,'2026-11-21 14:08:32'),(136,37,'docker create image',0,'2026-11-21 14:08:32'),(137,38,'WHERE',0,'2026-11-21 14:08:32'),(138,38,'HAVING',1,'2026-11-21 14:08:32'),(139,38,'ORDER BY',0,'2026-11-21 14:08:32'),(140,38,'GROUP BY',0,'2026-11-21 14:08:32'),(141,39,'Chỉ những dòng có giá trị khớp ở cả 2 bảng',1,'2026-11-21 14:08:32'),(142,39,'Toàn bộ dữ liệu của bảng bên trái',0,'2026-11-21 14:08:32'),(143,39,'Toàn bộ dữ liệu của bảng bên phải',0,'2026-11-21 14:08:32'),(144,39,'Tất cả các dòng của cả hai bảng bất kể có khớp hay không',0,'2026-11-21 14:08:32');
/*!40000 ALTER TABLE `quiz_answers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `quiz_attempt_answers`
--

DROP TABLE IF EXISTS `quiz_attempt_answers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `quiz_attempt_answers` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `attempt_id` int unsigned NOT NULL,
  `question_id` int unsigned NOT NULL,
  `answer_id` int unsigned NOT NULL,
  `is_correct` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `attempt_id` (`attempt_id`),
  KEY `question_id` (`question_id`),
  KEY `answer_id` (`answer_id`),
  CONSTRAINT `quiz_attempt_answers_ibfk_1` FOREIGN KEY (`attempt_id`) REFERENCES `quiz_attempts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `quiz_attempt_answers_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `quiz_questions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `quiz_attempt_answers_ibfk_3` FOREIGN KEY (`answer_id`) REFERENCES `quiz_answers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=106 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `quiz_attempt_answers`
--

LOCK TABLES `quiz_attempt_answers` WRITE;
/*!40000 ALTER TABLE `quiz_attempt_answers` DISABLE KEYS */;
INSERT INTO `quiz_attempt_answers` (`id`, `attempt_id`, `question_id`, `answer_id`, `is_correct`) VALUES (1,1,1,1,1),(2,1,2,5,1),(3,1,3,9,1),(4,2,1,1,1),(5,2,2,5,1),(6,2,3,9,1),(7,3,1,1,1),(8,3,2,5,1),(9,3,3,9,1),(10,4,1,1,1),(11,4,2,5,1),(12,4,3,9,1),(13,5,1,1,1),(14,5,2,5,1),(15,5,3,9,1),(16,6,1,1,1),(17,6,2,5,1),(18,6,3,9,1),(19,7,1,1,1),(20,7,2,5,1),(21,7,3,9,1),(22,8,1,1,1),(23,8,2,5,1),(24,8,3,9,1),(25,9,1,1,1),(26,9,2,5,1),(27,9,3,9,1),(28,10,1,1,1),(29,10,2,5,1),(30,10,3,9,1),(31,11,1,1,1),(32,11,2,5,1),(33,11,3,9,1),(34,12,1,1,1),(35,12,2,5,1),(36,12,3,9,1),(37,13,1,1,1),(38,13,2,5,1),(39,13,3,9,1),(40,14,1,1,1),(41,14,2,5,1),(42,14,3,9,1),(43,15,1,1,1),(44,15,2,5,1),(45,15,3,9,1),(46,16,1,1,1),(47,16,2,5,1),(48,16,3,9,1),(49,17,1,1,1),(50,17,2,5,1),(51,17,3,9,1),(52,18,1,1,1),(53,18,2,5,1),(54,18,3,9,1),(55,19,1,1,1),(56,19,2,5,1),(57,19,3,9,1),(58,20,1,1,1),(59,20,2,5,1),(60,20,3,9,1),(61,21,1,1,1),(62,21,2,5,1),(63,21,3,9,1),(64,22,1,1,1),(65,22,2,5,1),(66,22,3,9,1),(67,23,1,1,1),(68,23,2,5,1),(69,23,3,9,1),(70,24,1,1,1),(71,24,2,5,1),(72,24,3,9,1),(73,25,38,140,0),(74,25,39,142,0),(75,26,38,138,1),(76,26,39,144,0),(77,27,38,139,0),(78,27,39,144,0),(79,28,38,137,0),(80,28,39,141,1),(81,29,38,138,1),(82,29,39,144,0),(83,30,38,137,0),(84,30,39,142,0),(85,31,1,1,1),(86,31,2,5,1),(87,31,3,9,1),(88,32,1,1,1),(89,32,2,5,1),(90,32,3,9,1),(91,33,1,1,1),(92,33,2,5,1),(93,33,3,9,1),(94,34,1,1,1),(95,34,2,5,1),(96,34,3,9,1),(97,35,1,1,1),(98,35,2,5,1),(99,35,3,9,1),(100,36,1,1,1),(101,36,2,5,1),(102,36,3,9,1),(103,37,1,1,1),(104,37,2,5,1),(105,37,3,9,1);
/*!40000 ALTER TABLE `quiz_attempt_answers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `quiz_attempts`
--

DROP TABLE IF EXISTS `quiz_attempts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `quiz_attempts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `quiz_id` int unsigned NOT NULL,
  `score_achieved` decimal(5,2) NOT NULL DEFAULT '0.00',
  `total_score` decimal(5,2) NOT NULL DEFAULT '0.00',
  `score_percentage` decimal(5,2) NOT NULL DEFAULT '0.00',
  `is_passed` tinyint(1) NOT NULL DEFAULT '0',
  `started_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `submitted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `quiz_id` (`quiz_id`),
  KEY `idx_attempt_user` (`user_id`,`quiz_id`),
  CONSTRAINT `quiz_attempts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `quiz_attempts_ibfk_2` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `quiz_attempts`
--

LOCK TABLES `quiz_attempts` WRITE;
/*!40000 ALTER TABLE `quiz_attempts` DISABLE KEYS */;
INSERT INTO `quiz_attempts` (`id`, `user_id`, `quiz_id`, `score_achieved`, `total_score`, `score_percentage`, `is_passed`, `started_at`, `submitted_at`) VALUES (1,2,1,3.00,3.00,100.00,1,'2026-11-21 11:39:21','2026-11-21 18:39:21'),(2,2,1,3.00,3.00,100.00,1,'2026-11-21 11:39:21','2026-11-21 18:39:21'),(3,2,1,3.00,3.00,100.00,1,'2026-11-21 11:42:49','2026-11-21 18:42:49'),(4,2,1,3.00,3.00,100.00,1,'2026-11-21 11:45:38','2026-11-21 18:45:38'),(5,2,1,3.00,3.00,100.00,1,'2026-11-21 11:47:06','2026-11-21 18:47:06'),(6,2,1,3.00,3.00,100.00,1,'2026-11-21 11:47:30','2026-11-21 18:47:30'),(7,2,1,3.00,3.00,100.00,1,'2026-11-21 11:47:35','2026-11-21 18:47:35'),(8,2,1,3.00,3.00,100.00,1,'2026-11-21 11:48:19','2026-11-21 18:48:19'),(9,2,1,3.00,3.00,100.00,1,'2026-11-21 11:53:59','2026-11-21 18:53:59'),(10,2,1,3.00,3.00,100.00,1,'2026-11-21 11:54:34','2026-11-21 18:54:34'),(11,2,1,3.00,3.00,100.00,1,'2026-11-21 11:54:48','2026-11-21 18:54:48'),(12,2,1,3.00,3.00,100.00,1,'2026-11-21 12:06:40','2026-11-21 19:06:40'),(13,2,1,3.00,3.00,100.00,1,'2026-11-21 12:14:27','2026-11-21 19:14:27'),(14,2,1,3.00,3.00,100.00,1,'2026-11-21 13:10:30','2026-11-21 20:10:30'),(15,2,1,3.00,3.00,100.00,1,'2026-11-21 13:36:14','2026-11-21 20:36:14'),(16,2,1,3.00,3.00,100.00,1,'2026-11-21 13:36:38','2026-11-21 20:36:38'),(17,2,1,3.00,3.00,100.00,1,'2026-11-21 13:42:34','2026-11-21 20:42:34'),(18,2,1,3.00,3.00,100.00,1,'2026-11-21 14:07:17','2026-11-21 21:07:17'),(19,2,1,3.00,3.00,100.00,1,'2026-11-21 14:08:37','2026-11-21 21:08:37'),(20,2,1,3.00,3.00,100.00,1,'2026-11-21 14:09:50','2026-11-21 21:09:50'),(21,2,1,3.00,3.00,100.00,1,'2026-11-21 14:09:56','2026-11-21 21:09:56'),(22,2,1,3.00,3.00,100.00,1,'2026-11-21 14:15:27','2026-11-21 21:15:27'),(23,2,1,3.00,3.00,100.00,1,'2026-11-21 14:17:07','2026-11-21 21:17:07'),(24,2,1,3.00,3.00,100.00,1,'2026-11-21 14:18:15','2026-11-21 21:18:15'),(25,2,18,0.00,2.00,0.00,0,'2026-11-22 04:40:41','2026-11-22 11:40:41'),(26,2,18,1.00,2.00,50.00,0,'2026-11-22 04:40:53','2026-11-22 11:40:53'),(27,2,18,0.00,2.00,0.00,0,'2026-11-22 04:41:13','2026-11-22 11:41:13'),(28,2,18,1.00,2.00,50.00,0,'2026-11-22 04:41:36','2026-11-22 11:41:36'),(29,2,18,1.00,2.00,50.00,0,'2026-11-22 04:41:55','2026-11-22 11:41:55'),(30,2,18,0.00,2.00,0.00,0,'2026-11-22 04:42:11','2026-11-22 11:42:11'),(31,2,1,3.00,3.00,100.00,1,'2026-11-22 04:47:14','2026-11-22 11:47:14'),(32,2,1,3.00,3.00,100.00,1,'2026-11-22 04:49:51','2026-11-22 11:49:51'),(33,2,1,3.00,3.00,100.00,1,'2026-11-22 04:51:04','2026-11-22 11:51:04'),(34,2,1,3.00,3.00,100.00,1,'2026-11-22 04:54:52','2026-11-22 11:54:52'),(35,2,1,3.00,3.00,100.00,1,'2026-11-22 05:00:59','2026-11-22 12:00:59'),(36,2,1,3.00,3.00,100.00,1,'2026-11-22 05:05:38','2026-11-22 12:05:38'),(37,2,1,3.00,3.00,100.00,1,'2026-11-22 05:34:09','2026-11-22 12:34:09');
/*!40000 ALTER TABLE `quiz_attempts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `quiz_questions`
--

DROP TABLE IF EXISTS `quiz_questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `quiz_questions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `quiz_id` int unsigned NOT NULL,
  `question_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `question_type` enum('single_choice','multiple_choice','true_false') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'single_choice',
  `points` decimal(5,2) NOT NULL DEFAULT '1.00',
  `explanation` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `quiz_id` (`quiz_id`),
  CONSTRAINT `quiz_questions_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `quiz_questions`
--

LOCK TABLES `quiz_questions` WRITE;
/*!40000 ALTER TABLE `quiz_questions` DISABLE KEYS */;
INSERT INTO `quiz_questions` (`id`, `quiz_id`, `question_text`, `question_type`, `points`, `explanation`, `sort_order`, `created_at`) VALUES (1,1,'Trong mô hình MVC, thành phần nào trực tiếp tương tác với Cơ sở Dữ liệu?','single_choice',1.00,'Model đại diện cho dữ liệu và các quy tắc nghiệp vụ truy vấn DB.',1,'2026-11-21 11:33:12'),(2,1,'Kỹ thuật nào dùng để chống tấn công SQL Injection hiệu quả nhất?','single_choice',1.00,'Prepared Statements với tham số buộc chặt (parameter binding) ngăn chặn chèn mã độc.',2,'2026-11-21 11:33:12'),(3,1,'Trạng thái HTTP 200 biểu thị điều gì?','single_choice',1.00,'HTTP 200 OK báo hiệu yêu cầu đã được máy chủ xử lý thành công.',3,'2026-11-21 11:33:12'),(4,2,'Thư viện Python nào chuyên dùng để thao tác và phân tích cấu trúc dữ liệu bảng (DataFrames)?','single_choice',1.00,'Pandas cung cấp cấu trúc DataFrame mạnh mẽ cho phân tích số liệu.',1,'2026-11-21 11:33:12'),(5,2,'Thuật toán nào sau đây thuộc nhóm học có giám sát (Supervised Learning)?','single_choice',1.00,'Linear Regression và Random Forest là các thuật toán học có giám sát.',2,'2026-11-21 11:33:12'),(6,2,'Hiện tượng mô hình hoạt động cực tốt trên tập Train nhưng kém trên tập Test gọi là gì?','single_choice',1.00,'Overfitting (Quá khớp) xảy ra khi mô hình học vẹt cả nhiễu của tập huấn luyện.',3,'2026-11-21 11:33:12'),(7,3,'Hook nào trong React được dùng để thực hiện các side-effect (gọi API, lắng nghe event)?','single_choice',1.00,'useEffect xử lý các tác vụ ngoài luồng render như fetch data, timer, subscriptions.',1,'2026-11-21 11:33:12'),(8,3,'Điều gì xảy ra khi bạn gọi hàm cập nhật State trong React?','single_choice',1.00,'React xếp lịch re-render component và so sánh Virtual DOM để cập nhật DOM thật.',2,'2026-11-21 11:33:12'),(9,4,'Tính năng Auto Layout trong Figma tương tự với thuộc tính CSS nào?','single_choice',1.00,'Auto Layout mô phỏng hành vi của CSS Flexbox (hướng sắp xếp, khoảng cách, căn chỉnh).',1,'2026-11-21 11:33:12'),(10,4,'Nguyên tắc nào sau đây đảm bảo khoảng cách ngón tay bấm (tap target) thuận tiện trên Mobile?','single_choice',1.00,'Theo chuẩn Apple & Google, kích thước nút bấm tối thiểu khuyến nghị là 44x44 hoặc 48x48 dp/px.',2,'2026-11-21 11:33:12'),(11,5,'Chỉ thị nào trong Dockerfile được dùng để chỉ định Image nền khởi tạo?','single_choice',1.00,'FROM xác định base image (vd: FROM php:8.2-fpm hoặc FROM node:20).',1,'2026-11-21 11:33:12'),(12,5,'Lệnh Docker nào dùng để chạy các container đa dịch vụ được định nghĩa trong file YAML?','single_choice',1.00,'docker compose up khởi chạy toàn bộ dịch vụ được cấu hình trong docker-compose.yml.',2,'2026-11-21 11:33:12'),(13,6,'Mệnh đề nào dùng để lọc điều kiện trên kết quả đã được gom nhóm bởi GROUP BY?','single_choice',1.00,'HAVING dùng để lọc điều kiện sau khi nhóm (vd: HAVING COUNT(*) > 5), trong khi WHERE lọc trước khi nhóm.',1,'2026-11-21 11:33:12'),(14,6,'Loại JOIN nào trả về tất cả bản ghi từ bảng bên trái bất kể có bản ghi khớp bên phải hay không?','single_choice',1.00,'LEFT JOIN (hoặc LEFT OUTER JOIN) giữ lại toàn bộ hàng từ bảng bên trái.',2,'2026-11-21 11:33:12'),(15,7,'Ưu điểm lớn nhất của công nghệ được giảng dạy trong khóa học là gì?','single_choice',5.00,'Đáp án phản ánh chính xác nguyên lý thiết kế và tối ưu hiệu suất.',1,'2026-11-21 11:47:30'),(16,7,'Để bảo mật ứng dụng khi triển khai lên môi trường Production, phương pháp nào là bắt buộc?','single_choice',5.00,'HTTPS và xác thực Token an toàn là yêu cầu tiên quyết.',2,'2026-11-21 11:47:30'),(17,8,'Ưu điểm lớn nhất của công nghệ được giảng dạy trong khóa học là gì?','single_choice',5.00,'Đáp án phản ánh chính xác nguyên lý thiết kế và tối ưu hiệu suất.',1,'2026-11-21 11:47:30'),(18,8,'Để bảo mật ứng dụng khi triển khai lên môi trường Production, phương pháp nào là bắt buộc?','single_choice',5.00,'HTTPS và xác thực Token an toàn là yêu cầu tiên quyết.',2,'2026-11-21 11:47:30'),(19,9,'Ưu điểm lớn nhất của công nghệ được giảng dạy trong khóa học là gì?','single_choice',5.00,'Đáp án phản ánh chính xác nguyên lý thiết kế và tối ưu hiệu suất.',1,'2026-11-21 11:47:30'),(20,9,'Để bảo mật ứng dụng khi triển khai lên môi trường Production, phương pháp nào là bắt buộc?','single_choice',5.00,'HTTPS và xác thực Token an toàn là yêu cầu tiên quyết.',2,'2026-11-21 11:47:30'),(21,10,'Ưu điểm lớn nhất của công nghệ được giảng dạy trong khóa học là gì?','single_choice',5.00,'Đáp án phản ánh chính xác nguyên lý thiết kế và tối ưu hiệu suất.',1,'2026-11-21 11:47:30'),(22,10,'Để bảo mật ứng dụng khi triển khai lên môi trường Production, phương pháp nào là bắt buộc?','single_choice',5.00,'HTTPS và xác thực Token an toàn là yêu cầu tiên quyết.',2,'2026-11-21 11:47:30'),(23,11,'Ưu điểm lớn nhất của công nghệ được giảng dạy trong khóa học là gì?','single_choice',5.00,'Đáp án phản ánh chính xác nguyên lý thiết kế và tối ưu hiệu suất.',1,'2026-11-21 11:47:30'),(24,11,'Để bảo mật ứng dụng khi triển khai lên môi trường Production, phương pháp nào là bắt buộc?','single_choice',5.00,'HTTPS và xác thực Token an toàn là yêu cầu tiên quyết.',2,'2026-11-21 11:47:30'),(25,12,'Ưu điểm lớn nhất của công nghệ được giảng dạy trong khóa học là gì?','single_choice',5.00,'Đáp án phản ánh chính xác nguyên lý thiết kế và tối ưu hiệu suất.',1,'2026-11-21 11:47:30'),(26,12,'Để bảo mật ứng dụng khi triển khai lên môi trường Production, phương pháp nào là bắt buộc?','single_choice',5.00,'HTTPS và xác thực Token an toàn là yêu cầu tiên quyết.',2,'2026-11-21 11:47:30'),(27,13,'Trong mô hình MVC, thành phần nào chịu trách nhiệm trực tiếp tương tác và truy vấn Cơ sở dữ liệu?','single_choice',1.00,'Model là tầng đảm nhận toàn bộ logic dữ liệu, định nghĩa quan hệ và truy vấn CSDL.',1,'2026-11-21 14:07:38'),(28,13,'Giao thức HTTP hoạt động theo cơ chế nào sau đây giữa Trình duyệt và Máy chủ?','single_choice',1.00,'HTTP hoạt động theo mô hình Request - Response (Yêu cầu và Phản hồi).',2,'2026-11-21 14:07:38'),(29,13,'Mục đích quan trọng nhất của việc phân tách Controller và View trong kiến trúc MVC là gì?','single_choice',1.00,'Tách biệt luồng xử lý logic nghiệp vụ và giao diện hiển thị, giúp code dễ bảo trì và tái sử dụng.',3,'2026-11-21 14:07:38'),(30,14,'Kiểu dữ liệu nào trong Python là kiểu bất biến (Immutable)?','single_choice',1.00,'Tuple trong Python là immutable, không thể thay đổi giá trị sau khi khởi tạo.',1,'2026-11-21 14:08:32'),(31,14,'Cú pháp List Comprehension nào sau đây tạo ra danh sách số chẵn từ 0 đến 8?','single_choice',1.00,'[x for x in range(10) if x % 2 == 0] sẽ sinh ra [0, 2, 4, 6, 8].',2,'2026-11-21 14:08:32'),(32,15,'Điểm khác biệt căn bản giữa Props và State trong React là gì?','single_choice',1.00,'Props được truyền từ component cha xuống và mang tính read-only; State được quản lý nội bộ bên trong component và có thể thay đổi.',1,'2026-11-21 14:08:32'),(33,15,'Quy tắc bắt buộc khi viết thẻ JSX trong React là gì?','single_choice',1.00,'JSX phải luôn được bao bọc bởi một thẻ cha duy nhất (hoặc React Fragment).',2,'2026-11-21 14:08:32'),(34,16,'Phím tắt mặc định để tạo nhanh Auto Layout cho một cụm layer trong Figma là gì?','single_choice',1.00,'Shift + A là phím tắt tiêu chuẩn để bật Auto Layout trong Figma.',1,'2026-11-21 14:08:32'),(35,16,'Chế độ \'Fill container\' trong Auto Layout có ý nghĩa gì?','single_choice',1.00,'Layer con sẽ tự động co giãn để lấp đầy toàn bộ chiều rộng hoặc chiều cao của frame cha.',2,'2026-11-21 14:08:32'),(36,17,'Mối quan hệ giữa Docker Image và Docker Container là gì?','single_choice',1.00,'Image là bản thiết kế/khuôn mẫu chỉ đọc (Read-only Template), Container là một thể hiện (Instance) đang chạy của Image.',1,'2026-11-21 14:08:32'),(37,17,'Lệnh nào sau đây dùng để build một Docker Image từ Dockerfile trong thư mục hiện tại?','single_choice',1.00,'docker build -t <tên-image> . là lệnh chuẩn để build image.',2,'2026-11-21 14:08:32'),(38,18,'Mệnh đề nào trong SQL được dùng để lọc dữ liệu trên các hàm tổng hợp (AGGREGATE functions)?','single_choice',1.00,'Mệnh đề HAVING lọc kết quả sau khi đã GROUP BY và tính hàm tổng hợp; WHERE chỉ lọc dòng dữ liệu thô.',1,'2026-11-21 14:08:32'),(39,18,'INNER JOIN trả về kết quả như thế nào giữa 2 bảng?','single_choice',1.00,'INNER JOIN chỉ trả về các bản ghi thỏa mãn điều kiện kết nối ở cả 2 bảng.',2,'2026-11-21 14:08:32');
/*!40000 ALTER TABLE `quiz_questions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `quizzes`
--

DROP TABLE IF EXISTS `quizzes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `quizzes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `course_id` int unsigned NOT NULL,
  `lesson_id` int unsigned DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `duration_minutes` int unsigned NOT NULL DEFAULT '15',
  `passing_score` decimal(5,2) NOT NULL DEFAULT '80.00',
  `max_attempts` tinyint unsigned NOT NULL DEFAULT '0',
  `is_random_questions` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lesson_id` (`lesson_id`),
  KEY `course_id` (`course_id`),
  CONSTRAINT `quizzes_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `quizzes_ibfk_2` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `quizzes`
--

LOCK TABLES `quizzes` WRITE;
/*!40000 ALTER TABLE `quizzes` DISABLE KEYS */;
INSERT INTO `quizzes` (`id`, `course_id`, `lesson_id`, `title`, `description`, `duration_minutes`, `passing_score`, `max_attempts`, `is_random_questions`, `created_at`) VALUES (1,1,5,'Kiểm tra trắc nghiệm Kiến trúc MVC & Web Core','Bài kiểm tra trắc nghiệm đánh giá kiến thức bài học.',15,80.00,3,0,'2026-11-21 11:33:12'),(2,2,8,'Đánh giá Năng lực Python & Machine Learning','Bài kiểm tra trắc nghiệm đánh giá kiến thức bài học.',15,80.00,3,0,'2026-11-21 11:33:12'),(3,3,11,'Đánh giá Kiến thức ReactJS','Bài kiểm tra trắc nghiệm đánh giá kiến thức bài học.',15,80.00,3,0,'2026-11-21 11:33:12'),(4,4,14,'Kiểm tra Kiến thức UI/UX Design','Bài kiểm tra trắc nghiệm đánh giá kiến thức bài học.',15,80.00,3,0,'2026-11-21 11:33:12'),(5,5,17,'Đánh giá Kỹ năng Docker & DevOps','Bài kiểm tra trắc nghiệm đánh giá kiến thức bài học.',15,80.00,3,0,'2026-11-21 11:33:12'),(6,6,20,'Đánh giá Kỹ năng SQL Thực chiến','Bài kiểm tra trắc nghiệm đánh giá kiến thức bài học.',15,80.00,3,0,'2026-11-21 11:33:12'),(7,7,24,'Kiểm tra tổng hợp: Chuyên Sâu Golang & Kiến Trúc Microservices Hiệu Năng Cao','Bài kiểm tra gồm các câu hỏi trắc nghiệm kiến thức cốt lõi.',15,80.00,3,0,'2026-11-21 11:47:30'),(8,8,28,'Kiểm tra tổng hợp: Lập Trình Di Động Đa Nền Tảng với Flutter & Dart 2026','Bài kiểm tra gồm các câu hỏi trắc nghiệm kiến thức cốt lõi.',15,80.00,3,0,'2026-11-21 11:47:30'),(9,9,32,'Kiểm tra tổng hợp: Chinh Phục Điện Toán Đám Mây AWS Solutions Architect','Bài kiểm tra gồm các câu hỏi trắc nghiệm kiến thức cốt lõi.',15,80.00,3,0,'2026-11-21 11:47:30'),(10,10,36,'Kiểm tra tổng hợp: An Toàn Thông Tin & Thực Hành Ethical Hacking (Pentest Web)','Bài kiểm tra gồm các câu hỏi trắc nghiệm kiến thức cốt lõi.',15,80.00,3,0,'2026-11-21 11:47:30'),(11,11,40,'Kiểm tra tổng hợp: Digital Marketing Toàn Diện & Tăng Trưởng Doanh Thu với AI','Bài kiểm tra gồm các câu hỏi trắc nghiệm kiến thức cốt lõi.',15,80.00,3,0,'2026-11-21 11:47:30'),(12,12,44,'Kiểm tra tổng hợp: Tiếng Anh Giao Tiếp & Phỏng Vấn Dành Cho Dân IT (Tech English)','Bài kiểm tra gồm các câu hỏi trắc nghiệm kiến thức cốt lõi.',15,80.00,3,0,'2026-11-21 11:47:30'),(13,1,45,'Kiểm tra 15 phút: Kiến thức Nền tảng Web & Cấu trúc MVC','Bài kiểm tra nhanh củng cố kiến thức sau khi học xong Bài 1 & Bài 2 (35 phút video).',15,80.00,0,0,'2026-11-21 14:07:38'),(14,2,46,'Kiểm tra 15 phút: Cú pháp Python & Cấu trúc dữ liệu','Củng cố kiến thức sau khi học xong phần cú pháp cơ bản của Python (13 phút).',15,80.00,0,0,'2026-11-21 14:08:32'),(15,3,47,'Kiểm tra 15 phút: JSX, Props và Component React','Kiểm tra nhanh sau khi hoàn thành bài học về Component và Props trong React.',15,80.00,0,0,'2026-11-21 14:08:32'),(16,4,48,'Kiểm tra 15 phút: Auto Layout & Hệ thống Variants Figma','Kiểm tra nhanh kỹ năng dựng khung layout linh hoạt trong Figma sau bài học 1.',15,80.00,0,0,'2026-11-21 14:08:32'),(17,5,49,'Kiểm tra 15 phút: Khái niệm Docker, Image và Container','Kiểm tra thực hành các lệnh cơ bản Docker sau khi hoàn thành Bài 1.',15,80.00,0,0,'2026-11-21 14:08:32'),(18,6,50,'Kiểm tra 15 phút: Kỹ thuật Truy vấn SQL & JOIN Bảng','Củng cố kiến thức cú pháp SQL sau bài giảng thực chiến đầu tiên.',15,80.00,0,0,'2026-11-21 14:08:32');
/*!40000 ALTER TABLE `quizzes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rate_limits`
--

DROP TABLE IF EXISTS `rate_limits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rate_limits` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `hits` int unsigned NOT NULL DEFAULT '1',
  `expires_at` int unsigned NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rate_limits`
--

LOCK TABLES `rate_limits` WRITE;
/*!40000 ALTER TABLE `rate_limits` DISABLE KEYS */;
INSERT INTO `rate_limits` (`key`, `hits`, `expires_at`) VALUES ('forgot_password_otp:127.0.0.1',1,1795684527);
/*!40000 ALTER TABLE `rate_limits` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `refund_requests`
--

DROP TABLE IF EXISTS `refund_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `refund_requests`
--

LOCK TABLES `refund_requests` WRITE;
/*!40000 ALTER TABLE `refund_requests` DISABLE KEYS */;
INSERT INTO `refund_requests` (`id`, `order_id`, `user_id`, `method`, `amount`, `status`, `admin_note`, `created_at`, `processed_at`) VALUES (1,47,2,'wallet',2842270000,'approved','test fix','2026-07-10 20:18:01','2026-07-10 20:27:38'),(2,48,4,'wallet',1379700000,'rejected',NULL,'2026-07-10 20:25:33','2026-07-10 20:28:25');
/*!40000 ALTER TABLE `refund_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sections`
--

DROP TABLE IF EXISTS `sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sections` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `course_id` int unsigned NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_section_sort` (`course_id`,`sort_order`),
  CONSTRAINT `sections_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sections`
--

LOCK TABLES `sections` WRITE;
/*!40000 ALTER TABLE `sections` DISABLE KEYS */;
INSERT INTO `sections` (`id`, `course_id`, `title`, `sort_order`, `created_at`) VALUES (1,1,'Chương 1: Kiến thức cơ bản & Môi trường PHP (Đã cập nhật)',1,'2026-11-21 11:19:46'),(2,1,'Chương 2: Kiến trúc Nâng cao & Lập trình Hướng đối tượng',2,'2026-11-21 11:33:11'),(3,2,'Chương 1: Python Căn Bản & Cấu Trúc Dữ Liệu',1,'2026-11-21 11:33:12'),(4,3,'Chương 1: Kiến trúc React Core & Tư duy Component',1,'2026-11-21 11:33:12'),(5,4,'Chương 1: Công cụ Figma & Nguyên lý Visual Design',1,'2026-11-21 11:33:12'),(6,5,'Chương 1: Đóng gói Ứng dụng với Docker',1,'2026-11-21 11:33:12'),(7,6,'Chương 1: SQL Truy Vấn & Tối Ưu Hóa Dữ Liệu',1,'2026-11-21 11:33:12'),(8,7,'Chương 1: Nền tảng kiến thức & Thiết lập môi trường',1,'2026-11-21 11:47:30'),(9,7,'Chương 2: Thực chiến chuyên sâu & Kiểm tra đánh giá',2,'2026-11-21 11:47:30'),(10,8,'Chương 1: Nền tảng kiến thức & Thiết lập môi trường',1,'2026-11-21 11:47:30'),(11,8,'Chương 2: Thực chiến chuyên sâu & Kiểm tra đánh giá',2,'2026-11-21 11:47:30'),(12,9,'Chương 1: Nền tảng kiến thức & Thiết lập môi trường',1,'2026-11-21 11:47:30'),(13,9,'Chương 2: Thực chiến chuyên sâu & Kiểm tra đánh giá',2,'2026-11-21 11:47:30'),(14,10,'Chương 1: Nền tảng kiến thức & Thiết lập môi trường',1,'2026-11-21 11:47:30'),(15,10,'Chương 2: Thực chiến chuyên sâu & Kiểm tra đánh giá',2,'2026-11-21 11:47:30'),(16,11,'Chương 1: Nền tảng kiến thức & Thiết lập môi trường',1,'2026-11-21 11:47:30'),(17,11,'Chương 2: Thực chiến chuyên sâu & Kiểm tra đánh giá',2,'2026-11-21 11:47:30'),(18,12,'Chương 1: Nền tảng kiến thức & Thiết lập môi trường',1,'2026-11-21 11:47:30'),(19,12,'Chương 2: Thực chiến chuyên sâu & Kiểm tra đánh giá',2,'2026-11-21 11:47:30');
/*!40000 ALTER TABLE `sections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shipper_locations`
--

DROP TABLE IF EXISTS `shipper_locations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shipper_locations`
--

LOCK TABLES `shipper_locations` WRITE;
/*!40000 ALTER TABLE `shipper_locations` DISABLE KEYS */;
/*!40000 ALTER TABLE `shipper_locations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `site_promotions`
--

DROP TABLE IF EXISTS `site_promotions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `site_promotions`
--

LOCK TABLES `site_promotions` WRITE;
/*!40000 ALTER TABLE `site_promotions` DISABLE KEYS */;
INSERT INTO `site_promotions` (`id`, `name`, `discount_percent`, `status`, `starts_at`, `expires_at`, `created_at`) VALUES (1,'sale tí nhề',10,'active','2026-06-22','2026-06-23','2026-06-22 21:37:54');
/*!40000 ALTER TABLE `site_promotions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `store_stock`
--

DROP TABLE IF EXISTS `store_stock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=161 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `store_stock`
--

LOCK TABLES `store_stock` WRITE;
/*!40000 ALTER TABLE `store_stock` DISABLE KEYS */;
INSERT INTO `store_stock` (`id`, `store_id`, `product_id`, `variant_id`, `quantity`, `updated_at`) VALUES (1,1,1,0,2,'2026-07-10 19:35:49'),(2,1,2,0,1,'2026-07-10 19:35:49'),(3,1,3,0,1,'2026-07-10 19:35:49'),(4,1,4,0,3,'2026-07-10 19:35:49'),(5,1,5,0,2,'2026-07-10 19:35:49'),(6,1,6,0,1,'2026-07-10 19:35:49'),(7,1,7,0,1,'2026-07-10 19:35:49'),(8,1,8,0,1,'2026-07-10 19:35:49'),(9,1,9,0,2,'2026-07-10 19:35:49'),(10,1,10,0,1,'2026-07-10 19:35:49'),(11,1,11,0,20,'2026-07-10 19:35:49'),(12,1,12,0,16,'2026-07-10 19:35:49'),(13,1,13,0,9,'2026-07-10 19:35:49'),(14,1,14,0,6,'2026-07-10 19:35:49'),(15,1,15,0,12,'2026-07-10 19:35:49'),(16,1,16,0,1,'2026-07-10 19:35:49'),(17,1,17,0,2,'2026-07-10 19:35:49'),(18,1,18,0,5,'2026-07-10 19:35:49'),(19,1,19,0,3,'2026-07-10 19:35:49'),(20,1,20,0,2,'2026-07-10 19:35:49'),(21,1,21,0,1,'2026-07-10 19:35:49'),(22,1,22,0,4,'2026-07-10 19:35:49'),(23,1,23,0,3,'2026-07-10 19:35:49'),(24,1,24,0,1,'2026-07-10 19:35:49'),(25,1,25,0,1,'2026-07-10 19:35:49'),(26,1,26,0,1,'2026-07-10 19:35:49'),(27,1,27,0,1,'2026-07-10 19:35:49'),(28,1,28,0,1,'2026-07-10 19:35:49'),(29,1,29,0,2,'2026-07-10 19:35:49'),(30,1,30,0,3,'2026-07-10 19:35:49'),(31,2,1,0,2,'2026-07-10 19:35:49'),(32,2,2,0,1,'2026-07-10 19:35:49'),(33,2,3,0,1,'2026-07-10 19:35:49'),(34,2,4,0,3,'2026-07-10 19:35:49'),(35,2,5,0,2,'2026-07-10 19:35:49'),(36,2,6,0,1,'2026-07-10 19:35:49'),(37,2,7,0,1,'2026-07-10 19:35:49'),(38,2,8,0,1,'2026-07-10 19:35:49'),(39,2,9,0,2,'2026-07-10 19:35:49'),(40,2,10,0,1,'2026-07-10 19:35:49'),(41,2,11,0,20,'2026-07-10 19:35:49'),(42,2,12,0,16,'2026-07-10 19:35:49'),(43,2,13,0,9,'2026-07-10 19:35:49'),(44,2,14,0,6,'2026-07-10 19:35:49'),(45,2,15,0,12,'2026-07-10 19:35:49'),(46,2,16,0,1,'2026-07-10 19:35:49'),(47,2,17,0,2,'2026-07-10 19:35:49'),(48,2,18,0,5,'2026-07-10 19:35:49'),(49,2,19,0,3,'2026-07-10 19:35:49'),(50,2,20,0,2,'2026-07-10 19:35:49'),(51,2,21,0,1,'2026-07-10 19:35:49'),(52,2,22,0,4,'2026-07-10 19:35:49'),(53,2,23,0,3,'2026-07-10 19:35:49'),(54,2,24,0,1,'2026-07-10 19:35:49'),(55,2,25,0,1,'2026-07-10 19:35:49'),(56,2,26,0,1,'2026-07-10 19:35:49'),(57,2,27,0,1,'2026-07-10 19:35:49'),(58,2,28,0,1,'2026-07-10 19:35:49'),(59,2,29,0,2,'2026-07-10 19:35:49'),(60,2,30,0,3,'2026-07-10 19:35:49'),(61,3,1,0,2,'2026-07-10 19:35:49'),(62,3,2,0,1,'2026-07-10 19:35:49'),(63,3,3,0,1,'2026-07-10 19:35:49'),(64,3,4,0,3,'2026-07-10 19:35:49'),(65,3,5,0,2,'2026-07-10 19:35:49'),(66,3,6,0,1,'2026-07-10 19:35:49'),(67,3,7,0,1,'2026-07-10 19:35:49'),(68,3,8,0,1,'2026-07-10 19:35:49'),(69,3,9,0,2,'2026-07-10 19:35:49'),(70,3,10,0,1,'2026-07-10 19:35:49'),(71,3,11,0,20,'2026-07-10 19:35:49'),(72,3,12,0,16,'2026-07-10 19:35:49'),(73,3,13,0,9,'2026-07-10 19:35:49'),(74,3,14,0,6,'2026-07-10 19:35:49'),(75,3,15,0,12,'2026-07-10 19:35:49'),(76,3,16,0,1,'2026-07-10 19:35:49'),(77,3,17,0,2,'2026-07-10 19:35:49'),(78,3,18,0,5,'2026-07-10 19:35:49'),(79,3,19,0,3,'2026-07-10 19:35:49'),(80,3,20,0,2,'2026-07-10 19:35:49'),(81,3,21,0,1,'2026-07-10 19:35:49'),(82,3,22,0,4,'2026-07-10 19:35:49'),(83,3,23,0,3,'2026-07-10 19:35:49'),(84,3,24,0,1,'2026-07-10 19:35:49'),(85,3,25,0,1,'2026-07-10 19:35:49'),(86,3,26,0,1,'2026-07-10 19:35:49'),(87,3,27,0,1,'2026-07-10 19:35:49'),(88,3,28,0,1,'2026-07-10 19:35:49'),(89,3,29,0,2,'2026-07-10 19:35:49'),(90,3,30,0,3,'2026-07-10 19:35:49'),(91,4,1,0,2,'2026-07-10 19:35:49'),(92,4,2,0,1,'2026-07-10 19:35:49'),(93,4,3,0,1,'2026-07-10 19:35:49'),(94,4,4,0,3,'2026-07-10 19:35:49'),(95,4,5,0,2,'2026-07-10 19:35:49'),(96,4,6,0,1,'2026-07-10 19:35:49'),(97,4,7,0,1,'2026-07-10 19:35:49'),(98,4,8,0,1,'2026-07-10 19:35:49'),(99,4,9,0,2,'2026-07-10 19:35:49'),(100,4,10,0,1,'2026-07-10 19:35:49'),(101,4,11,0,20,'2026-07-10 19:35:49'),(102,4,12,0,16,'2026-07-10 19:35:49'),(103,4,13,0,9,'2026-07-10 19:35:49'),(104,4,14,0,6,'2026-07-10 19:35:49'),(105,4,15,0,12,'2026-07-10 19:35:49'),(106,4,16,0,1,'2026-07-10 19:35:49'),(107,4,17,0,2,'2026-07-10 19:35:49'),(108,4,18,0,5,'2026-07-10 19:35:49'),(109,4,19,0,3,'2026-07-10 19:35:49'),(110,4,20,0,2,'2026-07-10 19:35:49'),(111,4,21,0,1,'2026-07-10 19:35:49'),(112,4,22,0,4,'2026-07-10 19:35:49'),(113,4,23,0,3,'2026-07-10 19:35:49'),(114,4,24,0,1,'2026-07-10 19:35:49'),(115,4,25,0,1,'2026-07-10 19:35:49'),(116,4,26,0,1,'2026-07-10 19:35:49'),(117,4,27,0,1,'2026-07-10 19:35:49'),(118,4,28,0,1,'2026-07-10 19:35:49'),(119,4,29,0,2,'2026-07-10 19:35:49'),(120,4,30,0,3,'2026-07-10 19:35:49'),(121,5,1,0,2,'2026-07-10 19:35:49'),(122,5,2,0,1,'2026-07-10 19:35:49'),(123,5,3,0,1,'2026-07-10 19:35:49'),(124,5,4,0,3,'2026-07-10 19:35:49'),(125,5,5,0,2,'2026-07-10 19:35:49'),(126,5,6,0,1,'2026-07-10 19:35:49'),(127,5,7,0,1,'2026-07-10 19:35:49'),(128,5,8,0,1,'2026-07-10 19:35:49'),(129,5,9,0,2,'2026-07-10 19:35:49'),(130,5,10,0,1,'2026-07-10 19:35:49'),(131,5,11,0,20,'2026-07-10 19:35:49'),(132,5,12,0,16,'2026-07-10 19:35:49'),(133,5,13,0,9,'2026-07-10 19:35:49'),(134,5,14,0,6,'2026-07-10 19:35:49'),(135,5,15,0,12,'2026-07-10 19:35:49'),(136,5,16,0,1,'2026-07-10 19:35:49'),(137,5,17,0,2,'2026-07-10 19:35:49'),(138,5,18,0,5,'2026-07-10 19:35:49'),(139,5,19,0,3,'2026-07-10 19:35:49'),(140,5,20,0,2,'2026-07-10 19:35:49'),(141,5,21,0,1,'2026-07-10 19:35:49'),(142,5,22,0,4,'2026-07-10 19:35:49'),(143,5,23,0,3,'2026-07-10 19:35:49'),(144,5,24,0,1,'2026-07-10 19:35:49'),(145,5,25,0,1,'2026-07-10 19:35:49'),(146,5,26,0,1,'2026-07-10 19:35:49'),(147,5,27,0,1,'2026-07-10 19:35:49'),(148,5,28,0,1,'2026-07-10 19:35:49'),(149,5,29,0,2,'2026-07-10 19:35:49'),(150,5,30,0,3,'2026-07-10 19:35:49'),(151,1,84,0,1,'2026-11-26 07:27:47'),(152,2,84,0,1,'2026-11-26 07:27:47'),(153,3,84,0,1,'2026-11-26 07:27:47'),(154,4,84,0,1,'2026-11-26 07:27:47'),(155,5,84,0,1,'2026-11-26 07:27:47'),(156,1,85,0,10,'2026-11-26 08:24:58'),(157,2,85,0,10,'2026-11-26 08:24:58'),(158,3,85,0,10,'2026-11-26 08:24:58'),(159,4,85,0,10,'2026-11-26 08:24:58'),(160,5,85,0,10,'2026-11-26 08:24:58');
/*!40000 ALTER TABLE `store_stock` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stores`
--

DROP TABLE IF EXISTS `stores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=3161 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stores`
--

LOCK TABLES `stores` WRITE;
/*!40000 ALTER TABLE `stores` DISABLE KEYS */;
INSERT INTO `stores` (`id`, `name`, `address`, `lat`, `lng`, `phone`, `opening_hours`, `service_radius_km`, `status`, `created_at`) VALUES (1,'Trụ sở chính','Võng Thị, Tây Hồ, Hà Nội',21.05440000,105.81180000,'1800 1234','08:00-21:30',25.00,'active','2026-07-10 04:21:35'),(2,'Cơ sở 2','Số 3 Duy Tân, Cầu Giấy, Hà Nội',21.02940000,105.78250000,'1800 1234','08:00-21:30',25.00,'active','2026-07-10 04:21:35'),(3,'Cơ sở 3','Lê Duẩn, Hải Châu, Đà Nẵng',16.05940000,108.22080000,'1800 1234','08:00-21:30',25.00,'active','2026-07-10 04:21:35'),(4,'Cơ sở 4','Số 99 Nguyễn Huệ, Quận 1, TP. HCM',10.77560000,106.70190000,'1800 1234','08:00-21:30',25.00,'active','2026-07-10 04:21:35'),(5,'Cơ sở 5','Số 250 CMT8, Q.3, Sài Gòn',10.78180000,106.67980000,'1800 1234','08:00-21:30',25.00,'active','2026-07-10 04:21:35');
/*!40000 ALTER TABLE `stores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `support_messages`
--

DROP TABLE IF EXISTS `support_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `support_messages`
--

LOCK TABLES `support_messages` WRITE;
/*!40000 ALTER TABLE `support_messages` DISABLE KEYS */;
INSERT INTO `support_messages` (`id`, `session_id`, `sender`, `user_name`, `user_email`, `message`, `is_read`, `created_at`) VALUES (1,'4514baa5121baad24582da5d7a2d47c9','user','Nguyễn Văn A','khach@shop.test','chào',1,'2026-07-10 20:16:42'),(2,'4514baa5121baad24582da5d7a2d47c9','admin','Admin','','chào',1,'2026-07-10 20:16:52');
/*!40000 ALTER TABLE `support_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `trade_ins`
--

DROP TABLE IF EXISTS `trade_ins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `trade_ins`
--

LOCK TABLES `trade_ins` WRITE;
/*!40000 ALTER TABLE `trade_ins` DISABLE KEYS */;
INSERT INTO `trade_ins` (`id`, `user_id`, `customer_name`, `customer_phone`, `device_model`, `device_condition`, `estimated_value`, `voucher_code`, `status`, `created_at`) VALUES (1,NULL,'huy','0788415516','iPhone 14 Pro Max','Máy đẹp 99% (Không trầy xước)',18000000,'TI-65E07DB4','completed','2026-07-10 20:36:57');
/*!40000 ALTER TABLE `trade_ins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_addresses`
--

DROP TABLE IF EXISTS `user_addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_addresses` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `label` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `recipient` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `address_line` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ward` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `district` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`),
  CONSTRAINT `user_addresses_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_addresses`
--

LOCK TABLES `user_addresses` WRITE;
/*!40000 ALTER TABLE `user_addresses` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_addresses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_vouchers`
--

DROP TABLE IF EXISTS `user_vouchers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_vouchers`
--

LOCK TABLES `user_vouchers` WRITE;
/*!40000 ALTER TABLE `user_vouchers` DISABLE KEYS */;
INSERT INTO `user_vouchers` (`id`, `user_id`, `code`, `discount_percent`, `max_discount`, `expires_at`, `status`, `source_order_id`, `used_order_id`, `created_at`) VALUES (1,52,'WELCOME10-766',10,NULL,'2026-12-26','active',NULL,NULL,'2026-11-26 14:07:16'),(2,53,'WELCOME10-738',10,NULL,'2026-12-26','active',NULL,NULL,'2026-11-26 14:13:53'),(4,55,'WELCOME10-754',10,NULL,'2026-12-26','active',NULL,NULL,'2026-11-26 15:16:46'),(5,56,'WELCOME10-646',10,NULL,'2026-12-26','active',NULL,NULL,'2026-11-26 15:17:06'),(6,57,'WELCOME10-929',10,NULL,'2026-12-26','active',NULL,NULL,'2026-11-26 16:03:01'),(7,57,'HADES15-384',15,NULL,'2026-12-26','active',NULL,NULL,'2026-11-26 16:03:01');
/*!40000 ALTER TABLE `user_vouchers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_wallets`
--

DROP TABLE IF EXISTS `user_wallets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_wallets` (
  `user_id` int unsigned NOT NULL,
  `balance` bigint unsigned NOT NULL DEFAULT '0',
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  CONSTRAINT `user_wallets_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_wallets`
--

LOCK TABLES `user_wallets` WRITE;
/*!40000 ALTER TABLE `user_wallets` DISABLE KEYS */;
INSERT INTO `user_wallets` (`user_id`, `balance`, `updated_at`) VALUES (1,0,'2026-07-10 20:02:33'),(2,2842270000,'2026-07-10 20:27:38'),(56,0,'2026-11-26 15:18:50'),(57,0,'2026-11-26 16:03:05');
/*!40000 ALTER TABLE `user_wallets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','customer','shipper','student','instructor') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'student',
  `status` enum('active','inactive','banned') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `failed_login_attempts` int unsigned NOT NULL DEFAULT '0',
  `lockout_until` datetime DEFAULT NULL,
  `loyalty_points` int unsigned NOT NULL DEFAULT '0',
  `loyalty_tier` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Standard',
  `two_factor_secret` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `two_factor_enabled` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`id`, `name`, `email`, `phone`, `avatar`, `password`, `role`, `status`, `created_at`, `failed_login_attempts`, `lockout_until`, `loyalty_points`, `loyalty_tier`, `two_factor_secret`, `two_factor_enabled`) VALUES (1,'Admin','admin@shop.test','0988888888',NULL,'$2y$10$kdcd7bTl11qWkeuQlDjHd.wgsCUWuTjJnwtXad0WNKtB/9154yWwu','admin','active','2026-06-16 01:46:26',0,NULL,0,'Standard',NULL,0),(2,'Nguyễn Văn A','khach@shop.test','0912345678',NULL,'$2y$10$Qrp7GX7/3abDVbnAZdVLX.1edRvM80TkGu4fyTWJPaFa27LA2WbAC','customer','active','2026-06-16 01:46:26',0,NULL,22823,'Diamond',NULL,0),(21,'Nguyễn Văn Shipper','shipper@shop.test','0987654321',NULL,'$2y$10$Ad7KPM/fgPDwS9pfcemkVO.Rj33Co7FWQUtjX3faWjor3mIavORK6','shipper','active','2026-07-10 20:00:06',0,NULL,0,'Standard',NULL,0),(22,'Tài khoản Google','user.google@gmail.com','',NULL,'$2y$10$yp30kH9E0kFiPmcL3z2/gu.QGbEMuIIdCjjrHo//Xi5MeMH7m/bna','customer','active','2026-08-10 00:13:29',0,NULL,0,'Standard',NULL,0),(23,'Google User','fanhinbenho@gmail.com','',NULL,'$2y$10$KiESqwlXqDz72OJL1xu0PO/QHfnbMMVP9ylvgkTcUT0wo0Uruq2m6','customer','active','2026-08-12 13:19:52',0,NULL,0,'Standard',NULL,0),(24,'Google User','huy28072k5@gmail.comgmaigmai','',NULL,'$2y$10$.xlA7djohqSRJxDSAxwaOe2bbiP9Jr2PyjCIrceWECc9KBd/agHxi','customer','active','2026-08-12 14:35:25',0,NULL,0,'Standard',NULL,0),(25,'ThS. Nguyễn Văn A','giangvien@lms.local','',NULL,'$2y$10$DJs1JtsYS7c8XSc.//lW7OIj/ppI5T8vxyBzpERwKo/MDFB.t4z.6','instructor','active','2026-11-21 11:19:27',0,NULL,0,'Standard',NULL,0),(26,'TS. Trần Hoàng Minh','minh.tran@lms.local','',NULL,'$2y$10$.algbcOerNda/HuOgNCyeOjEDlPqhF.g8FT7ryw3FsX3CUNTOzOTm','instructor','active','2026-11-21 11:33:11',0,NULL,0,'Standard',NULL,0),(27,'Lê Thị Mai Hương','huong.le@lms.local','',NULL,'$2y$10$.algbcOerNda/HuOgNCyeOjEDlPqhF.g8FT7ryw3FsX3CUNTOzOTm','instructor','active','2026-11-21 11:33:11',0,NULL,0,'Standard',NULL,0),(28,'Hoàng Văn Đức','duc.hoang@lms.local','',NULL,'$2y$10$.algbcOerNda/HuOgNCyeOjEDlPqhF.g8FT7ryw3FsX3CUNTOzOTm','instructor','active','2026-11-21 11:33:11',0,NULL,0,'Standard',NULL,0),(40,'ThS. Đặng Tuấn Anh','tuananh.dang@lms.local','0912345678',NULL,'$2y$10$5G870H/L0syCspwd8XNQvez5jXU1X7iuDHPS3BHGMPHV4G/iv.2/m','instructor','active','2026-11-21 11:47:30',0,NULL,0,'Standard',NULL,0),(41,'KTS. Vũ Hải Nam','hainam.vu@lms.local','0912345678',NULL,'$2y$10$5G870H/L0syCspwd8XNQvez5jXU1X7iuDHPS3BHGMPHV4G/iv.2/m','instructor','active','2026-11-21 11:47:30',0,NULL,0,'Standard',NULL,0),(42,'Bùi Quỳnh Chi','quynhchi.bui@lms.local','0912345678',NULL,'$2y$10$5G870H/L0syCspwd8XNQvez5jXU1X7iuDHPS3BHGMPHV4G/iv.2/m','instructor','active','2026-11-21 11:47:30',0,NULL,0,'Standard',NULL,0),(43,'Nguyễn Đình Quân','quan.nguyen@gmail.com','0988776655',NULL,'$2y$10$5G870H/L0syCspwd8XNQvez5jXU1X7iuDHPS3BHGMPHV4G/iv.2/m','student','active','2026-11-21 11:47:30',0,NULL,0,'Standard',NULL,0),(44,'Phạm Thuỳ Linh','linh.pham@gmail.com','0988776655',NULL,'$2y$10$5G870H/L0syCspwd8XNQvez5jXU1X7iuDHPS3BHGMPHV4G/iv.2/m','student','active','2026-11-21 11:47:30',0,NULL,0,'Standard',NULL,0),(45,'Hoàng Minh Tuấn','tuan.hoang@gmail.com','0988776655',NULL,'$2y$10$5G870H/L0syCspwd8XNQvez5jXU1X7iuDHPS3BHGMPHV4G/iv.2/m','student','active','2026-11-21 11:47:30',0,NULL,0,'Standard',NULL,0),(46,'Đỗ Phương Anh','anh.do@gmail.com','0988776655',NULL,'$2y$10$5G870H/L0syCspwd8XNQvez5jXU1X7iuDHPS3BHGMPHV4G/iv.2/m','student','active','2026-11-21 11:47:30',0,NULL,0,'Standard',NULL,0),(47,'Trịnh Quốc Bảo','bao.trinh@gmail.com','0988776655',NULL,'$2y$10$5G870H/L0syCspwd8XNQvez5jXU1X7iuDHPS3BHGMPHV4G/iv.2/m','student','active','2026-11-21 11:47:30',0,NULL,0,'Standard',NULL,0),(48,'Ngô Thị Cẩm Tú','tu.ngo@gmail.com','0988776655',NULL,'$2y$10$5G870H/L0syCspwd8XNQvez5jXU1X7iuDHPS3BHGMPHV4G/iv.2/m','student','active','2026-11-21 11:47:30',0,NULL,0,'Standard',NULL,0),(52,'Google User','huy28072k5@gmail.comcom','',NULL,'$2y$10$ywgSCoLkG/2ea2itDH3KlOHAF5hHbDW98u3RG5Rnyo6jF1MqZUPvG','customer','active','2026-11-26 14:07:16',0,NULL,0,'Standard',NULL,0),(53,'Google User','huy28272k5@gmail.com','',NULL,'$2y$10$/ktgoQzr4IhK.RIGA9Ak9eprRatP9Prx.PSHb0.X3wRI5RhgXktsG','customer','active','2026-11-26 14:13:53',0,NULL,0,'Standard',NULL,0),(55,'Google User','thanhhaitk25@gmail.com','',NULL,'$2y$10$/wES64ErE0UJGzeez/Gaf.A0IhxioPNG0chiYObf3uKjEuKXtSMtG','customer','active','2026-11-26 15:16:46',0,NULL,0,'Standard',NULL,0),(56,'Google User','huy28072k5@gmail.com','',NULL,'$2y$10$C3SOqTqEaiu4R38He0Z/Q.obdJSGZH/oHvnOB58b0MGqynFoyToK6','customer','active','2026-11-26 15:17:06',0,NULL,0,'Standard',NULL,0),(57,'Trần Thị Vân','mail1.tqh123@gmail.com','0788415516',NULL,'$2y$10$1aB39LmfOmcXk7C62UhAmeqtBN5fuURucz3.XwfoF/0Iz6qUhkfXK','customer','active','2026-11-26 16:03:01',0,NULL,0,'Standard',NULL,0),(60,'Nguyễn Văn A','asdasda@gmail.com','0123444444445',NULL,'$2y$10$b4uMI.Nevx2H7RUzRXnc8.ZK7fJhhxGMinAgEOX6tFf2LQkoYC3sO','instructor','active','2026-11-26 14:27:16',0,NULL,0,'Standard',NULL,0);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `v_order_product_lines`
--

DROP TABLE IF EXISTS `v_order_product_lines`;
/*!50001 DROP VIEW IF EXISTS `v_order_product_lines`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_order_product_lines` AS SELECT
 1 AS `order_id`,
  1 AS `order_code`,
  1 AS `customer_name`,
  1 AS `email`,
  1 AS `phone`,
  1 AS `payment_method`,
  1 AS `payment_status`,
  1 AS `order_total`,
  1 AS `order_date`,
  1 AS `line_id`,
  1 AS `product_id`,
  1 AS `product_name`,
  1 AS `quantity`,
  1 AS `unit_price`,
  1 AS `line_total`,
  1 AS `product_image`,
  1 AS `product_stock`,
  1 AS `category_name` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_order_summary`
--

DROP TABLE IF EXISTS `v_order_summary`;
/*!50001 DROP VIEW IF EXISTS `v_order_summary`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_order_summary` AS SELECT
 1 AS `order_id`,
  1 AS `order_code`,
  1 AS `customer_name`,
  1 AS `email`,
  1 AS `phone`,
  1 AS `payment_method`,
  1 AS `payment_status`,
  1 AS `total_amount`,
  1 AS `created_at`,
  1 AS `item_count`,
  1 AS `total_quantity`,
  1 AS `product_list` */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `wallet_transactions`
--

DROP TABLE IF EXISTS `wallet_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wallet_transactions`
--

LOCK TABLES `wallet_transactions` WRITE;
/*!40000 ALTER TABLE `wallet_transactions` DISABLE KEYS */;
INSERT INTO `wallet_transactions` (`id`, `user_id`, `type`, `amount`, `ref_type`, `ref_id`, `note`, `created_at`) VALUES (3,2,'credit',2842270000,'refund',1,'Hoàn tiền đơn HD260710-QG62M','2026-07-10 20:27:38');
/*!40000 ALTER TABLE `wallet_transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `warehouses`
--

DROP TABLE IF EXISTS `warehouses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `warehouses` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `warehouses`
--

LOCK TABLES `warehouses` WRITE;
/*!40000 ALTER TABLE `warehouses` DISABLE KEYS */;
INSERT INTO `warehouses` (`id`, `name`, `latitude`, `longitude`, `is_active`) VALUES (1,'Kho chính Hà Nội',21.02850000,105.85420000,1);
/*!40000 ALTER TABLE `warehouses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `warranties`
--

DROP TABLE IF EXISTS `warranties`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `warranties`
--

LOCK TABLES `warranties` WRITE;
/*!40000 ALTER TABLE `warranties` DISABLE KEYS */;
INSERT INTO `warranties` (`id`, `order_id`, `product_id`, `customer_name`, `customer_phone`, `imei`, `warranty_code`, `status`, `started_at`, `expired_at`, `created_at`, `email`) VALUES (1,9,1,'Nguyen Van A','0987654321','866310356811401','WAR-H5MT2K2I','active','2026-07-10 04:22:02','2027-07-10 04:22:02','2026-07-10 04:22:02',NULL),(2,10,2,'Nguyen Van A','0987654321','868304397803763','WAR-MS6A3ZBW','active','2026-07-10 04:24:55','2027-07-10 04:24:55','2026-07-10 04:24:55',NULL),(3,11,4,'Nguyen Van A','0987654321','863913911242677','WAR-3DSRDJQG','active','2026-07-10 04:26:03','2027-07-10 04:26:03','2026-07-10 04:26:03',NULL),(4,12,36,'Nguyen Van A','0987654321','869106745489005','WAR-BZJ1F9FC','active','2026-07-10 04:26:54','2027-07-10 04:26:54','2026-07-10 04:26:54',NULL),(5,13,37,'Nguyen Van A','0987654321','864304033578474','WAR-JOZBVB4I','active','2026-07-10 04:32:11','2027-07-10 04:32:11','2026-07-10 04:32:11',NULL),(6,14,39,'Nguyen Van A','0987654321','867793051195531','WAR-M4253A5H','active','2026-07-10 04:32:30','2027-07-10 04:32:30','2026-07-10 04:32:30',NULL),(7,15,41,'Nguyen Van A','0987654321','866610891738443','WAR-33KGZGHG','active','2026-07-10 04:46:57','2027-07-10 04:46:57','2026-07-10 04:46:57',NULL),(8,16,43,'Nguyen Van A','0987654321','864488235362118','WAR-Q4PNBHC4','active','2026-07-10 04:47:12','2027-07-10 04:47:12','2026-07-10 04:47:12',NULL),(9,17,45,'Nguyen Van A','0987654321','862740937697977','WAR-B22EYRGX','active','2026-07-10 04:49:43','2027-07-10 04:49:43','2026-07-10 04:49:43',NULL),(10,18,47,'Nguyen Van A','0987654321','863842277468548','WAR-SNM4BGFE','active','2026-07-10 04:52:29','2027-07-10 04:52:29','2026-07-10 04:52:29',NULL),(11,19,49,'Nguyen Van A','0987654321','868689334926225','WAR-X4K5R13G','active','2026-07-10 04:58:10','2027-07-10 04:58:10','2026-07-10 04:58:10',NULL),(12,20,51,'Nguyen Van A','0987654321','864448408236350','WAR-8LCCNPW1','active','2026-07-10 05:00:23','2027-07-10 05:00:23','2026-07-10 05:00:23',NULL),(13,23,55,'Nguyen Van A','0987654321','860227122779213','WAR-3F464O4K','active','2026-07-10 05:01:08','2027-07-10 05:01:08','2026-07-10 05:01:08',NULL),(14,26,59,'Nguyen Van A','0987654321','867418786652707','WAR-QCRMFE2F','active','2026-07-10 05:02:38','2027-07-10 05:02:38','2026-07-10 05:02:38',NULL),(15,29,63,'Nguyen Van A','0987654321','862233312781525','WAR-SM3GB9OJ','active','2026-07-10 05:03:08','2027-07-10 05:03:08','2026-07-10 05:03:08',NULL),(16,32,67,'Nguyen Van A','0987654321','863918212512837','WAR-I0XW6O2D','active','2026-07-10 19:35:54','2027-07-10 19:35:54','2026-07-10 19:35:54',NULL),(17,35,71,'Nguyen Van A','0987654321','861793854804612','WAR-3INY4UO2','active','2026-07-10 19:42:45','2027-07-10 19:42:45','2026-07-10 19:42:45',NULL),(18,38,75,'Nguyen Van A','0987654321','861078796456070','WAR-RIWKCA1K','active','2026-07-10 19:43:35','2027-07-10 19:43:35','2026-07-10 19:43:35',NULL),(19,41,79,'Nguyen Van A','0987654321','860474138598756','WAR-MNA80UA4','active','2026-07-10 19:45:04','2027-07-10 19:45:04','2026-07-10 19:45:04',NULL),(20,44,83,'Nguyen Van A','0987654321','865437811525430','WAR-EJGUV6RG','active','2026-07-10 19:51:25','2027-07-10 19:51:25','2026-07-10 19:51:25','nva@example.com'),(21,47,28,'Nguyễn Văn A','0912345678','865122900450266','WAR-UR9UM08R','active','2026-07-10 20:17:29','2027-07-10 20:17:29','2026-07-10 20:17:29','khach@shop.test'),(22,47,26,'Nguyễn Văn A','0912345678','863986954491743','WAR-FP5MTM4H','active','2026-07-10 20:17:29','2027-07-10 20:17:29','2026-07-10 20:17:34','khach@shop.test'),(23,47,29,'Nguyễn Văn A','0912345678','868239826359720','WAR-P8QAX33N','active','2026-07-10 20:17:29','2027-07-10 20:17:29','2026-07-10 20:17:39','khach@shop.test'),(24,47,24,'Nguyễn Văn A','0912345678','865750426261051','WAR-7QZXA1AO','active','2026-07-10 20:17:29','2027-07-10 20:17:29','2026-07-10 20:17:44','khach@shop.test'),(25,47,8,'Nguyễn Văn A','0912345678','863510717734154','WAR-P2UC4WAL','active','2026-07-10 20:17:29','2027-07-10 20:17:29','2026-07-10 20:17:49','khach@shop.test'),(26,47,8,'Nguyễn Văn A','0912345678','862442150095926','WAR-W39VZLIQ','active','2026-07-10 20:17:29','2027-07-10 20:17:29','2026-07-10 20:17:54','khach@shop.test'),(27,47,8,'Nguyễn Văn A','0912345678','869676632770584','WAR-CDKUYIKO','active','2026-07-10 20:17:29','2027-07-10 20:17:29','2026-07-10 20:18:00','khach@shop.test'),(28,47,8,'Nguyễn Văn A','0912345678','864232097793167','WAR-BFEZGB51','active','2026-07-10 20:17:29','2027-07-10 20:17:29','2026-07-10 20:18:06','khach@shop.test'),(29,47,8,'Nguyễn Văn A','0912345678','865864148963046','WAR-VVVCWJAZ','active','2026-07-10 20:17:29','2027-07-10 20:17:29','2026-07-10 20:18:11','khach@shop.test'),(30,47,8,'Nguyễn Văn A','0912345678','869298350609561','WAR-I9WMJXDQ','active','2026-07-10 20:17:29','2027-07-10 20:17:29','2026-07-10 20:18:16','khach@shop.test'),(31,47,8,'Nguyễn Văn A','0912345678','864699292805092','WAR-QAPEAIGI','active','2026-07-10 20:17:29','2027-07-10 20:17:29','2026-07-10 20:18:21','khach@shop.test'),(32,47,8,'Nguyễn Văn A','0912345678','869782477295160','WAR-0PT4LAG0','active','2026-07-10 20:17:29','2027-07-10 20:17:29','2026-07-10 20:18:26','khach@shop.test'),(33,47,8,'Nguyễn Văn A','0912345678','868383424063364','WAR-OW1PNBGL','active','2026-07-10 20:17:29','2027-07-10 20:17:29','2026-07-10 20:18:32','khach@shop.test'),(34,47,8,'Nguyễn Văn A','0912345678','867722938847331','WAR-ZN6PMBAT','active','2026-07-10 20:17:29','2027-07-10 20:17:29','2026-07-10 20:18:37','khach@shop.test'),(35,47,8,'Nguyễn Văn A','0912345678','861744576655637','WAR-WLMDGYDV','active','2026-07-10 20:17:29','2027-07-10 20:17:29','2026-07-10 20:18:43','khach@shop.test'),(36,47,8,'Nguyễn Văn A','0912345678','864050046370554','WAR-AM9ST8JG','active','2026-07-10 20:17:29','2027-07-10 20:17:29','2026-07-10 20:18:48','khach@shop.test'),(37,47,8,'Nguyễn Văn A','0912345678','863152291379029','WAR-Q2L8NTWS','active','2026-07-10 20:17:29','2027-07-10 20:17:29','2026-07-10 20:18:54','khach@shop.test'),(38,47,8,'Nguyễn Văn A','0912345678','861910281673136','WAR-LAM3LZ3Q','active','2026-07-10 20:17:29','2027-07-10 20:17:29','2026-07-10 20:19:00','khach@shop.test'),(39,47,8,'Nguyễn Văn A','0912345678','864086314937666','WAR-HO9IRRG7','active','2026-07-10 20:17:29','2027-07-10 20:17:29','2026-07-10 20:19:05','khach@shop.test'),(40,47,8,'Nguyễn Văn A','0912345678','869246761526031','WAR-JKWJ77PQ','active','2026-07-10 20:17:29','2027-07-10 20:17:29','2026-07-10 20:19:10','khach@shop.test'),(41,47,8,'Nguyễn Văn A','0912345678','866731689362463','WAR-J8QXEDUF','active','2026-07-10 20:17:29','2027-07-10 20:17:29','2026-07-10 20:19:15','khach@shop.test'),(42,47,8,'Nguyễn Văn A','0912345678','860355499366841','WAR-ALAR5H6X','active','2026-07-10 20:17:29','2027-07-10 20:17:29','2026-07-10 20:19:20','khach@shop.test'),(43,47,8,'Nguyễn Văn A','0912345678','865024179928696','WAR-YUIVUGZP','active','2026-07-10 20:17:29','2027-07-10 20:17:29','2026-07-10 20:19:27','khach@shop.test'),(44,47,8,'Nguyễn Văn A','0912345678','863750074041144','WAR-62GUG6HS','active','2026-07-10 20:17:29','2027-07-10 20:17:29','2026-07-10 20:19:32','khach@shop.test'),(45,47,8,'Nguyễn Văn A','0912345678','867217733070389','WAR-K988RF6Z','active','2026-07-10 20:17:29','2027-07-10 20:17:29','2026-07-10 20:19:38','khach@shop.test'),(46,47,8,'Nguyễn Văn A','0912345678','861501860695901','WAR-D6CILLFY','active','2026-07-10 20:17:29','2027-07-10 20:17:29','2026-07-10 20:19:44','khach@shop.test'),(47,47,8,'Nguyễn Văn A','0912345678','860244692347896','WAR-UZR68QWB','active','2026-07-10 20:17:29','2027-07-10 20:17:29','2026-07-10 20:19:50','khach@shop.test'),(48,47,8,'Nguyễn Văn A','0912345678','860048294194546','WAR-PC1DTJE5','active','2026-07-10 20:17:29','2027-07-10 20:17:29','2026-07-10 20:19:56','khach@shop.test'),(49,175,29,'Nguyễn Văn A','0912345678','869842629936715','WAR-PO6AZLSE','active','2026-11-26 14:46:21','2027-11-26 14:46:21','2026-11-26 07:46:21','khach@shop.test');
/*!40000 ALTER TABLE `warranties` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `warranty_claims`
--

DROP TABLE IF EXISTS `warranty_claims`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `warranty_claims`
--

LOCK TABLES `warranty_claims` WRITE;
/*!40000 ALTER TABLE `warranty_claims` DISABLE KEYS */;
INSERT INTO `warranty_claims` (`id`, `warranty_id`, `customer_note`, `admin_note`, `status`, `created_at`, `updated_at`) VALUES (1,1,'Máy không lên nguồn','Đang thay thế IC nguồn chính hãng','repairing','2026-07-10 04:22:02','2026-07-10 04:22:02'),(2,2,'Máy không lên nguồn','Đang thay thế IC nguồn chính hãng','repairing','2026-07-10 04:24:55','2026-07-10 04:24:55'),(3,3,'Máy không lên nguồn','Đang thay thế IC nguồn chính hãng','repairing','2026-07-10 04:26:03','2026-07-10 04:26:03'),(4,4,'Máy không lên nguồn','Đang thay thế IC nguồn chính hãng','repairing','2026-07-10 04:26:54','2026-07-10 04:26:54'),(5,5,'Máy không lên nguồn','Đang thay thế IC nguồn chính hãng','repairing','2026-07-10 04:32:11','2026-07-10 04:32:11'),(6,6,'Máy không lên nguồn','Đang thay thế IC nguồn chính hãng','repairing','2026-07-10 04:32:30','2026-07-10 04:32:30'),(7,7,'Máy không lên nguồn','Đang thay thế IC nguồn chính hãng','repairing','2026-07-10 04:46:57','2026-07-10 04:46:57'),(8,8,'Máy không lên nguồn','Đang thay thế IC nguồn chính hãng','repairing','2026-07-10 04:47:12','2026-07-10 04:47:12'),(9,9,'Máy không lên nguồn','Đang thay thế IC nguồn chính hãng','repairing','2026-07-10 04:49:43','2026-07-10 04:49:43'),(10,10,'Máy không lên nguồn','Đang thay thế IC nguồn chính hãng','repairing','2026-07-10 04:52:29','2026-07-10 04:52:29'),(11,11,'Máy không lên nguồn','Đang thay thế IC nguồn chính hãng','repairing','2026-07-10 04:58:10','2026-07-10 04:58:10'),(12,12,'Máy không lên nguồn','Đang thay thế IC nguồn chính hãng','repairing','2026-07-10 05:00:23','2026-07-10 05:00:23'),(13,13,'Máy không lên nguồn','Đang thay thế IC nguồn chính hãng','repairing','2026-07-10 05:01:08','2026-07-10 05:01:08'),(14,14,'Máy không lên nguồn','Đang thay thế IC nguồn chính hãng','repairing','2026-07-10 05:02:38','2026-07-10 05:02:38'),(15,15,'Máy không lên nguồn','Đang thay thế IC nguồn chính hãng','repairing','2026-07-10 05:03:08','2026-07-10 05:03:08'),(16,16,'Máy không lên nguồn','Đang thay thế IC nguồn chính hãng','repairing','2026-07-10 19:35:54','2026-07-10 19:35:54'),(17,17,'Máy không lên nguồn','Đang thay thế IC nguồn chính hãng','repairing','2026-07-10 19:42:45','2026-07-10 19:42:45'),(18,18,'Máy không lên nguồn','Đang thay thế IC nguồn chính hãng','repairing','2026-07-10 19:43:35','2026-07-10 19:43:35'),(19,19,'Máy không lên nguồn','Đang thay thế IC nguồn chính hãng','repairing','2026-07-10 19:45:04','2026-07-10 19:45:04'),(20,20,'Máy không lên nguồn','Đang thay thế IC nguồn chính hãng','repairing','2026-07-10 19:51:25','2026-07-10 19:51:25');
/*!40000 ALTER TABLE `warranty_claims` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wishlists`
--

DROP TABLE IF EXISTS `wishlists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wishlists` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `product_id` int unsigned NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_wishlists_user_product` (`user_id`,`product_id`),
  KEY `idx_wishlists_user` (`user_id`),
  KEY `idx_wishlists_product` (`product_id`),
  CONSTRAINT `fk_wishlists_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_wishlists_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wishlists`
--

LOCK TABLES `wishlists` WRITE;
/*!40000 ALTER TABLE `wishlists` DISABLE KEYS */;
/*!40000 ALTER TABLE `wishlists` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Final view structure for view `v_order_product_lines`
--

/*!50001 DROP VIEW IF EXISTS `v_order_product_lines`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`hades_user`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `v_order_product_lines` AS select `o`.`id` AS `order_id`,`o`.`order_code` AS `order_code`,`o`.`customer_name` AS `customer_name`,`o`.`email` AS `email`,`o`.`phone` AS `phone`,`o`.`payment_method` AS `payment_method`,`o`.`payment_status` AS `payment_status`,`o`.`total_amount` AS `order_total`,`o`.`created_at` AS `order_date`,`oi`.`id` AS `line_id`,`oi`.`product_id` AS `product_id`,`oi`.`product_name` AS `product_name`,`oi`.`quantity` AS `quantity`,`oi`.`price` AS `unit_price`,(`oi`.`quantity` * `oi`.`price`) AS `line_total`,`p`.`image` AS `product_image`,`p`.`stock` AS `product_stock`,`c`.`name` AS `category_name` from (((`orders` `o` join `order_items` `oi` on((`oi`.`order_id` = `o`.`id`))) left join `products` `p` on((`p`.`id` = `oi`.`product_id`))) left join `categories` `c` on((`c`.`id` = `p`.`category_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_order_summary`
--

/*!50001 DROP VIEW IF EXISTS `v_order_summary`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`hades_user`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `v_order_summary` AS select `o`.`id` AS `order_id`,`o`.`order_code` AS `order_code`,`o`.`customer_name` AS `customer_name`,`o`.`email` AS `email`,`o`.`phone` AS `phone`,`o`.`payment_method` AS `payment_method`,`o`.`payment_status` AS `payment_status`,`o`.`total_amount` AS `total_amount`,`o`.`created_at` AS `created_at`,count(`oi`.`id`) AS `item_count`,coalesce(sum(`oi`.`quantity`),0) AS `total_quantity`,group_concat(concat(`oi`.`product_name`,' ×',`oi`.`quantity`,' @ ',format(`oi`.`price`,0),'đ') order by `oi`.`id` ASC separator ' | ') AS `product_list` from (`orders` `o` left join `order_items` `oi` on((`oi`.`order_id` = `o`.`id`))) group by `o`.`id`,`o`.`order_code`,`o`.`customer_name`,`o`.`email`,`o`.`phone`,`o`.`payment_method`,`o`.`payment_status`,`o`.`total_amount`,`o`.`created_at` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-11-26 22:35:54
