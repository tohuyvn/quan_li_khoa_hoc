<?php

namespace App;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class Mailer
{
    private static ?string $lastError = null;

    public static function getLastError(): string
    {
        return self::$lastError ?? '';
    }

    public static function isConfigured(): bool
    {
        $user = config('mail.username');
        $pass = config('mail.password');
        $from = config('mail.from_email');

        return $user !== '' && $pass !== '' && $from !== '';
    }

    public static function send(string $to, string $subject, string $htmlBody): bool
    {
        self::$lastError = null;

        if (!self::isConfigured()) {
            self::$lastError = 'Chưa cấu hình SMTP. Điền mail.password trong config/config.php.';
            self::logFallback($to, $subject, $htmlBody);
            return false;
        }

        if (!self::loadPhpMailer()) {
            return self::sendViaSocket($to, $subject, $htmlBody);
        }

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = config('mail.host');
            $mail->SMTPAuth = true;
            $mail->Username = config('mail.username');
            $mail->Password = str_replace(' ', '', (string) config('mail.password'));
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = (int) config('mail.port', 587);
            $mail->CharSet = 'UTF-8';
            $mail->Timeout = 30;

            if (config('mail.ssl_verify', false) === false) {
                $mail->SMTPOptions = [
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true,
                    ],
                ];
            }

            $mail->setFrom(config('mail.from_email'), config('mail.from_name', 'Shop Hades'));
            $mail->addAddress($to);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $htmlBody;
            $mail->AltBody = strip_tags($htmlBody);

            $mail->send();
            return true;
        } catch (Exception $e) {
            self::$lastError = $mail->ErrorInfo ?: $e->getMessage();
            self::logFallback($to, $subject, $htmlBody);
            return false;
        }
    }

    private static function loadPhpMailer(): bool
    {
        static $loaded = null;
        if ($loaded !== null) {
            return $loaded;
        }

        $autoloads = [
            BASE_PATH . '/vendor/autoload.php',
            dirname(BASE_PATH) . '/Lab4/vendor/autoload.php',
        ];

        foreach ($autoloads as $autoload) {
            if (is_file($autoload)) {
                require_once $autoload;
                $loaded = class_exists(PHPMailer::class);
                return $loaded;
            }
        }

        $loaded = false;
        return false;
    }

    private static function sendViaSocket(string $to, string $subject, string $htmlBody): bool
    {
        $host = config('mail.host');
        $port = (int) config('mail.port', 587);
        $user = config('mail.username');
        $pass = str_replace(' ', '', (string) config('mail.password'));
        $from = config('mail.from_email');
        $fromName = config('mail.from_name', 'Shop Hades');

        $context = null;
        if (config('mail.ssl_verify', false) === false) {
            $context = stream_context_create([
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                ],
            ]);
        }

        try {
            $socket = @stream_socket_client(
                "tcp://{$host}:{$port}",
                $errno,
                $errstr,
                15,
                STREAM_CLIENT_CONNECT,
                $context
            );

            if (!$socket) {
                self::$lastError = "Không kết nối SMTP: {$errstr}";
                self::logFallback($to, $subject, $htmlBody);
                return false;
            }

            self::read($socket);
            self::cmd($socket, 'EHLO localhost');
            self::cmd($socket, 'STARTTLS');
            stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
            self::cmd($socket, 'EHLO localhost');
            self::cmd($socket, 'AUTH LOGIN');
            self::cmd($socket, base64_encode($user));
            self::cmd($socket, base64_encode($pass));
            self::cmd($socket, "MAIL FROM:<{$from}>");
            self::cmd($socket, "RCPT TO:<{$to}>");
            self::cmd($socket, 'DATA');

            $headers = [
                "From: {$fromName} <{$from}>",
                "To: <{$to}>",
                "Subject: {$subject}",
                'MIME-Version: 1.0',
                'Content-Type: text/html; charset=UTF-8',
                'Content-Transfer-Encoding: 8bit',
            ];

            $message = implode("\r\n", $headers) . "\r\n\r\n" . $htmlBody . "\r\n.";
            fwrite($socket, $message . "\r\n");
            self::read($socket);
            self::cmd($socket, 'QUIT');
            fclose($socket);

            return true;
        } catch (\Throwable $e) {
            self::$lastError = $e->getMessage();
            self::logFallback($to, $subject, $htmlBody);
            return false;
        }
    }

    private static function cmd($socket, string $cmd): void
    {
        fwrite($socket, $cmd . "\r\n");
        self::read($socket);
    }

    private static function read($socket): string
    {
        $data = '';
        while ($line = fgets($socket, 515)) {
            $data .= $line;
            if (isset($line[3]) && $line[3] === ' ') {
                break;
            }
        }
        return $data;
    }

    private static function logFallback(string $to, string $subject, string $body): void
    {
        $logDir = BASE_PATH . '/storage/logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0775, true);
        }
        $log = sprintf(
            "[%s] TO: %s | SUBJECT: %s | BODY: %s | ERROR: %s\n",
            date('Y-m-d H:i:s'),
            $to,
            $subject,
            strip_tags($body),
            self::$lastError ?? 'unknown'
        );
        file_put_contents($logDir . '/mail.log', $log, FILE_APPEND);
    }
}
