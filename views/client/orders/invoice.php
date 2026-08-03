<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hóa đơn <?= e($order['vat_invoice_no'] ?? '') ?></title>
    <style>
        :root { --navy: #12366d; --blue: #1f5ca9; --ink: #172033; --muted: #64748b; --line: #dce4ef; --soft: #f5f8fc; --success: #16834b; }
        * { box-sizing: border-box; }
        body { margin: 0; background: radial-gradient(circle at top, #f8fbff 0, #e9eff7 62%); color: var(--ink); font: 14px/1.5 Inter, ui-sans-serif, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; }
        .toolbar { width: min(210mm, calc(100% - 32px)); margin: 20px auto 0; display: flex; align-items: center; justify-content: space-between; gap: 12px; }
        .toolbar-note { color: var(--muted); font-size: 12px; }
        .toolbar-actions { display: flex; gap: 10px; }
        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; min-height: 42px; border: 0; border-radius: 11px; padding: 10px 17px; cursor: pointer; text-decoration: none; font: inherit; font-weight: 750; transition: transform .18s ease, box-shadow .18s ease, background .18s ease; }
        .btn:hover { transform: translateY(-1px); }
        .btn-back { background: #fff; color: #334155; border: 1px solid #cfd9e7; }
        .btn-print { background: linear-gradient(135deg, var(--blue), var(--navy)); color: #fff; box-shadow: 0 8px 20px rgba(18, 54, 109, .2); }
        .invoice { position: relative; overflow: hidden; width: 210mm; min-height: 297mm; margin: 18px auto 32px; padding: 16mm 17mm; background: #fff; box-shadow: 0 18px 55px rgba(15, 35, 70, .14); }
        .invoice::before { content: ''; position: absolute; inset: 0 0 auto; height: 7px; background: linear-gradient(90deg, var(--navy), #2c73c7 58%, #77a8df); }
        .header { display: flex; justify-content: space-between; align-items: flex-start; gap: 28px; padding-bottom: 20px; border-bottom: 1px solid var(--line); }
        .brand-block { display: flex; align-items: center; gap: 12px; }
        .brand-mark { width: 44px; height: 44px; display: grid; place-items: center; border-radius: 13px; color: #fff; background: linear-gradient(145deg, var(--blue), var(--navy)); box-shadow: 0 7px 16px rgba(18, 54, 109, .2); font-size: 20px; font-weight: 900; }
        .brand { color: var(--navy); font-size: 21px; font-weight: 850; letter-spacing: -.03em; }
        .brand-meta { margin-top: 2px; color: var(--muted); font-size: 11px; }
        .invoice-title { text-align: right; }
        .invoice-title h1 { margin: 0 0 8px; color: var(--ink); font-size: 22px; line-height: 1.2; letter-spacing: -.025em; }
        .invoice-status { display: inline-flex; align-items: center; gap: 6px; margin-bottom: 9px; padding: 5px 9px; border-radius: 999px; color: var(--success); background: #ecfdf3; font-size: 10px; font-weight: 800; letter-spacing: .035em; text-transform: uppercase; }
        .invoice-meta { color: var(--muted); font-size: 12px; }
        .invoice-meta strong { color: var(--ink); }
        .muted { color: var(--muted); }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin: 22px 0; }
        .box { min-height: 145px; padding: 16px 17px; border: 1px solid var(--line); border-radius: 13px; background: linear-gradient(145deg, #fff, var(--soft)); }
        .box h2 { display: flex; align-items: center; gap: 8px; margin: 0 0 12px; color: var(--navy); font-size: 11px; font-weight: 850; letter-spacing: .08em; text-transform: uppercase; }
        .box h2::before { content: ''; width: 18px; height: 3px; border-radius: 99px; background: #2c73c7; }
        .box p { display: grid; grid-template-columns: 92px 1fr; gap: 7px; margin: 6px 0; font-size: 12px; }
        .box p strong { color: var(--muted); font-weight: 650; }
        table { width: 100%; border-collapse: separate; border-spacing: 0; }
        .items-table { overflow: hidden; margin-top: 4px; border: 1px solid var(--line); border-radius: 12px; }
        .items-table th, .items-table td { padding: 11px 12px; text-align: left; }
        .items-table th { color: #fff; background: var(--navy); border: 0; font-size: 10px; font-weight: 800; letter-spacing: .045em; text-transform: uppercase; }
        .items-table td { border: 0; border-bottom: 1px solid #e8edf4; font-size: 12px; }
        .items-table tbody tr:nth-child(even) td { background: #f8fafc; }
        .items-table tbody tr:last-child td { border-bottom: 0; }
        .number { text-align: right !important; white-space: nowrap; }
        .totals { width: 48%; margin: 16px 0 0 auto; }
        .totals td { padding: 7px 2px 7px 12px; border: 0; border-bottom: 1px solid #e8edf4; color: var(--muted); font-size: 12px; }
        .totals td:last-child { color: var(--ink); font-weight: 650; }
        .grand-total td { padding-top: 11px; border-bottom: 0; color: var(--navy) !important; font-size: 16px; font-weight: 850 !important; }
        .footer { margin-top: 42px; display: grid; grid-template-columns: 1fr 1fr; gap: 70px; text-align: center; }
        .footer strong { color: var(--navy); font-size: 12px; }
        .signature { height: 75px; }
        .footer .muted { font-size: 10px; }
        .note { margin-top: 30px; padding: 14px 16px; border-radius: 10px; color: var(--muted); background: var(--soft); font-size: 10px; text-align: center; }
        @media (max-width: 820px) {
            .toolbar { align-items: flex-start; }
            .toolbar-note { display: none; }
            .invoice { width: calc(100% - 24px); min-height: 0; margin: 14px auto 24px; padding: 28px 20px; }
            .header { flex-direction: column; }
            .invoice-title { width: 100%; text-align: left; }
            .grid { grid-template-columns: 1fr; }
            .box { min-height: 0; }
            .items-table { overflow-x: auto; }
            .items-table table { min-width: 600px; }
            .totals { width: 100%; }
        }
        @media (max-width: 480px) {
            .toolbar { width: calc(100% - 24px); }
            .btn { padding: 9px 12px; font-size: 12px; }
            .invoice { width: calc(100% - 16px); margin-top: 10px; padding: 25px 15px; }
            .brand-mark { width: 39px; height: 39px; }
            .invoice-title h1 { font-size: 19px; }
            .box p { grid-template-columns: 80px 1fr; }
            .footer { gap: 20px; }
        }
        @page { size: A4; margin: 0; }
        @media print {
            body { background: #fff; }
            .toolbar { display: none; }
            .invoice { margin: 0; padding: 16mm 17mm; box-shadow: none; width: 210mm; min-height: 297mm; }
            .invoice::before, .brand-mark, .invoice-status, .items-table th { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>
<?php
$itemsSubtotal = 0;
foreach ($items as $item) {
    $itemsSubtotal += (int) ($item['price'] ?? 0) * (int) ($item['quantity'] ?? 1);
}
$shippingFee = (int) ($order['shipping_fee'] ?? 0);
$discount = (int) ($order['discount_amount'] ?? 0);
$walletAmount = (int) ($order['wallet_amount'] ?? 0);
?>
<div class="toolbar">
    <span class="toolbar-note">Bản xem trước hóa đơn điện tử</span>
    <div class="toolbar-actions">
        <a class="btn btn-back" href="<?= e($backUrl ?? '/') ?>">← Quay lại</a>
        <button class="btn btn-print" type="button" onclick="window.print()">In hóa đơn</button>
    </div>
</div>

<main class="invoice">
    <header class="header">
        <div class="brand-block">
            <div class="brand-mark">H</div>
            <div>
                <div class="brand"><?= e(config('app_name', 'Shop Hades')) ?></div>
                <div class="brand-meta">Hotline: <?= e(config('hotline', '1800 1234')) ?></div>
                <div class="brand-meta">Hệ thống bán lẻ thiết bị công nghệ</div>
            </div>
        </div>
        <div class="invoice-title">
            <div class="invoice-status">✓ Đã phát hành</div>
            <h1>HÓA ĐƠN GIÁ TRỊ GIA TĂNG</h1>
            <div class="invoice-meta"><strong>Số:</strong> <?= e($order['vat_invoice_no'] ?? '') ?></div>
            <div class="invoice-meta"><strong>Ngày phát hành:</strong> <?= date('d/m/Y H:i', strtotime($order['vat_invoice_issued_at'] ?? $order['created_at'])) ?></div>
        </div>
    </header>

    <section class="grid">
        <div class="box">
            <h2>Thông tin đơn hàng</h2>
            <p><strong>Mã đơn:</strong> <?= e($order['order_code'] ?? ('#' . $order['id'])) ?></p>
            <p><strong>Khách hàng:</strong> <?= e($order['customer_name'] ?? '') ?></p>
            <p><strong>Điện thoại:</strong> <?= e($order['phone'] ?? '') ?></p>
            <p><strong>Địa chỉ giao:</strong> <?= e($order['shipping_address'] ?? $order['contact_address'] ?? '') ?></p>
        </div>
        <div class="box">
            <h2>Thông tin người nhận hóa đơn</h2>
            <p><strong>Người mua hàng:</strong> <?= e($order['vat_company_name'] ?? $order['customer_name'] ?? '') ?></p>
            <?php if (!empty($order['vat_tax_code'])): ?>
                <p><strong>Mã số thuế:</strong> <?= e($order['vat_tax_code']) ?></p>
            <?php endif; ?>
            <p><strong>Địa chỉ:</strong> <?= e($order['vat_address'] ?? '') ?></p>
            <p><strong>Email:</strong> <?= e($order['vat_email'] ?? '') ?></p>
        </div>
    </section>

    <div class="items-table">
        <table>
            <thead>
                <tr><th style="width:45px">STT</th><th>Sản phẩm</th><th class="number">SL</th><th class="number">Đơn giá</th><th class="number">Thành tiền</th></tr>
            </thead>
            <tbody>
            <?php foreach ($items as $index => $item): ?>
                <?php $lineTotal = (int) ($item['price'] ?? 0) * (int) ($item['quantity'] ?? 1); ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><strong><?= e($item['product_name'] ?? 'Sản phẩm') ?></strong></td>
                    <td class="number"><?= (int) ($item['quantity'] ?? 1) ?></td>
                    <td class="number"><?= formatMoney((int) ($item['price'] ?? 0)) ?></td>
                    <td class="number"><?= formatMoney($lineTotal) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <table class="totals">
        <tr><td>Tạm tính</td><td class="number"><?= formatMoney($itemsSubtotal) ?></td></tr>
        <?php if ($shippingFee > 0): ?><tr><td>Phí giao hàng</td><td class="number"><?= formatMoney($shippingFee) ?></td></tr><?php endif; ?>
        <?php if ($discount > 0): ?><tr><td>Giảm giá</td><td class="number">-<?= formatMoney($discount) ?></td></tr><?php endif; ?>
        <?php if ($walletAmount > 0): ?><tr><td>Thanh toán bằng Ví Hades</td><td class="number">-<?= formatMoney($walletAmount) ?></td></tr><?php endif; ?>
        <tr><td>Thuế VAT</td><td class="number">Đã bao gồm</td></tr>
        <tr class="grand-total"><td><?= !empty($order['is_preorder']) ? 'Tổng thanh toán/cọc' : 'Tổng thanh toán' ?></td><td class="number"><?= formatMoney((int) $order['total_amount']) ?></td></tr>
    </table>

    <section class="footer">
        <div><strong>Người mua hàng</strong><div class="signature"></div><span class="muted">Ký và ghi rõ họ tên</span></div>
        <div><strong>Người bán hàng</strong><div class="signature"></div><span class="muted">Ký và ghi rõ họ tên</span></div>
    </section>
    <div class="note">Hóa đơn được phát hành tự động từ hệ thống <?= e(config('app_name', 'Shop Hades')) ?> · Vui lòng lưu lại để đối chiếu khi cần hỗ trợ.</div>
</main>

<?php if (!empty($autoPrint)): ?>
<script>
window.addEventListener('load', function () {
    setTimeout(function () { window.print(); }, 250);
});
</script>
<?php endif; ?>
</body>
</html>
