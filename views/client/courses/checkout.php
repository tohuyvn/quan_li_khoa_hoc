<div class="container py-4" style="max-width: 960px;">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="/courses">Khóa học</a></li>
            <li class="breadcrumb-item"><a href="/courses/<?= $course['id'] ?>"><?= e($course['title']) ?></a></li>
            <li class="breadcrumb-item active">Thanh toán</li>
        </ol>
    </nav>

    <h1 class="h3 fw-bold text-dark mb-4"><i class="bi bi-shield-check text-success me-2"></i>Thanh Toán Khóa Học</h1>

    <form method="post" action="/courses/<?= $course['id'] ?>/payment">
        <?= csrf_field() ?>
        <input type="hidden" name="order_code" value="<?= e($orderCode) ?>">

        <div class="row g-4">
            <!-- Left Column: Payment Method Selection -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                    <h5 class="fw-bold mb-3"><i class="bi bi-wallet2 text-primary me-2"></i>Chọn phương thức thanh toán</h5>

                    <div class="d-flex flex-column gap-3">
                        <!-- Option 1: VietQR -->
                        <label class="form-check p-3 border rounded-3 d-flex align-items-center justify-content-between cursor-pointer border-primary bg-primary-subtle bg-opacity-25">
                            <div class="d-flex align-items-center gap-3">
                                <input class="form-check-input ms-0 mt-0" type="radio" name="payment_method" value="vietqr" checked onchange="togglePaymentUI('vietqr')">
                                <div>
                                    <div class="fw-bold text-dark">Chuyển khoản Ngân hàng (VietQR)</div>
                                    <div class="small text-muted">Kích hoạt khóa học tự động trong 30 giây</div>
                                </div>
                            </div>
                            <span class="badge bg-success">Khuyên dùng</span>
                        </label>

                        <!-- Option 2: VNPay -->
                        <label class="form-check p-3 border rounded-3 d-flex align-items-center justify-content-between cursor-pointer">
                            <div class="d-flex align-items-center gap-3">
                                <input class="form-check-input ms-0 mt-0" type="radio" name="payment_method" value="vnpay" onchange="togglePaymentUI('vnpay')">
                                <div>
                                    <div class="fw-bold text-dark">Cổng thanh toán VNPay (ATM / Visa / QR)</div>
                                    <div class="small text-muted">Hỗ trợ hơn 40 ngân hàng nội địa và quốc tế</div>
                                </div>
                            </div>
                            <span class="badge bg-light text-dark border">VNPay Sandbox</span>
                        </label>

                        <!-- Option 3: MoMo -->
                        <label class="form-check p-3 border rounded-3 d-flex align-items-center justify-content-between cursor-pointer">
                            <div class="d-flex align-items-center gap-3">
                                <input class="form-check-input ms-0 mt-0" type="radio" name="payment_method" value="momo" onchange="togglePaymentUI('momo')">
                                <div>
                                    <div class="fw-bold text-dark">Ví điện tử MoMo</div>
                                    <div class="small text-muted">Quét mã QR trên app MoMo siêu tốc</div>
                                </div>
                            </div>
                            <span class="badge bg-danger-subtle text-danger">MoMo QR</span>
                        </label>
                    </div>
                </div>

                <!-- VietQR Dynamic Box -->
                <div id="boxVietQR" class="card border-0 shadow-sm rounded-4 p-4 text-center bg-white">
                    <h6 class="fw-bold text-primary mb-2"><i class="bi bi-qr-code-scan me-1"></i> Quét mã VietQR để thanh toán</h6>
                    <p class="small text-muted mb-3">Mở ứng dụng ngân hàng bất kỳ để quét mã QR và xác nhận chuyển khoản:</p>

                    <div class="d-flex justify-content-center mb-3">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=<?= urlencode("2|99|0901234567|NGUYEN VAN A|LMS|0|0|" . $price . "|" . $orderCode . "|transfer_myqr") ?>" alt="Mã VietQR" class="img-thumbnail rounded-3 shadow-sm" style="width: 180px; height: 180px;">
                    </div>

                    <div class="bg-light p-3 rounded-3 text-start small">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Ngân hàng:</span>
                            <strong class="text-dark">MB Bank (Quân Đội)</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Số tài khoản:</span>
                            <strong class="text-dark font-monospace">090123456789</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Số tiền cần chuyển:</span>
                            <strong class="text-primary fs-6"><?= formatMoney($price) ?></strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Nội dung chuyển khoản:</span>
                            <strong class="text-danger font-monospace fs-6"><?= e($orderCode) ?></strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Order Summary -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 p-4 sticky-top" style="top: 24px;">
                    <h5 class="fw-bold mb-3">Thông tin đơn hàng</h5>

                    <div class="d-flex gap-3 align-items-center pb-3 border-bottom mb-3">
                        <img src="<?= e($course['thumbnail'] ?: '/image/courses/web_fullstack.jpg') ?>" class="rounded-3 object-fit-cover" style="width: 80px; height: 60px;" alt="">
                        <div>
                            <h6 class="fw-bold text-dark mb-1 line-clamp-2"><?= e($course['title']) ?></h6>
                            <div class="small text-muted"><i class="bi bi-person me-1"></i> <?= e($course['instructor_name'] ?? 'Giảng viên') ?></div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between text-secondary mb-2 small">
                        <span>Giá gốc:</span>
                        <span><?= formatMoney($course['price']) ?></span>
                    </div>

                    <?php if ($course['sale_price'] !== null && $course['sale_price'] < $course['price']): ?>
                        <div class="d-flex justify-content-between text-success mb-2 small">
                            <span>Ưu đãi giảm giá:</span>
                            <span>- <?= formatMoney($course['price'] - $course['sale_price']) ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="d-flex justify-content-between align-items-center pt-3 border-top mb-4">
                        <span class="fw-bold text-dark fs-5">Tổng thanh toán:</span>
                        <span class="fw-bold text-primary display-6 fs-3"><?= formatMoney($price) ?></span>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg rounded-pill fw-bold w-100 py-3 shadow">
                        <i class="bi bi-check-circle-fill me-2"></i> Xác nhận thanh toán & Kích hoạt
                    </button>

                    <div class="mt-4 pt-3 border-top text-center text-muted small">
                        <div class="d-flex justify-content-center gap-3 mb-2">
                            <span><i class="bi bi-shield-lock text-success me-1"></i> Bảo mật 256-bit</span>
                            <span><i class="bi bi-patch-check text-primary me-1"></i> Cấp chứng chỉ</span>
                        </div>
                        <p class="mb-0">Bằng việc hoàn tất, bạn đồng ý với Điều khoản sử dụng của Nền tảng Đào tạo LMS.</p>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function togglePaymentUI(method) {
    const boxVietQR = document.getElementById('boxVietQR');
    if (method === 'vietqr') {
        boxVietQR.classList.remove('d-none');
    } else {
        boxVietQR.classList.add('d-none');
    }
}
</script>
