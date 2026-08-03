<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
        <h3 class="fw-bold text-dark mb-0"><i class="bi bi-compass-fill me-2 text-primary"></i>Hành trình giao hàng</h3>
        <a href="/shipper/orders" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Quay lại</a>
    </div>

    <div class="row g-4">
        <!-- Map and Simulation controls -->
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold small">Bản đồ hành trình</h5>
                    <span class="badge bg-success text-white small" id="sim-status">Sẵn sàng</span>
                </div>
                <div class="card-body p-0">
                    <div id="shipper-map" style="height: 400px;"></div>
                </div>
            </div>

            <!-- Simulation Console -->
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0 fw-bold text-dark small"><i class="bi bi-terminal me-2"></i>Bảng điều khiển giả lập</h6>
                </div>
                <div class="card-body">
                    <div class="row g-2 mb-3">
                        <div class="col-sm-4">
                            <button id="btn-real-gps" class="btn btn-outline-primary btn-sm w-100 fw-bold"><i class="bi bi-geo-fill me-1"></i>Dùng GPS Thực (Browser)</button>
                        </div>
                        <div class="col-sm-4">
                            <button id="btn-start-sim" class="btn btn-success btn-sm w-100 fw-bold"><i class="bi bi-play-fill me-1"></i>Chạy Giả lập tự động</button>
                        </div>
                        <div class="col-sm-4">
                            <button id="btn-stop-sim" class="btn btn-danger btn-sm w-100 fw-bold d-none"><i class="bi bi-stop-fill me-1"></i>Dừng Giả lập</button>
                        </div>
                    </div>

                    <div class="alert alert-info py-2 small mb-3">
                        <i class="bi bi-info-circle-fill me-1"></i>
                        <strong>Hướng dẫn:</strong> Bạn có thể click trực tiếp vào một điểm bất kỳ trên bản đồ để cập nhật nhanh tọa độ của shipper, hoặc click "Chạy Giả lập tự động" để giả lập quá trình shipper di chuyển từ ngoài 3km tiến dần về nhà khách hàng.
                    </div>

                    <h6 class="fw-bold mb-2 small">Nhật ký định vị (10 giây/lần)</h6>
                    <div id="sim-logs" class="bg-dark text-light p-3 rounded" style="height: 120px; overflow-y: auto; font-family: monospace; font-size: 11px;">
                        [Chờ bắt đầu cập nhật tọa độ...]
                    </div>
                </div>
            </div>
        </div>

        <!-- Info & Completion Form -->
        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0 fw-bold small">Chi tiết đơn hàng</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2 small text-muted">Mã đơn hàng: <strong class="text-dark"><?= e($order['order_code']) ?></strong></p>
                    <p class="mb-2 small text-muted">Khách hàng: <strong class="text-dark"><?= e($order['customer_name'] ?: ($order['email'] ?? 'N/A')) ?></strong></p>
                    <p class="mb-2 small text-muted">Số điện thoại: <strong class="text-dark"><?= e($order['phone'] ?? 'N/A') ?></strong></p>
                    <p class="mb-2 small text-muted">Địa chỉ nhận hàng: <strong class="text-dark"><?= e($order['shipping_address'] ?? 'N/A') ?></strong></p>
                    <hr>
                    <p class="mb-2 small text-muted">Tọa độ đích: <strong class="text-dark"><?= e($order['shipping_lat'] ?? 'N/A') ?>, <?= e($order['shipping_lng'] ?? 'N/A') ?></strong></p>
                    <p class="mb-0 small text-muted">Tọa độ shipper hiện tại: <strong class="text-success" id="curr-coords">N/A</strong></p>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0 fw-bold small">Hoàn thành nhiệm vụ</h5>
                </div>
                <div class="card-body">
                    <form action="/shipper/orders/<?= (int)$order['id'] ?>/complete" method="POST" onsubmit="return confirm('Bạn chắc chắn đã giao hàng và nhận đủ tiền?')">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-success w-100 fw-bold"><i class="bi bi-check-circle-fill me-1"></i> XÁC NHẬN GIAO THÀNH CÔNG</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const orderId = <?= (int)$order['id'] ?>;
    
    // Fallback if destination coords are not set
    const destLat = <?= (float)($order['shipping_lat'] ?: 21.0285) ?>;
    const destLng = <?= (float)($order['shipping_lng'] ?: 105.8542) ?>;

    let map = null;
    let customerMarker = null;
    let shipperMarker = null;
    let simulationInterval = null;

    let shipperLat = destLat + 0.04; // Start outside the 3km zone
    let shipperLng = destLng + 0.04;

    const shipperIcon = L.icon({
        iconUrl: 'https://cdn-icons-png.flaticon.com/512/2972/2972185.png',
        iconSize: [36, 36],
        iconAnchor: [18, 36],
        popupAnchor: [0, -36]
    });

    const customerIcon = L.icon({
        iconUrl: 'https://cdn-icons-png.flaticon.com/512/1216/1216895.png',
        iconSize: [32, 32],
        iconAnchor: [16, 32],
        popupAnchor: [0, -32]
    });

    // Initialize Map
    function initMap() {
        map = L.map('shipper-map').setView([destLat, destLng], 13);
        
        let tileUrl = 'https://tiles.goong.io/assets/goong_map_web/{z}/{x}/{y}.png?api_key=<?= config('goong.api_key') ?>';
        const goongKey = '<?= config('goong.api_key') ?>';
        if (!goongKey || goongKey === 'dummy_goong_key' || goongKey.startsWith('t6QOq57e3Y24k5F4lJpZ5M7B2XyCw9v8o7n6m5l4')) {
            tileUrl = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
        }

        L.tileLayer(tileUrl, {
            attribution: '© Goong Maps'
        }).addTo(map);

        customerMarker = L.marker([destLat, destLng], { icon: customerIcon }).addTo(map)
            .bindPopup('Địa chỉ giao hàng (Khách hàng)').openPopup();

        // Shipper starting point marker
        shipperMarker = L.marker([shipperLat, shipperLng], { icon: shipperIcon, draggable: true }).addTo(map)
            .bindPopup('Vị trí của bạn (Kéo thả để di chuyển)').openPopup();

        // Dragend handler
        shipperMarker.on('dragend', function(event) {
            const position = shipperMarker.getLatLng();
            updateLocationCoords(position.lat, position.lng);
            logMessage('Bạn vừa di chuyển thủ công marker đến: ' + position.lat.toFixed(6) + ', ' + position.lng.toFixed(6));
        });

        // Click map handler
        map.on('click', function(e) {
            updateLocationCoords(e.latlng.lat, e.latlng.lng);
            logMessage('Bạn click vào bản đồ để di chuyển đến: ' + e.latlng.lat.toFixed(6) + ', ' + e.latlng.lng.toFixed(6));
        });
    }

    function updateLocationCoords(lat, lng) {
        shipperLat = lat;
        shipperLng = lng;
        if (shipperMarker) {
            shipperMarker.setLatLng([lat, lng]);
        }
        document.getElementById('curr-coords').textContent = lat.toFixed(6) + ', ' + lng.toFixed(6);
        sendLocation(lat, lng);
    }

    function logMessage(text) {
        const consoleEl = document.getElementById('sim-logs');
        const now = new Date();
        const timeStr = now.getHours().toString().padStart(2, '0') + ':' + 
                        now.getMinutes().toString().padStart(2, '0') + ':' + 
                        now.getSeconds().toString().padStart(2, '0');
        
        if (consoleEl.innerHTML.includes('[Chờ bắt đầu')) {
            consoleEl.innerHTML = '';
        }
        consoleEl.innerHTML += `[${timeStr}] ${text}<br>`;
        consoleEl.scrollTop = consoleEl.scrollHeight;
    }

    function calculateDistance(lat1, lng1, lat2, lng2) {
        const R = 6371; // km
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLng = (lng2 - lng1) * Math.PI / 180;
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                  Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                  Math.sin(dLng/2) * Math.sin(dLng/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return R * c;
    }

    function sendLocation(lat, lng) {
        fetch('/api/shipper/location', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': <?= json_encode(csrf_token()) ?>
            },
            body: JSON.stringify({
                order_id: orderId,
                latitude: lat,
                longitude: lng
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const distance = calculateDistance(lat, lng, destLat, destLng);
                logMessage(`<span class="text-success">Cập nhật GPS thành công! Khoảng cách tới khách hàng: ${distance.toFixed(2)} km. ${distance <= 3.0 ? '<b>(Khách hàng đã thấy shipper)</b>' : '<b>(Khách hàng chưa thấy shipper)</b>'}</span>`);
            } else {
                logMessage(`<span class="text-danger">Lỗi cập nhật: ${data.error || 'Lỗi không xác định'}</span>`);
            }
        })
        .catch(err => {
            logMessage(`<span class="text-danger">Lỗi kết nối: ${err.message}</span>`);
        });
    }

    // Geolocation API
    document.getElementById('btn-real-gps').addEventListener('click', function() {
        if (!navigator.geolocation) {
            alert('Trình duyệt của bạn không hỗ trợ định vị.');
            return;
        }

        navigator.geolocation.getCurrentPosition(function(position) {
            updateLocationCoords(position.coords.latitude, position.coords.longitude);
            map.setView([position.coords.latitude, position.coords.longitude], 15);
            logMessage('Lấy thành công tọa độ thực từ trình duyệt.');
        }, function(err) {
            alert('Không thể truy cập GPS: ' + err.message);
        });
    });

    // Auto Simulation
    document.getElementById('btn-start-sim').addEventListener('click', function() {
        document.getElementById('btn-start-sim').classList.add('d-none');
        document.getElementById('btn-stop-sim').classList.remove('d-none');
        document.getElementById('sim-status').textContent = 'Đang mô phỏng di chuyển';
        document.getElementById('sim-status').className = 'badge bg-warning text-dark small';

        logMessage('Bắt đầu mô phỏng hành trình tự động...');

        // Set starting position at 4.5km away
        shipperLat = destLat + 0.03;
        shipperLng = destLng + 0.03;
        updateLocationCoords(shipperLat, shipperLng);

        // Adjust bounds
        const group = new L.featureGroup([customerMarker, shipperMarker]);
        map.fitBounds(group.getBounds().pad(0.15));

        let step = 0;
        const totalSteps = 10;

        simulationInterval = setInterval(function() {
            step++;
            if (step > totalSteps) {
                // Arrived
                updateLocationCoords(destLat, destLng);
                logMessage('<span class="text-info fw-bold">Shipper đã đến địa chỉ nhận hàng!</span>');
                clearInterval(simulationInterval);
                document.getElementById('btn-stop-sim').click();
                return;
            }

            // Interpolate coordinates
            const ratio = step / totalSteps;
            const nextLat = (destLat + 0.03) + (destLat - (destLat + 0.03)) * ratio;
            const nextLng = (destLng + 0.03) + (destLng - (destLng + 0.03)) * ratio;

            updateLocationCoords(nextLat, nextLng);
        }, 10000); // every 10 seconds
    });

    document.getElementById('btn-stop-sim').addEventListener('click', function() {
        document.getElementById('btn-stop-sim').classList.add('d-none');
        document.getElementById('btn-start-sim').classList.remove('d-none');
        document.getElementById('sim-status').textContent = 'Sẵn sàng';
        document.getElementById('sim-status').className = 'badge bg-success text-white small';

        logMessage('Đã dừng mô phỏng hành trình tự động.');

        if (simulationInterval) {
            clearInterval(simulationInterval);
            simulationInterval = null;
        }
    });

    // Init map on load
    initMap();
    updateLocationCoords(shipperLat, shipperLng);
});
</script>
