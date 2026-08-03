<?php $title = 'Đánh giá khách hàng'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">⭐ Đánh giá khách hàng</h4>
    <div class="d-flex gap-2">
        <a href="/admin/messages" class="btn btn-sm btn-outline-secondary">💬 Tin nhắn trực tiếp</a>
        <a href="/admin/messages/reviews" class="btn btn-sm btn-primary fw-bold">⭐ Đánh giá</a>
    </div>
</div>

<!-- Search & Filter -->
<form method="get" action="/admin/messages/reviews" class="row g-2 mb-4">
    <div class="col-md-7">
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="text" name="q" class="form-control"
                   placeholder="Tìm theo tên, nội dung, sản phẩm..."
                   value="<?= e($q) ?>">
        </div>
    </div>
    <div class="col-md-3">
        <select name="rating" class="form-select">
            <option value="0" <?= $rating === 0 ? 'selected' : '' ?>>Tất cả số sao</option>
            <?php for ($i = 5; $i >= 1; $i--): ?>
                <option value="<?= $i ?>" <?= $rating === $i ? 'selected' : '' ?>>
                    <?= str_repeat('★', $i) ?><?= str_repeat('☆', 5 - $i) ?> (<?= $i ?> sao)
                </option>
            <?php endfor; ?>
        </select>
    </div>
    <div class="col-md-2 d-flex gap-2">
        <button type="submit" class="btn btn-primary w-100">Lọc</button>
        <a href="/admin/messages/reviews" class="btn btn-outline-secondary">✕</a>
    </div>
</form>

<!-- Stats row -->
<div class="row g-3 mb-4">
    <?php
    $counts = [5=>0, 4=>0, 3=>0, 2=>0, 1=>0];
    $total  = count($reviews);
    $sumRating = 0;
    foreach ($reviews as $r) {
        $rt = (int)$r['rating'];
        $counts[$rt] = ($counts[$rt] ?? 0) + 1;
        $sumRating += $rt;
    }
    $avg = $total > 0 ? round($sumRating / $total, 1) : 0;
    ?>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div style="font-size:32px;color:#f59e0b;">★</div>
            <div class="fw-bold fs-4"><?= $avg ?></div>
            <div class="text-muted small">Điểm trung bình (<?= $total ?> đánh giá)</div>
        </div>
    </div>
    <?php foreach ([5,4,3] as $star): ?>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div style="font-size:22px;color:#f59e0b;"><?= str_repeat('★', $star) ?></div>
            <div class="fw-bold fs-5"><?= $counts[$star] ?></div>
            <div class="text-muted small"><?= $star ?> sao</div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Reviews table -->
<?php if (empty($reviews)): ?>
    <div class="text-center py-5 text-muted">
        <div style="font-size:48px;">⭐</div>
        <p class="mt-3">Không tìm thấy đánh giá nào.</p>
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Sản phẩm</th>
                    <th>Người đánh giá</th>
                    <th>Sao</th>
                    <th>Nội dung</th>
                    <th>Thời gian</th>
                    <th width="80">Xóa</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reviews as $rev): ?>
                    <tr>
                        <td class="text-muted small"><?= (int)$rev['id'] ?></td>
                        <td>
                            <a href="/products/<?= (int)$rev['product_id'] ?>" target="_blank"
                               class="text-decoration-none fw-bold small">
                                <?= e($rev['product_name'] ?? 'Sản phẩm #' . $rev['product_id']) ?>
                            </a>
                        </td>
                        <td><?= e($rev['user_name']) ?></td>
                        <td>
                            <span style="color:#f59e0b;font-size:14px;">
                                <?= str_repeat('★', (int)$rev['rating']) ?><?= str_repeat('☆', 5 - (int)$rev['rating']) ?>
                            </span>
                            <span class="ms-1 badge bg-<?= $rev['rating'] >= 4 ? 'success' : ($rev['rating'] == 3 ? 'warning' : 'danger') ?>">
                                <?= $rev['rating'] ?>
                            </span>
                        </td>
                        <td class="small text-muted" style="max-width:260px;">
                            <?= e(mb_substr($rev['comment'] ?? '', 0, 120)) ?>
                            <?= strlen($rev['comment'] ?? '') > 120 ? '...' : '' ?>
                        </td>
                        <td class="small text-muted"><?= date('d/m/Y H:i', strtotime($rev['created_at'])) ?></td>
                        <td>
                            <form method="post" action="/admin/messages/reviews/<?= (int)$rev['id'] ?>/delete"
                                  onsubmit="return confirm('Xóa đánh giá này?')">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger">🗑</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <p class="text-muted small">Hiển thị <?= count($reviews) ?> đánh giá<?= ($q || $rating) ? ' (đã lọc)' : '' ?>.</p>
<?php endif; ?>
