<?php 
$title = 'Thông tin tài khoản - Shop Hades';
$user = $user ?? authUser();
$ordersCount = $ordersCount ?? 0;
$walletBalance = $walletBalance ?? 0;
$vouchersCount = $vouchersCount ?? 0;
$wishlistCount = $wishlistCount ?? 0;
$addresses = $addresses ?? [];

$points = (int)($user['loyalty_points'] ?? 0);
$tier = $user['loyalty_tier'] ?? 'Standard';

$nextTier = 'Standard';
$pointsNeeded = 0;
$progressPercent = 100;
$currentTierMin = 0;
$nextTierMin = 0;

if ($points < 1000) {
    $nextTier = 'Silver';
    $currentTierMin = 0;
    $nextTierMin = 1000;
    $pointsNeeded = $nextTierMin - $points;
    $progressPercent = min(100, max(0, (int) round((($points - $currentTierMin) / ($nextTierMin - $currentTierMin)) * 100)));
} elseif ($points < 5000) {
    $nextTier = 'Gold';
    $currentTierMin = 1000;
    $nextTierMin = 5000;
    $pointsNeeded = $nextTierMin - $points;
    $progressPercent = min(100, max(0, (int) round((($points - $currentTierMin) / ($nextTierMin - $currentTierMin)) * 100)));
} elseif ($points < 10000) {
    $nextTier = 'Diamond';
    $currentTierMin = 5000;
    $nextTierMin = 10000;
    $pointsNeeded = $nextTierMin - $points;
    $progressPercent = min(100, max(0, (int) round((($points - $currentTierMin) / ($nextTierMin - $currentTierMin)) * 100)));
} else {
    $nextTier = 'Diamond';
    $pointsNeeded = 0;
    $progressPercent = 100;
}

$initial = mb_strtoupper(mb_substr($user['name'] ?? 'H', 0, 1, 'UTF-8'), 'UTF-8');
?>

<style>
.profile-dashboard-header {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    border-radius: 16px;
    color: #fff;
    padding: 2.5rem 2rem;
    position: relative;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
}
.profile-dashboard-header::after {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 350px;
    height: 350px;
    background: radial-gradient(circle, rgba(239, 68, 68, 0.15) 0%, rgba(0, 0, 0, 0) 70%);
    border-radius: 50%;
    pointer-events: none;
}
.avatar-circle {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);
    color: #fff;
    font-size: 2.2rem;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    border: 4px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 6px 16px rgba(239, 68, 68, 0.3);
}
.stat-card-widget {
    background: #ffffff;
    border-radius: 14px;
    padding: 1.25rem;
    border: 1px solid #e2e8f0;
    transition: all 0.25s ease;
    text-decoration: none;
    color: inherit;
    display: flex;
    align-items: center;
    gap: 1rem;
}
.stat-card-widget:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.06);
    border-color: #cbd5e1;
    color: inherit;
}
.stat-icon-wrapper {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.4rem;
    flex-shrink: 0;
}
.profile-sidebar-card {
    background: #ffffff;
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    overflow: hidden;
}
.profile-nav-link {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.85rem 1.25rem;
    color: #475569;
    font-weight: 500;
    font-size: 0.95rem;
    text-decoration: none;
    border-left: 3px solid transparent;
    transition: all 0.2s ease;
}
.profile-nav-link:hover, .profile-nav-link.active {
    background-color: #f8fafc;
    color: #ef4444;
    border-left-color: #ef4444;
    font-weight: 600;
}
.profile-nav-link i {
    font-size: 1.1rem;
    width: 24px;
    text-align: center;
}
.content-section-card {
    background: #ffffff;
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    padding: 2rem;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
}
.tier-badge-pill {
    padding: 0.4rem 1rem;
    border-radius: 30px;
    font-weight: 700;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
}
.tier-standard { background: linear-gradient(135deg, #64748b, #475569); color: white; }
.tier-silver { background: linear-gradient(135deg, #94a3b8, #64748b); color: white; }
.tier-gold { background: linear-gradient(135deg, #f59e0b, #d97706); color: white; text-shadow: 0 1px 2px rgba(0,0,0,0.2); }
.tier-diamond { background: linear-gradient(135deg, #06b6d4, #2563eb); color: white; box-shadow: 0 4px 12px rgba(6, 182, 212, 0.3); }

.address-card-item {
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 1.25rem;
    transition: all 0.2s ease;
    background: #ffffff;
}
.address-card-item.is-default {
    border-color: #ef4444;
    background: #fef2f2;
}
</style>

<div class="container py-4">
    <!-- Header Banner -->
    <div class="profile-dashboard-header mb-4">
        <div class="row align-items-center g-3">
            <div class="col-auto">
                <div class="avatar-circle">
                    <?= e($initial) ?>
                </div>
            </div>
            <div class="col">
                <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                    <h3 class="mb-0 fw-bold text-white"><?= e($user['name']) ?></h3>
                    <?php
                    $tClass = 'tier-standard';
                    if (strcasecmp($tier, 'Silver') === 0) $tClass = 'tier-silver';
                    elseif (strcasecmp($tier, 'Gold') === 0) $tClass = 'tier-gold';
                    elseif (strcasecmp($tier, 'Diamond') === 0) $tClass = 'tier-diamond';
                    ?>
                    <span class="tier-badge-pill <?= $tClass ?>">
                        ⭐ <?= e($tier) ?>
                    </span>
                </div>
                <div class="d-flex align-items-center gap-3 text-slate-300 small flex-wrap opacity-90">
                    <span><i class="bi bi-envelope me-1"></i> <?= e($user['email']) ?></span>
                    <?php if (!empty($user['phone'])): ?>
                        <span><i class="bi bi-telephone me-1"></i> <?= e($user['phone']) ?></span>
                    <?php endif; ?>
                    <span><i class="bi bi-award me-1"></i> <strong><?= number_format($points) ?></strong> điểm tích lũy</span>
                </div>
            </div>
            <div class="col-md-auto text-md-end pt-2 pt-md-0">
                <a href="/cart" class="btn btn-light btn-sm fw-bold px-3 py-2 rounded-pill text-dark me-2">
                    <i class="bi bi-cart-fill me-1 text-danger"></i> Giỏ hàng
                </a>
                <form method="post" action="/logout" class="d-inline" onsubmit="return confirm('Bạn chắc chắn muốn đăng xuất?')">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-outline-light btn-sm px-3 py-2 rounded-pill opacity-90">
                        <i class="bi bi-box-arrow-right me-1"></i> Đăng xuất
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <a href="/orders" class="stat-card-widget">
                <div class="stat-icon-wrapper bg-danger bg-opacity-10 text-danger">
                    <i class="bi bi-box-seam-fill"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">Đơn hàng</div>
                    <div class="fs-5 fw-bold text-dark"><?= number_format($ordersCount) ?></div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="/wallet" class="stat-card-widget">
                <div class="stat-icon-wrapper bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-wallet2"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">Ví Hades</div>
                    <div class="fs-5 fw-bold text-dark"><?= formatMoney($walletBalance) ?></div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="/checkout" class="stat-card-widget">
                <div class="stat-icon-wrapper bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-ticket-perforated-fill"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">Mã giảm giá</div>
                    <div class="fs-5 fw-bold text-dark"><?= number_format($vouchersCount) ?> mã</div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="/wishlist" class="stat-card-widget">
                <div class="stat-icon-wrapper bg-danger bg-opacity-10 text-danger">
                    <i class="bi bi-heart-fill"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">Yêu thích</div>
                    <div class="fs-5 fw-bold text-dark"><?= number_format($wishlistCount) ?> món</div>
                </div>
            </a>
        </div>
    </div>

    <!-- Main Section: Sidebar & Content -->
    <div class="row g-4">
        <!-- Sidebar Navigation -->
        <div class="col-lg-3">
            <div class="profile-sidebar-card shadow-sm p-2">
                <div class="px-3 py-2 text-uppercase text-muted fw-bold border-bottom" style="font-size: 11px; letter-spacing: 0.5px;">
                    Tài khoản cá nhân
                </div>
                <div class="nav flex-column nav-pills mt-1" id="profileTab" role="tablist">
                    <a class="profile-nav-link active" id="tab-info-btn" data-bs-toggle="pill" href="#tab-info" role="tab">
                        <i class="bi bi-person-gear"></i> Thông tin cá nhân
                    </a>
                    <a class="profile-nav-link" id="tab-loyalty-btn" data-bs-toggle="pill" href="#tab-loyalty" role="tab">
                        <i class="bi bi-award-fill"></i> Hạng thành viên Loyalty
                    </a>
                    <a class="profile-nav-link" id="tab-address-btn" data-bs-toggle="pill" href="#tab-address" role="tab">
                        <i class="bi bi-geo-alt-fill"></i> Sổ địa chỉ nhận hàng
                    </a>
                    <a class="profile-nav-link" id="tab-password-btn" data-bs-toggle="pill" href="#tab-password" role="tab">
                        <i class="bi bi-shield-lock-fill"></i> Đổi mật khẩu
                    </a>
                </div>
                
                <div class="px-3 py-2 text-uppercase text-muted fw-bold border-top border-bottom mt-2" style="font-size: 11px; letter-spacing: 0.5px;">
                    Lối tắt mua sắm
                </div>
                <a href="/orders" class="profile-nav-link">
                    <i class="bi bi-bag-check"></i> Đơn hàng của tôi
                </a>
                <a href="/wallet" class="profile-nav-link">
                    <i class="bi bi-credit-card-2-front"></i> Ví Hades
                </a>
                <a href="/wishlist" class="profile-nav-link">
                    <i class="bi bi-heart"></i> Danh sách yêu thích
                </a>
                <a href="/" class="profile-nav-link text-muted">
                    <i class="bi bi-house"></i> Trang chủ Shop Hades
                </a>
            </div>
        </div>

        <!-- Right Content Tab Panes -->
        <div class="col-lg-9">
            <?php if ($msg = flash('success')): ?>
                <div class="alert alert-success border-0 shadow-sm rounded-3 d-flex align-items-center gap-2 mb-3">
                    <i class="bi bi-check-circle-fill fs-5"></i>
                    <div><?= e($msg) ?></div>
                </div>
            <?php endif; ?>
            <?php if ($msg = flash('error')): ?>
                <div class="alert alert-danger border-0 shadow-sm rounded-3 d-flex align-items-center gap-2 mb-3">
                    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                    <div><?= e($msg) ?></div>
                </div>
            <?php endif; ?>

            <div class="tab-content" id="profileTabContent">
                <!-- TAB 1: Thông tin cá nhân -->
                <div class="tab-pane fade show active" id="tab-info" role="tabpanel">
                    <div class="content-section-card shadow-sm">
                        <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                            <div>
                                <h5 class="fw-bold mb-1 text-dark">Chỉnh sửa thông tin liên hệ</h5>
                                <p class="text-muted small mb-0">Cập nhật họ tên và số điện thoại liên lạc chính của bạn</p>
                            </div>
                            <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill fw-semibold">
                                <i class="bi bi-shield-check me-1"></i> Tài khoản xác thực
                            </span>
                        </div>

                        <form method="post" action="/profile">
                            <?= csrf_field() ?>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small text-muted">Họ và tên *</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-person"></i></span>
                                        <input type="text" name="name" class="form-control border-start-0" value="<?= e($user['name']) ?>" required placeholder="Nguyễn Văn A">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small text-muted">Số điện thoại liên hệ</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-telephone"></i></span>
                                        <input type="text" name="phone" class="form-control border-start-0" value="<?= e($user['phone'] ?? '') ?>" placeholder="0912345678">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold small text-muted">Địa chỉ Email đăng nhập (Không thể đổi)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
                                        <input type="email" class="form-control border-start-0 bg-light" value="<?= e($user['email']) ?>" readonly disabled>
                                        <span class="input-group-text bg-light text-success"><i class="bi bi-check-circle-fill"></i></span>
                                    </div>
                                    <div class="form-text small text-muted">Email này dùng để nhận mã OTP và thông báo đơn hàng.</div>
                                </div>
                                <div class="col-12 pt-3">
                                    <button type="submit" class="btn btn-danger px-4 py-2 fw-semibold rounded-pill">
                                        <i class="bi bi-save me-1"></i> Lưu thông tin
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- TAB 2: Loyalty & Hạng thành viên -->
                <div class="tab-pane fade" id="tab-loyalty" role="tabpanel">
                    <div class="content-section-card shadow-sm">
                        <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                            <div>
                                <h5 class="fw-bold mb-1 text-dark">Chương trình Loyalty Hades</h5>
                                <p class="text-muted small mb-0">Tích điểm nhận ưu đãi giảm giá tự động khi đặt hàng</p>
                            </div>
                            <span class="tier-badge-pill <?= $tClass ?>">
                                ⭐ <?= e($tier) ?>
                            </span>
                        </div>

                        <div class="p-4 rounded-3 bg-light mb-4 border">
                            <div class="row align-items-center g-3">
                                <div class="col-md-7 border-end-md">
                                    <div class="text-muted small fw-bold text-uppercase mb-1">Tiến trình thăng hạng</div>
                                    <?php if ($points < 10000): ?>
                                        <h6 class="fw-bold mb-2">Cần tích lũy thêm <span class="text-danger"><?= number_format($pointsNeeded) ?></span> điểm để lên hạng <strong><?= e($nextTier) ?></strong></h6>
                                        <div class="progress mb-2" style="height: 12px; border-radius: 6px;">
                                            <div class="progress-bar bg-danger progress-bar-striped progress-bar-animated" role="progressbar" style="width: <?= $progressPercent ?>%;" aria-valuenow="<?= $progressPercent ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <div class="d-flex justify-content-between small text-muted">
                                            <span><?= number_format($points) ?> điểm</span>
                                            <span>Mục tiêu: <?= number_format($nextTierMin) ?> điểm</span>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-success fw-bold fs-6">
                                            🎉 Bạn đã đạt cấp bậc cao nhất (Diamond Member)!
                                        </div>
                                        <div class="small text-muted">Hưởng trọn vẹn 10% chiết khấu tự động cho mọi đơn hàng.</div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-5 text-center">
                                    <div class="text-muted small fw-bold text-uppercase mb-1">Điểm tích lũy hiện tại</div>
                                    <div class="fs-2 fw-extrabold text-danger"><?= number_format($points) ?></div>
                                    <div class="small text-muted">1.000đ thanh toán = 1 điểm tích lũy</div>
                                </div>
                            </div>
                        </div>

                        <h6 class="fw-bold mb-3">Quyền lợi theo hạng thành viên:</h6>
                        <div class="row g-3">
                            <div class="col-6 col-md-3">
                                <div class="p-3 border rounded-3 text-center <?= strcasecmp($tier, 'Standard') === 0 ? 'border-danger bg-danger bg-opacity-10' : 'bg-light' ?>">
                                    <div class="fw-bold text-dark">Standard</div>
                                    <div class="small text-muted mt-1">0 điểm</div>
                                    <div class="mt-2 text-danger fw-bold small">0% Ưu đãi</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="p-3 border rounded-3 text-center <?= strcasecmp($tier, 'Silver') === 0 ? 'border-danger bg-danger bg-opacity-10' : 'bg-light' ?>">
                                    <div class="fw-bold text-dark">Silver</div>
                                    <div class="small text-muted mt-1">1.000 điểm</div>
                                    <div class="mt-2 text-danger fw-bold small">Giảm 2% tự động</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="p-3 border rounded-3 text-center <?= strcasecmp($tier, 'Gold') === 0 ? 'border-danger bg-danger bg-opacity-10' : 'bg-light' ?>">
                                    <div class="fw-bold text-dark">Gold</div>
                                    <div class="small text-muted mt-1">5.000 điểm</div>
                                    <div class="mt-2 text-danger fw-bold small">Giảm 5% tự động</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="p-3 border rounded-3 text-center <?= strcasecmp($tier, 'Diamond') === 0 ? 'border-danger bg-danger bg-opacity-10' : 'bg-light' ?>">
                                    <div class="fw-bold text-dark">Diamond</div>
                                    <div class="small text-muted mt-1">10.000 điểm</div>
                                    <div class="mt-2 text-danger fw-bold small">Giảm 10% tự động</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 3: Sổ địa chỉ giao hàng -->
                <div class="tab-pane fade" id="tab-address" role="tabpanel">
                    <div class="content-section-card shadow-sm mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                            <div>
                                <h5 class="fw-bold mb-1 text-dark">Sổ địa chỉ giao hàng</h5>
                                <p class="text-muted small mb-0">Quản lý địa chỉ giao hàng để đặt hàng nhanh chóng hơn</p>
                            </div>
                            <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3" data-bs-toggle="collapse" data-bs-target="#newAddressForm">
                                <i class="bi bi-plus-lg me-1"></i> Thêm địa chỉ mới
                            </button>
                        </div>

                        <!-- Collapse Add New Address Form -->
                        <div class="collapse mb-4" id="newAddressForm">
                            <div class="p-3 border rounded-3 bg-light">
                                <h6 class="fw-bold mb-3">Thêm địa chỉ giao hàng mới</h6>
                                <form method="post" action="/profile/addresses">
                                    <?= csrf_field() ?>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold small">Tên người nhận *</label>
                                            <input type="text" name="recipient" class="form-control form-control-sm" required placeholder="Nguyễn Văn A">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold small">Số điện thoại *</label>
                                            <input type="text" name="phone" class="form-control form-control-sm" required placeholder="0912345678">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold small">Nhãn gợi nhớ</label>
                                            <input type="text" name="label" class="form-control form-control-sm" placeholder="VD: Nhà riêng, Công ty">
                                        </div>
                                        <div class="col-md-8">
                                            <label class="form-label fw-semibold small">Địa chỉ chi tiết (Số nhà, đường) *</label>
                                            <input type="text" name="address_line" class="form-control form-control-sm" required placeholder="Số 1 Võng Thị">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold small">Phường/Xã</label>
                                            <input type="text" name="ward" class="form-control form-control-sm" placeholder="Phường Bưởi">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold small">Quận/Huyện</label>
                                            <input type="text" name="district" class="form-control form-control-sm" placeholder="Quận Tây Hồ">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold small">Tỉnh/Thành phố *</label>
                                            <input type="text" name="city" class="form-control form-control-sm" required placeholder="Hà Nội">
                                        </div>
                                        <div class="col-12">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="is_default" id="is_default_chk" value="1">
                                                <label class="form-check-label small" for="is_default_chk">
                                                    Đặt làm địa chỉ mặc định
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-danger btn-sm px-4 rounded-pill">Lưu địa chỉ</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Address List -->
                        <?php if (empty($addresses)): ?>
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-geo-alt fs-1 d-block mb-2 opacity-25"></i>
                                Bạn chưa lưu địa chỉ nhận hàng nào.
                            </div>
                        <?php else: ?>
                            <div class="d-flex flex-column gap-3">
                                <?php foreach ($addresses as $addr): ?>
                                    <div class="address-card-item <?= $addr['is_default'] ? 'is-default' : '' ?>">
                                        <div class="d-flex justify-content-between align-items-start gap-2">
                                            <div>
                                                <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                                    <strong class="text-dark fs-6"><?= e($addr['recipient']) ?></strong>
                                                    <?php if ($addr['label']): ?>
                                                        <span class="badge bg-secondary small"><?= e($addr['label']) ?></span>
                                                    <?php endif; ?>
                                                    <?php if ($addr['is_default']): ?>
                                                        <span class="badge bg-danger small"><i class="bi bi-check-lg me-1"></i> Mặc định</span>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="text-muted small mb-1"><i class="bi bi-telephone me-1"></i> SĐT: <?= e($addr['phone']) ?></div>
                                                <div class="text-dark small">
                                                    <i class="bi bi-geo-alt me-1 text-danger"></i>
                                                    <?= e($addr['address_line']) ?><?= $addr['ward'] ? ', ' . e($addr['ward']) : '' ?><?= $addr['district'] ? ', ' . e($addr['district']) : '' ?>, <?= e($addr['city']) ?>
                                                </div>
                                                <?php if ($addr['latitude'] && $addr['longitude']): ?>
                                                    <div class="text-success small mt-2" style="font-size: 11px;">
                                                        <i class="bi bi-check-circle-fill me-1"></i> Tọa độ GPS đã định vị: <?= round($addr['latitude'], 5) ?>, <?= round($addr['longitude'], 5) ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>

                                            <div class="d-flex gap-2">
                                                <?php if (!$addr['is_default']): ?>
                                                    <form method="post" action="/profile/addresses/<?= $addr['id'] ?>/default" class="d-inline">
                                                        <?= csrf_field() ?>
                                                        <button type="submit" class="btn btn-sm btn-outline-primary rounded-pill py-1 px-3 small">Đặt mặc định</button>
                                                    </form>
                                                <?php endif; ?>
                                                <form method="post" action="/profile/addresses/<?= $addr['id'] ?>/delete" class="d-inline" onsubmit="return confirm('Xóa địa chỉ này?');">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle p-1" style="width:30px;height:30px;" title="Xóa địa chỉ"><i class="bi bi-trash"></i></button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- TAB 4: Đổi mật khẩu & Bảo mật -->
                <div class="tab-pane fade" id="tab-password" role="tabpanel">
                    <div class="content-section-card shadow-sm">
                        <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                            <div>
                                <h5 class="fw-bold mb-1 text-dark">Đổi mật khẩu tài khoản</h5>
                                <p class="text-muted small mb-0">Giúp bảo vệ tài khoản cá nhân của bạn an toàn hơn</p>
                            </div>
                            <i class="bi bi-shield-lock fs-3 text-danger"></i>
                        </div>

                        <form method="post" action="/profile">
                            <?= csrf_field() ?>
                            <input type="hidden" name="name" value="<?= e($user['name']) ?>">
                            <input type="hidden" name="phone" value="<?= e($user['phone'] ?? '') ?>">

                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-semibold small text-muted">Mật khẩu hiện tại *</label>
                                    <input type="password" name="current_password" class="form-control" placeholder="Nhập mật khẩu hiện tại" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small text-muted">Mật khẩu mới *</label>
                                    <input type="password" name="new_password" class="form-control" placeholder="Mật khẩu mới (tối thiểu 6 ký tự)" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small text-muted">Xác nhận mật khẩu mới *</label>
                                    <input type="password" name="confirm_password" class="form-control" placeholder="Nhập lại mật khẩu mới" required>
                                </div>
                                <div class="col-12 pt-3 d-flex gap-2">
                                    <button type="submit" class="btn btn-danger px-4 py-2 fw-semibold rounded-pill">
                                        <i class="bi bi-key me-1"></i> Cập nhật mật khẩu
                                    </button>
                                    <a href="/forgot-password" class="btn btn-outline-secondary px-4 py-2 rounded-pill">Quên mật khẩu?</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
