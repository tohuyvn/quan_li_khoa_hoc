# Shop Hades — Hướng dẫn Cấu hình & Khởi chạy (Local & Public)

Chào mừng bạn đến với **Shop Hades** – Hệ thống thương mại điện tử chuyên nghiệp được phát triển trên nền tảng PHP thuần tối ưu hóa SEO và bảo mật.

---

## 📌 Lưu Ý Quan Trọng Trước Khi Chạy
Để tránh các lỗi thường gặp, vui lòng đảm bảo:
1. **Thư mục làm việc (CWD):** Luôn mở Terminal tại thư mục gốc của dự án:
   ```bash
   cd /home/huytq/Documents/btvn/btvn
   ```
2. **Không chạy lệnh `php -S` đơn luồng thủ công:** 
   Hệ thống sử dụng cơ chế Server-Sent Events (SSE) để duy trì chat hỗ trợ trực tuyến và theo dõi đơn hàng thời gian thực. Nếu chạy đơn luồng, cả website sẽ bị **lag/treo** hoàn toàn. Luôn chạy qua các script được viết sẵn dưới đây.

---

## 🛠️ Cấu Hình Ban Đầu

1. **Khởi tạo file môi trường `.env`:**
   Nếu chưa có file `.env`, tạo mới từ file template:
   ```bash
   cp .env.example .env
   ```
   *Cấu hình thông tin kết nối Cơ sở dữ liệu (`DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`) trong `.env` cho khớp với hệ thống của bạn.*

2. **Cài đặt thư viện dependencies:**
   ```bash
   composer install
   ```

---

## 🚀 1. Khởi Chạy Local (Không Bị Lag)

Chúng ta sử dụng XAMPP PHP tích hợp sẵn đầy đủ các extension để khởi chạy server local kèm theo cấu hình **10 luồng xử lý đồng thời (`PHP_CLI_SERVER_WORKERS=10`)** để ngăn hiện tượng nghẽn do kết nối SSE:

```bash
# Phân quyền thực thi nếu cần
chmod +x start.sh

# Khởi chạy server local
./start.sh
```
* **Địa chỉ truy cập:** [http://127.0.0.1:8000/](http://127.0.0.1:8000/)
* **Trang Admin:** [http://127.0.0.1:8000/admin/login](http://127.0.0.1:8000/admin/login)

---

## 🌐 2. Khởi Chạy Public (Dùng Cho Thanh Toán MoMo / VNPay Webhook)

Để kiểm thử chức năng thanh toán trực tuyến (Momo Sandbox, VNPay) hoặc nhận phản hồi kết quả giao dịch (IPN/Callback), server local cần phải được đưa ra môi trường Internet công cộng (Public URL). Dự án đã tích hợp sẵn 3 phương án tự động cập nhật cấu hình cấu hình URL vào file `.env`:

### Phương án A: Sử dụng Cloudflare Tunnel (Khuyên dùng - Ổn định & Miễn phí)
*Yêu cầu máy đã cài đặt tiện ích `cloudflared`.*
```bash
chmod +x start-cloudflare.sh
./start-cloudflare.sh
```
* **Cách hoạt động:** Script sẽ tự tạo một tunnel ngẫu nhiên (ví dụ: `https://xxxx.trycloudflare.com`), tự động backup file `.env` và cập nhật các cấu hình callback URL MoMo/VNPay thành link public này. Khi bạn nhấn `Ctrl + C` để tắt, script sẽ tự động khôi phục lại file `.env` ban đầu.

### Phương án B: Sử dụng ngrok (Có Domain Cố Định)
*Yêu cầu máy đã cài đặt `ngrok`.*
```bash
chmod +x start-public.sh
./start-public.sh
```
* **Địa chỉ public:** Sử dụng domain được cấu hình cố định trong script: `https://dividend-stack-trustee.ngrok-free.dev/`

### Phương án C: Sử dụng localhost.run (Không cần cài đặt thêm)
*Chỉ yêu cầu Terminal hỗ trợ kết nối SSH.*
```bash
chmod +x start-localhost-run.sh
./start-localhost-run.sh
```
* **Cách hoạt động:** Kết nối tunnel thông qua SSH port forwarding trực tiếp từ localhost.run. Nhấn `yes` nếu SSH hỏi xác nhận vân tay máy chủ lần đầu.

---

## 🐳 3. Khởi Chạy & Public Bằng Docker (Tự động & Tối ưu)

Hệ thống được cấu hình sẵn môi trường Docker với PHP-FPM, Nginx, MySQL và tự động tích hợp sẵn **Cloudflare Tunnel** giúp public website ra internet chỉ bằng một lệnh duy nhất mà không cần cài đặt gì thêm ở máy host.

### Bước 0: Cài Docker trên Ubuntu 24.04 (nếu máy chưa có)

Nếu Terminal báo `Command 'docker' not found`, cài Docker Engine và Docker Compose Plugin từ kho chính thức của Docker:

```bash
sudo apt update
sudo apt install -y ca-certificates curl

sudo install -m 0755 -d /etc/apt/keyrings
sudo curl -fsSL https://download.docker.com/linux/ubuntu/gpg \
  -o /etc/apt/keyrings/docker.asc
sudo chmod a+r /etc/apt/keyrings/docker.asc

echo "Types: deb
URIs: https://download.docker.com/linux/ubuntu
Suites: noble
Components: stable
Architectures: $(dpkg --print-architecture)
Signed-By: /etc/apt/keyrings/docker.asc" \
| sudo tee /etc/apt/sources.list.d/docker.sources

sudo apt update
sudo apt install -y docker-ce docker-ce-cli containerd.io \
  docker-buildx-plugin docker-compose-plugin
```

Kiểm tra sau khi cài:

```bash
sudo docker --version
sudo docker compose version
```

Xem thêm: [Hướng dẫn cài Docker Engine trên Ubuntu](https://docs.docker.com/engine/install/ubuntu/).

### Chạy nhanh mỗi lần sử dụng

Sau khi đã cài Docker và khởi tạo dự án lần đầu, mỗi lần muốn chạy website local kèm link public, chỉ cần:

```bash
cd /home/huytq/Documents/btvn/btvn
sudo docker compose up -d
```

Truy cập website local tại [http://127.0.0.1:8000/](http://127.0.0.1:8000/).

Cloudflare Tunnel được chạy tự động cùng hệ thống. Lấy URL public bằng lệnh:

```bash
sudo docker compose logs tunnel \
  | grep -Eo 'https://[-a-z0-9]+\.trycloudflare\.com' \
  | tail -n 1
```

Kết quả sẽ là URL có dạng `https://xxxx.trycloudflare.com`. URL này có thể thay đổi sau mỗi lần tắt và chạy lại tunnel; nếu sử dụng MoMo/VNPay, hãy cập nhật URL mới trong `.env`.

> **Lưu ý:** Khi `sudo` yêu cầu mật khẩu, nhập mật khẩu đăng nhập Ubuntu. Terminal sẽ không hiển thị ký tự trong lúc nhập; đây là hành vi bình thường.

Khi không dùng nữa, tắt hệ thống:

```bash
sudo docker compose down
```

Chỉ thêm `--build` khi chạy lần đầu hoặc sau khi thay đổi `Dockerfile`/dependencies:

```bash
sudo docker compose up -d --build
```

Lệnh migration database chỉ cần chạy trong lần khởi tạo đầu tiên:

```bash
sudo docker compose exec app php scripts/migrate.php
```

### Bước 1: Khởi chạy các container Docker
Chạy lệnh sau tại thư mục gốc dự án:
```bash
cd /home/huytq/Documents/btvn/btvn
sudo docker compose up -d --build
```

### Bước 2: Khởi tạo database và dữ liệu mẫu

```bash
sudo docker compose exec app php scripts/migrate.php
```

Nếu MySQL vừa khởi động và chưa sẵn sàng, chờ vài giây rồi chạy lại lệnh trên.

Sau khi migration hoàn tất, truy cập:

* **Trang chủ:** [http://127.0.0.1:8000/](http://127.0.0.1:8000/)
* **Trang Admin:** [http://127.0.0.1:8000/admin/login](http://127.0.0.1:8000/admin/login)
* **Health check:** [http://127.0.0.1:8000/health](http://127.0.0.1:8000/health)

### Bước 3: Lấy URL public để cấu hình
Khi các container đã khởi động, Cloudflare Tunnel sẽ tự động tạo một public URL ngẫu nhiên. Hãy xem log để lấy URL này:
```bash
sudo docker compose logs tunnel
```
Tìm dòng có URL đuôi `.trycloudflare.com` (ví dụ: `https://xxxx.trycloudflare.com`).

### Bước 4: Cập nhật cấu hình `.env`
Mở file `.env` ở máy host và cập nhật biến `APP_BASE_URL` cùng các link callback thanh toán bằng URL vừa lấy được để các dịch vụ MoMo / VNPay có thể gửi phản hồi webhook về máy của bạn:
```env
APP_BASE_URL=https://xxxx.trycloudflare.com
VNPAY_RETURN_URL=https://xxxx.trycloudflare.com/payment/vnpay/return
MOMO_REDIRECT_URL=https://xxxx.trycloudflare.com/payment/momo/return
MOMO_IPN_URL=https://xxxx.trycloudflare.com/payment/momo/callback
```
*(Do thư mục code được mount trực tiếp vào container nên mọi thay đổi ở file `.env` trên máy host sẽ được áp dụng ngay lập tức mà không cần khởi động lại Docker).*

### Bước 5: Tắt hệ thống Docker
Khi không cần sử dụng nữa, bạn có thể tắt và dọn dẹp các tài nguyên Docker bằng lệnh:
```bash
sudo docker compose down
```

---

## 🧪 4. Chạy Kiểm Thử Hệ Thống (Tests)

Để đảm bảo toàn bộ logic đơn hàng, hoàn tiền, ví và voucher hoạt động ổn định trước khi triển khai, hãy chạy bộ kiểm thử PHPUnit bằng PHP của XAMPP:

```bash
/opt/lampp/bin/php vendor/bin/phpunit
```

---

## 🔎 5. Health Check Nhanh

Sau khi chạy web, có thể kiểm tra nhanh tình trạng app và database tại:

```bash
curl http://127.0.0.1:8000/health
```

* `200 OK`: App và DB đang sẵn sàng.
* `503 Service Unavailable`: Web đã khởi động nhưng DB chưa sẵn sàng hoặc cấu hình DB chưa đúng.

---

## 📂 Danh Sách Tài Khoản Thử Nghiệm

Hệ thống được khởi tạo sẵn các tài khoản demo sau (nếu dữ liệu được import từ `shop_hades.sql`):

* **Tài khoản Admin (Quản trị viên):**
  * Email: `admin@shop.test`
  * Mật khẩu: `admin123`
* **Tài khoản Customer (Khách hàng):**
  * Email: `user@shop.test`
  * Mật khẩu: `user123`