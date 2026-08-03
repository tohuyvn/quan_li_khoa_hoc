<?php $title = 'Ví Hades'; ?>

<div class="section-title"><h2>💳 Ví Hades</h2></div>

<p class="mb-4">
    <a href="/profile" style="color:var(--accent);text-decoration:none"><i class="bi bi-arrow-left"></i> Tài khoản</a> ·
    <a href="/orders" style="color:var(--accent);text-decoration:none">Đơn hàng của tôi</a>
</p>

<?php if ($msg = flash('success')): ?><div class="alert-hades alert-success"><?= e($msg) ?></div><?php endif; ?>
<?php if ($msg = flash('error')): ?><div class="alert-hades alert-danger"><?= e($msg) ?></div><?php endif; ?>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="content-card text-center py-4" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #fff;">
            <div class="small text-uppercase opacity-75 mb-1">Số dư khả dụng</div>
            <div style="font-size: 2rem; font-weight: 800; color: #fbbf24;"><?= formatMoney($balance) ?></div>
            <p class="small mb-0 mt-2 opacity-75">Dùng khi thanh toán tại checkout</p>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="content-card mb-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-ticket-perforated me-1"></i> Voucher cá nhân</h5>
            <?php if (empty($vouchers)): ?>
                <p class="text-muted small mb-0">Chưa có voucher. Hoàn tiền qua Voucher sẽ nhận mã tại đây.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead><tr><th>Mã</th><th>Giảm</th><th>Hạn</th><th></th></tr></thead>
                        <tbody>
                        <?php foreach ($vouchers as $v): ?>
                            <tr>
                                <td><code><?= e($v['code']) ?></code></td>
                                <td><?= (int) $v['discount_percent'] ?>%<?= $v['max_discount'] ? ' (tối đa ' . formatMoney($v['max_discount']) . ')' : '' ?></td>
                                <td class="small"><?= date('d/m/Y', strtotime($v['expires_at'])) ?></td>
                                <td><a href="/checkout" class="btn btn-sm btn-outline-danger">Dùng ngay</a></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <div class="content-card">
            <h5 class="fw-bold mb-3"><i class="bi bi-clock-history me-1"></i> Lịch sử giao dịch</h5>
            <?php if (empty($transactions)): ?>
                <p class="text-muted small mb-0">Chưa có giao dịch.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead><tr><th>Thời gian</th><th>Loại</th><th>Số tiền</th><th>Ghi chú</th></tr></thead>
                        <tbody>
                        <?php foreach ($transactions as $tx): ?>
                            <tr>
                                <td class="small"><?= date('d/m/Y H:i', strtotime($tx['created_at'])) ?></td>
                                <td>
                                    <?php if ($tx['type'] === 'credit'): ?>
                                        <span class="badge bg-success">Nhận</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Chi</span>
                                    <?php endif; ?>
                                </td>
                                <td class="<?= $tx['type'] === 'credit' ? 'text-success' : 'text-danger' ?> fw-semibold">
                                    <?= $tx['type'] === 'credit' ? '+' : '-' ?><?= formatMoney((int) $tx['amount']) ?>
                                </td>
                                <td class="small text-muted"><?= e($tx['note'] ?? '') ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
