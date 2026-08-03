<?php

namespace App\Controllers\Api;

use App\Models\Order;
use App\Models\Product;
use App\DB;
use App\Vnpay;
use App\Controllers\Api\AuthController;

class PaymentController
{
    private function getJsonInput(): array
    {
        $input = json_decode(file_get_contents('php://input'), true);
        return is_array($input) ? $input : $_POST;
    }

    private function sendResponse(array $data, int $statusCode = 200): void
    {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($statusCode);
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    private function authenticateUser(): array
    {
        $auth = new AuthController();
        $user = $auth->getAuthenticatedUser();
        if (!$user) {
            $this->sendResponse(['success' => false, 'message' => 'Token không hợp lệ hoặc đã hết hạn.'], 401);
        }
        return $user;
    }

    public function createPayment(): void
    {
        $user = $this->authenticateUser();
        $input = $this->getJsonInput();

        $paymentMethod = trim($input['payment_method'] ?? 'vnpay');
        $shippingAddress = trim($input['shipping_address'] ?? '');
        $contactAddress = trim($input['contact_address'] ?? '');
        $cart = $input['cart'] ?? [];

        if ($paymentMethod !== 'vnpay') {
            $this->sendResponse(['success' => false, 'message' => 'Phương thức thanh toán phải là vnpay.'], 400);
        }

        if ($shippingAddress === '') {
            $this->sendResponse(['success' => false, 'message' => 'Vui lòng cung cấp địa chỉ giao hàng.'], 400);
        }

        if (empty($cart) || !is_array($cart)) {
            $this->sendResponse(['success' => false, 'message' => 'Giỏ hàng không được để trống.'], 400);
        }

        $productModel = new Product();
        $cartItems = [];
        $totalAmount = 0;

        foreach ($cart as $index => $item) {
            $prodId = (int) ($item['product_id'] ?? 0);
            $qty = (int) ($item['quantity'] ?? 0);

            if ($prodId <= 0 || $qty <= 0) {
                $this->sendResponse(['success' => false, 'message' => "Sản phẩm hoặc số lượng tại index {$index} không hợp lệ."], 400);
            }

            $product = $productModel->find($prodId);
            if (!$product) {
                $this->sendResponse(['success' => false, 'message' => "Sản phẩm ID {$prodId} không tồn tại."], 404);
            }

            if ($product['status'] !== 'active') {
                $this->sendResponse(['success' => false, 'message' => "Sản phẩm '{$product['name']}' hiện không hoạt động."], 400);
            }

            if ($product['stock'] < $qty) {
                $this->sendResponse(['success' => false, 'message' => "Sản phẩm '{$product['name']}' không đủ hàng (còn {$product['stock']})."], 400);
            }

            $cartItems[] = [
                'product_id' => $prodId,
                'name' => $product['name'],
                'quantity' => $qty,
                'price' => $product['price'],
            ];

            $totalAmount += $product['price'] * $qty;
        }

        $orderModel = new Order();
        $orderData = [
            'reserve_stock' => true,
            'user_id' => $user['id'],
            'customer_name' => $user['name'],
            'phone' => $user['phone'] ?? '',
            'email' => $user['email'],
            'contact_address' => $contactAddress,
            'shipping_address' => $shippingAddress,
            'payment_method' => 'vnpay',
            'payment_status' => 'pending',
            'total_amount' => $totalAmount,
            'status' => 'pending',
        ];

        try {
            $order = $orderModel->create($orderData, $cartItems);
            
            $bankCode = trim($input['bank_code'] ?? 'NCB');
            $paymentUrl = Vnpay::createPaymentUrl($order, $bankCode);

            $this->sendResponse([
                'success' => true,
                'message' => 'Đơn hàng đã được tạo thành công.',
                'order' => [
                    'id' => $order['id'],
                    'total_amount' => $order['total_amount'],
                    'payment_method' => $order['payment_method'],
                    'payment_status' => $order['payment_status'],
                ],
                'payment_url' => $paymentUrl,
            ]);
        } catch (\Throwable $e) {
            error_log('API payment creation failed: ' . $e->getMessage());
            if ($e instanceof \RuntimeException
                && (str_contains($e->getMessage(), 'Sản phẩm') || str_contains($e->getMessage(), 'Biến thể'))
            ) {
                $this->sendResponse(['success' => false, 'message' => $e->getMessage()], 409);
            }
            $this->sendResponse(['success' => false, 'message' => 'Không thể tạo đơn hàng lúc này.'], 500);
        }
    }

    public function paymentReturn(): void
    {
        $input = $_GET;
        $orderModel = new Order();

        if (!Vnpay::verifyReturn($input)) {
            $this->sendResponse(['success' => false, 'message' => 'Xác thực chữ ký VNPay (SecureHash) thất bại.'], 400);
        }

        $vnpResponseCode = $input['vnp_ResponseCode'] ?? '';
        $txnRef = explode('_', $input['vnp_TxnRef'] ?? '')[0];
        $orderId = (int) $txnRef;
        $order = $orderModel->find($orderId);

        if (!$order) {
            $this->sendResponse(['success' => false, 'message' => 'Không tìm thấy đơn hàng tương ứng.'], 404);
        }

        if ($vnpResponseCode === '00') {
            if ($order['payment_status'] !== 'paid') {
                $orderModel->update($orderId, [
                    'payment_status' => 'paid',
                    'status' => 'confirmed',
                    'vnpay_txn_ref' => $input['vnp_TxnRef'] ?? '',
                    'vnpay_transaction_no' => $input['vnp_TransactionNo'] ?? '',
                    'vnpay_bank_code' => $input['vnp_BankCode'] ?? '',
                    'vnpay_pay_date' => $input['vnp_PayDate'] ?? '',
                ]);
            }
            $this->sendResponse([
                'success' => true,
                'message' => 'Thanh toán thành công.',
                'order_id' => $orderId,
                'vnpay' => [
                    'txn_ref' => $input['vnp_TxnRef'] ?? '',
                    'transaction_no' => $input['vnp_TransactionNo'] ?? '',
                    'bank_code' => $input['vnp_BankCode'] ?? '',
                    'pay_date' => $input['vnp_PayDate'] ?? '',
                ]
            ]);
        } else {
            $this->sendResponse([
                'success' => false,
                'message' => 'Thanh toán thất bại hoặc đã bị hủy. Mã phản hồi: ' . $vnpResponseCode,
                'order_id' => $orderId
            ], 400);
        }
    }

    public function paymentIpn(): void
    {
        $input = $_GET;
        $orderModel = new Order();

        try {
            if (!Vnpay::verifyReturn($input)) {
                $this->sendResponse(['RspCode' => '97', 'Message' => 'Invalid signature'], 200);
            }

            $txnRefParts = explode('_', $input['vnp_TxnRef'] ?? '');
            $orderId = (int) $txnRefParts[0];
            $order = $orderModel->find($orderId);

            if (!$order) {
                $this->sendResponse(['RspCode' => '01', 'Message' => 'Order not found'], 200);
            }

            $vnpAmount = (int) ($input['vnp_Amount'] ?? 0);
            $expectedAmount = (int) $order['total_amount'] * 100;
            if ($vnpAmount !== $expectedAmount) {
                $this->sendResponse(['RspCode' => '04', 'Message' => 'Invalid amount'], 200);
            }

            if ($order['payment_status'] === 'paid') {
                $this->sendResponse(['RspCode' => '02', 'Message' => 'Order already confirmed'], 200);
            }

            $vnpResponseCode = $input['vnp_ResponseCode'] ?? '';
            if ($vnpResponseCode === '00') {
                $orderModel->update($orderId, [
                    'payment_status' => 'paid',
                    'status' => 'confirmed',
                    'vnpay_txn_ref' => $input['vnp_TxnRef'] ?? '',
                    'vnpay_transaction_no' => $input['vnp_TransactionNo'] ?? '',
                    'vnpay_bank_code' => $input['vnp_BankCode'] ?? '',
                    'vnpay_pay_date' => $input['vnp_PayDate'] ?? '',
                ]);
            } else {
                $orderModel->update($orderId, [
                    'payment_status' => 'pending',
                    'status' => 'failed',
                ]);
            }

            $this->sendResponse(['RspCode' => '00', 'Message' => 'Confirm success'], 200);
        } catch (\Throwable $e) {
            error_log('VNPay IPN processing failed: ' . $e->getMessage());
            $this->sendResponse(['RspCode' => '99', 'Message' => 'Unknown error'], 200);
        }
    }
}
