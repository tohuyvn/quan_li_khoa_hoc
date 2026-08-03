<?php

namespace App;

class OtpService
{
    private const SESSION_KEY = 'pending_otp';

    public static function generate(): string
    {
        $length = (int) config('otp.length', 6);
        $min = (int) str_pad('1', $length, '0');
        $max = (int) str_pad('9', $length, '9');
        return (string) random_int($min, $max);
    }

    public static function send(string $email, string $action, array $payload = []): bool
    {
        $code = self::generate();
        $expire = (int) config('otp.expire_minutes', 5) * 60;

        $_SESSION[self::SESSION_KEY] = [
            'code' => $code,
            'email' => $email,
            'action' => $action,
            'payload' => $payload,
            'expires_at' => time() + $expire,
            'sent_at' => time(),
        ];

        $appName = config('app_name', 'Shop Hades');
        $subject = "[{$appName}] Mã OTP xác thực của bạn";
        $baseUrl = rtrim(config('base_url', 'http://127.0.0.1:8000'), '/');
        $logoUrl = $baseUrl . '/image/logo.webp';
        $expireMin = round($expire / 60);

        $body = <<<HTML
        <div style="background-color: #0f172a; padding: 40px 20px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; min-height: 100%; color: #f8fafc;">
            <div style="max-width: 500px; margin: 0 auto; background-color: #1e293b; border: 1px solid #334155; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.3);">
                <div style="background-color: #020617; padding: 24px; text-align: center; border-bottom: 1px solid #334155;">
                    <img src="{$logoUrl}" alt="{$appName} Logo" style="height: 48px; display: inline-block;">
                </div>
                <div style="padding: 32px 24px; text-align: center;">
                    <h3 style="color: #f8fafc; margin: 0 0 8px 0; font-size: 20px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Xác Thực OTP</h3>
                    <p style="color: #94a3b8; font-size: 14px; margin: 0 0 24px 0;">Mã OTP xác thực hành động <strong>{$action}</strong> trên Shop Hades của bạn:</p>
                    
                    <div style="background-color: #0f172a; border: 1px dashed #ef4444; border-radius: 12px; padding: 18px; margin-bottom: 24px; display: inline-block; width: 80%;">
                        <span style="font-size: 36px; font-weight: 800; letter-spacing: 8px; color: #ef4444; font-family: monospace;">{$code}</span>
                    </div>
                    
                    <p style="color: #64748b; font-size: 12px; margin: 0 0 16px 0; line-height: 1.5;">Mã có hiệu lực trong <strong>{$expireMin} phút</strong>. Vì lý do bảo mật, tuyệt đối không chia sẻ mã này với bất kỳ ai.</p>
                </div>
                <div style="background-color: #020617; padding: 16px; text-align: center; border-top: 1px solid #334155;">
                    <p style="color: #475569; font-size: 11px; margin: 0;">© 2026 Shop Hades. All rights reserved.</p>
                    <p style="color: #475569; font-size: 11px; margin: 4px 0 0 0;">Hotline hỗ trợ: 1800 1234</p>
                </div>
            </div>
        </div>
        HTML;

        $sent = Mailer::send($email, $subject, $body);

        if (!$sent) {
            if (config('app_env') === 'local') {
                $logDir = BASE_PATH . '/storage/logs';
                if (!is_dir($logDir)) {
                    mkdir($logDir, 0775, true);
                }
                file_put_contents(
                    $logDir . '/otp.log',
                    sprintf(
                        "[%s] %s | OTP: %s | Action: %s | ERROR: %s\n",
                        date('Y-m-d H:i:s'),
                        $email,
                        $code,
                        $action,
                        Mailer::getLastError()
                    ),
                    FILE_APPEND
                );
            }
            return true;
        }

        return true;
    }

    public static function pending(): ?array
    {
        return $_SESSION[self::SESSION_KEY] ?? null;
    }

    public static function verify(string $inputCode): bool
    {
        $pending = self::pending();
        if (!$pending) {
            return false;
        }
        if (time() > ($pending['expires_at'] ?? 0)) {
            self::clear();
            return false;
        }
        return hash_equals((string) $pending['code'], trim($inputCode));
    }

    public static function canResend(): bool
    {
        $pending = self::pending();
        if (!$pending) {
            return true;
        }
        $wait = (int) config('otp.resend_seconds', 60);
        return time() - ($pending['sent_at'] ?? 0) >= $wait;
    }

    public static function resend(): bool
    {
        $pending = self::pending();
        if (!$pending || !self::canResend()) {
            return false;
        }
        return self::send(
            $pending['email'],
            $pending['action'],
            $pending['payload'] ?? []
        );
    }

    public static function clear(): void
    {
        unset($_SESSION[self::SESSION_KEY]);
    }
}
