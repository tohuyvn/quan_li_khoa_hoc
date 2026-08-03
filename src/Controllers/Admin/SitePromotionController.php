<?php

namespace App\Controllers\Admin;

use App\Models\SitePromotion;

class SitePromotionController
{
    private SitePromotion $promotions;

    public function __construct()
    {
        $this->promotions = new SitePromotion();
    }

    public function index(): void
    {
        $this->guard();
        view('admin.site_promotions.index', [
            'promotions' => $this->promotions->all(),
            'activePromo' => $this->promotions->getActive(),
        ]);
    }

    public function createForm(): void
    {
        $this->guard();
        view('admin.site_promotions.form');
    }

    public function store(): void
    {
        $this->guard();
        $data = $this->validatedData();
        if ($data === null) {
            redirect('/admin/site-promotions/create');
        }

        $this->promotions->create($data);
        flash('success', 'Tạo ưu đãi chung thành công!');
        redirect('/admin/site-promotions');
    }

    public function editForm(int $id): void
    {
        $this->guard();
        $promo = $this->promotions->find($id);
        if (!$promo) {
            flash('error', 'Không tìm thấy ưu đãi chung.');
            redirect('/admin/site-promotions');
        }

        view('admin.site_promotions.form', ['promo' => $promo]);
    }

    public function update(int $id): void
    {
        $this->guard();
        $promo = $this->promotions->find($id);
        if (!$promo) {
            flash('error', 'Không tìm thấy ưu đãi chung.');
            redirect('/admin/site-promotions');
        }

        $data = $this->validatedData();
        if ($data === null) {
            redirect('/admin/site-promotions/' . $id . '/edit');
        }

        $this->promotions->update($id, $data);
        flash('success', 'Cập nhật ưu đãi chung thành công!');
        redirect('/admin/site-promotions');
    }

    public function destroy(int $id): void
    {
        $this->guard();
        $this->promotions->delete($id);
        flash('success', 'Đã xóa ưu đãi chung.');
        redirect('/admin/site-promotions');
    }

    private function guard(): void
    {
        if (!isAdmin()) {
            redirect('/admin/login');
        }
    }

    private function validatedData(): ?array
    {
        $name = trim($_POST['name'] ?? '');
        $discount = (int) ($_POST['discount_percent'] ?? 0);
        $startsAt = trim($_POST['starts_at'] ?? '');
        $expires = trim($_POST['expires_at'] ?? '');
        $status = ($_POST['status'] ?? 'inactive') === 'active' ? 'active' : 'inactive';

        if ($name === '' || $discount <= 0 || $discount > 100 || $expires === '') {
            setOld($_POST);
            flash('error', 'Vui lòng nhập đầy đủ thông tin hợp lệ.');
            return null;
        }

        if ($startsAt !== '' && $startsAt > $expires) {
            setOld($_POST);
            flash('error', 'Ngày bắt đầu không được sau ngày kết thúc.');
            return null;
        }

        return [
            'name' => $name,
            'discount_percent' => $discount,
            'starts_at' => $startsAt,
            'expires_at' => $expires,
            'status' => $status,
        ];
    }
}
