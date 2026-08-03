<h2 class="mb-4">Quản lý người dùng</h2>

<div class="mb-3">
    <a href="/admin/users/create" class="btn btn-primary">+ Thêm người dùng</a>
</div>

<div class="table-responsive">
    <table class="table table-striped table-bordered align-middle">
        <thead class="table-dark">
            <tr>
                <th width="60">ID</th>
                <th>Họ tên</th>
                <th>Email</th>
                <th>SĐT</th>
                <th width="100">Vai trò</th>
                <th width="160">Ngày tạo</th>
                <th width="160">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($users)): ?>
                <tr><td colspan="7" class="text-center">Chưa có người dùng</td></tr>
            <?php else: ?>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?= (int) $u['id'] ?></td>
                        <td><strong><?= e($u['name']) ?></strong></td>
                        <td><?= e($u['email']) ?></td>
                        <td><?= e($u['phone'] ?? '') ?></td>
                        <td>
                            <span class="badge bg-<?= ($u['role'] ?? '') === 'admin' ? 'danger' : 'primary' ?>">
                                <?= e($u['role']) ?>
                            </span>
                        </td>
                        <td class="small"><?= e($u['created_at'] ?? '') ?></td>
                        <td>
                            <a href="/admin/users/<?= (int) $u['id'] ?>/edit" class="btn btn-sm btn-warning">Sửa</a>
                            <form method="post" action="/admin/users/<?= (int) $u['id'] ?>/reset-password" class="d-inline"
                                  onsubmit="return confirm('Gửi email đặt lại mật khẩu cho <?= e($u['email']) ?>? User sẽ tự đặt mật khẩu mới.')">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-info text-white">Đặt lại MK</button>
                            </form>
                            <?php if (($u['role'] ?? '') !== 'admin'): ?>
                                <form method="post" action="/admin/users/<?= (int) $u['id'] ?>/delete" class="d-inline"
                                      onsubmit="return confirm('Xóa người dùng này?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
