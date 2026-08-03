<?php

namespace App\Services;

class VietQrService
{
    public static function config(): array
    {
        return [
            'bank_code' => (string) config('bank_transfer.bank_code', 'MB'),
            'bank_name' => (string) config('bank_transfer.bank_name', 'MB Bank'),
            'account_no' => (string) config('bank_transfer.account_no', '0788415516'),
            'account_name' => (string) config('bank_transfer.account_name', 'TO QUANG HUY'),
            'account_name_display' => (string) config('bank_transfer.account_name_display', 'Tô Quang Huy'),
            'template' => (string) config('bank_transfer.template', 'compact2'),
        ];
    }

    public static function imageUrl(?int $amount = null, ?string $addInfo = null, string $template = ''): string
    {
        $cfg = self::config();
        $tpl = $template !== '' ? $template : $cfg['template'];
        $url = sprintf(
            'https://img.vietqr.io/image/%s-%s-%s.png',
            rawurlencode($cfg['bank_code']),
            rawurlencode($cfg['account_no']),
            rawurlencode($tpl)
        );

        $query = ['accountName' => $cfg['account_name']];
        if ($amount !== null && $amount > 0) {
            $query['amount'] = (string) $amount;
        }
        if ($addInfo !== null && trim($addInfo) !== '') {
            $query['addInfo'] = self::sanitizeAddInfo($addInfo);
        }

        return $url . '?' . http_build_query($query);
    }

    public static function localFallbackUrl(): string
    {
        return '/image/vietqr_mb_toquanghuy.png';
    }

    public static function transferContent(array $order): string
    {
        $code = $order['order_code'] ?? ('DH' . ($order['id'] ?? ''));

        return self::sanitizeAddInfo('THANH TOAN ' . $code);
    }

    public static function sanitizeAddInfo(string $text): string
    {
        $text = mb_strtoupper(trim($text), 'UTF-8');
        $map = [
            'Á'=>'A','À'=>'A','Ả'=>'A','Ã'=>'A','Ạ'=>'A','Ă'=>'A','Ắ'=>'A','Ằ'=>'A','Ẳ'=>'A','Ẵ'=>'A','Ặ'=>'A',
            'Â'=>'A','Ấ'=>'A','Ầ'=>'A','Ẩ'=>'A','Ẫ'=>'A','Ậ'=>'A','Đ'=>'D',
            'É'=>'E','È'=>'E','Ẻ'=>'E','Ẽ'=>'E','Ẹ'=>'E','Ê'=>'E','Ế'=>'E','Ề'=>'E','Ể'=>'E','Ễ'=>'E','Ệ'=>'E',
            'Í'=>'I','Ì'=>'I','Ỉ'=>'I','Ĩ'=>'I','Ị'=>'I',
            'Ó'=>'O','Ò'=>'O','Ỏ'=>'O','Õ'=>'O','Ọ'=>'O','Ô'=>'O','Ố'=>'O','Ồ'=>'O','Ổ'=>'O','Ỗ'=>'O','Ộ'=>'O',
            'Ơ'=>'O','Ớ'=>'O','Ờ'=>'O','Ở'=>'O','Ỡ'=>'O','Ợ'=>'O',
            'Ú'=>'U','Ù'=>'U','Ủ'=>'U','Ũ'=>'U','Ụ'=>'U','Ư'=>'U','Ứ'=>'U','Ừ'=>'U','Ử'=>'U','Ữ'=>'U','Ự'=>'U',
            'Ý'=>'Y','Ỳ'=>'Y','Ỷ'=>'Y','Ỹ'=>'Y','Ỵ'=>'Y',
        ];
        $text = strtr($text, $map);
        $text = preg_replace('/[^A-Z0-9\s\-]/', '', $text) ?? '';
        $text = preg_replace('/\s+/', ' ', $text) ?? '';

        return trim(mb_substr($text, 0, 50));
    }
}
