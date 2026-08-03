<?php
/** @var array $product Product with pricing fields (sale_price, original_price, has_discount, discount_percent) */
?>
<div class="product-price">
    <?= formatMoney((int) ($product['sale_price'] ?? $product['price'])) ?>
    <?php if (!empty($product['has_discount'])): ?>
        <small><?= formatMoney((int) $product['original_price']) ?></small>
    <?php endif; ?>
</div>
