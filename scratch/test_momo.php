<?php
require_once __DIR__ . '/../src/bootstrap.php';

$order = [
    'id' => 99999,
    'total_amount' => 50000,
];

// Call createPaymentUrl
$requestId = (string) time();
$orderId = $requestId;
$orderInfo = 'Thanh toan don hang ' . $order['id'];
$extraData = base64_encode(json_encode(['orderId' => (int) $order['id']], JSON_UNESCAPED_UNICODE));

$signParams = [
    'accessKey' => config('momo.access_key'),
    'amount' => (string) $order['total_amount'],
    'extraData' => $extraData,
    'ipnUrl' => config('momo.ipn_url'),
    'orderId' => $orderId,
    'orderInfo' => $orderInfo,
    'partnerCode' => config('momo.partner_code'),
    'redirectUrl' => config('momo.redirect_url'),
    'requestId' => $requestId,
    'requestType' => 'captureWallet',
];

$raw = 'accessKey=' . $signParams['accessKey']
    . '&amount=' . $signParams['amount']
    . '&extraData=' . $signParams['extraData']
    . '&ipnUrl=' . $signParams['ipnUrl']
    . '&orderId=' . $signParams['orderId']
    . '&orderInfo=' . $signParams['orderInfo']
    . '&partnerCode=' . $signParams['partnerCode']
    . '&redirectUrl=' . $signParams['redirectUrl']
    . '&requestId=' . $signParams['requestId']
    . '&requestType=' . $signParams['requestType'];

$signature = hash_hmac('sha256', $raw, config('momo.secret_key'));

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
    'signature' => $signature,
];

$ch = curl_init(config('momo.endpoint'));
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($requestData),
    CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_SSL_VERIFYPEER => false,
]);
$body = curl_exec($ch);
$status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "HTTP Status: $status\n";
echo "Response Body:\n";
print_r(json_decode($body, true));
echo "\n";
