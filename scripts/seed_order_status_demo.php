#!/usr/bin/env php
<?php

require __DIR__ . '/../src/bootstrap.php';

use App\DB;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\ShippingEstimator;
use App\Services\StoreLocator;

$db = DB::connection();
$userModel = new User();
$productModel = new Product();
$orderModel = new Order();

$customer = $userModel->findByEmail('khach@shop.test');
$shipper = $userModel->findByEmail('shipper@shop.test');
$product = $productModel->allActive()[0] ?? null;

if (!$customer || !$shipper || !$product) {
    fwrite(STDERR, "Missing demo customer, shipper, or product. Run scripts/migrate.php first.\n");
    exit(1);
}

try {
    $stmtExisting = $db->prepare("SELECT id FROM orders WHERE customer_name LIKE 'Demo Status - %'");
    $stmtExisting->execute();
    $ids = array_map('intval', array_column($stmtExisting->fetchAll(PDO::FETCH_ASSOC), 'id'));
    if ($ids !== []) {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $db->prepare("DELETE FROM shipper_locations WHERE order_id IN ({$placeholders})")->execute($ids);
        $db->prepare("DELETE FROM order_status_logs WHERE order_id IN ({$placeholders})")->execute($ids);
        $db->prepare("DELETE FROM orders WHERE id IN ({$placeholders})")->execute($ids);
    }

    $cartItems = [[
        'product_id' => (int) $product['id'],
        'name' => $product['name'],
        'quantity' => 1,
        'price' => (int) $product['sale_price'],
    ]];

    $statuses = [
        'pending' => 'Chờ xử lý',
        'confirmed' => 'Đã xác nhận',
        'packing' => 'Đang đóng gói',
        'shipping' => 'Đang giao',
        'completed' => 'Hoàn thành',
    ];

    $created = [];
    $shippingLat = 21.0245;
    $shippingLng = 105.8560;
    $store = (new StoreLocator())->nearest($shippingLat, $shippingLng, $cartItems);
    $estimate = (new ShippingEstimator())->estimate($store['distance_km'] ?? null, 'morning');

    foreach ($statuses as $status => $label) {
        $slot = $status === 'shipping' ? 'any' : 'morning';
        $statusEstimate = (new ShippingEstimator())->estimate($store['distance_km'] ?? null, $slot);
        $order = $orderModel->create([
            'user_id' => (int) $customer['id'],
            'customer_name' => 'Demo Status - ' . $label,
            'phone' => '0912345678',
            'email' => $customer['email'],
            'contact_address' => '24 Trang Tien, Hoan Kiem, Ha Noi',
            'shipping_address' => '24 Trang Tien, Hoan Kiem, Ha Noi',
            'payment_method' => 'cash',
            'payment_status' => $status === 'completed' ? 'paid' : 'pending',
            'total_amount' => (int) $product['sale_price'],
            'status' => $status,
            'shipping_lat' => $shippingLat,
            'shipping_lng' => $shippingLng,
            'store_id' => $store['id'] ?? null,
            'address_province' => 'Ha Noi',
            'address_district' => 'Hoan Kiem',
            'address_ward' => 'Trang Tien',
            'address_street' => '24 Trang Tien',
            'preferred_time_slot' => $statusEstimate['preferred_time_slot'],
            'shipping_fee' => (int) $statusEstimate['shipping_fee'],
            'shipping_distance_km' => $statusEstimate['distance_km'],
            'estimated_delivery_from' => $statusEstimate['eta_from'],
            'estimated_delivery_to' => $statusEstimate['eta_to'],
        ], $cartItems);

        if (in_array($status, ['shipping', 'completed'], true)) {
            $order = $orderModel->update((int) $order['id'], ['shipper_id' => (int) $shipper['id']]);
        }

        $created[$status] = $order;
        \App\OrderStateService::logStatus((int) $order['id'], null, $status, 'system', null, 'Seed demo trạng thái: ' . $label);
    }

    $shippingOrderId = (int) $created['shipping']['id'];
    $stmtLoc = $db->prepare('INSERT INTO shipper_locations (order_id, shipper_id, latitude, longitude) VALUES (?, ?, ?, ?)');
    $stmtLoc->execute([$shippingOrderId, (int) $shipper['id'], 21.0260, 105.8580]);

} catch (Throwable $e) {
    fwrite(STDERR, 'Seed failed: ' . $e->getMessage() . PHP_EOL);
    exit(1);
}

echo "Created demo orders for customer khach@shop.test / 123456\n";
foreach ($created as $status => $order) {
    echo "- {$status}: {$order['order_code']} (id {$order['id']})\n";
}
echo "Shipping demo has shipper location near destination; open its detail page to see map.\n";
