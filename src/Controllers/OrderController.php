<?php

namespace App\Controllers;

use App\Models\Order;

class OrderController
{
    public function index(): void
    {
        $user = authUser();
        if (!$user) {
            redirect('/login');
            return;
        }

        $orderModel = new Order();
        $orders = $orderModel->findByUserId((int)$user['id']);

        $search = $_GET['q'] ?? '';
        $statusFilter = $_GET['status'] ?? '';
        $statusOptions = self::statusOptions();
        if (!array_key_exists($statusFilter, $statusOptions)) {
            $statusFilter = '';
        }

        if ($search !== '') {
            $orders = array_filter($orders, function($order) use ($search) {
                $codeMatches = str_contains(strtolower($order['order_code'] ?? ''), strtolower($search));
                $idMatches = str_contains((string)$order['id'], $search);
                $nameMatches = str_contains(strtolower($order['customer_name'] ?? ''), strtolower($search));
                $amountMatches = str_contains((string)$order['total_amount'], $search);
                $phoneMatches = str_contains((string)($order['phone'] ?? ''), $search);
                return $codeMatches || $idMatches || $nameMatches || $amountMatches || $phoneMatches;
            });
        }

        if ($statusFilter !== '') {
            $orders = array_filter($orders, function($order) use ($statusFilter) {
                return ($order['status'] ?? '') === $statusFilter;
            });
        }

        view('client.orders.index', [
            'orders' => (new Order())->withItemPreviews(array_values($orders)),
            'title' => 'Đơn hàng của tôi',
            'filters' => [
                'search' => $search,
                'status' => $statusFilter
            ],
            'statusOptions' => $statusOptions,
        ]);
    }

    public static function statusOptions(): array
    {
        return [
            '' => ['label' => 'Tất cả đơn hàng', 'icon' => 'bi-receipt', 'class' => 'secondary'],
            'pending' => ['label' => 'Chờ xử lý', 'icon' => 'bi-hourglass-split', 'class' => 'warning'],
            'confirmed' => ['label' => 'Đã xác nhận', 'icon' => 'bi-check2-circle', 'class' => 'info'],
            'packing' => ['label' => 'Đang đóng gói', 'icon' => 'bi-box-seam', 'class' => 'dark'],
            'shipping' => ['label' => 'Đang giao', 'icon' => 'bi-truck', 'class' => 'primary'],
            'completed' => ['label' => 'Hoàn thành', 'icon' => 'bi-check-circle-fill', 'class' => 'success'],
            'cancelled' => ['label' => 'Đã hủy', 'icon' => 'bi-x-circle', 'class' => 'secondary'],
        ];
    }

    public function show(int $id): void
    {
        $orderModel = new Order();
        $order = $orderModel->find($id);

        if (!$order) {
            flash('error', 'Không tìm thấy đơn hàng.');
            redirect('/');
            return;
        }

        // Check if logged in user is the owner
        $user = authUser();
        $isOwner = $user && (int)$order['user_id'] === (int)$user['id'];

        // Or check if session tracking is verified
        $isTracked = isset($_SESSION['tracked_order_' . $id]) && $_SESSION['tracked_order_' . $id] === true;

        if (!$isOwner && !$isTracked) {
            flash('error', 'Bạn không có quyền xem đơn hàng này.');
            if ($user) {
                redirect('/orders');
            } else {
                redirect('/orders/track');
            }
            return;
        }

        $items = $orderModel->itemsWithProduct($id);
        $refundRequests = [];

        $db = \App\DB::connection();
        $stmtLogs = $db->prepare('SELECT * FROM order_status_logs WHERE order_id = ? ORDER BY created_at ASC');
        $stmtLogs->execute([$id]);
        $statusLogs = $stmtLogs->fetchAll();
        $store = null;

        $deviceUnits = [];
        $warrantiesByImei = [];
        try {
            $stmtUnits = $db->prepare('
                SELECT d.id, d.imei, d.status, d.product_id, p.name AS product_name
                FROM device_units d
                LEFT JOIN products p ON p.id = d.product_id
                WHERE d.order_id = ?
                ORDER BY d.product_id ASC, d.id ASC
            ');
            $stmtUnits->execute([$id]);
            $deviceUnits = $stmtUnits->fetchAll(\PDO::FETCH_ASSOC) ?: [];

            $stmtW = $db->prepare('
                SELECT imei, warranty_code, status, started_at, expired_at, product_id
                FROM warranties
                WHERE order_id = ?
                ORDER BY id ASC
            ');
            $stmtW->execute([$id]);
            foreach ($stmtW->fetchAll(\PDO::FETCH_ASSOC) ?: [] as $w) {
                $warrantiesByImei[(string) ($w['imei'] ?? '')] = $w;
            }
        } catch (\Throwable) {
            $deviceUnits = [];
            $warrantiesByImei = [];
        }

        $unitsByProduct = [];
        foreach ($deviceUnits as $unit) {
            $pid = (int) ($unit['product_id'] ?? 0);
            $unitsByProduct[$pid][] = $unit;
        }

        view('client.orders.show', [
            'order' => $order,
            'items' => $items,
            'refundRequests' => $refundRequests,
            'canRefund' => \App\RefundService::canRequestRefund($order),
            'statusLogs' => $statusLogs,
            'store' => $store,
            'deviceUnits' => $deviceUnits,
            'unitsByProduct' => $unitsByProduct,
            'warrantiesByImei' => $warrantiesByImei,
            'title' => 'Đơn hàng ' . ($order['order_code'] ?? ('#' . $order['id']))
        ]);
    }

    public function invoice(int $id): void
    {
        $orderModel = new Order();
        $order = $orderModel->find($id);
        if (!$order || empty($order['vat_invoice_issued']) || empty($order['vat_company_name'])) {
            flash('error', 'Hóa đơn VAT không tồn tại hoặc chưa được phát hành.');
            redirect('/orders');
        }

        $user = authUser();
        $isOwner = $user && (int) $order['user_id'] === (int) $user['id'];
        $isTracked = !empty($_SESSION['tracked_order_' . $id]);
        $isRecentOrder = (int) ($_SESSION['last_order_id'] ?? 0) === $id;
        if (!$isOwner && !$isTracked && !$isRecentOrder) {
            flash('error', 'Bạn không có quyền xem hóa đơn này.');
            redirect($user ? '/orders' : '/orders/track');
        }

        view('client.orders.invoice', [
            'order' => $order,
            'items' => $orderModel->itemsWithProduct($id),
            'autoPrint' => !empty($_GET['auto_print']),
            'backUrl' => '/orders/success',
        ], '');
    }

    public function trackForm(): void
    {
        view('client.orders.track', [
            'title' => 'Tra cứu đơn hàng'
        ]);
    }

    public function trackSubmit(): void
    {
        $orderCode = trim($_POST['order_code'] ?? '');
        $phone = trim($_POST['phone'] ?? '');

        if ($orderCode === '' || $phone === '') {
            flash('error', 'Vui lòng điền đầy đủ mã đơn hàng và số điện thoại.');
            redirect('/orders/track');
            return;
        }

        $orderModel = new Order();
        $order = $orderModel->findByCode($orderCode);

        if (!$order && is_numeric($orderCode)) {
            $order = $orderModel->find((int)$orderCode);
        }

        if (!$order || trim($order['phone']) !== $phone) {
            flash('error', 'Không tìm thấy đơn hàng phù hợp với thông tin cung cấp.');
            redirect('/orders/track');
            return;
        }

        // Set session verification to view this order
        $_SESSION['tracked_order_' . $order['id']] = true;

        redirect('/orders/' . $order['id']);
    }

    public function success(): void
    {
        $orderId = (int) ($_SESSION['last_order_id'] ?? 0);
        if ($orderId <= 0) {
            $orderId = (int) ($_SESSION['pending_momo_order_id'] ?? $_SESSION['pending_vnpay_order_id'] ?? 0);
        }

        $order = null;
        $items = [];
        $vnpayResponse = $_SESSION['vnpay_response'] ?? null;
        unset($_SESSION['vnpay_response']);

        if ($orderId > 0) {
            $orderModel = new Order();
            $order = $orderModel->find($orderId);
            if ($order) {
                $items = $orderModel->itemsWithProduct($orderId);
                $_SESSION['last_order_id'] = $orderId;
            }
            unset($_SESSION['pending_momo_order_id'], $_SESSION['pending_vnpay_order_id'], $_SESSION['cart']);
        }

        view('client.orders.success', [
            'order' => $order,
            'items' => $items,
            'vnpayResponse' => $vnpayResponse,
            'autoPrintInvoice' => $order
                && (int) ($_SESSION['auto_print_invoice_order_id'] ?? 0) === (int) $order['id'],
        ]);
        if ($order && (int) ($_SESSION['auto_print_invoice_order_id'] ?? 0) === (int) $order['id']) {
            unset($_SESSION['auto_print_invoice_order_id']);
        }
    }

    public function cancel(int $id): void
    {
        $user = authUser();
        $orderModel = new Order();
        $order = $orderModel->find($id);

        if (!$order) {
            flash('error', 'Không tìm thấy đơn hàng.');
            redirect('/');
            return;
        }

        $isOwner = $user && (int)$order['user_id'] === (int)$user['id'];
        $isTracked = isset($_SESSION['tracked_order_' . $id]) && $_SESSION['tracked_order_' . $id] === true;

        if (!$isOwner && !$isTracked) {
            flash('error', 'Bạn không có quyền hủy đơn hàng này.');
            redirect('/');
            return;
        }

        if ($order['status'] !== 'pending' && $order['status'] !== 'confirmed') {
            flash('error', 'Chỉ có thể hủy đơn hàng ở trạng thái Chờ xử lý hoặc Đã xác nhận.');
            redirect('/orders/' . $id);
            return;
        }

        $result = \App\OrderStateService::transition($id, 'cancelled', 'customer', $user ? (int)$user['id'] : null, 'Khách hàng hủy đơn hàng');
        if ($result['ok']) {
            if ($order['payment_status'] === 'paid') {
                flash('success', 'Hủy đơn thành công! Tiền đã được hoàn về Ví Hades.');
            } else {
                flash('success', 'Hủy đơn hàng thành công!');
            }
        } else {
            flash('error', $result['error'] ?? 'Không thể hủy đơn hàng.');
        }
        redirect('/orders/' . $id);
    }

    public function refund(int $id): void
    {
        $user = authUser();
        $orderModel = new Order();
        $order = $orderModel->find($id);

        if (!$order) {
            flash('error', 'Không tìm thấy đơn hàng.');
            redirect('/');
            return;
        }

        $isOwner = $user && (int)$order['user_id'] === (int)$user['id'];
        $isTracked = isset($_SESSION['tracked_order_' . $id]) && $_SESSION['tracked_order_' . $id] === true;

        if (!$isOwner && !$isTracked) {
            flash('error', 'Bạn không có quyền yêu cầu hoàn tiền cho đơn hàng này.');
            redirect('/');
            return;
        }

        $method = trim($_POST['refund_method'] ?? 'wallet');
        $userId = $user ? (int) $user['id'] : (int) ($order['user_id'] ?? 0);

        try {
            if (!\App\RefundService::canRequestRefund($order)) {
                flash('error', 'Đơn hàng này không đủ điều kiện hoàn tiền hoặc đã có yêu cầu đang chờ duyệt.');
                redirect('/orders/' . $id);
                return;
            }

            \App\RefundService::createRequest($order, $userId, $method);

            $methodLabel = $method === 'voucher' ? 'Voucher giảm giá' : 'Ví Hades';
            flash('success', "Yêu cầu hoàn tiền qua {$methodLabel} đã được gửi. Admin sẽ duyệt trong thời gian sớm nhất.");
        } catch (\Throwable $e) {
            flash('error', $e->getMessage());
        }

        redirect('/orders/' . $id);
    }
}
