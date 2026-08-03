<?php

namespace App\Controllers;

use App\DB;

class TradeInController
{
    public static array $devices = [
        'iphone_14_pro_max' => ['name' => 'iPhone 14 Pro Max', 'base_value' => 18000000],
        'iphone_14_pro' => ['name' => 'iPhone 14 Pro', 'base_value' => 15000000],
        'iphone_13_pro_max' => ['name' => 'iPhone 13 Pro Max', 'base_value' => 12000000],
        'iphone_13' => ['name' => 'iPhone 13', 'base_value' => 9000000],
        'galaxy_s23_ultra' => ['name' => 'Samsung Galaxy S23 Ultra', 'base_value' => 14000000],
        'galaxy_s22_ultra' => ['name' => 'Samsung Galaxy S22 Ultra', 'base_value' => 9500000],
        'ipad_pro_m2' => ['name' => 'iPad Pro M2', 'base_value' => 16000000],
    ];

    public static array $conditions = [
        'like_new' => ['name' => 'Máy đẹp 99% (Không trầy xước)', 'ratio' => 1.0],
        'good' => ['name' => 'Máy trầy nhẹ 95% (Hoạt động tốt)', 'ratio' => 0.85],
        'scratched' => ['name' => 'Máy trầy móp nhiều 90%', 'ratio' => 0.70],
        'broken' => ['name' => 'Kính vỡ / lỗi nhẹ màn hình', 'ratio' => 0.40],
    ];

    public function index(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        view('client.trade_in.index', [
            'devices' => self::$devices,
            'conditions' => self::$conditions,
        ]);
    }

    public function submit(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $customerName = trim($_POST['customer_name'] ?? '');
        $customerPhone = trim($_POST['customer_phone'] ?? '');
        $deviceKey = trim($_POST['device_key'] ?? '');
        $conditionKey = trim($_POST['condition_key'] ?? '');

        if ($customerName === '' || $customerPhone === '' || !isset(self::$devices[$deviceKey]) || !isset(self::$conditions[$conditionKey])) {
            flash('error', 'Vui lòng nhập đầy đủ thông tin định giá.');
            redirect('/trade-in');
        }

        $device = self::$devices[$deviceKey];
        $cond = self::$conditions[$conditionKey];
        $estimatedValue = (int) round($device['base_value'] * $cond['ratio']);

        // Generate voucher code
        $voucherCode = 'TI-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));

        $db = DB::connection();
        
        // 1. Create Promotion Coupon
        $stmtPromo = $db->prepare('
            INSERT INTO promotions (code, discount_percent, discount_amount, discount_type, expires_at, status)
            VALUES (?, 0, ?, \'fixed\', ?, \'active\')
        ');
        $expiresAt = date('Y-m-d', strtotime('+30 days'));
        $stmtPromo->execute([$voucherCode, $estimatedValue, $expiresAt]);

        // 2. Create Trade-in request record
        $user = authUser();
        $userId = $user ? (int) $user['id'] : null;

        $stmtTrade = $db->prepare('
            INSERT INTO trade_ins (user_id, customer_name, customer_phone, device_model, device_condition, estimated_value, voucher_code, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, \'approved\')
        ');
        $stmtTrade->execute([
            $userId,
            $customerName,
            $customerPhone,
            $device['name'],
            $cond['name'],
            $estimatedValue,
            $voucherCode
        ]);

        $_SESSION['trade_in_success'] = [
            'device_name' => $device['name'],
            'condition_name' => $cond['name'],
            'estimated_value' => $estimatedValue,
            'voucher_code' => $voucherCode,
        ];

        flash('success', 'Thu cũ đổi mới thành công! Hãy lưu lại mã Voucher của bạn.');
        redirect('/trade-in');
    }
}
