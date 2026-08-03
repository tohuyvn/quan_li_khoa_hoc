<?php

namespace App\Controllers\Admin;

use App\Models\Order;

class PaymentController
{
    private Order $orders;

    public function __construct()
    {
        $this->orders = new Order();
    }

    public function index(): void
    {
        if (!isAdmin()) {
            redirect('/admin/login');
        }

        view('admin.payments.index', [
            'paidOrders' => $this->orders->paidOrders(),
            'orderSummaries' => $this->orders->summaryList(),
            'stats' => $this->orders->paymentStats(),
        ]);
    }

    public function show(int $id): void
    {
        if (!isAdmin()) {
            redirect('/admin/login');
        }

        $order = $this->orders->find($id);
        if (!$order) {
            flash('error', 'Không tìm thấy đơn hàng.');
            redirect('/admin/payments');
        }

        view('admin.payments.show', [
            'order' => $order,
            'items' => $this->orders->itemsWithProduct($id),
        ]);
    }
}
