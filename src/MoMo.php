<?php

namespace App;

class MoMo
{
    public const MAX_AMOUNT = 20000000;

    public static function createPaymentUrl(array $order): array|false
    {
        $amount = (int) $order['total_amount'];
        if ($amount < 1000 || $amount > self::MAX_AMOUNT) {
            self::log($order['id'], 'Amount out of range: ' . $amount);

            return false;
        }

        $requestId = (string) time();
        $orderId = $requestId;
        $orderInfo = 'Thanh toan don hang ' . ($order['order_code'] ?? $order['id']);
        $extraData = base64_encode(json_encode(['orderId' => (int) $order['id']], JSON_UNESCAPED_UNICODE));

        $signParams = [
            'accessKey' => config('momo.access_key'),
            'amount' => (string) $amount,
            'extraData' => $extraData,
            'ipnUrl' => config('momo.ipn_url'),
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'partnerCode' => config('momo.partner_code'),
            'redirectUrl' => config('momo.redirect_url'),
            'requestId' => $requestId,
            'requestType' => 'captureWallet',
        ];

        $requestData = [
            'partnerCode' => $signParams['partnerCode'],
            'partnerName' => config('momo.partner_name'),
            'storeId' => config('momo.store_id'),
            'requestId' => $signParams['requestId'],
            'amount' => $signParams['amount'],
            'orderId' => $signParams['orderId'],
            'orderInfo' => $signParams['orderInfo'],
            'redirectUrl' => $signParams['redirectUrl'],
            'ipnUrl' => $signParams['ipnUrl'],
            'lang' => 'vi',
            'extraData' => $extraData,
            'requestType' => $signParams['requestType'],
            'signature' => self::signCreate($signParams),
        ];

        $response = self::post(config('momo.endpoint'), $requestData);
        if (($response['status'] ?? 0) === 200) {
            $data = json_decode($response['body'] ?? '', true);
            if (($data['resultCode'] ?? -1) === 0 && !empty($data['payUrl'])) {
                return [
                    'payUrl' => $data['payUrl'],
                    'qrCodeUrl' => $data['qrCodeUrl'] ?? '',
                    'deeplink' => $data['deeplink'] ?? '',
                ];
            }
        }

        self::log($order['id'], $response['body'] ?? ($response['error'] ?? 'unknown'));

        return false;
    }

    public static function verifyCallback(array $payload): bool
    {
        $signature = $payload['signature'] ?? '';
        if ($signature === '') {
            return false;
        }

        $parts = [
            'accessKey' => config('momo.access_key'),
            'amount' => $payload['amount'] ?? '',
            'extraData' => $payload['extraData'] ?? '',
            'message' => $payload['message'] ?? '',
            'orderId' => $payload['orderId'] ?? '',
            'orderInfo' => $payload['orderInfo'] ?? '',
            'partnerCode' => $payload['partnerCode'] ?? '',
            'payType' => $payload['payType'] ?? '',
            'requestId' => $payload['requestId'] ?? '',
            'responseTime' => $payload['responseTime'] ?? '',
            'resultCode' => $payload['resultCode'] ?? '',
            'transId' => $payload['transId'] ?? '',
        ];

        $raw = '';
        foreach ($parts as $field => $value) {
            $raw .= ($raw === '' ? '' : '&') . $field . '=' . $value;
        }

        $calc = hash_hmac('sha256', $raw, config('momo.secret_key'));

        return hash_equals($calc, $signature);
    }

    public static function resolveOrderId(array $input): int
    {
        $fromExtra = self::parseOrderIdFromExtra($input['extraData'] ?? '');
        if ($fromExtra > 0) {
            return $fromExtra;
        }

        if (isset($_SESSION['pending_momo_order_id'])) {
            return (int) $_SESSION['pending_momo_order_id'];
        }

        return 0;
    }

    public static function parseOrderIdFromExtra(string $extraData): int
    {
        if ($extraData === '') {
            return 0;
        }

        $decoded = base64_decode($extraData, true);
        if ($decoded === false) {
            return 0;
        }

        $data = json_decode($decoded, true);
        if (!is_array($data)) {
            return 0;
        }

        return (int) ($data['orderId'] ?? $data['order_id'] ?? 0);
    }

    private static function signCreate(array $params): string
    {
        $raw = 'accessKey=' . $params['accessKey']
            . '&amount=' . $params['amount']
            . '&extraData=' . $params['extraData']
            . '&ipnUrl=' . $params['ipnUrl']
            . '&orderId=' . $params['orderId']
            . '&orderInfo=' . $params['orderInfo']
            . '&partnerCode=' . $params['partnerCode']
            . '&redirectUrl=' . $params['redirectUrl']
            . '&requestId=' . $params['requestId']
            . '&requestType=' . $params['requestType'];

        return hash_hmac('sha256', $raw, config('momo.secret_key'));
    }

    private static function post(string $url, array $data): array
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);
        $body = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($body === false) {
            return ['status' => 0, 'error' => $error];
        }

        return ['status' => $status, 'body' => $body];
    }

    private static function log(int $orderId, string $message): void
    {
        $logDir = BASE_PATH . '/storage/logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0775, true);
        }
        file_put_contents(
            $logDir . '/momo.log',
            sprintf("[%s] Order #%d | %s\n", date('Y-m-d H:i:s'), $orderId, $message),
            FILE_APPEND
        );
    }
}
