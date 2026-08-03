#!/bin/bash
# Shop Hades - Script sao lưu dữ liệu tự động hàng ngày
# Có thể cài đặt cronjob chạy hàng ngày: 0 2 * * * /bin/bash /path/to/project/scripts/db-backup.sh

# Cấu hình đường dẫn
BASE_PATH=$(dirname $(dirname $(readlink -f $0)))
BACKUP_DIR="$BASE_PATH/storage/backups"
DATE=$(date +%Y-%m-%d_%H%M%S)

# Đọc cấu hình database từ .env
DB_HOST=$(grep DB_HOST "$BASE_PATH/.env" | cut -d '=' -f2 | tr -d '"' | tr -d "'")
DB_NAME=$(grep DB_DATABASE "$BASE_PATH/.env" | cut -d '=' -f2 | tr -d '"' | tr -d "'")
DB_USER=$(grep DB_USERNAME "$BASE_PATH/.env" | cut -d '=' -f2 | tr -d '"' | tr -d "'")
DB_PASS=$(grep DB_PASSWORD "$BASE_PATH/.env" | cut -d '=' -f2 | tr -d '"' | tr -d "'")

# Mặc định nếu rỗng
DB_HOST=${DB_HOST:-"127.0.0.1"}
DB_NAME=${DB_NAME:-"shop_hades"}
DB_USER=${DB_USER:-"root"}

# Tạo thư mục lưu trữ nếu chưa có
mkdir -p "$BACKUP_DIR"

# File backup path
FILE_PATH="$BACKUP_DIR/db_backup_$DATE.sql"

# Tiến hành dump dữ liệu
if [ -z "$DB_PASS" ]; then
    mysqldump -h "$DB_HOST" -u "$DB_USER" "$DB_NAME" > "$FILE_PATH" 2>/dev/null
else
    mysqldump -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" > "$FILE_PATH" 2>/dev/null
fi

if [ $? -eq 0 ]; then
    echo "✅ Sao lưu thành công: $FILE_PATH"
    # Nén file sql thành zip để tiết kiệm dung lượng
    gzip "$FILE_PATH"
    echo "📦 Nén thành công: $FILE_PATH.gz"
else
    echo "❌ Lỗi: Không thể sao lưu database."
    exit 1
fi

# Tự động dọn dẹp các file sao lưu cũ hơn 7 ngày
find "$BACKUP_DIR" -type f -name "db_backup_*.sql.gz" -mtime +7 -delete
echo "🧹 Đã dọn dẹp các file sao lưu cũ hơn 7 ngày."
