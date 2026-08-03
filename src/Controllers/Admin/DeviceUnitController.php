<?php

namespace App\Controllers\Admin;

use App\Models\DeviceUnit;
use App\Models\Product;
use App\Models\Store;

class DeviceUnitController
{
    public function index(): void
    {
        $this->guard();
        $filters = [
            'q' => trim($_GET['q'] ?? ''),
            'status' => trim($_GET['status'] ?? ''),
            'product_id' => (int) ($_GET['product_id'] ?? 0) ?: null,
            'store_id' => (int) ($_GET['store_id'] ?? 0) ?: null,
        ];
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $result = (new DeviceUnit())->list($filters, $page, 40);

        view('admin.device_units.index', [
            'result' => $result,
            'filters' => $filters,
            'products' => (new Product())->allWithCategory(),
            'stores' => (new Store())->all(),
        ]);
    }

    public function store(): void
    {
        $this->guard();
        $imeiRaw = trim($_POST['imei'] ?? '');
        $productId = (int) ($_POST['product_id'] ?? 0);
        $storeId = (int) ($_POST['store_id'] ?? 0) ?: null;
        $variantId = (int) ($_POST['variant_id'] ?? 0) ?: null;

        if ($productId <= 0) {
            flash('error', 'Chọn sản phẩm.');
            redirect('/admin/device-units');
        }

        // Support bulk: one IMEI per line
        $imeis = preg_split('/[\s,;]+/', $imeiRaw) ?: [];
        $imeis = array_values(array_filter(array_map('trim', $imeis)));
        if ($imeis === []) {
            flash('error', 'Nhập ít nhất một IMEI.');
            redirect('/admin/device-units');
        }

        $model = new DeviceUnit();
        $ok = 0;
        $fail = 0;
        foreach ($imeis as $imei) {
            if (!preg_match('/^\d{10,20}$/', $imei)) {
                $fail++;
                continue;
            }
            if ($model->findByImei($imei)) {
                $fail++;
                continue;
            }
            try {
                if ($model->create([
                    'imei' => $imei,
                    'product_id' => $productId,
                    'variant_id' => $variantId,
                    'store_id' => $storeId,
                    'status' => 'available',
                ])) {
                    $ok++;
                } else {
                    $fail++;
                }
            } catch (\Throwable) {
                $fail++;
            }
        }

        flash('success', "Đã thêm {$ok} IMEI" . ($fail ? ", bỏ qua {$fail}" : '') . '.');
        redirect('/admin/device-units');
    }

    public function destroy(int $id): void
    {
        $this->guard();
        if ((new DeviceUnit())->delete($id)) {
            flash('success', 'Đã xóa IMEI (chỉ xóa máy còn available).');
        } else {
            flash('error', 'Không xóa được (máy đã reserve/sold).');
        }
        redirect('/admin/device-units');
    }

    public function autoSeed(): void
    {
        $this->guard();
        $storeId = (int) ($_POST['store_id'] ?? 0) ?: null;

        try {
            $result = \App\Services\DeviceUnitService::seedImeisForAllProducts($storeId);
            flash(
                'success',
                "Đã tự sinh {$result['created']} IMEI cho {$result['products']} sản phẩm"
                . ($result['skipped'] ? " (bỏ qua {$result['skipped']} SP đã đủ / hết tồn)." : '.')
            );
        } catch (\Throwable $e) {
            flash('error', 'Không thể sinh IMEI: ' . $e->getMessage());
        }

        redirect('/admin/device-units');
    }

    private function guard(): void
    {
        if (!isAdmin()) {
            redirect('/admin/login');
        }
    }
}
