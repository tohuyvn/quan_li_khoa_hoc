<?php
$title = 'Chi tiết đơn hàng ' . ($order['order_code'] ?? ('#' . $order['id']));
$currentStatus = $order['status'] ?? 'pending';
$shouldTrackShipment = $currentStatus === 'shipping' && !empty($order['shipping_lat']) && !empty($order['shipping_lng']);
?>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="content-card mb-4">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <h4 class="fw-bold mb-0">Đơn hàng <?= e($order['order_code'] ?? ('#' . $order['id'])) ?></h4>
                <span class="text-muted small"><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></span>
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th class="text-center">SL</th>
                            <th class="text-end d-none d-md-table-cell">Giá</th>
                            <th class="text-end">Tổng</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <?php
                            $pid = (int) ($item['product_id'] ?? 0);
                            $lineUnits = $unitsByProduct[$pid] ?? [];
                            ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="cart-thumb" style="width: 50px; height: 50px;">
                        <img src="<?= e(imageUrl($item['product_image'] ?? 'logo.webp', 'thumb')) ?>"
                                             alt="<?= e($item['product_name']) ?>"
                                             loading="lazy"
                                             decoding="async"
                                             style="width: 100%; height: 100%; object-fit: contain;"
                                             onerror="this.onerror=null;this.src='/image/logo.webp';">
                                        </div>
                                        <div class="fw-semibold small"><?= e($item['product_name']) ?></div>
                                    </div>
                                    <?php if (!empty($lineUnits)): ?>
                                        <div class="mt-2 ms-1 ps-1 border-start border-2 border-primary-subtle">
                                            <div class="small text-muted mb-1">IMEI / Serial (<?= count($lineUnits) ?>):</div>
                                            <ul class="list-unstyled mb-0 small">
                                                <?php foreach ($lineUnits as $unit): ?>
                                                    <?php
                                                    $imei = (string) ($unit['imei'] ?? '');
                                                    $w = $warrantiesByImei[$imei] ?? null;
                                                    $unitStatus = $unit['status'] ?? '';
                                                    ?>
                                                    <li class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                                        <code class="font-monospace"><?= e($imei) ?></code>
                                                        <span class="badge bg-secondary"><?= e($unitStatus) ?></span>
                                                        <?php if ($w): ?>
                                                            <span class="badge bg-success">BH: <?= e($w['warranty_code'] ?? '') ?></span>
                                                            <a href="/warranty?q=<?= urlencode($imei) ?>" class="link-primary text-decoration-none" style="font-size:11px;">
                                                                Tra cứu BH
                                                            </a>
                                                        <?php else: ?>
                                                            <span class="badge bg-warning text-dark">Chờ kích hoạt bảo hành</span>
                                                        <?php endif; ?>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">x<?= $item['quantity'] ?></td>
                                <td class="text-end text-muted d-none d-md-table-cell small"><?= formatMoney($item['price']) ?></td>
                                <td class="text-end fw-bold small"><?= formatMoney($item['price'] * $item['quantity']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="3" class="text-end d-none d-md-table-cell">Tạm tính:</td>
                            <td colspan="2" class="text-end d-md-none">Tạm tính:</td>
                            <td class="text-end fw-bold"><?= formatMoney($order['total_amount'] + ($order['discount_amount'] ?? 0)) ?></td>
                        </tr>
                        <?php if (!empty($order['discount_amount'])): ?>
                            <tr>
                                <td colspan="3" class="text-end d-none d-md-table-cell">Giảm giá:</td>
                                <td colspan="2" class="text-end d-md-none">Giảm giá:</td>
                                <td class="text-end text-danger">-<?= formatMoney($order['discount_amount']) ?></td>
                            </tr>
                        <?php endif; ?>
                        <?php if (!empty($order['wallet_amount'])): ?>
                            <tr>
                                <td colspan="3" class="text-end d-none d-md-table-cell">Ví Hades:</td>
                                <td colspan="2" class="text-end d-md-none">Ví Hades:</td>
                                <td class="text-end text-success">-<?= formatMoney($order['wallet_amount']) ?></td>
                            </tr>
                        <?php endif; ?>
                        <?php if (!empty($order['shipping_fee'])): ?>
                            <tr>
                                <td colspan="3" class="text-end d-none d-md-table-cell">Phí giao hàng:</td>
                                <td colspan="2" class="text-end d-md-none">Phí ship:</td>
                                <td class="text-end"><?= formatMoney($order['shipping_fee']) ?></td>
                            </tr>
                        <?php endif; ?>
                        <tr>
                            <td colspan="3" class="text-end border-top-0 d-none d-md-table-cell"><strong>Tổng thanh toán:</strong></td>
                            <td colspan="2" class="text-end border-top-0 d-md-none"><strong>Tổng cộng:</strong></td>
                            <td class="text-end border-top-0"><h4 class="text-danger fw-bold mb-0" style="font-size: 1.1rem;"><?= formatMoney($order['total_amount']) ?></h4></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <?php
            $unitsByProduct = $unitsByProduct ?? [];
            $warrantiesByImei = $warrantiesByImei ?? [];
            $deviceUnits = $deviceUnits ?? [];
            $needImeiQty = 0;
            foreach ($items as $it) {
                $needImeiQty += (int) ($it['quantity'] ?? 0);
            }
            $haveImeiQty = count($deviceUnits);
            $activeWarrantyQty = count($warrantiesByImei);
            $statusNow = $order['status'] ?? 'pending';
            ?>

            <div class="mt-3 p-3 rounded border bg-light">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                    <h6 class="fw-bold mb-0"><i class="bi bi-upc-scan me-1"></i> IMEI & bảo hành</h6>
                    <?php if ($activeWarrantyQty > 0): ?>
                        <a href="/warranty" class="btn btn-sm btn-outline-primary">Mở trang tra cứu BH</a>
                    <?php else: ?>
                        <span class="badge bg-secondary">Bảo hành chưa kích hoạt</span>
                    <?php endif; ?>
                </div>

                <?php if ($haveImeiQty > 0): ?>
                    <p class="small text-muted mb-2">
                        Đã gắn <strong><?= (int) $haveImeiQty ?></strong>/<?= (int) $needImeiQty ?> IMEI / Serial.
                        <?php if ($activeWarrantyQty > 0): ?>
                            Bấm <em>Tra cứu BH</em> cạnh IMEI để kiểm tra thời hạn bảo hành.
                        <?php else: ?>
                            Bảo hành sẽ tự kích hoạt khi đơn được giao thành công.
                        <?php endif; ?>
                    </p>
                    <?php if ($haveImeiQty < $needImeiQty): ?>
                        <div class="alert alert-warning py-2 small mb-0">
                            Thiếu <?= (int) ($needImeiQty - $haveImeiQty) ?> IMEI — shop sẽ bổ sung khi đóng gói đủ máy.
                        </div>
                    <?php endif; ?>
                <?php elseif (in_array($statusNow, ['pending', 'confirmed'], true)): ?>
                    <div class="small">
                        <p class="mb-2">Đơn chưa đến bước đóng gói nên chưa cấp IMEI / Serial.</p>
                        <div class="d-flex flex-wrap align-items-center gap-2 text-muted">
                            <span class="badge bg-success">1. Đã xác nhận</span>
                            <i class="bi bi-arrow-right"></i>
                            <span class="badge bg-light text-dark border">2. Đóng gói và cấp IMEI</span>
                            <i class="bi bi-arrow-right"></i>
                            <span class="badge bg-light text-dark border">3. Giao thành công, kích hoạt BH</span>
                        </div>
                    </div>
                <?php elseif ($statusNow === 'cancelled'): ?>
                    <p class="small text-muted mb-0">Đơn đã hủy — không gắn IMEI / bảo hành.</p>
                <?php else: ?>
                    <p class="small text-muted mb-0">
                        Chưa có IMEI gắn đơn. Liên hệ hotline <?= e(config('hotline', '1800 1234')) ?> nếu đơn đã giao mà chưa thấy IMEI.
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
<?php if ($shouldTrackShipment): ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<?php endif; ?>

        <div class="content-card mb-4">
            <h5 class="fw-bold mb-3 border-bottom pb-2">Hành trình đơn hàng</h5>
            
            <?php
            $steps = ['pending', 'confirmed', 'packing', 'shipping', 'completed'];
            $stepLabels = [
                'pending' => 'Chờ xử lý',
                'confirmed' => 'Xác nhận',
                'packing' => 'Đóng gói',
                'shipping' => 'Đang giao',
                'completed' => 'Hoàn thành'
            ];
            $isCancelled = $currentStatus === 'cancelled';
            $statusMap = [
                'pending' => ['label' => 'Chờ xử lý', 'class' => 'bg-warning text-dark'],
                'confirmed' => ['label' => 'Đã xác nhận', 'class' => 'bg-info text-white'],
                'packing' => ['label' => 'Đang đóng gói', 'class' => 'bg-dark text-white'],
                'shipping' => ['label' => 'Đang giao', 'class' => 'bg-primary text-white'],
                'completed' => ['label' => 'Hoàn thành', 'class' => 'bg-success text-white'],
                'cancelled' => ['label' => 'Đã hủy', 'class' => 'bg-secondary text-white'],
            ];
            ?>

            <?php if ($isCancelled): ?>
                <div class="alert alert-danger mb-3 small py-2 fw-semibold">Đơn hàng này đã bị hủy.</div>
            <?php else: ?>
                <div class="d-flex justify-content-between align-items-center mb-4 text-center position-relative px-2 mt-3">
                    <div class="position-absolute start-0 end-0 top-50 translate-middle-y bg-secondary" style="height: 2px; z-index: 1; opacity: 0.2;"></div>
                    <?php
                    $activeCount = array_search($currentStatus, $steps);
                    if ($activeCount === false) $activeCount = -1;
                    ?>
                    <?php foreach ($steps as $index => $step): 
                        $isActive = $index <= $activeCount;
                        $color = $isActive ? 'bg-success text-white' : 'bg-light text-muted border';
                        $labelColor = $isActive ? 'text-success fw-bold' : 'text-muted';
                    ?>
                        <div class="position-relative" style="z-index: 2; width: 60px;">
                            <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto <?= $color ?>" style="width: 24px; height: 24px; font-size: 11px; font-weight: bold;">
                                <?= $index + 1 ?>
                            </div>
                            <div class="small mt-1 <?= $labelColor ?>" style="font-size: 9px; white-space: nowrap;"><?= $stepLabels[$step] ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ($shouldTrackShipment): ?>
                <div id="tracking-notice" class="alert alert-info small mb-3">
                    Đang kiểm tra khung giờ giao hàng...
                </div>
                <div id="tracking-map" style="height: 300px; border-radius: 8px;" class="mb-3 d-none border"></div>
            <?php else: ?>
                <div class="alert alert-info small mb-3">
                    <?php if ($currentStatus === 'completed'): ?>
                        Đơn hàng đã hoàn thành. Bản đồ giao hàng đã được đóng.
                    <?php elseif ($currentStatus === 'packing'): ?>
                        Đơn hàng đang được đóng gói. Bản đồ shipper sẽ hiển thị khi shipper bắt đầu giao.
                    <?php elseif ($currentStatus === 'confirmed'): ?>
                        Đơn hàng đã được xác nhận và đang chờ đóng gói.
                    <?php elseif ($currentStatus === 'cancelled'): ?>
                        Đơn hàng đã hủy nên không có hành trình giao hàng.
                    <?php else: ?>
                        Đơn hàng đang chờ xử lý. Bản đồ shipper sẽ hiển thị khi đơn chuyển sang đang giao.
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="d-flex flex-column gap-2 border-top pt-3">
                <div class="d-flex justify-content-between align-items-center small">
                    <span class="text-muted">Trạng thái:</span>
                    <?php $s = $statusMap[$currentStatus] ?? ['label' => $currentStatus, 'class' => 'bg-secondary']; ?>
                    <span class="badge <?= $s['class'] ?> px-2 py-1"><?= $s['label'] ?></span>
                </div>
                <div class="d-flex justify-content-between align-items-center small">
                    <span class="text-muted">Thanh toán:</span>
                    <span class="fw-semibold"><?php
                        $pm = $order['payment_method'] ?? 'cash';
                        echo match ($pm) {
                            'vnpay' => 'VNPay',
                            'momo' => 'MoMo',
                            'bank_transfer' => 'VietQR (MB Bank)',
                            'installment' => 'Trả góp',
                            default => 'Tiền mặt',
                        };
                    ?></span>
                </div>
                <div class="d-flex justify-content-between align-items-center small">
                    <span class="text-muted">Kết quả:</span>
                    <?php if (($order['payment_status'] ?? '') === 'paid'): ?>
                        <span class="text-success fw-bold">Đã thanh toán</span>
                    <?php elseif (($order['payment_status'] ?? '') === 'refunded'): ?>
                        <span class="text-danger fw-bold">Đã hoàn tiền</span>
                    <?php else: ?>
                        <span class="text-warning fw-bold">Chưa thanh toán</span>
                    <?php endif; ?>
                </div>
                <?php if (($order['payment_method'] ?? '') === 'bank_transfer' && ($order['payment_status'] ?? '') !== 'paid'): ?>
                    <a href="/payment/vietqr/pay?order_id=<?= (int)$order['id'] ?>" class="btn btn-sm btn-danger mt-2">
                        🏦 Thanh toán / xem mã VietQR
                    </a>
                <?php endif; ?>
            </div>

            <?php if (!empty($statusLogs)): ?>
                <div class="mt-4 border-top pt-3">
                    <h6 class="fw-bold mb-3 small">Lịch sử trạng thái</h6>
                    <div class="position-relative ps-3" style="border-left: 2px solid #dee2e6;">
                        <?php foreach (array_reverse($statusLogs) as $log): ?>
                            <div class="mb-3 position-relative">
                                <div class="position-absolute" style="left: -22px; top: 4px; width: 10px; height: 10px; border-radius: 50%; background: #198754; border: 2px solid #fff;"></div>
                                <div class="small">
                                    <span class="fw-bold text-dark">
                                        <?= e($statusMap[$log['to_status']]['label'] ?? $log['to_status']) ?>
                                    </span>
                                    <span class="text-muted ms-2" style="font-size: 11px;"><?= date('d/m H:i', strtotime($log['created_at'])) ?></span>
                                </div>
                                <?php if ($log['note']): ?>
                                    <div class="text-muted mt-1" style="font-size: 11px; font-style: italic;"><?= e($log['note']) ?></div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="content-card mb-3">
            <h5 class="fw-bold mb-3 border-bottom pb-2">Thông tin giao hàng</h5>
            <div class="small">
                <p class="mb-1"><strong>Người nhận:</strong> <?= e($order['customer_name']) ?></p>
                <p class="mb-1"><strong>Điện thoại:</strong> <?= e($order['phone'] ?? 'N/A') ?></p>
                <p class="mb-1"><strong>Địa chỉ:</strong> <?= e($order['shipping_address'] ?? 'N/A') ?></p>
                <?php if (!empty($store)): ?>
                    <p class="mb-1"><strong>Cửa hàng giao:</strong> <?= e($store['name']) ?></p>
                <?php endif; ?>
                <?php if (!empty($order['shipping_distance_km'])): ?>
                    <p class="mb-1"><strong>Khoảng cách:</strong> ~<?= e((string) $order['shipping_distance_km']) ?> km</p>
                <?php endif; ?>
                <?php if (!empty($order['estimated_delivery_from'])): ?>
                    <?php
                    $from = date('d/m/Y', strtotime($order['estimated_delivery_from']));
                    $to = !empty($order['estimated_delivery_to']) ? date('d/m/Y', strtotime($order['estimated_delivery_to'])) : $from;

                    $dist = $order['shipping_distance_km'] !== null ? (float)$order['shipping_distance_km'] : null;
                    $timeText = '';
                    if ($dist !== null) {
                        if ($dist > 100) {
                            $timeText = ' (mất 3 ngày)';
                        } elseif ($dist > 50) {
                            $timeText = ' (mất 2 ngày)';
                        } elseif ($dist > 20) {
                            $timeText = ' (mất 1 ngày)';
                        } elseif ($dist < 10) {
                            $timeText = ' (mất 1h)';
                        } else {
                            $timeText = ' (mất 1 ngày)';
                        }
                    }
                    ?>
                    <p class="mb-1"><strong>Dự kiến giao:</strong> <?= $from === $to ? $from : "{$from} - {$to}" ?><?= $timeText ?></p>
                <?php endif; ?>
                <?php if (!empty($order['preferred_time_slot'])): ?>
                    <?php $slots = ['any' => 'Linh hoạt', 'morning' => 'Sáng', 'afternoon' => 'Chiều', 'evening' => 'Tối']; ?>
                    <p class="mb-1"><strong>Khung giờ:</strong> <?= e($slots[$order['preferred_time_slot']] ?? $order['preferred_time_slot']) ?></p>
                <?php endif; ?>
                <?php if (!empty($order['email'])): ?>
                    <p class="mb-0"><strong>Email:</strong> <?= e($order['email']) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($order['status'] === 'pending' || $order['status'] === 'confirmed'): ?>
            <div class="content-card mb-3">
                <h5 class="fw-bold mb-2 text-danger"><i class="bi bi-x-circle me-1"></i> Hủy đơn hàng</h5>
                <p class="small text-muted mb-3">Bạn có thể tự hủy đơn hàng khi đơn ở trạng thái Chờ xử lý hoặc Đã xác nhận.</p>
                <form action="/orders/<?= $order['id'] ?>/cancel" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này?');">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-danger w-100 btn-sm">Hủy đơn hàng</button>
                </form>
            </div>
        <?php endif; ?>

        <?php if ($canRefund ?? false): ?>
            <div class="content-card mb-3">
                <h5 class="fw-bold mb-2 text-warning"><i class="bi bi-arrow-counterclockwise me-1"></i> Yêu cầu hoàn tiền</h5>
                <p class="small text-muted mb-3">Chọn hình thức nhận hoàn tiền. Yêu cầu sẽ được admin duyệt trong thời gian sớm nhất.</p>
                <form action="/orders/<?= $order['id'] ?>/refund" method="POST" onsubmit="return confirm('Gửi yêu cầu hoàn tiền cho đơn này?');">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="refund_method" id="refund_wallet" value="wallet" checked>
                            <label class="form-check-label" for="refund_wallet">
                                <strong>Ví Hades</strong> — cộng <?= formatMoney($order['total_amount']) ?> vào ví nội bộ
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="refund_method" id="refund_voucher" value="voucher">
                            <label class="form-check-label" for="refund_voucher">
                                <strong>Voucher giảm giá</strong> — nhận mã 10% (tối đa <?= formatMoney($order['total_amount']) ?>)
                            </label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-warning w-100 btn-sm">Gửi yêu cầu hoàn tiền</button>
                </form>
            </div>
        <?php endif; ?>

        <?php if (!empty($refundRequests)): ?>
            <div class="content-card mb-3">
                <h5 class="fw-bold mb-2 border-bottom pb-2">Lịch sử yêu cầu hoàn tiền</h5>
                <?php foreach ($refundRequests as $rr): ?>
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom small">
                        <span>
                            <?= ($rr['method'] ?? '') === 'voucher' ? 'Voucher' : 'Ví Hades' ?>
                            · <?= formatMoney((int) $rr['amount']) ?>
                        </span>
                        <?php
                        $st = $rr['status'] ?? 'pending';
                        $badge = $st === 'approved' ? 'success' : ($st === 'rejected' ? 'danger' : 'warning');
                        $label = $st === 'approved' ? 'Đã duyệt' : ($st === 'rejected' ? 'Từ chối' : 'Chờ duyệt');
                        ?>
                        <span class="badge bg-<?= $badge ?>"><?= $label ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="content-card mb-3">
            <h5 class="fw-bold mb-3 border-bottom pb-2"><i class="bi bi-telephone-outbound me-1"></i> Hỗ trợ hoàn tiền</h5>
            <div class="small">
                <p class="mb-1"><i class="bi bi-telephone-fill me-2 text-primary"></i> Hotline: <strong>1800 1234</strong> (24/7)</p>
                <p class="mb-1"><i class="bi bi-envelope-fill me-2 text-primary"></i> Email: <strong>support@hades.vn</strong></p>
                <p class="mb-0"><i class="bi bi-chat-dots-fill me-2 text-primary"></i> Chat trực tiếp qua <strong>Hades AI Assistant</strong> hoặc <strong>Chat trực tuyến</strong> ở góc dưới bên phải màn hình.</p>
            </div>
        </div>

        <a href="/orders" class="btn btn-outline-secondary w-100"><i class="bi bi-arrow-left"></i> Quay lại danh sách</a>
    </div>
</div>

<?php if ($shouldTrackShipment): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const orderId = <?= (int)$order['id'] ?>;
    const guestToken = '<?= md5(($order['order_code'] ?? '') . ($order['phone'] ?? '')) ?>';
    
    let map = null;
    let customerMarker = null;
    let shipperMarker = null;
    let routeLine = null;
    
    const shipperIcon = L.divIcon({
        className: 'shipper-bike-icon',
        html: '<div style="width:34px;height:34px;border-radius:50%;background:#2563eb;color:#fff;display:flex;align-items:center;justify-content:center;border:3px solid #fff;box-shadow:0 6px 18px rgba(37,99,235,.35);font-size:18px;">🏍</div>',
        iconSize: [34, 34],
        iconAnchor: [17, 17],
        popupAnchor: [0, -18]
    });

    const customerIcon = L.icon({
        iconUrl: 'https://cdn-icons-png.flaticon.com/512/1216/1216895.png',
        iconSize: [32, 32],
        iconAnchor: [16, 32],
        popupAnchor: [0, -32]
    });

    function initMap(lat, lng) {
        const mapContainer = document.getElementById('tracking-map');
        mapContainer.classList.remove('d-none');
        
        map = L.map('tracking-map').setView([lat, lng], 15);
        
        let tileUrl = 'https://tiles.goong.io/assets/goong_map_web/{z}/{x}/{y}.png?api_key=<?= config('goong.api_key') ?>';
        const goongKey = '<?= config('goong.api_key') ?>';
        if (!goongKey || goongKey === 'dummy_goong_key' || goongKey.startsWith('t6QOq57e3Y24k5F4lJpZ5M7B2XyCw9v8o7n6m5l4')) {
            tileUrl = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
        }

        L.tileLayer(tileUrl, {
            attribution: '© Goong Maps'
        }).addTo(map);

        customerMarker = L.marker([lat, lng], { icon: customerIcon }).addTo(map)
            .bindPopup('Địa điểm nhận hàng của bạn').openPopup();
    }

    let trackingTimer = null;

    function handleTrackingData(data) {
        try {
            // Dynamic status updates
            const currentStatus = '<?= $order['status'] ?>';
            if (data.status !== currentStatus) {
                location.reload();
                return;
            }

            const notice = document.getElementById('tracking-notice');
            if (notice) {
                notice.textContent = data.tracking_message || 'Đang cập nhật hành trình giao hàng...';
                notice.className = data.tracking_visible ? 'alert alert-success small mb-3' : 'alert alert-info small mb-3';
            }

            if (!data.tracking_visible) {
                return;
            }

            if (data.shipping_lat && data.shipping_lng) {
                if (!map) {
                    initMap(data.shipping_lat, data.shipping_lng);
                }

                if (data.shipper) {
                    const shipperLat = data.shipper.lat;
                    const shipperLng = data.shipper.lng;
                    const distance = data.shipper.distance;

                    if (shipperMarker) {
                        animateMarker(shipperMarker, shipperMarker.getLatLng(), L.latLng(shipperLat, shipperLng));
                        shipperMarker.getPopup().setContent('Shipper đang di chuyển (cách ' + distance + ' km)');
                    } else {
                        shipperMarker = L.marker([shipperLat, shipperLng], { icon: shipperIcon }).addTo(map)
                            .bindPopup('Shipper đang di chuyển (cách ' + distance + ' km)').openPopup();
                    }

                    if (routeLine) {
                        routeLine.setLatLngs([[shipperLat, shipperLng], [data.shipping_lat, data.shipping_lng]]);
                    } else {
                        routeLine = L.polyline([[shipperLat, shipperLng], [data.shipping_lat, data.shipping_lng]], {
                            color: '#2563eb',
                            weight: 4,
                            opacity: 0.85,
                            dashArray: '8 10'
                        }).addTo(map);
                    }

                    const group = new L.featureGroup([customerMarker, shipperMarker]);
                    map.fitBounds(group.getBounds().pad(0.15));
                } else {
                    if (shipperMarker) {
                        map.removeLayer(shipperMarker);
                        shipperMarker = null;
                        map.setView([data.shipping_lat, data.shipping_lng], 15);
                    }
                    if (routeLine) {
                        map.removeLayer(routeLine);
                        routeLine = null;
                    }
                }
            }
        } catch (err) {
            console.error('Tracking Error:', err);
        }
    }

    function animateMarker(marker, from, to) {
        const duration = 900;
        const start = performance.now();
        function frame(now) {
            const progress = Math.min(1, (now - start) / duration);
            const lat = from.lat + (to.lat - from.lat) * progress;
            const lng = from.lng + (to.lng - from.lng) * progress;
            marker.setLatLng([lat, lng]);
            if (progress < 1) {
                requestAnimationFrame(frame);
            }
        }
        requestAnimationFrame(frame);
    }

    function refreshTracking() {
        fetch('/api/orders/' + orderId + '/tracking?token=' + guestToken, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function (r) { return r.json(); })
        .then(handleTrackingData)
        .catch(function () {});
    }

    refreshTracking();
    trackingTimer = setInterval(refreshTracking, 5000);

    window.addEventListener('pagehide', function () {
        if (trackingTimer) {
            clearInterval(trackingTimer);
        }
    });

    document.addEventListener('visibilitychange', function () {
        if (document.hidden && trackingTimer) {
            clearInterval(trackingTimer);
            trackingTimer = null;
        } else if (!document.hidden && !trackingTimer) {
            refreshTracking();
            trackingTimer = setInterval(refreshTracking, 5000);
        }
    });
});
</script>
<?php endif; ?>
