<?php

namespace App\Controllers\Api;

use App\DB;
use PDO;

class PromotionApiController
{
    public function check(): void
    {
        header('Content-Type: application/json');
        $code = trim($_GET['code'] ?? '');
        if ($code === '') {
            echo json_encode(['valid' => false, 'message' => 'Vui lòng nhập mã giảm giá']);
            exit;
        }

        $db = DB::connection();
        $stmt = $db->prepare('SELECT * FROM promotions WHERE code = ? LIMIT 1');
        $stmt->execute([$code]);
        $promo = $stmt->fetch();

        if (!$promo) {
            echo json_encode(['valid' => false, 'message' => 'Mã giảm giá không tồn tại']);
            exit;
        }

        if (($promo['status'] ?? 'active') !== 'active') {
            echo json_encode(['valid' => false, 'message' => 'Mã giảm giá đã bị khóa']);
            exit;
        }

        $today = date('Y-m-d');
        if ($promo['expires_at'] < $today) {
            echo json_encode(['valid' => false, 'message' => 'Mã giảm giá đã hết hạn sử dụng']);
            exit;
        }

        echo json_encode([
            'valid' => true,
            'code' => $promo['code'],
            'discount_percent' => (int) $promo['discount_percent'],
            'message' => 'Áp dụng mã giảm giá thành công: Giảm ' . $promo['discount_percent'] . '%'
        ]);
        exit;
    }
}
