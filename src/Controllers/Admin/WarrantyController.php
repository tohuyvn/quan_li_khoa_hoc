<?php

namespace App\Controllers\Admin;

use App\DB;
use App\WarrantyMailer;

class WarrantyController
{
    public function index(): void
    {
        $db = DB::connection();
        $stmt = $db->query("
            SELECT w.*, p.name AS product_name, p.image AS product_image, o.order_code 
            FROM warranties w 
            JOIN products p ON p.id = w.product_id 
            LEFT JOIN orders o ON o.id = w.order_id 
            ORDER BY w.created_at DESC
        ");
        $warranties = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        view('admin.warranties.index', [
            'title' => 'Quản lý Bảo hành',
            'warranties' => $warranties,
            'products' => $db->query("SELECT id, name FROM products WHERE status = 'active' ORDER BY name ASC")->fetchAll(\PDO::FETCH_ASSOC),
        ]);
    }

    public function activate(): void
    {
        if (!isAdmin()) {
            redirect('/admin/login');
        }

        $productId = (int) ($_POST['product_id'] ?? 0);
        $imei = preg_replace('/\s+/', '', trim($_POST['imei'] ?? ''));
        $phone = preg_replace('/\s+/', '', trim($_POST['customer_phone'] ?? ''));
        $name = trim($_POST['customer_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $months = max(1, min(36, (int) ($_POST['months'] ?? 12)));

        if ($productId <= 0 || $imei === '' || $phone === '' || $name === '') {
            flash('error', 'Vui lòng nhập đủ tên, SĐT, IMEI và sản phẩm.');
            redirect('/admin/warranties');
        }

        if (!preg_match('/^\d{10,20}$/', $imei)) {
            flash('error', 'IMEI không hợp lệ.');
            redirect('/admin/warranties');
        }

        $db = DB::connection();
        $check = $db->prepare('SELECT id FROM warranties WHERE imei = ? LIMIT 1');
        $check->execute([$imei]);
        if ($check->fetch()) {
            flash('error', 'IMEI này đã được kích hoạt bảo hành.');
            redirect('/admin/warranties');
        }

        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        do {
            $wCode = 'WAR-';
            for ($i = 0; $i < 8; $i++) {
                $wCode .= $chars[random_int(0, strlen($chars) - 1)];
            }
            $c = $db->prepare('SELECT COUNT(*) FROM warranties WHERE warranty_code = ?');
            $c->execute([$wCode]);
        } while ((int) $c->fetchColumn() > 0);

        $startedAt = date('Y-m-d H:i:s');
        $expiredAt = date('Y-m-d H:i:s', strtotime("+{$months} months"));

        $stmt = $db->prepare("
            INSERT INTO warranties (order_id, product_id, customer_name, customer_phone, email, imei, warranty_code, status, started_at, expired_at)
            VALUES (NULL, ?, ?, ?, ?, ?, ?, 'active', ?, ?)
        ");
        $stmt->execute([$productId, $name, $phone, $email !== '' ? $email : null, $imei, $wCode, $startedAt, $expiredAt]);

        // Mark device unit sold if exists
        try {
            $upd = $db->prepare("UPDATE device_units SET status = 'sold' WHERE imei = ? AND status IN ('available','reserved')");
            $upd->execute([$imei]);
        } catch (\Throwable) {
        }

        $productName = $db->prepare('SELECT name FROM products WHERE id = ?');
        $productName->execute([$productId]);
        $pName = (string) ($productName->fetchColumn() ?: '');

        if ($email !== '') {
            WarrantyMailer::sendActivated([
                'customer_name' => $name,
                'warranty_code' => $wCode,
                'imei' => $imei,
                'product_name' => $pName,
                'expired_at' => $expiredAt,
                'email' => $email,
            ], $email);
        }

        flash('success', "Đã kích hoạt BH tại quầy. Mã: {$wCode}");
        redirect('/admin/warranties');
    }

    public function claims(): void
    {
        $db = DB::connection();
        $stmt = $db->query("
            SELECT c.*, w.warranty_code, w.imei, w.email AS warranty_email, p.name AS product_name, p.image AS product_image, w.customer_name, w.customer_phone,
                   o.email AS order_email
            FROM warranty_claims c 
            JOIN warranties w ON w.id = c.warranty_id 
            JOIN products p ON p.id = w.product_id 
            LEFT JOIN orders o ON o.id = w.order_id
            ORDER BY c.created_at DESC
        ");
        $claims = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        view('admin.warranties.claims', [
            'title' => 'Yêu cầu Bảo hành & Sửa chữa',
            'claims' => $claims
        ]);
    }

    public function updateClaim(int $id): void
    {
        $status = trim($_POST['status'] ?? '');
        $adminNote = trim($_POST['admin_note'] ?? '');

        $validStatuses = ['pending', 'receiving', 'repairing', 'completed', 'rejected'];
        if (!in_array($status, $validStatuses, true)) {
            flash('error', 'Trạng thái bảo hành không hợp lệ.');
            redirect('/admin/warranties/claims');
        }

        $db = DB::connection();
        $stmt = $db->prepare("UPDATE warranty_claims SET status = ?, admin_note = ? WHERE id = ?");
        $stmt->execute([$status, $adminNote, $id]);

        $info = $db->prepare("
            SELECT c.*, w.warranty_code, w.customer_name, w.customer_phone, w.email AS warranty_email,
                   p.name AS product_name, o.email AS order_email
            FROM warranty_claims c
            JOIN warranties w ON w.id = c.warranty_id
            JOIN products p ON p.id = w.product_id
            LEFT JOIN orders o ON o.id = w.order_id
            WHERE c.id = ?
            LIMIT 1
        ");
        $info->execute([$id]);
        $row = $info->fetch(\PDO::FETCH_ASSOC);
        if ($row) {
            $email = $row['warranty_email'] ?: ($row['order_email'] ?? '');
            if ($email !== '') {
                WarrantyMailer::sendClaimStatus($row, $row, $email);
            }
        }

        flash('success', 'Cập nhật trạng thái bảo hành thành công!');
        redirect('/admin/warranties/claims');
    }
}
