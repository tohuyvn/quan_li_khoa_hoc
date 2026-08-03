<?php $title = 'Tra cứu đơn hàng'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="content-card shadow-sm p-4 rounded-3 border bg-white">
                <div class="text-center mb-4">
                    <div style="font-size: 48px; color: var(--accent);"><i class="bi bi-search"></i></div>
                    <h3 class="fw-bold mt-2" style="color:var(--accent);">Tra cứu đơn hàng</h3>
                    <p class="text-muted small">Nhập mã đơn hàng và số điện thoại mua hàng để xem trạng thái vận chuyển</p>
                </div>

                <?php if ($msg = flash('error')): ?>
                    <div class="alert-hades alert-danger mb-4"><?= e($msg) ?></div>
                <?php endif; ?>
                <?php if ($msg = flash('success')): ?>
                    <div class="alert-hades alert-success mb-4"><?= e($msg) ?></div>
                <?php endif; ?>

                <form method="post" action="/orders/track">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Mã đơn hàng *</label>
                        <input type="text" name="order_code" class="form-control" placeholder="Ví dụ: HD260630-X7K9M" required style="font-family: monospace;">
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Số điện thoại đặt hàng *</label>
                        <input type="text" name="phone" class="form-control" placeholder="Ví dụ: 0912345678" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary-hades w-100 py-2.5 fw-bold mb-3">TRA CỨU NGAY</button>
                    
                    <div class="text-center">
                        <a href="/" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left"></i> Quay lại trang chủ</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
