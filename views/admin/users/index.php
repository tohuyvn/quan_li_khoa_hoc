<?php
$counts = $segmentCounts ?? [
    'total' => count($users ?? []),
    'vip' => 0,
    'potential' => 0,
    'new' => 0,
    'inactive' => 0,
    'admin' => 0,
];
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
    padding: 16px 18px;
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.03);
    display: flex;
    align-items: center;
    gap: 14px;
    text-decoration: none;
    color: inherit;
}
.admin-stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 24px rgba(15, 23, 42, 0.08);
    border-color: #cbd5e1;
}

.admin-stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
}
.admin-stat-icon.blue { background: rgba(37, 99, 235, 0.1); color: #2563eb; }
.admin-stat-icon.success { background: rgba(16, 185, 129, 0.1); color: #10b981; }
.admin-stat-icon.warning { background: rgba(245, 158, 11, 0.1); color: #d97706; }
.admin-stat-icon.info { background: rgba(6, 182, 212, 0.1); color: #0891b2; }
.admin-stat-icon.secondary { background: rgba(100, 116, 139, 0.1); color: #64748b; }
.admin-stat-icon.red { background: rgba(239, 68, 68, 0.1); color: #ef4444; }

.admin-stat-number {
    font-size: 1.45rem;
    font-weight: 800;
    line-height: 1.1;
    color: #0f172a;
    letter-spacing: -0.02em;
}
.admin-stat-label {
    font-size: 12px;
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
    font-size: 12px;
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

.segment-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 12px;
    border-radius: 50px;
    font-size: 11.5px;
    font-weight: 700;
}

.btn-action-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
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
            <h2 class="h3 fw-bold mb-0 text-white"><i class="bi bi-people-fill me-2 text-primary"></i>Quản Lý Người Dùng & Phân Tệp Khách Hàng</h2>
            <span class="badge bg-primary rounded-pill px-3 py-1 text-uppercase" style="font-size: 10px; letter-spacing: 0.05em;">Customer Segmentation</span>
        </div>
        <p class="mb-0 text-slate-300 small" style="color: #cbd5e1;">Phân loại tệp khách hàng (Tiềm năng, VIP, Mới, Ngủ đông), theo dõi doanh thu tích lũy & quản lý tài khoản.</p>
    </div>
    <div>
        <a href="/admin/users/create" class="btn btn-primary btn-lg px-4 py-2 rounded-3 fw-bold shadow-sm d-inline-flex align-items-center gap-2" style="background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%); border: none;">
            <i class="bi bi-person-plus-fill fs-5"></i>
            <span>Thêm Người Dùng Mới</span>
        </a>
    </div>
</div>

<!-- Stat Cards (Customer Segments Summary) -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4 col-lg-2">
        <a href="/admin/users" class="admin-stat-card">
            <div class="admin-stat-icon blue"><i class="bi bi-people-fill"></i></div>
            <div>
                <div class="admin-stat-number"><?= number_format($counts['total']) ?></div>
                <div class="admin-stat-label">Tất Cả</div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <a href="/admin/users?segment=potential" class="admin-stat-card">
            <div class="admin-stat-icon success"><i class="bi bi-graph-up-arrow"></i></div>
            <div>
                <div class="admin-stat-number"><?= number_format($counts['potential']) ?></div>
                <div class="admin-stat-label">🔥 Tiềm Năng</div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <a href="/admin/users?segment=vip" class="admin-stat-card">
            <div class="admin-stat-icon warning"><i class="bi bi-star-fill"></i></div>
            <div>
                <div class="admin-stat-number"><?= number_format($counts['vip']) ?></div>
                <div class="admin-stat-label">⭐ Khách VIP</div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <a href="/admin/users?segment=new" class="admin-stat-card">
            <div class="admin-stat-icon info"><i class="bi bi-person-plus-fill"></i></div>
            <div>
                <div class="admin-stat-number"><?= number_format($counts['new']) ?></div>
                <div class="admin-stat-label">🆕 Khách Mới</div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <a href="/admin/users?segment=inactive" class="admin-stat-card">
            <div class="admin-stat-icon secondary"><i class="bi bi-moon-stars-fill"></i></div>
            <div>
                <div class="admin-stat-number"><?= number_format($counts['inactive']) ?></div>
                <div class="admin-stat-label">💤 Ngủ Đông</div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <a href="/admin/users?segment=admin" class="admin-stat-card">
            <div class="admin-stat-icon red"><i class="bi bi-shield-lock-fill"></i></div>
            <div>
                <div class="admin-stat-number"><?= number_format($counts['admin']) ?></div>
                <div class="admin-stat-label">🛡️ Quản Trị</div>
            </div>
        </a>
    </div>
</div>

<!-- Filter Toolbar (List chọn Tệp Khách hàng) -->
<div class="bg-white p-3 rounded-4 border mb-4 shadow-sm">
    <form method="get" action="/admin/users" class="row g-3">
        <div class="col-md-5">
            <label class="form-label small fw-bold text-muted mb-1"><i class="bi bi-search me-1"></i>Tìm kiếm tài khoản</label>
            <input type="text" name="q" class="form-control form-control-sm" placeholder="Tên, Email hoặc Số điện thoại..." value="<?= e($search ?? '') ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label small fw-bold text-muted mb-1"><i class="bi bi-diagram-3-fill me-1"></i>Lọc theo Tệp Khách Hàng (List Chọn)</label>
            <select name="segment" class="form-select form-select-sm fw-bold border-primary text-primary" onchange="this.form.submit()">
                <option value="">-- Tất cả tệp khách hàng --</option>
                <option value="potential" <?= ($segmentFilter ?? '') === 'potential' ? 'selected' : '' ?>>🔥 Khách Hàng Tiềm Năng (Đã phát sinh đơn)</option>
                <option value="vip" <?= ($segmentFilter ?? '') === 'vip' ? 'selected' : '' ?>>⭐ Khách Hàng VIP (Chi tiêu cao / >5 đơn)</option>
                <option value="new" <?= ($segmentFilter ?? '') === 'new' ? 'selected' : '' ?>>🆕 Khách Hàng Mới (Tạo trong 30 ngày)</option>
                <option value="inactive" <?= ($segmentFilter ?? '') === 'inactive' ? 'selected' : '' ?>>💤 Khách Hàng Ngủ Đông (Chưa từng mua)</option>
                <option value="admin" <?= ($segmentFilter ?? '') === 'admin' ? 'selected' : '' ?>>🛡️ Quản Trị Viên (System Admin)</option>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2 align-self-end">
            <button type="submit" class="btn btn-primary btn-sm flex-grow-1 fw-bold"><i class="bi bi-filter"></i> Lọc dữ liệu</button>
            <a href="/admin/users" class="btn btn-outline-secondary btn-sm"><i class="bi bi-x-circle"></i> Đặt lại</a>
        </div>
    </form>
</div>

<!-- Data Table Card -->
<div class="table-custom-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th width="60" class="text-center">ID</th>
                    <th width="50">Avatar</th>
                    <th>Thông Tin Tài Khoản</th>
                    <th width="200">Tệp Khách Hàng</th>
                    <th width="150" class="text-end">Đơn Hàng & Chi Tiêu</th>
                    <th width="120" class="text-center">Vai Trò</th>
                    <th width="130">Ngày Khởi Tạo</th>
                    <th width="160" class="text-center">Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <div class="text-muted">
                                <i class="bi bi-person-x fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                Không tìm thấy người dùng nào phù hợp với tệp khách hàng được lọc
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($users as $u):
                        $userId = (int)$u['id'];
                        $role = $u['role'] ?? 'user';
                        $initial = mb_strtoupper(mb_substr($u['name'] ?? 'U', 0, 1));
                        $seg = $u['segment'] ?? [
                            'name' => 'Khách Hàng',
                            'badge' => 'bg-secondary text-white',
                            'icon' => 'bi-person'
                        ];
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
                                <div class="small text-muted" style="font-size: 11.5px;">
                                    <i class="bi bi-envelope me-1"></i><?= e($u['email']) ?>
                                    <?php if (!empty($u['phone'])): ?>
                                        · <i class="bi bi-telephone me-1"></i><?= e($u['phone']) ?>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <span class="segment-badge <?= $seg['badge'] ?>">
                                    <i class="bi <?= $seg['icon'] ?>"></i> <?= e($seg['name']) ?>
                                </span>
                                <?php if (!empty($seg['desc'])): ?>
                                    <div class="text-muted small" style="font-size: 10.5px; margin-top: 2px;"><?= e($seg['desc']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <div class="fw-bold text-primary" style="font-size: 13px;"><?= number_format($u['total_spent'] ?? 0, 0, ',', '.') ?> đ</div>
                                <div class="badge bg-light text-dark border small fw-normal" style="font-size: 10.5px;">
                                    <?= number_format($u['total_orders'] ?? 0) ?> đơn hàng
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge rounded-pill <?= $role === 'admin' ? 'bg-danger text-white' : 'bg-primary text-white' ?> px-2 py-1" style="font-size: 11px;">
                                    <?= $role === 'admin' ? 'Admin' : 'Thành viên' ?>
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
