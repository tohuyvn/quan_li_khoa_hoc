<?php
/** @var array $items */
$items = $items ?? [];
if ($items === []) {
    return;
}
?>
<div class="mt-3">
    <h6 class="fw-bold mb-2">Sản phẩm trong đơn</h6>
    <div class="list-group list-group-flush border rounded">
        <?php foreach ($items as $item): ?>
            <div class="list-group-item d-flex align-items-center gap-3 py-2 px-3">
                <img src="<?= e(imageUrl($item['product_image'] ?? ($item['image'] ?? ''), 'thumb')) ?>"
                     alt="<?= e($item['product_name'] ?? ($item['name'] ?? '')) ?>"
                     style="width:48px;height:48px;object-fit:contain;border-radius:8px;border:1px solid #e2e8f0;background:#fff;flex-shrink:0"
                     loading="lazy"
                     onerror="this.onerror=null;this.src='/image/logo.webp';">
                <div class="flex-grow-1 small">
                    <div class="fw-semibold"><?= e($item['product_name'] ?? ($item['name'] ?? 'Sản phẩm')) ?></div>
                    <div class="text-muted">SL: <?= (int) ($item['quantity'] ?? 1) ?></div>
                </div>
                <div class="fw-bold text-danger small"><?= formatMoney($item['price'] ?? 0) ?></div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
