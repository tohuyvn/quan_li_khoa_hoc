<?php

namespace App\Controllers\Admin;

class PromotionController
{
    public function index(): void
    {
        if (!isAdmin()) {
            redirect('/admin/login');
        }

        $db = \App\DB::connection();
        $stmt = $db->query('SELECT * FROM promotions ORDER BY created_at DESC');
        $promotions = $stmt->fetchAll();

        view('admin.promotions.index', [
            'promotions' => $promotions,
        ]);
    }

    public function createForm(): void
    {
        if (!isAdmin()) {
            redirect('/admin/login');
        }

        view('admin.promotions.form');
    }

    public function store(): void
    {
        if (!isAdmin()) {
            redirect('/admin/login');
        }

        $code = strtoupper(trim($_POST['code'] ?? ''));
        $discount = (int)($_POST['discount_percent'] ?? 0);
        $expires = trim($_POST['expires_at'] ?? '');
        $status = trim($_POST['status'] ?? 'active');

        if (empty($code) || $discount <= 0 || $discount > 100 || empty($expires)) {
            flash('error', 'Vui lòng nhập đầy đủ thông tin hợp lệ.');
            redirect('/admin/promotions/create');
        }

        $db = \App\DB::connection();
        try {
            $stmt = $db->prepare('INSERT INTO promotions (code, discount_percent, status, expires_at) VALUES (?, ?, ?, ?)');
            $stmt->execute([$code, $discount, $status, $expires]);
            flash('success', 'Tạo chương trình khuyến mãi thành công!');
            redirect('/admin/promotions');
        } catch (\PDOException $e) {
            flash('error', 'Mã khuyến mãi đã tồn tại hoặc có lỗi xảy ra.');
            redirect('/admin/promotions/create');
        }
    }

    public function editForm(int $id): void
    {
        if (!isAdmin()) {
            redirect('/admin/login');
        }

        $db = \App\DB::connection();
        $stmt = $db->prepare('SELECT * FROM promotions WHERE id = ?');
        $stmt->execute([$id]);
        $promo = $stmt->fetch();

        if (!$promo) {
            flash('error', 'Không tìm thấy chương trình khuyến mãi.');
            redirect('/admin/promotions');
        }

        view('admin.promotions.form', [
            'promo' => $promo,
        ]);
    }

    public function update(int $id): void
    {
        if (!isAdmin()) {
            redirect('/admin/login');
        }

        $code = strtoupper(trim($_POST['code'] ?? ''));
        $discount = (int)($_POST['discount_percent'] ?? 0);
        $expires = trim($_POST['expires_at'] ?? '');
        $status = trim($_POST['status'] ?? 'active');

        if (empty($code) || $discount <= 0 || $discount > 100 || empty($expires)) {
            flash('error', 'Vui lòng nhập đầy đủ thông tin hợp lệ.');
            redirect('/admin/promotions/' . $id . '/edit');
        }

        $db = \App\DB::connection();
        try {
            $stmt = $db->prepare('UPDATE promotions SET code = ?, discount_percent = ?, status = ?, expires_at = ? WHERE id = ?');
            $stmt->execute([$code, $discount, $status, $expires, $id]);
            flash('success', 'Cập nhật chương trình khuyến mãi thành công!');
            redirect('/admin/promotions');
        } catch (\PDOException $e) {
            flash('error', 'Mã khuyến mãi đã tồn tại hoặc có lỗi xảy ra.');
            redirect('/admin/promotions/' . $id . '/edit');
        }
    }

    public function destroy(int $id): void
    {
        if (!isAdmin()) {
            redirect('/admin/login');
        }

        $db = \App\DB::connection();
        $stmt = $db->prepare('DELETE FROM promotions WHERE id = ?');
        $stmt->execute([$id]);

        flash('success', 'Xóa chương trình khuyến mãi thành công!');
        redirect('/admin/promotions');
    }
}
