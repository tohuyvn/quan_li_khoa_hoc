# Shop Hades — Public web qua Docker & Cloudflare

Hướng dẫn chạy Shop Hades trên Docker và mở ra internet qua Cloudflare Tunnel (demo `*.trycloudflare.com` hoặc domain riêng).

**Project:** `/home/huytq/Documents/btvn/btvn`

---

## Yêu cầu

| Công cụ | Mục đích |
|---------|----------|
| Docker + Docker Compose | Chạy PHP-FPM, Nginx, MySQL |
| `cloudflared` | Cloudflare Tunnel (HTTPS public) |

### Cài đặt (một lần)

```bash
# Docker
sudo apt update && sudo apt install -y docker.io docker-compose-plugin
sudo usermod -aG docker $USER   # logout/login lại

# Cloudflare Tunnel CLI
curl -L https://github.com/cloudflare/cloudflared/releases/latest/download/cloudflared-linux-amd64.deb -o cloudflared.deb
sudo dpkg -i cloudflared.deb
```

---

## Kiến trúc

```
Internet (HTTPS)
    ↓
Cloudflare Tunnel (cloudflared)
    ↓
http://127.0.0.1:8000  ←  Nginx container (shop_hades_web)
    ↓
PHP-FPM container (shop_hades_app)
    ↓
MySQL container (shop_hades_db)
```

- **Port local:** `8000` (map `8000:80` trong `docker-compose.yml`)
- **File cấu hình:** `docker-compose.yml`, `docker/nginx.conf`, `.env`

---

## Cách 1 — Docker + Cloudflare Tunnel (demo / test)

Phù hợp chia link tạm `https://xxxx.trycloudflare.com` (MoMo IPN, OTP, PWA HTTPS).

### 1. Cấu hình `.env`

```bash
cd /home/huytq/Documents/btvn/btvn
cp .env.example .env
# hoặc: cp .env.production .env
```

Chỉnh các biến sau (phải khớp `docker-compose.yml`):

```env
APP_DEBUG=false
APP_BASE_URL=http://127.0.0.1:8000

DB_HOST=db
DB_PORT=3306
DB_DATABASE=shop_hades
DB_USERNAME=hades_user
DB_PASSWORD=hades_password
```

### 2. Khởi động Docker

```bash
cd /home/huytq/Documents/btvn/btvn

docker compose up -d --build

# Chờ MySQL sẵn sàng (~15–30 giây), rồi migrate
docker compose exec app php scripts/migrate.php
```

Kiểm tra local: [http://127.0.0.1:8000](http://127.0.0.1:8000)

### 3. Mở public qua Cloudflare (terminal khác)

```bash
cloudflared tunnel --url http://127.0.0.1:8000
```

Copy URL hiện ra, ví dụ: `https://abcd-xyz.trycloudflare.com`

### 4. Cập nhật `.env` theo URL public

```bash
cd /home/huytq/Documents/btvn/btvn

CF_URL="https://abcd-xyz.trycloudflare.com"   # thay bằng URL thật

sed -i "s|^APP_BASE_URL=.*|APP_BASE_URL=$CF_URL|" .env
sed -i "s|^VNPAY_RETURN_URL=.*|VNPAY_RETURN_URL=$CF_URL/payment/vnpay/return|" .env
sed -i "s|^MOMO_REDIRECT_URL=.*|MOMO_REDIRECT_URL=$CF_URL/payment/momo/return|" .env
sed -i "s|^MOMO_IPN_URL=.*|MOMO_IPN_URL=$CF_URL/payment/momo/callback|" .env
```

PHP đọc `.env` mỗi request — **không cần restart** container (trừ khi đổi biến trong `docker-compose.yml`).

### 5. Truy cập

| Mục | URL |
|-----|-----|
| Trang chủ | `https://abcd-xyz.trycloudflare.com/` |
| Đăng nhập | `.../login` |
| Admin | `.../admin/login` |

---

## Cách 2 — XAMPP + Cloudflare (không Docker)

Project có script tự động:

```bash
cd /home/huytq/Documents/btvn/btvn
chmod +x start-cloudflare.sh
./start-cloudflare.sh
```

Script tự:

1. Chạy PHP built-in server port `8000`
2. Bật Cloudflare Tunnel
3. Cập nhật `APP_BASE_URL`, MoMo, VNPay trong `.env`
4. Khi `Ctrl+C` — khôi phục `.env` từ `.env.bak` (nếu có)

---

## Cách 3 — Production (domain riêng trên Cloudflare)

Khi đã có domain (vd. `shop.hades.vn`) và DNS quản lý bởi Cloudflare.

### 1. Docker chạy nền

```bash
cd /home/huytq/Documents/btvn/btvn
cp .env.production .env
# Sửa APP_BASE_URL, SMTP, MoMo, VNPay trong .env

docker compose up -d --build
docker compose exec app php scripts/migrate.php
```

### 2. Tạo tunnel cố định

```bash
cloudflared tunnel login
cloudflared tunnel create shop-hades
cloudflared tunnel route dns shop-hades shop.hades.vn
```

### 3. File `~/.cloudflared/config.yml`

```yaml
tunnel: <TUNNEL_ID>
credentials-file: /home/<user>/.cloudflared/<TUNNEL_ID>.json

ingress:
  - hostname: shop.hades.vn
    service: http://127.0.0.1:8000
  - service: http_status:404
```

### 4. Chạy tunnel

```bash
# Chạy thử
cloudflared tunnel run shop-hades

# Hoặc cài service (khởi động cùng máy)
sudo cloudflared service install
sudo systemctl start cloudflared
```

### 5. `.env` production

```env
APP_DEBUG=false
APP_BASE_URL=https://shop.hades.vn

VNPAY_RETURN_URL=https://shop.hades.vn/payment/vnpay/return
MOMO_REDIRECT_URL=https://shop.hades.vn/payment/momo/return
MOMO_IPN_URL=https://shop.hades.vn/payment/momo/callback
```

---

## Lệnh Docker thường dùng

| Việc | Lệnh |
|------|------|
| Khởi động | `docker compose up -d --build` |
| Xem log | `docker compose logs -f` |
| Log Nginx | `docker compose logs -f web` |
| Log PHP | `docker compose logs -f app` |
| Dừng | `docker compose down` |
| Dừng + xóa volume DB | `docker compose down -v` |
| Migrate DB | `docker compose exec app php scripts/migrate.php` |
| Seed dữ liệu mẫu | `docker compose exec app php scripts/migrate.php` *(gồm seed)* |
| Shell trong app | `docker compose exec app sh` |
| MySQL CLI | `docker compose exec db mysql -u hades_user -phades_password shop_hades` |

---

## Tóm tắt nhanh (copy-paste)

```bash
cd /home/huytq/Documents/btvn/btvn
docker compose up -d --build
docker compose exec app php scripts/migrate.php
cloudflared tunnel --url http://127.0.0.1:8000
# → Copy URL trycloudflare → cập nhật .env (APP_BASE_URL, MoMo, VNPay)
```

---

## Lưu ý quan trọng

1. **URL `trycloudflare.com` đổi mỗi lần tắt tunnel** — phải cập nhật lại `.env` (đặc biệt `MOMO_IPN_URL`).
2. **PWA / Tải app / Thêm vào màn hình** cần **HTTPS** — Cloudflare Tunnel đáp ứng được.
3. **`DB_HOST=db`** khi chạy Docker; dùng `127.0.0.1` khi chạy XAMPP local.
4. **Migration** chỉ chạy lần đầu hoặc khi đổi schema (`storage/.schema_version`). Seed chỉ qua `scripts/migrate.php`, không chạy mỗi request.
5. **MoMo IPN** bắt buộc URL public HTTPS — tunnel phải chạy liên tục khi test thanh toán.
6. Mở port `3306` ra ngoài (trong compose) chỉ nên dùng dev — production nên bỏ `ports` của service `db`.

---

## Xử lý lỗi

| Triệu chứng | Cách xử lý |
|-------------|------------|
| `502 Bad Gateway` | `docker compose logs app` — chờ MySQL hoặc chạy migrate |
| Trang trắng | `APP_DEBUG=true` tạm trong `.env`, xem log PHP |
| MoMo không callback | Kiểm tra `MOMO_IPN_URL` khớp URL tunnel hiện tại |
| Tunnel không có URL | Xem `storage/logs/cloudflared.log` hoặc output terminal |
| Permission storage | `docker compose exec app chown -R www-data:www-data storage` |

---

## File liên quan trong repo

| File | Mô tả |
|------|--------|
| `docker-compose.yml` | PHP-FPM + Nginx + MySQL |
| `Dockerfile` | Image PHP 8.2 |
| `docker/nginx.conf` | Cấu hình Nginx |
| `.env.example` / `.env.production` | Mẫu biến môi trường |
| `start.sh` | Chạy local XAMPP (không Docker) |
| `start-cloudflare.sh` | XAMPP + Cloudflare Tunnel tự động |
| `scripts/migrate.php` | Migration + seed |
