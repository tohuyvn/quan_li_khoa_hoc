<?php
$isEdit = !empty($user);
$u = $user ?? [];
?>

<h2 class="mb-4"><?= $isEdit ? 'Sửa người dùng' : 'Thêm người dùng' ?></h2>

<div class="card">
    <div class="card-body">
        <form method="post" action="<?= $isEdit ? '/admin/users/' . (int) $user['id'] . '/update' : '/admin/users' ?>">
            <?= csrf_field() ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Họ tên *</label>
                    <input type="text" name="name" class="form-control" value="<?= old('name', $u['name'] ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email *</label>
                    <input type="email" name="email" class="form-control" value="<?= old('email', $u['email'] ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Số điện thoại</label>
                    <input type="text" name="phone" class="form-control" value="<?= old('phone', $u['phone'] ?? '') ?>">
                </div>
                <?php if (!$isEdit || ($u['role'] ?? '') !== 'admin'): ?>
                    <div class="col-md-6">
                        <label class="form-label">Vai trò</label>
                        <?php $role = old('role', $u['role'] ?? 'customer'); ?>
                        <select name="role" class="form-select">
                            <option value="customer" <?= $role === 'customer' ? 'selected' : '' ?>>Customer</option>
                            <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>Admin</option>
                        </select>
                    </div>
                <?php endif; ?>
                <?php if (!$isEdit): ?>
                    <div class="col-md-6">
                        <label class="form-label">Mật khẩu *</label>
                        <input type="password" name="password" class="form-control" required minlength="6">
                    </div>
                <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-info mb-0">
                            Để đặt lại mật khẩu, dùng nút <strong>Đặt lại MK</strong> trong danh sách user.
                            Admin không thể xem hoặc đặt mật khẩu mới trực tiếp.
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Lưu</button>
                <a href="/admin/users" class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>
</div>
