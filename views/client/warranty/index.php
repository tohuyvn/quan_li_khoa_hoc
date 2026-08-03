<div class="container py-4">
    <div class="content-card p-4 p-md-5">
        <nav class="breadcrumb-nav mb-4" style="font-size: 13px;">
            <a href="/" class="text-decoration-none text-muted">Trang chủ</a>
            <span class="text-muted"> / </span>
            <span class="text-muted">Tra cứu bảo hành</span>
        </nav>

        <div class="text-center mb-5">
            <span class="badge bg-primary-subtle text-primary px-3 py-2 mb-2 border border-primary-subtle text-uppercase fw-bold" style="font-size: 11px; letter-spacing: 1px;">Bảo hành điện tử</span>
            <h1 class="fw-bold mb-3" style="font-size: 2.2rem; letter-spacing: -0.03em;">Hệ thống Tra cứu & Yêu cầu Bảo hành</h1>
            <p class="text-muted mx-auto" style="max-width: 600px; line-height: 1.6;">
                Sau khi đơn được giao thành công, nhập số điện thoại, mã đơn, mã bảo hành hoặc IMEI để kiểm tra thời hạn và gửi yêu cầu sửa chữa.
            </p>
        </div>

        <!-- Search Bar -->
        <div class="row justify-content-center mb-5">
            <div class="col-md-8 col-lg-6">
                <form action="/warranty" method="get" class="shadow-sm rounded-4 p-2 bg-light border d-flex gap-2">
                    <div class="input-group align-items-center flex-grow-1 ps-2">
                        <span class="text-muted me-2"><i class="bi bi-search" style="font-size: 18px;"></i></span>
                        <input type="text" name="q" class="form-control border-0 bg-transparent py-2 shadow-none" 
                               placeholder="Số điện thoại, mã đơn, mã bảo hành hoặc IMEI..." 
                               value="<?= htmlspecialchars($query ?? '') ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 fw-bold">Tra cứu</button>
                </form>
            </div>
        </div>

        <?php if ($results !== null): ?>
            <div class="mt-4">
                <h4 class="fw-bold mb-4" style="letter-spacing: -0.02em;">
                    <i class="bi bi-card-checklist text-primary me-2"></i>Kết quả tìm kiếm cho: 
                    <span class="text-primary font-monospace">"<?= htmlspecialchars($query) ?>"</span>
                </h4>

                <?php if (empty($results)): ?>
                    <div class="text-center py-5 border rounded-4 bg-light-subtle">
                        <div class="text-muted mb-3" style="font-size: 3rem;"><i class="bi bi-shield-slash"></i></div>
                        <h5 class="fw-bold text-dark">Không tìm thấy thông tin bảo hành</h5>
                        <p class="text-muted mb-0 small" style="max-width: 450px; margin: 0 auto;">
                            Vui lòng kiểm tra lại thông tin. Nếu sản phẩm chưa giao thành công, bảo hành chưa được kích hoạt.
                        </p>
                    </div>
                <?php else: ?>
                    <div class="row g-4">
                        <?php foreach ($results as $w): ?>
                            <div class="col-12">
                                <div class="card border rounded-4 shadow-sm overflow-hidden mb-4 bg-light-subtle">
                                    <div class="card-header bg-white border-bottom p-4">
                                        <div class="row align-items-center g-3">
                                            <div class="col-md-7 d-flex align-items-center gap-3">
                                                <img src="<?= e(imageUrl($w['product_image'] ?? '', 'thumb')) ?>"
                                                     alt="<?= e($w['product_name'] ?? '') ?>"
                                                     class="rounded-3 border bg-white"
                                                     style="width: 70px; height: 70px; object-fit: contain; padding: 4px;"
                                                     loading="lazy"
                                                     onerror="this.onerror=null;this.src='/image/logo.webp';">
                                                <div>
                                                    <h5 class="fw-bold mb-1 text-dark"><?= htmlspecialchars($w['product_name']) ?></h5>
                                                    <div class="d-flex flex-wrap gap-2 text-muted small mt-1">
                                                        <span>Mã BH: <strong class="font-monospace text-dark"><?= htmlspecialchars($w['warranty_code']) ?></strong></span>
                                                        <?php if (!empty($w['imei'])): ?>
                                                            <span>· IMEI: <strong class="font-monospace text-dark"><?= htmlspecialchars($w['imei']) ?></strong></span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-5 text-md-end">
                                                <?php if ($w['status'] === 'active'): ?>
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 fw-semibold" style="font-size: 13px;">
                                                        <i class="bi bi-shield-fill-check me-1"></i>Còn bảo hành
                                                    </span>
                                                <?php elseif ($w['status'] === 'expired'): ?>
                                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 fw-semibold" style="font-size: 13px;">
                                                        <i class="bi bi-shield-fill-x me-1"></i>Hết hạn bảo hành
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-2 fw-semibold" style="font-size: 13px;">
                                                        <i class="bi bi-shield-fill-exclamation me-1"></i>Bị hủy (Void)
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="card-body p-4">
                                        <div class="row g-4">
                                            <!-- Chi tiết thời hạn -->
                                            <div class="col-md-6">
                                                <h6 class="fw-bold text-uppercase text-muted mb-3" style="font-size: 11px; letter-spacing: 0.5px;">Thông tin thời hạn</h6>
                                                <div class="p-3 bg-white border rounded-3">
                                                    <div class="d-flex justify-content-between mb-2">
                                                        <span class="text-muted small">Khách hàng:</span>
                                                        <span class="fw-semibold text-dark"><?= htmlspecialchars($w['customer_name']) ?></span>
                                                    </div>
                                                    <div class="d-flex justify-content-between mb-2">
                                                        <span class="text-muted small">Số điện thoại:</span>
                                                        <span class="fw-semibold text-dark"><?= htmlspecialchars($w['customer_phone']) ?></span>
                                                    </div>
                                                    <div class="d-flex justify-content-between mb-2">
                                                        <span class="text-muted small">Ngày mua hàng:</span>
                                                        <span class="fw-semibold text-dark"><?= date('d/m/Y H:i', strtotime($w['started_at'])) ?></span>
                                                    </div>
                                                    <div class="d-flex justify-content-between">
                                                        <span class="text-muted small">Ngày hết hạn:</span>
                                                        <span class="fw-semibold text-dark"><?= date('d/m/Y H:i', strtotime($w['expired_at'])) ?></span>
                                                    </div>
                                                </div>

                                                <!-- Form yêu cầu bảo hành nếu còn hạn -->
                                                <?php if ($w['status'] === 'active'): ?>
                                                    <div class="mt-4">
                                                        <button class="btn btn-outline-primary w-100 fw-bold py-2 rounded-3" 
                                                                type="button" 
                                                                data-bs-toggle="collapse" 
                                                                data-bs-target="#claimForm-<?= $w['id'] ?>" 
                                                                aria-expanded="false">
                                                            <i class="bi bi-wrench-adjustable me-2"></i>Gửi yêu cầu bảo hành/sửa chữa
                                                        </button>
                                                        
                                                        <div class="collapse mt-3" id="claimForm-<?= $w['id'] ?>">
                                                            <form action="/warranty/claim" method="POST" class="p-3 bg-white border rounded-3 shadow-sm">
                                                                <?= csrf_field() ?>
                                                                <input type="hidden" name="warranty_id" value="<?= $w['id'] ?>">
                                                                <input type="hidden" name="query" value="<?= htmlspecialchars($query) ?>">
                                                                
                                                                <div class="mb-3">
                                                                    <label class="form-label fw-bold text-dark small">Mô tả tình trạng lỗi sản phẩm</label>
                                                                    <textarea name="customer_note" class="form-control" rows="3" 
                                                                              placeholder="Ví dụ: Máy không nhận sạc, màn hình có sọc xanh, micro bị rè..." required></textarea>
                                                                </div>
                                                                <button type="submit" class="btn btn-primary w-100 fw-bold">Gửi yêu cầu sửa chữa</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            </div>

                                            <!-- Lịch sử sửa chữa/bảo hành -->
                                            <div class="col-md-6">
                                                <h6 class="fw-bold text-uppercase text-muted mb-3" style="font-size: 11px; letter-spacing: 0.5px;">Lịch sử tiếp nhận bảo hành</h6>
                                                
                                                <?php if (empty($w['claims'])): ?>
                                                    <div class="text-center py-4 bg-white border rounded-3">
                                                        <span class="text-muted small"><i class="bi bi-info-circle me-1"></i>Thiết bị chưa từng bảo hành.</span>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="position-relative ps-3 border-start ms-2" style="border-color: #dee2e6 !important;">
                                                        <?php foreach ($w['claims'] as $c): ?>
                                                            <div class="mb-4 position-relative">
                                                                <!-- Status Dot -->
                                                                <div class="position-absolute bg-white rounded-circle border border-primary d-flex align-items-center justify-content-center" 
                                                                     style="width: 14px; height: 14px; left: -21px; top: 4px;">
                                                                    <div class="bg-primary rounded-circle" style="width: 6px; height: 6px;"></div>
                                                                </div>
                                                                
                                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                                    <span class="text-muted small fw-semibold"><?= date('d/m/Y H:i', strtotime($c['created_at'])) ?></span>
                                                                    
                                                                    <?php if ($c['status'] === 'pending'): ?>
                                                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle">Chờ tiếp nhận</span>
                                                                    <?php elseif ($c['status'] === 'receiving'): ?>
                                                                        <span class="badge bg-info-subtle text-info border border-info-subtle">Đang kiểm tra</span>
                                                                    <?php elseif ($c['status'] === 'repairing'): ?>
                                                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle">Đang sửa chữa</span>
                                                                    <?php elseif ($c['status'] === 'completed'): ?>
                                                                        <span class="badge bg-success-subtle text-success border border-success-subtle">Hoàn thành</span>
                                                                    <?php else: ?>
                                                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">Từ chối</span>
                                                                    <?php endif; ?>
                                                                </div>
                                                                
                                                                <div class="p-3 bg-white border rounded-3 shadow-none">
                                                                    <p class="mb-2 text-dark small"><strong>Lỗi mô tả:</strong> <?= nl2br(htmlspecialchars($c['customer_note'])) ?></p>
                                                                    <?php if (!empty($c['admin_note'])): ?>
                                                                        <div class="p-2 bg-light border-start border-3 border-primary rounded-1 mt-2">
                                                                            <p class="mb-0 text-muted small"><strong>Kỹ thuật viên:</strong> <?= nl2br(htmlspecialchars($c['admin_note'])) ?></p>
                                                                        </div>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
