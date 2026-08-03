<?php $title = 'Duyệt hoàn tiền'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Yêu cầu hoàn tiền chờ duyệt</h2>
    <a href="/admin/orders" class="btn btn-outline-secondary">← Đơn hàng</a>
</div>

<?php if ($msg = flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
<?php if ($msg = flash('error')): ?><div class="alert alert-danger"><?= e($msg) ?></div><?php endif; ?>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Đơn hàng</th>
                    <th>Khách</th>
                    <th>Số tiền</th>
                    <th>Phương thức</th>
                    <th>Ngày gửi</th>
                    <th class="text-end">Thao tác</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($refunds)): ?>
                <tr><td colspan="7" class="text-center text-muted py-4">Không có yêu cầu chờ duyệt.</td></tr>
            <?php else: ?>
                <?php foreach ($refunds as $r): ?>
                <tr>
                    <td><?= (int) $r['id'] ?></td>
                    <td>
                        <a href="/admin/orders/<?= (int) $r['order_id'] ?>" class="fw-bold text-decoration-none">
                            <?= e($r['order_code'] ?? ('#' . $r['order_id'])) ?>
                        </a>
                    </td>
                    <td>
                        <div class="small"><?= e($r['customer_name'] ?? '') ?></div>
                        <div class="text-muted" style="font-size:11px"><?= e($r['email'] ?? '') ?></div>
                    </td>
                    <td class="fw-bold text-danger"><?= formatMoney((int) $r['amount']) ?></td>
                    <td>
                        <?php
                        $methodLabels = ['wallet' => 'Ví Hades', 'voucher' => 'Voucher', 'original' => 'Gốc'];
                        echo e($methodLabels[$r['method'] ?? 'wallet'] ?? $r['method']);
                        ?>
                    </td>
                    <td class="small"><?= date('d/m/Y H:i', strtotime($r['created_at'])) ?></td>
                    <td class="text-end">
                        <form method="post" action="/admin/refunds/<?= (int) $r['id'] ?>/approve" class="d-inline">
                            <?= csrf_field() ?>
                            <input type="hidden" name="redirect" value="/admin/refunds">
                            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Duyệt hoàn tiền này?')">Duyệt</button>
                        </form>
                        <form method="post" action="/admin/refunds/<?= (int) $r['id'] ?>/reject" class="d-inline">
                            <?= csrf_field() ?>
                            <input type="hidden" name="redirect" value="/admin/refunds">
                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Từ chối yêu cầu này?')">Từ chối</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
