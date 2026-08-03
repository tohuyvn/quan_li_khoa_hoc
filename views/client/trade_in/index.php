<?php $title = 'Thu Cũ Đổi Mới – Lên Đời Điện Thoại'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="content-card p-4 p-md-5 shadow-sm rounded-4" style="background: #ffffff; border: 1px solid rgba(0,0,0,0.08);">
                <div class="text-center mb-5">
                    <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill fw-bold mb-3">CHƯƠNG TRÌNH ĐẶC BIỆT</span>
                    <h1 class="fw-bold text-dark display-6 mb-2">Thu Cũ Đổi Mới – Lên Đời Siêu Phẩm</h1>
                    <p class="text-muted">Định giá máy cũ nhanh chóng trong 3 giây. Nhận ngay Voucher giảm giá trực tiếp mua máy mới!</p>
                </div>

                <?php if (isset($_SESSION['trade_in_success'])): 
                    $success = $_SESSION['trade_in_success'];
                    unset($_SESSION['trade_in_success']);
                ?>
                    <div class="card border-0 rounded-4 p-4 text-center mb-4" style="background: #f8fdf9; border: 1px solid #d4edda !important;">
                        <div class="text-success mb-3" style="font-size: 64px;">
                            <i class="bi bi-patch-check-fill"></i>
                        </div>
                        <h3 class="fw-bold text-success mb-2">Định Giá Thành Công!</h3>
                        <p class="text-muted mb-4">Chúng tôi đã tạo voucher giảm giá trực tiếp cho bạn khi mua máy mới tại Shop Hades.</p>

                        <div class="bg-white p-4 rounded-3 shadow-sm mx-auto mb-4 border" style="max-width: 450px;">
                            <div class="text-muted small uppercase fw-bold mb-1">MÃ VOUCHER THU CŨ CỦA BẠN</div>
                            <div class="fs-2 fw-bold text-danger mb-3 font-monospace tracking-wide" id="voucherCodeText"><?= e($success['voucher_code']) ?></div>
                            <button class="btn btn-outline-danger btn-sm" onclick="copyVoucherCode()">
                                <i class="bi bi-clipboard"></i> Sao chép mã
                            </button>
                        </div>

                        <div class="row g-3 text-start mx-auto mb-4" style="max-width: 450px;">
                            <div class="col-6 text-muted">Thiết bị thu cũ:</div>
                            <div class="col-6 fw-bold text-end"><?= e($success['device_name']) ?></div>

                            <div class="col-6 text-muted">Tình trạng máy:</div>
                            <div class="col-6 fw-bold text-end"><?= e($success['condition_name']) ?></div>

                            <div class="col-6 text-muted">Giá trị thu hồi:</div>
                            <div class="col-6 fw-bold text-end text-success fs-5"><?= formatMoney($success['estimated_value']) ?></div>
                        </div>

                        <div class="d-flex justify-content-center gap-3">
                            <a href="/" class="btn btn-dark px-4 py-2.5 rounded-3 fw-bold">Mua Sắm Ngay</a>
                            <a href="/trade-in" class="btn btn-outline-secondary px-4 py-2.5 rounded-3">Định giá máy khác</a>
                        </div>
                    </div>

                    <script>
                        function copyVoucherCode() {
                            const codeText = document.getElementById('voucherCodeText').innerText;
                            navigator.clipboard.writeText(codeText).then(() => {
                                alert('Đã sao chép mã voucher: ' + codeText);
                            });
                        }
                    </script>
                <?php else: ?>
                    <form action="/trade-in/submit" method="POST" id="tradeInForm" class="needs-validation" novalidate>
                        <?= csrf_field() ?>
                        
                        <!-- Step 1: Chọn máy -->
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark mb-3"><i class="bi bi-phone me-2 text-danger"></i>1. Chọn dòng máy cũ của bạn</label>
                            <div class="row g-3">
                                <?php foreach ($devices as $key => $device): ?>
                                    <div class="col-md-6 col-lg-4">
                                        <div class="card border rounded-3 p-3 text-center cursor-pointer h-100 device-card" 
                                             data-key="<?= e($key) ?>" 
                                             data-val="<?= $device['base_value'] ?>"
                                             onclick="selectDevice(this)">
                                            <div class="fw-bold text-dark small mb-1"><?= e($device['name']) ?></div>
                                            <div class="text-muted small">Định giá tối đa:</div>
                                            <div class="text-danger fw-bold small"><?= formatMoney($device['base_value']) ?></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <input type="hidden" name="device_key" id="selectedDeviceKey" required>
                        </div>

                        <!-- Step 2: Chọn tình trạng -->
                        <div class="mb-4 d-none" id="conditionSection">
                            <label class="form-label fw-bold text-dark mb-3"><i class="bi bi-activity me-2 text-danger"></i>2. Chọn tình trạng thực tế của máy</label>
                            <div class="row g-3">
                                <?php foreach ($conditions as $key => $cond): ?>
                                    <div class="col-md-6">
                                        <div class="card border rounded-3 p-3 cursor-pointer h-100 condition-card" 
                                             data-key="<?= e($key) ?>" 
                                             data-ratio="<?= $cond['ratio'] ?>"
                                             onclick="selectCondition(this)">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span class="fw-bold text-dark small"><?= e($cond['name']) ?></span>
                                                <span class="badge bg-secondary-subtle text-secondary small"><?= (int)($cond['ratio'] * 100) ?>% giá trị</span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <input type="hidden" name="condition_key" id="selectedConditionKey" required>
                        </div>

                        <!-- Live Valuation Preview -->
                        <div class="card border-0 rounded-4 p-4 mb-4 text-center d-none" id="valuationPreview" style="background: #fffcf5; border: 1px solid #ffeeba !important;">
                            <div class="text-warning-emphasis small uppercase fw-bold mb-1">GIÁ TRỊ THU HỒI DỰ KIẾN</div>
                            <div class="display-6 fw-bold text-success mb-1" id="liveEstimatedValue">0đ</div>
                            <p class="text-muted small mb-0">Voucher giảm giá tương ứng sẽ được áp dụng trực tiếp tại trang thanh toán.</p>
                        </div>

                        <!-- Step 3: Thông tin liên hệ -->
                        <div class="mb-4 d-none" id="contactSection">
                            <label class="form-label fw-bold text-dark mb-3"><i class="bi bi-person me-2 text-danger"></i>3. Thông tin nhận Voucher</label>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="customer_name" class="form-label small text-muted">Họ và tên *</label>
                                    <input type="text" class="form-control" id="customer_name" name="customer_name" required 
                                           value="<?= authUser() ? e(authUser()['name']) : '' ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="customer_phone" class="form-label small text-muted">Số điện thoại *</label>
                                    <input type="text" class="form-control" id="customer_phone" name="customer_phone" required 
                                           value="<?= authUser() ? e(authUser()['phone']) : '' ?>">
                                </div>
                            </div>
                        </div>

                        <div class="text-center d-none" id="submitSection">
                            <button type="submit" class="btn btn-danger px-5 py-3 rounded-3 fw-bold shadow-sm">Nhận Voucher & Lên Đời Ngay</button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.cursor-pointer {
    cursor: pointer;
    transition: all 0.2s ease-in-out;
}
.device-card:hover, .condition-card:hover {
    border-color: var(--accent) !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}
.device-card.active, .condition-card.active {
    border-color: #dc3545 !important;
    background-color: #fff8f8 !important;
    box-shadow: 0 0 0 3px rgba(220,53,69,0.15);
}
</style>

<script>
let selectedBaseValue = 0;
let selectedRatio = 0;

function selectDevice(el) {
    document.querySelectorAll('.device-card').forEach(card => card.classList.remove('active'));
    el.classList.add('active');
    
    document.getElementById('selectedDeviceKey').value = el.dataset.key;
    selectedBaseValue = parseInt(el.dataset.val);
    
    // Show step 2
    document.getElementById('conditionSection').classList.remove('d-none');
    
    // Reset step 2 & live calculation if active
    document.querySelectorAll('.condition-card').forEach(card => card.classList.remove('active'));
    document.getElementById('selectedConditionKey').value = '';
    selectedRatio = 0;
    
    updateValuation();
}

function selectCondition(el) {
    document.querySelectorAll('.condition-card').forEach(card => card.classList.remove('active'));
    el.classList.add('active');
    
    document.getElementById('selectedConditionKey').value = el.dataset.key;
    selectedRatio = parseFloat(el.dataset.ratio);
    
    // Show live evaluation & contact section
    document.getElementById('valuationPreview').classList.remove('d-none');
    document.getElementById('contactSection').classList.remove('d-none');
    document.getElementById('submitSection').classList.remove('d-none');
    
    updateValuation();
}

function updateValuation() {
    if (selectedBaseValue > 0 && selectedRatio > 0) {
        const est = Math.round(selectedBaseValue * selectedRatio);
        document.getElementById('liveEstimatedValue').innerText = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(est);
    } else {
        document.getElementById('valuationPreview').classList.add('d-none');
    }
}
</script>
