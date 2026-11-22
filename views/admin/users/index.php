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

<div class="container-fluid py-4">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-primary bg-opacity-25 text-primary border border-primary border-opacity-25 rounded-pill px-3 py-1">
                    <i class="bi bi-people-fill me-1"></i> Customer Segmentation
                </span>
                <span class="text-white-50 small">• <?= number_format($counts['total']) ?> tài khoản người dùng</span>
            </div>
            <h1 class="h3 fw-bold text-white mb-1">Quản Lý Người Dùng & Phân Tệp Khách Hàng</h1>
            <p class="text-muted mb-0 small">Phân loại tệp học viên & khách hàng (Tiềm năng, VIP, Mới, Ngủ đông), theo dõi doanh thu tích lũy & quản lý tài khoản</p>
        </div>
        <div class="d-flex gap-2">
            <a href="/admin/users/create" class="btn-refract-action d-inline-flex align-items-center gap-2">
                <i class="bi bi-person-plus-fill"></i>
                <span>Thêm Người Dùng Mới</span>
            </a>
        </div>
    </div>

    <!-- Stat Cards (Customer Segments Summary) -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4 col-lg-2">
            <a href="/admin/users" class="refract-card p-3 d-block text-decoration-none h-100">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="small text-muted fw-semibold">Tất Cả</span>
                    <i class="bi bi-people-fill text-primary"></i>
                </div>
                <div class="fs-4 fw-bold text-white"><?= number_format($counts['total']) ?></div>
                <div class="small text-white-50" style="font-size: 11px;">Toàn bộ tài khoản</div>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <a href="/admin/users?segment=potential" class="refract-card p-3 d-block text-decoration-none h-100">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="small text-muted fw-semibold">Tiềm Năng</span>
                    <i class="bi bi-graph-up-arrow text-success"></i>
                </div>
                <div class="fs-4 fw-bold text-success"><?= number_format($counts['potential']) ?></div>
                <div class="small text-white-50" style="font-size: 11px;">Đã phát sinh đơn</div>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <a href="/admin/users?segment=vip" class="refract-card p-3 d-block text-decoration-none h-100">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="small text-muted fw-semibold">Khách VIP</span>
                    <i class="bi bi-star-fill text-warning"></i>
                </div>
                <div class="fs-4 fw-bold text-warning"><?= number_format($counts['vip']) ?></div>
                <div class="small text-white-50" style="font-size: 11px;">Chi tiêu cao / &gt;5 đơn</div>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <a href="/admin/users?segment=new" class="refract-card p-3 d-block text-decoration-none h-100">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="small text-muted fw-semibold">Khách Mới</span>
                    <i class="bi bi-person-plus-fill text-info"></i>
                </div>
                <div class="fs-4 fw-bold text-info"><?= number_format($counts['new']) ?></div>
                <div class="small text-white-50" style="font-size: 11px;">Mới tạo trong 30 ngày</div>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <a href="/admin/users?segment=inactive" class="refract-card p-3 d-block text-decoration-none h-100">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="small text-muted fw-semibold">Ngủ Đông</span>
                    <i class="bi bi-moon-stars-fill text-secondary"></i>
                </div>
                <div class="fs-4 fw-bold text-secondary"><?= number_format($counts['inactive']) ?></div>
                <div class="small text-white-50" style="font-size: 11px;">Chưa từng mua khóa học</div>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <a href="/admin/users?segment=admin" class="refract-card p-3 d-block text-decoration-none h-100">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="small text-muted fw-semibold">Quản Trị</span>
                    <i class="bi bi-shield-lock-fill text-danger"></i>
                </div>
                <div class="fs-4 fw-bold text-danger"><?= number_format($counts['admin']) ?></div>
                <div class="small text-white-50" style="font-size: 11px;">System Admin</div>
            </a>
        </div>
    </div>

    <!-- Filter Toolbar -->
    <div class="refract-card p-3 mb-4">
        <form method="get" action="/admin/users" class="row g-3 align-items-center">
            <div class="col-md-5">
                <div class="refract-search-pill w-100">
                    <i class="bi bi-search text-muted me-2"></i>
                    <input type="text" name="q" class="border-0 bg-transparent text-white w-100" style="outline: none; font-size: 0.85rem;" placeholder="Tìm theo tên, email hoặc số điện thoại..." value="<?= e($search ?? '') ?>">
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-funnel text-warning"></i>
                    <select name="segment" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">-- Tất cả tệp khách hàng --</option>
                        <option value="potential" <?= ($segmentFilter ?? '') === 'potential' ? 'selected' : '' ?>>🔥 Khách Hàng Tiềm Năng (Đã phát sinh đơn)</option>
                        <option value="vip" <?= ($segmentFilter ?? '') === 'vip' ? 'selected' : '' ?>>⭐ Khách Hàng VIP (Chi tiêu cao / >5 đơn)</option>
                        <option value="new" <?= ($segmentFilter ?? '') === 'new' ? 'selected' : '' ?>>🆕 Khách Hàng Mới (Tạo trong 30 ngày)</option>
                        <option value="inactive" <?= ($segmentFilter ?? '') === 'inactive' ? 'selected' : '' ?>>💤 Khách Hàng Ngủ Đông (Chưa từng mua)</option>
                        <option value="admin" <?= ($segmentFilter ?? '') === 'admin' ? 'selected' : '' ?>>🛡️ Quản Trị Viên (System Admin)</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3 d-flex gap-2 justify-content-end">
                <button type="submit" class="btn-refract-action btn-sm">
                    <i class="bi bi-filter me-1"></i> Lọc dữ liệu
                </button>
                <a href="/admin/users" class="btn btn-outline-secondary btn-sm px-3 rounded-pill d-inline-flex align-items-center">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Đặt lại
                </a>
            </div>
        </form>
    </div>

    <!-- Data Table Card (Refract Glass Table) -->
    <div class="refract-card p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th width="65" class="ps-4">ID</th>
                        <th width="55">Avatar</th>
                        <th>Thông Tin Tài Khoản</th>
                        <th width="210">Tệp Khách Hàng</th>
                        <th width="160" class="text-end">Đơn Hàng & Chi Tiêu</th>
                        <th width="120" class="text-center">Vai Trò</th>
                        <th width="130">Ngày Khởi Tạo</th>
                        <th width="170" class="text-end pe-4">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="text-muted py-3">
                                    <i class="bi bi-person-x fs-1 d-block mb-3 text-secondary opacity-50"></i>
                                    <h5 class="fw-bold text-white">Không tìm thấy người dùng</h5>
                                    <p class="small text-muted mb-0">Không có tài khoản nào phù hợp với điều kiện lọc hiện tại.</p>
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
                                'badge' => 'bg-secondary bg-opacity-25 text-secondary border border-secondary border-opacity-25',
                                'icon' => 'bi-person'
                            ];
                        ?>
                            <tr>
                                <td class="ps-4">
                                    <span class="text-white-50 font-monospace small">#<?= $userId ?></span>
                                </td>
                                <td>
                                    <div class="avatar-refract-pill" style="width: 36px; height: 36px; font-size: 13px; <?= $role === 'admin' ? 'border-color: #ef4444; color: #fca5a5;' : '' ?>">
                                        <?= $initial ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold text-white mb-0"><?= e($u['name']) ?></div>
                                    <div class="small text-white-50" style="font-size: 11.5px;">
                                        <i class="bi bi-envelope me-1 text-secondary"></i><?= e($u['email']) ?>
                                        <?php if (!empty($u['phone'])): ?>
                                            · <i class="bi bi-telephone me-1 text-secondary"></i><?= e($u['phone']) ?>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 rounded-pill px-2.5 py-1 small fw-semibold">
                                        <i class="bi <?= e($seg['icon']) ?> me-1"></i> <?= e($seg['name']) ?>
                                    </span>
                                    <?php if (!empty($seg['desc'])): ?>
                                        <div class="text-white-50 small mt-1" style="font-size: 10.5px;"><?= e($seg['desc']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <div class="fw-bold text-info" style="font-size: 13px;"><?= number_format($u['total_spent'] ?? 0, 0, ',', '.') ?> đ</div>
                                    <span class="badge bg-white bg-opacity-10 text-white-50 border border-white border-opacity-10 rounded-pill small fw-normal" style="font-size: 10px;">
                                        <?= number_format($u['total_orders'] ?? 0) ?> đơn hàng
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge rounded-pill <?= $role === 'admin' ? 'bg-danger bg-opacity-25 text-danger border border-danger border-opacity-25' : 'bg-primary bg-opacity-25 text-primary border border-primary border-opacity-25' ?> px-2.5 py-1 small fw-semibold">
                                        <?= $role === 'admin' ? 'Quản Trị' : 'Thành viên' ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="text-white-50 small">
                                        <i class="bi bi-calendar3 me-1 text-secondary"></i>
                                        <span><?= date('d/m/Y', strtotime($u['created_at'] ?? 'now')) ?></span>
                                    </div>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-inline-flex align-items-center gap-1.5">
                                        <a href="/admin/users/<?= $userId ?>/edit" class="btn btn-outline-warning btn-sm px-2.5 py-1 rounded-pill d-inline-flex align-items-center" title="Chỉnh sửa thông tin">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        
                                        <form method="post" action="/admin/users/<?= $userId ?>/reset-password" class="d-inline m-0"
                                              onsubmit="return confirm('Gửi email đặt lại mật khẩu cho <?= e($u['email']) ?>?')">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-outline-info btn-sm px-2.5 py-1 rounded-pill fw-semibold" style="font-size: 11px;" title="Reset mật khẩu">
                                                <i class="bi bi-key-fill me-1"></i>Reset MK
                                            </button>
                                        </form>

                                        <?php if ($role !== 'admin'): ?>
                                            <form method="post" action="/admin/users/<?= $userId ?>/delete" class="d-inline m-0"
                                                  onsubmit="return confirm('Bạn chắc chắn muốn xóa người dùng này?')">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-outline-danger btn-sm px-2.5 py-1 rounded-pill" title="Xóa tài khoản">
                                                    <i class="bi bi-trash3"></i>
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
</div>
