<?php

namespace App\Controllers;

use App\DB;
use App\Models\Order;
use App\OrderStateService;
use App\Services\DeliveryAutoCompletion;

class ShipperController
{
    private function guardShipper(): array
    {
        $user = authUser();
        if (!$user || ($user['role'] ?? '') !== 'shipper') {
            flash('error', 'Chỉ tài khoản Shipper mới có quyền truy cập khu vực này.');
            redirect('/login');
        }
        return $user;
    }

    public function orders(): void
    {
        $user = $this->guardShipper();
        $db = DB::connection();

        // Assigned packing, open packing (no shipper / timed out), or active shipping
        $stmt = $db->prepare('
            SELECT o.*, u.name as customer_name
            FROM orders o
            LEFT JOIN users u ON o.user_id = u.id
            WHERE (o.status = "shipping" AND o.shipper_id = ?)
               OR (
                    o.status = "packing"
                    AND (
                        o.shipper_id IS NULL
                        OR o.shipper_id = ?
                        OR (o.shipper_assigned_at IS NOT NULL AND o.shipper_assigned_at < DATE_SUB(NOW(), INTERVAL 15 MINUTE))
                    )
               )
            ORDER BY o.created_at DESC
        ');
        $stmt->execute([$user['id'], $user['id']]);
        $orders = $stmt->fetchAll();

        view('shipper.orders', [
            'title' => 'Danh sách đơn hàng cần giao',
            'orders' => $orders,
            'user' => $user
        ]);
    }

    public function acceptOrder(int $id): void
    {
        $user = $this->guardShipper();
        $orderModel = new Order();
        $order = $orderModel->find($id);

        if (!$order) {
            flash('error', 'Không tìm thấy đơn hàng.');
            redirect('/shipper/orders');
            return;
        }

        if ($order['status'] !== 'packing') {
            flash('error', 'Đơn hàng này chưa đóng gói xong hoặc đã có người khác nhận.');
            redirect('/shipper/orders');
            return;
        }

        $db = DB::connection();
        $db->beginTransaction();
        try {
            // Assign shipper ID first
            $orderModel->update($id, ['shipper_id' => $user['id']]);

            // Transition to shipping status
            $result = OrderStateService::transition($id, 'shipping', 'shipper', $user['id'], 'Shipper nhận giao đơn hàng.');
            if (!$result['ok']) {
                throw new \Exception($result['error'] ?? 'Lỗi chuyển trạng thái.');
            }

            $db->commit();
            flash('success', 'Nhận đơn hàng thành công! Hãy bắt đầu di chuyển để giao hàng.');
        } catch (\Throwable $e) {
            $db->rollBack();
            flash('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }

        redirect('/shipper/orders');
    }

    public function track(int $id): void
    {
        $user = $this->guardShipper();
        $orderModel = new Order();
        $order = $orderModel->find($id);

        if (!$order || (int)$order['shipper_id'] !== (int)$user['id'] || $order['status'] !== 'shipping') {
            flash('error', 'Đơn hàng không hợp lệ hoặc bạn không được giao đơn hàng này.');
            redirect('/shipper/orders');
            return;
        }

        view('shipper.tracking', [
            'title' => 'Mô phỏng hành trình giao hàng',
            'order' => $order,
            'user' => $user
        ]);
    }

    public function completeOrder(int $id): void
    {
        $user = $this->guardShipper();
        $orderModel = new Order();
        $order = $orderModel->find($id);

        if (!$order || (int)$order['shipper_id'] !== (int)$user['id']) {
            flash('error', 'Đơn hàng không hợp lệ.');
            redirect('/shipper/orders');
            return;
        }

        $proofPath = null;
        if (!empty($_FILES['delivery_proof']['name']) && ($_FILES['delivery_proof']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
            try {
                $imageInfo = \App\ImageService::inspectUploadedImage($_FILES['delivery_proof']);
                $dir = BASE_PATH . '/image/delivery_proofs';
                if (!is_dir($dir)) {
                    mkdir($dir, 0775, true);
                }
                $filename = 'pod_' . $id . '_' . bin2hex(random_bytes(8)) . '.' . $imageInfo['extension'];
                if (move_uploaded_file($_FILES['delivery_proof']['tmp_name'], $dir . '/' . $filename)) {
                    $proofPath = '/image/delivery_proofs/' . $filename;
                    $orderModel->update($id, ['delivery_proof_path' => $proofPath]);
                }
            } catch (\InvalidArgumentException $e) {
                flash('error', $e->getMessage());
                redirect('/shipper/orders/' . $id . '/track');
            }
        }

        $note = 'Shipper xác nhận giao hàng thành công.';
        if ($proofPath) {
            $note .= ' Có ảnh bằng chứng giao hàng.';
        }

        $result = OrderStateService::transition($id, 'completed', 'shipper', $user['id'], $note);
        if ($result['ok']) {
            flash('success', 'Đơn hàng đã được hoàn thành!' . ($proofPath ? ' Đã lưu ảnh giao hàng.' : ''));
        } else {
            flash('error', $result['error'] ?? 'Không thể hoàn thành đơn hàng.');
        }

        redirect('/shipper/orders');
    }

    public function updateLocation(): void
    {
        $user = authUser();
        if (!$user || ($user['role'] ?? '') !== 'shipper') {
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(401);
            echo json_encode(['error' => 'Bạn cần đăng nhập bằng tài khoản shipper.']);
            return;
        }

        // Parse input
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        
        $orderId = isset($input['order_id']) ? (int)$input['order_id'] : 0;
        $lat = isset($input['latitude']) ? (float)$input['latitude'] : 0.0;
        $lng = isset($input['longitude']) ? (float)$input['longitude'] : 0.0;

        if ($orderId <= 0 || $lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => 'Dữ liệu không hợp lệ.']);
            return;
        }

        $shipperId = (int) $user['id'];

        try {
            $db = DB::connection();
            // Validate order status is shipping
            $stmtCheck = $db->prepare('SELECT * FROM orders WHERE id = ? LIMIT 1');
            $stmtCheck->execute([$orderId]);
            $order = $stmtCheck->fetch();

            if (!$order
                || ($order['status'] ?? '') !== 'shipping'
                || (int) ($order['shipper_id'] ?? 0) !== $shipperId
            ) {
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['error' => 'Đơn hàng không hợp lệ hoặc không được giao cho bạn.']);
                return;
            }

            $stmt = $db->prepare('INSERT INTO shipper_locations (order_id, shipper_id, latitude, longitude) VALUES (?, ?, ?, ?)');
            $stmt->execute([$orderId, $shipperId, $lat, $lng]);
            $autoCompletion = DeliveryAutoCompletion::evaluate($db, $order);

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'auto_completed' => $autoCompletion['completed'],
                'auto_complete' => [
                    'eligible' => $autoCompletion['eligible'],
                    'remaining_seconds' => $autoCompletion['remaining_seconds'],
                    'distance_km' => $autoCompletion['distance_km'],
                ],
            ]);
        } catch (\Throwable $e) {
            error_log('Shipper location update failed: ' . $e->getMessage());
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['error' => 'Không thể cập nhật vị trí lúc này.']);
        }
    }
}
