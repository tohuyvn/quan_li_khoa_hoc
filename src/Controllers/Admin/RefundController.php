<?php

namespace App\Controllers\Admin;

use App\Models\RefundRequest;
use App\RefundService;

class RefundController
{
    public function index(): void
    {
        if (!isAdmin()) {
            redirect('/admin/login');
        }

        $model = new RefundRequest();
        view('admin.refunds.index', [
            'refunds' => $model->pendingList(),
        ]);
    }

    public function approve(int $id): void
    {
        if (!isAdmin()) {
            redirect('/admin/login');
        }

        $note = trim($_POST['admin_note'] ?? '');

        try {
            if (RefundService::approve($id, $note !== '' ? $note : null)) {
                flash('success', 'Đã duyệt yêu cầu hoàn tiền thành công.');
            } else {
                flash('error', 'Không thể duyệt yêu cầu này.');
            }
        } catch (\Throwable $e) {
            flash('error', 'Lỗi xử lý: ' . $e->getMessage());
        }

        redirect($_POST['redirect'] ?? '/admin/refunds');
    }

    public function reject(int $id): void
    {
        if (!isAdmin()) {
            redirect('/admin/login');
        }

        $note = trim($_POST['admin_note'] ?? '');

        if (RefundService::reject($id, $note !== '' ? $note : null)) {
            flash('success', 'Đã từ chối yêu cầu hoàn tiền.');
        } else {
            flash('error', 'Không thể từ chối yêu cầu này.');
        }

        redirect($_POST['redirect'] ?? '/admin/refunds');
    }
}
