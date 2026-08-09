# 🚀 Shop Hades — Roadmap Phát triển

> Cập nhật lần cuối: 2026-07-30  
> Phiên bản hiện tại: `v1.4.0`

---

## ✅ Đã hoàn thành (v1.0 → v1.3)

### 🛒 Core E-Commerce
- [x] Trang chủ — Danh sách sản phẩm, lọc theo danh mục, tìm kiếm real-time (AJAX)
- [x] Trang chi tiết sản phẩm — Chọn biến thể (màu sắc / dung lượng)
- [x] Giỏ hàng AJAX (thêm / xóa / cập nhật số lượng không reload trang)
- [x] Hệ thống Stepper số lượng `[- SỐ +]` cập nhật tổng tiền tức thì
- [x] Hiệu ứng icon giỏ hàng bay từ nút → Header badge
- [x] Thanh toán (Checkout) — Địa chỉ giao hàng, gợi ý địa chỉ qua Goong API
- [x] Tích hợp thanh toán **VNPay** (sandbox)
- [x] Tích hợp thanh toán **MoMo** (sandbox)
- [x] Tích hợp thanh toán **VietQR / Chuyển khoản ngân hàng**
- [x] So sánh sản phẩm (tối đa 3 sản phẩm, floating drawer)
- [x] Tra cứu đơn hàng theo mã đơn (không cần đăng nhập)

### 👤 Xác thực & Tài khoản
- [x] Đăng ký / Đăng nhập bằng email + mật khẩu
- [x] Xác thực OTP qua Gmail (6 số, hết hạn 5 phút)
- [x] Đăng nhập Google OAuth 2.0 — Người dùng mới nhận OTP trước khi tạo tài khoản
- [x] Quên mật khẩu — Đặt lại qua OTP email
- [x] Hồ sơ cá nhân — Đổi thông tin, ảnh đại diện, đổi mật khẩu
- [x] Ví điện tử nội bộ (UserWallet)

### 🏪 Quản trị Admin
- [x] Dashboard tổng quan doanh thu, đơn hàng
- [x] Quản lý sản phẩm — CRUD, biến thể, hình ảnh, giá, khuyến mãi
- [x] Quản lý danh mục
- [x] Quản lý đơn hàng — Duyệt, cập nhật trạng thái, in hóa đơn
- [x] Quản lý người dùng
- [x] Quản lý khuyến mãi (SitePromotion)
- [x] Quản lý kho hàng (Inventory / DeviceUnit)
- [x] Quản lý cơ sở (Store / Chi nhánh)
- [x] Quản lý Bảo hành
- [x] Quản lý Thu cũ đổi mới (Trade-In)
- [x] Quản lý hoàn tiền (Refund)

### 🎨 UI / UX
- [x] Dark mode (toàn bộ giao diện)
- [x] Responsive Mobile-first (Bootstrap 5.3)
- [x] Mobile bottom navigation bar
- [x] Toast notification system (success / error / info / warning)
- [x] Global click micro-animation (press scale effect)
- [x] Floating chatbot AI (Gemini 3.5 Flash)
- [x] Chat trực tuyến (Support real-time tab)
- [x] Floating promo badge, Service box (Kéo được)
- [x] Bản đồ chi nhánh Leaflet.js (lazy loaded)
- [x] PWA — Cài ứng dụng từ trình duyệt
- [x] Skeleton loading cho gợi ý tìm kiếm

---

## ✅ Đã hoàn thành trong v1.4

- [x] **Review & Rating ⭐** — Chỉ tài khoản có đơn hoàn thành được đánh giá, gắn nhãn “Đã mua hàng”
- [x] **Áp dụng Voucher ở Checkout** — Validate qua AJAX và kiểm tra lại ở server khi đặt hàng
- [x] **Wishlist — Sản phẩm yêu thích** — Lưu DB cho thành viên, localStorage cho khách và tự đồng bộ sau đăng nhập
- [x] **Email xác nhận đơn hàng tự động** — Gửi sau khi đơn hàng được tạo/thanh toán
- [x] **Tab chi tiết sản phẩm** — Mô tả / Thông số kỹ thuật / Đánh giá

## ⏭️ Hạng mục tiếp theo

- [ ] **Sản phẩm liên quan** — Gợi ý cùng danh mục và lịch sử xem ở trang chi tiết

---

## 🗓️ Kế hoạch tiếp theo

### 🎯 v1.4 — Tương tác & Chuyển đổi (Dự kiến: Tuần tới)

> **Mục tiêu**: Tăng tỷ lệ chuyển đổi và sự gắn kết của khách hàng

| # | Tính năng | Độ ưu tiên | Trạng thái |
|---|---|---|---|
| 1 | ⭐ **Review & Rating sản phẩm** | 🔴 Rất cao | ✅ Hoàn thành |
| 2 | 🏷️ **Áp dụng Voucher / Mã giảm giá** tại Checkout | 🔴 Rất cao | ✅ Hoàn thành |
| 3 | ❤️ **Wishlist — Sản phẩm yêu thích** | 🔴 Rất cao | ✅ Hoàn thành |
| 4 | 📧 **Email xác nhận đơn hàng tự động** | 🟠 Cao | ✅ Hoàn thành |
| 5 | 📦 **Tab trang chi tiết sản phẩm** | 🟠 Cao | ✅ Hoàn thành |

---

## ✅ Đã hoàn thành trong v1.5

- [x] **🔗 Sản phẩm liên quan (Related Products)** — Gợi ý tự động các sản phẩm cùng danh mục ở trang chi tiết
- [x] **👁️ Sản phẩm đã xem gần đây (Recently Viewed)** — Tự động lưu và gợi ý danh sách sản phẩm vừa duyệt
- [x] **🔍 SEO chuẩn Schema.org & OpenGraph** — Tích hợp JSON-LD Product Schema cho Google Rich Snippets & OpenGraph Meta tags
- [x] **🎨 Trang Error 404 nâng cấp** — Giao diện 404 đẹp hiện đại tích hợp ô tìm kiếm real-time và gợi ý sản phẩm HOT
- [x] **📦 Tab Lịch sử đơn hàng trong Profile** — Tích hợp quản lý & theo dõi đơn hàng trực tiếp ngay trong Hồ sơ cá nhân

---

### 🎯 v1.5 — Nội dung & Khám phá

> **Mục tiêu**: Cải thiện SEO, trải nghiệm duyệt sản phẩm và tìm kiếm

| # | Tính năng | Độ ưu tiên | Trạng thái / Ghi chú |
|---|---|---|---|
| 6 | 🔗 **Sản phẩm liên quan** (Related Products) | 🟠 Cao | ✅ Hoàn thành — Gợi ý cùng danh mục ở trang chi tiết |
| 7 | 🖼️ **Gallery ảnh sản phẩm nhiều hình + Zoom** | 🟡 Trung bình | ✅ Hoàn thành — Gallery thumbnail đổi màu & xem ảnh |
| 8 | 📄 **Phân trang AJAX mượt** khi lọc sản phẩm | 🟡 Trung bình | ✅ Hoàn thành |
| 9 | 👁️ **Xem gần đây** (Recently Viewed) | 🟡 Trung bình | ✅ Hoàn thành — Lưu session & hiển thị trang chi tiết |
| 10 | 🔍 **SEO chuẩn** cho từng trang sản phẩm | 🟡 Trung bình | ✅ Hoàn thành — OpenGraph, Schema.org JSON-LD |

---

### 🎯 v1.6 — Marketing & Giữ chân khách hàng (Dự kiến: 1 tháng)

> **Mục tiêu**: Tăng doanh thu qua khuyến mãi, gamification và thông báo

| # | Tính năng | Độ ưu tiên | Ghi chú |
|---|---|---|---|
| 11 | ⏱️ **Flash Sale / Đếm ngược khuyến mãi** | 🟠 Cao | Countdown timer real-time |
| 12 | 🎁 **Chương trình điểm thưởng / Loyalty** | 🟡 Trung bình | Tích điểm theo đơn hàng, đổi điểm lấy voucher |
| 13 | 📬 **Push Notification (PWA)** | 🟡 Trung bình | Thông báo đơn hàng, khuyến mãi mới |
| 14 | 📊 **Dashboard Admin nâng cao** | 🟡 Trung bình | Biểu đồ doanh thu theo ngày/tuần/tháng, Chart.js |
| 15 | 💬 **Hệ thống Q&A sản phẩm** | 🟢 Thấp | Khách hỏi, admin/người đã mua trả lời |

---

### 🎯 v2.0 — Mở rộng quy mô (Dài hạn)

> **Mục tiêu**: Biến Shop Hades thành nền tảng thương mại điện tử đầy đủ

| # | Tính năng | Ghi chú |
|---|---|---|
| 16 | 🚚 **Tích hợp đơn vị vận chuyển** (GHN / GHTK) | Tính phí ship real-time, theo dõi vận chuyển |
| 17 | 🛍️ **Multi-seller / Gian hàng** | Cho phép nhiều người bán cùng trên 1 platform |
| 18 | 📱 **Mobile App** (React Native / Flutter) | Dựng API RESTful cho app mobile |
| 19 | 🤖 **AI Gợi ý sản phẩm** thông minh | Dựa trên lịch sử mua hàng, hành vi duyệt |
| 20 | 🌍 **Đa ngôn ngữ** (i18n — VI / EN) | |

---

## 🐛 Backlog — Cần sửa / Cải thiện

| # | Vấn đề | Ưu tiên | Trạng thái |
|---|---|---|---|
| B1 | Trang 404 đẹp hơn, tích hợp ô tìm kiếm real-time | 🟡 | ✅ Hoàn thành |
| B2 | Loading skeleton cho danh sách sản phẩm khi lọc AJAX | 🟡 | ✅ Hoàn thành |
| B3 | Trang Profile → Thêm tab "Lịch sử đơn hàng" ngay trong Profile | 🟡 | ✅ Hoàn thành |
| B4 | Email OTP vào tab Spam/Promotions → Cải thiện email template | 🟡 | |
| B5 | Admin: Thống kê sản phẩm bán chạy nhất | 🟢 | |

---

## 📐 Tech Stack

```
Backend:  PHP 8.2 (MVC tự viết, không dùng framework)
Frontend: HTML5 + Vanilla CSS + Bootstrap 5.3
Database: MySQL 8.0
Mail:     Gmail SMTP (PHPMailer)
AI:       Google Gemini 3.5 Flash API
Payment:  VNPay / MoMo / VietQR
Maps:     Leaflet.js + OpenStreetMap + Goong Geocoding API
Auth:     Google OAuth 2.0 + OTP
PWA:      Service Worker + Web App Manifest
Deploy:   Cloudflare Tunnel (dev) → VPS (prod)
```

---

## 📊 Metrics mục tiêu khi ra production

| Metric | Mục tiêu |
|---|---|
| Page Load Time | < 2 giây |
| Core Web Vitals (LCP) | < 2.5s |
| Lighthouse Score | > 85 |
| Uptime | > 99.5% |
| Mobile Responsive | 100% |

---

*Roadmap này sẽ được cập nhật theo từng sprint phát triển.*
