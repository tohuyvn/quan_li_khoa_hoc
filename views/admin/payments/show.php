<div class="mb-3">
    <a href="/admin/payments" class="btn btn-sm btn-outline-secondary">← Quay lại</a>
</div>

<h2 class="mb-4">Chi tiết đơn hàng #<?= $order['id'] ?></h2>

<div class="row g-4">
    <div class="col-lg-6">
        <h5 class="mb-3">Thông tin khách hàng</h5>
        <table class="table table-bordered">
            <tr><th width="160">Họ tên</th><td><?= e($order['customer_name'] ?? '') ?></td></tr>
            <tr><th>Email</th><td><?= e($order['email'] ?? '') ?></td></tr>
            <tr><th>Điện thoại</th><td><?= e($order['phone'] ?? '') ?></td></tr>
            <tr><th>Địa chỉ liên hệ</th><td><?= e($order['contact_address'] ?? '') ?></td></tr>
            <tr><th>Địa chỉ giao hàng</th><td><?= e($order['shipping_address'] ?? '') ?></td></tr>
        </table>
    </div>
    <div class="col-lg-6">
        <h5 class="mb-3">Thông tin thanh toán</h5>
        <table class="table table-bordered">
            <tr>
                <th width="160">Trạng thái</th>
                <td>
                    <?php if (($order['payment_status'] ?? '') === 'paid'): ?>
                        <span class="badge bg-success">Đã thanh toán</span>
                    <?php else: ?>
                        <span class="badge bg-warning text-dark">Chờ thanh toán</span>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th>Phương thức</th>
                <td><?= match ($order['payment_method'] ?? '') {
                    'vnpay' => 'VNPay',
                    'momo' => 'MoMo',
                    'bank_transfer' => 'Chuyển khoản VietQR (MB)',
                    'installment' => 'Trả góp',
                    default => 'Tiền mặt (COD)',
                } ?></td>
            </tr>
            <tr><th>Tổng tiền</th><td><strong class="text-danger"><?= formatMoney($order['total_amount'] ?? 0) ?></strong></td></tr>
            <tr><th>Ngày đặt</th><td><?= e($order['created_at'] ?? '') ?></td></tr>
            <?php if (($order['payment_method'] ?? '') === 'vnpay'): ?>
                <tr><th>Mã GD VNPay</th><td><?= e($order['vnpay_transaction_no'] ?? '—') ?></td></tr>
                <tr><th>Mã tham chiếu</th><td><?= e($order['vnpay_txn_ref'] ?? '—') ?></td></tr>
                <tr><th>Ngân hàng</th><td><?= e(vnpayBankName($order['vnpay_bank_code'] ?? '')) ?></td></tr>
                <tr><th>Thời gian TT</th><td><?= e($order['vnpay_pay_date'] ?? '—') ?></td></tr>
            <?php endif; ?>
        </table>
    </div>
</div>

<h5 class="mb-3 mt-2">Sản phẩm trong đơn</h5>
<div class="table-responsive">
    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Sản phẩm</th>
                <th width="70">Ảnh</th>
                <th width="80">Mã SP</th>
                <th width="100">SL</th>
                <th width="160">Đơn giá</th>
                <th width="160">Thành tiền</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($items)): ?>
                <tr><td colspan="6" class="text-center text-muted">Không có sản phẩm</td></tr>
            <?php else: ?>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= e($item['product_name'] ?? '') ?></td>
                        <td>
                            <img src="<?= e(imageUrl($item['product_image'] ?? '', 'thumb')) ?>"
                                 alt="<?= e($item['product_name'] ?? '') ?>"
                                 style="width:40px;height:40px;object-fit:contain;border-radius:4px;border:1px solid #dee2e6;background:#fff"
                                 loading="lazy"
                                 onerror="this.onerror=null;this.src='/image/logo.webp';">
                        </td>
                        <td>#<?= (int) ($item['product_id'] ?? 0) ?></td>
                        <td><?= (int) ($item['quantity'] ?? 0) ?></td>
                        <td><?= formatMoney($item['price'] ?? 0) ?></td>
                        <td><?= formatMoney(($item['price'] ?? 0) * ($item['quantity'] ?? 0)) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
