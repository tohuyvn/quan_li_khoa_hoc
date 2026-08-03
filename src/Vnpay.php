<?php

namespace App;

class Vnpay
{
    private static function buildHashData(array $inputData): string
    {
        ksort($inputData);
        $hashData = '';
        $i = 0;
        foreach ($inputData as $key => $value) {
            if ($i === 1) {
                $hashData .= '&' . urlencode((string) $key) . '=' . urlencode((string) $value);
            } else {
                $hashData .= urlencode((string) $key) . '=' . urlencode((string) $value);
                $i = 1;
            }
        }

        return $hashData;
    }

    public static function createPaymentUrl(array $order, string $bankCode = ''): string
    {
        if ($bankCode === '') {
            $bankCode = config('vnpay.default_bank_code', 'NCB');
        }
        $vnp_TmnCode = config('vnpay.tmn_code');
        $vnp_HashSecret = config('vnpay.hash_secret');
        $vnp_Url = config('vnpay.url');
        $vnp_ReturnUrl = config('vnpay.return_url');

        $vnp_TxnRef = $order['id'] . '_' . time();
        $vnp_Amount = (int) $order['total_amount'] * 100;
        $vnp_OrderInfo = 'Thanh toan don hang #' . $order['id'];
        $vnp_CreateDate = date('YmdHis');
        $expireMinutes = (int) config('vnpay.expire_minutes', 30);
        $vnp_ExpireDate = date('YmdHis', strtotime('+' . $expireMinutes . ' minutes', strtotime($vnp_CreateDate)));
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        $inputData = [
            'vnp_Version' => '2.1.0',
            'vnp_TmnCode' => $vnp_TmnCode,
            'vnp_Amount' => $vnp_Amount,
            'vnp_Command' => 'pay',
            'vnp_CreateDate' => $vnp_CreateDate,
            'vnp_CurrCode' => 'VND',
            'vnp_ExpireDate' => $vnp_ExpireDate,
            'vnp_IpAddr' => $vnp_IpAddr,
            'vnp_Locale' => 'vn',
            'vnp_OrderInfo' => $vnp_OrderInfo,
            'vnp_OrderType' => 'other',
            'vnp_ReturnUrl' => $vnp_ReturnUrl,
            'vnp_TxnRef' => $vnp_TxnRef,
        ];

        if ($bankCode !== '') {
            $inputData['vnp_BankCode'] = $bankCode;
        }

        $hashData = self::buildHashData($inputData);
        $vnp_SecureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        $query = '';
        foreach ($inputData as $key => $value) {
            $query .= urlencode((string) $key) . '=' . urlencode((string) $value) . '&';
        }

        return $vnp_Url . '?' . $query . 'vnp_SecureHash=' . $vnp_SecureHash;
    }

    public static function verifyReturn(array $input): bool
    {
        $vnp_HashSecret = config('vnpay.hash_secret');
        $vnp_SecureHash = $input['vnp_SecureHash'] ?? '';
        unset($input['vnp_SecureHash'], $input['vnp_SecureHashType']);

        $hashData = self::buildHashData($input);

        return hash_hmac('sha512', $hashData, $vnp_HashSecret) === $vnp_SecureHash;
    }
}
