-- SQL Update data for Shop Hades
-- To run: mysql -u root -p shop_hades < data/shop_hades_update.sql

USE shop_hades;

-- Tắt khóa ngoại để xóa dữ liệu cũ
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE products;
TRUNCATE TABLE categories;
SET FOREIGN_KEY_CHECKS = 1;

-- Thêm 7 loại sản phẩm chính
INSERT INTO categories (id, name, description, status) VALUES
(1, 'Điện thoại', 'Điện thoại di động chính hãng, trả góp 0%', 'active'),
(2, 'Laptop', 'Máy tính xách tay từ các thương hiệu hàng đầu', 'active'),
(3, 'Phụ kiện', 'Tai nghe, cáp sạc, sạc dự phòng chất lượng cao', 'active'),
(4, 'Smartwatch', 'Đồng hồ thông minh theo dõi sức khỏe', 'active'),
(5, 'Đồng hồ', 'Đồng hồ đeo tay Casio lịch lãm, cá tính', 'active'),
(6, 'Máy cũ', 'Điện thoại, Laptop đã qua sử dụng, bảo hành uy tín', 'active'),
(7, 'Tablet', 'Máy tính bảng Apple iPad giải trí, làm việc tiện lợi', 'active');

-- Thêm 30 sản phẩm chia đều theo nhóm
INSERT INTO products (category_id, name, price, description, detail, stock, image, status) VALUES
-- Nhóm 1: Điện thoại (id loại = 1)
(1, 'iPhone 15 Pro Max 256GB', 29990000, 'Khung Titan cao cấp, chip A17 Pro siêu mạnh mẽ.', 'iPhone 15 Pro Max sở hữu thiết kế khung titan siêu bền và nhẹ, nút Action mới tiện dụng, hệ thống camera chuyên nghiệp 48MP và cổng kết nối USB-C tốc độ cao.', 12, 'ip15.webp', 'active'),
(1, 'iPhone 16 Pro 128GB', 28990000, 'Nút điều khiển camera chuyên biệt, chip A18 Pro.', 'iPhone 16 Pro nâng tầm nhiếp ảnh di động với nút Camera Control mới, chip xử lý A18 Pro tối ưu hiệu năng AI và thiết kế tản nhiệt tốt hơn.', 8, 'ip16.webp', 'active'),
(1, 'iPhone 17 Ultra Concept', 39990000, 'Thiết kế siêu mỏng tương lai, viền màn hình vô cực.', 'iPhone 17 Ultra là đỉnh cao công nghệ màn hình tràn viền hoàn toàn không khuyết điểm, camera ẩn dưới màn hình và camera zoom quang học 10x.', 3, 'ip17.webp', 'active'),
(1, 'iPhone 15 128GB', 19990000, 'Màn hình Dynamic Island, mặt lưng kính pha màu độc đáo.', 'iPhone 15 trang bị chip A16 Bionic mượt mà, camera sau nâng cấp lên 48MP và cổng sạc USB-C phổ biến.', 15, 'ip15.webp', 'active'),
(1, 'iPhone 16 Plus 256GB', 25990000, 'Màn hình 6.7 inch siêu rộng rãi, pin trâu vượt trội.', 'iPhone 16 Plus mang đến không gian trải nghiệm lớn cùng thời lượng pin tốt nhất dòng iPhone, chip A18 tối ưu chơi game đồ họa cao.', 10, 'ip16.webp', 'active'),

-- Nhóm 2: Laptop (id loại = 2)
(2, 'MacBook Pro M5 Max', 54990000, 'Siêu chip M5 Max thế hệ mới, màn hình Liquid Retina XDR.', 'Dành cho người dùng chuyên nghiệp đồ họa, lập trình và xử lý tác vụ nặng. MacBook Pro M5 Max cân mọi giới hạn hiệu năng với RAM khủng và tản nhiệt tối ưu.', 5, 'mac_m5.webp', 'active'),
(2, 'Dell XPS 13 Plus 9320', 38990000, 'Vỏ nhôm nguyên khối, hàng phím chức năng cảm ứng độc đáo.', 'Thiết kế tràn viền thời thượng, touchpad ẩn sang trọng và bộ vi xử lý Intel Core i7 thế hệ mới mạnh mẽ.', 6, 'dell_xps.webp', 'active'),
(2, 'ThinkPad X1 Carbon Gen 13', 45990000, 'Chất liệu sợi carbon siêu nhẹ siêu bền, bàn phím gõ đỉnh cao.', 'Lựa chọn số một cho doanh nhân và lập trình viên. ThinkPad X1 Carbon nổi tiếng với độ bền chuẩn quân đội và bàn phím huyền thoại êm ái.', 4, 'thinkpad_gen13.webp', 'active'),
(2, 'MacBook Air M2 13"', 24990000, 'Thiết kế vuông vắn siêu mỏng nhẹ, pin lên tới 18 giờ.', 'MacBook Air M2 mang lại sự cân bằng hoàn hảo giữa hiệu năng mượt mà và tính cơ động cực cao, phù hợp học sinh, sinh viên và văn phòng.', 11, 'mac_m5.webp', 'active'),
(2, 'Dell Inspiron 14 Core i5', 16990000, 'Vỏ kim loại bền bỉ, hiệu năng văn phòng mượt mà.', 'Dell Inspiron 14 trang bị màn hình FHD+ sắc nét, chip Core i5 đời mới đáp ứng tốt mọi công việc học tập và giải trí cơ bản.', 9, 'dell_xps.webp', 'active'),

-- Nhóm 3: Phụ kiện (id loại = 3)
(3, 'Cáp sạc nhanh USB-C bọc dù 60W', 190000, 'Dây bọc dù chống đứt, hỗ trợ sạc nhanh PD.', 'Cáp sạc nhanh USB-C sang USB-C hỗ trợ truyền dữ liệu tốc độ cao, chất liệu sợi nylon bọc dù siêu bền bỉ chống rối gập.', 100, 'day_sac.webp', 'active'),
(3, 'Củ sạc nhanh Hades GaN 65W', 390000, 'Công nghệ GaN sạc siêu mát, 3 cổng ra tiện lợi.', 'Củ sạc sử dụng vật liệu bán dẫn Gallium Nitride giúp thu nhỏ kích thước tối đa, sạc nhanh đồng thời cho cả Laptop, iPad và điện thoại.', 80, 'day_sac.webp', 'active'),
(3, 'Tai nghe True Wireless Hades Pro', 1290000, 'Chống ồn chủ động ANC, chống nước IPX4.', 'Tai nghe bluetooth không dây thiết kế công thái học ôm tai, chất âm bass sâu, pin kết hợp hộp sạc dùng đến 24 giờ liên tục.', 45, 'tai_nghe.webp', 'active'),
(3, 'Tai nghe chụp tai Gaming Pro', 850000, 'Âm thanh vòm 7.1, mic khử tiếng ồn tiện lợi.', 'Tai nghe Over-ear đệm da êm ái, cách âm tuyệt đối, mang lại trải nghiệm âm thanh chiến game 3D chân thực.', 30, 'tai_nghe.webp', 'active'),
(3, 'Sạc dự phòng Hades 20000mAh', 590000, 'Dung lượng khủng sạc nhiều lần, sạc nhanh 22.5W.', 'Tích hợp màn hình LED hiển thị % pin trực quan, hỗ trợ sạc nhanh đa thiết bị cùng lúc cực kỳ an toàn.', 60, 'day_sac.webp', 'active'),

-- Nhóm 4: Smartwatch (id loại = 4)
(4, 'Apple Watch Ultra 3', 21990000, 'Đồng hồ thám hiểm chuyên nghiệp, màn hình độ sáng 3000 nits.', 'Màn hình Sapphire chống trầy, vỏ Titan chuẩn quân đội, GPS tần số kép cực kỳ chính xác cho các hoạt động ngoài trời dã ngoại.', 7, 'smartwatch_apple.png', 'active'),
(4, 'Apple Watch Series 9', 9990000, 'Chip S9 SiP thông minh, tính năng Double Tap chạm đúp.', 'Theo dõi sức khỏe nâng cao: điện tâm đồ ECG, nồng độ oxy trong máu SpO2, đo nhiệt độ cơ thể hỗ trợ dự báo chu kỳ sức khỏe.', 14, 'smartwatch_apple.png', 'active'),
(4, 'Smartwatch Hades Active 2', 1990000, 'Đo nhịp tim 24/7, pin 14 ngày bền bỉ.', 'Đồng hồ thông minh thể thao hỗ trợ hơn 100 chế độ luyện tập, kháng nước 5ATM thoải mái đi bơi.', 25, 'smartwatch_apple.png', 'active'),
(4, 'Apple Watch SE 2024', 5990000, 'Giá trị vượt trội, tính năng phát hiện đụng xe khẩn cấp.', 'Trang bị các tính năng theo dõi sức khỏe cơ bản, thiết kế thể thao trẻ trung, lựa chọn hoàn hảo cho các bạn trẻ và gia đình.', 18, 'smartwatch_apple.png', 'active'),

-- Nhóm 5: Đồng hồ (id loại = 5)
(5, 'Đồng hồ Casio Edifice EFR', 3450000, 'Thiết kế kim lịch lãm, chống nước độ sâu 100 mét.', 'Dòng đồng hồ Casio cao cấp cho nam, vỏ thép không gỉ nguyên khối, mặt kính khoáng chống va đập cực tốt.', 10, 'watch_casio.png', 'active'),
(5, 'Casio G-Shock G-Steel Tough Solar', 4990000, 'Sử dụng pin năng lượng mặt trời, tự động đồng bộ giờ.', 'Chống va đập cực đỉnh, tích hợp đèn LED đôi siêu sáng, kết nối bluetooth thông minh với điện thoại.', 6, 'watch_casio.png', 'active'),
(5, 'Đồng hồ Casio Classic MTP Dây Da', 950000, 'Phong cách tối giản classic cổ điển, dây da bò thật.', 'Đồng hồ đeo tay thanh lịch cho nam nữ, phù hợp đi học, đi làm công sở, thời gian pin dùng đến 3 năm.', 20, 'watch_casio.png', 'active'),
(5, 'G-Shock GA-2100 "CasiOak"', 3120000, 'Mặt bát giác cá tính, thiết kế mỏng nhẹ hiện đại.', 'Vỏ bảo vệ cấu trúc carbon bền nhẹ, thiết kế sang trọng pha nét thể thao bụi bặm bán chạy số 1 toàn cầu.', 15, 'watch_casio.png', 'active'),

-- Nhóm 6: Máy cũ (id loại = 6)
(6, 'iPhone 15 Pro 128GB Cũ Đẹp', 21490000, 'Máy cũ đẹp 99% likenew như mới, bảo hành 6 tháng.', 'Cam kết zin nguyên bản 100%, chưa qua sửa chữa thay thế linh kiện, pin trên 90%, đầy đủ phụ kiện sạc cáp.', 5, 'ip15.webp', 'active'),
(6, 'MacBook Air M1 Cũ Likenew', 13990000, 'Lựa chọn giá rẻ tối ưu nhất của Apple, máy dùng lướt.', 'Ngoại hình đẹp keng không cấn móp, pin sạc ít lần còn rất trâu, bảo hành phần cứng 1 đổi 1 trong 30 ngày.', 4, 'mac_m5.webp', 'active'),
(6, 'Dell XPS 13 Cũ Giá Tốt', 18900000, 'Laptop văn phòng sang chảnh, độ bền siêu ổn định.', 'Dell XPS cũ qua sử dụng, vỏ nhôm còn rất đẹp, màn hình IPS siêu sáng, hoạt động trơn tru mát mẻ.', 3, 'dell_xps.webp', 'active'),
(6, 'iPad Air 5 M1 Cũ 64GB WiFi', 11490000, 'Hiệu năng chip M1 cực khỏe, máy nguyên bản.', 'iPad Air 5 cũ phục vụ vẽ đồ họa, ghi chú học tập mượt mà, cảm biến vân tay nhạy bén.', 5, 'tablet_ipad.png', 'active'),

-- Nhóm 7: Tablet (id loại = 7)
(7, 'iPad Pro M4 11 inch 256GB', 25990000, 'iPad mỏng nhất lịch sử, chip M4 siêu khủng, màn hình OLED.', 'iPad Pro cao cấp nhất của Apple với màn hình Tandem OLED rực rỡ, chip M4 tối ưu hóa hiệu năng làm việc đồ họa 3D.', 6, 'tablet_ipad.png', 'active'),
(7, 'iPad Air 6 M2 2026', 16490000, 'Sức mạnh từ chip M2 mượt mà, hỗ trợ Apple Pencil Pro.', 'iPad Air thế hệ mới nâng cấp cấu hình vượt trội đáp ứng tốt mọi công việc đa nhiệm học tập của bạn.', 10, 'tablet_ipad.png', 'active'),
(7, 'iPad Gen 10 64GB WiFi', 9490000, 'Thiết kế thời trang nhiều màu sắc trẻ trung, cổng USB-C.', 'iPad tiêu chuẩn màn hình 10.9 inch sắc nét Liquid Retina, nút nguồn tích hợp Touch ID, hỗ trợ bàn phím Magic Keyboard Folio.', 16, 'tablet_ipad.png', 'active');
