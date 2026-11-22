<?php

namespace App\Controllers\Admin;

use App\Models\Order;

class OrderController
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

        $search = trim($_GET['q'] ?? '');
        $statusFilter = trim($_GET['status'] ?? '');
        $paymentMethodFilter = trim($_GET['payment_method'] ?? '');
        $paymentStatusFilter = trim($_GET['payment_status'] ?? '');
        $periodFilter = trim($_GET['period'] ?? '');
        $dateFromFilter = trim($_GET['date_from'] ?? '');
        $dateToFilter = trim($_GET['date_to'] ?? '');

        $ordersList = $this->getFilteredOrders($search, $statusFilter, $paymentMethodFilter, $paymentStatusFilter, $periodFilter, $dateFromFilter, $dateToFilter);

        view('admin.orders.index', [
            'orders' => $ordersList,
            'search' => $search,
            'statusFilter' => $statusFilter,
            'paymentMethodFilter' => $paymentMethodFilter,
            'paymentStatusFilter' => $paymentStatusFilter,
            'periodFilter' => $periodFilter,
            'dateFromFilter' => $dateFromFilter,
            'dateToFilter' => $dateToFilter,
        ]);
    }

    public function exportCsv(): void
    {
        if (!isAdmin()) {
            redirect('/admin/login');
        }

        $search = trim($_GET['q'] ?? '');
        $statusFilter = trim($_GET['status'] ?? '');
        $paymentMethodFilter = trim($_GET['payment_method'] ?? '');
        $paymentStatusFilter = trim($_GET['payment_status'] ?? '');
        $periodFilter = trim($_GET['period'] ?? '');
        $dateFromFilter = trim($_GET['date_from'] ?? '');
        $dateToFilter = trim($_GET['date_to'] ?? '');

        $ordersList = $this->getFilteredOrders($search, $statusFilter, $paymentMethodFilter, $paymentStatusFilter, $periodFilter, $dateFromFilter, $dateToFilter);

        // Header for CSV download
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="orders_export_' . date('Ymd_His') . '.csv"');

        $output = fopen('php://output', 'w');
        // UTF-8 BOM for Excel compatibility
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        fputcsv($output, ['Mã đơn', 'Khách hàng', 'Email', 'SĐT', 'Địa chỉ', 'Tổng tiền (đ)', 'Thanh toán', 'Trạng thái TT', 'Trạng thái đơn', 'Ngày đặt']);

        foreach ($ordersList as $order) {
            fputcsv($output, [
                $order['id'],
                $order['customer_name'],
                $order['email'],
                $order['phone'],
                $order['shipping_address'] ?? $order['contact_address'] ?? '',
                $order['total_amount'],
                $order['payment_method'],
                $order['payment_status'],
                $order['status'],
                $order['created_at'],
            ]);
        }
        fclose($output);
        exit;
    }

    private function getFilteredOrders(
        string $search,
        string $statusFilter,
        string $paymentMethodFilter,
        string $paymentStatusFilter,
        string $periodFilter,
        string $dateFromFilter,
        string $dateToFilter
    ): array {
        $db = \App\DB::connection();
        $sql = "SELECT * FROM orders WHERE 1=1";
        $params = [];

        if ($search !== '') {
            $sql .= " AND (customer_name LIKE ? OR email LIKE ? OR id = ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = $search;
        }

        if ($statusFilter !== '') {
            $sql .= " AND status = ?";
            $params[] = $statusFilter;
        }

        if ($paymentMethodFilter !== '') {
            $sql .= " AND payment_method = ?";
            $params[] = $paymentMethodFilter;
        }

        if ($paymentStatusFilter !== '') {
            $sql .= " AND payment_status = ?";
            $params[] = $paymentStatusFilter;
        }

        if ($periodFilter !== '') {
            if ($periodFilter === 'day') {
                $sql .= " AND DATE(created_at) = CURRENT_DATE()";
            } elseif ($periodFilter === 'week') {
                $sql .= " AND YEARWEEK(created_at, 1) = YEARWEEK(CURRENT_DATE(), 1)";
            } elseif ($periodFilter === 'month') {
                $sql .= " AND YEAR(created_at) = YEAR(CURRENT_DATE()) AND MONTH(created_at) = MONTH(CURRENT_DATE())";
            } elseif ($periodFilter === 'quarter') {
                $sql .= " AND YEAR(created_at) = YEAR(CURRENT_DATE()) AND QUARTER(created_at) = QUARTER(CURRENT_DATE())";
            } elseif ($periodFilter === 'year') {
                $sql .= " AND YEAR(created_at) = YEAR(CURRENT_DATE())";
            }
        }

        if ($dateFromFilter !== '') {
            $sql .= " AND DATE(created_at) >= ?";
            $params[] = $dateFromFilter;
        }

        if ($dateToFilter !== '') {
            $sql .= " AND DATE(created_at) <= ?";
            $params[] = $dateToFilter;
        }

        $sql .= " ORDER BY created_at DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function show(int $id): void
    {
        if (!isAdmin()) {
            redirect('/admin/login');
        }

        $order = $this->orders->find($id);
        if (!$order) {
            flash('error', 'Không tìm thấy đơn hàng.');
            redirect('/admin/orders');
        }

        view('admin.orders.show', [
            'order' => $order,
            'items' => $this->orders->itemsWithProduct($id),
            'refundRequests' => [],
            'stores' => [],
            'deviceUnits' => $this->unitsForOrder($id),
        ]);
    }

    public function updateStore(int $id): void
    {
        if (!isAdmin()) {
            redirect('/admin/login');
        }
        flash('error', 'Hệ thống quản lý khóa học không sử dụng chi nhánh cửa hàng.');
        redirect('/admin/orders/' . $id);
    }

    public function updateInstallment(int $id): void
    {
        if (!isAdmin()) {
            redirect('/admin/login');
        }
        $status = trim($_POST['installment_status'] ?? '');
        if (!in_array($status, ['pending_review', 'approved', 'rejected'], true)) {
            flash('error', 'Trạng thái trả góp không hợp lệ.');
            redirect('/admin/orders/' . $id);
        }
        $fields = ['installment_status' => $status];
        if ($status === 'approved') {
            $fields['payment_status'] = 'paid';
            $fields['status'] = 'confirmed';
        }
        $this->orders->update($id, $fields);
        if ($status === 'approved') {
            \App\OrderStateService::logStatus($id, 'pending', 'confirmed', 'admin', authUser()['id'] ?? null, 'Duyệt hồ sơ trả góp');
        }
        flash('success', 'Đã cập nhật hồ sơ trả góp.');
        redirect('/admin/orders/' . $id);
    }

    private function unitsForOrder(int $orderId): array
    {
        try {
            $db = \App\DB::connection();
            $stmt = $db->prepare('SELECT * FROM device_units WHERE order_id = ? ORDER BY id ASC');
            $stmt->execute([$orderId]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Throwable) {
            return [];
        }
    }

    public function updateStatus(int $id): void
    {
        if (!isAdmin()) {
            redirect('/admin/login');
        }

        $newStatus = trim($_POST['status'] ?? '');
        $validStatuses = ['pending', 'confirmed', 'packing', 'shipping', 'completed', 'cancelled'];
        if (!in_array($newStatus, $validStatuses, true)) {
            flash('error', 'Trạng thái đơn hàng không hợp lệ.');
            redirect('/admin/orders/' . $id);
        }

        $order = $this->orders->find($id);
        if (!$order) {
            flash('error', 'Không tìm thấy đơn hàng.');
            redirect('/admin/orders');
        }

        $user = authUser();
        $result = \App\OrderStateService::transition($id, $newStatus, 'admin', $user ? (int)$user['id'] : null, 'Admin cập nhật trạng thái đơn hàng.');
        if (!$result['ok']) {
            flash('error', $result['error'] ?? 'Không thể cập nhật trạng thái.');
            redirect('/admin/orders/' . $id);
        }

        if (empty($result['unchanged']) && !empty($result['order']['email'])) {
            \App\OrderMailer::sendStatusUpdate($result['order']);
        }
        flash('success', 'Cập nhật trạng thái đơn hàng thành công!');

        redirect('/admin/orders/' . $id);
    }

    public function updatePreorderDeposit(int $id): void
    {
        if (!isAdmin()) {
            redirect('/admin/login');
        }

        $order = $this->orders->find($id);
        if (!$order) {
            flash('error', 'Không tìm thấy đơn hàng.');
            redirect('/admin/orders');
        }

        $this->orders->update($id, [
            'preorder_deposit_paid' => $order['total_amount'],
            'preorder_status' => 'deposit_paid'
        ]);

        \App\OrderStateService::logStatus($id, $order['status'], $order['status'], 'admin', authUser()['id'] ?? null, 'Xác nhận khách hàng đã thanh toán tiền đặt cọc pre-order.');

        flash('success', 'Đã cập nhật trạng thái đặt cọc thành công!');
        redirect('/admin/orders/' . $id);
    }

    public function completePreorder(int $id): void
    {
        if (!isAdmin()) {
            redirect('/admin/login');
        }

        $order = $this->orders->find($id);
        if (!$order) {
            flash('error', 'Không tìm thấy đơn hàng.');
            redirect('/admin/orders');
        }

        $this->orders->update($id, [
            'preorder_status' => 'completed',
            'payment_status' => 'paid'
        ]);

        \App\OrderStateService::logStatus($id, $order['status'], 'confirmed', 'admin', authUser()['id'] ?? null, 'Hoàn tất pre-order, đơn hàng sẵn sàng xử lý giao.');

        $this->orders->update($id, ['status' => 'confirmed']);

        flash('success', 'Đã hoàn tất pre-order thành công!');
        redirect('/admin/orders/' . $id);
    }

    public function issueVatInvoice(int $id): void
    {
        if (!isAdmin()) {
            redirect('/admin/login');
        }

        $order = $this->orders->find($id);
        if (!$order) {
            flash('error', 'Không tìm thấy đơn hàng.');
            redirect('/admin/orders');
        }

        if (empty($order['vat_company_name'])) {
            flash('error', 'Đơn hàng không có yêu cầu xuất hóa đơn VAT.');
            redirect('/admin/orders/' . $id);
        }

        $invoice = $this->orders->issueVatInvoice($id);
        $invoiceNo = (string) ($invoice['vat_invoice_no'] ?? '');

        flash('success', 'Xuất hóa đơn VAT thành công! Số hóa đơn: ' . $invoiceNo);
        redirect('/admin/orders/' . $id . '/invoice?auto_print=1');
    }

    public function invoice(int $id): void
    {
        if (!isAdmin()) {
            redirect('/admin/login');
        }

        $order = $this->orders->find($id);
        if (!$order || empty($order['vat_invoice_issued']) || empty($order['vat_company_name'])) {
            flash('error', 'Hóa đơn VAT không tồn tại hoặc chưa được phát hành.');
            redirect('/admin/orders/' . $id);
        }

        view('client.orders.invoice', [
            'order' => $order,
            'items' => $this->orders->itemsWithProduct($id),
            'autoPrint' => !empty($_GET['auto_print']),
            'backUrl' => '/admin/orders/' . $id,
        ], '');
    }

    public function confirmBankTransfer(int $id): void
    {
        if (!isAdmin()) {
            redirect('/admin/login');
        }

        $order = $this->orders->find($id);
        if (!$order) {
            flash('error', 'Không tìm thấy đơn hàng.');
            redirect('/admin/orders');
        }

        if (($order['payment_method'] ?? '') !== 'bank_transfer') {
            flash('error', 'Đơn này không phải chuyển khoản VietQR.');
            redirect('/admin/orders/' . $id);
        }

        $result = \App\PaymentService::markPaid($id);
        if (!$result['ok']) {
            flash('error', 'Không thể xác nhận thanh toán.');
            redirect('/admin/orders/' . $id);
        }

        if (empty($result['already_paid'])) {
            \App\OrderStateService::logStatus(
                $id,
                $order['status'] ?? 'pending',
                $result['order']['status'] ?? 'confirmed',
                'admin',
                authUser()['id'] ?? null,
                'Admin xác nhận đã nhận chuyển khoản VietQR'
            );
        }

        flash('success', !empty($result['already_paid'])
            ? 'Đơn đã được đánh dấu thanh toán trước đó.'
            : 'Đã xác nhận nhận chuyển khoản. Đơn chuyển sang đã thanh toán.');
        redirect('/admin/orders/' . $id);
    }
}
