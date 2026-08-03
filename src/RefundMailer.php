<?php

namespace App;

class RefundMailer
{
    public static function sendApproved(array $order, array $refund, ?string $voucherCode = null): bool
    {
        $to = $order['email'] ?? '';
        if ($to === '') {
            return false;
        }

        $appName = config('app_name', 'Shop Hades');
        $orderCode = $order['order_code'] ?? ('#' . ($order['id'] ?? ''));
        $amount = formatMoney((int) ($refund['amount'] ?? $order['total_amount'] ?? 0));
        $method = ($refund['method'] ?? 'wallet') === 'voucher' ? 'Voucher giảm giá' : 'Ví Hades';
        $name = $order['customer_name'] ?? 'Quý khách';

        $extra = '';
        if ($voucherCode) {
            $extra = "<p>Mã voucher của bạn: <strong style=\"color:#d70018;font-family:monospace;font-size:18px;\">{$voucherCode}</strong></p>"
                . '<p>Voucher có hiệu lực 30 ngày — dùng tại trang thanh toán hoặc xem tại <strong>Ví Hades</strong>.</p>';
        } elseif (($refund['method'] ?? 'wallet') === 'wallet') {
            $extra = '<p>Số tiền đã được cộng vào <strong>Ví Hades</strong>. Bạn có thể dùng khi thanh toán đơn tiếp theo.</p>';
        }

        $subject = "✅ Hoàn tiền đơn {$orderCode} đã được duyệt — {$appName}";
        $body = self::wrap(
            $name,
            "Yêu cầu hoàn tiền đơn <strong>{$orderCode}</strong> đã được duyệt.",
            "<p>Phương thức: <strong>{$method}</strong></p>"
            . "<p>Số tiền: <strong style=\"color:#198754;\">{$amount}</strong></p>"
            . $extra
        );

        return Mailer::send($to, $subject, $body);
    }

    public static function sendRejected(array $order, array $refund, ?string $adminNote = null): bool
    {
        $to = $order['email'] ?? '';
        if ($to === '') {
            return false;
        }

        $appName = config('app_name', 'Shop Hades');
        $orderCode = $order['order_code'] ?? ('#' . ($order['id'] ?? ''));
        $name = $order['customer_name'] ?? 'Quý khách';
        $note = $adminNote !== null && $adminNote !== ''
            ? '<p style="background:#fef2f2;padding:12px;border-radius:8px;color:#991b1b;">Lý do: ' . htmlspecialchars($adminNote, ENT_QUOTES, 'UTF-8') . '</p>'
            : '';

        $subject = "Yêu cầu hoàn tiền đơn {$orderCode} — {$appName}";
        $body = self::wrap(
            $name,
            "Rất tiếc, yêu cầu hoàn tiền đơn <strong>{$orderCode}</strong> chưa được duyệt.",
            $note . '<p>Nếu cần hỗ trợ thêm, vui lòng liên hệ hotline '
            . htmlspecialchars((string) config('hotline', '1800 1234'), ENT_QUOTES, 'UTF-8') . '.</p>'
        );

        return Mailer::send($to, $subject, $body);
    }

    private static function wrap(string $name, string $headline, string $content): string
    {
        $appName = config('app_name', 'Shop Hades');

        return <<<HTML
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"></head>
<body style="font-family:'Segoe UI',sans-serif;line-height:1.6;color:#333;padding:20px;background:#f4f6f9;">
<div style="max-width:560px;margin:0 auto;background:#fff;padding:28px;border-radius:12px;box-shadow:0 4px 16px rgba(0,0,0,0.06);">
    <h2 style="color:#d70018;margin-top:0;">{$appName}</h2>
    <p>Xin chào <strong>{$name}</strong>,</p>
    <p>{$headline}</p>
    {$content}
    <p style="margin-top:24px;color:#64748b;font-size:13px;">Trân trọng,<br>Đội ngũ {$appName}</p>
</div>
</body>
</html>
HTML;
    }
}
