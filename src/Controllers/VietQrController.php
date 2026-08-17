<?php

namespace App\Controllers;

use App\Models\Order;
use App\OrderStateService;
use App\PaymentService;
use App\Services\VietQrService;

class VietQrController
{
    public function pay(): void
    {
        $orderId = (int) ($_SESSION['pending_vietqr_order_id'] ?? $_GET['order_id'] ?? 0);
        $orderModel = new Order();
        $order = $orderId > 0 ? $orderModel->find($orderId) : null;

        if (!$order || ($order['payment_method'] ?? '') !== 'bank_transfer') {
            flash('error', 'Không tìm thấy đơn hàng chuyển khoản.');
            redirect('/cart');
        }

        if (($order['payment_status'] ?? '') === 'paid') {
            unset($_SESSION['cart'], $_SESSION['pending_vietqr_order_id']);
            $_SESSION['last_order_id'] = $orderId;
            flash('success', 'Thanh toán chuyển khoản thành công!');
            redirect('/orders/success');
        }

        $_SESSION['pending_vietqr_order_id'] = $orderId;
        $_SESSION['last_order_id'] = $orderId;

        $amount = (int) ($order['total_amount'] ?? 0);
        $addInfo = VietQrService::transferContent($order);
        $qrUrl = VietQrService::imageUrl($amount, $addInfo);
        $cfg = VietQrService::config();

        view('client.payment.vietqr_pay', [
            'title' => 'Thanh toán chuyển khoản VietQR',
            'order' => $order,
            'items' => $orderModel->itemsWithProduct($orderId),
            'qrUrl' => $qrUrl,
            'qrFallback' => VietQrService::localFallbackUrl(),
            'bank' => $cfg,
            'transferContent' => $addInfo,
            'amount' => $amount,
            'autoConfirm' => (bool) ($cfg['auto_confirm'] ?? true),
        ]);
    }

    /** Polling JSON — trang QR tự redirect khi payment_status = paid. */
    public function status(): void
    {
        header('Content-Type: application/json; charset=UTF-8');
        header('Cache-Control: no-store');

        $orderId = (int) ($_GET['order_id'] ?? $_SESSION['pending_vietqr_order_id'] ?? 0);
        if ($orderId <= 0) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'error' => 'Invalid order ID']);
            return;
        }

        $orderModel = new Order();
        $order = $orderModel->find($orderId);
        if (!$order || ($order['payment_method'] ?? '') !== 'bank_transfer') {
            http_response_code(404);
            echo json_encode(['ok' => false, 'error' => 'Order not found']);
            return;
        }

        $paid = ($order['payment_status'] ?? '') === 'paid';
        if ($paid) {
            unset($_SESSION['cart'], $_SESSION['pending_vietqr_order_id']);
            $_SESSION['last_order_id'] = $orderId;
        }

        echo json_encode([
            'ok' => true,
            'order_id' => $orderId,
            'order_code' => $order['order_code'] ?? null,
            'payment_status' => $order['payment_status'] ?? 'pending',
            'status' => $order['status'] ?? 'pending',
            'paid' => $paid,
            'redirect' => $paid ? '/orders/success' : null,
        ], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Webhook SePay (Money in).
     * Cấu hình URL: {APP_BASE_URL}/payment/vietqr/webhook
     * Auth: Authorization: Apikey {SEPAY_WEBHOOK_API_KEY}
     */
    public function webhook(): void
    {
        header('Content-Type: application/json; charset=UTF-8');

        $auth = $_SERVER['HTTP_AUTHORIZATION']
            ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION']
            ?? '';
        if ($auth === '' && function_exists('getallheaders')) {
            $headers = getallheaders();
            $auth = $headers['Authorization'] ?? $headers['authorization'] ?? '';
        }

        if (!VietQrService::verifySepayAuth($auth)) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        $payload = json_decode((string) file_get_contents('php://input'), true);
        if (!is_array($payload)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid JSON']);
            return;
        }

        $result = $this->applyIncomingTransfer($payload);
        http_response_code(!empty($result['http']) ? (int) $result['http'] : 200);
        echo json_encode([
            'success' => (bool) ($result['ok'] ?? false),
            'message' => $result['message'] ?? null,
            'order_id' => $result['order_id'] ?? null,
        ], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Dev-only: giả lập webhook đã nhận tiền để test auto-redirect.
     * POST /payment/vietqr/simulate {order_id} — chỉ khi APP_DEBUG=true
     */
    public function simulatePaid(): void
    {
        header('Content-Type: application/json; charset=UTF-8');

        $autoConfirm = (bool) (VietQrService::config()['auto_confirm'] ?? true);
        if (!(bool) config('app_debug', false) && !$autoConfirm) {
            http_response_code(404);
            echo json_encode(['ok' => false, 'error' => 'Not found']);
            return;
        }

        $orderId = (int) ($_POST['order_id'] ?? $_GET['order_id'] ?? 0);
        $orderModel = new Order();
        $order = $orderId > 0 ? $orderModel->find($orderId) : null;
        if (!$order || ($order['payment_method'] ?? '') !== 'bank_transfer') {
            http_response_code(404);
            echo json_encode(['ok' => false, 'error' => 'Order not found']);
            return;
        }

        $payload = [
            'id' => 0,
            'transferType' => 'in',
            'transferAmount' => (int) ($order['total_amount'] ?? 0),
            'accountNumber' => VietQrService::config()['account_no'],
            'content' => VietQrService::transferContent($order),
            'code' => $order['order_code'] ?? null,
            'referenceCode' => 'SIMULATE-' . time(),
        ];

        $result = $this->applyIncomingTransfer($payload);
        echo json_encode([
            'ok' => (bool) ($result['ok'] ?? false),
            'message' => $result['message'] ?? null,
            'paid' => true,
            'redirect' => '/orders/success',
        ], JSON_UNESCAPED_UNICODE);
    }

    public function confirmTransferred(): void
    {
        $orderId = (int) ($_POST['order_id'] ?? $_SESSION['pending_vietqr_order_id'] ?? 0);
        $orderModel = new Order();
        $order = $orderId > 0 ? $orderModel->find($orderId) : null;

        if (!$order || ($order['payment_method'] ?? '') !== 'bank_transfer') {
            flash('error', 'Đơn hàng không hợp lệ.');
            redirect('/cart');
        }

        if (($order['payment_status'] ?? '') === 'paid') {
            unset($_SESSION['cart'], $_SESSION['pending_vietqr_order_id']);
            $_SESSION['last_order_id'] = $orderId;
            flash('success', 'Thanh toán chuyển khoản thành công!');
            redirect('/orders/success');
        }

        unset($_SESSION['cart'], $_SESSION['pending_vietqr_order_id']);
        $_SESSION['last_order_id'] = $orderId;
        flash('success', 'Đã ghi nhận bạn đã chuyển khoản. Hệ thống sẽ tự xác nhận khi nhận được tiền (hoặc admin xác nhận).');
        redirect('/orders/success');
    }

    /**
     * @param array<string, mixed> $payload
     * @return array{ok:bool, message:string, order_id?:int, http?:int}
     */
    private function applyIncomingTransfer(array $payload): array
    {
        $transferType = strtolower((string) ($payload['transferType'] ?? ''));
        if ($transferType !== '' && $transferType !== 'in') {
            return ['ok' => true, 'message' => 'Ignored non-incoming transfer', 'http' => 200];
        }

        $amount = (int) ($payload['transferAmount'] ?? $payload['amount'] ?? 0);
        $content = (string) ($payload['content'] ?? $payload['description'] ?? '');
        $code = isset($payload['code']) ? (string) $payload['code'] : null;
        $accountNumber = (string) ($payload['accountNumber'] ?? '');

        if (!VietQrService::accountMatches($accountNumber)) {
            return ['ok' => false, 'message' => 'Account number mismatch', 'http' => 200];
        }

        $orderCode = VietQrService::extractOrderCode($content, $code);
        if ($orderCode === null) {
            return ['ok' => false, 'message' => 'Order code not found in transfer content', 'http' => 200];
        }

        $orderModel = new Order();
        $order = $orderModel->findByCode($orderCode);
        if (!$order || ($order['payment_method'] ?? '') !== 'bank_transfer') {
            return ['ok' => false, 'message' => 'Order not found for code ' . $orderCode, 'http' => 200];
        }

        $orderId = (int) $order['id'];
        if (($order['payment_status'] ?? '') === 'paid') {
            return ['ok' => true, 'message' => 'Already paid', 'order_id' => $orderId, 'http' => 200];
        }

        if (!VietQrService::amountsMatch((int) ($order['total_amount'] ?? 0), $amount)) {
            return [
                'ok' => false,
                'message' => 'Amount mismatch (order=' . (int) ($order['total_amount'] ?? 0) . ', transfer=' . $amount . ')',
                'order_id' => $orderId,
                'http' => 200,
            ];
        }

        $ref = (string) ($payload['referenceCode'] ?? $payload['id'] ?? '');
        $result = PaymentService::markPaid($orderId);
        if (!$result['ok']) {
            return ['ok' => false, 'message' => 'Failed to mark paid', 'order_id' => $orderId, 'http' => 500];
        }

        if (empty($result['already_paid'])) {
            OrderStateService::logStatus(
                $orderId,
                $order['status'] ?? 'pending',
                $result['order']['status'] ?? 'confirmed',
                'system',
                null,
                'SePay webhook xác nhận chuyển khoản VietQR' . ($ref !== '' ? " (ref: {$ref})" : '')
            );
        }

        return [
            'ok' => true,
            'message' => !empty($result['already_paid']) ? 'Already paid' : 'Marked paid',
            'order_id' => $orderId,
            'http' => 200,
        ];
    }
}
