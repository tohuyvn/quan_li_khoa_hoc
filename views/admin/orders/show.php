<style>
@media print {
    .content-card {
        border: none !important;
        padding: 0 !important;
        box-shadow: none !important;
    }
    .card {
        border: none !important;
        padding: 0 !important;
        box-shadow: none !important;
    }
    .row {
        display: flex;
        flex-direction: row;
        flex-wrap: wrap;
    }
    .col-md-5 {
        width: 42% !important;
        float: left;
    }
    .col-md-7 {
        width: 55% !important;
        float: right;
        margin-left: 3%;
    }
    .table-dark th {
        background-color: #333 !important;
        color: #fff !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
}
</style>

<div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
    <h2>Chi tiết đơn hàng <?= e($order['order_code'] ?? ('#' . $order['id'])) ?></h2>
    <div class="d-flex gap-2">
        <?php if (!empty($order['vat_invoice_issued'])): ?>
            <a href="/admin/orders/<?= (int) $order['id'] ?>/invoice?auto_print=1" class="btn btn-success fw-bold"><i class="bi bi-printer-fill me-1"></i> In hóa đơn VAT</a>
        <?php else: ?>
            <button onclick="window.print()" class="btn btn-success fw-bold"><i class="bi bi-printer-fill me-1"></i> In phiếu đơn hàng</button>
        <?php endif; ?>
        <a href="/admin/orders" class="btn btn-outline-secondary">← Quay lại danh sách</a>
    </div>
</div>

<h2 class="d-none d-print-block text-center mb-4 fw-bold">HÓA ĐƠN MUA HÀNG <?= e($order['order_code'] ?? ('#' . $order['id'])) ?></h2>

<div class="row">
    <!-- Cột trái: Thông tin đơn hàng & Khách hàng -->
    <div class="col-md-5">
        <div class="card mb-4 bg-white border rounded-3 p-4">
            <h4 class="mb-3 border-bottom pb-2" style="font-size: 16px; font-weight: 700; color: #333;">Thông tin đơn hàng</h4>
            <table class="table table-borderless table-sm small">
                <tr>
                    <td class="text-muted" width="150">Mã đơn hàng:</td>
                    <td><strong style="font-family: monospace; font-size:14px;"><?= e($order['order_code'] ?? ('#' . $order['id'])) ?></strong></td>
                </tr>
                <tr>
                    <td class="text-muted">Ngày đặt hàng:</td>
                    <td><?= date('d/m/Y H:i:s', strtotime($order['created_at'])) ?></td>
                </tr>
                <tr>
                    <td class="text-muted">Phương thức TT:</td>
                    <td><span class="text-uppercase fw-bold text-secondary"><?php
                        echo match ($order['payment_method'] ?? 'cash') {
                            'bank_transfer' => 'VietQR / CK ngân hàng',
                            'installment' => 'Trả góp',
                            default => e($order['payment_method'] ?? 'cash'),
                        };
                    ?></span></td>
                </tr>
                <tr>
                    <td class="text-muted">Trạng thái TT:</td>
                    <td>
                        <span class="badge bg-<?= ($order['payment_status'] === 'paid') ? 'success' : 'warning' ?>">
                            <?= ($order['payment_status'] === 'paid') ? 'Đã thanh toán' : 'Chưa thanh toán' ?>
                        </span>
                    </td>
                </tr>
                <?php if (!empty($order['coupon_code'])): ?>
                <tr>
                    <td class="text-muted">Mã giảm giá:</td>
                    <td><span class="badge bg-danger text-uppercase"><?= e($order['coupon_code']) ?></span></td>
                </tr>
                <tr>
                    <td class="text-muted">Số tiền giảm:</td>
                    <td class="text-danger">-<?= formatMoney($order['discount_amount']) ?></td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td class="text-muted">Tổng giá trị:</td>
                    <td><strong class="text-danger fs-6"><?= formatMoney($order['total_amount']) ?></strong></td>
                </tr>
            </table>

            <h4 class="mt-4 mb-3 border-bottom pb-2" style="font-size: 16px; font-weight: 700; color: #333;">Thông tin khách hàng</h4>
            <table class="table table-borderless table-sm small">
                <tr>
                    <td class="text-muted" width="150">Tên khách hàng:</td>
                    <td><strong><?= e($order['customer_name']) ?></strong></td>
                </tr>
                <tr>
                    <td class="text-muted">Email:</td>
                    <td><?= e($order['email']) ?></td>
                </tr>
                <tr>
                    <td class="text-muted">Số điện thoại:</td>
                    <td><?= e($order['phone']) ?></td>
                </tr>
                <tr>
                    <td class="text-muted">Địa chỉ giao hàng:</td>
                    <td><?= e($order['shipping_address'] ?? $order['contact_address'] ?? 'Không có') ?></td>
                </tr>
            </table>
        </div>

        <?php if (!empty($order['is_preorder'])): ?>
        <div class="card mb-4 bg-white border rounded-3 p-4">
            <h4 class="mb-3 border-bottom pb-2 text-danger" style="font-size: 16px; font-weight: 700;"><i class="bi bi-clock-fill"></i> Thông tin đặt trước (Pre-order)</h4>
            <table class="table table-borderless table-sm small mb-3">
                <tr>
                    <td class="text-muted" width="150">Trạng thái Pre-order:</td>
                    <td>
                        <span class="badge bg-<?= $order['preorder_status'] === 'completed' ? 'success' : ($order['preorder_status'] === 'deposit_paid' ? 'info text-dark' : 'warning text-dark') ?>">
                            <?= e(strtoupper($order['preorder_status'] ?? 'pending')) ?>
                        </span>
                    </td>
                </tr>
                <tr>
                    <td class="text-muted">Tiền cọc đã trả:</td>
                    <td class="fw-bold text-success"><?= formatMoney((int)($order['preorder_deposit_paid'] ?? 0)) ?></td>
                </tr>
            </table>

            <div class="d-flex gap-2">
                <?php if (($order['preorder_status'] ?? 'pending') === 'pending'): ?>
                    <form method="post" action="/admin/orders/<?= $order['id'] ?>/preorder-deposit" class="flex-fill">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-sm btn-info w-100 text-dark fw-bold">💵 Xác nhận đã cọc</button>
                    </form>
                <?php endif; ?>
                <?php if (($order['preorder_status'] ?? 'pending') !== 'completed'): ?>
                    <form method="post" action="/admin/orders/<?= $order['id'] ?>/preorder-complete" class="flex-fill">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-sm btn-success w-100 fw-bold">✅ Hoàn tất pre-order</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($order['vat_company_name'])): ?>
        <div class="card mb-4 bg-white border rounded-3 p-4">
            <h4 class="mb-3 border-bottom pb-2 text-primary" style="font-size: 16px; font-weight: 700;"><i class="bi bi-file-earmark-text-fill"></i> Yêu cầu hóa đơn VAT</h4>
            <table class="table table-borderless table-sm small mb-3">
                <tr>
                    <td class="text-muted" width="150">Người nhận hóa đơn:</td>
                    <td><strong><?= e($order['vat_company_name']) ?></strong></td>
                </tr>
                <tr>
                    <td class="text-muted">Mã số thuế:</td>
                    <td><?= !empty($order['vat_tax_code']) ? '<code>' . e($order['vat_tax_code']) . '</code>' : 'Không yêu cầu' ?></td>
                </tr>
                <tr>
                    <td class="text-muted">Địa chỉ:</td>
                    <td><?= e($order['vat_address']) ?></td>
                </tr>
                <tr>
                    <td class="text-muted">Email nhận HĐ:</td>
                    <td><?= e($order['vat_email']) ?></td>
                </tr>
                <tr>
                    <td class="text-muted">Trạng thái xuất HĐ:</td>
                    <td>
                        <?php if (!empty($order['vat_invoice_issued'])): ?>
                            <span class="badge bg-success">Đã xuất hóa đơn</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Chưa xuất hóa đơn</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php if (!empty($order['vat_invoice_no'])): ?>
                <tr>
                    <td class="text-muted">Số hóa đơn:</td>
                    <td><strong class="text-dark"><?= e($order['vat_invoice_no']) ?></strong></td>
                </tr>
                <tr>
                    <td class="text-muted">Ngày xuất HĐ:</td>
                    <td><?= date('d/m/Y H:i:s', strtotime($order['vat_invoice_issued_at'])) ?></td>
                </tr>
                <?php endif; ?>
            </table>

            <?php if (empty($order['vat_invoice_issued'])): ?>
                <form method="post" action="/admin/orders/<?= $order['id'] ?>/issue-vat-invoice">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-sm btn-primary w-100 fw-bold"><i class="bi bi-file-earmark-arrow-up"></i> Xuất hóa đơn VAT</button>
                </form>
            <?php else: ?>
                <a href="/admin/orders/<?= (int) $order['id'] ?>/invoice?auto_print=1" class="btn btn-sm btn-success w-100 fw-bold">
                    <i class="bi bi-printer-fill"></i> In lại hóa đơn VAT
                </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="card bg-white border rounded-3 p-4 d-print-none">
            <h4 class="mb-3 border-bottom pb-2" style="font-size: 16px; font-weight: 700; color: #333;">Cập nhật trạng thái đơn</h4>
            <form method="post" action="/admin/orders/<?= $order['id'] ?>/status">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <?php
                    $statusLabels = [
                        'pending'   => 'Chờ xử lý',
                        'confirmed' => 'Đã xác nhận',
                        'packing'   => 'Đang đóng gói',
                        'shipping'  => 'Đang giao hàng',
                        'completed' => 'Đã hoàn thành',
                        'cancelled' => 'Đã hủy',
                    ];
                    $currentStatus = $order['status'] ?? 'pending';
                    $allowed = array_merge([$currentStatus], \App\OrderStateService::allowedNext($currentStatus));
                    $allowed = array_unique($allowed);
                    ?>
                    <select name="status" class="form-select form-select-sm">
                        <?php foreach ($allowed as $st): ?>
                            <option value="<?= e($st) ?>" <?= $currentStatus === $st ? 'selected' : '' ?>>
                                <?= e($statusLabels[$st] ?? $st) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="form-text small">Luồng chuẩn: chờ xử lý → xác nhận → đóng gói → shipper nhận giao → hoàn thành.</div>
                    <?php
                    $requiredUnitQty = 0;
                    foreach (($items ?? []) as $item) {
                        $requiredUnitQty += max(1, (int) ($item['quantity'] ?? 1));
                    }
                    $assignedUnitQty = count($deviceUnits ?? []);
                    ?>
                    <?php if ($currentStatus === 'confirmed'): ?>
                        <div class="alert alert-info py-2 px-3 small mt-2 mb-0">
                            Chuyển sang <strong>Đang đóng gói</strong> sẽ tự lấy <?= $requiredUnitQty ?> IMEI / Serial từ kho.
                            Hệ thống sẽ chặn nếu kho không đủ.
                        </div>
                    <?php elseif (in_array($currentStatus, ['packing', 'shipping'], true)): ?>
                        <div class="alert <?= $assignedUnitQty >= $requiredUnitQty ? 'alert-success' : 'alert-danger' ?> py-2 px-3 small mt-2 mb-0">
                            Đã gắn <strong><?= $assignedUnitQty ?>/<?= $requiredUnitQty ?></strong> IMEI / Serial.
                            <?= $assignedUnitQty >= $requiredUnitQty ? 'Đủ điều kiện xử lý bước tiếp theo.' : 'Không được chuyển giao khi còn thiếu.' ?>
                        </div>
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn btn-primary btn-sm w-100">Cập nhật</button>
            </form>
        </div>

        <div class="card bg-white border rounded-3 p-4 mt-3 d-print-none">
            <h4 class="mb-3 border-bottom pb-2" style="font-size: 16px; font-weight: 700;">Kho xử lý đơn</h4>
            <form method="post" action="/admin/orders/<?= (int)$order['id'] ?>/store">
                <?= csrf_field() ?>
                <select name="store_id" class="form-select form-select-sm mb-2">
                    <?php foreach (($stores ?? []) as $st): ?>
                        <option value="<?= (int)$st['id'] ?>" <?= (int)($order['store_id'] ?? 0) === (int)$st['id'] ? 'selected' : '' ?>>
                            <?= e($st['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-outline-primary btn-sm w-100">Đổi cửa hàng nguồn</button>
            </form>
        </div>

        <?php if (($order['payment_method'] ?? '') === 'bank_transfer' && ($order['payment_status'] ?? '') !== 'paid'): ?>
        <div class="card bg-white border rounded-3 p-4 mt-3 d-print-none border-danger">
            <h4 class="mb-2 border-bottom pb-2 text-danger" style="font-size: 16px; font-weight: 700;">Chờ xác nhận VietQR</h4>
            <p class="small text-muted mb-3">
                Khách chọn chuyển khoản MB · 0788415516 · Tô Quang Huy.
                Đối chiếu nội dung CK với mã đơn rồi xác nhận.
            </p>
            <form method="post" action="/admin/orders/<?= (int)$order['id'] ?>/confirm-bank-transfer"
                  onsubmit="return confirm('Xác nhận đã nhận đủ tiền chuyển khoản cho đơn này?');">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-danger btn-sm w-100 fw-bold">✅ Xác nhận đã nhận CK</button>
            </form>
        </div>
        <?php endif; ?>

        <?php if (($order['payment_method'] ?? '') === 'installment'): ?>
        <div class="card bg-white border rounded-3 p-4 mt-3 d-print-none">
            <h4 class="mb-3 border-bottom pb-2" style="font-size: 16px; font-weight: 700;">Hồ sơ trả góp</h4>
            <p class="small mb-2">Kỳ hạn: <strong><?= (int)($order['installment_months'] ?? 0) ?> tháng</strong></p>
            <p class="small mb-2">Đặt cọc: <strong><?= formatMoney((int)($order['installment_deposit_amount'] ?? 0)) ?></strong></p>
            <p class="small mb-3">Trạng thái: <span class="badge bg-warning"><?= e($order['installment_status'] ?? 'pending_review') ?></span></p>
            <form method="post" action="/admin/orders/<?= (int)$order['id'] ?>/installment" class="d-flex gap-2">
                <?= csrf_field() ?>
                <button name="installment_status" value="approved" class="btn btn-success btn-sm flex-fill">Duyệt</button>
                <button name="installment_status" value="rejected" class="btn btn-danger btn-sm flex-fill">Từ chối</button>
            </form>
        </div>
        <?php endif; ?>

        <div class="card bg-white border rounded-3 p-4 mt-3">
            <h4 class="mb-3 border-bottom pb-2" style="font-size: 16px; font-weight: 700;">IMEI gắn đơn</h4>
            <?php if (!empty($deviceUnits)): ?>
                <ul class="small mb-0">
                    <?php foreach ($deviceUnits as $u): ?>
                        <li class="font-monospace mb-1"><?= e($u['imei']) ?> <span class="badge bg-secondary"><?= e($u['status']) ?></span></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="small text-muted mb-2">Chưa gắn IMEI / Serial. Số máy sẽ được lấy tự động khi chuyển đơn sang <strong>Đang đóng gói</strong>.</p>
                <a href="/admin/device-units" class="btn btn-sm btn-outline-primary">Kiểm tra kho IMEI</a>
            <?php endif; ?>
        </div>

        <?php if (!empty($refundRequests)): ?>
        <div class="card bg-white border rounded-3 p-4 mt-3 d-print-none">
            <h4 class="mb-3 border-bottom pb-2" style="font-size: 16px; font-weight: 700;">Yêu cầu hoàn tiền</h4>
            <?php foreach ($refundRequests as $rr): ?>
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom small">
                    <div>
                        <strong><?= ($rr['method'] ?? '') === 'voucher' ? 'Voucher' : 'Ví Hades' ?></strong>
                        · <?= formatMoney((int) $rr['amount']) ?>
                        <div class="text-muted"><?= date('d/m/Y H:i', strtotime($rr['created_at'])) ?></div>
                    </div>
                    <?php if (($rr['status'] ?? '') === 'pending'): ?>
                        <a href="/admin/refunds" class="btn btn-sm btn-warning">Duyệt tại đây</a>
                    <?php else: ?>
                        <span class="badge bg-<?= $rr['status'] === 'approved' ? 'success' : 'danger' ?>">
                            <?= $rr['status'] === 'approved' ? 'Đã duyệt' : 'Từ chối' ?>
                        </span>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Cột phải: Danh sách sản phẩm trong đơn -->
    <div class="col-md-7">
        <div class="card bg-white border rounded-3 p-4">
            <h4 class="mb-3 border-bottom pb-2" style="font-size: 16px; font-weight: 700; color: #333;">Danh sách sản phẩm</h4>
            <div class="table-responsive">
                <table class="table table-striped table-bordered align-middle small">
                    <thead class="table-dark">
                        <tr>
                            <th>Sản phẩm</th>
                            <th width="100">Đơn giá</th>
                            <th width="80" class="text-center">Số lượng</th>
                            <th width="120">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item):
                            $lineTotal = (int)$item['quantity'] * (int)$item['price'];
                        ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="<?= e(imageUrl($item['product_image'] ?? ($item['image'] ?? ''), 'thumb')) ?>"
                                             alt="<?= e($item['product_name'] ?? '') ?>"
                                             style="width:40px;height:40px;object-fit:contain;border-radius:6px;border:1px solid #dee2e6;background:#fff;flex-shrink:0"
                                             loading="lazy"
                                             onerror="this.onerror=null;this.src='/image/logo.webp';">
                                        <strong><?= e($item['product_name']) ?></strong>
                                    </div>
                                </td>
                                <td><?= formatMoney($item['price']) ?></td>
                                <td class="text-center"><?= (int)$item['quantity'] ?></td>
                                <td class="fw-bold text-danger"><?= formatMoney($lineTotal) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (!empty($order['coupon_code'])):
                            $subtotal = $order['total_amount'] + $order['discount_amount'];
                        ?>
                        <tr class="table-light">
                            <td colspan="3" class="text-end fw-bold">Tạm tính:</td>
                            <td class="fw-bold"><?= formatMoney($subtotal) ?></td>
                        </tr>
                        <tr class="table-light">
                            <td colspan="3" class="text-end fw-bold text-danger">Khuyến mãi (<?= e($order['coupon_code']) ?>):</td>
                            <td class="fw-bold text-danger">-<?= formatMoney($order['discount_amount']) ?></td>
                        </tr>
                        <?php endif; ?>
                        <tr class="table-light">
                            <td colspan="3" class="text-end fw-bold">Tổng thanh toán:</td>
                            <td class="fw-bold text-danger fs-6"><?= formatMoney($order['total_amount']) ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
