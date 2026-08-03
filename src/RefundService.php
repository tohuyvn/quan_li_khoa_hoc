<?php

namespace App;

use App\Models\Order;
use App\Models\RefundRequest;
use App\Models\UserVoucher;

class RefundService
{
    public static function canRequestRefund(array $order): bool
    {
        if (($order['payment_status'] ?? '') !== 'paid') {
            return false;
        }

        $status = $order['status'] ?? '';
        if (!in_array($status, ['confirmed', 'shipping', 'completed'], true)) {
            return false;
        }

        $pending = (new RefundRequest())->findPendingByOrder((int) $order['id']);

        return $pending === null;
    }

    public static function createRequest(array $order, int $userId, string $method): array
    {
        if (!self::canRequestRefund($order)) {
            throw new \RuntimeException('Đơn hàng không đủ điều kiện hoàn tiền.');
        }

        $method = in_array($method, ['wallet', 'voucher', 'original'], true) ? $method : 'wallet';

        if ($method !== 'wallet' && $method !== 'voucher') {
            $method = 'wallet';
        }

        if (!$userId && $method !== 'original') {
            throw new \RuntimeException('Cần đăng nhập để hoàn về Ví Hades hoặc nhận Voucher.');
        }

        return (new RefundRequest())->create([
            'order_id' => (int) $order['id'],
            'user_id'  => $userId > 0 ? $userId : ($order['user_id'] ?? null),
            'method'   => $method,
            'amount'   => (int) $order['total_amount'],
        ]);
    }

    public static function approve(int $refundId, ?string $adminNote = null): bool
    {
        $refundModel = new RefundRequest();
        $refund = $refundModel->find($refundId);

        if (!$refund || ($refund['status'] ?? '') !== 'pending') {
            return false;
        }

        $orderModel = new Order();
        $order = $orderModel->find((int) $refund['order_id']);
        if (!$order) {
            return false;
        }

        $userId = (int) ($refund['user_id'] ?? $order['user_id'] ?? 0);
        $amount = (int) $refund['amount'];
        $method = $refund['method'] ?? 'wallet';

        $db = \App\DB::connection();
        $db->beginTransaction();

        try {
            $voucherCode = null;

            if ($method === 'wallet' && $userId > 0) {
                WalletService::credit(
                    $userId,
                    $amount,
                    'refund',
                    $refundId,
                    'Hoàn tiền đơn ' . ($order['order_code'] ?? ('#' . $order['id'])),
                    false
                );
            } elseif ($method === 'voucher' && $userId > 0) {
                $voucherModel = new UserVoucher();
                $voucher = $voucherModel->create([
                    'user_id'          => $userId,
                    'discount_percent' => 10,
                    'max_discount'     => $amount,
                    'expires_at'       => date('Y-m-d', strtotime('+30 days')),
                    'source_order_id'  => (int) $order['id'],
                ]);
                $voucherCode = $voucher['code'] ?? null;
            }

            $orderModel->update((int) $order['id'], [
                'status'         => 'cancelled',
                'payment_status' => 'refunded',
            ]);

            $refundModel->updateStatus($refundId, 'approved', $adminNote);

            $db->commit();

            RefundMailer::sendApproved($order, $refund, $voucherCode);

            return true;
        } catch (\Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function reject(int $refundId, ?string $adminNote = null): bool
    {
        $refundModel = new RefundRequest();
        $refund = $refundModel->find($refundId);

        if (!$refund || ($refund['status'] ?? '') !== 'pending') {
            return false;
        }

        $refundModel->updateStatus($refundId, 'rejected', $adminNote);

        $orderModel = new Order();
        $order = $orderModel->find((int) ($refund['order_id'] ?? 0));
        if ($order) {
            RefundMailer::sendRejected($order, $refund, $adminNote);
        }

        return true;
    }
}
