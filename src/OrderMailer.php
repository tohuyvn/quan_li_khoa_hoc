<?php

namespace App;

/**
 * OrderMailer – Gửi email xác nhận đơn hàng cho khách.
 * Dùng App\Mailer để gửi qua SMTP Gmail đã cấu hình.
 */
class OrderMailer
{
    /**
     * Gửi email xác nhận đơn hàng.
     *
     * @param array $order   Thông tin đơn hàng (customer_name, email, phone, ...)
     * @param array $items   Danh sách sản phẩm trong đơn
     */
    public static function sendConfirmation(array $order, array $items): bool
    {
        $to      = $order['email'] ?? '';
        $name    = $order['customer_name'] ?? 'Quý khách';
        $orderId = $order['order_code'] ?? $order['id'] ?? '?';
        $total   = number_format((float)($order['total_amount'] ?? 0), 0, ',', '.') . ' đ';
        $method  = self::paymentLabel($order['payment_method'] ?? 'cash');
        $address = $order['shipping_address'] ?? $order['contact_address'] ?? '';

        if (empty($to)) {
            return false;
        }

        // Tạo bảng sản phẩm
        $rows = '';
        foreach ($items as $item) {
            $lineTotal = number_format((float)(($item['price'] ?? 0) * ($item['quantity'] ?? 1)), 0, ',', '.') . ' đ';
            $price     = number_format((float)($item['price'] ?? 0), 0, ',', '.') . ' đ';
            $rows .= <<<HTML
            <tr>
                <td style="padding:10px 12px;border-bottom:1px solid #f0f0f0;">
                    <strong>{$item['name']}</strong>
                </td>
                <td style="padding:10px 12px;border-bottom:1px solid #f0f0f0;text-align:center;">{$item['quantity']}</td>
                <td style="padding:10px 12px;border-bottom:1px solid #f0f0f0;text-align:right;color:#d70018;">{$price}</td>
                <td style="padding:10px 12px;border-bottom:1px solid #f0f0f0;text-align:right;font-weight:bold;">{$lineTotal}</td>
            </tr>
HTML;
        }

        $appName = config('app_name', 'Hades Shop');
        $subject = "✅ Xác nhận đơn hàng #{$orderId} - {$appName}";

        $body = <<<HTML
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Xác nhận đơn hàng</title>
</head>
<body style="margin:0;padding:0;background:#f4f6f9;font-family:'Segoe UI',Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6f9;padding:30px 0;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08);">

  <!-- Header -->
  <tr>
    <td style="background:linear-gradient(135deg,#d70018 0%,#8b0010 100%);padding:32px 40px;text-align:center;">
      <h1 style="margin:0;color:#fff;font-size:26px;letter-spacing:1px;">🛒 {$appName}</h1>
      <p style="margin:8px 0 0;color:rgba(255,255,255,0.85);font-size:14px;">Đơn hàng của bạn đã được xác nhận!</p>
    </td>
  </tr>

  <!-- Greeting -->
  <tr>
    <td style="padding:32px 40px 0;">
      <p style="margin:0;font-size:16px;color:#333;">Xin chào <strong>{$name}</strong>,</p>
      <p style="margin:12px 0 0;color:#555;line-height:1.6;">
        Cảm ơn bạn đã tin tưởng mua hàng tại <strong>{$appName}</strong>!
        Đơn hàng <strong>#{$orderId}</strong> của bạn đã được chúng tôi tiếp nhận và đang được xử lý.
      </p>
    </td>
  </tr>

  <!-- Order Info -->
  <tr>
    <td style="padding:24px 40px 0;">
      <table width="100%" cellpadding="0" cellspacing="0" style="background:#fafafa;border-radius:12px;padding:0;overflow:hidden;border:1px solid #eee;">
        <tr style="background:#f0f0f0;">
          <td colspan="2" style="padding:12px 18px;font-weight:bold;color:#333;font-size:14px;">📋 Thông tin đơn hàng</td>
        </tr>
        <tr>
          <td style="padding:10px 18px;color:#666;font-size:13px;width:45%;">Mã đơn hàng:</td>
          <td style="padding:10px 18px;font-weight:bold;color:#d70018;font-size:13px;">#{$orderId}</td>
        </tr>
        <tr style="background:#f9f9f9;">
          <td style="padding:10px 18px;color:#666;font-size:13px;">Phương thức thanh toán:</td>
          <td style="padding:10px 18px;font-weight:600;font-size:13px;">{$method}</td>
        </tr>
        <tr>
          <td style="padding:10px 18px;color:#666;font-size:13px;">Địa chỉ giao hàng:</td>
          <td style="padding:10px 18px;font-size:13px;">{$address}</td>
        </tr>
        <tr style="background:#f9f9f9;">
          <td style="padding:10px 18px;color:#666;font-size:13px;">Tổng thanh toán:</td>
          <td style="padding:10px 18px;font-weight:bold;color:#d70018;font-size:15px;">{$total}</td>
        </tr>
      </table>
    </td>
  </tr>

  <!-- Items Table -->
  <tr>
    <td style="padding:24px 40px 0;">
      <p style="margin:0 0 12px;font-weight:bold;color:#333;font-size:14px;">🛍️ Sản phẩm đã đặt</p>
      <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #eee;border-radius:8px;overflow:hidden;">
        <thead>
          <tr style="background:#d70018;color:#fff;">
            <th style="padding:10px 12px;text-align:left;font-size:13px;">Sản phẩm</th>
            <th style="padding:10px 12px;text-align:center;font-size:13px;">SL</th>
            <th style="padding:10px 12px;text-align:right;font-size:13px;">Đơn giá</th>
            <th style="padding:10px 12px;text-align:right;font-size:13px;">Thành tiền</th>
          </tr>
        </thead>
        <tbody>
          {$rows}
        </tbody>
        <tfoot>
          <tr style="background:#fff8f8;">
            <td colspan="3" style="padding:14px 12px;text-align:right;font-weight:bold;color:#333;font-size:14px;">Tổng cộng:</td>
            <td style="padding:14px 12px;text-align:right;font-weight:bold;color:#d70018;font-size:16px;">{$total}</td>
          </tr>
        </tfoot>
      </table>
    </td>
  </tr>

  <!-- CTA -->
  <tr>
    <td style="padding:28px 40px;text-align:center;">
      <a href="/" style="display:inline-block;background:linear-gradient(135deg,#d70018,#ff3344);color:#fff;padding:14px 36px;border-radius:50px;text-decoration:none;font-weight:bold;font-size:15px;box-shadow:0 4px 16px rgba(215,0,24,0.35);">
        🛒 Tiếp tục mua sắm
      </a>
    </td>
  </tr>

  <!-- Footer -->
  <tr>
    <td style="background:#f8f8f8;padding:20px 40px;text-align:center;border-top:1px solid #eee;">
      <p style="margin:0;color:#999;font-size:12px;">
        Nếu có thắc mắc, vui lòng liên hệ hotline hoặc email của chúng tôi.<br>
        © <?= date('Y') ?> {$appName} – Tất cả các quyền được bảo lưu.
      </p>
    </td>
  </tr>

</table>
</td></tr>
</table>
</body>
</html>
HTML;

        return Mailer::send($to, $subject, $body);
    }

    /**
     * Gửi email thông báo thay đổi trạng thái đơn hàng.
     */
    public static function sendStatusUpdate(array $order): bool
    {
        $to      = $order['email'] ?? '';
        $name    = $order['customer_name'] ?? 'Quý khách';
        $orderId = $order['order_code'] ?? $order['id'] ?? '?';
        $status  = self::statusLabel($order['status'] ?? 'pending');
        $appName = config('app_name', 'Hades Shop');

        if (empty($to)) return false;

        $subject = "🔔 Cập nhật trạng thái đơn hàng #{$orderId} - {$appName}";

        $body = <<<HTML
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"></head>
<body style="font-family:sans-serif; line-height:1.6; color:#333; padding:20px; background:#f4f4f4;">
    <div style="max-width:600px; margin:0 auto; background:#fff; padding:30px; border-radius:10px;">
        <h2 style="color:#d70018;">Cập nhật đơn hàng #{$orderId}</h2>
        <p>Xin chào <strong>{$name}</strong>,</p>
        <p>Đơn hàng của bạn đã có cập nhật mới về trạng thái:</p>
        <div style="background:#fff8f8; padding:15px; border-left:4px solid #d70018; margin:20px 0; font-size:18px; font-weight:bold;">
            Trạng thái hiện tại: <span style="color:#d70018;">{$status}</span>
        </div>
        <p>Bạn có thể theo dõi chi tiết đơn hàng tại website của chúng tôi.</p>
        <p style="margin-top:30px;">Trân trọng,<br>Đội ngũ {$appName}</p>
    </div>
</body>
</html>
HTML;

        return Mailer::send($to, $subject, $body);
    }

    private static function statusLabel(string $status): string
    {
        return match ($status) {
            'confirmed' => '✅ Đã xác nhận',
            'shipping'  => '🚚 Đang giao hàng',
            'completed' => '🏁 Đã hoàn thành',
            'cancelled' => '❌ Đã hủy',
            default     => '⏳ Đang xử lý',
        };
    }

    private static function paymentLabel(string $method): string
    {
        return match ($method) {
            'vnpay' => '💳 VNPay (Thẻ ngân hàng)',
            'momo'  => '📱 Ví MoMo',
            'bank_transfer' => '🏦 Chuyển khoản VietQR (MB Bank)',
            'installment' => '📑 Trả góp',
            default => '💵 Thanh toán khi nhận hàng (COD)',
        };
    }
}
