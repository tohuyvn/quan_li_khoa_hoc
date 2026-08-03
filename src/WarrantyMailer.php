<?php

namespace App;

class WarrantyMailer
{
    public static function sendActivated(array $warranty, ?string $email = null): bool
    {
        $to = $email ?: ($warranty['email'] ?? '');
        if ($to === '') {
            return false;
        }

        $appName = htmlspecialchars((string) config('app_name', 'Shop Hades'), ENT_QUOTES, 'UTF-8');
        $name = self::e($warranty['customer_name'] ?? 'Quý khách');
        $code = self::e($warranty['warranty_code'] ?? '');
        $imei = self::e($warranty['imei'] ?? '');
        $product = self::e($warranty['product_name'] ?? 'Sản phẩm');
        $expired = self::e($warranty['expired_at'] ?? '');
        $base = rtrim((string) config('base_url', ''), '/');
        $lookupUrl = $base . '/warranty';

        $subject = "🛡️ Kích hoạt bảo hành {$code} — {$appName}";
        $body = <<<HTML
<!DOCTYPE html><html lang="vi"><body style="font-family:Segoe UI,Arial,sans-serif;background:#f4f6f9;padding:24px;">
<table width="600" style="margin:auto;background:#fff;border-radius:12px;overflow:hidden">
<tr><td style="background:#d70018;color:#fff;padding:20px;text-align:center"><h2 style="margin:0">{$appName}</h2></td></tr>
<tr><td style="padding:24px">
<p>Xin chào <strong>{$name}</strong>,</p>
<p>Bảo hành điện tử cho <strong>{$product}</strong> đã được kích hoạt.</p>
<ul>
<li>Mã BH: <strong>{$code}</strong></li>
<li>IMEI: <strong>{$imei}</strong></li>
<li>Hết hạn: <strong>{$expired}</strong></li>
</ul>
<p><a href="{$lookupUrl}" style="background:#d70018;color:#fff;padding:10px 16px;border-radius:8px;text-decoration:none">Tra cứu bảo hành</a></p>
</td></tr></table></body></html>
HTML;

        return Mailer::send($to, $subject, $body);
    }

    public static function sendClaimStatus(array $claim, array $warranty, ?string $email = null): bool
    {
        $to = $email ?: ($warranty['email'] ?? '');
        if ($to === '') {
            return false;
        }

        $labels = [
            'pending' => 'Chờ tiếp nhận',
            'receiving' => 'Đã tiếp nhận',
            'repairing' => 'Đang sửa chữa',
            'completed' => 'Hoàn thành',
            'rejected' => 'Từ chối',
        ];
        $status = $labels[$claim['status'] ?? ''] ?? ($claim['status'] ?? '');
        $appName = htmlspecialchars((string) config('app_name', 'Shop Hades'), ENT_QUOTES, 'UTF-8');
        $name = self::e($warranty['customer_name'] ?? 'Quý khách');
        $code = self::e($warranty['warranty_code'] ?? '');
        $note = self::e($claim['admin_note'] ?? '—');
        $product = self::e($warranty['product_name'] ?? '');
        $statusSafe = self::e($status);

        $subject = "🔧 Cập nhật bảo hành {$code}: {$status} — " . config('app_name', 'Shop Hades');
        $body = <<<HTML
<!DOCTYPE html><html lang="vi"><body style="font-family:Segoe UI,Arial,sans-serif;background:#f4f6f9;padding:24px;">
<table width="600" style="margin:auto;background:#fff;border-radius:12px;overflow:hidden">
<tr><td style="background:#111;color:#fff;padding:20px;text-align:center"><h2 style="margin:0">{$appName}</h2></td></tr>
<tr><td style="padding:24px">
<p>Xin chào <strong>{$name}</strong>,</p>
<p>Yêu cầu bảo hành/sửa chữa cho <strong>{$product}</strong> (mã <strong>{$code}</strong>) đã cập nhật:</p>
<p style="font-size:18px"><strong>{$statusSafe}</strong></p>
<p>Ghi chú: {$note}</p>
</td></tr></table></body></html>
HTML;

        return Mailer::send($to, $subject, $body);
    }

    private static function e(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}
