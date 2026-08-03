<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Cửa hàng / Kho</h2>
    <a href="/admin/stores/create" class="btn btn-primary">+ Thêm cửa hàng</a>
</div>

<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>Tên</th>
                <th>Địa chỉ</th>
                <th>Bán kính (km)</th>
                <th>Trạng thái</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($stores as $s): ?>
                <tr>
                    <td class="fw-semibold"><?= e($s['name']) ?></td>
                    <td><?= e($s['address']) ?></td>
                    <td><?= e((string)$s['service_radius_km']) ?></td>
                    <td>
                        <span class="badge bg-<?= $s['status'] === 'active' ? 'success' : 'secondary' ?>">
                            <?= e($s['status']) ?>
                        </span>
                    </td>
                    <td class="text-end">
                        <a href="/admin/stores/<?= (int)$s['id'] ?>/edit" class="btn btn-sm btn-outline-primary">Sửa / Tồn kho</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
