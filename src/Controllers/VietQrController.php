<?php

namespace App\Controllers;

use App\Models\Order;
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
            $_SESSION['last_order_id'] = $orderId;
            flash('success', 'Đơn hàng đã được thanh toán.');
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
        ]);
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

        unset($_SESSION['cart'], $_SESSION['pending_vietqr_order_id']);
        $_SESSION['last_order_id'] = $orderId;
        flash('success', 'Đã ghi nhận bạn đã chuyển khoản. Shop sẽ kiểm tra và xác nhận trong ít phút.');
        redirect('/orders/success');
    }
}
