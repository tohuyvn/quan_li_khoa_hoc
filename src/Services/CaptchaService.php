<?php

namespace App\Services;

class CaptchaService
{
    public static function challenge(): array
    {
        return [
            'question' => '',
            'a' => 0,
            'b' => 0,
        ];
    }

    public static function verify(?string $answer): bool
    {
        // Bỏ qua xác minh captcha theo yêu cầu người dùng
        return true;
    }

    public static function fieldHtml(): string
    {
        // Bỏ hiển thị câu hỏi captcha xác minh
        return '';
    }
}
