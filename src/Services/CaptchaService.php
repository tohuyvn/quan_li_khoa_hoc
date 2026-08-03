<?php

namespace App\Services;

class CaptchaService
{
    public static function challenge(): array
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $a = random_int(1, 9);
        $b = random_int(1, 9);
        $_SESSION['captcha_answer'] = $a + $b;
        $_SESSION['captcha_expires'] = time() + 600;

        return [
            'question' => "{$a} + {$b} = ?",
            'a' => $a,
            'b' => $b,
        ];
    }

    public static function verify(?string $answer): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $expected = $_SESSION['captcha_answer'] ?? null;
        $expires = (int) ($_SESSION['captcha_expires'] ?? 0);
        unset($_SESSION['captcha_answer'], $_SESSION['captcha_expires']);

        if ($expected === null || $expires < time()) {
            return false;
        }

        return (int) trim((string) $answer) === (int) $expected;
    }

    public static function fieldHtml(): string
    {
        $c = self::challenge();
        $q = htmlspecialchars($c['question'], ENT_QUOTES, 'UTF-8');

        return '<div class="mb-3">'
            . '<label class="form-label">Xác minh: ' . $q . '</label>'
            . '<input type="number" name="captcha_answer" class="form-control" required placeholder="Nhập kết quả" autocomplete="off">'
            . '</div>';
    }
}
