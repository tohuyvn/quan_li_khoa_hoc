<?php
$totalUsersCount = count($users ?? []);
$adminCount = 0;
$customerCount = 0;

if (!empty($users)) {
    foreach ($users as $u) {
        if (($u['role'] ?? '') === 'admin') $adminCount++;
        else $customerCount++;
    }
}
?>

<style>
.admin-page-header {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    border-radius: 16px;
    padding: 24px 28px;
    color: #fff;
    margin-bottom: 24px;
    box-shadow: 0 10px 25px rgba(15, 23, 42, 0.12);
    position: relative;
    overflow: hidden;
}
.admin-page-header::after {
    content: '';
    position: absolute;
    right: -40px;
    top: -40px;
    width: 180px;
    height: 180px;
    background: rgba(59, 130, 246, 0.15);
    border-radius: 50%;
    filter: blur(30px);
}
.admin-stat-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 18px 20px;
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.03);
    display: flex;
    align-items: center;
    gap: 16px;
}
.admin-stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 24px rgba(15, 23, 42, 0.08);
    border-color: #cbd5e1;
}
.admin-stat-icon {
    width: 52px;
    height: 52px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    flex-shrink: 0;
}
.admin-stat-icon.blue { background: rgba(37, 99, 235, 0.1); color: #2563eb; }
.admin-stat-icon.red { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
.admin-stat-icon.purple { background: rgba(147, 51, 234, 0.1); color: #9333ea; }

.admin-stat-number {
    font-size: 1.6rem;
    font-weight: 800;
    line-height: 1.1;
    color: #0f172a;
    letter-spacing: -0.02em;
}
.admin-stat-label {
    font-size: 12.5px;
    color: #64748b;
    font-weight: 600;
    margin-top: 2px;
}

.table-custom-card {
    background: #ffffff;
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04);
    overflow: hidden;
}
.table-custom-card thead th {
    background: #f8fafc;
    color: #475569;
    font-size: 12.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    padding: 14px 16px;
    border-bottom: 2px solid #e2e8f0;
}
.user-row-item {
    transition: background 0.15s ease;
}
.user-row-item:hover {
    background-color: #f1f5f9 !important;
}

.user-avatar-circle {
    width: 42px;
    height: 42px;
    border-radius: 12px;
    background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 16px;
    flex-shrink: 0;
}
.user-avatar-circle.admin {
    background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
}

.badge-role-pill {
    padding: 5px 12px;
    border-radius: 50px;
    font-size: 11.5px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}
.badge-role-pill.admin { background: rgba(239, 68, 68, 0.15); color: #dc2626; border: 1px solid rgba(239, 68, 68, 0.3); }
.badge-role-pill.user { background: rgba(37, 99, 235, 0.15); color: #2563eb; border: 1px solid rgba(37, 99, 235, 0.3); }

.btn-action-icon {
    width: 34px;
    height: 34px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    transition: all 0.2s ease;
    border: none;
    text-decoration: none !important;
}
.btn-action-icon.edit { background: #fef3c7; color: #d97706; }
.btn-action-icon.edit:hover { background: #d97706; color: #fff; transform: translateY(-2px); }
.btn-action-icon.delete { background: #fee2e2; color: #dc2626; }
.btn-action-icon.delete:hover { background: #dc2626; color: #fff; transform: translateY(-2px); }
</style>

<!-- Header Banner -->
<div class="admin-page-header d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h2 class="h3 fw-bold mb-0 text-white"><i class="bi bi-people-fill me-2 text-primary"></i>Quản Lý Người Dùng</h2>
            <span class="badge bg-primary rounded-pill px-3 py-1 text-uppercase" style="font-size: 10px; letter-spacing: 0.05em;">User Accounts</span>
        </div>
        <p class="mb-0 text-slate-300 small" style="color: #cbd5e1;">Quản lý tài khoản thành viên, cấp quyền Admin, khôi phục mật khẩu & bảo mật tài khoản.</p>
    </div>
    <div>
        <a href="/admin/users/create" class="btn btn-primary btn-lg px-4 py-2 rounded-3 fw-bold shadow-sm d-inline-flex align-items-center gap-2" style="background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%); border: none;">
            <i class="bi bi-person-plus-fill fs-5"></i>
            <span>Thêm Người Dùng Mới</span>
        </a>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-12 col-md-4">
        <div class="admin-stat-card">
            <div class="admin-stat-icon blue"><i class="bi bi-people"></i></div>
            <div>
                <div class="admin-stat-number"><?= number_format($totalUsersCount) ?></div>
                <div class="admin-stat-label">Tổng Số Tài Khoản</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="admin-stat-card">
            <div class="admin-stat-icon red"><i class="bi bi-shield-lock-fill"></i></div>
            <div>
                <div class="admin-stat-number"><?= number_format($adminCount) ?></div>
                <div class="admin-stat-label">Tài Khoản Quản Trị (Admin)</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="admin-stat-card">
            <div class="admin-stat-icon purple"><i class="bi bi-person-badge"></i></div>
            <div>
                <div class="admin-stat-number"><?= number_format($customerCount) ?></div>
                <div class="admin-stat-label">Khách Hàng Thành Viên</div>
            </div>
        </div>
    </div>
</div>

<!-- Data Table Card -->
<div class="table-custom-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th width="60" class="text-center">ID</th>
                    <th width="60">Avatar</th>
                    <th>Họ và Tên</th>
                    <th>Email Liên Hệ</th>
                    <th>Số Điện Thoại</th>
                    <th width="130" class="text-center">Vai Trò</th>
                    <th width="150">Ngày Khởi Tạo</th>
                    <th width="180" class="text-center">Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <div class="text-muted">
                                <i class="bi bi-person-x fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                Chưa có tài khoản người dùng nào
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($users as $u):
                        $userId = (int)$u['id'];
                        $role = $u['role'] ?? 'user';
                        $initial = mb_strtoupper(mb_substr($u['name'] ?? 'U', 0, 1));
                    ?>
                        <tr class="user-row-item">
                            <td class="text-center fw-bold text-secondary">#<?= $userId ?></td>
                            <td>
                                <div class="user-avatar-circle <?= $role === 'admin' ? 'admin' : '' ?>">
                                    <?= $initial ?>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark mb-0"><?= e($u['name']) ?></div>
                            </td>
                            <td>
                                <span class="text-muted small"><i class="bi bi-envelope me-1"></i><?= e($u['email']) ?></span>
                            </td>
                            <td>
                                <?php if (!empty($u['phone'])): ?>
                                    <span class="fw-semibold text-dark small"><i class="bi bi-telephone me-1"></i><?= e($u['phone']) ?></span>
                                <?php else: ?>
                                    <span class="text-muted fst-italic small">Chưa cập nhật</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <span class="badge-role-pill <?= $role === 'admin' ? 'admin' : 'user' ?>">
                                    <i class="bi <?= $role === 'admin' ? 'bi-shield-check' : 'bi-person' ?>"></i>
                                    <?= $role === 'admin' ? 'Quản Trị Viên' : 'Khách Hàng' ?>
                                </span>
                            </td>
                            <td class="small text-muted">
                                <i class="bi bi-calendar3 me-1"></i><?= date('d/m/Y', strtotime($u['created_at'] ?? 'now')) ?>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center align-items-center gap-1">
                                    <a href="/admin/users/<?= $userId ?>/edit" class="btn-action-icon edit" title="Chỉnh sửa thông tin">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    
                                    <form method="post" action="/admin/users/<?= $userId ?>/reset-password" class="d-inline"
                                          onsubmit="return confirm('Gửi email đặt lại mật khẩu cho <?= e($u['email']) ?>?')">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-info text-white rounded-2 px-2 fw-bold" style="font-size: 11px;" title="Reset mật khẩu">
                                            <i class="bi bi-key-fill me-1"></i>Reset MK
                                        </button>
                                    </form>

                                    <?php if ($role !== 'admin'): ?>
                                        <form method="post" action="/admin/users/<?= $userId ?>/delete" class="d-inline"
                                              onsubmit="return confirm('Bạn chắc chắn muốn xóa người dùng này?')">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn-action-icon delete" title="Xóa tài khoản">
                                                <i class="bi bi-trash3-fill"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
