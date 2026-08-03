<?php

namespace App\Controllers\Admin;

use App\Models\Product;
use App\Models\Store;
use App\Models\StoreStock;

class StoreController
{
    public function index(): void
    {
        $this->guard();
        view('admin.stores.index', [
            'stores' => (new Store())->all(),
        ]);
    }

    public function createForm(): void
    {
        $this->guard();
        view('admin.stores.form', ['store' => null]);
    }

    public function store(): void
    {
        $this->guard();
        $data = $this->validated();
        if ($data === null) {
            redirect('/admin/stores/create');
        }
        (new Store())->create($data);
        flash('success', 'Đã thêm cửa hàng.');
        redirect('/admin/stores');
    }

    public function editForm(int $id): void
    {
        $this->guard();
        $store = (new Store())->find($id);
        if (!$store) {
            flash('error', 'Không tìm thấy cửa hàng.');
            redirect('/admin/stores');
        }
        view('admin.stores.form', [
            'store' => $store,
            'stock' => (new StoreStock())->forStore($id),
            'products' => (new Product())->allWithCategory(),
        ]);
    }

    public function update(int $id): void
    {
        $this->guard();
        $store = (new Store())->find($id);
        if (!$store) {
            flash('error', 'Không tìm thấy cửa hàng.');
            redirect('/admin/stores');
        }
        $data = $this->validated();
        if ($data === null) {
            redirect("/admin/stores/{$id}/edit");
        }
        (new Store())->update($id, $data);
        flash('success', 'Đã cập nhật cửa hàng.');
        redirect("/admin/stores/{$id}/edit");
    }

    public function updateStock(int $id): void
    {
        $this->guard();
        $productId = (int) ($_POST['product_id'] ?? 0);
        $qty = (int) ($_POST['quantity'] ?? 0);
        if ($productId <= 0 || $qty < 0) {
            flash('error', 'Dữ liệu tồn kho không hợp lệ.');
            redirect("/admin/stores/{$id}/edit");
        }
        (new StoreStock())->upsert($id, $productId, $qty, 0);
        flash('success', 'Đã cập nhật tồn kho cửa hàng.');
        redirect("/admin/stores/{$id}/edit");
    }

    private function validated(): ?array
    {
        $name = trim($_POST['name'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $lat = (float) ($_POST['lat'] ?? 0);
        $lng = (float) ($_POST['lng'] ?? 0);
        if ($name === '' || $address === '' || $lat == 0.0 || $lng == 0.0) {
            setOld($_POST);
            flash('error', 'Vui lòng nhập tên, địa chỉ và tọa độ hợp lệ.');
            return null;
        }

        return [
            'name' => $name,
            'address' => $address,
            'lat' => $lat,
            'lng' => $lng,
            'phone' => trim($_POST['phone'] ?? '') ?: null,
            'opening_hours' => trim($_POST['opening_hours'] ?? '') ?: null,
            'service_radius_km' => (float) ($_POST['service_radius_km'] ?? 12),
            'status' => ($_POST['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active',
        ];
    }

    private function guard(): void
    {
        if (!isAdmin()) {
            redirect('/admin/login');
        }
    }
}
